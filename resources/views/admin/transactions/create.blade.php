@extends('layouts.app')

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
                <h1 class="fs-3 mb-0 fw-bold">Buat Transaksi</h1>
                <p class="mb-0 text-muted small">Catat order baru dan terima pembayaran</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('admin.transactions.index') }}"
            class="btn btn-light d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4 border">
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
        <div class="fw-semibold text-danger mb-1">Terdapat {{ $errors->count() }} kesalahan pada form:</div>
        <ul class="mb-0 small text-danger ps-3">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

<form action="{{ route('admin.transactions.store') }}" method="POST" id="transactionForm" novalidate>
@csrf

<div class="row g-4">
    {{-- ── KIRI: FORM ── --}}
    <div class="col-12 col-xl-8">

        {{-- STEP 1: PELANGGAN --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent px-4 pt-4 pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                        style="width:32px;height:32px;font-size:.85rem;background:var(--bs-primary);">1</div>
                    <div>
                        <h6 class="mb-0 fw-bold">Informasi Pelanggan</h6>
                        <div class="text-muted" style="font-size:.72rem;">Cari pelanggan terdaftar atau isi manual untuk pelanggan walk-in</div>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
                <hr class="mb-4 mt-0">

                {{-- Search box --}}
                <div class="mb-3 position-relative">
                    <label class="form-label fw-medium small">Cari Pelanggan Terdaftar</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="ti ti-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" id="customerSearch"
                            placeholder="Ketik nama atau nomor HP..." autocomplete="off">
                        <span class="input-group-text bg-light border-start-0" id="clearCustomer"
                            style="cursor:pointer; display:none;">
                            <i class="ti ti-x text-muted fs-6"></i>
                        </span>
                    </div>
                    <div id="customerSuggestions"
                        class="position-absolute bg-white shadow rounded-3 border d-none mt-1"
                        style="z-index:9999;max-height:220px;overflow-y:auto;left:0;right:0;"></div>
                    <input type="hidden" id="customer_id" name="customer_id">
                </div>

                <div class="d-flex align-items-center gap-2 mb-3">
                    <hr class="flex-fill m-0"><small class="text-muted px-2 fw-medium" style="white-space:nowrap;">atau isi manual (walk-in)</small><hr class="flex-fill m-0">
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium small" for="customer_name">
                            Nama Pelanggan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-user text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 @error('customer_name') is-invalid @enderror"
                                id="customer_name" name="customer_name" value="{{ old('customer_name') }}"
                                placeholder="Nama lengkap pelanggan" required>
                            @error('customer_name')
                                <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium small" for="customer_phone">Nomor HP</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-phone text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 @error('customer_phone') is-invalid @enderror"
                                id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}"
                                placeholder="08XXXXXXXXXX">
                            @error('customer_phone')
                                <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STEP 2: LAYANAN --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent px-4 pt-4 pb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                            style="width:32px;height:32px;font-size:.85rem;background:var(--bs-primary);">2</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Detail Layanan</h6>
                            <div class="text-muted" style="font-size:.72rem;">Pilih layanan, jumlah, dan tambah baris jika perlu</div>
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
                    {{-- initial row --}}
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
                                    value="1" min="0.1" step="0.1">
                            </div>
                            <div class="col-5 col-md-2">
                                <label class="form-label fw-medium small mb-1">Harga/Unit</label>
                                <input type="text" class="form-control bg-light border-0 price-display text-muted" readonly placeholder="–">
                            </div>
                            <div class="col-9 col-md-2">
                                <label class="form-label fw-medium small mb-1">Subtotal</label>
                                <input type="text" class="form-control fw-bold text-primary subtotal-display"
                                    style="background:#2563eb08; border-color:#2563eb20 !important;" readonly placeholder="–">
                            </div>
                            <div class="col-3 col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-sm w-100 rounded-3 remove-item"
                                    style="height:38px;background:#dc354515;color:#dc3545;" title="Hapus baris">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                            <div class="col-12">
                                <input type="text" class="form-control form-control-sm"
                                    style="border-style:dashed !important; font-size:.8rem;" name="items[0][notes]"
                                    placeholder="📝  Catatan untuk layanan ini (opsional)...">
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

        {{-- STEP 3: DISKON & CATATAN --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent px-4 pt-4 pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                        style="width:32px;height:32px;font-size:.85rem;background:var(--bs-primary);">3</div>
                    <div>
                        <h6 class="mb-0 fw-bold">Voucher & Catatan</h6>
                        <div class="text-muted" style="font-size:.72rem;">Pilih voucher promo atau masukkan kode manual</div>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
                <hr class="mb-4 mt-0">
                <div class="row g-3">

                    {{-- ═══ VOUCHER PICKER (Shopee-style) ═══ --}}
                    <div class="col-12">
                        <label class="form-label fw-medium small mb-2">
                            <i class="ti ti-ticket me-1 text-primary"></i>Voucher / Diskon
                        </label>

                        {{-- Trigger row --}}
                        <div id="voucherPickerRow"
                            class="d-flex align-items-center justify-content-between p-3 rounded-3 border"
                            style="cursor:pointer; border-color:#e9ecef; transition:all .15s; background:#fff;"
                            onclick="openVoucherModal()"
                            onmouseover="this.style.borderColor='var(--bs-primary)'; this.style.background='#f8faff';"
                            onmouseout="this.style.borderColor='#e9ecef'; this.style.background='#fff';">
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

                        {{-- Manual code fallback --}}
                        <div class="mt-2">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <hr class="flex-fill m-0"><span class="text-muted px-2" style="font-size:.72rem;white-space:nowrap;">atau masukkan kode manual</span><hr class="flex-fill m-0">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-barcode text-muted fs-6"></i></span>
                                <input type="text" class="form-control border-start-0 font-monospace text-uppercase"
                                    id="discount_code" name="discount_code" placeholder="Ketik kode promo..." maxlength="50">
                                <button type="button" class="btn btn-outline-primary btn-sm px-3 fw-medium" id="btnCheckDiscount">
                                    <i class="ti ti-check me-1"></i>Pakai
                                </button>
                            </div>
                            <div id="discountMsg" class="mt-1" style="font-size:.75rem;"></div>
                        </div>
                    </div>
                    {{-- ═══ end VOUCHER PICKER ═══ --}}

                    <div class="col-md-6">
                        <label class="form-label fw-medium small" for="pickup_date">Estimasi Antar / Jemput</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-calendar-event text-muted"></i></span>
                            <input type="date" class="form-control border-start-0" id="pickup_date" name="pickup_date">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Catatan Order</label>
                        <div class="input-group align-items-start">
                            <span class="input-group-text bg-light border-end-0 pt-2" style="align-items:flex-start;"><i class="ti ti-notes text-muted"></i></span>
                            <textarea class="form-control border-start-0" name="notes" rows="2"
                                placeholder="Catatan khusus untuk keseluruhan order ini...">{{ old('notes') }}</textarea>
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
                        <div class="text-muted" style="font-size:.72rem;">Pilih cara pembayaran pelanggan</div>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
                <hr class="mb-4 mt-0">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="d-flex align-items-center gap-3 p-3 rounded-4 border payment-opt h-100"
                            style="cursor:pointer; transition:all .2s; border-width:2px !important;" id="optCash">
                            <input type="radio" name="payment_method" value="cash" id="radioCash" class="form-check-input mt-0 flex-shrink-0" checked>
                            <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success flex-shrink-0">
                                <i class="ti ti-cash fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-0">Tunai (Cash)</div>
                                <div class="text-muted small">Uang tunai diterima di kasir, langsung lunas</div>
                            </div>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="d-flex align-items-center gap-3 p-3 rounded-4 border payment-opt h-100"
                            style="cursor:pointer; transition:all .2s; border-width:2px !important;" id="optMidtrans">
                            <input type="radio" name="payment_method" value="midtrans" id="radioMidtrans" class="form-check-input mt-0 flex-shrink-0">
                            <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary flex-shrink-0">
                                <i class="ti ti-credit-card fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-0">Online (Midtrans)</div>
                                <div class="text-muted small">QRIS, Transfer Bank, E-Wallet & kartu kredit</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Info Midtrans --}}
                <div class="mt-3 rounded-3 p-3 d-none" id="midtransInfoCard"
                    style="background:#2563eb08; border: 1px solid #2563eb25;">
                    <div class="d-flex align-items-start gap-2">
                        <i class="ti ti-info-circle text-primary mt-1 flex-shrink-0"></i>
                        <p class="small text-muted mb-0 lh-base">
                            Setelah data tersimpan, popup Midtrans Snap akan muncul untuk memilih metode pembayaran (QRIS, VA, E-Wallet, dll). Pelanggan bisa bayar langsung.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="d-flex justify-content-between align-items-center gap-2 mb-5 pb-4">
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-light rounded-pill px-4 fw-medium border">
                <i class="ti ti-arrow-left me-1"></i>Batal
            </a>
            <button type="submit" id="btnSubmit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                <span class="btn-text"><i class="ti ti-circle-check me-1"></i>Simpan Transaksi</span>
                <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
            </button>
        </div>
    </div>

    {{-- ── KANAN: RINGKASAN --}}
    <div class="col-12 col-xl-4">
        <div class="position-sticky d-flex flex-column gap-3" style="top:80px;">

            {{-- Cost Summary --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div style="height:3px;background:linear-gradient(90deg,var(--bs-primary),#60a5fa);"></div>
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="ti ti-calculator text-primary"></i> Ringkasan Biaya
                    </h6>

                    {{-- Per-item list --}}
                    <div id="itemSummaryList" class="mb-3 d-flex flex-column gap-2 pb-3 border-bottom" style="font-size:.82rem;min-height:40px;"></div>

                    {{-- Breakdown --}}
                    <div class="d-flex flex-column gap-2 pt-2" style="font-size:.85rem;">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Subtotal</span>
                            <span id="summarySubtotal" class="fw-semibold text-dark">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between text-danger" id="discountRow" style="display:none!important;">
                            <span class="fw-medium"><i class="ti ti-tag me-1"></i>Diskon</span>
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
                            <span>Biaya Admin</span>
                            <span class="fw-medium">Rp {{ number_format($settings['service_fee'],0,',','.') }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Total --}}
                    <div class="d-flex justify-content-between align-items-center mt-3 p-3 rounded-3"
                        style="background:#2563eb10; border: 1.5px solid #2563eb20;">
                        <div class="fw-bold text-primary small text-uppercase" style="letter-spacing:.5px;">Total Akhir</div>
                        <div id="summaryTotal" class="fw-bold text-primary" style="font-size:1.3rem;">Rp 0</div>
                    </div>

                    <div class="text-center mt-3">
                        <span class="badge rounded-pill fw-normal text-muted bg-white border px-3 py-2" style="font-size:.72rem;">
                            <i class="ti ti-shield-check text-success me-1"></i>Kalkulasi Otomatis
                        </span>
                    </div>
                </div>
            </div>

            {{-- Tips Card --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <h6 class="fw-bold small mb-3 d-flex align-items-center gap-1 text-muted">
                        <i class="ti ti-bulb text-warning"></i> Tips Pengisian
                    </h6>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size:.75rem; color:#6c757d;">
                        <li class="d-flex align-items-start gap-2">
                            <i class="ti ti-point-filled text-primary mt-1 flex-shrink-0" style="font-size:.6rem;"></i>
                            <span>Cari nama pelanggan untuk mengisi otomatis data terdaftar</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="ti ti-point-filled text-primary mt-1 flex-shrink-0" style="font-size:.6rem;"></i>
                            <span>Layanan <strong>Flat</strong>: harga tetap, tidak tergantung berat</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="ti ti-point-filled text-primary mt-1 flex-shrink-0" style="font-size:.6rem;"></i>
                            <span>Layanan <strong>per Kg</strong>: jumlah = berat cucian</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="ti ti-point-filled text-primary mt-1 flex-shrink-0" style="font-size:.6rem;"></i>
                            <span>Tambah lebih dari satu layanan dalam satu transaksi</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

</form>

{{-- ══════════════════════════════════════════════════════════════════
     MODAL: VOUCHER PICKER (Shopee-style)
══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" style="max-width:520px;">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">

            {{-- Modal Header --}}
            <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#2563eb,#3b82f6);">
                <div class="d-flex align-items-center gap-3 py-1">
                    <div class="rounded-3 bg-white bg-opacity-20 p-2">
                        <i class="ti ti-ticket text-white fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="voucherModalLabel">Pilih Voucher Promo</h5>
                        <div class="text-white opacity-75" style="font-size:.75rem;">{{ $discounts->count() }} voucher tersedia untuk Anda</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- Subtotal hint --}}
            <div class="px-4 py-2 d-flex align-items-center gap-2" style="background:#f0f4ff; border-bottom:1px solid #dde7ff;">
                <i class="ti ti-info-circle text-primary"></i>
                <span class="small text-muted">Total belanja saat ini: <strong id="modalSubtotalHint" class="text-dark">Rp 0</strong></span>
            </div>

            <div class="modal-body p-4">
                @if($discounts->count())
                <div class="d-flex flex-column gap-3" id="voucherCardList">
                    @foreach($discounts as $disc)
                    <div class="voucher-card rounded-3 overflow-hidden position-relative"
                        style="border:1.5px solid #e9ecef; cursor:pointer; transition:all .2s;"
                        data-id="{{ $disc->id }}"
                        data-code="{{ $disc->code }}"
                        data-name="{{ $disc->name }}"
                        data-type="{{ $disc->type }}"
                        data-value="{{ $disc->value }}"
                        data-min="{{ $disc->min_transaction ?? 0 }}"
                        data-max="{{ $disc->max_discount ?? 0 }}"
                        data-formatted="{{ $disc->formatted_value }}"
                        onclick="selectVoucher(this)">

                        {{-- Left accent bar --}}
                        <div style="position:absolute;left:0;top:0;bottom:0;width:5px;background:linear-gradient(180deg,#2563eb,#3b82f6);"></div>

                        <div class="ps-4 pe-3 py-3">
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        {{-- Value badge (big, prominent) --}}
                                        <span class="fw-black text-white rounded-2 px-2 py-1"
                                            style="background:linear-gradient(135deg,#2563eb,#3b82f6);font-size:.9rem;letter-spacing:-.3px;">
                                            {{ $disc->type === 'percentage' ? 'DISKON ' . intval($disc->value) . '%' : 'HEMAT Rp ' . number_format($disc->value, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="fw-bold" style="font-size:.9rem;">{{ $disc->name }}</div>
                                    @if($disc->description)
                                    <div class="text-muted small">{{ $disc->description }}</div>
                                    @endif

                                    {{-- Conditions row --}}
                                    <div class="d-flex flex-wrap gap-2 mt-2" style="font-size:.72rem;">
                                        @if($disc->min_transaction > 0)
                                        <span class="d-flex align-items-center gap-1 text-muted">
                                            <i class="ti ti-shopping-cart" style="font-size:.75rem;"></i>
                                            Min. Rp {{ number_format($disc->min_transaction, 0, ',', '.') }}
                                        </span>
                                        @endif
                                        @if($disc->type === 'percentage' && $disc->max_discount > 0)
                                        <span class="d-flex align-items-center gap-1 text-muted">
                                            <i class="ti ti-arrow-bar-down" style="font-size:.75rem;"></i>
                                            Maks Rp {{ number_format($disc->max_discount, 0, ',', '.') }}
                                        </span>
                                        @endif
                                        @if($disc->end_date)
                                        <span class="d-flex align-items-center gap-1 text-muted">
                                            <i class="ti ti-clock" style="font-size:.75rem;"></i>
                                            s/d {{ $disc->end_date->format('d M Y') }}
                                        </span>
                                        @endif
                                        @if($disc->usage_limit)
                                        <span class="d-flex align-items-center gap-1 text-muted">
                                            <i class="ti ti-stack" style="font-size:.75rem;"></i>
                                            Sisa {{ $disc->usage_limit - $disc->usage_count }}x
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Selected indicator --}}
                                <div class="voucher-check-icon d-none flex-shrink-0 mt-1">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                        style="width:26px;height:26px;background:#198754;">
                                        <i class="ti ti-check text-white" style="font-size:.75rem;"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Code pill --}}
                            <div class="mt-2">
                                <span class="badge rounded-pill border px-2 py-1 font-monospace"
                                    style="background:#fff;color:#6c757d;border-color:#dee2e6!important;font-size:.68rem;"
                                    title="Kode voucher">
                                    <i class="ti ti-barcode me-1"></i>{{ $disc->code }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                {{-- Empty state --}}
                <div class="text-center py-5">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width:72px;height:72px;background:#f8f9fa;">
                        <i class="ti ti-ticket-off text-muted" style="font-size:2rem;"></i>
                    </div>
                    <div class="fw-semibold text-dark mb-1">Tidak Ada Voucher Aktif</div>
                    <div class="text-muted small">Belum ada promo yang tersedia saat ini.</div>
                </div>
                @endif
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold" id="btnConfirmVoucher" onclick="confirmVoucher()" disabled>
                    <i class="ti ti-check me-1"></i>Pakai Voucher Ini
                </button>
            </div>
        </div>
    </div>
</div>


@push('scripts')
@if(config('midtrans.is_production'))
<script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@else
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif


<style>
.payment-opt { user-select:none; }
.payment-opt:hover { background: #f8f9fa !important; }
.item-row { transition: box-shadow .2s; }
.item-row:hover { box-shadow: 0 2px 12px rgba(37,99,235,.08); }
/* Voucher picker */
.voucher-card:hover { border-color:#2563eb !important; box-shadow:0 4px 16px rgba(37,99,235,.15); transform:translateY(-1px); }
.voucher-card.selected { border-color:#198754 !important; background:#f0fff4; box-shadow:0 4px 16px rgba(25,135,84,.12); }
.voucher-card.ineligible { opacity:.45; cursor:not-allowed; pointer-events:none; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const settings = {
        taxEnabled: {{ ($settings['tax_enabled']??'0') === '1' ? 'true' : 'false' }},
        taxPercent: {{ ($settings['tax_percent']??'0') }},
        serviceFee: {{ ($settings['service_fee']??'0') }},
    };

    let itemIndex = 1;
    window.discountAmount = 0;  // global so voucher picker can access


    function fmt(n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); }

    // ── Recalc ─────────────────────────────────────────────────
    window.recalc = function recalc() {

        let subtotal = 0;
        const rows   = [];

        document.querySelectorAll('.item-row').forEach(row => {
            const sel   = row.querySelector('.service-select');
            const qty   = parseFloat(row.querySelector('.qty-input')?.value) || 0;
            const opt   = sel?.selectedOptions[0];
            const price = parseFloat(opt?.dataset.price) || 0;
            const type  = opt?.dataset.type || 'per_kg';
            const name  = opt?.text?.split(' — ')[0] || '';
            const line  = type === 'flat' ? price : Math.round(price * qty * 100) / 100;

            row.querySelector('.price-display').value    = price ? fmt(price) : '';
            row.querySelector('.subtotal-display').value = line  ? fmt(line)  : '';

            if (price && qty) {
                subtotal += line;
                rows.push({ name, qty, type: opt?.dataset.typeLabel ?? '', line });
            }
        });

        const disc    = window.discountAmount;
        const taxable = Math.max(0, subtotal - disc);
        const tax     = settings.taxEnabled ? Math.round(taxable * settings.taxPercent / 100) : 0;
        const fee     = settings.serviceFee;
        const total   = Math.max(0, taxable + tax + fee);

        document.getElementById('summarySubtotal').textContent = fmt(subtotal);
        document.getElementById('summaryTotal').textContent    = fmt(total);

        const taxEl = document.getElementById('summaryTax');
        if (taxEl) taxEl.textContent = fmt(tax);

        const discRow = document.getElementById('discountRow');
        if (discRow) {
            discRow.style.display = disc > 0 ? 'flex' : 'none';
            document.getElementById('summaryDiscount').textContent = '– ' + fmt(disc);
        }

        const list = document.getElementById('itemSummaryList');
        if (rows.length) {
            list.innerHTML = rows.map(r =>
                `<div class="d-flex justify-content-between align-items-center gap-2">
                    <span class="text-muted text-truncate" title="${r.name}" style="max-width:60%;">
                        <i class="ti ti-circle-filled me-1" style="font-size:.4rem;color:var(--bs-primary);vertical-align:middle;"></i>${r.name} × ${r.qty} ${r.type}
                    </span>
                    <span class="fw-semibold text-dark flex-shrink-0">${fmt(r.line)}</span>
                </div>`
            ).join('');
        } else {
            list.innerHTML = '<div class="text-muted text-center py-2 small fst-italic"><i class="ti ti-receipt-off me-1"></i>Belum ada layanan dipilih</div>';
        }

        return { subtotal };
    }

    // ── Build item row ────────────────────────────────────────
    function buildRow(idx) {
        const optionsHTML = document.querySelector('.service-select').innerHTML;
        const div = document.createElement('div');
        div.className = 'item-row rounded-3 p-3 mb-3 position-relative';
        div.style.cssText = 'background:#f8faff; border: 1.5px solid #2563eb25; border-left: 4px solid var(--bs-primary) !important;';
        div.innerHTML = `
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label fw-medium small mb-1">Layanan <span class="text-danger">*</span></label>
                    <select class="form-select service-select" name="items[${idx}][service_id]" required>
                        ${optionsHTML}
                    </select>
                </div>
                <div class="col-5 col-md-2">
                    <label class="form-label fw-medium small mb-1">Jumlah <span class="unit-label badge bg-light text-muted rounded-pill" style="font-size:.65rem;">–</span></label>
                    <input type="number" class="form-control qty-input" name="items[${idx}][quantity]" value="1" min="0.1" step="0.1">
                </div>
                <div class="col-5 col-md-2">
                    <label class="form-label fw-medium small mb-1">Harga/Unit</label>
                    <input type="text" class="form-control bg-light border-0 price-display text-muted" readonly placeholder="–">
                </div>
                <div class="col-9 col-md-2">
                    <label class="form-label fw-medium small mb-1">Subtotal</label>
                    <input type="text" class="form-control fw-bold text-primary subtotal-display" style="background:#2563eb08; border-color:#2563eb20 !important;" readonly placeholder="–">
                </div>
                <div class="col-3 col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-sm w-100 rounded-3 remove-item"
                        style="height:38px;background:#dc354515;color:#dc3545;" title="Hapus baris">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
                <div class="col-12">
                    <input type="text" class="form-control form-control-sm" style="border-style:dashed !important; font-size:.8rem;"
                        name="items[${idx}][notes]" placeholder="📝  Catatan untuk layanan ini (opsional)...">
                </div>
            </div>`;
        return div;
    }

    function attachRowEvents(row) {
        row.querySelector('.service-select').addEventListener('change', function () {
            const opt = this.selectedOptions[0];
            row.querySelector('.unit-label').textContent = opt?.dataset.typeLabel || '–';
            recalc();
        });
        row.querySelector('.qty-input').addEventListener('input', recalc);
        row.querySelector('.remove-item').addEventListener('click', function () {
            if (document.querySelectorAll('.item-row').length === 1) {
                Swal.fire({toast:true, position:'top-end', icon:'warning', title:'Minimal 1 layanan harus ada!', showConfirmButton:false, timer:2000});
                return;
            }
            row.style.transition = 'opacity .2s';
            row.style.opacity = '0';
            setTimeout(() => { row.remove(); recalc(); }, 200);
        });
    }

    attachRowEvents(document.querySelector('.item-row'));

    function addItem() {
        const row = buildRow(itemIndex++);
        document.getElementById('itemsContainer').appendChild(row);
        attachRowEvents(row);
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    document.getElementById('btnAddItem').addEventListener('click', addItem);
    document.getElementById('btnAddItem2').addEventListener('click', addItem);

    // ── Payment method highlight ──────────────────────────────
    document.querySelectorAll('[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.payment-opt').forEach(lbl => {
                lbl.style.background  = '';
                lbl.style.borderColor = '#e9ecef';
                lbl.style.boxShadow   = '';
            });
            const isCash  = this.value === 'cash';
            const color   = isCash ? '#198754' : 'var(--bs-primary)';
            const bgColor = isCash ? '#19875408' : '#2563eb08';
            const label   = this.closest('.payment-opt');
            if (label) {
                label.style.background  = bgColor;
                label.style.borderColor = isCash ? '#198754' : '#2563eb';
                label.style.boxShadow   = `0 0 0 3px ${isCash ? '#19875420' : '#2563eb20'}`;
            }
            const info = document.getElementById('midtransInfoCard');
            info.classList.toggle('d-none', isCash);
        });
    });
    document.querySelector('[name="payment_method"]:checked').dispatchEvent(new Event('change'));

    // ── Customer search ───────────────────────────────────────
    let searchTimer;
    const searchInput    = document.getElementById('customerSearch');
    const clearBtn       = document.getElementById('clearCustomer');
    const suggestionsBox = document.getElementById('customerSuggestions');

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        const q = this.value.trim();
        clearBtn.style.display = q ? 'flex' : 'none';
        if (q.length < 2) { suggestionsBox.classList.add('d-none'); return; }

        searchTimer = setTimeout(async () => {
            try {
                const res  = await fetch(`{{ route('admin.transactions.search-customers') }}?q=${encodeURIComponent(q)}`);
                const data = await res.json();
                if (!data.length) { suggestionsBox.classList.add('d-none'); return; }
                suggestionsBox.innerHTML = data.map(c =>
                    `<div class="px-3 py-2 customer-item d-flex align-items-center gap-3 border-bottom" style="cursor:pointer;" data-id="${c.id}" data-name="${c.name}" data-phone="${c.phone||''}">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                            style="width:32px;height:32px;font-size:.8rem;background:linear-gradient(135deg,#2563eb,#60a5fa);">
                            ${c.name.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <div class="fw-semibold small">${c.name}</div>
                            <div class="text-muted" style="font-size:.7rem;">${c.phone||'–'} · ${c.address||'Tidak ada alamat'}</div>
                        </div>
                    </div>`
                ).join('');
                suggestionsBox.classList.remove('d-none');
            } catch(e) {}
        }, 300);
    });

    clearBtn.addEventListener('click', function () {
        searchInput.value = '';
        document.getElementById('customer_id').value    = '';
        document.getElementById('customer_name').value  = '';
        document.getElementById('customer_phone').value = '';
        this.style.display = 'none';
        suggestionsBox.classList.add('d-none');
    });

    document.addEventListener('click', function (e) {
        const item = e.target.closest('.customer-item');
        if (item) {
            document.getElementById('customer_id').value    = item.dataset.id;
            document.getElementById('customer_name').value  = item.dataset.name;
            document.getElementById('customer_phone').value = item.dataset.phone;
            searchInput.value = item.dataset.name;
            clearBtn.style.display = 'flex';
            suggestionsBox.classList.add('d-none');
            document.getElementById('customer_name').classList.remove('is-invalid');
        } else if (!e.target.closest('#customerSearch') && !e.target.closest('#clearCustomer')) {
            suggestionsBox.classList.add('d-none');
        }
    });

    // ── Discount check ────────────────────────────────────────
    document.getElementById('btnCheckDiscount').addEventListener('click', async function () {
        const code = document.getElementById('discount_code').value.trim();
        const msg  = document.getElementById('discountMsg');
        if (!code) {
            msg.innerHTML = '<span class="text-warning"><i class="ti ti-alert-circle me-1"></i>Masukkan kode diskon terlebih dahulu.</span>';
            return;
        }
        const { subtotal } = recalc();
        if (subtotal <= 0) {
            msg.innerHTML = '<span class="text-warning"><i class="ti ti-alert-circle me-1"></i>Pilih layanan dahulu sebelum menggunakan diskon.</span>';
            return;
        }
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        try {
            const res  = await fetch(`{{ route('admin.transactions.check-discount') }}?code=${encodeURIComponent(code)}&subtotal=${subtotal}`);
            const data = await res.json();
            if (data.valid) {
                window.discountAmount = data.discount_amount;
                // Sync applied banner from manual code entry
                applyVoucherToForm(code, code, data.discount_amount);
                msg.innerHTML  = `<span class="text-success"><i class="ti ti-check me-1"></i>${data.message}</span>`;
            } else {
                window.discountAmount = 0;
                removeVoucher();
                msg.innerHTML  = `<span class="text-danger"><i class="ti ti-x me-1"></i>${data.message}</span>`;
            }
            recalc();
        } catch(e) {
            msg.innerHTML = '<span class="text-danger"><i class="ti ti-alert-circle me-1"></i>Error server.</span>';
        }
        this.disabled = false;
        this.innerHTML = '<i class="ti ti-check me-1"></i>Pakai';
    });

    // ── Form Submit ───────────────────────────────────────────
    document.getElementById('transactionForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const customerName = document.getElementById('customer_name').value.trim();
        if (!customerName) {
            document.getElementById('customer_name').classList.add('is-invalid');
            document.getElementById('customer_name').focus();
            Swal.fire({ title: 'Nama Wajib Diisi', text: 'Nama pelanggan tidak boleh kosong.', icon: 'warning' });
            return;
        }

        const hasValidItem = Array.from(document.querySelectorAll('.item-row')).some(row => {
            const sel = row.querySelector('.service-select');
            return sel && sel.value;
        });
        if (!hasValidItem) {
            Swal.fire({ title: 'Belum Ada Layanan', text: 'Pilih minimal 1 layanan sebelum menyimpan.', icon: 'warning' });
            return;
        }

        const btn    = document.getElementById('btnSubmit');
        const method = document.querySelector('[name="payment_method"]:checked').value;

        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback.dynamic-err').forEach(el => el.remove());

        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.btn-loading').classList.remove('d-none');

        try {
            const res  = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (res.status === 422) {
                btn.disabled = false;
                btn.querySelector('.btn-text').classList.remove('d-none');
                btn.querySelector('.btn-loading').classList.add('d-none');
                for (let key in data.errors) {
                    const input = document.querySelector(`[name="${key}"]`)
                               || document.querySelector(`[name^="${key.split('.')[0]}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const div = document.createElement('div');
                        div.className = 'invalid-feedback dynamic-err';
                        div.innerHTML = `<i class="ti ti-alert-circle me-1"></i>${data.errors[key][0]}`;
                        input.closest('.input-group')?.after(div) || input.after(div);
                    }
                }
                Swal.fire({ title: 'Data Tidak Valid', text: 'Periksa isian form.', icon: 'error' });
                return;
            }

            if (!res.ok) throw new Error(data.error || 'Server Error');

            if (method === 'midtrans' && data.snap_token) {
                snap.pay(data.snap_token, {
                    onSuccess: async () => {
                        await fetch(`${data.redirect}/check-payment`);
                        window.location.href = data.redirect;
                    },
                    onPending: () => { window.location.href = data.redirect; },
                    onError:   () => {
                        Swal.fire('Gagal', 'Pembayaran gagal.', 'error');
                        btn.disabled = false;
                        btn.querySelector('.btn-text').classList.remove('d-none');
                        btn.querySelector('.btn-loading').classList.add('d-none');
                    },
                    onClose: () => {
                        Swal.fire('Info', 'Pembayaran ditunda. Bisa bayar nanti di detail transaksi.', 'info')
                            .then(() => window.location.href = data.redirect);
                    }
                });
            } else {
                window.location.href = data.redirect;
            }

        } catch (err) {
            Swal.fire('Error', err.message, 'error');
            btn.disabled = false;
            btn.querySelector('.btn-text').classList.remove('d-none');
            btn.querySelector('.btn-loading').classList.add('d-none');
        }
    });

    recalc();
});

// ══════════════════════════════════════════════════════════════
//  VOUCHER PICKER  (global scope — called from onclick attrs)
// ══════════════════════════════════════════════════════════════
let selectedVoucherEl = null;

function openVoucherModal() {
    // Show current subtotal in modal hint
    const hint = document.getElementById('modalSubtotalHint');
    if (hint) hint.textContent = document.getElementById('summarySubtotal').textContent;

    // Disable vouchers that don't meet min transaction
    const st = getGlobalSubtotal();
    document.querySelectorAll('.voucher-card').forEach(c => {
        const min = parseFloat(c.dataset.min ?? 0);
        if (min > 0 && st < min) {
            c.classList.add('ineligible');
            c.title = 'Min. transaksi belum terpenuhi';
        } else {
            c.classList.remove('ineligible');
            c.title = '';
        }
    });

    new bootstrap.Modal(document.getElementById('voucherModal')).show();
}

function getGlobalSubtotal() {
    let st = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const sel = row.querySelector('.service-select');
        const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
        const opt = sel?.selectedOptions[0];
        const price = parseFloat(opt?.dataset.price) || 0;
        const type  = opt?.dataset.type || 'per_kg';
        if (price && qty) st += type === 'flat' ? price : Math.round(price * qty * 100) / 100;
    });
    return st;
}

function selectVoucher(card) {
    if (selectedVoucherEl) {
        selectedVoucherEl.classList.remove('selected');
        selectedVoucherEl.querySelector('.voucher-check-icon')?.classList.add('d-none');
    }
    selectedVoucherEl = card;
    card.classList.add('selected');
    card.querySelector('.voucher-check-icon')?.classList.remove('d-none');
    document.getElementById('btnConfirmVoucher').disabled = false;
}

function confirmVoucher() {
    if (!selectedVoucherEl) return;
    const { code, name, type, value, max } = selectedVoucherEl.dataset;
    const subtotal = getGlobalSubtotal();
    let amount = 0;
    if (type === 'fixed') {
        amount = Math.min(parseFloat(value), subtotal);
    } else {
        amount = subtotal * (parseFloat(value) / 100);
        const maxD = parseFloat(max ?? 0);
        if (maxD > 0) amount = Math.min(amount, maxD);
    }
    applyVoucherToForm(code, name, amount);
    bootstrap.Modal.getInstance(document.getElementById('voucherModal'))?.hide();
}

function applyVoucherToForm(code, name, amount) {
    window.discountAmount = amount;
    document.getElementById('discount_code').value = code;
    const savings = 'Hemat Rp ' + Math.round(amount).toLocaleString('id-ID');
    // Update picker row
    const lbl = document.getElementById('voucherPickerLabel');
    lbl.textContent = name;
    lbl.classList.remove('text-muted');
    lbl.classList.add('fw-semibold', 'text-dark');
    document.getElementById('voucherPickerSub').textContent = code;
    document.getElementById('voucherSelectedBadge').classList.remove('d-none');
    // Show applied banner
    document.getElementById('voucherApplied').classList.remove('d-none');
    document.getElementById('voucherAppliedName').textContent = name + '  (' + code + ')';
    document.getElementById('voucherAppliedSaving').textContent = savings;
    // Manual input feedback
    document.getElementById('discountMsg').innerHTML =
        '<span class="text-success"><i class="ti ti-check me-1"></i>Voucher <b>' + code + '</b> terpasang – ' + savings + '</span>';
    recalc();
}

function removeVoucher() {
    window.discountAmount = 0;
    document.getElementById('discount_code').value = '';
    document.getElementById('discountMsg').innerHTML = '';
    const lbl = document.getElementById('voucherPickerLabel');
    lbl.textContent = 'Pilih atau gunakan voucher promo';
    lbl.classList.add('text-muted');
    lbl.classList.remove('fw-semibold', 'text-dark');
    document.getElementById('voucherPickerSub').textContent = '{{ $discounts->count() > 0 ? $discounts->count() . " voucher tersedia" : "Tidak ada voucher aktif" }}';
    document.getElementById('voucherSelectedBadge').classList.add('d-none');
    document.getElementById('voucherApplied').classList.add('d-none');
    if (selectedVoucherEl) {
        selectedVoucherEl.classList.remove('selected');
        selectedVoucherEl.querySelector('.voucher-check-icon')?.classList.add('d-none');
        selectedVoucherEl = null;
    }
    document.getElementById('btnConfirmVoucher').disabled = true;
    recalc();
}
</script>
@endpush

@endsection
