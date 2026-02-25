<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Get admin dashboard information.
     * Returns JSON response.
     */
    public function index()
    {
        return response()->json([
            'message' => 'Admin dashboard',
            'user' => auth()->user(),
            'timestamp' => now()
        ]);
    }
}
