@extends('layout.app')

@section('title', $settings['home_page_title'] ?? 'Home - Furni')

@section('hero')
    <!-- Start Hero Section -->
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1>{!! $settings['home_hero_title'] ?? 'Modern Interior <span class="d-block">Design Studio</span>' !!}</h1>
                        <p class="mb-4">{{ $settings['home_hero_desc'] ?? 'Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique.' }}</p>
                        <p><a href="{{ route('shop') }}" class="btn btn-secondary me-2">Shop Now</a><a href="#" class="btn btn-white-outline">Explore</a></p>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="hero-img-wrap">
                        <img src="{{ asset($settings['home_hero_image'] ?? 'images/couch.png') }}" class="img-fluid" alt="Couch">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Hero Section -->
@endsection

@section('content')
    <!-- Start Product Section -->
    <div class="product-section">
        <div class="container">
            <div class="row">
                <!-- Column 1 -->
                <div class="col-md-12 col-lg-3 mb-5 mb-lg-0">
                    <h2 class="mb-4 section-title">{{ $settings['product_section_title'] ?? 'Crafted with excellent material.' }}</h2>
                    <p class="mb-4">{{ $settings['product_section_desc'] ?? 'Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit.' }}</p>
                    <p><a href="{{ route('shop') }}" class="btn">Explore</a></p>
                </div> 

                <!-- Dynamic Products Loop -->
                @foreach($products as $product)
                <div class="col-12 col-md-4 col-lg-3 mb-5 mb-md-0">
                    <a class="product-item" href="#">
                        <img src="{{ asset($product->image) }}" class="img-fluid product-thumbnail" alt="{{ $product->name }}">
                        <h3 class="product-title">{{ $product->name }}</h3>
                        <strong class="product-price">${{ number_format($product->price, 2) }}</strong>
                        <span class="icon-cross">
                            <img src="{{ asset('images/cross.svg') }}" class="img-fluid" alt="Cross">
                        </span>
                    </a>
                </div> 
                @endforeach
            </div>
        </div>
    </div>
    <!-- End Product Section -->

    <!-- Start Why Choose Us Section -->
    <div class="why-choose-section">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-6">
                    <h2 class="section-title">{{ $settings['why_choose_title'] ?? 'Why Choose Us' }}</h2>
                    <p>{{ $settings['why_choose_desc'] ?? 'Donec vitae odio quis nisl dapibus malesuada.' }}</p>

                    <div class="row my-5">
                        @foreach($features as $feature)
                        <div class="col-6 col-md-6 mb-4">
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

                <div class="col-lg-5">
                    <div class="img-wrap">
                        <img src="{{ asset($settings['why_choose_image'] ?? 'images/why-choose-us-img.jpg') }}" alt="Image" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Why Choose Us Section -->

    <!-- Start We Help Section -->
    <div class="we-help-section">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-7 mb-5 mb-lg-0">
                    <div class="imgs-grid">
                        <div class="grid grid-1"><img src="{{ asset('images/img-grid-1.jpg') }}" alt="Untree.co"></div>
                        <div class="grid grid-2"><img src="{{ asset('images/img-grid-2.jpg') }}" alt="Untree.co"></div>
                        <div class="grid grid-3"><img src="{{ asset('images/img-grid-3.jpg') }}" alt="Untree.co"></div>
                    </div>
                </div>
                <div class="col-lg-5 ps-lg-5">
                    <h2 class="section-title mb-4">{{ $settings['we_help_title'] ?? 'We Help You Make Modern Interior Design' }}</h2>
                    <p>{{ $settings['about_us_desc'] ?? '' }}</p>

                    <ul class="list-unstyled custom-list my-4">
                        @if(!empty($settings['we_help_list']))
                            @foreach(json_decode($settings['we_help_list'], true) as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        @else
                            <li>Donec vitae odio quis nisl dapibus malesuada</li>
                            <li>Donec vitae odio quis nisl dapibus malesuada</li>
                        @endif
                    </ul>
                    <p><a href="{{ route('shop') }}" class="btn">Explore</a></p>
                </div>
            </div>
        </div>
    </div>
    <!-- End We Help Section -->

    <!-- Start Popular Product -->
    <div class="popular-product">
        <div class="container">
            <div class="row">
                @foreach($popularProducts as $pop)
                <div class="col-12 col-md-6 col-lg-4 mb-4 mb-lg-0">
                    <div class="product-item-sm d-flex">
                        <div class="thumbnail">
                            <img src="{{ asset($pop->image) }}" alt="Image" class="img-fluid">
                        </div>
                        <div class="pt-3">
                            <h3>{{ $pop->name }}</h3>
                            <p>{{ $pop->description }}</p>
                            <p><a href="#">Read More</a></p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- End Popular Product -->

    <!-- Start Testimonial Slider -->
    <div class="testimonial-section">
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
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Testimonial Slider -->

    <!-- Start Blog Section -->
    <div class="blog-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-md-6">
                    <h2 class="section-title">Recent Blog</h2>
                </div>
                <div class="col-md-6 text-start text-md-end">
                    <a href="{{ route('blog') }}" class="more">View All Posts</a>
                </div>
            </div>

            <div class="row">
                @foreach($blogs as $blog)
                <div class="col-12 col-sm-6 col-md-4 mb-4 mb-md-0">
                    <div class="post-entry">
                        <a href="#" class="post-thumbnail"><img src="{{ asset($blog->image) }}" alt="Image" class="img-fluid"></a>
                        <div class="post-content-entry">
                            <h3><a href="#">{{ $blog->title }}</a></h3>
                            <div class="meta">
                                <span>by <a href="#">{{ $blog->author }}</a></span> <span>on <a href="#">{{ date('M d, Y', strtotime($blog->published_at)) }}</a></span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- End Blog Section -->   
@endsection