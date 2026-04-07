@extends('layouts.app')

@section('content')

{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                style="background:linear-gradient(135deg,#fd7e1420,#fd7e1410); border:1px solid #fd7e1430; width:48px;height:48px;">
                <i class="ti ti-tag fs-3" style="color:#fd7e14;"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Diskon & Promo</h1>
                <p class="mb-0 text-muted small">Kelola kode promo dan diskon transaksi</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('admin.discounts.create') }}"
            class="btn d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4 py-2 fw-semibold text-white"
            style="background:linear-gradient(135deg,#fd7e14,#e05a00);">
            <i class="ti ti-plus"></i> Buat Diskon
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

{{-- STATS --}}
<div class="row g-3 mb-4">
    @php
        $stats = [
            ['label'=>'Total Diskon',  'value'=>$totalCount,    'icon'=>'ti-tag',         'color'=>'#fd7e14'],
            ['label'=>'Aktif',         'value'=>$activeCount,   'icon'=>'ti-check',        'color'=>'#198754'],
            ['label'=>'Akan Datang',   'value'=>$upcomingCount, 'icon'=>'ti-clock',        'color'=>'#0dcaf0'],
            ['label'=>'Kadaluarsa',    'value'=>$expiredCount,  'icon'=>'ti-alert-circle', 'color'=>'#dc3545'],
        ];
    @endphp
    @foreach($stats as $s)
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">{{ $s['label'] }}</span>
                    <div class="rounded-2 p-1" style="background:{{ $s['color'] }}18;">
                        <i class="ti {{ $s['icon'] }} fs-5" style="color:{{ $s['color'] }};"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold" style="color:{{ $s['color'] }};">{{ $s['value'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- FILTER --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.discounts.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
            <div class="input-group input-group-sm flex-fill" style="min-width:200px;">
                <span class="input-group-text bg-light border-end-0"><i class="ti ti-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0"
                    placeholder="Cari nama atau kode diskon..." value="{{ request('search') }}">
            </div>
            <select name="type" class="form-select form-select-sm flex-shrink-0" style="width:auto;">
                <option value="">Semua Tipe</option>
                <option value="percentage" {{ request('type')==='percentage' ? 'selected':'' }}>Persentase (%)</option>
                <option value="fixed"      {{ request('type')==='fixed'      ? 'selected':'' }}>Nominal (Rp)</option>
            </select>
            <select name="status" class="form-select form-select-sm flex-shrink-0" style="width:auto;">
                <option value="">Semua Status</option>
                <option value="active"   {{ request('status')==='active'   ? 'selected':'' }}>Aktif</option>
                <option value="inactive" {{ request('status')==='inactive' ? 'selected':'' }}>Nonaktif</option>
                <option value="upcoming" {{ request('status')==='upcoming' ? 'selected':'' }}>Akan Datang</option>
                <option value="expired"  {{ request('status')==='expired'  ? 'selected':'' }}>Kadaluarsa</option>
            </select>
            <button type="submit"
                class="btn btn-sm rounded-pill px-4 fw-medium text-white flex-shrink-0"
                style="background:linear-gradient(135deg,#fd7e14,#e05a00);">
                <i class="ti ti-filter me-1"></i>Filter
            </button>
            @if(request()->hasAny(['search','type','status']))
            <a href="{{ route('admin.discounts.index') }}" class="btn btn-light btn-sm rounded-pill px-3 flex-shrink-0">
                <i class="ti ti-x"></i>
            </a>
            @endif
        </form>
    </div>
</div>

