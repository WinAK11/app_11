<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, $productId)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'You must be logged in to leave a review.'], 401);
            }
            return redirect()->route('login')->with('error', 'You must be logged in to leave a review.');
        }

        try {
            $product = Product::findOrFail($productId);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Product not found.'], 404);
            }
            return back()->with('error', 'Product not found.');
        }

        // Check if user has already reviewed this product
        if ($product->hasUserReviewed(Auth::id())) {
            if ($request->ajax()) {
                return response()->json(['error' => 'You have already reviewed this product.'], 422);
            }
            return back()->with('error', 'You have already reviewed this product.');
        }

        // Log the request data for debugging
        Log::info('Review submission data', [
            'rating' => $request->rating,
            'comment' => $request->comment,
            'product_id' => $productId,
            'user_id' => Auth::id()
        ]);

        // Validate the request
        try {
            $validatedData = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string|min:10|max:1000',
            ]);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        }

        try {
            // Create the review
            $review = Review::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'rating' => $validatedData['rating'],
                'comment' => $validatedData['comment'],
                'status' => 'approved', // Auto-approve for now
            ]);

            if ($request->ajax()) {
                $review->load('user');
                return response()->json([
                    'success' => true,
                    'message' => 'Your review has been submitted successfully!',
                    'review' => [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'user_name' => $review->user->name,
                        'created_at' => $review->created_at->format('F d, Y'),
                        'can_delete' => true
                    ],
                    'product_stats' => [
                        'average_rating' => $product->fresh()->average_rating,
                        'reviews_count' => $product->fresh()->reviews_count,
                        'rating_distribution' => $product->fresh()->rating_distribution,
                        'rating_percentages' => $product->fresh()->rating_percentages,
                    ]
                ]);
            }

            return back()->with('success', 'Your review has been submitted successfully!');
        } catch (\Exception $e) {
            Log::error('Review creation error', [
                'error' => $e->getMessage(),
                'product_id' => $productId,
                'user_id' => Auth::id()
            ]);
            
            if ($request->ajax()) {
                return response()->json(['error' => 'Failed to submit review. Please try again.'], 500);
            }
            return back()->with('error', 'Failed to submit review. Please try again.');
        }
    }

    /**
     * Get reviews for a product (AJAX endpoint)
     */
    public function getReviews(Request $request, $productId)
    {
        try {
            $product = Product::findOrFail($productId);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        $perPage = $request->get('per_page', 5);
        $reviews = $product->approvedReviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'reviews' => $reviews->map(function($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'user_name' => $review->user->name ?? 'Anonymous',
                    'created_at' => $review->created_at->format('F d, Y'),
                    'can_delete' => Auth::check() && (Auth::id() === $review->user_id || Auth::user()->usertype === 'ADM'),
                ];
            }),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
            'product_stats' => [
                'average_rating' => $product->average_rating,
                'reviews_count' => $product->reviews_count,
                'rating_distribution' => $product->rating_distribution,
                'rating_percentages' => $product->rating_percentages,
            ]
        ]);
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Request $request, $reviewId)
    {
        try {
            $review = Review::findOrFail($reviewId);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Review not found.'], 404);
            }
            return back()->with('error', 'Review not found.');
        }

        // Check if the user owns this review or is admin
        if (Auth::id() !== $review->user_id && Auth::user()->usertype !== 'ADM') {
            if ($request->ajax()) {
                return response()->json(['error' => 'You can only delete your own reviews.'], 403);
            }
            return back()->with('error', 'You can only delete your own reviews.');
        }

        try {
            $productId = $review->product_id;
            $review->delete();

            if ($request->ajax()) {
                $product = Product::find($productId);
                return response()->json([
                    'success' => true,
                    'message' => 'Review deleted successfully.',
                    'product_stats' => [
                        'average_rating' => $product->average_rating ?? 0,
                        'reviews_count' => $product->reviews_count ?? 0,
                        'rating_distribution' => $product->rating_distribution ?? [],
                        'rating_percentages' => $product->rating_percentages ?? [],
                    ]
                ]);
            }

            return back()->with('success', 'Review deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Review deletion error', [
                'error' => $e->getMessage(),
                'review_id' => $reviewId
            ]);
            
            if ($request->ajax()) {
                return response()->json(['error' => 'Failed to delete review.'], 500);
            }
            return back()->with('error', 'Failed to delete review.');
        }
    }

    /**
     * Update review status (for admin)
     */
    public function updateStatus(Request $request, $reviewId)
    {
        if (!Auth::check() || Auth::user()->usertype !== 'ADM') {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        try {
            $validatedData = $request->validate([
                'status' => 'required|in:pending,approved,rejected'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            $review = Review::findOrFail($reviewId);
            $review->update(['status' => $validatedData['status']]);

            return response()->json([
                'success' => true,
                'message' => 'Review status updated successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Review status update error', [
                'error' => $e->getMessage(),
                'review_id' => $reviewId
            ]);
            
            return response()->json(['error' => 'Failed to update review status.'], 500);
        }
    }
}