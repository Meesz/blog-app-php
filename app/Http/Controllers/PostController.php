<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Activity;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::orderBy('order', 'asc')->paginate(6);
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $post = Post::create($request->validate([
            'title' => 'required',
            'content' => 'required',
        ]));

        Activity::create([
            'description' => "Created post: {$post->title}",
            'type' => 'post_created',
            'subject_type' => Post::class,
            'subject_id' => $post->id,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('posts.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $post->update($request->validate([
            'title' => 'required',
            'content' => 'required',
        ]));

        return redirect()->route('posts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index');
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->all() as $item) {
            Post::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['status' => 'success']);
    }
}
