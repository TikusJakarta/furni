@extends('layout.app')

@section('title', $product->name . ' - Furni')

@section('content')
    <!-- Start Hero Section -->
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1>Detail Produk</h1>
                    </div>
                </div>
                <div class="col-lg-7"></div>
            </div>
        </div>
    </div>
    <!-- End Hero Section -->

    <div class="untree_co-section">
        <div class="container">
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row align-items-center">
                <!-- Gambar Produk -->
                <div class="col-md-6 mb-4 mb-md-0">
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded shadow-sm w-100" style="max-height: 450px; object-fit: cover;">
                </div>

                <!-- Informasi Produk -->
                <div class="col-md-6 pl-md-5">
                    <h2 class="text-black font-weight-bold mb-3">{{ $product->name }}</h2>
                    
                    <h3 class="text-primary font-weight-bold mb-4" style="font-size: 1.8rem;">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </h3>

                    <p class="text-muted mb-4">
                        {{ $product->description ?? 'Belum ada deskripsi untuk produk ini.' }}
                    </p>

                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><strong>Berat:</strong> {{ $product->weight >= 1000 ? ($product->weight / 1000) . ' kg' : $product->weight . ' gram' }}</li>
                        <li class="mb-2"><strong>Stok Tersedia:</strong> <span class="badge bg-{{ $product->stock > 0 ? 'success' : 'danger' }} text-white">{{ $product->stock > 0 ? $product->stock . ' pcs' : 'Habis' }}</span></li>
                    </ul>

                    <!-- Form Tambah ke Keranjang -->
                    <form action="{{ route('cart.add', $product->id) }}" method="GET" class="d-flex align-items-center flex-wrap">
                        @csrf
                        
                        <!-- Input Group Quantity Rapi -->
                        <div class="input-group mr-3 mb-3 mb-md-0" style="width: 140px;">
                            <div class="input-group-prepend">
                                <button class="btn btn-outline-secondary decrease px-3" type="button" style="border-color: #ced4da; color: #212529;">&minus;</button>
                            </div>
                            <input type="text" name="quantity" class="form-control text-center quantity-amount" value="1" min="1" max="{{ $product->stock }}" {{ $product->stock <= 0 ? 'disabled' : '' }} style="border-color: #ced4da;">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary increase px-3" type="button" style="border-color: #ced4da; color: #212529;">&plus;</button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-black py-2 px-4 text-white" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                            Add to Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Panggil Script Eksternal -->
    <script src="{{ asset('js/detail.js') }}"></script>
@endsection