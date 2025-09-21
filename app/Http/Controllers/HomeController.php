<?php

namespace App\Http\Controllers;

use App\Models\Author;
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

    public function searchResults(Request $request)
    {
        $query = $request->input('q', '');
        $search_type = $request->input('search_type', 'hybrid');
        $sort = $request->input('sort', 'relevance');
        $f_authors = $request->input('authors', '');
        $f_categories = $request->input('categories', '');
        $f_rating = $request->input('rating', '');
        $min_price = $request->input('min', 1);
        $max_price = $request->input('max', 1000000);

        // Get filter data
        $categories = Category::whereHas('products')->withCount('products')->orderBy('name', 'ASC')->get();
        $authors = Author::orderBy('name', 'ASC')->get();

        // Build the base query
        $productsQuery = Product::with(['category', 'author', 'reviews' => function($query) {
            $query->where('status', 'approved');
        }]);

        // Apply search query if provided, otherwise show all products
        if (!empty($query)) {
            if ($search_type === 'vector') {
                // Use vector search only
                $vectorResults = $this->performVectorSearch($query);
                if (!empty($vectorResults)) {
                    $productIds = collect($vectorResults)->pluck('id')->toArray();
                    $productsQuery->whereIn('id', $productIds);
                } else {
                    // Fallback to text search if vector search fails
                    $productsQuery->where(function($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                          ->orWhere('description', 'LIKE', "%{$query}%")
                          ->orWhere('short_description', 'LIKE', "%{$query}%");
                    });
                }
            } elseif ($search_type === 'hybrid') {
                // Use hybrid search - try vector first, then combine with text search
                $vectorResults = $this->performVectorSearch($query);
                $vectorProductIds = [];
                
                if (!empty($vectorResults)) {
                    $vectorProductIds = collect($vectorResults)->pluck('id')->toArray();
                }
                
                // Always include text search results
                $productsQuery->where(function($q) use ($query, $vectorProductIds) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('short_description', 'LIKE', "%{$query}%");
                    
                    // If we have vector results, also include them
                    if (!empty($vectorProductIds)) {
                        $q->orWhereIn('id', $vectorProductIds);
                    }
                });
            } else {
                // Use text search only
                $productsQuery->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('short_description', 'LIKE', "%{$query}%");
                });
            }
        }

        // Apply filters
        if ($f_authors) {
            $author_ids = explode(',', $f_authors);
            $productsQuery->whereIn('author_id', $author_ids);
        }

        if ($f_categories) {
            $category_ids = explode(',', $f_categories);
            $productsQuery->whereIn('category_id', $category_ids);
        }

        if ($f_rating) {
            $ratings = explode(',', $f_rating);
            $productsQuery->whereHas('reviews', function($q) use ($ratings) {
                $q->whereIn('rating', $ratings);
            });
        }

        // Apply price filter
        $productsQuery->where(function ($query) use ($min_price, $max_price) {
            $query->where(function($q) use ($min_price, $max_price) {
                $q->where('regular_price', '>=', $min_price)
                  ->where('regular_price', '<=', $max_price);
            })->orWhere(function($q) use ($min_price, $max_price) {
                $q->where('sale_price', '>=', $min_price)
                  ->where('sale_price', '<=', $max_price);
            });
        });

        // Apply sorting
        switch ($sort) {
            case 'name_asc':
                $productsQuery->orderBy('name', 'ASC');
                break;
            case 'name_desc':
                $productsQuery->orderBy('name', 'DESC');
                break;
            case 'price_asc':
                $productsQuery->orderBy('regular_price', 'ASC');
                break;
            case 'price_desc':
                $productsQuery->orderBy('regular_price', 'DESC');
                break;
            case 'rating':
                $productsQuery->withAvg('reviews', 'rating')
                    ->orderBy('reviews_avg_rating', 'DESC');
                break;
            case 'newest':
                $productsQuery->orderBy('created_at', 'DESC');
                break;
            case 'relevance':
            default:
                // For relevance, if we have vector search results, maintain their order
                if (!empty($query) && in_array($search_type, ['vector', 'hybrid'])) {
                    $vectorResults = $this->performVectorSearch($query);
                    if (!empty($vectorResults)) {
                        $productIds = collect($vectorResults)->pluck('id')->toArray();
                        $productsQuery->orderByRaw('FIELD(id, ' . implode(',', $productIds) . ')');
                    } else {
                        $productsQuery->orderBy('name', 'ASC');
                    }
                } else {
                    $productsQuery->orderBy('name', 'ASC');
                }
                break;
        }

        // Paginate results
        $products = $productsQuery->paginate(12);

        // Debug: Log search results for troubleshooting
        if (!empty($query)) {
            Log::info('Search Results Debug', [
                'query' => $query,
                'search_type' => $search_type,
                'total_results' => $products->total(),
                'current_page_results' => $products->count()
            ]);
        }

        return view('search-results', compact(
            'query', 'search_type', 'sort', 'f_authors', 'f_categories', 'f_rating',
            'min_price', 'max_price', 'categories', 'authors', 'products'
        ));
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
                Log::warning('Failed to generate embedding for search query: ' . $query);
                return [];
            }

            // Search in Qdrant (faster with lower threshold)
            $vectorResults = $qdrantService->searchByVector($embedding, 8, 0.5);

            Log::info('Vector search results', [
                'query' => $query,
                'vector_results_count' => count($vectorResults),
                'vector_results' => $vectorResults
            ]);

            if (empty($vectorResults)) {
                Log::info('No vector search results found for query: ' . $query);
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

            Log::info('Vector search final results', [
                'query' => $query,
                'final_results_count' => count($orderedResults)
            ]);

            return $orderedResults;

        } catch (\Exception $e) {
            Log::error('Vector search failed: ' . $e->getMessage(), [
                'query' => $query,
                'exception' => $e
            ]);
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
