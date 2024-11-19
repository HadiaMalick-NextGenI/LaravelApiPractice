<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$user = auth()->user();

        $token = '3|omuvZA0abJG1XsHTMImqenlViTvc8fVfJ4MxEYNh78280d6d';
        return view('v1/posts.index', compact('token'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('v1/posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return redirect()->route('posts.index')->with('success', 'Post created successfully!');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('v1.posts.view', ['postId' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Post::find($id);
        return view('v1.posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
