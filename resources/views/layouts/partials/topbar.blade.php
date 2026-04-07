@php
    $user = Auth::guard('customer')->check() ? Auth::guard('customer')->user() : auth()->user();
    $isCustomer = Auth::guard('customer')->check();
@endphp

<!-- TOPBAR -->
<nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3">
    <button id="toggleBtn" class="d-none d-lg-inline-flex btn btn-light btn-icon btn-sm ">
        <i class="ti ti-layout-sidebar-left-expand"></i>
    </button>

    <!-- MOBILE -->
    <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none me-2">
        <i class="ti ti-layout-sidebar-left-expand"></i>
    </button>
    <div>
        <!-- Navbar nav -->
        <ul class="list-unstyled d-flex align-items-center mb-0 gap-1">
            <!-- Bell icon / Notifikasi -->
            <li>
                <a class="position-relative btn-icon btn-sm btn-light btn rounded-circle" data-bs-toggle="dropdown" aria-expanded="false" href="#" role="button" id="notifBell">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-bell">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                        <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                    </svg>
                    @php
                        if ($isCustomer) {
                            $notifCount = \App\Models\Transaction::where('customer_id', $user->id)
                                ->where('updated_at', '>=', now()->subHours(24))
                                ->count();
                        } else {
                            $notifCount = \App\Models\Transaction::where('created_at', '>=', now()->subHours(24))
                                ->when($user->isEmployee(), fn($q) => $q->where('created_by', $user->id))
                                ->count();
                        }
                    @endphp
                    @if($notifCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger mt-2 ms-n2" id="notifBadge">
                        {{ $notifCount > 9 ? '9+' : $notifCount }}
                        <span class="visually-hidden">unread messages</span>
                    </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-md p-0" style="min-width:320px; max-height:400px; overflow-y:auto;">
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                        <span class="fw-semibold small">Notifikasi</span>
                        <span class="badge bg-primary-subtle text-primary">{{ $notifCount }} baru</span>
                    </div>
                    <ul class="list-unstyled p-0 m-0" id="notifList">
                        @php
                            if ($isCustomer) {
                                $notifItems = \App\Models\Transaction::where('customer_id', $user->id)
                                    ->latest()
                                    ->take(6)
                                    ->get();
                            } else {
                                $notifItems = \App\Models\Transaction::with('customer')
                                    ->when($user->isEmployee(), fn($q) => $q->where('created_by', $user->id))
                                    ->latest()
                                    ->take(6)
                                    ->get();
                            }
                        @endphp
                        @forelse($notifItems as $trx)
                        <li class="p-3 border-bottom notif-item hover-bg-light">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:36px;height:36px;background:{{ $trx->order_status === 'pending' ? 'rgba(253,126,20,0.15)' : ($trx->order_status === 'done' || $trx->order_status === 'delivered' ? 'rgba(25,135,84,0.15)' : 'rgba(13,110,253,0.15)') }}">
                                    <i class="ti ti-receipt fs-5" style="color:{{ $trx->order_status_color }}"></i>
                                </div>
                                <div class="flex-grow-1 small">
                                    <p class="mb-0 fw-semibold text-truncate" style="max-width:200px;">{{ $trx->invoice_number }}</p>
                                    <p class="mb-1 text-muted">{{ $isCustomer ? 'Pesanan Anda' : $trx->customer_name }} &bull; <span style="color:{{ $trx->order_status_color }}">{{ $trx->order_status_label }}</span></p>
                                    <div class="text-secondary">{{ $trx->created_at->diffForHumans() }}</div>
                                </div>
                                <span class="badge" style="background:{{ $trx->order_status_color }}20;color:{{ $trx->order_status_color }};font-size:10px;">
                                    {{ $trx->formatted_total }}
                                </span>
                            </div>
                        </li>
                        @empty
                        <li class="p-4 text-center text-muted small">
                            <i class="ti ti-bell-off fs-2 d-block mb-2 opacity-50"></i>
                            Tidak ada notifikasi
                        </li>
                        @endforelse
                    </ul>
                    @if($notifItems->isNotEmpty())
                    <div class="px-3 py-2 text-center border-top">
                        @if($isCustomer)
                            <a href="{{ route('customer.transactions.index') }}" class="text-primary small">Lihat riwayat laundry</a>
                        @elseif($user->isAdmin())
                            <a href="{{ route('admin.transactions.index') }}" class="text-primary small">Lihat semua transaksi</a>
                        @else
                            <a href="{{ route('employee.transactions.index') }}" class="text-primary small">Lihat semua transaksi</a>
                        @endif
                    </div>
                    @endif
                </div>
            </li>
            <!-- Dropdown -->
            <li class="ms-3 dropdown">
                <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    @php
                        $photoUrl = (!$isCustomer && $user->photo)
                            ? Storage::disk('public')->url($user->photo)
                            : null;
                    @endphp
                    @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="" class="avatar avatar-sm rounded-circle object-fit-cover" style="width:36px;height:36px;" />
                    @else
                    <div class="avatar avatar-sm rounded-circle bg-primary d-flex align-items-center justify-content-center fw-bold text-white" style="width:36px;height:36px;font-size:14px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 220px;">
                    <div>
                        <div class="d-flex gap-3 align-items-center border-bottom px-3 py-3">
                            @if($photoUrl)
                            <img src="{{ $photoUrl }}" alt="" class="avatar avatar-md rounded-circle object-fit-cover" style="width:44px;height:44px;" />
                            @else
                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold" style="width:44px;height:44px;font-size:18px;flex-shrink:0;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            @endif
                            <div>
                                <h4 class="mb-0 small text-truncate" style="max-width: 140px;">{{ $user->name ?? 'Pengguna' }}</h4>
                                <p class="mb-0 small text-muted text-truncate" style="max-width: 140px;">
                                    @php
                                        if ($isCustomer) {
                                            $roleBadge = ['label'=>'Pelanggan', 'class'=>'text-success', 'bg'=>'bg-success-subtle'];
                                        } else {
                                            $roleBadge = match($user->role) {
                                                'admin'    => ['label'=>'Admin',    'class'=>'text-danger',  'bg'=>'bg-danger-subtle'],
                                                'owner'    => ['label'=>'Owner',    'class'=>'text-warning', 'bg'=>'bg-warning-subtle'],
                                                'employee' => ['label'=>'Karyawan', 'class'=>'text-primary', 'bg'=>'bg-primary-subtle'],
                                                default    => ['label'=>ucfirst($user->role), 'class'=>'text-muted', 'bg'=>'bg-secondary-subtle'],
                                            };
                                        }
                                    @endphp
                                    <span class="badge {{ $roleBadge['bg'] }} {{ $roleBadge['class'] }} px-2 py-1" style="font-size:10px;">
                                        {{ $roleBadge['label'] }}
                                    </span>
                                </p>
                                <p class="mb-0 small text-muted text-truncate" style="max-width: 140px;">{{ $user->email ?? '' }}</p>
                            </div>
                        </div>
                        <div class="p-3 d-flex flex-column gap-1 small lh-lg">
                            <a href="{{ $isCustomer ? route('customer.profile.edit') : route('profile.edit') }}" class="d-flex align-items-center gap-2 text-dark text-decoration-none py-1 px-2 rounded hover-bg-light">
                                <i class="ti ti-user-circle fs-5 text-primary"></i>
                                <span>Edit Profil</span>
                            </a>
                            @if(!$isCustomer && $user->isAdmin())
                            <a href="{{ route('admin.settings.index') }}" class="d-flex align-items-center gap-2 text-dark text-decoration-none py-1 px-2 rounded hover-bg-light">
                                <i class="ti ti-settings fs-5 text-muted"></i>
                                <span>Pengaturan</span>
                            </a>
                            @endif
                            <form method="POST" action="{{ $isCustomer ? route('customer.logout') : route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="bg-transparent border-0 text-danger w-100 text-start p-0 d-flex align-items-center gap-2 py-1 px-2 rounded" style="cursor:pointer;">
                                    <i class="ti ti-logout fs-5"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>

