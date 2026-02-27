<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * Inputs that are never flashed for validation exceptions.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register exception reporting.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            try {
                Log::error('Uncaught exception: ' . get_class($e) . ' - ' . $e->getMessage(), [
                    'exception' => $e,
                ]);
            } catch (Throwable $ex) {
                // Prevent logging failure from crashing app
            }
        });
    }

    /**
     * Render exception into JSON response for API.
     */
    public function render($request, Throwable $e)
    {
        // Force JSON for API routes
        if ($request->expectsJson() || $request->is('api/*')) {

            // 422 - Validation
            if ($e instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors'  => $e->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // 401 - Unauthenticated
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], Response::HTTP_UNAUTHORIZED);
            }

            // 403 - Forbidden
            if ($e instanceof AuthorizationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden',
                ], Response::HTTP_FORBIDDEN);
            }

            // 404 - Model not found
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found',
                ], Response::HTTP_NOT_FOUND);
            }

            // 404 - Route not found
            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Route not found',
                ], Response::HTTP_NOT_FOUND);
            }

            // 500 - Database error
            if ($e instanceof QueryException) {

                Log::error('Database error', [
                    'sql' => $e->getSql(),
                    'bindings' => $e->getBindings(),
                    'message' => $e->getMessage(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Database error',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Other HTTP exceptions (405, 429...)
            if ($e instanceof HttpExceptionInterface) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: Response::$statusTexts[$e->getStatusCode()],
                ], $e->getStatusCode());
            }

            // Fallback - 500
            return response()->json([
                'success' => false,
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Server error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return parent::render($request, $e);
    }
}
