@extends('layout.app')

@section('title', 'Kelola Produk - Admin Furni')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">

    <div class="admin-dash">

        <!-- HERO -->
        <div class="hero admin-hero">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <h1>Kelola Produk</h1>
                        <p>Tambah, ubah, atau hapus produk yang dijual di toko.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-body">
            <div class="container">

                <div class="page-toolbar page-toolbar--split">
                    <a href="{{ route('admin.products.create') }}" class="btn-add">
                        <i class="fa fa-plus"></i> Tambah Produk Baru
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="btn-back">&larr; Kembali ke Dashboard</a>
                </div>

                @if(session('success'))
                    <div class="ad-alert ad-alert--success" role="status">
                        <i class="fa fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                <div class="panel">
                    <div class="table-wrap">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th>Nama Produk</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Berat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                                                class="product-thumb" loading="lazy">
                                        </td>
                                        <td class="product-name">{{ $product->name }}</td>
                                        <td class="num">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="stock {{ $product->stock <= 0 ? 'stock--out' : ($product->stock <= 5 ? 'stock--low' : '') }}">
                                                {{ $product->stock }}
                                                @if($product->stock <= 0)
                                                    <span class="stock-flag">Habis</span>
                                                @elseif($product->stock <= 5)
                                                    <span class="stock-flag">Menipis</span>
                                                @endif
                                            </span>
                                        </td>
                                        <td class="num">{{ $product->weight }} gram</td>
                                        <td>
                                            <div class="row-actions">
                                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                                    class="btn-row btn-row--edit">
                                                    <i class="fa fa-pen"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-row btn-row--delete">
                                                        <i class="fa fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="empty-state">
                                            <i class="fa fa-box-open"></i>
                                            Belum ada produk tersedia.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection