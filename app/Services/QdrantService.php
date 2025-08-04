<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class QdrantService
{
    protected $host = 'http://localhost:6333'; // Đổi nếu dùng Qdrant cloud

    public function searchByVector(array $vector, $limit = 8)
    {
        $response = Http::post($this->host . '/collections/products/points/search', [
            'vector' => $vector,
            'limit' => $limit,
        ]);
        return $response->json('result');
    }
}