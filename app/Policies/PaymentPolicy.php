<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
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
     * Super-admin or gestionnaire with SCI access can view.
     * Lecture-seule with SCI access can also view.
     */
    public function view(User $user, Payment $payment): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->accessibleSciIds()->contains($payment->sci_id);
    }

    /**
     * Super-admin or gestionnaire with SCI access can create.
     * Lecture-seule cannot create payments.
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
     * Super-admin or gestionnaire with SCI access can update.
     * Lecture-seule cannot update.
     */
    public function update(User $user, Payment $payment): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isLectureSeule()) {
            return false;
        }

        return $user->isGestionnaire() && $user->accessibleSciIds()->contains($payment->sci_id);
    }

    /**
     * Super-admin or gestionnaire with SCI access can delete.
     * Lecture-seule cannot delete.
     */
    public function delete(User $user, Payment $payment): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isLectureSeule()) {
            return false;
        }

        return $user->isGestionnaire() && $user->accessibleSciIds()->contains($payment->sci_id);
    }
}
