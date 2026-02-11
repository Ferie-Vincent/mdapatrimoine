<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\Lease;
use App\Models\LeaseMonthly;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Reminder;
use App\Models\Sci;
use App\Models\Tenant;
use App\Models\ServiceProvider as ServiceProviderModel;
use App\Models\User;
use App\Policies\DocumentPolicy;
use App\Policies\LeaseMonthlyPolicy;
use App\Policies\LeasePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PropertyPolicy;
use App\Policies\ReminderPolicy;
use App\Policies\SciPolicy;
use App\Policies\TenantPolicy;
use App\Policies\ServiceProviderPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Sci::class, SciPolicy::class);
        Gate::policy(Property::class, PropertyPolicy::class);
        Gate::policy(Tenant::class, TenantPolicy::class);
        Gate::policy(Lease::class, LeasePolicy::class);
        Gate::policy(LeaseMonthly::class, LeaseMonthlyPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(Document::class, DocumentPolicy::class);
        Gate::policy(Reminder::class, ReminderPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(ServiceProviderModel::class, ServiceProviderPolicy::class);

        // Super admin bypasses all gates
        Gate::before(function ($user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });
    }
}
