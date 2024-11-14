<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Resources\V1\PostCollection;
use App\Http\Resources\V1\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *     schema="PostResourceV1",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="My Blog"),
 *     @OA\Property(property="description", type="string", example="This is the description of the post."),
 *     @OA\Property(property="image", type="string", example="image.jpg"),
 *     @OA\Property(property="is_published", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-11-12T00:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-11-12T00:00:00"),
 *     @OA\Property(property="user_id", type="integer", example=1)
 * )
 * 
 * @OA\Tag(
 *     name="V1 Posts",
 *     description="Operations for managing posts in version 1 of the API"
 * )
 */

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/posts",
     *     summary="Get all posts",
     *     description="Retrieve a list of all posts with associated user details",
     *     tags={"V1 Posts"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="A list of posts",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/PostResourceV1")
     *             ),
     *             @OA\Property(property="message", type="string", example="All Posts"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An error occurred while retrieving posts"),
     *             @OA\Property(property="status", type="integer", example=500)
     *         )
     *     )
     * )
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('user')->get();

        $postsResource = new PostCollection($posts);

        return ApiResponse::success(data: $postsResource, message:"All Posts");
    }

    /**
     * @OA\Post(
     *      path="/api/v1/posts",
     *      tags={"V1 Posts"},
     *      security={{ "bearerAuth": {} }},
     *      summary="Create a new post",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title", "description", "image", "is_published"},
     *              @OA\Property(property="description", type="string", example="My Post"),
     *              @OA\Property(property="title", type="string", example="This is the description of my post"),
     *              @OA\Property(property="image", type="string", format="binary", example="image.png"),
     *              @OA\Property(property="is_published", type="boolean", example=true)
     *          )
     *      ),
     *     @OA\Response(
     *              response=201,
     *         description="Post created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post created successfully!"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/PostResourceV1"
     *                 ),
     *          ),
     *     ),
     *      @OA\Response(
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
     * @OA\Get(
     *     path="/api/v1/posts/{id}",
     *     summary="Get a single post",
     *     description="Retrieve a post by its ID",
     *     tags={"V1 Posts"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post",
     *         @OA\Schema( type="integer" )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             ref = "#/components/schemas/PostResourceV1"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Post not found"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 example=404
     *             )
     *         )
     *     )
     * )
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        if(!$post->id){
            return ApiResponse::error('Post not found',404);
        }

        $postResource = new PostResource($post);

        return ApiResponse::success(data: $postResource);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/posts/{id}",
     *     summary="Update a post",
     *     description="Update an existing post by its ID",
     *     tags={"V1 Posts"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"title", "description", "is_published"},
     *             @OA\Property(property="title", type="string", example="Updated Title"),
     *             @OA\Property(property="description", type="string", example="Updated description of the post."),
     *             @OA\Property(property="image", type="string", format="binary", description="New image file for the post"),
     *             @OA\Property(property="is_published", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post successfully updated",
     *         @OA\JsonContent(
     *             ref = "#/components/schemas/PostResourceV1",
     *             @OA\Property(property="message", type="string", example="Post updated successfully!"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Post not found"),
     *             @OA\Property(property="status", type="integer", example=404)
     *         )
     *     )
     * )
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        $user_id = Auth::user()->id;

        if(!$post->id){
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
     * @OA\Delete(
     *     path="/api/v1/posts/{id}",
     *     summary="Delete a post",
     *     description="Delete a post by its ID",
     *     security = {{ "bearerAuth": {} }},
     *     tags={"V1 Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Post successfully deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Post deleted successfully"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 example=204
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Post not found"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 example=404
     *             )
     *         )
     *     )
     * )
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if(!$post->id){
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
