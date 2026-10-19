@extends('layout.admin')

@section('title', 'Tambah Kupon - Admin Furni')

@section('admin_content')
    <div class="page-head">
        <div>
            <h1>Tambah Kupon Baru</h1>
            <p>Atur kode, jenis diskon, dan masa berlakunya.</p>
        </div>
        <a href="{{ route('admin.coupons.index') }}" class="btn-back">&larr; Kembali ke Daftar Kupon</a>
    </div>

    @if ($errors->any())
        <div class="ad-alert ad-alert--error" role="alert">
            <i class="fa fa-exclamation-circle"></i>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf

        <div class="form-wide">
            <div class="row g-3">

                <!-- FORM -->
                <div class="col-xl-7">
                    <section class="form-card form-card--compact h-100">
                        <div class="form-card-head">
                            <h2>Detail Kupon</h2>
                            <p>Kolom bertanda <span class="ad-req">*</span> wajib diisi.</p>
                        </div>

                        <div class="ad-field">
                            <label for="code" class="ad-label">Kode Kupon <span class="ad-req">*</span></label>
                            <input type="text" class="ad-input ad-input--code" id="code" name="code"
                                placeholder="Contoh: LEBARAN50" required value="{{ old('code') }}">
                            <small class="ad-hint">Kode akan otomatis diubah menjadi huruf kapital.</small>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 ad-field">
                                <label for="type" class="ad-label">Tipe Diskon <span class="ad-req">*</span></label>
                                <select name="type" id="type" class="ad-input" required>
                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Potongan Harga Tetap (Rp)</option>
                                </select>
                            </div>
                            <div class="col-sm-6 ad-field">
                                <label for="value" class="ad-label">Nilai Diskon <span class="ad-req">*</span></label>
                                <input type="number" step="0.01" class="ad-input" id="value" name="value"
                                    placeholder="Misal: 10 atau 50000" required value="{{ old('value') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 ad-field">
                                <label for="min_spend" class="ad-label">Minimal Belanja (Rp)</label>
                                <input type="number" step="0.01" class="ad-input" id="min_spend" name="min_spend"
                                    placeholder="0 jika tanpa minimal belanja" value="{{ old('min_spend', 0) }}">
                            </div>
                            <div class="col-sm-6 ad-field">
                                <label for="expires_at" class="ad-label">Masa Berakhir (Opsional)</label>
                                <input type="datetime-local" class="ad-input" id="expires_at" name="expires_at"
                                    value="{{ old('expires_at') }}">
                            </div>
                        </div>
                    </section>
                </div>

                <!-- PRATINJAU -->
                <div class="col-xl-5">
                    <section class="form-card form-card--compact h-100">
                        <div class="form-card-head">
                            <h2>Pratinjau Kupon</h2>
                            <p>Berubah langsung saat kamu mengisi form.</p>
                        </div>

                        <div class="coupon-ticket" aria-live="polite">
                            <div class="coupon-ticket-main">
                                <span class="coupon-ticket-label">KODE PROMO</span>
                                <strong class="coupon-ticket-code" id="pvCode">KODEKAMU</strong>
                                <span class="coupon-ticket-sub" id="pvMin">Tanpa minimal belanja</span>
                            </div>
                            <div class="coupon-ticket-side">
                                <strong id="pvValue">&mdash;</strong>
                                <span id="pvUnit">DISKON</span>
                            </div>
                        </div>
                        <p class="coupon-ticket-expiry" id="pvExpiry">Berlaku selamanya</p>

                        <h3 class="tip-title">Tips</h3>
                        <ul class="tip-list">
                            <li>Pakai kode yang singkat dan mudah diingat.</li>
                            <li>Tipe <strong>Persentase</strong>: isi 10 untuk diskon 10%.</li>
                            <li>Tipe <strong>Tetap</strong>: isi 50000 untuk potongan Rp 50.000.</li>
                            <li>Kosongkan masa berakhir kalau kupon tidak punya batas waktu.</li>
                        </ul>
                    </section>
                </div>

            </div>

            <!-- BAR SIMPAN -->
            <div class="savebar">
                <p>Kupon aktif dan bisa dipakai pelanggan setelah disimpan.</p>
                <div class="savebar-actions">
                    <a href="{{ route('admin.coupons.index') }}" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-submit">Simpan Kupon</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset("js/create-coupon.js") }}"></script>
@endpush