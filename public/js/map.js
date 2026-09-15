let map, marker;

document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('map');
    if (!mapElement) return;

    // Koordinat default (Jakarta)
    const defaultLat = -6.175392;
    const defaultLng = 106.827153;

    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    // Selector elemen form
    const cityInput = document.querySelector('input[name="state_country"]') || document.getElementById('c_state_country') || document.getElementById('city');
    const addressInput = document.querySelector('input[name="address"]') || document.querySelector('textarea[name="address"]') || document.getElementById('c_address');
    const postalInput = document.querySelector('input[name="postal_zip"]') || document.getElementById('c_postal_zip');
    const nameInput = document.querySelector('input[name="first_name"]') || document.getElementById('c_fname');
    const phoneInput = document.querySelector('input[name="phone"]') || document.getElementById('c_phone');

    let initialLat = latInput && latInput.value ? parseFloat(latInput.value) : defaultLat;
    let initialLng = lngInput && lngInput.value ? parseFloat(lngInput.value) : defaultLng;

    // 1. Inisialisasi Peta Leaflet
    map = L.map('map').setView([initialLat, initialLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

    function updatePosition(lat, lng) {
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], 15);

        if (latInput) latInput.value = lat;
        if (lngInput) lngInput.value = lng;

        const cityName = cityInput ? cityInput.value : '';
        fetchShippingRates(lat, lng, cityName);
    }

    // 2. Integrasi Kotak Search / Geosearch (OpenStreetMap Provider)
    if (window.GeoSearch) {
        const provider = new window.GeoSearch.OpenStreetMapProvider();
        const searchControl = new window.GeoSearch.GeoSearchControl({
            provider: provider,
            style: 'bar',
            autoComplete: true,
            autoCompleteDelay: 250,
            showMarker: false,
            showPopup: false,
            retainZoomLevel: false,
            searchLabel: 'Ketik nama jalan, gedung, atau kota...',
        });

        map.addControl(searchControl);

        map.on('geosearch/showlocation', function (result) {
            const lat = result.location.y;
            const lng = result.location.x;
            const label = result.location.label;

            if (addressInput && !addressInput.value) {
                addressInput.value = label;
            }

            updatePosition(lat, lng);
        });
    }

    marker.on('dragend', function () {
        const pos = marker.getLatLng();
        updatePosition(pos.lat, pos.lng);
    });

    map.on('click', function (e) {
        updatePosition(e.latlng.lat, e.latlng.lng);
    });

    if (cityInput) {
        cityInput.addEventListener('input', function () {
            const lat = latInput && latInput.value ? parseFloat(latInput.value) : initialLat;
            const lng = lngInput && lngInput.value ? parseFloat(lngInput.value) : initialLng;
            fetchShippingRates(lat, lng, cityInput.value);
        });
    }

    const savedAddressSelect = document.getElementById('saved_address_select');
    if (savedAddressSelect) {
        savedAddressSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (!selectedOption.value) return;

            const addr = selectedOption.dataset.address;
            const city = selectedOption.dataset.city;
            const postal = selectedOption.dataset.postal;
            const lat = parseFloat(selectedOption.dataset.lat) || defaultLat;
            const lng = parseFloat(selectedOption.dataset.lng) || defaultLng;
            const name = selectedOption.dataset.name;
            const phone = selectedOption.dataset.phone;

            if (addressInput) addressInput.value = addr;
            if (cityInput) cityInput.value = city;
            if (postalInput) postalInput.value = postal;
            if (nameInput) nameInput.value = name;
            if (phoneInput) phoneInput.value = phone;

            updatePosition(lat, lng);
        });
    }

    calculateCartWeight();
    fetchShippingRates(initialLat, initialLng, cityInput ? cityInput.value : '');
});

// Fungsi untuk mengambil data ongkir via AJAX ke Backend (Include Total Weight Murni Database)
function fetchShippingRates(lat, lng, cityName = '') {
    const courierSelect = document.getElementById('shipping_courier_select');
    if (!courierSelect) return;

    const totalWeight = calculateCartWeight();

    courierSelect.innerHTML = '<option value="">Mencari pilihan kurir...</option>';

    fetch('/checkout/check-rates', { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ 
            latitude: lat, 
            longitude: lng, 
            city: cityName,
            weight: totalWeight 
        })
    })
    .then(response => response.json())
    .then(data => {
        courierSelect.innerHTML = '<option value="">-- Pilih Layanan Kurir --</option>';
        
        if (data.success && data.pricing && data.pricing.length > 0) {
            let instantGroup = document.createElement('optgroup');
            instantGroup.label = '🚀 Layanan Instant / Same Day';

            let regulerGroup = document.createElement('optgroup');
            regulerGroup.label = '📦 Layanan Reguler & Ekonomi';

            let hasInstant = false;
            let hasReguler = false;

            data.pricing.forEach(rate => {
                let option = document.createElement('option');
                option.value = `${rate.courier_name} - ${rate.courier_service_name}`;
                option.dataset.cost = rate.price;
                option.text = `${rate.courier_name} - ${rate.courier_service_name} - Rp ${numberFormat(rate.price)} (${rate.shipment_duration})`;

                if (rate.type === 'instant') {
                    instantGroup.appendChild(option);
                    hasInstant = true;
                } else {
                    regulerGroup.appendChild(option);
                    hasReguler = true;
                }
            });

            if (hasInstant) courierSelect.appendChild(instantGroup);
            if (hasReguler) courierSelect.appendChild(regulerGroup);

        } else {
            courierSelect.innerHTML = '<option value="">Tidak ada kurir tersedia untuk lokasi ini</option>';
        }
    })
    .catch(error => {
        console.error('Error fetching shipping rates:', error);
        courierSelect.innerHTML = '<option value="">Gagal memuat kurir</option>';
    });
}

