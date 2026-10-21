@extends('layout.admin')

@section('title', 'CMS Pengaturan Web - Admin Furni')

@section('admin_content')
    @php
        // Nama bawaan menu sidebar (dari config/admin.php), dipakai sebagai placeholder
        $menuDefaults = collect(config('admin.menu'))
            ->filter(function ($item) { return isset($item['setting']); })
            ->mapWithKeys(function ($item) { return [$item['setting'] => $item['label']]; });
    @endphp

    <div class="page-head">
        <div>
            <h1>CMS Pengaturan Web</h1>
            <p>Ubah teks, judul, label, dan informasi website secara real-time.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="ad-alert ad-alert--success" role="status">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="ad-alert ad-alert--error" role="alert">
            <i class="fa fa-exclamation-circle"></i>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf

        <div class="row g-3">

            <!-- UMUM -->
            <div class="col-xl-6 col-xxl-4">
                <section class="form-card form-card--compact h-100">
                    <div class="form-card-head">
                        <h2>Pengaturan Umum Web</h2>
                        <p>Nama brand dan teks footer di seluruh website.</p>
                    </div>
                    <div class="ad-field">
                        <label for="brand_name" class="ad-label">Brand Name</label>
                        <input type="text" id="brand_name" name="brand_name" class="ad-input"
                            value="{{ $settings['brand_name'] ?? 'Furni' }}">
                    </div>
                    <div class="ad-field">
                        <label for="footer_description" class="ad-label">Footer Description</label>
                        <textarea id="footer_description" name="footer_description" class="ad-input"
                            rows="3">{{ $settings['footer_description'] ?? '' }}</textarea>
                    </div>
                </section>
            </div>

            <!-- DASHBOARD -->
            <div class="col-xl-6 col-xxl-4">
                <section class="form-card form-card--compact h-100">
                    <div class="form-card-head">
                        <h2>Judul &amp; Sambutan Dashboard</h2>
                        <p>Teks di bagian atas dashboard admin.</p>
                    </div>
                    <div class="ad-field">
                        <label for="admin_dashboard_title" class="ad-label">Judul Tab Browser</label>
                        <input type="text" id="admin_dashboard_title" name="admin_dashboard_title" class="ad-input"
                            value="{{ $settings['admin_dashboard_title'] ?? 'Admin Dashboard' }}">
                        <small class="ad-hint">Tampil di tab browser saat dashboard dibuka.</small>
                    </div>
                    <div class="ad-field">
                        <label for="admin_dashboard_welcome" class="ad-label">Teks Sambutan</label>
                        <input type="text" id="admin_dashboard_welcome" name="admin_dashboard_welcome" class="ad-input"
                            value="{{ $settings['admin_dashboard_welcome'] ?? '' }}">
                        <small class="ad-hint">Kosongkan untuk memakai sapaan bawaan.</small>
                    </div>
                </section>
            </div>

            <!-- MENU SIDEBAR -->
            <div class="col-xl-6 col-xxl-4">
                <section class="form-card form-card--compact h-100">
                    <div class="form-card-head">
                        <h2>Nama Menu Sidebar</h2>
                        <p>Kosongkan untuk memakai nama bawaan.</p>
                    </div>
                    <div class="ad-field">
                        <label for="admin_btn_manage_products" class="ad-label">Menu Produk</label>
                        <input type="text" id="admin_btn_manage_products" name="admin_btn_manage_products" class="ad-input"
                            value="{{ $settings['admin_btn_manage_products'] ?? '' }}"
                            placeholder="{{ $menuDefaults['admin_btn_manage_products'] ?? '' }}">
                    </div>
                    <div class="ad-field">
                        <label for="admin_btn_manage_orders" class="ad-label">Menu Pesanan</label>
                        <input type="text" id="admin_btn_manage_orders" name="admin_btn_manage_orders" class="ad-input"
                            value="{{ $settings['admin_btn_manage_orders'] ?? '' }}"
                            placeholder="{{ $menuDefaults['admin_btn_manage_orders'] ?? '' }}">
                    </div>
                    <div class="ad-field">
                        <label for="admin_btn_cms_settings" class="ad-label">Menu Pengaturan</label>
                        <input type="text" id="admin_btn_cms_settings" name="admin_btn_cms_settings" class="ad-input"
                            value="{{ $settings['admin_btn_cms_settings'] ?? '' }}"
                            placeholder="{{ $menuDefaults['admin_btn_cms_settings'] ?? '' }}">
                    </div>
                </section>
            </div>

            <!-- KARTU STATISTIK -->
            <div class="col-xl-6 col-xxl-4">
                <section class="form-card form-card--compact h-100">
                    <div class="form-card-head">
                        <h2>Label Kartu Statistik</h2>
                        <p>Judul kecil di tiap kartu angka pada dashboard.</p>
                    </div>
                    <div class="ad-field">
                        <label for="admin_card_total_products" class="ad-label">Kartu Produk</label>
                        <input type="text" id="admin_card_total_products" name="admin_card_total_products" class="ad-input"
                            value="{{ $settings['admin_card_total_products'] ?? 'Total Produk' }}">
                    </div>
                    <div class="ad-field">
                        <label for="admin_card_total_orders" class="ad-label">Kartu Pesanan</label>
                        <input type="text" id="admin_card_total_orders" name="admin_card_total_orders" class="ad-input"
                            value="{{ $settings['admin_card_total_orders'] ?? 'Total Pesanan' }}">
                    </div>
                    <div class="ad-field">
                        <label for="admin_card_total_revenue" class="ad-label">Kartu Pendapatan</label>
                        <input type="text" id="admin_card_total_revenue" name="admin_card_total_revenue" class="ad-input"
                            value="{{ $settings['admin_card_total_revenue'] ?? 'Pendapatan (Paid)' }}">
                    </div>
                </section>
            </div>

            <!-- TABEL PESANAN TERBARU -->
            <div class="col-xl-6 col-xxl-4">
                <section class="form-card form-card--compact h-100">
                    <div class="form-card-head">
                        <h2>Tabel Pesanan Terbaru</h2>
                        <p>Judul, tautan, dan pesan kosong tabel di dashboard.</p>
                    </div>
                    <div class="ad-field">
                        <label for="admin_recent_orders_title" class="ad-label">Judul Tabel</label>
                        <input type="text" id="admin_recent_orders_title" name="admin_recent_orders_title" class="ad-input"
                            value="{{ $settings['admin_recent_orders_title'] ?? 'Pesanan Terbaru Masuk' }}">
                    </div>
                    <div class="ad-field">
                        <label for="admin_link_view_all_orders" class="ad-label">Teks Tautan "Lihat Semua"</label>
                        <input type="text" id="admin_link_view_all_orders" name="admin_link_view_all_orders" class="ad-input"
                            value="{{ $settings['admin_link_view_all_orders'] ?? 'Lihat Semua Pesanan' }}">
                    </div>
                    <div class="ad-field">
                        <label for="admin_empty_orders" class="ad-label">Teks Data Kosong</label>
                        <input type="text" id="admin_empty_orders" name="admin_empty_orders" class="ad-input"
                            value="{{ $settings['admin_empty_orders'] ?? 'Belum ada pesanan masuk.' }}">
                    </div>
                </section>
            </div>

            <!-- HEADER KOLOM -->
            <div class="col-xl-6 col-xxl-4">
                <section class="form-card form-card--compact h-100">
                    <div class="form-card-head">
                        <h2>Header Kolom Tabel</h2>
                        <p>Nama kolom pada tabel pesanan terbaru.</p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 ad-field">
                            <label for="admin_th_order_id" class="ad-label">ID Order</label>
                            <input type="text" id="admin_th_order_id" name="admin_th_order_id" class="ad-input"
                                value="{{ $settings['admin_th_order_id'] ?? 'ID Order' }}">
                        </div>
                        <div class="col-sm-6 ad-field">
                            <label for="admin_th_customer" class="ad-label">Pemesan</label>
                            <input type="text" id="admin_th_customer" name="admin_th_customer" class="ad-input"
                                value="{{ $settings['admin_th_customer'] ?? 'Pemesan' }}">
                        </div>
                        <div class="col-sm-6 ad-field">
                            <label for="admin_th_total_price" class="ad-label">Total Harga</label>
                            <input type="text" id="admin_th_total_price" name="admin_th_total_price" class="ad-input"
                                value="{{ $settings['admin_th_total_price'] ?? 'Total Harga' }}">
                        </div>
                        <div class="col-sm-6 ad-field">
                            <label for="admin_th_status" class="ad-label">Status</label>
                            <input type="text" id="admin_th_status" name="admin_th_status" class="ad-input"
                                value="{{ $settings['admin_th_status'] ?? 'Status' }}">
                        </div>
                        <div class="col-sm-6 ad-field">
                            <label for="admin_th_date" class="ad-label">Tanggal</label>
                            <input type="text" id="admin_th_date" name="admin_th_date" class="ad-input"
                                value="{{ $settings['admin_th_date'] ?? 'Tanggal' }}">
                        </div>
                    </div>
                </section>
            </div>


            <!-- WARNA TAMPILAN WEBSITE -->
            @if(!empty($themeGroups))
                <div class="col-12">
                    <section class="form-card form-card--compact">
                        <div class="form-card-head">
                            <h2>Warna Tampilan Website</h2>
                            <p>Atur warna navbar, hero, dan footer. Kosongkan kolom untuk memakai warna bawaan template.</p>
                        </div>

                        <div class="row g-4">
                            <div class="col-xl-7">
                                <div class="row g-4">
                                    @foreach($themeGroups as $groupLabel => $fields)
                                        <div class="col-md-6">
                                            <h3 class="theme-group-title">{{ $groupLabel }}</h3>

                                            @foreach($fields as $key => $field)
                                                <div class="theme-row" data-theme-field data-key="{{ $key }}"
                                                    data-var="{{ $field['var'] }}" data-default="{{ $field['default'] }}"
                                                    @if(!empty($field['against'])) data-against="{{ $field['against'] }}" @endif>
                                                    <label class="ad-label" for="{{ $key }}">{{ $field['label'] }}</label>
                                                    <div class="theme-control">
                                                        <input type="color" class="theme-picker" value="{{ $field['default'] }}"
                                                            aria-label="Pilih warna: {{ $field['label'] }}">
                                                        <input type="text" id="{{ $key }}" name="{{ $key }}" class="ad-input theme-hex"
                                                            value="{{ $settings[$key] ?? '' }}" placeholder="{{ $field['default'] }}"
                                                            maxlength="7" autocomplete="off" spellcheck="false"
                                                            pattern="#[0-9a-fA-F]{6}" title="Kode warna 6 digit, contoh: {{ $field['default'] }}">
                                                        <button type="button" class="theme-reset" title="Kembalikan ke warna bawaan"
                                                            aria-label="Kembalikan {{ $field['label'] }} ke warna bawaan">
                                                            <i class="fa fa-rotate-left" aria-hidden="true"></i>
                                                        </button>
                                                    </div>
                                                    <small class="ad-hint theme-hint" hidden></small>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" class="theme-reset-all" id="themeResetAll">
                                    <i class="fa fa-rotate-left" aria-hidden="true"></i> Kembalikan semua ke warna bawaan
                                </button>
                            </div>

                            <!-- PRATINJAU -->
                            <div class="col-xl-5">
                                <div class="theme-preview-wrap">
                                    <span class="ad-label">Pratinjau</span>
                                    <div class="theme-preview" id="themePreview" aria-hidden="true">
                                        <div class="tp-nav">
                                            <span class="tp-brand">{{ $settings['brand_name'] ?? 'Furni' }}<i>.</i></span>
                                            <span class="tp-menu">
                                                <span class="is-active">Home</span>
                                                <span>Shop</span>
                                                <span>About</span>
                                            </span>
                                        </div>
                                        <div class="tp-hero"><strong>Judul Halaman</strong></div>
                                        <div class="tp-body"><span></span><span></span><span></span></div>
                                        <div class="tp-footer">
                                            <div class="tp-logo">{{ $settings['brand_name'] ?? 'Furni' }}.</div>
                                            <p>Deskripsi singkat toko di bagian footer.</p>
                                            <div class="tp-links"><span>About us</span><span>Services</span><span>Blog</span></div>
                                            <div class="tp-social"><b></b><b></b><b></b></div>
                                        </div>
                                    </div>
                                    <small class="ad-hint">Pratinjau berubah langsung saat warna diganti. Tersimpan setelah klik "Simpan Perubahan CMS".</small>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            @endif
        </div>

        <div class="savebar">
            <p>Perubahan langsung tampil di website setelah disimpan.</p>
            <button type="submit" class="btn-submit">Simpan Perubahan CMS</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('js/style-setting.js') }}"></script>
@endpush
