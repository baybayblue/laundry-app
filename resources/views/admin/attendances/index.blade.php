@extends('layouts.app')

@section('content')
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-6">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                style="background: linear-gradient(135deg, #2563eb20, #2563eb10); border: 1px solid #2563eb30; width:48px; height:48px;">
                <i class="ti ti-calendar-check fs-3 text-primary"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Absensi Harian</h1>
                <p class="mb-0 text-muted small">Monitor kehadiran karyawan hari ini</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 text-md-end">
        <form action="{{ route('admin.attendances.index') }}" method="GET" class="d-inline-flex gap-2">
            <input type="date" name="date" class="form-control form-control-sm rounded-pill px-3" value="{{ $date }}" onchange="this.form.submit()">
            <a href="{{ route('admin.attendances.report') }}" class="btn btn-light btn-sm rounded-pill px-3 border">
                <i class="ti ti-file-analytics me-1"></i> Laporan Bulanan
            </a>
        </form>
    </div>
</div>

{{-- STATS CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #19875415, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Hadir</span>
                    <div class="bg-success bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-user-check text-success fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-success">{{ $stats['total_present'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #fd7e1415, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Terlambat</span>
                    <div class="bg-warning bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-clock text-warning fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-warning">{{ $stats['total_late'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #dc354515, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Belum Absen</span>
                    <div class="bg-danger bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-user-x text-danger fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-danger">{{ max(0, $stats['total_absent']) }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ATTENDANCE TABLE --}}
<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th class="py-3 ps-4 text-muted fw-semibold small text-uppercase">Karyawan</th>
                    <th class="py-3 text-muted fw-semibold small text-uppercase">Jam Masuk</th>
                    <th class="py-3 text-muted fw-semibold small text-uppercase">Jam Keluar</th>
                    <th class="py-3 text-muted fw-semibold small text-uppercase text-center">Status</th>
                    <th class="py-3 text-muted fw-semibold small text-uppercase">Lokasi</th>
                    <th class="py-3 pe-4 text-muted fw-semibold small text-uppercase">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                                 style="width:34px; height:34px; background: linear-gradient(135deg, #2563eb, #60a5fa); font-size: .8rem;">
                                {{ substr($attendance->user->name, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold" style="font-size: .85rem;">{{ $attendance->user->name }}</h6>
                                <small class="text-muted">{{ $attendance->user->position ?? '-' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-medium">{{ $attendance->clock_in->format('H:i') }}</div>
                        <small class="text-muted" style="font-size: .7rem;">{{ $attendance->date->format('d M Y') }}</small>
                    </td>
                    <td>
                        @if($attendance->clock_out)
                            <div class="fw-medium">{{ $attendance->clock_out->format('H:i') }}</div>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge rounded-pill bg-{{ $attendance->status_color }} bg-opacity-10 text-{{ $attendance->status_color }} border border-{{ $attendance->status_color }} border-opacity-25 px-2 py-1" style="font-size: .7rem;">
                            {{ $attendance->status_label }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @if($attendance->lat_in && $attendance->long_in)
                                <a href="https://www.google.com/maps?q={{ $attendance->lat_in }},{{ $attendance->long_in }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill p-1 px-2" style="font-size: .7rem;" title="Lokasi Masuk">
                                    <i class="ti ti-map-pin"></i> In
                                </a>
                            @endif
                            @if($attendance->lat_out && $attendance->long_out)
                                <a href="https://www.google.com/maps?q={{ $attendance->lat_out }},{{ $attendance->long_out }}" target="_blank" class="btn btn-xs btn-outline-secondary rounded-pill p-1 px-2" style="font-size: .7rem;" title="Lokasi Keluar">
                                    <i class="ti ti-map-pin"></i> Out
                                </a>
                            @endif
                        </div>
                    </td>
                    <td class="pe-4">
                        <small class="text-muted text-truncate d-block" style="max-width: 150px;" title="{{ $attendance->note }}">
                            {{ $attendance->note ?? '-' }}
                        </small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="ti ti-info-circle fs-2 d-block mb-2"></i>
                        Belum ada data absensi untuk tanggal ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($attendances->hasPages())
    <div class="card-footer bg-white border-0 py-3 ps-4">
        {{ $attendances->links() }}
    </div>
    @endif
</div>
@endsection
