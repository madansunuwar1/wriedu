<?php


use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\DataEntryController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ApplicationHistoryController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Api\ApplicationApiController;
use App\Http\Controllers\Api\ChatApiController;
use App\Http\Controllers\Api\CasApiController;
use App\Http\Controllers\Api\UploadApiController;
use App\Http\Controllers\Api\EnquiryApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\LeadApiController;
use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\ActivityApiController;
use App\Http\Controllers\Api\RawLeadApiController;
use App\Http\Controllers\Api\PayableApiController;
use App\Http\Controllers\Api\ReceivableApiController;
use App\Http\Controllers\Api\PartnerApiController;
use App\Http\Controllers\Api\NoticeApiController;
use App\Http\Controllers\Api\LeadCommentApiController;
use App\Http\Controllers\Api\CommentApiController;
use App\Http\Controllers\Api\CalendarApiController;
use App\Http\Controllers\Api\StatusApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\CounselorApiController;
use App\Http\Controllers\Api\UniversityApiController;
use App\Http\Controllers\Api\FinanceApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\DataEntryApiController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\RegisteredUserApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\RoleApiController;
use App\Http\Controllers\Api\CommissionApiController;
use App\Http\Controllers\ComissionController;
use App\Http\Controllers\CommissionPayableController;
use App\Http\Controllers\CommissionTransactionController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\CommentaddController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RawLeadController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\CommentPassController;
use App\Http\Controllers\LeadCommentController;
use App\Http\Controllers\OTPVerificationController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\NameController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CASFeedbackController;
use App\Models\Application;
use App\Http\Controllers\RoleController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\VerificationController;
use App\Providers\RouteServiceProvider;