{{-- DISCOUNT CARDS --}}
@if($discounts->count())
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 mb-4">
    @foreach($discounts as $discount)
    @php
        $status = $discount->status;
        $statusConf = [
            'active'    => ['label'=>'Aktif',        'bg'=>'#19875418','color'=>'#198754','border'=>'#19875430','icon'=>'ti-check-circle'],
            'inactive'  => ['label'=>'Nonaktif',     'bg'=>'#6c757d18','color'=>'#6c757d','border'=>'#6c757d30','icon'=>'ti-x-circle'],
            'upcoming'  => ['label'=>'Akan Datang',  'bg'=>'#0dcaf018','color'=>'#0891b2','border'=>'#0dcaf030','icon'=>'ti-clock'],
            'expired'   => ['label'=>'Kadaluarsa',   'bg'=>'#dc354518','color'=>'#dc3545','border'=>'#dc354530','icon'=>'ti-alert-circle'],
            'exhausted' => ['label'=>'Habis Kuota',  'bg'=>'#ffc10718','color'=>'#b45309','border'=>'#ffc10730','icon'=>'ti-ban'],
        ][$status] ?? ['label'=>$status,'bg'=>'#f8f9fa','color'=>'#6c757d','border'=>'#dee2e6','icon'=>'ti-circle'];

        $topColor = match($status) {
            'active'    => '#198754',
            'upcoming'  => '#0dcaf0',
            'expired'   => '#dc3545',
            'exhausted' => '#ffc107',
            default     => '#6c757d',
        };
    @endphp
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 h-100 discount-card"
            style="transition:all .2s; opacity:{{ in_array($status,['inactive','expired','exhausted']) ? '.7' : '1' }};">
            <div class="rounded-top-4" style="height:4px; background:{{ $topColor }};"></div>
            <div class="card-body p-4">

                {{-- Header --}}
                <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                    <div class="flex-fill min-w-0">
                        <div class="fw-bold text-truncate mb-1">{{ $discount->name }}</div>
                        <span class="badge rounded-pill small" style="background:{{ $statusConf['bg'] }};color:{{ $statusConf['color'] }};border:1px solid {{ $statusConf['border'] }};font-size:.68rem;">
                            <i class="ti {{ $statusConf['icon'] }} me-1"></i>{{ $statusConf['label'] }}
                        </span>
                    </div>
                    {{-- Badge tipe --}}
                    <span class="badge rounded-pill flex-shrink-0"
                        style="background:{{ $discount->type==='percentage' ? '#6f42c118' : '#0d6efd18' }};
                               color:{{ $discount->type==='percentage' ? '#6f42c1' : '#0d6efd' }};
                               font-size:.68rem;">
                        {{ $discount->type==='percentage' ? '%' : 'Rp' }}
                    </span>
                </div>

                {{-- Nilai diskon --}}
                <div class="rounded-3 p-3 mb-3 text-center"
                    style="background:linear-gradient(135deg,{{ $topColor }}12,{{ $topColor }}05); border:1px dashed {{ $topColor }}40;">
                    <div class="fw-bold" style="font-size:1.8rem; color:{{ $topColor }}; letter-spacing:-1px;">
                        {{ $discount->formatted_value }}
                    </div>
                    @if($discount->type==='percentage' && $discount->max_discount)
                    <div class="text-muted" style="font-size:.72rem;">
                        maks. Rp {{ number_format($discount->max_discount, 0, ',', '.') }}
                    </div>
                    @endif
                </div>

                {{-- Kode kupon --}}
                @if($discount->code)
                <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded-3"
                    style="background:#f8f9fa; border:1px solid #e9ecef;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ti ti-ticket text-muted" style="font-size:.9rem;"></i>
                        <code class="fw-bold" style="font-size:.85rem; color:#495057; letter-spacing:1px;">{{ $discount->code }}</code>
                    </div>
                    <button class="btn btn-xs p-1 copy-code" data-code="{{ $discount->code }}" title="Salin kode"
                        style="background:none;border:none;color:#6c757d;">
                        <i class="ti ti-copy" style="font-size:.85rem;"></i>
                    </button>
                </div>
                @endif

                {{-- Info tambahan --}}
                <div class="d-flex flex-column gap-1 mb-3" style="font-size:.75rem;">
                    @if($discount->min_transaction)
                    <div class="d-flex align-items-center gap-2 text-muted">
                        <i class="ti ti-cash flex-shrink-0"></i>
                        <span>Min. transaksi: <strong class="text-dark">Rp {{ number_format($discount->min_transaction, 0, ',', '.') }}</strong></span>
                    </div>
                    @endif
                    @if($discount->usage_limit)
                    <div class="d-flex align-items-center gap-2 text-muted">
                        <i class="ti ti-repeat flex-shrink-0"></i>
                        <span>Digunakan: <strong class="text-dark">{{ $discount->usage_count }} / {{ $discount->usage_limit }}x</strong></span>
                    </div>
                    @endif
                    @if($discount->start_date || $discount->end_date)
                    <div class="d-flex align-items-center gap-2 text-muted">
                        <i class="ti ti-calendar flex-shrink-0"></i>
                        <span>
                            {{ $discount->start_date ? $discount->start_date->format('d M Y') : '–' }}
                            →
                            {{ $discount->end_date ? $discount->end_date->format('d M Y') : 'Selamanya' }}
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="d-flex align-items-center justify-content-between pt-3" style="border-top:1px solid #f0f0f0;">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input toggle-discount me-1" type="checkbox" role="switch"
                            id="toggle-{{ $discount->id }}"
                            {{ $discount->is_active ? 'checked' : '' }}
                            data-id="{{ $discount->id }}"
                            data-url="{{ route('admin.discounts.toggle', $discount) }}"
                            style="cursor:pointer;">
                        <label class="form-check-label text-muted small toggle-label" for="toggle-{{ $discount->id }}" style="cursor:pointer;">
                            {{ $discount->is_active ? 'Aktif' : 'Nonaktif' }}
                        </label>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.discounts.edit', $discount) }}"
                            class="btn btn-sm d-flex align-items-center justify-content-center rounded-2"
                            style="width:30px;height:30px;background:#fd7e1415;color:#fd7e14;" title="Edit">
                            <i class="ti ti-edit" style="font-size:.85rem;"></i>
                        </a>
                        <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button"
                                class="btn btn-sm d-flex align-items-center justify-content-center rounded-2 btn-delete"
                                style="width:30px;height:30px;background:#dc354515;color:#dc3545;"
                                data-name="{{ $discount->name }}" title="Hapus">
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
@if($discounts->hasPages())
<div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
    <div class="text-muted small">
        Menampilkan <strong>{{ $discounts->firstItem() }}</strong>–<strong>{{ $discounts->lastItem() }}</strong>
        dari <strong>{{ $discounts->total() }}</strong> diskon
    </div>
    {{ $discounts->appends(request()->query())->links('vendor.pagination.custom') }}
