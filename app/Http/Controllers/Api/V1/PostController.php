<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $all = $request->all();
        // $title = $request->input('title');
        $data = $request->only(['title', 'body']);
        // $author = $request->input('author');
        // return $author;
        $data['id'] = 100;

        // if (true) {
        //     return response()->json([
        //         'success' => false,
        //         // 'message' => 'You are not authenticated',
        //         'errors' => [
        //             ['title is required']
        //         ]
        //     ], 422);
        // }

        return response()
            ->json([
                'success' => true,
                'data' => $data
            ])
            ->setStatusCode(201)
            ->header('Test', 'Zura');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json([
            'id' => 1,
            'title' => 'Test',
            'body' => 'Long text',
            'author' => [
                'id' => 1,
                'name' => 'Zura'
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        // TODO
        return response()->json([], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return response()->noContent();
    }
}
