@extends('layouts.app')

@section('content')

{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            {{-- Ikon kategori dengan warna aslinya --}}
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 position-relative overflow-hidden"
                style="width:52px; height:52px; background:{{ $category->color }}18; border:2px solid {{ $category->color }}35;">
                <i class="ti {{ $category->icon }} fs-3" style="color:{{ $category->color }};"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Edit Kategori</h1>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="badge rounded-pill text-white small"
                        style="background:{{ $category->color }}; font-size:.7rem; padding:3px 10px;">
                        {{ $category->name }}
                    </span>
                    <span class="text-muted small">·</span>
                    <span class="text-muted small">{{ $category->stock_items_count }} barang</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('admin.categories.index') }}"
            class="btn btn-light d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- VALIDATION ERRORS --}}
@if($errors->any())
<div class="alert border-0 rounded-4 shadow-sm mb-4 d-flex align-items-start gap-3" style="background:#dc354510;">
    <i class="ti ti-alert-circle text-danger fs-4 mt-1 flex-shrink-0"></i>
    <div>
        <div class="fw-semibold text-danger mb-1">Terdapat {{ $errors->count() }} kesalahan:</div>
        <ul class="mb-0 small text-danger ps-3">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

<div class="row g-4">
    {{-- ── FORM KIRI ── --}}
    <div class="col-12 col-lg-7">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" id="catForm" novalidate>
            @csrf @method('PUT')

            {{-- SECTION: Info Kategori --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="rounded-top-4" style="height:4px; background:{{ $category->color }};"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-2 p-1" style="background:{{ $category->color }}18;">
                            <i class="ti ti-tags fs-5" style="color:{{ $category->color }};"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Informasi Kategori</h6>
                    </div>
                    <hr class="mb-3 mt-0">

                    <div class="mb-3">
                        <label class="form-label fw-medium small" for="name">
                            Nama Kategori <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="ti ti-tag text-muted"></i>
                            </span>
                            <input type="text"
                                class="form-control border-start-0 @error('name') is-invalid @enderror"
                                id="name" name="name"
                                value="{{ old('name', $category->name) }}"
                                required maxlength="100"
                                placeholder="Nama kategori...">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label fw-medium small" for="description">
                            Deskripsi <span class="text-muted fw-normal">(Opsional)</span>
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                            id="description" name="description" rows="2"
                            placeholder="Keterangan singkat tentang kategori...">{{ old('description', $category->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- SECTION: Tampilan --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-2 p-1 bg-info bg-opacity-10">
                            <i class="ti ti-palette text-info fs-5"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Tampilan</h6>
                    </div>
                    <hr class="mb-3 mt-0">

                    <div class="row g-4">
                        {{-- Warna --}}
                        <div class="col-md-5">
                            <label class="form-label fw-medium small">
                                Warna <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <input type="color"
                                    class="form-control form-control-color flex-shrink-0 @error('color') is-invalid @enderror"
                                    id="color" name="color"
                                    value="{{ old('color', $category->color) }}"
                                    style="width:44px; height:36px; padding:2px; border-radius:6px;">
                                <input type="text"
                                    class="form-control form-control-sm font-monospace"
                                    id="colorHex"
                                    value="{{ old('color', $category->color) }}"
                                    maxlength="7" placeholder="#000000">
                                @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            {{-- Color presets --}}
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                @foreach(['#0d6efd','#6f42c1','#20c997','#fd7e14','#198754','#0dcaf0','#dc3545','#ffc107','#6c757d','#e83e8c'] as $preset)
                                <button type="button"
                                    class="btn-color-preset border-0 shadow-sm"
                                    style="width:26px; height:26px; background:{{ $preset }}; border-radius:6px; cursor:pointer; transition:transform .15s;"
                                    data-color="{{ $preset }}"
                                    title="{{ $preset }}"></button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Ikon --}}
                        <div class="col-md-7">
                            <label class="form-label fw-medium small">
                                Ikon <span class="text-danger">*</span>
                            </label>
                            <input type="hidden" name="icon" id="iconInput" value="{{ old('icon', $category->icon) }}">
                            @php
                                $icons = [
                                    'ti-droplet','ti-wind','ti-feather','ti-star','ti-package',
                                    'ti-bottle','ti-tool','ti-tags','ti-category','ti-box',
                                    'ti-shirt','ti-hanger','ti-recycle','ti-wash','ti-sparkles',
                                    'ti-leaf','ti-flame'
                                ];
                                $currentIcon  = old('icon', $category->icon);
                                $currentColor = old('color', $category->color);
                            @endphp
                            <div class="d-flex flex-wrap gap-2" id="iconGrid">
                                @foreach($icons as $icon)
                                <button type="button"
                                    class="btn btn-sm icon-btn rounded-2 d-flex align-items-center justify-content-center {{ $currentIcon === $icon ? 'icon-selected' : '' }}"
                                    style="width:38px; height:38px;
                                        background: {{ $currentIcon === $icon ? $currentColor.'18' : '#f8f9fa' }};
                                        border: 2px solid {{ $currentIcon === $icon ? $currentColor : 'transparent' }};
                                        transition: all .15s;"
                                    data-icon="{{ $icon }}"
                                    title="{{ $icon }}">
                                    <i class="ti {{ $icon }}" style="font-size:1.1rem;"></i>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <small class="text-muted">
                    <i class="ti ti-clock me-1"></i>
                    Terakhir diperbarui: {{ $category->updated_at->diffForHumans() }}
                </small>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.categories.index') }}"
                        class="btn btn-light rounded-pill px-4">Batal</a>
                    <button type="submit"
                        class="btn rounded-pill px-5 fw-semibold shadow-sm text-white" id="btnSubmit"
                        style="background:linear-gradient(135deg,#6f42c1,#8b5cf6);">
                        <span class="btn-text">
                            <i class="ti ti-device-floppy me-1"></i>Perbarui
                        </span>
                        <span class="btn-loading d-none">
                            <span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ── SIDEBAR KANAN ── --}}
    <div class="col-12 col-lg-5">
        <div class="position-sticky d-flex flex-column gap-3" style="top:80px;">

            {{-- PREVIEW CARD --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <p class="fw-semibold text-muted small text-uppercase mb-3" style="letter-spacing:.6px; font-size:.7rem;">
                        <i class="ti ti-eye me-1"></i>Preview Kartu
                    </p>

                    {{-- Mini card preview --}}
                    <div class="rounded-4 overflow-hidden border" id="previewCard" style="background:#fff;">
                        <div id="previewBar" style="height:5px; background:{{ $currentColor }};"></div>
                        <div class="p-3">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                    id="previewIconWrap"
                                    style="width:44px; height:44px; background:{{ $currentColor }}18; border:1.5px solid {{ $currentColor }}35;">
                                    <i class="ti {{ $currentIcon }}" id="previewIcon"
                                        style="color:{{ $currentColor }}; font-size:1.2rem;"></i>
                                </div>
                                <div class="flex-fill min-w-0">
                                    <div class="fw-bold small mb-1 text-truncate" id="previewName">{{ $category->name }}</div>
                                    <span class="badge rounded-pill text-white" id="previewBadge"
                                        style="background:{{ $currentColor }}; font-size:.65rem; padding:2px 8px;">
                                        {{ $category->stock_items_count }} barang
                                    </span>
                                </div>
                            </div>
                            <p class="text-muted mb-2" id="previewDesc"
                                style="font-size:.78rem; line-height:1.45; min-height:2.2rem;">
                                {{ $category->description ?: 'Belum ada deskripsi.' }}
                            </p>
                            <div class="d-flex align-items-center justify-content-between pt-2"
                                style="border-top:1px solid #f0f0f0;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle" id="previewColorDot"
                                        style="width:12px;height:12px;background:{{ $currentColor }};border:2px solid #fff;outline:1px solid {{ $currentColor }}40;"></div>
                                    <span class="text-muted font-monospace" id="previewHex"
                                        style="font-size:.68rem;">{{ $currentColor }}</span>
                                </div>
                                <div class="d-flex gap-1">
                                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                                        style="width:24px;height:24px;background:#fd7e1415;color:#fd7e14;">
                                        <i class="ti ti-edit" style="font-size:.75rem;"></i>
                                    </div>
                                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                                        style="width:24px;height:24px;background:#dc354515;color:#dc3545;">
                                        <i class="ti ti-trash" style="font-size:.75rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-muted mt-2 mb-0" style="font-size:.72rem;">
                        <i class="ti ti-refresh me-1"></i>Preview diperbarui secara otomatis saat Anda mengedit.
                    </p>
                </div>
            </div>

            {{-- INFO / DANGER ZONE --}}
            @if($category->stock_items_count > 0)
            <div class="card border-0 shadow-sm rounded-4" style="background:linear-gradient(135deg,#0d6efd08,#fff);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="ti ti-info-circle text-primary fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-primary">Info</h6>
                    </div>
                    <p class="small text-muted mb-0">
                        Kategori ini digunakan oleh
                        <strong class="text-dark">{{ $category->stock_items_count }} barang</strong>.
                        Pindahkan semua barang ke kategori lain terlebih dahulu sebelum bisa menghapus kategori ini.
                    </p>
                </div>
            </div>
            @else
            <div class="card border-0 shadow-sm rounded-4" style="background:linear-gradient(135deg,#dc354508,#fff);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="ti ti-alert-triangle text-danger fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-danger">Zona Berbahaya</h6>
                    </div>
                    <p class="small text-muted mb-3">
                        Kategori ini belum memiliki barang, sehingga aman untuk dihapus secara permanen.
                    </p>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                        class="form-delete-sidebar">
                        @csrf @method('DELETE')
                        <button type="button"
                            class="btn btn-outline-danger btn-sm rounded-pill w-100 fw-medium btn-delete-sidebar">
                            <i class="ti ti-trash me-1"></i> Hapus Kategori Ini
                        </button>
                    </form>
                </div>
            </div>
            @endif

        </div>{{-- /sticky wrapper --}}
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const colorPicker = document.getElementById('color');
    const colorHex    = document.getElementById('colorHex');
    const iconInput   = document.getElementById('iconInput');

    // ── Sync color picker ↔ hex text ──
    colorPicker.addEventListener('input', function () {
        colorHex.value = this.value;
        updatePreview();
    });
    colorHex.addEventListener('input', function () {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            colorPicker.value = this.value;
            updatePreview();
        }
    });

    // ── Color presets ──
    document.querySelectorAll('.btn-color-preset').forEach(btn => {
        btn.addEventListener('mouseenter', () => btn.style.transform = 'scale(1.2)');
        btn.addEventListener('mouseleave', () => btn.style.transform = '');
        btn.addEventListener('click', function () {
            colorPicker.value = colorHex.value = this.dataset.color;
            updatePreview();
        });
    });

    // ── Icon selection ──
    document.querySelectorAll('.icon-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.icon-btn').forEach(b => {
                b.style.background = '#f8f9fa';
                b.style.border = '2px solid transparent';
            });
            const c = colorPicker.value;
            this.style.background = c + '18';
            this.style.border     = '2px solid ' + c;
            iconInput.value = this.dataset.icon;
            updatePreview();
        });
    });

    // ── Live text listeners ──
    document.getElementById('name').addEventListener('input', updatePreview);
    document.getElementById('description').addEventListener('input', updatePreview);

    function updatePreview() {
        const color = colorPicker.value || '#6f42c1';
        const icon  = iconInput.value  || 'ti-category';
        const name  = document.getElementById('name').value || 'Nama Kategori';
        const desc  = document.getElementById('description').value || 'Belum ada deskripsi.';

        document.getElementById('previewBar').style.background         = color;
        document.getElementById('previewIconWrap').style.background    = color + '18';
        document.getElementById('previewIconWrap').style.border        = '1.5px solid ' + color + '35';
        document.getElementById('previewIcon').className               = 'ti ' + icon + ' fs-4';
        document.getElementById('previewIcon').style.color             = color;
        document.getElementById('previewName').textContent             = name;
        document.getElementById('previewDesc').textContent             = desc;
        document.getElementById('previewBadge').style.background       = color;
        document.getElementById('previewColorDot').style.background    = color;
        document.getElementById('previewHex').textContent              = color;

        // Sync selected icon highlight with new color
        document.querySelectorAll('.icon-btn').forEach(b => {
            if (b.dataset.icon === icon) {
                b.style.background = color + '18';
                b.style.border     = '2px solid ' + color;
            }
        });
    }

    // ── Loading state on submit ──
    const form      = document.getElementById('catForm');
    const btnSubmit = document.getElementById('btnSubmit');
    form.addEventListener('submit', function () {
        if (!form.checkValidity()) return;
        btnSubmit.disabled = true;
        btnSubmit.querySelector('.btn-text').classList.add('d-none');
        btnSubmit.querySelector('.btn-loading').classList.remove('d-none');
    });

    // ── Danger Zone delete confirm ──
    document.querySelector('.btn-delete-sidebar')?.addEventListener('click', function () {
        const f = document.querySelector('.form-delete-sidebar');
        Swal.fire({
            title: 'Hapus Kategori?',
            html: `Kategori <strong>{{ $category->name }}</strong> akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="ti ti-trash me-1"></i>Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true,
        }).then(r => { if (r.isConfirmed) f.submit(); });
    });
});
</script>
@endpush
@endsection
