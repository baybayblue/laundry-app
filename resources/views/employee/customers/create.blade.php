@extends('layouts.app')

@section('content')
{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #19875420, #19875410); border: 1px solid #19875430; width:48px; height:48px;">
                <i class="ti ti-user-plus fs-3 text-success"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Tambah Pelanggan</h1>
                <p class="mb-0 text-muted small">Daftarkan profil pelanggan baru ke dalam sistem</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('employee.customers.index') }}" class="btn btn-light d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- VALIDATION ALERT (Global) --}}
@if($errors->any())
<div class="alert border-0 rounded-4 shadow-sm mb-4 d-flex align-items-start gap-3" style="background:#dc354510; border-left: 4px solid #dc3545 !important;">
    <i class="ti ti-alert-circle text-danger fs-4 mt-1 flex-shrink-0"></i>
    <div>
        <div class="fw-semibold text-danger mb-1">Terdapat {{ $errors->count() }} kesalahan pada form:</div>
        <ul class="mb-0 small text-danger ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="row g-4">
    {{-- MAIN FORM --}}
    <div class="col-12 col-lg-8">
        <form action="{{ route('employee.customers.store') }}" method="POST" id="customerForm" novalidate>
            @csrf

            {{-- SECTION: Informasi Pelanggan --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-1 bg-primary bg-opacity-10">
                            <i class="ti ti-user-circle text-primary"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Informasi Pelanggan</h6>
                    </div>
                    <hr class="mt-3 mb-0">
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="name">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-user text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}"
                                    placeholder="Masukkan nama lengkap..."
                                    minlength="3" maxlength="255" required>
                                @error('name')
                                    <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="email">
                                Alamat Email <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-mail text-muted"></i></span>
                                <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}"
                                    placeholder="email@contoh.com" maxlength="255">
                                @error('email')
                                    <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-2 p-3 rounded-3" style="background:#0d6efd10; border: 1px dashed #0d6efd60;">
                                <i class="ti ti-info-circle text-primary fs-5 flex-shrink-0"></i>
                                <div class="small">
                                    Jika email diisi, pelanggan dapat menggunakannya untuk login ke portal mandiri. Kata sandi otomatis diset ke: <code class="fw-bold bg-primary bg-opacity-10 px-2 py-1 rounded text-primary">password</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION: Kontak & Alamat --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-1 bg-success bg-opacity-10">
                            <i class="ti ti-map-pin text-success"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Kontak & Alamat</h6>
                    </div>
                    <hr class="mt-3 mb-0">
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium small" for="phone">Nomor Handphone <span class="text-muted fw-normal">(Opsional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-phone text-muted"></i></span>
                                <input type="tel" class="form-control border-start-0 @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone') }}"
                                    placeholder="08xxxxxxxxxx" maxlength="20"
                                    pattern="^(08|\+62)[0-9]{7,12}$">
                                @error('phone')
                                    <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Format: 08xxxxxxxxxx atau +62xxxxxxxxxx</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium small" for="address">Alamat Lengkap <span class="text-muted fw-normal">(Opsional)</span></label>
                            <div class="input-group align-items-start">
                                <span class="input-group-text bg-light border-end-0 pt-2"><i class="ti ti-map-pin text-muted"></i></span>
                                <textarea class="form-control border-start-0 @error('address') is-invalid @enderror"
                                    id="address" name="address" rows="4"
                                    placeholder="Alamat penjemputan/pengantaran pakaian...">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="d-flex justify-content-end gap-3">
                <a href="{{ route('employee.customers.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                <button type="reset" class="btn btn-outline-secondary rounded-pill px-4" id="btnReset">
                    <i class="ti ti-refresh me-1"></i>Reset
                </button>
                <button type="submit" class="btn btn-success rounded-pill px-5 fw-semibold shadow-sm" id="btnSubmit">
                    <span class="btn-text"><i class="ti ti-check me-1"></i>Simpan Pelanggan</span>
                    <span class="btn-loading d-none">
                        <span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>

    {{-- SIDEBAR TIPS --}}
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-3" style="background: linear-gradient(135deg, #19875408, #fff);">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3 d-flex align-items-center gap-2 text-success">
                    <i class="ti ti-bulb"></i> Bantuan Operasional
                </h6>
                <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-3">
                    <li class="d-flex align-items-start gap-2">
                        <i class="ti ti-circle-check text-success mt-1 flex-shrink-0"></i>
                        <span>Daftarkan pelanggan sebelum membuat transaksi jika data belum ada.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="ti ti-circle-check text-success mt-1 flex-shrink-0"></i>
                        <span>Pastikan nomor handphone benar untuk mempermudah koordinasi kurir.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="ti ti-circle-check text-success mt-1 flex-shrink-0"></i>
                        <span>Alamat yang lengkap menjamin ketepatan waktu pengiriman pakaian.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Loading state on submit
        const form = document.getElementById('customerForm');
        const btnSubmit = document.getElementById('btnSubmit');
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) return;
            btnSubmit.disabled = true;
            btnSubmit.querySelector('.btn-text').classList.add('d-none');
            btnSubmit.querySelector('.btn-loading').classList.remove('d-none');
        });

        // Client-side validation feedback
        form.addEventListener('input', function (e) {
            const el = e.target;
            if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                if (el.checkValidity()) {
                    if (el.value.length > 0) {
                        el.classList.remove('is-invalid');
                        el.classList.add('is-valid');
                    } else {
                        el.classList.remove('is-invalid');
                        el.classList.remove('is-valid');
                    }
                } else if (el.value.length > 0) {
                    el.classList.add('is-invalid');
                    el.classList.remove('is-valid');
                }
            }
        });
    });
</script>
@endpush
@endsection
