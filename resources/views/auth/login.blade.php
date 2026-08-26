@extends('layout.app')

@section('title', $settings['login_page_title'] ?? 'Login - Furni')

@section('content')
    <!-- Start Hero Section -->
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1>{{ $settings['login_hero_title'] ?? 'Login' }}</h1>
                    </div>
                </div>
                <div class="col-lg-7"></div>
            </div>
        </div>
    </div>
    <!-- End Hero Section -->

    <div class="untree_co-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="p-3 p-lg-5 border bg-white rounded shadow-sm">
                        <form action="{{ route('login.process') }}" method="POST">
                            @csrf
                            
                            <div class="form-group mb-3">
                                <label class="text-black" for="email">Email address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="text-black" for="password">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label text-black" for="remember">Remember me</label>
                                </div>
                                <a href="#">Forgot password?</a>
                            </div>

                            <button type="submit" class="btn btn-black btn-lg py-3 btn-block w-100">Login</button>
                        </form>

                        <div class="text-center mt-4">
                             <p class="mb-0">Don't have an account? <a href="#">Sign Up</a></p> 
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection