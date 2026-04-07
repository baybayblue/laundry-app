@extends('layouts.app')

@section('content')

{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                style="background:linear-gradient(135deg,#6f42c120,#6f42c110); border:1px solid #6f42c130; width:48px; height:48px;">
                <i class="ti ti-tags fs-3" style="color:#6f42c1;"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Kategori Barang</h1>
                <p class="mb-0 text-muted small">Kelola pengelompokan barang stok laundry</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('admin.categories.create') }}"
            class="btn d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4 py-2 fw-semibold text-white"
            style="background:linear-gradient(135deg,#6f42c1,#8b5cf6);">
            <i class="ti ti-plus"></i> Tambah Kategori
        </a>
    </div>
</div>

{{-- SESSION ALERTS --}}
@if(session('success'))
<div class="alert border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2" style="background:#19875415;">
    <i class="ti ti-circle-check text-success fs-5"></i>
    <span class="small fw-medium text-success">{{ session('success') }}</span>
</div>
@endif
@if(session('error'))
<div class="alert border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2" style="background:#dc354515;">
    <i class="ti ti-alert-circle text-danger fs-5"></i>
    <span class="small fw-medium text-danger">{{ session('error') }}</span>
</div>
@endif

{{-- STATS CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background:linear-gradient(135deg,#6f42c115,#fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Total Kategori</span>
                    <div class="rounded-2 p-1" style="background:#6f42c115;">
                        <i class="ti ti-tags fs-5" style="color:#6f42c1;"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold" style="color:#6f42c1;">{{ $totalCategories }}</div>
                <div class="text-muted" style="font-size:.72rem;">kelompok barang</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background:linear-gradient(135deg,#fd7e1415,#fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Total Barang</span>
                    <div class="bg-warning bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-package text-warning fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-warning">{{ $totalItems }}</div>
                <div class="text-muted" style="font-size:.72rem;">item stok terdaftar</div>
            </div>
        </div>
    </div>
</div>

{{-- SEARCH BAR --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="d-flex align-items-center gap-2">
            <div class="input-group input-group-sm flex-fill">
                <span class="input-group-text bg-light border-end-0"><i class="ti ti-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0"
                    placeholder="Cari nama kategori..." value="{{ request('search') }}">
            </div>
            <button type="submit"
                class="btn btn-sm rounded-pill px-4 fw-medium text-white flex-shrink-0"
                style="background:linear-gradient(135deg,#6f42c1,#8b5cf6);">
                <i class="ti ti-filter me-1"></i>Filter
            </button>
            @if(request('search'))
            <a href="{{ route('admin.categories.index') }}" class="btn btn-light btn-sm rounded-pill px-3 flex-shrink-0" title="Reset">
                <i class="ti ti-x"></i>
            </a>
            @endif
        </form>
    </div>
</div>

{{-- CATEGORY GRID --}}
@if($categories->count())
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 mb-4">
    @foreach($categories as $cat)
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 h-100 category-card" style="transition:all .2s;">
            {{-- Top colored bar --}}
            <div class="rounded-top-4" style="height:5px; background:{{ $cat->color }};"></div>
            <div class="card-body p-4">
                {{-- Icon + Name + Badge row --}}
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:48px; height:48px; background:{{ $cat->color }}18; border:1.5px solid {{ $cat->color }}35;">
                        <i class="ti {{ $cat->icon }} fs-4" style="color:{{ $cat->color }};"></i>
                    </div>
                    <div class="flex-fill min-w-0">
                        <div class="fw-bold fs-6 mb-1 text-truncate">{{ $cat->name }}</div>
                        <span class="badge rounded-pill small text-white"
                            style="background:{{ $cat->color }}; font-size:.7rem; padding:3px 10px;">
                            {{ $cat->stock_items_count }} barang
                        </span>
                    </div>
                </div>

                {{-- Description --}}
                <p class="text-muted small mb-3" style="line-height:1.5; min-height:2.8rem;">
                    {{ $cat->description ?: 'Belum ada deskripsi untuk kategori ini.' }}
                </p>

                {{-- Footer: color + actions --}}
                <div class="d-flex align-items-center justify-content-between pt-3"
                    style="border-top:1px solid #f0f0f0;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle border border-2"
                            style="width:14px; height:14px; background:{{ $cat->color }}; border-color:{{ $cat->color }}60 !important;"></div>
                        <span class="text-muted font-monospace" style="font-size:.72rem;">{{ $cat->color }}</span>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.categories.edit', $cat) }}"
                            class="btn btn-sm d-flex align-items-center justify-content-center rounded-2"
                            style="width:30px;height:30px;background:#fd7e1415;color:#fd7e14;"
                            title="Edit">
                            <i class="ti ti-edit" style="font-size:.85rem;"></i>
                        </a>
                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST"
                            class="form-delete-cat d-inline">
                            @csrf @method('DELETE')
                            <button type="button"
                                class="btn btn-sm d-flex align-items-center justify-content-center rounded-2 btn-delete-cat"
                                style="width:30px;height:30px;background:#dc354515;color:#dc3545;"
                                data-name="{{ $cat->name }}"
                                data-count="{{ $cat->stock_items_count }}"
                                title="Hapus">
                                <i class="ti ti-trash" style="font-size:.85rem;"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- PAGINATION --}}
@if($categories->hasPages())
<div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
    <div class="text-muted small">
        Menampilkan <span class="fw-semibold text-dark">{{ $categories->firstItem() }}</span>
        – <span class="fw-semibold text-dark">{{ $categories->lastItem() }}</span>
        dari <span class="fw-semibold text-dark">{{ $categories->total() }}</span> kategori
    </div>
    {{ $categories->appends(request()->query())->links('vendor.pagination.custom') }}
</div>
@else
<div class="text-muted small">
    Menampilkan <span class="fw-semibold text-dark">{{ $categories->count() }}</span> kategori
</div>
@endif

@else
{{-- EMPTY STATE --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body text-center py-5">
        <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle"
            style="width:72px;height:72px;background:linear-gradient(135deg,#e9ecef,#f8f9fa);">
            <i class="ti ti-tags fs-1 text-muted"></i>
        </div>
        <h5 class="fw-semibold text-muted mb-1">
            {{ request('search') ? 'Tidak Ada Hasil' : 'Belum Ada Kategori' }}
        </h5>
        <p class="small text-muted mb-3">
            {{ request('search') ? 'Coba ubah kata kunci pencarian.' : 'Tambahkan kategori pertama untuk mengelompokkan barang stok.' }}
        </p>
        @if(request('search'))
        <a href="{{ route('admin.categories.index') }}" class="btn btn-light btn-sm rounded-pill px-4">
            <i class="ti ti-x me-1"></i>Reset
        </a>
        @else
        <a href="{{ route('admin.categories.create') }}"
            class="btn btn-sm rounded-pill px-4 fw-medium text-white"
            style="background:linear-gradient(135deg,#6f42c1,#8b5cf6);">
            <i class="ti ti-plus me-1"></i>Tambah Kategori
        </a>
        @endif
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Card hover effect
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-4px)';
            card.style.boxShadow = '0 10px 30px rgba(0,0,0,.1)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
            card.style.boxShadow = '';
        });
    });

    // Delete confirm
    document.querySelectorAll('.btn-delete-cat').forEach(btn => {
        btn.addEventListener('click', function () {
            const name  = this.dataset.name;
            const count = parseInt(this.dataset.count);
            const form  = this.closest('form');

            if (count > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Bisa Dihapus',
                    html: `Kategori <strong>${name}</strong> masih digunakan oleh <strong>${count} barang</strong>.<br>Pindahkan barang ke kategori lain terlebih dahulu.`,
                    confirmButtonColor: '#6f42c1',
                    confirmButtonText: 'Mengerti',
                });
                return;
            }

            Swal.fire({
                title: 'Hapus Kategori?',
                html: `Kategori <strong>${name}</strong> akan dihapus permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="ti ti-trash me-1"></i>Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });
});
</script>
@endpush
@endsection
