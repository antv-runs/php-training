<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait HttpResponses
{
    protected function success($data = null, string $message = null, int $code = Response::HTTP_OK)
    {
        // Case 1: Resource collection (usually paginated list)
        if ($data instanceof ResourceCollection) {
            return $data->additional([
                'success' => true,
                'message' => $message,
            ]);
        }

        // Case 2: Single object or simple data
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    protected function error(string $message = null, int $code = Response::HTTP_BAD_REQUEST, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}
