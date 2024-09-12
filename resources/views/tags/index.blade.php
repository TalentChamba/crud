@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tags</h1>
    @auth
        <a href="{{ route('tags.create') }}" class="btn btn-primary mb-3">Create New Tag</a>
    @endauth
    <div class="row">
        @foreach ($tags as $tag)
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $tag->name }}</h5>
                        <p class="card-text">{{ $tag->posts_count }} posts</p>
                        <a href="{{ route('tags.show', $tag) }}" class="btn btn-primary">View Posts</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
