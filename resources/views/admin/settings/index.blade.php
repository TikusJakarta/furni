@extends('layout.app')

@section('title', 'CMS Pengaturan Web - Admin Furni')

@section('content')
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1>CMS Pengaturan Web</h1>
                        <p class="text-white-50">Ubah teks, judul, label, dan informasi website secara real-time.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="untree_co-section before-footer-section">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form action="{{ route('admin.settings.update') }}" method="POST"
                        class="bg-light p-4 rounded shadow-sm">
                        @csrf

                        <h4 class="mb-3 text-black font-weight-bold">Pengaturan Umum Web</h4>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Brand Name</label>
                            <input type="text" name="brand_name" class="form-control"
                                value="{{ $settings['brand_name'] ?? 'Furni' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Footer Description</label>
                            <textarea name="footer_description" class="form-control"
                                rows="3">{{ $settings['footer_description'] ?? '' }}</textarea>
                        </div>

                        <hr class="my-4">

                        <h4 class="mb-3 text-black font-weight-bold">Pengaturan Teks Admin Dashboard</h4>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Judul Utama Dashboard</label>
                            <input type="text" name="admin_dashboard_title" class="form-control"
                                value="{{ $settings['admin_dashboard_title'] ?? 'Admin Dashboard' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Teks Sambutan Dashboard</label>
                            <input type="text" name="admin_dashboard_welcome" class="form-control"
                                value="{{ $settings['admin_dashboard_welcome'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Judul Menu Cepat</label>
                            <input type="text" name="admin_quick_menu_title" class="form-control"
                                value="{{ $settings['admin_quick_menu_title'] ?? 'Menu Cepat Admin' }}">
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Label Tombol Produk</label>
                                <input type="text" name="admin_btn_manage_products" class="form-control"
                                    value="{{ $settings['admin_btn_manage_products'] ?? 'Kelola Produk' }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Label Tombol Pesanan</label>
                                <input type="text" name="admin_btn_manage_orders" class="form-control"
                                    value="{{ $settings['admin_btn_manage_orders'] ?? 'Kelola Pesanan' }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Label Tombol CMS</label>
                                <input type="text" name="admin_btn_cms_settings" class="form-control"
                                    value="{{ $settings['admin_btn_cms_settings'] ?? 'CMS Pengaturan Web' }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Label Card Produk</label>
                                <input type="text" name="admin_card_total_products" class="form-control"
                                    value="{{ $settings['admin_card_total_products'] ?? 'Total Produk' }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Label Card Pesanan</label>
                                <input type="text" name="admin_card_total_orders" class="form-control"
                                    value="{{ $settings['admin_card_total_orders'] ?? 'Total Pesanan' }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Label Card Pendapatan</label>
                                <input type="text" name="admin_card_total_revenue" class="form-control"
                                    value="{{ $settings['admin_card_total_revenue'] ?? 'Pendapatan (Paid)' }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Judul Tabel Pesanan Terbaru</label>
                            <input type="text" name="admin_recent_orders_title" class="form-control"
                                value="{{ $settings['admin_recent_orders_title'] ?? 'Pesanan Terbaru Masuk' }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Teks Link "Lihat Semua"</label>
                                <input type="text" name="admin_link_view_all_orders" class="form-control"
                                    value="{{ $settings['admin_link_view_all_orders'] ?? 'Lihat Semua Pesanan' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Teks Data Kosong / Belum Ada Pesanan</label>
                                <input type="text" name="admin_empty_orders" class="form-control"
                                    value="{{ $settings['admin_empty_orders'] ?? 'Belum ada pesanan masuk.' }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Header Kolom ID Order</label>
                                <input type="text" name="admin_th_order_id" class="form-control"
                                    value="{{ $settings['admin_th_order_id'] ?? 'ID Order' }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Header Kolom Pemesan</label>
                                <input type="text" name="admin_th_customer" class="form-control"
                                    value="{{ $settings['admin_th_customer'] ?? 'Pemesan' }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-weight-bold">Header Kolom Total Harga</label>
                                <input type="text" name="admin_th_total_price" class="form-control"
                                    value="{{ $settings['admin_th_total_price'] ?? 'Total Harga' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Header Kolom Status</label>
                                <input type="text" name="admin_th_status" class="form-control"
                                    value="{{ $settings['admin_th_status'] ?? 'Status' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Header Kolom Tanggal</label>
                                <input type="text" name="admin_th_date" class="form-control"
                                    value="{{ $settings['admin_th_date'] ?? 'Tanggal' }}">
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <a href="{{ route('admin.dashboard') }}"
                                class="btn btn-secondary text-white py-3 w-50 d-flex align-items-center justify-content-center">Kembali
                                ke Dashboard</a>
                            <button type="submit" class="btn btn-primary text-white py-3 w-50">Simpan Perubahan CMS</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection