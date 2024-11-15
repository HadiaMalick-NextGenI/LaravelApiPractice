@extends('layouts.app')

@section('title', 'Edit Post')
@section('content')

<div class="container">
    <h1>Edit Post</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            <ul>
                <li>{{ session('success') }}</li>
            </ul>
        </div>
    @endif

    <form id="edit-post-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ $post->title }}" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required>{{ $post->description }}</textarea>
        </div>

        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" class="form-control-file" id="image" name="image">
            @if ($post->image)
                <small>Current image:</small>
                <img src="/uploads/{{ $post->image }}" alt="{{ $post->title }}" width="100">
            @endif
        </div>

        <div class="form-group">
            <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1" {{ $post->is_published ? 'checked' : '' }}>
            <label for="is_published">Publish Post</label>
            <small class="form-text text-muted">Check to publish the post immediately.</small>
        </div>

        <button type="submit" class="btn btn-primary">Update Post</button>
    </form>
</div>

<script>
    document.getElementById('edit-post-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const blogId = window.location.pathname.split('/').pop();
        const title = document.getElementById('title').value;
        const description = document.getElementById('description').value;
        const isPublished = document.getElementById('is_published').checked ? 1 : 0;

        const data = {
            title: title,
            description: description,
            is_published: isPublished,
        };

        console.log(data);

        //const form = document.getElementById('edit-post-form');
        // const formData = new FormData(form);
        // // const formData = new FormData();
        // const urlParts = window.location.pathname.split('/'); 
        // const blogId = urlParts[urlParts.length - 1]; 
        // console.log(blogId);
        // formData.append('title', document.getElementById('title').value);
        // formData.append('description', document.getElementById('description').value);

        // console.log(document.getElementById('description').value);
        
        // const imageFile = document.getElementById('image').files[0];
        // if (imageFile) formData.append('image', imageFile);

        // const isPublished = document.getElementById('is_published').checked ? 1 : 0;
        // formData.append('is_published', isPublished);

        const token = '2|rXZwREoYl1eHRw6w95oZ1sqGSfYRdTLa0WOegQTKea363c59';
        // for (let [key, value] of formData.entries()) {
        //     console.log(`${key}: ${value}`);
        // }
        try {
            console.log("inside try catch");
            const response = await axios.put(`http://127.0.0.1:8000/api/v2/posts/${blogId}`, data, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    //'Content-Type': 'multipart/form-data',
                },
                //method: 'PUT',
            });

            if (response.status === 200) {
                alert('Post updated successfully!');
                //window.location.href = '/posts';
            }
        } catch (error) {
            console.error("Error updating post:", error);
            alert('There was an error updating the post.');
        }
    });
</script>

@endsection