@extends('layouts.app')

@section('content')
{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #19875420, #19875410); border: 1px solid #19875430; width:48px; height:48px;">
                <i class="ti ti-users text-success fs-3"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Daftar Pelanggan</h1>
                <p class="mb-0 text-muted small">Kelola data pelanggan dan akses portal mereka</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('admin.customers.create') }}" class="btn btn-success d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
            <i class="ti ti-user-plus fs-5"></i> Tambah Pelanggan
        </a>
    </div>
</div>

{{-- STATS CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #0d6efd15, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Total Pelanggan</span>
                    <div class="bg-primary bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-users text-primary fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-primary">{{ $customers->total() }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #19875415, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Halaman Ini</span>
                    <div class="bg-success bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-list-check text-success fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-success">{{ $customers->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #fd7e1415, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Halaman Saat Ini</span>
                    <div class="bg-warning bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-book-2 text-warning fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-warning">{{ $customers->currentPage() }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #6f42c115, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Total Halaman</span>
                    <div class="bg-purple bg-opacity-10 rounded-2 p-1" style="background-color: #6f42c115 !important;">
                        <i class="ti ti-layout-rows text-purple fs-5" style="color: #6f42c1 !important;"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold" style="color: #6f42c1;">{{ $customers->lastPage() }}</div>
            </div>
        </div>
    </div>
</div>

{{-- SEARCH & FILTER BAR --}}
<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-10">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 border"><i class="ti ti-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 border ps-0" placeholder="Cari nama, email, atau telepon pelanggan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-success btn-sm flex-fill rounded-pill">
                    <i class="ti ti-search me-1"></i>Cari
                </button>
                @if(request()->has('search') && request('search') != '')
                <a href="{{ route('admin.customers.index') }}" class="btn btn-light btn-sm rounded-pill" title="Reset Pencarian">
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
                    <tr style="background: linear-gradient(90deg, #f8f9ff, #f0f4ff); border-bottom: 2px solid #e9ecef;">
                        <th class="py-3 ps-4 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px; width:60px;">No</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Pelanggan</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Kontak</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Alamat</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Tgl. Daftar</th>
                        <th class="py-3 pe-4 text-end text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $index => $customer)
                    <tr class="customer-row" style="transition: background .15s;">
                        <td class="ps-4 text-muted fw-medium small">
                            {{ $customers->firstItem() + $index }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                    style="width:42px; height:42px; font-size:16px; background: linear-gradient(135deg, #198754, #20c997);">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $customer->name }}</h6>
                                    <small class="text-muted">{{ $customer->email ?? 'Belum ada email' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="d-inline-flex align-items-center gap-1 text-dark">
                                <i class="ti ti-phone text-muted small"></i>
                                <small>{{ $customer->phone ?? 'Tidak ada' }}</small>
                            </span>
                        </td>
                        <td>
                            <span class="d-inline-block text-truncate text-muted small" style="max-width: 200px;" title="{{ $customer->address }}">
                                {{ $customer->address ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $customer->created_at->format('d M Y') }}</small>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <a href="{{ route('admin.customers.edit', $customer) }}"
                                    class="btn btn-sm btn-icon rounded-2"
                                    style="background:#0d6efd15; color:#0d6efd; width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center;"
                                    data-bs-toggle="tooltip" title="Edit Data">
                                    <i class="ti ti-edit fs-6"></i>
                                </a>
                                <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="btn btn-sm btn-icon rounded-2 btn-delete"
                                        style="background:#dc354515; color:#dc3545; width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center;"
                                        data-name="{{ $customer->name }}"
                                        title="Hapus Data">
                                        <i class="ti ti-trash fs-6"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-4">
                                <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle"
                                    style="width:72px; height:72px; background: linear-gradient(135deg, #e9ecef, #f8f9fa);">
                                    <i class="ti ti-users-x fs-1 text-muted"></i>
                                </div>
                                <h5 class="fw-semibold text-muted mb-1">
                                    @if(request()->has('search') && request('search') != '')
                                        Pencarian Tidak Ditemukan
                                    @else
                                        Belum Ada Pelanggan
                                    @endif
                                </h5>
                                <p class="small text-muted mb-3">
                                    @if(request()->has('search') && request('search') != '')
                                        Tidak ada pelanggan yang cocok dengan "{{ request('search') }}".
                                    @else
                                        Tambahkan pelanggan pertama Anda untuk memulai transaksi.
                                    @endif
                                </p>
                                @if(request()->has('search') && request('search') != '')
                                    <a href="{{ route('admin.customers.index') }}" class="btn btn-light btn-sm rounded-pill px-4">
                                        <i class="ti ti-x me-1"></i>Reset Pencarian
                                    </a>
                                @else
                                    <a href="{{ route('admin.customers.create') }}" class="btn btn-success btn-sm rounded-pill px-4">
                                        <i class="ti ti-user-plus me-1"></i>Tambah Pelanggan
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
    @if($customers->hasPages())
    <div class="card-footer border-top-0 bg-transparent px-4 py-3">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
            <div class="text-muted small">
                Menampilkan <span class="fw-semibold text-dark">{{ $customers->firstItem() }}</span>
                – <span class="fw-semibold text-dark">{{ $customers->lastItem() }}</span>
                dari <span class="fw-semibold text-dark">{{ $customers->total() }}</span> pelanggan
            </div>
            <div class="pagination-wrapper">
                {{ $customers->appends(request()->query())->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
    @else
    <div class="card-footer border-top-0 bg-transparent px-4 py-2">
        <p class="text-muted small mb-0">
            Menampilkan <span class="fw-semibold text-dark">{{ $customers->count() }}</span> pelanggan
        </p>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Delete confirmation
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const name = this.dataset.name || 'pelanggan ini';
                const form = this.closest('form');
                Swal.fire({
                    title: 'Hapus Pelanggan?',
                    html: `Data pelanggan <strong>${name}</strong> akan dihapus beserta histori transaksinya secara permanen.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="ti ti-trash me-1"></i>Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Initialize tooltips
        const tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipEls.forEach(el => new bootstrap.Tooltip(el, { trigger: 'hover' }));

        // Row hover animation
        document.querySelectorAll('.customer-row').forEach(row => {
            row.addEventListener('mouseenter', () => row.style.backgroundColor = '#f8f9ff');
            row.addEventListener('mouseleave', () => row.style.backgroundColor = '');
        });
    });
</script>
@endpush
@endsection
