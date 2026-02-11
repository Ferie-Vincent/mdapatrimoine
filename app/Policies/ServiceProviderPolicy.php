<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceProviderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ServiceProvider $provider): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->accessibleSciIds()->contains($provider->sci_id);
    }

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

    public function update(User $user, ServiceProvider $provider): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isLectureSeule()) {
            return false;
        }

        return $user->accessibleSciIds()->contains($provider->sci_id);
    }

    public function delete(User $user, ServiceProvider $provider): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isLectureSeule()) {
            return false;
        }

        return $user->accessibleSciIds()->contains($provider->sci_id);
    }
}
