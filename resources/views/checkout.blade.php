@extends('layout.app')

@section('title', $settings['checkout_page_title'] ?? 'Checkout - Furni')

@section('content')
    <!-- Start Hero Section -->
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1>{{ $settings['checkout_hero_title'] ?? 'Checkout' }}</h1>
                    </div>
                </div>
                <div class="col-lg-7">
                    
                </div>
            </div>
        </div>
    </div>
    <!-- End Hero Section -->

    <div class="untree_co-section">
        <div class="container">
            <form action="{{ url('/checkout') }}" method="POST">
                @csrf
                <div class="row mb-5">
                    <div class="col-md-12">
                        <div class="border p-4 rounded" role="alert">
                            Returning customer? <a href="#">Click here</a> to login
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-5 mb-md-0">
                        <h2 class="h3 mb-3 text-black">Billing Details</h2>
                        <div class="p-3 p-lg-5 border bg-white">
                            <div class="form-group">
                                <label for="c_country" class="text-black">Country <span class="text-danger">*</span></label>
                                <select id="c_country" name="country" class="form-control" required>
                                    <option value="">Select a country</option>    
                                    <option value="bangladesh">bangladesh</option>    
                                    <option value="Algeria">Algeria</option>    
                                    <option value="Afghanistan">Afghanistan</option>    
                                    <option value="Ghana">Ghana</option>    
                                    <option value="Albania">Albania</option>    
                                    <option value="Bahrain">Bahrain</option>    
                                    <option value="Colombia">Colombia</option>    
                                    <option value="Dominican Republic">Dominican Republic</option>    
                                </select>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="c_fname" class="text-black">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="c_fname" name="first_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="c_lname" class="text-black">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="c_lname" name="last_name" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label for="c_companyname" class="text-black">Company Name </label>
                                    <input type="text" class="form-control" id="c_companyname" name="company_name">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label for="c_address" class="text-black">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="c_address" name="address" placeholder="Street address" required>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <input type="text" class="form-control" name="apartment" placeholder="Apartment, suite, unit etc. (optional)">
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="c_state_country" class="text-black">State / Country <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="c_state_country" name="state_country" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="c_postal_zip" class="text-black">Posta / Zip <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="c_postal_zip" name="postal_zip" required>
                                </div>
                            </div>

                            <div class="form-group row mb-5">
                                <div class="col-md-6">
                                    <label for="c_email_address" class="text-black">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="c_email_address" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="c_phone" class="text-black">Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="c_phone" name="phone" placeholder="Phone Number" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="c_create_account" class="text-black" data-bs-toggle="collapse" href="#create_an_account" role="button" aria-expanded="false" aria-controls="create_an_account">
                                    <input type="checkbox" value="1" id="c_create_account" name="create_account"> Create an account?
                                </label>
                                <div class="collapse" id="create_an_account">
                                    <div class="py-2 mb-4">
                                        <p class="mb-3">Create an account by entering the information below. If you are a returning customer please login at the top of the page.</p>
                                        <div class="form-group">
                                            <label for="c_account_password" class="text-black">Account Password</label>
                                            <input type="password" class="form-control" id="c_account_password" name="account_password">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="c_ship_different_address" class="text-black" data-bs-toggle="collapse" href="#ship_different_address" role="button" aria-expanded="false" aria-controls="ship_different_address">
                                    <input type="checkbox" value="1" id="c_ship_different_address" name="ship_different_address"> Ship To A Different Address?
                                </label>
                                <div class="collapse" id="ship_different_address">
                                    <div class="py-2">
                                        <div class="form-group">
                                            <label for="c_diff_country" class="text-black">Country <span class="text-danger">*</span></label>
                                            <select id="c_diff_country" name="diff_country" class="form-control">
                                                <option value="">Select a country</option>    
                                                <option value="bangladesh">bangladesh</option>    
                                                <option value="Algeria">Algeria</option>    
                                                <option value="Afghanistan">Afghanistan</option>    
                                                <option value="Ghana">Ghana</option>    
                                                <option value="Albania">Albania</option>    
                                                <option value="Bahrain">Bahrain</option>    
                                                <option value="Colombia">Colombia</option>    
                                                <option value="Dominican Republic">Dominican Republic</option>    
                                            </select>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                <label for="c_diff_fname" class="text-black">First Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="c_diff_fname" name="diff_first_name">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="c_diff_lname" class="text-black">Last Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="c_diff_lname" name="diff_last_name">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <label for="c_diff_companyname" class="text-black">Company Name </label>
                                                <input type="text" class="form-control" id="c_diff_companyname" name="diff_company_name">
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <div class="col-md-12">
                                                <label for="c_diff_address" class="text-black">Address <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="c_diff_address" name="diff_address" placeholder="Street address">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <input type="text" class="form-control" name="diff_apartment" placeholder="Apartment, suite, unit etc. (optional)">
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                <label for="c_diff_state_country" class="text-black">State / Country <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="c_diff_state_country" name="diff_state_country">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="c_diff_postal_zip" class="text-black">Posta / Zip <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="c_diff_postal_zip" name="diff_postal_zip">
                                            </div>
                                        </div>

                                        <div class="form-group row mb-5">
                                            <div class="col-md-6">
                                                <label for="c_diff_email_address" class="text-black">Email Address <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="c_diff_email_address" name="diff_email">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="c_diff_phone" class="text-black">Phone <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="c_diff_phone" name="diff_phone" placeholder="Phone Number">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="c_order_notes" class="text-black">Order Notes</label>
                                <textarea name="order_notes" id="c_order_notes" cols="30" rows="5" class="form-control" placeholder="Write your notes here..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="row mb-5">
                            <div class="col-md-12">
                                <h2 class="h3 mb-3 text-black">Coupon Code</h2>
                                <div class="p-3 p-lg-5 border bg-white">
                                    <label for="c_code" class="text-black mb-3">Enter your coupon code if you have one</label>
                                    <div class="input-group w-75 couponcode-wrap">
                                        <input type="text" class="form-control me-2" id="c_code" name="coupon_code" placeholder="Coupon Code" aria-label="Coupon Code" aria-describedby="button-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-black btn-sm" type="button" id="button-addon2">Apply</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-md-12">
                                <h2 class="h3 mb-3 text-black">Your Order</h2>
                                <div class="p-3 p-lg-5 border bg-white">
                                    <table class="table site-block-order-table mb-5">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cartItems ?? [
                                                (object)['name' => 'Top Up T-Shirt', 'quantity' => 1, 'price' => '250.00'],
                                                (object)['name' => 'Polo Shirt', 'quantity' => 1, 'price' => '100.00']
                                            ] as $item)
                                            <tr>
                                                <td>{{ $item->name }} <strong class="mx-2">x</strong> {{ $item->quantity }}</td>
                                                <td>${{ $item->price }}</td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td class="text-black font-weight-bold"><strong>Cart Subtotal</strong></td>
                                                <td class="text-black">${{ $subtotal ?? '350.00' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-black font-weight-bold"><strong>Order Total</strong></td>
                                                <td class="text-black font-weight-bold"><strong>${{ $total ?? '350.00' }}</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="border p-3 mb-3">
                                        <h3 class="h6 mb-0"><a class="d-block" data-bs-toggle="collapse" href="#collapsebank" role="button" aria-expanded="false" aria-controls="collapsebank">Direct Bank Transfer</a></h3>
                                        <div class="collapse" id="collapsebank">
                                            <div class="py-2">
                                                <p class="mb-0">Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order won’t be shipped until the funds have cleared in our account.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border p-3 mb-3">
                                        <h3 class="h6 mb-0"><a class="d-block" data-bs-toggle="collapse" href="#collapsecheque" role="button" aria-expanded="false" aria-controls="collapsecheque">Cheque Payment</a></h3>
                                        <div class="collapse" id="collapsecheque">
                                            <div class="py-2">
                                                <p class="mb-0">Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order won’t be shipped until the funds have cleared in our account.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border p-3 mb-5">
                                        <h3 class="h6 mb-0"><a class="d-block" data-bs-toggle="collapse" href="#collapsepaypal" role="button" aria-expanded="false" aria-controls="collapsepaypal">Paypal</a></h3>
                                        <div class="collapse" id="collapsepaypal">
                                            <div class="py-2">
                                                <p class="mb-0">Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order won’t be shipped until the funds have cleared in our account.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-black btn-lg py-3 btn-block">Place Order</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection