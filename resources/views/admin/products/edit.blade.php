@extends('layout.app')

@section('title', 'Edit Produk - Admin Furni')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">

    <div class="admin-dash">

        <!-- HERO -->
        <div class="hero admin-hero">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <h1>Edit Produk</h1>
                        <p>Perbarui data produk, lalu simpan perubahannya.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-body">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">

                        <div class="page-toolbar">
                            <a href="{{ route('admin.products.index') }}" class="btn-back">&larr; Kembali ke Daftar Produk</a>
                        </div>

                        @if($errors->any())
                            <div class="ad-alert ad-alert--error" role="alert">
                                <i class="fa fa-exclamation-circle"></i>
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.products.update', $product->id) }}" method="POST"
                            enctype="multipart/form-data" class="form-card">
                            @csrf
                            @method('PUT')

                            <div class="form-card-head">
                                <h2>{{ $product->name }}</h2>
                                <p>Semua kolom wajib diisi, kecuali gambar dan deskripsi.</p>
                            </div>

                            <div class="ad-field">
                                <label for="name" class="ad-label">Nama Produk</label>
                                <input type="text" id="name" name="name" class="ad-input"
                                    value="{{ old('name', $product->name) }}" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 ad-field">
                                    <label for="price" class="ad-label">Harga (Rp)</label>
                                    <input type="number" id="price" name="price" class="ad-input"
                                        value="{{ old('price', $product->price) }}" required>
                                </div>
                                <div class="col-md-6 ad-field">
                                    <label for="stock" class="ad-label">Stok</label>
                                    <input type="number" id="stock" name="stock" class="ad-input"
                                        value="{{ old('stock', $product->stock) }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 ad-field">
                                    <label for="weight" class="ad-label">Berat (Gram)</label>
                                    <input type="number" id="weight" name="weight" class="ad-input"
                                        value="{{ old('weight', $product->weight) }}" required>
                                </div>
                                <div class="col-md-6 ad-field">
                                    <label for="type" class="ad-label">Tipe Produk</label>
                                    <select id="type" name="type" class="ad-input" required>
                                        <option value="main" {{ old('type', $product->type) == 'main' ? 'selected' : '' }}>Main (Utama)</option>
                                        <option value="popular" {{ old('type', $product->type) == 'popular' ? 'selected' : '' }}>Popular</option>
                                    </select>
                                </div>
                            </div>

                            <div class="ad-field">
                                <span class="ad-label">Gambar Produk</span>
                                <div class="current-image">
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                                    <div>
                                        <strong>Gambar saat ini</strong>
                                        <span>Ini yang tampil di toko sekarang.</span>
                                    </div>
                                </div>
                                <label for="image" class="ad-label">Ganti Gambar (Opsional)</label>
                                <input type="file" id="image" name="image" class="ad-input" accept="image/*">
                                <small class="ad-hint">Biarkan kosong jika tidak ingin mengganti gambar produk.</small>
                            </div>

                            <div class="ad-field">
                                <label for="description" class="ad-label">Deskripsi</label>
                                <textarea id="description" name="description" class="ad-input"
                                    rows="4">{{ old('description', $product->description) }}</textarea>
                            </div>

                            <div class="form-actions">
                                <a href="{{ route('admin.products.index') }}" class="btn-cancel">Batal</a>
                                <button type="submit" class="btn-submit">Perbarui Produk</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection