@extends('customer.layouts.app')

@section('title', 'Buat Pesanan Laundry')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="font-size: .75rem;">
                <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Buat Pesanan</li>
            </ol>
        </nav>
        <h1 class="fs-4 mb-2 fw-bold text-dark">Buat Pesanan Laundry</h1>
        <p class="text-muted mb-0 small">
            Isi formulir di bawah untuk mengajukan penjemputan cucian Anda.
        </p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('customer.orders.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <!-- INFO ALERT -->
                        <div class="col-12">
                            <div class="alert alert-primary border-0 rounded-4 d-flex align-items-start gap-3 p-4" style="background: rgba(13, 110, 253, 0.05);">
                                <div class="bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center mt-1 flex-shrink-0" style="width:32px;height:32px;">
                                    <i class="ti ti-info-circle text-white"></i>
                                </div>
                                <div class="small">
                                    <h6 class="fw-bold mb-1 text-primary">Info Penjemputan</h6>
                                    <p class="text-muted mb-0 lh-base">Tim kami akan menjemput ke lokasi sesuai jadwal. Total tagihan akan dihitung setelah pengecekan berat dan jenis layanan oleh admin kami.</p>
                                </div>
                            </div>
                        </div>

                        <!-- PICKUP DATE -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small">Tanggal Penjemputan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-4"><i class="ti ti-calendar-event text-muted"></i></span>
                                <input type="date" name="pickup_date" class="form-control bg-light border-start-0 rounded-end-4 py-3 @error('pickup_date') is-invalid @enderror" 
                                       value="{{ old('pickup_date', now()->format('Y-m-d')) }}" required min="{{ now()->format('Y-m-d') }}">
                            </div>
                            @error('pickup_date')
                                <div class="invalid-feedback d-block small mt-1 ps-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ADDRESS -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small">Alamat Lengkap Penjemputan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-4 align-items-start pt-3"><i class="ti ti-map-pin text-muted"></i></span>
                                <textarea name="address" rows="3" class="form-control bg-light border-start-0 rounded-end-4 py-3 @error('address') is-invalid @enderror" 
                                          placeholder="Jl. Nama Jalan No. XX, Kelurahan, Kec..." required>{{ old('address', Auth::guard('customer')->user()->address) }}</textarea>
                            </div>
                            @error('address')
                                <div class="invalid-feedback d-block small mt-1 ps-2">{{ $message }}</div>
                            @enderror
                            <div class="form-text small opacity-75 ps-1 mt-2">Pastikan alamat akurat agar petugas kami mudah menemukan lokasi Anda.</div>
                        </div>

                        <!-- NOTES -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small">Catatan Tambahan (Opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-4 align-items-start pt-3"><i class="ti ti-notes text-muted"></i></span>
                                <textarea name="notes" rows="3" class="form-control bg-light border-start-0 rounded-end-4 py-3 @error('notes') is-invalid @enderror" 
                                          placeholder="Contoh: Titip di satpam, jemput sebelum jam 12, dll...">{{ old('notes') }}</textarea>
                            </div>
                            @error('notes')
                                <div class="invalid-feedback d-block small mt-1 ps-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- SUBMIT BUTTON -->
                        <div class="col-12 pt-3">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary rounded-pill py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                    <i class="ti ti-check fs-5"></i> Konfirmasi Pesanan
                                </button>
                            </div>
                            <div class="text-center mt-4">
                                <a href="{{ route('customer.dashboard') }}" class="text-muted small fw-medium text-decoration-none hover-text-primary transition-all">
                                    <i class="ti ti-arrow-left me-1"></i> Batal dan Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center pb-5 mt-2">
            <p class="text-muted small mb-3">Butuh bantuan langsung?</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="#" class="btn btn-sm btn-light border rounded-pill px-3 py-2 text-muted fw-bold small">
                    <i class="ti ti-brand-whatsapp text-success me-1"></i> WhatsApp CS
                </a>
                <a href="#" class="btn btn-sm btn-light border rounded-pill px-3 py-2 text-muted fw-bold small">
                    <i class="ti ti-help-circle text-primary me-1"></i> Panduan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

