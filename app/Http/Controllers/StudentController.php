<?php

namespace App\Http\Controllers;

use App\Models\RoomRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::select('id', 'name', 'email', 'phone', 'address', 'avatar', 'role', 'deleted_at')
            ->withTrashed()
            ->whereHas('student')
            ->with([
                'student:id,user_id,student_code,class',
                'roomRegistration:id,user_id,room_id,status,requested_at',
                'roomRegistration.room:id,room_code'
            ])
            ->filter($request->all());

        $users = $query->paginate(10)->appends($request->query());

        $totalStudents = User::withTrashed()->whereHas('student')->count();
        $statusCounts = RoomRegistration::select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('students.index', compact(
            'users',
            'totalStudents',
            'statusCounts'
        ));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|lowercase|email|max:255|unique:users,email',
            'password'      => 'nullable|string|min:8',
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|in:male,female',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:255',
            'avatar'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',

            'student_code'  => 'required|string|max:20|unique:students,student_code',
            'major'         => 'nullable|string|max:255',
            'class'         => 'nullable|string|max:255',
        ];

        $validated = $request->validate($rules);

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

        return redirect()->route('students.index')->with('success', 'Sinh viên đã được tạo thành công');
    }

    public function show(User $user)
    {
        $user->load(['student', 'roomRegistration.room.branch', 'roomAssignment.bills']);
        return view('students.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load(['student']);
        return view('students.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role !== 'student') {
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

            'student_code'  => 'required|string|max:20|unique:students,student_code,' . ($user->student->id ?? 'NULL'),
            'major'         => 'nullable|string|max:255',
            'class'         => 'nullable|string|max:255',
        ];

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

        $user->update($validated);
        $user->student->fill($validated)->save();

        return redirect()->route('students.index')->with('success', 'Sinh viên đã được cập nhật thành công');
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'student') {
            abort(403, 'Không Được Phép');
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('students.index')->with('success', 'Sinh viên đã được xoá thành công');
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        if ($user->role !== 'student') {
            abort(403, 'Không Được Phép');
        }

        $user->restore();

        return redirect()->route('students.index')->with('success', 'Sinh viên đã được khôi phục thành công');
    }
}
