@extends('customer.layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="font-size: .75rem;">
                <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profil Saya</li>
            </ol>
        </nav>
        <h1 class="fs-4 mb-2 fw-bold text-dark">Informasi Profil</h1>
        <p class="text-muted mb-0 small">
            Perbarui informasi pribadi dan keamanan akun Anda.
        </p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-3 p-3">
    <div class="bg-success bg-opacity-20 p-2 rounded-circle">
        <i class="ti ti-check fs-5 text-success"></i>
    </div>
    <div class="fw-semibold small text-success">{{ session('success') }}</div>
</div>
@endif

<form method="POST" action="{{ route('customer.profile.update') }}">
    @csrf
    @method('PUT')

    <div class="row g-4 pb-5">
        <!-- PERSONAL INFO -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center gap-2">
                    <i class="ti ti-user-circle text-primary fs-5"></i>
                    <h6 class="fw-bold mb-0">Data Pribadi</h6>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted small mb-2">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="ti ti-user text-muted"></i></span>
                                <input type="text" class="form-control bg-light border-start-0 rounded-end-3 py-2 @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $customer->name) }}" required>
                            </div>
                            @error('name')<div class="invalid-feedback d-block small mt-1 ps-2">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted small mb-2">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="ti ti-mail text-muted"></i></span>
                                <input type="email" class="form-control bg-light border-start-0 rounded-end-3 py-2 @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email', $customer->email) }}" required>
                            </div>
                            @error('email')<div class="invalid-feedback d-block small mt-1 ps-2">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted small mb-2">No. WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="ti ti-brand-whatsapp text-muted"></i></span>
                                <input type="text" class="form-control bg-light border-start-0 rounded-end-3 py-2 @error('phone') is-invalid @enderror" 
                                       name="phone" value="{{ old('phone', $customer->phone) }}">
                            </div>
                            @error('phone')<div class="invalid-feedback d-block small mt-1 ps-2">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted small mb-2">Alamat Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-bottom-0 border-end-0 rounded-top-3 align-items-start pt-3 w-100" style="height: 45px;"><i class="ti ti-map-pin text-muted"></i></span>
                                <textarea name="address" rows="3" class="form-control bg-light border-top-0 rounded-bottom-3 py-2 @error('address') is-invalid @enderror" 
                                          style="border-top-left-radius: 0; border-top-right-radius: 0;">{{ old('address', $customer->address) }}</textarea>
                            </div>
                            @error('address')<div class="invalid-feedback d-block small mt-1 ps-2">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECURITY -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center gap-2">
                    <i class="ti ti-shield-lock text-primary fs-5"></i>
                    <h6 class="fw-bold mb-0">Keamanan Password</h6>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="alert alert-warning border-0 rounded-4 shadow-none p-3 mb-4" style="background: rgba(255, 193, 7, 0.1);">
                        <div class="d-flex align-items-start gap-2">
                            <i class="ti ti-info-circle text-warning fs-5"></i>
                            <div class="small text-muted lh-sm">Kosongi bidang di bawah ini jika Anda tidak berniat mengganti password.</div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted small mb-2">Password Saat Ini</label>
                            <div class="input-group shadow-none border rounded-3 p-1">
                                <span class="input-group-text border-0 bg-transparent px-2"><i class="ti ti-lock-open text-muted opacity-50"></i></span>
                                <input type="password" class="form-control border-0 py-2 @error('current_password') is-invalid @enderror" 
                                       name="current_password" placeholder="Min. 8 Karakter">
                            </div>
                            @error('current_password')<div class="invalid-feedback d-block small mt-1 ps-2">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted small mb-2">Password Baru</label>
                            <div class="input-group shadow-none border rounded-3 p-1">
                                <span class="input-group-text border-0 bg-transparent px-2"><i class="ti ti-key text-muted opacity-50"></i></span>
                                <input type="password" class="form-control border-0 py-2 @error('password') is-invalid @enderror" 
                                       name="password">
                            </div>
                            @error('password')<div class="invalid-feedback d-block small mt-1 ps-2">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted small mb-2">Konfirmasi Password</label>
                            <div class="input-group shadow-none border rounded-3 p-1">
                                <span class="input-group-text border-0 bg-transparent px-2"><i class="ti ti-circle-check text-muted opacity-50"></i></span>
                                <input type="password" class="form-control border-0 py-2" name="password_confirmation">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-2">
                <a href="{{ route('customer.dashboard') }}" class="btn btn-light rounded-pill px-4 fw-bold small text-muted border">
                    <i class="ti ti-arrow-left me-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary shadow-sm rounded-pill px-5 fw-bold py-2">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

