<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of all users with role tabs
     */
    public function index(Request $request)
    {
        // Get search query and active tab (role)
        $search = $request->get('search', '');
        $activeTab = $request->get('tab', 'all'); // 'all' or specific role

        // Get available roles
        $availableRoles = User::getAvailableRoles();

        // Get user counts per role for tabs
        $roleCounts = [];
        $roleCounts['all'] = User::count();
        foreach ($availableRoles as $role) {
            $roleCounts[$role] = User::where('role', $role)->count();
        }

        // Build query
        $query = User::query();

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Apply role filter (tab selection)
        if ($activeTab && $activeTab !== 'all') {
            $query->where('role', $activeTab);
        }

        // Order by created_at descending
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Append query parameters to pagination links
        $users->appends([
            'search' => $search,
            'tab' => $activeTab,
        ]);

        // If AJAX request, return only the table content and pagination
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('superadmin.users.partials.table', [
                    'users' => $users,
                    'activeTab' => $activeTab,
                ])->render(),
                'pagination' => view('superadmin.users.partials.pagination', [
                    'users' => $users,
                    'activeTab' => $activeTab,
                    'search' => $search,
                ])->render(),
                'total' => $users->total(),
                'currentPage' => $users->currentPage(),
                'totalPages' => $users->lastPage(),
                'firstItem' => $users->firstItem(),
                'lastItem' => $users->lastItem(),
            ]);
        }

        return view('superadmin.users.index', [
            'users' => $users,
            'availableRoles' => $availableRoles,
            'search' => $search,
            'activeTab' => $activeTab,
            'roleCounts' => $roleCounts,
        ]);
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $availableRoles = User::getAvailableRoles();

        return view('superadmin.users.create', [
            'availableRoles' => $availableRoles,
        ]);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        // Get tab parameter for redirect
        $redirectTab = $request->get('tab', 'all');

        // Use Validator facade to manually validate and control redirect
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:' . implode(',', User::getAvailableRoles()),
        ], [
            'name.required' => __('messages.name_required'),
            'email.required' => __('messages.email_required'),
            'email.email' => __('messages.email_invalid'),
            'email.unique' => __('messages.email_already_exists'),
            'password.required' => __('messages.password_required'),
            'password.min' => __('messages.password_min_length'),
            'password.confirmed' => __('messages.password_confirmation_mismatch'),
            'role.required' => __('messages.role_required'),
            'role.in' => __('messages.invalid_role'),
        ]);

        if ($validator->fails()) {
            // If AJAX request, return JSON with validation errors
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.validation_failed'),
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Redirect back with errors and preserve tab parameter
            return redirect(route_with_lang('superadmin.users.index', ['tab' => $redirectTab]))
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
        ]);

        // If AJAX request, return JSON success response with updated table
        if ($request->ajax() || $request->wantsJson()) {
            // Get updated users list for the same tab
            $query = User::query();
            if ($redirectTab && $redirectTab !== 'all') {
                $query->where('role', $redirectTab);
            }
            $users = $query->orderBy('created_at', 'desc')->paginate(15);
            $users->appends(['search' => $request->get('search', ''), 'tab' => $redirectTab]);
            
            // Get updated role counts
            $roleCounts = [];
            $roleCounts['all'] = User::count();
            foreach (User::getAvailableRoles() as $role) {
                $roleCounts[$role] = User::where('role', $role)->count();
            }
            
            return response()->json([
                'success' => true,
                'message' => __('messages.user_created_successfully'),
                'html' => view('superadmin.users.partials.table', [
                    'users' => $users,
                    'activeTab' => $redirectTab,
                ])->render(),
                'pagination' => view('superadmin.users.partials.pagination', [
                    'users' => $users,
                    'activeTab' => $redirectTab,
                    'search' => $request->get('search', ''),
                ])->render(),
                'total' => $users->total(),
                'roleCounts' => $roleCounts,
                'tab' => $redirectTab,
            ]);
        }

        // Redirect back to the same tab that was active
        return redirect(route_with_lang('superadmin.users.index', ['tab' => $redirectTab]))
            ->with('success', __('messages.user_created_successfully'));
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $availableRoles = User::getAvailableRoles();

        return view('superadmin.users.show', [
            'user' => $user,
            'availableRoles' => $availableRoles,
        ]);
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $availableRoles = User::getAvailableRoles();

        return view('superadmin.users.edit', [
            'user' => $user,
            'availableRoles' => $availableRoles,
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|string|in:' . implode(',', User::getAvailableRoles()),
        ], [
            'name.required' => __('messages.name_required'),
            'email.required' => __('messages.email_required'),
            'email.email' => __('messages.email_invalid'),
            'email.unique' => __('messages.email_already_exists'),
            'password.min' => __('messages.password_min_length'),
            'password.confirmed' => __('messages.password_confirmation_mismatch'),
            'role.required' => __('messages.role_required'),
            'role.in' => __('messages.invalid_role'),
        ]);

        // Update user
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return redirect(route_with_lang('superadmin.users.index'))
            ->with('success', __('messages.user_updated_successfully'));
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return redirect(route_with_lang('superadmin.users.index'))
                ->with('error', __('messages.cannot_delete_yourself'));
        }

        $user->delete();

        return redirect(route_with_lang('superadmin.users.index'))
            ->with('success', __('messages.user_deleted_successfully'));
    }
}
