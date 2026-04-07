<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanKeuanganController extends Controller
{
    public function index(Request $request)
    {
        // ── Resolve Period ──────────────────────────────────────
        $period    = $request->get('period', 'this_month');
        $dateFrom  = $request->get('date_from');
        $dateTo    = $request->get('date_to');
        $groupBy   = $request->get('group_by', 'day'); // day | week | month

        [$startDate, $endDate] = $this->resolvePeriod($period, $dateFrom, $dateTo);

        // ── Summary Stats ───────────────────────────────────────
        $summary = DB::table('transactions')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->copy()->endOfDay()])
            ->selectRaw("
                COUNT(*) as total_transaksi,
                SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as transaksi_lunas,
                SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END) as total_pendapatan,
                SUM(CASE WHEN payment_status = 'paid' THEN subtotal ELSE 0 END) as total_subtotal,
                SUM(CASE WHEN payment_status = 'paid' THEN discount_amount ELSE 0 END) as total_diskon,
                SUM(CASE WHEN payment_status = 'paid' THEN tax_amount ELSE 0 END) as total_pajak,
                SUM(CASE WHEN payment_status = 'paid' THEN service_fee ELSE 0 END) as total_biaya_admin,
                SUM(CASE WHEN payment_status = 'pending' THEN total_amount ELSE 0 END) as piutang,
                SUM(CASE WHEN payment_method = 'cash' AND payment_status = 'paid' THEN total_amount ELSE 0 END) as pendapatan_tunai,
                SUM(CASE WHEN payment_method = 'midtrans' AND payment_status = 'paid' THEN total_amount ELSE 0 END) as pendapatan_online,
                AVG(CASE WHEN payment_status = 'paid' THEN total_amount ELSE NULL END) as rata_rata_transaksi
            ")
            ->first();

        // ── Previous Period for Comparison ──────────────────────
        $diff        = $startDate->diffInDays($endDate) + 1;
        $prevEnd     = $startDate->copy()->subDay();
        $prevStart   = $prevEnd->copy()->subDays($diff - 1);

        $prevSummary = DB::table('transactions')
            ->whereBetween('created_at', [$prevStart->startOfDay(), $prevEnd->copy()->endOfDay()])
            ->selectRaw("
                SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END) as total_pendapatan,
                COUNT(*) as total_transaksi
            ")
            ->first();

        // ── Chart Data (Revenue over time) ──────────────────────
        $chartData = $this->getChartData($startDate, $endDate, $groupBy);

        // ── Revenue by Payment Method ────────────────────────────
        $revenueByMethod = DB::table('transactions')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->copy()->endOfDay()])
            ->where('payment_status', 'paid')
            ->selectRaw("payment_method, SUM(total_amount) as total, COUNT(*) as jumlah")
            ->groupBy('payment_method')
            ->get();

        // ── Revenue by Service ───────────────────────────────────
        $revenueByService = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.created_at', [$startDate->startOfDay(), $endDate->copy()->endOfDay()])
            ->where('transactions.payment_status', 'paid')
            ->selectRaw("
                transaction_items.service_name,
                SUM(transaction_items.subtotal) as total_revenue,
                SUM(transaction_items.quantity) as total_qty,
                COUNT(DISTINCT transactions.id) as jumlah_transaksi
            ")
            ->groupBy('transaction_items.service_name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // ── Order Status Distribution ─────────────────────────────
        $orderStatusDist = DB::table('transactions')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->copy()->endOfDay()])
            ->selectRaw("order_status, COUNT(*) as jumlah")
            ->groupBy('order_status')
            ->get()
            ->keyBy('order_status');

        // ── Daily/Weekly top transactions ────────────────────────
        $topTransactions = Transaction::with(['items'])
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->copy()->endOfDay()])
            ->where('payment_status', 'paid')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        // ── Monthly comparison (last 12 months) ──────────────────
        $monthlyTrend = DB::table('transactions')
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as bulan,
                SUM(total_amount) as pendapatan,
                COUNT(*) as jumlah
            ")
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        return view('admin.laporan-keuangan.index', compact(
            'summary', 'prevSummary', 'chartData', 'revenueByMethod',
            'revenueByService', 'orderStatusDist', 'topTransactions',
            'monthlyTrend', 'startDate', 'endDate', 'period', 'groupBy'
        ));
    }

    // ── Export PDF / Print ───────────────────────────────────────
    public function print(Request $request)
    {
        $period   = $request->get('period', 'this_month');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        [$startDate, $endDate] = $this->resolvePeriod($period, $dateFrom, $dateTo);

        $transactions = Transaction::with(['items'])
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->copy()->endOfDay()])
            ->where('payment_status', 'paid')
            ->orderByDesc('created_at')
            ->get();

        $summary = DB::table('transactions')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->copy()->endOfDay()])
            ->selectRaw("
                COUNT(*) as total_transaksi,
                SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as transaksi_lunas,
                SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END) as total_pendapatan,
                SUM(CASE WHEN payment_status = 'paid' THEN discount_amount ELSE 0 END) as total_diskon,
                SUM(CASE WHEN payment_status = 'paid' THEN tax_amount ELSE 0 END) as total_pajak
            ")
            ->first();

        return view('admin.laporan-keuangan.print', compact(
            'transactions', 'summary', 'startDate', 'endDate'
        ));
    }

    // ── Export CSV ───────────────────────────────────────────────
    public function export(Request $request)
    {
        $period   = $request->get('period', 'this_month');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        [$startDate, $endDate] = $this->resolvePeriod($period, $dateFrom, $dateTo);

        $transactions = Transaction::with(['items'])
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->copy()->endOfDay()])
            ->orderByDesc('created_at')
            ->get();

        $filename = 'laporan_keuangan_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, [
                'No.Invoice', 'Tanggal', 'Pelanggan', 'No.HP', 'Layanan',
                'Subtotal', 'Diskon', 'Pajak', 'Biaya Admin', 'Total',
                'Metode', 'Status Order', 'Status Bayar', 'Dibayar Pada'
            ]);

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

    // ── Private Helpers ──────────────────────────────────────────
    private function resolvePeriod(string $period, ?string $dateFrom, ?string $dateTo): array
    {
        return match ($period) {
            'today'        => [Carbon::today(), Carbon::today()],
            'yesterday'    => [Carbon::yesterday(), Carbon::yesterday()],
            'this_week'    => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'last_week'    => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
            'this_month'   => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'last_month'   => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'this_quarter' => [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()],
            'this_year'    => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'custom'       => [
                $dateFrom ? Carbon::parse($dateFrom) : Carbon::now()->startOfMonth(),
                $dateTo   ? Carbon::parse($dateTo)   : Carbon::now(),
            ],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }

    private function getChartData(Carbon $startDate, Carbon $endDate, string $groupBy): array
    {
        $diff = $startDate->diffInDays($endDate);

        // Auto-determine group_by based on range
        if ($diff <= 31) {
            $format = '%Y-%m-%d';
            $label  = 'd M';
        } elseif ($diff <= 92) {
            $format = '%Y-%u'; // Week number
            $label  = 'W\w Y';
        } else {
            $format = '%Y-%m';
            $label  = 'M Y';
        }

        $raw = DB::table('transactions')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->copy()->endOfDay()])
            ->selectRaw("
                DATE_FORMAT(created_at, '{$format}') as periode,
                MIN(DATE(created_at)) as min_date,
                SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END) as pendapatan,
                COUNT(*) as jumlah_transaksi,
                SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as lunas
            ")
            ->groupBy('periode')
            ->orderBy('min_date')
            ->get();

        $labels    = [];
        $revenues  = [];
        $counts    = [];

        foreach ($raw as $row) {
            $date     = Carbon::parse($row->min_date);
            $labels[] = $date->translatedFormat('d M');
            $revenues[]= (float) $row->pendapatan;
            $counts[]  = (int) $row->jumlah_transaksi;
        }

        return compact('labels', 'revenues', 'counts');
    }
}
