<?php

namespace App\Http\Controllers;

use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Blog::orderBy('id', 'desc')->paginate(10);

        return BlogResource::collection($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "title" => "required",
            "content" => "required"
        ]);

        if($validator->fails()){
            return response()->json([
                "status" => false,
                "message" => "validation error",
                "errors" => $validator->errors()
            ]);
        }

        $data = Blog::create($request->all());

        return response()->json([
            "status" => true,
            "message" => "Blog successfully created",
            "data" => new BlogResource($data)
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Blog::findOrFail($id);

        return new BlogResource($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(string $id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = Blog::findOrFail($id);

        $validator = Validator::make($request->all(), [
            "title" => "required",
            "content" => "required"
        ]);

        if($validator->fails()){
            return response()->json([
                "status" => false,
                "message" => "validation error",
                "errors" => $validator->errors()
            ]);
        }

        $data->title = $request->input('title');
        $data->content = $request->input('content');
        $data->save();

        return response()->json([
            "status" => true,
            "message" => "Blog successfully updated",
            "data" => new BlogResource($data)
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Blog::findOrFail($id);

        if($data->delete()){
            return response()->json([
                "status" => true,
                "message" => "Blog successfully deleted.",
                "data" => new BlogResource($data)
            ], 200);
        }

        return null;
    }
}
