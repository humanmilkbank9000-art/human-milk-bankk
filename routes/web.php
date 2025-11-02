<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserRegisterController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\HealthScreeningController;
use App\Http\Controllers\BreastmilkRequestController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\NutritionalGuideController;

Route::get('/user-register', [UserRegisterController::class, 'user_register'])->name('user.register');
Route::post('/user-register/store', [UserRegisterController::class, 'store_user'])->name('user.store'); 
Route::get('/user-register-infant', [UserRegisterController::class, 'user_register_infant'])->name('user.register.infant');
Route::post('/user-register-infant/store', [UserRegisterController::class, 'store_infant'])->name('user.store.infant');
Route::post('/user-register-infant/save-temp', [UserRegisterController::class, 'save_temp_infant'])->name('user.save.temp.infant');

Route::get('/', [LoginController::class, 'login_page'])->name('login'); // default root → login form
Route::get('/login', [LoginController::class, 'login_page'])->name('login.page'); // alias
Route::post('/login', [LoginController::class, 'handle_login'])->name('login.submit'); // process login
Route::post('/check-username', [LoginController::class, 'check_username'])->name('check.username'); // check admin username
Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); // logout (both roles)

Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.forgot');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendRecoveryCode'])->name('password.forgot.send');

    Route::get('/verify-code', [ForgotPasswordController::class, 'showVerifyCodeForm'])->name('password.verify');
    Route::post('/verify-code', [ForgotPasswordController::class, 'verifyCode'])->name('password.verify.submit');

    Route::get('/reset-password', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

Route::get('/user/dashboard', [LoginController::class, 'user_dashboard'])->name('user.dashboard');
Route::get('/admin/dashboard', [LoginController::class, 'admin_dashboard'])->name('admin.dashboard');

Route::post('/admin/availability/store', [AvailabilityController::class, 'store'])
    ->name('admin.availability.store');
Route::get('/admin/availability/slots', [AvailabilityController::class, 'getAvailableSlots'])
    ->name('admin.availability.slots');
Route::delete('/admin/availability/{id}', [AvailabilityController::class, 'destroy'])
    ->name('admin.availability.destroy');

Route::get('/admin/health-screening', [HealthScreeningController::class, 'admin_health_screening'])->name('admin.health-screening');
Route::post('/admin/health-screening/{id}/accept', [HealthScreeningController::class, 'accept'])->name('admin.health-screening.accept');
Route::post('/admin/health-screening/{id}/reject', [HealthScreeningController::class, 'reject'])->name('admin.health-screening.reject');
Route::post('/admin/health-screening/{id}/undo-decline', [HealthScreeningController::class, 'undoDecline'])->name('admin.health-screening.undo-decline');
// Archive (soft-delete) endpoints
Route::post('/admin/health-screening/{id}/archive', [HealthScreeningController::class, 'archive'])->name('admin.health-screening.archive');
Route::post('/admin/health-screening/{id}/restore', [HealthScreeningController::class, 'restore'])->name('admin.health-screening.restore');

Route::get('/user/health-screening', [HealthScreeningController::class, 'user_health_screening'])->name('user.health-screening');
Route::post('/user/health-screening/store', [HealthScreeningController::class, 'store'])->name('health_screening.store');

Route::get('/admin/breastmilk-donation', [DonationController::class, 'admin_breastmilk_donation'])->name('admin.donation');
Route::get('/admin/donations/{id}', [DonationController::class, 'show'])->name('admin.donation.show');
Route::post('/admin/donations/{id}/validate-walkin', [DonationController::class, 'validateWalkIn'])->name('admin.donation.validate-walkin');
Route::post('/admin/donations/{id}/schedule-pickup', [DonationController::class, 'schedulePickup'])->name('admin.donation.schedule-pickup');
Route::post('/admin/donations/{id}/reschedule-pickup', [DonationController::class, 'reschedulePickup'])->name('admin.donation.reschedule-pickup');
// Assist Walk-in Donation (admin creates a walk-in donation and adds to inventory immediately)
Route::post('/admin/donations/assist-walkin', [DonationController::class, 'assistWalkIn'])->name('admin.donation.assist-walkin');
Route::post('/admin/donations/{id}/validate-pickup', [DonationController::class, 'validatePickup'])->name('admin.donation.validate-pickup');
// Decline donation (pending only)
Route::post('/admin/donations/{id}/decline', [DonationController::class, 'decline'])->name('admin.donation.decline');
// Archive donation
Route::post('/admin/donations/{id}/archive', [DonationController::class, 'archive'])->name('admin.donation.archive');
Route::post('/admin/donations/{id}/restore', [DonationController::class, 'restore'])->name('admin.donation.restore');
Route::get('/user/donate', [DonationController::class, 'user_donate'])->name('user.donate');
Route::post('/user/donate/store', [DonationController::class, 'store'])->name('donation.store');
// My Requests page for user
Route::get('/user/my-requests', [DonationController::class, 'user_my_requests'])->name('user.my-requests');

Route::get('/admin/breastmilk-request', [BreastmilkRequestController::class, 'admin_breastmilk_request'])->name('admin.request');
Route::get('/admin/breastmilk-request/{id}/prescription', [BreastmilkRequestController::class, 'showPrescription'])->name('admin.request.prescription');
// Stream prescription for users (secure, returns image inline)
Route::get('/user/breastmilk-request/{id}/prescription', [BreastmilkRequestController::class, 'streamPrescription'])->name('user.request.prescription');
// JSON endpoint returning base64 image for authenticated user (AJAX fallback)
Route::get('/user/breastmilk-request/{id}/prescription-json', [BreastmilkRequestController::class, 'prescriptionJson'])->name('user.request.prescription.json');
Route::get('/admin/breastmilk-request/inventory', [BreastmilkRequestController::class, 'getAvailableInventory'])->name('admin.request.inventory');
Route::get('/admin/breastmilk-request/check-contact', [BreastmilkRequestController::class, 'checkContact'])->name('admin.request.check-contact');
Route::post('/admin/breastmilk-request/{id}/approve', [BreastmilkRequestController::class, 'approve'])->name('admin.request.approve');
Route::post('/admin/breastmilk-request/{id}/decline', [BreastmilkRequestController::class, 'decline'])->name('admin.request.decline');
Route::post('/admin/breastmilk-request/{id}/dispense', [BreastmilkRequestController::class, 'dispense'])->name('admin.request.dispense');
Route::post('/admin/breastmilk-request/{id}/reject', [BreastmilkRequestController::class, 'reject'])->name('admin.request.reject');
// Archive request
Route::post('/admin/breastmilk-request/{id}/archive', [BreastmilkRequestController::class, 'archive'])->name('admin.request.archive');
Route::post('/admin/breastmilk-request/{id}/restore', [BreastmilkRequestController::class, 'restore'])->name('admin.request.restore');
Route::post('/admin/breastmilk-request/store-assisted', [BreastmilkRequestController::class, 'storeAssisted'])->name('admin.breastmilk-request.store-assisted');
Route::get('/user/breastmilk-request', [BreastmilkRequestController::class, 'index'])->name('user.breastmilk-request');
Route::post('/user/breastmilk-request/store', [BreastmilkRequestController::class, 'store'])->name('user.breastmilk-request.store');
Route::get('/user/breastmilk-request/infant/{infantId}', [BreastmilkRequestController::class, 'getInfantInfo'])->name('user.breastmilk-request.infant');
Route::get('/user/my-breastmilk-requests', [BreastmilkRequestController::class, 'myRequests'])->name('user.my-breastmilk-requests');

Route::get('/admin/inventory', [InventoryController::class, 'index'])->name('admin.inventory');
Route::post('/admin/inventory/pasteurize', [InventoryController::class, 'pasteurize'])->name('admin.inventory.pasteurize');
Route::post('/admin/inventory/dispose', [InventoryController::class, 'dispose'])->name('admin.inventory.dispose');
Route::get('/admin/inventory/batch/{id}', [InventoryController::class, 'getBatchDetails'])->name('admin.inventory.batch.details');
Route::get('/admin/inventory/stats', [InventoryController::class, 'getInventoryStats'])->name('admin.inventory.stats');

Route::get('/admin/monthly-reports', [ReportsController::class, 'admin_monthly_reports'])->name('admin.reports');

Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
    Route::get('{type}/preview', [ReportController::class, 'preview'])->name('preview');
    Route::get('{type}/download', [ReportController::class, 'download'])->name('download');
});

