<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Furni Control Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS Terpisah -->
    <link rel="stylesheet" href="{{ asset('css/admin-login.css') }}">
</head>
<body class="py-5">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5 col-xl-4">

                {{-- Alert Sukses / Logout --}}
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4 p-3" role="alert">
                        <i class="fa fa-check-circle text-success"></i>
                        <div class="small fw-medium">{{ session('success') }}</div>
                    </div>
                @endif

                {{-- Alert Error / Gagal Login --}}
                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4 p-3" role="alert">
                        <i class="fa fa-exclamation-circle text-danger"></i>
                        <div class="small fw-medium">{{ session('error') }}</div>
                    </div>
                @endif

                <!-- Card Form Utama -->
                <div class="card admin-card bg-white">
                    
                    <!-- Header Banner di dalam Card -->
                    <div class="admin-header-bg text-white text-center p-4">
                        <div class="bg-white bg-opacity-10 d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 55px; height: 55px;">
                            <i class="fa fa-shield-halved fa-lg text-white"></i>
                        </div>
                        <h4 class="fw-bold mb-1 text-white">Admin Portal</h4>
                        <p class="text-white-50 small mb-0">Masuk untuk mengelola sistem & inventaris Furni</p>
                    </div>

                    <div class="card-body p-4 p-lg-4">
                        <form action="{{ route('admin.login.process') }}" method="POST">
                            @csrf
                            
                            {{-- Input Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label text-dark fw-semibold small">Email Administrator <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-envelope" style="font-size: 14px;"></i></span>
                                    <input type="email" class="form-control bg-light border-start-0 py-2.5 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="admin@furni.com" required autofocus>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Password dengan Toggle Show/Hide --}}
                            <div class="mb-4">
                                <label for="password" class="form-label text-dark fw-semibold small">Kata Sandi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-lock" style="font-size: 14px;"></i></span>
                                    <input type="password" class="form-control bg-light border-start-0 border-end-0 py-2.5 @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••" required>
                                    <button class="btn btn-light border border-start-0 text-muted px-3" type="button" id="togglePassword">
                                        <i class="fa fa-eye" id="toggleIcon" style="font-size: 14px;"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tombol Aksi --}}
                            <button type="submit" class="btn text-white w-100 py-2.5 fw-semibold shadow-sm mb-3" style="background-color: #2f463d; border-color: #2f463d;">
                                Masuk ke Dashboard <i class="fa fa-arrow-right ms-1" style="font-size: 12px;"></i>
                            </button>
                        </form>

                        <!-- Navigasi Kembali -->
                        <div class="text-center pt-3 border-top mt-2">
                            <a href="{{ route('home') }}" class="text-decoration-none text-muted small fw-medium">
                                <i class="fa fa-arrow-left me-1"></i> Kembali ke Toko Utama
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Toggle Password -->
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
</body>
</html>