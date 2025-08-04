<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Slide;
use Illuminate\Http\Request;

class HomeController extends Controller {
    /**
    * Create a new controller instance.
    *
    * @return void
    */

    // public function __construct() {
    //     $this->middleware( 'auth' );
    // }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */

    public function index() {
        $slides = Slide::where('status', 1)->get()->take(3);
        $categories = Category::orderBy('name')->get();
        $sale_products = Product::whereNotNull('sale_price')->where('sale_price', '<>', '')->inRandomOrder()->get()->take(8);
        $featured_products = Product::where('featured', 1)->get()->take(8);
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

    public function search( Request $request ) {
        $query = mb_strtolower( $request->input( 'query' ) );
        $products = Product::all();
        $results = [];

        foreach ( $products as $product ) {
            $productName = mb_strtolower( $product->name );

            // Similar text between keyword and results
            similar_text( $query, $productName, $percent );

            // Levenshtein distance
            $distance = levenshtein( $query, $productName );

            // Fuzzy
            if ( $percent >= 60 || $distance <= 2 || stripos( $productName, $query ) !== false ) {
                $results[] = $product;
            }

            if ( count( $results ) >= 8 ) break;
        }

        return response()->json( $results );
    }
}