Route::get('/admin/settings', [App\Http\Controllers\LoginController::class, 'admin_settings'])->name('admin.settings');
Route::post('/admin/settings/update', [App\Http\Controllers\LoginController::class, 'admin_settings_update'])->name('admin.settings.update');

Route::get('/user/pending-donation', [DonationController::class, 'user_pending_donation'])->name('user.pending');
Route::get('/user/my-donation-history', [DonationController::class, 'user_my_donation_history'])->name('user.history');

// User settings (GET/POST)
Route::get('/user/settings', [App\Http\Controllers\LoginController::class, 'user_settings'])->name('user.settings');
Route::post('/user/settings/update-password', [App\Http\Controllers\LoginController::class, 'user_update_password'])->name('user.settings.update_password');
// Backwards-compatible route name expected by the blade template
Route::post('/user/settings/update-password', [App\Http\Controllers\LoginController::class, 'user_update_password'])->name('user.update-password');

// Notifications (AJAX)
Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread_count');
Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'delete'])->name('notifications.delete');
Route::delete('/notifications', [App\Http\Controllers\NotificationController::class, 'deleteAll'])->name('notifications.delete_all');

// Messages (AJAX) - Admin ↔ User Chat
Route::get('/messages/unread-count', [App\Http\Controllers\MessageController::class, 'unreadCount'])->name('messages.unread_count');
Route::get('/messages/conversation', [App\Http\Controllers\MessageController::class, 'getConversation'])->name('messages.conversation');
Route::post('/messages/send', [App\Http\Controllers\MessageController::class, 'sendMessage'])->name('messages.send');
Route::get('/messages/partners', [App\Http\Controllers\MessageController::class, 'getPartners'])->name('messages.partners');
Route::delete('/messages/{id}', [App\Http\Controllers\MessageController::class, 'deleteMessage'])->name('messages.delete');
Route::delete('/messages/conversation/{partnerId}', [App\Http\Controllers\MessageController::class, 'deleteConversation'])->name('messages.delete_conversation');

// FAQ Routes
Route::get('/api/faq/search', function(\Illuminate\Http\Request $request) {
    $query = $request->get('q', '');
    return response()->json(FaqController::searchFaq($query));
})->name('faq.search');

// Nutritional Guide Routes
Route::get('/api/nutritional-guide/search', function(\Illuminate\Http\Request $request) {
    $query = $request->get('q', '');
    return response()->json(NutritionalGuideController::searchGuide($query));
})->name('nutritional-guide.search');