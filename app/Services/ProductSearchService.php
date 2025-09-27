<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductSearchService
{
    protected OpenAIService $openAIService;
    protected QdrantService $qdrantService;

    public function __construct(OpenAIService $openAIService, QdrantService $qdrantService)
    {
        $this->openAIService = $openAIService;
        $this->qdrantService = $qdrantService;
    }

    /**
     * Performs a vector search for products and falls back to LIKE search if vector search fails.
     *
     * @param string $query The search query.
     * @param int $limit The maximum number of results to return from Qdrant.
     * @return \Illuminate\Support\Collection
     */
    public function searchProducts(string $query, int $limit = 8): \Illuminate\Support\Collection
    {
        $cacheKey = 'vector_search_' . md5(strtolower(trim($query)));

        try {
            $results = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($query, $limit) {
                $queryVector = $this->openAIService->getEmbedding($query);

                if (!$queryVector) {
                    throw new \Exception("Failed to generate embedding.");
                }

                $qdrantResults = $this->qdrantService->searchByVector($queryVector, $limit);

                if (empty($qdrantResults)) {
                    return collect(); // Return empty collection
                }

                $productIds = array_map(fn($result) => $result['id'], $qdrantResults);

                if (empty($productIds)) {
                    return collect(); // Return empty collection
                }

                $ids_ordered = implode(',', $productIds);

                return Product::whereIn('id', $productIds)
                    ->with('category')
                    ->orderByRaw("FIELD(id, $ids_ordered)")
                    ->get();
            });

            return $results;
        } catch (\Exception $e) {
            Log::error('Vector search failed, falling back to LIKE search.', ['query' => $query, 'error' => $e->getMessage()]);
            // Fallback: tìm kiếm theo tên sản phẩm nếu có lỗi, cũng cache kết quả này
            $fallbackCacheKey = 'fallback_search_' . md5(strtolower(trim($query)));
            $fallbackResults = Cache::remember($fallbackCacheKey, now()->addMinutes(60), function () use ($query, $limit) {
                return Product::where('name', 'LIKE', "%{$query}%")->with('category')->take($limit)->get();
            });
            return $fallbackResults;
        }
    }
}