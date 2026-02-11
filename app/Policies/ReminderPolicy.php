<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Reminder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReminderPolicy
{
    use HandlesAuthorization;

    /**
     * Any authenticated user can view the list.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Any authenticated user with SCI access can view a reminder.
     */
    public function view(User $user, Reminder $reminder): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->accessibleSciIds()->contains($reminder->sci_id);
    }

    /**
     * Super-admin or gestionnaire can create reminders.
     * Lecture-seule cannot create.
     */
    public function create(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isLectureSeule()) {
            return false;
        }

        return $user->isGestionnaire();
    }

    /**
     * Super-admin or gestionnaire can generate reminders.
     * Lecture-seule cannot generate.
     */
    public function generate(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isLectureSeule()) {
            return false;
        }

        return $user->isGestionnaire();
    }

    /**
     * Super-admin or gestionnaire with SCI access can update.
     * Lecture-seule cannot update.
     */
    public function update(User $user, Reminder $reminder): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isLectureSeule()) {
            return false;
        }

        return $user->isGestionnaire() && $user->accessibleSciIds()->contains($reminder->sci_id);
    }

    /**
     * Super-admin or gestionnaire with SCI access can delete.
     * Lecture-seule cannot delete.
     */
    public function delete(User $user, Reminder $reminder): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isLectureSeule()) {
            return false;
        }

        return $user->isGestionnaire() && $user->accessibleSciIds()->contains($reminder->sci_id);
    }
}
