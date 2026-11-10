@extends('layout.admin')

@section('title', 'Laporan Penjualan - ' . ($settings['brand_name'] ?? 'Furni'))

@section('admin_content')
    <div class="page-head">
        <div>
            <h1>Laporan Penjualan</h1>
            <p>Ringkasan pesanan dan produk terjual pada periode yang dipilih.</p>
        </div>
        <a href="{{ route('admin.reports.sales.export', request()->except('page')) }}" class="btn-add">
            <i class="fa fa-file-excel"></i> Unduh Excel
        </a>
    </div>

    @if($errors->any())
        <div class="ad-alert ad-alert--error" role="alert">
            <i class="fa fa-exclamation-circle"></i>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FILTER -->
    <form method="GET" action="{{ route('admin.reports.sales') }}" class="log-filters">
        <div class="log-filter">
            <label for="r-from" class="ad-label">Dari tanggal</label>
            <input type="date" id="r-from" name="from" class="ad-input" value="{{ $from->format('Y-m-d') }}">
        </div>

        <div class="log-filter">
            <label for="r-to" class="ad-label">Sampai tanggal</label>
            <input type="date" id="r-to" name="to" class="ad-input" value="{{ $to->format('Y-m-d') }}">
        </div>

        <div class="log-filter">
            <label for="r-status" class="ad-label">Status pesanan</label>
            <select id="r-status" name="status" class="ad-input">
                <option value="paid_group" {{ $status === 'paid_group' ? 'selected' : '' }}>Sudah dibayar</option>
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua status</option>
                @foreach($statusMeta as $key => $meta)
                    <option value="{{ $key }}" {{ $status === $key ? 'selected' : '' }}>{{ $meta[0] }}</option>
                @endforeach
            </select>
        </div>

        <div class="log-filter-actions">
            <button type="submit" class="btn-submit">Tampilkan</button>
            <a href="{{ route('admin.reports.sales') }}" class="btn-cancel">Reset</a>
        </div>
    </form>

    <!-- RINGKASAN -->
    <div class="row g-3 mb-2">
        <div class="col-sm-6 col-xl">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa fa-shopping-cart"></i></div>
                <div>
                    <p class="stat-label">Jumlah Pesanan</p>
                    <p class="stat-value">{{ number_format($summary['orders'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="stat-card stat-card--primary">
                <div class="stat-icon"><i class="fa fa-wallet"></i></div>
                <div>
                    <p class="stat-label">Total Pendapatan</p>
                    <p class="stat-value">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa fa-chart-line"></i></div>
                <div>
                    <p class="stat-label">Rata-rata per Pesanan</p>
                    <p class="stat-value">Rp {{ number_format($summary['average'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa fa-box"></i></div>
                <div>
                    <p class="stat-label">Item Terjual</p>
                    <p class="stat-value">{{ number_format($summary['items'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa fa-truck"></i></div>
                <div>
                    <p class="stat-label">Total Ongkir</p>
                    <p class="stat-value">Rp {{ number_format($summary['shipping'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
    <p class="ad-hint mb-4">
        Periode {{ $from->format('d M Y') }} &ndash; {{ $to->format('d M Y') }} &middot; Status: {{ $statusLabel }}.
        Angka ringkasan menghitung seluruh pesanan pada periode ini, bukan hanya halaman yang sedang dibuka.
    </p>

    <!-- TABEL PESANAN -->
    <div class="panel mb-3">
        <div class="panel-head">
            <h2>Daftar Pesanan</h2>
        </div>
        <div class="table-wrap">
            <table class="orders-table orders-table--wide">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Item</th>
                        <th>Ongkir</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php $meta = $statusMeta[$order->status] ?? [$order->status, 'status-other']; @endphp
                        <tr>
                            <td class="muted">{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td class="order-id">#{{ $order->order_id }}</td>
                            <td class="cell-main">{{ $order->customer_name ?: '-' }}</td>
                            <td class="num">{{ $order->items_qty }}</td>
                            <td class="num">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                            <td class="num"><strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></td>
                            <td><span class="status-badge {{ $meta[1] }}">{{ $meta[0] }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="fa fa-chart-line"></i>
                                Tidak ada pesanan pada periode dan status ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->total() > 0)
            <div class="panel-foot">
                {{ $orders->links('admin.partials.pagination') }}
                <p class="panel-foot-info">
                    Menampilkan {{ $orders->firstItem() }}&ndash;{{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan
                </p>
            </div>
        @endif
    </div>

    <!-- PRODUK TERJUAL -->
    <div class="panel">
        <div class="panel-head">
            <h2>Produk Terjual</h2>
        </div>
        <div class="table-wrap">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Jumlah Terjual</th>
                        <th>Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="muted">{{ $loop->iteration }}</td>
                            <td class="cell-main">{{ $product->product_name }}</td>
                            <td class="num">{{ number_format($product->qty, 0, ',', '.') }}</td>
                            <td class="num">Rp {{ number_format($product->revenue, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">
                                <i class="fa fa-box-open"></i>
                                Belum ada produk terjual pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
