<?php

namespace Dwivedianuj9118\ApiStarter\Exceptions;

use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Dwivedianuj9118\ApiStarter\Responses\ApiResponse;

class ApiExceptionHandler
{
    public static function handle(Throwable $e)
    {
        // Validation errors
        if ($e instanceof ValidationException) {
            return ApiResponse::error(
                'Validation failed',
                $e->errors(),
                422
            );
        }

        // Authentication errors
        if ($e instanceof AuthenticationException) {
            return ApiResponse::error(
                'Unauthenticated',
                null,
                401
            );
        }

        // HTTP exceptions
        if ($e instanceof HttpException) {
            return ApiResponse::error(
                $e->getMessage() ?: 'HTTP Error',
                null,
                $e->getStatusCode()
            );
        }

        // Fallback (500)
        return ApiResponse::error(
            config('app.debug') ? $e->getMessage() : 'Server error',
            null,
            500
        );
    }
}
