document.addEventListener("DOMContentLoaded", function() {
    const cityInput = document.getElementById('c_state_country');
    const postalInput = document.getElementById('c_postal_zip');
    const cityDatalist = document.getElementById('city-options');
    const postalDatalist = document.getElementById('postal-options');

    // 1. Ambil data kota & kode pos secara dinamis dari file JSON public/data/cities.json
    if (cityDatalist && postalDatalist) {
        fetch('/data/cities.json')
            .then(response => response.json())
            .then(data => {
                data.forEach(item => {
                    let cityOpt = document.createElement('option');
                    cityOpt.value = item.city;
                    cityOpt.dataset.postal = item.postal;
                    cityDatalist.appendChild(cityOpt);

                    let postalOpt = document.createElement('option');
                    postalOpt.value = item.postal;
                    postalOpt.dataset.city = item.city;
                    postalDatalist.appendChild(postalOpt);
                });
            })
            .catch(error => console.error('Gagal memuat data kota:', error));
    }

    // 2. Sinkronisasi otomatis Kota <-> Kode Pos
    if (cityInput && postalInput && cityDatalist) {
        cityInput.addEventListener('input', function() {
            const val = this.value;
            for (let option of cityDatalist.options) {
                if (option.value.toLowerCase() === val.toLowerCase() && option.dataset.postal) {
                    postalInput.value = option.dataset.postal;
                    break;
                }
            }
        });

        postalInput.addEventListener('input', function() {
            const val = this.value;
            for (let option of postalDatalist.options) {
                if (option.value === val && option.dataset.city) {
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
            setTimeout(calculateAllTotals, 150);
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
    
    if (courierSelect && courierSelect.options[courierSelect.selectedIndex]) {
        const selectedOption = courierSelect.options[courierSelect.selectedIndex];
        courierText = selectedOption.text.toUpperCase();
        baseShipping = parseInt(selectedOption.value) || 0;
    }
    
    if (baseShipping === 0) {
        const shippingText = document.getElementById('shipping-cost-text');
        if (shippingText) {
            baseShipping = parseInt(shippingText.innerText.replace(/[^0-9]/g, '')) || 0;
        }
    }

    const weightElem = document.getElementById('total-weight-amount');
    let totalWeight = 1;
    if (weightElem) {
        let rawWeight = parseFloat(weightElem.getAttribute('data-weight')) || 1;
        totalWeight = rawWeight >= 1000 ? rawWeight / 1000 : rawWeight;
    }

    // Cek apakah kurir instant
    const isInstant = courierText.includes('GOJEK') || 
                      courierText.includes('GRAB') || 
                      courierText.includes('INSTANT') || 
                      courierText.includes('SAMEDAY');

    let finalShippingCost = baseShipping;

    // Jika BUKAN kurir instant = baru dikali total berat (kg)
    if (!isInstant) {
        finalShippingCost = Math.round(baseShipping * totalWeight);
    }

    // Update input hidden & teks ongkir
    const shippingInput = document.getElementById('shipping_cost_input');
    if (shippingInput) {
        shippingInput.value = finalShippingCost;
    }
    const shippingCostText = document.getElementById('shipping-cost-text');
    if (shippingCostText) {
        shippingCostText.innerText = 'Rp ' + finalShippingCost.toLocaleString('id-ID');
    }

    // Ambil Subtotal
    const subtotalElem = document.getElementById('subtotal-text');
    const subtotal = subtotalElem ? parseInt(subtotalElem.getAttribute('data-subtotal')) || 0 : 0;

    // Ambil Diskon Kupon
    let discount = 0;
    const discountElem = document.getElementById('coupon-discount-text');
    if (discountElem) {
        discount = parseInt(discountElem.innerText.replace(/[^0-9]/g, '')) || 0;
    }

    // Cek Proteksi (+Rp 5.000)
    const isProtected = document.getElementById('use_protection') ? document.getElementById('use_protection').checked : false;
    const protectionFee = isProtected ? 5000 : 0;

    // Hitung Grand Total
    let grandTotal = (subtotal - discount) + finalShippingCost + protectionFee;

    // Update Order Total
    const orderTotalElem = document.getElementById('order-total-text');
    if (orderTotalElem) {
        orderTotalElem.innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }
}