@extends('layouts.app')

@section('content')

<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm rounded-circle border shadow-sm" style="width:36px;height:36px;">
                <i class="ti ti-arrow-left"></i>
            </a>
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                style="background: linear-gradient(135deg, #7c3aed20, #7c3aed10); border: 1px solid #7c3aed30; width:48px; height:48px;">
                <i class="ti ti-user-plus fs-3" style="color:#7c3aed;"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Tambah User</h1>
                <p class="mb-0 text-muted small">Buat akun baru untuk admin, owner, atau karyawan</p>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        {{-- LEFT: Main Info --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4 d-flex align-items-center gap-2">
                        <i class="ti ti-user text-primary"></i> Informasi Akun
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-user text-muted"></i></span>
                                <input type="text" name="name" class="form-control border-start-0 @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" placeholder="Nama lengkap" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-mail text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-start-0 @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="email@example.com" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-lock text-muted"></i></span>
                                <input type="password" name="password" id="password"
                                    class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror"
                                    placeholder="Min. 8 karakter" required>
                                <button type="button" class="input-group-text bg-light border-start-0" onclick="togglePwd('password','eyePass')">
                                    <i class="ti ti-eye" id="eyePass"></i>
                                </button>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Konfirmasi Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-lock-check text-muted"></i></span>
                                <input type="password" name="password_confirmation" id="passwordConf"
                                    class="form-control border-start-0 border-end-0"
                                    placeholder="Ulangi password" required>
                                <button type="button" class="input-group-text bg-light border-start-0" onclick="togglePwd('passwordConf','eyeConf')">
                                    <i class="ti ti-eye" id="eyeConf"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4 d-flex align-items-center gap-2">
                        <i class="ti ti-id-badge text-primary"></i> Data Pribadi
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">No. HP</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-phone text-muted"></i></span>
                                <input type="text" name="phone" class="form-control border-start-0 @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}" placeholder="08XXXXXXXXXX">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Jenis Kelamin</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">– Pilih –</option>
                                <option value="male"   {{ old('gender')==='male'   ? 'selected' : '' }}>Laki-laki</option>
                                <option value="female" {{ old('gender')==='female' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium small">Alamat</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-map-pin text-muted"></i></span>
                                <input type="text" name="address" class="form-control border-start-0 @error('address') is-invalid @enderror"
                                    value="{{ old('address') }}" placeholder="Alamat lengkap">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Posisi / Jabatan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-briefcase text-muted"></i></span>
                                <input type="text" name="position" class="form-control border-start-0 @error('position') is-invalid @enderror"
                                    value="{{ old('position') }}" placeholder="Cth: Kasir, Setrika, dll">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Role & Photo --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4 d-flex align-items-center gap-2">
                        <i class="ti ti-shield-check" style="color:#7c3aed;"></i> Role / Hak Akses
                    </h6>
                    <div class="d-flex flex-column gap-2" id="roleSelector">
                        @php
                            $roles = [
                                'admin'    => ['label'=>'Admin','desc'=>'Akses penuh ke semua fitur','icon'=>'ti-shield-check','color'=>'#dc3545'],
                                'owner'    => ['label'=>'Owner','desc'=>'Monitoring & laporan saja','icon'=>'ti-crown','color'=>'#f59e0b'],
                                'employee' => ['label'=>'Karyawan','desc'=>'Transaksi & presensi saja','icon'=>'ti-user-check','color'=>'#198754'],
                            ];
                        @endphp
                        @foreach($roles as $val => $info)
                        <label class="role-card d-flex align-items-center gap-3 p-3 rounded-3 border cursor-pointer"
                            style="cursor:pointer; transition: all .15s;"
                            data-color="{{ $info['color'] }}">
                            <input type="radio" name="role" value="{{ $val }}"
                                class="role-radio d-none" {{ old('role', 'employee') === $val ? 'checked' : '' }}>
                            <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width:40px;height:40px;background:{{ $info['color'] }}15;">
                                <i class="ti {{ $info['icon'] }}" style="color:{{ $info['color'] }};font-size:1.2rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">{{ $info['label'] }}</div>
                                <div class="text-muted" style="font-size:.72rem;">{{ $info['desc'] }}</div>
                            </div>
                            <i class="ti ti-circle-check check-icon text-muted" style="font-size:1.2rem;"></i>
                        </label>
                        @endforeach
                    </div>
                    @error('role')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="ti ti-photo text-muted"></i> Foto Profil
                    </h6>
                    <div class="text-center">
                        <div class="mb-3 mx-auto rounded-circle overflow-hidden border" id="photoPreviewWrap"
                            style="width:80px;height:80px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;">
                            <i class="ti ti-user fs-1 text-muted" id="photoPlaceholder"></i>
                            <img id="photoPreview" src="#" alt="" class="d-none" style="width:80px;height:80px;object-fit:cover;">
                        </div>
                        <label for="photo" class="btn btn-sm btn-light border rounded-pill px-3" style="cursor:pointer;">
                            <i class="ti ti-upload me-1"></i>Upload Foto
                        </label>
                        <input type="file" name="photo" id="photo" class="d-none" accept="image/*" onchange="previewPhoto(this)">
                        <p class="text-muted mt-2 mb-0" style="font-size:.7rem;">JPG, PNG, WEBP — maks. 2MB</p>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column gap-2">
                <button type="submit" class="btn btn-primary fw-semibold rounded-pill py-2"
                    style="background:linear-gradient(135deg,#7c3aed,#9333ea);border:none;">
                    <i class="ti ti-check me-1"></i>Simpan User
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-light border rounded-pill">Batal</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<style>
    .role-card { background: #f9fafb; }
    .role-card.active { font-weight: 600; }
    .role-card .check-icon { opacity: .3; }
    .role-card.active .check-icon { opacity: 1; color: var(--active-color, #2563eb) !important; }
</style>
<script>
function togglePwd(id, iconId) {
    const inp = document.getElementById(id);
    const icon = document.getElementById(iconId);
    if (inp.type === 'password') { inp.type = 'text'; icon.className = 'ti ti-eye-off'; }
    else { inp.type = 'password'; icon.className = 'ti ti-eye'; }
}
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('photoPreview').src = e.target.result;
            document.getElementById('photoPreview').classList.remove('d-none');
            document.getElementById('photoPlaceholder').classList.add('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
// Role card selector
document.querySelectorAll('.role-card').forEach(card => {
    const radio = card.querySelector('.role-radio');
    const updateCards = () => {
        document.querySelectorAll('.role-card').forEach(c => {
            const r = c.querySelector('.role-radio');
            const col = c.dataset.color;
            if (r.checked) {
                c.classList.add('active');
                c.style.borderColor = col;
                c.style.background = col + '10';
                c.style.setProperty('--active-color', col);
            } else {
                c.classList.remove('active');
                c.style.borderColor = '#dee2e6';
                c.style.background = '#f9fafb';
            }
        });
    };
    card.addEventListener('click', () => { radio.checked = true; updateCards(); });
    if (radio.checked) { updateCards(); }
});
</script>
@endpush

@endsection
