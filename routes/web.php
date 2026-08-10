<?php

Route::get('/temizle', function() {
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    return "Önbellek temizlendi!";
});

Route::get('/migrate-veritabanini-guncelle', function () {
    Artisan::call('migrate --force');
    return "Veritabanı başarıyla güncellendi!";
});

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BioLinkController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShortLinkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AppSettingsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\BioLinkBlockController;
use App\Http\Controllers\CustomPageController;
use App\Http\Controllers\PaymentSettingsController;
use App\Http\Controllers\Gateways\MollieController;
use App\Http\Controllers\Gateways\PaypalController;
use App\Http\Controllers\Gateways\PaystackController;
use App\Http\Controllers\Gateways\RazorpayController;
use App\Http\Controllers\Gateways\StripeController;
use App\Http\Controllers\Gateways\ToslaController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\InstallerDBController;
use App\Http\Controllers\VersionController;

use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$installed = Storage::disk('public')->exists('installed');

if ($installed) {
    require __DIR__ . '/auth.php';

    Route::get('/', [HomeController::class, 'Home']);
    Route::get('/lang/{locale}', [AppSettingsController::class, 'languageChange']);

    Route::middleware(['auth', 'verified', 'role:SUPER-ADMIN|PREMIUM|STANDARD|BASIC', 'next_payment'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Bio-link items routes start
        Route::prefix('/bio-links')->group(function () {
            Route::get('/', [BioLinkController::class, 'index']);
            Route::post('/create', [BioLinkController::class, 'create']);
            Route::patch('/update/{id}', [BioLinkController::class, 'update']);
            Route::delete('/delete/{id}', [BioLinkController::class, 'delete']);
            Route::get('/search', [BioLinkController::class, 'search']);

            Route::prefix('/customize')->group(function () {
                Route::get('/{id}', [BioLinkController::class, 'customize']);

                Route::put('/add-socials/{id}', [BioLinkController::class, 'add_socials']);
                Route::post('/update-profile/{id}', [BioLinkController::class, 'update_profile']);
                Route::post('/update-logo/{id}', [BioLinkController::class, 'update_logo']);
                Route::put('/update-theme/{themeId}/{linkId}', [BioLinkController::class, 'update_theme']);

                Route::prefix('/custom-theme')->group(function () {
                    Route::post('/create/{id}', [BioLinkController::class, 'custom_theme_create']);
                    Route::put('/active/{id}', [BioLinkController::class, 'custom_theme_active']);
                    Route::post('/update/{themeId}/{linkId}', [BioLinkController::class, 'custom_theme_update']);
                });

                Route::prefix('/block')->group(function () {
                    Route::post('/add', [BioLinkBlockController::class, 'add']);
                    Route::post('/edit/{id}', [BioLinkBlockController::class, 'edit']);
                    Route::put('/position/{id}', [BioLinkBlockController::class, 'position']);
                    Route::delete('/delete/{id}', [BioLinkBlockController::class, 'delete']);
                });
            });
        });
        // Bio-link items routes end


        // Short-links routes start
        Route::prefix('/short-links')->group(function () {
            Route::get('/', [ShortLinkController::class, 'index']);
            Route::post('/create', [ShortLinkController::class, 'create']);
            Route::patch('/update/{id}', [ShortLinkController::class, 'update']);
            Route::delete('/delete/{id}', [ShortLinkController::class, 'delete']);
            Route::get('/search', [ShortLinkController::class, 'search']);
        });
        // Short-links routes end
        Route::get('/link/analytics/{id}', [ShortLinkController::class, 'analytics']);


        // QR-Code routes start
        Route::prefix('/qrcodes')->group(function () {
            Route::get('/', [QRCodeController::class, 'index']);
            Route::get('/create', [QRCodeController::class, 'create'])->middleware('check_payment');
            Route::post('/save', [QRCodeController::class, 'save_qr'])->middleware('check_payment');
            Route::post('/create/link-qr', [QRCodeController::class, 'save_link_qr'])->middleware('check_payment');
            Route::delete('/delete/{id}', [QRCodeController::class, 'delete']);
        });
        // QR-Code routes end


        // Project routes start
        Route::prefix('/projects')->group(function () {
            Route::get('/', [ProjectController::class, 'index']);
            Route::post('/create', [ProjectController::class, 'create']);
            Route::put('/update/{id}', [ProjectController::class, 'update']);
            Route::delete('/delete/{id}', [ProjectController::class, 'delete']);
            Route::get('/qrcodes/{id}', [ProjectController::class, 'qrcodes']);
            Route::get('/search', [ProjectController::class, 'search']);
        });
        // Project routes end


        // Account settings routes start
        Route::prefix('/settings')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('settings');
            Route::post('/profile', [SettingsController::class, 'profile']);
        });
        // Account settings routes end


        // Plans Routes Start
        Route::prefix('/current-plan')->group(function () {
            Route::get('/', [PlanController::class, 'user_plan'])->name('plan');
            Route::get('/update', [PlanController::class, 'get_plan'])->name('plan.select');
            Route::get('/selected/{id}', [PlanController::class, 'selected'])->name('plan.selected');
            Route::post('/basic-plan/{id}', [PlanController::class, 'basic_plan'])->name('plan.basic-plan');
        });
        // Plans Routes End


        Route::get('/billing/{id}', [SubscriptionController::class, 'Billing'])->name('billing');
        // Paypal routes start
        Route::post('paypal/payment', [PaypalController::class, 'payment'])->name('paypal.payment');
        Route::get('paypal/success', [PaypalController::class, 'success'])->name('paypal.success');
        Route::get('paypal/cancel', [PaypalController::class, 'cancel'])->name('paypal.cancel');


        // Paypal routes start
        Route::post('stripe/payment', [StripeController::class, 'payment'])->name('stripe.payment');
        Route::get('stripe/success', [StripeController::class, 'success'])->name('stripe.success');
        Route::get('stripe/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');


        // Razorpay routes start
        Route::get('razorpay/form', [RazorpayController::class, 'show_form'])->name('razorpay.form');
        Route::post('razorpay/payment', [RazorpayController::class, 'payment'])->name('razorpay.payment');


        // mollie routes start
        Route::post('mollie/payment', [MollieController::class, 'payment'])->name('mollie.payment');
        Route::get('mollie/success', [MollieController::class, 'success'])->name('mollie.success');


        // paystack routes start
        Route::get('paystack/redirect', [PaystackController::class, 'paystack_redirect'])->name('paystack.redirect');
        Route::get('paystack/callback', [PaystackController::class, 'verify_transaction'])->name('paystack.callback');

        // Tosla ödeme
        Route::post('tosla/payment', [ToslaController::class, 'payment'])->name('tosla.payment');
        Route::get('tosla/form', [ToslaController::class, 'form'])->name('tosla.form');
        Route::get('tosla/callback', [ToslaController::class, 'callback'])->name('tosla.callback');
        Route::get('tosla/success', [ToslaController::class, 'success'])->name('tosla.success');
        Route::get('tosla/cancel', [ToslaController::class, 'cancel'])->name('tosla.cancel');
    });


    // Only admin routes
    Route::middleware(['auth', 'web', 'role:SUPER-ADMIN'])->prefix('/admin')->group(function () {
        Route::prefix('/users')->group(function () {
            Route::get('/', [SuperAdminController::class, 'users']);
            Route::put('/update/{id}', [SuperAdminController::class, 'users_update']);
            Route::get('/search', [SuperAdminController::class, 'users_search']);
        });


        // Manage themes Routes Started
        Route::prefix('/manage-themes')->group(function () {
            Route::get('/', [SuperAdminController::class, 'ManageThemes']);
            Route::put('/type/{id}', [SuperAdminController::class, 'TypeThemes']);
        });
        // Manage themes Routes Started


        // Subscriptions plan routes start
        Route::prefix('/subscriptions')->group(function () {
            Route::get('/', [SubscriptionController::class, 'subscriptions']);
            Route::get('/search', [SubscriptionController::class, 'subscriptions_search']);
        });
        // Subscriptions plan routes end


        //Testimonials Routes Started
        Route::prefix('/testimonials')->group(function () {
            Route::get('/', [SuperAdminController::class, 'Testimonials']);
            Route::post('/add', [SuperAdminController::class, 'AddTestimonial']);
            Route::post('/update/{id}', [SuperAdminController::class, 'UpdateTestimonial']);
            Route::delete('/delete/{id}', [SuperAdminController::class, 'DeleteTestimonial']);
        });
        //Testimonials Routes End


        //Plans Routes Start
        Route::prefix('/pricing-plans')->group(function () {
            Route::get('/', [PlanController::class, 'get_admin_plan'])->name('plans');
            Route::get('/create', [PlanController::class, 'create_plan'])->name('plan.create');
            Route::post('/store', [PlanController::class, 'store_plan'])->name('plan.store');
            Route::get('/update/{id}', [PlanController::class, 'get_update_plan'])->name('plan.update');
            Route::put('/update/{id}', [PlanController::class, 'update_plan'])->name('plan.update');
        });
        //Plans Routes End


        //---------- App Settings routes start ----------//
        Route::prefix('/app-settings')->group(function () {
            Route::get('/', [AppSettingsController::class, 'index']);
            Route::post('/app/update', [AppSettingsController::class, 'app_update'])->name('settings.app');
            Route::patch('/auth/google', [AppSettingsController::class, 'auth_google'])->name('settings.google');
            Route::patch('/smtp/update', [AppSettingsController::class, 'smtp_update'])->name('settings.smtp');
        });
        //---------- App Settings routes end ----------// 


        //---------- App Control routes start ----------//
        Route::get('/app-control', [AppSettingsController::class, 'appControl']);
        //---------- App Control routes start ----------//


        //---------- Payment Settings routes start ----------//
        Route::prefix('/payments-setup')->group(function () {
            Route::get('/', [PaymentSettingsController::class, 'index']);
            Route::patch('/tosla', [PaymentSettingsController::class, 'tosla_update'])->name('payment.tosla');
        });
        //---------- Payment Settings routes end ----------// 


        //---------- Custom page create routes start ----------//
        Route::prefix('/custom-page')->group(function () {
            Route::get('/', [CustomPageController::class, 'index'])->name('custom-page');
            Route::get('/create', [CustomPageController::class, 'create'])->name('custom-page.create');
            Route::post('/store', [CustomPageController::class, 'store'])->name('custom-page.store');
            Route::get('/update/{id}', [CustomPageController::class, 'update'])->name('custom-page.update');
            Route::put('/save/{id}', [CustomPageController::class, 'save'])->name('custom-page.save');
            Route::delete('/delete/{id}', [CustomPageController::class, 'delete'])->name('custom-page.delete');
        });
        //---------- Custom page create routes end ----------//
    });


    // Intro page update for only admin
    Route::middleware(['auth', 'web', 'role:SUPER-ADMIN'])->group(function () {
        Route::prefix('home-section')->group(function () {
            Route::put('/edit/{sectionId}', [HomeController::class, 'EditHomeSection']);
            Route::put('/edit-list/{sectionId}', [HomeController::class, 'EditSectionList']);
        });

        // New version update
        Route::get('/version/check', [VersionController::class, 'checkVersion']);
        Route::get('/version/current', [VersionController::class, 'getCurrentVersion']);
        Route::get('/version/update', [VersionController::class, 'updateVersion']);
    });

    // Accessing biolink by link name
    Route::get('/{linkName}', [BioLinkController::class, 'bioLinkView']);

    // Custom created pages
    Route::get('/app/{page}', [CustomPageController::class, 'pageView'])->name('custom-page.view');
} else {

    Route::prefix('/setup')->group(function () {
        Route::get('/', [InstallerController::class, 'checkServer'])->name('setup');

        Route::get('/step-1', [InstallerController::class, 'viewStep1'])->name('view.step1');
        Route::post('/step-1', [InstallerController::class, 'setupStep1'])->name('setup.step1');

        Route::get('/step-2', [InstallerController::class, 'viewStep2'])->name('view.step2');
        Route::post('/step-2', [InstallerController::class, 'setupStep2'])->name('setup.step2');

        Route::get('/step-3', [InstallerController::class, 'viewStep3'])->name('view.step3');
        Route::post('/step-3', [InstallerController::class, 'setupStep3'])->name('setup.step3');

        Route::get('/install', [InstallerController::class, 'installView']);
        Route::post('/install', [InstallerController::class, 'installVersion']);

        Route::post('/check-database', [InstallerDBController::class, 'databaseChecker']);
        Route::get('/generate-app-key', [InstallerController::class, 'generateAppKey']);
        Route::post('/verify-purchase', [InstallerController::class, 'verifyPurchase']);
    });
    Route::get('/{url?}', [InstallerController::class, 'backToSetup'])->where('url', '^(?!setup).*$');
}
