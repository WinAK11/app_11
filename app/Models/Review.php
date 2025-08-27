<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'comment',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
    * Get the user that owns the review.
    */

    public function user() {
        return $this->belongsTo( User::class );
    }

    /**
    * Get the product that owns the review.
    */

    public function product() {
        return $this->belongsTo( Product::class );
    }

    /**
    * Scope a query to only include approved reviews.
    */

    public function scopeApproved( $query ) {
        return $query->where( 'status', 'approved' );
    }

    /**
    * Check if review is approved.
    */

    public function isApproved() {
        return $this->status === 'approved';
    }
}
