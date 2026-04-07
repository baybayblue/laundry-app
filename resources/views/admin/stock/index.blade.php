@extends('layouts.app')

@section('content')
{{-- PAGE HEADER --}}
<div class="row mb-4 align-items-center g-3">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #fd7e1420, #fd7e1410); border: 1px solid #fd7e1430; width:48px; height:48px;">
                <i class="ti ti-package fs-3 text-warning"></i>
            </div>
            <div>
                <h1 class="fs-3 mb-0 fw-bold">Stok Barang</h1>
                <p class="mb-0 text-muted small">Kelola persediaan bahan & perlengkapan laundry</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('admin.stock.create') }}" class="btn btn-warning d-inline-flex align-items-center gap-2 shadow-sm rounded-pill px-4 text-white">
            <i class="ti ti-plus fs-5"></i> Tambah Barang
        </a>
    </div>
</div>

{{-- STATS CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #0d6efd15, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Total Item</span>
                    <div class="bg-primary bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-packages text-primary fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-primary">{{ $totalItems }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #19875415, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Stok Aman</span>
                    <div class="bg-success bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-circle-check text-success fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-success">{{ $totalItems - $lowStockCount - $emptyCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #fd7e1415, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Stok Menipis</span>
                    <div class="bg-warning bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-alert-triangle text-warning fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-warning">{{ $lowStockCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #dc354515, #fff);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Stok Habis</span>
                    <div class="bg-danger bg-opacity-10 rounded-2 p-1">
                        <i class="ti ti-x text-danger fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-danger">{{ $emptyCount }}</div>
            </div>
        </div>
    </div>
</div>

{{-- SEARCH & FILTER BAR --}}
<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.stock.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama barang atau supplier..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="category" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Menipis</option>
                    <option value="empty" {{ request('status') == 'empty' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-warning btn-sm text-white flex-fill rounded-pill">
                    <i class="ti ti-filter me-1"></i>Filter
                </button>
                @if(request()->hasAny(['search','category','status']))
                <a href="{{ route('admin.stock.index') }}" class="btn btn-light btn-sm rounded-pill" title="Reset">
                    <i class="ti ti-x"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- DATA TABLE --}}
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr style="background: linear-gradient(90deg, #fffbf0, #fff8e8); border-bottom: 2px solid #e9ecef;">
                        <th class="py-3 ps-4 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px; width:50px;">No</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Barang</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Kategori</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase text-center" style="letter-spacing:.5px;">Stok</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Harga/Satuan</th>
                        <th class="py-3 text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Supplier</th>
                        <th class="py-3 pe-4 text-end text-muted fw-semibold small text-uppercase" style="letter-spacing:.5px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                    @php
                        $status = $item->stockStatus();
                        $statusConfig = [
                            'aman'    => ['text' => 'Aman',    'class' => 'success', 'icon' => 'ti-circle-check'],
                            'menipis' => ['text' => 'Menipis', 'class' => 'warning', 'icon' => 'ti-alert-triangle'],
                            'habis'   => ['text' => 'Habis',   'class' => 'danger',  'icon' => 'ti-x'],
                        ][$status];
                    @endphp
                    <tr class="stock-row" style="transition: background .15s;">
                        <td class="ps-4 text-muted fw-medium small">{{ $items->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($item->photo)
                                    <img src="{{ asset('storage/' . $item->photo) }}" alt="{{ $item->name }}"
                                        style="width:40px; height:40px; object-fit:cover;"
                                        class="rounded-2 shadow-sm border">
                                @else
                                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width:40px; height:40px; background: linear-gradient(135deg, #fd7e14, #ffc107);">
                                        <i class="ti ti-package text-white"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $item->name }}</h6>
                                    <small class="text-muted">Satuan: {{ $item->unit }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($item->category)
                            <span class="badge rounded-pill px-3 text-white"
                                style="background: {{ $item->category->color ?? '#6c757d' }};">
                                <i class="ti {{ $item->category->icon ?? 'ti-tags' }} me-1" style="font-size:.7rem;"></i>
                                {{ $item->category->name }}
                            </span>
                            @else
                            <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center">
                                <span class="fw-bold fs-5 {{ $status === 'habis' ? 'text-danger' : ($status === 'menipis' ? 'text-warning' : 'text-dark') }}">
                                    {{ $item->stock }}
                                </span>
                                <span class="badge rounded-pill px-2 small bg-{{ $statusConfig['class'] }} bg-opacity-10 text-{{ $statusConfig['class'] }} border border-{{ $statusConfig['class'] }} border-opacity-25">
                                    <i class="ti {{ $statusConfig['icon'] }} me-1"></i>{{ $statusConfig['text'] }}
                                </span>
                                <small class="text-muted" style="font-size:.7rem;">min. {{ $item->min_stock }}</small>
                            </div>
                        </td>
                        <td>
                            @if($item->price_per_unit)
                                <span class="fw-medium text-dark small">Rp {{ number_format($item->price_per_unit, 0, ',', '.') }}</span>
                                <small class="text-muted d-block">/ {{ $item->unit }}</small>
                            @else
                                <small class="text-muted fst-italic">-</small>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $item->supplier ?? '-' }}</small>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                {{-- Tombol Adjust Stok --}}
                                <button type="button"
                                    class="btn btn-sm btn-icon rounded-2 btn-adjust"
                                    style="background:#19875415; color:#198754; width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center;"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-stock="{{ $item->stock }}"
                                    data-unit="{{ $item->unit }}"
                                    title="Ubah Stok">
                                    <i class="ti ti-transfer-in fs-6"></i>
                                </button>
                                {{-- Tombol Detail --}}
                                <a href="{{ route('admin.stock.show', $item) }}"
                                    class="btn btn-sm btn-icon rounded-2"
                                    style="background:#0d6efd15; color:#0d6efd; width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center;"
                                    data-bs-toggle="tooltip" title="Riwayat Stok">
                                    <i class="ti ti-history fs-6"></i>
                                </a>
                                {{-- Tombol Edit --}}
                                <a href="{{ route('admin.stock.edit', $item) }}"
                                    class="btn btn-sm btn-icon rounded-2"
                                    style="background:#fd7e1415; color:#fd7e14; width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center;"
                                    data-bs-toggle="tooltip" title="Edit Barang">
                                    <i class="ti ti-edit fs-6"></i>
                                </a>
                                {{-- Tombol Hapus --}}
                                <form action="{{ route('admin.stock.destroy', $item) }}" method="POST" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="btn btn-sm btn-icon rounded-2 btn-delete"
                                        style="background:#dc354515; color:#dc3545; width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center;"
                                        data-name="{{ $item->name }}" title="Hapus">
                                        <i class="ti ti-trash fs-6"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle"
                                    style="width:72px; height:72px; background: linear-gradient(135deg, #e9ecef, #f8f9fa);">
                                    <i class="ti ti-package fs-1 text-muted"></i>
                                </div>
                                <h5 class="fw-semibold text-muted mb-1">
                                    {{ request()->hasAny(['search','category','status']) ? 'Tidak Ada Hasil' : 'Belum Ada Barang' }}
                                </h5>
                                <p class="small text-muted mb-3">
                                    {{ request()->hasAny(['search','category','status']) ? 'Coba ubah filter pencarian Anda.' : 'Tambahkan barang pertama ke dalam daftar stok.' }}
                                </p>
                                @if(request()->hasAny(['search','category','status']))
                                    <a href="{{ route('admin.stock.index') }}" class="btn btn-light btn-sm rounded-pill px-4">
                                        <i class="ti ti-x me-1"></i>Reset Filter
                                    </a>
                                @else
                                    <a href="{{ route('admin.stock.create') }}" class="btn btn-warning btn-sm rounded-pill px-4 text-white">
                                        <i class="ti ti-plus me-1"></i>Tambah Barang
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION FOOTER --}}
    @if($items->hasPages())
    <div class="card-footer border-top-0 bg-transparent px-4 py-3">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
            <div class="text-muted small">
                Menampilkan <span class="fw-semibold text-dark">{{ $items->firstItem() }}</span>
                – <span class="fw-semibold text-dark">{{ $items->lastItem() }}</span>
                dari <span class="fw-semibold text-dark">{{ $items->total() }}</span> barang
            </div>
            <div>{{ $items->appends(request()->query())->links('vendor.pagination.custom') }}</div>
        </div>
    </div>
    @else
    <div class="card-footer border-top-0 bg-transparent px-4 py-2">
        <p class="text-muted small mb-0">Menampilkan <span class="fw-semibold text-dark">{{ $items->count() }}</span> barang</p>
    </div>
    @endif
</div>

{{-- MODAL ADJUST STOK --}}
<div class="modal fade" id="adjustModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold mb-0">Penyesuaian Stok</h5>
                    <p class="text-muted small mb-0" id="adjustSubtitle">Tambah atau kurangi stok barang</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="adjustForm" method="POST">
                @csrf
                <div class="modal-body px-4 py-3">
                    {{-- Stok saat ini --}}
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-4" style="background:#f8f9fa;">
                        <i class="ti ti-package text-muted fs-3"></i>
                        <div>
                            <div class="small text-muted">Stok Saat Ini</div>
                            <div class="fs-4 fw-bold text-dark" id="adjustCurrentStock">0</div>
                            <div class="small text-muted" id="adjustUnit"></div>
                        </div>
                    </div>

                    {{-- Jenis perubahan --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium small">Jenis Perubahan <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="type" id="typeIn" value="in" checked>
                                <label class="btn btn-outline-success w-100 rounded-3 d-flex align-items-center justify-content-center gap-2 py-3 fw-semibold" for="typeIn">
                                    <i class="ti ti-arrow-down-circle fs-5"></i> Stok Masuk
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="type" id="typeOut" value="out">
                                <label class="btn btn-outline-danger w-100 rounded-3 d-flex align-items-center justify-content-center gap-2 py-3 fw-semibold" for="typeOut">
                                    <i class="ti ti-arrow-up-circle fs-5"></i> Stok Keluar
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Jumlah --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium small" for="adjustQty">Jumlah <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-hash text-muted"></i></span>
                            <input type="number" class="form-control border-start-0" id="adjustQty" name="quantity" min="1" placeholder="Masukkan jumlah..." required>
                            <span class="input-group-text bg-light" id="adjustUnitLabel"></span>
                        </div>
                    </div>

                    {{-- Preview stok baru --}}
                    <div class="d-flex align-items-center gap-2 p-3 rounded-3 mb-3" style="background:#f0f4ff;" id="stockPreview">
                        <i class="ti ti-calculator text-primary fs-5"></i>
                        <div class="small">
                            Estimasi stok setelah perubahan: <span class="fw-bold text-primary" id="stockAfterPreview">—</span>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div class="mb-1">
                        <label class="form-label fw-medium small" for="adjustNote">Catatan <span class="text-muted fw-normal">(Opsional)</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 pt-2" style="align-items:flex-start;"><i class="ti ti-notes text-muted"></i></span>
                            <textarea class="form-control border-start-0" id="adjustNote" name="note" rows="2" placeholder="Misal: Restok dari Supplier A, pemakaian mingguan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-semibold" id="btnAdjustSubmit">
                        <i class="ti ti-check me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tooltips — hanya untuk elemen yang BUKAN tombol adjust
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el =>
        new bootstrap.Tooltip(el, { trigger: 'hover' })
    );

    // Row hover
    document.querySelectorAll('.stock-row').forEach(row => {
        row.addEventListener('mouseenter', () => row.style.backgroundColor = '#fffbf0');
        row.addEventListener('mouseleave', () => row.style.backgroundColor = '');
    });

    // ── MODAL UBAH STOK ──
    const modalEl = document.getElementById('adjustModal');
    const adjustModal = new bootstrap.Modal(modalEl);
    let currentStock = 0;

    // Tambahkan CSRF token ke form setiap kali modal dibuka
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const adjustForm = document.getElementById('adjustForm');

    // Pastikan form punya _token
    let tokenInput = adjustForm.querySelector('input[name="_token"]');
    if (!tokenInput) {
        tokenInput = document.createElement('input');
        tokenInput.type  = 'hidden';
        tokenInput.name  = '_token';
        adjustForm.appendChild(tokenInput);
    }
    if (csrfToken) tokenInput.value = csrfToken;

    document.querySelectorAll('.btn-adjust').forEach(btn => {
        btn.addEventListener('click', function () {
            const id    = this.dataset.id;
            const name  = this.dataset.name;
            const stock = parseInt(this.dataset.stock);
            const unit  = this.dataset.unit;

            currentStock = stock;

            document.getElementById('adjustSubtitle').textContent       = name;
            document.getElementById('adjustCurrentStock').textContent    = stock;
            document.getElementById('adjustUnit').textContent            = unit;
            document.getElementById('adjustUnitLabel').textContent       = unit;
            document.getElementById('adjustQty').value                   = '';
            document.getElementById('adjustNote').value                  = '';
            document.getElementById('stockAfterPreview').textContent     = '—';
            document.getElementById('stockAfterPreview').className       = 'fw-bold text-primary';

            // Set action URL ke endpoint adjust
            adjustForm.action = `/admin/stock/${id}/adjust`;

            // Reset pilihan ke Stok Masuk
            document.getElementById('typeIn').checked = true;

            adjustModal.show();
        });
    });

    // ── PREVIEW ESTIMASI STOK ──
    function updatePreview() {
        const qty  = parseInt(document.getElementById('adjustQty').value) || 0;
        const type = document.querySelector('input[name="type"]:checked')?.value;
        const el   = document.getElementById('stockAfterPreview');

        if (!qty) { el.textContent = '—'; el.className = 'fw-bold text-primary'; return; }

        const after = type === 'in' ? currentStock + qty : currentStock - qty;
        el.textContent = after;
        el.className   = 'fw-bold ' + (after < 0 ? 'text-danger' : after === 0 ? 'text-warning' : 'text-success');
    }

    document.getElementById('adjustQty').addEventListener('input', updatePreview);
    document.querySelectorAll('input[name="type"]').forEach(r => r.addEventListener('change', updatePreview));

    // ── LOADING STATE SAAT SUBMIT ──
    adjustForm.addEventListener('submit', function () {
        const btn = document.getElementById('btnAdjustSubmit');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    });

    // ── KONFIRMASI HAPUS ──
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            const name = this.dataset.name;
            const form = this.closest('form');
            Swal.fire({
                title: 'Hapus Barang?',
                html: `Barang <strong>${name}</strong> beserta seluruh riwayat stoknya akan dihapus permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="ti ti-trash me-1"></i>Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
            }).then(result => { if (result.isConfirmed) form.submit(); });
        });
    });
});
</script>
@endpush
@endsection
