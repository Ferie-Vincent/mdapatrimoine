<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LeaseMonthly;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeaseMonthlyPolicy
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
     * Super-admin or user assigned to the monthly's SCI.
     */
    public function view(User $user, LeaseMonthly $leaseMonthly): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->accessibleSciIds()->contains($leaseMonthly->sci_id);
    }

    /**
     * Super-admin or gestionnaire can create.
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

        return true;
    }

    /**
     * Super-admin or gestionnaire assigned to the monthly's SCI can update.
     * Lecture-seule cannot update.
     */
    public function update(User $user, LeaseMonthly $leaseMonthly): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isLectureSeule()) {
            return false;
        }

        return $user->accessibleSciIds()->contains($leaseMonthly->sci_id);
    }

    /**
     * Super-admin or gestionnaire assigned to the monthly's SCI can delete.
     * Lecture-seule cannot delete.
     */
    public function delete(User $user, LeaseMonthly $leaseMonthly): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isLectureSeule()) {
            return false;
        }

        return $user->accessibleSciIds()->contains($leaseMonthly->sci_id);
    }
}
