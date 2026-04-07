<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\StockItem;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Key Stats
        $totalSales = Transaction::where('payment_status', 'paid')->sum('total_amount');
        $todayOrders = Transaction::whereDate('created_at', $today)->count();
        $todayAttendance = Attendance::whereDate('date', $today)->count();
        $lowStockCount = StockItem::whereColumn('stock', '<=', 'min_stock')->count();

        // Previous stats for trend (e.g. versus yesterday)
        $yesterday = Carbon::yesterday();
        $yesterdaySales = Transaction::where('payment_status', 'paid')
            ->whereDate('created_at', $yesterday)
            ->sum('total_amount');
        
        $salesTrend = $yesterdaySales > 0 ? (($totalSales - $yesterdaySales) / $yesterdaySales) * 100 : 0;

        // Recent Transactions (with pending payment button support)
        $recentTransactions = Transaction::latest()->take(5)->get();

        // Low Stock Items
        $lowStockItems = StockItem::whereColumn('stock', '<=', 'min_stock')
            ->take(5)
            ->get();

        // Order Status Distribution
        $orderStatus = Transaction::select('order_status', DB::raw('count(*) as total'))
            ->groupBy('order_status')
            ->pluck('total', 'order_status');

        // Revenue Chart (Last 7 days)
        $revenueData = Transaction::where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Pending payment transactions (for "Bayar Sekarang" button)
        $pendingPaymentTransactions = Transaction::where('payment_status', 'pending')
            ->where('payment_method', 'midtrans')
            ->whereNotNull('midtrans_snap_token')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalSales', 'todayOrders', 'todayAttendance', 'lowStockCount',
            'recentTransactions', 'lowStockItems', 'orderStatus', 'revenueData',
            'salesTrend', 'pendingPaymentTransactions'
        ));
    }

}
