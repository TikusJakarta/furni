@extends('layout.app')

@section('title', $settings['shop_page_title'] ?? 'Shop - Furni')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}?v={{ filemtime(public_path('css/shop.css')) }}">
@endpush

@section('content')
    <!-- Start Hero Section -->
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1>{{ $settings['shop_hero_title'] ?? 'Shop' }}</h1>
                    </div>
                </div>
                <div class="col-lg-7"></div>
            </div>
        </div>
    </div>
    <!-- End Hero Section -->

    <div class="untree_co-section product-section before-footer-section">
        <div class="container shop-wrap">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row mb-5">
                <div class="col-md-8 col-lg-6 mx-auto">
                    <form action="{{ route('shop') }}" method="GET" class="shop-search" role="search">
                        <div class="position-relative">
                            <div class="shop-search-box">
                                <i class="fa fa-search shop-search-icon" aria-hidden="true"></i>
                                <input type="text" id="search-input" name="search" class="shop-search-input"
                                    placeholder="Cari nama produk furniture..." value="{{ request('search') }}"
                                    autocomplete="off" aria-label="Cari produk">

                                @if(request('search'))
                                    <a href="{{ route('shop') }}" class="shop-search-reset" title="Hapus pencarian"
                                        aria-label="Hapus pencarian">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </a>
                                @endif

                                <button class="shop-search-btn" type="submit">Cari</button>
                            </div>

                            <div id="search-suggestions-box"
                                class="position-absolute start-0 end-0 bg-white border rounded-bottom shadow-lg d-none">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Grid di tengah: 5 produk sebaris di layar lebar, turun otomatis di layar lebih kecil --}}
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 justify-content-center gy-5 shop-grid">
                @forelse($products as $product)
                    <div class="col">
                        <div class="product-item {{ $product->effective_stock <= 0 ? 'opacity-75' : '' }}">

                            <!-- Card / Trigger Modal dengan data-variants -->
                            <a href="#" class="product-modal-trigger" data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="Rp {{ number_format($product->price, 0, ',', '.') }}"
                                data-description="{{ $product->description ?? 'Belum ada deskripsi untuk produk ini.' }}"
                                data-weight="{{ $product->weight >= 1000 ? ($product->weight / 1000) . ' kg' : $product->weight . ' gram' }}"
                                data-stock="{{ $product->effective_stock }}" data-image="{{ asset($product->image) }}"
                                data-rating="{{ number_format($product->averageRating(), 1) }}"
                                data-reviews-count="{{ $product->reviews->count() }}"
                                data-variants='@json($product->variants)'
                                data-cart-url="{{ route('cart.add', $product->id) }}">

                                <img src="{{ asset($product->image) }}" class="img-fluid product-thumbnail"
                                    alt="{{ $product->name }}">
                                <h3 class="product-title">{{ $product->name }}</h3>
                                <strong class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</strong>

                                @if($product->effective_stock > 0)
                                    <span class="d-block text-muted small mt-1">Stock: {{ $product->effective_stock }}</span>
                                @else
                                    <span class="d-block text-danger font-weight-bold small mt-1">Stok Habis</span>
                                @endif
                            </a>

                            @if($product->effective_stock > 0)
                                <a href="{{ route('cart.add', $product->id) }}" class="icon-cross">
                                    <img src="{{ asset('images/cross.svg') }}" class="img-fluid" alt="Cross">
                                </a>
                            @else
                                <span class="icon-cross" style="background-color: #ccc; cursor: not-allowed;" title="Stok Habis">
                                    <img src="{{ asset('images/cross.svg') }}" class="img-fluid opacity-50" alt="Cross">
                                </span>
                            @endif

                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Tidak ada produk yang ditemukan.</p>
                    </div>
                @endforelse
            </div>

            {{-- BAGIAN PAGINASI & TEKS INFORMASI PRODUK --}}
            @if($products->total() > 0)
                <div class="row mt-5">
                    <div class="col-12 text-center">
                        <p class="text-muted small mb-3 fw-medium">
                            Menampilkan {{ $products->firstItem() }}–{{ $products->lastItem() }} dari {{ $products->total() }} produk
                        </p>

                        {{-- Tampilan tombol halaman ada di partials/shop-pagination.blade.php --}}
                        {{ $products->onEachSide(1)->appends(request()->except('page'))->links('partials.shop-pagination') }}
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- Modal Pop-up Detail Produk -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="close" id="closeModalBtn" aria-label="Close"
                        style="border: none; background: none; font-size: 1.5rem; cursor: pointer; outline: none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <img id="modal-img" src="" alt="" class="img-fluid rounded shadow-sm w-100"
                                style="max-height: 350px; object-fit: cover;">
                        </div>

                        <!-- Informasi Produk -->
                        <div class="col-md-6 pl-md-4">
                            <h3 id="modal-name" class="text-black font-weight-bold mb-2"></h3>

                            <!-- Bagian Rating Bintang di Modal -->
                            <div class="mb-2">
                                <span id="modal-rating-stars" class="text-warning"></span>
                                <span id="modal-rating-text" class="text-muted small ms-2"></span>
                            </div>

                            <h4 id="modal-price" class="text-primary font-weight-bold mb-3" style="font-size: 1.5rem;"></h4>
                            <p id="modal-desc" class="text-muted mb-4 small"></p>

                            <ul class="list-unstyled mb-3">
                                <li class="mb-2"><strong>Berat:</strong> <span id="modal-weight"></span></li>
                                <li class="mb-2"><strong>Stok Tersedia:</strong> <span id="modal-stock-badge"></span></li>
                            </ul>

                            <form id="modal-form" action="" method="GET" class="d-flex flex-column align-items-stretch">
                                @csrf

                                <!-- Pilihan Varian Produk (Sudah di dalam form) -->
                                <div id="modal-variants-container" class="mb-3 d-none">
                                    <label class="form-label font-weight-bold text-dark d-block mb-1">Pilih Varian:</label>
                                    <select id="modal-variant-select" name="product_variant_id" class="form-control">
                                        <option value="">-- Pilih Varian --</option>
                                    </select>
                                </div>

                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="input-group me-3 mb-3 mb-md-0 modal-qty-group" style="width: 130px;">
                                        <button class="btn btn-outline-secondary decrease px-3 modal-qty-btn"
                                            type="button">-</button>
                                        <input type="text" name="quantity"
                                            class="form-control text-center quantity-amount modal-qty-input" value="1" min="1"
                                            id="modal-stock-input">
                                        <button class="btn btn-outline-secondary increase px-3 modal-qty-btn"
                                            type="button">+</button>
                                    </div>

                                    <button type="submit" id="modal-submit-btn"
                                        class="btn btn-black text-white px-4 ms-md-2 modal-submit-btn">
                                        Add to Cart
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panggil file JavaScript terpisah -->
    <script src="{{ asset('js/detail.js') }}"></script>
    <script src="{{ asset('js/sugested.js') }}"></script>
    <script src="{{ asset('js/variant-modal.js') }}"></script>

    @if(session('swal_error'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Waduh, Keranjang Kosong!',
                text: '{{ session('swal_error') }}',
                confirmButtonColor: '#3b5d50',
                confirmButtonText: 'Belanja Sekarang'
            });
        </script>
    @endif
@endsection