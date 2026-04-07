@extends('layouts.app')

@section('content')

{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                style="background: linear-gradient(135deg, #2563eb20, #2563eb10); border: 1px solid #2563eb30; width:48px; height:48px;">
                <i class="ti ti-receipt-2 fs-3 text-primary"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Daftar Transaksi</h1>
                <p class="mb-0 text-muted small">Kelola semua order dan pembayaran laundry</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end d-flex gap-2 justify-content-md-end flex-wrap">
        <a href="{{ route('admin.transactions.export', request()->query()) }}"
            class="btn btn-light d-inline-flex align-items-center gap-2 rounded-pill px-3 border shadow-sm"
            style="font-size:.85rem; color:#198754;">
            <i class="ti ti-file-spreadsheet"></i> Export CSV
        </a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.transactions.create') }}"
            class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
            <i class="ti ti-plus fs-5"></i> Buat Transaksi
        </a>
        @endif
    </div>
</div>

{{-- STATS CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #2563eb15, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Total Order</span>
                    <div class="bg-primary bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-receipt-2 text-primary fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-primary">{{ number_format($stats['total']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #fd7e1415, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Menunggu</span>
                    <div class="bg-warning bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-clock text-warning fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-warning">{{ number_format($stats['pending']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #6f42c115, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Diproses</span>
                    <div class="rounded-2 p-1" style="background:#6f42c115;">
                        <i class="ti ti-loader fs-5" style="color:#6f42c1;"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold" style="color:#6f42c1;">{{ number_format($stats['processing']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #19875415, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Revenue (Lunas)</span>
                    <div class="bg-success bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-wallet text-success fs-5"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold text-success" style="font-size:1.1rem !important;">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</div>
                <div class="text-muted small mt-1">{{ $stats['paid'] }} transaksi lunas</div>
            </div>
        </div>
    </div>
</div>

{{-- ACTIVE FILTER BADGES --}}
@php
    $orderStatusMap   = ['pending'=>'Menunggu','processing'=>'Diproses','done'=>'Selesai','delivered'=>'Terkirim','cancelled'=>'Dibatalkan','cancel_requested'=>'Pengajuan Batal'];
    $paymentStatusMap = ['pending'=>'Belum Bayar','paid'=>'Lunas','failed'=>'Gagal','expired'=>'Expired'];
    $paymentMethodMap = ['cash'=>'Tunai','midtrans'=>'Online'];

    $activeFilters = array_filter([
        'search'         => request('search'),
        'order_status'   => request('order_status'),
        'payment_status' => request('payment_status'),
        'payment_method' => request('payment_method'),
        'date_from'      => request('date_from'),
        'date_to'        => request('date_to'),
    ]);

    $filterLabels = [];
    if (request('search'))         $filterLabels['search']         = 'Cari: "' . request('search') . '"';
    if (request('order_status'))   $filterLabels['order_status']   = 'Status: ' . ($orderStatusMap[request('order_status')]   ?? request('order_status'));
    if (request('payment_status')) $filterLabels['payment_status'] = 'Bayar: '  . ($paymentStatusMap[request('payment_status')] ?? request('payment_status'));
    if (request('payment_method')) $filterLabels['payment_method'] = 'Metode: ' . ($paymentMethodMap[request('payment_method')] ?? request('payment_method'));
    if (request('date_from'))      $filterLabels['date_from']      = 'Dari: '   . request('date_from');
    if (request('date_to'))        $filterLabels['date_to']        = 'S/d: '    . request('date_to');
@endphp

@if(count($activeFilters))
<div class="d-flex align-items-center gap-2 flex-wrap mb-3">
    <span class="text-muted small fw-medium"><i class="ti ti-filter me-1"></i>Filter aktif:</span>
    @foreach($activeFilters as $key => $val)
    <a href="{{ route('admin.transactions.index', array_diff_key(request()->query(), [$key => ''])) }}"
        class="badge rounded-pill d-inline-flex align-items-center gap-1 px-3 py-2 text-decoration-none"
        style="background:#2563eb15;color:#2563eb;border:1px solid #2563eb30;font-size:.72rem;">
        {{ $filterLabels[$key] ?? $val }}
        <i class="ti ti-x" style="font-size:.6rem;"></i>
    </a>
    @endforeach
    <a href="{{ route('admin.transactions.index') }}" class="text-muted small text-decoration-none">
        <i class="ti ti-x me-1"></i>Hapus semua
    </a>
</div>
@endif

{{-- SEARCH & FILTER BAR --}}
<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.transactions.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                        placeholder="Invoice, Nama, atau HP..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select name="order_status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach(['pending'=>'Menunggu','processing'=>'Diproses','done'=>'Selesai','delivered'=>'Terkirim','cancelled'=>'Dibatalkan','cancel_requested'=>'Pengajuan Batal'] as $v=>$l)
                    <option value="{{ $v }}" {{ request('order_status')===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="payment_status" class="form-select form-select-sm">
                    <option value="">Semua Bayar</option>
                    @foreach(['pending'=>'Belum Bayar','paid'=>'Lunas','failed'=>'Gagal','expired'=>'Expired'] as $v=>$l)
                    <option value="{{ $v }}" {{ request('payment_status')===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="payment_method" class="form-select form-select-sm">
                    <option value="">Semua Metode</option>
                    <option value="cash" {{ request('payment_method')==='cash'?'selected':'' }}>Tunai</option>
                    <option value="midtrans" {{ request('payment_method')==='midtrans'?'selected':'' }}>Online</option>
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-1">
                <input type="date" name="date_from" class="form-control form-control-sm"
                    value="{{ request('date_from') }}" title="Dari tanggal" style="min-width:0;">
                <input type="date" name="date_to" class="form-control form-control-sm"
                    value="{{ request('date_to') }}" title="Sampai tanggal" style="min-width:0;">
            </div>
            <div class="col-12 col-md-1 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm text-white flex-fill rounded-pill">
                    <i class="ti ti-filter me-1"></i>Filter
                </button>
                @if(count($activeFilters))
                <a href="{{ route('admin.transactions.index') }}" class="btn btn-light btn-sm rounded-pill" title="Reset">
                    <i class="ti ti-x"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- DATA TABLE --}}
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr style="background: linear-gradient(90deg, #f0f4ff, #e8eeff); border-bottom: 2px solid #e9ecef;">
                        <th class="py-3 ps-4 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px; width:50px;">No</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Invoice</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Pelanggan</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Layanan</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Total</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Status</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Bayar</th>
                        <th class="py-3 pe-4 text-end text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $index => $trx)
                    <tr class="trx-row" style="transition: background .15s; cursor:pointer;"
                        onclick="window.location='{{ route('admin.transactions.show', $trx) }}'">
                        <td class="ps-4 text-muted fw-medium small">
                            {{ $transactions->firstItem() + $index }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width:38px;height:38px;background:linear-gradient(135deg,#2563eb,#60a5fa);">
                                    <i class="ti ti-receipt text-white" style="font-size:.9rem;"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-primary font-monospace" style="font-size:.75rem;">{{ $trx->invoice_number }}</div>
                                    <small class="text-muted">{{ $trx->created_at->format('d M Y, H:i') }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 fw-bold text-white"
                                    style="width:34px;height:34px;font-size:.85rem;background:linear-gradient(135deg,#2563eb,#60a5fa);">
                                    {{ strtoupper(substr($trx->customer_name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold" style="font-size:.85rem;">{{ $trx->customer_name }}</h6>
                                    @if($trx->customer_phone)
                                    <small class="text-muted">{{ $trx->customer_phone }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-medium small">{{ $trx->items->count() }} Item</div>
                            <div class="text-muted text-truncate" style="max-width:140px;font-size:.72rem;">
                                {{ $trx->items->pluck('service_name')->join(', ') }}
                            </div>
                        </td>
                        <td>
                            <span class="fw-semibold text-dark small">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</span>
                            <small class="text-muted d-block" style="font-size:.7rem;">
                                @if($trx->payment_method === 'midtrans')
                                <i class="ti ti-credit-card me-1"></i>Online
                                @else
                                <i class="ti ti-cash me-1"></i>Tunai
                                @endif
                            </small>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3 small @php
                                $statusClass = match($trx->order_status) {
                                    'pending' => 'warning',
                                    'processing' => 'primary',
                                    'done', 'delivered' => 'success',
                                    'cancelled' => 'danger',
                                    'cancel_requested' => 'dark',
                                    default => 'secondary'
                                };
                            @endphp bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} border border-{{ $statusClass }} border-opacity-25">
                                {{ $trx->order_status_label }}
                            </span>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3 small bg-{{ $trx->payment_status === 'paid' ? 'success' : ($trx->payment_status === 'pending' ? 'warning' : 'danger') }} bg-opacity-10 text-{{ $trx->payment_status === 'paid' ? 'success' : ($trx->payment_status === 'pending' ? 'warning' : 'danger') }} border border-{{ $trx->payment_status === 'paid' ? 'success' : ($trx->payment_status === 'pending' ? 'warning' : 'danger') }} border-opacity-25">
                                {{ $trx->payment_status_label }}
                            </span>
                        </td>
                        <td class="pe-4 text-end" onclick="event.stopPropagation()">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                @if($trx->payment_method === 'midtrans' && $trx->payment_status === 'pending')
                                <a href="{{ route('admin.transactions.show', $trx) }}"
                                    class="btn btn-sm rounded-pill px-2 py-1"
                                    style="background:rgba(13, 110, 253, 0.1); color:#0d6efd; font-size:.7rem; border:1px solid rgba(13, 110, 253, 0.25);">
                                    <i class="ti ti-credit-card me-1"></i>Bayar
                                </a>
                                @endif
                                <a href="{{ route('admin.transactions.show', $trx) }}"
                                    class="btn btn-sm btn-icon rounded-2 border"
                                    style="width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; color:#475569;"
                                    title="Detail">
                                    <i class="ti ti-eye fs-6"></i>
                                </a>
                                <a href="{{ route('admin.transactions.invoice', $trx) }}" target="_blank"
                                    class="btn btn-sm btn-icon rounded-2 border text-success"
                                    style="width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center;"
                                    title="Cetak Invoice">
                                    <i class="ti ti-printer fs-6"></i>
                                </a>
                                @if($trx->payment_status !== 'paid' && auth()->user()->isAdmin())
                                <form action="{{ route('admin.transactions.destroy', $trx) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-icon rounded-2 border text-danger"
                                        style="width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center;"
                                        data-name="{{ $trx->invoice_number }}" title="Hapus">
                                        <i class="ti ti-trash fs-6"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="py-4">
                                <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle"
                                    style="width:72px; height:72px; background: linear-gradient(135deg, #e9ecef, #f8f9fa);">
                                    <i class="ti ti-receipt-off fs-1 text-muted"></i>
                                </div>
                                <h5 class="fw-semibold text-muted mb-1">
                                    {{ count($activeFilters) ? 'Tidak Ada Hasil' : 'Belum Ada Transaksi' }}
                                </h5>
                                <p class="small text-muted mb-3">
                                    {{ count($activeFilters) ? 'Coba ubah filter pencarian Anda.' : 'Buat transaksi pertama untuk memulai.' }}
                                </p>
                                @if(count($activeFilters))
                                <a href="{{ route('admin.transactions.index') }}" class="btn btn-light btn-sm rounded-pill px-4">
                                    <i class="ti ti-x me-1"></i>Reset Filter
                                </a>
                                @else
                                <a href="{{ route('admin.transactions.create') }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                    <i class="ti ti-plus me-1"></i>Buat Transaksi
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION FOOTER --}}
    @if($transactions->hasPages())
    <div class="card-footer border-top-0 bg-transparent px-4 py-3">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
            <div class="text-muted small">
                Menampilkan <span class="fw-semibold text-dark">{{ $transactions->firstItem() }}</span>
                – <span class="fw-semibold text-dark">{{ $transactions->lastItem() }}</span>
                dari <span class="fw-semibold text-dark">{{ $transactions->total() }}</span> transaksi
            </div>
            <div>{{ $transactions->appends(request()->query())->links('vendor.pagination.custom') }}</div>
        </div>
    </div>
    @else
    <div class="card-footer border-top-0 bg-transparent px-4 py-2">
        <p class="text-muted small mb-0">Menampilkan <span class="fw-semibold text-dark">{{ $transactions->count() }}</span> transaksi</p>
    </div>
    @endif
</div>

@push('scripts')
<style>
    .trx-row:hover { background: #f0f4ff !important; }
    .btn-icon { transition: transform .15s, box-shadow .15s; }
    .btn-icon:hover { transform: translateY(-1px); box-shadow: 0 3px 8px rgba(0,0,0,.1); }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            const name = this.dataset.name;
            const form = this.closest('form');
            Swal.fire({
                title: 'Hapus Transaksi?',
                html: `Invoice <strong>${name}</strong> akan dihapus permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="ti ti-trash me-1"></i>Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
            }).then(result => { if (result.isConfirmed) form.submit(); });
        });
    });
});
</script>
@endpush

@endsection