// Fungsi Helper untuk Menghitung & Memperbarui Tampilan Total Berat Murni dari Database
function calculateCartWeight() {
    let totalWeight = 0;
    const cartItems = document.querySelectorAll('.cart-item-row'); 

    if (cartItems.length > 0) {
        cartItems.forEach(row => {
            const weight = parseFloat(row.dataset.weight) || 0; // Berat satuan dari database
            const qty = parseFloat(row.dataset.qty) || 1;       // Quantity produk
            totalWeight += (weight * qty);
        });
    } else {
        totalWeight = 0; 
    }

    const weightDisplayEl = document.getElementById('total-weight-amount');
    if (weightDisplayEl) {
        weightDisplayEl.dataset.weight = totalWeight;
        if (totalWeight >= 1000) {
            weightDisplayEl.innerText = (totalWeight / 1000) + ' kg';
        } else {
            weightDisplayEl.innerText = totalWeight + ' gram';
        }
    }

    return totalWeight;
}

// Event untuk tombol Simpan Alamat
document.addEventListener('click', function (e) {
    if (e.target && e.target.id === 'btn-save-address') {
        const labelInput = document.getElementById('new_address_label');
        const label = labelInput ? labelInput.value.trim() : '';

        const firstName = document.querySelector('input[name="first_name"]')?.value || document.getElementById('c_fname')?.value || '';
        const lastName = document.querySelector('input[name="last_name"]')?.value || document.getElementById('c_lname')?.value || '';
        const recipientName = (firstName + ' ' + lastName).trim();
        
        const phone = document.querySelector('input[name="phone"]')?.value || document.getElementById('c_phone')?.value || '';
        const address = document.querySelector('input[name="address"]')?.value || document.getElementById('c_address')?.value || '';
        const city = document.querySelector('input[name="state_country"]')?.value || document.getElementById('c_state_country')?.value || '';
        const postalZip = document.querySelector('input[name="postal_zip"]')?.value || document.getElementById('c_postal_zip')?.value || '';
        const latitude = document.getElementById('latitude')?.value || '';
        const longitude = document.getElementById('longitude')?.value || '';

        if (!label) {
            alert('Silakan isi label alamat terlebih dahulu (Cth: Rumah / Toko).');
            if (labelInput) labelInput.focus();
            return;
        }

        if (!address || !city) {
            alert('Alamat dan kota wajib diisi!');
            return;
        }

        fetch('/checkout/save-address', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                label: label,
                recipient_name: recipientName || 'Penerima',
                phone: phone,
                address: address,
                city: city,
                postal_zip: postalZip,
                latitude: latitude,
                longitude: longitude
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Gagal menyimpan alamat.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem.');
        });
    }
});

function numberFormat(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'shipping_courier_select') {
        const selectedOption = e.target.options[e.target.selectedIndex];
        const cost = selectedOption.dataset.cost ? parseFloat(selectedOption.dataset.cost) : 0;

        const shippingCostInput = document.getElementById('shipping_cost_input');
        const shippingCostText = document.getElementById('shipping-cost-text');
        const orderTotalText = document.getElementById('order-total-text');
        
        if (shippingCostInput) shippingCostInput.value = cost;
        if (shippingCostText) shippingCostText.innerText = 'Rp ' + numberFormat(cost);

        let subtotal = 0;
        const subtotalEl = document.getElementById('subtotal-amount') || document.querySelector('.cart-subtotal') || document.getElementById('subtotal-text'); 
        if (subtotalEl) {
            subtotal = parseFloat(subtotalEl.dataset.subtotal || subtotalEl.innerText.replace(/[^0-9]/g, '')) || 0;
        }
        
        let discount = 0;
        const discountEl = document.getElementById('discount-amount');
        if (discountEl) {
            discount = parseFloat(discountEl.dataset.discount || discountEl.innerText.replace(/[^0-9]/g, '')) || 0;
        }

        let total = (subtotal - discount) + cost;
        if (orderTotalText) orderTotalText.innerText = 'Rp ' + numberFormat(total);
    }
});