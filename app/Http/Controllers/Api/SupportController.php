<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class SupportController extends Controller
{
    #[OA\Get(
        path: "/api/supports",
        summary: "Get all user's supports",
        tags: ["Supports"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 201, description: "Supports retrieved successfully"),
            new OA\Response(response: 400, description: "Validation error")
        ]
    )]
    public function index()
    {
        $user = Auth::guard('api')->user();

        return response()->json([
            'supports' => $user->supports,
        ]);
    }

    #[OA\Post(
        path: "/api/supports",
        summary: "Create a new support",
        tags: ["Supports"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    required: ["name", "description", "cost"],
                    properties: [
                        new OA\Property(property: "theme", type: "string", description: "Support's name"),
                        new OA\Property(property: "description", type: "string", description: "Support's description"),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Support created successfully"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();

        $support = $user->supports()->create($request->all());

        return response()->json([
            'support' => $support,
        ]);
    }
}
