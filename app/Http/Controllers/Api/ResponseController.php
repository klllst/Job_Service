<?php

namespace App\Http\Controllers\Api;

use App\Enums\AdStatus;
use App\Enums\ResponseStatus;
use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Response;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class ResponseController extends Controller
{
    #[OA\Get(
        path: "/api/ads/{ad}/responses",
        summary: "Get all responses for an ad",
        tags: ["Responses"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "ad",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "Ad ID"
            ),
        ],
        responses: [
            new OA\Response(response: 201, description: "Responses retrieved su"),
            new OA\Response(response: 400, description: "Validation error")
        ]
    )]
    public function index(Ad $ad)
    {
        return response()->json([
            'responses' => $ad->responses,
        ]);
    }

    #[OA\Post(
        path: "/api/ads/{ad}/responses",
        summary: "Create a new response for an ad",
        tags: ["Responses"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "ad",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "Ad ID"
            ),
        ],
        responses: [
            new OA\Response(response: 201, description: "Response created successfully"),
            new OA\Response(response: 400, description: "Validation error")
        ]
    )]
    public function store(Ad $ad)
    {
        $response = $ad->responses()->create([
            'status' => ResponseStatus::Pending,
            'user_id' => Auth::guard('api')->id(),
        ]);

        return response()->json([
            'response' => $response,
        ]);
    }

    #[OA\Delete(
        path: "/api/ads/{ad}/responses/{response}",
        summary: "Delete a response for an ad",
        tags: ["Responses"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "ad",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "Ad ID"
            ),
            new OA\Parameter(
                name: "response",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "Response ID"
            ),
        ],
        responses: [
            new OA\Response(response: 201, description: "Response deleted successfully"),
            new OA\Response(response: 400, description: "Validation error")
        ]
    )]
    public function delete(Ad $ad, Response $response)
    {
        $response->delete();

        return response()->json([
            'message' => 'Response deleted',
        ]);
    }

    #[OA\Post(
        path: "/api/ads/{ad}/responses/{response}/accept",
        summary: "Accept a response for an ad",
        tags: ["Responses"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "ad",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "Ad ID"
            ),
            new OA\Parameter(
                name: "response",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "Response ID"
            ),
        ],
        responses: [
            new OA\Response(response: 201, description: "Response accepted"),
            new OA\Response(response: 400, description: "Validation error")
        ]
    )]
    public function accept(Ad $ad, Response $response)
    {
        $ad->update([
            'status' => AdStatus::InProgress,
            'worker_id' => $response->user_id,
        ]);

        $response->update([
            'status' => ResponseStatus::Accepted
        ]);

        return response()->json([
            'message' => 'Response accepted'
        ]);
    }

    #[OA\Post(
        path: "/api/ads/{ad}/responses/{response}/reject",
        summary: "Reject a response for an ad",
        tags: ["Responses"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "ad",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "Ad ID"
            ),
            new OA\Parameter(
                name: "response",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "Response ID"
            ),
        ],
        responses: [
            new OA\Response(response: 201, description: "Response rejected"),
            new OA\Response(response: 400, description: "Validation error")
        ]
    )]
    public function reject(Ad $ad, Response $response)
    {
        $ad->update([
            'status' => AdStatus::Published,
            'worker_id' => null,
        ]);

        $response->update([
            'status' => ResponseStatus::Accepted
        ]);

        return response()->json([
            'message' => 'Response rejected'
        ]);
    }
}
