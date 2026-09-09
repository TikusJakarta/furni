@extends('layout.app')

@section('title', 'Kelola Kupon Promo - Admin Furni')

@section('content')
<div class="hero">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-5">
                <div class="intro-excerpt">
                    <h1>Kelola Kode Promo</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="untree_co-section before-footer-section">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row mb-4">
            <div class="col-md-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary text-white">+ Buat Kupon Baru</a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary text-white">&larr; Kembali ke Dashboard</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="site-blocks-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode Promo</th>
                                <th>Tipe</th>
                                <th>Nilai Diskon</th>
                                <th>Min. Belanja</th>
                                <th>Masa Berakhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $coupon)
                            <tr>
                                <td><strong class="text-primary">{{ $coupon->code }}</strong></td>
                                <td>{{ ucfirst($coupon->type) }}</td>
                                <td>
                                    @if($coupon->type == 'percentage')
                                        {{ $coupon->value }}%
                                    @else
                                        Rp {{ number_format($coupon->value, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td>Rp {{ number_format($coupon->min_spend, 0, ',', '.') }}</td>
                                <td>{{ $coupon->expires_at ? \Carbon\Carbon::parse($coupon->expires_at)->format('d M Y, H:i') : 'Selamanya' }}</td>
                                <td>
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kupon ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada kode promo tersedia.</td>
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