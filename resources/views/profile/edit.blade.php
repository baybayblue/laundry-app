@extends('layouts.app')

@section('content')

<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                style="background: linear-gradient(135deg, #2563eb20, #2563eb10); border: 1px solid #2563eb30; width:48px; height:48px;">
                <i class="ti ti-user-circle fs-3 text-primary"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Edit Profil</h1>
                <p class="mb-0 text-muted small">Perbarui informasi akun Anda sebagai
                    <span class="badge bg-{{ $user->isAdmin() ? 'danger' : 'primary' }}-subtle text-{{ $user->isAdmin() ? 'danger' : 'primary' }} px-2">
                        {{ $user->isAdmin() ? 'Owner' : 'Karyawan' }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">

        @if(session('success'))
        <div class="alert border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2"
            style="background:#19875415;border-left:4px solid #198754 !important;">
            <i class="ti ti-circle-check text-success fs-5"></i>
            <span class="small fw-medium text-success">{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="alert border-0 rounded-4 shadow-sm mb-4 d-flex align-items-start gap-3"
            style="background:#dc354510; border-left: 4px solid #dc3545 !important;">
            <i class="ti ti-alert-circle text-danger fs-4 mt-1 flex-shrink-0"></i>
            <div>
                <div class="fw-semibold text-danger mb-1">Terdapat kesalahan:</div>
                <ul class="mb-0 small text-danger ps-3">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
            @csrf
            @method('PUT')

            {{-- Foto Profil --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                        <i class="ti ti-photo text-primary"></i> Foto Profil
                    </h5>
                    <div class="d-flex align-items-center gap-4">
                        <div class="position-relative">
                            @if($user->photo)
                            <img src="{{ Storage::disk('public')->url($user->photo) }}" alt="photo"
                                id="photoPreview"
                                class="rounded-circle object-fit-cover border shadow-sm"
                                style="width:90px;height:90px;">
                            @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold bg-primary shadow-sm"
                                style="width:90px;height:90px;font-size:2rem;"
                                id="photoPlaceholder">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <img id="photoPreview" class="rounded-circle object-fit-cover border shadow-sm d-none"
                                style="width:90px;height:90px;" alt="preview">
                            @endif
                        </div>
                        <div>
                            <label for="photoInput" class="btn btn-light rounded-pill border px-4 fw-medium mb-2" style="cursor:pointer;">
                                <i class="ti ti-upload me-2"></i>Pilih Foto
                            </label>
                            <input type="file" id="photoInput" name="photo" accept="image/*" class="d-none">
                            <div class="text-muted small">JPG, PNG atau WebP. Maks. 2MB</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informasi Dasar --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                        <i class="ti ti-user text-primary"></i> Informasi Pribadi
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-user text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-mail text-muted"></i></span>
                                <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Nomor HP</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-phone text-muted"></i></span>
                                <input type="text" class="form-control border-start-0"
                                    name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08XXXXXXXXXX">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Alamat</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-map-pin text-muted"></i></span>
                                <input type="text" class="form-control border-start-0"
                                    name="address" value="{{ old('address', $user->address) }}" placeholder="Alamat">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ubah Password --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
                        <i class="ti ti-lock text-primary"></i> Ubah Password
                    </h5>
                    <p class="text-muted small mb-4">Kosongkan jika tidak ingin mengubah password</p>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium small">Password Saat Ini</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-lock text-muted"></i></span>
                                <input type="password" class="form-control border-start-0 @error('current_password') is-invalid @enderror"
                                    name="current_password" placeholder="Masukkan password saat ini">
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-key text-muted"></i></span>
                                <input type="password" class="form-control border-start-0 @error('password') is-invalid @enderror"
                                    name="password" placeholder="Min. 8 karakter">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-key text-muted"></i></span>
                                <input type="password" class="form-control border-start-0"
                                    name="password_confirmation" placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                    <i class="ti ti-check me-1"></i>Simpan Perubahan
                </button>
                @if(auth()->user()->isAdmin())
                <a href="/" class="btn btn-light rounded-pill px-4 border">Batal</a>
                @else
                <a href="{{ route('employee.dashboard') }}" class="btn btn-light rounded-pill px-4 border">Batal</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Info Card --}}
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top:80px;">
            <div class="card-body p-4 text-center">
                @if($user->photo)
                <img src="{{ Storage::disk('public')->url($user->photo) }}" alt="" class="rounded-circle object-fit-cover mb-3 border shadow-sm" style="width:80px;height:80px;">
                @else
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold bg-primary mb-3" style="width:80px;height:80px;font-size:1.8rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                @endif
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-2">{{ $user->email }}</p>
                <span class="badge bg-{{ $user->isAdmin() ? 'danger' : 'primary' }}-subtle text-{{ $user->isAdmin() ? 'danger' : 'primary' }} px-3 py-2 rounded-pill mb-3">
                    {{ $user->isAdmin() ? '👑 Owner' : '💼 Karyawan' }}
                </span>
                @if($user->position)
                <div class="text-muted small">{{ $user->position }}</div>
                @endif
                @if($user->phone)
                <div class="text-muted small mt-1"><i class="ti ti-phone me-1"></i>{{ $user->phone }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('photoInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
        const preview = document.getElementById('photoPreview');
        const placeholder = document.getElementById('photoPlaceholder');
        if (preview) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        }
        if (placeholder) placeholder.classList.add('d-none');
    };
    reader.readAsDataURL(file);
});
</script>
@endpush

@endsection
