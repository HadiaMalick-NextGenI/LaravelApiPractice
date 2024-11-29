<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class JsonPlaceholderController extends Controller
{
    protected $client;

    public function __construct(){
        $this->client = new Client([
            'base_uri' => 'https://jsonplaceholder.typicode.com/',
            'timeout' => 5,
            //timeout error
            //'timeout' => 0.001,
        ]);
    }

    public function fetchPosts(){
        try{
            //invalid domain error
            // $client = new Client();
            // $response = $client->get('http://invalid.domain'); 

            //client error: not found
            // $client = new Client();
            // $response = $client->get('https://jsonplaceholder.typicode.com/nonexistent'); 

            //server error
            // $client = new Client();
            // $response = $client->get('https://httpstat.us/500'); 

            $response = $this->client->get('posts');
            $posts = json_decode($response->getBody()->getContents(), true);

            return ApiResponse::success(data: $posts, message:"All JsonPlaceHolder Posts");
        }catch(RequestException $e){
            return $this->handleGuzzleException($e);
        }
    }

    public function getPost($id){
        try{
            $response = $this->client->get("posts/{$id}");
            $post = json_decode($response->getBody()->getContents(), true);
            return ApiResponse::success( message:"Single JsonPlaceHolder Post", data: $post);
        }catch(RequestException $e){
            return $this->handleGuzzleException($e);
        }
    }

    public function createPost(Request $request){
        try{
            $response = $this->client->post('posts', [
                'json' => $request->all(),
            ]);

            $post = json_decode($response->getBody()->getContents(), true);
            return ApiResponse::success(code:201, message:"JsonPlaceHolder Post Created!", data: $post);
        }catch(RequestException $e){
            return $this->handleGuzzleException($e);
        }
    }

    public function updatePost(Request $request, $id){
        try{
            $response = $this->client->put("posts/{$id}", [
                'json' => $request->all(),
            ]);
    
            $post = json_decode($response->getBody()->getContents(), true);
            return ApiResponse::success(message: "JsonPlaceHolder Post Updated!", data: $post);
        }catch(RequestException $e){
            return $this->handleGuzzleException($e);
        }
    }
    
    public function deletePost($id){
        try{
            $this->client->delete("posts/{$id}");
            return ApiResponse::successNoData();
        }catch(RequestException $e){
            return $this->handleGuzzleException($e);
        }
    }

    private function handleGuzzleException(RequestException $e)
    {
        if ($e->hasResponse()) {
            $statusCode = $e->getResponse()->getStatusCode();

            if ($statusCode >= 400 && $statusCode < 500) {
                $message = "Client error: " . $e->getResponse()->getReasonPhrase();
                return ApiResponse::error($message, $statusCode);
            } elseif ($statusCode >= 500) {
                $message = "Server error: " . $e->getResponse()->getReasonPhrase();
                return ApiResponse::error($message, $statusCode);
            }
        }

        return ApiResponse::error("Something went wrong: " . $e->getMessage(), 500);
    }
}
