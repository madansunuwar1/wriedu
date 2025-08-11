<template>
    <div>
        <div v-if="loading" class="text-center p-5">
            <p></p>
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div v-else-if="error" class="alert alert-danger mx-10">
            {{ error }}
        </div>
        <div v-else>
            <div class="card mx-9 mt-n10">
                <div class="card-body pb-0">
                    <!-- The new ribbon host is positioned absolutely relative to the card -->
                    <div class="remarks-host">
                        <transition name="unfurl">
                            <div v-if="showRemarks" class="ribbon-wrapper">
                                <div class="ribbon-content">
                                    <h6 class="fw-semibold mb-2">Review Notes</h6>
                                    <p class="mb-0">{{ application.notes }}</p>
                                    <button v-if="hasEditPermission" type="button"
                                            class="btn-close"
                                            @click="hideRemarks"
                                            aria-label="Close remarks">
                                    </button>
                                </div>
                            </div>
                        </transition>
                    </div>

                    <div class="d-md-flex align-items-start justify-content-between text-center text-md-start">
                        <div class="d-md-flex align-items-center">
                            <div class="rounded-circle position-relative mb-9 mb-md-0 d-inline-block">
                                <img :src="avatarUrl" alt="Profile Image" class="img-fluid rounded-circle" width="100"
                                    height="100">
                                <span v-if="hasEditPermission"
                                    class="text-bg-primary rounded-circle text-white d-flex align-items-center justify-content-center position-absolute bottom-0 end-0 p-1 border border-2 border-white">
                                    <i class="bi bi-plus text-white"></i>
                                </span>
                            </div>
                            <div class="ms-0 ms-md-3 mb-9 mb-md-0">
                                <div
                                    class="d-flex align-items-center justify-content-center justify-content-md-start mb-1">
                                    <h4 class="me-7 mb-0 fs-7">{{ application.name }}</h4>
                                    <span
                                        class="badge fs-2 fw-bold rounded-pill bg-primary-subtle text-primary border-primary border">{{
                                            application.status || 'N/A' }}</span>
                                </div>
                                <div class="d-align-items-center mt-1">
                                    <div class="d-flex align-items-center me-4">
                                        <p class="fs-3 mb-0 text-muted me-2">Student ID:</p>
                                        <span class="field-value-header fs-3"
                                            @click="hasEditPermission && editingField !== 'student_id' && startEditing('student_id', application.student_id)">
                                            <template v-if="editingField !== 'student_id'">
                                                {{ application.student_id || 'Not set' }}
                                            </template>
                                            <div v-else class="d-inline-flex align-items-center">
                                                <input v-model="editingValue" type="text"
                                                    class="form-control form-control-sm d-inline-block w-auto"
                                                    placeholder="Enter Student ID" @keyup.enter="saveField()"
                                                    @keyup.esc="cancelEditing">
                                                <button @click.stop="saveField()"
                                                    class="btn btn-sm btn-success ms-1 py-1 px-2" title="Save"><i
                                                        class="fas fa-check"></i></button>
                                                <button @click.stop="cancelEditing"
                                                    class="btn btn-sm btn-secondary ms-1 py-1 px-2" title="Cancel"><i
                                                        class="fas fa-times"></i></button>
                                            </div>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <p class="fs-3 mb-0 text-muted me-2">DOB:</p>
                                        <span class="field-value-header fs-3"
                                            @click="hasEditPermission && editingField !== 'birth_date' && startEditing('birth_date', application.birth_date)">
                                            <template v-if="editingField !== 'birth_date'">
                                                {{ application.birth_date || 'Not set' }}
                                            </template>
                                            <div v-else class="d-inline-flex align-items-center">
                                                <flatpickr v-model="editingValue" :config="flatpickrConfig"
                                                    class="form-control form-control-sm d-inline-block w-auto"
                                                    placeholder="Select Date.." @keyup.esc="cancelEditing"></flatpickr>
                                                <button @click.stop="saveField()"
                                                    class="btn btn-sm btn-success ms-1 py-1 px-2" title="Save"><i
                                                        class="fas fa-check"></i></button>
                                                <button @click.stop="cancelEditing"
                                                    class="btn btn-sm btn-secondary ms-1 py-1 px-2" title="Cancel"><i
                                                        class="fas fa-times"></i></button>
                                            </div>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <ul class="nav nav-pills user-profile-tab mt-4 justify-content-center justify-content-md-start"
                        id="pills-tab" role="tablist">
                        <li class="nav-item me-2 me-md-3" role="presentation">
                            <button class="nav-link active" id="pills-profile-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile"
                                aria-selected="true">
                                <i class="bi bi-person me-0 me-md-2 fs-6"></i>
                                <span class="d-none d-md-block">Student Profile</span>
                            </button>
                        </li>
                        <li class="nav-item me-2 me-md-3" role="presentation">
                            <button class="nav-link" id="pills-doc-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-doc" type="button" role="tab" aria-controls="pills-doc"
                                aria-selected="false">
                                <i class="bi bi-file-earmark me-0 me-md-2 fs-6"></i>
                                <span class="d-none d-md-block">Upload Documents</span>
                            </button>
                        </li>
                        <li class="nav-item me-2 me-md-3" role="presentation">
                            <button class="nav-link" id="pills-status-management-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-status-management" type="button" role="tab"
                                aria-controls="pills-status-management" aria-selected="false">
                                <i class="bi bi-files me-0 me-md-2 fs-6"></i>
                                <span class="d-none d-md-block">Application Status</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-add-university-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-add-university" type="button" role="tab"
                                aria-controls="pills-add-university" aria-selected="false">
                                <i class="bi bi-card-text me-0 me-md-2 fs-6"></i>
                                <span class="d-none d-md-block">Add To Another University</span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content mx-10" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-profile" role="tabpanel"
                    aria-labelledby="pills-profile-tab">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body p-4">
                                    <h5 class="mb-9">Personal Information</h5>
                                    <div v-for="field in personalFields" :key="field.key"
                                        class="d-flex align-items-center mb-9" :data-field="field.key">
                                        <div :class="`text-icon-${field.icon}`"
                                            class="fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 35px; height: 35px;">
                                            <i :class="`bi bi-${field.icon}`"></i>
                                        </div>
                                        <div class="ms-6">
                                            <h6 class="mb-1">{{ field.label }}</h6>
                                            <span class="field-value"
                                                @click="hasEditPermission && editingField !== field.key && startEditing(field.key, application[field.key])">
                                                <template v-if="editingField !== field.key">
                                                    {{ application[field.key] || 'Not set' }} 
                                                    <i v-if="hasEditPermission" class="bi bi-pencil text-success edit-icon ms-2"></i>
                                                </template>
                                                <div v-else class="d-inline-flex align-items-center">
                                                    <input v-model="editingValue" type="text"
                                                        class="form-control form-control-sm d-inline-block w-auto"
                                                        @keyup.enter="saveField()" @keyup.esc="cancelEditing">
                                                    <button @click.stop="saveField()"
                                                        class="btn btn-sm btn-success ms-1 py-1 px-2" title="Save"><i
                                                            class="fas fa-check"></i></button>
                                                    <button @click.stop="cancelEditing"
                                                        class="btn btn-sm btn-secondary ms-1 py-1 px-2"
                                                        title="Cancel"><i class="fas fa-times"></i></button>
                                                </div>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-body p-4">
                                    <h5 class="mb-9">Academic Information</h5>
                                    <div v-for="field in academicFields" :key="field.key"
                                        class="d-flex align-items-center mb-9" :data-field="field.key">
                                        <div :class="`text-icon-${field.icon}`"
                                            class="fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 35px; height: 35px;">
                                            <i :class="`bi bi-${field.icon}`"></i>
                                        </div>
                                        <div class="ms-6">
                                            <h6 class="mb-1">{{ field.label }}</h6>
                                            <span class="field-value"
                                                @click="hasEditPermission && editingField !== field.key && startEditing(field.key, application[field.key])">
                                                <template v-if="editingField !== field.key">
                                                    {{ application[field.key] || 'Not set' }} 
                                                    <i v-if="hasEditPermission" class="bi bi-pencil text-success edit-icon ms-2"></i>
                                                </template>
                                                <div v-else class="d-inline-flex align-items-center">
                                                    <input v-model="editingValue" type="text"
                                                        class="form-control form-control-sm d-inline-block w-auto"
                                                        @keyup.enter="saveField()" @keyup.esc="cancelEditing">
                                                    <button @click.stop="saveField()"
                                                        class="btn btn-sm btn-success ms-1 py-1 px-2" title="Save"><i
                                                            class="fas fa-check"></i></button>
                                                    <button @click.stop="cancelEditing"
                                                        class="btn btn-sm btn-secondary ms-1 py-1 px-2"
                                                        title="Cancel"><i class="fas fa-times"></i></button>
                                                </div>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body p-4">
                                    <h5 class="mb-9">Test Information</h5>
                                    <div v-for="field in testFields" :key="field.key"
                                        class="d-flex align-items-center mb-9" :data-field="field.key">
                                        <div :class="`text-icon-${field.icon}`"
                                            class="fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 35px; height: 35px;">
                                            <i :class="`bi bi-${field.icon}`"></i>
                                        </div>
                                        <div class="ms-6">
                                            <h6 class="mb-1">{{ field.label }}</h6>
                                            <span class="field-value"
                                                @click="hasEditPermission && editingField !== field.key && startEditing(field.key, application[field.key])">
                                                <template v-if="editingField !== field.key">
                                                    {{ application[field.key] || 'Not set' }} 
                                                    <i v-if="hasEditPermission" class="bi bi-pencil text-success edit-icon ms-2"></i>
                                                </template>
                                                <div v-else class="d-inline-flex align-items-center">
                                                    <input v-model="editingValue" type="text"
                                                        class="form-control form-control-sm d-inline-block w-auto"
                                                        @keyup.enter="saveField()" @keyup.esc="cancelEditing">
                                                    <button @click.stop="saveField()"
                                                        class="btn btn-sm btn-success ms-1 py-1 px-2" title="Save"><i
                                                            class="fas fa-check"></i></button>
                                                    <button @click.stop="cancelEditing"
                                                        class="btn btn-sm btn-secondary ms-1 py-1 px-2"
                                                        title="Cancel"><i class="fas fa-times"></i></button>
                                                </div>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body p-4">
                                    <h5 class="mb-9">University Information</h5>
                                    <div v-for="field in universityFields" :key="field.key"
                                        class="d-flex align-items-center mb-9" :data-field="field.key">
                                        <div :class="`text-icon-${field.icon}`"
                                            class="fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 35px; height: 35px;">
                                            <i :class="`bi bi-${field.icon}`"></i>
                                        </div>
                                        <div class="ms-6">
                                            <h6 class="mb-1">{{ field.label }}</h6>
                                            <span v-if="field.type === 'text'" class="field-value"
                                                @click="hasEditPermission && editingField !== field.key && startEditing(field.key, application[field.key])">
                                                <template v-if="editingField !== field.key">
                                                    {{ application[field.key] || 'Not set' }} 
                                                    <i v-if="hasEditPermission" class="bi bi-pencil text-success edit-icon ms-2"></i>
                                                </template>
                                                <div v-else class="d-inline-flex align-items-center">
                                                    <input v-model="editingValue" type="text"
                                                        class="form-control form-control-sm d-inline-block w-auto"
                                                        @keyup.enter="saveField()" @keyup.esc="cancelEditing">
                                                    <button @click.stop="saveField()"
                                                        class="btn btn-sm btn-success ms-1 py-1 px-2" title="Save"><i
                                                            class="fas fa-check"></i></button>
                                                    <button @click.stop="cancelEditing"
                                                        class="btn btn-sm btn-secondary ms-1 py-1 px-2"
                                                        title="Cancel"><i class="fas fa-times"></i></button>
                                                </div>
                                            </span>
                                            <span v-else-if="field.type === 'select'" class="field-value"
                                                @click="hasEditPermission && editingField !== field.key && startEditing(field.key, application[field.key])">
                                                <template v-if="editingField !== field.key">
                                                    {{ application[field.key] || 'Not set' }} 
                                                    <i v-if="hasEditPermission" class="bi bi-pencil text-success edit-icon ms-2"></i>
                                                </template>
                                                <div v-else class="d-inline-flex align-items-center">
                                                    <select v-model="editingValue"
                                                        class="form-select form-select-sm d-inline-block w-auto"
                                                        @change="handleInlineSelectChange(field.key, $event.target.value)">
                                                        <option value="">Select {{ field.label }}</option>
                                                        <option v-for="option in formOptions[field.optionsKey]"
                                                            :key="option" :value="option">{{ option }}</option>
                                                    </select>
                                                    <button @click.stop="saveField()"
                                                        class="btn btn-sm btn-success ms-1 py-1 px-2" title="Save"><i
                                                            class="fas fa-check"></i></button>
                                                    <button @click.stop="cancelEditing"
                                                        class="btn btn-sm btn-secondary ms-1 py-1 px-2"
                                                        title="Cancel"><i class="fas fa-times"></i></button>
                                                </div>
                                            </span>
                                            <span v-else-if="field.type === 'partner-select'" class="field-value"
                                                @click="hasEditPermission && editingField !== field.key && startEditing(field.key, application.partner?.id)">
                                                <template v-if="editingField !== field.key">
                                                    {{ application.partner?.agency_name || 'No Partner Assigned' }} 
                                                    <i v-if="hasEditPermission" class="bi bi-pencil text-success edit-icon ms-2"></i>
                                                </template>
                                                <div v-else class="d-inline-flex align-items-center">
                                                    <select v-model="editingValue"
                                                        class="form-select form-select-sm d-inline-block w-auto">
                                                        <option value="">Select Partner</option>
                                                        <option v-for="partner in partners" :key="partner.id"
                                                            :value="partner.id">{{
                                                                partner.agency_name }}</option>
                                                    </select>
                                                    <button @click.stop="saveField()"
                                                        class="btn btn-sm btn-success ms-1 py-1 px-2" title="Save"><i
                                                            class="fas fa-check"></i></button>
                                                    <button @click.stop="cancelEditing"
                                                        class="btn btn-sm btn-secondary ms-1 py-1 px-2"
                                                        title="Cancel"><i class="fas fa-times"></i></button>
                                                </div>
                                            </span>
                                            <span v-else-if="field.type === 'select-level'" class="field-value"
                                                @click="hasEditPermission && editingField !== field.key && startEditing(field.key, application[field.key])">
                                                <template v-if="editingField !== field.key">
                                                    {{ application[field.key] || 'Not set' }} 
                                                    <i v-if="hasEditPermission" class="bi bi-pencil text-success edit-icon ms-2"></i>
                                                </template>
                                                <div v-else class="d-inline-flex align-items-center">
                                                    <select v-model="editingValue"
                                                        class="form-select form-select-sm d-inline-block w-auto"
                                                        @change="handleInlineSelectChange(field.key, $event.target.value)">
                                                        <option value="">Select Level</option>
                                                        <option value="Undergraduate">Undergraduate</option>
                                                        <option value="Postgraduate">Postgraduate</option>
                                                    </select>
                                                    <button @click.stop="saveField()"
                                                        class="btn btn-sm btn-success ms-1 py-1 px-2" title="Save"><i
                                                            class="fas fa-check"></i></button>
                                                    <button @click.stop="cancelEditing"
                                                        class="btn btn-sm btn-secondary ms-1 py-1 px-2"
                                                        title="Cancel"><i class="fas fa-times"></i></button>
                                                </div>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-9">
                                        <div class="bg-info-subtle text-info fs-14 round-40 rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 35px; height: 35px;">
                                            <i class="bi bi-person-check"></i>
                                        </div>
                                        <div class="ms-6">
                                            <h6 class="mb-1">Document Forwarded From</h6>
                                            <span>{{ leadUser.name || 'User information not available' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card border mb-3">
                                <div class="card-header">
                                    <ul class="nav nav-tabs user-profile-tab card-header-tabs" id="commentTabs" role="tablist">
                                        <li v-if="hasEditPermission" class="nav-item" role="presentation">
                                            <button class="nav-link" :class="{ active: hasEditPermission }" id="comments-tab" data-bs-toggle="tab"
                                                data-bs-target="#comments-pane" type="button" role="tab"
                                                aria-controls="comments-pane" aria-selected="true">
                                                <i class="bi bi-chat-dots me-2"></i>Comments
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" :class="{ active: !hasEditPermission }" id="cas-feedback-tab" data-bs-toggle="tab"
                                                data-bs-target="#cas-feedback-pane" type="button" role="tab"
                                                aria-controls="cas-feedback-pane" aria-selected="false">
                                                <i class="bi bi-clipboard-check me-2"></i>CAS Feedback
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="tab-content" id="commentTabsContent">
                                <div class="tab-pane fade" :class="{ 'show active': hasEditPermission }" id="comments-pane" role="tabpanel"
                                    aria-labelledby="comments-tab">
                                    <div v-if="hasEditPermission">
                                        <div class="card border mb-3">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <img src="/assets/images/profile/user-1.jpg" alt="User Profile" width="32"
                                                        height="32" class="rounded-circle">
                                                    <h6 class="mb-0 ms-3">Add New Comment</h6>
                                                </div>
                                                <form @submit.prevent="submitComment">
                                                    <div class="mb-3">
                                                        <select v-model="newComment.comment_type" class="form-select" required>
                                                            <option value="" disabled>Select type of comment</option>
                                                            <option v-for="type in commentAdds" :key="type.id"
                                                                :value="type.applications">{{
                                                                    type.applications }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <textarea v-model="newComment.comment" class="form-control"
                                                            placeholder="Enter Your Comment" required></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary outline-button shadow-none"> <i class="bi bi-chat me-2"></i>Post</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div v-for="comment in leadComments" :key="comment.id"
                                            class="p-4 rounded-4 text-bg-light mb-3">
                                            <div class="card-body border-bottom">
                                                <div class="d-flex justify-content-between">
                                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                                        <img :src="comment.user && comment.user.avatar ? `/storage/avatars/${comment.user.avatar}` : '/assets/images/profile/user-1.jpg'"
                                                            alt="User Image" class="rounded-circle" width="40" height="40"
                                                            style="object-fit: cover;">
                                                        <h6 class="mb-0">{{ comment.user ? comment.user.name : 'Unknown User' }}
                                                        </h6>
                                                        <span class="fs-2 text-muted">{{ formatDate(comment.created_at, true)
                                                            }}</span>
                                                        <small class="badge text-bg-secondary">{{ comment.comment_type }}</small>
                                                    </div>
                                                    <div v-if="hasEditPermission" class="dropdown">
                                                        <a class="text-dark d-flex align-items-center justify-content-center bg-transparent p-2 fs-4 rounded-circle"
                                                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a class="dropdown-item" href="#"
                                                                    @click.prevent="openEditCommentModal(comment)">Edit Comment</a>
                                                            </li>
                                                            <li><a class="dropdown-item text-danger" href="#"
                                                                    @click.prevent="deleteComment(comment.id)">Delete Comment</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <p class="text-dark my-4 text-start">{{ comment.comment }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" :class="{ 'show active': !hasEditPermission }" id="cas-feedback-pane" role="tabpanel"
                                    aria-labelledby="cas-feedback-tab">
                                    <div class="card border mb-3">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <img src="/assets/images/profile/user-1.jpg" alt="User Profile" width="32"
                                                    height="32" class="rounded-circle">
                                                <h6 class="mb-0 ms-3">Add CAS Feedback</h6>
                                            </div>
                                            <form @submit.prevent="submitCasFeedback">
                                                <div class="mb-3">
                                                    <label for="feedback_subject" class="form-label">Subject</label>
                                                    <input type="text" v-model="newCasFeedback.subject" id="feedback_subject"
                                                        class="form-control" placeholder="Enter feedback subject" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="cas_feedback_details" class="form-label">Feedback Details</label>
                                                    <textarea v-model="newCasFeedback.feedback" id="cas_feedback_details"
                                                        class="form-control" placeholder="Enter your CAS feedback details..." required
                                                        rows="4" ></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-success outline-button shadow-none">
                                                    <i class="bi bi-send me-2"></i>Submit Feedback
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div v-if="casFeedbackLoading" class="text-center p-4">
                                        <div class="spinner-border text-success" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                    <div v-else class="cas-feedback-section">
                                        <div v-for="feedback in casFeedbacks" :key="feedback.id"
                                            class="p-4 rounded-4 text-bg-light mb-3">
                                            <div class="card-body border-bottom">
                                                <div class="d-flex justify-content-between">
                                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                                        <img :src="feedback.user && feedback.user.avatar ? `/storage/avatars/${feedback.user.avatar}` : '/assets/images/profile/user-1.jpg'"
                                                            alt="User Image" class="rounded-circle" width="40" height="40"
                                                            style="object-fit: cover;">
                                                        <div>
                                                            <h6 class="mb-0">
                                                                {{ feedback.user ? feedback.user.name : 'Unknown User' }}
                                                                <span
                                                                    class="fw-bold ms-2 badge rounded-pill bg-primary-subtle text-primary border-primary">
                                                                    {{ feedback.subject }}
                                                                </span>
                                                            </h6>
                                                            <span class="fs-2 text-muted">{{ formatDate(feedback.created_at, true) }}</span>
                                                        </div>
                                                    </div>
                                                    <div v-if="hasEditPermission" class="dropdown">
                                                        <a class="text-dark d-flex align-items-center justify-content-center bg-transparent p-2 fs-4 rounded-circle"
                                                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a class="dropdown-item" href="#"
                                                                    @click.prevent="openEditCasModal(feedback)">Edit Feedback</a></li>
                                                            <li><a class="dropdown-item text-danger" href="#"
                                                                    @click.prevent="deleteCasFeedback(feedback.id)">Delete Feedback</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <p class="text-dark my-3 text-start">{{ feedback.feedback }}</p>
                                            </div>
                                        </div>
                                        <div v-if="!casFeedbacks.length" class="text-center p-4 text-muted">
                                            No CAS feedback has been submitted for this application yet.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="tab-pane fade" id="pills-doc" role="tabpanel" aria-labelledby="pills-doc-tab">
                    <div class="page-container card">
                        <div v-if="hasEditPermission" class="create-section">
                            <h2 class="section-title">Upload Documents</h2>
                            <div id="uploadContainer" class="upload-container" @click="triggerFileInput"
                                @dragover.prevent="dragover" @dragleave.prevent="dragleave" @drop.prevent="drop">
                                <div v-if="fileUploads.length === 0">
                                    <div class="upload-icon">
                                        <img src="/img/wri.png" alt="Upload icon">
                                    </div>
                                    <p class="upload-text">Drag & drop files here or click to select</p>
                                </div>
                                <div v-else class="progress-section">
                                    <div v-for="(item, index) in fileUploads" :key="index" class="file-item">
                                        <div class="file-details">
                                            <div class="file-name">{{ item.file.name }}</div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" :style="{ width: item.progress + '%' }">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-1">
                                                <div class="file-size">{{ (item.file.size / 1024).toFixed(1) }} KB</div>
                                                <div class="progress-percentage">{{ Math.round(item.progress) }}%</div>
                                            </div>
                                            <div class="upload-status small"
                                                :class="{ 'text-success': item.status === 'success', 'text-danger': item.status === 'error' }">
                                                {{ item.message }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="file" ref="fileInput" @change="handleFileSelect" multiple
                                    style="display: none;">
                            </div>
                        </div>
                        <div class="index-section">
                            <h2 class="section-title">Uploaded Files</h2>
                            <div class="index-section-content">
                                <table class="documents-table">
                                    <tbody>
                                        <tr v-if="uploads.length === 0">
                                            <td colspan="3" class="text-center py-4">No documents uploaded yet.</td>
                                        </tr>
                                        <tr v-for="upload in uploads" :key="upload.id">
                                            <td>
                                                <i class="bi bi-check-circle text-success"></i>
                                                <div class="file-wrapper">
                                                    <div class="file-icon"><i
                                                            class="bi bi-file-earmark text-primary"></i></div>
                                                    <a :href="`/storage/uploads/${upload.fileInput}`" class="file-link"
                                                        target="_blank" download>
                                                        <span class="file-name">{{ upload.fileInput }}</span>
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="upload-date">{{ formatDate(upload.created_at) }}</td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a :href="`/storage/uploads/${upload.fileInput}`" class="view-link"
                                                        target="_blank" title="View">
                                                        <i class="bi bi-eye text-info"></i>
                                                    </a>
                                                    <button v-if="hasEditPermission" @click="deleteUpload(upload.id)" class="btn-danger"
                                                        style="border: none; background: transparent; padding: 0; cursor:pointer;"
                                                        title="Delete">
                                                        <i class="bi bi-x-lg text-danger"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-status-management" role="tabpanel"
                    aria-labelledby="pills-status-management-tab">
                    <div class="card card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-group mb-3 w-50">
                                <label for="document_status" class="form-label">Application Status</label>
                                <select v-model="application.status" id="document_status" class="form-select w-100" :disabled="!hasEditPermission">
                                    <option value="">Select Status</option>
                                    <option v-for="doc in documents" :key="doc.document" :value="doc.document">{{
                                        doc.document }}
                                    </option>
                                </select>
                            </div>
                            <div class="my-auto">
                                <button v-if="hasEditPermission" @click="saveStatus" type="button" class="btn btn-primary"
                                    :disabled="!application.status || isSavingStatus">
                                    <span v-if="isSavingStatus" class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    {{ isSavingStatus ? 'Saving...' : 'Save Permanent' }}
                                </button>
                            </div>
                        </div>
                        <ul id="progressBar">
                            <li v-for="(step, index) in statusSteps" :key="step.value" :class="{
                                'completed': currentStatusIndex > index,
                                'current': currentStatusIndex === index,
                                'disabled': currentStatusIndex < index,
                            }">
                                <a href="#"><span class="step">{{ index + 1 }}</span> {{ step.text }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-add-university" role="tabpanel"
                    aria-labelledby="pills-add-university-tab">
                    <div v-if="hasEditPermission" class="card w-100 mt-4">
                        <div class="card-body">
                            <h4 class="card-title mb-3">Add Application for Same Student</h4>
                            <form @submit.prevent="submitNewApplication">
                                <div class="row">
                                    <div class="col-md-6 form-group mb-4">
                                        <label for="studyLevel">Study Level:</label>
                                        <select v-model="newApplicationForm.studyLevel" id="studyLevel"
                                            class="form-select">
                                            <option value="Undergraduate">Undergraduate</option>
                                            <option value="Postgraduate">Postgraduate</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group mb-4">
                                        <label for="country">Country:</label>
                                        <select v-model="newApplicationForm.country" id="country" class="form-select">
                                            <option value="">Select Country</option>
                                            <option v-for="country in formOptions.countries" :key="country"
                                                :value="country">{{
                                                    country }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group mb-4">
                                        <label for="location">Location:</label>
                                        <select v-model="newApplicationForm.location" id="location" class="form-select"
                                            :disabled="!newApplicationForm.country || formOptions.locations.length === 0">
                                            <option value="">Select Location</option>
                                            <option v-for="location in formOptions.locations" :key="location"
                                                :value="location">{{
                                                    location }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group mb-4">
                                        <label for="newUniversity">University:</label>
                                        <select v-model="newApplicationForm.university" id="newUniversity"
                                            class="form-select"
                                            :disabled="!newApplicationForm.location || formOptions.universities.length === 0">
                                            <option value="">Select University</option>
                                            <option v-for="uni in formOptions.universities" :key="uni" :value="uni">{{
                                                uni }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group mb-4">
                                        <label for="newCourse">Course:</label>
                                        <select v-model="newApplicationForm.course" id="newCourse" class="form-select"
                                            :disabled="!newApplicationForm.university || formOptions.courses.length === 0">
                                            <option value="">Select Course</option>
                                            <option v-for="course in formOptions.courses" :key="course" :value="course">
                                                {{ course }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group mb-4">
                                        <label for="newIntake">Intake:</label>
                                        <select v-model="newApplicationForm.intake" id="newIntake" class="form-select"
                                            :disabled="!newApplicationForm.course || formOptions.intakes.length === 0">
                                            <option value="">Select Intake</option>
                                            <option v-for="intake in formOptions.intakes" :key="intake" :value="intake">
                                                {{ intake }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group mb-4">
                                        <label for="fee">Fee:</label>
                                        <input v-model="newApplicationForm.fee" type="text" id="fee"
                                            class="form-control" placeholder="Enter Fee Amount">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Submit New Application</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div v-else class="alert alert-warning mt-4">
                        You do not have permission to add a new application for this student.
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="editCommentModalLabel"
            aria-hidden="true" ref="editCommentModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCommentModalLabel">Edit Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="updateComment">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="editCommentType" class="form-label">Comment Type</label>
                                <select v-model="editingComment.comment_type" id="editCommentType" class="form-select"
                                    required>
                                    <option value="" disabled>Select type</option>
                                    <option v-for="type in commentAdds" :key="type.id" :value="type.applications">{{
                                        type.applications }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editCommentText" class="form-label">Comment</label>
                                <textarea v-model="editingComment.comment" class="form-control" id="editCommentText"
                                    rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editCasFeedbackModal" tabindex="-1" aria-labelledby="editCasFeedbackModalLabel" aria-hidden="true" ref="editCasFeedbackModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCasFeedbackModalLabel">Edit CAS Feedback</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="updateCasFeedback">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_feedback_subject" class="form-label">Subject</label>
                                <input type="text" v-model="editingCasFeedback.subject" id="edit_feedback_subject" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_feedback_details" class="form-label">Feedback Details</label>
                                <textarea v-model="editingCasFeedback.feedback" id="edit_feedback_details" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_feedback_type" class="form-label">Type</label>
                                    <input type="text" v-model="editingCasFeedback.feedback_type" id="edit_feedback_type" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_priority" class="form-label">Priority</label>
                                    <select v-model="editingCasFeedback.priority" id="edit_priority" class="form-select" required>
                                        <option>Low</option>
                                        <option>Medium</option>
                                        <option>High</option>
                                        <option>Critical</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_status" class="form-label">Status</label>
                                <select v-model="editingCasFeedback.status" id="edit_status" class="form-select" required>
                                    <option>Open</option>
                                    <option>In Progress</option>
                                    <option>Resolved</option>
                                    <option>Closed</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2'; // Kept for confirmation dialogs (e.g., "Are you sure?")
import * as bootstrap from 'bootstrap';
import flatpickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import { useToast } from 'vue-toastification'; // The only new import

// The old showNotification function is no longer needed.

export default {
    components: {
        flatpickr
    },
    // This setup function makes the toast service available as `this.toast`
    setup() {
        const toast = useToast();
        return { toast };
    },
    data() {
        return {
            userPermissions: [],
            flatpickrConfig: {
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                allowInput: true,
            },
            loading: true,
            error: null,
            application: {},
            leadComments: [],
            casFeedbacks: [],
            casFeedbackLoading: false,
            newCasFeedback: {
                subject: '',
                feedback: '',
                priority: 'Medium',
                status: 'Open',
                feedback_type: 'General',
            },
            editingCasFeedback: {},
            casEditModalInstance: null,
            commentAdds: [],
            uploads: [],
            partners: [],
            allApplicationStatuses: [],
            leadUser: {},
            isSavingStatus: false,
            editingField: null,
            editingValue: '',
            inlineEditState: {},
            newComment: { comment: '', comment_type: '' },
            editModalInstance: null,
            editingComment: { id: null, comment: '', comment_type: '' },
            fileUploads: [],
            formOptions: {
                countries: [],
                locations: [],
                universities: [],
                courses: [],
                intakes: [],
            },
            newApplicationForm: {
                studyLevel: 'Undergraduate',
                country: '',
                location: '',
                university: '',
                course: '',
                intake: '',
                fee: '',
            },
            showRemarks: false, 
            personalFields: [
                { key: 'name', label: 'Name', icon: 'person', type: 'text' },
                { key: 'phone', label: 'Phone Number', icon: 'telephone', type: 'text' },
                { key: 'email', label: 'Email', icon: 'envelope', type: 'text' },
            ],
            academicFields: [
                { key: 'lastqualification', label: 'Previous Study', icon: 'journal-check', type: 'text' },
                { key: 'gpa', label: 'GPA', icon: 'award', type: 'text' },
                { key: 'passed', label: 'Passed Date', icon: 'calendar', type: 'text' },
            ],
            testFields: [
                { key: 'englishTest', label: 'English Test', icon: 'book', type: 'text' },
                { key: 'score', label: 'Score', icon: 'file-earmark-text', type: 'text' },
                { key: 'englishscore', label: 'English Score', icon: 'translate', type: 'text' },
                { key: 'englishtheory', label: 'English Theory', icon: 'book-half', type: 'text' },
            ],
            universityFields: [
                { key: 'level', label: 'Study Level', icon: 'mortarboard', type: 'select-level' },
                { key: 'country', label: 'Country', icon: 'globe', type: 'select', optionsKey: 'countries' },
                { key: 'location', label: 'Location', icon: 'geo-alt', type: 'select', optionsKey: 'locations' },
                { key: 'university', label: 'University', icon: 'building', type: 'select', optionsKey: 'universities' },
                { key: 'course', label: 'Course', icon: 'book', type: 'select', optionsKey: 'courses' },
                { key: 'intake', label: 'Intake', icon: 'calendar-event', type: 'select', optionsKey: 'intakes' },
                { key: 'fee', label: 'Fee', icon: 'wallet', type: 'text' },
                // { key: 'partnerDetails', label: 'Partner', icon: 'person-plus', type: 'partner-select' },
                { key: 'courseSearchField', label: 'Partner', icon: 'person-badge', type: 'text' },
            ],
        };
    },
    computed: {
        hasEditPermission() {
            // Reverted to your exact original permission check
            return this.userPermissions.includes('Edit applications');
        },
        avatarUrl() {
            return this.application.avatar ? `/storage/avatars/${this.application.avatar}` : '/assets/images/profile/user-1.jpg';
        },
        documents() {
            if (!this.allApplicationStatuses.length) {
                return [];
            }
            return this.allApplicationStatuses
                .filter(doc => !doc.country || doc.country === this.application.country)
                .sort((a, b) => a.id - b.id);
        },
        statusSteps() {
            return this.documents.map(doc => ({ value: doc.document, text: doc.document }));
        },
        currentStatusIndex() {
            if (!this.application.status) return -1;
            return this.statusSteps.findIndex(step => step.value === this.application.status);
        },
    },
    watch: {
        'newApplicationForm.studyLevel'(newLevel) {
            this.newApplicationForm.course = '';
            this.newApplicationForm.intake = '';
            const { university, location } = this.newApplicationForm;
            if (university && location) {
                this.fetchCoursesForNewAppForm(university, location, newLevel);
            }
        },
        'newApplicationForm.country': async function (newCountry) {
            this.newApplicationForm.location = '';
            this.newApplicationForm.university = '';
            this.newApplicationForm.course = '';
            this.newApplicationForm.intake = '';
            this.formOptions.locations = [];
            this.formOptions.universities = [];
            this.formOptions.courses = [];
            this.formOptions.intakes = [];
            if (newCountry) {
                try {
                    const { data } = await axios.get(`/api/data-entries/get-locations-by-country?country=${encodeURIComponent(newCountry)}`);
                    if (data.status === 'success') this.formOptions.locations = data.data;
                } catch (error) { this.handleFormError("locations", error); }
            }
        },
        'newApplicationForm.location': async function (newLocation) {
            this.newApplicationForm.university = '';
            this.newApplicationForm.course = '';
            this.newApplicationForm.intake = '';
            this.formOptions.universities = [];
            this.formOptions.courses = [];
            this.formOptions.intakes = [];
            if (newLocation) {
                try {
                    const { data } = await axios.get(`/api/data-entries/get-universities-by-location?location=${encodeURIComponent(newLocation)}`);
                    if (data.status === 'success') this.formOptions.universities = data.data;
                } catch (error) { this.handleFormError("universities", error); }
            }
        },
        'newApplicationForm.university': async function (newUniversity) {
            this.newApplicationForm.course = '';
            this.newApplicationForm.intake = '';
            const { location, studyLevel } = this.newApplicationForm;
            if (newUniversity && location) {
                this.fetchCoursesForNewAppForm(newUniversity, location, studyLevel);
            }
        },
        'newApplicationForm.course': async function (newCourse) {
            this.newApplicationForm.intake = '';
            const { university, location } = this.newApplicationForm;
            if (newCourse && university && location) {
                try {
                    const params = new URLSearchParams({
                        course: newCourse,
                        university: university,
                        location: location
                    }).toString();
                    const { data } = await axios.get(`/api/data-entries/get-intakes-by-course?${params}`);
                    if (data.status === 'success') this.formOptions.intakes = data.data;
                } catch (error) { this.handleFormError("intakes", error); }
            }
        },
    },
    async created() {
        await this.fetchUserPermissions();
        await this.fetchApplicationDetails();
        await this.fetchCountries();
        await this.fetchCasFeedbacks();
    },
    mounted() {
        if (this.$refs.editCommentModal) {
            this.editModalInstance = new bootstrap.Modal(this.$refs.editCommentModal);
        }
        if (this.$refs.editCasFeedbackModal) {
            this.casEditModalInstance = new bootstrap.Modal(this.$refs.editCasFeedbackModal);
        }
         // Set default active tab based on permissions
        this.$nextTick(() => {
            const tabEl = document.querySelector(this.hasEditPermission ? '#comments-tab' : '#cas-feedback-tab');
            if (tabEl) {
                const tab = new bootstrap.Tab(tabEl);
                tab.show();
            }
        });
    },
    methods: {
        async fetchUserPermissions() {
            try {
                const { data } = await axios.get('/api/user-permissions'); 
                this.userPermissions = data.permissions || [];
            } catch (error) {
                console.error('Failed to fetch user permissions:', error);
                this.toast.error('Could not verify user permissions. Some features may be disabled.');
                this.userPermissions = [];
            }
        },
        async fetchApplicationDetails() {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await axios.get(`/api/applications/record/${this.$route.params.id}`);
                this.application = data.application || {};
                this.leadComments = data.lead_comments || [];
                this.commentAdds = data.commentAdds || [];
                this.uploads = data.uploads || [];
                this.partners = data.partners || [];
                this.allApplicationStatuses = (data.documents || []).filter(d => d.status === 'application');
                this.leadUser = data.lead?.user || {};

                if (this.application.notes && this.application.show_remarks !== 0) {
                    setTimeout(() => {
                        this.showRemarks = true;
                    }, 500);
                }
            } catch (err) {
                this.handleError(err, 'Could not load application details.');
            } finally {
                this.loading = false;
            }
        },
        async startEditing(fieldName, currentValue) {
            if (!this.hasEditPermission || this.editingField) {
                return;
            }
            this.editingField = fieldName;
            this.editingValue = currentValue || '';
            this.inlineEditState = { ...this.application };
            if (fieldName === 'partnerDetails') {
                this.editingValue = this.application.partner?.id || '';
                return;
            }
            try {
                if (this.formOptions.countries.length === 0) await this.fetchCountries();
                const cascadeFields = ['country', 'location', 'university', 'course', 'intake', 'level'];
                if (cascadeFields.includes(fieldName)) {
                    if (this.application.country) {
                        const { data } = await axios.get(`/api/data-entries/get-locations-by-country?country=${encodeURIComponent(this.application.country)}`);
                        if (data.status === 'success') this.formOptions.locations = data.data;
                    }
                    if (this.application.location) {
                        const { data } = await axios.get(`/api/data-entries/get-universities-by-location?location=${encodeURIComponent(this.application.location)}`);
                        if (data.status === 'success') this.formOptions.universities = data.data;
                    }
                    if (this.application.university) {
                        const params = new URLSearchParams({
                            university: this.application.university,
                            location: this.application.location
                        });
                        if (this.application.level) {
                            params.append('level', this.application.level);
                        }
                        const { data } = await axios.get(`/api/data-entries/get-courses-by-university?${params.toString()}`);
                        if (data.status === 'success') this.formOptions.courses = data.data;
                    }
                    if (this.application.course) {
                        const params = new URLSearchParams({
                            course: this.application.course,
                            university: this.application.university,
                            location: this.application.location
                        }).toString();
                        const { data } = await axios.get(`/api/data-entries/get-intakes-by-course?${params}`);
                        if (data.status === 'success') this.formOptions.intakes = data.data;
                    }
                }
            } catch (error) {
                console.error('Error populating form options:', error);
            }
        },
        cancelEditing() {
            this.editingField = null;
            this.editingValue = '';
            this.inlineEditState = {};
            this.formOptions.locations = [];
            this.formOptions.universities = [];
            this.formOptions.courses = [];
            this.formOptions.intakes = [];
        },
        async saveField() {
            if (!this.hasEditPermission) return;
            const fieldName = this.editingField;
            let valueToSave = this.editingValue;
            if (!fieldName) {
                this.cancelEditing();
                return;
            }
            try {
                let apiFieldName = fieldName;
                if (apiFieldName === 'partnerDetails') {
                    apiFieldName = 'partner_id';
                }
                const response = await axios.put(`/api/applications/${this.application.id}/update-field`, {
                    field: apiFieldName,
                    value: valueToSave,
                });
                if (response.data.success) {
                    this.application = { ...this.application, ...response.data.application };
                    if (apiFieldName === 'partner_id' && response.data.application.partner) {
                        this.application.partner = response.data.application.partner;
                    }
                    this.toast.success('Field updated successfully.');
                }
            } catch (error) {
                this.handleError(error, 'Could not update field.');
            } finally {
                this.cancelEditing();
            }
        },
        async handleInlineSelectChange(fieldName, value) {
            this.editingValue = value;
            this.inlineEditState[fieldName] = value;
            if (fieldName === 'level') {
                this.inlineEditState.course = '';
                this.inlineEditState.intake = '';
                this.formOptions.courses = [];
                this.formOptions.intakes = [];
            } else if (fieldName === 'country') {
                this.inlineEditState.location = '';
                this.inlineEditState.university = '';
                this.inlineEditState.course = '';
                this.inlineEditState.intake = '';
                this.formOptions.locations = [];
                this.formOptions.universities = [];
                this.formOptions.courses = [];
                this.formOptions.intakes = [];
                try {
                    const { data } = await axios.get(`/api/data-entries/get-locations-by-country?country=${encodeURIComponent(value)}`);
                    if (data.status === 'success') this.formOptions.locations = data.data;
                } catch (error) { this.handleError(error, 'Could not fetch locations.'); }
            } else if (fieldName === 'location') {
                this.inlineEditState.university = '';
                this.inlineEditState.course = '';
                this.inlineEditState.intake = '';
                this.formOptions.universities = [];
                this.formOptions.courses = [];
                this.formOptions.intakes = [];
                try {
                    const { data } = await axios.get(`/api/data-entries/get-universities-by-location?location=${encodeURIComponent(value)}`);
                    if (data.status === 'success') this.formOptions.universities = data.data;
                } catch (error) { this.handleError(error, 'Could not fetch universities.'); }
            } else if (fieldName === 'university') {
                this.inlineEditState.course = '';
                this.inlineEditState.intake = '';
                this.formOptions.courses = [];
                this.formOptions.intakes = [];
            } else if (fieldName === 'course') {
                this.inlineEditState.intake = '';
                this.formOptions.intakes = [];
                const { university, location } = this.inlineEditState;
                if (value && university && location) {
                    try {
                        const params = new URLSearchParams({ course: value, university: university, location: location }).toString();
                        const { data } = await axios.get(`/api/data-entries/get-intakes-by-course?${params}`);
                        if (data.status === 'success') this.formOptions.intakes = data.data;
                    } catch (error) { this.handleError(error, 'Could not fetch intakes.'); }
                }
            }
            if (this.inlineEditState.university && this.inlineEditState.location) {
                const { university, location, level } = this.inlineEditState;
                try {
                    const params = new URLSearchParams({ university: university, location: location });
                    if (level) {
                        params.append('level', level);
                    }
                    const { data } = await axios.get(`/api/data-entries/get-courses-by-university?${params.toString()}`);
                    if (data.status === 'success') {
                        this.formOptions.courses = data.data;
                    }
                } catch (error) {
                    this.handleError(error, 'Could not fetch courses.');
                }
            }
        },
        async fetchCoursesForNewAppForm(university, location, level) {
            try {
                const params = new URLSearchParams({ university: university, location: location, });
                if (level) {
                    params.append('level', level);
                }
                const { data } = await axios.get(`/api/data-entries/get-courses-by-university?${params.toString()}`);
                if (data.status === 'success') {
                    this.formOptions.courses = data.data;
                }
            } catch (error) {
                this.handleFormError("courses", error);
            }
        },
        async submitNewApplication() {
            if (!this.hasEditPermission) return;
            try {
                const payload = { ...this.newApplicationForm, original_application_id: this.application.id, name: this.application.name, email: this.application.email, phone: this.application.phone };
                const { data } = await axios.post('/api/application/studentstore', payload);
                if (data.success) {
                    this.toast.success(data.message || 'New application created!');
                       this.$router.push({ name: 'ApplicationList' });
                    Object.assign(this.newApplicationForm, { studyLevel: 'Undergraduate', country: '', location: '', university: '', course: '', intake: '', fee: '' });
                    Object.assign(this.formOptions, { locations: [], universities: [], courses: [], intakes: [] });
                }
            } catch (error) {
                this.handleError(error, 'Could not create new application.');
            }
        },
        async submitComment() {
            if (!this.hasEditPermission) return;
            if (!this.newComment.comment_type || !this.newComment.comment) {
                this.toast.warning('Please select a comment type and enter a comment.');
                return;
            }
            try {
                const payload = {
                    application_id: this.application.id,
                    comment: this.newComment.comment,
                    comment_type: this.newComment.comment_type,
                };
                const { data } = await axios.post('/api/comments', payload);
                if (data.success) {
                    this.toast.success('Comment posted successfully.');
                    this.leadComments.unshift(data.comment);
                    this.newComment.comment = '';
                    this.newComment.comment_type = '';
                }
            } catch (error) {
                this.handleError(error, 'Could not post the comment.');
            }
        },
        openEditCommentModal(comment) {
            if (!this.hasEditPermission) return;
            this.editingComment = { ...comment };
            this.editModalInstance.show();
        },
        async updateComment() {
            if (!this.hasEditPermission) return;
            try {
                const { data } = await axios.put(`/api/comment/update/${this.editingComment.id}`, this.editingComment);
                if (data.success) {
                    this.toast.success('Comment updated successfully!');
                    this.editModalInstance.hide();
                    await this.fetchApplicationDetails();
                }
            } catch (error) {
                this.handleError(error, 'Could not update comment.');
            }
        },
        async deleteComment(commentId) {
            if (!this.hasEditPermission) return;
            const result = await Swal.fire({ title: 'Are you sure?', text: "You won't be able to revert this!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Yes, delete it!' });
            if (result.isConfirmed) {
                try {
                    await axios.delete(`/api/comment/destroy/${commentId}`);
                    this.toast.success('The comment has been deleted.');
                    await this.fetchApplicationDetails();
                } catch (error) {
                    this.handleError(error, 'Could not delete the comment.');
                }
            }
        },
        async fetchCasFeedbacks() {
            this.casFeedbackLoading = true;
            try {
                const { data } = await axios.get(`/api/applications/${this.$route.params.id}/cas-feedbacks`);
                if (data.success) {
                    this.casFeedbacks = data.data;
                }
            } catch (error) {
                this.handleError(error, 'Could not fetch CAS feedbacks.');
            } finally {
                this.casFeedbackLoading = false;
            }
        },
        async submitCasFeedback() {
            if (!this.hasEditPermission) return;
            try {
                const payload = {
                    ...this.newCasFeedback,
                    application_id: this.application.id,
                };
                const { data } = await axios.post('/api/cas-feedbacks', payload);
                if (data.success) {
                    this.casFeedbacks.unshift(data.data);
                    this.newCasFeedback.subject = '';
                    this.newCasFeedback.feedback = '';
                    this.toast.success('CAS Feedback submitted successfully.');
                }
            } catch (error) {
                this.handleError(error, 'Could not submit CAS feedback.');
            }
        },
        openEditCasModal(feedback) {
            if (!this.hasEditPermission) return;
            this.editingCasFeedback = { ...feedback };
            this.casEditModalInstance.show();
        },
        async updateCasFeedback() {
            if (!this.hasEditPermission) return;
            try {
                const { data } = await axios.put(`/api/cas-feedbacks/${this.editingCasFeedback.id}`, this.editingCasFeedback);
                if (data.success) {
                    const index = this.casFeedbacks.findIndex(f => f.id === data.data.id);
                    if (index !== -1) {
                        this.casFeedbacks.splice(index, 1, data.data);
                    }
                    this.casEditModalInstance.hide();
                    this.toast.success('CAS Feedback updated successfully!');
                }
            } catch (error) {
                this.handleError(error, 'Could not update CAS feedback.');
            }
        },
        async deleteCasFeedback(feedbackId) {
            if (!this.hasEditPermission) return;
            const result = await Swal.fire({ title: 'Are you sure?', text: "You won't be able to revert this!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Yes, delete it!' });
            if (result.isConfirmed) {
                try {
                    await axios.delete(`/api/cas-feedbacks/${feedbackId}`);
                    this.casFeedbacks = this.casFeedbacks.filter(f => f.id !== feedbackId);
                    this.toast.success('The CAS feedback has been deleted.');
                } catch (error) {
                    this.handleError(error, 'Could not delete the CAS feedback.');
                }
            }
        },
        triggerFileInput() { if (this.hasEditPermission) this.$refs.fileInput.click(); },
        dragover(event) { if (this.hasEditPermission) event.currentTarget.classList.add('dragover'); },
        dragleave(event) { if (this.hasEditPermission) event.currentTarget.classList.remove('dragleave'); },
        drop(event) {
            if (!this.hasEditPermission) return;
            event.currentTarget.classList.remove('dragover');
            this.handleFiles(event.dataTransfer.files);
        },
        handleFileSelect(event) {
            if (!this.hasEditPermission) return;
            this.handleFiles(event.target.files);
            event.target.value = ''; 
        },
        async handleFiles(files) {
            if (!this.hasEditPermission || files.length === 0) return;

            const newUploadItems = Array.from(files)
                .filter(file => {
                    if (file.size > 5 * 1024 * 1024) {
                        this.toast.error(`File ${file.name} is too large. Max size is 5MB.`);
                        return false;
                    }
                    return true;
                })
                .map(file => ({ file, progress: 0, status: 'uploading', message: 'Queued...' }));

            if (newUploadItems.length === 0) return;
            this.fileUploads = [...this.fileUploads, ...newUploadItems];
            const uploadPromises = newUploadItems.map(item => this.uploadFile(item));
            await Promise.allSettled(uploadPromises);
            await this.refreshUploadsList();
            setTimeout(() => { this.fileUploads = []; }, 2000);
        },
        async uploadFile(uploadItem) {
            const formData = new FormData();
            formData.append('fileInput[]', uploadItem.file);
            formData.append('application_id', this.application.id);
            try {
                await axios.post('/api/upload/store', formData, {
                    onUploadProgress: (e) => {
                        if (e.lengthComputable) {
                            uploadItem.progress = Math.min(99, Math.round((e.loaded * 100) / e.total));
                            uploadItem.message = 'Uploading...';
                        }
                    }
                });
                uploadItem.progress = 100;
                uploadItem.status = 'success';
                uploadItem.message = 'Success!';
            } catch (error) {
                uploadItem.progress = 100;
                uploadItem.status = 'error';
                uploadItem.message = error.response?.data?.message || 'Upload Failed.';
                this.toast.error(`Failed to upload ${uploadItem.file.name}:`, error);
                throw error;
            }
        },
        async refreshUploadsList() {
            try {
                const { data } = await axios.get(`/api/applications/record/${this.$route.params.id}`);
                this.uploads = data.uploads || [];
            } catch(error) {
                this.handleError(error, 'Could not refresh the file list.');
            }
        },
        async deleteUpload(uploadId) {
            if (!this.hasEditPermission) return;
            const result = await Swal.fire({ title: 'Are you sure?', text: 'Do you want to delete this document?', icon: 'warning', showCancelButton: true });
            if (result.isConfirmed) {
                try {
                    await axios.delete(`/api/upload/destroy/${uploadId}`);
                    this.toast.success('Document has been deleted.');
                    await this.refreshUploadsList();
                } catch (error) {
                    this.handleError(error, 'Could not delete the document.');
                }
            }
        },
        async saveStatus() {
            if (!this.hasEditPermission) return;
            this.isSavingStatus = true;
            try {
                const { data } = await axios.post(`/api/applications/savestatus`, {
                    document: this.application.status,
                    application_id: this.application.id,
                });
                this.toast.success(data.message || 'Status updated!');
                this.application.status = data.new_status || this.application.status;
            } catch (error) {
                this.handleError(error, 'Error saving status.');
            } finally {
                this.isSavingStatus = false;
            }
        },
        handleError(error, defaultMessage) {
            console.error(`${defaultMessage}:`, error);
            const errorMsg = error.response?.data?.message || defaultMessage;
            this.toast.error(errorMsg);
        },
        handleFormError(field, error) {
            console.error(`Failed to fetch ${field}:`, error);
            this.toast.error(`Could not load ${field} list.`);
        },
        formatDate(dateString, withTime = false) {
            if (!dateString) return '';
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            if (withTime) {
                options.hour = '2-digit';
                options.minute = '2-digit';
            }
            return new Date(dateString).toLocaleDateString(undefined, options);
        },
        async hideRemarks() {
            if (!this.hasEditPermission) return;
            this.showRemarks = false; // Optimistically hide the UI element
            try {
                // The API call to update 'show_remarks' is rejected by the backend.
                // A logical workaround is to clear the note's content to persist the dismissal.
                // This will cause `this.application.notes` to be false on the next page load,
                // thus preventing the ribbon from reappearing.
                await axios.put(`/api/applications/${this.application.id}/update-field`, {
                    field: 'notes',
                    value: null
                });
                // Update local state to match the change
                this.application.notes = null;
                this.application.show_remarks = 0;
            } catch (error) {
                // If the update fails, revert the optimistic UI change.
                this.showRemarks = true;
                this.handleError(error, 'An error occurred while dismissing the review note.');
            }
        },
        async fetchCountries() {
            try {
                const { data } = await axios.get('/api/data-entries/get-countries');
                if (data.status === 'success') {
                    this.formOptions.countries = data.data;
                }
            } catch (error) {
                this.handleFormError("countries", error);
            }
        },
    },
};
</script>

<style scoped>
/* Make the card a positioning context for the absolute-positioned ribbon */
.card {
  position: relative;
  /* overflow: visible is crucial for the ribbon to show outside the card's bounds */
  overflow: visible;
}

/* --- NEW 3D RIBBON STYLES --- */

.remarks-host {
  position: absolute;
  top: 20px;
  right: -8px; /* Extend slightly more to accommodate the 3D effect */
  width: 330px;
  z-index: 10;
  /* The drop-shadow is the master shadow for the whole component */
  filter: drop-shadow(2px 4px 5px rgba(0, 0, 0, 0.4));
}

.ribbon-wrapper {
  position: relative;
  background: linear-gradient(to right, #2fbb54, #28a745); /* Main gradient */
  color: white;
  padding: 1rem 1rem 1rem 2.5rem;
  z-index: 2; /* Main ribbon is on top */

  /* Shape for the main front-facing part of the ribbon */
  clip-path: polygon(
    0 0,                   /* Top-left */
    100% 0,                  /* Top-right */
    100% calc(100% - 20px),  /* Bottom-right corner (start of the notch) */
    calc(100% - 20px) 100%, /* Bottom-right corner (end of the notch) */
    0 100%,                   /* Bottom-left */
    20px 50%                 /* V-cut on the left side */
  );
}

/* ::before creates the part of the ribbon that's "folded over the top" of the card */
.ribbon-wrapper::before {
  content: '';
  position: absolute;
  top: -10px; /* Sits 10px above the main ribbon, appearing over the card's edge */
  right: 0px;
  height: 10px;
  width: 50px;
  /* Use a darker shade for the "back" of the ribbon */
  background: #218838;
  /* Angled cut on the left to match the fold's perspective */
  clip-path: polygon(0 0, 100% 0, 100% 100%, 25% 100%);
  z-index: -1; /* Sits behind the main ribbon */
}

/* ::after creates the crucial fold shadow and the bottom fold corner */
.ribbon-wrapper::after {
  content: '';
  position: absolute;
  bottom: 0;
  right: 0;
  width: 20px;
  height: 20px;
  /* A dark gradient to simulate a crease shadow */
  background: linear-gradient(to top right, rgba(0,0,0,0.5) 50%, transparent 51%);
  z-index: 3; /* Must be on top of the main ribbon to cast the shadow */
}


.ribbon-content {
  position: relative;
  z-index: 4; /* Content text must be on top of everything */
}

.ribbon-content .btn-close {
  position: absolute;
  top: -2px;
  right: 2px;
  filter: invert(1) grayscale(100%) brightness(2);
  --bs-btn-close-opacity: 0.8;
  --bs-btn-close-hover-opacity: 1;
}

.ribbon-content .btn-close:focus {
    box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.5);
}

/* --- END 3D RIBBON STYLES --- */


/* Transition for unfurling effect (unchanged) */
.unfurl-enter-active,
.unfurl-leave-active {
    transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55), opacity 0.3s;
}
.unfurl-enter-from,
.unfurl-leave-to {
  transform: scaleX(0);
  opacity: 0;
}
/* This makes the animation start from the top-right corner */
.ribbon-wrapper {
    transform-origin: top right;
}


* {
  box-sizing: border-box;
}

.outline-button{
    border-radius: 8px !important;
    padding: 6px 20px !important;
    background-color: white !important;
    color: green;
}

.card-header-tabs{
    margin-left: 0px !important;
    margin-right: 0px !important;
    margin-bottom: 0px !important;
}
.card-header{
    padding: 0px 20px !important;

}

.nav-tabs, .nav-tabs .nav-link{
    border-radius: 0px !important;
    box-shadow: none;
}
.page-container {
    background-color: white;
    display: flex;
    flex-direction: row;
    gap: 1rem;
    padding: 1rem;
}

.create-section {
    flex: 0 0 500px;
    border-radius: 8px;
    padding: 1rem;
}

.index-section {
    flex: 1;
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    height: 600px;
    min-width: 0;
}

.section-title {
    color: green;
    font-size: 20px;
    margin-bottom: 20px !important;
    font-family: 'Poppins', sans-serif;
}

.upload-container {
    border: 2px dashed #24a52f;
    border-radius: 8px;
    min-height: 510px;
    text-align: center;
    cursor: pointer;
    position: relative;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-color: rgba(252, 247, 247, 0.5);
}

.nav-pills .nav-link {
    border-radius: 0px !important;
}

.upload-container.dragover {
    border-color: #4CAF50;
    background-color: #f0f8f0;
}

.upload-icon img {
    width: 80px;
    height: 80px;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.progress-section {
    width: 100%;
    max-height: 350px;
    overflow-y: auto;
    padding: 1rem;
}

.file-item {
    margin-bottom: 1rem;
    text-align: left;
}

.progress-bar {
    width: 100%;
    background-color: #e0e0e0;
    border-radius: 5px;
    height: 8px;
    margin: 5px 0;
}

.progress-fill {
    height: 100%;
    background-color: #4CAF50;
    border-radius: 5px;
    transition: width 0.3s ease;
}

.index-section-content {
    height: calc(100% - 60px);
    padding: 10px;
    border: 2px dashed #24a52f;
    border-radius: 8px;
    overflow-y: auto;
}

.documents-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 10px;
    table-layout: fixed;
}

.documents-table td {
    padding: 8px 12px;
    vertical-align: middle;
}

.documents-table td:first-child {
    display: flex;
    align-items: center;
    gap: 10px;
}

.documents-table td:last-child {
    width: 80px; /* Action buttons column */
}

.file-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-grow: 1;
    min-width: 0;
}

.file-icon {
    font-size: 1.5rem;
    flex-shrink: 0;
}

a.file-link {
    text-decoration: none;
    color: inherit;
    flex-grow: 1;
    min-width: 0;
}

.file-name {
    font-size: 1rem;
    font-family: Poppins, sans-serif;
    color: #333;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.file-name:hover {
    color: #4CAF50;
}

.upload-date {
    color: #666;
    font-size: 0.9rem;
    white-space: nowrap;
    width: 120px;
}

.action-buttons {
    display: flex;
    gap: 12px;
    align-items: center;
    justify-content: flex-end;
}

.view-link {
    text-decoration: none;
}

.field-value {
    position: relative;
    cursor: pointer;
    padding: 2px 4px;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.field-value:hover {
    background-color: #e9f5ea;
}

.field-value .edit-icon {
    opacity: 0;
    transition: opacity 0.2s;
}

.field-value:hover .edit-icon {
    opacity: 1;
}

.text-icon-person { color: #C62828; }
.text-icon-telephone { color: #43A047; }
.text-icon-envelope { color: #1E88E5; }
.text-icon-journal-check { color: #5E35B1; }
.text-icon-book { color: #D81B60; }
.text-icon-award { color: #FFB300; }
.text-icon-file-earmark-text { color: #C62828; }
.text-icon-translate { color: #43A047; }
.text-icon-book-half { color: #8E24AA; }
.text-icon-calendar { color: #1E88E5; }
.text-icon-building { color: #3949AB; }
.text-icon-globe { color: #C62828; }
.text-icon-geo-alt { color: #558B2F; }
.text-icon-calendar-event { color: #2E7D32; }
.text-icon-wallet { color: #FFB300; }
.text-icon-person-plus { color: #6A1B9A; }
.text-icon-person-badge { color: #0277BD; }
.text-icon-mortarboard { color: #00695C; }

#progressBar {
    list-style: none;
    padding: 0;
    margin: 20px 0;
    display: flex;
    justify-content: space-between;
    position: relative;
}

#progressBar li {
    flex: 1;
    text-align: center;
    position: relative;
    font-size: 0.85rem;
    color: #6c757d;
}

#progressBar li a {
    text-decoration: none;
    color: inherit;
    display: block;
    padding: 10px 5px;
}

#progressBar li .step {
    display: block;
    width: 30px;
    height: 30px;
    line-height: 28px;
    border: 1px solid #ced4da;
    border-radius: 50%;
    margin: 0 auto 10px auto;
    background-color: #fff;
    color: #6c757d;
    font-weight: bold;
}

#progressBar li.disabled .step { border-color: #e9ecef; color: #adb5bd; }
#progressBar li.current { color: #0d6efd; font-weight: bold; }
#progressBar li.current .step { border-color: #0d6efd; background-color: #0d6efd; color: #fff; }
#progressBar li.completed { color: #198754; }
#progressBar li.completed .step { border-color: #198754; background-color: #198754; color: #fff; }
#progressBar li:not(:last-child)::after {
    content: '';
    position: absolute;
    width: calc(100% - 40px);
    height: 2px;
    top: 15px;
    left: calc(50% + 20px);
    z-index: -1;
    transition: background-color 0.3s ease;
    background-color: #e9ecef;
}

#progressBar li.completed::after { background-color: #198754; }
#progressBar li.current~li::after { background-color: #e9ecef; }
#progressBar li.current:not(:last-child)::after {
    background: linear-gradient(to right, #198754 50%, #e9ecef 50%);
    background-size: 200% 100%;
    background-position: right bottom;
}

@media (max-width: 1200px) {
    .page-container {
        flex-direction: column;
    }

    .create-section,
    .index-section {
        flex: none;
        width: 100%;
        height: auto;
    }
}
</style>
```