use App\Jobs\ProcessNewUserNotifications;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\Enquiry;
use App\Models\Lead;
use App\Models\DataEntry;
use App\Models\Document;


Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard


    // Profile routes
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::controller(ApplicationController::class)->group(function () {
        // List all forms
        Route::get('form/index', 'index')
            ->name('backend.form.index');

        // Create new form
        Route::get('/form/create', 'create')
            ->name('backend.form.create');

        Route::post('/form/store', 'store')
            ->name('backend.applications.store');

        // Edit and update form
        Route::get('/form/edit/{id}', 'edit')
            ->name('application.edit');
        Route::put('/form/{id}', 'update')
            ->name('backend.form.update');
        Route::post('/forms/{id}/update', 'update')
            ->name('form.update.ajax');

        // Delete form
        Route::delete('/form/destroy/{id}', 'destroy')
            ->name('application.destroy');

        // AJAX route for single field updates
        Route::post('/update-student-details', 'updateSingleField');
    });




    Route::middleware('auth:sanctum')->prefix('api')->group(function () {
        // Applications routes
        Route::get('/applications', [ApplicationApiController::class, 'index']);
        Route::post('/application', [ApplicationApiController::class, 'store']);

        Route::get('/applications/record/{id}/{type?}', [ApplicationApiController::class, 'record']);

        Route::post('/applications/record/{id}/{type?}', [ApplicationApiController::class, 'recordApplication']);
        Route::post('/applications/import', [ApplicationApiController::class, 'import']);
        Route::post('/applications/update-single-field', [ApplicationApiController::class, 'updateSingleField']);
        Route::put('/applications/{id}/update-field', [ApplicationApiController::class, 'updateField']);


        // CORRECTED: Changed from DELETE to POST to match the frontend call, made {id} required.
        Route::post('/applications/{id}/withdraw', [ApplicationApiController::class, 'withdraw'])->name('api.applications.withdraw');

        Route::post('/applications/savestatus', [ApplicationApiController::class, 'saveDocumentStatus']);
        Route::get('/user-applications/{userId}', [ApplicationApiController::class, 'getUserApplications']);
        Route::post('/upload/store', [UploadController::class, 'store']);
        Route::post('/application/studentstore', [ApplicationApiController::class, 'studentstore']);
        Route::get('/university-data', [ApplicationApiController::class, 'getUniversityDataForVue']);
        Route::get('applications/export-all', [ApplicationApiController::class, 'exportAll']);
        Route::get('/comments/types', [ApplicationApiController::class, 'comment']);


        Route::get('/calendar', [CalendarApiController::class, 'index']);
        Route::get('/calendar/month-data', [CalendarApiController::class, 'getMonthData']);
        Route::post('/convert/ad-to-bs', [CalendarApiController::class, 'convertAdToBs']);
        Route::post('/convert/bs-to-ad', [CalendarApiController::class, 'convertBsToAd']);
        Route::get('/bs-month-info', [CalendarApiController::class, 'getBsMonthInfo']);
        Route::post('/events', [CalendarApiController::class, 'storeEvent']);
        Route::get('/notices/{noticeId}', [CalendarApiController::class, 'getNoticeDetails']);



        Route::get('/enquiries', [EnquiryApiController::class, 'index']);
        Route::post('/enquiries', [EnquiryApiController::class, 'store']);
        Route::get('/enquiries/create', [EnquiryApiController::class, 'create']);
        Route::get('/enquiries/{id}', [EnquiryApiController::class, 'edit']);
        Route::put('/enquiries/{id}', [EnquiryApiController::class, 'update']);

        // Important:  This route fetches the main enquiry *and* the comments!
        Route::get('/enquiries/record/{id}', [EnquiryApiController::class, 'records']);
        Route::delete('/enquiries/{id}', [EnquiryApiController::class, 'destroy']);
        Route::post('/enquiries/import', [EnquiryApiController::class, 'import']);

        Route::post('/comments', [LeadCommentApiController::class, 'store']);
        Route::put('/comments/{id}', [LeadCommentApiController::class, 'update']);

        // Route for deleting a comment
        Route::delete('/comments/{id}', [LeadCommentApiController::class, 'destroy']);
        Route::get('/reminders', [LeadCommentApiController::class, 'getReminders']);
        Route::post('/reminders/{id}/complete', [LeadCommentApiController::class, 'markComplete']);

        Route::get('/user/current', function () {
            return response()->json([
                'user' => [
                    'id' => auth()->id(),
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'avatar' => auth()->user()->avatar,
                    'last_name' => auth()->user()->last_name ?? ''
                ]
            ]);
        });


        Route::get('/notices', [NoticeApiController::class, 'index']);
        Route::post('/notices', [NoticeApiController::class, 'store']);
        Route::get('/notices/{id}', [NoticeApiController::class, 'show']);
        Route::put('/notices/{id}', [NoticeApiController::class, 'update']);
        Route::delete('/notices/{id}', [NoticeApiController::class, 'destroy']);
        Route::post('/notices/{id}/mark-as-read', [NoticeApiController::class, 'markAsRead']);
        Route::get('/notifications', [NoticeApiController::class, 'getNotifications']);

        Route::get('/notifications', [NotificationApiController::class, 'getNotifications']);

        // Route to mark a single notification as read
        Route::post('/notifications/{id}/mark-as-read', [NotificationApiController::class, 'markAsRead']);

        // Route to mark all of the user's notifications as read
        Route::post('/notifications/mark-all-as-read', [NotificationApiController::class, 'markAllAsRead']);

        Route::get('/comments/add', [CommentApiController::class, 'index']);
        Route::post('/comments/create', [CommentApiController::class, 'store']);
        Route::put('/comments/{id}', [CommentApiController::class, 'update']);
        Route::delete('/comments/{id}', [CommentApiController::class, 'destroy']);



        Route::get('/documents', [StatusApiController::class, 'index'])->name('backend.document.index');
        Route::get('/documents/create', [StatusApiController::class, 'create'])->name('backend.document.create');
        Route::post('/documents', [StatusApiController::class, 'store'])->name('backend.document.store');
        Route::get('/documents/{id}/edit', [StatusApiController::class, 'edit'])->name('backend.document.edit');
        Route::put('/documents/{id}', [StatusApiController::class, 'update'])->name('backend.document.update');
        Route::delete('/documents/{id}', [StatusApiController::class, 'destroy'])->name('backend.document.destroy');
        Route::put('/documents/{id}', [StatusApiController::class, 'updateIds']);



        Route::get('/products', [ProductApiController::class, 'index']);
        Route::post('/products', [ProductApiController::class, 'store']);
        Route::delete('/products/{id}', [ProductApiController::class, 'destroy']);
        Route::get('/names', [CounselorApiController::class, 'index']);
        Route::post('/names', [CounselorApiController::class, 'store']);
        Route::put('/names/{id}', [CounselorApiController::class, 'update']);
        Route::delete('/names/{id}', [CounselorApiController::class, 'destroy']);



        Route::get('/universities', [UniversityApiController::class, 'index']);
        Route::post('/universities', [UniversityApiController::class, 'store']);
        Route::put('/universities/{id}', [UniversityApiController::class, 'update']);
        Route::delete('/universities/{id}', [UniversityApiController::class, 'destroy']);
        Route::get('/course-finder-data', [UniversityApiController::class, 'getCourseFinderData']);
        Route::get('/university-profile/{id}', [UniversityApiController::class, 'getUniversityProfileData']);

        Route::get('/manage/universities', [UniversityApiController::class, 'getManagedUniversities']);
        Route::post('/manage/universities', [UniversityApiController::class, 'storeManagedUniversity']);
        Route::put('/manage/universities/{id}', [UniversityApiController::class, 'updateManagedUniversity']);
        Route::delete('/manage/universities/{id}', [UniversityApiController::class, 'destroyManagedUniversity']);


        Route::get('/finance', [FinanceApiController::class, 'index'])->name('finance.index');
        Route::get('/finance/create', [FinanceApiController::class, 'create'])->name('finance.create');
        Route::post('/finance', [FinanceApiController::class, 'store'])->name('finance.store');
        Route::get('/finance/{id}/edit', [FinanceApiController::class, 'edit'])->name('finance.edit');
        Route::put('/finance/{id}', [FinanceApiController::class, 'update'])->name('finance.update');
        Route::delete('/finance/{id}', [FinanceApiController::class, 'destroy'])->name('finance.destroy');



        Route::get('/commission-payable', [PayableApiController::class, 'index']);
        Route::post('/commission-payable', [PayableApiController::class, 'store']);
        Route::get('/commission-payable/{id}', [PayableApiController::class, 'show']);
        Route::put('/commission-payable/{id}', [PayableApiController::class, 'update']);
        Route::patch('/commission-payable/{id}', [PayableApiController::class, 'update']);
        Route::delete('/commission-payable/{id}', [PayableApiController::class, 'destroy']);



        Route::get('/leads', [LeadApiController::class, 'getLeadsApi']);
        Route::get('/leads/export', [LeadApiController::class, 'exportLeadsApi']);
        Route::post('/leads/import', [LeadApiController::class, 'importApi']);
        Route::post('/leads', [LeadApiController::class, 'storeapi']);
        Route::get('/leads/create-data', [LeadApiController::class, 'getCreateData']);
        Route::get('/leads/{lead}', [LeadApiController::class, 'getLeadRecordApi']);
        Route::put('/leads/{lead}/update-field', [LeadApiController::class, 'updateField']);
        Route::delete('/leads/{lead}', [LeadApiController::class, 'destroyApi']);
        Route::post('/leads/{lead}/convert-to-hot', [LeadApiController::class, 'convertToHot']);
        Route::post('/leads/{lead}/withdraw', [LeadApiController::class, 'withdraw']);
        Route::post('/leads/{lead}/forward', [LeadApiController::class, 'forwardApplication']);
        Route::get('/users/list', [LeadApiController::class, 'getUserListApi'])->middleware('auth');



        Route::post('/leads/{lead}/comments', [LeadCommentApiController::class, 'storeApi']);
        Route::post('/comments/{comment}/snooze', [LeadCommentApiController::class, 'snoozeCommentApi']);





        Route::get('/raw-leads', [RawLeadApiController::class, 'index'])->name('api.rawleads.index');
        Route::get('/raw-leads/filter-options', [RawLeadApiController::class, 'getFilterOptions'])->name('api.rawleads.filterOptions');
        Route::post('/raw-leads/bulk-assign', [RawLeadApiController::class, 'bulkAssign'])->name('api.rawleads.bulkAssign');
        Route::post('/raw-leads/bulk-destroy', [RawLeadApiController::class, 'bulkDestroy'])->name('api.rawleads.bulkDestroy');
        Route::delete('/raw-leads/{rawLead}', [RawLeadApiController::class, 'destroy'])->name('api.rawleads.destroy');
        Route::get('/raw-leads/export-all', [RawLeadApiController::class, 'exportAllData'])->name('rawlead.export.all');
        Route::get('/raw-leads/upload', [RawLeadApiController::class, 'upload'])->name('backend.leadform.import');
        Route::get('/raw-leads/{rawLead}/edit', [RawLeadApiController::class, 'edit'])->name('rawlead.edit');
        Route::post('/raw-leads/import', [RawLeadApiController::class, 'import'])->name('api.rawlead.import');
        Route::get('/raw-leads/import-config', [RawLeadApiController::class, 'getImportConfig'])->name('api.rawlead.import-config');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // 2. This is the API endpoint your Vue component will call to get all its data.
        // It's good practice to prefix API-like routes.
        Route::get('/dashboard/stats', [DashboardApiController::class, 'getDashboardStats']);
        // This route for the calendar iframe, which you already have.
        Route::get('/backend/calendar/iframe', function () {
            // Assuming you have a view at resources/views/backend/calendar/iframe.blade.php
            return view('backend.calendar.iframe');
        })->name('backend.calendar.iframe');

        //activity log controller//
        Route::get('/users-activity', [ActivityApiController::class, 'getUsersWithActivity'])->name('users');
        Route::get('/check-online-users', [ActivityApiController::class, 'checkOnlineUsers']);
        Route::get('/logs', [ActivityApiController::class, 'getLogs'])->name('logs');
        Route::get('/stats', [ActivityApiController::class, 'getStats'])->name('stats');
        Route::get('/descriptions', [ActivityApiController::class, 'getDescriptions'])->name('descriptions');

        // Comments routes
        // Route::get('/comments', [ApplicationApiController::class, 'comment']);
        // Route::get('/comments/index', [EnquiryApiController::class, 'indexcomment']);
        // Route::get('/comment', [EnquiryApiController::class, 'comment']);
        // Route::get('/comments/index', [ApplicationApiController::class, 'indexcomment']);
        // Route::post('/comments', [ApplicationApiController::class, 'store']);
        // Route::get('/comments/{id}/edit', [ApplicationApiController::class, 'edit']);
        // Route::put('/comments/{id}', [ApplicationApiController::class, 'update']);
        Route::get('/roles', [RoleApiController::class, 'index']);
        Route::post('/roles', [RoleApiController::class, 'store']);
        Route::get('/roles/create', [RoleApiController::class, 'create']);
        Route::get('/roles/{role}', [RoleApiController::class, 'show']);
        Route::get('/roles/{role}/edit', [RoleApiController::class, 'edit']);
        Route::put('/roles/{role}', [RoleApiController::class, 'update']);
        Route::get('/roles/{id}', [RoleApiController::class, 'showupdate']);
        Route::delete('/roles/{role}', [RoleApiController::class, 'destroy']);
        Route::get('/roles/create', [RoleApiController::class, 'create'])->name('roles.create');
        Route::get('/all-permissions', [RoleApiController::class, 'getAllPermissions']);
        Route::get('/user-permissions', [RoleApiController::class, 'getUserPermissions']);

        Route::get('/university-data', [DataEntryApiController::class, 'getUniversityDataForVue']);
        Route::post('/data-entries', [DataEntryApiController::class, 'store']);
        Route::post('/data-entries/import', [DataEntryApiController::class, 'import']);
        Route::delete('/data-entries/{id}', [DataEntryApiController::class, 'destroy']);
        Route::get('/data-entries/form-options', [DataEntryApiController::class, 'getFormOptions']);
        Route::get('/data-entries/get-countries', [DataEntryApiController::class, 'getCountries']);
        Route::get('/data-entries/get-locations-by-country', [DataEntryApiController::class, 'getLocationsByCountry']);
        Route::get('/data-entries/get-universities-by-location', [DataEntryApiController::class, 'getUniversitiesByLocation']);
        Route::get('/data-entries/get-courses-by-university', [DataEntryApiController::class, 'getCoursesByUniversity']);
        Route::get('/data-entries/get-intakes-by-course', [DataEntryApiController::class, 'getIntakesByCourse']);

        Route::get('/auth/user', [ApiAuthController::class, 'user']);

        Route::get('/users', [RegisteredUserApiController::class, 'index']);
        Route::post('/users/{user}/update-role', [UserRoleController::class, 'updateRole']);
        Route::post('/users/{user}/update-assignments', [RegisteredUserApiController::class, 'updateAssignments']);
        Route::delete('/users/{user}', [RegisteredUserApiController::class, 'destroy']);
        Route::post('/users/{user}/restore', [RegisteredUserApiController::class, 'restore']);
        Route::get('/users/export', [RegisteredUserApiController::class, 'export']);
        Route::post('/users/store', [RegisteredUserApiController::class, 'store']);


        Route::get('/user/profile', [ProfileApiController::class, 'show'])->name('api.auth.user.profile.show');
        Route::get('/user/profile/{userId?}', [ProfileApiController::class, 'show'])->name('api.user.profile.show');
        // Fetches the authenticated user's profile (e.g., /api/user/profile)


        // UPDATING PROFILES (This is the key change)
        // Updates a specific user by ID. Add authorization logic here!
        Route::post('/user/profile/update/{userId?}', [ProfileApiController::class, 'updateProfile'])->name('api.user.profile.update');

        // UPDATING PASSWORD (This should only apply to the authenticated user)
        Route::post('/user/password/update', [ProfileApiController::class, 'updatePassword'])->name('api.auth.user.password.update');

        Route::post('/broadcasting/auth', [BroadcastController::class, 'authenticate'])->middleware('web');
        // Route::post('/logout', [AuthController::class, 'logout']);

        // Route to get the currently authenticated user
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::post('/auth/generate-token', [AuthTokenController::class, 'generateToken']);
        Route::post('/auth/refresh', [AuthTokenController::class, 'refreshToken']);
        Route::get('/auth/validate', [AuthTokenController::class, 'validateToken']);

        Route::get('/chat/current-user', [ChatApiController::class, 'getCurrentUser']);
        Route::get('/chat/users', [ChatApiController::class, 'getUsers']);
        Route::get('/chat/conversations/{userId}', [ChatApiController::class, 'getConversation']);
        Route::post('/chat/send', [ChatApiController::class, 'sendMessage']);
        Route::post('/chat/typing', [ChatApiController::class, 'sendTypingIndicator']);
        Route::post('/chat/mark-seen', [ChatApiController::class, 'markMessagesAsSeen']);

        Route::get('/commission-payable', [PayableApiController::class, 'index']);
        Route::post('/commission-payable', [PayableApiController::class, 'store']);
        Route::get('/commission-payable/{id}', [PayableApiController::class, 'show']);
        Route::post('/commission-payables/{id}', [PayableApiController::class, 'updatecommission']);
        Route::post('/commission-payable/{id}', [PayableApiController::class, 'update']);
        Route::delete('/commission-payable/{id}', [PayableApiController::class, 'destroy']);
        Route::get('/partners/name', [PayableApiController::class, 'getPartners']);
        Route::get('/intakes', [PayableApiController::class, 'getintakes']);
        Route::get('/payable/{id}', [PayableApiController::class, 'accountpayableview']); // Add this route

        Route::get('/commissions', [ReceivableApiController::class, 'index'])->name('commissions.index');
        Route::get('/commission-form-data', [ReceivableApiController::class, 'getFormData']);
        Route::post('/commissions', [ReceivableApiController::class, 'store']);
        Route::get('/commission/default', [ReceivableApiController::class, 'showDefault']);
        Route::get('/commission/{commission}', [ReceivableApiController::class, 'show']);
        Route::put('/commission/{commission}', [ReceivableApiController::class, 'update']);
        Route::patch('/commission/{commission}', [ReceivableApiController::class, 'update']);
        Route::delete('/commissions/{commission}', [ReceivableApiController::class, 'destroy']);
        Route::post('/commission/{commission}/commission-type', [ReceivableApiController::class, 'addCommissionType']);
        Route::delete('/commission/{commission}/commission-type/{type}', [ReceivableApiController::class, 'deleteCommissionType']);
        Route::get('/accountreceivable/{id}', [ReceivableApiController::class, 'accountreceivableview']);
        Route::post('/accountreceivableview/{id}', [ReceivableApiController::class, 'updatecommission']);

        Route::get('/partners', [PartnerApiController::class, 'index']);
        Route::post('/partners', [PartnerApiController::class, 'store']);
        Route::get('/partners/{id}', [PartnerApiController::class, 'show']);
        Route::put('/partners/{id}', [PartnerApiController::class, 'update']);
        Route::post('/partners/bulk-upload', [PartnerApiController::class, 'bulkUpload']);
        Route::get('/partners/download-template', [PartnerApiController::class, 'downloadTemplate']);
        Route::delete('/partners/{partner}', [PartnerApiController::class, 'destroy']);

        Route::get('/applications/{application}/cas-feedbacks', [CasApiController::class, 'index']);
        Route::post('/cas-feedbacks', [CasApiController::class, 'store']);
        Route::put('/cas-feedbacks/{casFeedback}', [CasApiController::class, 'update']);
        Route::delete('/cas-feedbacks/{casFeedback}', [CasApiController::class, 'destroy']);

        Route::get('/commission-rates', [CommissionApiController::class, 'index']);
        Route::delete('/commission-rates/{id}', [CommissionApiController::class, 'destroy']);
        Route::post('/commission-rates/{id}/duplicate', [CommissionApiController::class, 'duplicate']);
        Route::post('/commission-rates/import', [CommissionApiController::class, 'import']);
        Route::get('/commission-rates/export', [CommissionApiController::class, 'export']);
    });



















    Route::middleware('auth:sanctum')->prefix('app')->group(function () {
        Route::get('/{any}', [ApplicationApiController::class, 'webIndex'])->where('any', '.*');
    });
    Route::get('/enquiries/record/{id}', [ApplicationApiController::class, 'webIndex']);
    Route::get('/admin', [ApplicationApiController::class, 'webIndex']);


















    // Lead routes
    Route::controller(LeadController::class)->group(function () {
        Route::get('/leadform/create', 'create')->name('backend.leadform.create');
        Route::get('/leadform/index', 'index')->name('backend.leadform.index');
        Route::post('/leadform/store', 'store')->name('backend.leadform.store');
        Route::get('/lead/{id}/edit', 'edit')->name('lead.edit');
        Route::put('/backend/leadform/{id}', 'update')->name('backend.leadform.update');
        Route::delete('/lead/{id}', 'destroy')->name('lead.destroy');
        Route::get('/leadform/indexs', 'indexs')->name('backend.leadform.indexs');
        Route::get('/leadform/records/{id}', 'records')->name('backend.leadform.records');
        Route::post('/leadform/import', 'import')->name('leadform.import');
        Route::get('/upload/file', 'upload')->name('backend.leadform.upload');
        Route::post('/save-document-status', 'saveDocumentStatus')->name('save.document.status');
        Route::post('/backend/leads/{id}/convert-to-hot', 'convertToHot')->name('backend.lead.convertToHot');

        Route::get('/leadform/ui', 'ui')->name('backend.leadform.ui');

        Route::put('/backend/leadform/{id}/update-field', [LeadController::class, 'updateField'])
            ->name('backend.leadform.update-field');


        Route::delete('backend/lead/withdraw/{id}', 'withdraw')
            ->name('backend.lead.withdraw');
    });

    // Data Entry routes
    Route::controller(DataEntryController::class)->group(function () {
        Route::get('/dataentry/create', 'create')->name('backend.dataentry.create');
        Route::get('/dataentry/index', 'index')->name('backend.dataentry.index');
        Route::post('/dataentry/store', 'store')->name('backend.dataentry.store');
        Route::get('/dataentry/{id}/edit', 'edit')->name('data_entrie.edit');
        Route::put('/dataentry/{id}', 'update')->name('backend.dataentry.update');
        Route::delete('/dataentry/{id}', 'destroy')->name('data_entrie.destroy');
        Route::post('/dataentry/import', 'import')->name('dataentry.import');
        Route::get('/dataentry/indexs', 'indexs')->name('backend.dataentrys.indexs');
    });

    // User routes
    Route::controller(RegisteredUserController::class)->group(function () {
        Route::get('/user/index', 'index')->name('backend.user.index');
        Route::get('/user/profile', 'profile')->name('backend.user.profile');
        Route::post('/dataentry', 'store')->name('backend.user.store');
        Route::get('/usert/{id}/edit', 'edit')->name('user.edit');
        Route::put('/usert/{id}', 'update')->name('backend.user.update');
        Route::delete('/usert/{id}', 'destroy')->name('user.destroy');
        Route::post('/user/{id}/update-application', 'updateAssignments')->name('user.update-application');
        Route::match(['get', 'post'], '/user/{id}/update-lead', 'updateAssignments')->name('user.update-lead');
        Route::post('/user/{id}/restore',  'restore')->name('user.restore');
    });

    // Application History routes


    // Application routes
    Route::controller(ApplicationController::class)->group(function () {
        Route::get('/application/sample', 'sample')->name('backend.application.sample');
        Route::get('/application/studentapplication', 'studentapplication')->name('backend.application.studentapplication');
        Route::post('/applications/studentstore', 'studentstore')->name('backend.application.studentstore');
    });


    Route::controller(NoticeController::class)->group(function () {
        Route::get('/notice/create', 'create')->name('backend.notice.create');
        Route::get('/notice/index', 'index')->name('backend.notice.index');
        Route::post('/notice/store', 'store')->name('backend.notice.store');
        Route::get('/notice/{id}/edit', 'edit')->name('backend.notice.edit');
        Route::put('/notice/{id}', 'update')->name('backend.notice.update');
        Route::get('/notice/show/{id}', 'show')->name('backend.notice.show');
        Route::get('/notice/searchcourse', 'searchcourse')->name('backend.notice.searchcourse');
        Route::get('/notice/samplenotice', 'samplenotice')->name('backend.notice.samplenotice');
        Route::post('/notice/mark-all-read', 'markAllAsRead')->name('backend.notice.markAllAsRead');
        Route::get('/notice/universityprofile/{id}', 'universityprofile')->name('university.profile');
    });

    // Comment Add routes
    Route::controller(CommentaddController::class)->group(function () {
        Route::get('/application/comments', 'comments')->name('backend.application.comments');
        Route::get('/application/indexcomments', 'indexcomments')->name('backend.application.indexcomments');
        Route::post('/application/storecomments', 'storecomments')->name('backend.application.storecomments');
        Route::get('/application/commentadd/{commentadd}/edit', 'edit')->name('commentadd.edit');
        Route::put('/application/commentadd/{id}', 'update')->name('backend.application.updatecomments');
        Route::delete('/application/commentadd/{commentadd}', 'destroy')->name('commentadd.destroy');
    });

    // Document routes
    Route::controller(DocumentController::class)->group(function () {
        Route::get('/document/create', 'create')->name('backend.document.create');
        Route::get('/document/index', 'index')->name('backend.document.index');
        Route::post('/document/store', 'store')->name('backend.document.store');
        Route::get('document/{id}/edit', 'edit')->name('document.edit');
        Route::put('/document/{id}', 'update')->name('backend.document.update');
        Route::delete('document/{id}', 'destroy')->name('document.destroy');
    });


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard/filter', [DashboardController::class, 'filterData'])->name('dashboard.filter');

    Route::get('/calendar/month-data', [DashboardController::class, 'getMonthData'])->name('calendar.month_data');

    // Product routes
    Route::controller(ProductController::class)->group(function () {
        Route::get('/product/create', 'create')->name('backend.product.create');
        Route::get('/product/index', 'index')->name('backend.product.index');
        Route::post('/product/store', 'store')->name('backend.product.store');
        Route::get('/products/{product}/edit', 'edit')->name('product.edit');
        Route::put('/product/{id}', 'update')->name('backend.product.update');
        Route::delete('/products/{id}', 'destroy')->name('product.destroy');
    });

    // Enquiry routes
    Route::controller(EnquiryController::class)->group(function () {
        Route::get('/enquiry', 'create')->name('backend.enquiry.create');
        Route::get('/enquiry/index', 'index')->name('backend.enquiry.index');
        Route::post('/enquiry/store', 'store')->name('backend.enquiry.store');
        Route::get('/enquiries/{id}/edit', 'edit')->name('enquirie.edit');
        Route::put('/enquiries/{id}', 'update')->name('backend.enquiry.update');
        Route::delete('/enquiries/{id}', 'destroy')->name('enquirie.destroy');
        Route::get('/enquirys/indexs', 'indexs')->name('backend.enquiryhistory.indexs');
        Route::get('/enquiry/records/{id}', 'records')->name('backend.enquiryhistory.records');
        // Import route
        Route::post('/enquiry/import', 'import')->name('enquiry.import');
    });

    // Comment Pass routes
    Route::controller(LeadCommentController::class)->group(function () {
        Route::get('/commentpass/create', 'create')->name('backend.enquiryhistory.create');
        Route::get('/commentpass/index', 'index')->name('backend.enquiryhistory.index');
        Route::post('/commentpass/store', 'store')->name('backend.enquiryhistory.store');
        Route::get('/comment_pass/{id}/edit', 'edit')->name('comment_pass.edit');
        Route::put('/comment_passe/{id}', 'update')->name('backend.enquiryhistory.update');
    });

    // Upload routes
    Route::controller(UploadController::class)->group(function () {
        Route::get('/documents/create', 'create')->name('backend.upload.create');
        Route::get('/documents/index', 'index')->name('backend.upload.index');
        Route::post('/documents/store', 'store')->name('backend.upload.store');
        Route::get('/documents/{id}/edit', 'edit')->name('upload.edit');
        Route::delete('/documents/{id}', 'destroy')->name('upload.destroy');
    });

    Route::middleware(['auth'])->group(function () {

        // CAS Feedback Routes
        Route::prefix('cas-feedback')->name('backend.casfeedback.')->group(function () {
            // Store new CAS feedback
            Route::post('/store', [CASFeedbackController::class, 'store'])->name('store');

            // Update existing CAS feedback
            Route::put('/{id}', [CASFeedbackController::class, 'update'])->name('update');

            // Delete CAS feedback
            Route::delete('/{id}', [CASFeedbackController::class, 'destroy'])->name('destroy');

            // Get CAS feedback by application (AJAX)
            Route::get('/application/{applicationId}', [CASFeedbackController::class, 'getByApplication'])->name('by-application');

            // Get single CAS feedback details (AJAX)
            Route::get('/{id}', [CASFeedbackController::class, 'show'])->name('show');

            // Update status only (AJAX)
            Route::patch('/{id}/status', [CASFeedbackController::class, 'updateStatus'])->name('update-status');
        });

        // Alternative route naming (if you prefer shorter names)
        Route::name('cas-feedback.')->group(function () {
            Route::delete('/cas-feedback/{id}', [CASFeedbackController::class, 'destroy'])->name('destroy');
        });
    });

    // Name routes
    Route::controller(NameController::class)->group(function () {
        Route::get('name/create', 'create')->name('backend.name.create');
        Route::get('name/index', 'index')->name('backend.name.index');
        Route::post('/name/store', 'store')->name('backend.name.store');
        Route::get('/name/{id}/edit', 'edit')->name('name.edit');
        Route::delete('/name/{id}', 'destroy')->name('name.destroy');
        Route::put('/name/{id}', 'update')->name('backend.name.update');
    });

    // Finance routes
    Route::controller(FinanceController::class)->group(function () {
        Route::get('/finance/create', 'create')->name('backend.Finance.create');
        Route::patch('/finance/updateReceivable/{appId}', [FinanceController::class, 'updateReceivable'])->name('finance.updateReceivable');



        Route::get('/finance/accountreceivable', 'accountreceivable')->name('backend.Finance.accountreceivable');

        Route::get('/finance/accountreceivableview/{id}', 'accountreceivableview')->name('backend.Finance.accountreceivableview');
        Route::get('/finance/accountpayableview/{id}', 'accountpayableview')->name('backend.Finance.accountpayableview');

        Route::get('/finance/payable', 'payable')->name('backend.Finance.paypal');

        Route::get('/finance/index', 'index')->name('backend.Finance.index');
        Route::post('finance/store', 'store')->name('backend.Finance.store');
        Route::get('/finance/{id}/edit', 'edit')->name('finance.edit');
        Route::put('/backend/finance/{id}', 'update')->name('backend.Finance.update');
        Route::delete('/finance/{id}', 'destroy')->name('finance.destroy');
        Route::post('/finance/import', 'import')->name('finance.import');
        Route::get('/finance/visa', 'visa')->name('backend.Finance.visa');
    });

    Route::get('/comission/create', [ComissionController::class, 'create'])->name('backend.comission.create');
    Route::get('/comissionpayable', [CommissionPayableController::class, 'create'])->name('backend.comission.payable.comission');
    Route::get('/comissionpayable/index', [CommissionPayableController::class, 'index'])->name('backend.comission.payable.index');
    Route::post('/comissionpayable/store', [CommissionPayableController::class, 'store'])->name('backend.comission.payable.store');
    Route::get('/commission/rec/{id}', [App\Http\Controllers\CommissionPayableController::class, 'record'])
        ->name('backend.commission.payable.record');
    // Also add the route for updating commission
    Route::put('/commission/update/{id}', [App\Http\Controllers\CommissionPayableController::class, 'updateCommission'])
        ->name('backend.commission.updateCommission');
    Route::get('/comission/indexs', [ComissionController::class, 'indexs'])->name('backend.comission.indexs');


    Route::get('/comission/index', [ComissionController::class, 'index'])->name('backend.comission.index');
    Route::post('/comission/store', [ComissionController::class, 'store'])->name('backend.comission.store');
    Route::put('/backend/{id}/commission/', [ComissionController::class, 'updateCommission'])->name('backend.comission.updateCommission');
    Route::match(['put', 'post'], '/finance/accountreceivableview/{id}', [CommissionTransactionController::class, 'update'])
        ->name('finance.accountreceivableview.update');
    Route::get('/commission/{id}/edit', [ComissionController::class, 'edit'])->name('comission.edit');
    Route::delete('/comission/{id}', [ComissionController::class, 'destroy'])->name('comission.destroy');

    Route::get('/comission/records/{id}', [ComissionController::class, 'record'])->name('backend.comission.record');







    Route::get('/activity-log', [ActivityLogController::class, 'index'])
        ->name('backend.activity.index');
    Route::get('/user-activity/{user?}', [ActivityLogController::class, 'record'])->name('backend.activity.record');

    // Lead Comment routes
    Route::controller(LeadCommentController::class)->group(function () {
        Route::get('/leadcomment/create', 'create')->name('backend.leadcomment.create');
        Route::get('/leadcomment/index', 'index')->name('backend.leadcomment.index');
        Route::post('leadcomment/store', 'store')->name('backend.leadcomment.store');
        Route::get('/lead_comment/{id}/edit', 'edit')->name('lead_comment.edit');
        Route::put('/backend/lead_comment/{id}', 'update')->name('backend.leadcomment.update');
    });
    Route::middleware('auth')->group(function () {
        Route::get('/lead-reminders', [LeadCommentController::class, 'getReminders']);
        Route::post('/lead-reminders/{id}/complete', [LeadCommentController::class, 'markComplete']);
        Route::get('/backend/leadcomment/load', [LeadCommentController::class, 'loadComments'])
            ->name('lead.comments.load');
        Route::get('/backend/leadcomment/load/{leadId}', [LeadCommentController::class, 'loadLeadComments'])->name('lead.comments.load.lead');
    });

    // Image routes
    Route::controller(ImageController::class)->group(function () {
        Route::get('/image/create', 'create')->name('backend.image.create');
        Route::get('/image/index', 'index')->name('backend.image.index');
        Route::post('image/store', 'store')->name('backend.image.store');
    });
});

