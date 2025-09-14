<?php

use App\Models\Product;
use App\Services\AIService;
use App\Services\QdrantService;

test('search endpoint returns json response', function () {
    $response = $this->get('/search?query=test');

    $response->assertStatus(200);
    $response->assertJson([]);
});

test('search with different search types', function () {
    $searchTypes = ['text', 'vector', 'hybrid'];
    
    foreach ($searchTypes as $type) {
        $response = $this->get("/search?query=book&search_type={$type}");
        $response->assertStatus(200);
        $response->assertJson([]);
    }
});

test('search handles empty query', function () {
    $response = $this->get('/search?query=');
    
    $response->assertStatus(200);
    $response->assertJson([]);
});

test('product model has embedding fields', function () {
    $product = new Product();
    
    expect($product->getFillable())->toContain('embedding');
    expect($product->getFillable())->toContain('has_embedding');
    expect($product->getFillable())->toContain('embedding_updated_at');
});

test('ai service can generate embeddings', function () {
    $aiService = new AIService();
    
    // This test will only work if OpenAI API key is configured
    if (config('services.openai.key')) {
        $embedding = $aiService->generateEmbedding('test text');
        expect($embedding)->toBeArray();
    } else {
        expect($aiService->generateEmbedding('test text'))->toBeNull();
    }
});

test('qdrant service can be instantiated', function () {
    $qdrantService = new QdrantService();
    
    expect($qdrantService)->toBeInstanceOf(QdrantService::class);
});
