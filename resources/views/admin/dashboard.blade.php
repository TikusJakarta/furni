@extends('layout.app')

@section('title', ($settings['admin_dashboard_title'] ?? 'Admin Dashboard') . ' - ' . ($settings['brand_name'] ?? 'Furni'))

@section('content')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">

    <div class="admin-dash">

        <!-- HERO -->
        <div class="hero admin-hero">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <h1>{{ $settings['admin_dashboard_title'] ?? 'Admin Dashboard' }}</h1>
                        <p>
                            {{ $settings['admin_dashboard_welcome'] ?? 'Selamat datang kembali, ' . auth()->user()->name . '! Kelola toko kamu dengan mudah dari sini.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-body">
            <div class="container">

                <!-- NAVIGASI ADMIN -->
                <nav class="admin-nav" aria-label="Menu admin">
                    <ul class="nav">
                        <li class="nav-item flex-fill">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link active" aria-current="page">
                                <i class="fa fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item flex-fill">
                            <a href="{{ route('admin.products.index') }}" class="nav-link">
                                <i class="fa fa-box"></i> Produk
                            </a>
                        </li>
                        <li class="nav-item flex-fill">
                            <a href="{{ route('admin.orders.index') }}" class="nav-link">
                                <i class="fa fa-shopping-cart"></i> Pesanan
                            </a>
                        </li>
                        <li class="nav-item flex-fill">
                            <a href="{{ route('admin.returns.index') }}" class="nav-link">
                                <i class="fa fa-undo"></i> Retur
                            </a>
                        </li>
                        <li class="nav-item flex-fill">
                            <a href="{{ route('admin.coupons.index') }}" class="nav-link">
                                <i class="fa fa-tags"></i> Kupon
                            </a>
                        </li>
                        <li class="nav-item flex-fill">
                            <a href="{{ route('admin.users.index') }}" class="nav-link">
                                <i class="fa fa-users"></i> Pengguna
                            </a>
                        </li>
                        <li class="nav-item flex-fill">
                            <a href="{{ route('admin.settings.index') }}" class="nav-link">
                                <i class="fa fa-cogs"></i> Pengaturan
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- STATISTIK -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fa fa-box"></i></div>
                            <div>
                                <p class="stat-label">{{ $settings['admin_card_total_products'] ?? 'Total Produk' }}</p>
                                <p class="stat-value">{{ $totalProducts }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fa fa-shopping-cart"></i></div>
                            <div>
                                <p class="stat-label">{{ $settings['admin_card_total_orders'] ?? 'Total Pesanan' }}</p>
                                <p class="stat-value">{{ $totalOrders }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card stat-card--primary">
                            <div class="stat-icon"><i class="fa fa-wallet"></i></div>
                            <div>
                                <p class="stat-label">{{ $settings['admin_card_total_revenue'] ?? 'Pendapatan (Paid)' }}</p>
                                <p class="stat-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PESANAN TERBARU -->
                <div class="panel">
                    <div class="panel-head">
                        <h2>{{ $settings['admin_recent_orders_title'] ?? 'Pesanan Terbaru Masuk' }}</h2>
                        <a href="{{ route('admin.orders.index') }}" class="section-link">
                            {{ $settings['admin_link_view_all_orders'] ?? 'Lihat Semua Pesanan' }} &rarr;
                        </a>
                    </div>

                    <div class="table-wrap">
                        <table class="orders-table">
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
                                        <td class="order-id">#{{ $order->id }}</td>
                                        <td>{{ $order->first_name }} {{ $order->last_name }}</td>
                                        <td class="num">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="status-badge
                                                @if($order->status == 'paid') status-paid
                                                @elseif($order->status == 'pending') status-pending
                                                @elseif($order->status == 'completed') status-completed
                                                @else status-other @endif">
                                                {{ strtoupper($order->status) }}
                                            </span>
                                        </td>
                                        <td class="muted">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="empty-state">
                                            <i class="fa fa-inbox"></i>
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