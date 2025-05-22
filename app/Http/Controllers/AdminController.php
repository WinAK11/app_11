<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller {
    public function index() {
        return view( 'admin.index' );
    }

    public function categories() {
        $categories = Category::orderBy( 'id', 'ASC' )->paginate( 10 );
        return view( 'admin.categories', compact( 'categories' ) );
    }

    public function category_add() {
        return view( 'admin.category-add' );
    }

    public function category_store( Request $request ) {
        $request->validate( [
            'name'=> 'required',
            'slug'=> 'required|unique:categories,slug',
            'image'=> 'mimes:png,jpg,jpeg|max:2048'
        ] );

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug( $request->name );
        $image = $request->file( 'image' );
        $file_extension = $request->file( 'image' )->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GenerateCategoryThumbailsImage( $image, $file_name );
        $category->image = $file_name;
        $category->save();
        return redirect()->route( 'admin.categories' )->with( 'status', 'Category has been added successfully.' );
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
            'slug'=> 'required|unique:categories,slug,'.$request->id,
            'image'=> 'mimes:png,jpg,jpeg|max:2048'
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
            'sale_price',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'author_id' => 'required'
        ]);
        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
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
            $gallery_images = implode(',', $gallery_arr);
        }
        $product->images = $gallery_images;
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been added successfully.');
    }

    public function GenerateProductThumbnailImage($image, $imageName)
    {
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');
        $destinationPath = public_path('uploads/products');
        $img = Image::read($image->path());
        $img->cover(540, 689, 'top');
        $img->resize(540, 689, function ($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);

        $img->resize(104, 104, function ($constraint){
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
            'slug' => 'required|unique:products,slug,'.$request->id,
            'short_description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'nullable',
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
        $product->sale_price = $request->sale_price;
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
            foreach (explode(',', $product->images) as $ofile) {
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
            $gallery_images = implode(',', $gallery_arr);
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

        foreach (explode(',', $product->images) as $ofile) {
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
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GenerateAuthorThumbnailsImage($image, $file_name);
        $author->image = $file_name;
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
}
