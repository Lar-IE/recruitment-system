<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DigitalIdController as AdminDigitalIdController;
use App\Http\Controllers\Admin\DocumentReviewController as AdminDocumentReviewController;
use App\Http\Controllers\Admin\ApplicationsController as AdminApplicationsController;
use App\Http\Controllers\Admin\ReportsController as AdminReportsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\EmployerController as AdminEmployerController;
use App\Http\Controllers\Admin\JobseekerController as AdminJobseekerController;
use App\Http\Controllers\Admin\PagesController as AdminPagesController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\DigitalIdVerificationController;
use App\Http\Controllers\Employer\DashboardController as EmployerDashboardController;
use App\Http\Controllers\Employer\DigitalIdController as EmployerDigitalIdController;
use App\Http\Controllers\Employer\DocumentController as EmployerDocumentController;
use App\Http\Controllers\Employer\JobPostController as EmployerJobPostController;
use App\Http\Controllers\Employer\JobseekerDirectoryController;
use App\Http\Controllers\Employer\ApplicantsController as EmployerApplicantsController;
use App\Http\Controllers\Employer\PagesController as EmployerPagesController;
use App\Http\Controllers\Employer\AtsController as EmployerAtsController;
use App\Http\Controllers\Employer\NotificationController as EmployerNotificationController;
use App\Http\Controllers\Employer\SubUserController as EmployerSubUserController;
use App\Http\Controllers\Jobseeker\DashboardController as JobseekerDashboardController;
use App\Http\Controllers\Jobseeker\DocumentController as JobseekerDocumentController;
use App\Http\Controllers\Jobseeker\HistoryController as JobseekerHistoryController;
use App\Http\Controllers\Jobseeker\JobController as JobseekerJobController;
use App\Http\Controllers\Jobseeker\PagesController as JobseekerPagesController;
use App\Http\Controllers\Jobseeker\ProfileController as JobseekerProfileController;
use App\Http\Controllers\Jobseeker\NotificationController as JobseekerNotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureEmployerApproved;

