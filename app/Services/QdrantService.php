<?php

namespace App\Services;

use Qdrant\Qdrant;
use Qdrant\Config;
use Qdrant\Http\Builder;
use Qdrant\Endpoints\Collections;
use Qdrant\Models\Request\CreateCollection;
use Qdrant\Models\Request\VectorParams;
use Qdrant\Models\PointsStruct;
use Qdrant\Models\PointStruct;
use Qdrant\Models\VectorStruct;
use Qdrant\Models\Request\SearchRequest;
use Qdrant\Models\Filter\Filter;
use Qdrant\Models\Filter\Condition\MatchString;
use Illuminate\Support\Facades\Log;

class QdrantService
{
    protected $client;
    protected $collectionName = 'products';
    protected $vectorSize = 1536; // OpenAI embedding size

    public function __construct()
    {
        $this->initializeClient();
    }

    protected function initializeClient()
    {
        try {
            $config = new Config(config('services.qdrant.host', 'http://localhost:6333'));
            
            if (config('services.qdrant.api_key')) {
                $config->setApiKey(config('services.qdrant.api_key'));
            }

            $transport = (new Builder())->build($config);
            $this->client = new Qdrant($transport);
        } catch (\Exception $e) {
            Log::error('Failed to initialize Qdrant client: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a collection for storing product vectors
     */
    public function createCollection()
    {
        try {
            $createCollection = new CreateCollection();
            $createCollection->addVector(
                new VectorParams($this->vectorSize, VectorParams::DISTANCE_COSINE), 
                'content'
            );
            
            $response = $this->client->collections($this->collectionName)->create($createCollection);
            Log::info('Qdrant collection created successfully', ['response' => $response]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create Qdrant collection: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if collection exists
     */
    public function collectionExists()
    {
        try {
            $response = $this->client->collections($this->collectionName)->info();
            return isset($response['result']);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Insert or update product vectors
     */
    public function upsertProduct($productId, array $embedding, array $payload = [])
    {
        try {
            $points = new PointsStruct();
            $points->addPoint(
                new PointStruct(
                    (int) $productId,
                    new VectorStruct($embedding, 'content'),
                    $payload
                )
            );

            $response = $this->client->collections($this->collectionName)->points()->upsert($points, ['wait' => 'true']);
            Log::info('Product vector upserted successfully', ['product_id' => $productId]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to upsert product vector: ' . $e->getMessage(), ['product_id' => $productId]);
            throw $e;
        }
    }

    /**
     * Search for similar products using vector similarity
     */
    public function searchByVector(array $vector, $limit = 8, $scoreThreshold = 0.7)
    {
        try {
            $searchRequest = (new SearchRequest(new VectorStruct($vector, 'content')))
                ->setLimit($limit)
                ->setParams([
                    'hnsw_ef' => 64, // Reduced for faster search
                    'exact' => false,
                ])
                ->setWithPayload(false) // Don't return payload for faster response
                ->setScoreThreshold($scoreThreshold);

            $response = $this->client->collections($this->collectionName)->points()->search($searchRequest);
            
            if (isset($response['result'])) {
                return $response['result'];
            }
            
            return [];
        } catch (\Exception $e) {
            Log::error('Vector search failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Search with filters (e.g., by category, author)
     */
    public function searchWithFilters(array $vector, array $filters = [], $limit = 8)
    {
        try {
            $searchRequest = (new SearchRequest(new VectorStruct($vector, 'content')))
                ->setLimit($limit)
                ->setParams([
                    'hnsw_ef' => 128,
                    'exact' => false,
                ])
                ->setWithPayload(true);

            // Add filters if provided
            if (!empty($filters)) {
                $filter = new Filter();
                
                foreach ($filters as $field => $value) {
                    if (is_array($value)) {
                        // Handle array values (e.g., multiple categories)
                        $filter->addMust(new MatchString($field, $value[0])); // Simplified for now
                    } else {
                        $filter->addMust(new MatchString($field, $value));
                    }
                }
                
                $searchRequest->setFilter($filter);
            }

            $response = $this->client->collections($this->collectionName)->points()->search($searchRequest);
            
            if (isset($response['result'])) {
                return $response['result'];
            }
            
            return [];
        } catch (\Exception $e) {
            Log::error('Filtered vector search failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete a product vector
     */
    public function deleteProduct($productId)
    {
        try {
            $response = $this->client->collections($this->collectionName)->points()->delete([$productId]);
            Log::info('Product vector deleted successfully', ['product_id' => $productId]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to delete product vector: ' . $e->getMessage(), ['product_id' => $productId]);
            throw $e;
        }
    }

    /**
     * Get collection statistics
     */
    public function getCollectionInfo()
    {
        try {
            $response = $this->client->collections($this->collectionName)->info();
            return $response['result'] ?? null;
        } catch (\Exception $e) {
            Log::error('Failed to get collection info: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Legacy method for backward compatibility
     */
    public function searchByVectorLegacy(array $vector, $limit = 8)
    {
        return $this->searchByVector($vector, $limit);
    }
}