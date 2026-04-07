<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Transaction as MidtransTransaction;
use Midtrans\Snap;

class TransactionController extends Controller
{
    // ── INDEX ──────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Transaction::with(['customer', 'items', 'createdBy'])
            ->latest();

        if ($s = $request->search) {
            $query->search($s);
        }
        if ($s = $request->order_status) {
            $query->byOrderStatus($s);
        }
        if ($s = $request->payment_status) {
            $query->byPaymentStatus($s);
        }
        if ($s = $request->payment_method) {
            $query->where('payment_method', $s);
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(10)->withQueryString();

        // ── Stats (1 aggregate query) ──────────────────────────
        $statsRaw = DB::table('transactions')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN order_status = 'processing' THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid,
                SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END) as revenue
            ")
            ->first();

        $stats = [
            'total'      => (int) $statsRaw->total,
            'pending'    => (int) $statsRaw->pending,
            'processing' => (int) $statsRaw->processing,
            'paid'       => (int) $statsRaw->paid,
            'revenue'    => (float) $statsRaw->revenue,
        ];

        return view('admin.transactions.index', compact('transactions', 'stats'));
    }

    // ── SEARCH CUSTOMERS (AJAX) ────────────────────────────────
    public function searchCustomers(Request $request)
    {
        $q = trim($request->q ?? '');
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $customers = \App\Models\Customer::where('name', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->limit(8)
            ->get(['id', 'name', 'phone', 'address']);

        return response()->json($customers);
    }

    // ── EXPORT CSV ─────────────────────────────────────────────
    public function export(Request $request)
    {
        $query = Transaction::with(['items'])->latest();

        if ($s = $request->search) $query->search($s);
        if ($s = $request->order_status) $query->byOrderStatus($s);
        if ($s = $request->payment_status) $query->byPaymentStatus($s);
        if ($s = $request->payment_method) $query->where('payment_method', $s);
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to) $query->whereDate('created_at', '<=', $request->date_to);

        $transactions = $query->get();

        $filename = 'transaksi_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['No.Invoice', 'Tanggal', 'Pelanggan', 'No.HP', 'Layanan', 'Subtotal', 'Diskon', 'Pajak', 'Biaya Admin', 'Total', 'Metode', 'Status Order', 'Status Bayar', 'Dibayar Pada']);

            foreach ($transactions as $t) {
                $serviceNames = $t->items->pluck('service_name')->join(', ');
                fputcsv($out, [
                    $t->invoice_number,
                    $t->created_at->format('d/m/Y H:i'),
                    $t->customer_name,
                    $t->customer_phone ?? '-',
                    $serviceNames,
                    $t->subtotal,
                    $t->discount_amount,
                    $t->tax_amount,
                    $t->service_fee,
                    $t->total_amount,
                    $t->payment_method === 'cash' ? 'Tunai' : 'Online (Midtrans)',
                    $t->order_status_label,
                    $t->payment_status_label,
                    $t->paid_at?->format('d/m/Y H:i') ?? '-',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── CREATE ─────────────────────────────────────────────────
    public function create()
    {
        $services  = Service::active()->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $settings  = Setting::allFlat();
        $discounts = \App\Models\Discount::active()->orderBy('name')->get();

        return view('admin.transactions.create', compact('services', 'customers', 'settings', 'discounts'));
    }


    // ── STORE ──────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'          => 'required|string|max:100',
            'customer_phone'         => 'nullable|string|max:20',
            'payment_method'         => 'required|in:cash,midtrans',
            'items'                  => 'required|array|min:1',
            'items.*.service_id'     => 'required|exists:services,id',
            'items.*.quantity'       => 'required|numeric|min:0.1',
            'pickup_date'            => 'nullable|date',
            'notes'                  => 'nullable|string|max:500',
        ]);

        $settings = Setting::allFlat();

        DB::beginTransaction();
        try {
            // ── Calculate items ────────────────────────────────
            $subtotal  = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $service   = Service::findOrFail($item['service_id']);
                $qty       = (float) $item['quantity'];
                $unitPrice = (float) $service->price;
                $lineTotal = $service->type === 'flat' ? $unitPrice : round($unitPrice * $qty, 2);
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'service_id'   => $service->id,
                    'service_name' => $service->name,
                    'service_type' => $service->type,
                    'quantity'     => $qty,
                    'unit_price'   => $unitPrice,
                    'subtotal'     => $lineTotal,
                    'notes'        => $item['notes'] ?? null,
                ];
            }

            // ── Discount ───────────────────────────────────────
            $discountAmount = 0;
            $discountId     = null;
            $discountCode   = null;

            if ($request->discount_code) {
                $discount = Discount::where('code', $request->discount_code)
                    ->where('is_active', true)
                    ->first();

                if ($discount && $discount->status === 'active') {
                    $discountAmount = $discount->calculate($subtotal);
                    $discountId     = $discount->id;
                    $discountCode   = $discount->code;
                    $discount->increment('usage_count');
                }
            }

            // ── Tax & Fee ──────────────────────────────────────
            $taxEnabled  = ($settings['tax_enabled'] ?? '0') === '1';
            $taxPercent  = (float) ($settings['tax_percent'] ?? 0);
            $serviceFee  = (float) ($settings['service_fee'] ?? 0);
            $taxAmount   = $taxEnabled ? round(($subtotal - $discountAmount) * $taxPercent / 100, 2) : 0;
            $totalAmount = round($subtotal - $discountAmount + $taxAmount + $serviceFee, 2);

            // ── Customer resolve ───────────────────────────────
            $customerId    = $request->customer_id ?: null;
            $customerName  = $request->customer_name;
            $customerPhone = $request->customer_phone;

            if ($customerId) {
                $customer      = Customer::find($customerId);
                $customerName  = $customer->name;
                $customerPhone = $customer->phone;
            }

            // ── Invoice number (lock to prevent race condition) ─
            $invoiceNumber = Transaction::generateInvoiceNumber();

            // ── Create transaction ─────────────────────────────
            $transaction = Transaction::create([
                'invoice_number'  => $invoiceNumber,
                'customer_id'     => $customerId,
                'customer_name'   => $customerName,
                'customer_phone'  => $customerPhone,
                'discount_id'     => $discountId,
                'discount_code'   => $discountCode,
                'subtotal'        => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount'      => $taxAmount,
                'service_fee'     => $serviceFee,
                'total_amount'    => $totalAmount,
                'payment_method'  => $request->payment_method,
                'payment_status'  => $request->payment_method === 'cash' ? 'paid' : 'pending',
                'order_status'    => $request->payment_method === 'cash' ? 'processing' : 'pending',
                'notes'           => $request->notes,
                'pickup_date'     => $request->pickup_date,
                'paid_at'         => $request->payment_method === 'cash' ? now() : null,
                'created_by'      => auth()->id(),
            ]);

            // ── Save items ─────────────────────────────────────
            foreach ($itemsData as $item) {
                $item['transaction_id'] = $transaction->id;
                TransactionItem::create($item);
            }

            // ── Midtrans Snap Token ────────────────────────────
            if ($request->payment_method === 'midtrans') {
                $snapToken = $this->createSnapToken($transaction, $settings);
                if ($snapToken) {
                    $transaction->update([
                        'midtrans_order_id'   => $transaction->invoice_number,
                        'midtrans_snap_token' => $snapToken,
                    ]);
                }
            }

            DB::commit();

            if ($request->payment_method === 'midtrans' && $transaction->midtrans_snap_token) {
                return response()->json([
                    'success'    => true,
                    'snap_token' => $transaction->midtrans_snap_token,
                    'order_id'   => $transaction->invoice_number,
                    'redirect'   => route('admin.transactions.show', $transaction),
                ]);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'  => true,
                    'message'  => "Transaksi {$transaction->invoice_number} berhasil dibuat!",
                    'redirect' => route('admin.transactions.show', $transaction),
                ]);
            }

            return redirect()->route('admin.transactions.show', $transaction)
                ->with('success', "Transaksi {$transaction->invoice_number} berhasil dibuat!");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Transaction store error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()], 422);
            }
            return back()->withInput()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    // ── SHOW ───────────────────────────────────────────────────
    public function show(Transaction $transaction)
    {
        $transaction->load(['items.service', 'customer', 'discount', 'createdBy']);
        $settings = Setting::allFlat();
        return view('admin.transactions.show', compact('transaction', 'settings'));
    }

    // ── UPDATE INFO (AJAX) ─────────────────────────────────────
    public function updateInfo(Request $request, Transaction $transaction)
    {
        if ($transaction->payment_status === 'paid' && $transaction->order_status === 'delivered') {
            return response()->json(['error' => 'Transaksi sudah selesai dan tidak dapat diedit.'], 422);
        }

        $request->validate([
            'notes'       => 'nullable|string|max:500',
            'pickup_date' => 'nullable|date',
        ]);

        $transaction->update([
            'notes'       => $request->notes,
            'pickup_date' => $request->pickup_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Informasi transaksi berhasil diperbarui.',
        ]);
    }

    // ── UPDATE STATUS ──────────────────────────────────────────
    public function updateStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,done,delivered,cancelled',
        ]);

        $transaction->update(['order_status' => $request->order_status]);

        return response()->json([
            'success' => true,
            'message' => "Status diubah ke: {$transaction->order_status_label}",
            'label'   => $transaction->order_status_label,
            'color'   => $transaction->order_status_color,
        ]);
    }

    // ── APPROVE CANCELLATION ───────────────────────────────────
    public function approveCancel(Transaction $transaction)
    {
        if ($transaction->order_status !== 'cancel_requested') {
            return response()->json(['error' => 'Transaksi tidak dalam pengajuan pembatalan.'], 422);
        }

        $transaction->update(['order_status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => "Pembatalan transaksi {$transaction->invoice_number} disetujui.",
        ]);
    }

    // ── GET SNAP TOKEN (On-demand) ─────────────────────────────
    public function getSnapToken(Transaction $transaction)
    {
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

    // ── DESTROY ────────────────────────────────────────────────
    public function destroy(Transaction $transaction)
    {
        if ($transaction->payment_status === 'paid') {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Tidak bisa menghapus transaksi yang sudah dibayar.'], 422);
            }
            return back()->withErrors(['error' => 'Tidak bisa menghapus transaksi yang sudah dibayar.']);
        }
        $transaction->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'redirect' => route('admin.transactions.index')]);
        }
        return redirect()->route('admin.transactions.index')
            ->with('success', "Transaksi {$transaction->invoice_number} dihapus.");
    }

    // ── INVOICE (print) ────────────────────────────────────────
    public function invoice(Transaction $transaction)
    {
        $transaction->load(['items', 'customer', 'discount', 'createdBy']);
        $settings = Setting::allFlat();
        return view('admin.transactions.invoice', compact('transaction', 'settings'));
    }

    // ── CHECK DISCOUNT (AJAX) ──────────────────────────────────
    public function checkDiscount(Request $request)
    {
        $request->validate(['code' => 'required|string', 'subtotal' => 'required|numeric']);

        $discount = Discount::where('code', strtoupper($request->code))
            ->where('is_active', true)
            ->first();

        if (!$discount) {
            return response()->json(['valid' => false, 'message' => 'Kode diskon tidak ditemukan.']);
        }
        if ($discount->status !== 'active') {
            return response()->json(['valid' => false, 'message' => 'Kode diskon tidak aktif atau sudah kadaluarsa.']);
        }
        if ($discount->min_transaction && $request->subtotal < $discount->min_transaction) {
            return response()->json([
                'valid'   => false,
                'message' => 'Minimum transaksi Rp ' . number_format($discount->min_transaction, 0, ',', '.'),
            ]);
        }

        $discountAmount = $discount->calculate((float) $request->subtotal);

        return response()->json([
            'valid'           => true,
            'discount_amount' => $discountAmount,
            'message'         => "Diskon {$discount->formatted_value} berhasil diterapkan!",
            'discount_name'   => $discount->name,
        ]);
    }

    // ── MIDTRANS CALLBACK (webhook) ────────────────────────────
    public function midtransCallback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed    = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            Log::warning('Midtrans: invalid signature for order ' . $request->order_id);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transaction = Transaction::where('invoice_number', $request->order_id)
            ->orWhere('midtrans_order_id', $request->order_id)
            ->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $transactionStatus = $request->transaction_status;
        $fraudStatus       = $request->fraud_status ?? 'accept';

        if ($transactionStatus === 'capture') {
            $payStatus = $fraudStatus === 'accept' ? 'paid' : 'failed';
        } elseif ($transactionStatus === 'settlement') {
            $payStatus = 'paid';
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'failure'])) {
            $payStatus = 'failed';
        } elseif ($transactionStatus === 'expire') {
            $payStatus = 'expired';
        } else {
            $payStatus = 'pending';
        }

        $updates = [
            'payment_status'        => $payStatus,
            'midtrans_payment_type' => $request->payment_type,
        ];

        if ($payStatus === 'paid') {
            $updates['paid_at']      = now();
            $updates['order_status'] = 'processing';
        }

        $transaction->update($updates);

        Log::info("Midtrans callback OK: {$transaction->invoice_number} → {$payStatus}");
        return response()->json(['message' => 'OK']);
    }

    // ── CHECK PAYMENT STATUS (Manual check from UI) ───────────
    public function checkPaymentStatus(Transaction $transaction)
    {
        if ($transaction->payment_status === 'paid') {
            return response()->json(['success' => true, 'status' => 'paid']);
        }

        try {
            \Midtrans\Config::$serverKey    = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');

            $status = \Midtrans\Transaction::status($transaction->invoice_number);

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
            Log::error('Midtrans check status error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengecek status ke Midtrans.'], 500);
        }
    }

    // ── PRIVATE: Create Midtrans Snap Token ────────────────────
    private function createSnapToken(Transaction $transaction, array $settings): ?string
    {
        try {
            \Midtrans\Config::$serverKey    = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized  = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds        = config('midtrans.is_3ds');

            $params = [
                'transaction_details' => [
                    'order_id'     => $transaction->invoice_number,
                    'gross_amount' => (int) $transaction->total_amount,
                ],
                'customer_details' => [
                    'first_name' => $transaction->customer_name,
                    'phone'      => $transaction->customer_phone ?? '',
                    'email'      => $transaction->customer?->email ?? 'noreply@laundry.com',
                ],
                'item_details' => $transaction->items->map(fn($item) => [
                    'id'       => 'SVC-' . $item->service_id,
                    'price'    => (int) $item->unit_price,
                    'quantity' => (int) ceil($item->quantity),
                    'name'     => $item->service_name . ' (' . $item->quantity . ' ' . $item->getTypeLabel() . ')',
                ])->toArray(),
                'callbacks' => [
                    'finish' => route('admin.transactions.show', $transaction),
                ],
            ];

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

            return \Midtrans\Snap::getSnapToken($params);

        } catch (\Throwable $e) {
            Log::error('Midtrans getSnapToken error: ' . $e->getMessage());
            return null;
        }
    }
}
