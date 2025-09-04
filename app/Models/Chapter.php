<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model {
    protected $fillable = [
        'ebook_id',
        'index',
        'title',
        'text',
        'audio_path',
    ];

    public function ebook() {
        return $this->belongsTo( Ebook::class );
    }
}
