@extends('layouts.app')

@section('content')

{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-7">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                style="background:linear-gradient(135deg,#2563eb20,#2563eb10);border:1px solid #2563eb30;width:52px;height:52px;">
                <i class="ti ti-receipt-2 fs-3 text-primary"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge rounded-pill fw-medium" style="background:#2563eb15;color:#2563eb;font-size:.7rem;letter-spacing:.3px;">
                        {{ $transaction->invoice_number }}
                    </span>
                    <span class="badge rounded-pill fw-medium small"
                        style="background:{{ $transaction->order_status_color }}18;color:{{ $transaction->order_status_color }};border:1px solid {{ $transaction->order_status_color }}30;font-size:.7rem;">
                        {{ $transaction->order_status_label }}
                    </span>
                </div>
                <h1 class="fs-4 mb-0 fw-bold">Detail Transaksi</h1>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-5 d-flex gap-2 justify-content-md-end flex-wrap">
        @if($transaction->order_status === 'cancel_requested')
        <button type="button" id="btnApproveCancel"
            class="btn btn-sm btn-danger rounded-pill px-3 d-flex align-items-center gap-2 shadow-sm"
            data-url="{{ route('admin.transactions.approve-cancel', $transaction) }}">
            <i class="ti ti-check"></i> Setujui Pembatalan
        </button>
        @endif
        <a href="{{ route('admin.transactions.invoice', $transaction) }}" target="_blank"
            class="btn btn-sm btn-light rounded-pill px-3 d-flex align-items-center gap-2 border shadow-sm">
            <i class="ti ti-printer text-muted"></i> Cetak Invoice
        </a>
        @if($transaction->payment_method === 'midtrans' && $transaction->payment_status !== 'paid')
        <button type="button" id="btnRefreshPayment"
            class="btn btn-sm btn-outline-primary rounded-pill px-3 d-flex align-items-center gap-2"
            data-url="{{ route('admin.transactions.check-payment', $transaction) }}">
            <i class="ti ti-refresh"></i> Cek Bayar
        </button>
        @endif
        @if($transaction->payment_status !== 'paid' && auth()->user()->isAdmin())
        <form action="{{ route('admin.transactions.destroy', $transaction) }}" method="POST" class="d-inline" id="deleteForm">
            @csrf @method('DELETE')
            <button type="button" id="btnDelete"
                class="btn btn-sm btn-outline-danger rounded-pill px-3 d-flex align-items-center gap-2">
                <i class="ti ti-trash"></i> Hapus
            </button>
        </form>
        @endif
        <a href="{{ route('admin.transactions.index') }}"
            class="btn btn-sm btn-light rounded-pill px-3 d-flex align-items-center gap-2 border shadow-sm">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2" style="background:#19875415;border-left:3px solid #198754 !important;">
    <i class="ti ti-circle-check text-success fs-5"></i>
    <span class="small fw-medium text-success">{{ session('success') }}</span>
</div>
@endif

{{-- STATS BAR --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background:linear-gradient(135deg,#2563eb15,#fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Status Order</span>
                    <div class="rounded-2 p-1" style="background:{{ $transaction->order_status_color }}20;">
                        <i class="ti ti-clipboard-list fs-5" style="color:{{ $transaction->order_status_color }};"></i>
                    </div>
                </div>
                <span class="badge rounded-pill px-3 py-2"
                    style="background:{{ $transaction->order_status_color }}15;color:{{ $transaction->order_status_color }};border:1px solid {{ $transaction->order_status_color }}35;font-size:.78rem;">
                    {{ $transaction->order_status_label }}
                </span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background:linear-gradient(135deg,#19875415,#fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Pembayaran</span>
                    <div class="rounded-2 p-1" style="background:{{ $transaction->payment_status_color }}20;">
                        <i class="ti ti-wallet fs-5" style="color:{{ $transaction->payment_status_color }};"></i>
                    </div>
                </div>
                <span class="badge rounded-pill px-3 py-2"
                    style="background:{{ $transaction->payment_status_color }}15;color:{{ $transaction->payment_status_color }};border:1px solid {{ $transaction->payment_status_color }}35;font-size:.78rem;">
                    {{ $transaction->payment_status_label }}
                </span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background:linear-gradient(135deg,#6f42c115,#fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Metode</span>
                    <div class="rounded-2 p-1" style="background:#6f42c115;">
                        <i class="ti {{ $transaction->payment_method === 'midtrans' ? 'ti-credit-card' : 'ti-cash' }} fs-5" style="color:#6f42c1;"></i>
                    </div>
                </div>
                <div class="fw-semibold small mt-1">
                    @if($transaction->payment_method === 'midtrans')
                    <i class="ti ti-credit-card text-primary me-1"></i>Online
                    @else
                    <i class="ti ti-cash text-success me-1"></i>Tunai
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background:linear-gradient(135deg,#2563eb20,#fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Total Tagihan</span>
                    <div class="bg-primary bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-receipt-2 text-primary fs-5"></i>
                    </div>
                </div>
                <div class="fw-bold text-primary" style="font-size:1.15rem;">{{ $transaction->formatted_total }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- ── KIRI ── --}}
    <div class="col-12 col-lg-8">

        {{-- Bayar Sekarang (Midtrans pending) --}}
        @if($transaction->payment_method === 'midtrans' && $transaction->payment_status === 'pending' && $transaction->midtrans_snap_token)
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div style="height:3px;background:linear-gradient(90deg,var(--bs-primary),#60a5fa);"></div>
            <div class="card-body p-4 d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-primary bg-opacity-10 flex-shrink-0">
                        <i class="ti ti-credit-card text-primary fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-primary">Pembayaran Online Tertunda</h6>
                        <p class="text-muted small mb-0">Klik tombol untuk melanjutkan pembayaran via Midtrans (QRIS, VA, E-Wallet)</p>
                    </div>
                </div>
                <button type="button" id="btnPayNow"
                    class="btn btn-primary fw-semibold rounded-pill px-4 shadow-sm flex-shrink-0"
                    data-snap-token="{{ $transaction->midtrans_snap_token }}"
                    data-check-url="{{ route('admin.transactions.check-payment', $transaction) }}"
                    data-redirect="{{ route('admin.transactions.show', $transaction) }}">
                    <i class="ti ti-credit-card me-2"></i>Bayar Sekarang
                </button>
            </div>
        </div>
        @endif

        {{-- Timeline Status --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4 d-flex align-items-center gap-2">
                    <i class="ti ti-chart-timeline text-primary"></i> Alur Proses Order
                </h6>
                @php
                    $statusOrder = ['pending','processing','done','delivered'];
                    $timelineMap = [
                        'pending'    => ['label'=>'Menunggu',  'icon'=>'ti-clock',      'color'=>'#fd7e14'],
                        'processing' => ['label'=>'Diproses',  'icon'=>'ti-loader',     'color'=>'#2563eb'],
                        'done'       => ['label'=>'Selesai',   'icon'=>'ti-check',      'color'=>'#198754'],
                        'delivered'  => ['label'=>'Terkirim',  'icon'=>'ti-truck',      'color'=>'#20c997'],
                        'cancelled'  => ['label'=>'Dibatalkan','icon'=>'ti-circle-x',   'color'=>'#dc3545'],
                        'cancel_requested' => ['label'=>'Pengajuan Batal', 'icon'=>'ti-clock-pause', 'color'=>'#6f42c1'],
                    ];
                    $currentIdx  = array_search($transaction->order_status, $statusOrder);
                    $isCancelled = in_array($transaction->order_status, ['cancelled', 'cancel_requested']);
                @endphp
                <div class="d-flex align-items-center" style="overflow-x:auto; padding-bottom:4px;">
                    @foreach($statusOrder as $i => $st)
                    @php
                        $info    = $timelineMap[$st];
                        $isDone  = !$isCancelled && ($i <= $currentIdx);
                        $isCurr  = $transaction->order_status === $st;
                        $color   = $isDone ? $info['color'] : '#dee2e6';
                        $textClr = $isDone ? $info['color'] : '#adb5bd';
                    @endphp
                    <div class="d-flex flex-column align-items-center flex-shrink-0" style="min-width:80px;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 position-relative {{ $isCurr ? 'shadow-sm' : '' }}"
                            style="width:48px;height:48px;background:{{ $isDone ? $info['color'].'20' : '#f8f9fa' }};border:2px solid {{ $color }};transition:all .3s;">
                            <i class="ti {{ $info['icon'] }}" style="color:{{ $textClr }};font-size:1.1rem;"></i>
                            @if($isCurr)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary border border-white" style="font-size:.5rem;width:14px;height:14px;padding:0;display:flex;align-items:center;justify-content:center;">&nbsp;</span>
                            @endif
                        </div>
                        <div class="text-center small" style="font-weight:{{ $isCurr ? '700' : '400' }};color:{{ $textClr }};font-size:.72rem;line-height:1.2;">
                            {{ $info['label'] }}
                        </div>
                    </div>
                    @if($i < count($statusOrder) - 1)
                    <div class="flex-grow-1 mb-5" style="height:2px;background:{{ ($currentIdx !== false && $i < $currentIdx && !$isCancelled) ? 'var(--bs-primary)' : '#dee2e6' }};min-width:16px;transition:background .3s;margin-top:-20px;"></div>
                    @endif
                    @endforeach
                    @if($isCancelled)
                    <div class="ms-2 d-flex flex-column align-items-center flex-shrink-0" style="min-width:80px;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 shadow-sm"
                            style="width:48px;height:48px;background:#dc354520;border:2px solid #dc3545;">
                            <i class="ti ti-circle-x" style="color:#dc3545;font-size:1.1rem;"></i>
                        </div>
                        <div class="text-center fw-bold" style="font-size:.72rem;color:{{ $timelineMap[$transaction->order_status]['color'] }};">
                            {{ $timelineMap[$transaction->order_status]['label'] }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Layanan Dipesan --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div style="height:3px;background:linear-gradient(90deg,var(--bs-primary),#60a5fa);"></div>
            <div class="card-body p-0">
                <div class="px-4 pt-4 pb-3 d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-wash text-primary fs-5"></i>
                    </div>
                    <h6 class="fw-bold mb-0">Layanan Dipesan</h6>
                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary ms-1">{{ $transaction->items->count() }} item</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size:.85rem;">
                        <thead>
                            <tr style="background:linear-gradient(90deg,#f0f4ff,#e8eeff);border-bottom:2px solid #e9ecef;">
                                <th class="py-3 ps-4 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Layanan</th>
                                <th class="py-3 text-center text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Qty</th>
                                <th class="py-3 text-end text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Harga/Unit</th>
                                <th class="py-3 pe-4 text-end text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction->items as $item)
                            <tr style="border-bottom:1px solid #f0f4ff;">
                                <td class="py-3 ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width:34px;height:34px;background:linear-gradient(135deg,#2563eb,#60a5fa);">
                                            <i class="ti ti-hanger text-white" style="font-size:.8rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $item->service_name }}</div>
                                            @if($item->notes)
                                            <div class="text-muted" style="font-size:.72rem;"><i class="ti ti-note me-1"></i>{{ $item->notes }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="badge rounded-pill bg-light text-dark border px-3">{{ $item->quantity }} {{ $item->getTypeLabel() }}</span>
                                </td>
                                <td class="py-3 text-end text-muted">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="py-3 pe-4 text-end fw-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background:#f8faff;">
                            <tr>
                                <td colspan="3" class="py-2 pe-3 text-end text-muted small">Subtotal</td>
                                <td class="py-2 pe-4 text-end fw-medium">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @if($transaction->discount_amount > 0)
                            <tr class="text-danger">
                                <td colspan="3" class="py-2 pe-3 text-end small">
                                    <i class="ti ti-tag me-1"></i>Diskon ({{ $transaction->discount_code }})
                                </td>
                                <td class="py-2 pe-4 text-end fw-medium">– Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if($transaction->tax_amount > 0)
                            <tr class="text-muted">
                                <td colspan="3" class="py-2 pe-3 text-end small">PPN</td>
                                <td class="py-2 pe-4 text-end">Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if($transaction->service_fee > 0)
                            <tr class="text-muted">
                                <td colspan="3" class="py-2 pe-3 text-end small">Biaya Admin</td>
                                <td class="py-2 pe-4 text-end">Rp {{ number_format($transaction->service_fee, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            <tr style="border-top:2px solid #e9ecef;">
                                <td colspan="3" class="py-3 pe-3 text-end fw-bold fs-6">TOTAL</td>
                                <td class="py-3 pe-4 text-end fw-bold fs-5 text-primary">{{ $transaction->formatted_total }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Catatan & Info Tambahan --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="ti ti-notes text-muted"></i> Catatan & Jadwal
                    </h6>
                    @if($transaction->payment_status !== 'paid' || $transaction->order_status !== 'delivered')
                    <button type="button" class="btn btn-sm btn-light rounded-pill px-3 border" id="btnEditToggle">
                        <i class="ti ti-pencil me-1"></i>Edit
                    </button>
                    @endif
                </div>

                {{-- View Mode --}}
                <div id="infoViewMode" class="row g-3">
                    <div class="col-md-6">
                        <div class="rounded-3 p-3" style="background:#f8faff;border:1px solid #e8eeff;">
                            <div class="text-muted small fw-medium mb-1"><i class="ti ti-notes me-1"></i>Catatan Order</div>
                            <div class="fw-medium" id="displayNotes">{{ $transaction->notes ?: '–' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="rounded-3 p-3" style="background:#f8faff;border:1px solid #e8eeff;">
                            <div class="text-muted small fw-medium mb-1"><i class="ti ti-calendar-event me-1"></i>Estimasi Antar/Jemput</div>
                            <div class="fw-medium" id="displayPickup">
                                {{ $transaction->pickup_date ? $transaction->pickup_date->format('d M Y') : '–' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Edit Mode --}}
                <div id="infoEditMode" class="d-none">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Catatan Order</label>
                            <div class="input-group align-items-start">
                                <span class="input-group-text bg-light border-end-0 pt-2" style="align-items:flex-start;"><i class="ti ti-notes text-muted"></i></span>
                                <textarea class="form-control border-start-0" id="editNotes" rows="3"
                                    placeholder="Catatan order...">{{ $transaction->notes }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Estimasi Antar/Jemput</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-calendar-event text-muted"></i></span>
                                <input type="date" class="form-control border-start-0" id="editPickup"
                                    value="{{ $transaction->pickup_date ? $transaction->pickup_date->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold" id="btnSaveInfo"
                            data-url="{{ route('admin.transactions.update-info', $transaction) }}">
                            <span class="save-text"><i class="ti ti-check me-1"></i>Simpan</span>
                            <span class="save-loading d-none"><span class="spinner-border spinner-border-sm"></span></span>
                        </button>
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-3 border" id="btnCancelEdit">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── KANAN ── --}}
    <div class="col-12 col-lg-4">
        <div class="d-flex flex-column gap-3">

            {{-- Pelanggan --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div style="height:3px;background:linear-gradient(90deg,var(--bs-primary),#60a5fa);"></div>
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="ti ti-user text-primary"></i> Pelanggan
                    </h6>
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white fw-bold"
                            style="width:48px;height:48px;font-size:1.2rem;background:linear-gradient(135deg,#2563eb,#60a5fa);">
                            {{ strtoupper(substr($transaction->customer_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:.95rem;">{{ $transaction->customer_name }}</div>
                            @if($transaction->customer_phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $transaction->customer_phone) }}" target="_blank"
                                class="text-muted small text-decoration-none d-flex align-items-center gap-1 mt-1">
                                <i class="ti ti-brand-whatsapp text-success"></i>{{ $transaction->customer_phone }}
                            </a>
                            @endif
                            <div class="mt-2">
                                @if($transaction->customer_id)
                                <span class="badge rounded-pill" style="background:#2563eb15;color:#2563eb;font-size:.7rem;">
                                    <i class="ti ti-user-check me-1"></i>Pelanggan Terdaftar
                                </span>
                                @else
                                <span class="badge rounded-pill" style="background:#fd7e1415;color:#fd7e14;font-size:.7rem;">
                                    <i class="ti ti-walk me-1"></i>Walk-in
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Update Status (admin only) --}}
            @if($transaction->order_status !== 'cancelled' && $transaction->order_status !== 'delivered' && auth()->user()->isAdmin())
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="ti ti-arrows-exchange text-primary"></i> Ubah Status Order
                    </h6>
                    @php
                        $statusFlow = [
                            'pending'    => ['label'=>'Menunggu',  'color'=>'#fd7e14', 'icon'=>'ti-clock'],
                            'processing' => ['label'=>'Diproses',  'color'=>'#2563eb', 'icon'=>'ti-loader'],
                            'done'       => ['label'=>'Selesai',   'color'=>'#198754', 'icon'=>'ti-check'],
                            'delivered'  => ['label'=>'Terkirim',  'color'=>'#20c997', 'icon'=>'ti-truck'],
                            'cancelled'  => ['label'=>'Dibatalkan','color'=>'#dc3545', 'icon'=>'ti-circle-x'],
                        ];
                    @endphp
                    <div class="d-flex flex-column gap-2">
                        @foreach($statusFlow as $status => $info)
                        <button type="button"
                            class="btn btn-sm text-start rounded-3 status-btn d-flex align-items-center gap-2"
                            style="background:{{ $transaction->order_status === $status ? $info['color'].'20' : '#f8f9fa' }};
                                   color:{{ $transaction->order_status === $status ? $info['color'] : '#6c757d' }};
                                   border:1px solid {{ $transaction->order_status === $status ? $info['color'].'40' : '#e9ecef' }};
                                   font-weight:{{ $transaction->order_status === $status ? '700' : '400' }};"
                            data-status="{{ $status }}"
                            data-url="{{ route('admin.transactions.update-status', $transaction) }}"
                            {{ $transaction->order_status === $status ? 'disabled' : '' }}>
                            <i class="ti {{ $info['icon'] }}" style="font-size:.9rem;"></i>
                            {{ $info['label'] }}
                            @if($transaction->order_status === $status)
                            <span class="ms-auto badge rounded-pill text-white" style="background:{{ $info['color'] }};font-size:.6rem;">Saat ini</span>
                            @else
                            <i class="ti ti-chevron-right ms-auto" style="font-size:.8rem;opacity:.4;"></i>
                            @endif
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Informasi Transaksi --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="ti ti-info-circle text-muted"></i> Informasi Transaksi
                    </h6>
                    <div class="d-flex flex-column gap-3" style="font-size:.82rem;">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">No. Invoice</span>
                            <span class="fw-semibold font-monospace text-primary" style="font-size:.75rem;">{{ $transaction->invoice_number }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Tanggal Order</span>
                            <span class="fw-medium">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        @if($transaction->paid_at)
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Tanggal Bayar</span>
                            <span class="fw-medium text-success">{{ $transaction->paid_at->format('d M Y, H:i') }}</span>
                        </div>
                        @endif
                        @if($transaction->pickup_date)
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Estimasi Antar</span>
                            <span class="fw-medium">{{ $transaction->pickup_date->format('d M Y') }}</span>
                        </div>
                        @endif
                        @if($transaction->midtrans_payment_type)
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Tipe Bayar</span>
                            <span class="fw-medium">{{ str_replace('_', ' ', ucfirst($transaction->midtrans_payment_type)) }}</span>
                        </div>
                        @endif
                        @if($transaction->createdBy)
                        <div class="pt-2 mt-1 border-top">
                            <div class="text-muted mb-1">Dibuat oleh</div>
                            <span class="badge rounded-pill fw-medium" style="background:#2563eb10;color:#2563eb;font-size:.75rem;">
                                <i class="ti ti-user me-1"></i>{{ $transaction->createdBy->name }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="ti ti-bolt text-warning"></i> Aksi Cepat
                    </h6>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('admin.transactions.invoice', $transaction) }}" target="_blank"
                            class="btn btn-sm rounded-3 d-flex align-items-center gap-2 fw-medium"
                            style="background:#2563eb10;color:#2563eb;border:1px solid #2563eb25;">
                            <i class="ti ti-printer"></i>Cetak / Preview Invoice
                        </a>
                        @if($transaction->customer_phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $transaction->customer_phone) }}?text={{ urlencode('Halo ' . $transaction->customer_name . ', pesanan laundry Anda (' . $transaction->invoice_number . ') ' . strtolower($transaction->order_status_label) . '. Total: ' . $transaction->formatted_total) }}"
                            target="_blank"
                            class="btn btn-sm rounded-3 d-flex align-items-center gap-2 fw-medium"
                            style="background:#19875410;color:#198754;border:1px solid #19875425;">
                            <i class="ti ti-brand-whatsapp"></i>Kirim Notif WhatsApp
                        </a>
                        @endif
                        <a href="{{ route('admin.transactions.create') }}"
                            class="btn btn-sm rounded-3 d-flex align-items-center gap-2 fw-medium"
                            style="background:#f8f9fa;color:#6c757d;border:1px solid #e9ecef;">
                            <i class="ti ti-plus"></i>Buat Transaksi Baru
                        </a>
                    </div>
                </div>
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
.status-btn { transition: all .15s; }
.status-btn:not([disabled]):hover { transform: translateX(2px); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Pay Now ───────────────────────────────────────────────
    const btnPayNow = document.getElementById('btnPayNow');
    if (btnPayNow) {
        btnPayNow.addEventListener('click', function () {
            const checkUrl   = this.dataset.checkUrl;
            const redirectUrl = this.dataset.redirect;
            snap.pay(this.dataset.snapToken, {
                onSuccess: async () => { await fetch(checkUrl); window.location.href = redirectUrl; },
                onPending: async () => { await fetch(checkUrl); window.location.reload(); },
                onError:   () => Swal.fire('Gagal', 'Pembayaran tidak berhasil.', 'error'),
                onClose:   () => Swal.fire('Info', 'Pembayaran ditutup. Bisa dilanjutkan kapan saja.', 'info'),
            });
        });
    }

    // ── Setujui Pembatalan ────────────────────────────────────
    document.getElementById('btnApproveCancel')?.addEventListener('click', function () {
        Swal.fire({
            title: 'Setujui Pembatalan?',
            html: `Pesanan <b>{{ $transaction->invoice_number }}</b> akan dibatalkan secara permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(this.dataset.url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => window.location.reload());
                    } else {
                        Swal.fire('Gagal', data.error, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Gagal memproses pengajuan.', 'error');
                }
            }
        });
    });

    // ── Cek Pembayaran ────────────────────────────────────────
    document.getElementById('btnRefreshPayment')?.addEventListener('click', async function () {
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mengecek...';
        try {
            const res  = await fetch(this.dataset.url);
            const data = await res.json();
            if (data.status === 'paid') {
                Swal.fire('Berhasil!', data.message, 'success').then(() => window.location.reload());
            } else {
                Swal.fire('Info', data.message, 'info');
            }
        } catch (e) {
            Swal.fire('Error', 'Gagal mengecek status.', 'error');
        }
        this.disabled = false;
        this.innerHTML = '<i class="ti ti-refresh"></i> Cek Bayar';
    });

    // ── Update Status ─────────────────────────────────────────
    document.querySelectorAll('.status-btn:not([disabled])').forEach(btn => {
        btn.addEventListener('click', async function () {
            const label = this.querySelector('span:not(.ms-auto)')?.textContent.trim() || this.textContent.trim().split('\n')[0].trim();
            const confirmed = await Swal.fire({
                title: 'Ubah Status?',
                html: `Status akan diubah ke: <strong>${label}</strong>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Ubah',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            });
            if (!confirmed.isConfirmed) return;

            const res  = await fetch(this.dataset.url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ order_status: this.dataset.status }),
            });
            const data = await res.json();
            if (data.success) {
                Swal.fire({ toast: true, position: 'bottom-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                setTimeout(() => window.location.reload(), 800);
            }
        });
    });

    // ── Inline Edit ───────────────────────────────────────────
    const editToggle = document.getElementById('btnEditToggle');
    document.getElementById('btnEditToggle')?.addEventListener('click', () => {
        document.getElementById('infoViewMode').classList.add('d-none');
        document.getElementById('infoEditMode').classList.remove('d-none');
        editToggle?.classList.add('d-none');
    });

    document.getElementById('btnCancelEdit')?.addEventListener('click', () => {
        document.getElementById('infoViewMode').classList.remove('d-none');
        document.getElementById('infoEditMode').classList.add('d-none');
        editToggle?.classList.remove('d-none');
    });

    document.getElementById('btnSaveInfo')?.addEventListener('click', async function () {
        const notes  = document.getElementById('editNotes').value;
        const pickup = document.getElementById('editPickup').value;

        this.disabled = true;
        this.querySelector('.save-text').classList.add('d-none');
        this.querySelector('.save-loading').classList.remove('d-none');

        try {
            const res  = await fetch(this.dataset.url, {
                method: 'PUT',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ notes, pickup_date: pickup }),
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('displayNotes').textContent  = notes || '–';
                document.getElementById('displayPickup').textContent = pickup
                    ? new Date(pickup + 'T00:00:00').toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })
                    : '–';

                document.getElementById('infoViewMode').classList.remove('d-none');
                document.getElementById('infoEditMode').classList.add('d-none');
                editToggle?.classList.remove('d-none');

                Swal.fire({ toast: true, position: 'bottom-end', icon: 'success', title: 'Informasi berhasil disimpan', showConfirmButton: false, timer: 2000 });
            } else {
                Swal.fire('Error', data.error || 'Gagal menyimpan.', 'error');
            }
        } catch (e) {
            Swal.fire('Error', 'Terjadi kesalahan.', 'error');
        }

        this.disabled = false;
        this.querySelector('.save-text').classList.remove('d-none');
        this.querySelector('.save-loading').classList.add('d-none');
    });

    // ── Delete ────────────────────────────────────────────────
    document.getElementById('btnDelete')?.addEventListener('click', function () {
        Swal.fire({
            title: 'Hapus Transaksi?',
            html: `Invoice <b>{{ $transaction->invoice_number }}</b> akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then(result => { if (result.isConfirmed) document.getElementById('deleteForm').submit(); });
    });
});
</script>
@endpush

@endsection
