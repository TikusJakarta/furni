@if(session('payment_success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Pembayaran Berhasil!',
                text: "{{ session('payment_success') }}",
                confirmButtonText: 'OK',
                confirmButtonColor: '#3b5d50'
            });
        });
    </script>
@endif