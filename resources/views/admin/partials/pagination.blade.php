@if ($paginator->hasPages())
<nav aria-label="Page navigation" class="admin-pagination-container">
    <ul class="pagination admin-pagination justify-content-center">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link" aria-hidden="true"><i class="fas fa-chevron-left fa-sm"></i></span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                    <i class="fas fa-chevron-left fa-sm"></i>
                </a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @php
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();

            // Hiển thị trang đầu tiên
            if ($currentPage > 1) {
                echo '<li class="page-item"><a class="page-link" href="'.$paginator->url(1).'">1</a></li>';

                // Hiển thị dấu "..." nếu cần
                if ($currentPage > 3) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }

            // Hiển thị trang trước trang hiện tại
            if ($currentPage > 2) {
                echo '<li class="page-item"><a class="page-link" href="'.$paginator->url($currentPage - 1).'">'.($currentPage - 1).'</a></li>';
            }
        @endphp

        {{-- Trang hiện tại --}}
        <li class="page-item active" aria-current="page">
            <span class="page-link page-link-active">{{ $currentPage }}</span>
        </li>

        @php
            // Hiển thị trang sau trang hiện tại
            if ($currentPage < $lastPage - 1) {
                echo '<li class="page-item"><a class="page-link" href="'.$paginator->url($currentPage + 1).'">'.($currentPage + 1).'</a></li>';
            }

            // Hiển thị dấu "..." và trang cuối cùng
            if ($currentPage < $lastPage) {
                if ($currentPage < $lastPage - 2) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }

                if ($currentPage != $lastPage) {
                    echo '<li class="page-item"><a class="page-link" href="'.$paginator->url($lastPage).'">'.$lastPage.'</a></li>';
                }
            }
        @endphp

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                    <i class="fas fa-chevron-right fa-sm"></i>
                </a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link" aria-hidden="true"><i class="fas fa-chevron-right fa-sm"></i></span>
            </li>
        @endif
    </ul>
</nav>

<style>
/* CSS cô lập cho phân trang admin */
.admin-pagination-container {
    margin-top: 1rem;
}

.admin-pagination {
    display: flex;
    justify-content: center;
}

.admin-pagination .page-item {
    margin: 0 2px;
}

.admin-pagination .page-item .page-link {
    border-radius: 4px;
    padding: 0.4rem 0.75rem;
    color: #4e73df;
    border: 1px solid #dee2e6;
    background-color: #fff;
    font-size: 0.9rem;
    line-height: 1.25;
    text-align: center;
    transition: all 0.2s;
    display: block;
}

.admin-pagination .page-item.active .page-link {
    background-color: #4e73df !important;
    border-color: #4e73df !important;
    color: white !important;
    z-index: 3;
    display: block !important;
}

.admin-pagination .page-item .page-link:hover {
    background-color: #eaecf4;
    border-color: #dee2e6;
    color: #224abe;
}

.admin-pagination .page-item.disabled .page-link {
    color: #858796;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

.admin-pagination .page-link i.fa-sm {
    font-size: 0.7rem;
}

.admin-pagination .page-link-active {
    display: block !important;
}
</style>
@endif
