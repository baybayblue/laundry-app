@extends('layouts.app')

@section('content')

{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-7">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                style="background: linear-gradient(135deg, #16a34a20, #16a34a10); border: 1px solid #16a34a30; width:48px; height:48px;">
                <i class="ti ti-chart-bar fs-3" style="color:#16a34a;"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Laporan Keuangan</h1>
                <p class="mb-0 text-muted small">
                    <i class="ti ti-calendar me-1"></i>
                    {{ $startDate->translatedFormat('d F Y') }} – {{ $endDate->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-5 d-flex gap-2 justify-content-md-end flex-wrap">
        <a href="{{ route('admin.laporan-keuangan.print', request()->query()) }}" target="_blank"
            class="btn btn-light d-inline-flex align-items-center gap-2 rounded-pill px-3 border shadow-sm"
            style="font-size:.85rem; color:#7c3aed;">
            <i class="ti ti-printer"></i> Cetak
        </a>
        <a href="{{ route('admin.laporan-keuangan.export', request()->query()) }}"
            class="btn btn-light d-inline-flex align-items-center gap-2 rounded-pill px-3 border shadow-sm"
            style="font-size:.85rem; color:#16a34a;">
            <i class="ti ti-file-spreadsheet"></i> Export CSV
        </a>
    </div>
</div>

{{-- PERIOD FILTER --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.laporan-keuangan.index') }}" id="filterForm" class="row g-2 align-items-end">
            <div class="col-12 col-md-auto">
                <label class="form-label form-label-sm mb-1 text-muted fw-medium small">Periode</label>
                <div class="d-flex flex-wrap gap-1" id="periodBtns">
                    @foreach([
                        'today'        => 'Hari Ini',
                        'yesterday'    => 'Kemarin',
                        'this_week'    => 'Minggu Ini',
                        'last_week'    => 'Minggu Lalu',
                        'this_month'   => 'Bulan Ini',
                        'last_month'   => 'Bulan Lalu',
                        'this_quarter' => 'Kuartal Ini',
                        'this_year'    => 'Tahun Ini',
                        'custom'       => 'Custom',
                    ] as $val => $lbl)
                    <button type="button" class="btn btn-sm period-btn rounded-pill px-3
                        {{ $period === $val ? 'btn-primary text-white' : 'btn-light border' }}"
                        data-period="{{ $val }}" style="font-size:.78rem;">
                        {{ $lbl }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="period" id="periodInput" value="{{ $period }}">
            </div>

            {{-- Custom Range --}}
            <div class="col-12 col-md-auto" id="customRangeDiv" style="{{ $period !== 'custom' ? 'display:none;' : '' }}">
                <label class="form-label form-label-sm mb-1 text-muted fw-medium small">Rentang Tanggal</label>
                <div class="d-flex gap-2 align-items-center">
                    <input type="date" name="date_from" class="form-control form-control-sm" style="min-width:140px;"
                        value="{{ $period === 'custom' ? $startDate->format('Y-m-d') : '' }}">
                    <span class="text-muted small">s/d</span>
                    <input type="date" name="date_to" class="form-control form-control-sm" style="min-width:140px;"
                        value="{{ $period === 'custom' ? $endDate->format('Y-m-d') : '' }}">
                </div>
            </div>

            <div class="col-12 col-md-auto ms-md-auto">
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4">
                    <i class="ti ti-filter me-1"></i>Terapkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- SUMMARY STATS --}}
@php
    $prevRevenue = (float) ($prevSummary->total_pendapatan ?? 0);
    $currRevenue = (float) ($summary->total_pendapatan ?? 0);
    $revenueGrowth = $prevRevenue > 0 ? (($currRevenue - $prevRevenue) / $prevRevenue) * 100 : null;

    $prevTrx = (int) ($prevSummary->total_transaksi ?? 0);
    $currTrx = (int) ($summary->total_transaksi ?? 0);
    $trxGrowth = $prevTrx > 0 ? (($currTrx - $prevTrx) / $prevTrx) * 100 : null;
@endphp

<div class="row g-3 mb-4">
    {{-- Total Pendapatan --}}
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-3 position-relative">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-medium">Total Pendapatan</span>
                    <div class="rounded-2 p-1" style="background:#16a34a15;">
                        <i class="ti ti-currency-dollar fs-5" style="color:#16a34a;"></i>
                    </div>
                </div>
                <div class="fw-bold mb-1" style="font-size:1.2rem; color:#16a34a;">
                    Rp {{ number_format($summary->total_pendapatan ?? 0, 0, ',', '.') }}
                </div>
                @if($revenueGrowth !== null)
                <div class="d-flex align-items-center gap-1" style="font-size:.72rem;">
                    <span class="{{ $revenueGrowth >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                        <i class="ti ti-trending-{{ $revenueGrowth >= 0 ? 'up' : 'down' }}"></i>
                        {{ number_format(abs($revenueGrowth), 1) }}%
                    </span>
                    <span class="text-muted">vs periode lalu</span>
                </div>
                @endif
                <div style="position:absolute;bottom:0;right:0;width:60px;height:60px;background:linear-gradient(135deg,#16a34a08,#16a34a20);border-radius:50% 0 16px 0;"></div>
            </div>
        </div>
    </div>

    {{-- Total Transaksi --}}
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-3 position-relative">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-medium">Total Transaksi</span>
                    <div class="rounded-2 p-1" style="background:#2563eb15;">
                        <i class="ti ti-receipt-2 fs-5 text-primary"></i>
                    </div>
                </div>
                <div class="fw-bold mb-1 text-primary" style="font-size:1.5rem;">
                    {{ number_format($summary->total_transaksi ?? 0) }}
                </div>
                @if($trxGrowth !== null)
                <div class="d-flex align-items-center gap-1" style="font-size:.72rem;">
                    <span class="{{ $trxGrowth >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                        <i class="ti ti-trending-{{ $trxGrowth >= 0 ? 'up' : 'down' }}"></i>
                        {{ number_format(abs($trxGrowth), 1) }}%
                    </span>
                    <span class="text-muted">vs periode lalu</span>
                </div>
                @endif
                <div class="text-muted mt-1" style="font-size:.72rem;">
                    {{ $summary->transaksi_lunas ?? 0 }} lunas
                </div>
                <div style="position:absolute;bottom:0;right:0;width:60px;height:60px;background:linear-gradient(135deg,#2563eb08,#2563eb20);border-radius:50% 0 16px 0;"></div>
            </div>
        </div>
    </div>

    {{-- Rata-rata Transaksi --}}
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-3 position-relative">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-medium">Rata-rata/Transaksi</span>
                    <div class="rounded-2 p-1" style="background:#d97706_15;">
                        <i class="ti ti-calculator fs-5" style="color:#d97706;"></i>
                    </div>
                </div>
                <div class="fw-bold mb-1" style="font-size:1.1rem; color:#d97706;">
                    Rp {{ number_format($summary->rata_rata_transaksi ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-muted" style="font-size:.72rem;">per transaksi lunas</div>
                <div style="position:absolute;bottom:0;right:0;width:60px;height:60px;background:linear-gradient(135deg,#d9770608,#d9770620);border-radius:50% 0 16px 0;"></div>
            </div>
        </div>
    </div>

    {{-- Piutang --}}
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-3 position-relative">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-medium">Piutang (Belum Bayar)</span>
                    <div class="rounded-2 p-1" style="background:#dc354515;">
                        <i class="ti ti-clock fs-5 text-danger"></i>
                    </div>
                </div>
                <div class="fw-bold mb-1 text-danger" style="font-size:1.1rem;">
                    Rp {{ number_format($summary->piutang ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-muted" style="font-size:.72rem;">menunggu pembayaran</div>
                <div style="position:absolute;bottom:0;right:0;width:60px;height:60px;background:linear-gradient(135deg,#dc354508,#dc354520);border-radius:50% 0 16px 0;"></div>
            </div>
        </div>
    </div>
</div>

{{-- CHARTS ROW --}}
<div class="row g-3 mb-4">
    {{-- Revenue Chart --}}
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h6 class="fw-bold mb-0">Grafik Pendapatan</h6>
                        <small class="text-muted">Pendapatan & jumlah transaksi per periode</small>
                    </div>
                    <div class="d-flex align-items-center gap-3" style="font-size:.75rem;">
                        <span class="d-flex align-items-center gap-1">
                            <span style="width:12px;height:3px;background:#16a34a;border-radius:2px;display:inline-block;"></span>
                            Pendapatan
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span style="width:12px;height:3px;background:#2563eb;border-radius:2px;display:inline-block;"></span>
                            Transaksi
                        </span>
                    </div>
                </div>
                <div style="height:260px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Method Donut --}}
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h6 class="fw-bold mb-0">Metode Pembayaran</h6>
                    <small class="text-muted">Distribusi pendapatan per metode</small>
                </div>
                <div style="height:180px; position:relative;">
                    <canvas id="methodChart"></canvas>
                </div>
                <div class="mt-3">
                    @foreach($revenueByMethod as $m)
                    @php
                        $methodLabel = $m->payment_method === 'cash' ? 'Tunai' : 'Online (Midtrans)';
                        $methodIcon  = $m->payment_method === 'cash' ? 'ti-cash' : 'ti-credit-card';
                        $methodColor = $m->payment_method === 'cash' ? '#16a34a' : '#2563eb';
                    @endphp
                    <div class="d-flex align-items-center justify-content-between py-2" style="border-top: 1px solid #f3f4f6;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle d-inline-flex" style="width:8px;height:8px;background:{{ $methodColor }};flex-shrink:0;"></span>
                            <i class="ti {{ $methodIcon }} small" style="color:{{ $methodColor }};"></i>
                            <span class="small">{{ $methodLabel }}</span>
                        </div>
                        <div class="text-end">
                            <div class="fw-semibold small">Rp {{ number_format($m->total, 0, ',', '.') }}</div>
                            <div class="text-muted" style="font-size:.7rem;">{{ $m->jumlah }} transaksi</div>
                        </div>
                    </div>
                    @endforeach
                    @if($revenueByMethod->isEmpty())
                    <div class="text-center text-muted small py-3">Belum ada data</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- BREAKDOWN ROW --}}
<div class="row g-3 mb-4">
    {{-- Income Statement --}}
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-1">Ringkasan Keuangan</h6>
                <small class="text-muted d-block mb-4">Rincian pendapatan & biaya</small>

                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px dashed #e5e7eb;">
                    <span class="small text-muted">Subtotal Layanan</span>
                    <span class="fw-semibold small">Rp {{ number_format($summary->total_subtotal ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px dashed #e5e7eb;">
                    <span class="small text-muted d-flex align-items-center gap-1">
                        <i class="ti ti-discount-2 text-warning"></i> Total Diskon
                    </span>
                    <span class="fw-semibold small text-warning">– Rp {{ number_format($summary->total_diskon ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px dashed #e5e7eb;">
                    <span class="small text-muted d-flex align-items-center gap-1">
                        <i class="ti ti-receipt-tax text-info"></i> Pajak (PPN)
                    </span>
                    <span class="fw-semibold small text-info">+ Rp {{ number_format($summary->total_pajak ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px dashed #e5e7eb;">
                    <span class="small text-muted d-flex align-items-center gap-1">
                        <i class="ti ti-credit-card-refund text-secondary"></i> Biaya Admin
                    </span>
                    <span class="fw-semibold small text-secondary">+ Rp {{ number_format($summary->total_biaya_admin ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-3 mt-1" style="border-top:2px solid #e5e7eb;">
                    <span class="fw-bold" style="color:#16a34a;">Total Diterima</span>
                    <span class="fw-bold" style="color:#16a34a; font-size:1rem;">
                        Rp {{ number_format($summary->total_pendapatan ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Status --}}
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-1">Status Order</h6>
                <small class="text-muted d-block mb-4">Distribusi status pesanan</small>
                @php
                    $statusConfig = [
                        'pending'    => ['label' => 'Menunggu',   'color' => '#f59e0b', 'icon' => 'ti-clock'],
                        'processing' => ['label' => 'Diproses',   'color' => '#2563eb', 'icon' => 'ti-loader'],
                        'done'       => ['label' => 'Selesai',    'color' => '#16a34a', 'icon' => 'ti-circle-check'],
                        'delivered'  => ['label' => 'Terkirim',   'color' => '#0891b2', 'icon' => 'ti-truck-delivery'],
                        'cancelled'  => ['label' => 'Dibatalkan', 'color' => '#dc3545', 'icon' => 'ti-circle-x'],
                    ];
                    $totalOrder = $summary->total_transaksi ?? 0;
                @endphp
                @foreach($statusConfig as $key => $cfg)
                @php
                    $count = (int) ($orderStatusDist[$key]->jumlah ?? 0);
                    $pct   = $totalOrder > 0 ? round(($count / $totalOrder) * 100) : 0;
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small d-flex align-items-center gap-2">
                            <i class="ti {{ $cfg['icon'] }}" style="color:{{ $cfg['color'] }};"></i>
                            {{ $cfg['label'] }}
                        </span>
                        <span class="small fw-semibold">{{ $count }} <span class="text-muted fw-normal">({{ $pct }}%)</span></span>
                    </div>
                    <div class="progress" style="height:6px; border-radius:4px; background:#f3f4f6;">
                        <div class="progress-bar" style="width:{{ $pct }}%; background:{{ $cfg['color'] }}; border-radius:4px; transition: width .6s ease;"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Top Transactions --}}
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-1">Transaksi Terbesar</h6>
                <small class="text-muted d-block mb-3">5 transaksi nilai tertinggi</small>
                @forelse($topTransactions as $idx => $trx)
                <div class="d-flex align-items-center gap-3 {{ !$loop->last ? 'mb-3 pb-3' : '' }}"
                    style="{{ !$loop->last ? 'border-bottom:1px dashed #f3f4f6;' : '' }}">
                    <div class="d-flex align-items-center justify-content-center rounded-circle fw-bold text-white flex-shrink-0"
                        style="width:32px;height:32px;font-size:.75rem;
                            background: {{ ['linear-gradient(135deg,#f59e0b,#fbbf24)','linear-gradient(135deg,#9ca3af,#d1d5db)','linear-gradient(135deg,#b45309,#d97706)','linear-gradient(135deg,#2563eb,#60a5fa)','linear-gradient(135deg,#16a34a,#4ade80)'][$idx] }};">
                        {{ $idx + 1 }}
                    </div>
                    <div class="flex-fill min-width-0">
                        <div class="fw-semibold font-monospace text-primary" style="font-size:.72rem;">{{ $trx->invoice_number }}</div>
                        <div class="text-muted" style="font-size:.7rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $trx->customer_name }}
                        </div>
                    </div>
                    <div class="text-end flex-shrink-0">
                        <div class="fw-bold small" style="color:#16a34a;">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted small py-3">
                    <i class="ti ti-receipt-off fs-2 d-block mb-2"></i>
                    Belum ada transaksi
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- REVENUE BY SERVICE --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h6 class="fw-bold mb-0">Pendapatan per Layanan</h6>
                <small class="text-muted">Top 10 layanan berdasarkan nilai pendapatan</small>
            </div>
        </div>
        @php
            $maxRevenue = $revenueByService->max('total_revenue') ?: 1;
            $serviceColors = ['#16a34a','#2563eb','#7c3aed','#d97706','#0891b2','#dc2626','#db2777','#16a34a','#9333ea','#1d4ed8'];
        @endphp
        <div class="table-responsive">
            <table class="table table-borderless align-middle mb-0">
                <thead>
                    <tr style="border-bottom:2px solid #f3f4f6;">
                        <th class="text-muted fw-semibold small text-uppercase ps-0" style="letter-spacing:.5px; width:30px;">No</th>
                        <th class="text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Layanan</th>
                        <th class="text-muted fw-semibold small text-uppercase text-center" style="letter-spacing:.5px;">Qty</th>
                        <th class="text-muted fw-semibold small text-uppercase text-center" style="letter-spacing:.5px;">Transaksi</th>
                        <th class="text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px; min-width:200px;">Kontribusi</th>
                        <th class="text-muted fw-semibold small text-uppercase text-end pe-0" style="letter-spacing:.5px;">Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($revenueByService as $idx => $svc)
                    @php
                        $pct   = round(($svc->total_revenue / $maxRevenue) * 100);
                        $color = $serviceColors[$idx % count($serviceColors)];
                    @endphp
                    <tr style="border-bottom:1px solid #f9fafb;">
                        <td class="ps-0 text-muted small">{{ $idx + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="rounded-circle flex-shrink-0" style="width:8px;height:8px;background:{{ $color }};display:inline-block;"></span>
                                <span class="fw-medium small">{{ $svc->service_name }}</span>
                            </div>
                        </td>
                        <td class="text-center small text-muted">{{ number_format($svc->total_qty, 1) }}</td>
                        <td class="text-center small text-muted">{{ $svc->jumlah_transaksi }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="flex-fill" style="background:#f3f4f6;border-radius:4px;height:6px;">
                                    <div style="width:{{ $pct }}%;height:6px;background:{{ $color }};border-radius:4px;transition:width .6s ease;"></div>
                                </div>
                                <span class="text-muted" style="font-size:.7rem;min-width:30px;">{{ $pct }}%</span>
                            </div>
                        </td>
                        <td class="text-end pe-0 fw-semibold small" style="color:{{ $color }};">
                            Rp {{ number_format($svc->total_revenue, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="ti ti-ironing fs-2 d-block mb-2"></i>
                            Belum ada data layanan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($revenueByService->isNotEmpty())
                <tfoot>
                    <tr style="border-top:2px solid #e5e7eb;">
                        <td colspan="4" class="pt-3 ps-0 fw-bold small">Total Keseluruhan</td>
                        <td colspan="2" class="pt-3 pe-0 text-end fw-bold" style="color:#16a34a; font-size:.9rem;">
                            Rp {{ number_format($revenueByService->sum('total_revenue'), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

{{-- MONTHLY TREND --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h6 class="fw-bold mb-0">Tren Pendapatan 12 Bulan Terakhir</h6>
                <small class="text-muted">Grafik pendapatan bulanan setahun terakhir</small>
            </div>
        </div>
        <div style="height:200px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
    .period-btn { transition: all .2s ease; }
    .period-btn:hover:not(.btn-primary) { background: #f0f4ff !important; border-color: #2563eb !important; color: #2563eb !important; }
    .min-width-0 { min-width: 0; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Period selector ──────────────────────────────────────
    const periodBtns = document.querySelectorAll('.period-btn');
    const periodInput = document.getElementById('periodInput');
    const customRangeDiv = document.getElementById('customRangeDiv');

    periodBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            periodBtns.forEach(b => {
                b.classList.remove('btn-primary', 'text-white');
                b.classList.add('btn-light', 'border');
            });
            this.classList.add('btn-primary', 'text-white');
            this.classList.remove('btn-light', 'border');
            periodInput.value = this.dataset.period;
            customRangeDiv.style.display = this.dataset.period === 'custom' ? '' : 'none';
        });
    });

    // ── Revenue Chart ────────────────────────────────────────
    const chartLabels  = @json($chartData['labels'] ?? []);
    const chartRevenue = @json($chartData['revenues'] ?? []);
    const chartCounts  = @json($chartData['counts'] ?? []);

    const revenueCtx = document.getElementById('revenueChart').getContext('2d');

    const greenGrad = revenueCtx.createLinearGradient(0, 0, 0, 260);
    greenGrad.addColorStop(0, 'rgba(22,163,74,0.18)');
    greenGrad.addColorStop(1, 'rgba(22,163,74,0.01)');

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [
                {
                    label: 'Pendapatan (Rp)',
                    data: chartRevenue,
                    borderColor: '#16a34a',
                    backgroundColor: greenGrad,
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: '#16a34a',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y',
                },
                {
                    label: 'Jumlah Transaksi',
                    data: chartCounts,
                    borderColor: '#2563eb',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: false,
                    tension: 0.4,
                    borderDash: [4,2],
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            if (ctx.datasetIndex === 0) {
                                return ' Pendapatan: Rp ' + Number(ctx.raw).toLocaleString('id-ID');
                            }
                            return ' Transaksi: ' + ctx.raw;
                        }
                    }
                }
            },
            scales: {
                x: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 11 } } },
                y: {
                    position: 'left',
                    grid: { color: '#f3f4f6', drawBorder: false },
                    ticks: {
                        font: { size: 10 },
                        callback: v => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1) + 'jt' : (v >= 1000 ? (v/1000).toFixed(0) + 'rb' : v))
                    }
                },
                y1: {
                    position: 'right',
                    grid: { display: false },
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });

    // ── Method Donut Chart ───────────────────────────────────
    const methodData   = @json($revenueByMethod->pluck('total')->toArray());
    const methodLabels = @json($revenueByMethod->map(fn($m) => $m->payment_method === 'cash' ? 'Tunai' : 'Online')->toArray());

    if (methodData.length > 0) {
        const methodCtx = document.getElementById('methodChart').getContext('2d');
        new Chart(methodCtx, {
            type: 'doughnut',
            data: {
                labels: methodLabels,
                datasets: [{
                    data: methodData,
                    backgroundColor: ['#16a34a', '#2563eb', '#7c3aed', '#d97706'],
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' Rp ' + Number(ctx.raw).toLocaleString('id-ID')
                        }
                    }
                }
            }
        });
    }

    // ── Monthly Trend Bar Chart ───────────────────────────────
    const trendLabels  = @json($monthlyTrend->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m->bulan)->translatedFormat('M Y'))->toArray());
    const trendRevenue = @json($monthlyTrend->pluck('pendapatan')->toArray());

    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const barGrad = trendCtx.createLinearGradient(0, 0, 0, 200);
    barGrad.addColorStop(0, 'rgba(22,163,74,0.85)');
    barGrad.addColorStop(1, 'rgba(22,163,74,0.3)');

    new Chart(trendCtx, {
        type: 'bar',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Pendapatan',
                data: trendRevenue,
                backgroundColor: barGrad,
                borderRadius: 6,
                borderSkipped: false,
                hoverBackgroundColor: '#16a34a',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' Rp ' + Number(ctx.raw).toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                y: {
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        font: { size: 10 },
                        callback: v => v >= 1000000 ? 'Rp ' + (v/1000000).toFixed(1) + 'jt' :
                                       v >= 1000    ? 'Rp ' + (v/1000).toFixed(0) + 'rb' : 'Rp ' + v
                    }
                }
            }
        }
    });
});
</script>
@endpush

@endsection
