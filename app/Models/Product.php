<?php

namespace App\Models;
use App\Models\Ebook;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    public function category() {
        return $this->belongsTo( Category::class, 'category_id' );
    }

    public function author() {
        return $this->belongsTo( Author::class, 'author_id', 'id' );
    }

}
