@extends('layout.app')

@section('title', ($settings['admin_dashboard_title'] ?? 'Admin Dashboard') . ' - ' . ($settings['brand_name'] ?? 'Furni'))

@section('content')
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1>{{ $settings['admin_dashboard_title'] ?? 'Admin Dashboard' }}</h1>
                        <p class="text-white-50">
                            {{ $settings['admin_dashboard_welcome'] ?? 'Selamat datang kembali, ' . auth()->user()->name . '! Kelola toko kamu dengan mudah dari sini.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="untree_co-section before-footer-section">
        <div class="container">
            
            <!-- Tombol Navigasi Cepat / Quick Links -->
            <div class="row mb-5">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm p-4 bg-light">
                        <h4 class="mb-3 text-black font-weight-bold">
                            {{ $settings['admin_quick_menu_title'] ?? 'Menu Cepat Admin' }}
                        </h4>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-primary me-2 mb-2 px-3 py-2" style="font-size: 15px !important; color: #ffffff !important; display: inline-flex; align-items: center;">
                                <i class="fa fa-box me-2"></i> Kelola Produk
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-dark me-2 mb-2 px-3 py-2" style="font-size: 15px !important; color: #ffffff !important; display: inline-flex; align-items: center;">
                                <i class="fa fa-shopping-cart me-2"></i> Kelola Pesanan
                            </a>
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-success me-2 mb-2 px-3 py-2" style="font-size: 15px !important; color: #ffffff !important; display: inline-flex; align-items: center;">
                                <i class="fa fa-tags me-2"></i> Kelola Kupon Promo
                            </a>
                            <!-- Tombol Kelola Pengguna dipaksa warna kuning terang & teks hitam -->
                            <a href="{{ route('admin.users.index') }}" class="me-2 mb-2 px-3 py-2 text-dark" style="background-color: #ffc107 !important; color: #000000 !important; font-size: 15px !important; font-weight: 500; border-radius: 5px; display: inline-flex; align-items: center; text-decoration: none;">
                                <i class="fa fa-users me-2"></i> Kelola Pengguna
                            </a>
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary me-2 mb-2 px-3 py-2" style="font-size: 15px !important; color: #ffffff !important; display: inline-flex; align-items: center;">
                                <i class="fa fa-cogs me-2"></i> CMS Pengaturan Web
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Statistik / Cards -->
            <div class="row mb-5">
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm p-4 bg-light">
                        <h6 class="text-muted text-uppercase small">
                            {{ $settings['admin_card_total_products'] ?? 'Total Produk' }}
                        </h6>
                        <h3 class="font-weight-bold text-black">{{ $totalProducts }}</h3>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm p-4 bg-light">
                        <h6 class="text-muted text-uppercase small">
                            {{ $settings['admin_card_total_orders'] ?? 'Total Pesanan' }}
                        </h6>
                        <h3 class="font-weight-bold text-black">{{ $totalOrders }}</h3>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm p-4 bg-light">
                        <h6 class="text-muted text-uppercase small">
                            {{ $settings['admin_card_total_revenue'] ?? 'Pendapatan (Paid)' }}
                        </h6>
                        <h3 class="font-weight-bold text-success">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>

            <!-- Tabel Pesanan Terbaru -->
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h4 text-black m-0">
                            {{ $settings['admin_recent_orders_title'] ?? 'Pesanan Terbaru Masuk' }}
                        </h3>
                        <a href="{{ route('admin.orders.index') }}" class="text-decoration-underline small">
                            {{ $settings['admin_link_view_all_orders'] ?? 'Lihat Semua Pesanan' }} &rarr;
                        </a>
                    </div>
                    <div class="site-blocks-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ $settings['admin_th_order_id'] ?? 'ID Order' }}</th>
                                    <th>{{ $settings['admin_th_customer'] ?? 'Pemesan' }}</th>
                                    <th>{{ $settings['admin_th_total_price'] ?? 'Total Harga' }}</th>
                                    <th>{{ $settings['admin_th_status'] ?? 'Status' }}</th>
                                    <th>{{ $settings['admin_th_date'] ?? 'Tanggal' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>{{ $order->first_name }} {{ $order->last_name }}</td>
                                    <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($order->status == 'paid') bg-success 
                                            @elseif($order->status == 'pending') bg-warning text-dark 
                                            @elseif($order->status == 'completed') bg-info text-dark
                                            @else bg-danger @endif">
                                            {{ strtoupper($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        {{ $settings['admin_empty_orders'] ?? 'Belum ada pesanan masuk.' }}
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