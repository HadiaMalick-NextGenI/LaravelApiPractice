<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class JsonPlaceholderController extends Controller
{
    protected $client;

    public function __construct(){
        $this->client = new Client([
            'base_uri' => 'https://jsonplaceholder.typicode.com/',
            'timeout' => 2,
        ]);
    }

    public function fetchPosts(){
        try{
            $response = $this->client->get('posts');
            $posts = json_decode($response->getBody()->getContents(), true);

            return ApiResponse::success(data: $posts, message:"All JsonPlaceHolder Posts");
        }catch(RequestException $e){
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function getPost($id){
        try{
            $response = $this->client->get("posts/{$id}");
            $post = json_decode($response->getBody()->getContents(), true);
            return ApiResponse::success( message:"Single JsonPlaceHolder Post", data: $post);
        }catch(RequestException $e){
            return ApiResponse::error($e->getMessage(), 404);
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
            return ApiResponse::error($e->getMessage(), 500);
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
            return ApiResponse::error($e->getMessage(), 404);
        }
    }
    
    public function deletePost($id){
        try{
            $this->client->delete("posts/{$id}");
            return ApiResponse::successNoData();
        }catch(RequestException $e){
            return ApiResponse::error($e->getMessage(), 404);
        }
    }
}
