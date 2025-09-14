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
            // Add full-text search indexes for faster text search
            $table->index('name');
            $table->index('description');
            $table->index('short_description');
            $table->index('has_embedding');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['description']);
            $table->dropIndex(['short_description']);
            $table->dropIndex(['has_embedding']);
        });
    }
};
