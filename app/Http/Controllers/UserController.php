<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $authRole = Auth::user()->role;

        $query = User::filter($request->all());

        if ($authRole === 'super_admin') {
            $query->whereIn('role', ['admin', 'staff']);
            $totalAdmins = User::where('role', 'admin')->count();
        } else {
            $query->where('role', 'staff');
            $totalAdmins = null;
        }

        $users = $query->paginate(10)->appends($request->query());

        $totalStaffs = User::where('role', 'staff')->count();

        return view('users.index', compact('users', 'totalAdmins', 'totalStaffs'));
    }

    public function create()
    {
        $branches = Branch::pluck('name', 'id')->toArray();
        return view('users.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $authRole = Auth::user()->role;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => 'nullable|string|min:8',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'branches' => 'array',
            'branches.*' => 'exists:branches,id',
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

        $user = User::create($validated);

        $user->branches()->sync($validated['branches'] ?? []);

        return redirect()->route('users.index')->with('success', __('Đã tạo thành công'));
    }

    public function edit(User $user)
    {
        $user->load('branches');
        $branches = Branch::pluck('name', 'id')->toArray();
        return view('users.edit', compact('user', 'branches'));
    }

    public function update(Request $request, User $user)
    {
        $authRole = Auth::user()->role;

        if ($user->role === 'super_admin' || ($user->role === 'admin' && $authRole === 'admin')) {
            abort(403, __('Unauthorized'));
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
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

        return redirect()->route('users.index')->with('success', __('Đã cập nhật thành công'));
    }

    public function destroy(User $user)
    {
        $authRole = Auth::user()->role;

        if ($user->role === 'super_admin' || ($user->role === 'admin' && $authRole === 'admin')) {
            abort(403, __('Unauthorized'));
        }

        try {
            $avatar = $user->avatar;

            $user->branches()->detach();
            $user->delete();

            if ($avatar) {
                Storage::disk('public')->delete($avatar);
            }

            return redirect()->route('users.index')->with('success', __('Đã xoá thành công'));
        } catch (QueryException $e) {
            $msg = $e->getCode() === '23000'
                ? __('Không thể xóa vì có dữ liệu liên quan')
                : __('Đã xảy ra lỗi khi xoá');

            return redirect()->back()->with('error', $msg);
        }
    }

    public function updateBranches(Request $request, User $user)
    {
        $authRole = Auth::user()->role;

        if ($user->role === 'super_admin' || ($user->role === 'admin' && $authRole === 'admin')) {
            abort(403, __('Unauthorized'));
        }

        $validated = $request->validate([
            'branches' => 'array',
            'branches.*' => 'exists:branches,id',
        ]);

        $user->branches()->sync($validated['branches'] ?? []);

        return redirect()->back()->with('success', __('Đã cập nhật thành công'));
    }
}
