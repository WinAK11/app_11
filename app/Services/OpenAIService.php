<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected string $apiKey;
    protected string $embeddingModel = 'text-embedding-3-small'; // Model mới hơn và hiệu quả hơn 'text-embedding-ada-002'

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
        if (!$this->apiKey) {
            Log::error('OpenAI API key is not set.');
            throw new Exception('OpenAI API key is not configured.');
        }
    }

    /**
     * Generate embedding for a given text.
     *
     * @param string $text
     * @return array|null
     */
    public function getEmbedding(string $text): ?array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30) // Đặt timeout để tránh chờ quá lâu
                ->post('https://api.openai.com/v1/embeddings', [
                    'input' => $text,
                    'model' => $this->embeddingModel,
                ]);

            if ($response->failed()) {
                Log::error('OpenAI API call failed for embedding', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            return $response->json('data.0.embedding');
        } catch (Exception $e) {
            Log::error('Error getting embedding from OpenAI', ['message' => $e->getMessage()]);
            return null;
        }
    }
}