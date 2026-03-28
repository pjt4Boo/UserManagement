<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $this->authorize('viewAny', User::class);

        // If user is not admin, redirect to their own profile
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('users.show', auth()->user());
        }

        $users = User::paginate(15);

        return view('users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(CreateUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $user = User::create($request->validated());

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        $this->authorize('view', $user);

        return view('users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('users.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validated();

        // Only update password if provided
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Deactivate the specified user.
     */
    public function deactivate(User $user): RedirectResponse
    {
        $this->authorize('deactivate', $user);

        $user->update(['status' => 'inactive']);

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'User deactivated successfully.');
    }

    /**
     * Activate the specified user.
     */
    public function activate(User $user): RedirectResponse
    {
        $this->authorize('deactivate', $user);

        $user->update(['status' => 'active']);

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'User activated successfully.');
    }

    /**
     * Soft delete the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Show audit logs for a user.
     */
    public function auditLogs(User $user): View
    {
        $this->authorize('view', $user);

        $auditLogs = $user->auditLogs()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('users.audit-logs', [
            'user' => $user,
            'auditLogs' => $auditLogs,
        ]);
    }
}
