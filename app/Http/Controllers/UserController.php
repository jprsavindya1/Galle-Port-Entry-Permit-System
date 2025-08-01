<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
class UserController extends Controller
{   /*
     ***********  User Crud functions *********   
    */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

public function store(Request $request)
{
    $allowedRoles = ['clerk'];

    if (auth()->user()->role === 'super-admin') {
        $allowedRoles = ['clerk', 'admin', 'super-admin'];
    } elseif (auth()->user()->role === 'admin') {
        $allowedRoles = ['clerk'];
    }

    $validated = $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'role' => ['required', Rule::in($allowedRoles)],
    ]);

    User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => $validated['role'],
    ]);

    return redirect()->route('users.index')->with('success', 'User created successfully');
}


    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
{
    $allowedRoles = ['clerk'];

    if (auth()->user()->role === 'super-admin') {
        $allowedRoles = ['clerk', 'admin', 'super-admin'];
    } elseif (auth()->user()->role === 'admin') {
        $allowedRoles = ['clerk'];
    }

    $validated = $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:6|confirmed',
        'role' => ['required', Rule::in($allowedRoles)],
    ]);

    // Prevent role downgrade of a higher-privilege user by admin
    if (
        auth()->user()->role !== 'super-admin' &&
        ($user->role === 'admin' || $user->role === 'super-admin')
    ) {
        return back()->withErrors(['role' => 'You are not authorized to modify admin or super-admin users.']);
    }

    $user->update([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'role' => $validated['role'],
        'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
    ]);

    return redirect()->route('users.index')->with('success', 'User updated successfully');
}
public function destroy(User $user)
{
    if (
        $user->role === 'admin' && auth()->user()->role !== 'super-admin' ||
        $user->role === 'super-admin' && auth()->user()->role !== 'super-admin'
    ) {
        return back()->withErrors(['role' => 'You are not authorized to delete this user.']);
    }

    $user->delete();

    return redirect()->route('users.index')->with('success', 'User deleted successfully');
}


}
