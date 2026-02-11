<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()->with('scis');

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        if ($request->has('is_active') && $request->input('is_active') !== '') {
            $query->where('is_active', (bool) $request->input('is_active'));
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('users.index');
    }

    public function store(StoreUserRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validated();

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        $sciIds = $data['sci_ids'] ?? [];
        unset($data['sci_ids'], $data['password_confirmation'], $data['avatar']);

        $user = User::create($data);
        $user->scis()->sync($sciIds);

        AuditService::log('created', $user, $data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Utilisateur cree avec succes.']);
        }

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $user->load('scis');

        return view('users.show', compact('user'));
    }

    public function edit(User $user): RedirectResponse
    {
        return redirect()->route('users.show', $user);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $user);

        $data = $request->validated();

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        $sciIds = $data['sci_ids'] ?? [];
        unset($data['sci_ids'], $data['password_confirmation'], $data['avatar']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        $user->scis()->sync($sciIds);

        AuditService::log('updated', $user, $data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Utilisateur mis a jour avec succes.']);
        }

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        if ($user->id === auth()->id()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        AuditService::log('deleted', $user);

        return redirect()
            ->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
