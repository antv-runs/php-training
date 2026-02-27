<?php

namespace App\Traits;
use Symfony\Component\HttpFoundation\Response;

trait HttpResponses
{
    protected function success($data = null, string $message = null, int $code = Response::HTTP_OK)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
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
