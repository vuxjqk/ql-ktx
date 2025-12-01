{{-- resources/views/student/contact.blade.php --}}
@extends('student.layouts.app')

@section('title', 'Liên hệ')

@pushOnce('styles')
    <style>
        .contact-card {
            transition: all 0.3s ease;
        }

        .contact-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }
    </style>
@endPushOnce

@section('content')
    <!-- Hero -->
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl font-bold text-white mb-4">Liên hệ với chúng tôi</h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                Bạn cần hỗ trợ về hệ thống, báo lỗi góp ý tính năng
                <br>hay chỉ đơn giản là muốn gửi lời chào?
                <br>Nhóm chúng tôi luôn sẵn sàng lắng nghe!
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

            <!-- Form liên hệ -->
            <div>
                <div class="bg-white rounded-2xl shadow-xl p-8 lg:p-10">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Gửi tin nhắn trực tiếp</h2>

                    @if (session('success'))
                        <div
                            class="mb-6 p-5 bg-green-50 border border-green-200 text-green-800 rounded-xl text-center font-medium">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('student.contact.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Họ tên -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user mr-2 text-blue-600"></i>Họ và tên
                            </label>
                            <input type="text" name="name" id="name" required
                                value="{{ old('name', auth()->user()?->name) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg input-focus transition"
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
                            <input type="email" name="email" id="email" required
                                value="{{ old('email', auth()->user()?->email) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg input-focus transition"
                                placeholder="mssv@huit.edu.vn">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Chủ đề -->
                        <div>
                            <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-tag mr-2 text-blue-600"></i>Chủ đề
                            </label>
                            <select name="subject" id="subject" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg input-focus transition">
                                <option value="">-- Chọn chủ đề --</option>
                                <option value="Hỗ trợ kỹ thuật">Hỗ trợ kỹ thuật</option>
                                <option value="Góp ý tính năng">Góp ý tính năng</option>
                                <option value="Báo lỗi hệ thống">Báo lỗi hệ thống</option>
                                <option value="Thanh toán/Hóa đơn">Thanh toán/Hóa đơn</option>
                                <option value="Phòng ở & Đặt phòng">Phòng ở & Đặt phòng</option>
                                <option value="Khác">Khác</option>
                            </select>
                            @error('subject')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nội dung -->
                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-comment-dots mr-2 text-blue-600"></i>Nội dung tin nhắn
                            </label>
                            <textarea name="message" id="message" rows="6" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg input-focus transition resize-none"
                                placeholder="Mô tả chi tiết vấn đề hoặc ý kiến của bạn...">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nút gửi -->
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-4 rounded-xl font-bold text-lg hover:from-blue-700 hover:to-indigo-800 transition shadow-lg flex items-center justify-center">
                            <i class="fas fa-paper-plane mr-3"></i>
                            Gửi tin nhắn
                        </button>
                    </form>
                </div>
            </div>

            <!-- Thông tin liên hệ + thành viên nhóm -->
            <div class="space-y-8">
                <!-- Thông tin chung -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Thông tin liên hệ nhanh</h3>
                    <div class="space-y-5 text-gray-700">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-envelope text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold">Email nhóm phát triển</p>
                                <p class="text-blue-600">info@huit.edu.vn</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-facebook text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold">Fanpage hỗ trợ</p>
                                <a href="https://facebook.com/ktxhuit" target="_blank"
                                    class="text-blue-600 hover:underline">fb.com/ktxhuit</a>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-phone text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold">Hotline (24/7)</p>
                                <p class="text-blue-600">1800-2025 (miễn phí)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thành viên hỗ trợ -->
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Hoặc liên hệ trực tiếp</h3>
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Trần Anh Vũ -->
                        <div class="contact-card bg-white rounded-xl shadow-lg p-6 flex items-center space-x-5">
                            <img src="{{ asset('storage/logo/huit.jpg') }}" alt="Trần Anh Vũ"
                                class="w-20 h-20 rounded-full object-cover border-4 border-blue-100">
                            <div>
                                <h4 class="font-bold text-lg">Trần Anh Vũ</h4>
                                <p class="text-gray-600 text-sm">Nhóm trưởng • Full-stack</p>
                                <div class="flex space-x-3 mt-2">
                                    <a href="https://fb.com/trananhvu.it" class="text-blue-600 hover:text-blue-800"><i
                                            class="fab fa-facebook"></i></a>
                                    <a href="mailto:info@huit.edu.vn" class="text-gray-600 hover:text-gray-800"><i
                                            class="fas fa-envelope"></i></a>
                                </div>
                            </div>
                        </div>

                        <!-- Vũ Đình Ân -->
                        <div class="contact-card bg-white rounded-xl shadow-lg p-6 flex items-center space-x-5">
                            <img src="{{ asset('storage/logo/huit.jpg') }}" alt="Vũ Đình Ân"
                                class="w-20 h-20 rounded-full object-cover border-4 border-purple-100">
                            <div>
                                <h4 class="font-bold text-lg">Vũ Đình Ân</h4>
                                <p class="text-gray-600 text-sm">Frontend • UI/UX</p>
                                <div class="flex space-x-3 mt-2">
                                    <a href="https://fb.com/vudinhan" class="text-blue-600 hover:text-blue-800"><i
                                            class="fab fa-facebook"></i></a>
                                </div>
                            </div>
                        </div>

                        <!-- Trần Huỳnh Đức Anh -->
                        <div class="contact-card bg-white rounded-xl shadow-lg p-6 flex items-center space-x-5">
                            <img src="{{ asset('storage/logo/huit.jpg') }}" alt="Trần Huỳnh Đức Anh"
                                class="w-20 h-20 rounded-full object-cover border-4 border-purple-100">
                            <div>
                                <h4 class="font-bold text-lg">Trần Huỳnh Đức Anh</h4>
                                <p class="text-gray-600 text-sm">Backend • Database</p>
                                <div class="flex space-x-3 mt-2">
                                    <a href="https://fb.com/tranhuynhducanh" class="text-blue-600 hover:text-blue-800"><i
                                            class="fab fa-facebook"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
