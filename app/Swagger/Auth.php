<?php

namespace App\Swagger;

/**
     * @OA\Post(
     *     path="/api/signup",
     *     summary="User Signup",
     *     description="Registers a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created successfully"),
     *     @OA\Response(response=400, description="Bad Request")
     * )
*/

/**
 * @OA\Post(
 *     path="/api/login",
 *     summary="User Login",
 *     description="Logs in an existing user and returns a token",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
 *             @OA\Property(property="password", type="string", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful, returns user data and token"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
*/

/**
 * @OA\Post(
 *     path="/api/logout",
 *     summary="User Logout",
 *     description="Logs out the current user",
 *     tags={"Authentication"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=204,
 *         description="Logout successful"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */