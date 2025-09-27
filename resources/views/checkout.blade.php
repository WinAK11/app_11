@extends('layouts.app')
@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
      <h2 class="page-title">Shipping and Checkout</h2>
      <div class="checkout-steps">
        <a href="{{route('cart.index')}}" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">01</span>
          <span class="checkout-steps__item-title">
            <span>Shopping Bag</span>
            <em>Manage Your Items List</em>
          </span>
        </a>
        <a href="javascript:void(0)" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">02</span>
          <span class="checkout-steps__item-title">
            <span>Shipping and Checkout</span>
            <em>Checkout Your Items List</em>
          </span>
        </a>
        <a href="javascript:void(0)" class="checkout-steps__item">
          <span class="checkout-steps__item-number">03</span>
          <span class="checkout-steps__item-title">
            <span>Confirmation</span>
            <em>Review And Submit Your Order</em>
          </span>
        </a>
      </div>
      <form name="checkout-form" action="{{route('cart.place.order')}}" method="post">
        @csrf
        <input type="hidden" name="shipping_cost" id="shipping_cost_input" value="30000">
        <div class="checkout-form">
          <div class="billing-info__wrapper">
            <div class="row">
              <div class="col-6">
                <h4>SHIPPING DETAILS</h4>
              </div>
              <div class="col-6">
              </div>
            </div>

            @if ($address)
            <div class="row">
                <div class="col-md-12">
                    <div class="my-account__address-list">
                        <div class="my-account__address-list-item">
                            <div class="my-account__address-item__detail">
                                <p>{{$address->name}}</p>
                                <p>{{$address->address}}</p>
                                <p>{{$address->city}}, {{$address->state}}, {{$address->country}}</p>
                                <p>{{$address->zip}}</p>
                                <br/>
                                <p>{{$address->city}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="row mt-5">
              <div class="col-md-6">
                <div class="form-floating my-3">
                  <input type="text" class="form-control" name="name" required="" value="{{old('name')}}">
                  <label for="name">Full Name *</label>
                  @error('name')<span class="text-danger">{{$message}}</span> @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating my-3">
                  <input type="text" class="form-control" name="phone" required="" value="{{old('phone')}}">
                  <label for="phone">Phone Number *</label>
                  @error('phone')<span class="text-danger">{{$message}}</span> @enderror
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-floating my-3">
                  <select class="form-control" name="province" id="province" required>
                    <option value="">Select Province *</option>
                  </select>
                  <label for="province">Province *</label>
                  @error('province')<span class="text-danger">{{$message}}</span> @enderror
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-floating mt-3 mb-3">
                  <select class="form-control" name="city" id="city" required>
                    <option value="">Select Town / City *</option>
                  </select>
                  <label for="city">Town / City *</label>
                  @error('city')<span class="text-danger">{{$message}}</span> @enderror
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-floating my-3">
                  <select class="form-control" name="district" id="district" required>
                    <option value="">Select District *</option>
                  </select>
                  <label for="district">District *</label>
                  @error('district')<span class="text-danger">{{$message}}</span> @enderror
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-floating my-3">
                  <input type="text" class="form-control" name="address" required="" value="{{old('address')}}">
                  <label for="address">Address *</label>
                  @error('address')<span class="text-danger">{{$message}}</span> @enderror
                </div>
              </div>
              {{-- <div class="col-md-6">
                <div class="form-floating my-3">
                  <input type="text" class="form-control" name="locality" required="" value="{{old('locality')}}">
                  <label for="locality">Road Name, Area, Colony *</label>
                  @error('locality')<span class="text-danger">{{$message}}</span> @enderror
                </div>
              </div> --}}
            </div>
            @endif
          </div>
          <div class="checkout__totals-wrapper">
            <div class="sticky-content">
              <div class="checkout__totals">
                <h3>Your Order</h3>
                <table class="checkout-cart-items">
                  <thead>
                    <tr>
                      <th>PRODUCT</th>
                      <th class="text-right">SUBTOTAL</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach (Cart::instance('cart')->content() as $item)
                    <tr>
                      <td>
                        {{$item->name}} x {{$item->qty}}
                      </td>
                      <td class="text-right">
                        {{ number_format((int)ceil(str_replace(',', '', $item->subtotal())), 0, ',', ',') }}đ
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                @if (Session::has('discounts'))
                <table class="checkout-totals">
                    <tbody>
                        <tr>
                            <th>Subtotal</th>
                            <td class="text-right">{{ number_format((int)ceil(str_replace(',', '', Cart::instance('cart')->subtotal())), 0, ',', ',') }}đ</td>
                        </tr>
                        <tr>
                            <th>Discount({{Session::get('coupon')['code']}})</th>
                            <td class="text-right">{{ number_format((int)ceil(str_replace(',', '', Session::get('discounts')['discount'])), 0, ',', ',') }}đ</td>
                        </tr>
                        <tr>
                            <th>Subtotal after Discount</th>
                            <td class="text-right">{{ number_format((int)ceil(str_replace(',', '', Session::get('discounts')['subtotal'])), 0, ',', ',') }}đ</td>
                        </tr>
                        {{-- <tr class="hidden">
                            <th>VAT</th>
                            <td class="text-right">{{ number_format(intval(str_replace(',', '', Session::get('discounts')['tax'])), 0, ',', ',') }}đ</td>
                        </tr> --}}
                        <tr>
                            <th>Total</th>
                            <td class="text-right">{{ number_format((int)ceil(str_replace(',', '', Session::get('discounts')['total'])), 0, ',', ',') }}đ</td>
                        </tr>
                    </tbody>
                </table>
                @else
                <table class="checkout-totals">
                  <tbody>
                    <tr>
                      <th>SUBTOTAL</th>
                      <td class="text-right">{{ number_format((int)ceil(str_replace(',', '', Cart::instance('cart')->subtotal())), 0, ',', ',') }}đ</td>
                    </tr>
                    <tr>
                      <th>SHIPPING</th>
                      <td class="text-right" id="shipping-cost">30,000đ</td>
                    </tr>
                    {{-- <tr>
                      <th>VAT</th>
                      <td class="text-right">{{ number_format(intval(str_replace(',', '', Cart::instance('cart')->tax())), 0, ',', ',') }}đ</td>
                    </tr> --}}
                    <tr>
                      <th>TOTAL</th>
                      <td class="text-right" id="total-cost">{{ number_format((int)ceil(str_replace(',', '', Cart::instance('cart')->total())) + 30000, 0, ',', ',') }}đ</td>
                    </tr>
                  </tbody>
                </table>
                @endif
              </div>
              <div class="checkout__payment-methods">
                <div class="form-check">
                    <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                    id="mode1" value="momo">
                    <label class="form-check-label" for="mode1">
                        MoMo
                    </label>
                </div>
                {{-- <div class="form-check">
                    <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                    id="mode2" value="paypal">
                    <label class="form-check-label" for="mode2">
                        Paypal
                    </label>
                </div> --}}
                <div class="form-check">
                    <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                    id="mode3" value="cod">
                    <label class="form-check-label" for="mode3">
                        Cash on delivery
                    </label>
                </div>
                <div class="policy-text">
                  Your personal data will be used to process your order, support your experience throughout this
                  website, and for other purposes described in our <a href="terms.html" target="_blank">privacy
                    policy</a>.
                </div>
              </div>
              <button class="btn btn-primary btn-checkout">PLACE ORDER</button>
            </div>
          </div>
        </div>
      </form>
    </section>
  </main>
@endsection

<style>
.form-floating > select.form-control {
    padding-top: 1.625rem;
    padding-bottom: 0.625rem;
    height: auto;
    min-height: 3.5rem;
}
.form-floating > label {
    padding-left: 0.75rem;
    padding-top: 0.75rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cartTotal = {{ intval(str_replace(',', '', Cart::instance('cart')->total())) }};
    // Fetch provinces
    fetch('https://provinces.open-api.vn/api/p/')
        .then(res => res.json())
        .then(provinces => {
            const provinceSelect = document.getElementById('province');
            provinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province.code;
                option.text = province.name;
                provinceSelect.appendChild(option);
            });
        });
    
    function updateShippingAndTotal() {
        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');
        const selectedProvinceText = provinceSelect.options[provinceSelect.selectedIndex].text;
        const selectedCityText = citySelect.options[citySelect.selectedIndex].text;

        let shippingCost = 30000; // Default shipping cost

        if (selectedProvinceText === 'Thành phố Hồ Chí Minh') {
            shippingCost = 20000;
            if (selectedCityText === 'Quận 10') {
              shippingCost = 15000;
            }
        }

        document.getElementById('shipping_cost_input').value = shippingCost;
        document.getElementById('shipping-cost').innerText = shippingCost.toLocaleString('vi-VN') + 'đ';
        const newTotal = cartTotal + shippingCost;
        document.getElementById('total-cost').innerText = newTotal.toLocaleString('vi-VN') + 'đ';
    }
    
    document.getElementById('province').addEventListener('change', function() {
        // When province changes, reset shipping to default before new calculation
        const provinceCode = this.value;
        const citySelect = document.getElementById('city');
        citySelect.innerHTML = '<option value="">Select Town / City *</option>';
        document.getElementById('district').innerHTML = '<option value="">Select District *</option>';
        updateShippingAndTotal();
        if (!provinceCode) return;

        // Fetch cities
        fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
            .then(res => res.json())
            .then(data => {
                data.districts.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.code;
                    option.text = city.name;
                    citySelect.appendChild(option);
                });
                updateShippingAndTotal();
            });
    });

    document.getElementById('city').addEventListener('change', function() {
        const cityCode = this.value;
        const districtSelect = document.getElementById('district');
        districtSelect.innerHTML = '<option value="">Select District *</option>';
        updateShippingAndTotal();
        if (!cityCode) return;

        // Fetch districts
        fetch(`https://provinces.open-api.vn/api/d/${cityCode}?depth=2`)
            .then(res => res.json())
            .then(data => {
                data.wards.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.name;
                    option.text = district.name;
                    districtSelect.appendChild(option);
                });
                updateShippingAndTotal();
            });
    });
});
</script>