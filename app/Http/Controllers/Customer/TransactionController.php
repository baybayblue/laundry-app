<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Transaction as MidtransTransaction;
use Midtrans\Snap;

class TransactionController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        $transactions = Transaction::with('items')
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate(10);

        return view('customer.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        // Ensure this transaction belongs to the logged-in customer
        if ($transaction->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $transaction->load(['items.service']);
        return view('customer.transactions.show', compact('transaction'));
    }

    public function checkPaymentStatus(Transaction $transaction)
    {
        // Ownership check
        if ($transaction->customer_id !== Auth::guard('customer')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($transaction->payment_status === 'paid') {
            return response()->json(['success' => true, 'status' => 'paid']);
        }

        try {
            Config::$serverKey    = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');

            $status = MidtransTransaction::status($transaction->invoice_number);

            $transactionStatus = $status->transaction_status;
            $fraudStatus       = $status->fraud_status ?? 'accept';

            $payStatus = 'pending';
            if ($transactionStatus === 'capture') {
                $payStatus = $fraudStatus === 'accept' ? 'paid' : 'failed';
            } elseif ($transactionStatus === 'settlement') {
                $payStatus = 'paid';
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'failure'])) {
                $payStatus = 'failed';
            } elseif ($transactionStatus === 'expire') {
                $payStatus = 'expired';
            }

            if ($payStatus !== $transaction->payment_status) {
                $updates = [
                    'payment_status'        => $payStatus,
                    'midtrans_payment_type' => $status->payment_type,
                ];

                if ($payStatus === 'paid') {
                    $updates['paid_at']      = now();
                    $updates['order_status'] = 'processing';
                }

                $transaction->update($updates);
            }

            return response()->json([
                'success' => true,
                'status'  => $payStatus,
                'message' => $payStatus === 'paid' ? 'Pembayaran berhasil dikonfirmasi!' : 'Status: ' . $payStatus
            ]);

        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), '404')) {
                return response()->json(['success' => true, 'status' => 'pending', 'message' => 'Status: pending (Menunggu pembayaran)']);
            }
            return response()->json(['success' => false, 'message' => 'Gagal mengecek status ke Midtrans.'], 500);
        }
    }

    // ── GET SNAP TOKEN (On-demand) ─────────────────────────────
    public function getSnapToken(Transaction $transaction)
    {
        if ($transaction->customer_id !== Auth::guard('customer')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($transaction->payment_status === 'paid') {
            return response()->json(['error' => 'Transaksi sudah lunas.'], 422);
        }

        if ($transaction->total_amount <= 0) {
            return response()->json(['error' => 'Total tagihan belum dihitung oleh admin.'], 422);
        }

        if ($transaction->midtrans_snap_token) {
            return response()->json(['snap_token' => $transaction->midtrans_snap_token]);
        }

        $settings  = Setting::allFlat();
        $snapToken = $this->createSnapToken($transaction, $settings);

        if ($snapToken) {
            $transaction->update([
                'midtrans_order_id'   => $transaction->invoice_number,
                'midtrans_snap_token' => $snapToken,
            ]);
            return response()->json(['snap_token' => $snapToken]);
        }

        return response()->json(['error' => 'Gagal membuat sesi pembayaran Midtrans.'], 500);
    }

    // ── REQUEST CANCELLATION ──────────────────────────────────
    public function requestCancel(Transaction $transaction)
    {
        if ($transaction->customer_id !== Auth::guard('customer')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!in_array($transaction->order_status, ['pending', 'processing'])) {
            return response()->json(['error' => 'Pesanan tidak dapat dibatalkan pada tahap ini.'], 422);
        }

        $transaction->update(['order_status' => 'cancel_requested']);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan pembatalan telah dikirim. Mohon tunggu persetujuan admin.',
        ]);
    }

    // ── PRIVATE: Create Midtrans Snap Token ────────────────────
    private function createSnapToken(Transaction $transaction, array $settings): ?string
    {
        try {
            Config::$serverKey    = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized  = config('midtrans.is_sanitized');
            Config::$is3ds        = config('midtrans.is_3ds');

            $params = [
                'transaction_details' => [
                    'order_id'     => $transaction->invoice_number,
                    'gross_amount' => (int) $transaction->total_amount,
                ],
                'customer_details' => [
                    'first_name' => $transaction->customer_name,
                    'phone'      => $transaction->customer_phone ?? '',
                    'email'      => Auth::guard('customer')->user()->email ?? 'customer@laundry.com',
                ],
                'item_details' => $transaction->items->map(fn($item) => [
                    'id'       => 'SVC-' . $item->service_id,
                    'price'    => (int) $item->unit_price,
                    'quantity' => (int) ceil($item->quantity),
                    'name'     => $item->service_name . ' (' . $item->quantity . ' ' . $item->getTypeLabel() . ')',
                ])->toArray(),
                'callbacks' => [
                    'finish' => route('customer.transactions.show', $transaction),
                ],
            ];

            if ($transaction->items->isEmpty()) {
                $params['item_details'][] = [
                    'id'       => 'SVC-PICKUP',
                    'price'    => (int) $transaction->total_amount,
                    'quantity' => 1,
                    'name'     => 'Layanan Laundry (Estimasi)',
                ];
            }

            if ($transaction->discount_amount > 0) {
                $params['item_details'][] = [
                    'id'       => 'DISC',
                    'price'    => -(int) $transaction->discount_amount,
                    'quantity' => 1,
                    'name'     => 'Diskon ' . ($transaction->discount_code ?? ''),
                ];
            }

            if ($transaction->tax_amount > 0) {
                $params['item_details'][] = [
                    'id'       => 'TAX',
                    'price'    => (int) $transaction->tax_amount,
                    'quantity' => 1,
                    'name'     => 'PPN',
                ];
            }

            return Snap::getSnapToken($params);

        } catch (\Throwable $e) {
            Log::error('Customer Midtrans getSnapToken error: ' . $e->getMessage());
            return null;
        }
    }

    // ── INVOICE (print) ────────────────────────────────────────
    public function invoice(Transaction $transaction)
    {
        if ($transaction->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $transaction->load(['items', 'customer', 'discount']);
        $settings = \App\Models\Setting::allFlat();
        return view('admin.transactions.invoice', compact('transaction', 'settings'));
    }
}
