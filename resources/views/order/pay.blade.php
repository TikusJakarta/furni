@extends('layout.app')

@section('title', 'Pembayaran Order #' . $order->id . ' - Furni')

@section('content')
<div class="hero py-4 py-lg-5">
    <div class="container">
        <div class="row justify-content-between align-items-center">
            <div class="col-lg-6">
                <div class="intro-excerpt">
                    <h1>Selesaikan Pembayaran</h1>
                    <p class="text-white-50 mb-0">Order ID: <span class="text-white fw-bold">#{{ $order->id }}</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="untree_co-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card border-0 shadow-lg p-4 p-lg-5 rounded-4 bg-white">
                    
                    @if(session('error'))
                        <div class="alert alert-danger rounded-3 mb-4">{{ session('error') }}</div>
                    @endif

                    <!-- Total Tagihan Box -->
                    <div class="text-center p-3 rounded-4 mb-4 payment-total-box">
                        <span class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.8px;">Total Tagihan</span>
                        <h2 class="fw-bold mb-0 mt-1 payment-total-price">Rp {{ number_format($order->total_price, 0, ',', '.') }}</h2>
                    </div>

                    {{-- ALUR 1: JIKA METODE QR CODE (TANPA BUKTI) --}}
                    @if(isset($order->payment_method) && $order->payment_method == 'qr')
                        <div class="text-center mb-4">
                            <div class="p-3 bg-light rounded-4 border border-2 border-dashed d-inline-block mb-3">
                                <img src="{{ asset('images/qr-dummy.png') }}" alt="QR Code" class="img-fluid rounded-3 bg-white p-2 shadow-sm payment-qr-img">
                            </div>
                            <h5 class="text-dark fw-bold mb-1">Scan QR Code Berikut</h5>
                            <p class="text-muted small mb-0">Gunakan aplikasi e-wallet atau m-banking untuk scan dan bayar.</p>
                        </div>

                        <form action="{{ route('order.markPaid', $order->id) }}" method="POST">
                            @csrf
                            <div class="d-flex align-items-center gap-2 mt-4">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-semibold w-50">&larr; Kembali</a>
                                <button type="submit" class="btn text-white px-4 py-2 rounded-pill fw-semibold w-50 btn-payment-main">Konfirmasi Selesai</button>
                            </div>
                        </form>

                    {{-- ALUR 2: JIKA TRANSFER BANK (WAJIB UPLOAD BUKTI MAKS 8MB) --}}
                    @else
                        <div class="p-4 rounded-4 mb-4 text-white payment-bank-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="fw-bold mb-0 text-white">Transfer Bank BCA</h5>
                                <span class="badge bg-white text-success px-2 py-1 rounded-pill small fw-bold">Manual</span>
                            </div>
                            <p class="mb-1 text-white-50 small">No. Rekening:</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <strong class="fs-5 font-monospace payment-rek-number" id="rekNum">1234567890</strong>
                                <button type="button" onclick="navigator.clipboard.writeText('1234567890'); alert('Nomor rekening berhasil disalin!');" class="btn btn-sm btn-light text-success fw-bold px-3 rounded-pill shadow-sm">Salin</button>
                            </div>
                            <hr class="border-light opacity-25 my-2">
                            <p class="mb-0 text-white-50 small">Atas Nama: <strong class="text-white">Furni Store</strong></p>
                        </div>

                        <form action="{{ route('order.uploadProof', $order->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <label for="proof_of_payment" class="form-label fw-bold text-dark small text-uppercase">Unggah Bukti Transfer</label>
                                <input type="file" name="proof_of_payment" id="proof_of_payment" 
                                       class="form-control rounded-3 p-3 @error('proof_of_payment') is-invalid @enderror" 
                                       accept="image/png, image/jpeg, image/jpg" required>
                                <small class="text-muted mt-2 d-block">Format: JPG, JPEG, PNG. Maksimal ukuran file: <strong>8 MB</strong>.</small>
                                
                                @error('proof_of_payment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex align-items-center gap-2 mt-4">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-semibold w-50">&larr; Kembali</a>
                                <button type="submit" class="btn text-white px-4 py-2 rounded-pill fw-semibold w-50 btn-payment-main">Kirim & Konfirmasi</button>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection