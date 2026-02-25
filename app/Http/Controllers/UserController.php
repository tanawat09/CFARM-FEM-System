<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,safety_officer',
            'phone' => 'nullable|string',
            'employee_id' => 'nullable|string',
            'department' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'],
            'employee_id' => $validated['employee_id'],
            'department' => $validated['department'],
            'status' => 'active',
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('users.index')->with('success', 'เพิ่มผู้ใช้งานสำเร็จ');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,safety_officer',
            'phone' => 'nullable|string',
            'employee_id' => 'nullable|string',
            'department' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $user->update($validated);
        $user->syncRoles([$validated['role']]);

        // If password is provided, update it
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('users.index')->with('success', 'อัปเดตข้อมูลผู้ใช้งานสำเร็จ');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'ลบผู้ใช้งานสำเร็จ');
    }
}
