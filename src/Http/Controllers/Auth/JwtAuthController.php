<?php

namespace Dwivedianuj9118\ApiStarter\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Dwivedianuj9118\ApiStarter\Responses\ApiResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Dwivedianuj9118\ApiStarter\Support\AuthModel;

/**
 * @OA\Tag(
 *     name="JWT Auth",
 *     description="JWT Authentication endpoints"
 * )
 */
class JwtAuthController
{
    /**
     * @OA\Post(
     *   path="/auth/register",
     *   tags={"JWT Auth"},
     *   summary="Register a new user",
     *   description="Register a user and generate JWT token",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name","email","password"},
     *       @OA\Property(property="name", type="string", example="John Doe"),
     *       @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *       @OA\Property(property="password", type="string", format="password", example="secret123")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="User registered successfully",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="User registered"),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         @OA\Property(property="token", type="string"),
     *         @OA\Property(property="user", type="object")
     *       )
     *     )
     *   ),
     *   @OA\Response(response=422, description="Validation error"),
     *   @OA\Response(response=500, description="JWT secret not configured")
     * )
     */
    public function register(Request $request)
    {
        if (!config('jwt.secret')) {
            return ApiResponse::error(
                'JWT secret is not configured. Please run: php artisan jwt:secret',
                null,
                500
            );
        }

        $model = AuthModel::model();
        $table = (new $model)->getTable();

        $request->validate([
            'name' => 'required|string',
            'email' => "required|email|unique:$table,email",
            'password' => 'required|min:6'
        ]);

        $user = $model::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return ApiResponse::success([
            'user' => $user,
            'token' => $token
        ], 'User registered');
    }

    /**
     * @OA\Post(
     *   path="/auth/login",
     *   tags={"JWT Auth"},
     *   summary="Login user",
     *   description="Authenticate user and generate JWT token",
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
     *         @OA\Property(property="token", type="string"),
     *         @OA\Property(property="user", type="object")
     *       )
     *     )
     *   ),
     *   @OA\Response(response=401, description="Invalid credentials"),
     *   @OA\Response(response=500, description="JWT secret not configured")
     * )
     */
    public function login(Request $request)
    {
        if (!config('jwt.secret')) {
            return ApiResponse::error(
                'JWT secret is not configured. Please run: php artisan jwt:secret',
                null,
                500
            );
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!$token = JWTAuth::attempt($credentials)) {
            return ApiResponse::error('Invalid credentials', null, 401);
        }

        return ApiResponse::success([
            'token' => $token,
            'user' => auth()->user()
        ], 'Login successful');
    }

    /**
     * @OA\Post(
     *   path="/auth/refresh",
     *   tags={"JWT Auth"},
     *   summary="Refresh JWT token",
     *   description="Generate a new JWT token using a valid token",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Token refreshed successfully",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         @OA\Property(property="token", type="string")
     *       )
     *     )
     *   ),
     *   @OA\Response(response=401, description="Token invalid or expired")
     * )
     */
    public function refresh()
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());
        } catch (\Exception $e) {
            return ApiResponse::error('Token invalid or expired', null, 401);
        }

        return ApiResponse::success([
            'token' => $token
        ], 'Token refreshed');
    }

    /**
     * @OA\Post(
     *   path="/auth/logout",
     *   tags={"JWT Auth"},
     *   summary="Logout user",
     *   description="Invalidate the current JWT token",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Logout successful",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="Logged out")
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return ApiResponse::success(null, 'Logged out');
    }
}
