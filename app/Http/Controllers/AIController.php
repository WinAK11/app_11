<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller {
    public function suggestCategory( Request $request ) {
        $request->validate( [
            'title' => 'required|string',
            'author' => 'required|string',
        ] );

        $prompt = "Suggest the most fitting book category (like Fantasy, Romance, Biography, etc.) for the book titled '{$request->title}' by {$request->author}. Respond with only the category name.";

        $aiCategory = trim( $this->callOpenAI( $prompt ) );

        // Get all categories from the database
        $categories = Category::all();

        $bestMatch = null;
        $highestScore = 0;

        foreach ( $categories as $category ) {
            similar_text( strtolower( $aiCategory ), strtolower( $category->name ), $percent );
            if ( $percent > $highestScore ) {
                $highestScore = $percent;
                $bestMatch = $category;
            }
        }

        if ( $bestMatch && $highestScore >= 65 ) {
            // You can tweak this threshold
            return response()->json( [
                'category' => $bestMatch->id,
                'category_name' => $bestMatch->name,
                'matched' => true
            ] );
        } else {
            return response()->json( [
                'category' => null,
                'category_name' => $aiCategory,
                'matched' => false,
                'message' => 'No suitable category match found.'
            ] );
        }
    }

    public function generateDescription( Request $request ) {
        $request->validate( [
            'title' => 'required|string',
            'author' => 'required|string',
        ] );

        $prompt = "Write a short and engaging description for a book titled ' {
                    $request->title}
                    ' by {$request->author}. Make it appealing for potential readers.";

        $response = $this->callOpenAI( $prompt );

        return response()->json( [
            'description' => trim( $response )
        ] );
    }

    private function callOpenAI( string $prompt ): string {
        $apiKey = config( 'services.openai.key' );

        $response = Http::withToken( $apiKey )->post( 'https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o',
            'messages' => [
                [ 'role' => 'system', 'content' => 'You are a helpful assistant that categorizes books and writes descriptions.' ],
                [ 'role' => 'user', 'content' => $prompt ]
            ],
            'temperature' => 0.7,
        ] );

        return $response->json( 'choices.0.message.content' ) ?? 'Unknown';
    }
}
