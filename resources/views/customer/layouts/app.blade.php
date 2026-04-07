<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <title>@yield('title', 'Portal Pelanggan') - {{ config('app.name', 'Laundry') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/assets/scss/style.scss', 'resources/assets/js/main.js'])

    <!-- Midtrans Snap -->
    @if(config('midtrans.is_production'))
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif
</head>

<body>
    <div id="overlay" class="overlay"></div>

    @include('layouts.partials.topbar')

    @include('layouts.partials.sidebar')

    <!-- MAIN CONTENT -->
    <main id="content" class="content py-10">
        <div class="container-fluid">
            @yield('content')
            
            @include('layouts.partials.footer')
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                background: '#ffffff',
                color: '#343a40',
                customClass: {
                    popup: 'shadow-lg border-0 rounded-3',
                    timerProgressBar: 'bg-primary'
                },
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });

            @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session("success") }}',
                iconColor: '#0d6efd'
            });
            @endif

            @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session("error") }}'
            });
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>

