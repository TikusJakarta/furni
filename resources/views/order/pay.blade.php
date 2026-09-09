@extends('layout.app')

@section('title', 'Pembayaran Order #' . $order->id . ' - Furni')

@section('content')
<div class="hero">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-5">
                <div class="intro-excerpt">
                    <h1>Selesaikan Pembayaran</h1>
                    <p class="text-white-50">Order ID: #{{ $order->id }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="untree_co-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 bg-light">
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <h4 class="text-black font-weight-bold mb-2">Total Tagihan</h4>
                    <h2 class="text-primary font-weight-bold mb-4">Rp {{ number_format($order->total_price, 0, ',', '.') }}</h2>

                    <hr class="mb-4">

                    {{-- ALUR 1: JIKA METODE QR CODE (TANPA BUKTI) --}}
                    @if(isset($order->payment_method) && $order->payment_method == 'qr')
                        <div class="text-center mb-4">
                            <h5 class="text-black font-weight-bold mb-3">Scan QR Code Berikut:</h5>
                            <img src="{{ asset('images/qr-dummy.png') }}" alt="QR Code" class="img-fluid rounded border p-2 bg-white" style="max-width: 200px;">
                            <p class="text-muted small mt-2">Gunakan aplikasi e-wallet atau m-banking untuk scan dan bayar.</p>
                        </div>

                        <form action="{{ route('order.markPaid', $order->id) }}" method="POST">
                            @csrf
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary text-white">&larr; Kembali</a>
                                <button type="submit" class="btn btn-success text-white">Konfirmasi Selesai QR</button>
                            </div>
                        </form>

                    {{-- ALUR 2: JIKA TRANSFER BANK (WAJIB UPLOAD BUKTI MAKS 8MB) --}}
                    @else
                        <div class="p-3 bg-white rounded border mb-3">
                            <h5 class="text-black font-weight-bold mb-2">Transfer Bank BCA</h5>
                            <p class="mb-1 text-muted">No. Rekening: <strong>1234567890</strong></p>
                            <p class="mb-0 text-muted">Atas Nama: <strong>Furni Store</strong></p>
                        </div>

                        <form action="{{ route('order.uploadProof', $order->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="proof_of_payment" class="form-label fw-bold text-black">Unggah Bukti Transfer</label>
                                <input type="file" name="proof_of_payment" id="proof_of_payment" 
                                       class="form-control @error('proof_of_payment') is-invalid @enderror" 
                                       accept="image/png, image/jpeg, image/jpg" required>
                                <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal ukuran file: <strong>8 MB</strong>.</small>
                                
                                @error('proof_of_payment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary text-white">&larr; Kembali</a>
                                <button type="submit" class="btn btn-success text-white">Kirim Bukti & Konfirmasi</button>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection