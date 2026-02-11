<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Sci;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SciPolicy
{
    use HandlesAuthorization;

    /**
     * Any authenticated user can view the list of SCIs.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Super-admin can view any SCI.
     * Other roles can only view SCIs they are assigned to.
     */
    public function view(User $user, Sci $sci): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->scis()->where('scis.id', $sci->id)->exists();
    }

    /**
     * Only super-admin can create a new SCI.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Super-admin can update any SCI.
     * Other roles can update only SCIs they are assigned to.
     */
    public function update(User $user, Sci $sci): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->scis()->where('scis.id', $sci->id)->exists();
    }

    /**
     * Only super-admin or users assigned to the SCI can delete it.
     */
    public function delete(User $user, Sci $sci): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->scis()->where('scis.id', $sci->id)->exists();
    }
}