Route::middleware('auth')->group(function () {
    // Email Verification Routes
    Route::group(['prefix' => 'email'], function () {
        Route::get('/verify', [VerifyEmailController::class, 'show'])
            ->name('verification.notice');

        Route::get('/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
            ->middleware(['signed'])
            ->name('verification.verify');



        Route::post('/verification-notification', [VerifyEmailController::class, 'send'])
            ->name('verification.send');
    });

    Route::middleware(['auth'])->group(function () {
        Route::post('/verify-otp', [OTPVerificationController::class, 'verify'])
            ->name('verify.otp');
        Route::post('/resend-otp', [OTPVerificationController::class, 'resend'])
            ->name('resend.otp');
    });




    // Separate OTP Verification Routes
    Route::group(['prefix' => 'otp'], function () {
        Route::get('/expired', [VerificationController::class, 'expired'])
            ->name('otp.expired');

        Route::get('/verify', [VerificationController::class, 'show'])
            ->name('otp.notice');

        Route::post('/verify', [VerificationController::class, 'verify'])
            ->name('otp.verify');

        Route::post('/resend', [VerificationController::class, 'resend'])
            ->name('otp.resend');
        Route::post('/send-initial-otp', [OTPVerificationController::class, 'sendInitialOtp'])
            ->middleware('auth');
    });
});
Route::get('/test-notification/{user}', function (User $user) {
    ProcessNewUserNotifications::dispatch($user)
        ->onQueue('notifications')
        ->delay(now()->addSeconds(1));

    return "Notification process started for user: " . $user->name;
})->middleware(['auth', 'admin']);

Route::get('/support', function () {
    return view('support');
})->name('support');


















// In your web.php routes file
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    if ($request->ajax()) {
        return response()->json(['message' => 'Logged out successfully']);
    }

    return redirect()->route('login');
})->name('logout');


