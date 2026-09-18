document.addEventListener('DOMContentLoaded', function () {
    const addressDropdown = document.getElementById('saved-address-dropdown') || document.getElementById('saved_address_select');
    
    if (addressDropdown) {
        addressDropdown.addEventListener('change', function () {
            if (!this.value) return;
            
            const selectedOption = this.options[this.selectedIndex];

            const data = {
                recipient_name: selectedOption.dataset.name || '',
                phone: selectedOption.dataset.phone || '',
                address: selectedOption.dataset.address || '',
                city: selectedOption.dataset.city || '',
                postal_code: selectedOption.dataset.postal || '',
                latitude: selectedOption.dataset.lat || '',
                longitude: selectedOption.dataset.lng || ''
            };

            const fullName = (data.recipient_name || '').trim();
            const parts = fullName.split(' ');
            const firstName = parts[0] || '';
            const lastName = parts.slice(1).join(' ') || '';

            const firstNameInput = document.getElementById('c_fname');
            const lastNameInput = document.getElementById('c_lname');
            const phoneInput = document.getElementById('c_phone');
            const addressInput = document.getElementById('c_address');
            const stateInput = document.getElementById('c_state_country');
            const postalInput = document.getElementById('c_postal_zip');

            if (firstNameInput) firstNameInput.value = firstName;
            if (lastNameInput) lastNameInput.value = lastName;
            if (phoneInput) phoneInput.value = data.phone || '';
            if (addressInput) addressInput.value = data.address || '';
            if (stateInput) stateInput.value = data.city || '';
            if (postalInput) postalInput.value = data.postal_code || '';

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
    }

    const btnSaveAddress = document.getElementById('btn-save-address');
    if (btnSaveAddress) {
        btnSaveAddress.addEventListener('click', function () {
            if (typeof marker !== 'undefined' && marker.getLatLng) {
                const latLng = marker.getLatLng();
                
                const latInput = document.getElementById('latitude');
                const lngInput = document.getElementById('longitude');
                
                if (latInput) latInput.value = latLng.lat;
                if (lngInput) lngInput.value = latLng.lng;
            }
        });
    }
});