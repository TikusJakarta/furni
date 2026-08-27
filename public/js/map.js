document.addEventListener("DOMContentLoaded", function() {
    let defaultLat = -6.175392;
    let defaultLng = 106.827153;

    let savedLat = document.getElementById('latitude').value;
    let savedLng = document.getElementById('longitude').value;

    let lat = savedLat ? parseFloat(savedLat) : defaultLat;
    let lng = savedLng ? parseFloat(savedLng) : defaultLng;

    var map = L.map('map').setView([lat, lng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker = L.marker([lat, lng], { draggable: true }).addTo(map);

    function updateMarkerPosition(latLng) {
        document.getElementById('latitude').value = latLng.lat;
        document.getElementById('longitude').value = latLng.lng;
        
        fetchBiteshipRates(latLng.lat, latLng.lng);
    }

    if(!savedLat) {
        updateMarkerPosition({ lat: defaultLat, lng: defaultLng });
    } else {
        fetchBiteshipRates(lat, lng);
    }

    marker.on('dragend', function(e) {
        var position = marker.getLatLng();
        updateMarkerPosition(position);
        map.panTo(position);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateMarkerPosition(e.latlng);
        map.panTo(e.latlng);
    });

    function fetchBiteshipRates(latitude, longitude) {
        let selectCourier = document.getElementById('shipping_courier_select');
        if (!selectCourier) return;

        selectCourier.innerHTML = '<option value="">Memuat tarif kurir dari Biteship...</option>';

        fetch('/check-shipping-rates', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'include',
            body: JSON.stringify({ 
                latitude: latitude, 
                longitude: longitude 
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success && data.pricing.length > 0) {
                selectCourier.innerHTML = '<option value="">-- Pilih Layanan Kurir --</option>';

                // 1. Buat Grup Kategori agar rapi
                let instantGroup = document.createElement('optgroup');
                instantGroup.label = '⚡ Layanan Instant & Same Day';

                let regulerGroup = document.createElement('optgroup');
                regulerGroup.label = '📦 Layanan Reguler & Ekonomi';

                data.pricing.forEach(item => {
                    let opt = document.createElement('option');
                    
                    let courierNameService = `${item.courier_name.toUpperCase()} - ${item.courier_service_name}`;
                    
                    // Simpan nama kurir di value (untuk database shipping_courier)
                    opt.value = courierNameService;
                    // Simpan harga di attribute data-price
                    opt.setAttribute('data-price', item.price);
                    
                    opt.textContent = `${courierNameService} - Rp ${item.price.toLocaleString('id-ID')} (${item.shipment_duration})`;

                    // 2. Pisahkan otomatis masuk grup Instant atau Reguler berdasarkan durasi/nama layanan
                    let serviceName = item.courier_service_name.toLowerCase();
                    let duration = item.shipment_duration.toLowerCase();

                    if (serviceName.includes('instant') || serviceName.includes('same day') || duration.includes('hour') || duration.includes('jam')) {
                        instantGroup.appendChild(opt);
                    } else {
                        regulerGroup.appendChild(opt);
                    }
                });

                // 3. Masukkan grup ke dalam select (hanya jika ada isinya)
                if (instantGroup.children.length > 0) selectCourier.appendChild(instantGroup);
                if (regulerGroup.children.length > 0) selectCourier.appendChild(regulerGroup);

            } else {
                selectCourier.innerHTML = '<option value="">Kurir tidak tersedia untuk lokasi ini</option>';
            }
        })
        .catch(err => {
            console.error('Biteship Error:', err);
            selectCourier.innerHTML = '<option value="">Gagal memuat tarif kurir</option>';
        });
    }

    let courierSelectEl = document.getElementById('shipping_courier_select');
    if (courierSelectEl) {
        courierSelectEl.addEventListener('change', function() {
            let selectedOption = this.options[this.selectedIndex];
            // Ambil harga dari attribute data-price
            let cost = parseFloat(selectedOption.getAttribute('data-price')) || 0;

            document.getElementById('shipping_cost_input').value = cost;
            
            let shippingText = document.getElementById('shipping-cost-text');
            if (shippingText) {
                shippingText.innerText = 'Rp ' + cost.toLocaleString('id-ID');
            }

            // Ambil nilai subtotal dan hitung Order Total (Subtotal + Ongkir)
            let subtotalElem = document.getElementById('subtotal-text');
            let subtotal = parseFloat(subtotalElem?.getAttribute('data-subtotal')) || 0;
            let grandTotal = subtotal + cost;

            let orderTotalElem = document.getElementById('order-total-text');
            if (orderTotalElem) {
                orderTotalElem.innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
            }
        });
    }
});