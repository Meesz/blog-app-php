<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

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
        ]) + ['user_id' => auth()->id()]);

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
        // Only count view if IP hasn't viewed in last 24 hours
        $ipAddress = request()->ip();
        $hasViewed = $post->views()
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>=', now()->subHours(24))
            ->exists();

        if (!$hasViewed) {
            $post->addView();
        }

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required'
        ]);

        $post->update($validated);

        return redirect()->route('posts.index')
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully.');
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->all() as $item) {
            Post::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['status' => 'success']);
    }
}
