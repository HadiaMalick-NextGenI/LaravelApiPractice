<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Resources\V1\UserResource;
use App\Jobs\LogActivityJob;
use App\Jobs\SendWelcomeEmailJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;

/**
 * @OA\Schema(
 *     schema="UserResourceV1",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-11-12T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-11-12T10:30:00Z")
 * )
 * * @OA\Tag(
 *     name="V1 Authentication",
 *     description="Endpoints for user authentication in version 1 of the API"
 * )
 */

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/auth/signup",
     *     summary="User Signup",
     *     description="Register a new user in the system",
     *     tags={"V1 Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Registration successful!"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/UserResourceV1"
     *             ),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The provided data is invalid."),
     *             @OA\Property(property="status", type="integer", example=400)
     *         )
     *     )
     * )
     */
    public function signup(UserRegistrationRequest $request){

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ];
        
        $user = User::create($data);

        $registrationToken = $user->createToken('registration_token', ['registration']);

        // Log::info('Dispatching SendWelcomeEmailJob for user: ' . $user->email);
        // dispatch(new SendWelcomeEmailJob($user));
        // Log::info('SendWelcomeEmailJob dispatched.');
        
        //SendWelcomeEmailJob::dispatchAfterResponse($user);

        Bus::chain([
            new SendWelcomeEmailJob($user),
            new LogActivityJob($user),
        ])->dispatch();

        $userResource = new UserResource($user);
        return ApiResponse::success(
            data: $userResource, 
            code: 201, 
            message:"Registration successful!",
            token: $registrationToken,
            token_type: 'bearer',
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="User Login",
     *     description="Logs in an existing user and returns a token",
     *     tags={"V1 Authentication"},
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
     *         description="Login successful, returns user data and token",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful!"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/UserResourceV1"
     *             ),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         ),
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Invalid request data")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Unauthorized")
     *          )
     *      ),
     * )
    */
    public function login(UserLoginRequest $request){

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $authUser = Auth::user();
            $token = $authUser->createToken('access_token', ['access']);

            $userResource = new UserResource($authUser);
            return ApiResponse::success(
                data: $userResource,
                message:"Login successful!",
                token: $token,
                token_type: 'bearer'
            );
        }else{
            return ApiResponse::error('Credentials didn\'t match', 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="User Logout",
     *     description="Logs out the current user",
     *     tags={"V1 Authentication"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=204,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User logged out successfully"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 example=204
     *             ),
     *         ),
     *     ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Unauthorized")
     *          )
     *      ),
     * )
     */
    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->delete();

        return ApiResponse::successNoData();
    }

}
