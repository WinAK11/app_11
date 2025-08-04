<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIService {
    protected $apiKey;
    protected $apiUrl = 'https://api.deepseek.com/v1/chat/completions';

    public function __construct() {
        $this->apiKey = env( 'DEEPSEEK_API_KEY' );
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
}
