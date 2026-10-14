@extends('layout.app')

@section('title', 'Kelola Kupon Promo - Admin Furni')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">

    <div class="admin-dash">

        <!-- HERO -->
        <div class="hero admin-hero">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <h1>Kelola Kode Promo</h1>
                        <p>Buat dan atur kupon diskon untuk pelanggan.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-body">
            <div class="container">

                <div class="page-toolbar page-toolbar--split">
                    <a href="{{ route('admin.coupons.create') }}" class="btn-add">
                        <i class="fa fa-plus"></i> Buat Kupon Baru
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
                                    @php
                                        $expiresAt = $coupon->expires_at ? \Carbon\Carbon::parse($coupon->expires_at) : null;
                                        $isExpired = $expiresAt && $expiresAt->isPast();
                                    @endphp
                                    <tr>
                                        <td><span class="coupon-code">{{ $coupon->code }}</span></td>
                                        <td><span class="pill pill--muted">{{ ucfirst($coupon->type) }}</span></td>
                                        <td class="discount-value">
                                            @if($coupon->type == 'percentage')
                                                {{ $coupon->value }}%
                                            @else
                                                Rp {{ number_format($coupon->value, 0, ',', '.') }}
                                            @endif
                                        </td>
                                        <td class="num">Rp {{ number_format($coupon->min_spend, 0, ',', '.') }}</td>
                                        <td>
                                            @if($expiresAt)
                                                <span class="expiry {{ $isExpired ? 'expiry--expired' : '' }}">
                                                    {{ $expiresAt->format('d M Y, H:i') }}
                                                </span>
                                                @if($isExpired)
                                                    <span class="expiry-flag">Kedaluwarsa</span>
                                                @endif
                                            @else
                                                <span class="expiry expiry--forever">Selamanya</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus kupon ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-row btn-row--delete">
                                                    <i class="fa fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="empty-state">
                                            <i class="fa fa-tags"></i>
                                            Belum ada kode promo tersedia.
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