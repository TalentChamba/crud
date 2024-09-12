<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class TagController extends BaseController
{
    // Constructor: Apply authentication middleware to all methods except index and show
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    // Display a list of all tags with the count of associated posts
    public function index()
    {
        $tags = Tag::withCount('posts')->get();
        return view('tags.index', compact('tags'));
    }

    // Show the form for creating a new tag
    public function create()
    {
        return view('tags.create');
    }

    // Store a newly created tag in the database
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|unique:tags|max:255',
        ]);

        // Create a new tag with the validated data
        Tag::create($validatedData);

        // Redirect to the tags index page with a success message
        return redirect()->route('tags.index')->with('success', 'Tag created successfully.');
    }

    // Display the specified tag and its associated published posts
    public function show(Tag $tag)
    {
        $posts = $tag->posts()->where('is_published', true)->latest()->paginate(10);
        return view('tags.show', compact('tag', 'posts'));
    }

    // Show the form for editing the specified tag
    public function edit(Tag $tag)
    {
        return view('tags.edit', compact('tag'));
    }

    // Update the specified tag in the database
    public function update(Request $request, Tag $tag)
    {
        // Validate the incoming request data, allowing the current tag's name
        $validatedData = $request->validate([
            'name' => 'required|unique:tags,name,' . $tag->id . '|max:255',
        ]);

        // Update the tag with the validated data
        $tag->update($validatedData);

        // Redirect to the tags index page with a success message
        return redirect()->route('tags.index')->with('success', 'Tag updated successfully.');
    }

    // Remove the specified tag from the database
    public function destroy(Tag $tag)
    {
        $tag->delete();
        // Redirect to the tags index page with a success message
        return redirect()->route('tags.index')->with('success', 'Tag deleted successfully.');
    }
}
