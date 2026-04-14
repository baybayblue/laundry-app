@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row g-4">
        {{-- Form Pengajuan --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Ajukan Cuti/Izin</h5>
                </div>
                <div class="card-body pt-0">
                    <form action="{{ route('employee.leave-requests.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tipe Kehadiran</label>
                            <select name="type" class="form-select rounded-3 border-light bg-light" required>
                                <option value="leave">Cuti</option>
                                <option value="sick">Sakit</option>
                                <option value="other">Izin Lainnya</option>
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Mulai Tanggal</label>
                                <input type="date" name="start_date" class="form-control rounded-3 border-light bg-light" required min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control rounded-3 border-light bg-light" required min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Alasan / Keperluan</label>
                            <textarea name="reason" class="form-control rounded-3 border-light bg-light" rows="4" required placeholder="Jelaskan alasan pengajuan Anda..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                            <i class="ti ti-send me-1"></i> KIRIM PENGAJUAN
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Daftar Riwayat --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Riwayat Pengajuan</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="border-0">Tipe</th>
                                    <th class="border-0">Tanggal</th>
                                    <th class="border-0">Alasan</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $req)
                                <tr>
                                    <td>
                                        <span class="badge bg-opacity-10 text-{{ $req->type === 'sick' ? 'danger' : 'primary' }} bg-{{ $req->type === 'sick' ? 'danger' : 'primary' }} rounded-pill px-3">
                                            {{ $req->type_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold">{{ $req->start_date->format('d M') }} - {{ $req->end_date->format('d M Y') }}</div>
                                        <div class="text-muted" style="font-size:.7rem;">
                                            {{ $req->start_date->diffInDays($req->end_date) + 1 }} hari
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $req->reason }}">
                                            {{ $req->reason }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $req->status_color }} rounded-pill px-3">
                                            {{ $req->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if($req->status === 'pending')
                                        <form action="{{ route('employee.leave-requests.destroy', $req) }}" method="POST" onsubmit="return confirm('Batalkan pengajuan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0" title="Batalkan">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="ti ti-calendar-off fs-1 d-block mb-2"></i>
                                        Belum ada riwayat pengajuan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
