<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Sistem Internal - Laundry Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/assets/scss/style.scss', 'resources/assets/js/main.js'])
    <style>
        body {
            background-color: #f8f9fa;
            background-image: radial-gradient(circle at 50% -20%, #e9ecef, #f8f9fa);
        }
        .login-card {
            background: #ffffff;
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
        }
        .icon-circle {
            width: 64px;
            height: 64px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <div class="container d-flex flex-column justify-content-center align-items-center min-vh-100 py-5">
        <div class="text-center mb-4">
            <div class="icon-circle bg-primary bg-opacity-10 mb-3">
                <i class="ti ti-shield-lock fs-1 text-primary"></i>
            </div>
            <h1 class="h3 fw-bold text-dark">Portal Internal</h1>
            <p class="text-muted">Masuk sebagai Admin atau Karyawan</p>
        </div>

        <div class="card border-0 rounded-4 w-100 login-card" style="max-width: 420px;">
            <div class="card-body p-4 p-md-5">
                @if (session('success'))
                    <div class="alert alert-success border-0 rounded-3 mb-4">
                        <i class="ti ti-check me-1"></i> {{ session('success') }}
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="alert alert-danger border-0 rounded-3 mb-4">
                        <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf
                    
                    <div class="mb-4">
                        <label for="email" class="form-label fw-medium text-dark"><i class="ti ti-mail me-1"></i> Email</label>
                        <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" required autofocus>
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
                    
                    <div class="text-center">
                        <a href="{{ route('customer.login') }}" class="text-decoration-none small text-muted hover-primary">
                            <i class="ti ti-external-link me-1"></i> Portal Pelanggan
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
