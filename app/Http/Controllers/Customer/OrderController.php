<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function create()
    {
        return view('customer.orders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pickup_date' => 'required|date|after_or_equal:today',
            'notes'       => 'nullable|string|max:500',
            'address'     => 'required|string|max:255',
        ]);

        $customer = Auth::guard('customer')->user();

        $transaction = Transaction::create([
            'invoice_number' => Transaction::generateInvoiceNumber(),
            'customer_id'    => $customer->id,
            'customer_name'  => $customer->name,
            'customer_phone' => $customer->phone,
            'order_status'   => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'midtrans', // Default to midtrans for customer orders
            'pickup_date'    => $request->pickup_date,
            'notes'          => "Alamat Penjemputan: " . $request->address . "\nCatatan: " . $request->notes,
            'subtotal'       => 0,
            'total_amount'   => 0,
        ]);

        return redirect()->route('customer.dashboard')
            ->with('success', 'Pesanan jemputan berhasil dibuat! Petugas kami akan segera menghubungi Anda.');
    }
}
