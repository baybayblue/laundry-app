@extends('customer.layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-12 col-md-6">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="font-size: .75rem;">
                <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Riwayat Transaksi</li>
            </ol>
        </nav>
        <h1 class="fs-4 mb-2 fw-bold text-dark">Riwayat Transaksi Saya</h1>
        <p class="text-muted mb-0 small">
            Daftar lengkap pesanan laundry Anda.
        </p>
    </div>
    <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('customer.orders.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm border-0 d-inline-flex align-items-center gap-2">
            <i class="ti ti-plus fs-5"></i>
            <span>Pesan Laundry</span>
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
    <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">Daftar Transaksi</h6>
        <span class="badge bg-light text-muted rounded-pill px-3 py-1 font-monospace small" style="font-size: .65rem;">
            {{ $transactions->total() }} TOTAL
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr class="bg-light">
                    <th class="ps-4 text-muted small py-3" style="font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">No. Invoice</th>
                    <th class="text-muted small py-3" style="font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Cucian</th>
                    <th class="text-muted small py-3" style="font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Nominal</th>
                    <th class="text-muted small py-3" style="font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                    <th class="pe-4 text-muted small py-3 text-end" style="font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold small text-primary mb-0">{{ $trx->invoice_number }}</div>
                        <small class="text-muted" style="font-size: .68rem;">{{ $trx->created_at->format('d/m/Y H:i') }}</small>
                    </td>
                    <td>
                        <div class="small fw-semibold text-dark">{{ $trx->items->count() }} Paket/Item</div>
                        <div class="text-muted text-truncate" style="max-width: 180px; font-size: .68rem;">
                            {{ $trx->items->pluck('service_name')->join(', ') ?: 'Sedang dicek admin' }}
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold small text-dark mb-1">{{ $trx->formatted_total }}</div>
                        <span class="badge bg-opacity-10 px-2 py-0 rounded-pill" 
                              style="background: {{ $trx->payment_status_color }}15; color: {{ $trx->payment_status_color }}; font-size: .62rem;">
                            {{ $trx->payment_status_label }}
                        </span>
                    </td>
                    <td>
                        <span class="badge border bg-white px-2 py-1 rounded-pill" 
                              style="color: {{ $trx->order_status_color }}; border-color: {{ $trx->order_status_color }}40 !important; font-size: .62rem; font-weight: 700;">
                            {{ $trx->order_status_label }}
                        </span>
                    </td>
                    <td class="pe-4 text-end">
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            @if(in_array($trx->payment_method, ['midtrans', null, '']) && $trx->payment_status === 'pending')
                                @if($trx->total_amount > 0)
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold pay-now-btn" 
                                        data-url="{{ route('customer.transactions.snap-token', $trx) }}"
                                        style="font-size: .68rem;">
                                    Bayar Sekarang
                                </button>
                                @else
                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-bold" 
                                        onclick="Swal.fire('Info', 'Tagihan sedang dihitung oleh admin.', 'info')"
                                        style="font-size: .68rem;">
                                    Bayar Sekarang
                                </button>
                                @endif
                            @endif
                            <a href="{{ route('customer.transactions.show', $trx) }}" class="btn btn-sm btn-icon rounded-circle bg-light border-0 d-inline-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                                <i class="ti ti-chevron-right text-primary"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="py-4">
                            <i class="ti ti-receipt-off fs-1 text-muted opacity-25 mb-3 d-block"></i>
                            <h6 class="fw-bold text-muted mb-0">Belum Ada Transaksi</h6>
                            <p class="small text-muted">Riwayat laundry Anda akan muncul di sini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($transactions->hasPages())
    <div class="card-footer bg-white border-top py-3 px-4">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0 d-none d-md-block">
                <p class="text-muted mb-0 small">Menampilkan {{ $transactions->firstItem() }} ke {{ $transactions->lastItem() }} dari {{ $transactions->total() }} data</p>
            </div>
            <div class="col-md-6  pagination-sm text-md-end">
                <div class="d-flex justify-content-center justify-content-md-end">
                    {{ $transactions->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

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
                    if (!res.ok) throw new Error('Gagal mengambil token pembayaran.');
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
@endsection

