<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\OpenAIService;
use App\Services\QdrantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Exception;

class GenerateProductEmbeddings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-product-embeddings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and store embeddings for all existing products in Qdrant.';

    /**
     * Execute the console command.
     */
    public function handle(OpenAIService $openAIService, QdrantService $qdrantService): int
    {
        $this->info('Starting to generate embeddings for all products...');

        $productCount = Product::count();
        if ($productCount === 0) {
            $this->info('No products found in the database.');
            return self::SUCCESS;
        }

        $progressBar = $this->output->createProgressBar($productCount);
        $progressBar->start();

        // Product processing
        Product::with(['category', 'author'])->chunkById(50, function ($products) use ($openAIService, $qdrantService, $progressBar) {
            $pointsToUpsert = [];

            foreach ($products as $product) {
                try {
                    // Create embedded strings
                    $textToEmbed = "Tên sách: {$product->name}. ";
                    $textToEmbed .= "Mô tả: {$product->short_description}. ";
                    if ($product->category) {
                        $textToEmbed .= "Thể loại: {$product->category->name}. ";
                    }
                    if ($product->author) {
                        $textToEmbed .= "Tác giả: {$product->author->name}.";
                    }

                    $vector = $openAIService->getEmbedding(trim($textToEmbed));

                    if ($vector) {
                        $pointsToUpsert[] = [
                            'id' => $product->id,
                            'vector' => $vector,
                            'payload' => ['name' => $product->name, 'category_id' => $product->category_id]
                        ];
                    } else {
                        $this->warn("\nCould not generate embedding for product ID: {$product->id}");
                    }
                } catch (Exception $e) {
                    $this->error("\nAn error occurred for product ID: {$product->id}. Error: {$e->getMessage()}");
                    Log::error("Embedding generation exception for product ID: {$product->id}", ['error' => $e->getMessage()]);
                }
                $progressBar->advance();
            }

            if (!empty($pointsToUpsert) && !$qdrantService->upsertPoints($pointsToUpsert)) {
                $this->error("\nFailed to upsert a batch of points to Qdrant.");
            }
        });

        $progressBar->finish();
        $this->info("\n\nEmbedding generation and storage complete!");
        return self::SUCCESS;
    }
}