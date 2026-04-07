<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan – {{ $startDate->translatedFormat('d F Y') }} s/d {{ $endDate->translatedFormat('d F Y') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #1f2937; background: #fff; }

        .print-header { padding: 24px; background: linear-gradient(135deg, #16a34a, #4ade80); color: white; }
        .print-header h1 { font-size: 20px; font-weight: 700; margin-bottom: 2px; }
        .print-header p { font-size: 11px; opacity: .85; }

        .section { padding: 20px 24px; }
        .section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #6b7280; margin-bottom: 12px; border-bottom: 2px solid #e5e7eb; padding-bottom: 6px; }

        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        .stat-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px; }
        .stat-box .label { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
        .stat-box .value { font-size: 14px; font-weight: 700; }
        .stat-box.green .value { color: #16a34a; }
        .stat-box.blue .value { color: #2563eb; }
        .stat-box.red .value { color: #dc2626; }
        .stat-box.orange .value { color: #d97706; }

        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th { background: #f3f4f6; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; padding: 8px 10px; text-align: left; border-bottom: 2px solid #e5e7eb; }
        td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
        tr:nth-child(even) td { background: #fafafa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-danger  { background: #fee2e2; color: #dc2626; }
        .badge-secondary { background: #f3f4f6; color: #6b7280; }

        tfoot td { font-weight: 700; background: #f9fafb !important; border-top: 2px solid #e5e7eb; }

        .print-footer { padding: 12px 24px; background: #f9fafb; border-top: 1px solid #e5e7eb; font-size: 10px; color: #9ca3af; text-align: center; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

<div class="no-print" style="background:#1f2937;padding:10px 24px;display:flex;align-items:center;gap:12px;">
    <button onclick="window.print()" style="background:#16a34a;color:white;border:none;padding:8px 20px;border-radius:20px;cursor:pointer;font-size:12px;font-weight:600;">
        🖨️ Cetak Laporan
    </button>
    <a href="{{ route('admin.laporan-keuangan.index', request()->query()) }}" style="color:#9ca3af;font-size:11px;text-decoration:none;">
        ← Kembali ke Laporan
    </a>
</div>

<div class="print-header">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
        <div>
            <h1>📊 Laporan Keuangan</h1>
            <p>Periode: {{ $startDate->translatedFormat('d F Y') }} – {{ $endDate->translatedFormat('d F Y') }}</p>
        </div>
        <div style="text-align:right; font-size:10px; opacity:.8;">
            <div>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</div>
            <div>Laundry Management System</div>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-title">Ringkasan</div>
    <div class="stats-grid">
        <div class="stat-box green">
            <div class="label">Total Pendapatan</div>
            <div class="value">Rp {{ number_format($summary->total_pendapatan ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="stat-box blue">
            <div class="label">Total Transaksi Lunas</div>
            <div class="value">{{ $summary->transaksi_lunas ?? 0 }} dari {{ $summary->total_transaksi ?? 0 }}</div>
        </div>
        <div class="stat-box orange">
            <div class="label">Total Diskon</div>
            <div class="value">Rp {{ number_format($summary->total_diskon ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="stat-box orange">
            <div class="label">Total Pajak (PPN)</div>
            <div class="value">Rp {{ number_format($summary->total_pajak ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-title">Detail Transaksi Lunas ({{ $transactions->count() }} transaksi)</div>
    <table>
        <thead>
            <tr>
                <th style="width:140px;">No. Invoice</th>
                <th style="width:90px;">Tanggal</th>
                <th>Pelanggan</th>
                <th>Layanan</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right">Diskon</th>
                <th class="text-right">Pajak</th>
                <th class="text-right">Total</th>
                <th class="text-center">Metode</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
            <tr>
                <td style="font-family:monospace; font-size:10px; color:#2563eb;">{{ $t->invoice_number }}</td>
                <td style="font-size:10px;">{{ $t->created_at->format('d/m/Y') }}</td>
                <td>
                    <div style="font-weight:600;">{{ $t->customer_name }}</div>
                    @if($t->customer_phone)
                    <div style="font-size:10px;color:#9ca3af;">{{ $t->customer_phone }}</div>
                    @endif
                </td>
                <td style="font-size:10px;max-width:150px;">{{ $t->items->pluck('service_name')->join(', ') }}</td>
                <td class="text-right">{{ number_format($t->subtotal, 0, ',', '.') }}</td>
                <td class="text-right" style="color:#d97706;">{{ $t->discount_amount > 0 ? '– '.number_format($t->discount_amount, 0, ',', '.') : '-' }}</td>
                <td class="text-right" style="color:#0891b2;">{{ $t->tax_amount > 0 ? number_format($t->tax_amount, 0, ',', '.') : '-' }}</td>
                <td class="text-right" style="font-weight:700;color:#16a34a;">{{ number_format($t->total_amount, 0, ',', '.') }}</td>
                <td class="text-center">
                    <span class="badge {{ $t->payment_method === 'cash' ? 'badge-success' : 'badge-secondary' }}">
                        {{ $t->payment_method === 'cash' ? 'Tunai' : 'Online' }}
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge badge-success">Lunas</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center" style="padding:20px;color:#9ca3af;">Tidak ada data transaksi lunas</td>
            </tr>
            @endforelse
        </tbody>
        @if($transactions->isNotEmpty())
        <tfoot>
            <tr>
                <td colspan="4" style="font-weight:700;">TOTAL KESELURUHAN</td>
                <td class="text-right">{{ number_format($summary->total_subtotal ?? 0, 0, ',', '.') }}</td>
                <td class="text-right" style="color:#d97706;">{{ number_format($summary->total_diskon ?? 0, 0, ',', '.') }}</td>
                <td class="text-right" style="color:#0891b2;">{{ number_format($summary->total_pajak ?? 0, 0, ',', '.') }}</td>
                <td class="text-right" style="color:#16a34a;">{{ number_format($summary->total_pendapatan ?? 0, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

<div class="print-footer">
    Laporan ini digenerate otomatis oleh sistem • {{ now()->translatedFormat('d F Y, H:i') }} WIB
</div>

</body>
</html>
