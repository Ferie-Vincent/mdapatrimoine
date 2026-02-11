<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
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
     * Any authenticated user with SCI access can view a document.
     */
    public function view(User $user, Document $document): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->accessibleSciIds()->contains($document->sci_id);
    }

    /**
     * Super-admin or gestionnaire can create documents.
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
     * Super-admin or gestionnaire can generate documents.
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
    public function update(User $user, Document $document): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isLectureSeule()) {
            return false;
        }

        return $user->isGestionnaire() && $user->accessibleSciIds()->contains($document->sci_id);
    }

    /**
     * Super-admin or gestionnaire with SCI access can delete.
     * Lecture-seule cannot delete.
     */
    public function delete(User $user, Document $document): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isLectureSeule()) {
            return false;
        }

        return $user->isGestionnaire() && $user->accessibleSciIds()->contains($document->sci_id);
    }
}
