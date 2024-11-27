<?php

namespace App\Http\Controllers\Api\V2;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function subscribe(Request $request){
        $validatedData = $request->validate([
            'email' => 'required|email|unique:subscribers,email',
        ]);

        $subscriber = Subscriber::create($validatedData);

        return ApiResponse::success(data: $subscriber, code: 201, message: "Subscription successful!");
    }

    public function unsubscribe(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|exists:subscribers,email',
        ]);

        Subscriber::where('email', $validatedData['email'])->delete();

        return ApiResponse::successNoData();
    }
}
