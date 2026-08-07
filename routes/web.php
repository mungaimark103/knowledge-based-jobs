<?php

use App\Http\Controllers\AgencyDashboardController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CandidateProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployerDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OpportunityController;

Route::inertia('/', 'Welcome')->name('home');

Route::get('/opportunities', [OpportunityController::class, 'index'])->name('opportunities.index');
Route::get('/opportunities/{id}', [OpportunityController::class, 'show'])->name('opportunities.show');
Route::post('/opportunities/{id}/apply', [OpportunityController::class, 'apply'])->name('opportunities.apply');
Route::get('/employer/portal-switch', [EmployerDashboardController::class, 'switchPortal'])->name('employer.portal.switch');
Route::get('/candidate/portal-switch', [EmployerDashboardController::class, 'candidatePortalSwitch'])->name('candidate.portal.switch');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/candidate/resume', [CandidateProfileController::class, 'uploadResume'])->name('candidate.resume');
    Route::post('/candidate/profile', [CandidateProfileController::class, 'updateStructuredProfile'])->name('candidate.profile.update');
    Route::get('/candidate/resume/view', [CandidateProfileController::class, 'viewResume'])->name('candidate.resume.view');
    Route::get('/candidate/resume/download', [CandidateProfileController::class, 'downloadResume'])->name('candidate.resume.download');
    Route::get('/employer/dashboard', [EmployerDashboardController::class, 'index'])->name('employer.dashboard');
    Route::post('/employer/organization', [EmployerDashboardController::class, 'updateOrganization'])->name('employer.organization.update');
    Route::post('/employer/jobs', [EmployerDashboardController::class, 'storeJob'])->name('employer.jobs.store');
    Route::get('/opportunities/{id}/applicants', [OpportunityController::class, 'applicants'])->name('opportunities.applicants');

    // Candidate In-App Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Agency Super Admin Subsystem
    Route::get('/admin/dashboard', [AgencyDashboardController::class, 'index'])->name('admin.dashboard');
    Route::patch('/admin/organizations/{id}/verify', [AgencyDashboardController::class, 'toggleVerifyOrganization'])->name('admin.organizations.verify');
    Route::patch('/admin/candidates/{id}/verify', [AgencyDashboardController::class, 'toggleVerifyCandidate'])->name('admin.candidates.verify');
    Route::post('/admin/candidates/proxy', [AgencyDashboardController::class, 'createProxyCandidate'])->name('admin.candidates.proxy');
    Route::post('/admin/criteria/weights', [AgencyDashboardController::class, 'updateCriteriaWeights'])->name('admin.criteria.weights');
    Route::post('/admin/staff/provision', [AgencyDashboardController::class, 'provisionStaff'])->name('admin.staff.provision');
    Route::get('/admin/audit/export', [AgencyDashboardController::class, 'exportAuditCsv'])->name('admin.audit.export');
    Route::post('/admin/rules', [AgencyDashboardController::class, 'storeRule'])->name('admin.rules.store');
    Route::patch('/admin/rules/{id}/toggle', [AgencyDashboardController::class, 'toggleRule'])->name('admin.rules.toggle');
});

// Google OAuth Single Sign-On Routes
Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('auth.google.callback');

require __DIR__.'/settings.php';
