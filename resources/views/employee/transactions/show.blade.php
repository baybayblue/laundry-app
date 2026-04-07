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
                <h1 class="fs-3 mb-0 fw-bold">Detail Transaksi</h1>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-5 d-flex gap-2 justify-content-md-end flex-wrap">
        <a href="{{ route('employee.transactions.index') }}"
            class="btn btn-sm btn-light rounded-pill px-3 d-flex align-items-center gap-2 border shadow-sm">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>
</div>

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

        {{-- Timeline Status --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4 d-flex align-items-center gap-2">
                    <i class="ti ti-chart-timeline text-primary"></i> Alur Proses Order
                </h6>
                @php
                    $statusOrder = ['pending','processing','done','delivered'];
                    $timelineMap = [
                        'pending'    => ['label'=>'Menunggu',  'icon'=>'ti-clock',    'color'=>'#fd7e14'],
                        'processing' => ['label'=>'Diproses',  'icon'=>'ti-loader',   'color'=>'#2563eb'],
                        'done'       => ['label'=>'Selesai',   'icon'=>'ti-check',    'color'=>'#198754'],
                        'delivered'  => ['label'=>'Terkirim',  'icon'=>'ti-truck',    'color'=>'#20c997'],
                        'cancelled'  => ['label'=>'Dibatalkan','icon'=>'ti-circle-x', 'color'=>'#dc3545'],
                    ];
                    $currentIdx  = array_search($transaction->order_status, $statusOrder);
                    $isCancelled = $transaction->order_status === 'cancelled';
                @endphp
                <div class="d-flex align-items-center" style="overflow-x:auto; padding-bottom:4px;">
                    @foreach($statusOrder as $i => $st)
                    @php
                        $info   = $timelineMap[$st];
                        $isDone = !$isCancelled && ($i <= $currentIdx);
                        $isCurr = $transaction->order_status === $st;
                        $color  = $isDone ? $info['color'] : '#dee2e6';
                        $textClr = $isDone ? $info['color'] : '#adb5bd';
                    @endphp
                    <div class="d-flex flex-column align-items-center flex-shrink-0" style="min-width:80px;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mb-2"
                            style="width:48px;height:48px;background:{{ $isDone ? $info['color'].'20' : '#f8f9fa' }};border:2px solid {{ $color }};">
                            <i class="ti {{ $info['icon'] }}" style="color:{{ $textClr }};font-size:1.1rem;"></i>
                        </div>
                        <div class="text-center small" style="font-weight:{{ $isCurr ? '700' : '400' }};color:{{ $textClr }};font-size:.72rem;">
                            {{ $info['label'] }}
                        </div>
                    </div>
                    @if($i < count($statusOrder) - 1)
                    <div class="flex-grow-1 mb-5" style="height:2px;background:{{ ($currentIdx !== false && $i < $currentIdx && !$isCancelled) ? 'var(--bs-primary)' : '#dee2e6' }};min-width:16px;margin-top:-20px;"></div>
                    @endif
                    @endforeach
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
                                <td colspan="3" class="py-2 pe-3 text-end small"><i class="ti ti-tag me-1"></i>Diskon ({{ $transaction->discount_code }})</td>
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
                                    <i class="ti ti-user-check me-1"></i>Terdaftar
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
                        @if($transaction->notes)
                        <div class="pt-2 mt-1 border-top">
                            <div class="text-muted mb-1">Catatan</div>
                            <div class="small">{{ $transaction->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Aksi Cepat --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="ti ti-bolt text-warning"></i> Aksi Cepat
                    </h6>
                    <div class="d-flex flex-column gap-2">
                        @if($transaction->customer_phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $transaction->customer_phone) }}?text={{ urlencode('Halo ' . $transaction->customer_name . ', pesanan laundry Anda (' . $transaction->invoice_number . ') ' . strtolower($transaction->order_status_label) . '. Total: ' . $transaction->formatted_total) }}"
                            target="_blank"
                            class="btn btn-sm rounded-3 d-flex align-items-center gap-2 fw-medium"
                            style="background:#19875410;color:#198754;border:1px solid #19875425;">
                            <i class="ti ti-brand-whatsapp"></i>Kirim Notif WhatsApp
                        </a>
                        @endif
                        <a href="{{ route('employee.transactions.create') }}"
                            class="btn btn-sm rounded-3 d-flex align-items-center gap-2 fw-medium"
                            style="background:#f8f9fa;color:#6c757d;border:1px solid #e9ecef;">
                            <i class="ti ti-plus"></i>Tambah Transaksi Baru
                        </a>
                        <a href="{{ route('employee.transactions.index') }}"
                            class="btn btn-sm rounded-3 d-flex align-items-center gap-2 fw-medium"
                            style="background:#2563eb10;color:#2563eb;border:1px solid #2563eb25;">
                            <i class="ti ti-list"></i>Data Laundry Saya
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
