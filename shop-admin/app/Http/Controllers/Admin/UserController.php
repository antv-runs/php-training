<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Contracts\UserServiceInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * Inject UserServiceInterface
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $data = $this->userService->getListData($request);
        $roles = $this->userService->getRoles();

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
            'paginator' => $data['paginator']
        ]);
    }

    public function create()
    {
        $roles = $this->userService->getRoles();
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

        $this->userService->createUser($data);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'User created successfully'], 201);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = $this->userService->getRoles();
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

        $result = $this->userService->updateUser($user, $data);

        if (!$result['success']) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $result['message']], 403);
            }
            return redirect()->route('admin.users.index')->with('error', $result['message']);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => $result['message'], 'data' => $result['data']]);
        }

        return redirect()->route('admin.users.index')->with('success', $result['message']);
    }

    public function destroy($id, Request $request)
    {
        $user = User::findOrFail($id);

        $result = $this->userService->deleteUser($user);

        if (!$result['success']) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $result['message']], 403);
            }
            return redirect()->route('admin.users.index')->with('error', $result['message']);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => $result['message']]);
        }

        return redirect()->route('admin.users.index')->with('success', $result['message']);
    }

    /**
     * Show trashed users
     */
    public function trashed(Request $request)
    {
        $data = $this->userService->getTrashed($request);
        $roles = $this->userService->getRoles();

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('admin.users.trashed', [
            'users' => $data['data'],
            'pagination' => $data['pagination'],
            'paginator' => $data['paginator'],
            'roles' => $roles
        ]);
    }

    /**
     * Restore user
     */
    public function restore($id, Request $request)
    {
        $result = $this->userService->restoreUser($id);

        if (!$result['success']) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $result['message']], 400);
            }
            return redirect()->route('admin.users.trashed')->with('error', $result['message']);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => $result['message'], 'data' => $result['data']]);
        }

        return redirect()->route('admin.users.trashed')->with('success', $result['message']);
    }

    /**
     * Force delete user
     */
    public function forceDelete($id, Request $request)
    {
        $result = $this->userService->forceDeleteUser($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => $result['message']]);
        }

        return redirect()->route('admin.users.trashed')->with('success', $result['message']);
    }
}

