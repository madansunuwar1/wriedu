@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
<div class="position-relative overflow-hidden">
    <div class="card mx-9 mt-n10">
        <div class="card-body pb-0">
            <div class="d-md-flex align-items-center justify-content-between text-center text-md-start">
                <div class="d-md-flex align-items-center">
                    <div class="rounded-circle position-relative mb-9 mb-md-0 d-inline-block">
                        <img src="{{asset('../assets/images/profile/user-1.jpg')}}" alt="student-img" class="img-fluid rounded-circle" width="100" height="100">
                    </div>
                    <div class="ms-0 ms-md-3 mb-9 mb-md-0">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-1">
                            <h4 class="me-7 mb-0 fs-7">{{ $enquirie->uname }}</h4>
                            <span class="badge fs-2 fw-bold rounded-pill bg-primary-subtle text-primary border-primary border">Active</span>
                        </div>
                        <p class="fs-4 mb-1">Enquiry Record</p>
                    </div>
                </div>
            </div>
            
            <ul class="nav nav-pills user-profile-tab mt-4 justify-content-center justify-content-md-start" id="pills-tab" role="tablist">
                <li class="nav-item me-2 me-md-3" role="presentation">
                    <button class="nav-link position-relative rounded-0 active d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-details-tab" data-bs-toggle="pill" data-bs-target="#pills-details" type="button" role="tab" aria-controls="pills-details" aria-selected="true">
                        <i class="ti ti-user-circle me-0 me-md-6 fs-6"></i>
                        <span class="d-none d-md-block">Student Details</span>
                    </button>
                </li>
                <li class="nav-item me-2 me-md-3" role="presentation">
                    <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent py-6" id="pills-comments-tab" data-bs-toggle="pill" data-bs-target="#pills-comments" type="button" role="tab" aria-controls="pills-comments" aria-selected="false">
                        <i class="ti ti-message-circle me-0 me-md-6 fs-6"></i>
                        <span class="d-none d-md-block">Comments</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="tab-content mx-10" id="pills-tabContent">
    <!-- Student Details Tab -->
    <div class="tab-pane fade show active" id="pills-details" role="tabpanel" aria-labelledby="pills-details-tab" tabindex="0">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body p-4">
                        <h5 class="mb-9">Personal Information</h5>
                        <div class="d-flex align-items-center mb-9">
                            <div class="ms-6">
                                <h6 class="mb-1">Student Name</h6>
                                <p class="mb-0">{{ $enquirie->uname }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-9">
                            <div class="ms-6">
                                <h6 class="mb-1">Email</h6>
                                <p class="mb-0">{{ $enquirie->email }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-9">
                            <div class="ms-6">
                                <h6 class="mb-1">Contact Number</h6>
                                <p class="mb-0">{{ $enquirie->contact }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-9">
                            <div class="ms-6">
                                <h6 class="mb-1">Guardian's Name</h6>
                                <p class="mb-0">{{ $enquirie->guardians }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-9">
                            <div class="ms-6">
                                <h6 class="mb-1">Guardian's Number</h6>
                                <p class="mb-0">{{ $enquirie->contacts }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-9">
                            <div class="ms-6">
                                <h6 class="mb-1">Country</h6>
                                <p class="mb-0">{{ $enquirie->country }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body p-4">
                        <h5 class="mb-9">Education & Test Scores</h5>
                        <div class="d-flex align-items-center mb-9">
                            <div class="ms-6">
                                <h6 class="mb-1">Education Level</h6>
                                <p class="mb-0">{{ $enquirie->education }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-9">
                            <div class="ms-6">
                                <h6 class="mb-1">IELTS</h6>
                                <p class="mb-0">{{ $enquirie->ielts ?: 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-9">
                            <div class="ms-6">
                                <h6 class="mb-1">TOEFL</h6>
                                <p class="mb-0">{{ $enquirie->toefl ?: 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-9">
                            <div class="ms-6">
                                <h6 class="mb-1">Counselor</h6>
                                <p class="mb-0">{{ $enquirie->counselor }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments Tab -->
    <div class="tab-pane fade" id="pills-comments" role="tabpanel" aria-labelledby="pills-comments-tab" tabindex="0">
        <div class="row">
            <div class="col-lg-12">
                <div class="card border">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{asset('../assets/images/profile/user-1.jpg')}}" alt="user-profile" width="32" height="32" class="rounded-circle">
                            <h6 class="mb-0 ms-6">Add New Comment</h6>
                        </div>

                        <form action="{{ route('backend.enquiryhistory.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id"
                                    value="{{ Auth::check() ? Auth::id() : '' }}">
                                <input type="hidden" name="enquiry_id" value="{{ $enquirie->id }}" />
                            <div class="userprofile-quill-editors mb-3">
                                <textarea id="comment" name="comment" class="form-control" placeholder="Enter your comment" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary shadow-none">Post</button>
                        </form>
                    </div>
                </div>
<div style="height: 92vh; overflow-y: auto;">
             @foreach ($lead_comments->sortByDesc('created_at') as $lead_comment)
    @if ($lead_comment->enquiry_id == $enquirie->id)
        <div class="p-4 rounded-4 text-bg-light mb-3">
            <div class="card-body border-bottom">
                <div class="d-flex justify-content-between">
                    <div class="d-flex align-items-center gap-6 flex-wrap">
                        <img src="{{ asset('../assets/images/profile/user-6.jpg') }}" alt="user-img"
                             class="rounded-circle" width="40" height="40">
                        <h6 class="mb-0">{{ $lead_comment->createdBy->name ?? 'Unknown User' }}
                            {{ $lead_comment->createdBy->last ?? '' }}</h6>
                        <span class="fs-2">
                            <span class="p-1 text-bg-light rounded-circle d-inline-block"></span>
                            {{ $lead_comment->created_at->format('F j, Y \a\t g:i a') }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="dropdown ms-auto">
                            <a class="text-dark d-flex align-items-center justify-content-center bg-transparent p-2 fs-4 rounded-circle"
                                href="javascript:void(0)" id="dropdownMenuButton{{ $loop->index }}"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots-vertical"></i>
                            </a>
                            <ul class="dropdown-menu"
                                aria-labelledby="dropdownMenuButton{{ $loop->index }}">
                                <li><a class="dropdown-item" href="#">Edit</a></li>
                                <li><a class="dropdown-item" href="#">Delete</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <p class="text-dark my-3">{{ $lead_comment->comment }}</p>
                @if ($lead_comment->updated_by && $lead_comment->updated_at != $lead_comment->created_at)
                    <small class="text-muted">(Updated)</small>
                @endif
            </div>
        </div>
    @endif
@endforeach
@foreach ($lead_comments as $lead_comment)
    {{ \Log::info($lead_comment) }}
@endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Comment Modal -->
<div class="modal fade" id="editCommentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCommentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <textarea class="form-control" name="comment" rows="5" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the edit modal
    const editModal = new bootstrap.Modal(document.getElementById('editCommentModal'));
    
    // Set up edit form when edit button is clicked
    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(dropdown => {
        dropdown.addEventListener('click', function() {
            const commentId = this.id.replace('dropdownMenuButton', '');
            const editLink = this.nextElementSibling.querySelector('a[href*="edit"]');
            
            if (editLink) {
                editLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = document.getElementById('editCommentForm');
                    form.action = this.href;
                    fetch(this.href)
                        .then(response => response.json())
                        .then(data => {
                            form.querySelector('textarea').value = data.comment;
                            editModal.show();
                        });
                });
            }
        });
    });
});
</script>
@endsection