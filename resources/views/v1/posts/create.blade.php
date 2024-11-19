@extends('layouts.app')

@section('title', 'Create Post')
@section('content')

<div class="container">
    <h1>Create a New Post</h1>
    
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

    <form id="create-post-form" method="POST" enctype="multipart/form-data" action="#" >
        @csrf
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" class="form-control-file" id="image" name="image" required>
        </div>

        <div class="form-group">
            <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1">
            <label for="is_published">Publish Post</label>
            <small class="form-text text-muted">Check to publish the post immediately.</small>
        </div>

        <button type="submit" class="btn btn-primary">Create Post</button>
    </form>
</div>

<script>

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    const token = getCookie('authToken');

    async function getCurrentUserId() {
        const token = getCookie('authToken');
        const response = await axios.get('http://127.0.0.1:8000/api/v1/user', {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        return response.data.id;
    }

    document.getElementById('create-post-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('title', document.getElementById('title').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('image', document.getElementById('image').files[0]);

        const isPublished = document.getElementById('is_published').checked ? 1 : 0;
        formData.append('is_published', isPublished);

        try {
            const response = await axios.post('http://127.0.0.1:8000/api/v1/posts', formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                }
            });

            if (response.status === 201) {
                alert('Post created successfully!');
                window.location.href = '/posts'; 
            }
        } catch (error) {
            console.error("Error creating post:", error);
            alert('There was an error creating the post.');
        }
    });
</script>

@endsection