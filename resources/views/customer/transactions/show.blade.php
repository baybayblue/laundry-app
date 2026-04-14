@extends('customer.layouts.app')

@section('title', 'Detail Transaksi ' . $transaction->invoice_number)

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-12 col-md-6">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="font-size: .75rem;">
                <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customer.transactions.index') }}" class="text-decoration-none">Transaksi</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $transaction->invoice_number }}</li>
            </ol>
        </nav>
        <h1 class="fs-4 mb-2 fw-bold text-dark">Detail Transaksi</h1>
        <p class="text-muted mb-0 small">
            Rincian pesanan dan status pemrosesan laundry Anda.
        </p>
    </div>
    <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        @if($transaction->payment_method === 'midtrans' && $transaction->payment_status !== 'paid' && $transaction->total_amount > 0)
            <button type="button" id="btnRefreshPayment"
                class="btn btn-outline-primary rounded-pill px-4 shadow-sm border small fw-bold"
                data-url="{{ route('customer.transactions.check-payment', $transaction) }}">
                <i class="ti ti-refresh me-1"></i> Cek Pembayaran
            </button>
        @endif
        <a href="{{ route('customer.transactions.invoice', $transaction) }}" target="_blank" class="btn btn-primary rounded-pill shadow-sm px-4 fw-bold small">
            <i class="ti ti-printer me-2 fs-5"></i> Cetak Invoice
        </a>
    </div>
</div>

