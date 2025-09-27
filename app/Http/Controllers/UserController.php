<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $authRole = Auth::user()->role;

        $query = User::withTrashed()
            ->filter($request->all());

        if ($authRole === 'super_admin') {
            $query->whereIn('role', ['admin', 'staff']);
            $totalAdmins = User::withTrashed()->where('role', 'admin')->count();
        } else {
            $query->where('role', 'staff');
            $totalAdmins = null;
        }

        $users = $query->paginate(10)->appends($request->query());

        $totalStaffs = User::withTrashed()->where('role', 'staff')->count();

        return view('users.index', compact('users', 'totalAdmins', 'totalStaffs'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $authRole = Auth::user()->role;

        $rules = [
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|lowercase|email|max:255|unique:users,email',
            'password'      => 'nullable|string|min:8',
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|in:male,female',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:255',
            'avatar'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ];

        if ($authRole === 'super_admin') {
            $rules['role'] = 'required|in:admin,staff';
        }

        $validated = $request->validate($rules);

        if (empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['email']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($authRole !== 'super_admin') {
            $validated['role'] = 'staff';
        }

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Nhân sự đã được tạo thành công');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $authRole = Auth::user()->role;

        if ($user->role === 'super_admin' || ($user->role === 'admin' && $authRole === 'admin')) {
            abort(403, 'Không Được Phép');
        }

        $rules = [
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'password'      => 'nullable|string|min:8',
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|in:male,female',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:255',
            'avatar'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ];

        if ($authRole === 'super_admin') {
            $rules['role'] = 'required|in:admin,staff';
        }

        $validated = $request->validate($rules);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($authRole !== 'super_admin') {
            unset($validated['role']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Nhân sự đã được cập nhật thành công');
    }

    public function destroy(User $user)
    {
        $authRole = Auth::user()->role;

        if ($user->role === 'super_admin' || ($user->role === 'admin' && $authRole === 'admin')) {
            abort(403, 'Không Được Phép');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Nhân sự đã được xoá thành công');
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        $authRole = Auth::user()->role;

        if ($user->role === 'super_admin' || ($user->role === 'admin' && $authRole === 'admin')) {
            abort(403, 'Không Được Phép');
        }

        $user->restore();

        return redirect()->route('users.index')->with('success', 'Nhân sự đã được khôi phục thành công');
    }
}
