<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(UserRegistrationRequest $request){

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ];
        
        $user = User::create($data);

        return ApiResponse::success(data: $user, code: 201, message:"Registration successful!");
    }

    public function login(UserLoginRequest $request){

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $authUser = Auth::user();
            $token = $authUser->createToken("API Token");

            return ApiResponse::success(
                data: $authUser,
                message:"Login successful!",
                token: $token,
                token_type: 'bearer'
            );
        }else{
            return ApiResponse::error('Credentials didn\'t match', 401);
        }
    }

    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->delete();

        return ApiResponse::successNoData();
    }
}
