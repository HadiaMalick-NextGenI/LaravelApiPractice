@if ($isOwner)
    <a href="{{ $url }}" class="btn btn-warning btn-sm">Edit</a>
@else
    <a href="javascript:void(0)" class="btn btn-warning btn-sm disabled" title="Not allowed">Edit</a>
@endif