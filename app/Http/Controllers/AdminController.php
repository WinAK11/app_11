<?php

namespace App\Http\Controllers;

use App\Mail\DeliveryConfirmationMail;
use App\Models\Author;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Slide;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller {
    public function index() {
        // Revenue comparison logic
        $now = Carbon::now();

        // This Week vs Last Week
        $thisWeekRevenue = Order::where('status', 'delivered')
            ->whereBetween('delivered_date', [$now->startOfWeek()->format('Y-m-d'), $now->endOfWeek()->format('Y-m-d')])
            ->sum('total');
        $lastWeekRevenue = Order::where('status', 'delivered')
            ->whereBetween('delivered_date', [$now->copy()->subWeek()->startOfWeek()->format('Y-m-d'), $now->copy()->subWeek()->endOfWeek()->format('Y-m-d')])
            ->sum('total');
        $weekDiff = $thisWeekRevenue - $lastWeekRevenue;
        $weekPercentageChange = $lastWeekRevenue > 0 ? ($weekDiff / $lastWeekRevenue) * 100 : ($thisWeekRevenue > 0 ? 100 : 0);

        // This Month vs Last Month
        $thisMonthRevenue = Order::where('status', 'delivered')
            ->whereBetween('delivered_date', [$now->startOfMonth()->format('Y-m-d'), $now->endOfMonth()->format('Y-m-d')])
            ->sum('total');
        $lastMonthRevenue = Order::where('status', 'delivered')
            ->whereBetween('delivered_date', [$now->copy()->subMonth()->startOfMonth()->format('Y-m-d'), $now->copy()->subMonth()->endOfMonth()->format('Y-m-d')])
            ->sum('total');
        $monthDiff = $thisMonthRevenue - $lastMonthRevenue;
        $monthPercentageChange = $lastMonthRevenue > 0 ? ($monthDiff / $lastMonthRevenue) * 100 : ($thisMonthRevenue > 0 ? 100 : 0);

        // This Quarter vs Last Quarter
        $thisQuarterRevenue = Order::where('status', 'delivered')
            ->whereBetween('delivered_date', [$now->startOfQuarter()->format('Y-m-d'), $now->endOfQuarter()->format('Y-m-d')])
            ->sum('total');
        $lastQuarterRevenue = Order::where('status', 'delivered')
            ->whereBetween('delivered_date', [$now->copy()->subQuarter()->startOfQuarter()->format('Y-m-d'), $now->copy()->subQuarter()->endOfQuarter()->format('Y-m-d')])
            ->sum('total');
        $quarterDiff = $thisQuarterRevenue - $lastQuarterRevenue;
        $quarterPercentageChange = $lastQuarterRevenue > 0 ? ($quarterDiff / $lastQuarterRevenue) * 100 : ($thisQuarterRevenue > 0 ? 100 : 0);

        $revenueComparison = [
            'week' => ['current' => $thisWeekRevenue, 'previous' => $lastWeekRevenue, 'percentage' => $weekPercentageChange],
            'month' => ['current' => $thisMonthRevenue, 'previous' => $lastMonthRevenue, 'percentage' => $monthPercentageChange],
            'quarter' => ['current' => $thisQuarterRevenue, 'previous' => $lastQuarterRevenue, 'percentage' => $quarterPercentageChange],
        ];

        $allProductsSales = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', 'delivered')
            ->whereMonth('orders.delivered_date', $now->month)
            ->whereYear('orders.delivered_date', $now->year)
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('products.name')
            ->orderBy('total_quantity', 'desc');

        $topProducts = $allProductsSales->get();
        $totalProductQuantity = $topProducts->sum('total_quantity');

        $topProductLabels = [];
        $topProductData = [];
        $topProductQty = [];
        foreach ($topProducts->take(4) as $item) {
            $topProductLabels[] = $item->name;
            $topProductData[] = $totalProductQuantity > 0 ? round($item->total_quantity / $totalProductQuantity * 100, 2) : 0;
            $topProductQty[] = $item->total_quantity;
        }
        if ($topProducts->count() > 4) {
            $otherQuantity = $topProducts->slice(4)->sum('total_quantity');
            $topProductLabels[] = 'Khác';
            $topProductData[] = $totalProductQuantity > 0 ? round($otherQuantity / $totalProductQuantity * 100, 2) : 0;
            $topProductQty[] = $otherQuantity;
        }

        $allCategoriesSales = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', 'delivered')
            ->whereMonth('orders.delivered_date', $now->month)
            ->whereYear('orders.delivered_date', $now->year)
            ->select('categories.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('categories.name')
            ->orderBy('total_quantity', 'desc');

        $topCategories = $allCategoriesSales->get();
        $totalCategoryQuantity = $topCategories->sum('total_quantity');

        $topCategoryLabels = [];
        $topCategoryData = [];
        $topCategoryQty = [];
        foreach ($topCategories->take(4) as $item) {
            $topCategoryLabels[] = $item->name;
            $topCategoryData[] = $totalCategoryQuantity > 0 ? round($item->total_quantity / $totalCategoryQuantity * 100, 2) : 0;
            $topCategoryQty[] = $item->total_quantity;
        }
        if ($topCategories->count() > 4) {
            $otherQuantity = $topCategories->slice(4)->sum('total_quantity');
            $topCategoryLabels[] = 'Khác';
            $topCategoryData[] = $totalCategoryQuantity > 0 ? round($otherQuantity / $totalCategoryQuantity * 100, 2) : 0;
            $topCategoryQty[] = $otherQuantity;
        }

        $orders = Order::orderBy( 'created_at', 'DESC' )->get()->take( 10 );
        $dashboardDatas = DB::select( "Select sum(total) AS TotalAmount,
                                    sum(if(status='ordered', total, 0)) as TotalOrderedAmount,
                                    sum(if(status='delivered', total, 0)) as TotalDeliveredAmount, 
                                    sum(if(status='canceled', total, 0)) as TotalCanceledAmount,
                                    sum(if(status!='canceled', 1, 0)) as Total,
                                    sum(if(status='ordered', 1, 0)) as TotalOrdered, 
                                    sum(if(status='delivered', 1, 0)) as TotalDelivered,
                                    sum(if(status='canceled', 1, 0)) as TotalCanceled
                                    From Orders
        " );

$monthlyDatas = DB::select("
    SELECT
        M.id AS MonthNo,
        M.name AS MonthName,
        IFNULL(D.TotalAmount, 0) AS TotalAmount, 
        IFNULL(D.TotalOrderedAmount, 0) AS TotalOrderedAmount,
        IFNULL(D.TotalDeliveredAmount, 0) AS TotalDeliveredAmount,
        IFNULL(D.TotalCanceledAmount, 0) AS TotalCanceledAmount,
        IFNULL(D.TotalOrders, 0) AS TotalOrders, 
        IFNULL(D.TotalOrdered, 0) AS TotalOrdered,
        IFNULL(D.TotalDelivered, 0) AS TotalDelivered,
        IFNULL(D.TotalCanceled, 0) AS TotalCanceled
    FROM month_names M
    LEFT JOIN (
        SELECT
            MONTH(created_at) AS MonthNo, 
            SUM(IF(status != 'canceled', total, 0)) AS TotalAmount,
            SUM(IF(status = 'ordered', total, 0)) AS TotalOrderedAmount,
            SUM(IF(status = 'delivered', total, 0)) AS TotalDeliveredAmount,
            SUM(IF(status = 'canceled', total, 0)) AS TotalCanceledAmount,
            COUNT(*) AS TotalOrders,
            SUM(IF(status = 'ordered', 1, 0)) AS TotalOrdered,
            SUM(IF(status = 'delivered', 1, 0)) AS TotalDelivered,
            SUM(IF(status = 'canceled', 1, 0)) AS TotalCanceled
        FROM Orders
        WHERE YEAR(created_at) = YEAR(NOW()) 
        GROUP BY MONTH(created_at)
        ORDER BY MONTH(created_at)
    ) D ON D.MonthNo = M.id
");


        $AmountM = implode(', ', collect($monthlyDatas)->pluck('TotalAmount')->toArray());
        $OrderedAmountM = implode(', ', collect($monthlyDatas)->pluck('TotalOrderedAmount')->toArray());
        $DeliveredAmountM = implode(', ', collect($monthlyDatas)->pluck('TotalDeliveredAmount')->toArray());
        $CanceledAmountM = implode(', ', collect($monthlyDatas)->pluck('TotalCanceledAmount')->toArray());

        $TotalAmount = collect($monthlyDatas)->sum('TotalAmount');
        $TotalOrderedAmount = collect($monthlyDatas)->sum('TotalOrderedAmount');
        $TotalDeliveredAmount = collect($monthlyDatas)->sum('TotalDeliveredAmount');
        $TotalCanceledAmount = collect($monthlyDatas)->sum('TotalCanceledAmount');
        return view( 'admin.index', compact('orders', 'dashboardDatas', 'AmountM', 'OrderedAmountM', 'DeliveredAmountM', 'CanceledAmountM', 'TotalAmount', 'TotalOrderedAmount', 'TotalDeliveredAmount', 'TotalCanceledAmount', 'revenueComparison', 'topProductLabels', 'topProductData', 'topProductQty', 'topCategoryLabels', 'topCategoryData', 'topCategoryQty', 'monthlyDatas') );
    }

    public function categories() {
        $categories = Category::orderBy( 'id', 'ASC' )->paginate( 10 );
        return view( 'admin.categories', compact( 'categories' ) );
    }

    public function category_add(Request $request) {
        $suggestedName = $request->query('name', '');
        return view( 'admin.category-add', compact('suggestedName') );
    }

    public function category_store(Request $request) {
        $request->validate([
            'name'  => 'required',
            'slug'  => 'required|unique:categories,slug',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->slug);

        $image = $request->file('image');

        if ($image) {
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;

            $this->GenerateCategoryThumbailsImage($image, $file_name);
            $category->image = $file_name;
        }

        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category has been added successfully.');
    }


    public function GenerateCategoryThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124, 124, 'top');
        $img->resize(124, 124, function ($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function category_edit($id)
    {
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }

    public function category_update(Request $request)
    {
        $request->validate( [
            'name'=> 'required',
            'slug'=> 'required|unique:categories,slug, '.$request->id,
            'image'=> 'mimes:png, jpg, jpeg|max:2048'
        ] );
        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug( $request->name );
        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/categories').'/'.$category->image)){
                File::delete(public_path('uploads/categories').'/'.$category->image);
            }
            $image = $request->file( 'image' );
            $file_extension = $request->file( 'image' )->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateCategoryThumbailsImage( $image, $file_name );
            $category->image = $file_name;
        }
        $category->save();
        return redirect()->route( 'admin.categories' )->with( 'status', 'Category has been updated successfully.' );
    }

    public function category_delete($id)
    {
        $category = Category::find($id);
        if(File::exists(public_path('uploads/categories').'/'.$category->image)){
            File::delete(public_path('uploads/categories').'/'.$category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category has been deleted sucessfully.');
    }

    public function products()
    {
        $products = Product::orderBy('id', 'ASC')->paginate(10);
        return view('admin.products', compact('products'));
    }

    public function product_add()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $authors = Author::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-add', compact('categories', 'authors'));
    }

    public function product_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'nullable|numeric',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'author_id' => 'required'
        ]);
        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        // $product->sale_price = $request->sale_price;
        $product->sale_price = $request->filled('sale_price') ? $request->sale_price : null;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->image = $request->image;
        $product->category_id = $request->category_id;
        $product->author_id = $request->author_id;

        $current_timestamp = Carbon::now()->timestamp;

        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;
        if($request->hasFile('images')){
            $allowedfileExtion = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtion);
                if($gcheck){
                    $gfileName = $current_timestamp . "-" . $counter . "." . $gextension;
                    $this->GenerateProductThumbnailImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(', ', $gallery_arr);
        }
        $product->images = $gallery_images;
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been added successfully.');
    }

    public function GenerateProductThumbnailImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/products');
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');

        // Create directories if they do not exist
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        if (!File::exists($destinationPathThumbnail)) {
            File::makeDirectory($destinationPathThumbnail, 0755, true);
        }

        $img = Image::read($image->path());

        // Resize to main image dimensions
        $img->cover(540, 689, 'top');
        $img->resize(540, 689, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);

        // Resize to thumbnail dimensions
        $img->resize(104, 104, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPathThumbnail.'/'.$imageName);
    }


    public function product_edit($id)
    {
        $product = Product::find($id);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $authors = Author::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-edit', compact('product', 'categories', 'authors'));
    }

    public function product_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,' .$request->id,
            'short_description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'nullable|numeric',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'author_id' => 'required'
        ]);
        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->regular_price = $request->regular_price;
        // $product->sale_price = $request->sale_price;
        $product->sale_price = $request->filled('sale_price') ? $request->sale_price : null;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->author_id = $request->author_id;
        $current_timestamp = Carbon::now()->timestamp;

        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/products').'/'.$product->image)){
                File::delete(public_path('uploads/products').'/'.$product->image);
            }
            if(File::exists(public_path('uploads/products/thumbnails').'/'.$product->image)){
                File::delete(public_path('uploads/products/thumbnails').'/'.$product->image);
            }
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;
        if($request->hasFile('images')){
            foreach (explode(', ', $product->images) as $ofile) {
                if(File::exists(public_path('uploads/products').'/'.$ofile)){
                    File::delete(public_path('uploads/products').'/'.$ofile);
                }
                if(File::exists(public_path('uploads/products/thumbnails').'/'.$ofile)){
                    File::delete(public_path('uploads/products/thumbnails').'/'.$ofile);
                }
            }
            $allowedfileExtion = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            // Log::info('Request::UploadMultipleImage' . $request); // Debug line
            Log::info($request->file('images')); // Debug line
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtion);
                if($gcheck){
                    $gfileName = $current_timestamp . "-" . $counter . "." . $gextension;
                    $this->GenerateProductThumbnailImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(', ', $gallery_arr);
            $product->images = $gallery_images;
        }
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been updated successfully.');
    }

    public function product_delete($id)
    {
        $product = Product::find($id);
        if(File::exists(public_path('uploads/products').'/'.$product->image)){
            File::delete(public_path('uploads/products').'/'.$product->image);
        }
        if(File::exists(public_path('uploads/products/thumbnails').'/'.$product->image)){
            File::delete(public_path('uploads/products/thumbnails').'/'.$product->image);
        }

        foreach (explode(', ', $product->images) as $ofile) {
            if(File::exists(public_path('uploads/products').'/'.$ofile)){
                File::delete(public_path('uploads/products').'/'.$ofile);
            }
            if(File::exists(public_path('uploads/products/thumbnails').'/'.$ofile)){
                File::delete(public_path('uploads/products/thumbnails').'/'.$ofile);
            }
        }

        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product has been deleted successfully.');
    }

    public function authors()
    {
        $authors = Author::orderBy('id', 'ASC')->paginate(10);
        return view('admin.authors', compact('authors'));
    }

    public function add_author()
    {
        return view('admin.author-add');
    }

    public function author_store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'slug'=>'required|unique:authors,slug',
            'nationality',
            'biography',
            'image'=>'mimes:png,jpg,jpeg|max:2048'
        ]);
        $author = new Author();
        $author->name=$request->name;
        $author->slug=Str::slug($request->name);
        $author->nationality = $request->nationality;
        $author->biography = $request->biography;
        $image = $request->file('image');
        if($image){
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateAuthorThumbnailsImage($image, $file_name);
            $author->image = $file_name;
        }
        $author->save();
        return redirect()->route('admin.authors')->with('status', 'Author has been add successfully!');
    }

    public function author_edit($id)
    {
        $author = Author::find($id);
        return view('admin.author-edit', compact('author'));
    }

    public function author_update(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'slug'=>'required|unique:authors,slug,'.$request->id,
            'nationality',
            'biography',
            'image'=>'mimes:png,jpg,jpeg|max:2048'
        ]);
        $author=Author::find($request->id);
        $author->name=$request->name;
        $author->slug=Str::slug($request->name);

        if ($request->hasFile('image'))
        {
            if (File::exists(public_path('uploads/authors').'/'.$author->image))
            {
                File::delete(public_path('uploads/authors').'/'.$author->image);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateAuthorThumbnailsImage($image, $file_name);
            $author->image = $file_name;
        }
        if ($author->save()) {
            return redirect()->route('admin.authors')->with('status', 'Author has been updated successfully!');
        } else {
            return redirect()->route('admin.authors')->with('error', 'Failed to update author!');
        }
    }

    public function GenerateAuthorThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/authors');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function($constraint)
        {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function author_delete($id)
    {
        $author=Author::find($id);
        if (File::exists(public_path('uploads/authors').'/'.$author->image))
        {
            File::delete(public_path('uploads/authors').'/'.$author->image);
        }
        $author->delete();
        return redirect()->route('admin.authors')->with('status', "Author has been deleted successfully!");
    }

    public function settings()
    {
        $user = Auth::user();
        return view('admin.settings', compact('user'));
    }

    public function account_update( Request $request ) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validate general info
        $request->validate( [
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ] );

        // Update general info
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->email = $request->email;

        // Check if user wants to update password
        if ( $request->filled( 'old_password' ) || $request->filled( 'new_password' ) || $request->filled( 'confirm_password' ) ) {
            $request->validate( [
                'old_password' => 'required',
                'new_password' => 'required|min:8|confirmed', // matches confirm_password
            ] );

            if ( !Hash::check( $request->old_password, $user->password ) ) {
                return back()->withErrors( [ 'old_password' => 'Old password is incorrect.' ] );
            }

            $user->password = Hash::make( $request->new_password );
        }

        $user->save();

        return back()->with( 'success', 'Account updated successfully.' );
    }

    public function coupons()
    {
        $coupons = Coupon::orderBy('id', 'ASC')->paginate(12);
        return view('admin.coupons', compact('coupons'));
    }

    public function coupon_add()
    {
        return view('admin.coupon-add');
    }

    public function coupon_store(Request $request)
    {
        $request->validate([
            'code'=>'required',
            'type'=>'required',
            'value'=>'required|numeric',
            'cart_value'=>'required|numeric',
            'expired_on'=>'required|date'
        ]);
        $coupon = new Coupon();
        $coupon->code=$request->code;
        $coupon->type=$request->type;
        $coupon->value=$request->value;
        $coupon->cart_value=$request->cart_value;
        $coupon->expired_on=$request->expired_on;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status', 'Coupon has been added successfully!');
    }

    public function coupon_edit($id)
    {
        $coupon = Coupon::find($id);
        return view('admin.coupon-edit', compact('coupon'));
    }

    public function coupon_update(Request $request)
    {
        $request->validate([
            'code'=>'required',
            'type'=>'required',
            'value'=>'required|numeric',
            'cart_value'=>'required|numeric',
            'expired_on'=>'required|date'
        ]);
        $coupon = Coupon::find($request->id);
        $coupon->code=$request->code;
        $coupon->type=$request->type;
        $coupon->value=$request->value;
        $coupon->cart_value=$request->cart_value;
        $coupon->expired_on=$request->expired_on;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status', 'Coupon has been updated successfully!');
    }

    public function coupon_delete($id)
    {
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('status', 'Coupon has been deleted successfully!');
    }


    public function orders()
    {
        $orders = Order::orderBy('created_at', 'DESC')->paginate(12);
        return view('admin.orders', compact('orders'));
    }

    public function order_details($order_id)
    {
        $order = Order::find($order_id);
        $orderItems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id', $order_id)->first();

        return view('admin.order-details', compact('order', 'orderItems', 'transaction'));
    }

    public function update_order_status(Request $request)
    {
       $order = Order::find($request->order_id);
       $order->status = $request->order_status;
       if($request->order_status == 'delivered'){
        $order->delivered_date = Carbon::now();
        // Send delivery confirmation email
        Mail::to($order->user->email)->send(new DeliveryConfirmationMail($order));
       }
       else if($request->order_status == 'canceled'){
        $order->canceled_date = Carbon::now();
       }
       $order->save();

       if($request->order_status == 'delivered'){
        $transaction = Transaction::where('order_id', $request->order_id)->first();
        $transaction->status = 'approved';
        $transaction->save();
       }

       return back()->with('status', 'Status changed successfully.');
    }

    public function search( Request $request ) {
        $query = $request->input( 'query' );
        $result = Product::where( 'name', 'LIKE', "%{$query}%" )->get()->take( 8 );
        return response()->json( $result );
    }

    public function slides()
    {
        $slides = Slide::orderBy('id', 'DESC')->paginate(12);
        return view('admin.slides', compact('slides'));
    }

    public function slide_add()
    {
        return view('admin.slide-add');
    }

    public function slide_store(Request $request)
    {
        $request->validate([
            'tagline'=>'required',
            'title'=>'required',
            'subtitle'=>'required',
            'link'=>'required',
            'status'=>'required',
            'image'=>'required|mimes:png,jpg,jpeg|max:2038',
        ]);
        $slide = new Slide();
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        $image = $request->file('image');
        $imageName = Carbon::now()->timestamp . '.' . $image->extension();
        $this->GenerateSlideThumbnailImage($image, $imageName);
        $slide->image = $imageName;
        $slide->save();
        return redirect()->route('admin.slides')->with('status', 'Slide has been added successfully.');
    }

    public function GenerateSlideThumbnailImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/slides');
        $img = Image::read($image->path());
        $img->cover(400, 690, "top");
        $img->resize(400, 690, function($constraint)
        {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function slide_edit($id)
    {
        $slide = Slide::find($id);
        return view('admin.slide-edit', compact('slide'));
    }

    public function slide_update(Request $request)
    {
        $request->validate([
            'tagline'=>'required',
            'title'=>'required',
            'subtitle'=>'required',
            'link'=>'required',
            'status'=>'required',
            'image'=>'mimes:png,jpg,jpeg|max:2038',
        ]);
        $slide = Slide::find($request->id);
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        if ($request->hasFile('image'))
        {
            if (File::exists(public_path('uploads/slides').'/'.$slide->image))
            {
                File::delete(public_path('uploads/slides').'/'.$slide->image);
            }
            $image = $request->file('image');
            $imageName = Carbon::now()->timestamp . '.' . $image->extension();
            $this->GenerateSlideThumbnailImage($image, $imageName);
            $slide->image = $imageName;
        }
        $slide->save();
        return redirect()->route('admin.slides')->with('status', 'Slide has been updated successfully.');
    }

    public function slide_delete($id)
    {
        $slide = Slide::find($id);
        if (File::exists(public_path('uploads/slides').'/'.$slide->image))
        {
            File::delete(public_path('uploads/slides').'/'.$slide->image);
        }
        $slide->delete();
        return redirect()->route('admin.slides')->with("status", "Slide has been deleted successfully.");
    }

    public function getWeeklyRevenueForMonth(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer'
        ]);

        $month = $request->input('month');
        $year = $request->input('year');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $weeklyTotal = [];
        $weeklyPending = [];
        $weeklyDelivered = [];
        $weeklyCanceled = [];
        $weekLabels = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $startOfWeek = $currentDate->copy()->startOfWeek();
            $endOfWeek = $currentDate->copy()->endOfWeek();

            // Lấy dữ liệu cho tuần hiện tại
            $weeklyRevenue = Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->selectRaw("
                    SUM(total) as total_amount,
                    SUM(IF(status = 'ordered', total, 0)) as pending_amount,
                    SUM(IF(status = 'delivered', total, 0)) as delivered_amount,
                    SUM(IF(status = 'canceled', total, 0)) as canceled_amount
                ")
                ->first();

            $weekLabels[] = "Tuần " . $currentDate->weekOfMonth . " (" . $startOfWeek->format('d/m') . " - " . $endOfWeek->format('d/m') . ")";
            $weeklyTotal[] = $weeklyRevenue->total_amount ?? 0;
            $weeklyPending[] = $weeklyRevenue->pending_amount ?? 0;
            $weeklyDelivered[] = $weeklyRevenue->delivered_amount ?? 0;
            $weeklyCanceled[] = $weeklyRevenue->canceled_amount ?? 0;

            $currentDate->addWeek();
        }

        return response()->json([
            'labels' => $weekLabels,
            'total' => $weeklyTotal,
            'pending' => $weeklyPending,
            'delivered' => $weeklyDelivered,
            'canceled' => $weeklyCanceled,
        ]);
    }
}
