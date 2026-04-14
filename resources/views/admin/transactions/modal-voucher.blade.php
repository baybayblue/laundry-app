<div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" style="max-width:520px;">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">

            {{-- Modal Header --}}
            <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#2563eb,#3b82f6);">
                <div class="d-flex align-items-center gap-3 py-1">
                    <div class="rounded-3 bg-white bg-opacity-20 p-2">
                        <i class="ti ti-ticket text-white fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="voucherModalLabel">Pilih Voucher Promo</h5>
                        <div class="text-white opacity-75" style="font-size:.75rem;">{{ $discounts->count() }} voucher tersedia untuk Anda</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- Subtotal hint --}}
            <div class="px-4 py-2 d-flex align-items-center gap-2" style="background:#f0f4ff; border-bottom:1px solid #dde7ff;">
                <i class="ti ti-info-circle text-primary"></i>
                <span class="small text-muted">Total belanja saat ini: <strong id="modalSubtotalHint" class="text-dark">Rp 0</strong></span>
            </div>

            <div class="modal-body p-4">
                @if($discounts->count())
                <div class="d-flex flex-column gap-3" id="voucherCardList">
                    @foreach($discounts as $disc)
                    <div class="voucher-card rounded-3 overflow-hidden position-relative"
                        style="border:1.5px solid #e9ecef; cursor:pointer; transition:all .2s;"
                        data-id="{{ $disc->id }}"
                        data-code="{{ $disc->code }}"
                        data-name="{{ $disc->name }}"
                        data-type="{{ $disc->type }}"
                        data-value="{{ $disc->value }}"
                        data-min="{{ $disc->min_transaction ?? 0 }}"
                        data-max="{{ $disc->max_discount ?? 0 }}"
                        data-formatted="{{ $disc->formatted_value }}"
                        onclick="selectVoucher(this)">

                        {{-- Left accent bar --}}
                        <div style="position:absolute;left:0;top:0;bottom:0;width:5px;background:linear-gradient(180deg,#2563eb,#3b82f6);"></div>

                        <div class="ps-4 pe-3 py-3">
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        {{-- Value badge (big, prominent) --}}
                                        <span class="fw-black text-white rounded-2 px-2 py-1"
                                            style="background:linear-gradient(135deg,#2563eb,#3b82f6);font-size:.9rem;letter-spacing:-.3px;">
                                            {{ $disc->type === 'percentage' ? 'DISKON ' . intval($disc->value) . '%' : 'HEMAT Rp ' . number_format($disc->value, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="fw-bold" style="font-size:.9rem;">{{ $disc->name }}</div>
                                    @if($disc->description)
                                    <div class="text-muted small">{{ $disc->description }}</div>
                                    @endif

                                    {{-- Conditions row --}}
                                    <div class="d-flex flex-wrap gap-2 mt-2" style="font-size:.72rem;">
                                        @if($disc->min_transaction > 0)
                                        <span class="d-flex align-items-center gap-1 text-muted">
                                            <i class="ti ti-shopping-cart" style="font-size:.75rem;"></i>
                                            Min. Rp {{ number_format($disc->min_transaction, 0, ',', '.') }}
                                        </span>
                                        @endif
                                        @if($disc->type === 'percentage' && $disc->max_discount > 0)
                                        <span class="d-flex align-items-center gap-1 text-muted">
                                            <i class="ti ti-arrow-bar-down" style="font-size:.75rem;"></i>
                                            Maks Rp {{ number_format($disc->max_discount, 0, ',', '.') }}
                                        </span>
                                        @endif
                                        @if($disc->end_date)
                                        <span class="d-flex align-items-center gap-1 text-muted">
                                            <i class="ti ti-clock" style="font-size:.75rem;"></i>
                                            s/d {{ $disc->end_date->format('d M Y') }}
                                        </span>
                                        @endif
                                        @if($disc->usage_limit)
                                        <span class="d-flex align-items-center gap-1 text-muted">
                                            <i class="ti ti-stack" style="font-size:.75rem;"></i>
                                            Sisa {{ $disc->usage_limit - $disc->usage_count }}x
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Selected indicator --}}
                                <div class="voucher-check-icon d-none flex-shrink-0 mt-1">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                        style="width:26px;height:26px;background:#198754;">
                                        <i class="ti ti-check text-white" style="font-size:.75rem;"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Code pill --}}
                            <div class="mt-2">
                                <span class="badge rounded-pill border px-2 py-1 font-monospace"
                                    style="background:#fff;color:#6c757d;border-color:#dee2e6!important;font-size:.68rem;"
                                    title="Kode voucher">
                                    <i class="ti ti-barcode me-1"></i>{{ $disc->code }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                {{-- Empty state --}}
                <div class="text-center py-5">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width:72px;height:72px;background:#f8f9fa;">
                        <i class="ti ti-ticket-off text-muted" style="font-size:2rem;"></i>
                    </div>
                    <div class="fw-semibold text-dark mb-1">Tidak Ada Voucher Aktif</div>
                    <div class="text-muted small">Belum ada promo yang tersedia saat ini.</div>
                </div>
                @endif
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold" id="btnConfirmVoucher" onclick="confirmVoucher()" disabled>
                    <i class="ti ti-check me-1"></i>Pakai Voucher Ini
                </button>
            </div>
        </div>
    </div>
</div>
