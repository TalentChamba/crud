@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $post->title }}</h1>
    <p>By {{ $post->user->name }} | Published: {{ $post->published_at->format('M d, Y') }}</p>
    <div>
        {!! nl2br(e($post->content)) !!}
    </div>
    <div class="mt-3">
        <h4>Tags:</h4>
        @foreach ($post->tags as $tag)
            <a href="{{ route('tags.show', $tag) }}" class="badge bg-secondary">{{ $tag->name }}</a>
        @endforeach
    </div>
    @can('update', $post)
        <a href="{{ route('posts.edit', $post) }}" class="btn btn-primary mt-3">Edit Post</a>
    @endcan
    @can('delete', $post)
        <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger mt-3" onclick="return confirm('Are you sure you want to delete this post?')">Delete Post</button>
        </form>
    @endcan
</div>
@endsection
