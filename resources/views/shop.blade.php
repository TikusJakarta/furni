@extends('layout.app')

@section('title', $settings['shop_page_title'] ?? 'Shop - Furni')

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
        <div class="container">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Form Pencarian Produk -->
            <div class="row mb-5">
                <div class="col-md-6 mx-auto">
                    <form action="{{ route('shop') }}" method="GET">
                        <div class="input-group shadow-sm">
                            <input type="text" name="search" class="form-control form-control-lg"
                                placeholder="Cari nama produk furniture..." value="{{ request('search') }}">
                            <button class="btn btn-primary text-white px-4" type="submit">
                                <i class="fa fa-search me-1"></i> Cari
                            </button>
                            @if(request('search'))
                                <a href="{{ route('shop') }}"
                                    class="btn btn-outline-secondary d-flex align-items-center">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                @foreach($products as $product)
                    <div class="col-12 col-md-4 col-lg-3 mb-5">
                        <div class="product-item {{ $product->effective_stock <= 0 ? 'opacity-75' : '' }}">

                            <!-- Card / Trigger Modal -->
                            <a href="#" class="product-modal-trigger" data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="Rp {{ number_format($product->price, 0, ',', '.') }}"
                                data-description="{{ $product->description ?? 'Belum ada deskripsi untuk produk ini.' }}"
                                data-weight="{{ $product->weight >= 1000 ? ($product->weight / 1000) . ' kg' : $product->weight . ' gram' }}"
                                data-stock="{{ $product->effective_stock }}" data-image="{{ asset($product->image) }}"
                                data-rating="{{ number_format($product->averageRating(), 1) }}"
                                data-reviews-count="{{ $product->reviews->count() }}"
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
                @endforeach
            </div>
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

                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><strong>Berat:</strong> <span id="modal-weight"></span></li>
                                <li class="mb-2"><strong>Stok Tersedia:</strong> <span id="modal-stock-badge"></span></li>
                            </ul>

                            <form id="modal-form" action="" method="GET" class="d-flex align-items-center flex-wrap">
                                @csrf
                                <div class="input-group me-3 mb-3 mb-md-0 modal-qty-group">
                                    <button class="btn btn-outline-secondary decrease px-3 modal-qty-btn"
                                        type="button">-</button>
                                    <input type="text" name="quantity"
                                        class="form-control text-center quantity-amount modal-qty-input" value="1" min="1"
                                        id="modal-stock-input">
                                    <button class="btn btn-outline-secondary increase px-3 modal-qty-btn"
                                        type="button">+</button>
                                </div>

                                <button type="submit" id="modal-submit-btn"
                                    class="btn btn-black text-white px-4 ms-2 modal-submit-btn">
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/detail.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const closeBtn = document.getElementById('closeModalBtn');
            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    $('#productModal').modal('hide');
                });
            }
        });
    </script>

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