<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DossierCardController;
use App\Http\Controllers\ExcelDatabaseController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\FinancialCurrentController;
use App\Http\Controllers\MonthlyExcelViewController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\LeaseMonthlyController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\SciController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ProviderContractController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\SetActiveSci;
use Illuminate\Support\Facades\Route;

// Twilio webhook (no auth, no CSRF)
Route::post('/webhooks/twilio/status', [\App\Http\Controllers\TwilioWebhookController::class, 'status'])
    ->name('webhooks.twilio.status');

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Authenticated + SCI-scoped routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', SetActiveSci::class])->group(function () {

    // CSRF token refresh (for offline sync replay)
    Route::get('/csrf-token', fn () => response()->json(['token' => csrf_token()]))->name('csrf-token');

    // Dashboard & SCI switching (all roles)
    Route::post('/switch-sci', [DashboardController::class, 'switchSci'])->name('switch-sci');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/search', [SearchController::class, 'search'])->name('search');

    /*
    |----------------------------------------------------------------------
    | Super admin only
    |----------------------------------------------------------------------
    */
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/scis/create', [SciController::class, 'create'])->name('scis.create');
        Route::post('/scis', [SciController::class, 'store'])->name('scis.store');
        Route::get('/scis/{sci}/edit', [SciController::class, 'edit'])->name('scis.edit');
        Route::put('/scis/{sci}', [SciController::class, 'update'])->name('scis.update');
        Route::delete('/scis/{sci}', [SciController::class, 'destroy'])->name('scis.destroy');

        // Users CRUD
        Route::resource('users', UserController::class);

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    /*
    |----------------------------------------------------------------------
    | Write routes: super_admin + gestionnaire
    | (must be before wildcard show routes to avoid {param} catching "create")
    |----------------------------------------------------------------------
    */
    Route::middleware('role:super_admin,gestionnaire')->group(function () {
        // Properties CRUD
        Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
        Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
        Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
        Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
        Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
        Route::delete('/properties/{property}/photos/{index}', [PropertyController::class, 'deletePhoto'])->name('properties.delete-photo');

        // Tenants CRUD
        Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
        Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{tenant}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
        Route::put('/tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
        Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');

        // Leases CRUD
        Route::get('/leases/create', [LeaseController::class, 'create'])->name('leases.create');
        Route::post('/leases', [LeaseController::class, 'store'])->name('leases.store');
        Route::get('/leases/{lease}/edit', [LeaseController::class, 'edit'])->name('leases.edit');
        Route::put('/leases/{lease}', [LeaseController::class, 'update'])->name('leases.update');
        Route::delete('/leases/{lease}', [LeaseController::class, 'destroy'])->name('leases.destroy');
        Route::post('/leases/{lease}/terminate', [LeaseController::class, 'terminate'])->name('leases.terminate');
        Route::post('/leases/{lease}/activate', [LeaseController::class, 'activate'])->name('leases.activate');

        // Payments (create/store)
        Route::get('/payments/create/{monthly}', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::post('/payments/refund-deposit', [PaymentController::class, 'refundDeposit'])->name('payments.refund-deposit');

        // Monthly generation
        Route::post('/monthlies/generate', [LeaseMonthlyController::class, 'generateMonthlies'])->name('monthlies.generate');

        // Document generation (all POST routes)
        Route::post('/documents/generate-quittance', [DocumentController::class, 'generateQuittance'])->name('documents.generate-quittance');
        Route::post('/documents/generate-receipt', [DocumentController::class, 'generateReceipt'])->name('documents.generate-receipt');
        Route::post('/documents/generate-notice', [DocumentController::class, 'generateNotice'])->name('documents.generate-notice');
        Route::post('/documents/generate-statement', [DocumentController::class, 'generateStatement'])->name('documents.generate-statement');
        Route::post('/documents/generate-monthly-report', [DocumentController::class, 'generateMonthlyReport'])->name('documents.generate-monthly-report');
        Route::post('/documents/generate-attestation', [DocumentController::class, 'generateAttestation'])->name('documents.generate-attestation');

        // Reminders (create, send, auto-generate)
        Route::post('/reminders', [ReminderController::class, 'store'])->name('reminders.store');
        Route::post('/reminders/{reminder}/send', [ReminderController::class, 'send'])->name('reminders.send');
        Route::post('/reminders/auto-generate', [ReminderController::class, 'autoGenerate'])->name('reminders.auto-generate');

        // Audit Logs (read-only but restricted to these roles)
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');

        // Point Financier Courant (write)
        Route::post('/financial-current/provisions', [FinancialCurrentController::class, 'storeProvision'])->name('financial-current.store-provision');
        Route::delete('/financial-current/provisions/{provision}', [FinancialCurrentController::class, 'destroyProvision'])->name('financial-current.destroy-provision');
        Route::post('/financial-current/purchases', [FinancialCurrentController::class, 'storePurchase'])->name('financial-current.store-purchase');
        Route::delete('/financial-current/purchases/{purchase}', [FinancialCurrentController::class, 'destroyPurchase'])->name('financial-current.destroy-purchase');
        Route::post('/financial-current/budgets', [FinancialCurrentController::class, 'storeBudget'])->name('financial-current.store-budget');
        Route::post('/financial-current/fixed-charges', [FinancialCurrentController::class, 'storeFixedCharge'])->name('financial-current.store-fixed-charge');
        Route::delete('/financial-current/fixed-charges/{fixedCharge}', [FinancialCurrentController::class, 'destroyFixedCharge'])->name('financial-current.destroy-fixed-charge');
        Route::get('/financial-current/attestation/{type}/{id}', [FinancialCurrentController::class, 'showAttestation'])->name('financial-current.attestation');
        Route::post('/financial-current/attestation/{type}/{id}/signature', [FinancialCurrentController::class, 'saveSignature'])->name('financial-current.save-signature');

        // PDF Excel-style generation
        Route::post('/documents/generate-fiche-locataire', [DocumentController::class, 'generateFicheLocataire'])->name('documents.generate-fiche-locataire');
        Route::post('/documents/generate-recu-excel', [DocumentController::class, 'generateRecuExcel'])->name('documents.generate-recu-excel');
        Route::post('/documents/generate-quittance-excel', [DocumentController::class, 'generateQuittanceExcel'])->name('documents.generate-quittance-excel');

        // Service Providers CRUD
        Route::post('/service-providers', [ServiceProviderController::class, 'store'])->name('service-providers.store');
        Route::put('/service-providers/{provider}', [ServiceProviderController::class, 'update'])->name('service-providers.update');
        Route::delete('/service-providers/{provider}', [ServiceProviderController::class, 'destroy'])->name('service-providers.destroy');

        // Provider Contracts CRUD
        Route::post('/provider-contracts', [ProviderContractController::class, 'store'])->name('provider-contracts.store');
        Route::put('/provider-contracts/{contract}', [ProviderContractController::class, 'update'])->name('provider-contracts.update');
        Route::delete('/provider-contracts/{contract}', [ProviderContractController::class, 'destroy'])->name('provider-contracts.destroy');

        // Staff (Personnel) CRUD + Payroll
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
        Route::post('/staff/payrolls', [StaffController::class, 'storePayroll'])->name('staff.store-payroll');
        Route::delete('/staff/payrolls/{payroll}', [StaffController::class, 'destroyPayroll'])->name('staff.destroy-payroll');
    });

    /*
    |----------------------------------------------------------------------
    | Read-only routes (all authenticated users, including lecture_seule)
    | (wildcard {param} routes must come after specific routes like /create)
    |----------------------------------------------------------------------
    */
    // Properties
    Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');

    // Tenants
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/{tenant}', [TenantController::class, 'show'])->name('tenants.show');

    // Leases
    Route::get('/leases', [LeaseController::class, 'index'])->name('leases.index');
    Route::get('/leases/{lease}', [LeaseController::class, 'show'])->name('leases.show');

    // Monthlies
    Route::get('/monthlies', [LeaseMonthlyController::class, 'index'])->name('monthlies.index');
    Route::get('/monthlies/{monthly}', [LeaseMonthlyController::class, 'show'])->name('monthlies.show');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

    // Documents (read & download)
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');

    // Reminders (read)
    Route::get('/reminders', [ReminderController::class, 'index'])->name('reminders.index');

    // Exports
    Route::get('/exports/tenants', [ExportController::class, 'exportTenants'])->name('exports.tenants');
    Route::get('/exports/properties', [ExportController::class, 'exportProperties'])->name('exports.properties');
    Route::get('/exports/payments', [ExportController::class, 'exportPayments'])->name('exports.payments');
    Route::get('/exports/unpaid', [ExportController::class, 'exportUnpaid'])->name('exports.unpaid');
    Route::get('/exports/leases', [ExportController::class, 'exportLeases'])->name('exports.leases');
    Route::get('/exports/monthlies', [ExportController::class, 'exportMonthlies'])->name('exports.monthlies');
    Route::get('/exports/service-providers', [ExportController::class, 'exportServiceProviders'])->name('exports.service-providers');
    Route::get('/exports/staff', [ExportController::class, 'exportStaff'])->name('exports.staff');
    Route::get('/exports/staff-payroll', [ExportController::class, 'exportStaffPayroll'])->name('exports.staff-payroll');
    Route::get('/exports/scis', [ExportController::class, 'exportScis'])->name('exports.scis');

    // Service Providers (read-only for all)
    Route::get('/service-providers', [ServiceProviderController::class, 'index'])->name('service-providers.index');

    // Staff / Personnel (read-only for all)
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');

    // Gallery (read-only for all)
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');

    // SCIs (read-only for all)
    Route::get('/scis', [SciController::class, 'index'])->name('scis.index');
    Route::get('/scis/{sci}', [SciController::class, 'show'])->name('scis.show');

    // Excel-style screens (read-only for all authenticated)
    Route::get('/excel-database', [ExcelDatabaseController::class, 'index'])->name('excel.database');
    Route::get('/dossier-card/{lease}', [DossierCardController::class, 'show'])->name('dossier-card.show');
    Route::get('/monthly-management', [MonthlyExcelViewController::class, 'index'])->name('monthly-management.index');
    Route::get('/financial-current', [FinancialCurrentController::class, 'index'])->name('financial-current.index');
});

require __DIR__.'/auth.php';
