let map, marker;

document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('map');
    if (!mapElement) return;

    // Koordinat default (Jakarta)
    const defaultLat = -6.175392;
    const defaultLng = 106.827153;

    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    const cityInput = document.querySelector('input[name="state_country"]') || document.getElementById('c_state_country') || document.getElementById('city');
    const addressInput = document.querySelector('input[name="address"]') || document.querySelector('textarea[name="address"]') || document.getElementById('c_address');
    const postalInput = document.querySelector('input[name="postal_zip"]') || document.getElementById('c_postal_zip');
    const nameInput = document.querySelector('input[name="first_name"]') || document.getElementById('c_fname');
    const phoneInput = document.querySelector('input[name="phone"]') || document.getElementById('c_phone');
    
    // Selector Checkbox Proteksi
    const protectionCheckbox = document.getElementById('use_protection');

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

    // 2. Integrasi Kotak Search / Geosearch
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

    // 3. Event ketika mengetik/memilih Kota
    if (cityInput) {
        cityInput.addEventListener('input', function () {
            const val = cityInput.value;
            
            const match = val.match(/^(.*?)\s*\((.*?)\)$/);
            if (match) {
                const cityName = match[1].trim();
                const postalCode = match[2].trim();
                
                cityInput.value = cityName;
                if (postalInput) {
                    postalInput.value = postalCode;
                }
            }

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

    // 4. AMBIL DATA KOTA & KODE POS
    fetch('/api/get-cities')
        .then(response => response.json())
        .then(data => {
            const cityDatalist = document.getElementById('city-options');
            const postalDatalist = document.getElementById('postal-options');
            
            if (data && Array.isArray(data)) {
                data.forEach(item => {
                    if (cityDatalist && item.city && item.postal_code) {
                        let option = document.createElement('option');
                        option.value = `${item.city} (${item.postal_code})`;
                        cityDatalist.appendChild(option);
                    }
                    if (postalDatalist && item.postal_code) {
                        let option = document.createElement('option');
                        option.value = item.postal_code;
                        postalDatalist.appendChild(option);
                    }
                });
            }
        })
        .catch(error => console.error('Gagal memuat rekomendasi kota/pos:', error));

    if (protectionCheckbox) {
        protectionCheckbox.addEventListener('change', function() {
            const lat = latInput && latInput.value ? parseFloat(latInput.value) : initialLat;
            const lng = lngInput && lngInput.value ? parseFloat(lngInput.value) : initialLng;
            fetchShippingRates(lat, lng, cityInput ? cityInput.value : '');
        });
    }

    calculateCartWeight();
    updateProtectionLabelDisplay();
    fetchShippingRates(initialLat, initialLng, cityInput ? cityInput.value : '');
});

// Fungsi untuk mengira dan memaparkan nominal proteksi secara langsung pada label
function updateProtectionLabelDisplay() {
    let totalProductValue = 0;
    
    const subtotalEl = document.getElementById('subtotal-text');
    if (subtotalEl) {
        totalProductValue = parseFloat(subtotalEl.dataset.subtotal) || 0;
    }

    const protectionFee = totalProductValue * 0.005;

    const feeLabelEl = document.getElementById('protection-fee-label');
    if (feeLabelEl) {
        feeLabelEl.innerText = `Rp ${numberFormat(protectionFee)}`;
    }
}

// Fungsi untuk mengambil data ongkir via AJAX ke Backend
// Fungsi untuk mengambil data ongkir via AJAX ke Backend
function fetchShippingRates(lat, lng, cityName = '') {
    const courierSelect = document.getElementById('shipping_courier_select');
    if (!courierSelect) return;

    const totalWeight = calculateCartWeight();
    const protectionCheckbox = document.getElementById('use_protection');
    const useProtection = protectionCheckbox ? protectionCheckbox.checked : false;

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
            weight: totalWeight,
            use_protection: useProtection
        })
    })
    .then(response => response.json())
    .then(data => {
        courierSelect.innerHTML = '<option value="">-- Pilih Layanan Kurir --</option>';
        
        if (data.success && data.pricing && data.pricing.length > 0) {
            let instantGroup = document.createElement('optgroup');
            instantGroup.label = '🚀 Layanan Instant';

            let samedayGroup = document.createElement('optgroup');
            samedayGroup.label = '🕒 Layanan Same Day';

            let regulerGroup = document.createElement('optgroup');
            regulerGroup.label = '📦 Layanan Reguler & Ekonomi';

            let hasInstant = false;
            let hasSameday = false;
            let hasReguler = false;

            data.pricing.forEach(rate => {
                let option = document.createElement('option');
                option.value = `${rate.courier_name} - ${rate.courier_service_name}`;
                
                option.dataset.cost = rate.price;
                let insuranceCost = rate.insurance_cost || 0;
                option.dataset.insurance = insuranceCost; 

                let totalCostWithInsurance = rate.price + (useProtection ? insuranceCost : 0);

                option.text = `${rate.courier_name} - ${rate.courier_service_name} - Rp ${numberFormat(totalCostWithInsurance)} (${rate.shipment_duration})`;

                const type = (rate.type || '').toLowerCase();
                const serviceName = (rate.courier_service_name || '').toLowerCase();

                if (type === 'instant' || (serviceName.includes('instant') && !serviceName.includes('same'))) {
                    instantGroup.appendChild(option);
                    hasInstant = true;
                } else if (type === 'sameday' || type === 'same_day' || serviceName.includes('same day') || serviceName.includes('sameday')) {
                    samedayGroup.appendChild(option);
                    hasSameday = true;
                } else {
                    regulerGroup.appendChild(option);
                    hasReguler = true;
                }
            });

            if (hasInstant) courierSelect.appendChild(instantGroup);
            if (hasSameday) courierSelect.appendChild(samedayGroup);
            if (hasReguler) courierSelect.appendChild(regulerGroup);

            if (courierSelect.value) {
                courierSelect.dispatchEvent(new Event('change'));
            }

        } else {
            courierSelect.innerHTML = '<option value="">Tidak ada kurir tersedia untuk lokasi ini</option>';
        }
    })
    .catch(error => {
        console.error('Error fetching shipping rates:', error);
        courierSelect.innerHTML = '<option value="">Gagal memuat kurir</option>';
    });
}

// Fungsi Helper untuk Menghitung Jumlah Berat
function calculateCartWeight() {
    let totalWeight = 0;
    const cartItems = document.querySelectorAll('.cart-item-row'); 

    if (cartItems.length > 0) {
        cartItems.forEach(row => {
            const weight = parseFloat(row.dataset.weight) || 0; 
            const qty = parseFloat(row.dataset.qty) || 1;       
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
            alert('Silakan isi label alamat terlebih dahulu.');
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