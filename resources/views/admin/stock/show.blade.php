@extends('layouts.app')

@section('content')
{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            @if($stock->photo)
                <img src="{{ asset('storage/' . $stock->photo) }}" alt="" class="rounded-3 shadow-sm border flex-shrink-0" style="width:52px;height:52px;object-fit:cover;">
            @else
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:52px;height:52px;background:linear-gradient(135deg,#fd7e14,#ffc107);">
                    <i class="ti ti-package text-white fs-3"></i>
                </div>
            @endif
            <div>
                <h1 class="fs-3 mb-0 fw-bold">{{ $stock->name }}</h1>
                <p class="mb-0 text-muted small">Riwayat pergerakan stok barang</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end d-flex gap-2 justify-content-md-end">
        <a href="{{ route('admin.stock.edit', $stock) }}" class="btn btn-warning text-white d-inline-flex align-items-center gap-2 rounded-pill px-3 shadow-sm">
            <i class="ti ti-edit"></i> Edit
        </a>
        <a href="{{ route('admin.stock.index') }}" class="btn btn-light d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- DETAIL CARDS --}}
<div class="row g-3 mb-4">
    {{-- Info Barang --}}
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3 d-flex align-items-center gap-2">
                    <i class="ti ti-package text-warning"></i> Detail Barang
                </h6>
                @php $statusCfg = ['aman' => ['class'=>'success','icon'=>'ti-circle-check'], 'menipis' => ['class'=>'warning','icon'=>'ti-alert-triangle'], 'habis' => ['class'=>'danger','icon'=>'ti-x']][$stock->stockStatus()]; @endphp
                <div class="d-flex flex-column gap-2 small">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Kategori</span>
                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2">{{ $stock->category }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Satuan</span>
                        <span class="fw-semibold">{{ $stock->unit }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Stok Sekarang</span>
                        <span class="badge bg-{{ $statusCfg['class'] }} bg-opacity-10 text-{{ $statusCfg['class'] }} border border-{{ $statusCfg['class'] }} border-opacity-25 rounded-pill fw-bold">
                            {{ $stock->stock }} {{ $stock->unit }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Min. Stok</span>
                        <span class="fw-semibold">{{ $stock->min_stock }} {{ $stock->unit }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Harga/Satuan</span>
                        <span class="fw-semibold">{{ $stock->price_per_unit ? 'Rp '.number_format($stock->price_per_unit,0,',','.') : '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Supplier</span>
                        <span class="fw-semibold">{{ $stock->supplier ?? '-' }}</span>
                    </div>
                </div>
                @if($stock->description)
                <div class="mt-3 p-3 rounded-3 bg-light small text-muted">
                    <i class="ti ti-notes me-1"></i>{{ $stock->description }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Totaliser stok masuk/keluar --}}
    <div class="col-12 col-md-8">
        <div class="row g-3 h-100">
            <div class="col-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #19875415, #fff);">
                    <div class="card-body p-4 d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="bg-success bg-opacity-10 rounded-2 p-2">
                                <i class="ti ti-arrow-down-circle text-success fs-4"></i>
                            </div>
                            <span class="text-muted small fw-medium">Total Stok Masuk</span>
                        </div>
                        <div class="fs-2 fw-bold text-success">
                            {{ $logs->getCollection()->where('type','in')->sum('quantity') + $logs->where('type','in')->sum('quantity') }}
                        </div>
                        <div class="small text-muted">{{ $stock->unit }} (halaman ini)</div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #dc354515, #fff);">
                    <div class="card-body p-4 d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="bg-danger bg-opacity-10 rounded-2 p-2">
                                <i class="ti ti-arrow-up-circle text-danger fs-4"></i>
                            </div>
                            <span class="text-muted small fw-medium">Total Stok Keluar</span>
                        </div>
                        <div class="fs-2 fw-bold text-danger">
                            {{ $logs->where('type','out')->sum('quantity') }}
                        </div>
                        <div class="small text-muted">{{ $stock->unit }} (halaman ini)</div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #0d6efd08, #fff);">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <i class="ti ti-history text-primary fs-4 flex-shrink-0"></i>
                        <div>
                            <div class="fw-semibold text-dark">{{ $logs->total() }} Total Catatan</div>
                            <div class="text-muted small">Seluruh riwayat pergerakan stok tercatat</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- LOG TABLE --}}
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
        <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
            <i class="ti ti-history text-primary"></i> Riwayat Pergerakan Stok
        </h6>
        <hr class="mt-3 mb-0">
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr style="background:#f8f9ff; border-bottom: 2px solid #e9ecef;">
                        <th class="py-3 ps-4 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px; width:50px;">No</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Jenis</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase text-center" style="letter-spacing:.5px;">Jumlah</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase text-center" style="letter-spacing:.5px;">Sebelum → Sesudah</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Catatan</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Oleh</th>
                        <th class="py-3 pe-4 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $i => $log)
                    <tr>
                        <td class="ps-4 text-muted small">{{ $logs->firstItem() + $i }}</td>
                        <td>
                            @if($log->type === 'in')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">
                                    <i class="ti ti-arrow-down-circle me-1"></i>Masuk
                                </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3">
                                    <i class="ti ti-arrow-up-circle me-1"></i>Keluar
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="fw-bold {{ $log->type === 'in' ? 'text-success' : 'text-danger' }}">
                                {{ $log->type === 'in' ? '+' : '-' }}{{ $log->quantity }}
                            </span>
                            <small class="text-muted d-block">{{ $stock->unit }}</small>
                        </td>
                        <td class="text-center">
                            <span class="text-muted small font-monospace">
                                {{ $log->stock_before }} → <span class="fw-semibold text-dark">{{ $log->stock_after }}</span>
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $log->note ?? '-' }}</small>
                        </td>
                        <td>
                            <small class="text-dark fw-medium">{{ $log->user?->name ?? 'Sistem' }}</small>
                        </td>
                        <td class="pe-4">
                            <small class="text-muted" title="{{ $log->created_at->format('d M Y H:i') }}">
                                {{ $log->created_at->diffForHumans() }}
                            </small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="ti ti-history fs-1 text-muted opacity-25 d-block mb-2"></i>
                            <p class="text-muted small mb-0">Belum ada riwayat pergerakan stok</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($logs->hasPages())
    <div class="card-footer border-top-0 bg-transparent px-4 py-3">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
            <div class="text-muted small">
                Menampilkan <span class="fw-semibold text-dark">{{ $logs->firstItem() }}</span>
                – <span class="fw-semibold text-dark">{{ $logs->lastItem() }}</span>
                dari <span class="fw-semibold text-dark">{{ $logs->total() }}</span> catatan
            </div>
            <div>{{ $logs->links('vendor.pagination.custom') }}</div>
        </div>
    </div>
    @endif
</div>
@endsection
