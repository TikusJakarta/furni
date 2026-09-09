@extends('layout.app')

@section('title', 'Tambah Kupon - Admin Furni')

@section('content')
<div class="hero">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-5">
                <div class="intro-excerpt">
                    <h1>Tambah Kupon Baru</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="untree_co-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="p-4 p-lg-5 border bg-white rounded">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.coupons.store') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="code" class="text-black font-weight-bold">Kode Kupon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="code" name="code" placeholder="Contoh: LEBARAN50" required value="{{ old('code') }}">
                            <small class="text-muted">Kode akan otomatis diubah menjadi huruf kapital.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="type" class="text-black font-weight-bold">Tipe Diskon <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="percentage">Persentase (%)</option>
                                <option value="fixed">Potongan Harga Tetap (Rp)</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="value" class="text-black font-weight-bold">Nilai Diskon <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="value" name="value" placeholder="Masukkan angka (misal: 10 untuk 10% atau 50000 untuk Rp 50rb)" required value="{{ old('value') }}">
                        </div>

                        <div class="form-group mb-3">
                            <label for="min_spend" class="text-black font-weight-bold">Minimal Belanja (Rp)</label>
                            <input type="number" step="0.01" class="form-control" id="min_spend" name="min_spend" placeholder="0 jika tanpa minimal belanja" value="{{ old('min_spend', 0) }}">
                        </div>

                        <div class="form-group mb-4">
                            <label for="expires_at" class="text-black font-weight-bold">Masa Berakhir (Opsional)</label>
                            <input type="datetime-local" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary text-white">Simpan Kupon</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection