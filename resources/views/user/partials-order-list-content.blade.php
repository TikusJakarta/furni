@forelse($orders as $order)
    @php
        $statusClass = 'status-' . strtolower($order->status);
    @endphp

    <div class="card modern-order-card mb-4 shadow-sm {{ $statusClass }}">
        <!-- Card Header: ID & Tanggal Kiri, Status Kanan -->
        <div class="card-header bg-white py-3 px-4 border-bottom-0 d-flex flex-wrap justify-content-between align-items-center gap-3 bg-transparent">
            <div class="d-flex align-items-center gap-4">
                <span class="badge bg-light text-dark border px-3 py-1.5 font-monospace fw-bold shadow-2xs" style="font-size: 12px; letter-spacing: 0.5px;">
                    #{{ $order->id }}
                </span>
                <span class="text-muted small d-flex align-items-center gap-1.5">
                    <i class="fa fa-calendar-alt text-secondary opacity-50" style="font-size: 11px;"></i> 
                    <span>{{ $order->created_at->format('d M Y, H:i') }}</span>
                </span>
            </div>
            
            <div>
                @php
                    $statusBadgeClasses = [
                        'pending'    => 'status-badge-pending',
                        'verifikasi' => 'status-badge-verifikasi',
                        'paid'       => 'status-badge-paid',
                        'packing'    => 'status-badge-packing',
                        'shipping'   => 'status-badge-shipping',
                        'shipped'    => 'status-badge-shipped',
                        'completed'  => 'status-badge-completed',
                        'cancelled'  => 'status-badge-cancelled'
                    ];
                    $badgeClass = $statusBadgeClasses[$order->status] ?? 'status-badge-pending';
                @endphp
                <span class="custom-status-badge {{ $badgeClass }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>

        <!-- Card Body: Daftar Produk & Harga Total -->
        <div class="card-body px-4 py-2">
            <div class="order-items-wrapper">
                @foreach($order->orderItems as $item)
                    <div class="order-item-row py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        
                        <!-- Info Produk & Thumbnail -->
                        <div class="d-flex align-items-center gap-3">
                            @php
                                $rawImage = $item->product->image ?? ($item->product->gambar ?? ($item->product->photo ?? ($item->product->thumbnail ?? null)));

                                if ($rawImage) {
                                    if (filter_var($rawImage, FILTER_VALIDATE_URL)) {
                                        $productImage = $rawImage;
                                    } elseif (str_starts_with($rawImage, 'storage/') || str_starts_with($rawImage, 'images/')) {
                                        $productImage = asset($rawImage);
                                    } else {
                                        $productImage = asset('storage/' . $rawImage);
                                    }
                                } else {
                                    $productImage = asset('images/product-1.png');
                                }
                            @endphp

                            <img src="{{ $productImage }}" 
                                 alt="{{ $item->product->name ?? 'Produk' }}" 
                                 class="order-thumb flex-shrink-0"
                                 onerror="this.src='{{ asset('images/product-1.png') }}'">

                            <div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 14.5px;">
                                    {{ $item->product->name ?? 'Produk Dihapus' }}
                                    @if($item->productVariant)
                                        <span class="badge bg-light text-muted fw-normal ms-1 border" style="font-size: 10px;">
                                            {{ $item->productVariant->name }}
                                        </span>
                                    @endif
                                </h6>
                                <span class="text-muted small">
                                    {{ $item->quantity }}x <span class="text-dark fw-semibold">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Tombol Beli Ulang per Item -->
                        <div>
                            @if($item->product)
                                <a href="{{ route('cart.add', $item->product_id) }}{{ $item->product_variant_id ? '?product_variant_id=' . $item->product_variant_id : '' }}"
                                    class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1 fw-medium" style="font-size: 12px;">
                                    <i class="fa fa-redo me-1" style="font-size: 10px;"></i> Beli Ulang
                                </a>
                            @else
                                <span class="badge bg-light text-muted border" style="font-size: 11px;">Tidak Tersedia</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Bagian Proteksi & Total Harga -->
            <div class="row align-items-center py-3 mt-2 bg-light rounded-3 px-3 g-2">
                <div class="col-md-7">
                    @if($order->is_protected)
                        <div class="text-success small fw-semibold d-flex align-items-center gap-2">
                            <i class="fa fa-shield-alt text-success"></i>
                            <div>
                                <span class="d-block" style="font-size: 13px;">Dilindungi Garansi Retur</span>
                                <span class="text-muted fw-normal" style="font-size: 11px;">Biaya proteksi: Rp {{ number_format($order->protection_fee, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-muted small d-flex align-items-center gap-2">
                            <i class="fa fa-info-circle"></i>
                            <span>Tanpa Proteksi Tambahan</span>
                        </div>
                    @endif
                </div>

                <div class="col-md-5 text-md-end">
                    <span class="text-muted small d-block" style="font-size: 11px;">Total Pembayaran</span>
                    <h5 class="text-dark fw-bold mb-0" style="font-size: 16px;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <!-- Card Footer: Aksi Tombol -->
        <div class="card-body bg-white py-3 px-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                @if($order->status == 'pending')
                    <a href="{{ route('order.pay', $order->id) }}" class="btn btn-dark btn-sm rounded-pill px-4 py-2 fw-semibold shadow-sm" style="font-size: 13px;">
                        <i class="fa fa-credit-card me-1"></i> Bayar Sekarang
                    </a>
                @endif
            </div>

            <div>
                @if($order->is_protected && in_array($order->status, ['completed', 'shipped']))
                    @php
                        $existingReturn = \App\Models\OrderReturn::where('order_id', $order->id)->first();
                    @endphp

                    @if(!$existingReturn)
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-2 fw-medium" data-bs-toggle="modal"
                            data-bs-target="#returnModal{{ $order->id }}" style="font-size: 13px;">
                            <i class="fa fa-undo me-1"></i> Ajukan Retur / Refund
                        </button>
                    @else
                        <span class="badge bg-secondary text-white px-3 py-2 rounded-pill" style="font-size: 12px;">
                            Retur {{ ucfirst($existingReturn->status) }}
                        </span>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Retur -->
    @if($order->is_protected && in_array($order->status, ['completed', 'shipped']))
        @php
            $existingReturn = $existingReturn ?? \App\Models\OrderReturn::where('order_id', $order->id)->first();
        @endphp

        @if(!$existingReturn)
            <div class="modal fade" id="returnModal{{ $order->id }}" tabindex="-1"
                aria-labelledby="returnModalLabel{{ $order->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <form action="{{ route('order.return', $order->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header border-0 pb-0 pt-4 px-4">
                                <h5 class="modal-title fw-bold text-dark">
                                    Form Retur Pesanan #{{ $order->id }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-start pt-3 px-4">
                                <div class="mb-3">
                                    <label class="form-label text-dark fw-semibold small">Alasan Retur / Kendala <span class="text-danger">*</span></label>
                                    <textarea class="form-control rounded-3 bg-light border-0 p-3" name="reason" rows="4"
                                        placeholder="Jelaskan kendala atau alasan pengajuan retur secara detail..."
                                        required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-dark fw-semibold small">Upload Foto Bukti Kondisi Barang <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control rounded-3 bg-light border-0 p-2" name="proof_image"
                                        accept="image/png, image/jpeg, image/jpg" required>
                                    <div class="form-text text-muted" style="font-size: 11px;">Format: JPG, JPEG, PNG. Maksimal ukuran 2MB.</div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                                <button type="button" class="btn btn-light btn-sm rounded-pill px-4 py-2" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 py-2 fw-semibold">Kirim Pengajuan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endif

@empty
    <div class="text-center py-5 bg-white rounded-4 shadow-sm border p-5">
        <div class="mb-3 text-muted opacity-50">
            <i class="fa fa-shopping-bag fa-3x"></i>
        </div>
        <h5 class="text-dark fw-bold mb-1">Belum ada pesanan</h5>
        <p class="text-muted small mb-4">Kamu belum memiliki riwayat pesanan pada kategori ini.</p>
        <a href="{{ route('shop') }}" class="btn btn-dark btn-sm rounded-pill px-4 py-2">Mulai Belanja Sekarang</a>
    </div>
@endforelse