@if(session('order_success_popup'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let paymentMethod = "{{ session('payment_method') }}";
            let orderId = "{{ session('order_id') }}";
            let totalPrice = "Rp {{ number_format(session('total_price'), 0, ',', '.') }}";

            let htmlContent = `Pesanan <b>#` + orderId + `</b> berhasil dibuat!<br>Total Tagihan: <b>` + totalPrice + `</b><br><br>`;
            
            if (paymentMethod === 'qr') {
                htmlContent += `Silakan selesaikan pembayaran melalui QR Code.<br><br>` +
                               `<a href="/order/` + orderId + `/pay" class="btn btn-primary text-white px-4 py-2" style="background-color: #3b5d50; border: none;">Scan & Bayar QR</a>`;
            } else {
                htmlContent += `Silakan transfer ke Rekening BCA: <b>1234567890</b> (a.n Furni Store).<br><br>` +
                               `<a href="/order/` + orderId + `/pay" class="btn btn-success text-white px-4 py-2">Konfirmasi Transfer Bank</a>`;
            }

            Swal.fire({
                icon: 'success',
                title: 'Pesanan Berhasil Dibuat!',
                html: htmlContent,
                showConfirmButton: true,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#6c757d'
            });
        });
    </script>
@endif