<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts DataTable</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    
</head>
<body>
    <div class="container mt-3">
        <h1>Posts DataTable</h1>
        <div class="mb-3">
            <a href="/posts/create" class="btn btn-success btn-sm" id="create-post-btn">Create New Post</a>
        </div>
        <table id="posts-table" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Author</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <script>

        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }

        const token = getCookie('authToken');

        $(document).on('click', '.delete-post', function() {
            var postId = $(this).data('id');
            var deletePostUrl = @json(route('posts.destroy', ':id'));
            deletePostUrl = deletePostUrl.replace(':id', postId);
            
            if (confirm("Are you sure you want to delete this post?")) {
                $.ajax({
                    url: deletePostUrl,
                    type: 'DELETE',
                    headers: {
                        Authorization: `Bearer ${token}`
                    },
                    success: function(response) {
                        alert(response.message);
                        $('#posts-table').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred while deleting the post.');
                    }
                });
            }
        });

        function initializePostsTable(token) {
            var postsUrl = @json(route('posts.index'));
            $('#posts-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: postsUrl,
                    type: 'GET',
                    headers: {
                        Authorization: `Bearer ${token}`
                    },
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'title', name: 'title' },
                    { data: 'description', name: 'description' },
                    { data: 'user_name', name: 'user_name' },
                    { data: 'created_at', name: 'created_at' },
                    { 
                        data: 'actions', 
                        name: 'actions',
                        orderable: false,
                        searchable: false 
                    }
                ],
                language: {
                    searchPlaceholder: 'Search posts...',
                    emptyTable: 'No posts available at the moment.'
                }
            });
        }

        $(document).ready(function () {
            initializePostsTable(token);
            console.log(document.getElementById('create-post-btn').href);
        });
    </script>
</body>
</html>