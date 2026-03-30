<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->id === $task->user_id;
    }

    public function create(User $user): bool
    {
        if ($user->isSuperAdmin() || $user->isGestionnaire()) {
            return true;
        }

        return false;
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isLectureSeule()) {
            return false;
        }

        return $user->id === $task->user_id;
    }

    public function delete(User $user, Task $task): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isLectureSeule()) {
            return false;
        }

        return $user->id === $task->user_id;
    }

    public function assign(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
