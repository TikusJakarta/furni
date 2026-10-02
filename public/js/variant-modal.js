// public/js/variant-modal.js
document.addEventListener('DOMContentLoaded', function () {
    const closeBtn = document.getElementById('closeModalBtn');
    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            $('#productModal').modal('hide');
        });
    }

    // Script interaktif Modal & Varian Produk
    document.querySelectorAll('.product-modal-trigger').forEach(trigger => {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();

            const name = this.getAttribute('data-name');
            const price = this.getAttribute('data-price');
            const desc = this.getAttribute('data-description');
            const weight = this.getAttribute('data-weight');
            const stock = this.getAttribute('data-stock');
            const image = this.getAttribute('data-image');
            const cartUrl = this.getAttribute('data-cart-url');
            const variants = JSON.parse(this.getAttribute('data-variants') || '[]');

            // Set basic modal values
            document.getElementById('modal-name').textContent = name;
            document.getElementById('modal-price').textContent = price;
            document.getElementById('modal-desc').textContent = desc;
            document.getElementById('modal-weight').textContent = weight;
            document.getElementById('modal-stock-badge').textContent = stock;
            document.getElementById('modal-img').src = image;
            document.getElementById('modal-form').action = cartUrl;

            // Handle variants container & dropdown
            const variantContainer = document.getElementById('modal-variants-container');
            const variantSelect = document.getElementById('modal-variant-select');
            variantSelect.innerHTML = '<option value="">-- Pilih Varian --</option>';

            if (variants.length > 0) {
                variantContainer.classList.remove('d-none');
                variants.forEach(v => {
                    let opt = document.createElement('option');
                    opt.value = v.id;
                    opt.setAttribute('data-price', v.price ? 'Rp ' + Number(v.price).toLocaleString('id-ID') : price);
                    opt.setAttribute('data-stock', v.stock);
                    opt.textContent = `${v.name} ${v.price ? '(Rp ' + Number(v.price).toLocaleString('id-ID') + ')' : ''} - Stok: ${v.stock}`;
                    variantSelect.appendChild(opt);
                });

                // Event ketika user mengganti pilihan varian
                variantSelect.onchange = function () {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.value) {
                        document.getElementById('modal-price').textContent = selectedOption.getAttribute('data-price');
                        document.getElementById('modal-stock-badge').textContent = selectedOption.getAttribute('data-stock');
                    } else {
                        document.getElementById('modal-price').textContent = price;
                        document.getElementById('modal-stock-badge').textContent = stock;
                    }
                };
            } else {
                variantContainer.classList.add('d-none');
            }

            $('#productModal').modal('show');
        });
    });
});