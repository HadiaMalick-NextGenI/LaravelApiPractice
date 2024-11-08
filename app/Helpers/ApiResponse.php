<?php

namespace App\Helpers;

class ApiResponse{
    public static function success($data, $message = "", $metadata = [], $links = [], 
    $code = 200, $token = null, $token_type = null){
        $response = [
            'status' => 'success',
            'data' => $data,
            'message' => $message,
            'metadata' => $metadata,
            'links' => $links
        ];
        if($token){
            $response['token'] = $token;
            $response['token_type'] = $token_type;
        }
        return response()->json($response, $code);
    }

    public static function successNoData(){
        $code = 204;
        $response = [
            'status' => 'success',
        ];
        return response()->json($response, $code);
    }

    public static function error($message, $code = 400, $details = []){
        return response()->json([
            'status' => 'error',
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details
            ],
            'metadata' => ['timestamp' => now(),]
        ], $code);
    }
}