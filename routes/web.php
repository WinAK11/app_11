<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FreeProductController;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function()
{
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
    Route::get('/account-detail', [UserController::class, 'account_detail'])->name('user.account.detail');
    Route::put('/account/update', [UserController::class, 'account_update'])->name('user.account.update');
    Route::get('/account-orders', [UserController::class, 'orders'])->name('user.orders');
    Route::get('/account-order/{order_id}/details', [UserController::class, 'order_details'])->name('user.order.details');
    Route::put('/account-order/cancel-order', [UserController::class, 'order_cancel'])->name('user.order.cancel');
    
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add_to_cart'])->name('cart.add');
    Route::put('/cart/increase-quantity/{rowId}', [CartController::class, 'increase_cart_quantity'])->name('cart.quantity.increase');
    Route::put('/cart/decrease-quantity/{rowId}', [CartController::class, 'decrease_cart_quantity'])->name('cart.quantity.decrease');
    Route::delete('/cart/remove/{rowId}', [CartController::class, 'remove_item'])->name('cart.item.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear_cart'])->name('cart.clear');
    Route::post('/cart/apply-coupon', [CartController::class, 'apply_coupon'])->name('cart.coupon.apply');
    Route::delete('/cart/remove-coupon', [CartController::class, 'remove_coupon'])->name('cart.coupon.remove');

    Route::post('/wishlist/add', [WishlistController::class, 'add_to_wishlist'])->name('wishlist.add');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::delete('/wishlist/item/remove/{rowId}', [WishlistController::class, 'remove_item'])->name('wishlist.item.remove');
    Route::delete('/wishlist/clear', [WishlistController::class, 'clear_wishlist'])->name('wishlist.clear');
    Route::post('/wishlist/add-to-cart/{rowId}', [WishlistController::class, 'add_to_cart'])->name('wishlist.addtocart');

    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/place-order', [CartController::class, 'place_order'])->name('cart.place.order');
    Route::get('/order-confirm',[CartController::class,'order_confirm'])->name('cart.order.confirm');
    Route::get('/momo-payment',[CartController::class,'momo_payment'])->name('cart.order.momopayment');
});