Route::post('/backend/session', [ImageController::class, 'session'])
    ->name('backend.script.session');



Route::match(['get', 'post'], '/backend/alert', [ImageController::class, 'alert'])
    ->name('backend.script.alert');



Route::post('/backend/media', [ImageController::class, 'media'])
    ->name('backend.script.media');




Route::post('/backend/pagination', [ImageController::class, 'pagination'])
    ->name('backend.script.pagination');

Route::post('/backend/notification', [ImageController::class, 'notification'])
    ->name('backend.script.notification');


Route::post('/backend/navbar', [ImageController::class, 'navbar'])
    ->name('backend.script.navbar');


Route::get('/guest', [GuestController::class, 'showGuestPage'])->name('guest.show');


Route::get('/comments', [CommentController::class, 'index']);
Route::post('/comments', [CommentController::class, 'store']);
Route::get('/comments/{id}', [CommentController::class, 'show']);
Route::put('/comments/{id}', [CommentController::class, 'update']);
Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
Route::post('/comments/{commentId}/mentions', [CommentController::class, 'addMention']);

Route::middleware(['auth'])->group(function () {
    Route::get('/notice/get-notifications', [LeadCommentController::class, 'getNotifications']);
    Route::post('/notice/mark-read/{id}', [LeadCommentController::class, 'markAsRead']);
    Route::post('/notice/mark-all-read', [LeadCommentController::class, 'markAllAsRead']);
});


