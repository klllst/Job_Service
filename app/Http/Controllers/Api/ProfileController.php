<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    #[OA\Get(
        path: "/api/profile/responses",
        summary: "Get all user's responses",
        tags: ["Profile"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 201, description: "Responses retrieved successfully"),
            new OA\Response(response: 400, description: "Validation error")
        ]
    )]
    public function selfResponses()
    {
        $user = Auth::guard('api')->user();

        return response()->json([
            'responses' => $user->responses,
        ]);
    }

    #[OA\Get(
        path: "/api/profile/ads",
        summary: "Get all user's ads",
        tags: ["Profile"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 201, description: "Responses ads successfully"),
            new OA\Response(response: 400, description: "Validation error")
        ]
    )]
    public function selfAds()
    {
        $user = Auth::guard('api')->user();

        return response()->json([
            'ads' => $user->employerAds,
        ]);
    }

    #[OA\Get(
        path: "/api/profile/reviews",
        summary: "Get all user's reviews",
        tags: ["Profile"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 201, description: "Reviews retrieved successfully"),
            new OA\Response(response: 400, description: "Validation error")
        ]
    )]
    public function selfReviews()
    {
        $user = Auth::guard('api')->user();

        return response()->json([
            'reviews' => $user->responds,
        ]);
    }
}
