@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                style="background:linear-gradient(135deg,#fd7e1420,#fd7e1410); border:1px solid #fd7e1430; width:48px;height:48px;">
                <i class="ti ti-plus fs-3" style="color:#fd7e14;"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Buat Diskon Baru</h1>
                <p class="mb-0 text-muted small">Tambahkan kode promo atau diskon untuk transaksi</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('admin.discounts.index') }}"
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
    {{-- FORM --}}
    <div class="col-12 col-lg-7">
        <form action="{{ route('admin.discounts.store') }}" method="POST" id="discountForm" novalidate>
            @csrf

            {{-- INFO DASAR --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="rounded-top-4" style="height:4px; background:linear-gradient(90deg,#fd7e14,#ffc107);"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-2 p-1" style="background:#fd7e1415;">
                            <i class="ti ti-tag fs-5" style="color:#fd7e14;"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Informasi Diskon</h6>
                    </div>
                    <hr class="mb-3 mt-0">

                    <div class="mb-3">
                        <label class="form-label fw-medium small" for="name">
                            Nama Diskon <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="ti ti-tag text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name') }}"
                                placeholder="Contoh: Promo Harbolnas, Member VIP..." required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Kode Kupon --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium small" for="code">
                            Kode Kupon <span class="text-muted fw-normal">(Opsional — akan digenerate otomatis jika kosong)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="ti ti-ticket text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 font-monospace text-uppercase @error('code') is-invalid @enderror"
                                id="code" name="code" value="{{ old('code') }}"
                                placeholder="Contoh: LEBARAN30, MEMBER20..." maxlength="50"
                                oninput="this.value=this.value.toUpperCase()">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3" id="btnGenCode" title="Generate kode acak">
                                <i class="ti ti-refresh"></i>
                            </button>
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="text-muted mt-1" style="font-size:.72rem;">
                            <i class="ti ti-info-circle me-1"></i>Gunakan huruf kapital tanpa spasi. Kode ini yang dimasukkan pelanggan saat transaksi.
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-medium small" for="description">
                            Deskripsi <span class="text-muted fw-normal">(Opsional)</span>
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                            id="description" name="description" rows="2"
                            placeholder="Keterangan singkat tentang promo ini...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- NILAI DISKON --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-2 p-1 bg-success bg-opacity-10">
                            <i class="ti ti-currency-dollar text-success fs-5"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Nilai Diskon</h6>
                    </div>
                    <hr class="mb-3 mt-0">

                    {{-- Tipe --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium small">Tipe Diskon <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <label class="flex-fill d-flex align-items-center gap-3 p-3 rounded-3 border type-option" style="cursor:pointer;transition:all .15s;">
                                <input type="radio" name="type" value="percentage" class="form-check-input mt-0 type-radio"
                                    {{ old('type', 'percentage') === 'percentage' ? 'checked' : '' }}>
                                <div>
                                    <div class="fw-semibold mb-0">Persentase</div>
                                    <div class="text-muted" style="font-size:.72rem;">Diskon berdasarkan % dari total</div>
                                </div>
                                <span class="ms-auto fw-bold fs-5" style="color:#6f42c1;">%</span>
                            </label>
                            <label class="flex-fill d-flex align-items-center gap-3 p-3 rounded-3 border type-option" style="cursor:pointer;transition:all .15s;">
                                <input type="radio" name="type" value="fixed" class="form-check-input mt-0 type-radio"
                                    {{ old('type') === 'fixed' ? 'checked' : '' }}>
                                <div>
                                    <div class="fw-semibold mb-0">Nominal</div>
                                    <div class="text-muted" style="font-size:.72rem;">Potongan harga tetap (Rp)</div>
                                </div>
                                <span class="ms-auto fw-bold fs-6" style="color:#0d6efd;">Rp</span>
                            </label>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="value">
                                Nilai <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-medium" id="valuePrefix">%</span>
                                <input type="number" class="form-control @error('value') is-invalid @enderror"
                                    id="value" name="value" value="{{ old('value', 0) }}"
                                    min="0.01" step="0.01" placeholder="0">
                                @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6" id="maxDiscountWrapper">
                            <label class="form-label fw-medium small" for="max_discount">
                                Maks. Potongan <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-medium">Rp</span>
                                <input type="number" class="form-control @error('max_discount') is-invalid @enderror"
                                    id="max_discount" name="max_discount" value="{{ old('max_discount') }}"
                                    min="0" step="1000" placeholder="Batas maks diskon...">
                                @error('max_discount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SYARAT & KETENTUAN --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-2 p-1 bg-info bg-opacity-10">
                            <i class="ti ti-adjustments text-info fs-5"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Syarat & Ketentuan</h6>
                    </div>
                    <hr class="mb-3 mt-0">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="min_transaction">
                                Min. Transaksi <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-medium">Rp</span>
                                <input type="number" class="form-control @error('min_transaction') is-invalid @enderror"
                                    id="min_transaction" name="min_transaction" value="{{ old('min_transaction') }}"
                                    min="0" step="1000" placeholder="Minimum total belanja...">
                                @error('min_transaction')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="usage_limit">
                                Batas Penggunaan <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="ti ti-repeat text-muted"></i></span>
                                <input type="number" class="form-control @error('usage_limit') is-invalid @enderror"
                                    id="usage_limit" name="usage_limit" value="{{ old('usage_limit') }}"
                                    min="1" placeholder="Kosong = tanpa batas...">
                                <span class="input-group-text bg-light text-muted small">kali</span>
                                @error('usage_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="start_date">
                                Berlaku Mulai <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                id="start_date" name="start_date" value="{{ old('start_date') }}">
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="end_date">
                                Berlaku Sampai <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                id="end_date" name="end_date" value="{{ old('end_date') }}">
                            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mt-3 d-flex align-items-center gap-3 p-3 rounded-3" style="background:#f8f9fa;">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="is_active" name="is_active" value="1"
                                {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                        </div>
                        <div>
                            <label class="fw-medium small mb-0" for="is_active">Aktifkan Diskon</label>
                            <p class="text-muted mb-0" style="font-size:.72rem;">Diskon yang aktif bisa digunakan dalam transaksi</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.discounts.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                <button type="submit" id="btnSubmit"
                    class="btn rounded-pill px-5 fw-semibold shadow-sm text-white"
                    style="background:linear-gradient(135deg,#fd7e14,#e05a00);">
                    <span class="btn-text"><i class="ti ti-plus me-1"></i>Simpan Diskon</span>
                    <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>

    {{-- SIDEBAR --}}
    <div class="col-12 col-lg-5">
        <div class="position-sticky d-flex flex-column gap-3" style="top:80px;">

            {{-- Live Preview --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <p class="fw-semibold text-muted small text-uppercase mb-3" style="letter-spacing:.6px;font-size:.7rem;">
                        <i class="ti ti-eye me-1"></i>Preview Diskon
                    </p>

                    <div class="rounded-4 overflow-hidden border" id="previewCard">
                        <div id="previewBar" style="height:4px;background:#fd7e14;"></div>
                        <div class="p-3">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div>
                                    <div class="fw-bold small text-truncate" id="previewName">Nama Diskon</div>
                                    <span class="badge rounded-pill small mt-1" style="background:#19875418;color:#198754;font-size:.65rem;">
                                        <i class="ti ti-check-circle me-1"></i>Aktif
                                    </span>
                                </div>
                                <span class="badge rounded-pill flex-shrink-0" id="previewTypeBadge"
                                    style="background:#6f42c118;color:#6f42c1;font-size:.7rem;">%</span>
                            </div>

                            <div class="text-center py-3 rounded-3 mb-2" id="previewValueBox"
                                style="background:linear-gradient(135deg,#fd7e1412,#fd7e1405);border:1px dashed #fd7e1440;">
                                <div class="fw-bold" id="previewValue" style="font-size:2rem;color:#fd7e14;letter-spacing:-1px;">0%</div>
                            </div>

                            <div class="rounded-3 p-2 mb-2" style="background:#f8f9fa;border:1px solid #e9ecef;">
                                <code class="fw-bold" id="previewCode" style="font-size:.85rem;color:#495057;letter-spacing:1px;">XXXXXXXX</code>
                            </div>

                            <div class="d-flex flex-column gap-1" style="font-size:.72rem;color:#6c757d;">
                                <div id="previewMinTrx" class="d-none d-flex align-items-center gap-1">
                                    <i class="ti ti-cash"></i><span>Min. transaksi: <strong id="previewMinVal" class="text-dark"></strong></span>
                                </div>
                                <div id="previewMaxDisc" class="d-none d-flex align-items-center gap-1">
                                    <i class="ti ti-arrow-down-circle"></i><span>Maks. potongan: <strong id="previewMaxVal" class="text-dark"></strong></span>
                                </div>
                                <div id="previewUsage" class="d-none d-flex align-items-center gap-1">
                                    <i class="ti ti-repeat"></i><span>Batas: <strong id="previewUsageVal" class="text-dark"></strong>x</span>
                                </div>
                                <div id="previewDates" class="d-none d-flex align-items-center gap-1">
                                    <i class="ti ti-calendar"></i><span id="previewDatesVal"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-muted mt-2 mb-0" style="font-size:.72rem;">
                        <i class="ti ti-refresh me-1"></i>Preview diperbarui otomatis.
                    </p>
                </div>
            </div>

            {{-- Simulasi Diskon --}}
            <div class="card border-0 shadow-sm rounded-4" style="background:linear-gradient(135deg,#fd7e1408,#fff);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="ti ti-calculator text-warning fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-warning">Simulasi Diskon</h6>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-medium small mb-1" for="simAmount">Jumlah Transaksi</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light fw-medium">Rp</span>
                            <input type="number" class="form-control" id="simAmount" min="0" step="1000" placeholder="50000">
                        </div>
                    </div>
                    <div class="rounded-3 p-3" style="background:#fff;border:1px solid #f0f0f0;">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Subtotal</span>
                            <span id="simSubtotal">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-2 text-danger">
                            <span><i class="ti ti-tag me-1"></i>Diskon</span>
                            <span id="simDiscount">– Rp 0</span>
                        </div>
                        <hr class="my-1">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total Bayar</span>
                            <span id="simTotal" class="text-success">Rp 0</span>
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
    const nameEl  = document.getElementById('name');
    const codeEl  = document.getElementById('code');
    const typeRadios = document.querySelectorAll('.type-radio');
    const valueEl = document.getElementById('value');
    const minTrxEl = document.getElementById('min_transaction');
    const maxDiscEl = document.getElementById('max_discount');
    const limitEl  = document.getElementById('usage_limit');
    const startEl  = document.getElementById('start_date');
    const endEl    = document.getElementById('end_date');

    function fmt(n) { return 'Rp ' + (parseInt(n)||0).toLocaleString('id-ID'); }
    function getType() { return document.querySelector('.type-radio:checked')?.value || 'percentage'; }

    function updateTypeUI() {
        const t = getType();
        document.getElementById('valuePrefix').textContent = t === 'percentage' ? '%' : 'Rp';
        document.getElementById('maxDiscountWrapper').style.display = t === 'percentage' ? '' : 'none';
        document.querySelectorAll('.type-option').forEach(opt => {
            const r = opt.querySelector('.type-radio');
            opt.style.borderColor = r.checked ? '#fd7e14' : '';
            opt.style.background  = r.checked ? '#fd7e1408' : '';
        });
    }

    function updatePreview() {
        const t     = getType();
        const val   = parseFloat(valueEl.value) || 0;
        const name  = nameEl.value || 'Nama Diskon';
        const code  = codeEl.value || 'XXXXXXXX';
        const min   = parseFloat(minTrxEl.value) || 0;
        const max   = parseFloat(maxDiscEl.value) || 0;
        const limit = parseInt(limitEl.value) || 0;
        const start = startEl.value;
        const end   = endEl.value;

        document.getElementById('previewName').textContent = name;
        document.getElementById('previewCode').textContent = code.toUpperCase() || 'XXXXXXXX';
        document.getElementById('previewTypeBadge').textContent = t === 'percentage' ? '%' : 'Rp';
        document.getElementById('previewTypeBadge').style.color = t === 'percentage' ? '#6f42c1' : '#0d6efd';
        document.getElementById('previewTypeBadge').style.background = t === 'percentage' ? '#6f42c118' : '#0d6efd18';
        document.getElementById('previewValue').textContent = t === 'percentage' ? val + '%' : 'Rp ' + val.toLocaleString('id-ID');

        const pMin = document.getElementById('previewMinTrx');
        if (min > 0) { pMin.classList.remove('d-none'); document.getElementById('previewMinVal').textContent = fmt(min); }
        else pMin.classList.add('d-none');

        const pMax = document.getElementById('previewMaxDisc');
        if (max > 0 && t === 'percentage') { pMax.classList.remove('d-none'); document.getElementById('previewMaxVal').textContent = fmt(max); }
        else pMax.classList.add('d-none');

        const pUsage = document.getElementById('previewUsage');
        if (limit > 0) { pUsage.classList.remove('d-none'); document.getElementById('previewUsageVal').textContent = limit; }
        else pUsage.classList.add('d-none');

        const pDates = document.getElementById('previewDates');
        if (start || end) {
            pDates.classList.remove('d-none');
            document.getElementById('previewDatesVal').textContent = (start || '–') + ' → ' + (end || 'Selamanya');
        } else pDates.classList.add('d-none');

        updateSimulation();
    }

    function updateSimulation() {
        const t     = getType();
        const val   = parseFloat(valueEl.value) || 0;
        const minTrx = parseFloat(minTrxEl.value) || 0;
        const maxD  = parseFloat(maxDiscEl.value) || 0;
        const amount = parseFloat(document.getElementById('simAmount').value) || 0;

        let discount = 0;
        if (amount >= minTrx || minTrx === 0) {
            if (t === 'fixed') discount = Math.min(val, amount);
            else {
                discount = amount * val / 100;
                if (maxD) discount = Math.min(discount, maxD);
            }
        }
        document.getElementById('simSubtotal').textContent = fmt(amount);
        document.getElementById('simDiscount').textContent = '– ' + fmt(discount);
        document.getElementById('simTotal').textContent    = fmt(Math.max(0, amount - discount));
    }

    typeRadios.forEach(r => r.addEventListener('change', () => { updateTypeUI(); updatePreview(); }));
    [nameEl, codeEl, valueEl, minTrxEl, maxDiscEl, limitEl, startEl, endEl].forEach(el => el?.addEventListener('input', updatePreview));
    document.getElementById('simAmount').addEventListener('input', updateSimulation);

    // Auto-generate code
    document.getElementById('btnGenCode').addEventListener('click', async function () {
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        try {
            const res  = await fetch('{{ route("admin.discounts.generate-code") }}');
            const data = await res.json();
            codeEl.value = data.code; updatePreview();
        } catch(e) { console.error(e); }
        this.innerHTML = '<i class="ti ti-refresh"></i>';
    });

    // Submit loading
    document.getElementById('discountForm').addEventListener('submit', function () {
        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.btn-loading').classList.remove('d-none');
    });

    updateTypeUI();
    updatePreview();
});
</script>
@endpush
@endsection
