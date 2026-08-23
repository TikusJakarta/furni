@extends('layout.app')

@section('title', $settings['services_page_title'] ?? 'Services - Furni')

@section('hero')
    <!-- Start Hero Section -->
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1>{{ $settings['services_hero_title'] ?? 'Services' }}</h1>
                        <p class="mb-4">{{ $settings['services_hero_desc'] ?? 'Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate.' }}</p>
                        <p><a href="{{ route('shop') }}" class="btn btn-secondary me-2">Shop Now</a><a href="#" class="btn btn-white-outline">Explore</a></p>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="hero-img-wrap">
                        <img src="{{ asset($settings['services_hero_image'] ?? 'images/couch.png') }}" class="img-fluid" alt="Couch">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Hero Section -->
@endsection

@section('content')
    <!-- Start Why Choose Us Section -->
    <div class="why-choose-section">
        <div class="container">
            <div class="row my-5">
                @foreach($features as $feature)
                <div class="col-6 col-md-6 col-lg-3 mb-4">
                    <div class="feature">
                        <div class="icon">
                            <img src="{{ asset($feature->icon) }}" alt="Image" class="img-fluid">
                        </div>
                        <h3>{{ $feature->title }}</h3>
                        <p>{{ $feature->description }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- End Why Choose Us Section -->

    <!-- Start Product Section -->
    <div class="product-section pt-0">
        <div class="container">
            <div class="row">
                <!-- Start Column 1 -->
                <div class="col-md-12 col-lg-3 mb-5 mb-lg-0">
                    <h2 class="mb-4 section-title">Crafted with excellent material.</h2>
                    <p class="mb-4">Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique. </p>
                    <p><a href="{{ route('shop') }}" class="btn">Explore</a></p>
                </div> 
                <!-- End Column 1 -->

                @foreach($products as $product)
                <!-- Start Product Column -->
                <div class="col-12 col-md-4 col-lg-3 mb-5 mb-md-0">
                    <a class="product-item" href="{{ route('shop') }}">
                        <img src="{{ asset($product->image) }}" class="img-fluid product-thumbnail" alt="{{ $product->name }}">
                        <h3 class="product-title">{{ $product->name }}</h3>
                        <strong class="product-price">${{ number_format($product->price, 2) }}</strong>
                        <span class="icon-cross">
                            <img src="{{ asset('images/cross.svg') }}" class="img-fluid" alt="Cross">
                        </span>
                    </a>
                </div> 
                <!-- End Product Column -->
                @endforeach
            </div>
        </div>
    </div>
    <!-- End Product Section -->

    <!-- Start Testimonial Slider -->
    <div class="testimonial-section before-footer-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 mx-auto text-center">
                    <h2 class="section-title">Testimonials</h2>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="testimonial-slider-wrap text-center">
                        <div id="testimonial-nav">
                            <span class="prev" data-controls="prev"><span class="fa fa-chevron-left"></span></span>
                            <span class="next" data-controls="next"><span class="fa fa-chevron-right"></span></span>
                        </div>

                        <div class="testimonial-slider">
                            @foreach($testimonials as $testi)
                            <div class="item">
                                <div class="row justify-content-center">
                                    <div class="col-lg-8 mx-auto">
                                        <div class="testimonial-block text-center">
                                            <blockquote class="mb-5">
                                                <p>&ldquo;{{ $testi->quote }}&rdquo;</p>
                                            </blockquote>
                                            <div class="author-info">
                                                <div class="author-pic">
                                                    <img src="{{ asset($testi->image) }}" alt="{{ $testi->name }}" class="img-fluid">
                                                </div>
                                                <h3 class="font-weight-bold">{{ $testi->name }}</h3>
                                                <span class="position d-block mb-3">{{ $testi->position }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            <!-- END item -->
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Testimonial Slider -->
@endsection