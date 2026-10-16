@extends('layout.admin')

@section('title', 'Kelola Produk - Admin Furni')

@section('admin_content')
    <div class="page-head">
        <div>
            <h1>Kelola Produk</h1>
            <p>Tambah, ubah, atau hapus produk yang dijual di toko.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn-add">
            <i class="fa fa-plus"></i> Tambah Produk Baru
        </a>
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

        @if($products->total() > 0)
            <div class="panel-foot">
                {{ $products->links('admin.partials.pagination') }}
                <p class="panel-foot-info">
                    Menampilkan {{ $products->firstItem() }}&ndash;{{ $products->lastItem() }} dari {{ $products->total() }} produk
                </p>
            </div>
        @endif
    </div>
@endsection
