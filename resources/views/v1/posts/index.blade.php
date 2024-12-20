@extends('layouts.app')

@section('title', 'Posts')
@section('content')

<div class="container">
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('posts.create') }}" class="btn btn-primary">Create Post</a>
    </div>

    <div id="posts-container" class="row">
        
    </div>
</div>

<script>
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    async function getCurrentUserId() {
        const token = getCookie('authToken');
        const response = await axios.get('http://127.0.0.1:8000/api/v1/user', {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        return response.data.id;
    }

    const fetchPosts = async () => {
        try {
            const token = getCookie('authToken');

            const response = await axios.get('http://127.0.0.1:8000/api/v1/posts', {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            });
            return response.data.data.data; 
        } catch (error) {
            console.error("There was an error fetching posts:", error);
            return []; 
        }
    };

    const deletePost = async (postId) => {
        if (confirm("Are you sure you want to delete this post?")) {
            try {
                const token = getCookie('authToken');
                await axios.delete(`http://127.0.0.1:8000/api/v1/posts/${postId}`, {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });
                alert('Post deleted successfully!');
                window.location.reload();
            } catch (error) {
                console.error("Error deleting post:", error);
                alert('There was an error deleting the post.');
            }
        }
    };

    const loadPosts = async () => {
        const posts = await fetchPosts(); 
        const userId = await getCurrentUserId();
        const postsContainer = document.getElementById('posts-container');

        if (Array.isArray(posts)) {
            posts.forEach(post => {
                const postElement = document.createElement('div');
                postElement.classList.add('col-md-4', 'mb-4');
                postElement.innerHTML = `
                    <div class="card">
                        <img src="/uploads/${post.image}" class="card-img-top" alt="${post.title}">
                        <div class="card-body">
                            <h5 class="card-title">${post.title}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">By ${post.author.name}</h6>
                            <p class="card-text">${post.description}</p>
                        </div>
                        ${post.author.id === userId ? `
                        <div class="d-flex justify-content-between">
                            <a href="/posts/edit/${post.id}" class="btn btn-warning btn-sm">Edit</a>
                            <button onclick="deletePost(${post.id})" class="btn btn-danger btn-sm">Delete</button>
                        </div>` : ''}
                    </div>
                `;
                postsContainer.appendChild(postElement);
            });
        } else {
            console.error("Expected posts to be an array, but got:", posts);
        }
    };

    loadPosts();
</script>

@endsection