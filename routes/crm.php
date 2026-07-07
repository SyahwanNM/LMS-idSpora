<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Public\PublicEventController;
use App\Http\Controllers\Public\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\User\UserModuleController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Public\SocialAuthController;
use App\Http\Controllers\Trainer\TrainerApiController;
use App\Http\Controllers\Trainer\EventModuleController as TrainerEventModuleController;

use App\Http\Controllers\User\NotificationsController;
use App\Http\Controllers\Public\PublicPagesController;
use App\Http\Controllers\Admin\CourseReportController;
use App\Http\Controllers\Admin\CourseRevenueDetailController;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Http\Controllers\User\ResellerController;

        // CRM Routes
        Route::prefix('admin/crm')->name('admin.crm.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\CRM\CRMController::class, 'dashboard'])->name('dashboard');

            // Certificate management (moved to CRM)
            Route::get('/certificates', [\App\Http\Controllers\CRM\CertificateController::class, 'index'])->name('certificates.index');
            
            // Event Certificates
            Route::get('/certificates/events/{event}/edit', [\App\Http\Controllers\CRM\CertificateController::class, 'edit'])->name('certificates.edit');
            Route::put('/certificates/events/{event}', [\App\Http\Controllers\CRM\CertificateController::class, 'update'])->name('certificates.update');
            Route::get('/events/{event}/certificates/generate-massal', [\App\Http\Controllers\CRM\CertificateController::class, 'generateMassal'])->name('certificates.generate-massal');
            
            // Custom Template Builder for Events
            Route::get('/certificates/events/{event}/template-builder', [\App\Http\Controllers\CRM\CertificateController::class, 'templateBuilder'])->name('certificates.template-builder');
            Route::post('/certificates/events/{event}/template-builder', [\App\Http\Controllers\CRM\CertificateController::class, 'saveCustomTemplate'])->name('certificates.save-custom-template');
            Route::post('/certificates/events/{event}/template-builder/reset', [\App\Http\Controllers\CRM\CertificateController::class, 'resetCustomTemplate'])->name('certificates.reset-custom-template');

            // Course Certificates
            Route::get('/certificates/courses/{course}/edit', [\App\Http\Controllers\CRM\CertificateController::class, 'editCourse'])->name('certificates.edit-course');
            Route::put('/certificates/courses/{course}', [\App\Http\Controllers\CRM\CertificateController::class, 'updateCourse'])->name('certificates.update-course');
            Route::get('/courses/{course}/certificates/generate-massal', [\App\Http\Controllers\CRM\CertificateController::class, 'generateMassalCourse'])->name('certificates.generate-massal-course');

            // Custom Template Builder for Courses
            Route::get('/certificates/courses/{course}/template-builder', [\App\Http\Controllers\CRM\CertificateController::class, 'templateBuilderCourse'])->name('certificates.template-builder-course');
            Route::post('/certificates/courses/{course}/template-builder', [\App\Http\Controllers\CRM\CertificateController::class, 'saveCustomTemplateCourse'])->name('certificates.save-custom-template-course');
            Route::post('/certificates/courses/{course}/template-builder/reset', [\App\Http\Controllers\CRM\CertificateController::class, 'resetCustomTemplateCourse'])->name('certificates.reset-custom-template-course');

            // Shared Asset Upload for Builder
            Route::post('/certificates/builder/upload-asset', [\App\Http\Controllers\CRM\CertificateController::class, 'uploadBuilderAsset'])->name('certificates.builder-upload-asset');

            // Customer management
            Route::get('/customers', [\App\Http\Controllers\CRM\CRMController::class, 'customers'])->name('customers.index');
            Route::get('/customers/{customer}', [\App\Http\Controllers\CRM\CRMController::class, 'showCustomer'])->name('customers.show');
            Route::get('/customers/{customer}/edit', [\App\Http\Controllers\CRM\CRMController::class, 'editCustomer'])->name('customers.edit');
            Route::put('/customers/{customer}', [\App\Http\Controllers\CRM\CRMController::class, 'updateCustomer'])->name('customers.update');
            Route::delete('/customers/{customer}', [\App\Http\Controllers\CRM\CRMController::class, 'destroyCustomer'])->name('customers.destroy');
            Route::post('/customers/{customer}/adjust-points', [\App\Http\Controllers\CRM\CRMController::class, 'adjustPoints'])->name('customers.adjust-points');

            // Voucher Management CRUD
            Route::get('/vouchers', [\App\Http\Controllers\CRM\CRMController::class, 'vouchersIndex'])->name('vouchers.index');
            Route::get('/vouchers/create', [\App\Http\Controllers\CRM\CRMController::class, 'vouchersCreate'])->name('vouchers.create');
            Route::post('/vouchers', [\App\Http\Controllers\CRM\CRMController::class, 'vouchersStore'])->name('vouchers.store');
            Route::get('/vouchers/{voucher}/edit', [\App\Http\Controllers\CRM\CRMController::class, 'vouchersEdit'])->name('vouchers.edit');
            Route::put('/vouchers/{voucher}', [\App\Http\Controllers\CRM\CRMController::class, 'vouchersUpdate'])->name('vouchers.update');
            Route::delete('/vouchers/{voucher}', [\App\Http\Controllers\CRM\CRMController::class, 'vouchersDestroy'])->name('vouchers.destroy');


            // Feedback Analysis
            Route::get('/feedback', [\App\Http\Controllers\CRM\CRMController::class, 'feedbackAnalysis'])->name('feedback.index');

            // Support Messages
            Route::get('/support', [\App\Http\Controllers\CRM\CRMController::class, 'supportMessages'])->name('support.index');
            Route::post('/support/{message}/status', [\App\Http\Controllers\CRM\CRMController::class, 'updateSupportStatus'])->name('support.updateStatus');

            // Broadcast/Blast
            Route::get('/broadcast', [\App\Http\Controllers\CRM\CRMController::class, 'broadcastIndex'])->name('broadcast.index');
            Route::get('/broadcast/create', [\App\Http\Controllers\CRM\CRMController::class, 'broadcastCreate'])->name('broadcast.create');
            Route::post('/broadcast/send', [\App\Http\Controllers\CRM\CRMController::class, 'broadcastSend'])->name('broadcast.send');
            Route::get('/broadcast/estimate-count', [\App\Http\Controllers\CRM\CRMController::class, 'estimateCount'])->name('broadcast.estimate-count');
        });