Route::resource('comments', LeadCommentController::class);
Route::post('/notice/mark-read/{id}', [NoticeController::class, 'markAsRead']);
Route::post('/notice/mark-all-read', [LeadController::class, 'markAllAsRead']);


Route::get('/get-countries', [DataEntryController::class, 'getCountries']);
Route::get('/get-locations-by-country', [DataEntryController::class, 'getLocationsByCountry']);
Route::get('/get-universities-by-location', [DataEntryController::class, 'getUniversitiesByLocation']);
Route::get('/get-courses-by-university', [DataEntryController::class, 'getCoursesByUniversity']);
Route::get('/get-intakes-by-course', [DataEntryController::class, 'getIntakesByCourse']);



Route::get('/get-required-documents', [DataEntryController::class, 'getRequiredDocuments']);



Route::post('/forward-document', [DocumentController::class, 'forwardDocument']);
Route::post('/update-student-details', [DocumentController::class, 'updateSingleField']);

Route::controller(ApplicationHistoryController::class)->group(function () {
    Route::get('/application/index', 'index')->name('backend.application.index');
    Route::get('/application/record/{id}/{type?}', 'record')->name('backend.application.record');
    Route::get('/application/comment', 'comment')->name('backend.application.comment');
    Route::get('/application/indexcomment', 'indexcomment')->name('backend.application.indexcomment');
    Route::post('/application/store', 'store')->name('backend.application.store');
    Route::get('/comment/{id}/edit', 'edit')->name('comment.edit');
    Route::put('/comment/update/{id}', 'update')->name('backend.application.updatecommentts');
    Route::delete('/comment/{id}', 'destroy')->name('comment.destroy');
    Route::delete('/application/withdraw/{id?}', 'withdraw')->name('backend.application.withdraw');
    Route::get('/application/record/{id}/{type?}', 'record')->name('backend.application.view');
    Route::post('/form/import', 'import')->name('form.import');
    Route::put('/backend/application/{id}/update-field', [ApplicationHistoryController::class, 'updateField'])
        ->name('backend.application.update-field');

    Route::get('/application-count', function (Request $request) {
        try {
            $count = 0;

            if ($request->has('email')) {
                $count = Application::where('email', $request->email)->count();
            } elseif ($request->has('phone')) {
                $count = Application::where('phone', $request->phone)->count();
            }

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            // Log the error message
            \Log::error('Application count error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    });
});



Route::post('application/record/{id}/{type?}', [ApplicationHistoryController::class, 'recordApplication'])
    ->name('backend.application.record');



Route::post('/lead/forward/{id}/{type}', [LeadController::class, 'storeForwardedLead'])->name('backend.lead.forward');



Route::get('/backend/document-forward', [OTPVerificationController::class, 'forward'])
    ->name('emails.document_forwarded');

Route::get('/test-email', [LeadController::class, 'testMail']);


Route::get('/notices/unseen', [NoticeController::class, 'getUnseenNotices']);
Route::post('/notices/mark-as-seen/{id}', [NoticeController::class, 'markAsSeen']);


Route::get('/universities', [UniversityController::class, 'index'])->name('backend.dataentry.universities');
Route::post('/universities', [UniversityController::class, 'store'])->name('universities.store');
Route::get('/universities/{university}/edit', [UniversityController::class, 'edit'])->name('universities.edit');
Route::put('/universities/{university}', [UniversityController::class, 'update'])->name('universities.update');
Route::delete('/universities/{university}', [UniversityController::class, 'destroy'])->name('universities.destroy');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'getNotifications']);
    Route::get('/notifications/unread', [NotificationController::class, 'getUnreadNotifications']);
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/reminder', [NotificationController::class, 'createReminderNotification']);
    Route::get('/notifications/{id}/redirect', [NotificationController::class, 'showFromNotification'])
        ->name('notifications.redirect')->middleware('auth');
    Route::post('/notice/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::post('/notice/notifications/markasread', [NotificationController::class, 'markAsRead']);
});
// In routes/web.php or routes.php
Route::post('/leadform/records/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);


Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'showChatWidget'])->name('components.chat-widget');
    Route::get('/chat/users', [ChatController::class, 'getUsers'])->middleware('auth');
    Route::get('/chat/conversations/{userId}', [ChatController::class, 'getConversation']);
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::get('/chat/{userId}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/typing', [ChatController::class, 'sendTypingIndicator']);
    Route::post('/chat/mark-read', [ChatController::class, 'markAsRead']);
    Route::post('/chat/mark-seen', [ChatController::class, 'markMessageAsSeen']);
    Route::post('/chat/mark-delivered', [ChatController::class, 'markMessageAsDelivered']); // New route
    Route::post('/chat/reply', [ChatController::class, 'replyToMessage']);
    Route::post('/chat/forward', [ChatController::class, 'forwardMessage']);
});

