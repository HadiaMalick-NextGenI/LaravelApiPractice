<?php

namespace App\Http\Controllers\Api\V2;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="V2 Profile",
 *     description="Endpoints for managing user profiles in version 2 of the API"
 * )
 */

class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v2/profile",
     *     summary="Insert Profile Data",
     *     description="Insert or update profile data for the authenticated user",
     *     tags={"V2 Profile"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="bio", type="string", example="This is a sample bio.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile data inserted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile data inserted successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/UserResourceV2"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthorized for this action")
     *         )
     *     )
     * )
     */
    public function insertProfileData(Request $request)
    {
        if (!$request->user()->tokenCan('registration')) {
            return response()->json(['message' => 'Unauthorized for this action'], 403);
        }

        $profileData = $request->validate([
            'bio' => 'nullable|string',
        ]);

        $request->user()->update([
            'bio' => $profileData['bio'],
        ]);

        return ApiResponse::success(data: $request->user(),message: 'Profile data inserted successfully');
    }
}
