<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Accepts one or more role names separated by commas.
     * Example usage in routes: middleware('role:super_admin,gestionnaire')
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Accès non autorisé.');
        }

        // Flatten roles in case they are passed as a single comma-separated string
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $allowedRoles[] = trim($r);
            }
        }

        if (!in_array($user->role, $allowedRoles, true)) {
            abort(403, 'Vous n\'avez pas les droits nécessaires pour accéder à cette ressource.');
        }

        return $next($request);
    }
}
