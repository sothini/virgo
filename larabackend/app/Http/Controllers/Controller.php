<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Laravel API Documentation",
    description: "API documentation for the Laravel application",
    contact: new OA\Contact(
        email: "kaongallostan@gmail.com"
    ),
    license: new OA\License(
        name: "Apache 2.0",
        url: "http://www.apache.org/licenses/LICENSE-2.0.html"
    )
)]
#[OA\PathItem(path: "/api")]
abstract class Controller
{
    //
}
