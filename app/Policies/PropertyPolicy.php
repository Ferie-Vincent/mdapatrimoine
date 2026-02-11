<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
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
     * Super-admin or user assigned to the property's SCI.
     */
    public function view(User $user, Property $property): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->accessibleSciIds()->contains($property->sci_id);
    }

    /**
     * Super-admin or user assigned to the property's SCI can create.
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
     * Super-admin or user assigned to the property's SCI can update.
     * Lecture-seule cannot update.
     */
    public function update(User $user, Property $property): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isLectureSeule()) {
            return false;
        }

        return $user->accessibleSciIds()->contains($property->sci_id);
    }

    /**
     * Super-admin or user assigned to the property's SCI can delete.
     * Lecture-seule cannot delete.
     */
    public function delete(User $user, Property $property): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isLectureSeule()) {
            return false;
        }

        return $user->accessibleSciIds()->contains($property->sci_id);
    }
}
