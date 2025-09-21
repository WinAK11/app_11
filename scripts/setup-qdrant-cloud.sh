#!/bin/bash

# Qdrant Cloud Setup Script
# This script helps you set up Qdrant Cloud for vector search

echo "🚀 Qdrant Cloud Setup for Vector Search"
echo "========================================"
echo ""

echo "📋 Prerequisites:"
echo "1. Qdrant Cloud account (sign up at https://cloud.qdrant.io/)"
echo "2. OpenAI API key"
echo "3. Laravel application with vector search implementation"
echo ""

echo "🔧 Setup Steps:"
echo ""

echo "1. Create a Qdrant Cloud cluster:"
echo "   - Go to https://cloud.qdrant.io/"
echo "   - Sign up for a free account"
echo "   - Create a new cluster (free tier)"
echo "   - Note down your cluster URL and API key"
echo ""

echo "2. Update your .env file with:"
echo "   QDRANT_HOST=https://your-cluster-id.region.cloud.qdrant.io:6333"
echo "   QDRANT_API_KEY=your_api_key_here"
echo "   OPENAI_API_KEY=your_openai_api_key_here"
echo ""

echo "3. Run the following commands:"
echo "   php artisan migrate"
echo "   php artisan embeddings:generate"
echo ""

echo "4. Test your setup:"
echo "   php artisan test tests/Feature/VectorSearchTest.php"
echo ""

echo "✅ That's it! Your vector search should now be working."
echo ""
echo "💡 Tips:"
echo "- The free tier includes 1GB storage (~100k-200k product embeddings)"
echo "- You can upgrade anytime as your needs grow"
echo "- Check the VECTOR_SEARCH_SETUP.md file for detailed instructions"
echo ""

read -p "Press Enter to continue..."
