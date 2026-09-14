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

            <!-- ADMIN NAVIGATION TABS (Navbar Cepat Antar Menu) -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm bg-white p-2">
                        <ul class="nav nav-pills nav-fill gap-2">
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link active fw-bold py-2">
                                    <i class="fa fa-home me-1"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.products.index') }}" class="nav-link text-dark fw-bold py-2">
                                    <i class="fa fa-box me-1"></i> Produk
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.orders.index') }}" class="nav-link text-dark fw-bold py-2">
                                    <i class="fa fa-shopping-cart me-1"></i> Pesanan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.returns.index') }}" class="nav-link text-dark fw-bold py-2">
                                    <i class="fa fa-undo me-1"></i> Retur
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.coupons.index') }}" class="nav-link text-dark fw-bold py-2">
                                    <i class="fa fa-tags me-1"></i> Kupon
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.users.index') }}" class="nav-link text-dark fw-bold py-2">
                                    <i class="fa fa-users me-1"></i> Pengguna
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.settings.index') }}" class="nav-link text-dark fw-bold py-2">
                                    <i class="fa fa-cogs me-1"></i> Pengaturan
                                </a>
                            </li>
                        </ul>
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