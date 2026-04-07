@extends('layouts.app')

@section('content')

{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                style="background:linear-gradient(135deg,#6f42c120,#6f42c110); border:1px solid #6f42c130; width:48px; height:48px;">
                <i class="ti ti-ironing fs-3" style="color:#6f42c1;"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Layanan Laundry</h1>
                <p class="mb-0 text-muted small">Kelola layanan dan harga yang tersedia</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('admin.services.create') }}"
            class="btn d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4 py-2 fw-semibold text-white"
            style="background:linear-gradient(135deg,#6f42c1,#8b5cf6);">
            <i class="ti ti-plus"></i> Tambah Layanan
        </a>
    </div>
</div>

{{-- ALERTS --}}
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

{{-- STATS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background:linear-gradient(135deg,#6f42c115,#fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Total Layanan</span>
                    <div class="rounded-2 p-1" style="background:#6f42c115;">
                        <i class="ti ti-ironing fs-5" style="color:#6f42c1;"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold" style="color:#6f42c1;">{{ $totalServices }}</div>
                <div class="text-muted" style="font-size:.72rem;">jenis layanan</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background:linear-gradient(135deg,#19875415,#fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Aktif</span>
                    <div class="bg-success bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-check text-success fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-success">{{ $activeCount }}</div>
                <div class="text-muted" style="font-size:.72rem;">tersedia untuk transaksi</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background:linear-gradient(135deg,#6c757d15,#fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Nonaktif</span>
                    <div class="bg-secondary bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-x text-secondary fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-secondary">{{ $inactiveCount }}</div>
                <div class="text-muted" style="font-size:.72rem;">sementara dinonaktifkan</div>
            </div>
        </div>
    </div>
</div>

{{-- FILTER --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.services.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
            <div class="input-group input-group-sm flex-fill" style="min-width:200px;">
                <span class="input-group-text bg-light border-end-0"><i class="ti ti-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0"
                    placeholder="Cari nama layanan..." value="{{ request('search') }}">
            </div>
            <select name="type" class="form-select form-select-sm flex-shrink-0" style="width:auto;">
                <option value="">Semua Tipe</option>
                <option value="per_kg"  {{ request('type') === 'per_kg'  ? 'selected' : '' }}>per Kg</option>
                <option value="per_pcs" {{ request('type') === 'per_pcs' ? 'selected' : '' }}>per Pcs</option>
                <option value="flat"    {{ request('type') === 'flat'    ? 'selected' : '' }}>Flat</option>
            </select>
            <select name="status" class="form-select form-select-sm flex-shrink-0" style="width:auto;">
                <option value="">Semua Status</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit"
                class="btn btn-sm rounded-pill px-4 fw-medium text-white flex-shrink-0"
                style="background:linear-gradient(135deg,#6f42c1,#8b5cf6);">
                <i class="ti ti-filter me-1"></i>Filter
            </button>
            @if(request('search') || request('type') || request('status'))
            <a href="{{ route('admin.services.index') }}" class="btn btn-light btn-sm rounded-pill px-3 flex-shrink-0">
                <i class="ti ti-x"></i>
            </a>
            @endif
        </form>
    </div>
</div>

{{-- SERVICE GRID --}}
@if($services->count())
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 mb-4">
    @foreach($services as $service)
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 h-100 service-card" style="transition:all .2s;opacity:{{ $service->is_active ? '1' : '.65' }};">
            {{-- color bar --}}
            <div class="rounded-top-4" style="height:5px; background:{{ $service->color }};"></div>
            <div class="card-body p-4">
                {{-- Header: icon + name + status --}}
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:48px; height:48px; background:{{ $service->color }}18; border:1.5px solid {{ $service->color }}35;">
                        <i class="ti {{ $service->icon }} fs-4" style="color:{{ $service->color }};"></i>
                    </div>
                    <div class="flex-fill min-w-0">
                        <div class="fw-bold text-truncate mb-1">{{ $service->name }}</div>
                        <div class="d-flex align-items-center gap-1 flex-wrap">
                            {{-- Tipe badge --}}
                            @php
                                $typeConfig = [
                                    'per_kg'  => ['label'=>'per Kg',  'class'=>'bg-primary bg-opacity-10 text-primary'],
                                    'per_pcs' => ['label'=>'per Pcs', 'class'=>'bg-info bg-opacity-10 text-info'],
                                    'flat'    => ['label'=>'Flat',    'class'=>'bg-warning bg-opacity-10 text-warning'],
                                ][$service->type] ?? ['label'=>$service->type,'class'=>'bg-secondary bg-opacity-10 text-secondary'];
                            @endphp
                            <span class="badge rounded-pill small {{ $typeConfig['class'] }}" style="font-size:.68rem;">
                                {{ $typeConfig['label'] }}
                            </span>
                            {{-- Status badge --}}
                            @if($service->is_active)
                            <span class="badge rounded-pill small bg-success bg-opacity-10 text-success" style="font-size:.68rem;">
                                <i class="ti ti-check me-1" style="font-size:.6rem;"></i>Aktif
                            </span>
                            @else
                            <span class="badge rounded-pill small bg-secondary bg-opacity-10 text-secondary" style="font-size:.68rem;">
                                <i class="ti ti-x me-1" style="font-size:.6rem;"></i>Nonaktif
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Price --}}
                <div class="rounded-3 p-3 mb-3" style="background:{{ $service->color }}08; border:1px solid {{ $service->color }}20;">
                    <div class="d-flex align-items-baseline gap-1">
                        <span class="fw-bold fs-5" style="color:{{ $service->color }};">
                            Rp {{ number_format($service->price, 0, ',', '.') }}
                        </span>
                        <span class="text-muted small">/ {{ $service->getTypeLabel() }}</span>
                    </div>
                    <div class="text-muted mt-1" style="font-size:.72rem;">
                        <i class="ti ti-clock me-1"></i>Estimasi: {{ $service->getEstimatedLabel() }}
                    </div>
                </div>

                {{-- Description --}}
                <p class="text-muted small mb-3" style="line-height:1.5; min-height:2.5rem;">
                    {{ $service->description ?: 'Belum ada deskripsi.' }}
                </p>

                {{-- Footer Actions --}}
                <div class="d-flex align-items-center justify-content-between pt-3"
                    style="border-top:1px solid #f0f0f0;">
                    {{-- Toggle switch --}}
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input toggle-service" type="checkbox" role="switch"
                                id="toggle-{{ $service->id }}"
                                {{ $service->is_active ? 'checked' : '' }}
                                data-id="{{ $service->id }}"
                                data-url="{{ route('admin.services.toggle', $service) }}"
                                style="cursor:pointer;">
                        </div>
                        <label class="form-check-label text-muted small" for="toggle-{{ $service->id }}" style="cursor:pointer;">
                            {{ $service->is_active ? 'Aktif' : 'Nonaktif' }}
                        </label>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.services.edit', $service) }}"
                            class="btn btn-sm d-flex align-items-center justify-content-center rounded-2"
                            style="width:30px;height:30px;background:#fd7e1415;color:#fd7e14;"
                            title="Edit">
                            <i class="ti ti-edit" style="font-size:.85rem;"></i>
                        </a>
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST"
                            class="form-delete-service d-inline">
                            @csrf @method('DELETE')
                            <button type="button"
                                class="btn btn-sm d-flex align-items-center justify-content-center rounded-2 btn-delete-service"
                                style="width:30px;height:30px;background:#dc354515;color:#dc3545;"
                                data-name="{{ $service->name }}"
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
@if($services->hasPages())
<div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
    <div class="text-muted small">
        Menampilkan <strong>{{ $services->firstItem() }}</strong>–<strong>{{ $services->lastItem() }}</strong>
        dari <strong>{{ $services->total() }}</strong> layanan
    </div>
    {{ $services->appends(request()->query())->links('vendor.pagination.custom') }}
</div>
@else
<div class="text-muted small">Menampilkan {{ $services->count() }} layanan</div>
@endif

@else
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body text-center py-5">
        <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle"
            style="width:72px;height:72px;background:linear-gradient(135deg,#e9ecef,#f8f9fa);">
            <i class="ti ti-ironing fs-1 text-muted"></i>
        </div>
        <h5 class="fw-semibold text-muted mb-1">
            {{ request()->hasAny(['search','type','status']) ? 'Tidak Ada Hasil' : 'Belum Ada Layanan' }}
        </h5>
        <p class="small text-muted mb-3">
            {{ request()->hasAny(['search','type','status']) ? 'Coba ubah filter pencarian.' : 'Tambahkan layanan pertama untuk mulai menerima transaksi.' }}
        </p>
        @if(request()->hasAny(['search','type','status']))
        <a href="{{ route('admin.services.index') }}" class="btn btn-light btn-sm rounded-pill px-4">
            <i class="ti ti-x me-1"></i>Reset
        </a>
        @else
        <a href="{{ route('admin.services.create') }}"
            class="btn btn-sm rounded-pill px-4 fw-medium text-white"
            style="background:linear-gradient(135deg,#6f42c1,#8b5cf6);">
            <i class="ti ti-plus me-1"></i>Tambah Layanan
        </a>
        @endif
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Card hover
    document.querySelectorAll('.service-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-4px)';
            card.style.boxShadow = '0 10px 30px rgba(0,0,0,.1)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
            card.style.boxShadow = '';
        });
    });

    // Toggle aktif/nonaktif
    document.querySelectorAll('.toggle-service').forEach(toggle => {
        toggle.addEventListener('change', async function () {
            const id  = this.dataset.id;
            const url = this.dataset.url;
            const card = this.closest('.service-card');
            const label = document.querySelector(`label[for="toggle-${id}"]`);

            try {
                const res  = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });
                const data = await res.json();

                label.textContent = data.is_active ? 'Aktif' : 'Nonaktif';
                card.style.opacity = data.is_active ? '1' : '0.65';

                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: data.is_active ? 'success' : 'info',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                });
            } catch (e) {
                this.checked = !this.checked; // revert
                Swal.fire('Error', 'Gagal mengubah status layanan.', 'error');
            }
        });
    });

    // Delete confirm
    document.querySelectorAll('.btn-delete-service').forEach(btn => {
        btn.addEventListener('click', function () {
            const name = this.dataset.name;
            const form = this.closest('form');
            Swal.fire({
                title: 'Hapus Layanan?',
                html: `Layanan <strong>${name}</strong> akan dihapus permanen.`,
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
