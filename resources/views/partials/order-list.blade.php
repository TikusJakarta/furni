@forelse($orders as $order)
    <div class="card mb-3 shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <div>
                    <h5 class="mb-0 text-black font-weight-bold">Order #{{ $order->id }}</h5>
                    <small class="text-muted">Tanggal: {{ $order->created_at->format('d M Y, H:i') }}</small>
                </div>
                <div>
                    @if($order->status == 'pending')
                        <span class="badge bg-warning text-dark px-3 py-2">Belum Dibayar</span>
                    @elseif($order->status == 'verifikasi')
                        <span class="badge bg-warning text-dark px-3 py-2">Menunggu Verifikasi</span>
                    @elseif($order->status == 'paid')
                        <span class="badge bg-info text-dark px-3 py-2">Pembayaran Diterima</span>
                    @elseif($order->status == 'packing')
                        <span class="badge bg-info text-dark px-3 py-2">Sedang Dikemas</span>
                    @elseif($order->status == 'shipping')
                        <span class="badge bg-info text-dark px-3 py-2">Menentukan Kurir</span>
                    @elseif($order->status == 'shipped')
                        <span class="badge bg-primary px-3 py-2">Sedang Dikirim</span>
                    @elseif($order->status == 'completed')
                        <span class="badge bg-success px-3 py-2">Selesai</span>
                    @elseif($order->status == 'cancelled')
                        <span class="badge bg-danger px-3 py-2">Dibatalkan</span>
                    @else
                        <span class="badge bg-secondary px-3 py-2">{{ ucfirst($order->status) }}</span>
                    @endif
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-8">
                    @foreach($order->orderItems as $item)
                        <div class="d-flex align-items-center mb-2">
                            <img src="{{ asset($item->product->image ?? 'images/product-1.png') }}" alt="" width="50" class="rounded me-3" style="object-fit: cover; height: 50px;">
                            <div>
                                <h6 class="mb-0 text-black">{{ $item->product->name ?? 'Produk tidak ditemukan' }}</h6>
                                <small class="text-muted">{{ $item->quantity }} pcs x Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="text-muted small d-block">Total Tagihan:</span>
                    <h5 class="text-primary font-weight-bold mb-3">Rp {{ number_format($order->total_price, 0, ',', '.') }}</h5>
                    
                    @if($order->status == 'pending')
                        <a href="{{ route('order.pay', $order->id) }}" class="btn btn-black btn-sm px-4">Bayar Sekarang</a>
                    @elseif($order->status == 'verifikasi')
                        <small class="text-muted d-block font-italic">Bukti transfer sedang dicek admin</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="text-center py-5">
        <p class="text-muted">Tidak ada riwayat pesanan pada kategori ini.</p>
    </div>
@endforelse