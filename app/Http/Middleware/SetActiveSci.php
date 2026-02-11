<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Sci;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetActiveSci
{
    /**
     * Handle an incoming request.
     *
     * Reads 'sci_id' from session or request, resolves the active SCI,
     * and shares 'activeSci' and 'userScis' with all views.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Determine the SCIs accessible by this user
        if ($user->isSuperAdmin()) {
            $userScis = Sci::orderBy('name')->get();
        } else {
            $userScis = $user->scis()->orderBy('name')->get();
        }

        // Resolve active SCI id from session only (switching is done via POST /switch-sci)
        $sciId = $request->session()->get('sci_id');

        $activeSci = null;

        if ($sciId) {
            // Verify the user actually has access to this SCI
            $activeSci = $userScis->firstWhere('id', (int) $sciId);
        }

        // If no valid SCI selected and user is not super_admin, default to first assigned SCI
        if (!$activeSci && !$user->isSuperAdmin()) {
            $activeSci = $userScis->first();

            if ($activeSci) {
                $request->session()->put('sci_id', $activeSci->id);
            }
        }

        // If super_admin with no SCI selected, activeSci remains null (show all)
        // Persist selection for super_admin too if they chose one
        if ($activeSci && $user->isSuperAdmin()) {
            $request->session()->put('sci_id', $activeSci->id);
        }

        // Make sci_id available as a request attribute for controllers
        $request->attributes->set('sci_id', $activeSci?->id);

        // Share with all views
        View::share('activeSci', $activeSci);
        View::share('userScis', $userScis);

        return $next($request);
    }
}
