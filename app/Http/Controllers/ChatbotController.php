<?php

// app/Http/Controllers/ChatbotController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class ChatbotController extends Controller
{
    private function jsonResponse(string $html, ?int $cartCount = null)
    {
        return response()->json([
            'html' => $html,
            'cart_count' => $cartCount,
        ]);
    }

    public function handle(Request $request)
    {
        $message = $request->input('message');
        $state = Session::get('chatbot_state', 'initial');

        // Handle button clicks with 'action:' prefix
        if (str_starts_with($message, 'action:')) {
            $action = substr($message, 7);
            switch ($action) {
                case 'start':
                    return $this->showInitialOptions();
                case 'search':
                    Session::put('chatbot_state', 'awaiting_search_query');
                    return $this->jsonResponse('Tuyệt vời! Bạn muốn tìm sách gì ạ? Vui lòng nhập tên sách.');
                case 'orders':
                    return $this->checkOrderStatus();
                case 'add_to_cart':
                    return $this->initiateAddToCart();
                case 'add_more_yes':
                    Session::put('chatbot_state', 'awaiting_add_to_cart_selection');
                    return $this->promptForProductToAdd("Bạn muốn thêm sản phẩm nào tiếp theo?");
                case 'add_more_no':
                    Session::forget('chatbot_search_results');
                    Session::put('chatbot_state', 'initial');
                    $cartUrl = route('cart.index');
                    $cartLink = "<a href='{$cartUrl}' target='_blank' style='display:inline-block; margin-top:10px; font-weight:bold; color: #ffc107;'>Xem giỏ hàng</a>";
                    $html = "Được rồi. Bạn có thể {$cartLink} của mình." . "<br><br>Bạn cần giúp gì nữa không?" . $this->getInitialOptionsHtml();
                    return $this->jsonResponse($html);
            }
        }

        // Handle product selection for cart with 'add_product_id:' prefix
        if (str_starts_with($message, 'add_product_id:')) {
            if ($state === 'awaiting_add_to_cart_selection') {
                $productId = (int) substr($message, 15);
                return $this->handleAddToCartSelection($productId);
            }
        }

        // Handle text input based on conversation state
        switch ($state) {
            case 'awaiting_search_query':
                return $this->searchProducts($message);
            case 'awaiting_add_to_cart_selection':
                return $this->jsonResponse("Vui lòng chọn một sản phẩm từ danh sách bên trên bằng cách nhấn vào nút 'Thêm' tương ứng.");
            // For any other state where we expect a button click, if the user types text, we restart the flow.
            case 'search_results_displayed':
            case 'awaiting_add_more_to_cart':
            case 'initial':
            default:
                // Handle natural language search intent
                $lowerMessage = mb_strtolower($message);
                $searchKeywords = ['tìm kiếm', 'tìm', 'kiếm', 'search for', 'search'];
                $matchedKeyword = null;

                foreach ($searchKeywords as $keyword) {
                    if (str_starts_with($lowerMessage, $keyword . ' ')) {
                        $matchedKeyword = $keyword;
                        break;
                    }
                }

                if ($matchedKeyword !== null) {
                    $query = trim(preg_replace('/^(sản phẩm|sách|cuốn|quyển|book|for)\s+/i', '', trim(substr($message, strlen($matchedKeyword)))));
                    if (!empty($query)) {
                        return $this->searchProducts($query);
                    }
                }

                return $this->showInitialOptions(); // Fallback to initial options
        }
    }

    private function showInitialOptions()
    {
        Session::put('chatbot_state', 'initial');
        Session::forget('chatbot_search_results');
        $optionsHtml = $this->getInitialOptionsHtml();
        return $this->jsonResponse("Xin chào! Mình có thể giúp gì cho bạn?{$optionsHtml}");
    }

    private function getInitialOptionsHtml()
    {
        $searchBtn = "<button onclick=\"sendChatbotMessage('action:search', this.innerText)\" class='chatbot-button'>Tìm kiếm sản phẩm</button>";
        $ordersBtn = "<button onclick=\"sendChatbotMessage('action:orders', this.innerText)\" class='chatbot-button'>Kiểm tra đơn hàng</button>";

        return "<div class='chatbot-options'>{$searchBtn}{$ordersBtn}</div>";
    }

    private function searchProducts(string $query)
    {
        if (empty($query)) {
            return $this->jsonResponse('Bạn muốn tìm sách gì ạ?');
        }

        $products = Product::all();
        $results = collect();
        $lowerQuery = mb_strtolower($query);

        foreach ($products as $product) {
            $productName = mb_strtolower($product->name);
            similar_text($lowerQuery, $productName, $percent);
            $distance = levenshtein($lowerQuery, $productName);
            if ($percent >= 60 || $distance <= 2 || stripos($productName, $lowerQuery) !== false) {
                $results->push($product);
            }
            if ($results->count() >= 5) break;
        }

        if ($results->isEmpty()) {
            Session::put('chatbot_state', 'initial');
            $message = 'Xin lỗi, mình không tìm thấy sản phẩm nào phù hợp với "' . e($query) . '".';
            return $this->jsonResponse($message . "<br><br>Bạn muốn làm gì tiếp theo không?" . $this->getInitialOptionsHtml());
        }

        Session::put('chatbot_search_results', $results);
        Session::put('chatbot_state', 'search_results_displayed');

        $list = $results->map(function ($p) {
            $productUrl = route('shop.product.details', ['product_slug' => $p->slug]);
            $style = "display: inline-block; background-color: #e9ecef; color: #495057; padding: 8px 15px; margin: 5px; border-radius: 20px; text-decoration: none; border: 1px solid #ced4da; font-weight: 500;";
            return "<a href='{$productUrl}' target='_blank' style='{$style}'>{$p->name}</a>";
        })->implode('');

        $responseMessage = "Mình tìm thấy các sản phẩm sau:<br><div style='padding-top: 10px;'>{$list}</div>";

        $addToCartBtn = "<button onclick=\"sendChatbotMessage('action:add_to_cart', 'Thêm vào giỏ hàng')\" class='chatbot-button'>Thêm vào giỏ hàng</button>";
        $searchBtn = "<button onclick=\"sendChatbotMessage('action:search', 'Tìm sản phẩm khác')\" class='chatbot-button'>Tìm sản phẩm khác</button>";
        $ordersBtn = "<button onclick=\"sendChatbotMessage('action:orders', this.innerText)\" class='chatbot-button'>Kiểm tra đơn hàng</button>";

        $followUp = "<br><br>Bạn muốn làm gì tiếp theo không?<div class='chatbot-options'>{$addToCartBtn}{$searchBtn}{$ordersBtn}</div>";

        return $this->jsonResponse($responseMessage . $followUp);
    }

    private function checkOrderStatus()
    {
        Session::put('chatbot_state', 'initial');
        Session::forget('chatbot_search_results');

        if (!Auth::check()) {
            $loginUrl = route('login');
            $html = "Bạn cần <a href='{$loginUrl}' target='_blank' style='color: #ffc107; text-decoration: underline;'>đăng nhập</a> để kiểm tra đơn hàng." . "<br><br>Bạn muốn làm gì tiếp theo không?" . $this->getInitialOptionsHtml();
            return $this->jsonResponse($html);
        }

        $orders = Order::where('user_id', Auth::id())->orderBy('created_at', 'desc')->take(5)->get();
        $followUp = "<br><br>Bạn muốn làm gì tiếp theo không?" . $this->getInitialOptionsHtml();

        if ($orders->isEmpty()) {
            return $this->jsonResponse('Bạn chưa có đơn hàng nào.' . $followUp);
        }

        $list = $orders->map(function ($order) {
            $orderUrl = route('user.order.details', ['order_id' => $order->id]);
            $status = htmlspecialchars($order->status);
            $date = $order->created_at->format('d/m/Y');
            $style = "display: block; background-color: #f8f9fa; color: #212529; padding: 10px 15px; margin: 5px 0; border-radius: 8px; text-decoration: none; border: 1px solid #dee2e6;";
            return "<a href='{$orderUrl}' target='_blank' style='{$style}'>Đơn hàng #{$order->id} - Ngày: {$date} - Trạng thái: <strong>{$status}</strong></a>";
        })->implode('');

        $allOrdersUrl = route('user.orders');
        $list .= "<br><a href='{$allOrdersUrl}' target='_blank' style='display:inline-block; margin-top:10px; font-weight:bold; color: #ffc107;'>Xem tất cả đơn hàng</a>";
        $responseMessage = "Đây là các đơn hàng gần đây của bạn:<br><div style='padding-top: 10px;'>{$list}</div>";

        return $this->jsonResponse($responseMessage . $followUp);
    }

    private function initiateAddToCart()
    {
        if (!Auth::check()) {
            Session::put('chatbot_state', 'initial');
            $loginUrl = route('login');
            $html = "Bạn cần <a href='{$loginUrl}' target='_blank' style='color: #ffc107; text-decoration: underline;'>đăng nhập</a> để thêm sản phẩm vào giỏ hàng." . "<br><br>Bạn muốn làm gì tiếp theo không?" . $this->getInitialOptionsHtml();
            return $this->jsonResponse($html);
        }

        if (!Session::has('chatbot_search_results') || Session::get('chatbot_search_results')->isEmpty()) {
            Session::put('chatbot_state', 'initial');
            $html = "Đã có lỗi xảy ra hoặc không có sản phẩm nào để thêm. Vui lòng tìm kiếm lại." . $this->getInitialOptionsHtml();
            return $this->jsonResponse($html);
        }

        Session::put('chatbot_state', 'awaiting_add_to_cart_selection');
        return $this->promptForProductToAdd("Tuyệt vời! Bạn muốn thêm sản phẩm nào vào giỏ hàng?");
    }

    private function promptForProductToAdd(string $promptMessage)
    {
        $products = Session::get('chatbot_search_results');

        $list = $products->map(function ($p) {
            $productName = e($p->name);
            $buttonText = "Thêm: {$productName}";
            return "<button onclick=\"sendChatbotMessage('add_product_id:{$p->id}', '{$buttonText}')\" class='chatbot-button'>{$buttonText}</button>";
        })->implode('');

        return $this->jsonResponse("{$promptMessage}<div class='chatbot-options'>{$list}</div>");
    }

    private function handleAddToCartSelection(int $productId)
    {
        $products = Session::get('chatbot_search_results', collect());
        $productToAdd = $products->firstWhere('id', $productId);

        if (!$productToAdd) {
            return $this->jsonResponse("Rất tiếc, sản phẩm này không có trong danh sách tìm kiếm nữa. Vui lòng thử lại.");
        }

        Cart::instance('cart')->add($productToAdd->id, $productToAdd->name, 1, $productToAdd->sale_price ?? $productToAdd->regular_price)->associate('App\Models\Product');
        $newCartCount = Cart::instance('cart')->content()->count();

        $remainingProducts = $products->where('id', '!=', $productId);
        Session::put('chatbot_search_results', $remainingProducts);

        $message = "Đã thêm '<strong>" . e($productToAdd->name) . "</strong>' vào giỏ hàng.";

        if ($remainingProducts->isEmpty()) {
            Session::forget('chatbot_search_results');
            Session::put('chatbot_state', 'initial');
            $html = $message . "<br><br>Tất cả sản phẩm trong danh sách đã được thêm. Bạn cần giúp gì nữa không?" . $this->getInitialOptionsHtml();
            return $this->jsonResponse($html, $newCartCount);
        } else {
            Session::put('chatbot_state', 'awaiting_add_more_to_cart');
            $yesBtn = "<button onclick=\"sendChatbotMessage('action:add_more_yes', 'Có')\" class='chatbot-button'>Có, tất nhiên</button>";
            $cartUrl = route('cart.index');
            $noBtn = "<a href='{$cartUrl}' class='chatbot-button' style='text-decoration: none;'>Xem giỏ hàng</a>";
            $followUp = "<br><br>Bạn có muốn thêm sản phẩm nào khác từ danh sách tìm kiếm không?<div class='chatbot-options'>{$yesBtn}{$noBtn}</div>";
            return $this->jsonResponse($message . $followUp, $newCartCount);
        }
    }
}
