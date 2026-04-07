@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                style="background:linear-gradient(135deg,{{ $settings['brand_color'] ?? '#6f42c1' }}20,{{ $settings['brand_color'] ?? '#6f42c1' }}10);
                       border:1px solid {{ $settings['brand_color'] ?? '#6f42c1' }}30; width:52px;height:52px;">
                <i class="ti ti-building-store fs-3" style="color:{{ $settings['brand_color'] ?? '#6f42c1' }};"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Pengaturan Toko</h1>
                <p class="mb-0 text-muted small">Profil, kontak, jam operasional & konfigurasi transaksi</p>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2" style="background:#19875415;">
    <i class="ti ti-circle-check text-success fs-5"></i>
    <span class="small fw-medium text-success">{{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div class="alert border-0 rounded-4 shadow-sm mb-4 d-flex align-items-start gap-3" style="background:#dc354510;">
    <i class="ti ti-alert-circle text-danger fs-4 mt-1 flex-shrink-0"></i>
    <div>
        <div class="fw-semibold text-danger mb-1">Terdapat kesalahan:</div>
        <ul class="mb-0 small text-danger ps-3">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
@csrf

<div class="row g-4">
    {{-- LEFT: TAB NAV --}}
    <div class="col-12 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top:80px;">
            <div class="card-body p-3">
                <nav class="nav flex-column gap-1" id="settingsTabs" role="tablist">
                    @php
                        $tabs = [
                            ['id'=>'profil',       'icon'=>'ti-building-store', 'label'=>'Profil Toko'],
                            ['id'=>'kontak',       'icon'=>'ti-phone',          'label'=>'Kontak & Lokasi'],
                            ['id'=>'operasional',  'icon'=>'ti-clock',          'label'=>'Jam Operasional'],
                            ['id'=>'transaksi',    'icon'=>'ti-receipt',         'label'=>'Transaksi & Pajak'],
                            ['id'=>'tampilan',     'icon'=>'ti-palette',         'label'=>'Tampilan'],
                        ];
                    @endphp
                    @php
                        $bc = $settings['brand_color'] ?? '#6f42c1';
                        $ac = $settings['accent_color'] ?? '#8b5cf6';
                        $activeTabStyle = "background:linear-gradient(135deg,{$bc},{$ac});";
                    @endphp
                    @foreach($tabs as $i => $tab)
                    <a class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 {{ $i===0 ? 'active text-white' : 'text-muted' }}"
                        id="tab-{{ $tab['id'] }}" href="#panel-{{ $tab['id'] }}"
                        role="tab" data-tab="{{ $tab['id'] }}"
                        style="{{ $i===0 ? $activeTabStyle : '' }} transition:all .2s; font-size:.85rem;">
                        <i class="ti {{ $tab['icon'] }} fs-6"></i>
                        <span>{{ $tab['label'] }}</span>
                    </a>
                    @endforeach
                </nav>
            </div>
        </div>
    </div>

    {{-- RIGHT: PANELS --}}
    <div class="col-12 col-lg-9">

        {{-- ── PROFIL ─────────────────────────────────────────── --}}
        <div id="panel-profil" class="settings-panel">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="rounded-top-4" style="height:4px;background:linear-gradient(90deg,{{ $settings['brand_color']??'#6f42c1' }},{{ $settings['accent_color']??'#8b5cf6' }});"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div class="rounded-2 p-1" style="background:{{ $settings['brand_color']??'#6f42c1' }}18;">
                            <i class="ti ti-building-store fs-5" style="color:{{ $settings['brand_color']??'#6f42c1' }};"></i>
                        </div>
                        <h5 class="mb-0 fw-semibold">Profil Toko</h5>
                    </div>

                    {{-- Logo --}}
                    <div class="mb-4">
                        <label class="form-label fw-medium small">Logo Toko</label>
                        <div class="d-flex align-items-center gap-4">
                            <div class="rounded-3 overflow-hidden d-flex align-items-center justify-content-center flex-shrink-0"
                                id="logoPreview"
                                style="width:80px;height:80px;background:#f8f9fa;border:2px dashed #dee2e6;">
                                @if($settings['store_logo'])
                                    <img src="{{ asset('storage/'.$settings['store_logo']) }}" alt="Logo" class="img-fluid" style="object-fit:contain;width:100%;height:100%;">
                                @else
                                    <i class="ti ti-photo fs-2 text-muted"></i>
                                @endif
                            </div>
                            <div>
                                <input type="file" class="form-control form-control-sm" id="store_logo" name="store_logo"
                                    accept="image/*" style="max-width:260px;">
                                <div class="text-muted mt-1" style="font-size:.72rem;">JPG, PNG, WebP, SVG. Maks. 2MB.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="store_name">
                                Nama Toko <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="store_name" name="store_name"
                                value="{{ old('store_name', $settings['store_name']??'') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="store_tagline">Tagline</label>
                            <input type="text" class="form-control" id="store_tagline" name="store_tagline"
                                value="{{ old('store_tagline', $settings['store_tagline']??'') }}"
                                placeholder="Contoh: Bersih, Wangi, Tepat Waktu">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium small" for="store_description">Deskripsi Singkat</label>
                            <textarea class="form-control" id="store_description" name="store_description" rows="3"
                                placeholder="Ceritakan sedikit tentang usaha laundry Anda...">{{ old('store_description', $settings['store_description']??'') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── KONTAK ──────────────────────────────────────────── --}}
        <div id="panel-kontak" class="settings-panel d-none">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="rounded-top-4" style="height:4px;background:linear-gradient(90deg,#0d6efd,#0dcaf0);"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div class="rounded-2 p-1 bg-primary bg-opacity-10">
                            <i class="ti ti-phone text-primary fs-5"></i>
                        </div>
                        <h5 class="mb-0 fw-semibold">Kontak & Lokasi</h5>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="store_phone">Nomor Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="ti ti-phone text-muted"></i></span>
                                <input type="text" class="form-control" id="store_phone" name="store_phone"
                                    value="{{ old('store_phone', $settings['store_phone']??'') }}"
                                    placeholder="0812-XXXX-XXXX">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="store_whatsapp">Nomor WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light" style="background:#25d36615!important;border-color:#25d36640!important;">
                                    <i class="ti ti-brand-whatsapp" style="color:#25d366;"></i>
                                </span>
                                <input type="text" class="form-control" id="store_whatsapp" name="store_whatsapp"
                                    value="{{ old('store_whatsapp', $settings['store_whatsapp']??'') }}"
                                    placeholder="628XXXXXXXXX (format internasional)">
                            </div>
                            <div class="text-muted mt-1" style="font-size:.72rem;">Gunakan format internasional tanpa + (misal: 628123456789)</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium small" for="store_email">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="ti ti-mail text-muted"></i></span>
                                <input type="email" class="form-control" id="store_email" name="store_email"
                                    value="{{ old('store_email', $settings['store_email']??'') }}"
                                    placeholder="email@laundrytoko.com">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium small" for="store_address">Alamat</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light align-items-start pt-2"><i class="ti ti-map-pin text-muted"></i></span>
                                <textarea class="form-control" id="store_address" name="store_address" rows="2"
                                    placeholder="Jl. Nama Jalan, RT/RW, Kelurahan, Kecamatan...">{{ old('store_address', $settings['store_address']??'') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="store_city">Kota/Kabupaten</label>
                            <input type="text" class="form-control" id="store_city" name="store_city"
                                value="{{ old('store_city', $settings['store_city']??'') }}"
                                placeholder="Jakarta Selatan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="store_maps_url">Link Google Maps</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="ti ti-map text-muted"></i></span>
                                <input type="url" class="form-control" id="store_maps_url" name="store_maps_url"
                                    value="{{ old('store_maps_url', $settings['store_maps_url']??'') }}"
                                    placeholder="https://maps.google.com/...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="store_instagram">Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="ti ti-brand-instagram text-muted"></i></span>
                                <input type="text" class="form-control" id="store_instagram" name="store_instagram"
                                    value="{{ old('store_instagram', $settings['store_instagram']??'') }}"
                                    placeholder="@namalaundry">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="store_facebook">Facebook</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="ti ti-brand-facebook text-muted"></i></span>
                                <input type="text" class="form-control" id="store_facebook" name="store_facebook"
                                    value="{{ old('store_facebook', $settings['store_facebook']??'') }}"
                                    placeholder="nama-laundry">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── OPERASIONAL ─────────────────────────────────────── --}}
        <div id="panel-operasional" class="settings-panel d-none">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="rounded-top-4" style="height:4px;background:linear-gradient(90deg,#198754,#20c997);"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-2 p-1 bg-success bg-opacity-10">
                            <i class="ti ti-clock text-success fs-5"></i>
                        </div>
                        <h5 class="mb-0 fw-semibold">Jam Operasional</h5>
                    </div>
                    <p class="text-muted small mb-4">Isi format <code>HH:MM-HH:MM</code> (misal: <code>07:00-21:00</code>), atau isi <code>Tutup</code> jika libur.</p>

                    @php
                        $days = [
                            'op_monday'    => 'Senin',
                            'op_tuesday'   => 'Selasa',
                            'op_wednesday' => 'Rabu',
                            'op_thursday'  => 'Kamis',
                            'op_friday'    => 'Jumat',
                            'op_saturday'  => 'Sabtu',
                            'op_sunday'    => 'Minggu',
                        ];
                    @endphp
                    <div class="d-flex flex-column gap-2">
                        @foreach($days as $key => $dayLabel)
                        @php $val = old($key, $settings[$key] ?? '07:00-21:00'); @endphp
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3"
                            style="background:{{ $val==='Tutup' ? '#dc354508' : '#19875408' }};
                                   border:1px solid {{ $val==='Tutup' ? '#dc354530' : '#19875430' }};"
                            id="row-{{ $key }}">
                            <div style="width:90px;" class="fw-medium small flex-shrink-0">{{ $dayLabel }}</div>
                            <div class="form-check form-switch mb-0 flex-shrink-0">
                                <input class="form-check-input day-toggle" type="checkbox"
                                    id="toggle-{{ $key }}" data-target="{{ $key }}"
                                    {{ $val !== 'Tutup' ? 'checked' : '' }}>
                            </div>
                            <div class="flex-fill">
                                @if($val === 'Tutup')
                                <input type="hidden" name="{{ $key }}" id="{{ $key }}" value="Tutup">
                                <span class="badge rounded-pill text-danger" style="background:#dc354518;font-size:.75rem;">
                                    <i class="ti ti-x me-1"></i>Tutup
                                </span>
                                @else
                                <div class="d-flex align-items-center gap-2">
                                    <input type="time" class="form-control form-control-sm" name="_{{ $key }}_open"
                                        id="{{ $key }}_open" value="{{ explode('-', $val)[0] ?? '07:00' }}"
                                        style="max-width:110px;">
                                    <span class="text-muted small">–</span>
                                    <input type="time" class="form-control form-control-sm" name="_{{ $key }}_close"
                                        id="{{ $key }}_close" value="{{ explode('-', $val)[1] ?? '21:00' }}"
                                        style="max-width:110px;">
                                    <input type="hidden" name="{{ $key }}" id="{{ $key }}" value="{{ $val }}">
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TRANSAKSI ───────────────────────────────────────── --}}
        <div id="panel-transaksi" class="settings-panel d-none">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="rounded-top-4" style="height:4px;background:linear-gradient(90deg,#fd7e14,#ffc107);"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div class="rounded-2 p-1" style="background:#fd7e1415;">
                            <i class="ti ti-receipt fs-5" style="color:#fd7e14;"></i>
                        </div>
                        <h5 class="mb-0 fw-semibold">Transaksi & Pajak</h5>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3"
                                style="background:#f8f9fa;border:1px solid #e9ecef;">
                                <div>
                                    <div class="fw-medium small mb-0">Aktifkan Pajak (PPN)</div>
                                    <div class="text-muted" style="font-size:.72rem;">Pajak akan ditambahkan otomatis ke total transaksi</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="tax_enabled" name="tax_enabled" value="1"
                                        {{ ($settings['tax_enabled']??'0') === '1' ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium small" for="tax_percent">Persentase Pajak (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="tax_percent" name="tax_percent"
                                    value="{{ old('tax_percent', $settings['tax_percent']??'11') }}"
                                    min="0" max="100" step="0.5">
                                <span class="input-group-text bg-light fw-medium">%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium small" for="service_fee">Biaya Admin/Layanan (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-medium">Rp</span>
                                <input type="number" class="form-control" id="service_fee" name="service_fee"
                                    value="{{ old('service_fee', $settings['service_fee']??'0') }}"
                                    min="0" step="500">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium small" for="currency_symbol">Simbol Mata Uang</label>
                            <input type="text" class="form-control" id="currency_symbol" name="currency_symbol"
                                value="{{ old('currency_symbol', $settings['currency_symbol']??'Rp') }}"
                                maxlength="5">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium small" for="receipt_footer">Footer Struk/Nota</label>
                            <textarea class="form-control" id="receipt_footer" name="receipt_footer" rows="2"
                                placeholder="Teks pada bagian bawah struk pembayaran...">{{ old('receipt_footer', $settings['receipt_footer']??'') }}</textarea>
                        </div>
                    </div>

                    {{-- Preview struk mini --}}
                    <div class="mt-4 p-3 rounded-3 border" style="background:#fff;font-family:monospace;font-size:.78rem;max-width:340px;">
                        <div class="text-center mb-2">
                            <strong class="d-block fs-6">{{ $settings['store_name']??'Nama Toko' }}</strong>
                            <span class="text-muted">{{ $settings['store_tagline']??'' }}</span>
                        </div>
                        <hr style="border-style:dashed;">
                        <div class="d-flex justify-content-between"><span>Cuci Reguler 3kg</span><span>Rp 21.000</span></div>
                        <div class="d-flex justify-content-between text-muted mt-1"><span>PPN {{ $settings['tax_percent']??'11' }}%</span><span>Rp 2.310</span></div>
                        <hr style="border-style:dashed;">
                        <div class="d-flex justify-content-between fw-bold"><span>TOTAL</span><span>Rp 23.310</span></div>
                        <div class="text-center text-muted mt-2" style="font-size:.7rem;">{{ $settings['receipt_footer']??'' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TAMPILAN ────────────────────────────────────────── --}}
        <div id="panel-tampilan" class="settings-panel d-none">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="rounded-top-4" style="height:4px;background:linear-gradient(90deg,#6f42c1,#e83e8c);"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div class="rounded-2 p-1 bg-purple bg-opacity-10" style="background:#6f42c115;">
                            <i class="ti ti-palette fs-5" style="color:#6f42c1;"></i>
                        </div>
                        <h5 class="mb-0 fw-semibold">Tampilan & Brand</h5>
                    </div>
                    <p class="text-muted small mb-4">Warna brand digunakan pada elemen-elemen visual di seluruh aplikasi.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="brand_color">Warna Utama (Brand)</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" class="form-control form-control-color"
                                    id="brand_color" name="brand_color"
                                    value="{{ old('brand_color', $settings['brand_color']??'#6f42c1') }}"
                                    style="width:48px;height:38px;">
                                <input type="text" class="form-control form-control-sm font-monospace"
                                    id="brand_color_hex" value="{{ old('brand_color', $settings['brand_color']??'#6f42c1') }}" maxlength="7">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="accent_color">Warna Aksen</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" class="form-control form-control-color"
                                    id="accent_color" name="accent_color"
                                    value="{{ old('accent_color', $settings['accent_color']??'#8b5cf6') }}"
                                    style="width:48px;height:38px;">
                                <input type="text" class="form-control form-control-sm font-monospace"
                                    id="accent_color_hex" value="{{ old('accent_color', $settings['accent_color']??'#8b5cf6') }}" maxlength="7">
                            </div>
                        </div>
                    </div>

                    {{-- Color preview --}}
                    <div class="mt-4 rounded-3 p-4 text-white" id="colorPreview"
                        style="background:linear-gradient(135deg,{{ $settings['brand_color']??'#6f42c1' }},{{ $settings['accent_color']??'#8b5cf6' }});">
                        <div class="fw-bold fs-5">{{ $settings['store_name']??'Nama Toko' }}</div>
                        <div class="small opacity-75">{{ $settings['store_tagline']??'Tagline toko Anda' }}</div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-light rounded-pill px-3" style="font-size:.8rem;">Contoh Tombol</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SUBMIT --}}
        <div class="d-flex justify-content-end">
            <button type="submit" id="btnSave"
                class="btn rounded-pill px-5 fw-semibold shadow-sm text-white py-2"
                style="background:linear-gradient(135deg,{{ $settings['brand_color']??'#6f42c1' }},{{ $settings['accent_color']??'#8b5cf6' }});">
                <span class="btn-text"><i class="ti ti-device-floppy me-2"></i>Simpan Pengaturan</span>
                <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
            </button>
        </div>
    </div>
</div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Tab switching ──────────────────────────────────────────────
    const tabs = document.querySelectorAll('[data-tab]');
    const panels = document.querySelectorAll('.settings-panel');
    const brandColor = document.getElementById('brand_color')?.value || '#6f42c1';
    const accentColor = document.getElementById('accent_color')?.value || '#8b5cf6';

    tabs.forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            const target = this.dataset.tab;

            tabs.forEach(t => {
                t.classList.remove('active', 'text-white');
                t.classList.add('text-muted');
                t.style.background = '';
            });
            panels.forEach(p => p.classList.add('d-none'));

            this.classList.add('active', 'text-white');
            this.classList.remove('text-muted');
            this.style.background = `linear-gradient(135deg,${document.getElementById('brand_color').value},${document.getElementById('accent_color').value})`;
            document.getElementById('panel-' + target)?.classList.remove('d-none');
        });
    });

    // ── Color sync ────────────────────────────────────────────────
    function syncColor(picker, hex) {
        picker.addEventListener('input', () => { hex.value = picker.value; updateColorPreview(); });
        hex.addEventListener('input', function () {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) { picker.value = this.value; updateColorPreview(); }
        });
    }
    syncColor(document.getElementById('brand_color'), document.getElementById('brand_color_hex'));
    syncColor(document.getElementById('accent_color'), document.getElementById('accent_color_hex'));

    function updateColorPreview() {
        const b = document.getElementById('brand_color').value;
        const a = document.getElementById('accent_color').value;
        document.getElementById('colorPreview').style.background = `linear-gradient(135deg,${b},${a})`;
    }

    // ── Logo preview ──────────────────────────────────────────────
    document.getElementById('store_logo')?.addEventListener('change', function () {
        const file = this.files[0]; if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            const prev = document.getElementById('logoPreview');
            prev.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:contain;">`;
        };
        reader.readAsDataURL(file);
    });

    // ── Day toggle (jam operasional) ──────────────────────────────
    document.querySelectorAll('.day-toggle').forEach(toggle => {
        toggle.addEventListener('change', function () {
            const key = this.dataset.target;
            const row = document.getElementById('row-' + key);
            const hidden = document.getElementById(key);
            const openEl = document.getElementById(key + '_open');
            const closeEl = document.getElementById(key + '_close');

            if (this.checked) {
                row.style.background = '#19875408';
                row.style.border = '1px solid #19875430';
                const openVal = openEl?.value || '07:00';
                const closeVal = closeEl?.value || '21:00';

                // rebuild input group
                const container = this.closest('.d-flex').querySelector('.flex-fill');
                container.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <input type="time" class="form-control form-control-sm" name="_${key}_open" id="${key}_open" value="${openVal}" style="max-width:110px;">
                        <span class="text-muted small">–</span>
                        <input type="time" class="form-control form-control-sm" name="_${key}_close" id="${key}_close" value="${closeVal}" style="max-width:110px;">
                        <input type="hidden" name="${key}" id="${key}" value="${openVal}-${closeVal}">
                    </div>`;

                document.querySelectorAll(`[name="_${key}_open"], [name="_${key}_close"]`).forEach(el => {
                    el.addEventListener('input', () => {
                        const o = document.getElementById(key + '_open').value;
                        const c = document.getElementById(key + '_close').value;
                        document.getElementById(key).value = o + '-' + c;
                    });
                });
            } else {
                row.style.background = '#dc354508';
                row.style.border = '1px solid #dc354530';
                const container = this.closest('.d-flex').querySelector('.flex-fill');
                container.innerHTML = `
                    <input type="hidden" name="${key}" id="${key}" value="Tutup">
                    <span class="badge rounded-pill text-danger" style="background:#dc354518;font-size:.75rem;">
                        <i class="ti ti-x me-1"></i>Tutup
                    </span>`;
            }
        });
    });

    // Sync time inputs to hidden on load
    document.querySelectorAll('[name^="_op_"]').forEach(el => {
        el.addEventListener('input', function () {
            const keyPart = this.name.replace('_op_', 'op_').replace(/_open$|_close$/, '');
            const openEl  = document.querySelector(`[name="_${keyPart}_open"]`);
            const closeEl = document.querySelector(`[name="_${keyPart}_close"]`);
            if (openEl && closeEl) {
                document.getElementById(keyPart).value = openEl.value + '-' + closeEl.value;
            }
        });
    });

    // ── Submit loading ─────────────────────────────────────────────
    document.getElementById('settingsForm').addEventListener('submit', function () {
        const btn = document.getElementById('btnSave');
        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.btn-loading').classList.remove('d-none');
    });
});
</script>
@endpush
@endsection
