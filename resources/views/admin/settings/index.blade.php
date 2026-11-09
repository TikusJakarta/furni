@extends('layout.admin')

@section('title', 'CMS Website - Admin Furni')

@section('admin_content')
    <div class="page-head">
        <div>
            <h1>CMS Website</h1>
            <p>Atur judul, teks, dan tampilan yang dilihat pengunjung di website.</p>
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

            <!-- IDENTITAS & SEO -->
            <div class="col-xl-6 col-xxl-4">
                <section class="form-card form-card--compact h-100">
                    <div class="form-card-head">
                        <h2>Identitas &amp; SEO</h2>
                        <p>Nama toko dan deskripsi untuk mesin pencari seperti Google.</p>
                    </div>
                    <div class="ad-field">
                        <label for="brand_name" class="ad-label">Nama Brand</label>
                        <input type="text" id="brand_name" name="brand_name" class="ad-input"
                            value="{{ $settings['brand_name'] ?? 'Furni' }}">
                        <small class="ad-hint">Tampil di navbar, footer, dan judul tab browser.</small>
                    </div>
                    <div class="ad-field">
                        <label for="meta_description" class="ad-label">Deskripsi Website</label>
                        <textarea id="meta_description" name="meta_description" class="ad-input" rows="3"
                            maxlength="255">{{ $settings['meta_description'] ?? '' }}</textarea>
                        <small class="ad-hint">Ringkasan toko yang muncul di hasil pencarian (sekitar 150 karakter).</small>
                    </div>
                    <div class="ad-field">
                        <label for="meta_keywords" class="ad-label">Kata Kunci</label>
                        <input type="text" id="meta_keywords" name="meta_keywords" class="ad-input"
                            value="{{ $settings['meta_keywords'] ?? '' }}" placeholder="furniture, kursi, meja, sofa">
                        <small class="ad-hint">Pisahkan dengan koma.</small>
                    </div>
                </section>
            </div>

            <!-- FOOTER -->
            <div class="col-xl-6 col-xxl-4">
                <section class="form-card form-card--compact h-100">
                    <div class="form-card-head">
                        <h2>Footer</h2>
                        <p>Teks di bagian bawah setiap halaman.</p>
                    </div>
                    <div class="ad-field">
                        <label for="footer_description" class="ad-label">Deskripsi Footer</label>
                        <textarea id="footer_description" name="footer_description" class="ad-input"
                            rows="4">{{ $settings['footer_description'] ?? '' }}</textarea>
                        <small class="ad-hint">Kosongkan untuk memakai teks bawaan template.</small>
                    </div>
                    <div class="ad-field">
                        <label for="footer_newsletter_title" class="ad-label">Judul Newsletter</label>
                        <input type="text" id="footer_newsletter_title" name="footer_newsletter_title" class="ad-input"
                            value="{{ $settings['footer_newsletter_title'] ?? 'Subscribe to Newsletter' }}">
                    </div>
                    <div class="ad-field">
                        <label for="footer_sofa_image" class="ad-label">Gambar Sofa Footer</label>
                        <input type="text" id="footer_sofa_image" name="footer_sofa_image" class="ad-input"
                            value="{{ $settings['footer_sofa_image'] ?? 'images/sofa.png' }}">
                        <small class="ad-hint">Lokasi file di folder public, misalnya images/sofa.png.</small>
                    </div>
                </section>
            </div>

            <!-- MEDIA SOSIAL -->
            <div class="col-xl-6 col-xxl-4">
                <section class="form-card form-card--compact h-100">
                    <div class="form-card-head">
                        <h2>Media Sosial</h2>
                        <p>Tautan ikon sosial di footer. Kosongkan jika tidak dipakai.</p>
                    </div>
                    <div class="ad-field">
                        <label for="social_facebook" class="ad-label">Facebook</label>
                        <input type="url" id="social_facebook" name="social_facebook" class="ad-input"
                            value="{{ $settings['social_facebook'] ?? '' }}" placeholder="https://facebook.com/tokokamu">
                    </div>
                    <div class="ad-field">
                        <label for="social_twitter" class="ad-label">Twitter / X</label>
                        <input type="url" id="social_twitter" name="social_twitter" class="ad-input"
                            value="{{ $settings['social_twitter'] ?? '' }}" placeholder="https://x.com/tokokamu">
                    </div>
                    <div class="ad-field">
                        <label for="social_instagram" class="ad-label">Instagram</label>
                        <input type="url" id="social_instagram" name="social_instagram" class="ad-input"
                            value="{{ $settings['social_instagram'] ?? '' }}" placeholder="https://instagram.com/tokokamu">
                    </div>
                    <div class="ad-field">
                        <label for="social_linkedin" class="ad-label">LinkedIn</label>
                        <input type="url" id="social_linkedin" name="social_linkedin" class="ad-input"
                            value="{{ $settings['social_linkedin'] ?? '' }}"
                            placeholder="https://linkedin.com/company/tokokamu">
                    </div>
                </section>
            </div>

            <!-- HALAMAN SHOP -->
            <div class="col-xl-6 col-xxl-4">
                <section class="form-card form-card--compact h-100">
                    <div class="form-card-head">
                        <h2>Halaman Shop</h2>
                        <p>Judul yang dilihat pengunjung di halaman belanja.</p>
                    </div>
                    <div class="ad-field">
                        <label for="shop_page_title" class="ad-label">Judul Tab Browser</label>
                        <input type="text" id="shop_page_title" name="shop_page_title" class="ad-input"
                            value="{{ $settings['shop_page_title'] ?? 'Shop - Furni' }}">
                    </div>
                    <div class="ad-field">
                        <label for="shop_hero_title" class="ad-label">Judul Banner</label>
                        <input type="text" id="shop_hero_title" name="shop_hero_title" class="ad-input"
                            value="{{ $settings['shop_hero_title'] ?? 'Shop' }}">
                        <small class="ad-hint">Tulisan besar di bagian atas halaman Shop.</small>
                    </div>
                </section>
            </div>

            <!-- INVOICE -->
            <div class="col-xl-6 col-xxl-4">
                <section class="form-card form-card--compact h-100">
                    <div class="form-card-head">
                        <h2>Invoice</h2>
                        <p>Identitas toko yang tercetak di invoice pesanan.</p>
                    </div>
                    <div class="ad-field">
                        <label for="invoice_company" class="ad-label">Nama Usaha</label>
                        <input type="text" id="invoice_company" name="invoice_company" class="ad-input"
                            value="{{ $settings['invoice_company'] ?? '' }}"
                            placeholder="{{ $settings['brand_name'] ?? 'Furni' }}">
                        <small class="ad-hint">Kosongkan untuk memakai nama brand.</small>
                    </div>
                    <div class="ad-field">
                        <label for="invoice_address" class="ad-label">Alamat Toko</label>
                        <textarea id="invoice_address" name="invoice_address" class="ad-input" rows="3"
                            maxlength="255">{{ $settings['invoice_address'] ?? '' }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 ad-field">
                            <label for="invoice_phone" class="ad-label">Telepon</label>
                            <input type="text" id="invoice_phone" name="invoice_phone" class="ad-input"
                                value="{{ $settings['invoice_phone'] ?? '' }}">
                        </div>
                        <div class="col-sm-6 ad-field">
                            <label for="invoice_email" class="ad-label">Email</label>
                            <input type="email" id="invoice_email" name="invoice_email" class="ad-input"
                                value="{{ $settings['invoice_email'] ?? '' }}">
                        </div>
                    </div>
                    <div class="ad-field">
                        <label for="invoice_payment_info" class="ad-label">Informasi Pembayaran</label>
                        <textarea id="invoice_payment_info" name="invoice_payment_info" class="ad-input" rows="3"
                            maxlength="255"
                            placeholder="BCA 1234567890 a.n. Nama Toko">{{ $settings['invoice_payment_info'] ?? '' }}</textarea>
                        <small class="ad-hint">Tampil di invoice yang belum lunas.</small>
                    </div>
                    <div class="ad-field">
                        <label for="invoice_note" class="ad-label">Catatan Kaki</label>
                        <input type="text" id="invoice_note" name="invoice_note" class="ad-input" maxlength="255"
                            value="{{ $settings['invoice_note'] ?? '' }}" placeholder="Terima kasih telah berbelanja!">
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
                                                            pattern="#[0-9a-fA-F]{6}"
                                                            title="Kode warna 6 digit, contoh: {{ $field['default'] }}">
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
                                            <div class="tp-links"><span>About us</span><span>Services</span><span>Blog</span>
                                            </div>
                                            <div class="tp-social"><b></b><b></b><b></b></div>
                                        </div>
                                    </div>
                                    <small class="ad-hint">Pratinjau berubah langsung saat warna diganti. Tersimpan setelah klik
                                        "Simpan Perubahan CMS".</small>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            @endif

        </div>

        <!-- BAR SIMPAN -->
        <div class="savebar">
            <p>Perubahan langsung tampil di website setelah disimpan.</p>
            <button type="submit" class="btn-submit">Simpan Perubahan</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('js/style-setting.js') }}"></script>
@endpush