Route::get('/calendar', [CalendarController::class, 'index'])->name('backend.calendar.index');
Route::get('/calendar/month-data', [CalendarController::class, 'getMonthData'])->name('calendar.month-data');
Route::post('/calendar/events', [CalendarController::class, 'storeEvent'])->name('calendar.store-event');
Route::get('/calendars', [CalendarController::class, 'showCalendar'])->name('calendar.show');
Route::post('/convert-ad-to-bs', [CalendarController::class, 'convertAdToBs'])->name('convert.ad.to.bs');
Route::post('/convert-bs-to-ad', [CalendarController::class, 'convertBsToAd'])->name('convert.bs.to.ad');
Route::get('/convert-date', [CalendarController::class, 'convertDate'])->name('convert.date');
// Route for getting notice details
Route::get('/calendar/notice-details/{noticeId}', [CalendarController::class, 'getNoticeDetails'])
    ->name('calendar.notice.details');

// If you prefer the version with event data
Route::get('/calendar/notice-details-with-event/{noticeId}', [CalendarController::class, 'getNoticeDetailsWithEvent'])
    ->name('calendar.notice.details.with.event');

// Make sure your existing calendar routes are also present
Route::get('/calendar/month-data', [CalendarController::class, 'getMonthData'])
    ->name('calendar.month.data');