Route::middleware('maintenance')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/digital-ids/verify/{token}', [DigitalIdVerificationController::class, 'show'])
        ->name('digital-ids.verify');
    Route::post('/digital-ids/verify/{token}/documents/{type}/download', [DigitalIdVerificationController::class, 'download'])
        ->name('digital-ids.verify.documents.download');

    Route::get('/dashboard', DashboardRedirectController::class)
        ->middleware(['auth', 'verified', 'active'])
        ->name('dashboard');

    Route::middleware(['auth', 'verified', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users');
        Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.update-role');
        Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/employers', [AdminEmployerController::class, 'index'])->name('employers');
        Route::post('/employers/{employer}/approve', [AdminEmployerController::class, 'approve'])->name('employers.approve');
        Route::post('/employers/{employer}/suspend', [AdminEmployerController::class, 'suspend'])->name('employers.suspend');
        Route::post('/employers/{employer}/activate', [AdminEmployerController::class, 'activate'])->name('employers.activate');
        Route::get('/jobseekers', [AdminJobseekerController::class, 'index'])->name('jobseekers');
        Route::post('/jobseekers/{jobseeker}/suspend', [AdminJobseekerController::class, 'suspend'])->name('jobseekers.suspend');
        Route::post('/jobseekers/{jobseeker}/activate', [AdminJobseekerController::class, 'activate'])->name('jobseekers.activate');
        Route::get('/job-posts', [AdminPagesController::class, 'jobPosts'])->name('job-posts');
        Route::get('/applications', [AdminApplicationsController::class, 'index'])->name('applications');
        Route::get('/documents', [AdminDocumentReviewController::class, 'index'])->name('documents');
        Route::get('/digital-ids', [AdminDigitalIdController::class, 'index'])->name('digital-ids');
        Route::get('/digital-ids/{digitalId}', [AdminDigitalIdController::class, 'show'])->name('digital-ids.show');
        Route::post('/digital-ids/{digitalId}/revoke', [AdminDigitalIdController::class, 'revoke'])->name('digital-ids.revoke');
        Route::get('/reports', [AdminReportsController::class, 'index'])->name('reports');
        Route::get('/reports/applications.csv', [AdminReportsController::class, 'applicationsCsv'])->name('reports.applications');
        Route::get('/reports/users.csv', [AdminReportsController::class, 'usersCsv'])->name('reports.users');
        Route::get('/reports/hiring.csv', [AdminReportsController::class, 'hiringCsv'])->name('reports.hiring');
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/toggle-maintenance', [AdminSettingsController::class, 'toggleMaintenance'])->name('settings.toggle-maintenance');
    });

    Route::middleware(['auth', 'active', 'role:employer'])->prefix('employer')->name('employer.')->group(function () {
        Route::get('/pending', fn () => view('employer.pending'))->name('pending');
    });

    Route::middleware(['employer.user', 'employer.approved'])->prefix('employer')->name('employer.')->group(function () {
        Route::get('/dashboard', [EmployerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/job-posts', [EmployerJobPostController::class, 'index'])
            ->middleware('employer.role:admin,recruiter,viewer')
            ->name('job-posts.index');
        Route::get('/job-posts/create', [EmployerJobPostController::class, 'create'])
            ->middleware('employer.role:admin,recruiter')
            ->name('job-posts.create');
        Route::post('/job-posts', [EmployerJobPostController::class, 'store'])
            ->middleware('employer.role:admin,recruiter')
            ->name('job-posts.store');
        Route::get('/job-posts/{jobPost}', [EmployerJobPostController::class, 'show'])
            ->middleware('employer.role:admin,recruiter,viewer')
            ->whereNumber('jobPost')
            ->name('job-posts.show');
        Route::get('/job-posts/{jobPost}/edit', [EmployerJobPostController::class, 'edit'])
            ->middleware('employer.role:admin,recruiter')
            ->whereNumber('jobPost')
            ->name('job-posts.edit');
        Route::put('/job-posts/{jobPost}', [EmployerJobPostController::class, 'update'])
            ->middleware('employer.role:admin,recruiter')
            ->whereNumber('jobPost')
            ->name('job-posts.update');
        Route::delete('/job-posts/{jobPost}', [EmployerJobPostController::class, 'destroy'])
            ->middleware('employer.role:admin,recruiter')
            ->whereNumber('jobPost')
            ->name('job-posts.destroy');
        Route::post('/job-posts/{jobPost}/publish', [EmployerJobPostController::class, 'publish'])
            ->middleware('employer.role:admin,recruiter')
            ->whereNumber('jobPost')
            ->name('job-posts.publish');
        Route::post('/job-posts/{jobPost}/close', [EmployerJobPostController::class, 'close'])
            ->middleware('employer.role:admin,recruiter')
            ->whereNumber('jobPost')
            ->name('job-posts.close');
        Route::post('/job-posts/{jobPost}/duplicate', [EmployerJobPostController::class, 'duplicate'])
            ->middleware('employer.role:admin,recruiter')
            ->whereNumber('jobPost')
            ->name('job-posts.duplicate');
        Route::get('/jobseekers', [JobseekerDirectoryController::class, 'index'])
            ->middleware('employer.role:admin,recruiter,viewer')
            ->name('jobseekers.index');
        Route::get('/jobseekers/{jobseeker}', [JobseekerDirectoryController::class, 'show'])
            ->middleware('employer.role:admin,recruiter,viewer')
            ->whereNumber('jobseeker')
            ->name('jobseekers.show');
        Route::get('/applicants', [EmployerApplicantsController::class, 'index'])
            ->middleware('employer.role:admin,recruiter,viewer')
            ->name('applicants');
        Route::get('/applicants/{application}', [EmployerApplicantsController::class, 'show'])
            ->middleware('employer.role:admin,recruiter,viewer')
            ->name('applicants.show');
        Route::post('/applicants/{application}/notes', [\App\Http\Controllers\Employer\NoteController::class, 'store'])
            ->middleware('employer.role:admin,recruiter')
            ->name('applicants.notes.store');
        Route::put('/applicants/{application}/notes/{note}', [\App\Http\Controllers\Employer\NoteController::class, 'update'])
            ->middleware('employer.role:admin,recruiter')
            ->name('applicants.notes.update');
        Route::delete('/applicants/{application}/notes/{note}', [\App\Http\Controllers\Employer\NoteController::class, 'destroy'])
            ->middleware('employer.role:admin,recruiter')
            ->name('applicants.notes.destroy');
        Route::get('/ats', [EmployerAtsController::class, 'index'])
            ->middleware('employer.role:admin,recruiter,viewer')
            ->name('ats');
        Route::post('/ats/{application}/status', [EmployerAtsController::class, 'updateStatus'])
            ->middleware('employer.role:admin,recruiter')
            ->name('ats.status');
        Route::get('/documents', [EmployerDocumentController::class, 'index'])
            ->middleware('employer.role:admin,recruiter,viewer')
            ->name('documents');
        Route::get('/documents/{jobseeker}', [EmployerDocumentController::class, 'show'])
            ->middleware('employer.role:admin,recruiter,viewer')
            ->name('documents.show');
        Route::post('/documents/{document}/request-update', [EmployerDocumentController::class, 'requestUpdate'])
            ->middleware('employer.role:admin,recruiter')
            ->name('documents.request-update');
        Route::get('/notifications', [EmployerNotificationController::class, 'index'])
            ->middleware('employer.role:admin,recruiter,viewer')
            ->name('notifications');
        Route::get('/notifications/{notification}/read', [EmployerNotificationController::class, 'markReadAndRedirect'])
            ->middleware('employer.role:admin,recruiter,viewer')
            ->name('notifications.read');
        Route::get('/digital-ids', [EmployerDigitalIdController::class, 'index'])
            ->middleware('employer.role:admin,recruiter,viewer')
            ->name('digital-ids');
        Route::post('/digital-ids', [EmployerDigitalIdController::class, 'store'])
            ->middleware('employer.role:admin,recruiter')
            ->name('digital-ids.store');
        Route::get('/digital-ids/{digitalId}', [EmployerDigitalIdController::class, 'show'])
            ->middleware('employer.role:admin,recruiter,viewer')
            ->name('digital-ids.show');
        Route::post('/digital-ids/{digitalId}/toggle', [EmployerDigitalIdController::class, 'toggle'])
            ->middleware('employer.role:admin,recruiter')
            ->name('digital-ids.toggle');

        Route::get('/sub-users', [EmployerSubUserController::class, 'index'])
            ->middleware('employer.role:admin')
            ->name('sub-users.index');
        Route::get('/sub-users/create', [EmployerSubUserController::class, 'create'])
            ->middleware('employer.role:admin')
            ->name('sub-users.create');
        Route::post('/sub-users', [EmployerSubUserController::class, 'store'])
            ->middleware('employer.role:admin')
            ->name('sub-users.store');
        Route::get('/sub-users/{subUser}/edit', [EmployerSubUserController::class, 'edit'])
            ->middleware('employer.role:admin')
            ->name('sub-users.edit');
        Route::put('/sub-users/{subUser}', [EmployerSubUserController::class, 'update'])
            ->middleware('employer.role:admin')
            ->name('sub-users.update');
        Route::post('/sub-users/{subUser}/toggle-status', [EmployerSubUserController::class, 'toggleStatus'])
            ->middleware('employer.role:admin')
            ->name('sub-users.toggle-status');
        Route::delete('/sub-users/{subUser}', [EmployerSubUserController::class, 'destroy'])
            ->middleware('employer.role:admin')
            ->name('sub-users.destroy');
    });

    Route::middleware(['auth', 'verified', 'active', 'role:jobseeker', 'jobseeker.profile'])->prefix('jobseeker')->name('jobseeker.')->group(function () {
        Route::get('/dashboard', [JobseekerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [JobseekerProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [JobseekerProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [JobseekerProfileController::class, 'update'])->name('profile.update');
        Route::get('/jobs', [JobseekerJobController::class, 'index'])->name('jobs');
        Route::get('/jobs/{jobPost}', [JobseekerJobController::class, 'show'])->name('jobs.show');
        Route::post('/jobs/{jobPost}/apply', [JobseekerJobController::class, 'apply'])->name('jobs.apply');
        Route::get('/documents', [JobseekerDocumentController::class, 'index'])->name('documents');
        Route::post('/documents', [JobseekerDocumentController::class, 'store'])->name('documents.store');
        Route::post('/documents/upload-all', [JobseekerDocumentController::class, 'storeBatch'])->name('documents.store-all');
        Route::get('/documents/{document}', [JobseekerDocumentController::class, 'show'])->name('documents.show');
        Route::get('/documents/{document}/download', [JobseekerDocumentController::class, 'download'])->name('documents.download');
        Route::get('/digital-id', [JobseekerPagesController::class, 'digitalId'])->name('digital-id');
        Route::post('/digital-id/photo', [\App\Http\Controllers\Jobseeker\DigitalIdController::class, 'updatePhoto'])->name('digital-id.photo');
        Route::get('/history', [JobseekerHistoryController::class, 'index'])->name('history');
        Route::get('/history/{application}', [JobseekerHistoryController::class, 'show'])->name('history.show');
        Route::get('/notifications', [JobseekerNotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/{notification}/read', [JobseekerNotificationController::class, 'markReadAndRedirect'])->name('notifications.read');
    });

    Route::middleware(['auth', 'active'])->group(function () {
        Route::get('/account-settings', [ProfileController::class, 'accountSettings'])->name('profile.settings');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__.'/auth.php';
