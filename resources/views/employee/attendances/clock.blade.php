@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-4 text-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                    style="background: linear-gradient(135deg, #2563eb20, #2563eb10); border: 1px solid #2563eb30; width:64px; height:64px;">
                    <i class="ti ti-alarm fs-1 text-primary"></i>
                </div>
                <h4 class="fw-bold mb-1">Absensi Karyawan</h4>
                <p class="text-muted small mb-0">{{ Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                <div id="digitalClock" class="fs-2 fw-bold text-primary mt-2">00:00:00</div>
            </div>
            <div class="card-body p-4 pt-0">
                <div id="locationStatus" class="alert alert-info border-0 rounded-3 small mb-4">
                    <i class="ti ti-map-pin me-1"></i> Mendeteksi lokasi...
                </div>

                @if(!$todayAttendance)
                <form action="{{ route('employee.attendances.clock-in') }}" method="POST" id="clockForm">
                    @csrf
                    <input type="hidden" name="lat" id="lat">
                    <input type="hidden" name="long" id="long">
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Catatan (Opsional)</label>
                        <textarea name="note" class="form-control rounded-3 border-0 bg-light" rows="3" placeholder="Contoh: Datang lebih awal..."></textarea>
                    </div>

                    <button type="submit" id="btnClock" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" disabled>
                        <i class="ti ti-login fs-4"></i> ABSEN MASUK
                    </button>
                </form>
                @elseif(!$todayAttendance->clock_out)
                <div class="alert alert-success border-0 rounded-3 small mb-4">
                    <i class="ti ti-check me-1"></i> Anda sudah absen masuk pada <strong>{{ $todayAttendance->clock_in->format('H:i') }}</strong>
                </div>

                <form action="{{ route('employee.attendances.clock-out') }}" method="POST" id="clockForm">
                    @csrf
                    <input type="hidden" name="lat" id="lat">
                    <input type="hidden" name="long" id="long">
                    
                    <button type="submit" id="btnClock" class="btn btn-danger w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" disabled>
                        <i class="ti ti-logout fs-4"></i> ABSEN KELUAR
                    </button>
                </form>
                @else
                <div class="text-center py-4">
                    <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px; height:60px;">
                        <i class="ti ti-circle-check fs-1 text-success"></i>
                    </div>
                    <h5 class="fw-bold text-success mb-1">Absensi Selesai!</h5>
                    <p class="text-muted small mb-0">Terima kasih untuk kerja keras Anda hari ini.</p>
                    <div class="mt-4 p-3 rounded-4 bg-light border-0">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Masuk:</span>
                            <span class="fw-bold small">{{ $todayAttendance->clock_in->format('H:i') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Keluar:</span>
                            <span class="fw-bold small">{{ $todayAttendance->clock_out?->format('H:i') ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mt-4 text-center">
                    <a href="{{ route('employee.leave-requests.index') }}" class="btn btn-outline-secondary btn-sm border-0">
                        <i class="ti ti-calendar-event me-1"></i> Ajukan Cuti/Izin
                    </a>
                </div>
            </div>
            <div class="card-footer bg-light border-0 py-3 text-center">
                <a href="{{ route('employee.dashboard') }}" class="text-decoration-none text-muted small">
                    <i class="ti ti-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        document.getElementById('digitalClock').textContent = timeString;
    }
    setInterval(updateClock, 1000);
    updateClock();

    function getLocation() {
        const statusDiv = document.getElementById('locationStatus');
        const btn = document.getElementById('btnClock');
        const latInput = document.getElementById('lat');
        const longInput = document.getElementById('long');

        if (!navigator.geolocation) {
            statusDiv.className = 'alert alert-danger border-0 rounded-3 small mb-4';
            statusDiv.innerHTML = '<i class="ti ti-alert-triangle me-1"></i> Browser Anda tidak mendukung Geolocation.';
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const long = position.coords.longitude;
                latInput.value = lat;
                longInput.value = long;
                
                statusDiv.className = 'alert alert-success border-0 rounded-3 small mb-4';
                statusDiv.innerHTML = `<i class="ti ti-map-pin me-1"></i> Lokasi terdeteksi (${lat.toFixed(4)}, ${long.toFixed(4)})`;
                if(btn) btn.disabled = false;
            },
            (error) => {
                statusDiv.className = 'alert alert-danger border-0 rounded-3 small mb-4';
                let errorMsg = 'Gagal mengakses lokasi.';
                switch(error.code) {
                    case error.PERMISSION_DENIED: errorMsg = "Izin lokasi ditolak. Harap aktifkan GPS."; break;
                    case error.POSITION_UNAVAILABLE: errorMsg = "Informasi lokasi tidak tersedia."; break;
                    case error.TIMEOUT: errorMsg = "Waktu permintaan lokasi habis."; break;
                }
                statusDiv.innerHTML = `<i class="ti ti-alert-triangle me-1"></i> ${errorMsg}`;
                console.error(error);
            },
            { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
        );
    }

    document.addEventListener('DOMContentLoaded', getLocation);
</script>
@endpush
@endsection
