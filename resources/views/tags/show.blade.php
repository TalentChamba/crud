@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Posts tagged with "{{ $tag->name }}"</h1>
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
                <a href="{{ route('posts.show', $post) }}" class="btn btn-primary">Read More</a>
            </div>
        </div>
    @endforeach
    {{ $posts->links() }}
</div>
@endsection
