<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audiobook extends Model {
    protected $fillable = [ 'product_id', 'file_path', 'preview_path' ];

}
