<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $userId = Auth::id();

        // Transaksi yang dibuat employee ini hari ini
        $todayTransactions = Transaction::where('created_by', $userId)
            ->whereDate('created_at', $today)
            ->count();

        // Total transaksi employee ini
        $totalTransactions = Transaction::where('created_by', $userId)->count();

        // Transaksi pending (belum selesai)
        $pendingTransactions = Transaction::where('created_by', $userId)
            ->whereIn('order_status', ['pending', 'processing'])
            ->count();

        // Transaksi terbaru oleh employee ini
        $recentTransactions = Transaction::with('customer')
            ->where('created_by', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('employee.dashboard', compact(
            'todayTransactions',
            'totalTransactions',
            'pendingTransactions',
            'recentTransactions'
        ));
    }
}
