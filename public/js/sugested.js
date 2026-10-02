document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-input');
    const suggestionsBox = document.getElementById('search-suggestions-box');
    let debounceTimer;

    if (!searchInput || !suggestionsBox) return;

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            suggestionsBox.classList.add('d-none');
            suggestionsBox.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(() => {
            // Gunakan URL endpoint secara langsung agar terbaca di file JS
            fetch(`/shop/suggestions?q=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        let html = '';
                        data.data.forEach(product => {
                            html += `
                                        <a href="${product.url}" class="d-flex align-items-center p-3 text-decoration-none border-bottom bg-white" style="transition: background 0.2s;">
                                            <img src="${product.image}" alt="${product.name}" class="rounded border me-3" style="width: 45px; height: 45px; object-fit: cover; flex-shrink: 0;">
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h6 class="mb-0 text-dark text-truncate font-weight-bold" style="font-size: 0.9rem;">${product.name}</h6>
                                                <span class="text-primary font-weight-bold" style="font-size: 0.8rem;">Rp ${product.price}</span>
                                            </div>
                                        </a>
                                    `;
                        });
                        suggestionsBox.innerHTML = html;
                        suggestionsBox.classList.remove('d-none');
                    } else {
                        suggestionsBox.innerHTML = '<div class="p-3 text-muted text-center small">Produk tidak ditemukan</div>';
                        suggestionsBox.classList.remove('d-none');
                    }
                })
                .catch(err => console.error('Gagal mengambil suggestion:', err));
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.classList.add('d-none');
        }
    });
});