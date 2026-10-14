@extends('layout.app')

@section('title', 'Tambah Kupon - Admin Furni')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">

    <div class="admin-dash">

        <!-- HERO -->
        <div class="hero admin-hero">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <h1>Tambah Kupon Baru</h1>
                        <p>Atur kode, jenis diskon, dan masa berlakunya.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-body">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">

                        @if ($errors->any())
                            <div class="ad-alert ad-alert--error" role="alert">
                                <i class="fa fa-exclamation-circle"></i>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.coupons.store') }}" method="POST" class="form-card">
                            @csrf

                            <div class="form-card-head">
                                <h2>Kupon baru</h2>
                                <p>Kolom bertanda <span class="ad-req">*</span> wajib diisi.</p>
                            </div>

                            <div class="ad-field">
                                <label for="code" class="ad-label">Kode Kupon <span class="ad-req">*</span></label>
                                <input type="text" class="ad-input ad-input--code" id="code" name="code"
                                    placeholder="Contoh: LEBARAN50" required value="{{ old('code') }}">
                                <small class="ad-hint">Kode akan otomatis diubah menjadi huruf kapital.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 ad-field">
                                    <label for="type" class="ad-label">Tipe Diskon <span class="ad-req">*</span></label>
                                    <select name="type" id="type" class="ad-input" required>
                                        <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Potongan Harga Tetap (Rp)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 ad-field">
                                    <label for="value" class="ad-label">Nilai Diskon <span class="ad-req">*</span></label>
                                    <input type="number" step="0.01" class="ad-input" id="value" name="value"
                                        placeholder="Misal: 10 atau 50000" required value="{{ old('value') }}">
                                </div>
                            </div>
                            <small class="ad-hint" style="margin-top: -8px; margin-bottom: 20px;">
                                Isi 10 untuk diskon 10% (tipe Persentase), atau 50000 untuk potongan Rp 50.000 (tipe Tetap).
                            </small>

                            <div class="row">
                                <div class="col-md-6 ad-field">
                                    <label for="min_spend" class="ad-label">Minimal Belanja (Rp)</label>
                                    <input type="number" step="0.01" class="ad-input" id="min_spend" name="min_spend"
                                        placeholder="0 jika tanpa minimal belanja" value="{{ old('min_spend', 0) }}">
                                </div>
                                <div class="col-md-6 ad-field">
                                    <label for="expires_at" class="ad-label">Masa Berakhir (Opsional)</label>
                                    <input type="datetime-local" class="ad-input" id="expires_at" name="expires_at"
                                        value="{{ old('expires_at') }}">
                                </div>
                            </div>

                            <div class="form-actions">
                                <a href="{{ route('admin.coupons.index') }}" class="btn-cancel">Kembali</a>
                                <button type="submit" class="btn-submit">Simpan Kupon</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection