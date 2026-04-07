@extends('layouts.app')

@section('content')
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <h1 class="fs-3 mb-0 fw-bold">Tambah Kategori</h1>
        <p class="mb-0 text-muted small">Buat kelompok baru untuk barang stok</p>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-light d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>
</div>

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
    <div class="col-12 col-lg-7">
        <form action="{{ route('admin.categories.store') }}" method="POST" id="catForm" novalidate>
            @csrf
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-1" style="background:#6f42c115;"><i class="ti ti-tags" style="color:#6f42c1;"></i></div>
                        <h6 class="mb-0 fw-semibold">Info Kategori</h6>
                    </div>
                    <hr class="mt-3 mb-0">
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium small" for="name">Nama Kategori <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-tag text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}"
                                    placeholder="Misal: Deterjen, Pewangi, Pelembut..." required maxlength="100">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium small" for="description">Deskripsi <span class="text-muted fw-normal">(Opsional)</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                id="description" name="description" rows="2"
                                placeholder="Keterangan singkat tentang kategori ini...">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Warna & Ikon --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-1 bg-info bg-opacity-10"><i class="ti ti-palette text-info"></i></div>
                        <h6 class="mb-0 fw-semibold">Tampilan</h6>
                    </div>
                    <hr class="mt-3 mb-0">
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-medium small" for="color">Warna Kategori <span class="text-danger">*</span></label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" class="form-control form-control-color flex-shrink-0 @error('color') is-invalid @enderror"
                                    id="color" name="color" value="{{ old('color', '#6f42c1') }}" style="width:48px; height:38px; padding:2px;">
                                <input type="text" class="form-control font-monospace small" id="colorHex"
                                    value="{{ old('color', '#6f42c1') }}" maxlength="7" placeholder="#6f42c1">
                                @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mt-2 d-flex flex-wrap gap-1" id="colorPresets">
                                @foreach(['#0d6efd','#6f42c1','#20c997','#fd7e14','#198754','#0dcaf0','#dc3545','#ffc107','#6c757d','#e83e8c'] as $preset)
                                <button type="button" class="btn-color-preset border-0 rounded-2"
                                    style="width:24px; height:24px; background:{{ $preset }}; cursor:pointer;"
                                    data-color="{{ $preset }}" title="{{ $preset }}"></button>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-medium small">Ikon <span class="text-danger">*</span></label>
                            <input type="hidden" name="icon" id="iconInput" value="{{ old('icon', 'ti-category') }}">
                            <div class="d-flex flex-wrap gap-2" id="iconGrid">
                                @php
                                $icons = ['ti-droplet','ti-wind','ti-feather','ti-star','ti-package','ti-bottle','ti-tool',
                                          'ti-tags','ti-category','ti-box','ti-shirt','ti-hanger','ti-recycle',
                                          'ti-wash','ti-sparkles','ti-leaf','ti-flame'];
                                @endphp
                                @foreach($icons as $icon)
                                <button type="button"
                                    class="btn btn-sm icon-btn rounded-2 d-flex align-items-center justify-content-center {{ old('icon','ti-category') === $icon ? 'icon-selected' : '' }}"
                                    style="width:38px; height:38px; background: {{ old('icon','ti-category') === $icon ? '#6f42c115' : '#f8f9fa' }}; border: 2px solid {{ old('icon','ti-category') === $icon ? '#6f42c1' : 'transparent' }};"
                                    data-icon="{{ $icon }}" title="{{ $icon }}">
                                    <i class="ti {{ $icon }}"></i>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                <button type="submit" class="btn rounded-pill px-5 fw-semibold shadow-sm text-white" id="btnSubmit"
                    style="background: linear-gradient(135deg, #6f42c1, #8b5cf6);">
                    <span class="btn-text"><i class="ti ti-check me-1"></i>Simpan Kategori</span>
                    <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>

    {{-- PREVIEW --}}
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 mb-3 position-sticky" style="top:80px;">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3">Preview Kartu Kategori</h6>
                <div class="card border-0 rounded-4 shadow-sm" id="previewCard" style="background: #fff;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                id="previewIconWrap"
                                style="width:52px; height:52px; background:#6f42c120; border: 2px solid #6f42c140;">
                                <i class="ti ti-category fs-3" id="previewIcon" style="color:#6f42c1;"></i>
                            </div>
                            <div class="flex-fill">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <h6 class="fw-bold mb-0" id="previewName" style="color:#212529;">Nama Kategori</h6>
                                    <span class="badge rounded-pill text-white small" id="previewBadge"
                                        style="background:#6f42c1;">0 item</span>
                                </div>
                                <p class="text-muted small mb-0" id="previewDesc">Deskripsi kategori akan muncul di sini.</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-3 pt-3" style="border-top: 1px solid #f0f0f0;">
                            <div class="rounded-circle border" id="previewColorDot" style="width:16px; height:16px; background:#6f42c1;"></div>
                            <span class="text-muted small font-monospace" id="previewHex">#6f42c1</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const colorPicker = document.getElementById('color');
    const colorHex    = document.getElementById('colorHex');
    const iconInput   = document.getElementById('iconInput');

    // Sync color picker ↔ hex input
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

    // Color presets
    document.querySelectorAll('.btn-color-preset').forEach(btn => {
        btn.addEventListener('click', function () {
            const c = this.dataset.color;
            colorPicker.value = c;
            colorHex.value    = c;
            updatePreview();
        });
    });

    // Icon selection
    document.querySelectorAll('.icon-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.icon-btn').forEach(b => {
                b.style.background = '#f8f9fa';
                b.style.border = '2px solid transparent';
            });
            this.style.background = colorPicker.value + '20';
            this.style.border     = '2px solid ' + colorPicker.value;
            iconInput.value = this.dataset.icon;
            updatePreview();
        });
    });

    // Name & desc preview
    document.getElementById('name').addEventListener('input', updatePreview);
    document.getElementById('description').addEventListener('input', updatePreview);

    function updatePreview() {
        const color = colorPicker.value || '#6f42c1';
        const icon  = iconInput.value  || 'ti-category';
        const name  = document.getElementById('name').value || 'Nama Kategori';
        const desc  = document.getElementById('description').value || 'Deskripsi kategori akan muncul di sini.';

        document.getElementById('previewIconWrap').style.background = color + '20';
        document.getElementById('previewIconWrap').style.border     = '2px solid ' + color + '40';
        document.getElementById('previewIcon').className = 'ti ' + icon + ' fs-3';
        document.getElementById('previewIcon').style.color   = color;
        document.getElementById('previewName').textContent   = name;
        document.getElementById('previewDesc').textContent   = desc;
        document.getElementById('previewBadge').style.background = color;
        document.getElementById('previewColorDot').style.background = color;
        document.getElementById('previewHex').textContent = color;

        // juga update border selected icon
        document.querySelectorAll('.icon-btn').forEach(b => {
            if (b.dataset.icon === icon) {
                b.style.background = color + '20';
                b.style.border     = '2px solid ' + color;
            }
        });
    }

    // Loading state on submit
    const form = document.getElementById('catForm');
    const btn  = document.getElementById('btnSubmit');
    form.addEventListener('submit', function () {
        if (!form.checkValidity()) return;
        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.btn-loading').classList.remove('d-none');
    });
});
</script>
@endpush
@endsection
