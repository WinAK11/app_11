<?php

namespace App\Models;
use App\Models\Ebook;
use App\Models\Review;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    public function category() {
        return $this->belongsTo( Category::class, 'category_id' );
    }

    public function author() {
        return $this->belongsTo( Author::class, 'author_id', 'id' );
    }

    /**
     * Get all reviews for the product.
     */
    public function reviews() {
        return $this->hasMany( Review::class );
    }

    /**
    * Get approved reviews for the product.
    */

    public function approvedReviews() {
        return $this->reviews()->approved()->with( 'user' );
    }

    /**
    * Check if a user has reviewed this product.
    */

    public function hasUserReviewed( $userId ) {
        return $this->reviews()->where( 'user_id', $userId )->exists();
    }

    /**
    * Get the average rating for the product.
    */

    public function getAverageRatingAttribute() {
        return round( $this->approvedReviews()->avg( 'rating' ), 1 ) ?: 0;
    }

    /**
    * Get the total number of approved reviews.
    */

    public function getReviewsCountAttribute() {
        return $this->approvedReviews()->count();
    }

    /**
    * Get rating distribution ( count of each star rating ).
    */

    public function getRatingDistributionAttribute() {
        $distribution = [];
        for ( $i = 5; $i >= 1; $i-- ) {
            $distribution[ $i ] = $this->approvedReviews()->where( 'rating', $i )->count();
        }
        return $distribution;
    }

    /**
    * Get percentage for each star rating.
    */

    public function getRatingPercentagesAttribute() {
        $totalReviews = $this->reviews_count;
        $percentages = [];

        if ( $totalReviews > 0 ) {
            foreach ( $this->rating_distribution as $stars => $count ) {
                $percentages[ $stars ] = round( ( $count / $totalReviews ) * 100 );
            }
        } else {
            for ( $i = 5; $i >= 1; $i-- ) {
                $percentages[ $i ] = 0;
            }
        }

        return $percentages;
    }

    /**
    * Get star display for average rating.
    */

    public function getStarDisplayAttribute() {
        $rating = $this->average_rating;
        $fullStars = floor( $rating );
        $halfStar = ( $rating - $fullStars ) >= 0.5 ? 1 : 0;
        $emptyStars = 5 - $fullStars - $halfStar;

        return [
            'full' => $fullStars,
            'half' => $halfStar,
            'empty' => $emptyStars,
            'rating' => $rating
        ];
    }

}
