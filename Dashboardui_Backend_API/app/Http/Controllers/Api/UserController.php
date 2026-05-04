<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * Get list of users
     */
    public function index(Request $request)
    {
        try {
            $users = User::select('id', 'name', 'email', 'role', 'created_at')
                ->paginate($request->get('per_page', 15));

            return $this->success($users);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch users: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get a specific user
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            return $this->success([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ]);
        } catch (\Exception $e) {
            return $this->notFound('User not found');
        }
    }

    /**
     * Update a user
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $currentUser = $request->user();

            // Only allow users to update themselves, or admins to update anyone
            if ($user->id !== $currentUser->id && !$currentUser->hasAdminPrivileges()) {
                return $this->forbidden('You do not have permission to update this user');
            }

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
                'role' => 'sometimes|string|in:'.implode(',', User::getAvailableRoles()),
            ]);

            // Only superadmin can change roles
            if (isset($validated['role']) && !$currentUser->isSuperAdmin()) {
                return $this->forbidden('Only superadmin can change user roles');
            }

            $user->update($validated);

            return $this->success([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ], 'User updated successfully');
        } catch (ValidationException $e) {
            return $this->validationError($e->validator);
        } catch (\Exception $e) {
            return $this->error('Failed to update user: ' . $e->getMessage(), 500);
        }
    }
}
