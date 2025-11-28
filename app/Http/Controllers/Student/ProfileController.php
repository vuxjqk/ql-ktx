<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('student.profile.edit');
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'student_code' => 'required|string|max:20|unique:students,student_code,' . ($user->student?->id ?? 'NULL'),
            'class' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

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

        return redirect()->back()->with('success', __('Đã cập nhật thành công'));
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()->with('success', __('Đã cập nhật mật khẩu thành công'));
    }

    public function updateAvatar(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $validated = $request->validate([
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()->back()->with('success', __('Đã cập nhật ảnh đại diện thành công'));
    }

    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        try {
            $user->delete();

            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('student.home')->with('success', __('Tài khoản đã được xoá thành công'));
        } catch (QueryException $e) {
            $msg = $e->getCode() === '23000'
                ? __('Không thể xóa vì có dữ liệu liên quan')
                : __('Đã xảy ra lỗi khi xoá');

            return redirect()->back()->with('error', $msg);
        }
    }
}
