<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function create()
    {
        $services  = Service::active()->orderBy('name')->get();
        $discounts = \App\Models\Discount::active()->orderBy('name')->get();
        $settings  = Setting::allFlat();
        
        return view('customer.orders.create', compact('services', 'discounts', 'settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pickup_date'    => 'required|date|after_or_equal:today',
            'pickup_address' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,midtrans',
            'notes'          => 'nullable|string|max:500',
            'items'          => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.quantity'   => 'required|numeric|min:0.1',
        ]);

        $customer = Auth::guard('customer')->user();
        $settings = Setting::allFlat();

        DB::beginTransaction();
        try {
            // ── Calculate items ────────────────────────────────
            $subtotal  = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                if (($item['quantity'] ?? 0) <= 0) continue;

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

            if (empty($itemsData)) {
                return back()->with('error', 'Silakan pilih setidaknya satu layanan.')->withInput();
            }

            // ── Discount ───────────────────────────────────────
            $discountAmount = 0;
            $discountId     = null;
            $discountCode   = null;

            if ($request->discount_code) {
                $discount = \App\Models\Discount::where('code', $request->discount_code)
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

            // ── Create transaction ─────────────────────────────
            $transaction = Transaction::create([
                'invoice_number'  => Transaction::generateInvoiceNumber(),
                'customer_id'     => $customer->id,
                'customer_name'   => $customer->name,
                'customer_phone'  => $customer->phone,
                'discount_id'     => $discountId,
                'discount_code'   => $discountCode,
                'subtotal'        => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount'      => $taxAmount,
                'service_fee'     => $serviceFee,
                'total_amount'    => $totalAmount,
                'payment_method'  => $request->payment_method,
                'payment_status'  => 'pending',
                'order_status'    => 'pending',
                'notes'           => $request->notes,
                'pickup_date'     => $request->pickup_date,
                'pickup_address'  => $request->pickup_address,
                'source'          => 'online_customer',
                'created_by'      => null,
            ]);

            foreach ($itemsData as $data) {
                $data['transaction_id'] = $transaction->id;
                TransactionItem::create($data);
            }

            DB::commit();

            $redirect = redirect()->route('customer.transactions.show', $transaction)
                ->with('success', 'Pesanan berhasil dibuat!');
            
            if ($request->payment_method === 'midtrans') {
                $redirect->with('trigger_snap', true);
            }

            return $redirect;

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
