@extends('layout.admin')

@section('title', 'Kelola Pesanan - Admin Furni')

@section('admin_content')
    <div class="page-head">
        <div>
            <h1>Kelola Pesanan Masuk</h1>
            <p>Cek bukti bayar, lalu ubah status pesanan sesuai progresnya.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="ad-alert ad-alert--success" role="status">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="panel">
        <div class="table-wrap">
            <table class="orders-table orders-table--wide">
                <thead>
                    <tr>
                        <th>ID Order</th>
                        <th>Pemesan</th>
                        <th>Total Harga</th>
                        <th>Kurir & Ongkir</th>
                        <th>Bukti Bayar</th>
                        <th>Status Saat Ini</th>
                        <th>Ubah Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="order-id">#{{ $order->id }}</td>

                            <td>
                                <span class="cell-main">{{ $order->first_name }} {{ $order->last_name }}</span>
                                <span class="cell-sub">{{ $order->email }}</span>
                            </td>

                            <td class="num">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>

                            <td>
                                <span class="courier">{{ strtoupper($order->shipping_courier) }}</span>
                                <span class="cell-sub num">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </td>

                            <td>
                                @if($order->proof_of_payment)
                                    <a href="{{ asset($order->proof_of_payment) }}" target="_blank" rel="noopener"
                                        class="btn-proof">
                                        <i class="fa fa-image"></i> Lihat Bukti
                                    </a>
                                @elseif(isset($order->payment_method) && $order->payment_method == 'qr')
                                    <span class="pill pill--info">QRIS (Tanpa Bukti)</span>
                                @else
                                    <span class="pill pill--muted">Belum ada</span>
                                @endif
                            </td>

                            <td>
                                <span class="status-badge
                                    @if($order->status == 'paid') status-paid
                                    @elseif($order->status == 'completed') status-completed
                                    @elseif($order->status == 'pending') status-unpaid
                                    @elseif($order->status == 'verifikasi') status-verify
                                    @elseif($order->status == 'packing' || $order->status == 'shipping') status-process
                                    @elseif($order->status == 'shipped') status-shipped
                                    @else status-other @endif">
                                    @switch($order->status)
                                        @case('pending') Belum Bayar @break
                                        @case('verifikasi') Verifikasi @break
                                        @case('paid') Bayar @break
                                        @case('packing') Kemas Barang @break
                                        @case('shipping') Menentukan Kurir @break
                                        @case('shipped') Dikirim @break
                                        @case('completed') Selesai @break
                                        @case('cancelled') Dibatalkan @break
                                        @default {{ strtoupper($order->status) }}
                                    @endswitch
                                </span>
                            </td>

                            <td>
                                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                    @csrf
                                    <select name="status" class="status-select"
                                        aria-label="Ubah status pesanan #{{ $order->id }}"
                                        onchange="this.form.submit()">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Belum Bayar</option>
                                        <option value="verifikasi" {{ $order->status == 'verifikasi' ? 'selected' : '' }}>Verifikasi</option>
                                        <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Bayar (Lunas)</option>
                                        <option value="packing" {{ $order->status == 'packing' ? 'selected' : '' }}>Kemas Barang</option>
                                        <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>Menentukan Kurir</option>
                                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="fa fa-inbox"></i>
                                Belum ada pesanan masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
