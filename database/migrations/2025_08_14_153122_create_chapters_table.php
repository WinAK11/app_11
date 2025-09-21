<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChaptersTable extends Migration
{
    public function up()
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ebook_id');
            $table->integer('index'); // Chapter number (1, 2, 3...)
            $table->text('title')->nullable(); // Optional: chapter title
            $table->longText('text'); // Full chapter text
            $table->string('audio_path')->nullable(); // Path to audio file
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('ebook_id')->references('id')->on('ebooks')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chapters');
    }
}
