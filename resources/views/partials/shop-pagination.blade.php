{{--
    Tampilan paginasi halaman Shop.
    Simpan di: resources/views/partials/shop-pagination.blade.php
    Dipanggil dengan: {{ $products->links('partials.shop-pagination') }}
    Laravel sendiri yang menyiapkan $paginator dan $elements (nomor halaman + "..."),
    jadi tidak perlu hitung apa pun di sini. Warnanya ada di public/css/shop.css.
--}}
@if ($paginator->hasPages())
    <nav aria-label="Page navigation" class="shop-pagination">
        <ul class="pagination justify-content-center flex-wrap mb-0 align-items-center">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link rounded-3 border text-muted px-3 py-2 mx-1 page-link-disabled">
                        <i class="fa fa-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link rounded-3 border text-dark px-3 py-2 mx-1 shadow-sm page-link-normal"
                        href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya">
                        <i class="fa fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Nomor halaman (sudah diringkas dengan "...") --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link rounded-3 border text-muted px-3 py-2 mx-1 page-link-disabled">&hellip;</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link rounded-3 border px-3 py-2 mx-1 fw-bold text-white shadow-sm page-link-active"
                                    aria-current="page">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link rounded-3 border text-dark px-3 py-2 mx-1 shadow-sm page-link-normal"
                                    href="{{ $url }}" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-3 border text-dark px-3 py-2 mx-1 shadow-sm page-link-normal"
                        href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya">
                        <i class="fa fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link rounded-3 border text-muted px-3 py-2 mx-1 page-link-disabled">
                        <i class="fa fa-chevron-right"></i>
                    </span>
                </li>
            @endif

        </ul>
    </nav>
@endif