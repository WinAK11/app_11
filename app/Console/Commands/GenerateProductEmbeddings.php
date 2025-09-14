<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\AIService;
use App\Services\QdrantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateProductEmbeddings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'embeddings:generate {--force : Force regenerate existing embeddings} {--limit= : Limit number of products to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate embeddings for products and store them in Qdrant';

    protected $aiService;
    protected $qdrantService;

    public function __construct()
    {
        parent::__construct();
        $this->aiService = new AIService();
        $this->qdrantService = new QdrantService();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting product embedding generation...');

        // Check if Qdrant collection exists, create if not
        if (!$this->qdrantService->collectionExists()) {
            $this->info('Creating Qdrant collection...');
            try {
                $this->qdrantService->createCollection();
                $this->info('Qdrant collection created successfully.');
            } catch (\Exception $e) {
                $this->error('Failed to create Qdrant collection: ' . $e->getMessage());
                return 1;
            }
        } else {
            $this->info('Qdrant collection already exists.');
        }

        // Get products to process
        $query = Product::with(['author', 'category']);
        
        if (!$this->option('force')) {
            $query->where('has_embedding', false);
        }

        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }

        $products = $query->get();
        $totalProducts = $products->count();

        if ($totalProducts === 0) {
            $this->info('No products to process.');
            return 0;
        }

        $this->info("Processing {$totalProducts} products...");

        $progressBar = $this->output->createProgressBar($totalProducts);
        $progressBar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($products as $product) {
            try {
                // Generate embedding
                $embedding = $this->aiService->generateProductEmbedding($product);
                
                if (!$embedding) {
                    $this->error("Failed to generate embedding for product: {$product->name}");
                    $errorCount++;
                    $progressBar->advance();
                    continue;
                }

                // Store in database
                $product->update([
                    'embedding' => $embedding,
                    'has_embedding' => true,
                    'embedding_updated_at' => now(),
                ]);

                // Store in Qdrant
                $payload = [
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'short_description' => $product->short_description,
                    'category_id' => $product->category_id,
                    'author_id' => $product->author_id,
                    'category_name' => $product->category?->name,
                    'author_name' => $product->author?->name,
                ];

                $this->qdrantService->upsertProduct($product->id, $embedding, $payload);
                $successCount++;

            } catch (\Exception $e) {
                $this->error("Error processing product {$product->name}: " . $e->getMessage());
                Log::error('Embedding generation error', [
                    'product_id' => $product->id,
                    'error' => $e->getMessage()
                ]);
                $errorCount++;
            }

            $progressBar->advance();
            
            // Add small delay to avoid rate limiting
            usleep(100000); // 0.1 second
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("Embedding generation completed!");
        $this->info("Successfully processed: {$successCount} products");
        if ($errorCount > 0) {
            $this->warn("Failed to process: {$errorCount} products");
        }

        return 0;
    }
}
