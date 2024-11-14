<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts</title>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @vite('resources/js/app.js')
</head>
<body>
    <div id="posts-container">
        <!-- Posts will be displayed here -->
    </div>

    <script>
        // const token = @json($token);

        // axios.defaults.headers.common['Authorization'] = token;

        const loadPosts = async () => {
            const posts = await fetchPosts(); 
            const postsContainer = document.getElementById('posts-container');

            posts.forEach(post => {
                const postElement = document.createElement('div');
                postElement.innerHTML = `
                    <h3>${post.title}</h3>
                    <p>${post.body}</p>
                `;
                postsContainer.appendChild(postElement);
            });
        };

        loadPosts();
    </script>
</body>
</html>
