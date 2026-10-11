@if ($paginator->hasPages())
    <div class="pager" role="navigation" aria-label="Pagination">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="pager-btn pager-arrow is-disabled" aria-disabled="true">
                <i class="fa-solid fa-arrow-left"></i><span class="pager-label">Prev</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pager-btn pager-arrow">
                <i class="fa-solid fa-arrow-left"></i><span class="pager-label">Prev</span>
            </a>
        @endif

        {{-- Page numbers --}}
        <div class="pager-pages">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pager-dots">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pager-btn is-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pager-btn" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pager-btn pager-arrow">
                <span class="pager-label">Next</span><i class="fa-solid fa-arrow-right"></i>
            </a>
        @else
            <span class="pager-btn pager-arrow is-disabled" aria-disabled="true">
                <span class="pager-label">Next</span><i class="fa-solid fa-arrow-right"></i>
            </span>
        @endif

    </div>
@endif