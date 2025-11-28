<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with(['student', 'activeBooking'])
            ->where('role', 'student')
            ->filter($request->all())
            ->paginate(10)
            ->appends($request->query());

        $totalStudents = User::where('role', 'student')->count();

        return view('students.index', compact('users', 'totalStudents'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => 'nullable|string|min:8',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'student_code' => 'required|string|max:20|unique:students,student_code',
            'class' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        if (empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['email']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $validated['role'] = 'student';

        $user = User::create($validated);
        $user->student()->create($validated);

        return redirect()->route('students.index')->with('success', __('Đã tạo thành công'));
    }

    public function show(User $user)
    {
        $user->load('student');
        return view('students.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load('student');
        return view('students.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role !== 'student') {
            abort(403, __('Unauthorized'));
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'student_code' => 'required|string|max:20|unique:students,student_code,' . ($user->student?->id ?? 'NULL'),
            'class' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

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

        $user->update($validated);

        $studentData = [
            'student_code' => $validated['student_code'],
            'class' => $validated['class'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ];

        if ($user->student) {
            $user->student->update($studentData);
        } else {
            $user->student()->create($studentData);
        }

        return redirect()->route('students.index')->with('success', __('Đã cập nhật thành công'));
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'student') {
            abort(403, __('Unauthorized'));
        }

        try {
            $avatar = $user->avatar;

            $user->delete();

            if ($avatar) {
                Storage::disk('public')->delete($avatar);
            }

            return redirect()->route('students.index')->with('success', __('Đã xoá thành công'));
        } catch (QueryException $e) {
            $msg = $e->getCode() === '23000'
                ? __('Không thể xóa vì có dữ liệu liên quan')
                : __('Đã xảy ra lỗi khi xoá');

            return redirect()->back()->with('error', $msg);
        }
    }
}
