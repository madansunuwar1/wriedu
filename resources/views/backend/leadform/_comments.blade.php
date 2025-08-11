@foreach ($lead_comments as $comment)
    @include('backend.leadform._single_comment', ['comment' => $comment])
@endforeach