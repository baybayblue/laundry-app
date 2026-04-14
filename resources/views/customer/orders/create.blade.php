@extends('customer.layouts.app')

@section('title', 'Buat Pesanan Laundry')

@section('content')

{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                style="background: linear-gradient(135deg, #2563eb20, #2563eb10); border: 1px solid #2563eb30; width:48px; height:48px;">
                <i class="ti ti-receipt-2 fs-3 text-primary"></i>
            </div>
            <div>
                <h1 class="fs-4 mb-0 fw-bold">Buat Pesanan Baru</h1>
                <p class="mb-0 text-muted small">Pilih layanan dan atur jadwal penjemputan</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('customer.dashboard') }}"
            class="btn btn-light d-inline-flex align-items-center gap-2 rounded-pill px-4 border small">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- VALIDATION ALERT --}}
@if($errors->any())
<div class="alert border-0 rounded-4 shadow-sm mb-4 d-flex align-items-start gap-3"
    style="background:#dc354510; border-left: 4px solid #dc3545 !important;">
    <i class="ti ti-alert-circle text-danger fs-4 mt-1 flex-shrink-0"></i>
    <div>
        <div class="fw-semibold text-danger mb-1">Terdapat kesalahan pada form:</div>
        <ul class="mb-0 small text-danger ps-3">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

<form action="{{ route('customer.orders.store') }}" method="POST" id="transactionForm" novalidate>
@csrf

