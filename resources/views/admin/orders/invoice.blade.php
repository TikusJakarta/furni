<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoiceNumber }} - {{ $company }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/invoice.css') }}?v={{ filemtime(public_path('css/invoice.css')) }}">
</head>

<body>

    <div class="invoice-toolbar no-print">
        <a href="{{ route('admin.orders.index') }}" class="tb-link">&larr; Kembali ke Pesanan</a>
        <p class="tb-hint">Klik tombol di kanan, lalu pilih tujuan <strong>"Simpan sebagai PDF"</strong> di jendela cetak.</p>
        <button type="button" class="tb-btn" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <main class="invoice">

        <!-- KEPALA -->
        <header class="inv-head">
            <div class="inv-company">
                <div class="inv-brand">{{ $company }}</div>
                @if($companyAddress)
                    <p>{!! nl2br(e($companyAddress)) !!}</p>
                @endif
                @if($companyPhone || $companyEmail)
                    <p>{{ implode(' · ', array_filter([$companyPhone, $companyEmail])) }}</p>
                @endif
            </div>

            <div class="inv-title">
                <h1>INVOICE</h1>
                <span class="inv-stamp {{ $stamp['class'] }}">{{ $stamp['label'] }}</span>
            </div>
        </header>

        <section class="inv-meta">
            <div class="inv-box">
                <h2>Ditagihkan kepada</h2>
                <strong>{{ $customerName }}</strong>
                @if($order->email)
                    <p>{{ $order->email }}</p>
                @endif
                @if($customerPhone)
                    <p>{{ $customerPhone }}</p>
                @endif
                @if($customerAddress)
                    <p>{{ $customerAddress }}</p>
                @endif
            </div>

            <div class="inv-box">
                <h2>Detail invoice</h2>
                <dl>
                    <div><dt>No. Invoice</dt><dd>{{ $invoiceNumber }}</dd></div>
                    <div><dt>No. Pesanan</dt><dd>#{{ $order->id }}</dd></div>
                    <div><dt>Tanggal</dt><dd>{{ $order->created_at->format('d M Y, H:i') }}</dd></div>
                    <div><dt>Pembayaran</dt><dd>{{ $paymentMethod }}</dd></div>
                    @if($order->shipping_courier)
                        <div><dt>Kurir</dt><dd>{{ strtoupper($order->shipping_courier) }}</dd></div>
                    @endif
                </dl>
            </div>
        </section>

        <table class="inv-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Produk</th>
                    <th class="col-num">Harga</th>
                    <th class="col-qty">Qty</th>
                    <th class="col-num">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td class="col-num">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                        <td class="col-qty">{{ $item['qty'] }}</td>
                        <td class="col-num">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="inv-empty">Rincian produk tidak tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <section class="inv-bottom">
            <div class="inv-notes">
                @if(! $isPaid && $paymentInfo && $order->status !== 'cancelled')
                    <h2>Informasi pembayaran</h2>
                    <p>{!! nl2br(e($paymentInfo)) !!}</p>
                @endif
                @if($note)
                    <h2>Catatan</h2>
                    <p>{!! nl2br(e($note)) !!}</p>
                @endif
            </div>

            <table class="inv-totals">
                <tr>
                    <td>Subtotal Produk</td>
                    <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
                
                <tr>
                    <td>PPN (12%)</td>
                    <td>Rp {{ number_format($tax ?? 0, 0, ',', '.') }}</td>
                </tr>

                @if(isset($protectionFee) && $protectionFee > 0)
                    <tr>
                        <td>Biaya Proteksi</td>
                        <td>Rp {{ number_format($protectionFee, 0, ',', '.') }}</td>
                    </tr>
                @endif

                <tr>
                    <td>Ongkos kirim</td>
                    <td>Rp {{ number_format($shipping, 0, ',', '.') }}</td>
                </tr>

                @if(isset($discount) && $discount > 0)
                    <tr class="is-discount">
                        <td>Diskon / Kupon</td>
                        <td>&minus; Rp {{ number_format($discount, 0, ',', '.') }}</td>
                    </tr>
                @endif

                <tr class="is-total">
                    <td>Total</td>
                    <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </section>

        <footer class="inv-foot">
            <p>Dicetak oleh {{ auth()->user()->name }} pada {{ now()->format('d M Y, H:i') }}</p>
        </footer>
    </main>

</body>

</html>