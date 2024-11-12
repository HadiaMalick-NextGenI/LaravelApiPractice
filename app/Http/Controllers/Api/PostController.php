<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('user')->get();

        $postsResource = new PostCollection($posts);
        //$postsResource = PostResource::collection($posts);

        return ApiResponse::success(data: $postsResource, message:"All Posts");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request)
    {
        $user_id = Auth::user()->id;
        $img = $request->image;
        $path = public_path(). '/uploads';
        $imageName = $this->uploadImage($img, $path);

        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName,
            'is_published' => $request->is_published,
            'user_id' => $user_id,
        ]);

        $postResource = new PostResource($post);

        return ApiResponse::success( data: $postResource , code: 201, message:"Post created successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);

        if(!$post){
            return ApiResponse::error('Post not found',404);
        }

        $postResource = new PostResource($post);

        return ApiResponse::success(data: $postResource);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, string $id)
    {
        $user_id = Auth::user()->id;
        $post = Post::find($id);

        if(!$post){
            return ApiResponse::error('Post not found',404);
        }

        if($request->image != ''){
            $path = public_path(). '/uploads/';

            if($post->image != '' && $post->image != null){
                $old_file = $path . $post->image;
                if(file_exists($old_file)){
                    unlink($old_file);
                }
            }

            $img = $request->image;
            $imageName = $this->uploadImage($img, $path);

        }else{
            $imageName = $post->image;
        }

        $post->update([
            'title' => $request->title ?? $post->title,
            'description' => $request->description ?? $post->description,
            'image' => $imageName,
            'is_published' =>$request->is_published ?? $post->is_published,
            'user_id' => $user_id
        ]);

        $postResource = new PostResource($post);

        return ApiResponse::success(data: $postResource, message: "Post updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);
        
        if(!$post){
            return ApiResponse::error('Post not found',404);
        }

        $imgPath = public_path(). '/uploads/'. $post->image;
        unlink($imgPath);

        $post->delete();
        
        return ApiResponse::successNoData();
    }

    private function uploadImage($image, $path)
    {
        $extension = $image->getClientOriginalExtension();
        $imageName = time() . '.' . $extension;
        $image->move($path, $imageName);

        return $imageName;
    }

}
