# Vector Search Setup Guide

This guide will help you set up vector database search functionality using Qdrant and OpenAI embeddings.

## Prerequisites

1. **Qdrant Database**: You need a running Qdrant instance
2. **OpenAI API Key**: For generating embeddings
3. **Laravel Application**: This project with the vector search implementation

## Installation Steps

### 1. Set up Qdrant

#### Option A: Qdrant Cloud (Recommended - Free Tier Available)
1. Go to [Qdrant Cloud](https://cloud.qdrant.io/)
2. Sign up for a free account
3. Create a new cluster (free tier includes 1GB storage)
4. Get your cluster URL and API key from the dashboard
5. Use these in your environment configuration

#### Option B: Using Docker (Local Development)
```bash
docker run -p 6333:6333 qdrant/qdrant
```

#### Option C: Using Docker Compose
Create a `docker-compose.yml` file:
```yaml
version: '3.8'
services:
  qdrant:
    image: qdrant/qdrant
    ports:
      - "6333:6333"
    volumes:
      - qdrant_storage:/qdrant/storage
    environment:
      - QDRANT__SERVICE__HTTP_PORT=6333

volumes:
  qdrant_storage:
```

Then run:
```bash
docker-compose up -d
```

### 2. Environment Configuration

Add these variables to your `.env` file:

#### For Qdrant Cloud:
```env
# Qdrant Cloud Configuration
QDRANT_HOST=https://your-cluster-id.eu-central.aws.cloud.qdrant.io:6333
QDRANT_API_KEY=your_qdrant_cloud_api_key

# OpenAI Configuration (for embeddings)
OPENAI_API_KEY=your_openai_api_key_here

# Optional: DeepSeek Configuration (for AI features)
DEEPSEEK_API_KEY=your_deepseek_api_key_here
```

#### For Local Docker:
```env
# Qdrant Local Configuration
QDRANT_HOST=http://localhost:6333
QDRANT_API_KEY=

# OpenAI Configuration (for embeddings)
OPENAI_API_KEY=your_openai_api_key_here

# Optional: DeepSeek Configuration (for AI features)
DEEPSEEK_API_KEY=your_deepseek_api_key_here
```

**Note**: For Qdrant Cloud, you'll get the host URL and API key from your cluster dashboard. The free tier includes:
- 1GB storage
- 1 million vectors
- Basic support

#### Why Choose Qdrant Cloud?

**Advantages over local Docker:**
- ✅ **No setup required** - Just sign up and get started
- ✅ **Better performance** - Optimized infrastructure
- ✅ **Automatic backups** - Your data is safe
- ✅ **Scalability** - Easy to upgrade as you grow
- ✅ **Global availability** - Multiple regions available
- ✅ **SSL/TLS security** - Encrypted connections
- ✅ **Monitoring** - Built-in metrics and monitoring
- ✅ **No maintenance** - Qdrant handles updates and maintenance

**Free Tier Limits:**
- 1GB storage (enough for ~100,000-200,000 product embeddings)
- 1 million vectors
- Basic support
- Perfect for development and small to medium applications

### 3. Run Database Migration

```bash
php artisan migrate
```

### 4. Generate Embeddings for Existing Products

```bash
# Generate embeddings for all products (this may take a while)
php artisan embeddings:generate

# Or generate for a limited number of products
php artisan embeddings:generate --limit=10

# Force regenerate existing embeddings
php artisan embeddings:generate --force
```

## Usage

### Search Types

The search functionality now supports three modes:

1. **AI + Text (Hybrid)**: Combines vector similarity search with traditional text search
2. **AI Only (Vector)**: Uses only vector similarity search for semantic understanding
3. **Text Only**: Uses traditional text-based search (fallback)

### API Usage

You can also use the search functionality via API:

```javascript
// Example AJAX call
$.ajax({
    type: "GET",
    url: "/search",
    data: {
        query: "your search query",
        search_type: "hybrid" // or "vector" or "text"
    },
    success: function(data) {
        console.log(data);
    }
});
```

### Programmatic Usage

```php
use App\Services\AIService;
use App\Services\QdrantService;

// Generate embedding for a search query
$aiService = new AIService();
$embedding = $aiService->generateSearchEmbedding("your search query");

// Search using Qdrant
$qdrantService = new QdrantService();
$results = $qdrantService->searchByVector($embedding, 10, 0.7);
```

## Features

### Vector Search Benefits

1. **Semantic Understanding**: Finds products based on meaning, not just keywords
2. **Multilingual Support**: Works across different languages
3. **Context Awareness**: Understands relationships between concepts
4. **Similarity Scoring**: Provides relevance scores for results

### Search Result Enhancements

- Clean, fast search results without similarity scores
- Author information for better context
- Fallback to text search if vector search fails
- Loading indicators and error handling
- Optimized performance with debounced requests

## Troubleshooting

### Common Issues

1. **Qdrant Connection Error**
   - For Cloud: Verify your cluster URL and API key are correct
   - For Local: Ensure Qdrant is running on the correct port
   - Check QDRANT_HOST in your .env file

2. **OpenAI API Errors**
   - Verify your OPENAI_API_KEY is correct
   - Check your OpenAI account has sufficient credits

3. **No Search Results**
   - Ensure embeddings have been generated: `php artisan embeddings:generate`
   - Check if products have the `has_embedding` flag set to true

4. **Performance Issues**
   - Qdrant Cloud provides better performance than local Docker
   - Optimize embedding generation with rate limiting

5. **SSL/TLS Issues with Qdrant Cloud**
   - Ensure your Qdrant Cloud URL uses HTTPS
   - Check if your server can make outbound HTTPS connections

### Monitoring

Check the Laravel logs for vector search related errors:
```bash
tail -f storage/logs/laravel.log
```

## Advanced Configuration

### Custom Vector Size

If you want to use a different embedding model, update the vector size in `QdrantService.php`:

```php
protected $vectorSize = 1536; // Change this to match your embedding model
```

### Custom Search Parameters

You can customize search parameters in the `searchByVector` method:

```php
$results = $qdrantService->searchByVector($embedding, $limit, $scoreThreshold);
```

- `$limit`: Maximum number of results
- `$scoreThreshold`: Minimum similarity score (0.0 to 1.0)

## Support

For issues or questions:
1. Check the Laravel logs
2. Verify Qdrant is running and accessible
3. Ensure all environment variables are set correctly
4. Test with a small number of products first
