@extends('customer.layouts.app')

@section('title', 'Dashboard Pelanggan')

@section('content')
<div class="row align-items-center mb-4 text-dark">
    <div class="col-12 col-md-6">
        <h1 class="fs-4 mb-2 fw-bold">Dashboard</h1>
        <p class="text-muted mb-0 small">
            Selamat datang kembali, <strong>{{ Auth::guard('customer')->user()->name }}</strong>! 👋
        </p>
    </div>
    <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('customer.orders.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4 py-2">
            <i class="ti ti-plus fs-5"></i> Pesan Laundry
        </a>
    </div>
</div>

{{-- KPI STATS --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" 
             style="background: linear-gradient(135deg, #0d6efd, #0a58ca);">
            <div class="card-body p-4 position-relative">
                <div class="mb-3">
                    <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                        <i class="ti ti-receipt text-white fs-4"></i>
                    </div>
                </div>
                <h6 class="text-white text-opacity-75 small fw-medium mb-1">Total Laundry</h6>
                <h3 class="text-white fw-bold mb-0">{{ $totalTransactions }} Pesanan</h3>
                <div class="position-absolute bottom-0 end-0 opacity-10 mb-n2 me-n2">
                    <i class="ti ti-receipt-2" style="font-size: 5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" 
             style="background: linear-gradient(135deg, #198754, #157347);">
            <div class="card-body p-4 position-relative">
                <div class="mb-3">
                    <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                        <i class="ti ti-wallet text-white fs-4"></i>
                    </div>
                </div>
                <h6 class="text-white text-opacity-75 small fw-medium mb-1">Total Pengeluaran</h6>
                <h3 class="text-white fw-bold mb-0">Rp {{ number_format($totalSpending, 0, ',', '.') }}</h3>
                <div class="position-absolute bottom-0 end-0 opacity-10 mb-n2 me-n2">
                    <i class="ti ti-wallet" style="font-size: 5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" 
             style="background: linear-gradient(135deg, #fd7e14, #d9480f);">
            <div class="card-body p-4 position-relative">
                <div class="mb-3">
                    <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                        <i class="ti ti-shirt text-white fs-4"></i>
                    </div>
                </div>
                <h6 class="text-white text-opacity-75 small fw-medium mb-1">Status Terakhir</h6>
                <h3 class="text-white fw-bold mb-0 text-truncate">
                    {{ $lastTransaction ? $lastTransaction->order_status_label : 'Belum Ada Aktivitas' }}
                </h3>
                <div class="position-absolute bottom-0 end-0 opacity-10 mb-n2 me-n2">
                    <i class="ti ti-package" style="font-size: 5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- ACTIVE ORDERS --}}
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">Pesanan Aktif</h5>
                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-1 small">{{ $activeTransactions->count() }} Sedang Diproses</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th class="ps-4 text-muted small py-3">No. Invoice</th>
                                <th class="text-muted small py-3">Layanan</th>
                                <th class="text-muted small py-3">Total</th>
                                <th class="text-muted small py-3">Status</th>
                                <th class="pe-4 text-muted small py-3 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeTransactions as $trx)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold small text-dark">{{ $trx->invoice_number }}</div>
                                    <small class="text-muted">{{ $trx->created_at->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <div class="text-muted text-truncate" style="max-width:180px; font-size: .75rem;">
                                        {{ $trx->items->pluck('service_name')->join(', ') ?: 'Sedang dicek admin' }}
                                    </div>
                                </td>
                                <td class="fw-bold small text-dark">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-opacity-10 px-2 py-1 rounded-pill" 
                                          style="background: {{ $trx->order_status_color }}15; color: {{ $trx->order_status_color }}; font-size: .7rem;">
                                        {{ $trx->order_status_label }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        @if(in_array($trx->payment_method, ['midtrans', null, '']) && $trx->payment_status === 'pending')
                                            @if($trx->total_amount > 0)
                                            <button class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold pay-now-btn" 
                                                    data-url="{{ route('customer.transactions.snap-token', $trx) }}"
                                                    style="font-size: .7rem;">
                                                <i class="ti ti-credit-card me-1"></i>Bayar Sekarang
                                            </button>
                                            @else
                                            <button class="btn btn-sm btn-secondary rounded-pill px-3 py-1 fw-bold bg-opacity-75" 
                                                    onclick="Swal.fire('Info', 'Tagihan sedang dihitung oleh admin.', 'info')"
                                                    style="font-size: .7rem;">
                                                <i class="ti ti-wallet me-1"></i>Bayar Sekarang
                                            </button>
                                            @endif
                                        @endif
                                        <a href="{{ route('customer.transactions.show', $trx) }}" class="btn btn-sm btn-icon rounded-circle bg-light border-0 d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                            <i class="ti ti-eye text-primary"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="py-3">
                                        <i class="ti ti-package-off fs-1 text-muted opacity-50 d-block mb-3"></i>
                                        <h6 class="fw-bold text-muted">Tidak Ada Pesanan Aktif</h6>
                                        <p class="small text-muted mb-3">Pesananmu akan muncul di sini saat sedang diproses.</p>
                                        <a href="{{ route('customer.orders.create') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Mulai Pesan</a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- RECENT ACTIVITY --}}
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold mb-0 text-dark">Riwayat Terakhir</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($recentTransactions as $trx)
                    <li class="list-group-item d-flex align-items-center justify-content-between py-3 px-4 border-light transition-all hover-bg-light">
                        <div>
                            <div class="fw-bold small text-dark">{{ $trx->invoice_number }}</div>
                            <div class="text-muted" style="font-size:.72rem;">{{ $trx->created_at->format('d M Y, H:i') }}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold small text-dark mb-1">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</div>
                            @if(in_array($trx->payment_method, ['midtrans', null, '']) && $trx->payment_status === 'pending')
                                @if($trx->total_amount > 0)
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 fw-bold pay-now-btn" 
                                        data-url="{{ route('customer.transactions.snap-token', $trx) }}"
                                        style="font-size: .65rem;">
                                    Bayar
                                </button>
                                @else
                                <button class="btn btn-sm border-0 text-muted rounded-pill px-2 py-0 fw-bold" 
                                        onclick="Swal.fire('Info', 'Tagihan sedang dihitung oleh admin.', 'info')"
                                        style="font-size: .65rem;">
                                    Bayar
                                </button>
                                @endif
                            @else
                                <span class="badge bg-opacity-10 px-2 py-0 rounded-pill" 
                                      style="background: {{ $trx->payment_status_color }}15; color: {{ $trx->payment_status_color }}; font-size: .65rem;">
                                    {{ $trx->payment_status_label }}
                                </span>
                            @endif
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center py-4 text-muted small">
                        Belum ada riwayat transaksi.
                    </li>
                    @endforelse
                </ul>
                <div class="p-3">
                    <a href="{{ route('customer.transactions.index') }}" class="btn btn-light w-100 rounded-pill fw-bold small py-2 bg-opacity-50 border text-muted">Lihat Semua Riwayat</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const payNowBtns = document.querySelectorAll('.pay-now-btn');
        payNowBtns.forEach(btn => {
            btn.addEventListener('click', async function() {
                const endpoint = this.dataset.url;
                const originalText = this.innerHTML;
                
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                
                try {
                    const res = await fetch(endpoint);
                    const data = await res.json();
                    
                    if (data.snap_token) {
                        snap.pay(data.snap_token, {
                            onSuccess: () => window.location.reload(),
                            onPending: () => window.location.reload(),
                            onError: () => Swal.fire('Error', 'Pembayaran gagal.', 'error'),
                            onClose: () => { 
                                this.disabled = false; 
                                this.innerHTML = originalText;
                            }
                        });
                    } else {
                        throw new Error(data.error || 'Gagal membuat sesi pembayaran.');
                    }
                } catch (e) {
                    Swal.fire('Gagal', e.message, 'error');
                    this.disabled = false;
                    this.innerHTML = originalText;
                }
            });
        });
    });
</script>
@endpush

