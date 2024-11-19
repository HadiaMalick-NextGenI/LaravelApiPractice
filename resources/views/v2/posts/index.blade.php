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

            if (confirm("Are you sure you want to delete this post?")) {
                $.ajax({
                    url: '/api/v2/posts/' + postId,
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

        $(document).ready(function () {

            $('#posts-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: {
                    url: '/api/v2/posts',
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
                        data: 'id',
                        render: function(data, type, row) {
                            return `
                            <a href="/posts/${data}" class="btn btn-primary">View</a>
                            <a href="/posts/edit/${data}" class="btn btn-warning">Edit</a>
                            <button class="btn btn-danger delete-post" data-id="${data}">Delete</button>
                            `;
                        }
                    }
                ]
            });
        });
    </script>
</body>
</html>