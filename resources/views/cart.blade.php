@extends('layout.app')

@section('title', $settings['cart_page_title'] ?? 'Cart - Furni')

@section('content')
    <!-- Start Hero Section -->
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1>{{ $settings['cart_hero_title'] ?? 'Cart' }}</h1>
                    </div>
                </div>
                <div class="col-lg-7"></div>
            </div>git s
        </div>
    </div>
    <!-- End Hero Section -->

    <div class="untree_co-section before-footer-section">
        <div class="container">
            <form id="cart-form" action="{{ route('cart.update') }}" method="POST">
                @csrf
                <div class="row mb-5">
                    <div class="col-md-12">
                        <div class="site-blocks-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="product-thumbnail">Image</th>
                                        <th class="product-name">Product</th>
                                        <th class="product-price">Price</th>
                                        <th class="product-quantity">Quantity</th>
                                        <th class="product-total">Total</th>
                                        <th class="product-remove">Remove</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cartItems ?? [] as $id => $item)
                                    <tr data-id="{{ $id }}">
                                        <td class="product-thumbnail">
                                            <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="img-fluid">
                                        </td>
                                        <td class="product-name">
                                            <h2 class="h5 text-black">{{ $item['name'] }}</h2>
                                        </td>
                                        <td class="product-price-val" data-price="{{ $item['price'] }}">
                                            ${{ number_format($item['price'], 2) }}
                                        </td>
                                        <td>
                                            <div class="input-group mb-3 d-flex align-items-center quantity-container" style="max-width: 120px;">
                                                <div class="input-group-prepend">
                                                    <button class="btn btn-outline-black decrease" type="button">&minus;</button>
                                                </div>
                                                <input type="text" name="quantity[{{ $id }}]" class="form-control text-center quantity-amount" value="{{ $item['quantity'] }}" aria-label="Quantity">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-black increase" type="button">&plus;</button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="product-row-total">
                                            ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                        </td>
                                        <td><a href="{{ route('cart.remove', $id) }}" class="btn btn-black btn-sm">X</a></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <p class="mb-3 text-muted">Your cart is currently empty.</p>
                                            <a href="{{ route('shop') }}" class="btn btn-black btn-sm">Shop Now</a>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="row mb-5">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <button type="submit" class="btn btn-black btn-sm btn-block">Update Cart</button>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('shop') }}" class="btn btn-outline-black btn-sm btn-block">Continue Shopping</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 pl-5">
                        <div class="row justify-content-end">
                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col-md-12 text-right border-bottom mb-5">
                                        <h3 class="text-black h4 text-uppercase">Cart Totals</h3>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <span class="text-black">Subtotal</span>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <strong class="text-black cart-subtotal">${{ number_format($subtotal ?? 0, 2) }}</strong>
                                    </div>
                                </div>
                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <span class="text-black">Total</span>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <strong class="text-black cart-total">${{ number_format($total ?? 0, 2) }}</strong>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <a href="{{ route('checkout') }}" class="btn btn-black btn-lg py-3 btn-block text-center text-white">Proceed To Checkout</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/cart.js') }}"></script>
@endpush