@forelse($orders as $order)
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <span class="fw-bold text-black">Order ID: #{{ $order->id }}</span>
                <span class="text-muted small ms-3">Tanggal: {{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div>
                @php
                    $badgeColors = [
                        'pending' => 'warning text-dark',
                        'verifikasi' => 'info text-dark',
                        'paid' => 'primary',
                        'packing' => 'primary',
                        'shipping' => 'info text-white',
                        'shipped' => 'info text-white',
                        'completed' => 'success',
                        'cancelled' => 'danger'
                    ];
                @endphp
                <span class="badge bg-{{ $badgeColors[$order->status] ?? 'secondary' }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>

        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <ul class="list-unstyled mb-0">
                        @foreach($order->orderItems as $item)
                            <li class="mb-2 d-flex justify-content-between align-items-center border-bottom pb-2">
                                <div>
                                    <span class="fw-bold text-black">{{ $item->product->name ?? 'Produk Dihapus' }}</span> 
                                    <span class="text-muted small">({{ $item->quantity }}x Rp {{ number_format($item->price, 0, ',', '.') }})</span>
                                </div>
                                
                                <!-- Tombol Beli Ulang per Produk -->
                                <div>
                                    @if($item->product)
                                        <a href="{{ route('cart.add', $item->product_id) }}" class="btn btn-outline-dark btn-sm py-0 px-2" style="font-size: 12px;">
                                            <i class="fa fa-redo me-1"></i> Beli Ulang
                                        </a>
                                    @else
                                        <span class="badge bg-secondary" style="font-size: 11px;">Tidak Tersedia</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="text-muted small d-block">Total Pembayaran:</span>
                    <h5 class="text-black fw-bold mb-0">Rp {{ number_format($order->total_price, 0, ',', '.') }}</h5>
                </div>
            </div>

            <!-- STATUS PROTEKSI DI DALAM CARD BODY -->
            <div class="mt-3 pt-2 border-top">
                @if($order->is_protected)
                    <span class="text-success small fw-bold">
                        <i class="fa fa-shield-alt me-1"></i> Dilindungi Garansi Retur (Biaya: Rp {{ number_format($order->protection_fee, 0, ',', '.') }})
                    </span>
                @else
                    <span class="text-muted small">
                        <i class="fa fa-info-circle me-1"></i> Tanpa Proteksi (Pesanan bersifat final & tidak ada garansi retur)
                    </span>
                @endif
            </div>
        </div>

        <div class="card-footer bg-light d-flex justify-content-between align-items-center py-2">
            <div>
                @if($order->status == 'pending')
                    <a href="{{ route('order.pay', $order->id) }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-credit-card me-1"></i> Bayar Sekarang
                    </a>
                @endif
            </div>

            <div>
                <!-- Tombol Ajukan Retur (Hanya muncul jika MEMILIKI PROTEKSI DAN status 'completed' / 'shipped') -->
                @if($order->is_protected && in_array($order->status, ['completed', 'shipped']))
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#returnModal{{ $order->id }}">
                        <i class="fa fa-undo me-1"></i> Ajukan Retur / Refund
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- MODAL FORM RETUR UNTUK MASING-MASING ORDER (Hanya ada jika is_protected true) -->
    @if($order->is_protected && in_array($order->status, ['completed', 'shipped']))
        <div class="modal fade" id="returnModal{{ $order->id }}" tabindex="-1" aria-labelledby="returnModalLabel{{ $order->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('order.return', $order->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold text-black" id="returnModalLabel{{ $order->id }}">Form Retur Pesanan #{{ $order->id }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-start">
                            
                            <div class="mb-3">
                                <label class="form-label text-black fw-bold">Alasan Retur / Kendala <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="reason" rows="4" placeholder="Jelaskan alasan pengajuan retur atau refund (misal: barang rusak, tidak sesuai deskripsi, dll)..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-black fw-bold">Upload Foto Bukti Kondisi Barang <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="proof_image" accept="image/png, image/jpeg, image/jpg" required>
                                <div class="form-text">Format: JPG, JPEG, PNG. Ukuran maksimal 2MB.</div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger btn-sm">Kirim Pengajuan Retur</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@empty
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="fa fa-shopping-bag fa-3x text-muted"></i>
        </div>
        <h5 class="text-black fw-bold">Belum ada pesanan</h5>
        <p class="text-muted">Kamu belum memiliki riwayat pesanan pada kategori ini.</p>
        <a href="{{ route('shop') }}" class="btn btn-black btn-sm">Mulai Belanja</a>
    </div>
@endforelse