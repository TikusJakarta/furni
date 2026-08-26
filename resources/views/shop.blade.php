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
                <div class="col-lg-7">
                    
                </div>
            </div>
        </div>
    </div>
    <!-- End Hero Section -->

    <div class="untree_co-section product-section before-footer-section">
        <div class="container">
            <div class="row">
                @foreach($products as $product)
                <div class="col-12 col-md-4 col-lg-3 mb-5">
                    <div class="product-item">
                        <a href="{{ url('/shop/item') }}">
                            <img src="{{ asset($product->image) }}" class="img-fluid product-thumbnail" alt="{{ $product->name }}">
                            <h3 class="product-title">{{ $product->name }}</h3>
                            <strong class="product-price">${{ number_format($product->price, 2) }}</strong>
                            
                            <span class="d-block text-muted small mt-1">Stock: {{ $product->stock }}</span>
                        </a>

                        <a href="{{ route('cart.add', $product->id) }}" class="icon-cross">
                            <img src="{{ asset('images/cross.svg') }}" class="img-fluid" alt="Cross">
                        </a>
                    </div>
                </div> 
                @endforeach
            </div>
        </div>
    </div>
@endsection