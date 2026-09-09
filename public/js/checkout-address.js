document.addEventListener('DOMContentLoaded', function () {
    const addressDropdown = document.getElementById('saved-address-dropdown');
    if (!addressDropdown) return;

    addressDropdown.addEventListener('change', function () {
        if (!this.value) return;
        let data = JSON.parse(this.value);

        // Autofill form billing details
        const firstNameInput = document.getElementById('c_fname');
        const phoneInput = document.getElementById('c_phone');
        const addressInput = document.getElementById('c_address');
        const stateInput = document.getElementById('c_state_country');
        const postalInput = document.getElementById('c_postal_zip');

        if (firstNameInput) firstNameInput.value = data.recipient_name || '';
        if (phoneInput) phoneInput.value = data.phone || '';
        if (addressInput) addressInput.value = data.address || '';
        if (stateInput) stateInput.value = data.city || '';
        if (postalInput) postalInput.value = data.postal_code || '';

        // Update koordinat peta jika ada
        if (data.latitude && data.longitude) {
            const lat = parseFloat(data.latitude);
            const lng = parseFloat(data.longitude);

            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            if (latInput) latInput.value = lat;
            if (lngInput) lngInput.value = lng;

            if (typeof marker !== 'undefined' && typeof map !== 'undefined') {
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], 15);
            }

            if (typeof fetchShippingRates === 'function') {
                fetchShippingRates(lat, lng, data.city);
            }
        }
    });
});