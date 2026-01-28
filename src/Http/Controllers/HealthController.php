<?php

namespace Dwivedianuj9118\ApiStarter\Http\Controllers;

use Dwivedianuj9118\ApiStarter\Responses\ApiResponse;

/**
 * @OA\Get(
 *   path="/health",
 *   tags={"Health"},
 *   summary="API health check",
 *   @OA\Response(
 *     response=200,
 *     description="API is healthy"
 *   )
 * )
 */
class HealthController
{
    public function index()
    {
        return ApiResponse::success([
            'status' => 'OK',
            'timestamp' => now(),
        ], 'API is healthy');
    }
}
