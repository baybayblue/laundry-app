<!-- SIDEBAR -->
<aside id="sidebar" class="sidebar">
    <div class="logo-area">
        <a href="/" class="d-inline-flex"><img src="{{ asset('assets/images/logo-icon.svg') }}" alt="" width="24">
            <span class="logo-text ms-2"> <img src="{{ asset('assets/images/logo.svg') }}" alt=""></span>
        </a>
    </div>
    <ul class="nav flex-column">

        {{-- ══════════════════════════════════ --}}
        {{-- ADMIN MENU --}}
        {{-- ══════════════════════════════════ --}}
        @if(auth()->guard('web')->user()?->isAdmin())
        <li class="px-4 py-2"><small class="nav-text">Main</small></li>
        <li>
            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">
                <i class="ti ti-home"></i><span class="nav-text">Dashboard</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                <i class="ti ti-users-group"></i><span class="nav-text">Manajemen User</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                <i class="ti ti-users"></i><span class="nav-text">Pelanggan</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}" href="{{ route('admin.employees.index') }}">
                <i class="ti ti-user-check"></i><span class="nav-text">Karyawan</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}" href="{{ route('admin.stock.index') }}">
                <i class="ti ti-package"></i><span class="nav-text">Stok Barang</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                <i class="ti ti-tags"></i><span class="nav-text">Kategori Barang</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}" href="{{ route('admin.services.index') }}">
                <i class="ti ti-ironing"></i><span class="nav-text">Layanan</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.discounts.*') ? 'active' : '' }}" href="{{ route('admin.discounts.index') }}">
                <i class="ti ti-tag"></i><span class="nav-text">Diskon & Promo</span>
            </a>
        </li>

        <li class="px-4 py-2 mt-2"><small class="nav-text">Monitoring</small></li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}" href="{{ route('admin.transactions.index') }}">
                <i class="ti ti-receipt"></i><span class="nav-text">Semua Transaksi</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.laporan-keuangan.*') ? 'active' : '' }}" href="{{ route('admin.laporan-keuangan.index') }}">
                <i class="ti ti-chart-bar"></i><span class="nav-text">Laporan Keuangan</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.attendances.index') ? 'active' : '' }}" href="{{ route('admin.attendances.index') }}">
                <i class="ti ti-calendar-check"></i><span class="nav-text">Monitoring Absensi</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.leave-requests.*') ? 'active' : '' }}" href="{{ route('admin.leave-requests.index') }}">
                <i class="ti ti-calendar-event"></i><span class="nav-text">Data Pengajuan Cuti</span>
            </a>
        </li>

        <li class="px-4 py-2 mt-2"><small class="nav-text">Presensi</small></li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.attendances.clock') ? 'active' : '' }}" href="{{ route('admin.attendances.clock') }}">
                <i class="ti ti-alarm"></i><span class="nav-text">Presensi Sekarang</span>
            </a>
        </li>

        <li class="px-4 py-2 mt-2"><small class="nav-text">Pengaturan</small></li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                <i class="ti ti-building-store"></i><span class="nav-text">Pengaturan Toko</span>
            </a>
        </li>
        @endif

        {{-- ══════════════════════════════════ --}}
        {{-- OWNER MENU (monitoring only) --}}
        {{-- ══════════════════════════════════ --}}
        @if(auth()->guard('web')->user()?->isOwner())
        <li class="px-4 py-2"><small class="nav-text">Dashboard</small></li>
        <li>
            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">
                <i class="ti ti-home"></i><span class="nav-text">Dashboard</span>
            </a>
        </li>

        <li class="px-4 py-2 mt-2"><small class="nav-text">Monitoring</small></li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}" href="{{ route('admin.transactions.index') }}">
                <i class="ti ti-receipt"></i><span class="nav-text">Data Laundry</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.laporan-keuangan.*') ? 'active' : '' }}" href="{{ route('admin.laporan-keuangan.index') }}">
                <i class="ti ti-chart-bar"></i><span class="nav-text">Laporan Keuangan</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.attendances.index') ? 'active' : '' }}" href="{{ route('admin.attendances.index') }}">
                <i class="ti ti-calendar-check"></i><span class="nav-text">Monitoring Absensi</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.leave-requests.*') ? 'active' : '' }}" href="{{ route('admin.leave-requests.index') }}">
                <i class="ti ti-calendar-event"></i><span class="nav-text">Monitoring Cuti</span>
            </a>
        </li>
        @endif

        {{-- ══════════════════════════════════ --}}
        {{-- EMPLOYEE MENU --}}
        {{-- ══════════════════════════════════ --}}
        @if(auth()->guard('web')->user()?->isEmployee())
        <li class="px-4 py-2"><small class="nav-text">Operasional</small></li>
        <li>
            <a class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}" href="{{ route('employee.dashboard') }}">
                <i class="ti ti-home"></i><span class="nav-text">Dashboard</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('employee.transactions.create') ? 'active' : '' }}" href="{{ route('employee.transactions.create') }}">
                <i class="ti ti-plus"></i><span class="nav-text">Tambah Transaksi</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('employee.transactions.index') ? 'active' : '' }}" href="{{ route('employee.transactions.index') }}">
                <i class="ti ti-receipt"></i><span class="nav-text">Data Laundry</span>
            </a>
        </li>

        <li>
            <a class="nav-link {{ request()->routeIs('employee.customers.*') ? 'active' : '' }}" href="{{ route('employee.customers.index') }}">
                <i class="ti ti-users"></i><span class="nav-text">Data Pelanggan</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('employee.attendances.clock') ? 'active' : '' }}" href="{{ route('employee.attendances.clock') }}">
                <i class="ti ti-alarm"></i><span class="nav-text">Presensi Sekarang</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('employee.leave-requests.*') ? 'active' : '' }}" href="{{ route('employee.leave-requests.index') }}">
                <i class="ti ti-calendar-event"></i><span class="nav-text">Pengajuan Cuti/Izin</span>
            </a>
        </li>
        @endif

        {{-- ══════════════════════════════════ --}}
        {{-- CUSTOMER MENU --}}
        {{-- ══════════════════════════════════ --}}
        @if(Auth::guard('customer')->check())
        <li class="px-4 py-2"><small class="nav-text">Portal Pelanggan</small></li>
        <li>
            <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">
                <i class="ti ti-smart-home"></i><span class="nav-text">Dashboard</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('customer.orders.create') ? 'active' : '' }}" href="{{ route('customer.orders.create') }}">
                <i class="ti ti-plus"></i><span class="nav-text">Buat Pesanan</span>
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('customer.transactions.*') ? 'active' : '' }}" href="{{ route('customer.transactions.index') }}">
                <i class="ti ti-receipt"></i><span class="nav-text">Transaksi Saya</span>
            </a>
        </li>
        <li class="px-4 py-2 mt-2"><small class="nav-text">Profil</small></li>
        <li>
            <a class="nav-link {{ request()->routeIs('customer.profile.edit') ? 'active' : '' }}" href="{{ route('customer.profile.edit') }}">
                <i class="ti ti-user-circle"></i><span class="nav-text">Edit Profil</span>
            </a>
        </li>
        @endif
    </ul>
</aside>
