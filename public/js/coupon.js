document.addEventListener('DOMContentLoaded', function () {
    const applyBtn = document.getElementById('apply-coupon-btn');
    const removeBtn = document.getElementById('remove-coupon-btn');
    const couponInput = document.getElementById('coupon_code_input');

    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            let code = couponInput.value.trim();
            if (!code) {
                alert('Silakan masukkan kode kupon terlebih dahulu.');
                return;
            }

            // Kirim request AJAX untuk apply kupon
            fetch('/checkout/apply-coupon', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ coupon_code: code })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Refresh halaman untuk memperbarui total harga & diskon
                } else {
                    alert(data.message || 'Gagal menerapkan kupon.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Fallback jika backend mengembalikan redirect response biasa
                submitCouponFormFallback(code);
            });
        });
    }

    if (removeBtn) {
        removeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            fetch('/checkout/remove-coupon', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(() => {
                location.reload();
            })
            .catch(() => {
                window.location.href = '/checkout/remove-coupon';
            });
        });
    }

    // Fallback form biasa jika tidak menggunakan endpoint json murni
    function submitCouponFormFallback(code) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '/checkout/apply-coupon';
        
        let csrfToken = document.querySelector('input[name="_token"]').value;
        
        let csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;

        let codeInput = document.createElement('input');
        codeInput.type = 'hidden';
        codeInput.name = 'coupon_code';
        codeInput.value = code;

        form.appendChild(csrfInput);
        form.appendChild(codeInput);
        document.body.appendChild(form);
        form.submit();
    }
});