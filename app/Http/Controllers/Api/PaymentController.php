<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    #[OA\Post(
        path: "/api/payments/replenish",
        summary: "Replenish balance",
        tags: ["Payment"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    required: ["sum"],
                    properties: [
                        new OA\Property(property: "sum", type: "int", description: "Money sum"),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Successful replenishment"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function replenish(Request $request)
    {
        $user = Auth::guard('api')->user();

        $user->update([
            'balance' => $user->balance + $request->input('sum')
        ]);

        return response()->json([
            'message' => 'Successful replenishment',
        ]);
    }

    #[OA\Post(
        path: "/api/payments/withdraw",
        summary: "Withdraw from balance",
        tags: ["Payment"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    required: ["sum"],
                    properties: [
                        new OA\Property(property: "sum", type: "int", description: "Money sum"),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Successful withdraw"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function withdraw(Request $request)
    {
        $user = Auth::guard('api')->user();

        if ($user->balance < $request->input('sum')) {
            return response()->json([
                'sum' => 'Insufficient money',
            ], 400);
        }

        $user->update([
            'balance' => $user->balance - $request->input('sum')
        ]);

        return response()->json([
            'message' => 'Successful withdraw',
        ]);
    }
}