<div class="row g-4 pb-5">
    <!-- LEFT COLUMN: Timeline & Items -->
    <div class="col-lg-8">
        <!-- STATUS TIMELINE -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center gap-2">
                <i class="ti ti-timeline text-primary fs-5"></i>
                <h6 class="fw-bold mb-0">Status Pemrosesan</h6>
            </div>
            <div class="card-body px-4 py-4 pt-2">
                @php
                    $statusSteps = ['pending', 'processing', 'done', 'delivered'];
                    $statusInfo = [
                        'pending'          => ['label' => 'Menunggu',       'icon' => 'ti-hourglass',      'color' => '#f59e0b'],
                        'processing'       => ['label' => 'Diproses',       'icon' => 'ti-loader-2',       'color' => '#0d6efd'],
                        'done'             => ['label' => 'Selesai',        'icon' => 'ti-circle-check',   'color' => '#198754'],
                        'delivered'        => ['label' => 'Terkirim',       'icon' => 'ti-truck-delivery', 'color' => '#06b6d4'],
                        'cancelled'        => ['label' => 'Dibatalkan',     'icon' => 'ti-circle-x',      'color' => '#dc3545'],
                        'cancel_requested' => ['label' => 'Batal Diajukan', 'icon' => 'ti-clock-pause',    'color' => '#6f42c1'],
                    ];
                    $currentIndex = array_search($transaction->order_status, $statusSteps);
                    if (in_array($transaction->order_status, ['cancelled', 'cancel_requested'])) $currentIndex = -1;
                @endphp

                <div class="d-flex align-items-center flex-nowrap overflow-auto py-3 px-1" style="scrollbar-width: none;">
                    @foreach($statusSteps as $i => $step)
                        @php
                            $info = $statusInfo[$step];
                            $isCompleted = $currentIndex !== -1 && $i <= $currentIndex;
                            $isActive = $transaction->order_status === $step;
                        @endphp
                        <div class="d-flex flex-column align-items-center text-center flex-shrink-0" style="min-width: 100px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 position-relative"
                                 style="width: 44px; height: 44px; 
                                        background: {{ $isCompleted ? $info['color'].'15' : 'transparent' }}; 
                                        border: 2px solid {{ $isCompleted ? $info['color'] : '#e9ecef' }}; 
                                        z-index: 2; transition: all 0.3s;
                                        box-shadow: {{ $isActive ? '0 0 10px '.$info['color'].'40' : 'none' }};">
                                <i class="ti {{ $info['icon'] }} {{ $isActive ? 'animate-pulse' : '' }}" 
                                   style="color: {{ $isCompleted ? $info['color'] : '#adb5bd' }}; font-size: 1.1rem;"></i>
                            </div>
                            <span class="small {{ $isActive ? 'fw-bold text-dark' : 'text-muted' }}" style="font-size: 0.68rem;">{{ $info['label'] }}</span>
                        </div>
                        @if($i < count($statusSteps) - 1)
                            <div class="flex-grow-1 border-top mt-n4 align-middle" style="height: 1px; min-width: 30px; border-top: 2px dashed {{ ($currentIndex !== -1 && $i < $currentIndex) ? $statusInfo[$statusSteps[$i+1]]['color'] : '#e9ecef' }} !important; margin-bottom: 1.5rem; opacity: 0.5;"></div>
                        @endif
                    @endforeach
                    
                    @if(in_array($transaction->order_status, ['cancelled', 'cancel_requested']))
                        <div class="flex-grow-1 border-top mt-n4 align-middle" style="height: 1px; min-width: 30px; border-top: 2px dashed #e9ecef !important; margin-bottom: 1.5rem; opacity: 0.5;"></div>
                        <div class="d-flex flex-column align-items-center text-center flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-2"
                                 style="width: 44px; height: 44px; background: {{ $statusInfo[$transaction->order_status]['color'] }}15; border: 2px solid {{ $statusInfo[$transaction->order_status]['color'] }};">
                                <i class="ti {{ $statusInfo[$transaction->order_status]['icon'] }}" style="color: {{ $statusInfo[$transaction->order_status]['color'] }}; font-size: 1.1rem;"></i>
                            </div>
                            <span class="small fw-bold" style="color: {{ $statusInfo[$transaction->order_status]['color'] }}; font-size: 0.68rem;">{{ $statusInfo[$transaction->order_status]['label'] }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ITEMS DETAIL -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-basket text-primary fs-5"></i>
                    <h6 class="fw-bold mb-0">Rincian Cucian</h6>
                </div>
                <span class="badge bg-light text-muted rounded-pill px-3">{{ $transaction->items->count() }} Item</span>
            </div>
            <div class="card-body p-0">
                @if($transaction->items->count() > 0)
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="small text-muted fw-bold" style="font-size: .65rem; text-transform: uppercase;">
                                <th class="py-3 ps-4">Layanan</th>
                                <th class="py-3 text-center">Jumlah</th>
                                <th class="py-3 text-end pe-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction->items as $item)
                            <tr>
                                <td class="py-3 ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 bg-light p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                            <i class="ti ti-hanger text-primary fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark mb-0 small">{{ $item->service_name }}</div>
                                            @if($item->notes)
                                                <div class="text-muted extra-small"><i class="ti ti-note me-1"></i>{{ $item->notes }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="badge bg-white text-dark border px-2 py-1 rounded-pill fw-medium small">
                                        {{ floatval($item->quantity) }} {{ $item->getTypeLabel() }}
                                    </span>
                                </td>
                                <td class="py-3 text-end pe-4 fw-bold small text-dark">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light bg-opacity-25">
                            @php
                                $rows = [
                                    ['label' => 'Subtotal', 'val' => $transaction->subtotal, 'class' => 'text-muted'],
                                    ['label' => 'Diskon ' . ($transaction->discount_code ? "($transaction->discount_code)" : ""), 'val' => $transaction->discount_amount, 'class' => 'text-danger', 'neg' => true],
                                    ['label' => 'PPN', 'val' => $transaction->tax_amount, 'class' => 'text-muted'],
                                    ['label' => 'Biaya Layanan', 'val' => $transaction->service_fee, 'class' => 'text-muted'],
                                ];
                            @endphp
                            @foreach($rows as $row)
                                @if($row['val'] > 0 || (isset($row['neg']) && $row['val'] > 0))
                                <tr>
                                    <td colspan="2" class="py-2 text-end small {{ $row['class'] }}">{{ $row['label'] }}</td>
                                    <td class="py-2 text-end pe-4 small {{ $row['class'] }}">
                                        {{ isset($row['neg']) ? '-' : '' }} Rp {{ number_format($row['val'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                            <tr class="border-top">
                                <td colspan="2" class="py-3 text-end fw-bold text-dark fs-6">TOTAL TAGIHAN</td>
                                <td class="py-3 text-end pe-4 fw-bold text-primary fs-6">{{ $transaction->formatted_total }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="p-5 text-center text-muted py-5">
                    <i class="ti ti-package fs-1 opacity-25 mb-3 d-block"></i>
                    <p class="mb-0 small px-5">Pesanan baru saja diajukan. Kami akan melengkapi rincian layanan segera setelah cucian dijemput dan dicek.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- CANCEL REQUEST -->
        @if(in_array($transaction->order_status, ['pending', 'processing']))
        <div class="card border-0 shadow-sm rounded-4 border-start border-danger border-4 mb-4">
            <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h6 class="fw-bold text-dark mb-1">Hapus atau Batalkan Pesanan?</h6>
                    <p class="text-muted small mb-0">Jika Anda salah input atau ingin membatalkan, silakan ajukan di sini. Persetujuan admin diperlukan.</p>
                </div>
                <button class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-bold" id="btnRequestCancel" data-url="{{ route('customer.transactions.request-cancel', $transaction) }}">
                    <i class="ti ti-trash me-1"></i> Ajukan Hapus / Batal
                </button>
            </div>
        </div>
        @endif
    </div>

    <!-- RIGHT COLUMN -->
    <div class="col-lg-4">
        <!-- PAYMENT ACTION -->
        @if($transaction->payment_status !== 'paid' && $transaction->order_status !== 'cancelled')
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white overflow-hidden">
            <div class="card-body p-4 text-center position-relative">
                <div class="mb-3 position-relative z-1">
                    <div class="rounded-circle bg-white text-primary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="ti ti-credit-card fs-3"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-2 position-relative z-1 text-white">Selesaikan Bayar</h5>
                
                @if($transaction->total_amount <= 0)
                    <p class="small opacity-75 mb-4 position-relative z-1 text-white lh-sm">Total tagihan belum muncul. Silakan tunggu update dari tim laundry kami.</p>
                    <button type="button" class="btn btn-white w-100 fw-bold border-0 shadow-sm text-primary py-2 rounded-pill position-relative z-1" 
                            onclick="Swal.fire('Info', 'Mohon tunggu admin menghitung total tagihan Anda sebelum melakukan pembayaran.', 'info')">
                        <i class="ti ti-wallet me-2"></i> Bayar Sekarang
                    </button>
                @else
                    <p class="small opacity-75 mb-4 position-relative z-1 text-white lh-sm">Pastikan Anda membayar tepat waktu menggunakan metode pilihan Anda.</p>
                    <button type="button" class="btn btn-white w-100 fw-bold border-0 shadow-sm text-primary py-2 rounded-pill position-relative z-1" id="btnPayNow" 
                            data-url="{{ route('customer.transactions.snap-token', $transaction) }}">
                        <i class="ti ti-wallet me-2"></i> Bayar Sekarang
                    </button>
                @endif
                <div class="position-absolute top-0 end-0 opacity-10 mt-n3 me-n3">
                    <i class="ti ti-wallet" style="font-size: 8rem;"></i>
                </div>
            </div>
        </div>
        @endif

        <!-- INFO CARD -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4 d-flex align-items-center gap-2 text-dark">
                    <i class="ti ti-info-circle text-muted"></i> Info Transaksi
                </h6>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Pembayaran</span>
                        <span class="badge bg-opacity-10 px-3 py-1 rounded-pill" 
                              style="background:{{ $transaction->payment_status_color }}18;color:{{ $transaction->payment_status_color }}; text-transform: uppercase; font-size: 0.6rem;">
                            {{ $transaction->payment_status_label }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Invoice</span>
                        <span class="fw-bold text-dark small">{{ $transaction->invoice_number }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Alamat Jemput</span>
                        <span class="text-dark small text-end fw-medium" style="max-width: 150px;">{{ $transaction->pickup_date ? $transaction->pickup_date->translatedFormat('d M Y') : 'Segera' }}</span>
                    </div>
                    <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Metode Bayar</span>
                        <span class="fw-bold text-dark small">{{ $transaction->payment_method_label }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- NOTES -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2 text-dark">
                    <i class="ti ti-notes text-muted"></i> Catatan Anda
                </h6>
                <div class="p-3 bg-light rounded-3 small text-muted lh-base">
                    {{ $transaction->notes ?: 'Anda tidak menyertakan catatan khusus pada pesanan ini.' }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-trigger Snap if redirected from creation
        @if(session('trigger_snap'))
            const autoSnapBtn = document.getElementById('btnPayNow');
            if (autoSnapBtn && !autoSnapBtn.disabled) {
                setTimeout(() => autoSnapBtn.click(), 1000);
            }
        @endif

        const btnPayNow = document.getElementById('btnPayNow');
        if (btnPayNow) {
            btnPayNow.addEventListener('click', async function() {
                const endpoint = this.dataset.url;
                const originalHtml = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>...';
                try {
                    const res = await fetch(endpoint);
                    const data = await res.json();
                    if (data.snap_token) {
                        snap.pay(data.snap_token, {
                            onSuccess: () => window.location.reload(),
                            onPending: () => window.location.reload(),
                            onError: () => Swal.fire('Gagal', 'Pembayaran gagal.', 'error'),
                            onClose: () => { this.disabled = false; this.innerHTML = originalHtml; }
                        });
                    } else { throw new Error(data.error || 'Token gagal dibuat.'); }
                } catch (e) { Swal.fire('Error', e.message, 'error'); this.disabled = false; this.innerHTML = originalHtml; }
            });
        }

        const btnCancel = document.getElementById('btnRequestCancel');
        if (btnCancel) {
            btnCancel.addEventListener('click', function() {
                Swal.fire({
                    title: 'Ajukan Pembatalan?',
                    text: 'Admin akan mengecek permintaan Anda.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ya, Ajukan!',
                    cancelButtonText: 'Batal'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        const response = await fetch(this.dataset.url, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                        });
                        const data = await response.json();
                        if (data.success) { Swal.fire('Berhasil!', data.message, 'success').then(() => window.location.reload()); }
                        else { Swal.fire('Gagal', data.error, 'error'); }
                    }
                });
            });
        }

        const btnRefresh = document.getElementById('btnRefreshPayment');
        if (btnRefresh) {
            btnRefresh.addEventListener('click', async function() {
                this.disabled = true;
                const orig = this.innerHTML;
                this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                try {
                    const res = await fetch(this.dataset.url);
                    const data = await res.json();
                    if (data.status === 'paid') { Swal.fire('Berhasil!', 'Pembayaran diterima.', 'success').then(() => window.location.reload()); }
                    else { Swal.fire('Status: ' + data.status, data.message, 'info'); }
                } catch (e) { Swal.fire('Error', 'Gagal cek status.', 'error'); }
                this.disabled = false;
                this.innerHTML = orig;
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
    .animate-pulse { animation: pulse 2s infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: .7; transform: scale(1.05); } }
    .extra-small { font-size: 0.65rem; }
</style>
@endpush
@endsection