Route::middleware(['auth', AuthAdmin::class])->group(function()
{
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/publishers', [AdminController::class, 'publishers'])->name('admin.publishers');
    Route::get('/admin/publisher/add', [AdminController::class, 'add_publisher'])->name('admin.publisher.add');
    Route::post('/admin/publisher/store', [AdminController::class, 'publisher_store'])->name('admin.publisher.store');
    Route::get('/admin/publisher/edit/{id}', [AdminController::class, 'publisher_edit'])->name('admin.publisher.edit');
    Route::put('/admin/publisher/update', [AdminController::class, 'publisher_update'])->name('admin.publisher.update');
    Route::delete('/admin/publisher/{id}/delete', [AdminController::class, 'publisher_delete'])->name('admin.publisher.delete');

    Route::get('/admin/authors', [AdminController::class, 'authors'])->name('admin.authors');
    Route::get('/admin/author/add', [AdminController::class, 'add_author'])->name('admin.author.add');
    Route::post('/admin/author/store', [AdminController::class, 'author_store'])->name('admin.author.store');
    Route::get('/admin/author/edit/{id}', [AdminController::class, 'author_edit'])->name('admin.author.edit');
    Route::put('/admin/author/update', [AdminController::class, 'author_update'])->name('admin.author.update');
    Route::delete('/admin/author/{id}/delete', [AdminController::class, 'author_delete'])->name('admin.author.delete');

    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/category/add', [AdminController::class, 'category_add'])->name('admin.category.add');
    Route::post('/admin/category/store', [AdminController::class, 'category_store'])->name('admin.category.store');
    Route::get('/admin/category/{id}/edit', [AdminController::class, 'category_edit'])->name('admin.category.edit');
    Route::put('/admin/category/update', [AdminController::class, 'category_update'])->name('admin.category.update');
    Route::delete('/admin/category/{id}/delete', [AdminController::class, 'category_delete'])->name('admin.category.delete');

    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/product/add', [AdminController::class, 'product_add'])->name('admin.product.add');
    Route::post('/admin/product/store', [AdminController::class, 'product_store'])->name('admin.product.store');
    Route::get('/admin/product/{id}/edit', [AdminController::class, 'product_edit'])->name('admin.product.edit');
    Route::put('/admin/product/update', [AdminController::class, 'product_update'])->name('admin.product.update');
    Route::delete('/admin/product/{id}/delete', [AdminController::class, 'product_delete'])->name('admin.product.delete');

    Route::get('/admin/coupons', [AdminController::class, 'coupons'])->name('admin.coupons');
    Route::get('/admin/coupon/add', [AdminController::class, 'coupon_add'])->name('admin.coupon.add');
    Route::post('/admin/coupon/store', [AdminController::class, 'coupon_store'])->name('admin.coupon.store');
    Route::get('/admin/coupon/{id}/edit', [AdminController::class, 'coupon_edit'])->name('admin.coupon.edit');
    Route::put('/admin/coupon/update', [AdminController::class, 'coupon_update'])->name('admin.coupon.update');
    Route::delete('/admin/coupon/{id}/delete', [AdminController::class, 'coupon_delete'])->name('admin.coupon.delete');

    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::put('/admin/settings/update', [AdminController::class, 'account_update'])->name('admin.account.update');

    Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/admin/order/{order_id}/details', [AdminController::class, 'order_details'])->name('admin.order.details');
    Route::put('/admin/order/update-status', [AdminController::class, 'update_order_status'])->name('admin.order.status.update');

    Route::get('/admin/search', [AdminController::class, 'search'])->name('admin.search');

    Route::get('/admin/slides', [AdminController::class, 'slides'])->name('admin.slides');
    Route::get('/admin/slide/add', [AdminController::class, 'slide_add'])->name('admin.slide.add');
    Route::post('/admin/slide/store', [AdminController::class, 'slide_store'])->name('admin.slide.store');
    Route::get('/admin/slide/{id}/edit', [AdminController::class, 'slide_edit'])->name('admin.slide.edit');
    Route::put('/admin/slide/update', [AdminController::class, 'slide_update'])->name('admin.slide.update');
    Route::delete('/admin/slide/{id}/delete', [AdminController::class, 'slide_delete'])->name('admin.slide.delete');

});

Route::prefix('admin/free-products')->middleware(['auth', AuthAdmin::class])->group(function() {
    Route::get('ebooks', [FreeProductController::class, 'ebooks'])->name('admin.ebooks');
    Route::get('ebook/add', [FreeProductController::class, 'ebook_add'])->name('admin.ebook.add');
    Route::post('ebook/store', [FreeProductController::class, 'ebook_store'])->name('admin.ebook.store');
    Route::get('ebook/{id}/edit', [FreeProductController::class, 'ebook_edit'])->name('admin.ebook.edit');
    Route::put('/admin/ebook/update', [FreeProductController::class, 'ebook_update'])->name('admin.ebook.update');
    Route::delete('/admin/ebook/{id}/delete', [FreeProductController::class, 'ebook_delete'])->name('admin.ebook.delete');
});

// Route::get('/{product_slug}', [ShopController::class, 'product_details'])->name('product.details');

require __DIR__.'/auth.php';

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/aboutus', [HomeController::class, 'aboutus'])->name('home.aboutus');
Route::get('/contact', [HomeController::class, 'contact'])->name('home.contact');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product.details');
Route::get('/account', [HomeController::class, 'account'])->name('home.account');
Route::get('/account-wishlist', [HomeController::class, 'accountWishlist'])->name('home.accountWishlist');
Route::get('/account-order', [HomeController::class, 'accountOrder'])->name('home.accountOrder');
Route::get('/search', [HomeController::class, 'search'])->name('home.search');
Route::get('/free-products', [FreeProductController::class, 'index'])->name('free.products');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/read/sherlock-holmes', function () {
    return view('epub-reader');
})->name('epub.read.sherlock');
Route::get('/read/ebook/{id}', [FreeProductController::class, 'ebook_read'])->name('epub.reader');

Route::post('/ai/suggest-category', [AIController::class, 'suggestCategory']);
Route::post('/ai/generate-description', [AIController::class, 'generateDescription']);

Route::get('/test-audio', function () {
    return view('test-audio');
});

Route::get('/api/products', [ProductController::class, 'index']);
