<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductSearchService;
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
        $search_query = $request->query('query'); // New: Get search query
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
        // $categories = Category::orderBy('name', 'ASC')->get();
        $categories = Category::whereHas( 'products' )->withCount( 'products' )->orderBy( 'name', 'ASC' )->get();
        $authors = Author::orderBy('name', 'ASC')->get();

        // Initialize product query
        $productsQuery = Product::query();

        // Apply search query if present
        if (!empty($search_query)) {
            // Use the ProductSearchService to get relevant product IDs
            $productSearchService = app(ProductSearchService::class); // Resolve the service
            $searchResults = $productSearchService->searchProducts($search_query, 50); // Get more results for shop page
            $productIds = $searchResults->pluck('id')->toArray();

            // Filter the main query by these IDs and maintain order
            if (!empty($productIds)) {
                $ids_ordered = implode(',', $productIds);
                $productsQuery->whereIn('id', $productIds)->orderByRaw("FIELD(id, $ids_ordered)");
            } else {
                $productsQuery->whereRaw('1 = 0'); // Return no results if vector search yields nothing
            }
        }

        $products = $productsQuery->when($f_authors, fn ($query) => $query->whereIn('author_id', explode(',', $f_authors)))
            ->when($f_categories, fn ($query) => $query->whereIn('category_id', explode(',', $f_categories)))
            ->where(fn ($query) => $query->whereBetween('regular_price', [$min_price, $max_price])->orWhereBetween('sale_price', [$min_price, $max_price]))
            ->orderBy($o_column, $o_order) // Apply default/selected sorting if no search query or after search query
            ->paginate(12);
        return view('shop', compact('products', 'order', 'authors', 'f_authors', 'categories', 'f_categories', 'min_price', 'max_price'));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->first();
        $related_products = Product::where('slug', '<>', $product_slug)->get()->take(8);
        return view('details', compact('product', 'related_products'));
    }
}
