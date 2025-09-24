<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('embedding')->nullable()->after('images');
            $table->boolean('has_embedding')->default(false)->after('embedding');
            $table->timestamp('embedding_updated_at')->nullable()->after('has_embedding');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['embedding', 'has_embedding', 'embedding_updated_at']);
        });
    }
};
