@extends('layouts.app')

@section('content')
{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle overflow-hidden border border-2 flex-shrink-0" style="width:52px; height:52px;">
                <div class="d-flex align-items-center justify-content-center fw-bold text-white h-100"
                    style="background: linear-gradient(135deg, #198754, #20c997); font-size:20px;">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Edit: {{ $customer->name }}</h1>
                <p class="mb-0 text-muted small d-flex align-items-center gap-1">
                    <i class="ti ti-mail"></i> {{ $customer->email ?? 'Belum ada email' }}
                </p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('admin.customers.index') }}" class="btn btn-light d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- VALIDATION ALERT (Global) --}}
@if($errors->any())
<div class="alert border-0 rounded-4 shadow-sm mb-4 d-flex align-items-start gap-3" style="background:#dc354510; border-left: 4px solid #dc3545 !important;">
    <i class="ti ti-alert-circle text-danger fs-4 mt-1 flex-shrink-0"></i>
    <div>
        <div class="fw-semibold text-danger mb-1">Terdapat {{ $errors->count() }} kesalahan pada form:</div>
        <ul class="mb-0 small text-danger ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="row g-4">
    {{-- MAIN FORM --}}
    <div class="col-12 col-lg-8">
        <form action="{{ route('admin.customers.update', $customer) }}" method="POST" id="customerForm" novalidate>
            @csrf
            @method('PUT')

            {{-- SECTION: Informasi Akun --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-1 bg-primary bg-opacity-10">
                            <i class="ti ti-user-circle text-primary"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Informasi Pelanggan</h6>
                    </div>
                    <hr class="mt-3 mb-0">
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="name">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-user text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $customer->name) }}"
                                    minlength="3" maxlength="255" required>
                                @error('name')
                                    <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="email">
                                Alamat Email <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-mail text-muted"></i></span>
                                <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $customer->email) }}"
                                    maxlength="255">
                                @error('email')
                                    <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION: Data Pribadi --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-1 bg-success bg-opacity-10">
                            <i class="ti ti-map-pin text-success"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Kontak & Alamat</h6>
                    </div>
                    <hr class="mt-3 mb-0">
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium small" for="phone">Nomor Handphone <span class="text-muted fw-normal">(Opsional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-phone text-muted"></i></span>
                                <input type="tel" class="form-control border-start-0 @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone', $customer->phone) }}"
                                    placeholder="08xxxxxxxxxx" maxlength="20"
                                    pattern="^(08|\+62)[0-9]{7,12}$">
                                @error('phone')
                                    <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Format: 08xxxxxxxxxx atau +62xxxxxxxxxx</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium small" for="address">Alamat Lengkap <span class="text-muted fw-normal">(Opsional)</span></label>
                            <div class="input-group align-items-start">
                                <span class="input-group-text bg-light border-end-0 pt-2"><i class="ti ti-map-pin text-muted"></i></span>
                                <textarea class="form-control border-start-0 @error('address') is-invalid @enderror"
                                    id="address" name="address" rows="4"
                                    placeholder="Alamat penjemputan/pengantaran pakaian...">{{ old('address', $customer->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        <i class="ti ti-clock me-1"></i>
                        Terakhir diperbarui: {{ $customer->updated_at->diffForHumans() }}
                    </small>
                </div>
                <div class="d-flex gap-3">
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-success rounded-pill px-5 fw-semibold shadow-sm" id="btnSubmit">
                        <span class="btn-text"><i class="ti ti-device-floppy me-1"></i>Perbarui Data</span>
                        <span class="btn-loading d-none">
                            <span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- SIDEBAR INFO --}}
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3 d-flex align-items-center gap-2">
                    <i class="ti ti-user-check text-success"></i> Info Pelanggan
                </h6>
                <div class="d-flex flex-column gap-3 small">
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted">ID Pelanggan</span>
                        <span class="fw-semibold font-monospace text-dark">#{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted">Terdaftar Sejak</span>
                        <span class="fw-semibold text-dark">{{ $customer->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #dc354508, #fff);">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-2 d-flex align-items-center gap-2 text-danger">
                    <i class="ti ti-alert-triangle"></i> Zona Berbahaya
                </h6>
                <p class="small text-muted mb-3">Menghapus data pelanggan akan mencabut akses login mereka jika ada, dan menyembunyikan riwayat mereka.</p>
                <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="form-delete-sidebar">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill w-100 btn-delete-sidebar">
                        <i class="ti ti-trash me-1"></i>Hapus Pelanggan Ini
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Loading state on submit
        const form = document.getElementById('customerForm');
        const btnSubmit = document.getElementById('btnSubmit');
        form.addEventListener('submit', function () {
            if (!form.checkValidity()) return;
            btnSubmit.disabled = true;
            btnSubmit.querySelector('.btn-text').classList.add('d-none');
            btnSubmit.querySelector('.btn-loading').classList.remove('d-none');
        });

        // Client side validation
        form.addEventListener('input', function (e) {
            const el = e.target;
            if (['INPUT', 'TEXTAREA'].includes(el.tagName)) {
                if (el.checkValidity()) {
                    if (el.value.length > 0) {
                        el.classList.remove('is-invalid');
                        el.classList.add('is-valid');
                    } else {
                        el.classList.remove('is-invalid');
                        el.classList.remove('is-valid');
                    }
                } else if (el.value.length > 0) {
                    el.classList.add('is-invalid');
                    el.classList.remove('is-valid');
                }
            }
        });

        // Sidebar delete button
        const btnDeleteSidebar = document.querySelector('.btn-delete-sidebar');
        if (btnDeleteSidebar) {
            btnDeleteSidebar.addEventListener('click', function () {
                const form = document.querySelector('.form-delete-sidebar');
                Swal.fire({
                    title: 'Hapus Pelanggan?',
                    html: `Data pelanggan <strong>{{ $customer->name }}</strong> akan dihapus permanen.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="ti ti-trash me-1"></i>Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true,
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        }
    });
</script>
@endpush
@endsection
