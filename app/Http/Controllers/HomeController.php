<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Slide;
use App\Services\AIService;
use App\Services\QdrantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $query = $request->input( 'query' );
        $searchType = $request->input( 'search_type', 'hybrid' ); // 'text', 'vector', 'hybrid'

        if (empty($query)) {
            return response()->json([]);
        }

        $results = [];

        try {
            // Try vector search first if enabled
            if (in_array($searchType, ['vector', 'hybrid'])) {
                $vectorResults = $this->performVectorSearch($query);
                if (!empty($vectorResults)) {
                    $results = $vectorResults;
                }
            }

            // Fallback to text search if vector search fails or hybrid mode
            if (empty($results) || $searchType === 'text' || $searchType === 'hybrid') {
                $textResults = $this->performTextSearch($query);

                if ($searchType === 'hybrid' && !empty($results)) {
                    // Merge results, avoiding duplicates
                    $vectorIds = collect($results)->pluck('id')->toArray();
                    $textResults = collect($textResults)->filter(function($product) use ($vectorIds) {
                        return !in_array($product->id, $vectorIds);
                    })->values()->toArray();

                    $results = array_merge($results, $textResults);
                } else {
                    $results = $textResults;
                }
            }

            // Limit results
            $results = array_slice($results, 0, 8);

        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            // Fallback to text search on error
            $results = $this->performTextSearch($query);
        }

        return response()->json( $results );
    }

    /**
     * Perform vector-based search using Qdrant
     */
    protected function performVectorSearch(string $query): array
    {
        try {
            $aiService = new AIService();
            $qdrantService = new QdrantService();

            // Generate embedding for the search query
            $embedding = $aiService->generateSearchEmbedding($query);

            if (!$embedding) {
                Log::warning('Failed to generate embedding for search query');
                return [];
            }

            // Search in Qdrant (faster with lower threshold)
            $vectorResults = $qdrantService->searchByVector($embedding, 8, 0.5);

            if (empty($vectorResults)) {
                return [];
            }

            // Get product IDs from vector search results
            $productIds = collect($vectorResults)->pluck('id')->toArray();

            // Fetch full product data from database
            $products = Product::with(['author', 'category'])
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            // Order results by vector search score (without storing score)
            $orderedResults = [];
            foreach ($vectorResults as $result) {
                if (isset($products[$result['id']])) {
                    $orderedResults[] = $products[$result['id']];
                }
            }

            return $orderedResults;

        } catch (\Exception $e) {
            Log::error('Vector search failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Perform traditional text-based search (optimized with database queries)
     */
    protected function performTextSearch(string $query): array
    {
        // Use database LIKE queries for faster search
        $products = Product::with(['author', 'category'])
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('short_description', 'LIKE', "%{$query}%");
            })
            ->limit(8)
            ->get();

        return $products->toArray();
    }
}
