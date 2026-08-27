document.addEventListener('DOMContentLoaded', function () {
    const cartForm = document.getElementById('cart-form');
    if (!cartForm) return;

    function updateCartViaAjax() {
        const formData = new FormData(cartForm);

        fetch(cartForm.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const subtotalElem = document.querySelector('.cart-subtotal');
                const totalElem = document.querySelector('.cart-total');

                // Ubah format ke Rupiah menggunakan toLocaleString('id-ID')
                if (subtotalElem) {
                    subtotalElem.textContent = 'Rp ' + Number(data.subtotal).toLocaleString('id-ID');
                }
                if (totalElem) {
                    totalElem.textContent = 'Rp ' + Number(data.total).toLocaleString('id-ID');
                }
            }
        })
        .catch(error => console.error('Error AJAX:', error));
    }

    function recalculateRow(row) {
        const priceVal = row.querySelector('.product-price-val');
        const qtyInput = row.querySelector('.quantity-amount');
        const rowTotalElem = row.querySelector('.product-row-total');

        if (priceVal && qtyInput && rowTotalElem) {
            let quantity = parseInt(qtyInput.value) || 1;
            if (quantity < 1) {
                quantity = 1;
                qtyInput.value = 1;
            }

            const price = parseFloat(priceVal.getAttribute('data-price')) || 0;
            const rowTotal = price * quantity;
            
            rowTotalElem.textContent = 'Rp ' + Number(rowTotal).toLocaleString('id-ID');
        }
    }

    // 1. Tangani Tombol Kurang (-)
    cartForm.querySelectorAll('.decrease').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault(); 
            const row = this.closest('tr');
            const qtyInput = row.querySelector('.quantity-amount');
            let currentQty = parseInt(qtyInput.value) || 1;

            if (currentQty > 1) {
                qtyInput.value = currentQty - 1;
                recalculateRow(row);
                updateCartViaAjax();
            }
        });
    });

    // 2. Tangani Tombol Tambah (+) 
    cartForm.querySelectorAll('.increase').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault(); 
            const row = this.closest('tr');
            const qtyInput = row.querySelector('.quantity-amount');
            let currentQty = parseInt(qtyInput.value) || 1;

            qtyInput.value = currentQty + 1;
            recalculateRow(row);
            updateCartViaAjax();
        });
    });

    // 3. Kalau user mengetik kuantitas secara manual
    cartForm.querySelectorAll('.quantity-amount').forEach(input => {
        input.addEventListener('input', function () {
            const row = this.closest('tr');
            recalculateRow(row);
            updateCartViaAjax();
        });

        input.addEventListener('change', function () {
            if (parseInt(this.value) < 1 || isNaN(parseInt(this.value))) {
                this.value = 1;
            }
            const row = this.closest('tr');
            recalculateRow(row);
            updateCartViaAjax();
        });
    });

    cartForm.addEventListener('submit', function (e) {
        e.preventDefault();
        updateCartViaAjax();
    });
});