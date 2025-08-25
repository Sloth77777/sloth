<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

abstract class Controller
{
    public function getErrorsResponse($errors = [], int $code = 422): JsonResponse
    {
        return Response::json(['errors' => $errors], $code, [], JSON_UNESCAPED_UNICODE);
    }

    public function getSuccessResponse(array $data = [], int $code = 200): JsonResponse
    {
        return Response::json($data, $code);
    }
}
