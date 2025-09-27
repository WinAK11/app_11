<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Mail\OrderPlacedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }

    public function add_to_cart(Request $request)
    {
        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');
        return redirect()->back();
    }

    public function increase_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $quantity = $product->qty+1;
        Cart::instance('cart')->update($rowId, $quantity);
        return redirect()->back();
    }
    
    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $quantity = $product->qty-1;
        Cart::instance('cart')->update($rowId, $quantity);
        return redirect()->back();
    }

    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function clear_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function apply_coupon(Request $request)
    {
        $coupon_code = $request->coupon_code;
        if (isset($coupon_code))
        {
            $coupon = Coupon::where('code', $coupon_code)->where('expired_on', '>=', Carbon::today())
            ->where('cart_value', '<=', (int)ceil(str_replace(',', '', Cart::instance('cart')->subtotal())))->first();
            if (!$coupon)
            {
                return redirect()->back()->with('error', 'Invalid coupon code!');
            }
            else
            {
                Session::put('coupon', [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'cart_value' => $coupon->cart_value
                ]);
                $this->calculate_discount();
                return redirect()->back()->with('success', 'Coupon has been applied!');
            }
        }
        else
        {
            return redirect()->back()->with('error', 'Invalid coupon code!');
        }
    }

    public function calculate_discount()
    {
        $discount=0;
        if (Session::has('coupon'))
        {
            if (Session::get('coupon')['type']=='fixed')
            {
                $discount = Session::get('coupon')['value'];
            }
            else
            {
                $discount = ((int)ceil(str_replace(',', '', Cart::instance('cart')->subtotal()))*Session::get('coupon')['value'])/100;
            }
            $subtotalAfterDiscount = (int)ceil(str_replace(',', '', Cart::instance('cart')->subtotal())) - $discount;
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax'))/100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;
            Session::put('discounts', [
                'discount'=>number_format((int)ceil($discount), 2, '.', ''),
                'subtotal'=>number_format((int)ceil($subtotalAfterDiscount), 2, '.', ''),
                'tax'=>number_format((int)ceil($taxAfterDiscount), 2, '.', ''),
                'total'=>number_format((int)ceil($totalAfterDiscount), 2, '.', '')
            ]);
        }
    }

    public function remove_coupon()
    {
        Session::forget('coupon');
        Session::forget('discounts');
        return back()->with('success', 'Coupon has been removed!');
    }

    public function checkout()
    {
        if(!Auth::check())
        {
            return redirect()->route('login');
        }
        $address = Address::where('user_id', Auth:: user()->id) ->where('isdefault', 1)->first();

        return view('checkout', compact('address'));
    }

    public function place_order (Request $request)
    {
        $user_id = Auth:: user()->id;
        $address = Address::where('user_id', $user_id) ->where('isdefault', true)->first();
        if(!$address)
        {
            $request->validate([
                'name' => 'required | max: 100',
                'phone' => 'required | numeric | digits:10',
                'shipping_cost' => 'required | numeric',
                'province' => 'required',
                'city' => 'required',
                'district' => 'required',
                'address' => 'required',
                // 'locality' => 'required',
            ]);

            $address = new Address();
            $address->name = $request->name;
            $address->phone = $request->phone;
            // $address->zip = $request->zip;
            $address->shipping_cost = $request->shipping_cost;
            $address->province = $request->province;
            $address->city = $request->city;
            $address->district = $request->district;
            $address->address = $request->address;
            // $address->locality = $request->locality;
            $address->country = 'Vietnam';
            $address->user_id = $user_id;
            $address->isdefault = true;
        }

        $this->setAmountforCheckout($request->shipping_cost); // Pass the shipping cost from the request

        $order = new Order();
        $order->user_id = $user_id;
        $order->subtotal = (int)ceil(str_replace(',', '', Session::get('checkout')['subtotal']));
        $order->discount = (int)ceil(str_replace(',', '', Session::get('checkout')['discount']));
        $order->tax = (int)ceil(str_replace(',', '', Session::get('checkout')['tax']));
        $order->shipping_cost = (int)ceil(str_replace(',', '', Session::get('checkout')['shipping_cost']));
        $order->total = (int)ceil(str_replace(',', '', Session::get('checkout')['total']));
        $order->name = $address->name;
        $order->phone = $address->phone;
        // $order->locality = $address->locality;
        $order->address = $address->address;
        $order->province = $address->province;
        $order->city = $address->city;
        $order->district = $address->district;
        $order->country = $address->country;
        // $order->zip = $address->zip;
        $order->save();

        foreach(Cart::instance('cart')->content() as $item)
        {
            $orderItem = new OrderItem();
            $orderItem->product_id = $item->id;
            $orderItem->order_id = $order->id;
            $orderItem->price = $item->price;
            $orderItem->quantity = $item->qty;
            $orderItem->save();

            // Decrease product quantity
            $product = \App\Models\Product::find($item->id);
            $product->quantity -= $item->qty;
            $product->save();
        }

        // Prepare order data for the email
        $orderData = [
            'name' => $request->name ?? $address->name,
            'address' => $request->address ?? $address->address,
            'province' => $request->province ?? $address->province,
            'city' => $request->city ?? $address->city,
            'district' => $request->district ?? $address->district,
            'country' => $request->country ?? $address->country,
            // 'zip' => $request->zip ?? $address->zip,
            'total' => Cart::instance('cart')->total(),
        ];

        $cart = Cart::instance('cart')->content();

        // Send email
        Mail::to(Auth::user()->email)->send(new OrderPlacedMail($orderData, $cart));

        if ($request->mode == "momo")
        {
            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->order_id = $order->id;
            $transaction->mode = $request->mode;
            $transaction->status = "pending";
            $transaction->save();
            return $this->momo_payment($request, $order);
        }
        // elseif ($request->mode == "paypal")
        // {
        //     $transaction = new Transaction();
        //     $transaction->user_id = $user_id;
        //     $transaction->order_id = $order->id;
        //     $transaction->mode = $request->mode;
        //     $transaction->status = "pending";
        //     $transaction->save();
        //     Cart::instance('cart')->destroy();
        //     Session::forget('checkout');
        //     Session::forget('coupon');
        //     Session::forget('discounts');
        //     Session::put('order_id', $order->id);
        //     return redirect()->route('cart.order.confirm');
        // }
        elseif ($request->mode == "cod")
        {
            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->order_id = $order->id;
            $transaction->mode = $request->mode;
            $transaction->status = "pending";
            $transaction->save();
            Cart::instance('cart')->destroy();
            Session::forget('checkout');
            Session::forget('coupon');
            Session::forget('discounts');
            Session::put('order_id', $order->id);
            return redirect()->route('cart.order.confirm');
        }
    }

    public function setAmountforCheckout($shippingCostFromRequest = null) // Accept shipping cost as a parameter
    {
        if(!Cart::instance('cart')->content()->count() > 0)
        {
            Session::forget('checkout');
            return;
        }

        // Ensure shippingCost is a numeric value, default to 0 if not provided or invalid
        $shippingCost = is_numeric($shippingCostFromRequest) ? (float) $shippingCostFromRequest : 0;

        if(Session::has('coupon'))
        {
            $discount = (int)ceil(str_replace(',', '', Session::get('discounts')['discount']));
            $subtotalAfterDiscount = (int)ceil(str_replace(',', '', Session::get('discounts')['subtotal']));
            // Recalculate tax based on subtotal after discount, as 'discounts' session might not have it explicitly
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax'))/100;
            $totalAfterDiscountAndTax = $subtotalAfterDiscount + $taxAfterDiscount;

            Session::put('checkout',[
                'discount' => number_format($discount, 2, '.', ''),
                'subtotal' => number_format($subtotalAfterDiscount, 2, '.', ''),
                'shipping_cost' => number_format($shippingCost, 2, '.', ''),
                'tax' => number_format($taxAfterDiscount, 2, '.', ''),
                'total' => number_format($totalAfterDiscountAndTax + $shippingCost, 2, '.', ''),
            ]);
        }
        else
        {
            $subtotal = (int)ceil(str_replace(',', '', Cart::instance('cart')->subtotal()));
            $tax = (int)ceil(str_replace(',', '', Cart::instance('cart')->tax()));
            $total = $subtotal + $tax;

            Session::put('checkout',[
                'discount' => number_format(0, 2, '.', ''),
                'subtotal' => number_format($subtotal, 2, '.', ''),
                'shipping_cost' => number_format($shippingCost, 2, '.', ''),
                'tax' => number_format($tax, 2, '.', ''),
                'total' => number_format($total + $shippingCost, 2, '.', ''),
            ]);
        }
    }

    public function order_confirm()
    {
        if (Session::has('order_id'))
        {
            $order = Order::find (Session::get('order_id'));
            return view('order-confirm', compact('order'));
        }
        return redirect()->route('cart.index');
    }

    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }

     public function momo_payment(Request $request, Order $order)
    {

        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderInfo = "Thanh toán qua ATM MoMo";
        $amount = (string) Cart::instance('cart')->total(0, '', '');
        // dd($amount);
        $orderId = time() . "";
        $redirectUrl = url('/order-confirm');
        $ipnUrl = url('/order-confirm');
        $extraData = "";

        $requestId = time() . "";
        $requestType = "payWithATM";
        // $extraData = ($_POST["extraData"] ? $_POST["extraData"] : "");
        //before sign HMAC SHA256 signature
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        $data = array(
        'partnerCode' => $partnerCode,
        'partnerName' => "Test",
        "storeId" => "MomoTestStore",
        'requestId' => $requestId,
        'amount' => $amount,
        'orderId' => $orderId,
        'orderInfo' => $orderInfo,
        'redirectUrl' => $redirectUrl,
        'ipnUrl' => $ipnUrl,
        'lang' => 'vi',
        'extraData' => $extraData,
        'requestType' => $requestType,
        'signature' => $signature
        );
        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);  // decode json
            Cart::instance('cart')->destroy();
            Session::forget('checkout');
            Session::forget('coupon');
            Session::forget('discounts');
            Session::put('order_id', $order->id);
        //Just a example, please check more in there
        return redirect()->to($jsonResult['payUrl']);

        // header('Location: ' . $jsonResult['payUrl']);
    }
}
