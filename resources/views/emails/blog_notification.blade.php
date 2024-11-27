<!DOCTYPE html>
<html>
<head>
    <title>New Blog Post!</title>
</head>
<body>
    <p>A new blog post has been published:</p>
    <h2>{{ $post->title }}</h2>
    <p>{{ Str::limit($post->description, 100) }}</p>
    <p><a href="{{ url('/posts/' . $post->id) }}">Read more</a></p>
</body>
</html>