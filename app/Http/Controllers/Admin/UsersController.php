<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UsersController extends Controller
{
    /**
     * Display user list with filters & search
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->where('role', 'user');

        // --- Filter ---
        if ($request->filled('filter')) {
            $query->when($request->filter == 'active', fn($q) => $q->where('is_active', 1))
                ->when($request->filter == 'inactive', fn($q) => $q->where('is_active', 0));
        }

        // --- Search by email ---
        if ($request->filled('search')) {
            $query->where('email', 'like', "%" . $request->search . "%");
        }

        $users = $query->orderByDesc('id')->paginate(10);

        return view('admin.pages.users.index', compact('users'));
    }


    /**
     * Update user basic info
     */

    public function update(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'is_active' => 'required',
        ]);

        $validated['is_active'] = (int) $request->is_active;

        $user->update([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'is_active' => $validated['is_active'],
        ]);

        return back()->with('success', 'User updated successfully!');
    }

}
