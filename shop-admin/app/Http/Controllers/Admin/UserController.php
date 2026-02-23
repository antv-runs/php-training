<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        // add validation and store logic later
        return redirect()->route('admin.users.index')->with('success', 'User created (placeholder)');
    }

    public function edit($id)
    {
        return view('admin.users.edit');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.users.index')->with('success', 'User updated (placeholder)');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.users.index')->with('success', 'User deleted (placeholder)');
    }
}
