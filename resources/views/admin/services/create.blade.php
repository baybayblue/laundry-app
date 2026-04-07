@extends('layouts.app')

@section('content')

{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                style="background:linear-gradient(135deg,#6f42c120,#6f42c110); border:1px solid #6f42c130; width:48px;height:48px;">
                <i class="ti ti-plus fs-3" style="color:#6f42c1;"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Tambah Layanan</h1>
                <p class="mb-0 text-muted small">Daftarkan jenis layanan laundry baru</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('admin.services.index') }}"
            class="btn btn-light d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
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
    {{-- FORM KIRI --}}
    <div class="col-12 col-lg-7">
        <form action="{{ route('admin.services.store') }}" method="POST" id="serviceForm" novalidate>
            @csrf

            {{-- INFO LAYANAN --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="rounded-top-4" style="height:4px; background:linear-gradient(90deg,#6f42c1,#8b5cf6);"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-2 p-1" style="background:#6f42c115;">
                            <i class="ti ti-ironing fs-5" style="color:#6f42c1;"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Informasi Layanan</h6>
                    </div>
                    <hr class="mb-3 mt-0">

                    <div class="mb-3">
                        <label class="form-label fw-medium small" for="name">
                            Nama Layanan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="ti ti-ironing text-muted"></i>
                            </span>
                            <input type="text"
                                class="form-control border-start-0 @error('name') is-invalid @enderror"
                                id="name" name="name"
                                value="{{ old('name') }}"
                                required placeholder="Contoh: Cuci Reguler, Express...">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small" for="description">
                            Deskripsi <span class="text-muted fw-normal">(Opsional)</span>
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                            id="description" name="description" rows="2"
                            placeholder="Keterangan singkat tentang layanan ini...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Status aktif --}}
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#f8f9fa;">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="is_active" name="is_active" value="1"
                                {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                        </div>
                        <div>
                            <label class="fw-medium small mb-0" for="is_active">Aktifkan Layanan</label>
                            <p class="text-muted mb-0" style="font-size:.72rem;">Layanan yang aktif bisa dipilih saat membuat transaksi</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HARGA & WAKTU --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-2 p-1 bg-success bg-opacity-10">
                            <i class="ti ti-currency-dollar text-success fs-5"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Harga & Estimasi</h6>
                    </div>
                    <hr class="mb-3 mt-0">

                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-medium small">
                                Tipe Harga <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex flex-column gap-2">
                                @foreach([
                                    ['per_kg',  'ti-weight',     'per Kg',  'Harga per kilogram'],
                                    ['per_pcs', 'ti-package',    'per Pcs', 'Harga per satuan/item'],
                                    ['flat',    'ti-minus',      'Flat',    'Harga tetap sekali bayar'],
                                ] as [$val, $ico, $lbl, $hint])
                                <label class="d-flex align-items-center gap-3 p-3 rounded-3 border type-option"
                                    style="cursor:pointer; transition:all .15s;">
                                    <input type="radio" name="type" value="{{ $val }}" class="form-check-input mt-0 type-radio"
                                        {{ old('type', 'per_kg') === $val ? 'checked' : '' }}>
                                    <div class="rounded-2 p-1 flex-shrink-0" style="background:#6f42c115;">
                                        <i class="ti {{ $ico }} fs-5" style="color:#6f42c1;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium small">{{ $lbl }}</div>
                                        <div class="text-muted" style="font-size:.7rem;">{{ $hint }}</div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-7">
                            <div class="mb-3">
                                <label class="form-label fw-medium small" for="price">
                                    Harga <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-medium">Rp</span>
                                    <input type="number"
                                        class="form-control @error('price') is-invalid @enderror"
                                        id="price" name="price"
                                        value="{{ old('price', 0) }}"
                                        min="0" step="500"
                                        placeholder="0">
                                    <span class="input-group-text bg-light text-muted small" id="priceUnit">/ Kg</span>
                                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div>
                                <label class="form-label fw-medium small" for="estimated_hours">
                                    Estimasi Waktu <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="ti ti-clock text-muted"></i></span>
                                    <input type="number"
                                        class="form-control @error('estimated_hours') is-invalid @enderror"
                                        id="estimated_hours" name="estimated_hours"
                                        value="{{ old('estimated_hours', 24) }}"
                                        min="1" max="720" placeholder="24">
                                    <span class="input-group-text bg-light text-muted small">jam</span>
                                    @error('estimated_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="text-muted mt-1" id="estimatedDisplay" style="font-size:.72rem;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAMPILAN --}}
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
                            <label class="form-label fw-medium small">Warna <span class="text-danger">*</span></label>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <input type="color" class="form-control form-control-color flex-shrink-0"
                                    id="color" name="color" value="{{ old('color', '#6f42c1') }}"
                                    style="width:44px;height:36px;padding:2px;border-radius:6px;">
                                <input type="text" class="form-control form-control-sm font-monospace"
                                    id="colorHex" value="{{ old('color', '#6f42c1') }}" maxlength="7">
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(['#0d6efd','#6f42c1','#20c997','#fd7e14','#198754','#0dcaf0','#dc3545','#ffc107','#e83e8c','#6c757d'] as $p)
                                <button type="button" class="btn-color-preset border-0 shadow-sm"
                                    style="width:26px;height:26px;background:{{ $p }};border-radius:6px;cursor:pointer;transition:transform .15s;"
                                    data-color="{{ $p }}"></button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Ikon --}}
                        <div class="col-md-7">
                            <label class="form-label fw-medium small">Ikon <span class="text-danger">*</span></label>
                            <input type="hidden" name="icon" id="iconInput" value="{{ old('icon', 'ti-wash') }}">
                            @php
                                $icons = ['ti-wash','ti-bolt','ti-shirt','ti-temperature','ti-droplet-off','ti-shoe','ti-heart',
                                          'ti-stars','ti-sparkles','ti-wind','ti-feather','ti-leaf','ti-package','ti-box','ti-tags','ti-hanger'];
                                $curIcon  = old('icon', 'ti-wash');
                                $curColor = old('color', '#6f42c1');
                            @endphp
                            <div class="d-flex flex-wrap gap-2" id="iconGrid">
                                @foreach($icons as $icon)
                                <button type="button"
                                    class="btn btn-sm icon-btn rounded-2 d-flex align-items-center justify-content-center"
                                    style="width:38px;height:38px;
                                        background:{{ $curIcon===$icon ? $curColor.'18' : '#f8f9fa' }};
                                        border:2px solid {{ $curIcon===$icon ? $curColor : 'transparent' }};
                                        transition:all .15s;"
                                    data-icon="{{ $icon }}" title="{{ $icon }}">
                                    <i class="ti {{ $icon }}" style="font-size:1.1rem;"></i>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.services.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                <button type="submit" class="btn rounded-pill px-5 fw-semibold shadow-sm text-white" id="btnSubmit"
                    style="background:linear-gradient(135deg,#6f42c1,#8b5cf6);">
                    <span class="btn-text"><i class="ti ti-plus me-1"></i>Tambah Layanan</span>
                    <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>

    {{-- SIDEBAR KANAN --}}
    <div class="col-12 col-lg-5">
        <div class="position-sticky d-flex flex-column gap-3" style="top:80px;">

            {{-- Preview --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <p class="fw-semibold text-muted small text-uppercase mb-3" style="letter-spacing:.6px;font-size:.7rem;">
                        <i class="ti ti-eye me-1"></i>Preview Kartu
                    </p>

                    <div class="rounded-4 overflow-hidden border" id="previewCard" style="background:#fff;">
                        <div id="previewBar" style="height:5px;background:#6f42c1;"></div>
                        <div class="p-3">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                    id="previewIconWrap"
                                    style="width:44px;height:44px;background:#6f42c118;border:1.5px solid #6f42c135;">
                                    <i class="ti ti-wash" id="previewIcon" style="color:#6f42c1;font-size:1.2rem;"></i>
                                </div>
                                <div class="flex-fill min-w-0">
                                    <div class="fw-bold small mb-1 text-truncate" id="previewName">Nama Layanan</div>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <span class="badge rounded-pill" id="previewTypeBadge"
                                            style="background:#0d6efd18;color:#0d6efd;font-size:.65rem;">per Kg</span>
                                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success" style="font-size:.65rem;">Aktif</span>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-3 p-2 mb-2" id="previewPriceBox"
                                style="background:#6f42c108;border:1px solid #6f42c120;">
                                <div class="d-flex align-items-baseline gap-1">
                                    <span class="fw-bold" id="previewPrice" style="color:#6f42c1;">Rp 0</span>
                                    <span class="text-muted" id="previewPriceUnit" style="font-size:.75rem;">/ Kg</span>
                                </div>
                                <div class="text-muted" id="previewEstimate" style="font-size:.7rem;">
                                    <i class="ti ti-clock me-1"></i>Estimasi: 24 jam
                                </div>
                            </div>

                            <p class="text-muted mb-0" id="previewDesc" style="font-size:.78rem;line-height:1.45;">
                                Belum ada deskripsi.
                            </p>
                        </div>
                    </div>

                    <p class="text-muted mt-2 mb-0" style="font-size:.72rem;">
                        <i class="ti ti-refresh me-1"></i>Preview diperbarui otomatis.
                    </p>
                </div>
            </div>

            {{-- Panduan --}}
            <div class="card border-0 shadow-sm rounded-4" style="background:linear-gradient(135deg,#0d6efd08,#fff);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="ti ti-bulb text-warning fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-warning">Panduan Pengisian</h6>
                    </div>
                    <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
                        <li class="d-flex gap-2">
                            <i class="ti ti-check-circle text-success flex-shrink-0 mt-1"></i>
                            <span><strong>per Kg</strong> — untuk cucian reguler yang dihitung per berat (kg)</span>
                        </li>
                        <li class="d-flex gap-2">
                            <i class="ti ti-check-circle text-success flex-shrink-0 mt-1"></i>
                            <span><strong>per Pcs</strong> — untuk item satuan seperti sepatu, jas, atau gaun</span>
                        </li>
                        <li class="d-flex gap-2">
                            <i class="ti ti-check-circle text-success flex-shrink-0 mt-1"></i>
                            <span><strong>Flat</strong> — harga tetap untuk item khusus seperti selimut atau boneka</span>
                        </li>
                        <li class="d-flex gap-2">
                            <i class="ti ti-check-circle text-success flex-shrink-0 mt-1"></i>
                            <span><strong>Estimasi</strong> — waktu pengerjaan dari diterima hingga selesai</span>
                        </li>
                    </ul>
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
    const typeRadios  = document.querySelectorAll('.type-radio');
    const priceInput  = document.getElementById('price');
    const estInput    = document.getElementById('estimated_hours');

    const typeLabels = { per_kg: 'per Kg', per_pcs: 'per Pcs', flat: 'Flat' };

    // Color picker sync
    colorPicker.addEventListener('input', () => { colorHex.value = colorPicker.value; updatePreview(); });
    colorHex.addEventListener('input', function () {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) { colorPicker.value = this.value; updatePreview(); }
    });

    // Color presets
    document.querySelectorAll('.btn-color-preset').forEach(btn => {
        btn.addEventListener('mouseenter', () => btn.style.transform = 'scale(1.2)');
        btn.addEventListener('mouseleave', () => btn.style.transform = '');
        btn.addEventListener('click', function () {
            colorPicker.value = colorHex.value = this.dataset.color; updatePreview();
        });
    });

    // Icon selection
    document.querySelectorAll('.icon-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.icon-btn').forEach(b => {
                b.style.background = '#f8f9fa'; b.style.border = '2px solid transparent';
            });
            const c = colorPicker.value;
            this.style.background = c + '18'; this.style.border = '2px solid ' + c;
            iconInput.value = this.dataset.icon; updatePreview();
        });
    });

    // Type radio — update price unit label
    typeRadios.forEach(r => {
        r.addEventListener('change', () => { updateTypeHighlight(); updatePreview(); });
    });
    function updateTypeHighlight() {
        document.querySelectorAll('.type-option').forEach(opt => {
            const r = opt.querySelector('.type-radio');
            opt.style.borderColor = r.checked ? colorPicker.value : '';
            opt.style.background  = r.checked ? colorPicker.value + '08' : '';
        });
    }

    // Price + estimate
    priceInput.addEventListener('input', updatePreview);
    estInput.addEventListener('input', updatePreview);

    // Name + desc
    document.getElementById('name').addEventListener('input', updatePreview);
    document.getElementById('description').addEventListener('input', updatePreview);

    function getSelectedType() {
        const r = document.querySelector('.type-radio:checked');
        return r ? r.value : 'per_kg';
    }

    function formatEstimate(h) {
        h = parseInt(h) || 0;
        if (h < 24) return h + ' jam';
        const d = Math.floor(h / 24), rem = h % 24;
        return d + ' hari' + (rem > 0 ? ' ' + rem + ' jam' : '');
    }

    function formatPrice(n) {
        n = parseInt(n) || 0;
        return 'Rp ' + n.toLocaleString('id-ID');
    }

    function updatePreview() {
        const color = colorPicker.value || '#6f42c1';
        const icon  = iconInput.value  || 'ti-wash';
        const name  = document.getElementById('name').value || 'Nama Layanan';
        const desc  = document.getElementById('description').value || 'Belum ada deskripsi.';
        const type  = getSelectedType();
        const price = priceInput.value;
        const est   = estInput.value;
        const unit  = typeLabels[type] || type;

        document.getElementById('previewBar').style.background           = color;
        document.getElementById('previewIconWrap').style.background      = color + '18';
        document.getElementById('previewIconWrap').style.border          = '1.5px solid ' + color + '35';
        document.getElementById('previewIcon').className                 = 'ti ' + icon;
        document.getElementById('previewIcon').style.color               = color;
        document.getElementById('previewName').textContent               = name;
        document.getElementById('previewDesc').textContent               = desc;
        document.getElementById('previewPrice').textContent              = formatPrice(price);
        document.getElementById('previewPrice').style.color              = color;
        document.getElementById('previewPriceUnit').textContent          = '/ ' + unit;
        document.getElementById('previewPriceBox').style.background      = color + '08';
        document.getElementById('previewPriceBox').style.border          = '1px solid ' + color + '20';
        document.getElementById('previewEstimate').innerHTML             = '<i class="ti ti-clock me-1"></i>Estimasi: ' + formatEstimate(est);
        document.getElementById('previewTypeBadge').textContent          = unit;
        document.getElementById('previewTypeBadge').style.background     = color + '18';
        document.getElementById('previewTypeBadge').style.color          = color;
        document.getElementById('priceUnit').textContent                 = '/ ' + unit;

        // Sync icon highlight
        document.querySelectorAll('.icon-btn').forEach(b => {
            if (b.dataset.icon === icon) { b.style.background = color + '18'; b.style.border = '2px solid ' + color; }
        });

        // Sync type highlight
        updateTypeHighlight();

        // Estimate display
        document.getElementById('estimatedDisplay').textContent = est ? 'Setara dengan ' + formatEstimate(est) : '';
    }

    // Init
    updatePreview();
    updateTypeHighlight();

    // Submit loading
    document.getElementById('serviceForm').addEventListener('submit', function () {
        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.btn-loading').classList.remove('d-none');
    });
});
</script>
@endpush
@endsection
