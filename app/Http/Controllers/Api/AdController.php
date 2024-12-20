<?php

namespace App\Http\Controllers\Api;

use App\Enums\AdStatus;
use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class AdController extends Controller
{
    #[OA\Get(
        path: "/api/ads",
        summary: "Retrieve a list of ads",
        tags: ["Ads"],
        responses: [
            new OA\Response(response: 200, description: "Ads retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function index()
    {
        $ads = Ad::where('status', AdStatus::Published)
            ->get();

        return response()->json([
            'ads' => $ads
        ]);
    }

    #[OA\Post(
        path: "/api/ads",
        summary: "Create a new ad",
        tags: ["Ads"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    required: ["name", "description", "cost"],
                    properties: [
                        new OA\Property(property: "name", type: "string", description: "Ad's name"),
                        new OA\Property(property: "description", type: "string", description: "Ad's description"),
                        new OA\Property(property: "cost", type: "double", description: "Ad's cost"),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Ad created successfully"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();

        if ($user->balance < $request->input('cost')) {
            return response()->json([
                'cost' => 'Insufficient money',
            ], 400);
        }

        $data = $request->all();
        $data['employer_id'] = $user->id;

        $ad = Ad::create($data);

        $user->update([
            'balance' => $user->balance - $ad->cost,
        ]);

        return response()->json([
            'ad' => $ad
        ]);
    }

    #[OA\Put(
        path: "/api/ads/{id}",
        summary: "Update an existing ad",
        tags: ["Ads"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "Ad ID"
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "name", type: "string", description: "Ad's name"),
                        new OA\Property(property: "description", type: "string", description: "Ad's description"),
                        new OA\Property(property: "cost", type: "number", description: "Ad's cost")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Ad updated successfully"),
            new OA\Response(response: 400, description: "Insufficient funds"),
            new OA\Response(response: 404, description: "Ad not found")
        ]
    )]
    public function update(Request $request, Ad $ad)
    {
        $user = Auth::guard('api')->user();

        if ($user->balance < $request->input('cost')) {
            return response()->json([
                'cost' => 'Недостаточно средств',
            ], 400);
        }
        $oldBalance = $user->balance + $ad->cost;

        $ad->update($request->all());

        $user->update([
            'balance' => $oldBalance - $ad->cost,
        ]);

        return response()->json([
            'ad' => $ad
        ]);
    }

    #[OA\Delete(
        path: "/api/ads/{id}",
        summary: "Delete an ad",
        tags: ["Ads"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "Ad ID"
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Ad deleted successfully"),
            new OA\Response(response: 404, description: "Ad not found")
        ]
    )]
    public function delete(Ad $ad)
    {
        $ad->delete();

        return response()->json([
            'message' => 'Ad deleted'
        ]);
    }

    #[OA\Post(
        path: "/api/ads/{id}",
        summary: "Complete the ad",
        tags: ["Ads"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "Ad ID"
            )
        ],
        responses: [
            new OA\Response(response: 201, description: "Ad completed successfully"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function complete(Ad $ad)
    {
        $ad->update([
            'status' => AdStatus::Completed
        ]);

        $worker = $ad->worker;

        $newBalance = $worker->balance + $ad->cost;

        $worker->update([
            'balance' => $newBalance,
        ]);

        return response()->json([
            'ad' => $ad
        ]);
    }
}
