<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    public function productReviews($productId)
    {
        return response()->json(Review::where('product_id', $productId)->where('is_approved', true)->orderByDesc('created_at')->get());
    }

    public function featured()
    {
        return response()->json(Review::with('product')->where('is_approved', true)->where('rating', '>=', 4)->latest()->take(10)->get());
    }

    public function store(Request $request, $productId)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $review = Review::create([...$data, 'product_id' => $productId, 'is_approved' => false]);
        return response()->json($review, 201);
    }
}
