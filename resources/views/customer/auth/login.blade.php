<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Customer Login - Laundry System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/assets/scss/style.scss', 'resources/assets/js/main.js'])
    <style>
        body {
            background-color: #f3f6ff;
            background-image: radial-gradient(circle at 10% 20%, rgb(239, 246, 255) 0%, rgb(219, 234, 254) 90%);
        }
        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
    </style>
</head>

<body>
    <div class="container d-flex flex-column justify-content-center align-items-center min-vh-100 py-5">
        <div class="text-center mb-4">
            <div class="d-inline-flex bg-white p-3 rounded-circle shadow-sm mb-3">
                <i class="ti ti-washing-machine fs-1 text-primary"></i>
            </div>
            <h1 class="h2 fw-bold text-dark">Portal Pelanggan</h1>
            <p class="text-muted">Masuk untuk melihat status cucian Anda</p>
        </div>

        <div class="card shadow-lg border-0 rounded-4 w-100 login-card" style="max-width: 420px;">
            <div class="card-body p-4 p-md-5">
                <form method="POST" action="{{ route('customer.login.post') }}" novalidate>
                    @csrf
                    
                    <div class="mb-4">
                        <label for="email" class="form-label fw-medium text-dark"><i class="ti ti-mail me-1"></i> Email Address</label>
                        <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="masukkan email anda..." required autofocus>
                        @error('email')
                            <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-medium text-dark"><i class="ti ti-lock me-1"></i> Password</label>
                        <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••" required>
                        @error('password')
                            <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label text-muted small user-select-none" for="remember">Ingat Saya</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm mb-3">
                        Masuk Sistem
                    </button>

                    <div class="text-center mb-3">
                        <p class="mb-0 small text-muted">
                            Belum punya akun?
                            <a href="{{ route('customer.register') }}" class="text-primary fw-semibold text-decoration-none">Daftar sekarang</a>
                        </p>
                    </div>
                    
                    <div class="text-center">
                        <a href="{{ url('/') }}" class="text-decoration-none small text-muted hover-primary">
                            <i class="ti ti-arrow-left me-1"></i> Kembali ke Website Utama
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="mt-5 text-center text-muted small">
            &copy; {{ date('Y') }} Laundry Management System.
        </div>
    </div>
</body>

</html>
