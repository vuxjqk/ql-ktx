<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('student.profile.edit');
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        $request->validate([
            'student_code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'class' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Cập nhật user
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Cập nhật avatar
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::delete('public/' . $user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }

        // Cập nhật student
        $student = Student::updateOrCreate(
            ['id' => $request->id],
            array_merge(
                $request->only(['phone', 'date_of_birth', 'gender', 'class', 'address']),
                [
                    'user_id' => auth()->id(),
                    'student_code' => $request->student_code
                ]
            )
        );
        return back();
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|confirmed|min:8',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back();
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();
        $user->delete();

        return redirect('/')->with('status', 'Tài khoản đã được xóa vĩnh viễn.');
    }
}
