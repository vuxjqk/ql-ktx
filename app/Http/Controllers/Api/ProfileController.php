<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function me(Request $request)
    {
        $user = $request->user()->load([
            'student',
            'branches',
            'activeBooking.room.floor.branch',
            'bills' => fn($q) => $q->latest()->take(5),
        ]);

        return response()->json($user);
    }

    public function update(Request $request)
    {
        $user = $request->user()->loadMissing('student');

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email:filter',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'avatar' => 'sometimes|nullable|string',
            'student_code' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
                Rule::unique('students', 'student_code')->ignore(optional($user->student)->id),
            ],
            'class' => 'sometimes|nullable|string|max:100',
            'date_of_birth' => 'sometimes|nullable|date',
            'gender' => 'sometimes|nullable|in:male,female',
            'phone' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string|max:255',
        ]);

        $studentData = Arr::only($data, [
            'student_code',
            'class',
            'date_of_birth',
            'gender',
            'phone',
            'address',
        ]);

        $studentDataWithoutCode = $studentData;
        unset($studentDataWithoutCode['student_code']);

        if (
            !$user->student
            && !empty($studentDataWithoutCode)
            && !array_key_exists('student_code', $studentData)
        ) {
            throw ValidationException::withMessages([
                'student_code' => __('Vui lòng cung cấp mã sinh viên để tạo hồ sơ học viên.'),
            ]);
        }

        DB::transaction(function () use ($user, $data, $studentData) {
            if (array_key_exists('name', $data)) {
                $user->name = $data['name'];
            }

            if (array_key_exists('email', $data)) {
                $user->email = strtolower($data['email']);
            }

            if (array_key_exists('avatar', $data)) {
                $user->avatar = $data['avatar'];
            }

            if ($user->isDirty()) {
                $user->save();
            }

            // Student info
            if (!empty($studentData)) {
                if ($user->student) {
                    $user->student->fill($studentData)->save();
                } elseif (array_key_exists('student_code', $studentData)) {
                    $user->student()->create($studentData);
                }
            }
        });

        return response()->json(
            $user->fresh()->load([
                'student',
                'branches',
                'activeBooking.room.floor.branch',
            ])
        );
    }

    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => __('Cập nhật ảnh đại diện thành công.'),
            'avatar_url' => asset('storage/' . $user->avatar),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Logout tất cả session khác
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => __('Đã cập nhật mật khẩu thành công. Vui lòng đăng nhập lại.'),
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        try {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->tokens()->delete();
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => __('Tài khoản đã được xóa thành công.'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa tài khoản thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }
}
