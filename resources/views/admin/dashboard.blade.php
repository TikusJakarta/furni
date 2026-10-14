@extends('layout.admin')

@section('title', ($settings['admin_dashboard_title'] ?? 'Admin Dashboard') . ' - ' . ($settings['brand_name'] ?? 'Furni'))

@section('admin_content')
    @php
        // Label & warna status (sama dengan halaman Pesanan)
        $statusMeta = [
            'pending'    => ['Belum Bayar',       'status-unpaid',    'seg-unpaid'],
            'verifikasi' => ['Verifikasi',        'status-verify',    'seg-verify'],
            'paid'       => ['Bayar',             'status-paid',      'seg-paid'],
            'packing'    => ['Kemas Barang',      'status-process',   'seg-process'],
            'shipping'   => ['Menentukan Kurir',  'status-process',   'seg-process2'],
            'shipped'    => ['Dikirim',           'status-shipped',   'seg-shipped'],
            'completed'  => ['Selesai',           'status-completed', 'seg-completed'],
            'cancelled'  => ['Dibatalkan',        'status-other',     'seg-other'],
        ];

        // Data tambahan dari controller. Semuanya opsional: kalau belum dikirim, bagian terkait disembunyikan.
        $hasExtra = isset($salesChart);
        $statCol  = isset($totalCustomers) ? 'col-sm-6 col-xl-3' : 'col-md-4';

        $short = function ($n) {
            if ($n >= 1000000) return rtrim(rtrim(number_format($n / 1000000, 1, ',', '.'), '0'), ',') . ' jt';
            if ($n >= 1000)    return number_format($n / 1000, 0, ',', '.') . ' rb';
            return (string) (int) $n;
        };

        $trend = null;
        if (isset($revenueThisMonth, $revenueLastMonth) && $revenueLastMonth > 0) {
            $trend = round(($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth * 100);
        }

        $chart      = collect($salesChart ?? []);
        $chartMax   = max(1, (int) $chart->max('total'));
        $chartTotal = (int) $chart->sum('total');

        $counts      = collect($statusCounts ?? []);
        $countsTotal = (int) $counts->sum();
    @endphp

    <!-- KEPALA -->
    <div class="page-head">
        <div>
            <p class="welcome-line">
                {{ $settings['admin_dashboard_welcome'] ?? 'Selamat datang kembali, ' . auth()->user()->name . '! Kelola toko kamu dengan mudah dari sini.' }}
            </p>
            <p>{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn-add">
            <i class="fa fa-plus"></i> Tambah Produk
        </a>
    </div>

    <!-- STATISTIK -->
    <div class="row g-3 mb-4">
        <div class="{{ $statCol }}">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa fa-box"></i></div>
                <div>
                    <p class="stat-label">{{ $settings['admin_card_total_products'] ?? 'Total Produk' }}</p>
                    <p class="stat-value">{{ $totalProducts }}</p>
                    @isset($lowStockCount)
                        <p class="stat-meta">{{ $lowStockCount }} stok menipis</p>
                    @endisset
                </div>
            </div>
        </div>
        <div class="{{ $statCol }}">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa fa-shopping-cart"></i></div>
                <div>
                    <p class="stat-label">{{ $settings['admin_card_total_orders'] ?? 'Total Pesanan' }}</p>
                    <p class="stat-value">{{ $totalOrders }}</p>
                    @isset($pendingVerification)
                        <p class="stat-meta">{{ $pendingVerification }} menunggu verifikasi</p>
                    @endisset
                </div>
            </div>
        </div>
        @isset($totalCustomers)
            <div class="{{ $statCol }}">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa fa-users"></i></div>
                    <div>
                        <p class="stat-label">Total Pelanggan</p>
                        <p class="stat-value">{{ $totalCustomers }}</p>
                        <p class="stat-meta">Akun terdaftar</p>
                    </div>
                </div>
            </div>
        @endisset
        <div class="{{ $statCol }}">
            <div class="stat-card stat-card--primary">
                <div class="stat-icon"><i class="fa fa-wallet"></i></div>
                <div>
                    <p class="stat-label">{{ $settings['admin_card_total_revenue'] ?? 'Pendapatan (Paid)' }}</p>
                    <p class="stat-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    @isset($revenueThisMonth)
                        <p class="stat-meta">
                            @if($trend !== null)
                                <span class="trend {{ $trend >= 0 ? 'trend--up' : 'trend--down' }}">
                                    <i class="fa fa-arrow-{{ $trend >= 0 ? 'up' : 'down' }}"></i> {{ abs($trend) }}%
                                </span>
                            @endif
                            Bulan ini Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}
                        </p>
                    @endisset
                </div>
            </div>
        </div>
    </div>

    @if($hasExtra)
        <!-- PERLU TINDAKAN -->
        <div class="todo-grid">
            <a href="{{ route('admin.orders.index') }}" class="todo-tile {{ ($pendingVerification ?? 0) > 0 ? 'is-alert' : '' }}">
                <span class="todo-count">{{ $pendingVerification ?? 0 }}</span>
                <span>
                    <span class="todo-title">Verifikasi pembayaran</span>
                    <span class="todo-sub">{{ ($pendingVerification ?? 0) > 0 ? 'Pesanan menunggu dicek' : 'Semua sudah dicek' }}</span>
                </span>
                <i class="fa fa-chevron-right todo-arrow"></i>
            </a>
            <a href="{{ route('admin.returns.index') }}" class="todo-tile {{ ($pendingReturns ?? 0) > 0 ? 'is-alert' : '' }}">
                <span class="todo-count">{{ $pendingReturns ?? 0 }}</span>
                <span>
                    <span class="todo-title">Retur menunggu</span>
                    <span class="todo-sub">{{ ($pendingReturns ?? 0) > 0 ? 'Pengajuan belum diproses' : 'Tidak ada pengajuan' }}</span>
                </span>
                <i class="fa fa-chevron-right todo-arrow"></i>
            </a>
            <a href="{{ route('admin.products.index') }}" class="todo-tile {{ ($lowStockCount ?? 0) > 0 ? 'is-alert' : '' }}">
                <span class="todo-count">{{ $lowStockCount ?? 0 }}</span>
                <span>
                    <span class="todo-title">Stok menipis</span>
                    <span class="todo-sub">{{ ($lowStockCount ?? 0) > 0 ? 'Produk perlu diisi ulang' : 'Stok aman' }}</span>
                </span>
                <i class="fa fa-chevron-right todo-arrow"></i>
            </a>
        </div>

        <!-- GRAFIK -->
        <div class="row g-3 mb-4">
            <div class="col-lg-7">
                <div class="panel h-100">
                    <div class="panel-head">
                        <h2>Penjualan 7 Hari Terakhir</h2>
                        <span class="chart-total">Rp {{ number_format($chartTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="panel-body">
                        <div class="bar-chart" role="img" aria-label="Grafik penjualan 7 hari terakhir">
                            @foreach($chart as $day)
                                @php $pct = $day['total'] > 0 ? max(4, round($day['total'] / $chartMax * 100)) : 0; @endphp
                                <div class="bar-col" title="{{ $day['date'] }}: Rp {{ number_format($day['total'], 0, ',', '.') }}">
                                    <div class="bar-track">
                                        <div class="bar {{ $loop->last ? 'bar--today' : '' }} {{ $day['total'] == 0 ? 'bar--empty' : '' }}"
                                            style="height: {{ $pct }}%">
                                            @if($day['total'] > 0)
                                                <span class="bar-value">{{ $short($day['total']) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="bar-label">{{ $day['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                        <p class="chart-note">Hanya pesanan yang sudah dibayar.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="panel h-100">
                    <div class="panel-head">
                        <h2>Status Pesanan</h2>
                        <span class="chart-total">{{ $countsTotal }} pesanan</span>
                    </div>
                    <div class="panel-body">
                        @if($countsTotal > 0)
                            <div class="stack-bar" role="img" aria-label="Komposisi status pesanan">
                                @foreach($statusMeta as $key => $meta)
                                    @if(($counts[$key] ?? 0) > 0)
                                        <span class="{{ $meta[2] }}" style="width: {{ ($counts[$key] / $countsTotal) * 100 }}%"
                                            title="{{ $meta[0] }}: {{ $counts[$key] }}"></span>
                                    @endif
                                @endforeach
                            </div>
                            <ul class="legend">
                                @foreach($statusMeta as $key => $meta)
                                    @php $n = $counts[$key] ?? 0; @endphp
                                    <li>
                                        <span class="dot {{ $meta[2] }}"></span>
                                        <span class="name">{{ $meta[0] }}</span>
                                        <span class="count">{{ $n }}</span>
                                        <span class="pct">{{ round($n / $countsTotal * 100) }}%</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="list-empty">Belum ada data pesanan.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-3 mb-3">
        <!-- PESANAN TERBARU -->
        <div class="{{ $hasExtra ? 'col-xl-8' : 'col-12' }}">
            <div class="panel h-100">
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
                                @php $meta = $statusMeta[$order->status] ?? [strtoupper($order->status), 'status-other', '']; @endphp
                                <tr>
                                    <td class="order-id">#{{ $order->id }}</td>
                                    <td>{{ $order->first_name }} {{ $order->last_name }}</td>
                                    <td class="num">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                    <td><span class="status-badge {{ $meta[1] }}">{{ $meta[0] }}</span></td>
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

        @if($hasExtra)
            <!-- PRODUK TERLARIS -->
            <div class="col-xl-4">
                <div class="panel h-100">
                    <div class="panel-head">
                        <h2>Produk Terlaris</h2>
                        <a href="{{ route('admin.products.index') }}" class="section-link">Semua produk &rarr;</a>
                    </div>
                    @if(collect($topProducts ?? [])->isEmpty())
                        <div class="list-empty">Belum ada penjualan.</div>
                    @else
                        <ul class="list-rows">
                            @foreach($topProducts as $p)
                                <li>
                                    <span class="rank">{{ $loop->iteration }}</span>
                                    <img src="{{ asset($p->image) }}" alt="{{ $p->name }}" class="product-thumb" loading="lazy">
                                    <div class="main">
                                        <span class="name">{{ $p->name }}</span>
                                        <span class="sub">Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                                    </div>
                                    <span class="sold-count">{{ (int) $p->sold }} terjual</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endif
    </div>

    @if($hasExtra)
        <div class="row g-3">
            <!-- STOK MENIPIS -->
            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-head">
                        <h2>Stok Menipis</h2>
                        <a href="{{ route('admin.products.index') }}" class="section-link">Kelola stok &rarr;</a>
                    </div>
                    @if(collect($lowStockProducts ?? [])->isEmpty())
                        <div class="list-empty">Semua stok aman.</div>
                    @else
                        <ul class="list-rows">
                            @foreach($lowStockProducts as $p)
                                <li>
                                    <img src="{{ asset($p->image) }}" alt="{{ $p->name }}" class="product-thumb" loading="lazy">
                                    <div class="main">
                                        <span class="name">{{ $p->name }}</span>
                                        <span class="sub">Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                                    </div>
                                    <span class="stock {{ $p->stock <= 0 ? 'stock--out' : 'stock--low' }}">
                                        <span class="stock-flag">{{ $p->stock <= 0 ? 'Habis' : 'Sisa ' . $p->stock }}</span>
                                    </span>
                                    <a href="{{ route('admin.products.edit', $p->id) }}" class="btn-row btn-row--edit">
                                        <i class="fa fa-pen"></i> Edit
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- PELANGGAN BARU -->
            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-head">
                        <h2>Pelanggan Baru</h2>
                        <a href="{{ route('admin.users.index') }}" class="section-link">Semua pengguna &rarr;</a>
                    </div>
                    @if(collect($newCustomers ?? [])->isEmpty())
                        <div class="list-empty">Belum ada pelanggan.</div>
                    @else
                        <ul class="list-rows">
                            @foreach($newCustomers as $c)
                                <li>
                                    <span class="user-avatar" aria-hidden="true">{{ strtoupper(mb_substr($c->name, 0, 1)) }}</span>
                                    <div class="main">
                                        <span class="name">{{ $c->name }}</span>
                                        <span class="sub">{{ $c->email }}</span>
                                    </div>
                                    <span class="sub">{{ $c->created_at->format('d M Y') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endsection
