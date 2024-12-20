<?php

namespace App\Helpers;

class ApiResponse{
    public static function success($code = 200, $message = "", $data, $metadata = [], $links = [], $token = null, $token_type = null){

        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'metadata' => $metadata,
            'links' => $links
        ];
        if($token){
            $response['token'] = $token;
            $response['token_type'] = $token_type;
        }

        return response()->json(data: $response, status: $code);
    }

    public static function successNoData(){
        $code = 204;
        $response = [
            'status' => 'success',
        ];
        return response()->json(data: $response, status: $code);
    }

    public static function error($message, $code = 400, $details = []){
        return response()->json(data: [
            'status' => 'error',
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details
            ],
            'metadata' => ['timestamp' => now(),]
        ], status: $code);
    }
}