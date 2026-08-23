@extends('layout.app')

@section('title', $settings['blog_page_title'] ?? 'Blog')

@section('hero')
    <!-- Start Hero Section -->
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1>{{ $settings['blog_hero_title'] ?? 'Blog' }}</h1>
                        <p class="mb-4">{{ $settings['blog_hero_desc'] ?? 'Read our latest articles...' }}</p>
                        <p><a href="{{ route('shop') }}" class="btn btn-secondary me-2">Shop Now</a><a href="#" class="btn btn-white-outline">Explore</a></p>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="hero-img-wrap">
                        <img src="{{ asset($settings['blog_hero_image'] ?? 'images/couch.png') }}" class="img-fluid" alt="Couch">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Hero Section -->
@endsection

@section('content')
    <!-- Start Blog Section -->
    <div class="blog-section">
        <div class="container">
            <div class="row">
                @foreach($posts as $post)
                <div class="col-12 col-sm-6 col-md-4 mb-5">
                    <div class="post-entry">
                        <a href="#" class="post-thumbnail"><img src="{{ asset($post->image) }}" alt="Image" class="img-fluid"></a>
                        <div class="post-content-entry">
                            <h3><a href="#">{{ $post->title }}</a></h3>
                            <div class="meta">
                                <span>by <a href="#">{{ $post->author }}</a></span> <span>on <a href="#">{{ \Carbon\Carbon::parse($post->date)->format('M d, Y') }}</a></span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- End Blog Section -->   

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
                            @foreach($testimonials as $testimonial)
                            <div class="item">
                                <div class="row justify-content-center">
                                    <div class="col-lg-8 mx-auto">
                                        <div class="testimonial-block text-center">
                                            <blockquote class="mb-5">
                                                <p>&ldquo;{{ $testimonial->quote }}&rdquo;</p>
                                            </blockquote>

                                            <div class="author-info">
                                                <div class="author-pic">
                                                    <img src="{{ asset($testimonial->image) }}" alt="{{ $testimonial->name }}" class="img-fluid">
                                                </div>
                                                <h3 class="font-weight-bold">{{ $testimonial->name }}</h3>
                                                <span class="position d-block mb-3">{{ $testimonial->position }}</span>
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