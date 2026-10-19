@extends('layout.admin')

@section('title', 'Edit Produk - Admin Furni')

@section('admin_content')
    <div class="page-head">
        <div>
            <h1>Edit Produk</h1>
            <p>Perbarui data produk, lalu simpan perubahannya.</p>
        </div>
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

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-wide">
            <div class="row g-3">

                <!-- INFORMASI -->
                <div class="col-xl-7">
                    <section class="form-card form-card--compact h-100">
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
                            <div class="col-sm-6 ad-field">
                                <label for="price" class="ad-label">Harga (Rp)</label>
                                <input type="number" id="price" name="price" class="ad-input"
                                    value="{{ old('price', $product->price) }}" required>
                            </div>
                            <div class="col-sm-6 ad-field">
                                <label for="stock" class="ad-label">Stok</label>
                                <input type="number" id="stock" name="stock" class="ad-input"
                                    value="{{ old('stock', $product->stock) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 ad-field">
                                <label for="weight" class="ad-label">Berat (Gram)</label>
                                <input type="number" id="weight" name="weight" class="ad-input"
                                    value="{{ old('weight', $product->weight) }}" required>
                            </div>
                            <div class="col-sm-6 ad-field">
                                <label for="type" class="ad-label">Tipe Produk</label>
                                <select id="type" name="type" class="ad-input" required>
                                    <option value="main" {{ old('type', $product->type) == 'main' ? 'selected' : '' }}>Main (Utama)</option>
                                    <option value="popular" {{ old('type', $product->type) == 'popular' ? 'selected' : '' }}>Popular</option>
                                </select>
                            </div>
                        </div>

                        <div class="ad-field">
                            <label for="description" class="ad-label">Deskripsi</label>
                            <textarea id="description" name="description" class="ad-input"
                                rows="5">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </section>
                </div>

                <!-- GAMBAR -->
                <div class="col-xl-5">
                    <section class="form-card form-card--compact h-100">
                        <div class="form-card-head">
                            <h2>Gambar Produk</h2>
                            <p id="imgCaption">Gambar saat ini di toko.</p>
                        </div>

                        <div class="img-preview has-image" id="imgPreview">
                            <img id="imgPreviewEl" src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                            <div class="img-empty">
                                <i class="fa fa-image" aria-hidden="true"></i>
                                <span>Belum ada gambar</span>
                            </div>
                        </div>

                        <div class="ad-field">
                            <label for="image" class="ad-label">Ganti Gambar (Opsional)</label>
                            <input type="file" id="image" name="image" class="ad-input" accept="image/*">
                            <small class="ad-hint">Biarkan kosong jika tidak ingin mengganti gambar.</small>
                        </div>
                    </section>
                </div>

            </div>

            <!-- BAR SIMPAN -->
            <div class="savebar">
                <p>Perubahan baru tampil di toko setelah disimpan.</p>
                <div class="savebar-actions">
                    <a href="{{ route('admin.products.index') }}" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-submit">Perbarui Produk</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset("js/edit-product.js") }}"></script>
@endpush