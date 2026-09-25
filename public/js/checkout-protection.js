document.addEventListener("DOMContentLoaded", function() {
    const cityInput = document.getElementById('c_state_country');
    const postalInput = document.getElementById('c_postal_zip');
    const cityDatalist = document.getElementById('city-options');
    const postalDatalist = document.getElementById('postal-options');

    // 1. Ambil data kota & kode pos secara dinamis dari Database API
    if (cityDatalist && postalDatalist) {
        fetch('/api/get-cities')
            .then(response => response.json())
            .then(data => {
                cityDatalist.innerHTML = '';
                postalDatalist.innerHTML = '';
                
                data.forEach(item => {
                    let cityOpt = document.createElement('option');
                    cityOpt.value = `${item.city} (${item.postal_code})`; 
                    cityOpt.dataset.cityName = item.city;
                    cityOpt.dataset.postal = item.postal_code;
                    cityDatalist.appendChild(cityOpt);

                    let postalOpt = document.createElement('option');
                    postalOpt.value = item.postal_code;
                    postalOpt.dataset.city = item.city;
                    postalDatalist.appendChild(postalOpt);
                });
            })
            .catch(error => console.error('Gagal memuat data kota dari database:', error));
    }

    // 2. Sinkronisasi otomatis Kota <-> Kode Pos
    if (cityInput && postalInput && cityDatalist) {
        cityInput.addEventListener('input', function() {
            const val = this.value.trim().toLowerCase();
            for (let option of cityDatalist.options) {
                if (option.value.trim().toLowerCase() === val || 
                    (option.dataset.cityName && option.dataset.cityName.toLowerCase() === val)) {
                    postalInput.value = option.dataset.postal;
                    break;
                }
            }
        });

        postalInput.addEventListener('input', function() {
            const val = this.value.trim();
            for (let option of postalDatalist.options) {
                if (option.value.trim() === val && option.dataset.city) {
                    cityInput.value = option.dataset.city;
                    break;
                }
            }
        });
    }

    // 3. Event listener untuk perubahan kurir & proteksi
    const courierSelect = document.getElementById('shipping_courier_select');
    const protectionCheckbox = document.getElementById('use_protection');

    if (courierSelect) {
        courierSelect.addEventListener('change', function() {
            calculateAllTotals();
        });
    }

    if (protectionCheckbox) {
        protectionCheckbox.addEventListener('change', function() {
            calculateAllTotals();
        });
    }

    setTimeout(calculateAllTotals, 300);
});

// 4. Fungsi Utama Kalkulasi Ongkir & Total Belanja
function calculateAllTotals() {
    const courierSelect = document.getElementById('shipping_courier_select');
    let baseShipping = 0;
    let courierText = '';
    
    // Ambil murni dari value dropdown kurir (bukan dari teks HTML yang sudah di-format)
    if (courierSelect && courierSelect.options[courierSelect.selectedIndex]) {
        const selectedOption = courierSelect.options[courierSelect.selectedIndex];
        courierText = selectedOption.text.toUpperCase();
        baseShipping = parseInt(selectedOption.value) || 0;
    }

    const weightElem = document.getElementById('total-weight-amount');
    let totalWeight = 1;
    if (weightElem) {
        let rawWeight = parseFloat(weightElem.getAttribute('data-weight')) || 1;
        totalWeight = rawWeight >= 1000 ? rawWeight / 1000 : rawWeight;
    }

    const isInstant = courierText.includes('GOJEK') || 
                      courierText.includes('GRAB') || 
                      courierText.includes('INSTANT') || 
                      courierText.includes('SAMEDAY');

    let finalShippingCost = baseShipping;

    // Perkalian berat hanya dilakukan jika bukan kurir instant dan ada harga dasarnya
    if (!isInstant && baseShipping > 0) {
        finalShippingCost = Math.round(baseShipping * totalWeight);
    }

    const shippingInput = document.getElementById('shipping_cost_input');
    if (shippingInput) {
        shippingInput.value = finalShippingCost;
    }
    const shippingCostText = document.getElementById('shipping-cost-text');
    if (shippingCostText) {
        shippingCostText.innerText = 'Rp ' + finalShippingCost.toLocaleString('id-ID');
    }

    const subtotalElem = document.getElementById('subtotal-text');
    const subtotal = subtotalElem ? parseInt(subtotalElem.getAttribute('data-subtotal')) || 0 : 0;

    let discount = 0;
    const discountElem = document.getElementById('coupon-discount-text');
    if (discountElem) {
        discount = parseInt(discountElem.innerText.replace(/[^0-9]/g, '')) || 0;
    }

    // Hitung nominal proteksi (0.5% dari subtotal, minimal Rp 2.000)
    let protectionFeeValue = 0;
    if (subtotal > 0) {
        let calculatedFee = Math.round(subtotal * 0.005);
        protectionFeeValue = Math.max(2000, calculatedFee);
    }

    // Teks nominal proteksi TETAP MUNCUL di layar meskipun checkbox belum dicentang
    const protectionLabel = document.getElementById('protection-fee-label');
    if (protectionLabel) {
        protectionLabel.innerText = 'Rp ' + protectionFeeValue.toLocaleString('id-ID');
    }

    // Cek apakah dicentang untuk menentukan apakah nominalnya masuk ke Grand Total
    const isProtected = document.getElementById('use_protection') ? document.getElementById('use_protection').checked : false;
    let protectionFeeForTotal = isProtected ? protectionFeeValue : 0;

    let grandTotal = (subtotal - discount) + finalShippingCost + protectionFeeForTotal;

    const orderTotalElem = document.getElementById('order-total-text');
    if (orderTotalElem) {
        orderTotalElem.innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }
}