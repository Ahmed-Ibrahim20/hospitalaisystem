@if ($paginator->hasPages())
<nav class="pagination-wrapper" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
    <div class="pagination-info">
        <span class="pagination-text">
            عرض {{ $paginator->firstItem() }} إلى {{ $paginator->lastItem() }} من {{ $paginator->total() }} نتيجة
        </span>
    </div>

    <ul class="pagination pagination-custom">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
        <li class="page-item disabled" aria-disabled="true">
            <span class="page-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z" />
                </svg>
                السابق
            </span>
        </li>
        @else
        <li class="page-item">
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z" />
                </svg>
                السابق
            </a>
        </li>
        @endif

        {{-- Pagination Elements --}}
        @php
        $start = max($paginator->currentPage() - 2, 1);
        $end = min($start + 4, $paginator->lastPage());
        $start = max($end - 4, 1);
        @endphp

        {{-- First Page --}}
        @if ($start > 1)
        <li class="page-item">
            <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
        </li>
        @if ($start > 2)
        <li class="page-item disabled" aria-disabled="true">
            <span class="page-link">...</span>
        </li>
        @endif
        @endif

        {{-- Page Numbers --}}
        @for ($page = $start; $page <= $end; $page++)
            @if ($page==$paginator->currentPage())
            <li class="page-item active" aria-current="page">
                <span class="page-link">{{ $page }}</span>
            </li>
            @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
            </li>
            @endif
            @endfor

            {{-- Last Page --}}
            @if ($end < $paginator->lastPage())
                @if ($end < $paginator->lastPage() - 1)
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">...</span>
                    </li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
                    </li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                            التالي
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z" />
                            </svg>
                        </a>
                    </li>
                    @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">
                            التالي
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z" />
                            </svg>
                        </span>
                    </li>
                    @endif
    </ul>
</nav>

<style>
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 1rem 0;
    }

    .pagination-info {
        color: #6b7280;
        font-size: 0.875rem;
    }

    .pagination-text {
        font-weight: 500;
    }

    .pagination-custom {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 0.25rem;
    }

    .pagination-custom .page-item {
        display: flex;
    }

    .pagination-custom .page-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        color: #374151;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
        background: white;
    }

    .pagination-custom .page-link:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
        color: #1f2937;
        transform: translateY(-1px);
    }

    .pagination-custom .page-item.active .page-link {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        border-color: #3b82f6;
        color: white;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
    }

    .pagination-custom .page-item.disabled .page-link {
        background: #f9fafb;
        border-color: #e5e7eb;
        color: #9ca3af;
        cursor: not-allowed;
    }

    .pagination-custom .page-item.disabled .page-link:hover {
        transform: none;
        background: #f9fafb;
        border-color: #e5e7eb;
    }

    @media (max-width: 768px) {
        .pagination-wrapper {
            flex-direction: column;
            text-align: center;
        }

        .pagination-custom {
            justify-content: center;
            flex-wrap: wrap;
        }

        .pagination-custom .page-link {
            padding: 0.375rem 0.5rem;
            font-size: 0.875rem;
        }
    }
</style>
@endif