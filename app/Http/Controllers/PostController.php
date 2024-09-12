<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

class PostController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index()
    {
        // Fetch published posts with user and tags, paginated
        $posts = Post::with('user', 'tags')
            ->where('is_published', true)
            ->where('published_at', '<=', now()) // Only show posts that are scheduled to be published
            ->latest()
            ->paginate(10);

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        $tags = Tag::all();
        return view('posts.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'tags' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $post = Auth::user()->posts()->create($validatedData);

        if (isset($validatedData['tags'])) {
            $post->tags()->sync($validatedData['tags']);
        }

        // Log the activity
        activity()->performedOn($post)->causedBy(Auth::user())->log('created');

        return redirect()->route('posts.show', $post)->with('success', 'Post created successfully.');
    }

    public function show(Post $post)
    {
        // Check if the post is published or if the user is authorized to view it
        if (!$post->is_published && (!Auth::check() || !Auth::user()->can('update', $post))) {
            abort(404);
        }

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        $tags = Tag::all();
        return view('posts.edit', compact('post', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'tags' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $post->update($validatedData);

        if (isset($validatedData['tags'])) {
            $post->tags()->sync($validatedData['tags']);
        }

        // Log the activity
        activity()->performedOn($post)->causedBy(Auth::user())->log('updated');

        return redirect()->route('posts.show', $post)->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        // Log the activity
        activity()->performedOn($post)->causedBy(Auth::user())->log('deleted');

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }
}
