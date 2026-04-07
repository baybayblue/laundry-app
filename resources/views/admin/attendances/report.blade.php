@extends('layouts.app')

@section('content')
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-6">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                style="background: linear-gradient(135deg, #2563eb20, #2563eb10); border: 1px solid #2563eb30; width:48px; height:48px;">
                <i class="ti ti-file-analytics fs-3 text-primary"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Laporan Absensi</h1>
                <p class="mb-0 text-muted small">Ringkasan kehadiran bulanan karyawan</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 text-md-end">
        <form action="{{ route('admin.attendances.report') }}" method="GET" class="d-inline-flex gap-2">
            <select name="month" class="form-select form-select-sm rounded-pill px-3" onchange="this.form.submit()">
                @foreach(range(1, 12) as $m)
                    <option value="{{ sprintf('%02d', $m) }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
            <select name="year" class="form-select form-select-sm rounded-pill px-3" onchange="this.form.submit()">
                @foreach(range(date('Y')-2, date('Y')) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <a href="{{ route('admin.attendances.index') }}" class="btn btn-light btn-sm rounded-pill px-3 border">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </form>
    </div>
</div>

{{-- REPORT TABLE --}}
<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th class="py-3 ps-4 text-muted fw-semibold small text-uppercase">Karyawan</th>
                    <th class="py-3 text-muted fw-semibold small text-uppercase">Posisi</th>
                    <th class="py-3 text-muted fw-semibold small text-uppercase text-center">Hadir</th>
                    <th class="py-3 text-muted fw-semibold small text-uppercase text-center">Terlambat</th>
                    <th class="py-3 text-muted fw-semibold small text-uppercase text-center">Izin/Cuti</th>
                    <th class="py-3 text-muted fw-semibold small text-uppercase text-center">Persentase</th>
                    <th class="py-3 pe-4 text-muted fw-semibold small text-uppercase text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData as $data)
                @php
                    $totalDays = 25; // Assuming 25 work days in a month for calculation
                    $percentage = round(($data['present'] / $totalDays) * 100);
                @endphp
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold">{{ $data['name'] }}</div>
                    </td>
                    <td>{{ $data['position'] ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">{{ $data['present'] }} hr</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">{{ $data['late'] }} hr</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">{{ $data['leave'] }} hr</span>
                    </td>
                    <td class="text-center" style="width: 150px;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-fill" style="height: 6px; border-radius: 4px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $percentage) }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="fw-bold">{{ $percentage }}%</small>
                        </div>
                    </td>
                    <td class="pe-4 text-end">
                        <button class="btn btn-sm btn-icon rounded-2 border-0" style="background: #2563eb15; color: #2563eb;" title="Rincian Detail">
                            <i class="ti ti-eye fs-6"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">Belum ada data laporan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
