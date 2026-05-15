<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * @var list<string>
     */
    private array $manageableRoles = ['patient', 'companion', 'doctor', 'secretary'];

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $role = $request->string('role')->toString();
        $search = $request->string('search')->toString();

        $users = User::query()
            ->whereIn('role', $this->manageableRoles)
            ->when($role !== '', fn ($query) => $query->where('role', $role))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'manageableRoles' => $this->manageableRoles,
            'search' => $search,
            'activeRole' => $role,
        ]);
    }

    public function create()
    {
        $this->authorize('create', User::class);

        return view('admin.users.create', [
            'manageableRoles' => $this->manageableRoles,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', Rule::in($this->manageableRoles)],
        ], [
            'role.in' => get_translation('admin_users_role_not_allowed'),
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'email_verified_at' => now(),
        ]);

        return redirect(route_with_lang('admin.users.index'))
            ->with('success', get_translation('user_created_successfully'));
    }

    public function edit(string $lang, User $user)
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', [
            'user' => $user,
            'manageableRoles' => $this->manageableRoles,
        ]);
    }

    public function update(Request $request, string $lang, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'role' => ['required', Rule::in($this->manageableRoles)],
        ], [
            'role.in' => get_translation('admin_users_role_not_allowed'),
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return redirect(route_with_lang('admin.users.index'))
            ->with('success', get_translation('user_updated_successfully'));
    }

    public function destroy(string $lang, User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();

        return redirect(route_with_lang('admin.users.index'))
            ->with('success', get_translation('user_deleted_successfully'));
    }
}
