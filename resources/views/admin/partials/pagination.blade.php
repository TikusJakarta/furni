@if ($paginator->hasPages())
    <nav class="pager" role="navigation" aria-label="Navigasi halaman">
        <ul class="pager-list">

            {{-- Sebelumnya --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span class="pager-link is-disabled" aria-disabled="true" aria-label="Sebelumnya">
                        <i class="fa fa-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="pager-link" rel="prev" aria-label="Sebelumnya">
                        <i class="fa fa-chevron-left"></i>
                    </a>
                @endif
            </li>

            {{-- Nomor halaman --}}
            @foreach ($elements as $element)

                {{-- Titik-titik: bisa diisi angka untuk lompat ke halaman tertentu --}}
                @if (is_string($element))
                    <li>
                        <form method="GET" action="{{ $paginator->path() }}" class="pager-jump-form">
                            @foreach (request()->except('page') as $name => $value)
                                @if (is_scalar($value))
                                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <input type="number" name="page" class="pager-jump" placeholder="&hellip;"
                                min="1" max="{{ $paginator->lastPage() }}" inputmode="numeric" required
                                title="Ketik nomor halaman (1&ndash;{{ $paginator->lastPage() }}), lalu tekan Enter"
                                aria-label="Lompat ke halaman, 1 sampai {{ $paginator->lastPage() }}">
                        </form>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span class="pager-link is-active" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pager-link" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Berikutnya --}}
            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="pager-link" rel="next" aria-label="Berikutnya">
                        <i class="fa fa-chevron-right"></i>
                    </a>
                @else
                    <span class="pager-link is-disabled" aria-disabled="true" aria-label="Berikutnya">
                        <i class="fa fa-chevron-right"></i>
                    </span>
                @endif
            </li>

        </ul>
    </nav>
@endif
