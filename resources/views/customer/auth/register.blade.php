<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Daftar Akun - Portal Pelanggan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/assets/scss/style.scss', 'resources/assets/js/main.js'])
    <style>
        body {
            background-color: #f3f6ff;
            background-image: radial-gradient(circle at 10% 20%, rgb(239, 246, 255) 0%, rgb(219, 234, 254) 90%);
        }
        .register-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.97);
        }
    </style>
</head>

<body>
    <div class="container d-flex flex-column justify-content-center align-items-center min-vh-100 py-5">
        <div class="text-center mb-4">
            <div class="d-inline-flex bg-white p-3 rounded-circle shadow-sm mb-3">
                <i class="ti ti-washing-machine fs-1 text-primary"></i>
            </div>
            <h1 class="h2 fw-bold text-dark">Buat Akun Baru</h1>
            <p class="text-muted">Daftarkan diri untuk melacak laundry Anda</p>
        </div>

        <div class="card shadow-lg border-0 rounded-4 w-100 register-card" style="max-width: 480px;">
            <div class="card-body p-4 p-md-5">

                @if($errors->any())
                <div class="alert border-0 rounded-3 mb-4 small" style="background:#dc354510;border-left:3px solid #dc3545 !important;">
                    <i class="ti ti-alert-circle text-danger me-2"></i>
                    {{ $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ route('customer.register.post') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-medium text-dark small"><i class="ti ti-user me-1"></i>Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            name="name" value="{{ old('name') }}" placeholder="Nama lengkap Anda" required autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium text-dark small"><i class="ti ti-mail me-1"></i>Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" placeholder="email@contoh.com" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-dark small"><i class="ti ti-phone me-1"></i>Nomor HP</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                name="phone" value="{{ old('phone') }}" placeholder="08XXXXXXXXXX">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-dark small"><i class="ti ti-map-pin me-1"></i>Alamat</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                name="address" value="{{ old('address') }}" placeholder="Alamat singkat">
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium text-dark small"><i class="ti ti-lock me-1"></i>Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" placeholder="Minimal 8 karakter" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium text-dark small"><i class="ti ti-lock-check me-1"></i>Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control"
                            name="password_confirmation" placeholder="Ulangi password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm mb-3">
                        <i class="ti ti-user-plus me-2"></i>Buat Akun
                    </button>

                    <div class="text-center">
                        <p class="mb-0 small text-muted">
                            Sudah punya akun?
                            <a href="{{ route('customer.login') }}" class="text-primary fw-semibold text-decoration-none">Masuk di sini</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-4 text-center text-muted small">
            &copy; {{ date('Y') }} Laundry Management System.
        </div>
    </div>
</body>

</html>
