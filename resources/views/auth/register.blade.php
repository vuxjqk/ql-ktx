{{-- resources/views/auth/register.blade.php --}}
@extends('student.layouts.app')

@section('title', 'Đăng ký tài khoản')

@section('content')
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Logo & Title -->
            <div class="text-center mb-10">
                <div
                    class="mx-auto w-28 h-28 bg-white rounded-full shadow-xl flex items-center justify-center mb-6 border-4 border-blue-100">
                    <i class="fas fa-user-plus text-6xl text-blue-600"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-3">Tạo tài khoản mới</h1>
                <p class="text-lg text-gray-600">Đăng ký để bắt đầu sử dụng hệ thống KTX HUIT</p>
            </div>

            <!-- Register Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-8 sm:p-10">
                    <form method="POST" action="{{ route('register') }}" class="space-y-6">
                        @csrf

                        <!-- Họ và tên -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user mr-2 text-blue-600"></i>Họ và tên
                            </label>
                            <input id="name" name="name" type="text" autocomplete="name" required
                                value="{{ old('name') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Nguyễn Văn A">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-envelope mr-2 text-blue-600"></i>Email sinh viên
                            </label>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                value="{{ old('email') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="mssv@huit.edu.vn">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mật khẩu -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock mr-2 text-blue-600"></i>Mật khẩu
                            </label>
                            <input id="password" name="password" type="password" autocomplete="new-password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Tối thiểu 8 ký tự">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Xác nhận mật khẩu -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock mr-2 text-blue-600"></i>Nhập lại mật khẩu
                            </label>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                autocomplete="new-password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Nhập lại mật khẩu">
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-4 rounded-xl font-bold text-lg hover:from-blue-700 hover:to-indigo-800 transition shadow-lg">
                            <i class="fas fa-user-plus mr-2"></i>
                            Đăng ký tài khoản
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500 font-medium">Hoặc đăng nhập bằng</span>
                        </div>
                    </div>

                    <!-- Social Register (có thể dùng để login luôn) -->
                    <div class="grid grid-cols-3 gap-4">
                        <a href="{{ route('auth.redirect', 'google') }}"
                            class="social-btn border-gray-300 hover:border-red-400 hover:bg-red-50 text-gray-700">
                            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="h-6 w-6">
                            <span class="hidden sm:inline">Google</span>
                        </a>

                        <a href="{{ route('auth.redirect', 'facebook') }}"
                            class="social-btn border-gray-300 hover:border-blue-600 hover:bg-blue-50 text-gray-700">
                            <i class="fab fa-facebook-f text-xl text-blue-600"></i>
                            <span class="hidden sm:inline">Facebook</span>
                        </a>

                        <a href="{{ route('auth.redirect', 'github') }}"
                            class="social-btn border-gray-300 hover:border-black hover:bg-gray-100 text-gray-700">
                            <i class="fab fa-github text-xl"></i>
                            <span class="hidden sm:inline">GitHub</span>
                        </a>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center mt-8 pt-6 border-t border-gray-200">
                        <p class="text-gray-600">
                            Đã có tài khoản?
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
