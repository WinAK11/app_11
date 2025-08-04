<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ebook extends Model {
    protected $fillable = [ 'product_id', 'file_path', 'preview_path', 'cover_path', 'title', 'author', 'category_id', 'format', 'description' ];

    public function category() {
        return $this->belongsTo( Category::class, 'category_id' );
    }
}
