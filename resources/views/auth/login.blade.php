@extends('layout.app')

@section('title', $settings['login_page_title'] ?? 'Login - Furni')

@section('hero')
    <!-- Start Hero Section -->
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between align-items-center">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1>{{ $settings['login_hero_title'] ?? 'Login' }}</h1>
                        <p class="mb-0 text-white-50">Masuk ke akun Furni kamu untuk melanjutkan pesanan & kelola profil.</p>
                    </div>
                </div>
                <div class="col-lg-7"></div>
            </div>
        </div>
    </div>
    <!-- End Hero Section -->
@endsection

@section('content')
    <div class="untree_co-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    
                    {{-- Alert Success --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Alert Error --}}
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Card Form -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4 p-lg-5">
                            
                            <div class="text-center mb-4">
                                <h3 class="fw-bold text-dark">Masuk ke Akun</h3>
                                <p class="text-muted small">Silakan isi kredensial kamu di bawah ini</p>
                            </div>

                            <form action="{{ route('login.process') }}" method="POST">
                                @csrf
                                
                                {{-- Email Input --}}
                                <div class="mb-3">
                                    <label for="email" class="form-label text-dark fw-semibold small">Alamat Email <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fa fa-envelope text-muted"></i></span>
                                        <input type="email" class="form-control bg-light border-start-0 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Password Input --}}
                                <div class="mb-3">
                                    <label for="password" class="form-label text-dark fw-semibold small">Kata Sandi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fa fa-lock text-muted"></i></span>
                                        <input type="password" class="form-control bg-light border-start-0 border-end-0 @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••" required>
                                        <button class="btn btn-light border border-start-0 text-muted" type="button" id="togglePassword">
                                            <i class="fa fa-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Remember & Forgot --}}
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label text-muted small" for="remember">Ingat saya</label>
                                    </div>
                                    <a href="#" class="small text-decoration-none text-dark fw-medium">Lupa kata sandi?</a>
                                </div>

                                {{-- Submit Button --}}
                                <button type="submit" class="btn btn-dark w-100 py-2.5 fw-semibold mb-3">Masuk Sekarang</button>
                            </form>

                            <div class="text-center pt-3 border-top">
                                <p class="text-muted small mb-0">Belum memiliki akun? <a href="#" class="text-dark fw-bold text-decoration-none">Daftar sekarang</a></p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Script Toggle Password --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (togglePassword && passwordInput && toggleIcon) {
                togglePassword.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    toggleIcon.classList.toggle('fa-eye', !isPassword);
                    toggleIcon.classList.toggle('fa-eye-slash', isPassword);
                });
            }
        });
    </script>
@endsection