Route::middleware(['auth'])->group(function () {
    Route::get('/role/create', [RoleController::class, 'create'])->name('backend.roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('backend.roles.store');
    Route::get('/role/index', [RoleController::class, 'index'])->name('backend.roles.index');
    Route::get('/roles/{id}', [RoleController::class, 'show'])->name('backend.roles.show');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->name('backend.roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/users/{user}/roles/edit', [UserRoleController::class, 'edit'])->name('user.roles.edit');
    Route::post('/user/{userId}/update-role', [UserRoleController::class, 'updateRole'])->name('user.update-role')->middleware('auth');
    Route::post('/users/assign-role', [UserRoleController::class, 'assignRole']);
    Route::delete('/users/{user}/roles/{role}', [UserRoleController::class, 'removeRole']);
    Route::get('/users/{user}/roles', [UserRoleController::class, 'getRoles']);
});

Route::post('/user/{userId}/update-role', [UserRoleController::class, 'updateRole'])->name('user.updateRole');

Route::post('/assign-permission', [UserRoleController::class, 'assignPermission'])->name('assign.permission');
Route::post('/assign-role', [UserRoleController::class, 'assignRole'])->name('assign.role');



/* Faculty start section */
Route::get('/faculty/create', [FacultyController::class, 'create'])->name('backend.content.create');
Route::get('/faculty/index', [FacultyController::class, 'index'])->name('backend.content.index');
Route::post('/faculty/roles', [FacultyController::class, 'store'])->name('backend.content.store');
Route::post('/faculty/forward/{id}/{type}', [FacultyController::class, 'storeForwardedfaculty'])->name('backend.faculty.forward');



Route::prefix('backend/dashboard')->group(function () {
    Route::get('/backend/dashboard/filter', [App\Http\Controllers\Backend\DashboardController::class, 'getFilteredData'])->name('backend.dashboard.filter');
    Route::get('/backend/dashboard/filter/custom', [App\Http\Controllers\Backend\DashboardController::class, 'getCustomFilteredData'])->name('backend.dashboard.filter.custom');
});
Route::get('/dashboard', function () {
    $user = Auth::user();
    $leads = [];
    $enquiries = [];
    $applications = [];

    $roleName = $user->role->name ?? null; // assuming there's a relation like user->role

    switch ($roleName) {
        case 'Leads Manager':
            $leads = Lead::all();
            break;

        case 'Applications Manager':
            $applications = Application::all();
            break;

        case 'Administrator':
            $leads = Lead::all();
            $enquiries = Enquiry::all();
            $applications = Application::all();
            break;

        default:
            $leads = Lead::all();
            $enquiries = Enquiry::all();
            $applications = Application::all();
            break;
    }

    return view('dashboard', compact('leads', 'enquiries', 'applications', 'user'));
})->name('dashboard');


Route::put('/backend/leadform/{id}/update-creator', [LeadController::class, 'updateCreator'])->name('backend.leadform.update-creator');





/*raw controller */
Route::controller(RawLeadController::class)->group(function () {

    Route::get('/rawleadform/index', 'index')->name('backend.leadform.rawlead');
    Route::post('/rawleadform/store', 'store')->name('backend.rawleadform.store');
    Route::get('/rawlead/{rawLead}/edit', 'edit')->name('rawlead.edit');
    Route::put('/backend/rawleadform/{rawLead}', 'update')->name('backend.leadform.update');
    Route::delete('/rawlead/{rawLead}', 'destroy')->name('rawlead.destroy');

    Route::get('/rawleadform/records/{rawLead}', 'show')->name('backend.rawleadform.records.show');

    Route::post('/rawleadform/import', 'import')->name('rawleadform.import');
    Route::get('/rawlead/upload/file', 'upload')->name('backend.leadform.import');

    Route::post('/rawleads/{rawLead}/status', 'updateStatus')->name('backend.rawlead.updateStatus');
    Route::post('/rawleads/{rawLead}/comments', 'addComment')->name('backend.rawlead.addComment');

    Route::post('/rawleads/bulk-assign', 'bulkAssign')->name('backend.rawlead.bulkAssign');
    Route::post('/rawleads/bulk-destroy', 'bulkDestroy')->name('backend.rawlead.bulkDestroy');
});
Route::get('/raw-leads/export-all', [RawLeadController::class, 'exportAllData'])
    ->name('rawlead.export.all');

Route::post('/lead/followup/{lead}', [LeadController::class, 'storeFollowUp'])->name('lead.followup');


Route::post('/leadform/withdraw/{id}', [LeadController::class, 'withdraw'])->name('backend.leadform.withdraw');



Route::post('/save-document-status', [LeadController::class, 'saveDocumentStatus'])->name('save.document.status');
Route::post('/save-application-status', [ApplicationHistoryController::class, 'saveDocumentStatus'])->name('save.document.status');









Route::match(['get', 'post'], '/stores', [StoreController::class, 'store'])->name('stores.index');



Route::get('/record', [ApplicationController::class, 'record'])->name('backend.form.record');

Route::get('/comission/create', [ComissionController::class, 'create'])->name('backend.comission.create');
Route::get('/comission/index', [ComissionController::class, 'index'])->name('backend.comission.index');
Route::post('/comission/store', [ComissionController::class, 'store'])->name('backend.comission.store');
Route::put('/backend/commission/{id}', [ComissionController::class, 'update'])->name('backend.comission.update');
Route::get('/commission/{id}/edit', [ComissionController::class, 'edit'])->name('backend.comission.edit');
Route::delete('/comission/{id}', [ComissionController::class, 'destroy'])->name('backend.comission.delete');
Route::get('/commission-management', [ComissionController::class, 'commissionindex'])->name('backend.Finance.commissionindex');

Route::get('/partners', [PartnerController::class, 'index'])->name('backend.partners.index');
Route::get('/partners/create', [PartnerController::class, 'create'])->name('backend.partners.create');
Route::post('/partners', [PartnerController::class, 'store'])->name('partners.store');
Route::get('/partners/{partner}', [PartnerController::class, 'show'])->name('partners.show');
Route::get('/partners/{partner}/edit', [PartnerController::class, 'edit'])->name('backend.partners.edit');
Route::put('/partners/{partner}', [PartnerController::class, 'update'])->name('partners.update');
Route::patch('/partners/{partner}', [PartnerController::class, 'update'])->name('partners.update');
Route::delete('/partners/{partner}', [PartnerController::class, 'destroy'])->name('partners.destroy');
Route::get('/backend/partners/import', [PartnerController::class, 'import'])->name('backend.partners.import');
Route::post('/backend/partners/import', [PartnerController::class, 'processImport'])->name('backend.partners.process-import');
Route::get('/backend/partners/download-template', [PartnerController::class, 'downloadTemplate'])->name('backend.partners.download-template');
Route::get('/backend/partners/download-template', [PartnerController::class, 'downloadTemplate'])->name('backend.partners.download-template');
Route::post('/backend/partners/import', [PartnerController::class, 'processImport'])->name('backend.partners.process-import');






Route::post('/commission-payable/{id}/duplicate', [CommissionPayableController::class, 'duplicatePayable'])->name('commission.payable.duplicate');
Route::post('/commission-payable/{id}/mark-as-paid', [CommissionPayableController::class, 'markAsPaid'])->name('commission.payable.mark-as-paid');
Route::delete('/commission-payable/{id}', [CommissionPayableController::class, 'destroyPayable'])->name('commission.payable.destroy');
Route::get('/commission-payable/export', [CommissionPayableController::class, 'exportPayable'])->name('commission.payable.export');
Route::post('/commission-payable/import', [CommissionPayableController::class, 'importPayable'])->name('commission.payable.import');
Route::post('/commission/{id}/duplicate', [ComissionController::class, 'duplicateCommission'])->name('commission.duplicate');
Route::delete('/commission/{id}', [ComissionController::class, 'destroyCommission'])->name('commission.destroy');
Route::get('/commission/export', [ComissionController::class, 'exportCommission'])->name('commission.export');
Route::post('/commission/import', [ComissionController::class, 'importCommission'])->name('commission.import');


// In routes/web.php or routes/api.php
Route::middleware(['auth', 'security.monitoring'])->group(function () {
    Route::post('/api/security/validate-session', [SecurityController::class, 'validateSession']);
    Route::post('/api/security/audit-log', [SecurityController::class, 'recordAuditLog']);
    Route::post('/api/security/event', [SecurityController::class, 'recordSecurityEvent']);
    Route::post('/api/security/extend-session', [SecurityController::class, 'extendSession']);
    Route::get('/api/session/id', [SecurityController::class, 'getSessionId']);
});





require __DIR__ . '/auth.php';
