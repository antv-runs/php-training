<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *     title="Shop API",
 *     version="1.0.0"
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class OpenApi {}