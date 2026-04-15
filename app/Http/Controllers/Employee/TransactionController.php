<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class TransactionController extends Controller
{
    // ── INDEX ──────────────────────────────────────────────────
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Transaction::with(['customer', 'items'])
            ->where('created_by', $userId)
            ->latest();

        if ($s = $request->search) {
            $query->search($s);
        }
        if ($s = $request->order_status) {
            $query->byOrderStatus($s);
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(10)->withQueryString();

        // Stats
        $statsRaw = DB::table('transactions')
            ->where('created_by', $userId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN order_status = 'processing' THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid
            ")
            ->first();

        $stats = [
            'total'      => (int) $statsRaw->total,
            'pending'    => (int) $statsRaw->pending,
            'processing' => (int) $statsRaw->processing,
            'paid'       => (int) $statsRaw->paid,
        ];

        return view('employee.transactions.index', compact('transactions', 'stats'));
    }

    // ── SEARCH CUSTOMERS (AJAX) ────────────────────────────────
    public function searchCustomers(Request $request)
    {
        $q = trim($request->q ?? '');
        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $customers = Customer::where('name', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->limit(8)
            ->get(['id', 'name', 'phone', 'address']);

        return response()->json($customers);
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

    // ── CREATE ─────────────────────────────────────────────────
    public function create()
    {
        $services  = Service::active()->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $settings  = Setting::allFlat();
        $discounts = Discount::active()->orderBy('name')->get();

        return view('employee.transactions.create', compact('services', 'customers', 'settings', 'discounts'));
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

            $taxEnabled  = ($settings['tax_enabled'] ?? '0') === '1';
            $taxPercent  = (float) ($settings['tax_percent'] ?? 0);
            $serviceFee  = (float) ($settings['service_fee'] ?? 0);
            $taxAmount   = $taxEnabled ? round(($subtotal - $discountAmount) * $taxPercent / 100, 2) : 0;
            $totalAmount = round($subtotal - $discountAmount + $taxAmount + $serviceFee, 2);

            $customerId    = $request->customer_id ?: null;
            $customerName  = $request->customer_name;
            $customerPhone = $request->customer_phone;

            if ($customerId) {
                $customer      = Customer::find($customerId);
                $customerName  = $customer->name;
                $customerPhone = $customer->phone;
            }

            $invoiceNumber = Transaction::generateInvoiceNumber();

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
                    'redirect'   => route('employee.transactions.show', $transaction),
                ]);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'  => true,
                    'message'  => "Transaksi {$transaction->invoice_number} berhasil dibuat!",
                    'redirect' => route('employee.transactions.show', $transaction),
                ]);
            }

            return redirect()->route('employee.transactions.show', $transaction)
                ->with('success', "Transaksi {$transaction->invoice_number} berhasil dibuat!");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Employee transaction store error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()], 422);
            }
            return back()->withInput()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }

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
                    'email'      => $transaction->customer?->email ?? 'noreply@laundry.com',
                ],
                'item_details' => $transaction->items->map(fn($item) => [
                    'id'       => 'SVC-' . $item->service_id,
                    'price'    => (int) $item->unit_price,
                    'quantity' => (int) ceil($item->quantity),
                    'name'     => $item->service_name . ' (' . $item->quantity . ' ' . $item->getTypeLabel() . ')',
                ])->toArray(),
                'callbacks' => [
                    'finish' => route('employee.transactions.show', $transaction),
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

            return Snap::getSnapToken($params);

        } catch (\Throwable $e) {
            Log::error('Employee Midtrans getSnapToken error: ' . $e->getMessage());
            return null;
        }
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        // Ownership check
        if ($transaction->created_by != Auth::id()) {
            return response()->json(['error' => 'Anda tidak memiliki akses ke transaksi ini.'], 403);
        }

        // Validity check (cannot update if cancelled or requested for deletion)
        if (in_array($transaction->order_status, ['cancelled', 'cancel_requested'])) {
            return response()->json(['error' => 'Status transaksi tidak dapat diubah (Dibatalkan/Sedang Diajukan Penghapusan).'], 422);
        }

        $request->validate([
            'order_status' => 'required|in:pending,processing,done,delivered',
        ]);

        $transaction->update(['order_status' => $request->order_status]);

        return response()->json([
            'success' => true,
            'message' => "Status diubah ke: {$transaction->order_status_label}",
            'label'   => $transaction->order_status_label,
            'color'   => $transaction->order_status_color,
        ]);
    }

    // ── SHOW ───────────────────────────────────────────────────
    public function show(Transaction $transaction)
    {
        // Employee can only view their own transactions
        if ($transaction->created_by != Auth::id()) {
            abort(403, 'Anda tidak bisa mengakses transaksi ini.');
        }

        $transaction->load(['items.service', 'customer', 'discount', 'createdBy']);
        $settings = Setting::allFlat();
        return view('employee.transactions.show', compact('transaction', 'settings'));
    }

    public function requestDelete(Request $request, Transaction $transaction)
    {
        // Ownership check
        if ($transaction->created_by != Auth::id()) {
            return response()->json(['error' => 'Anda tidak memiliki akses ke transaksi ini.'], 403);
        }

        // Validity check
        if ($transaction->order_status === 'cancelled') {
            return response()->json(['error' => 'Transaksi sudah dibatalkan.'], 422);
        }

        if ($transaction->order_status === 'cancel_requested') {
            return response()->json(['error' => 'Pengajuan penghapusan sedang diproses.'], 422);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $transaction->update([
            'order_status'        => 'cancel_requested',
            'delete_reason'       => $request->reason,
            'delete_requested_by' => Auth::id(),
            'delete_requested_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan penghapusan berhasil dikirim. Mohon tunggu persetujuan Admin/Owner.'
        ]);
    }
}
