@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 fw-bold text-dark">Dashboard Karyawan</h1>
        <p class="text-muted mb-0 small">Selamat datang, <strong>{{ auth()->user()->name }}</strong> &bull; {{ now()->format('l, d F Y') }}</p>
    </div>
    <a href="{{ route('employee.transactions.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="ti ti-plus"></i> Tambah Transaksi
    </a>
</div>

{{-- Stats Cards --}}
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #0d6efd, #0a58ca);">
            <div class="card-body p-4 text-white">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-white bg-opacity-25 rounded-3 p-2">
                        <i class="ti ti-receipt fs-4"></i>
                    </div>
                    <span class="badge bg-white bg-opacity-25 text-white">Hari Ini</span>
                </div>
                <h2 class="fw-bold mb-1">{{ $todayTransactions }}</h2>
                <p class="mb-0 opacity-75 small">Transaksi hari ini</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #198754, #157347);">
            <div class="card-body p-4 text-white">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-white bg-opacity-25 rounded-3 p-2">
                        <i class="ti ti-checks fs-4"></i>
                    </div>
                    <span class="badge bg-white bg-opacity-25 text-white">Total</span>
                </div>
                <h2 class="fw-bold mb-1">{{ $totalTransactions }}</h2>
                <p class="mb-0 opacity-75 small">Total transaksi dibuat</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #fd7e14, #e67300);">
            <div class="card-body p-4 text-white">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-white bg-opacity-25 rounded-3 p-2">
                        <i class="ti ti-clock fs-4"></i>
                    </div>
                    <span class="badge bg-white bg-opacity-25 text-white">Aktif</span>
                </div>
                <h2 class="fw-bold mb-1">{{ $pendingTransactions }}</h2>
                <p class="mb-0 opacity-75 small">Transaksi sedang diproses</p>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row g-4 mb-4">
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="ti ti-bolt text-primary"></i> Aksi Cepat
                </h5>
                <div class="d-grid gap-3">
                    <a href="{{ route('employee.transactions.create') }}" class="btn btn-primary d-flex align-items-center gap-3 p-3 rounded-3 text-start">
                        <div class="bg-white bg-opacity-25 rounded-2 p-2">
                            <i class="ti ti-plus fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">Tambah Data Laundry</div>
                            <div class="small opacity-75">Buat transaksi laundry baru</div>
                        </div>
                    </a>
                    <a href="{{ route('employee.transactions.index') }}" class="btn btn-light border d-flex align-items-center gap-3 p-3 rounded-3 text-start">
                        <div class="bg-primary bg-opacity-10 rounded-2 p-2">
                            <i class="ti ti-list fs-4 text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">Lihat Data Laundry</div>
                            <div class="small text-muted">Kelola transaksi yang sudah dibuat</div>
                        </div>
                    </a>
                    <a href="{{ route('employee.attendances.clock') }}" class="btn btn-light border d-flex align-items-center gap-3 p-3 rounded-3 text-start">
                        <div class="bg-success bg-opacity-10 rounded-2 p-2">
                            <i class="ti ti-alarm fs-4 text-success"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">Presensi Sekarang</div>
                            <div class="small text-muted">Catat kehadiran hari ini</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="ti ti-history text-primary"></i> Transaksi Terakhir
                    </h5>
                    <a href="{{ route('employee.transactions.index') }}" class="text-primary small">Lihat semua</a>
                </div>
                @forelse($recentTransactions as $trx)
                <div class="d-flex align-items-center gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:38px;height:38px;background:{{ $trx->order_status_color }}20;">
                        <i class="ti ti-arrow-up-right fs-5" style="color:{{ $trx->order_status_color }};"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold small text-truncate">{{ $trx->invoice_number }}</div>
                        <div class="small text-muted text-truncate">{{ $trx->customer_name }}</div>
                    </div>
                    <div class="text-end flex-shrink-0">
                        <div class="small fw-semibold">{{ $trx->formatted_total }}</div>
                        <span class="badge rounded-pill" style="background:{{ $trx->order_status_color }}20;color:{{ $trx->order_status_color }};font-size:10px;">
                            {{ $trx->order_status_label }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="ti ti-receipt-off fs-2 d-block mb-2 opacity-50"></i>
                    <small>Belum ada transaksi</small>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
