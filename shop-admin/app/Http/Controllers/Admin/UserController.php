<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Build query with search and filters
     * This method is designed to be API-friendly
     */
    private function buildQuery(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Sort
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');

        if (in_array($sortBy, ['id', 'name', 'email', 'role', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query;
    }

    /**
     * Get paginated users data
     * Returns array format for API compatibility
     */
    private function getListData(Request $request)
    {
        $perPage = (int)$request->input('per_page', 15);
        $users = $this->buildQuery($request)->paginate($perPage);

        return [
            'data' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
            'filters' => [
                'search' => $request->input('search'),
                'role' => $request->input('role'),
                'sort_by' => $request->input('sort_by', 'id'),
                'sort_order' => $request->input('sort_order', 'desc'),
            ]
        ];
    }

    public function index(Request $request)
    {
        $data = $this->getListData($request);
        $roles = ['admin' => 'Admin', 'user' => 'User'];

        // Return JSON if requested (API compatibility)
        if ($request->wantsJson() || $request->query('api')) {
            return response()->json($data);
        }

        // Return view for web
        return view('admin.users.index', [
            'users' => $data['data'],
            'pagination' => $data['pagination'],
            'filters' => $data['filters'],
            'roles' => $roles,
            'paginator' => $this->buildQuery($request)->paginate((int)$request->input('per_page', 15))
        ]);
    }

    public function create()
    {
        $roles = ['admin' => 'Admin', 'user' => 'User'];
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'User created successfully'], 201);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = ['admin' => 'Admin', 'user' => 'User'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Prevent admin from removing their own admin role accidentally
        if (Auth::id() === $user->id && ($data['role'] ?? $user->role) !== 'admin') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'You cannot remove your own admin role.'], 403);
            }
            return redirect()->route('admin.users.index')->with('error', 'You cannot remove your own admin role.');
        }

        $user->update($data);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'User updated successfully', 'data' => $user]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    public function destroy($id, Request $request)
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        if (Auth::id() === $user->id) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'You cannot delete your own account.'], 403);
            }
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'User deleted successfully']);
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}

