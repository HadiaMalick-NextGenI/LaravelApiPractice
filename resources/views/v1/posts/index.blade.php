@extends('layouts.app')

@section('title', 'Posts')
@section('content')

<div id="posts-container" class="row">
    <!-- Posts will be displayed here in a grid format -->
</div>

<script>
    const fetchPosts = async () => {
        const token = '2|rXZwREoYl1eHRw6w95oZ1sqGSfYRdTLa0WOegQTKea363c59';
        try {
            const response = await axios.get('http://127.0.0.1:8000/api/v2/posts', {
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

    const loadPosts = async () => {
        const posts = await fetchPosts(); 
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