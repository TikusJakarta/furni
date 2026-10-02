let currentShippingCost = 0;

document.addEventListener("DOMContentLoaded", function() {
    const courierSelect = document.getElementById('shipping_courier_select');
    const protectionCheckbox = document.getElementById('use_protection');

    if (courierSelect) {
        courierSelect.addEventListener('change', function() {
            // Ambil harga dari option yang dipilih
            const selectedOption = this.options[this.selectedIndex];
            currentShippingCost = selectedOption.dataset.cost ? parseFloat(selectedOption.dataset.cost) : (parseInt(this.value) || 0);
            calculateTotals();
        });
    }

    if (protectionCheckbox) {
        protectionCheckbox.addEventListener('change', function() {
            calculateTotals();
        });
    }
});

function calculateTotals() {
    const courierSelect = document.getElementById('shipping_courier_select');
    if (courierSelect && courierSelect.value !== "") {
        const selectedOption = courierSelect.options[courierSelect.selectedIndex];
        currentShippingCost = selectedOption.dataset.cost ? parseFloat(selectedOption.dataset.cost) : (parseInt(courierSelect.value) || currentShippingCost);
    }

    // Masukkan ke input tersembunyi untuk backend
    const shippingInput = document.getElementById('shipping_cost_input');
    if (shippingInput) {
        shippingInput.value = currentShippingCost;
    }
    
    // Tampilkan teks ongkir
    const shippingText = document.getElementById('shipping-cost-text');
    if (shippingText) {
        shippingText.innerText = 'Rp ' + currentShippingCost.toLocaleString('id-ID');
    }

    const subtotalElem = document.getElementById('subtotal-text');
    const subtotal = subtotalElem ? parseInt(subtotalElem.getAttribute('data-subtotal')) || 0 : 0;

    let discount = 0;
    const discountElem = document.getElementById('coupon-discount-text');
    if (discountElem) {
        discount = parseInt(discountElem.innerText.replace(/[^0-9]/g, '')) || 0;
    }

    // Hitung proteksi (0.5% dari subtotal, minimal Rp 2.000)
    let protectionFee = 0;
    if (subtotal > 0) {
        protectionFee = Math.max(2000, Math.round(subtotal * 0.005));
    }

    const protectionLabel = document.getElementById('protection-fee-label');
    if (protectionLabel) {
        protectionLabel.innerText = 'Rp ' + protectionFee.toLocaleString('id-ID');
    }

    const isProtected = document.getElementById('use_protection')?.checked || false;
    let finalProtection = isProtected ? protectionFee : 0;

    // Grand Total
    let grandTotal = (subtotal - discount) + currentShippingCost + finalProtection;

    const totalElem = document.getElementById('order-total-text');
    if (totalElem) {
        totalElem.innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }
}