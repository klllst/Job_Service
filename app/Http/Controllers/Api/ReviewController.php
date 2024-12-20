<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class ReviewController extends Controller
{
    #[OA\Post(
        path: "/api/ads/{ad}/reviews",
        summary: "Create a new review",
        tags: ["Reviews"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "ad",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "ID of the ad"
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    required: ["description", "score"],
                    properties: [
                        new OA\Property(property: "description", type: "string", description: "Review description"),
                        new OA\Property(property: "score", type: "integer", description: "Review score"),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Review created successfully"),
            new OA\Response(response: 400, description: "Validation error")
        ]
    )]
    public function store(Request $request, Ad $ad)
    {
        $data = $request->all();
        $data['responder_id'] = User::where('id', $ad->worker_id)->value('id');
        $data['reviewer_id'] = Auth::guard('api')->id();

        $review = $ad->review()->create($data);

        $responder = $ad->employer;

        $rating = Review::where('responder_id', $responder->id)->avg('score');
        $responder->update([
            'rating' => $rating,
        ]);

        return response()->json([
            'review' => $review,
        ]);
    }

    #[OA\Delete(
        path: "/api/ads/{ad}/reviews/{review}",
        summary: "Delete a review",
        tags: ["Reviews"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "ad",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "ID of the ad"
            ),
            new OA\Parameter(
                name: "review",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "ID of the review to delete"
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Review deleted successfully"),
            new OA\Response(response: 404, description: "Review not found")
        ]
    )]
    public function delete(Ad $ad, Review $review)
    {
        $review->delete();

        return response()->json([
            'message' => 'Review deleted'
        ]);
    }
}
