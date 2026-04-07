@extends('layouts.app')

@section('content')
{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #0d6efd20, #0d6efd10); border: 1px solid #0d6efd30; width:48px; height:48px;">
                <i class="ti ti-user-plus fs-3 text-primary"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Tambah Karyawan</h1>
                <p class="mb-0 text-muted small">Daftarkan staf baru beserta data lengkapnya</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('admin.employees.index') }}" class="btn btn-light d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
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
        <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data" id="employeeForm" novalidate>
            @csrf

            {{-- SECTION: Informasi Akun --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-1 bg-primary bg-opacity-10">
                            <i class="ti ti-user-circle text-primary"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Informasi Akun</h6>
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
                                    id="name" name="name" value="{{ old('name') }}"
                                    placeholder="Masukkan nama lengkap..."
                                    minlength="3" maxlength="255" required>
                                @error('name')
                                    <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="email">
                                Alamat Email <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-mail text-muted"></i></span>
                                <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}"
                                    placeholder="email@contoh.com" maxlength="255" required>
                                @error('email')
                                    <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-2 p-3 rounded-3" style="background:#fff3cd20; border: 1px dashed #ffc10760;">
                                <i class="ti ti-lock text-warning fs-5 flex-shrink-0"></i>
                                <div class="small">
                                    Kata sandi default karyawan adalah: <code class="fw-bold bg-warning bg-opacity-10 px-2 py-1 rounded text-dark">password</code>
                                    <span class="text-muted ms-1">— karyawan dapat mengubahnya setelah login pertama.</span>
                                </div>
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
                            <i class="ti ti-id text-success"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Data Pribadi Karyawan</h6>
                    </div>
                    <hr class="mt-3 mb-0">
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="phone">Nomor Handphone</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-phone text-muted"></i></span>
                                <input type="tel" class="form-control border-start-0 @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone') }}"
                                    placeholder="08xxxxxxxxxx" maxlength="20"
                                    pattern="^(08|\+62)[0-9]{7,12}$">
                                @error('phone')
                                    <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Format: 08xxxxxxxxxx atau +62xxxxxxxxxx</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="gender">Jenis Kelamin</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-gender-bigender text-muted"></i></span>
                                <select class="form-select border-start-0 @error('gender') is-invalid @enderror" id="gender" name="gender">
                                    <option value="" disabled selected>Pilih Jenis Kelamin...</option>
                                    <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium small" for="position">Posisi / Jabatan</label>
                            <div class="row g-2" id="positionGroup">
                                @php
                                    $positions = [
                                        ['value' => 'Kasir', 'icon' => 'ti-cash', 'color' => '#0d6efd'],
                                        ['value' => 'Staff Cuci', 'icon' => 'ti-droplet', 'color' => '#198754'],
                                        ['value' => 'Staff Setrika', 'icon' => 'ti-flame', 'color' => '#fd7e14'],
                                        ['value' => 'Kurir', 'icon' => 'ti-truck', 'color' => '#6f42c1'],
                                    ];
                                @endphp
                                @foreach($positions as $pos)
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="position" id="pos_{{ Str::slug($pos['value']) }}"
                                        value="{{ $pos['value'] }}" {{ old('position') == $pos['value'] ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary w-100 rounded-3 d-flex flex-column align-items-center py-3 gap-1 position-card"
                                        for="pos_{{ Str::slug($pos['value']) }}"
                                        style="--pos-color: {{ $pos['color'] }}; font-size:.8rem; font-weight:600;">
                                        <i class="ti {{ $pos['icon'] }} fs-4"></i>
                                        {{ $pos['value'] }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @error('position')
                                <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium small" for="address">Alamat Lengkap</label>
                            <div class="input-group align-items-start">
                                <span class="input-group-text bg-light border-end-0 pt-2"><i class="ti ti-map-pin text-muted"></i></span>
                                <textarea class="form-control border-start-0 @error('address') is-invalid @enderror"
                                    id="address" name="address" rows="3"
                                    placeholder="Jl. Contoh No. 1, Kota, Provinsi...">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION: Foto Profil --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-1 bg-info bg-opacity-10">
                            <i class="ti ti-camera text-info"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Foto Profil</h6>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill small fw-normal">Opsional</span>
                    </div>
                    <hr class="mt-3 mb-0">
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-4 flex-wrap">
                        {{-- Preview area --}}
                        <div class="text-center flex-shrink-0">
                            <div id="photoPreviewWrap" class="rounded-circle overflow-hidden border-3 border-dashed border-light-subtle d-flex align-items-center justify-content-center mx-auto"
                                style="width:100px; height:100px; background:#f8f9fa; border: 2px dashed #dee2e6;">
                                <div id="photoPlaceholder" class="text-muted text-center">
                                    <i class="ti ti-user fs-1 d-block opacity-25"></i>
                                </div>
                                <img id="photoPreview" src="#" alt="Preview" class="d-none w-100 h-100" style="object-fit:cover;">
                            </div>
                            <small class="text-muted d-block mt-2">Preview</small>
                        </div>
                        {{-- Upload area --}}
                        <div class="flex-fill">
                            <label class="form-label fw-medium small" for="photo">Upload Foto</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror"
                                id="photo" name="photo" accept="image/png,image/jpeg,image/jpg">
                            <div class="form-text">Format: JPG, PNG, JPEG. Ukuran maks: 2MB.</div>
                            @error('photo')
                                <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="d-flex justify-content-end gap-3">
                <a href="{{ route('admin.employees.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                <button type="reset" class="btn btn-outline-secondary rounded-pill px-4" id="btnReset">
                    <i class="ti ti-refresh me-1"></i>Reset
                </button>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-semibold shadow-sm" id="btnSubmit">
                    <span class="btn-text"><i class="ti ti-check me-1"></i>Simpan Karyawan</span>
                    <span class="btn-loading d-none">
                        <span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>

    {{-- SIDEBAR TIPS --}}
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-3" style="background: linear-gradient(135deg, #0d6efd08, #fff);">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3 d-flex align-items-center gap-2">
                    <i class="ti ti-info-circle text-primary"></i> Panduan Pengisian
                </h6>
                <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
                    <li class="d-flex align-items-start gap-2">
                        <i class="ti ti-circle-check text-success mt-1 flex-shrink-0"></i>
                        <span>Nama dan Email wajib diisi dan harus unik.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="ti ti-circle-check text-success mt-1 flex-shrink-0"></i>
                        <span>Kata sandi awal adalah <code>password</code>. Karyawan sebaiknya menggantinya setelah login.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="ti ti-circle-check text-success mt-1 flex-shrink-0"></i>
                        <span>Pilih posisi sesuai tugas utama karyawan di laundry.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="ti ti-circle-check text-success mt-1 flex-shrink-0"></i>
                        <span>Foto profil bersifat opsional, tapi membantu identifikasi karyawan.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="ti ti-circle-check text-success mt-1 flex-shrink-0"></i>
                        <span>Ukuran foto maksimal 2MB, format JPG atau PNG.</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #19875408, #fff);">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3 d-flex align-items-center gap-2">
                    <i class="ti ti-briefcase text-success"></i> Deskripsi Posisi
                </h6>
                <div class="d-flex flex-column gap-2 small text-muted">
                    <div class="d-flex align-items-center gap-2 p-2 rounded-2" style="background:#0d6efd10;">
                        <i class="ti ti-cash text-primary"></i>
                        <div><strong class="text-dark">Kasir</strong> — Mengelola transaksi & pembayaran</div>
                    </div>
                    <div class="d-flex align-items-center gap-2 p-2 rounded-2" style="background:#19875410;">
                        <i class="ti ti-droplet text-success"></i>
                        <div><strong class="text-dark">Staff Cuci</strong> — Proses pencucian pakaian</div>
                    </div>
                    <div class="d-flex align-items-center gap-2 p-2 rounded-2" style="background:#fd7e1410;">
                        <i class="ti ti-flame text-warning"></i>
                        <div><strong class="text-dark">Staff Setrika</strong> — Finishing & pengemasan</div>
                    </div>
                    <div class="d-flex align-items-center gap-2 p-2 rounded-2" style="background:#6f42c110;">
                        <i class="ti ti-truck" style="color:#6f42c1;"></i>
                        <div><strong class="text-dark">Kurir</strong> — Antar jemput laundry</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
    .position-card:hover {
        border-color: var(--pos-color) !important;
        color: var(--pos-color) !important;
        background-color: color-mix(in srgb, var(--pos-color) 8%, transparent) !important;
    }
    .btn-check:checked + .position-card {
        border-color: var(--pos-color) !important;
        color: var(--pos-color) !important;
        background-color: color-mix(in srgb, var(--pos-color) 12%, transparent) !important;
        box-shadow: 0 0 0 0.15rem color-mix(in srgb, var(--pos-color) 25%, transparent);
    }
    .position-card { transition: all .2s; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Photo preview
        const photoInput = document.getElementById('photo');
        const photoPreview = document.getElementById('photoPreview');
        const photoPlaceholder = document.getElementById('photoPlaceholder');

        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            // Client-side size validation
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran foto maksimal 2MB. Silakan pilih file yang lebih kecil.',
                    confirmButtonColor: '#0d6efd'
                });
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = e => {
                photoPreview.src = e.target.result;
                photoPreview.classList.remove('d-none');
                photoPlaceholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        });

        // Reset button clears preview
        document.getElementById('btnReset').addEventListener('click', function () {
            photoPreview.src = '#';
            photoPreview.classList.add('d-none');
            photoPlaceholder.classList.remove('d-none');
        });

        // Loading state on submit
        const form = document.getElementById('employeeForm');
        const btnSubmit = document.getElementById('btnSubmit');
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) return;
            btnSubmit.disabled = true;
            btnSubmit.querySelector('.btn-text').classList.add('d-none');
            btnSubmit.querySelector('.btn-loading').classList.remove('d-none');
        });

        // Client-side validation feedback
        form.addEventListener('input', function (e) {
            const el = e.target;
            if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' || el.tagName === 'SELECT') {
                if (el.checkValidity()) {
                    el.classList.remove('is-invalid');
                    el.classList.add('is-valid');
                } else if (el.value.length > 0) {
                    el.classList.add('is-invalid');
                    el.classList.remove('is-valid');
                }
            }
        });
    });
</script>
@endpush
@endsection
