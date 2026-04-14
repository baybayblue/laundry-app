@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Manajemen Pengajuan Cuti</h4>
            <p class="text-muted small mb-0">Tinjau dan proses pengajuan izin/cuti dari karyawan.</p>
        </div>
        <div class="btn-group rounded-pill overflow-hidden border">
            <a href="{{ route('admin.leave-requests.index', ['status' => 'pending']) }}" class="btn btn-{{ $status === 'pending' ? 'primary' : 'white' }} btn-sm px-3">Menunggu</a>
            <a href="{{ route('admin.leave-requests.index', ['status' => 'approved']) }}" class="btn btn-{{ $status === 'approved' ? 'primary' : 'white' }} btn-sm px-3">Disetujui</a>
            <a href="{{ route('admin.leave-requests.index', ['status' => 'rejected']) }}" class="btn btn-{{ $status === 'rejected' ? 'primary' : 'white' }} btn-sm px-3">Ditolak</a>
            <a href="{{ route('admin.leave-requests.index', ['status' => 'all']) }}" class="btn btn-{{ $status === 'all' ? 'primary' : 'white' }} btn-sm px-3">Semua</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 border-0">Karyawan</th>
                            <th class="border-0">Tipe & Durasi</th>
                            <th class="border-0">Alasan</th>
                            <th class="border-0">Status</th>
                            <th class="pe-4 border-0 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width:40px;height:40px;">
                                        {{ substr($req->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold mb-0 text-dark">{{ $req->user->name }}</div>
                                        <div class="text-muted" style="font-size: .75rem;">{{ $req->user->position ?? 'Karyawan' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="badge bg-light text-dark border rounded-pill px-3 mb-1">
                                    {{ $req->type_label }}
                                </div>
                                <div class="small fw-semibold">{{ $req->start_date->format('d M') }} - {{ $req->end_date->format('d M Y') }}</div>
                                <div class="text-muted" style="font-size:.7rem;">
                                    Total {{ $req->start_date->diffInDays($req->end_date) + 1 }} hari
                                </div>
                            </td>
                            <td>
                                <p class="mb-0 small text-muted" style="max-width: 250px; white-space: normal;">
                                    "{{ $req->reason }}"
                                </p>
                            </td>
                            <td>
                                <span class="badge bg-{{ $req->status_color }} rounded-pill px-3">
                                    {{ $req->status_label }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                @if($req->status === 'pending')
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="processRequest('reject', {{ $req->id }})">
                                        Tolak
                                    </button>
                                    <form action="{{ route('admin.leave-requests.approve', $req) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" onclick="return confirm('Setujui pengajuan ini?')">
                                            Setujui
                                        </button>
                                    </form>
                                </div>
                                @else
                                <div class="small text-muted">
                                    Diproses oleh <strong>{{ $req->approver?->name }}</strong><br>
                                    {{ $req->approved_at?->format('d/m/Y H:i') }}
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="ti ti-loader fs-1 d-block mb-2"></i>
                                Tidak ada data pengajuan dalam kategori ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($requests->hasPages())
            <div class="p-4 border-top">
                {{ $requests->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="fw-bold mb-0">Tolak Pengajuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <p class="text-muted small">Berikan alasan mengapa pengajuan ini ditolak (opsional).</p>
                    <textarea name="notes" class="form-control rounded-3 border-light bg-light" rows="3" placeholder="Contoh: Jadwal padat, butuh tenaga..."></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 shadow-sm">Ya, Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function processRequest(action, id) {
        if (action === 'reject') {
            const form = document.getElementById('rejectForm');
            form.action = `{{ url('admin/leave-requests') }}/${id}/reject`;
            new bootstrap.Modal(document.getElementById('rejectModal')).show();
        }
    }
</script>
@endpush
@endsection
