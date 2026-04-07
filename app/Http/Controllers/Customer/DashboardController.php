<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        // Total transaksi pelanggan ini
        $totalTransactions = Transaction::where('customer_id', $customer->id)->count();

        // Total pengeluaran (hanya yang sudah dibayar)
        $totalSpending = Transaction::where('customer_id', $customer->id)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Transaksi terakhir
        $lastTransaction = Transaction::with('items')
            ->where('customer_id', $customer->id)
            ->latest()
            ->first();

        // 5 transaksi terbaru untuk tabel
        $recentTransactions = Transaction::with('items')
            ->where('customer_id', $customer->id)
            ->latest()
            ->take(5)
            ->get();

        // Transaksi aktif (belum selesai)
        $activeTransactions = Transaction::with('items')
            ->where('customer_id', $customer->id)
            ->whereNotIn('order_status', ['done', 'delivered', 'cancelled'])
            ->latest()
            ->get();

        return view('customer.dashboard', compact(
            'totalTransactions',
            'totalSpending',
            'lastTransaction',
            'recentTransactions',
            'activeTransactions'
        ));
    }
}
