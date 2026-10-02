document.addEventListener('change', function (e) {
    if (e.target && e.target.id === 'shipping_courier_select') {
        const selectedOption = e.target.options[e.target.selectedIndex];
        if (!selectedOption || !selectedOption.value) return;

        const baseCost = parseFloat(selectedOption.dataset.cost) || 0;
        let insuranceCost = parseFloat(selectedOption.dataset.insurance) || 0; 
        
        if (insuranceCost <= 0) {
            const subtotalEl = document.getElementById('subtotal-text');
            const subtotal = subtotalEl ? parseFloat(subtotalEl.dataset.subtotal) || 0 : 0;
            insuranceCost = subtotal * 0.005; 
        }

        const feeLabelEl = document.getElementById('protection-fee-label');
        if (feeLabelEl) {
            feeLabelEl.innerText = `Rp ${numberFormat(insuranceCost)}`;
        }

        selectedOption.dataset.insurance = insuranceCost;

        const protectionCheckbox = document.getElementById('use_protection');
        const useProtection = protectionCheckbox ? protectionCheckbox.checked : false;

        const finalShippingCost = baseCost + (useProtection ? insuranceCost : 0);

        const shippingCostInput = document.getElementById('shipping_cost_input');
        if (shippingCostInput) {
            shippingCostInput.value = finalShippingCost;
        }

        const shippingCostText = document.getElementById('shipping-cost-text');
        if (shippingCostText) {
            shippingCostText.innerText = `Rp ${numberFormat(finalShippingCost)}`;
        }

        updateGrandTotal(finalShippingCost, useProtection ? insuranceCost : 0);
    }

    if (e.target && e.target.id === 'use_protection') {
        const courierSelect = document.getElementById('shipping_courier_select');
        if (courierSelect && courierSelect.selectedIndex > 0) {
            courierSelect.dispatchEvent(new Event('change'));
        }
    }
});

function updateGrandTotal(shippingCost, protectionFee) {
    const subtotalEl = document.getElementById('subtotal-text');
    const subtotal = subtotalEl ? parseFloat(subtotalEl.dataset.subtotal) || 0 : 0;

    const couponDiscountEl = document.getElementById('coupon-discount-text');
    let discount = 0;
    if (couponDiscountEl) {
        const rawDiscount = couponDiscountEl.innerText.replace(/[^0-9]/g, '');
        discount = parseFloat(rawDiscount) || 0;
    }

    const protectionCheckbox = document.getElementById('use_protection');
    const useProtection = protectionCheckbox ? protectionCheckbox.checked : false;
    const actualProtectionFee = useProtection ? protectionFee : 0;

    const grandTotal = Math.max(0, subtotal - discount) + shippingCost;

    const orderTotalText = document.getElementById('order-total-text');
    if (orderTotalText) {
        orderTotalText.innerText = `Rp ${numberFormat(grandTotal)}`;
    }
}

function numberFormat(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}