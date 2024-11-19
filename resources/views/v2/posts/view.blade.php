<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2>Post Details</h2>
    
    <div id="post-detail" class="mt-4">
        <div id="loading" class="text-center">
            <p>Loading...</p>
        </div>
    </div>
    
</div>

<script>
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    const token = getCookie('authToken');

    $(document).ready(function() {
        const postId = window.location.pathname.split('/').pop();

        $.ajax({
            url: `/api/v2/posts/${postId}`,
            method: 'GET',
            headers: 
            {
                Authorization: `Bearer ${token}`
            },
            success: function(response) {
                const post = response.data;

                $('#loading').hide();

                $('#post-detail').html(`
                    <h3>${post.title}</h3>
                    <p><strong>Created at:</strong> ${post.created_at}</p>
                    <p><strong>Content:</strong> ${post.description}</p>
                `);
            },
            error: function(error) {
                console.error('Error fetching post:', error);
                $('#loading').text('Failed to load post.');
            }
        });
    });
</script>

</body>
</html>