<div class="row g-4 pb-5">
    {{-- ── KIRI: FORM ── --}}
    <div class="col-12 col-xl-8">

        {{-- STEP 1: INFORMASI ANDA --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent px-4 pt-4 pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                        style="width:32px;height:32px;font-size:.85rem;background:var(--bs-primary);">1</div>
                    <div>
                        <h6 class="mb-0 fw-bold">Informasi Penjemputan</h6>
                        <div class="text-muted" style="font-size:.72rem;">Pastikan alamat dan jadwal penjemputan sudah benar</div>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
                <hr class="mb-4 mt-0">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Nama Pelanggan</label>
                        <input type="text" class="form-control bg-light border-0" value="{{ auth()->user()->name }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Nomor HP</label>
                        <input type="text" class="form-control bg-light border-0" value="{{ auth()->user()->phone }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium small" for="pickup_date">
                            Tanggal Penjemputan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-calendar-event text-muted"></i></span>
                            <input type="date" class="form-control border-start-0 @error('pickup_date') is-invalid @enderror" 
                                id="pickup_date" name="pickup_date" value="{{ old('pickup_date', date('Y-m-d')) }}"
                                min="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-medium small" for="pickup_address">
                            Alamat Penjemputan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group align-items-start">
                            <span class="input-group-text bg-light border-end-0 pt-2" style="align-items:flex-start;"><i class="ti ti-map-pin text-muted"></i></span>
                            <textarea class="form-control border-start-0 @error('pickup_address') is-invalid @enderror" 
                                id="pickup_address" name="pickup_address" rows="3" 
                                placeholder="Jl. Nama Jalan No. XX..." required>{{ old('pickup_address', auth()->user()->address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STEP 2: PILIH LAYANAN --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent px-4 pt-4 pb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                            style="width:32px;height:32px;font-size:.85rem;background:var(--bs-primary);">2</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Detail Layanan</h6>
                            <div class="text-muted" style="font-size:.72rem;">Tambahkan layanan laundry yang Anda inginkan</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-medium" id="btnAddItem">
                        <i class="ti ti-plus me-1"></i>Tambah Baris
                    </button>
                </div>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
                <hr class="mb-4 mt-0">
                <div id="itemsContainer">
                    {{-- Row Template (matched with admin) --}}
                    <div class="item-row rounded-3 p-3 mb-3 position-relative"
                        style="background:#f8faff; border: 1.5px solid #2563eb25; border-left: 4px solid var(--bs-primary) !important;">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-5">
                                <label class="form-label fw-medium small mb-1">Layanan <span class="text-danger">*</span></label>
                                <select class="form-select service-select" name="items[0][service_id]" required>
                                    <option value="">– Pilih layanan –</option>
                                    @foreach($services as $svc)
                                    <option value="{{ $svc->id }}"
                                        data-price="{{ $svc->price }}"
                                        data-type="{{ $svc->type }}"
                                        data-type-label="{{ $svc->getTypeLabel() }}">
                                        {{ $svc->name }} — {{ $svc->getFormattedPrice() }} / {{ $svc->getTypeLabel() }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-5 col-md-2">
                                <label class="form-label fw-medium small mb-1">
                                    Jumlah <span class="unit-label badge bg-light text-muted rounded-pill" style="font-size:.65rem;">–</span>
                                </label>
                                <input type="number" class="form-control qty-input" name="items[0][quantity]"
                                    value="1" min="0.1" step="0.1" required>
                            </div>
                            <div class="col-7 col-md-4">
                                <label class="form-label fw-medium small mb-1">Subtotal Estimasi</label>
                                <input type="text" class="form-control fw-bold text-primary subtotal-display bg-light border-0" readonly placeholder="–">
                            </div>
                            <div class="col-12 col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-sm w-100 rounded-3 remove-item"
                                    style="height:38px;background:#dc354515;color:#dc3545;" title="Hapus baris">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                            <div class="col-12">
                                <input type="text" class="form-control form-control-sm"
                                    style="border-style:dashed !important; font-size:.8rem;" name="items[0][notes]"
                                    placeholder="📝  Catatan untuk item ini (opsional)...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-2">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-4 fw-medium" id="btnAddItem2">
                        <i class="ti ti-plus me-1 text-primary"></i>Tambah Layanan Lain
                    </button>
                </div>
            </div>
        </div>

        {{-- STEP 3: VOUCHER & CATATAN --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent px-4 pt-4 pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                        style="width:32px;height:32px;font-size:.85rem;background:var(--bs-primary);">3</div>
                    <div>
                        <h6 class="mb-0 fw-bold">Voucher & Catatan</h6>
                        <div class="text-muted" style="font-size:.72rem;">Pilih promo untuk mendapatkan potongan harga</div>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
                <hr class="mb-4 mt-0">
                <div class="row g-3">
                    {{-- VOUCHER PICKER (Shopee-style) --}}
                    <div class="col-12">
                        <div id="voucherPickerRow"
                            class="d-flex align-items-center justify-content-between p-3 rounded-3 border"
                            style="cursor:pointer; border-color:#e9ecef; transition:all .15s; background:#fff;"
                            onclick="openVoucherModal()">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width:38px;height:38px;background:linear-gradient(135deg,#2563eb,#3b82f6);">
                                    <i class="ti ti-ticket text-white fs-5"></i>
                                </div>
                                <div id="voucherPickerText">
                                    <div class="fw-medium text-muted small" id="voucherPickerLabel">Pilih atau gunakan voucher promo</div>
                                    <div class="text-muted" style="font-size:.72rem;" id="voucherPickerSub">
                                        @if($discounts->count()) {{ $discounts->count() }} voucher tersedia @else Tidak ada voucher aktif @endif
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span id="voucherSelectedBadge" class="badge rounded-pill d-none"
                                    style="background:#2563eb20;color:#2563eb;border:1px solid #2563eb40;">Terpasang</span>
                                <i class="ti ti-chevron-right text-muted"></i>
                            </div>
                        </div>

                        {{-- Applied voucher display --}}
                        <div id="voucherApplied" class="d-none mt-2">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3"
                                style="background:linear-gradient(135deg,#2563eb08,#3b82f608);border:1.5px dashed #2563eb60;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-circle-check text-success fs-5"></i>
                                    <div>
                                        <div class="fw-semibold small" id="voucherAppliedName">–</div>
                                        <div class="text-success fw-bold" style="font-size:.8rem;" id="voucherAppliedSaving">Hemat –</div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm text-muted border-0" onclick="removeVoucher()" title="Hapus voucher">
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="discount_code" id="discount_code">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium small">Catatan Pesanan</label>
                        <div class="input-group align-items-start">
                            <span class="input-group-text bg-light border-end-0 pt-2" style="align-items:flex-start;"><i class="ti ti-notes text-muted"></i></span>
                            <textarea class="form-control border-start-0" name="notes" rows="2"
                                placeholder="Tuliskan catatan tambahan jika ada...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STEP 4: PEMBAYARAN --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent px-4 pt-4 pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                        style="width:32px;height:32px;font-size:.85rem;background:var(--bs-primary);">4</div>
                    <div>
                        <h6 class="mb-0 fw-bold">Metode Pembayaran</h6>
                        <div class="text-muted" style="font-size:.72rem;">Pilih cara Anda ingin membayar</div>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
                <hr class="mb-4 mt-0">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="d-flex align-items-center gap-3 p-3 rounded-4 border payment-opt h-100"
                            style="cursor:pointer; transition:all .2s; border-width:2px !important;" id="optCash">
                            <input type="radio" name="payment_method" value="cash" id="radioCash" class="form-check-input mt-0 flex-shrink-0">
                            <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success flex-shrink-0">
                                <i class="ti ti-cash fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-0">Tunai (Cash)</div>
                                <div class="text-muted small">Bayar langsung saat penjemputan</div>
                            </div>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="d-flex align-items-center gap-3 p-3 rounded-4 border payment-opt h-100 border-primary"
                            style="cursor:pointer; transition:all .2s; border-width:2px !important;" id="optMidtrans">
                            <input type="radio" name="payment_method" value="midtrans" id="radioMidtrans" class="form-check-input mt-0 flex-shrink-0" checked>
                            <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary flex-shrink-0">
                                <i class="ti ti-credit-card fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-0">Online (Midtrans)</div>
                                <div class="text-muted small">QRIS, Transfer Bank, E-Wallet</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Info Midtrans --}}
                <div class="mt-3 rounded-3 p-3" id="midtransInfoCard"
                    style="background:#2563eb08; border: 1px solid #2563eb25;">
                    <div class="d-flex align-items-start gap-2">
                        <i class="ti ti-info-circle text-primary mt-1 flex-shrink-0"></i>
                        <p class="small text-muted mb-0 lh-base">
                            Setelah pesanan dibuat, jendela pembayaran akan muncul otomatis jika total tagihan sudah diketahui.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center gap-2 mb-5">
            <a href="{{ route('customer.dashboard') }}" class="btn btn-light rounded-pill px-4 fw-medium border">
                Batal
            </a>
            <button type="submit" id="btnSubmit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                <span class="btn-text"><i class="ti ti-check me-1"></i>Konfirmasi Pesanan</span>
                <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
            </button>
        </div>
    </div>

    {{-- ── KANAN: RINGKASAN --}}
    <div class="col-12 col-xl-4">
        <div class="position-sticky d-flex flex-column gap-3" style="top:2rem;">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div style="height:3px;background:linear-gradient(90deg,var(--bs-primary),#60a5fa);"></div>
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2 text-dark">
                        <i class="ti ti-calculator text-primary"></i> Ringkasan Estimasi
                    </h6>
                    <div id="itemSummaryList" class="mb-3 d-flex flex-column gap-2 pb-3 border-bottom" style="font-size:.82rem;">
                        <div class="text-muted text-center py-2 small fst-italic">Belum ada layanan dipilih</div>
                    </div>
                    <div class="d-flex flex-column gap-2 pt-2" style="font-size:.85rem;">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Subtotal</span>
                            <span id="summarySubtotal" class="fw-semibold text-dark">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between text-danger" id="discountRow" style="display:none!important;">
                            <span class="fw-medium">Diskon</span>
                            <span id="summaryDiscount" class="fw-bold">– Rp 0</span>
                        </div>
                        @if(($settings['tax_enabled']??'0') === '1')
                        <div class="d-flex justify-content-between text-muted">
                            <span>PPN {{ $settings['tax_percent']??'11' }}%</span>
                            <span id="summaryTax">Rp 0</span>
                        </div>
                        @endif
                        @if(($settings['service_fee']??'0') > 0)
                        <div class="d-flex justify-content-between text-muted">
                            <span>Biaya Layanan</span>
                            <span class="fw-medium">Rp {{ number_format($settings['service_fee'],0,',','.') }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 p-3 rounded-3"
                        style="background:#2563eb10; border: 1.5px solid #2563eb20;">
                        <div class="fw-bold text-primary small">TOTAL ESTIMASI</div>
                        <div id="summaryTotal" class="fw-bold text-primary" style="font-size:1.3rem;">Rp 0</div>
                    </div>
                    <p class="text-center text-muted mt-3 mb-0" style="font-size: .7rem;">
                        <i class="ti ti-info-circle me-1"></i>Total akhir mungkin berubah setelah admin menimbang pakaian Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</form>

{{-- MODAL VOUCHER (Reuse Admin UI) --}}
@include('admin.transactions.modal-voucher', ['discounts' => $discounts])

@push('scripts')
<style>
.item-row { transition: all .2s; }
.voucher-card:hover { border-color:#2563eb !important; background:#f8faff; }
.voucher-card.selected { border-color:#198754 !important; background:#f0fff4; }
.voucher-card.ineligible { opacity:.5; pointer-events:none; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const settings = {
        taxEnabled: {{ ($settings['tax_enabled']??'0') === '1' ? 'true' : 'false' }},
        taxPercent: {{ ($settings['tax_percent']??'0') }},
        serviceFee: {{ ($settings['service_fee']??'0') }},
    };

    let itemIndex = 1;
    window.discountAmount = 0;
    let selectedVoucherData = null;

    function fmt(n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); }

    // ── Recalc Logic ──────────────────────────────────────────
    window.recalc = function recalc() {
        let subtotal = 0;
        const rows = [];
        document.querySelectorAll('.item-row').forEach(row => {
            const sel = row.querySelector('.service-select');
            const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
            const opt = sel?.selectedOptions[0];
            if (!opt || !opt.value) return;

            const price = parseFloat(opt.dataset.price) || 0;
            const type = opt.dataset.type || 'per_kg';
            const line = type === 'flat' ? price : Math.round(price * qty * 100) / 100;
            
            row.querySelector('.subtotal-display').value = line ? fmt(line) : '';
            subtotal += line;
            rows.push({ name: opt.text.split(' — ')[0], qty, type: opt.dataset.typeLabel, line });
        });

        // Voucher re-validation
        if (selectedVoucherData && subtotal < selectedVoucherData.min) {
            removeVoucher();
            Swal.fire({toast:true, position:'top-end', icon:'info', title:'Voucher terlepas (min. transaksi tidak terpenuhi)', showConfirmButton:false, timer:3000});
        }
        
        const disc = window.discountAmount;
        const taxable = Math.max(0, subtotal - disc);
        const tax = settings.taxEnabled ? Math.round(taxable * settings.taxPercent / 100) : 0;
        const fee = settings.serviceFee;
        const total = taxable + tax + fee;

        document.getElementById('summarySubtotal').textContent = fmt(subtotal);
        document.getElementById('summaryTotal').textContent = fmt(total);
        if (document.getElementById('summaryTax')) document.getElementById('summaryTax').textContent = fmt(tax);
        
        const discRow = document.getElementById('discountRow');
        if (discRow) {
            discRow.style.display = disc > 0 ? 'flex' : 'none';
            document.getElementById('summaryDiscount').textContent = '– ' + fmt(disc);
        }

        const list = document.getElementById('itemSummaryList');
        if (rows.length) {
            list.innerHTML = rows.map(r => `
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted text-truncate" style="max-width:70%">${r.name} x${r.qty}</span>
                    <span class="fw-bold text-dark">${fmt(r.line)}</span>
                </div>
            `).join('');
        } else {
            list.innerHTML = '<div class="text-muted text-center py-2 small fst-italic">Belum ada layanan dipilih</div>';
        }
    }

    // ── Row Management ────────────────────────────────────────
    function attachRowEvents(row) {
        row.querySelector('.service-select').addEventListener('change', function() {
            row.querySelector('.unit-label').textContent = this.selectedOptions[0]?.dataset.typeLabel || '–';
            recalc();
        });
        row.querySelector('.qty-input').addEventListener('input', recalc);
        row.querySelector('.remove-item').addEventListener('click', () => {
            if (document.querySelectorAll('.item-row').length > 1) {
                row.remove(); recalc();
            }
        });
    }

    document.getElementById('btnAddItem').onclick = document.getElementById('btnAddItem2').onclick = function() {
        const first = document.querySelector('.item-row');
        const clone = first.cloneNode(true);
        clone.querySelectorAll('input').forEach(i => i.value = i.classList.contains('qty-input') ? 1 : '');
        clone.querySelector('select').selectedIndex = 0;
        clone.querySelector('select').name = `items[${itemIndex}][service_id]`;
        clone.querySelector('.qty-input').name = `items[${itemIndex}][quantity]`;
        if (clone.querySelector('[name*="[notes]"]')) clone.querySelector('[name*="[notes]"]').name = `items[${itemIndex}][notes]`;
        
        itemIndex++;
        document.getElementById('itemsContainer').appendChild(clone);
        attachRowEvents(clone);
    };

    attachRowEvents(document.querySelector('.item-row'));

    // ── Voucher Logic ─────────────────────────────────────────
    window.openVoucherModal = function() {
        const { subtotal } = { subtotal: parseFloat(document.getElementById('summarySubtotal').textContent.replace(/[^0-9]/g, '')) || 0 };
        document.getElementById('modalSubtotalHint').textContent = fmt(subtotal);
        
        document.querySelectorAll('.voucher-card').forEach(card => {
            const min = parseFloat(card.dataset.min);
            card.classList.toggle('ineligible', subtotal < min);
        });
        new bootstrap.Modal(document.getElementById('voucherModal')).show();
    }

    window.selectVoucher = function(card) {
        if (card.classList.contains('ineligible')) return;
        document.querySelectorAll('.voucher-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        document.getElementById('btnConfirmVoucher').disabled = false;
    }

    window.confirmVoucher = function() {
        const card = document.querySelector('.voucher-card.selected');
        if (!card) return;

        const subtotal = parseFloat(document.getElementById('summarySubtotal').textContent.replace(/[^0-9]/g, '')) || 0;
        const val = parseFloat(card.dataset.value);
        const type = card.dataset.type;
        const maxDisc = parseFloat(card.dataset.max);
        
        let calculated = 0;
        if (type === 'fixed') {
            calculated = val;
        } else {
            calculated = (subtotal * val) / 100;
            if (maxDisc > 0 && calculated > maxDisc) calculated = maxDisc;
        }

        window.discountAmount = Math.min(calculated, subtotal);
        selectedVoucherData = { id: card.dataset.id, code: card.dataset.code, min: parseFloat(card.dataset.min) };

        document.getElementById('discount_code').value = card.dataset.code;
        document.getElementById('voucherAppliedName').textContent = card.dataset.name;
        document.getElementById('voucherAppliedSaving').textContent = 'Hemat ' + fmt(window.discountAmount);
        
        document.getElementById('voucherPickerRow').classList.add('d-none');
        document.getElementById('voucherApplied').classList.remove('d-none');
        
        bootstrap.Modal.getInstance(document.getElementById('voucherModal')).hide();
        recalc();
    }

    window.removeVoucher = function() {
        window.discountAmount = 0;
        selectedVoucherData = null;
        document.getElementById('discount_code').value = '';
        document.getElementById('voucherPickerRow').classList.remove('d-none');
        document.getElementById('voucherApplied').classList.add('d-none');
        recalc();
    }

    // ── Payment Selection Logic ───────────────────────────────
    const optCash = document.getElementById('optCash');
    const optMidtrans = document.getElementById('optMidtrans');
    const radioCash = document.getElementById('radioCash');
    const radioMidtrans = document.getElementById('radioMidtrans');
    const midtransInfo = document.getElementById('midtransInfoCard');

    function updatePaymentUI() {
        if (radioCash.checked) {
            optCash.classList.add('border-primary');
            optMidtrans.classList.remove('border-primary');
            midtransInfo.classList.add('d-none');
        } else {
            optMidtrans.classList.add('border-primary');
            optCash.classList.remove('border-primary');
            midtransInfo.classList.remove('d-none');
        }
    }

    optCash.onclick = () => { radioCash.checked = true; updatePaymentUI(); };
    optMidtrans.onclick = () => { radioMidtrans.checked = true; updatePaymentUI(); };
    updatePaymentUI();

    // Form submission
    document.getElementById('transactionForm').onsubmit = function() {
        this.querySelector('.btn-text').classList.add('d-none');
        this.querySelector('.btn-loading').classList.remove('d-none');
        document.getElementById('btnSubmit').disabled = true;
    };
});
</script>
@endpush

@endsection
