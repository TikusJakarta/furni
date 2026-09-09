@extends('layout.app')

@section('title', $settings['checkout_page_title'] ?? 'Checkout - Furni')

@section('content')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Start Hero Section -->
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1>{{ $settings['checkout_hero_title'] ?? 'Checkout' }}</h1>
                    </div>
                </div>
                <div class="col-lg-7"></div>
            </div>
        </div>
    </div>
    <!-- End Hero Section -->

    <div class="untree_co-section">
        <div class="container">

            {{-- Notifikasi Error / Success --}}
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf
                <div class="row mb-5">
                    <div class="col-md-12">
                        <div class="border p-4 rounded" role="alert">
                            Returning customer? <a href="{{ route('login') }}">Click here</a> to login
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Kolom Kiri: Billing Details -->
                    <div class="col-md-6 mb-5 mb-md-0">

                        {{-- Dropdown Pilihan Alamat Tersimpan --}}
                        @if(isset($userAddresses) && $userAddresses->count() > 0)
                            <div class="mb-4 p-4 bg-light border rounded shadow-sm">
                                <label for="saved_address_select" class="font-weight-bold text-black mb-2">📍 Pilih dari Alamat Tersimpan:</label>
                                <select id="saved_address_select" class="form-control">
                                    <option value="">-- Ketik manual atau pilih alamat tersimpan --</option>
                                    @foreach($userAddresses as $addr)
                                        <option value="{{ $addr->id }}" data-address="{{ $addr->address }}"
                                            data-city="{{ $addr->city }}" data-postal="{{ $addr->postal_code }}"
                                            data-lat="{{ $addr->latitude }}" data-lng="{{ $addr->longitude }}"
                                            data-name="{{ $addr->recipient_name }}" data-phone="{{ $addr->phone }}">
                                            {{ $addr->label }} — {{ $addr->recipient_name }} ({{ $addr->address }},
                                            {{ $addr->city }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <h2 class="h3 mb-3 text-black">Billing Details</h2>
                        <div class="p-3 p-lg-5 border bg-white">

                            <div class="form-group">
                                <label for="c_country" class="text-black">Country <span class="text-danger">*</span></label>
                                <select id="c_country" name="country" class="form-control" required>
                                    <option value="">Select a country</option>
                                    @php
                                        $countriesList = $countries ?? ['Indonesia', 'Bangladesh', 'Algeria', 'Afghanistan', 'Ghana', 'Albania', 'Bahrain', 'Colombia', 'Dominican Republic'];
                                    @endphp
                                    @foreach($countriesList as $country)
                                        <option value="{{ $country }}" {{ old('country') == $country ? 'selected' : '' }}>
                                            {{ $country }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="c_fname" class="text-black">First Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="c_fname" name="first_name" required
                                        value="{{ old('first_name', auth()->user()->name ?? '') }}">
                                    @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="c_lname" class="text-black">Last Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="c_lname" name="last_name" required
                                        value="{{ old('last_name') }}">
                                    @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label for="c_companyname" class="text-black">Company Name</label>
                                    <input type="text" class="form-control" id="c_companyname" name="company_name"
                                        value="{{ old('company_name') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label for="c_address" class="text-black">Address <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="c_address" name="address"
                                        placeholder="Street address" required value="{{ old('address') }}">
                                    @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <input type="text" class="form-control" name="apartment"
                                    placeholder="Apartment, suite, unit etc. (optional)" value="{{ old('apartment') }}">
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="c_state_country" class="text-black">State / City <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="c_state_country" name="state_country"
                                        required value="{{ old('state_country') }}">
                                    @error('state_country') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="c_postal_zip" class="text-black">Postal / Zip <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="c_postal_zip" name="postal_zip" required
                                        value="{{ old('postal_zip') }}">
                                    @error('postal_zip') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <div class="col-md-6">
                                    <label for="c_email_address" class="text-black">Email Address <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="c_email_address" name="email" required
                                        value="{{ old('email', auth()->user()->email ?? '') }}">
                                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="c_phone" class="text-black">Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="c_phone" name="phone"
                                        placeholder="Phone Number" required value="{{ old('phone') }}">
                                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="text-black font-weight-bold mb-2">Pin Location on Map (Optional)</label>
                                <p class="text-muted small mb-2">Klik atau geser pin pada peta untuk menandai titik lokasi
                                    pengiriman.</p>
                                <div id="map" data-subtotal="{{ $total ?? ($subtotal ?? 0) }}"
                                    style="height: 320px; width: 100%; border-radius: 6px;" class="border"></div>
                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                                @error('latitude') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Kotak & Tombol Simpan Alamat Baru --}}
                            <div class="mt-4 p-4 bg-light border rounded shadow-sm mb-4">
                                <h5 class="font-weight-bold text-black mb-2" style="font-size: 15px;">💾 Simpan Alamat Ini untuk Checkout Berikutnya</h5>
                                <div class="row align-items-end">
                                    <div class="col-md-8 mb-2 mb-md-0">
                                        <label class="small text-muted mb-1">Label Alamat (Cth: Rumah Utama, Toko, Kantor)</label>
                                        <input type="text" id="new_address_label" class="form-control form-control-sm" placeholder="Beri nama alamat ini...">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" id="btn-save-address" class="btn btn-primary btn-sm btn-block py-2 text-white font-weight-bold" style="background-color: #2f3b4c; border-color: #2f3b4c;">
                                            Simpan Alamat
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Opsi Alamat Pengiriman Berbeda -->
                            <div class="form-group">
                                <label for="c_ship_different_address" class="text-black" data-bs-toggle="collapse"
                                    href="#ship_different_address" role="button" aria-expanded="false">
                                    <input type="checkbox" value="1" id="c_ship_different_address"
                                        name="ship_different_address" {{ old('ship_different_address') ? 'checked' : '' }}>
                                    Ship To A Different Address?
                                </label>
                                <div class="collapse {{ old('ship_different_address') ? 'show' : '' }}"
                                    id="ship_different_address">
                                    <div class="py-2">
                                        <div class="form-group">
                                            <label for="c_diff_country" class="text-black">Country</label>
                                            <select id="c_diff_country" name="diff_country" class="form-control">
                                                <option value="">Select a country</option>
                                                @foreach($countriesList as $country)
                                                    <option value="{{ $country }}" {{ old('diff_country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                <label for="c_diff_fname" class="text-black">First Name</label>
                                                <input type="text" class="form-control" id="c_diff_fname"
                                                    name="diff_first_name" value="{{ old('diff_first_name') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="c_diff_lname" class="text-black">Last Name</label>
                                                <input type="text" class="form-control" id="c_diff_lname"
                                                    name="diff_last_name" value="{{ old('diff_last_name') }}">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <label for="c_diff_address" class="text-black">Address</label>
                                                <input type="text" class="form-control" id="c_diff_address"
                                                    name="diff_address" placeholder="Street address"
                                                    value="{{ old('diff_address') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="c_order_notes" class="text-black">Order Notes</label>
                                <textarea name="order_notes" id="c_order_notes" cols="30" rows="5" class="form-control"
                                    placeholder="Write your notes here...">{{ old('order_notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Order Summary, Kupon & Payment Methods -->
                    <div class="col-md-6">

                        <!-- KOTAK KUPON PROMO -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h3 class="h3 mb-3 text-black">Kode Promo</h3>
                                <div class="p-3 p-lg-4 border bg-white">
                                    @if(session('coupon'))
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="text-success fw-bold"><i class="fa fa-tag"></i> Kupon Aktif:</span>
                                                <code>{{ session('coupon')['code'] }}</code>
                                            </div>
                                            <button type="button" id="remove-coupon-btn"
                                                class="btn btn-sm btn-outline-danger">Hapus Kupon</button>
                                        </div>
                                    @else
                                        <div class="input-group">
                                            <input type="text" id="coupon_code_input" class="form-control"
                                                placeholder="Masukkan kode promo">
                                            <button class="btn btn-black text-white" type="button"
                                                id="apply-coupon-btn">Gunakan</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- YOUR ORDER -->
                        <div class="row mb-5">
                            <div class="col-md-12">
                                <h2 class="h3 mb-3 text-black">Your Order</h2>
                                <div class="p-3 p-lg-5 border bg-white">
                                    <table class="table site-block-order-table mb-4">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($cartItems ?? [] as $id => $item)
                                                <tr>
                                                    <td>{{ $item['name'] }} <strong class="mx-2">x</strong>
                                                        {{ $item['quantity'] }}
                                                    </td>
                                                    <td>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-center">Keranjang belanja kosong.</td>
                                                </tr>
                                            @endforelse
                                            <tr>
                                                <td class="text-black font-weight-bold"><strong>Cart Subtotal</strong></td>
                                                <td class="text-black" id="subtotal-text"
                                                    data-subtotal="{{ $subtotal ?? 0 }}">Rp
                                                    {{ number_format($subtotal ?? 0, 0, ',', '.') }}
                                                </td>
                                            </tr>

                                            @if(isset($discount) && $discount > 0)
                                                <tr class="text-success">
                                                    <td><strong>Diskon Kupon</strong></td>
                                                    <td class="text-end" id="coupon-discount-text"><strong>- Rp
                                                            {{ number_format($discount, 0, ',', '.') }}</strong></td>
                                                </tr>
                                            @endif

                                            <tr>
                                                <td class="text-black font-weight-bold"><strong>Shipping Cost</strong></td>
                                                <td class="text-black" id="shipping-cost-text">Rp 0</td>
                                            </tr>
                                            <tr>
                                                <td class="text-black font-weight-bold"><strong>Order Total</strong></td>
                                                <td class="text-black font-weight-bold"><strong id="order-total-text">Rp
                                                        {{ number_format($total ?? 0, 0, ',', '.') }}</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- DROPDOWN PILIHAN KURIR -->
                                    <div class="form-group mb-4">
                                        <label for="shipping_courier_select" class="text-black font-weight-bold">Pilih Kurir
                                            Pengiriman <span class="text-danger">*</span></label>
                                        <select id="shipping_courier_select" name="shipping_courier" class="form-control"
                                            required>
                                            <option value="">-- Geser atau klik pin di peta terlebih dahulu --</option>
                                        </select>
                                        <input type="hidden" name="shipping_cost" id="shipping_cost_input" value="0">
                                        @error('shipping_courier') <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                        @error('shipping_cost') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <!-- Direct Bank Transfer -->
                                    <div class="border p-3 mb-3">
                                        <div class="form-check p-0">
                                            <input class="form-check-input ms-0 me-2" type="radio" name="payment_method"
                                                value="bank" id="bank" {{ old('payment_method') == 'bank' ? 'checked' : '' }}
                                                required>
                                            <label class="form-check-label text-black font-weight-bold" for="bank"
                                                data-bs-toggle="collapse" href="#collapsebank" role="button"
                                                aria-expanded="false" aria-controls="collapsebank">
                                                Direct Bank Transfer
                                            </label>
                                        </div>
                                        <div class="collapse {{ old('payment_method') == 'bank' ? 'show' : '' }}"
                                            id="collapsebank">
                                            <div class="py-2">
                                                <p class="mb-0 text-muted small">Make your payment directly into our bank
                                                    account. Please use your Order ID as the payment reference.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- QRIS / QR Code Payment -->
                                    <div class="border p-3 mb-5">
                                        <div class="form-check p-0">
                                            <input class="form-check-input ms-0 me-2" type="radio" name="payment_method"
                                                value="qr" id="qr" {{ old('payment_method') == 'qr' ? 'checked' : '' }}>
                                            <label class="form-check-label text-black font-weight-bold" for="qr"
                                                data-bs-toggle="collapse" href="#collapseqr" role="button"
                                                aria-expanded="false" aria-controls="collapseqr">
                                                QRIS / QR Code Payment
                                            </label>
                                        </div>
                                        <div class="collapse {{ old('payment_method') == 'qr' ? 'show' : '' }}"
                                            id="collapseqr">
                                            <div class="py-2">
                                                <p class="mb-0 text-muted small">Scan the QRIS code using your mobile
                                                    banking or e-wallet application (GoPay, OVO, Dana, BCA Mobile, etc.).
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @error('payment_method') <small class="text-danger d-block mb-3">{{ $message }}</small>
                                    @enderror

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-black btn-lg py-3 btn-block w-100"
                                            @if(empty($cartItems)) disabled @endif>Place Order</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Leaflet JS & Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="{{ asset('js/map.js') }}"></script>
    <script src="{{ asset('js/coupon.js') }}"></script>
    <script src="{{ asset('js/checkout-address.js') }}"></script>
@endsection