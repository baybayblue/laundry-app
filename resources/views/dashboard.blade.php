@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-12 col-md-6">
        <h1 class="fs-3 mb-1 fw-bold">Dashboard</h1>
        <p class="text-muted mb-0 small">
            Selamat datang kembali, <strong>{{ auth()->user()->name }}</strong>!
            @if(auth()->user()->isOwner())
            <span class="badge rounded-pill ms-1" style="background:#f59e0b15;color:#b45309;border:1px solid #f59e0b30;font-size:.7rem;">Owner</span>
            @elseif(auth()->user()->isAdmin())
            <span class="badge rounded-pill ms-1" style="background:#dc354515;color:#dc3545;border:1px solid #dc354530;font-size:.7rem;">Admin</span>
            @endif
        </p>
    </div>
    <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
        <div class="d-inline-flex align-items-center gap-2 bg-white p-2 rounded-3 shadow-sm border px-3">
            <i class="ti ti-calendar text-primary"></i>
            <span class="small fw-medium">{{ Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>
</div>

{{-- KPI STATS --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" 
             style="background: linear-gradient(135deg, #2563eb, #3b82f6);">
            <div class="card-body p-4 position-relative">
                <div class="mb-3">
                    <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                        <i class="ti ti-currency-dollar text-white fs-4"></i>
                    </div>
                </div>
                <h6 class="text-white text-opacity-75 small fw-medium mb-1">Total Pendapatan</h6>
                <h3 class="text-white fw-bold mb-2">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                <div class="small {{ $salesTrend >= 0 ? 'text-white' : 'text-danger' }}">
                    <i class="ti ti-arrow-{{ $salesTrend >= 0 ? 'up' : 'down' }}-right me-1"></i>
                    {{ number_format(abs($salesTrend), 1) }}% <span class="text-white text-opacity-50">vs Kemarin</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: #fff;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                        <i class="ti ti-shopping-cart text-success fs-4"></i>
                    </div>
                    @if($todayOrders > 0)
                        <span class="badge rounded-pill bg-success small">+{{ $todayOrders }} Baru</span>
                    @endif
                </div>
                <h6 class="text-muted small fw-medium mb-1">Order Hari Ini</h6>
                <h3 class="fw-bold mb-0">{{ $todayOrders }}</h3>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: #fff;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                        <i class="ti ti-users text-info fs-4"></i>
                    </div>
                    <a href="{{ route('admin.attendances.index') }}" class="text-info small text-decoration-none">Detail</a>
                </div>
                <h6 class="text-muted small fw-medium mb-1">Karyawan Hadir</h6>
                <h3 class="fw-bold mb-0">{{ $todayAttendance }}</h3>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: #fff;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                        <i class="ti ti-package text-warning fs-4"></i>
                    </div>
                    @if($lowStockCount > 0)
                        <span class="badge rounded-pill bg-danger small">{{ $lowStockCount }} Kritis</span>
                    @endif
                </div>
                <h6 class="text-muted small fw-medium mb-1">Stok Hampir Habis</h6>
                <h3 class="fw-bold mb-0">{{ $lowStockCount }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- REVENUE CHART --}}
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Tren Pendapatan 7 Hari</h5>
                <button class="btn btn-sm btn-light border rounded-pill px-3">
                    <i class="ti ti-download me-1"></i> Export
                </button>
            </div>
            <div class="card-body p-4">
                <canvas id="revenueChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>

    {{-- ORDER STATUS --}}
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-4 px-4">
                <h5 class="fw-bold mb-1">Status Pesanan</h5>
                <p class="text-muted small mb-0">Distribusi status laundry saat ini</p>
            </div>
            <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center text-center">
                <div style="height: 200px; width: 200px;">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="mt-4 w-100">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted"><i class="ti ti-circle-filled text-warning me-1"></i> Menunggu</span>
                        <span class="fw-bold small">{{ $orderStatus['pending'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted"><i class="ti ti-circle-filled text-primary me-1"></i> Proses</span>
                        <span class="fw-bold small">{{ $orderStatus['processing'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="small text-muted"><i class="ti ti-circle-filled text-success me-1"></i> Selesai</span>
                        <span class="fw-bold small">{{ $orderStatus['done'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- RECENT TRANSACTIONS --}}
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Transaksi Terbaru</h5>
                <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th class="ps-4 text-muted small py-3">Pelanggan</th>
                                <th class="text-muted small py-3">Total</th>
                                <th class="text-muted small py-3">Status</th>
                                <th class="pe-4 text-muted small py-3 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $trx)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2 text-decoration-none">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold" style="width:34px; height:34px;">
                                            {{ substr($trx->customer_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold small text-dark">{{ $trx->customer_name }}</div>
                                            <div class="text-muted" style="font-size: .75rem;">#{{ $trx->invoice_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-bold small text-dark">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $statusColor = match($trx->order_status) {
                                            'pending' => 'warning',
                                            'processing' => 'primary',
                                            'done' => 'success',
                                            'delivered' => 'info',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }} small px-2 py-1 rounded-pill">
                                        {{ ucfirst($trx->order_status) }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        @if($trx->payment_method === 'midtrans' && $trx->payment_status === 'pending')
                                        <a href="{{ route('admin.transactions.show', $trx) }}"
                                            class="btn btn-sm rounded-pill px-2 py-1"
                                            style="background:rgba(13, 110, 253, 0.1); color:#0d6efd; font-size:.7rem; border:1px solid rgba(13, 110, 253, 0.25);">
                                            <i class="ti ti-credit-card me-1"></i>Bayar
                                        </a>
                                        @endif
                                        <a href="{{ route('admin.transactions.show', $trx) }}" class="btn btn-sm btn-icon rounded-circle bg-light border-0">
                                            <i class="ti ti-eye text-primary"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT PANEL: Owner/Admin sees pending payments --}}
    <div class="col-12 col-lg-4">
        {{-- PENDING PAYMENTS --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0" style="font-size:1rem;">Tagihan Pending</h5>
                <span class="badge rounded-pill" style="background:#fd7e1415;color:#fd7e14;border:1px solid #fd7e1430;">
                    {{ $pendingPaymentTransactions->count() }} Transaksi
                </span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($pendingPaymentTransactions as $trx)
                    <li class="list-group-item d-flex align-items-center justify-content-between py-3 px-4">
                        <div>
                            <div class="fw-semibold small">{{ $trx->customer_name }}</div>
                            <div class="text-muted" style="font-size:.72rem;">#{{ $trx->invoice_number }}</div>
                        </div>
                        <div class="text-end d-flex flex-column align-items-end gap-1">
                            <div class="fw-bold small text-primary">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</div>
                            <a href="{{ route('admin.transactions.show', $trx) }}"
                                class="btn btn-sm rounded-pill px-2 py-0"
                                style="background:rgba(13, 110, 253, 0.1); color:#0d6efd; font-size:.68rem; border:1px solid rgba(13, 110, 253, 0.25);">
                                <i class="ti ti-credit-card me-1"></i>Bayar Skg
                            </a>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center py-4 text-success small">
                        <i class="ti ti-check-double me-1"></i> Tidak ada tagihan pending.
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueLabels = @json($revenueData->pluck('date'));
        const revenueValues = @json($revenueData->pluck('total'));
        
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Pendapatan',
                    data: revenueValues,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { borderDash: [5, 5] },
                        ticks: {
                            callback: function(value) { return 'Rp ' + value.toLocaleString(); }
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusData = {
            labels: ['Menunggu', 'Proses', 'Selesai'],
            datasets: [{
                data: [
                    {{ $orderStatus['pending'] ?? 0 }},
                    {{ $orderStatus['processing'] ?? 0 }},
                    {{ $orderStatus['done'] ?? 0 }}
                ],
                backgroundColor: ['#f59e0b', '#2563eb', '#10b981'],
                borderWidth: 0,
                cutout: '70%'
            }]
        };
        
        new Chart(statusCtx, {
            type: 'doughnut',
            data: statusData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endpush
