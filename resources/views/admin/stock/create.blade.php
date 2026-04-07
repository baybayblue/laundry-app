@extends('layouts.app')

@section('content')
{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #fd7e1420, #fd7e1410); border: 1px solid #fd7e1430; width:48px; height:48px;">
                <i class="ti ti-plus fs-3 text-warning"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Tambah Barang Stok</h1>
                <p class="mb-0 text-muted small">Daftarkan bahan atau perlengkapan laundry baru</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('admin.stock.index') }}" class="btn btn-light d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- VALIDATION ALERT --}}
@if($errors->any())
<div class="alert border-0 rounded-4 shadow-sm mb-4 d-flex align-items-start gap-3" style="background:#dc354510; border-left: 4px solid #dc3545 !important;">
    <i class="ti ti-alert-circle text-danger fs-4 mt-1 flex-shrink-0"></i>
    <div>
        <div class="fw-semibold text-danger mb-1">Terdapat {{ $errors->count() }} kesalahan:</div>
        <ul class="mb-0 small text-danger ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <form action="{{ route('admin.stock.store') }}" method="POST" enctype="multipart/form-data" id="stockForm" novalidate>
            @csrf

            {{-- SECTION: Info Barang --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-1 bg-warning bg-opacity-10">
                            <i class="ti ti-package text-warning"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Informasi Barang</h6>
                    </div>
                    <hr class="mt-3 mb-0">
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium small" for="name">Nama Barang <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-tag text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}"
                                    placeholder="Contoh: Sabun Attack 1kg, Pewangi So Klin..." required maxlength="255">
                                @error('name') <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="category_id">Kategori <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <div class="input-group flex-fill">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-tags text-muted"></i></span>
                                    <select class="form-select border-start-0 @error('category_id') is-invalid @enderror"
                                        id="category_id" name="category_id" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <button type="button" class="btn btn-outline-secondary rounded-2 flex-shrink-0 px-3"
                                    id="btnQuickAddCat" title="Tambah kategori baru">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>
                            <div class="form-text">Atau <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="btnQuickAddCat2">+ buat kategori baru</button></div>
                        </div>


                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="unit">Satuan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-ruler text-muted"></i></span>
                                <input type="text" list="unitList" class="form-control border-start-0 @error('unit') is-invalid @enderror"
                                    id="unit" name="unit" value="{{ old('unit') }}"
                                    placeholder="Contoh: pcs, kg, liter..." required>
                                @error('unit') <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                            </div>
                            <datalist id="unitList">
                                <option value="pcs">
                                <option value="kg">
                                <option value="gram">
                                <option value="liter">
                                <option value="ml">
                                <option value="lusin">
                                <option value="dus">
                                <option value="pak">
                                <option value="karung">
                            </datalist>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="supplier">Supplier <span class="text-muted fw-normal">(Opsional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-truck text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 @error('supplier') is-invalid @enderror"
                                    id="supplier" name="supplier" value="{{ old('supplier') }}"
                                    placeholder="Nama supplier atau toko..." maxlength="255">
                                @error('supplier') <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="price_per_unit">Harga per Satuan <span class="text-muted fw-normal">(Opsional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 fw-semibold text-muted small">Rp</span>
                                <input type="number" class="form-control border-start-0 @error('price_per_unit') is-invalid @enderror"
                                    id="price_per_unit" name="price_per_unit" value="{{ old('price_per_unit') }}"
                                    placeholder="0" min="0" step="100">
                                @error('price_per_unit') <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium small" for="description">Keterangan <span class="text-muted fw-normal">(Opsional)</span></label>
                            <div class="input-group align-items-start">
                                <span class="input-group-text bg-light border-end-0 pt-2"><i class="ti ti-notes text-muted"></i></span>
                                <textarea class="form-control border-start-0 @error('description') is-invalid @enderror"
                                    id="description" name="description" rows="2"
                                    placeholder="Keterangan tambahan tentang barang ini...">{{ old('description') }}</textarea>
                                @error('description') <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION: Stok --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-1 bg-success bg-opacity-10">
                            <i class="ti ti-chart-bar text-success"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Pengaturan Stok</h6>
                    </div>
                    <hr class="mt-3 mb-0">
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="stock">Stok Awal <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-hash text-muted"></i></span>
                                <input type="number" class="form-control border-start-0 @error('stock') is-invalid @enderror"
                                    id="stock" name="stock" value="{{ old('stock', 0) }}"
                                    min="0" required>
                                @error('stock') <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                            </div>
                            <div class="form-text">Jumlah stok yang tersedia saat ini</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small" for="min_stock">Batas Minimum Stok <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-alert-triangle text-warning"></i></span>
                                <input type="number" class="form-control border-start-0 @error('min_stock') is-invalid @enderror"
                                    id="min_stock" name="min_stock" value="{{ old('min_stock', 5) }}"
                                    min="0" required>
                                @error('min_stock') <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                            </div>
                            <div class="form-text">Sistem akan menandai barang sebagai "Menipis" di bawah angka ini</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION: Foto --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-1 bg-info bg-opacity-10">
                            <i class="ti ti-camera text-info"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold">Foto Barang</h6>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill small fw-normal">Opsional</span>
                    </div>
                    <hr class="mt-3 mb-0">
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-4 flex-wrap">
                        <div class="text-center flex-shrink-0">
                            <div id="photoPreviewWrap" class="rounded-3 overflow-hidden d-flex align-items-center justify-content-center mx-auto"
                                style="width:100px; height:100px; background:#f8f9fa; border: 2px dashed #dee2e6;">
                                <div id="photoPlaceholder">
                                    <i class="ti ti-package text-muted opacity-25 fs-1"></i>
                                </div>
                                <img id="photoPreview" src="#" alt="" class="d-none w-100 h-100" style="object-fit:cover;">
                            </div>
                            <small class="text-muted d-block mt-2">Preview</small>
                        </div>
                        <div class="flex-fill">
                            <label class="form-label fw-medium small" for="photo">Upload Foto</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror"
                                id="photo" name="photo" accept="image/png,image/jpeg,image/jpg">
                            <div class="form-text">Format: JPG, PNG. Ukuran maks: 2MB.</div>
                            @error('photo') <div class="invalid-feedback"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="d-flex justify-content-end gap-3">
                <a href="{{ route('admin.stock.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                <button type="reset" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="ti ti-refresh me-1"></i>Reset
                </button>
                <button type="submit" class="btn btn-warning text-white rounded-pill px-5 fw-semibold shadow-sm" id="btnSubmit">
                    <span class="btn-text"><i class="ti ti-check me-1"></i>Simpan Barang</span>
                    <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>

    {{-- SIDEBAR --}}
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-3" style="background: linear-gradient(135deg, #fd7e1408, #fff);">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3 d-flex align-items-center gap-2 text-warning">
                    <i class="ti ti-bulb"></i> Panduan Pengisian
                </h6>
                <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-3">
                    <li class="d-flex align-items-start gap-2">
                        <i class="ti ti-circle-check text-success mt-1 flex-shrink-0"></i>
                        <span><strong>Nama Barang</strong> tuliskan spesifik termasuk merek/ukuran. Misal: "Sabun Attack 1kg".</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="ti ti-circle-check text-success mt-1 flex-shrink-0"></i>
                        <span><strong>Kategori</strong> membantu pengelompokan dan filter barang. Buat kategori baru sesuai kebutuhan.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="ti ti-circle-check text-success mt-1 flex-shrink-0"></i>
                        <span><strong>Batas Minimum</strong> adalah angka yang memicu notifikasi "Stok Menipis". Atur sesuai kebutuhan operasional harian.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="ti ti-circle-check text-success mt-1 flex-shrink-0"></i>
                        <span>Setiap perubahan stok nantinya bisa dilacak melalui <strong>Riwayat Stok</strong>.</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #0d6efd08, #fff);">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3 d-flex align-items-center gap-2 text-primary">
                    <i class="ti ti-list"></i> Contoh Barang Laundry
                </h6>
                <div class="d-flex flex-column gap-2 small">
                    @php
                        $examples = [
                            ['icon' => 'ti-droplet', 'color' => '#0d6efd', 'name' => 'Sabun / Deterjen', 'desc' => 'Rinso, Attack, So Klin Bubuk'],
                            ['icon' => 'ti-wind', 'color' => '#198754', 'name' => 'Pewangi & Pelembut', 'desc' => 'Molto, Downy, Nuklir'],
                            ['icon' => 'ti-star', 'color' => '#fd7e14', 'name' => 'Pemutih', 'desc' => 'Bayclin, So Klin Pemutih'],
                            ['icon' => 'ti-package', 'color' => '#6f42c1', 'name' => 'Plastik & Kemasan', 'desc' => 'Kantong plastik, hanger, tag'],
                        ];
                    @endphp
                    @foreach($examples as $ex)
                    <div class="d-flex align-items-center gap-2 p-2 rounded-2" style="background:{{ $ex['color'] }}10;">
                        <i class="ti {{ $ex['icon'] }}" style="color:{{ $ex['color'] }};"></i>
                        <div>
                            <div class="fw-semibold text-dark" style="font-size:.8rem;">{{ $ex['name'] }}</div>
                            <div class="text-muted" style="font-size:.73rem;">{{ $ex['desc'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL QUICK-ADD KATEGORI --}}
<div class="modal fade" id="quickAddCatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h6 class="modal-title fw-bold">Tambah Kategori Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="mb-3">
                    <label class="form-label fw-medium small" for="qaCatName">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="qaCatName" placeholder="Misal: Deterjen, Pewangi..." maxlength="100">
                    <div class="text-danger small mt-1 d-none" id="qaCatError"></div>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-medium small" for="qaCatColor">Warna</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="color" class="form-control form-control-color flex-shrink-0" id="qaCatColor" value="#6f42c1" style="width:40px;height:34px;padding:2px;">
                        <div class="d-flex flex-wrap gap-1">
                            @foreach(['#0d6efd','#6f42c1','#20c997','#fd7e14','#198754','#0dcaf0','#dc3545','#ffc107'] as $p)
                            <button type="button" class="border-0 rounded-1 qa-color-preset"
                                style="width:20px;height:20px;background:{{ $p }};cursor:pointer;" data-color="{{ $p }}"></button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn rounded-pill px-4 fw-semibold text-white" id="btnSaveQuickCat"
                    style="background:linear-gradient(135deg,#6f42c1,#8b5cf6);">
                    <span class="btn-qa-text"><i class="ti ti-plus me-1"></i>Tambah</span>
                    <span class="btn-qa-loading d-none"><span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Photo preview
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    const photoPlaceholder = document.getElementById('photoPlaceholder');

    photoInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({ icon: 'error', title: 'File Terlalu Besar', text: 'Maksimal 2MB.', confirmButtonColor: '#fd7e14' });
            this.value = ''; return;
        }
        const reader = new FileReader();
        reader.onload = e => {
            photoPreview.src = e.target.result;
            photoPreview.classList.remove('d-none');
            photoPlaceholder.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    });

    // Loading state
    const form = document.getElementById('stockForm');
    const btnSubmit = document.getElementById('btnSubmit');
    form.addEventListener('submit', function () {
        if (!form.checkValidity()) return;
        btnSubmit.disabled = true;
        btnSubmit.querySelector('.btn-text').classList.add('d-none');
        btnSubmit.querySelector('.btn-loading').classList.remove('d-none');
    });

    // ── QUICK ADD CATEGORY MODAL ──
    const qaModal   = new bootstrap.Modal(document.getElementById('quickAddCatModal'));
    const catSelect = document.getElementById('category_id');
    const csrf      = document.querySelector('meta[name="csrf-token"]')?.content;

    function openQaModal() { document.getElementById('qaCatName').value = ''; document.getElementById('qaCatError').classList.add('d-none'); qaModal.show(); }
    document.getElementById('btnQuickAddCat')?.addEventListener('click', openQaModal);
    document.getElementById('btnQuickAddCat2')?.addEventListener('click', openQaModal);

    // Color presets
    document.querySelectorAll('.qa-color-preset').forEach(btn =>
        btn.addEventListener('click', () => document.getElementById('qaCatColor').value = btn.dataset.color)
    );

    // Save quick category via AJAX
    document.getElementById('btnSaveQuickCat').addEventListener('click', async function () {
        const name  = document.getElementById('qaCatName').value.trim();
        const color = document.getElementById('qaCatColor').value;
        const errEl = document.getElementById('qaCatError');
        const btn   = this;

        if (!name) { errEl.textContent = 'Nama kategori wajib diisi.'; errEl.classList.remove('d-none'); return; }

        btn.disabled = true;
        btn.querySelector('.btn-qa-text').classList.add('d-none');
        btn.querySelector('.btn-qa-loading').classList.remove('d-none');

        try {
            const res = await fetch('{{ route("admin.categories.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ name, color, icon: 'ti-category', description: '' }),
            });
            const data = await res.json();

            if (!res.ok) {
                const msg = data.errors?.name?.[0] || data.message || 'Gagal menyimpan kategori.';
                errEl.textContent = msg; errEl.classList.remove('d-none');
            } else {
                // Rebuild dropdown
                catSelect.innerHTML = '<option value="">-- Pilih Kategori --</option>';
                data.categories.forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value   = cat.id;
                    opt.text    = cat.name;
                    opt.dataset.color = cat.color;
                    opt.dataset.icon  = cat.icon;
                    if (cat.name === name) opt.selected = true;
                    catSelect.appendChild(opt);
                });
                qaModal.hide();
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false });
            }
        } catch (e) {
            errEl.textContent = 'Terjadi kesalahan jaringan.'; errEl.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            btn.querySelector('.btn-qa-text').classList.remove('d-none');
            btn.querySelector('.btn-qa-loading').classList.add('d-none');
        }
    });
});
</script>
@endpush
@endsection
