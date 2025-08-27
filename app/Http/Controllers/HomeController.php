<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Slide;
use App\Services\OpenAIService;
use App\Services\ProductSearchService;
use App\Services\QdrantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller {
    protected $qdrantService;
    protected $openAIService;

    protected ProductSearchService $productSearchService;

    public function __construct(QdrantService $qdrantService, OpenAIService $openAIService, ProductSearchService $productSearchService)
    {
        $this->qdrantService = $qdrantService;
        $this->openAIService = $openAIService;
        $this->productSearchService = $productSearchService;
        // $this->middleware('auth');
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */

    public function index() {
        // Tối ưu: Cache các truy vấn trên trang chủ để giảm tải cho DB và tăng tốc độ tải trang.
        // Cache được đặt với thời gian khác nhau tùy thuộc vào tần suất thay đổi của dữ liệu.

        $slides = Cache::remember('home_slides', now()->addMinutes(60), function () {
            // Lấy 3 slide mới nhất đang hoạt động
            return Slide::where('status', 1)->latest()->take(3)->get();
        });

        $categories = Cache::remember('home_categories', now()->addMinutes(60), function () {
            return Category::orderBy('name')->get();
        });

        // Cache sản phẩm sale trong 15 phút để giữ sự mới mẻ
        $sale_products = Cache::remember('home_sale_products', now()->addMinutes(15), function () {
            return Product::where('sale_price', '>', 0)->inRandomOrder()->take(8)->get();
        });

        $featured_products = Cache::remember('home_featured_products', now()->addMinutes(60), function () {
            return Product::where('featured', 1)->inRandomOrder()->take(8)->get();
        });

        return view( 'index', compact('slides', 'categories', 'sale_products', 'featured_products') );
    }

    public function aboutus() {
        return view( 'aboutus' );
    }

    public function contact() {
        return view( 'contact' );
    }

    public function shop() {
        return view( 'shop' );
    }

    public function account() {
        return view( 'account' );
    }

    public function accountWishlist() {
        return view( 'account-wishlist' );
    }

    public function accountOrder() {
        return view( 'account-order' );
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
 
        // Bắt đầu tìm kiếm khi người dùng gõ ít nhất 3 ký tự để tối ưu
        if (empty($query) || mb_strlen(trim($query)) < 3) {
            return response()->json([]);
        }
 
        // Sửa lỗi: Trả về danh sách sản phẩm thay vì từ khóa gợi ý.
        // Frontend (file app.blade.php) đang mong đợi một danh sách các đối tượng sản phẩm
        // (với các thuộc tính như slug, image, name) để hiển thị gợi ý.
        // Việc trả về chuỗi ký tự đã gây ra lỗi "undefined".
 
        // Sử dụng ProductSearchService đã được tối ưu (có caching) để lấy sản phẩm.
        $relatedProducts = $this->productSearchService->searchProducts($query, 8); // Lấy 8 sản phẩm gợi ý
 
        // Trả về collection sản phẩm dưới dạng JSON, đúng với định dạng frontend cần.
        return response()->json($relatedProducts);
    }
}
