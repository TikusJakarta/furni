@extends('layout.app')

@section('title', 'Kelola Pesanan - Admin Furni')

@section('content')
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1>Kelola Pesanan Masuk</h1>
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
                <div class="col-md-12 d-flex justify-content-end">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary text-white">&larr; Kembali ke
                        Dashboard</a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="site-blocks-table">
                        <table class="table">
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
                                        <td><strong>#{{ $order->id }}</strong></td>
                                        <td>{{ $order->first_name }} {{ $order->last_name }} <br><small
                                                class="text-muted">{{ $order->email }}</small></td>
                                        <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                        <td>{{ strtoupper($order->shipping_courier) }} <br><small>Rp
                                                {{ number_format($order->shipping_cost, 0, ',', '.') }}</small></td>

                                        <td>
                                            @if($order->proof_of_payment)
                                                <a href="{{ asset($order->proof_of_payment) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary px-2 py-1">
                                                    <i class="fa fa-image me-1"></i> Lihat Bukti
                                                </a>
                                            @elseif(isset($order->payment_method) && $order->payment_method == 'qr')
                                                <span class="badge bg-info text-dark">QRIS (Tanpa Bukti)</span>
                                            @else
                                                <span class="badge bg-secondary">Belum ada</span>
                                            @endif
                                        </td>

                                       <td>
    <span class="badge 
        @if($order->status == 'paid' || $order->status == 'completed') bg-success 
        @elseif($order->status == 'pending') bg-secondary 
        @elseif($order->status == 'verifikasi') bg-warning text-dark
        @elseif($order->status == 'packing' || $order->status == 'shipping') bg-info text-dark
        @elseif($order->status == 'shipped') bg-primary
        @else bg-danger @endif">
        
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
                                                <select name="status" class="form-control form-control-sm"
                                                    onchange="this.form.submit()">
                                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>
                                                        Belum Bayar</option>
                                                    <option value="verifikasi" {{ $order->status == 'verifikasi' ? 'selected' : '' }}>Verifikasi</option>
                                                    <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Bayar
                                                        (Lunas)</option>
                                                    <option value="packing" {{ $order->status == 'packing' ? 'selected' : '' }}>
                                                        Kemas Barang</option>
                                                    <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>
                                                        Menentukan Kurir</option>
                                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>
                                                        Dikirim</option>
                                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Belum ada pesanan masuk.</td>
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