<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['posts'] = Post::all();

        return ApiResponse::success($data, "All Posts");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request)
    {
        $img = $request->image;
        $path = public_path(). '/uploads';
        $imageName = $this->uploadImage($img, $path);

        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName,
        ]);

        return ApiResponse::success(data: $post, code: 201, message:"Post created successfully!");
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

        return ApiResponse::success(data: $post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, string $id)
    {
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
        ]);

        return ApiResponse::success(data: $post, message: "Post updated successfully!");
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
