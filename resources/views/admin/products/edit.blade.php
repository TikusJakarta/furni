@extends('layout.app')

@section('title', 'Edit Produk - Admin Furni')

@section('content')
<div class="hero">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-5">
                <div class="intro-excerpt">
                    <h1>Edit Produk</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="untree_co-section before-footer-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="bg-light p-4 rounded shadow-sm">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Nama Produk</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Harga (Rp)</label>
                            <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Stok</label>
                            <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Berat (Gram)</label>
                            <input type="number" name="weight" class="form-control" value="{{ old('weight', $product->weight) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Tipe Produk</label>
                            <select name="type" class="form-control" required>
                                <option value="main" {{ old('type', $product->type) == 'main' ? 'selected' : '' }}>Main (Utama)</option>
                                <option value="popular" {{ old('type', $product->type) == 'popular' ? 'selected' : '' }}>Popular</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Gambar Produk Saat Ini</label>
                        <div class="mb-2">
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" width="100" class="rounded shadow-sm">
                        </div>
                        <label class="form-label font-weight-bold">Ganti Gambar (Opsional)</label>
                        <input type="file" name="image" class="form-control">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti gambar produk.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary text-white w-100 py-3">Perbarui Produk</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection