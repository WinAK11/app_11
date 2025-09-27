<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService {
    protected $apiKey;
    protected $apiUrl = 'https://api.deepseek.com/v1/chat/completions';
    protected $openaiApiKey;
    protected $openaiEmbeddingUrl = 'https://api.openai.com/v1/embeddings';

    public function __construct() {
        $this->apiKey = env( 'DEEPSEEK_API_KEY' );
        $this->openaiApiKey = config( 'services.openai.key' );
    }

    public function suggestCategory( string $title, string $author ): string {
        $prompt = "Suggest exactly ONE book category (e.g., 'Fantasy', 'Romance') for: '{$title}' by {$author}. Respond with ONLY the category name.";

        $response = $this->makeApiRequest( $prompt );

        return $response->successful()
        ? trim( $response->json( 'choices.0.message.content' ) )
        : 'General Fiction';
    }

    public function generateDescription( string $title, string $author ): string {
        $prompt = "Write a concise 100-word description for '{$title}' by {$author}. Focus on the main plot points without spoilers.";

        $response = $this->makeApiRequest( $prompt );

        return $response->successful()
        ? trim( $response->json( 'choices.0.message.content' ) )
        : 'A captivating literary work worth exploring.';
    }

    public function suggestFromTitleAuthor( string $title, string $author ): array {
        $prompt = "For '{$title}' by {$author}, suggest: 1) A single category, 2) A 100-word description. Respond in JSON format: {'category':'...','description':'...'}";

        $response = $this->makeApiRequest( $prompt, true );

        return $response->successful()
        ? json_decode( $response->json( 'choices.0.message.content' ), true )
        : [ 'category' => 'General Fiction', 'description' => 'A classic work of literature.' ];
    }

    protected function makeApiRequest( string $prompt, bool $jsonMode = false ): \Illuminate\Http\Client\Response {
        $payload = [
            'model' => 'deepseek-chat',
            'messages' => [ [ 'role' => 'user', 'content' => $prompt ] ],
            'max_tokens' => 300,
        ];

        if ( $jsonMode ) {
            $payload[ 'response_format' ] = [ 'type' => 'json_object' ];
        }

        return Http::withHeaders( [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ] )->timeout( 30 )->post( $this->apiUrl, $payload );
    }

    /**
     * Generate embeddings for text using OpenAI API
     */
    public function generateEmbedding(string $text): ?array
    {
        try {
            if (!$this->openaiApiKey) {
                Log::warning('OpenAI API key not configured for embedding generation');
                return null;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openaiApiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->openaiEmbeddingUrl, [
                'model' => 'text-embedding-ada-002',
                'input' => $text,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data'][0]['embedding'])) {
                    return $data['data'][0]['embedding'];
                }
            }

            Log::error('Failed to generate embedding', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception while generating embedding: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate embeddings for product content
     */
    public function generateProductEmbedding($product): ?array
    {
        // Combine product information for embedding
        $content = $this->buildProductContent($product);
        return $this->generateEmbedding($content);
    }

    /**
     * Build content string from product data for embedding
     */
    protected function buildProductContent($product): string
    {
        $content = $product->name;
        
        if ($product->short_description) {
            $content .= ' ' . $product->short_description;
        }
        
        if ($product->description) {
            $content .= ' ' . $product->description;
        }
        
        if ($product->author) {
            $content .= ' Author: ' . $product->author->name;
        }
        
        if ($product->category) {
            $content .= ' Category: ' . $product->category->name;
        }

        return trim($content);
    }

    /**
     * Generate embeddings for search query
     */
    public function generateSearchEmbedding(string $query): ?array
    {
        return $this->generateEmbedding($query);
    }
}
