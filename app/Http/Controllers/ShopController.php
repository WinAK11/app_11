<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $order = $request->query('order') ? $request->query('order') : -1;
        $o_column = "";
        $o_order = "";
        $f_authors = $request->query('authors');
        $f_categories = $request->query('categories');
        $min_price = $request->query('min')?$request->query('min'):1;
        $max_price = $request->query('max')?$request->query('max'):1000000;
        switch($order)
        {
            case 1:
                $o_column='name';
                $o_order='DESC';
                break;
            case 2:
                $o_column='regular_price';
                $o_order='ASC';
                break;
            case 3:
                $o_column='regular_price';
                $o_order='DESC';
                break;
            default:
                $o_column='name';
                $o_order='ASC';
                break;
        }
        $categories = Category::orderBy('name', 'ASC')->get();
        $authors = Author::orderBy('name', 'ASC')->get();
        $products = Product::when($f_authors, function ($query) use ($f_authors) {
            $author_ids = explode(',', $f_authors);
            return $query->whereIn('author_id', $author_ids);
        })
        ->when($f_categories, function ($query) use ($f_categories) {
            $category_ids = explode(',', $f_categories);
            return $query->whereIn('category_id', $category_ids);
        })
        ->where(function ($query) use ($min_price, $max_price) {
            $query->whereBetween('regular_price', [$min_price, $max_price])
            ->orWhereBetween('sale_price', [$min_price, $max_price]);
        })->orderBy($o_column, $o_order)->paginate(12);
        return view('shop', compact('products', 'order', 'authors', 'f_authors', 'categories', 'f_categories', 'min_price', 'max_price'));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->first();
        $related_products = Product::where('slug', '<>', $product_slug)->get()->take(8);
        return view('details', compact('product', 'related_products'));
    }
}
