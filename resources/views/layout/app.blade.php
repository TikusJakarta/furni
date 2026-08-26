<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Untree.co">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">

    <meta name="description" content="@yield('meta_description', $settings['meta_description'] ?? '')" />
    <meta name="keywords" content="@yield('meta_keywords', $settings['meta_keywords'] ?? 'bootstrap, bootstrap4')" />

    <!-- Bootstrap CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/tiny-slider.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <title>
        @yield('title', ($settings['brand_name'] ?? 'Furni') . ' - Free Bootstrap 5 Template for Furniture and Interior Design')
    </title>
    @stack('styles')
</head>

<body>

    <!-- Start Header/Navigation -->
    <nav class="custom-navbar navbar navbar-expand-md navbar-dark bg-dark" aria-label="Furni navigation bar">
        <div class="container">
            <a class="navbar-brand"
                href="{{ route('home') }}">{{ $settings['brand_name'] ?? 'Furni' }}<span>.</span></a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsFurni"
                aria-controls="navbarsFurni" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsFurni">
                <ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
                    <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('shop*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('shop') }}">Shop</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('about*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('about') }}">About us</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('services*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('services') }}">Services</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('blog*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('blog') }}">Blog</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('contact*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('contact') }}">Contact us</a>
                    </li>
                </ul>

                <ul class="custom-navbar-cta navbar-nav mb-2 mb-md-0 ms-5">
                    <li><a class="nav-link" href="{{ route('login') }}"><img src="{{ asset('images/user.svg') }}" alt="User"></a></li>
                    @auth
    <!-- Tombol Logout Darurat -->
    <form action="{{ route('logout') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-danger ms-2">Logout</button>
    </form>
@endauth
                    <li><a class="nav-link" href="{{ route('cart') }}"><img src="{{ asset('images/cart.svg') }}"
                                alt="Cart"></a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End Header/Navigation -->

    <!-- Dynamic Hero Section -->
    @yield('hero')

    <!-- Dynamic Content Section -->
    @yield('content')

    <!-- Start Footer Section -->
    <footer class="footer-section">
        <div class="container relative">
            <div class="sofa-img">
                <img src="{{ asset($settings['footer_sofa_image'] ?? 'images/sofa.png') }}" alt="Image" class="img-fluid">
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="subscription-form">
                        <h3 class="d-flex align-items-center">
                            <span class="me-1"><img src="{{ asset('images/envelope-outline.svg') }}" alt="Image"
                                    class="img-fluid"></span>
                            <span>{{ $settings['footer_newsletter_title'] ?? 'Subscribe to Newsletter' }}</span>
                        </h3>

                        <form action="#" method="POST" class="row g-3">
                            @csrf
                            <div class="col-auto">
                                <input type="text" name="name" class="form-control" placeholder="Enter your name"
                                    required>
                            </div>
                            <div class="col-auto">
                                <input type="email" name="email" class="form-control" placeholder="Enter your email"
                                    required>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">
                                    <span class="fa fa-paper-plane"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row g-5 mb-5">
                <div class="col-lg-4">
                    <div class="mb-4 footer-logo-wrap">
                        <a href="{{ route('home') }}"
                            class="footer-logo">{{ $settings['brand_name'] ?? 'Furni' }}<span>.</span></a>
                    </div>
                    <p class="mb-4">{{ $settings['footer_description'] ?? 'Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit.' }}</p>

                    <ul class="list-unstyled custom-social">
                        <li><a href="{{ $settings['social_facebook'] ?? '#' }}"><span class="fa fa-brands fa-facebook-f"></span></a></li>
                        <li><a href="{{ $settings['social_twitter'] ?? '#' }}"><span class="fa fa-brands fa-twitter"></span></a></li>
                        <li><a href="{{ $settings['social_instagram'] ?? '#' }}"><span class="fa fa-brands fa-instagram"></span></a></li>
                        <li><a href="{{ $settings['social_linkedin'] ?? '#' }}"><span class="fa fa-brands fa-linkedin"></span></a></li>
                    </ul>
                </div>

                <div class="col-lg-8">
                    <div class="row links-wrap">
                        <div class="col-6 col-sm-6 col-md-3">
                            <ul class="list-unstyled">
                                <li><a href="{{ route('about') }}">About us</a></li>
                                <li><a href="{{ route('services') }}">Services</a></li>
                                <li><a href="{{ route('blog') }}">Blog</a></li>
                                <li><a href="{{ route('contact') }}">Contact us</a></li>
                            </ul>
                        </div>
                        <div class="col-6 col-sm-6 col-md-3">
                            <ul class="list-unstyled">
                                <li><a href="#">Support</a></li>
                                <li><a href="#">Knowledge base</a></li>
                                <li><a href="#">Live chat</a></li>
                            </ul>
                        </div>
                        <div class="col-6 col-sm-6 col-md-3">
                            <ul class="list-unstyled">
                                <li><a href="#">Jobs</a></li>
                                <li><a href="#">Our team</a></li>
                                <li><a href="#">Leadership</a></li>
                                <li><a href="#">Privacy Policy</a></li>
                            </ul>
                        </div>
                        <div class="col-6 col-sm-6 col-md-3">
                            <ul class="list-unstyled">
                                <li><a href="#">Nordic Chair</a></li>
                                <li><a href="#">Kruzo Aero</a></li>
                                <li><a href="#">Ergonomic Chair</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top copyright">
                <div class="row pt-4">
                    <div class="col-lg-6">
                        <p class="mb-2 text-center text-lg-start">
                            Copyright &copy;
                            <script>document.write(new Date().getFullYear());</script>. All Rights Reserved. &mdash;
                            Designed with love by <a href="https://untree.co">Untree.co</a> Distributed By <a
                                href="https://themewagon.com">ThemeWagon</a>
                        </p>
                    </div>
                    <div class="col-lg-6 text-center text-lg-end">
                        <ul class="list-unstyled d-inline-flex ms-auto">
                            <li class="me-4"><a href="#">Terms &amp; Conditions</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- End Footer Section -->

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/tiny-slider.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    @stack('scripts')
</body>

</html>