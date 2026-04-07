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
        if (strlen($q) < 2) {
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

            DB::commit();

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

    // ── SHOW ───────────────────────────────────────────────────
    public function show(Transaction $transaction)
    {
        // Employee can only view their own transactions
        if ($transaction->created_by !== Auth::id()) {
            abort(403, 'Anda tidak bisa mengakses transaksi ini.');
        }

        $transaction->load(['items.service', 'customer', 'discount', 'createdBy']);
        $settings = Setting::allFlat();
        return view('employee.transactions.show', compact('transaction', 'settings'));
    }
}
