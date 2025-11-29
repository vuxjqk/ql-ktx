{{-- resources/views/auth/passwords/reset.blade.php --}}
@extends('student.layouts.app')

@section('title', 'Đặt lại mật khẩu')

@section('content')
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Logo & Title -->
            <div class="text-center mb-10">
                <div
                    class="mx-auto w-28 h-28 bg-white rounded-full shadow-xl flex items-center justify-center mb-6 border-4 border-blue-100">
                    <i class="fas fa-shield-alt text-6xl text-blue-600"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-3">Đặt lại mật khẩu</h1>
                <p class="text-lg text-gray-600">Nhập mật khẩu mới cho tài khoản của bạn</p>
            </div>

            <!-- Reset Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-8 sm:p-10">

                    @if (session('status'))
                        <div
                            class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-center font-medium">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                        @csrf

                        <!-- Hidden token & email -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email (hiển thị readonly) -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-envelope mr-2 text-blue-600"></i>Email
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                                required readonly class="input-focus bg-gray-50 text-gray-700 cursor-not-allowed">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mật khẩu mới -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock mr-2 text-blue-600"></i>Mật khẩu mới
                            </label>
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                class="input-focus" placeholder="Tối thiểu 8 ký tự">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Xác nhận mật khẩu -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock mr-2 text-blue-600"></i>Nhập lại mật khẩu
                            </label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                autocomplete="new-password" class="input-focus" placeholder="Nhập lại mật khẩu mới">
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-4 rounded-xl font-bold text-lg hover:from-blue-700 hover:to-indigo-800 transition shadow-lg">
                            <i class="fas fa-check mr-2"></i>
                            Cập nhật mật khẩu
                        </button>
                    </form>

                    <!-- Back to Login -->
                    <div class="text-center mt-8 pt-6 border-t border-gray-200">
                        <p class="text-gray-600">
                            Đã có mật khẩu mới?
                            <a href="{{ route('login') }}"
                                class="font-bold text-blue-600 hover:text-blue-800 transition underline underline-offset-2">
                                Đăng nhập ngay
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer Text -->
            <div class="text-center mt-10 text-gray-500 text-sm">
                <p>© 2025 Hệ thống Quản lý Ký túc xá Sinh viên HUIT</p>
                <p class="mt-1">Phát triển bởi Trần Anh Vũ • Vũ Đình Ân • Trần Huỳnh Đức Anh</p>
            </div>
        </div>
    </div>
@endsection
