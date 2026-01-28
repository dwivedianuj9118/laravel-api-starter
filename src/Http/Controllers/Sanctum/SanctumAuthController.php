<?php

namespace Dwivedianuj9118\ApiStarter\Http\Controllers\Sanctum;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Dwivedianuj9118\ApiStarter\Responses\ApiResponse;

/**
 * @OA\Tag(
 *     name="Sanctum Auth",
 *     description="SPA authentication using Laravel Sanctum"
 * )
 */
class SanctumAuthController
{
    /**
     * @OA\Post(
     *   path="/spa/login",
     *   tags={"Sanctum Auth"},
     *   summary="Login using Sanctum (SPA authentication)",
     *   description="Authenticate user and generate Sanctum token for SPA usage",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *       @OA\Property(property="password", type="string", format="password", example="secret")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Login successful",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="Login successful"),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         @OA\Property(property="token", type="string", example="1|abcdef123456"),
     *         @OA\Property(property="user", type="object")
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Invalid credentials"
     *   )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return ApiResponse::error('Invalid credentials', null, 401);
        }

        $user = $request->user();
        $token = $user->createToken('spa')->plainTextToken;

        return ApiResponse::success([
            'token' => $token,
            'user' => $user
        ], 'Login successful');
    }

    /**
     * @OA\Post(
     *   path="/spa/logout",
     *   tags={"Sanctum Auth"},
     *   summary="Logout Sanctum authenticated user",
     *   description="Invalidate all Sanctum tokens for the authenticated user",
     *   security={{"sanctum":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Logout successful",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="Logged out")
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthenticated"
     *   )
     * )
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }

        return ApiResponse::success(null, 'Logged out');
    }
}
