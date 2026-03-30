<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService,
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Task::class);

        $sciId = $request->attributes->get('sci_id');
        $user = $request->user();
        $date = $request->input('date', today()->format('Y-m-d'));
        $showArchived = $request->boolean('show_archived', false);

        $kanbanData = $this->taskService->getKanbanData(
            $user->id,
            $sciId,
            $date,
            $showArchived
        );

        if ($request->query('format') === 'json') {
            return response()->json([
                'success' => true,
                'columns' => $kanbanData,
            ]);
        }

        $assignableUsers = [];
        if ($user->isSuperAdmin()) {
            $assignableUsers = User::where('is_active', true)
                ->whereIn('role', ['super_admin', 'gestionnaire'])
                ->pluck('name', 'id')
                ->toArray();
        }

        return view('tasks.index', [
            'kanbanData' => $kanbanData,
            'assignableUsers' => $assignableUsers,
            'selectedDate' => $date,
            'showArchived' => $showArchived,
        ]);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $this->authorize('create', Task::class);

        $sciId = $request->attributes->get('sci_id');

        // When "Toutes les SCIs" is active, default to user's first SCI
        if (!$sciId) {
            $user = $request->user();
            $firstSci = $user->isSuperAdmin()
                ? \App\Models\Sci::where('is_active', true)->first()
                : $user->scis()->first();

            if (!$firstSci) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune SCI disponible. Veuillez en creer une d\'abord.',
                ], 422);
            }

            $sciId = $firstSci->id;
        }

        $task = $this->taskService->createTask(
            $request->validated(),
            $request->user()->id,
            (int) $sciId
        );

        return response()->json([
            'success' => true,
            'task' => $task->load(['user:id,name', 'sci:id,name', 'related']),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $task = $this->taskService->updateTask($task, $request->validated());

        return response()->json([
            'success' => true,
            'task' => $task->load(['user:id,name', 'sci:id,name', 'related']),
        ]);
    }

    public function destroy(Request $request, Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $this->taskService->deleteTask($task);

        return response()->json(['success' => true]);
    }

    public function move(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $request->validate([
            'status' => ['required', 'in:a_faire,en_cours,bloque,fait,archive'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        $task = $this->taskService->moveTask(
            $task,
            $request->input('status'),
            $request->input('position')
        );

        return response()->json([
            'success' => true,
            'task' => $task->load(['user:id,name', 'sci:id,name', 'related']),
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'tasks' => ['required', 'array'],
            'tasks.*' => ['integer', 'exists:tasks,id'],
        ]);

        $this->taskService->reorderTasks($request->input('tasks'));

        return response()->json(['success' => true]);
    }

    public function assign(Request $request, Task $task): JsonResponse
    {
        $this->authorize('assign', Task::class);

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $task = $this->taskService->assignTask($task, (int) $request->input('user_id'));

        return response()->json([
            'success' => true,
            'task' => $task->load(['user:id,name', 'sci:id,name', 'related']),
        ]);
    }

    public function generateAuto(Request $request): JsonResponse
    {
        $this->authorize('create', Task::class);

        $sciId = $request->attributes->get('sci_id');
        $count = $this->taskService->generateAutoTasks($sciId ? (int) $sciId : null);

        return response()->json([
            'success' => true,
            'message' => "{$count} tâche(s) générée(s) automatiquement.",
            'count' => $count,
        ]);
    }
}
