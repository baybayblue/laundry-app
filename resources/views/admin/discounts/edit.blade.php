@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                style="width:52px;height:52px;background:#fd7e1418;border:2px solid #fd7e1435;">
                <i class="ti ti-tag fs-3" style="color:#fd7e14;"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Edit Diskon</h1>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <code class="badge rounded-pill small" style="background:#f8f9fa;color:#495057;font-size:.75rem;letter-spacing:1px; font-weight:600;">
                        {{ $discount->code }}
                    </code>
                    <span class="text-muted small">·</span>
                    <span class="badge rounded-pill small" style="background:{{ $discount->status==='active' ? '#19875418' : '#6c757d18' }};color:{{ $discount->status==='active' ? '#198754' : '#6c757d' }};">
                        {{ $discount->status_label }}
                    </span>
                </div>
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
    <div class="col-12 col-lg-7">
        <form action="{{ route('admin.discounts.update', $discount) }}" method="POST" id="discountForm" novalidate>
            @csrf @method('PUT')

            {{-- INFO --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="rounded-top-4" style="height:4px;background:linear-gradient(90deg,#fd7e14,#ffc107);"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-2 p-1" style="background:#fd7e1415;"><i class="ti ti-tag fs-5" style="color:#fd7e14;"></i></div>
                        <h6 class="mb-0 fw-semibold">Informasi Diskon</h6>
                    </div>
                    <hr class="mb-3 mt-0">

                    <div class="mb-3">
                        <label class="form-label fw-medium small" for="name">Nama Diskon <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-tag text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name', $discount->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small" for="code">Kode Kupon</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-ticket text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 font-monospace text-uppercase @error('code') is-invalid @enderror"
                                id="code" name="code" value="{{ old('code', $discount->code) }}"
                                maxlength="50" oninput="this.value=this.value.toUpperCase()">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3" id="btnGenCode">
                                <i class="ti ti-refresh"></i>
                            </button>
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label fw-medium small" for="description">Deskripsi <span class="text-muted fw-normal">(Opsional)</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                            id="description" name="description" rows="2">{{ old('description', $discount->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- NILAI --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-2 p-1 bg-success bg-opacity-10"><i class="ti ti-currency-dollar text-success fs-5"></i></div>
                        <h6 class="mb-0 fw-semibold">Nilai Diskon</h6>
                    </div>
                    <hr class="mb-3 mt-0">

                    <div class="mb-3">
                        <label class="form-label fw-medium small">Tipe Diskon <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <label class="flex-fill d-flex align-items-center gap-3 p-3 rounded-3 border type-option" style="cursor:pointer;transition:all .15s;">
                                <input type="radio" name="type" value="percentage" class="form-check-input mt-0 type-radio"
                                    {{ old('type', $discount->type) === 'percentage' ? 'checked' : '' }}>
                                <div><div class="fw-semibold mb-0">Persentase</div><div class="text-muted" style="font-size:.72rem;">% dari total</div></div>
                                <span class="ms-auto fw-bold fs-5" style="color:#6f42c1;">%</span>
                            </label>
                            <label class="flex-fill d-flex align-items-center gap-3 p-3 rounded-3 border type-option" style="cursor:pointer;transition:all .15s;">
                                <input type="radio" name="type" value="fixed" class="form-check-input mt-0 type-radio"
                                    {{ old('type', $discount->type) === 'fixed' ? 'checked' : '' }}>
                                <div><div class="fw-semibold mb-0">Nominal</div><div class="text-muted" style="font-size:.72rem;">Potongan Rp tetap</div></div>
                                <span class="ms-auto fw-bold fs-6" style="color:#0d6efd;">Rp</span>
                            </label>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="value">Nilai <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-medium" id="valuePrefix">{{ $discount->type==='percentage' ? '%' : 'Rp' }}</span>
                                <input type="number" class="form-control @error('value') is-invalid @enderror"
                                    id="value" name="value" value="{{ old('value', $discount->value) }}" min="0.01" step="0.01">
                                @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6" id="maxDiscountWrapper" style="display:{{ old('type',$discount->type)==='percentage'?'':'none' }};">
                            <label class="form-label fw-medium small" for="max_discount">Maks. Potongan <span class="text-muted fw-normal">(Opsional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-medium">Rp</span>
                                <input type="number" class="form-control @error('max_discount') is-invalid @enderror"
                                    id="max_discount" name="max_discount" value="{{ old('max_discount', $discount->max_discount) }}" min="0" step="1000">
                                @error('max_discount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SYARAT --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-2 p-1 bg-info bg-opacity-10"><i class="ti ti-adjustments text-info fs-5"></i></div>
                        <h6 class="mb-0 fw-semibold">Syarat & Ketentuan</h6>
                    </div>
                    <hr class="mb-3 mt-0">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Min. Transaksi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-medium">Rp</span>
                                <input type="number" class="form-control @error('min_transaction') is-invalid @enderror"
                                    id="min_transaction" name="min_transaction" value="{{ old('min_transaction', $discount->min_transaction) }}" min="0" step="1000" placeholder="Kosong = bebas">
                                @error('min_transaction')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Batas Penggunaan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="ti ti-repeat text-muted"></i></span>
                                <input type="number" class="form-control @error('usage_limit') is-invalid @enderror"
                                    id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $discount->usage_limit) }}" min="1" placeholder="Kosong = ∞">
                                <span class="input-group-text bg-light text-muted small">kali</span>
                                @error('usage_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Berlaku Mulai</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                id="start_date" name="start_date" value="{{ old('start_date', $discount->start_date?->format('Y-m-d')) }}">
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Berlaku Sampai</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                id="end_date" name="end_date" value="{{ old('end_date', $discount->end_date?->format('Y-m-d')) }}">
                            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Usage stats --}}
                    @if($discount->usage_count > 0)
                    <div class="mt-3 p-3 rounded-3" style="background:#0d6efd08;border:1px solid #0d6efd20;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="fw-medium text-primary">Penggunaan</small>
                            <small class="text-muted">{{ $discount->usage_count }}{{ $discount->usage_limit ? ' / '.$discount->usage_limit : '' }}x digunakan</small>
                        </div>
                        @if($discount->usage_limit)
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-primary" style="width:{{ min(100, ($discount->usage_count/$discount->usage_limit)*100) }}%"></div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <div class="mt-3 d-flex align-items-center gap-3 p-3 rounded-3" style="background:#f8f9fa;">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="is_active" name="is_active" value="1"
                                {{ old('is_active', $discount->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                        </div>
                        <div>
                            <label class="fw-medium small mb-0" for="is_active">Status Diskon</label>
                            <p class="text-muted mb-0" style="font-size:.72rem;">Aktifkan agar diskon ini bisa digunakan dalam transaksi</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <small class="text-muted"><i class="ti ti-clock me-1"></i>Terakhir diperbarui: {{ $discount->updated_at->diffForHumans() }}</small>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    <button type="submit" id="btnSubmit"
                        class="btn rounded-pill px-5 fw-semibold shadow-sm text-white"
                        style="background:linear-gradient(135deg,#fd7e14,#e05a00);">
                        <span class="btn-text"><i class="ti ti-device-floppy me-1"></i>Perbarui</span>
                        <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- SIDEBAR --}}
    <div class="col-12 col-lg-5">
        <div class="position-sticky d-flex flex-column gap-3" style="top:80px;">

            {{-- Preview --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <p class="fw-semibold text-muted small text-uppercase mb-3" style="letter-spacing:.6px;font-size:.7rem;">
                        <i class="ti ti-eye me-1"></i>Preview Diskon
                    </p>
                    <div class="rounded-4 overflow-hidden border" id="previewCard">
                        <div id="previewBar" style="height:4px;background:#fd7e14;"></div>
                        <div class="p-3">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div class="fw-bold small text-truncate" id="previewName">{{ $discount->name }}</div>
                                <span class="badge rounded-pill flex-shrink-0" id="previewTypeBadge"
                                    style="background:{{ $discount->type==='percentage'?'#6f42c118':'#0d6efd18' }};color:{{ $discount->type==='percentage'?'#6f42c1':'#0d6efd' }};font-size:.7rem;">
                                    {{ $discount->type==='percentage'?'%':'Rp' }}
                                </span>
                            </div>
                            <div class="text-center py-3 rounded-3 mb-2" id="previewValueBox"
                                style="background:linear-gradient(135deg,#fd7e1412,#fd7e1405);border:1px dashed #fd7e1440;">
                                <div class="fw-bold" id="previewValue" style="font-size:2rem;color:#fd7e14;letter-spacing:-1px;">{{ $discount->formatted_value }}</div>
                            </div>
                            <div class="rounded-3 p-2 mb-2" style="background:#f8f9fa;border:1px solid #e9ecef;">
                                <code class="fw-bold" id="previewCode" style="font-size:.85rem;color:#495057;letter-spacing:1px;">{{ $discount->code }}</code>
                            </div>
                            <div class="d-flex flex-column gap-1" style="font-size:.72rem;color:#6c757d;">
                                @if($discount->min_transaction)
                                <div class="d-flex align-items-center gap-1">
                                    <i class="ti ti-cash"></i><span>Min. transaksi: <strong class="text-dark">Rp {{ number_format($discount->min_transaction,0,',','.') }}</strong></span>
                                </div>
                                @endif
                                @if($discount->usage_limit)
                                <div class="d-flex align-items-center gap-1">
                                    <i class="ti ti-repeat"></i><span>Digunakan {{ $discount->usage_count }} / {{ $discount->usage_limit }}x</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mt-2 mb-0" style="font-size:.72rem;"><i class="ti ti-refresh me-1"></i>Preview diperbarui otomatis.</p>
                </div>
            </div>

            {{-- Simulasi --}}
            <div class="card border-0 shadow-sm rounded-4" style="background:linear-gradient(135deg,#fd7e1408,#fff);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="ti ti-calculator text-warning fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-warning">Simulasi Diskon</h6>
                    </div>
                    <div class="mb-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light fw-medium">Rp</span>
                            <input type="number" class="form-control" id="simAmount" min="0" step="1000" placeholder="Masukkan nominal transaksi...">
                        </div>
                    </div>
                    <div class="rounded-3 p-3" style="background:#fff;border:1px solid #f0f0f0;">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Subtotal</span><span id="simSubtotal">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-2 text-danger">
                            <span><i class="ti ti-tag me-1"></i>Diskon</span><span id="simDiscount">– Rp 0</span>
                        </div>
                        <hr class="my-1">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total Bayar</span><span id="simTotal" class="text-success">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="card border-0 shadow-sm rounded-4" style="background:linear-gradient(135deg,#dc354508,#fff);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="ti ti-alert-triangle text-danger fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-danger">Zona Berbahaya</h6>
                    </div>
                    <p class="small text-muted mb-3">Hapus diskon ini secara permanen.</p>
                    <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST" class="form-delete">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill w-100 fw-medium btn-delete"
                            data-name="{{ $discount->name }}">
                            <i class="ti ti-trash me-1"></i>Hapus Diskon Ini
                        </button>
                    </form>
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
        const t   = getType();
        const val = parseFloat(valueEl.value) || 0;
        document.getElementById('previewName').textContent = nameEl.value || 'Nama Diskon';
        document.getElementById('previewCode').textContent = (codeEl.value || 'XXXXXXXX').toUpperCase();
        document.getElementById('previewTypeBadge').textContent = t === 'percentage' ? '%' : 'Rp';
        document.getElementById('previewTypeBadge').style.color = t === 'percentage' ? '#6f42c1' : '#0d6efd';
        document.getElementById('previewTypeBadge').style.background = t === 'percentage' ? '#6f42c118' : '#0d6efd18';
        document.getElementById('previewValue').textContent = t === 'percentage' ? val+'%' : 'Rp '+val.toLocaleString('id-ID');
        updateSimulation();
    }

    function updateSimulation() {
        const t   = getType();
        const val = parseFloat(valueEl.value) || 0;
        const min = parseFloat(minTrxEl.value) || 0;
        const max = parseFloat(maxDiscEl.value) || 0;
        const amt = parseFloat(document.getElementById('simAmount').value) || 0;
        let disc  = 0;
        if (amt >= min || min === 0) {
            disc = t === 'fixed' ? Math.min(val, amt) : Math.min(amt*val/100, max || Infinity);
        }
        document.getElementById('simSubtotal').textContent  = fmt(amt);
        document.getElementById('simDiscount').textContent  = '– ' + fmt(disc);
        document.getElementById('simTotal').textContent     = fmt(Math.max(0, amt-disc));
    }

    typeRadios.forEach(r => r.addEventListener('change', () => { updateTypeUI(); updatePreview(); }));
    [nameEl, codeEl, valueEl, minTrxEl, maxDiscEl, limitEl].forEach(el => el?.addEventListener('input', updatePreview));
    document.getElementById('simAmount').addEventListener('input', updateSimulation);

    document.getElementById('btnGenCode').addEventListener('click', async function () {
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        try {
            const res = await fetch('{{ route("admin.discounts.generate-code") }}');
            codeEl.value = (await res.json()).code; updatePreview();
        } catch(e) {}
        this.innerHTML = '<i class="ti ti-refresh"></i>';
    });

    document.getElementById('discountForm').addEventListener('submit', function () {
        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.btn-loading').classList.remove('d-none');
    });

    document.querySelector('.btn-delete')?.addEventListener('click', function () {
        Swal.fire({
            title:'Hapus Diskon?',
            html:`Diskon <strong>{{ $discount->name }}</strong> akan dihapus permanen.`,
            icon:'warning', showCancelButton:true,
            confirmButtonColor:'#dc3545', cancelButtonColor:'#6c757d',
            confirmButtonText:'<i class="ti ti-trash me-1"></i>Ya, Hapus!',
            cancelButtonText:'Batal', reverseButtons:true,
        }).then(r => { if(r.isConfirmed) document.querySelector('.form-delete').submit(); });
    });

    updateTypeUI();
    updatePreview();
});
</script>
@endpush
@endsection
