<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $transaction->invoice_number }} | {{ $settings['store_name'] ?? 'Laundry' }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; color: #111; background: #fff; max-width: 400px; margin: 0 auto; padding: 20px; }
        .center  { text-align: center; }
        .right   { text-align: right; }
        .bold    { font-weight: 700; }
        .store-name { font-size: 18px; font-weight: 900; letter-spacing: 1px; }
        .tagline { font-size: 10px; color: #666; margin-top: 2px; }
        .divider { border: none; border-top: 1px dashed #999; margin: 8px 0; }
        .divider-solid { border: none; border-top: 2px solid #111; margin: 8px 0; }
        .row     { display: flex; justify-content: space-between; align-items: flex-start; margin: 3px 0; }
        .row .label { flex: 1; color: #555; }
        .row .value { font-weight: 600; text-align: right; margin-left: 8px; }
        .item-name  { font-weight: 600; }
        .item-sub   { color: #666; font-size: 11px; }
        .total-row  { display: flex; justify-content: space-between; font-size: 14px; font-weight: 900; margin: 6px 0; }
        .status-badge { display: inline-block; padding: 2px 8px; border: 1px solid; border-radius: 3px; font-size: 10px; font-weight: 700; }
        .paid   { color: #198754; border-color: #198754; }
        .pending{ color: #fd7e14; border-color: #fd7e14; }
        .failed { color: #dc3545; border-color: #dc3545; }
        .expired{ color: #6c757d; border-color: #6c757d; }
        .footer { text-align: center; font-size: 10px; color: #888; margin-top: 8px; }
        .qr-area { text-align: center; padding: 10px 0 4px; }
        .qr-label { font-size: 9px; color: #aaa; margin-top: 4px; }
        /* Print styles */
        @media print {
            body { padding: 5px; }
            .no-print { display: none !important; }
            @page { margin: 0; size: 80mm auto; }
        }
        /* Screen-only: nice print preview box */
        @media screen {
            body { border: 1px solid #e9ecef; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin-top: 20px; }
        }
    </style>
</head>
<body>

{{-- Print button (screen only) --}}
<div class="no-print" style="text-align:center;margin-bottom:16px;padding-top:4px;">
    <button onclick="window.print()"
        style="background:linear-gradient(135deg,#0d6efd,#0a58ca);color:#fff;border:none;padding:9px 28px;border-radius:20px;cursor:pointer;font-size:13px;font-weight:600;box-shadow:0 2px 8px rgba(13,110,253,.3);">
        🖨️ Cetak Invoice
    </button>
    <a href="{{ route('admin.transactions.show', $transaction) }}"
        style="margin-left:10px;color:#6c757d;text-decoration:none;font-size:13px;">← Kembali</a>
</div>

{{-- Store Header --}}
<div class="center" style="margin-bottom:12px;">
    @if($settings['store_logo'] ?? null)
    <img src="{{ asset('storage/'.$settings['store_logo']) }}" alt="Logo" style="height:40px;object-fit:contain;margin-bottom:6px;"><br>
    @endif
    <div class="store-name">{{ strtoupper($settings['store_name'] ?? 'LAUNDRY') }}</div>
    @if($settings['store_tagline'] ?? null)
    <div class="tagline">{{ $settings['store_tagline'] }}</div>
    @endif
    @if($settings['store_address'] ?? null)
    <div class="tagline">{{ $settings['store_address'] }}</div>
    @endif
    @if($settings['store_phone'] ?? null)
    <div class="tagline">Telp: {{ $settings['store_phone'] }}</div>
    @endif
</div>

<hr class="divider-solid">
<div class="center bold" style="font-size:13px;letter-spacing:2px;">NOTA / INVOICE</div>
<hr class="divider">

{{-- Invoice Info --}}
<div class="row"><span class="label">No. Invoice</span><span class="value" style="font-family:monospace;">{{ $transaction->invoice_number }}</span></div>
<div class="row"><span class="label">Tanggal</span><span class="value">{{ $transaction->created_at->format('d/m/Y H:i') }}</span></div>
<div class="row"><span class="label">Pelanggan</span><span class="value">{{ $transaction->customer_name }}</span></div>
@if($transaction->customer_phone)
<div class="row"><span class="label">No. HP</span><span class="value">{{ $transaction->customer_phone }}</span></div>
@endif
@if($transaction->pickup_date)
<div class="row"><span class="label">Antar/Jemput</span><span class="value">{{ $transaction->pickup_date->format('d/m/Y') }}</span></div>
@endif

<hr class="divider">

{{-- Items --}}
<div style="margin-bottom:4px;" class="bold">LAYANAN:</div>
@foreach($transaction->items as $item)
<div style="margin-bottom:6px;">
    <div class="item-name">{{ $item->service_name }}</div>
    <div class="row item-sub">
        <span>{{ $item->quantity }} {{ $item->getTypeLabel() }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
        <span class="bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
    </div>
    @if($item->notes)
    <div class="item-sub" style="color:#999;">Catatan: {{ $item->notes }}</div>
    @endif
</div>
@endforeach

<hr class="divider">

{{-- Subtotals --}}
<div class="row"><span class="label">Subtotal</span><span class="value">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span></div>

@if($transaction->discount_amount > 0)
<div class="row"><span class="label">Diskon ({{ $transaction->discount_code }})</span><span class="value">– Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span></div>
@endif
@if($transaction->tax_amount > 0)
<div class="row"><span class="label">PPN</span><span class="value">Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span></div>
@endif
@if($transaction->service_fee > 0)
<div class="row"><span class="label">Biaya Admin</span><span class="value">Rp {{ number_format($transaction->service_fee, 0, ',', '.') }}</span></div>
@endif

<hr class="divider-solid">

<div class="total-row">
    <span>TOTAL</span>
    <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
</div>

<hr class="divider">

{{-- Payment info --}}
<div class="row">
    <span class="label">Metode Bayar</span>
    <span class="value">{{ $transaction->payment_method === 'midtrans' ? 'Online/Transfer' : 'Tunai' }}</span>
</div>
@if($transaction->midtrans_payment_type)
<div class="row">
    <span class="label">Via</span>
    <span class="value">{{ str_replace('_', ' ', ucfirst($transaction->midtrans_payment_type)) }}</span>
</div>
@endif
<div class="row">
    <span class="label">Status Bayar</span>
    <span class="value">
        <span class="status-badge {{ $transaction->payment_status }}">{{ strtoupper($transaction->payment_status_label) }}</span>
    </span>
</div>
@if($transaction->paid_at)
<div class="row">
    <span class="label">Waktu Bayar</span>
    <span class="value">{{ $transaction->paid_at->format('d/m/Y H:i') }}</span>
</div>
@endif

@if($transaction->notes)
<hr class="divider">
<div style="font-size:10px;color:#666;"><strong>Catatan:</strong> {{ $transaction->notes }}</div>
@endif

{{-- QR Code --}}
<hr class="divider">
<div class="qr-area">
    <canvas id="qrCanvas"></canvas>
    <div class="qr-label">Scan untuk cek status order</div>
</div>

<hr class="divider">

{{-- Footer --}}
<div class="footer">
    @if($settings['receipt_footer'] ?? null)
    <p>{{ $settings['receipt_footer'] }}</p>
    @else
    <p>Terima kasih atas kepercayaan Anda!</p>
    @endif
    @if($settings['store_whatsapp'] ?? null)
    <p>WA: +{{ $settings['store_whatsapp'] }}</p>
    @endif
    @if($transaction->createdBy)
    <p style="margin-top:6px;">Kasir: {{ $transaction->createdBy->name }}</p>
    @endif
    <p style="margin-top:4px;color:#bbb;">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
</div>

{{-- QR Code Generator --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById('qrCanvas'), {
        text: '{{ route('admin.transactions.show', $transaction) }}',
        width:  90,
        height: 90,
        colorDark:  '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M
    });
</script>

</body>
</html>
