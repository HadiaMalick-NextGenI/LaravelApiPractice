@if ($isOwner)
    <button class="btn btn-danger btn-sm delete-post" data-id="{{ $postId }}">Delete</button>
@else
    <button class="btn btn-danger btn-sm disabled" title="Not allowed">Delete</button>
@endif