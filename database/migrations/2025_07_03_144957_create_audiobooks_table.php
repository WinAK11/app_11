<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('audiobooks', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('author');
        $table->string('category');
        $table->string('description');
        $table->string('file_path'); // path to the audio file
        $table->string('cover_path')->nullable();
        $table->string('duration')->nullable(); // optional: total play time
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('audiobooks');
}
};
