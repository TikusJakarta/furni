@extends('layout.app')

@section('title', 'Tambah Produk - Admin Furni')

@section('content')
<div class="hero">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-5">
                <div class="intro-excerpt">
                    <h1>Tambah Produk</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="untree_co-section before-footer-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-light p-4 rounded shadow-sm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Nama Produk</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Harga (Rp)</label>
                            <input type="number" name="price" class="form-control" value="{{ old('price') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Stok</label>
                            <input type="number" name="stock" class="form-control" value="{{ old('stock', 10) }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Berat (Gram)</label>
                            <input type="number" name="weight" class="form-control" value="{{ old('weight', 1000) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Tipe Produk</label>
                            <select name="type" class="form-control" required>
                                <option value="main">Main (Utama)</option>
                                <option value="popular">Popular</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Gambar Produk</label>
                        <input type="file" name="image" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary text-white w-100 py-3">Simpan Produk</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection