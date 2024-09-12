@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Blog Posts</h1>
    @auth
        <a href="{{ route('posts.create') }}" class="btn btn-primary mb-3">Create New Post</a>
    @endauth
    @foreach ($posts as $post)
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="card-title">{{ $post->title }}</h2>
                <p class="card-text">{{ Str::limit($post->content, 200) }}</p>
                <p class="card-text">
                    <small class="text-muted">
                        By {{ $post->user->name }} | Published: {{ $post->published_at->format('M d, Y') }}
                    </small>
                </p>
                <div class="mb-2">
                    @foreach ($post->tags as $tag)
                        <a href="{{ route('tags.show', $tag) }}" class="badge bg-secondary">{{ $tag->name }}</a>
                    @endforeach
                </div>
                <a href="{{ route('posts.show', $post) }}" class="btn btn-primary">Read More</a>
            </div>
        </div>
    @endforeach
    {{ $posts->links() }}
</div>
@endsection
