@extends('layouts.app')

@section('content')

{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                style="background: rgba(13, 110, 253, 0.1); border: 1px solid rgba(13, 110, 253, 0.2); width:48px; height:48px;">
                <i class="ti ti-users-group fs-3 text-primary"></i>
            </div>
            <div>
                <h1 class="fs-4 mb-0 fw-bold">Manajemen User</h1>
                <p class="mb-0 text-muted small">Kelola akun admin, owner, dan karyawan</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('admin.users.create') }}"
            class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
            <i class="ti ti-plus fs-5"></i> Tambah User
        </a>
    </div>
</div>

{{-- STATS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: white; border-bottom: 3px solid #0d6efd !important;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold">TOTAL USER</span>
                    <i class="ti ti-users text-primary fs-5 opacity-50"></i>
                </div>
                <div class="fs-3 fw-bold text-dark">{{ number_format($stats['total']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #dc354515, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Admin</span>
                    <div class="bg-danger bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-shield-check text-danger fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-danger">{{ number_format($stats['admin']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #f59e0b15, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Owner</span>
                    <div class="bg-warning bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-crown text-warning fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-warning">{{ number_format($stats['owner']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #19875415, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Karyawan</span>
                    <div class="bg-success bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-user-check text-success fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-success">{{ number_format($stats['employee']) }}</div>
            </div>
        </div>
    </div>
</div>

{{-- FILTER --}}
<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                        placeholder="Cari nama, email, atau HP..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="role" class="form-select form-select-sm">
                    <option value="">Semua Role</option>
                    <option value="admin"    {{ request('role')==='admin'    ? 'selected' : '' }}>Admin</option>
                    <option value="owner"    {{ request('role')==='owner'    ? 'selected' : '' }}>Owner</option>
                    <option value="employee" {{ request('role')==='employee' ? 'selected' : '' }}>Karyawan</option>
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm text-white flex-fill rounded-pill">
                    <i class="ti ti-filter me-1"></i>Filter
                </button>
                @if(request('search') || request('role'))
                <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm rounded-pill border" title="Reset">
                    <i class="ti ti-x"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- TABLE --}}
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th class="py-3 ps-4 text-muted fw-bold small text-uppercase" style="letter-spacing:.5px; width:50px;">No</th>
                        <th class="py-3 text-muted fw-bold small text-uppercase" style="letter-spacing:.5px;">User</th>
                        <th class="py-3 text-muted fw-bold small text-uppercase" style="letter-spacing:.5px;">Role</th>
                        <th class="py-3 text-muted fw-bold small text-uppercase" style="letter-spacing:.5px;">Kontak</th>
                        <th class="py-3 text-muted fw-bold small text-uppercase" style="letter-spacing:.5px;">Posisi</th>
                        <th class="py-3 pe-4 text-end text-muted fw-bold small text-uppercase" style="letter-spacing:.5px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    <tr style="transition: background .15s;">
                        <td class="ps-4 text-muted fw-medium small">{{ $users->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($user->photo)
                                    <img src="{{ asset('storage/' . $user->photo) }}" class="rounded-circle object-fit-cover flex-shrink-0" width="40" height="40" alt="">
                                @else
                                    @php
                                        $roleColor = match($user->role) {
                                            'admin'    => '#dc3545',
                                            'owner'    => '#f59e0b',
                                            'employee' => '#198754',
                                            default    => '#6c757d',
                                        };
                                    @endphp
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 fw-bold text-white shadow-sm"
                                        style="width:40px;height:40px;font-size:1rem;background: #0d6efd;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:.9rem;">{{ $user->name }}
                                        @if($user->id === auth()->id())
                                        <span class="badge bg-primary bg-opacity-10 text-primary ms-1" style="font-size:.6rem;">YOU</span>
                                        @endif
                                    </div>
                                    <div class="text-muted" style="font-size:.75rem;">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $badgeStyle = match($user->role) {
                                    'admin'    => 'background:#dc354510;color:#dc3545;border:1px solid #dc354520;',
                                    'owner'    => 'background:#f59e0b10;color:#92400e;border:1px solid #f59e0b20;',
                                    'employee' => 'background:#19875410;color:#198754;border:1px solid #19875420;',
                                    default    => 'background:#6c757d10;color:#6c757d;',
                                };
                                $icon = match($user->role) {
                                    'admin'    => 'ti-shield-check',
                                    'owner'    => 'ti-crown',
                                    'employee' => 'ti-user-check',
                                    default    => 'ti-user',
                                };
                            @endphp
                            <span class="badge rounded-pill px-3 py-2 fw-bold" style="{{ $badgeStyle }}font-size:.65rem; text-transform: uppercase;">
                                <i class="ti {{ $icon }} me-1"></i>{{ $user->role_label }}
                            </span>
                        </td>
                        <td>
                            <div class="small">
                                @if($user->phone)
                                <div><i class="ti ti-phone text-muted me-1"></i>{{ $user->phone }}</div>
                                @endif
                                @if($user->address)
                                <div class="text-muted text-truncate" style="max-width:160px; font-size:.75rem;">
                                    <i class="ti ti-map-pin me-1"></i>{{ $user->address }}
                                </div>
                                @endif
                                @if(!$user->phone && !$user->address)
                                <span class="text-muted">–</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="text-dark small fw-medium">{{ $user->position ?: '–' }}</span>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                    class="btn btn-sm btn-icon rounded-circle border"
                                    style="width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; color:#475569;"
                                    title="Edit">
                                    <i class="ti ti-edit fs-5"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-icon rounded-circle border text-danger"
                                        style="width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center;"
                                        data-name="{{ $user->name }}" title="Hapus">
                                        <i class="ti ti-trash fs-5"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-4">
                                <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle"
                                    style="width:72px; height:72px; background: linear-gradient(135deg, #e9ecef, #f8f9fa);">
                                    <i class="ti ti-users-group fs-1 text-muted"></i>
                                </div>
                                <h5 class="fw-semibold text-muted mb-1">Belum Ada User</h5>
                                <p class="small text-muted mb-3">Tambahkan user pertama.</p>
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                    <i class="ti ti-plus me-1"></i>Tambah User
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
    <div class="card-footer border-top-0 bg-transparent px-4 py-3">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
            <div class="text-muted small">
                Menampilkan <span class="fw-semibold text-dark">{{ $users->firstItem() }}</span>
                – <span class="fw-semibold text-dark">{{ $users->lastItem() }}</span>
                dari <span class="fw-semibold text-dark">{{ $users->total() }}</span> user
            </div>
            <div>{{ $users->appends(request()->query())->links('vendor.pagination.custom') }}</div>
        </div>
    </div>
    @else
    <div class="card-footer border-top-0 bg-transparent px-4 py-2">
        <p class="text-muted small mb-0">Menampilkan <span class="fw-semibold text-dark">{{ $users->count() }}</span> user</p>
    </div>
    @endif
</div>

@push('scripts')
<style>
    tr:hover { background: #faf5ff !important; }
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
                title: 'Hapus User?',
                html: `User <strong>${name}</strong> akan dihapus permanen.`,
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

    @if(session('success'))
    Swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000,timerProgressBar:true})
        .fire({icon:'success',title:'{!! session('success') !!}'});
    @endif
    @if(session('error'))
    Swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:4000})
        .fire({icon:'error',title:'{{ session('error') }}'});
    @endif
});
</script>
@endpush

@endsection
