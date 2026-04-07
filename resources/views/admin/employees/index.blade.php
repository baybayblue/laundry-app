@extends('layouts.app')

@section('content')
{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #0d6efd20, #0d6efd10); border: 1px solid #0d6efd30; width:48px; height:48px;">
                <i class="ti ti-users fs-3 text-primary"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Daftar Karyawan</h1>
                <p class="mb-0 text-muted small">Kelola akun staf operasional laundry Anda</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
            <i class="ti ti-user-plus fs-5"></i> Tambah Karyawan
        </a>
    </div>
</div>

{{-- STATS CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #0d6efd15, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Total Karyawan</span>
                    <div class="bg-primary bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-users text-primary fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-primary">{{ $employees->total() }}</div>
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
                <div class="fs-3 fw-bold text-success">{{ $employees->count() }}</div>
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
                <div class="fs-3 fw-bold text-warning">{{ $employees->currentPage() }}</div>
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
                <div class="fs-3 fw-bold" style="color: #6f42c1;">{{ $employees->lastPage() }}</div>
            </div>
        </div>
    </div>
</div>

{{-- SEARCH & FILTER BAR --}}
<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.employees.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 border"><i class="ti ti-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 border ps-0" placeholder="Cari nama atau email karyawan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="position" class="form-select form-select-sm">
                    <option value="">Semua Posisi</option>
                    <option value="Kasir" {{ request('position') == 'Kasir' ? 'selected' : '' }}>Kasir</option>
                    <option value="Staff Cuci" {{ request('position') == 'Staff Cuci' ? 'selected' : '' }}>Staff Cuci</option>
                    <option value="Staff Setrika" {{ request('position') == 'Staff Setrika' ? 'selected' : '' }}>Staff Setrika</option>
                    <option value="Kurir" {{ request('position') == 'Kurir' ? 'selected' : '' }}>Kurir</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="gender" class="form-select form-select-sm">
                    <option value="">Semua Gender</option>
                    <option value="L" {{ request('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ request('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill rounded-pill">
                    <i class="ti ti-filter me-1"></i>Filter
                </button>
                @if(request()->hasAny(['search','position','gender']))
                <a href="{{ route('admin.employees.index') }}" class="btn btn-light btn-sm rounded-pill" title="Reset Filter">
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
                        <th class="py-3 ps-4 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px; width:60px;">No</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Karyawan</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Kontak</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Posisi</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Gender</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Tgl. Daftar</th>
                        <th class="py-3 pe-4 text-end text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $index => $employee)
                    <tr class="employee-row" style="transition: background .15s;">
                        <td class="ps-4 text-muted fw-medium small">
                            {{ $employees->firstItem() + $index }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($employee->photo)
                                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->name }}"
                                        style="width: 42px; height: 42px; object-fit: cover;"
                                        class="rounded-circle shadow-sm border border-2 border-white">
                                @else
                                    <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center fw-bold text-white"
                                        style="width:42px; height:42px; font-size:16px; background: linear-gradient(135deg, #0d6efd, #0dcaf0); flex-shrink:0;">
                                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $employee->name }}</h6>
                                    <small class="text-muted">{{ $employee->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($employee->phone)
                                <span class="d-flex align-items-center gap-1 text-dark">
                                    <i class="ti ti-phone text-muted small"></i>
                                    <small>{{ $employee->phone }}</small>
                                </span>
                            @else
                                <small class="text-muted fst-italic">-</small>
                            @endif
                        </td>
                        <td>
                            @php
                                $positionColors = [
                                    'Kasir'         => ['bg' => '#0d6efd', 'text' => '#fff'],
                                    'Staff Cuci'    => ['bg' => '#198754', 'text' => '#fff'],
                                    'Staff Setrika' => ['bg' => '#fd7e14', 'text' => '#fff'],
                                    'Kurir'         => ['bg' => '#6f42c1', 'text' => '#fff'],
                                ];
                                $color = $positionColors[$employee->position] ?? ['bg' => '#6c757d', 'text' => '#fff'];
                            @endphp
                            <span class="badge rounded-pill px-3 py-1 small"
                                style="background-color: {{ $color['bg'] }}20; color: {{ $color['bg'] }}; border: 1px solid {{ $color['bg'] }}40; font-weight:600;">
                                {{ $employee->position ?? 'Belum Diset' }}
                            </span>
                        </td>
                        <td>
                            @if($employee->gender == 'L')
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2">
                                    <i class="ti ti-gender-male me-1"></i>L
                                </span>
                            @elseif($employee->gender == 'P')
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2">
                                    <i class="ti ti-gender-female me-1"></i>P
                                </span>
                            @else
                                <span class="text-muted small fst-italic">-</span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $employee->created_at->format('d M Y') }}</small>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <a href="{{ route('admin.employees.edit', $employee) }}"
                                    class="btn btn-sm btn-icon rounded-2"
                                    style="background:#0d6efd15; color:#0d6efd; width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center;"
                                    data-bs-toggle="tooltip" title="Edit Data">
                                    <i class="ti ti-edit fs-6"></i>
                                </a>
                                <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="btn btn-sm btn-icon rounded-2 btn-delete"
                                        style="background:#dc354515; color:#dc3545; width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center;"
                                        data-name="{{ $employee->name }}"
                                        title="Hapus Data">
                                        <i class="ti ti-trash fs-6"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle"
                                    style="width:72px; height:72px; background: linear-gradient(135deg, #e9ecef, #f8f9fa);">
                                    <i class="ti ti-user-off fs-1 text-muted"></i>
                                </div>
                                <h5 class="fw-semibold text-muted mb-1">
                                    @if(request()->hasAny(['search','position','gender']))
                                        Tidak Ada Hasil
                                    @else
                                        Belum Ada Karyawan
                                    @endif
                                </h5>
                                <p class="small text-muted mb-3">
                                    @if(request()->hasAny(['search','position','gender']))
                                        Coba ubah filter pencarian Anda.
                                    @else
                                        Tambahkan karyawan pertama untuk membantu operasional.
                                    @endif
                                </p>
                                @if(request()->hasAny(['search','position','gender']))
                                    <a href="{{ route('admin.employees.index') }}" class="btn btn-light btn-sm rounded-pill px-4">
                                        <i class="ti ti-x me-1"></i>Reset Filter
                                    </a>
                                @else
                                    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                        <i class="ti ti-user-plus me-1"></i>Tambah Karyawan
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
    @if($employees->hasPages())
    <div class="card-footer border-top-0 bg-transparent px-4 py-3">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
            <div class="text-muted small">
                Menampilkan <span class="fw-semibold text-dark">{{ $employees->firstItem() }}</span>
                – <span class="fw-semibold text-dark">{{ $employees->lastItem() }}</span>
                dari <span class="fw-semibold text-dark">{{ $employees->total() }}</span> karyawan
            </div>
            <div class="pagination-wrapper">
                {{ $employees->appends(request()->query())->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
    @else
    <div class="card-footer border-top-0 bg-transparent px-4 py-2">
        <p class="text-muted small mb-0">
            Menampilkan <span class="fw-semibold text-dark">{{ $employees->count() }}</span> karyawan
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
                const name = this.dataset.name || 'karyawan ini';
                const form = this.closest('form');
                Swal.fire({
                    title: 'Hapus Karyawan?',
                    html: `Akun <strong>${name}</strong> akan kehilangan akses masuk ke sistem dan data tidak bisa dipulihkan.`,
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
        document.querySelectorAll('.employee-row').forEach(row => {
            row.addEventListener('mouseenter', () => row.style.backgroundColor = '#f8f9ff');
            row.addEventListener('mouseleave', () => row.style.backgroundColor = '');
        });
    });
</script>
@endpush
@endsection
