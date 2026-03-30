<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Lease;
use App\Models\LeaseMonthly;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Reminder;
use App\Models\Sci;
use App\Models\ServiceProvider;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $gestionnaire;
    private User $lecteur;
    private Sci $sci;
    private Sci $otherSci;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create(['role' => 'super_admin']);
        $this->gestionnaire = User::factory()->create(['role' => 'gestionnaire']);
        $this->lecteur = User::factory()->create(['role' => 'lecture_seule']);

        $this->sci = Sci::factory()->create();
        $this->otherSci = Sci::factory()->create();

        // Gestionnaire and lecteur have access to $this->sci only
        $this->sci->users()->attach([$this->gestionnaire->id, $this->lecteur->id]);
    }

    /* ================================================================== */
    /*  SciPolicy                                                          */
    /* ================================================================== */

    public function test_sci_viewAny_allowed_for_all(): void
    {
        $this->assertTrue($this->superAdmin->can('viewAny', Sci::class));
        $this->assertTrue($this->gestionnaire->can('viewAny', Sci::class));
        $this->assertTrue($this->lecteur->can('viewAny', Sci::class));
    }

    public function test_sci_view_own(): void
    {
        $this->assertTrue($this->superAdmin->can('view', $this->sci));
        $this->assertTrue($this->gestionnaire->can('view', $this->sci));
        $this->assertTrue($this->lecteur->can('view', $this->sci));
    }

    public function test_sci_view_other_denied_for_non_admin(): void
    {
        $this->assertTrue($this->superAdmin->can('view', $this->otherSci));
        $this->assertFalse($this->gestionnaire->can('view', $this->otherSci));
        $this->assertFalse($this->lecteur->can('view', $this->otherSci));
    }

    public function test_sci_create_only_super_admin(): void
    {
        $this->assertTrue($this->superAdmin->can('create', Sci::class));
        $this->assertFalse($this->gestionnaire->can('create', Sci::class));
        $this->assertFalse($this->lecteur->can('create', Sci::class));
    }

    public function test_sci_update_super_admin_and_assigned(): void
    {
        $this->assertTrue($this->superAdmin->can('update', $this->sci));
        $this->assertTrue($this->gestionnaire->can('update', $this->sci));
        $this->assertFalse($this->gestionnaire->can('update', $this->otherSci));
    }

    public function test_sci_delete_super_admin_and_assigned(): void
    {
        $this->assertTrue($this->superAdmin->can('delete', $this->sci));
        $this->assertTrue($this->gestionnaire->can('delete', $this->sci));
        $this->assertFalse($this->gestionnaire->can('delete', $this->otherSci));
    }

    /* ================================================================== */
    /*  PropertyPolicy                                                      */
    /* ================================================================== */

    public function test_property_viewAny_allowed_for_all(): void
    {
        $this->assertTrue($this->superAdmin->can('viewAny', Property::class));
        $this->assertTrue($this->gestionnaire->can('viewAny', Property::class));
        $this->assertTrue($this->lecteur->can('viewAny', Property::class));
    }

    public function test_property_view_by_sci_access(): void
    {
        $property = Property::factory()->create(['sci_id' => $this->sci->id]);
        $otherProperty = Property::factory()->create(['sci_id' => $this->otherSci->id]);

        $this->assertTrue($this->superAdmin->can('view', $otherProperty));
        $this->assertTrue($this->gestionnaire->can('view', $property));
        $this->assertFalse($this->gestionnaire->can('view', $otherProperty));
        $this->assertTrue($this->lecteur->can('view', $property));
        $this->assertFalse($this->lecteur->can('view', $otherProperty));
    }

    public function test_property_create_denied_for_lecteur(): void
    {
        $this->assertTrue($this->superAdmin->can('create', Property::class));
        $this->assertTrue($this->gestionnaire->can('create', Property::class));
        $this->assertFalse($this->lecteur->can('create', Property::class));
    }

    public function test_property_update_denied_for_lecteur(): void
    {
        $property = Property::factory()->create(['sci_id' => $this->sci->id]);

        $this->assertTrue($this->superAdmin->can('update', $property));
        $this->assertTrue($this->gestionnaire->can('update', $property));
        $this->assertFalse($this->lecteur->can('update', $property));
    }

    public function test_property_delete_denied_for_other_sci(): void
    {
        $otherProperty = Property::factory()->create(['sci_id' => $this->otherSci->id]);

        $this->assertTrue($this->superAdmin->can('delete', $otherProperty));
        $this->assertFalse($this->gestionnaire->can('delete', $otherProperty));
    }

    /* ================================================================== */
    /*  TenantPolicy                                                        */
    /* ================================================================== */

    public function test_tenant_crud_permissions(): void
    {
        $tenant = Tenant::factory()->create(['sci_id' => $this->sci->id]);
        $otherTenant = Tenant::factory()->create(['sci_id' => $this->otherSci->id]);

        // viewAny
        $this->assertTrue($this->lecteur->can('viewAny', Tenant::class));

        // view own SCI
        $this->assertTrue($this->gestionnaire->can('view', $tenant));
        $this->assertFalse($this->gestionnaire->can('view', $otherTenant));

        // create
        $this->assertTrue($this->gestionnaire->can('create', Tenant::class));
        $this->assertFalse($this->lecteur->can('create', Tenant::class));

        // update
        $this->assertTrue($this->gestionnaire->can('update', $tenant));
        $this->assertFalse($this->lecteur->can('update', $tenant));

        // delete
        $this->assertTrue($this->superAdmin->can('delete', $tenant));
        $this->assertFalse($this->lecteur->can('delete', $tenant));
    }

    /* ================================================================== */
    /*  LeasePolicy                                                         */
    /* ================================================================== */

    public function test_lease_crud_permissions(): void
    {
        $property = Property::factory()->create(['sci_id' => $this->sci->id]);
        $tenant = Tenant::factory()->create(['sci_id' => $this->sci->id]);
        $lease = Lease::factory()->create([
            'sci_id' => $this->sci->id, 'property_id' => $property->id, 'tenant_id' => $tenant->id,
        ]);

        $otherProperty = Property::factory()->create(['sci_id' => $this->otherSci->id]);
        $otherTenant = Tenant::factory()->create(['sci_id' => $this->otherSci->id]);
        $otherLease = Lease::factory()->create([
            'sci_id' => $this->otherSci->id, 'property_id' => $otherProperty->id, 'tenant_id' => $otherTenant->id,
        ]);

        $this->assertTrue($this->gestionnaire->can('view', $lease));
        $this->assertFalse($this->gestionnaire->can('view', $otherLease));
        $this->assertTrue($this->gestionnaire->can('create', Lease::class));
        $this->assertFalse($this->lecteur->can('create', Lease::class));
        $this->assertTrue($this->gestionnaire->can('update', $lease));
        $this->assertFalse($this->lecteur->can('update', $lease));
        $this->assertFalse($this->gestionnaire->can('delete', $otherLease));
    }

    /* ================================================================== */
    /*  LeaseMonthlyPolicy                                                  */
    /* ================================================================== */

    public function test_lease_monthly_crud_permissions(): void
    {
        $property = Property::factory()->create(['sci_id' => $this->sci->id]);
        $tenant = Tenant::factory()->create(['sci_id' => $this->sci->id]);
        $lease = Lease::factory()->create([
            'sci_id' => $this->sci->id, 'property_id' => $property->id, 'tenant_id' => $tenant->id,
        ]);
        $monthly = LeaseMonthly::factory()->create([
            'lease_id' => $lease->id, 'sci_id' => $this->sci->id,
        ]);

        $this->assertTrue($this->gestionnaire->can('view', $monthly));
        $this->assertTrue($this->gestionnaire->can('create', LeaseMonthly::class));
        $this->assertFalse($this->lecteur->can('create', LeaseMonthly::class));
        $this->assertTrue($this->gestionnaire->can('update', $monthly));
        $this->assertFalse($this->lecteur->can('update', $monthly));
    }

    /* ================================================================== */
    /*  PaymentPolicy                                                       */
    /* ================================================================== */

    public function test_payment_requires_gestionnaire_for_write(): void
    {
        $property = Property::factory()->create(['sci_id' => $this->sci->id]);
        $tenant = Tenant::factory()->create(['sci_id' => $this->sci->id]);
        $lease = Lease::factory()->create([
            'sci_id' => $this->sci->id, 'property_id' => $property->id, 'tenant_id' => $tenant->id,
        ]);
        $monthly = LeaseMonthly::factory()->create([
            'lease_id' => $lease->id, 'sci_id' => $this->sci->id,
        ]);
        $payment = Payment::factory()->create([
            'lease_monthly_id' => $monthly->id, 'sci_id' => $this->sci->id,
        ]);

        // viewAny allowed for all
        $this->assertTrue($this->lecteur->can('viewAny', Payment::class));

        // view allowed for users with SCI access
        $this->assertTrue($this->gestionnaire->can('view', $payment));
        $this->assertTrue($this->lecteur->can('view', $payment));

        // create requires gestionnaire+
        $this->assertTrue($this->superAdmin->can('create', Payment::class));
        $this->assertTrue($this->gestionnaire->can('create', Payment::class));
        $this->assertFalse($this->lecteur->can('create', Payment::class));

        // update/delete require gestionnaire + SCI access
        $this->assertTrue($this->gestionnaire->can('update', $payment));
        $this->assertFalse($this->lecteur->can('update', $payment));
        $this->assertTrue($this->gestionnaire->can('delete', $payment));
        $this->assertFalse($this->lecteur->can('delete', $payment));
    }

    /* ================================================================== */
    /*  DocumentPolicy                                                      */
    /* ================================================================== */

    public function test_document_policy_with_generate(): void
    {
        $document = Document::factory()->create(['sci_id' => $this->sci->id]);

        $this->assertTrue($this->superAdmin->can('viewAny', Document::class));
        $this->assertTrue($this->lecteur->can('view', $document));
        $this->assertTrue($this->gestionnaire->can('create', Document::class));
        $this->assertFalse($this->lecteur->can('create', Document::class));
        $this->assertTrue($this->gestionnaire->can('generate', Document::class));
        $this->assertFalse($this->lecteur->can('generate', Document::class));
        $this->assertTrue($this->gestionnaire->can('update', $document));
        $this->assertFalse($this->lecteur->can('update', $document));
    }

    /* ================================================================== */
    /*  ReminderPolicy                                                      */
    /* ================================================================== */

    public function test_reminder_policy_with_generate(): void
    {
        $property = Property::factory()->create(['sci_id' => $this->sci->id]);
        $tenant = Tenant::factory()->create(['sci_id' => $this->sci->id]);
        $lease = Lease::factory()->create([
            'sci_id' => $this->sci->id, 'property_id' => $property->id, 'tenant_id' => $tenant->id,
        ]);
        $monthly = LeaseMonthly::factory()->create([
            'lease_id' => $lease->id, 'sci_id' => $this->sci->id,
        ]);
        $reminder = Reminder::factory()->create([
            'sci_id' => $this->sci->id, 'lease_monthly_id' => $monthly->id,
        ]);

        $this->assertTrue($this->gestionnaire->can('view', $reminder));
        $this->assertTrue($this->gestionnaire->can('create', Reminder::class));
        $this->assertFalse($this->lecteur->can('create', Reminder::class));
        $this->assertTrue($this->gestionnaire->can('generate', Reminder::class));
        $this->assertFalse($this->lecteur->can('generate', Reminder::class));
    }

    /* ================================================================== */
    /*  UserPolicy                                                          */
    /* ================================================================== */

    public function test_user_policy_super_admin_only(): void
    {
        $this->assertTrue($this->superAdmin->can('viewAny', User::class));
        $this->assertFalse($this->gestionnaire->can('viewAny', User::class));
        $this->assertFalse($this->lecteur->can('viewAny', User::class));

        $this->assertTrue($this->superAdmin->can('create', User::class));
        $this->assertFalse($this->gestionnaire->can('create', User::class));

        $this->assertTrue($this->superAdmin->can('update', $this->gestionnaire));
        $this->assertFalse($this->gestionnaire->can('update', $this->lecteur));

        $this->assertTrue($this->superAdmin->can('delete', $this->gestionnaire));
        $this->assertFalse($this->gestionnaire->can('delete', $this->lecteur));
    }

    /* ================================================================== */
    /*  ServiceProviderPolicy                                               */
    /* ================================================================== */

    public function test_service_provider_crud_permissions(): void
    {
        $provider = ServiceProvider::factory()->create(['sci_id' => $this->sci->id]);
        $otherProvider = ServiceProvider::factory()->create(['sci_id' => $this->otherSci->id]);

        $this->assertTrue($this->lecteur->can('viewAny', ServiceProvider::class));
        $this->assertTrue($this->gestionnaire->can('view', $provider));
        $this->assertFalse($this->gestionnaire->can('view', $otherProvider));
        $this->assertTrue($this->gestionnaire->can('create', ServiceProvider::class));
        $this->assertFalse($this->lecteur->can('create', ServiceProvider::class));
        $this->assertTrue($this->gestionnaire->can('update', $provider));
        $this->assertFalse($this->lecteur->can('update', $provider));
        $this->assertFalse($this->gestionnaire->can('delete', $otherProvider));
    }
}