</div>
@else
<div class="text-muted small">Menampilkan {{ $discounts->count() }} diskon</div>
@endif

@else
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body text-center py-5">
        <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle"
            style="width:72px;height:72px;background:linear-gradient(135deg,#fff3e0,#fff8f0);">
            <i class="ti ti-tag fs-1" style="color:#fd7e14;"></i>
        </div>
        <h5 class="fw-semibold text-muted mb-1">
            {{ request()->hasAny(['search','type','status']) ? 'Tidak Ada Hasil' : 'Belum Ada Diskon' }}
        </h5>
        <p class="small text-muted mb-3">
            {{ request()->hasAny(['search','type','status']) ? 'Coba ubah filter pencarian.' : 'Buat diskon pertama untuk menarik lebih banyak pelanggan.' }}
        </p>
        @if(request()->hasAny(['search','type','status']))
        <a href="{{ route('admin.discounts.index') }}" class="btn btn-light btn-sm rounded-pill px-4">Reset</a>
        @else
        <a href="{{ route('admin.discounts.create') }}"
            class="btn btn-sm rounded-pill px-4 fw-medium text-white"
            style="background:linear-gradient(135deg,#fd7e14,#e05a00);">
            <i class="ti ti-plus me-1"></i>Buat Diskon
        </a>
        @endif
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Hover card
    document.querySelectorAll('.discount-card').forEach(card => {
        card.addEventListener('mouseenter', () => { card.style.transform='translateY(-4px)'; card.style.boxShadow='0 10px 30px rgba(0,0,0,.1)'; });
        card.addEventListener('mouseleave', () => { card.style.transform=''; card.style.boxShadow=''; });
    });

    // Copy kode
    document.querySelectorAll('.copy-code').forEach(btn => {
        btn.addEventListener('click', function () {
            navigator.clipboard.writeText(this.dataset.code).then(() => {
                Swal.fire({ toast:true, position:'bottom-end', icon:'success', title:`Kode "${this.dataset.code}" disalin!`, showConfirmButton:false, timer:2000 });
            });
        });
    });

    // Toggle
    document.querySelectorAll('.toggle-discount').forEach(toggle => {
        toggle.addEventListener('change', async function () {
            const url  = this.dataset.url;
            const card = this.closest('.discount-card');
            const label = document.querySelector(`label[for="toggle-${this.dataset.id}"]`);
            try {
                const res  = await fetch(url, { method:'POST', headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept':'application/json' } });
                const data = await res.json();
                label.textContent = data.is_active ? 'Aktif' : 'Nonaktif';
                card.style.opacity = data.is_active ? '1' : '0.7';
                Swal.fire({ toast:true, position:'bottom-end', icon: data.is_active ? 'success':'info', title:data.message, showConfirmButton:false, timer:2500, timerProgressBar:true });
            } catch(e) {
                this.checked = !this.checked;
                Swal.fire('Error', 'Gagal mengubah status.', 'error');
            }
        });
    });

    // Delete confirm
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = this.closest('form');
            Swal.fire({
                title:'Hapus Diskon?',
                html:`Diskon <strong>${this.dataset.name}</strong> akan dihapus permanen.`,
                icon:'warning', showCancelButton:true,
                confirmButtonColor:'#dc3545', cancelButtonColor:'#6c757d',
                confirmButtonText:'<i class="ti ti-trash me-1"></i>Ya, Hapus!',
                cancelButtonText:'Batal', reverseButtons:true,
            }).then(r => { if(r.isConfirmed) form.submit(); });
        });
    });
});
</script>
@endpush
@endsection
