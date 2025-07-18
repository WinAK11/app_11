<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
// use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            ->where('cart_value', '<=', floatval(str_replace(',', '', Cart::instance('cart')->subtotal())))->first();
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
                $discount = (floatval(str_replace(',', '', Cart::instance('cart')->subtotal()))*Session::get('coupon')['value'])/100;
            }
            $subtotalAfterDiscount = floatval(str_replace(',', '', Cart::instance('cart')->subtotal())) - $discount;
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax'))/100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;
            Session::put('discounts', [
                'discount'=>number_format(floatval($discount), 2, '.', ''),
                'subtotal'=>number_format(floatval($subtotalAfterDiscount), 2, '.', ''),
                'tax'=>number_format(floatval($taxAfterDiscount), 2, '.', ''),
                'total'=>number_format(floatval($totalAfterDiscount), 2, '.', '')
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
                'zip' => 'required | numeric | digits:6',
                'state' => 'required',
                'city' => 'required',
                'address' => 'required',
                'locality' => 'required',
            ]);

            $address = new Address();
            $address->name = $request->name;
            $address->phone = $request->phone;
            $address->zip = $request->zip;
            $address->state = $request->state;
            $address->city = $request->city;
            $address->address = $request->address;
            $address->locality = $request->locality;
            $address->country = 'Vietnam';
            $address->user_id = $user_id;
            $address->isdefault = true;
        }

        $this->setAmountforCheckout();

        $order = new Order();
        $order->user_id = $user_id;
        $order->subtotal = floatval(str_replace(',', '', Session::get('checkout')['subtotal']));
        $order->discount = floatval(str_replace(',', '', Session::get('checkout')['discount']));
        $order->tax = floatval(str_replace(',', '', Session::get('checkout')['tax']));
        $order->total = floatval(str_replace(',', '', Session::get('checkout')['total']));
        $order->name = $address->name;
        $order->phone = $address->phone;
        $order->locality = $address->locality;
        $order->address = $address->address;
        $order->city = $address->city;
        $order->state = $address->state;
        $order->country = $address->country;
        $order->zip = $address->zip;
        $order->save();

        foreach(Cart::instance('cart')->content() as $item)
        {
            $orderItem = new OrderItem();
            $orderItem->product_id = $item->id;
            $orderItem->order_id = $order->id;
            $orderItem->price = $item->price;
            $orderItem->quantity = $item->qty;
            $orderItem->save();
        }

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
        elseif ($request->mode == "paypal")
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

    public function setAmountforCheckout()
    {
        if(!Cart::instance('cart')->content()->count() > 0)
        {
            Session::forget('checkout');
            return;
        }
        if(Session::has('coupon'))
        {
            Session::put('checkout',[
            'discount' => Session::get('discounts') ['discount'],
            'subtotal' => Session::get('discounts') ['subtotal'],
            'tax' => Session::get('discounts') ['tax'], 'total' => Session::get('discounts') ['total'],
            ]);
        }
        else
        {
            Session::put('checkout',[
            'discount' => 0,
            'subtotal' => Cart::instance('cart')->subtotal(),
            'tax' => Cart::instance('cart')->tax(),
            'total' => Cart::instance('cart')->total(),
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
