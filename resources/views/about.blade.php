@extends('layout.app')

@section('title', $settings['about_page_title'] ?? 'About Us - Furni')

@section('hero')
    <!-- Start Hero Section -->
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1>{{ $settings['about_hero_title'] ?? 'About Us' }}</h1>
                        <p class="mb-4">{{ $settings['about_hero_desc'] ?? 'Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique.' }}</p>
                        <p><a href="{{ route('shop') }}" class="btn btn-secondary me-2">Shop Now</a><a href="#" class="btn btn-white-outline">Explore</a></p>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="hero-img-wrap">
                        <img src="{{ asset($settings['about_hero_image'] ?? 'images/couch.png') }}" class="img-fluid" alt="Couch">
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
            <div class="row justify-content-between align-items-center">
                <div class="col-lg-6">
                    <h2 class="section-title">{{ $settings['about_why_choose_title'] ?? 'Why Choose Us' }}</h2>
                    <p>{{ $settings['about_why_choose_desc'] ?? '' }}</p>

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
                        <img src="{{ asset($settings['about_why_choose_image'] ?? 'images/why-choose-us-img.jpg') }}" alt="Image" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Why Choose Us Section -->

    <!-- Start Team Section -->
    <div class="untree_co-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-5 mx-auto text-center">
                    <h2 class="section-title">{{ $settings['about_team_title'] ?? 'Our Team' }}</h2>
                </div>
            </div>

            <div class="row">
                @foreach($teams as $team)
                <!-- Start Team Column -->
                <div class="col-12 col-md-6 col-lg-3 mb-5 mb-md-0">
                    <img src="{{ asset($team->image) }}" class="img-fluid mb-5" alt="{{ $team->first_name }}">
                    <h3><a href="#"><span>{{ $team->first_name }}</span> {{ $team->last_name }}</a></h3>
                    <span class="d-block position mb-4">{{ $team->position }}</span>
                    <p>{{ $team->bio }}</p>
                    <p class="mb-0"><a href="#" class="more dark">Learn More <span class="icon-arrow_forward"></span></a></p>
                </div> 
                <!-- End Team Column -->
                @endforeach
            </div>
        </div>
    </div>
    <!-- End Team Section -->

    <!-- Start Testimonial Slider -->
    <div class="testimonial-section before-footer-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 mx-auto text-center">
                    <h2 class="section-title">{{ $settings['about_testimonial_title'] ?? 'Testimonials' }}</h2>
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