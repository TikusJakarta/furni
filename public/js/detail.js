document.addEventListener('DOMContentLoaded', function () {
    const modalTriggers = document.querySelectorAll('.product-modal-trigger');
    const productModalEl = document.getElementById('productModal');
    
    if (!productModalEl) return;

    // Inisialisasi modal Bootstrap 5
    const productModal = new bootstrap.Modal(productModalEl);

    // Elemen di dalam modal
    const modalImg = document.getElementById('modal-img');
    const modalName = document.getElementById('modal-name');
    const modalPrice = document.getElementById('modal-price');
    const modalDesc = document.getElementById('modal-desc');
    const modalWeight = document.getElementById('modal-weight');
    const modalStockBadge = document.getElementById('modal-stock-badge');
    const modalForm = document.getElementById('modal-form');
    const qtyInput = document.getElementById('modal-stock-input');
    const submitBtn = document.getElementById('modal-submit-btn');
    
    // Elemen Rating Baru
    const modalRatingStars = document.getElementById('modal-rating-stars');
    const modalRatingText = document.getElementById('modal-rating-text');

    let maxStock = 999;

    // Ketika salah satu produk diklik
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();

            // Ambil data dari atribut data-*
            const name = this.getAttribute('data-name');
            const price = this.getAttribute('data-price');
            const desc = this.getAttribute('data-description');
            const weight = this.getAttribute('data-weight');
            const stock = parseInt(this.getAttribute('data-stock'));
            const image = this.getAttribute('data-image');
            const cartUrl = this.getAttribute('data-cart-url');
            const rating = parseFloat(this.getAttribute('data-rating')) || 0;
            const reviewsCount = this.getAttribute('data-reviews-count') || 0;

            maxStock = stock;

            // Masukkan data dasar ke dalam elemen modal
            modalImg.src = image;
            modalName.textContent = name;
            modalPrice.textContent = price;
            modalDesc.textContent = desc;
            modalWeight.textContent = weight;
            modalForm.action = cartUrl;
            qtyInput.value = 1;
            qtyInput.setAttribute('max', stock);

            // Render Tampilan Rating Bintang di Modal
            if (modalRatingStars && modalRatingText) {
                modalRatingText.textContent = `(${rating.toFixed(1)} dari 5.0 - ${reviewsCount} Ulasan)`;
                let stars = '';
                for (let i = 1; i <= 5; i++) {
                    stars += i <= Math.floor(rating) ? '★' : '☆';
                }
                modalRatingStars.textContent = stars;
            }

            // Atur badge stok & tombol aktif/mati
            if (stock > 0) {
                modalStockBadge.innerHTML = `<span class="badge bg-success text-white">${stock} pcs</span>`;
                qtyInput.removeAttribute('disabled');
                submitBtn.removeAttribute('disabled');
                submitBtn.style.opacity = '1';
            } else {
                modalStockBadge.innerHTML = `<span class="badge bg-danger text-white">Habis</span>`;
                qtyInput.setAttribute('disabled', 'true');
                submitBtn.setAttribute('disabled', 'true');
                submitBtn.style.opacity = '0.5';
            }

            // Tampilkan modal
            productModal.show();
        });
    });

    // Logika Tombol Plus & Minus dalam Modal
    const decreaseBtn = productModalEl.querySelector('.decrease');
    const increaseBtn = productModalEl.querySelector('.increase');

    if (decreaseBtn && increaseBtn && qtyInput) {
        decreaseBtn.addEventListener('click', function (e) {
            e.preventDefault();
            let currentVal = parseInt(qtyInput.value) || 1;
            if (currentVal > 1) {
                qtyInput.value = currentVal - 1;
            }
        });

        increaseBtn.addEventListener('click', function (e) {
            e.preventDefault();
            let currentVal = parseInt(qtyInput.value) || 1;
            if (currentVal < maxStock) {
                qtyInput.value = currentVal + 1;
            }
        });

        qtyInput.addEventListener('input', function () {
            let val = parseInt(this.value);
            if (isNaN(val)) this.value = 1;
            if (val > maxStock) this.value = maxStock;
        });

        qtyInput.addEventListener('blur', function() {
             if (this.value === "" || parseInt(this.value) < 1) this.value = 1;
        });
    }
});