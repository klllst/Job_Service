<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[
    OA\Info(version: "1.0.0", description: "job service api", title: "Job Service Documentation"),
    OA\Server(url: 'http://127.0.0.1:8000', description: "local server"),
    OA\SecurityScheme(
        securityScheme: "bearerAuth",
        type: "http",
        scheme: "bearer",
        bearerFormat: "JWT"
    ),
]
abstract class Controller
{
    //
}
