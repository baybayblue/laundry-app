@if ($paginator->hasPages())
<nav aria-label="Navigasi Halaman">
    <ul class="pagination pagination-sm mb-0 gap-1">

        {{-- Previous Page --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link rounded-2 border-0 bg-light text-muted"
                    style="width:34px; height:34px; display:flex; align-items:center; justify-content:center;">
                    <i class="ti ti-chevron-left fs-6"></i>
                </span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link rounded-2 border-0 bg-light text-dark" href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    style="width:34px; height:34px; display:flex; align-items:center; justify-content:center; transition:.15s;">
                    <i class="ti ti-chevron-left fs-6"></i>
                </a>
            </li>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="page-item disabled">
                    <span class="page-link border-0 bg-transparent text-muted px-2">{{ $element }}</span>
                </li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active">
                            <span class="page-link rounded-2 border-0 fw-semibold text-white"
                                style="width:34px; height:34px; display:flex; align-items:center; justify-content:center; background: var(--bs-primary); box-shadow: 0 2px 8px rgba(var(--bs-primary-rgb),.35);">
                                {{ $page }}
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link rounded-2 border-0 bg-light text-dark fw-medium" href="{{ $url }}"
                                style="width:34px; height:34px; display:flex; align-items:center; justify-content:center; transition:.15s;"
                                onmouseover="this.style.background='#e9ecef'" onmouseout="this.style.background=''">
                                {{ $page }}
                            </a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link rounded-2 border-0 bg-light text-dark" href="{{ $paginator->nextPageUrl() }}" rel="next"
                    style="width:34px; height:34px; display:flex; align-items:center; justify-content:center; transition:.15s;">
                    <i class="ti ti-chevron-right fs-6"></i>
                </a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link rounded-2 border-0 bg-light text-muted"
                    style="width:34px; height:34px; display:flex; align-items:center; justify-content:center;">
                    <i class="ti ti-chevron-right fs-6"></i>
                </span>
            </li>
        @endif

    </ul>
</nav>
@endif
