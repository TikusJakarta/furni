@extends('layout.admin')

@section('title', 'CMS Pengaturan Web - Admin Furni')

@section('admin_content')
    <div class="page-head">
        <div>
            <h1>CMS Pengaturan Web</h1>
            <p>Ubah teks, judul, label, dan informasi website secara real-time.</p>
        </div>
    </div>

    <div class="form-narrow" style="max-width: 900px;">
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

        <form action="{{ route('admin.settings.update') }}" method="POST" class="form-card form-card--wide">
            @csrf

            <!-- ========== UMUM ========== -->
            <section class="form-section">
                <div class="form-section-head">
                    <h3>Pengaturan Umum Web</h3>
                    <p>Nama brand dan teks footer yang tampil di seluruh website.</p>
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

            <!-- ========== TEKS ADMIN DASHBOARD ========== -->
            <section class="form-section">
                <div class="form-section-head">
                    <h3>Pengaturan Teks Admin Dashboard</h3>
                    <p>Semua teks yang tampil di halaman dashboard admin.</p>
                </div>

                <h4 class="form-subtitle">Judul dan sambutan</h4>
                <div class="ad-field">
                    <label for="admin_dashboard_title" class="ad-label">Judul Utama Dashboard</label>
                    <input type="text" id="admin_dashboard_title" name="admin_dashboard_title" class="ad-input"
                        value="{{ $settings['admin_dashboard_title'] ?? 'Admin Dashboard' }}">
                </div>
                <div class="ad-field">
                    <label for="admin_dashboard_welcome" class="ad-label">Teks Sambutan Dashboard</label>
                    <input type="text" id="admin_dashboard_welcome" name="admin_dashboard_welcome" class="ad-input"
                        value="{{ $settings['admin_dashboard_welcome'] ?? '' }}">
                </div>
                <div class="ad-field">
                    <label for="admin_quick_menu_title" class="ad-label">Judul Menu Cepat</label>
                    <input type="text" id="admin_quick_menu_title" name="admin_quick_menu_title" class="ad-input"
                        value="{{ $settings['admin_quick_menu_title'] ?? 'Menu Cepat Admin' }}">
                </div>

                <h4 class="form-subtitle">Label tombol menu</h4>
                <div class="row">
                    <div class="col-md-4 ad-field">
                        <label for="admin_btn_manage_products" class="ad-label">Tombol Produk</label>
                        <input type="text" id="admin_btn_manage_products" name="admin_btn_manage_products" class="ad-input"
                            value="{{ $settings['admin_btn_manage_products'] ?? 'Kelola Produk' }}">
                    </div>
                    <div class="col-md-4 ad-field">
                        <label for="admin_btn_manage_orders" class="ad-label">Tombol Pesanan</label>
                        <input type="text" id="admin_btn_manage_orders" name="admin_btn_manage_orders" class="ad-input"
                            value="{{ $settings['admin_btn_manage_orders'] ?? 'Kelola Pesanan' }}">
                    </div>
                    <div class="col-md-4 ad-field">
                        <label for="admin_btn_cms_settings" class="ad-label">Tombol CMS</label>
                        <input type="text" id="admin_btn_cms_settings" name="admin_btn_cms_settings" class="ad-input"
                            value="{{ $settings['admin_btn_cms_settings'] ?? 'CMS Pengaturan Web' }}">
                    </div>
                </div>

                <h4 class="form-subtitle">Label kartu statistik</h4>
                <div class="row">
                    <div class="col-md-4 ad-field">
                        <label for="admin_card_total_products" class="ad-label">Kartu Produk</label>
                        <input type="text" id="admin_card_total_products" name="admin_card_total_products" class="ad-input"
                            value="{{ $settings['admin_card_total_products'] ?? 'Total Produk' }}">
                    </div>
                    <div class="col-md-4 ad-field">
                        <label for="admin_card_total_orders" class="ad-label">Kartu Pesanan</label>
                        <input type="text" id="admin_card_total_orders" name="admin_card_total_orders" class="ad-input"
                            value="{{ $settings['admin_card_total_orders'] ?? 'Total Pesanan' }}">
                    </div>
                    <div class="col-md-4 ad-field">
                        <label for="admin_card_total_revenue" class="ad-label">Kartu Pendapatan</label>
                        <input type="text" id="admin_card_total_revenue" name="admin_card_total_revenue" class="ad-input"
                            value="{{ $settings['admin_card_total_revenue'] ?? 'Pendapatan (Paid)' }}">
                    </div>
                </div>

                <h4 class="form-subtitle">Tabel pesanan terbaru</h4>
                <div class="ad-field">
                    <label for="admin_recent_orders_title" class="ad-label">Judul Tabel Pesanan Terbaru</label>
                    <input type="text" id="admin_recent_orders_title" name="admin_recent_orders_title" class="ad-input"
                        value="{{ $settings['admin_recent_orders_title'] ?? 'Pesanan Terbaru Masuk' }}">
                </div>
                <div class="row">
                    <div class="col-md-6 ad-field">
                        <label for="admin_link_view_all_orders" class="ad-label">Teks Link "Lihat Semua"</label>
                        <input type="text" id="admin_link_view_all_orders" name="admin_link_view_all_orders" class="ad-input"
                            value="{{ $settings['admin_link_view_all_orders'] ?? 'Lihat Semua Pesanan' }}">
                    </div>
                    <div class="col-md-6 ad-field">
                        <label for="admin_empty_orders" class="ad-label">Teks Data Kosong / Belum Ada Pesanan</label>
                        <input type="text" id="admin_empty_orders" name="admin_empty_orders" class="ad-input"
                            value="{{ $settings['admin_empty_orders'] ?? 'Belum ada pesanan masuk.' }}">
                    </div>
                </div>

                <h4 class="form-subtitle">Header kolom tabel</h4>
                <div class="row">
                    <div class="col-md-4 ad-field">
                        <label for="admin_th_order_id" class="ad-label">Kolom ID Order</label>
                        <input type="text" id="admin_th_order_id" name="admin_th_order_id" class="ad-input"
                            value="{{ $settings['admin_th_order_id'] ?? 'ID Order' }}">
                    </div>
                    <div class="col-md-4 ad-field">
                        <label for="admin_th_customer" class="ad-label">Kolom Pemesan</label>
                        <input type="text" id="admin_th_customer" name="admin_th_customer" class="ad-input"
                            value="{{ $settings['admin_th_customer'] ?? 'Pemesan' }}">
                    </div>
                    <div class="col-md-4 ad-field">
                        <label for="admin_th_total_price" class="ad-label">Kolom Total Harga</label>
                        <input type="text" id="admin_th_total_price" name="admin_th_total_price" class="ad-input"
                            value="{{ $settings['admin_th_total_price'] ?? 'Total Harga' }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 ad-field">
                        <label for="admin_th_status" class="ad-label">Kolom Status</label>
                        <input type="text" id="admin_th_status" name="admin_th_status" class="ad-input"
                            value="{{ $settings['admin_th_status'] ?? 'Status' }}">
                    </div>
                    <div class="col-md-6 ad-field">
                        <label for="admin_th_date" class="ad-label">Kolom Tanggal</label>
                        <input type="text" id="admin_th_date" name="admin_th_date" class="ad-input"
                            value="{{ $settings['admin_th_date'] ?? 'Tanggal' }}">
                    </div>
                </div>
            </section>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Simpan Perubahan CMS</button>
            </div>
        </form>
    </div>
@endsection
