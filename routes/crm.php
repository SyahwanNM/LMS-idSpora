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
use App\Http\Controllers\TrainerApiController;
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

            // Course Certificates
            Route::get('/certificates/courses/{course}/edit', [\App\Http\Controllers\CRM\CertificateController::class, 'editCourse'])->name('certificates.edit-course');
            Route::put('/certificates/courses/{course}', [\App\Http\Controllers\CRM\CertificateController::class, 'updateCourse'])->name('certificates.update-course');
            Route::get('/courses/{course}/certificates/generate-massal', [\App\Http\Controllers\CRM\CertificateController::class, 'generateMassalCourse'])->name('certificates.generate-massal-course');

            // Customer management
            Route::get('/customers', [\App\Http\Controllers\CRM\CRMController::class, 'customers'])->name('customers.index');
            Route::get('/customers/{customer}', [\App\Http\Controllers\CRM\CRMController::class, 'showCustomer'])->name('customers.show');
            Route::get('/customers/{customer}/edit', [\App\Http\Controllers\CRM\CRMController::class, 'editCustomer'])->name('customers.edit');
            Route::put('/customers/{customer}', [\App\Http\Controllers\CRM\CRMController::class, 'updateCustomer'])->name('customers.update');

            // Feedback Analysis
            Route::get('/feedback', [\App\Http\Controllers\CRM\CRMController::class, 'feedbackAnalysis'])->name('feedback.index');

            // Support Messages
            Route::get('/support', [\App\Http\Controllers\CRM\CRMController::class, 'supportMessages'])->name('support.index');
            Route::post('/support/{message}/status', [\App\Http\Controllers\CRM\CRMController::class, 'updateSupportStatus'])->name('support.updateStatus');

            // Broadcast/Blast
            Route::get('/broadcast', [\App\Http\Controllers\CRM\CRMController::class, 'broadcastIndex'])->name('broadcast.index');
            Route::get('/broadcast/create', [\App\Http\Controllers\CRM\CRMController::class, 'broadcastCreate'])->name('broadcast.create');
            Route::post('/broadcast/send', [\App\Http\Controllers\CRM\CRMController::class, 'broadcastSend'])->name('broadcast.send');
        });
