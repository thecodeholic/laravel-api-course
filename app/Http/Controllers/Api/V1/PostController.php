<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $posts = Post::where('author_id', $user->id)->paginate();
        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();

        $data['author_id'] = Auth::id();
        $post = Post::create($data);

        return response()->json(new PostResource($post), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $user = Auth::user();
        abort_if($post->author_id != $user->id, 403, 'Access denied');

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $user = Auth::user();
        abort_if($post->author_id != $user->id, 403, 'Access denied');

        $data = $request->validated();

        $post->update($data);

        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $user = Auth::user();
        abort_if($post->author_id != $user->id, 403, 'Access denied');

        $post->delete();
        return response()->noContent();
    }
}
