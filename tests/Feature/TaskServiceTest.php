<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Lease;
use App\Models\LeaseMonthly;
use App\Models\Property;
use App\Models\Sci;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TaskService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    private TaskService $taskService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskService = app()->make(TaskService::class);
    }

    /**
     * Create base test data: SCI, users, property, tenant.
     */
    private function createBaseData(): array
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $gestionnaire = User::create([
            'name' => 'Gestionnaire A',
            'email' => 'gestionnaire@test.com',
            'password' => bcrypt('password'),
            'role' => 'gestionnaire',
            'is_active' => true,
        ]);

        $lectureSeule = User::create([
            'name' => 'Lecture Seule',
            'email' => 'lecture@test.com',
            'password' => bcrypt('password'),
            'role' => 'lecture_seule',
            'is_active' => true,
        ]);

        $sci = Sci::create([
            'name' => 'SCI Test',
            'rccm' => 'RCCM-TEST-001',
            'ifu' => 'IFU-TEST',
            'address' => 'Cotonou, Bénin',
            'phone' => '+229 97 00 00 00',
            'email' => 'contact@scitest.com',
        ]);

        $gestionnaire->scis()->attach($sci->id);
        $lectureSeule->scis()->attach($sci->id);

        $property = Property::create([
            'sci_id' => $sci->id,
            'reference' => 'APT-TEST-001',
            'type' => 'appartement',
            'address' => '123 Rue Test',
            'city' => 'Cotonou',
            'status' => 'disponible',
            'rent_reference' => 150000,
            'charges_reference' => 15000,
        ]);

        $tenant = Tenant::create([
            'sci_id' => $sci->id,
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean.dupont@test.com',
            'phone' => '+229 96 00 00 00',
            'address' => '456 Rue Commerce',
        ]);

        return compact('superAdmin', 'gestionnaire', 'lectureSeule', 'sci', 'property', 'tenant');
    }

    /* ------------------------------------------------------------------ */
    /*  CRUD Tests                                                         */
    /* ------------------------------------------------------------------ */

    public function test_create_manual_task_successfully(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['gestionnaire']);

        $task = $this->taskService->createTask([
            'title' => 'Appeler le plombier',
            'category' => 'autre',
            'priority' => 'haute',
            'scheduled_date' => today()->format('Y-m-d'),
        ], $data['gestionnaire']->id, $data['sci']->id);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Appeler le plombier',
            'category' => 'autre',
            'priority' => 'haute',
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
        ]);
    }

    public function test_update_task_status_to_fait_sets_completed_at(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['gestionnaire']);

        $task = Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Test task',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'a_faire',
            'scheduled_date' => today(),
            'position' => 0,
        ]);

        $updated = $this->taskService->updateTask($task, ['status' => 'fait']);

        $this->assertNotNull($updated->completed_at);
    }

    public function test_move_task_back_from_fait_clears_completed_at(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['gestionnaire']);

        $task = Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Test task',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'fait',
            'scheduled_date' => today(),
            'completed_at' => now(),
            'position' => 0,
        ]);

        $moved = $this->taskService->moveTask($task, 'a_faire');

        $this->assertNull($moved->completed_at);
        $this->assertEquals('a_faire', $moved->status);
    }

    /* ------------------------------------------------------------------ */
    /*  Auto-generation Tests                                              */
    /* ------------------------------------------------------------------ */

    public function test_generate_auto_tasks_for_unpaid_echeances(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['superAdmin']);

        $lease = Lease::create([
            'sci_id' => $data['sci']->id,
            'property_id' => $data['property']->id,
            'tenant_id' => $data['tenant']->id,
            'start_date' => today()->subMonths(3),
            'end_date' => today()->addMonths(9),
            'duration_months' => 12,
            'rent_amount' => 150000,
            'charges_amount' => 15000,
            'deposit_amount' => 300000,
            'status' => 'actif',
        ]);

        LeaseMonthly::create([
            'lease_id' => $lease->id,
            'sci_id' => $data['sci']->id,
            'month' => today()->format('Y-m'),
            'rent_due' => 150000,
            'charges_due' => 15000,
            'penalty_due' => 0,
            'total_due' => 165000,
            'paid_amount' => 0,
            'remaining_amount' => 165000,
            'status' => 'impaye',
            'due_date' => today(),
        ]);

        $count = $this->taskService->generateAutoTasks($data['sci']->id);

        $this->assertGreaterThanOrEqual(1, $count);
        $this->assertDatabaseHas('tasks', [
            'sci_id' => $data['sci']->id,
            'category' => 'encaissement',
            'related_type' => LeaseMonthly::class,
            'is_auto' => true,
        ]);
    }

    public function test_auto_tasks_deduplication(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['superAdmin']);

        $lease = Lease::create([
            'sci_id' => $data['sci']->id,
            'property_id' => $data['property']->id,
            'tenant_id' => $data['tenant']->id,
            'start_date' => today()->subMonths(3),
            'end_date' => today()->addMonths(9),
            'duration_months' => 12,
            'rent_amount' => 150000,
            'charges_amount' => 15000,
            'deposit_amount' => 300000,
            'status' => 'actif',
        ]);

        LeaseMonthly::create([
            'lease_id' => $lease->id,
            'sci_id' => $data['sci']->id,
            'month' => today()->format('Y-m'),
            'rent_due' => 150000,
            'charges_due' => 15000,
            'penalty_due' => 0,
            'total_due' => 165000,
            'paid_amount' => 0,
            'remaining_amount' => 165000,
            'status' => 'impaye',
            'due_date' => today(),
        ]);

        // First call
        $count1 = $this->taskService->generateAutoTasks($data['sci']->id);
        // Second call
        $count2 = $this->taskService->generateAutoTasks($data['sci']->id);

        $this->assertGreaterThanOrEqual(1, $count1);
        $this->assertEquals(0, $count2);

        $taskCount = Task::where('related_type', LeaseMonthly::class)
            ->where('category', 'encaissement')
            ->count();
        $this->assertEquals(1, $taskCount);
    }

    public function test_generate_lease_expiry_tasks(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['superAdmin']);

        Lease::create([
            'sci_id' => $data['sci']->id,
            'property_id' => $data['property']->id,
            'tenant_id' => $data['tenant']->id,
            'start_date' => today()->subYear(),
            'end_date' => today()->addDays(20),
            'duration_months' => 12,
            'rent_amount' => 150000,
            'charges_amount' => 15000,
            'deposit_amount' => 300000,
            'status' => 'actif',
        ]);

        $count = $this->taskService->generateAutoTasks($data['sci']->id);

        $this->assertGreaterThanOrEqual(1, $count);
        $this->assertDatabaseHas('tasks', [
            'sci_id' => $data['sci']->id,
            'category' => 'administratif',
            'related_type' => Lease::class,
            'is_auto' => true,
        ]);
    }

    public function test_generate_tenant_id_expiry_tasks(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['superAdmin']);

        $data['tenant']->update([
            'id_expiration' => today()->addDays(15),
            'is_active' => true,
        ]);

        $count = $this->taskService->generateAutoTasks($data['sci']->id);

        $this->assertGreaterThanOrEqual(1, $count);
        $this->assertDatabaseHas('tasks', [
            'sci_id' => $data['sci']->id,
            'category' => 'administratif',
            'related_type' => Tenant::class,
            'related_id' => $data['tenant']->id,
            'is_auto' => true,
        ]);
    }

    public function test_deduplication_ignores_archived_status(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['superAdmin']);

        $data['tenant']->update([
            'id_expiration' => today()->addDays(15),
            'is_active' => true,
        ]);

        // Generate task, then archive it
        $this->taskService->generateAutoTasks($data['sci']->id);
        Task::where('related_type', Tenant::class)
            ->where('related_id', $data['tenant']->id)
            ->update(['status' => 'archive']);

        // Re-generate — should NOT create a duplicate (isDuplicate checks all statuses)
        $count = $this->taskService->generateAutoTasks($data['sci']->id);

        $this->assertEquals(0, $count);
        $this->assertEquals(1, Task::where('related_type', Tenant::class)
            ->where('related_id', $data['tenant']->id)
            ->count());
    }

    /* ------------------------------------------------------------------ */
    /*  Reorder Tests                                                      */
    /* ------------------------------------------------------------------ */

    public function test_reorder_tasks(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['gestionnaire']);

        $task1 = Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Task 1',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'a_faire',
            'scheduled_date' => today(),
            'position' => 0,
        ]);

        $task2 = Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Task 2',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'a_faire',
            'scheduled_date' => today(),
            'position' => 1,
        ]);

        $task3 = Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Task 3',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'a_faire',
            'scheduled_date' => today(),
            'position' => 2,
        ]);

        // Reorder: 3, 1, 2
        $this->taskService->reorderTasks([$task3->id, $task1->id, $task2->id]);

        $this->assertEquals(0, $task3->fresh()->position);
        $this->assertEquals(1, $task1->fresh()->position);
        $this->assertEquals(2, $task2->fresh()->position);
    }

    public function test_reorder_is_idempotent(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['gestionnaire']);

        $task1 = Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Task A',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'a_faire',
            'scheduled_date' => today(),
            'position' => 0,
        ]);

        $task2 = Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Task B',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'a_faire',
            'scheduled_date' => today(),
            'position' => 1,
        ]);

        $order = [$task2->id, $task1->id];

        $this->taskService->reorderTasks($order);
        $this->taskService->reorderTasks($order);

        $this->assertEquals(0, $task2->fresh()->position);
        $this->assertEquals(1, $task1->fresh()->position);
    }

    /* ------------------------------------------------------------------ */
    /*  Assignation Tests                                                  */
    /* ------------------------------------------------------------------ */

    public function test_assign_task_to_user(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['superAdmin']);

        $task = Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['superAdmin']->id,
            'created_by' => $data['superAdmin']->id,
            'title' => 'Task to assign',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'a_faire',
            'scheduled_date' => today(),
            'position' => 0,
        ]);

        $assigned = $this->taskService->assignTask($task, $data['gestionnaire']->id);

        $this->assertEquals($data['gestionnaire']->id, $assigned->user_id);
    }

    public function test_auto_tasks_not_assigned_to_lecture_seule(): void
    {
        // Create a SCI with only a lecture_seule user — no gestionnaire, no super_admin
        $lectureSeule = User::create([
            'name' => 'Only Lecture',
            'email' => 'only-lecture@test.com',
            'password' => bcrypt('password'),
            'role' => 'lecture_seule',
            'is_active' => true,
        ]);

        $sci = Sci::create([
            'name' => 'SCI Lecture Only',
            'rccm' => 'RCCM-LS-001',
            'ifu' => 'IFU-LS',
            'address' => 'Cotonou',
            'phone' => '+229 97 11 11 11',
            'email' => 'ls@scitest.com',
        ]);

        $lectureSeule->scis()->attach($sci->id);

        $property = Property::create([
            'sci_id' => $sci->id,
            'reference' => 'APT-LS-001',
            'type' => 'appartement',
            'address' => '999 Rue LS',
            'city' => 'Cotonou',
            'status' => 'disponible',
            'rent_reference' => 100000,
            'charges_reference' => 10000,
        ]);

        $tenant = Tenant::create([
            'sci_id' => $sci->id,
            'first_name' => 'LS',
            'last_name' => 'User',
            'email' => 'ls-user@test.com',
            'phone' => '+229 96 11 11 11',
            'address' => '999 Rue',
            'id_expiration' => today()->addDays(10),
            'is_active' => true,
        ]);

        $count = $this->taskService->generateAutoTasks($sci->id);

        // No tasks should be created because there's no valid assignee
        $this->assertEquals(0, $count);
        $this->assertEquals(0, Task::where('sci_id', $sci->id)->count());
    }

    /* ------------------------------------------------------------------ */
    /*  Kanban Data Tests                                                  */
    /* ------------------------------------------------------------------ */

    public function test_kanban_data_returns_correct_structure(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['gestionnaire']);

        $columns = $this->taskService->getKanbanData(
            $data['gestionnaire']->id,
            $data['sci']->id,
            today()->format('Y-m-d'),
        );

        $this->assertArrayHasKey('a_faire', $columns);
        $this->assertArrayHasKey('en_cours', $columns);
        $this->assertArrayHasKey('bloque', $columns);
        $this->assertArrayHasKey('fait', $columns);
        $this->assertArrayHasKey('archive', $columns);
        $this->assertCount(5, $columns);
    }

    public function test_kanban_data_includes_eager_loaded_relations(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['gestionnaire']);

        Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Task with relations',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'a_faire',
            'scheduled_date' => today(),
            'position' => 0,
        ]);

        $columns = $this->taskService->getKanbanData(
            $data['gestionnaire']->id,
            $data['sci']->id,
            today()->format('Y-m-d'),
        );

        $task = $columns['a_faire'][0];

        $this->assertTrue($task->relationLoaded('user'));
        $this->assertTrue($task->relationLoaded('sci'));
        $this->assertTrue($task->relationLoaded('related'));
    }

    /* ------------------------------------------------------------------ */
    /*  Visibility Tests                                                   */
    /* ------------------------------------------------------------------ */

    public function test_visibility_gestionnaire_sees_only_own_tasks(): void
    {
        $data = $this->createBaseData();

        $gestionnaire2 = User::create([
            'name' => 'Gestionnaire B',
            'email' => 'gestionnaire-b@test.com',
            'password' => bcrypt('password'),
            'role' => 'gestionnaire',
            'is_active' => true,
        ]);

        // Task for gestionnaire A
        Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Task A',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'a_faire',
            'scheduled_date' => today(),
            'position' => 0,
        ]);

        // Task for gestionnaire B
        Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $gestionnaire2->id,
            'created_by' => $gestionnaire2->id,
            'title' => 'Task B',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'a_faire',
            'scheduled_date' => today(),
            'position' => 0,
        ]);

        $this->actingAs($data['gestionnaire']);
        $columns = $this->taskService->getKanbanData(
            $data['gestionnaire']->id,
            $data['sci']->id,
            today()->format('Y-m-d'),
        );

        $allTasks = collect($columns)->flatten();
        $this->assertCount(1, $allTasks);
        $this->assertEquals('Task A', $allTasks->first()->title);
    }

    public function test_visibility_super_admin_sees_all_tasks(): void
    {
        $data = $this->createBaseData();

        // Task for gestionnaire
        Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Gestionnaire Task',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'a_faire',
            'scheduled_date' => today(),
            'position' => 0,
        ]);

        // Task for super admin
        Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['superAdmin']->id,
            'created_by' => $data['superAdmin']->id,
            'title' => 'Admin Task',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'a_faire',
            'scheduled_date' => today(),
            'position' => 1,
        ]);

        $this->actingAs($data['superAdmin']);
        $columns = $this->taskService->getKanbanData(
            $data['superAdmin']->id,
            $data['sci']->id,
            today()->format('Y-m-d'),
        );

        $allTasks = collect($columns)->flatten();
        $this->assertCount(2, $allTasks);
    }

    /* ------------------------------------------------------------------ */
    /*  HTTP Route Tests                                                   */
    /* ------------------------------------------------------------------ */

    public function test_move_task_invalid_status_returns_422(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['gestionnaire']);

        $task = Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Task to move',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'a_faire',
            'scheduled_date' => today(),
            'position' => 0,
        ]);

        $response = $this->patchJson(route('tasks.move', $task), [
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422);
    }

    public function test_create_task_as_lecture_seule_returns_403(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['lectureSeule']);

        $response = $this->postJson(route('tasks.store'), [
            'title' => 'Should not be created',
            'category' => 'autre',
            'priority' => 'moyenne',
            'scheduled_date' => today()->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
    }

    /* ------------------------------------------------------------------ */
    /*  Archive Tests                                                      */
    /* ------------------------------------------------------------------ */

    public function test_auto_archive_only_affects_fait_tasks(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['gestionnaire']);

        // Task "fait" completed 10 days ago — should be archived
        Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Old completed',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'fait',
            'scheduled_date' => today()->subDays(10),
            'completed_at' => now()->subDays(10),
            'position' => 0,
        ]);

        // Task "en_cours" — should NOT be archived
        Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Still in progress',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'en_cours',
            'scheduled_date' => today()->subDays(10),
            'position' => 1,
        ]);

        // Task "bloque" — should NOT be archived
        Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Still blocked',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'bloque',
            'scheduled_date' => today()->subDays(10),
            'position' => 2,
        ]);

        $archived = $this->taskService->archiveOldTasks(7);

        $this->assertEquals(1, $archived);
        $this->assertDatabaseHas('tasks', ['title' => 'Old completed', 'status' => 'archive']);
        $this->assertDatabaseHas('tasks', ['title' => 'Still in progress', 'status' => 'en_cours']);
        $this->assertDatabaseHas('tasks', ['title' => 'Still blocked', 'status' => 'bloque']);
    }

    /* ------------------------------------------------------------------ */
    /*  Progress count Test                                                */
    /* ------------------------------------------------------------------ */

    public function test_progress_count_matches_kanban_data(): void
    {
        $data = $this->createBaseData();
        $this->actingAs($data['gestionnaire']);

        // 2 pending, 1 completed
        Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Pending 1',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'a_faire',
            'scheduled_date' => today(),
            'position' => 0,
        ]);

        Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Pending 2',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'en_cours',
            'scheduled_date' => today(),
            'position' => 1,
        ]);

        Task::create([
            'sci_id' => $data['sci']->id,
            'user_id' => $data['gestionnaire']->id,
            'created_by' => $data['gestionnaire']->id,
            'title' => 'Completed',
            'category' => 'autre',
            'priority' => 'moyenne',
            'status' => 'fait',
            'scheduled_date' => today(),
            'completed_at' => now(),
            'position' => 2,
        ]);

        $columns = $this->taskService->getKanbanData(
            $data['gestionnaire']->id,
            $data['sci']->id,
            today()->format('Y-m-d'),
        );

        $completedCount = count($columns['fait']);
        $totalCount = collect($columns)->except('archive')->flatten()->count();

        $this->assertEquals(1, $completedCount);
        $this->assertEquals(3, $totalCount);
    }
}
