<div class="p-4 rounded-4 text-bg-light mb-3">
    <div class="card-body border-bottom">
        <div class="d-flex justify-content-between">
            <div class="d-flex align-items-center gap-6 flex-wrap">
                <img src="{{ asset('../assets/images/profile/user-6.jpg') }}" alt="user-img" class="rounded-circle" width="40" height="40">
                <h6 class="mb-0">
                    {{ $comment->user?->name ?? 'Unknown User' }}
                </h6>
                <span class="fs-2">
                    <span class="p-1 text-bg-light rounded-circle d-inline-block"></span>
                    {{ $comment->created_at->format('F j, Y \a\t g:i a') }}
                </span>
            </div>

            <div class="d-flex align-items-center">
                <div class="dropdown ms-auto">
                    <a class="text-dark d-flex align-items-center justify-content-center bg-transparent p-2 fs-4 rounded-circle"
                       href="javascript:void(0)"
                       id="dropdownMenuButton{{ rand() }}"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </a>
                    <ul class="dropdown-menu"
                        aria-labelledby="dropdownMenuButton{{ rand() }}">
                        <li><a class="dropdown-item" href="#">Edit</a></li>
                        <li><a class="dropdown-item" href="#">Delete</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <p class="text-dark my-3">{{ $comment->comment }}</p>

        @if ($comment->updated_by && $comment->updated_at != $comment->created_at)
            <small class="text-muted">(Updated)</small>
        @endif
    </div>
</div>