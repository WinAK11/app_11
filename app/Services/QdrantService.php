<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QdrantService
{
    protected $host = 'http://localhost:6333';
    protected $collectionName = 'products';

    public function searchByVector(array $vector, $limit = 8)
    {
        $response = Http::post($this->host . '/collections/products/points/search', [
            'vector' => $vector,
            'limit' => $limit,
        ]);
        return $response->json('result');
    }

    /**
     * Upsert (insert or update) points in a Qdrant collection.
     *
     * @param array $points An array of points to upsert.
     * @return bool True on success, false on failure.
     */
    public function upsertPoints(array $points): bool
    {
        if (empty($points)) {
            return true;
        }

        $response = Http::timeout(60)->put("{$this->host}/collections/{$this->collectionName}/points", [
            'points' => $points,
            'wait' => true
        ]);

        if ($response->failed()) {
            return false;
        }

        return $response->json('status') === 'ok';
    }

    /**
     * Delete points from a Qdrant collection.
     *
     * @param array $pointIds An array of point IDs to delete.
     * @return bool True on success, false on failure.
     */
    public function deletePoints(array $pointIds): bool
    {
        $response = Http::timeout(60)->post("{$this->host}/collections/{$this->collectionName}/points/delete", [
            'points' => $pointIds,
            'wait' => true
        ]);

        if ($response->failed()) {
            Log::error('Qdrant delete failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return false;
        }

        return $response->json('status') === 'ok';
    }
}