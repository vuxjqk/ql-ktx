@extends('student.layouts.app')

@section('title', 'Thông tin cá nhân')

@section('content')
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                <i class="fas fa-user-circle mr-3"></i>
                Thông tin cá nhân
            </h1>
            <p class="text-xl text-blue-100">
                Cập nhật hồ sơ, ảnh đại diện và bảo mật tài khoản của bạn
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar: Avatar + Info Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-8 text-center sticky top-24">
                    <div class="relative inline-block">
                        <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('images/default-avatar.png') }}"
                            alt="{{ auth()->user()->name }}"
                            class="w-40 h-40 rounded-full object-cover mx-auto border-4 border-blue-100 shadow-xl">

                        <label for="avatar-upload"
                            class="absolute bottom-2 right-2 bg-blue-600 text-white p-3 rounded-full cursor-pointer hover:bg-blue-700 transition-all shadow-lg">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="avatar-upload" class="hidden" accept="image/*">
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 mt-6">{{ auth()->user()->name }}</h3>
                    <p class="text-gray-600">{{ auth()->user()->student?->student_code ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ auth()->user()->email ?? 'Chưa có email' }}</p>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="space-y-3 text-left">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-phone text-blue-600 w-8"></i>
                                <span>{{ auth()->user()?->student?->phone ?? 'Chưa cập nhật' }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-calendar-alt text-green-600 w-8"></i>
                                <span>{{ auth()->user()->student?->date_of_birth?->format('d/m/Y') ?? 'Chưa cập nhật' }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-home text-purple-600 w-8"></i>
                                <span>{{ auth()->user()->student?->class ?? 'Chưa cập nhật lớp' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Forms -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Form 1: Cập nhật hồ sơ cá nhân -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-user-edit text-blue-600 text-2xl mr-3"></i>
                        <h2 class="text-2xl font-bold text-gray-900">Cập nhật hồ sơ</h2>
                    </div>

                    <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user mr-1"></i> Họ và tên
                                </label>
                                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-id-card mr-1"></i> Mã sinh viên
                                </label>
                                <input type="text" name="student_code"
                                    value="{{ auth()->user()->student?->student_code ?? '' }}"
                                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-envelope mr-1"></i> Email
                                </label>
                                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-phone mr-1"></i> Số điện thoại
                                </label>
                                <input type="text" name="phone"
                                    value="{{ old('phone', auth()->user()?->student?->phone) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                @error('phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-birthday-cake mr-1"></i> Ngày sinh
                                </label>
                                <input type="date" name="date_of_birth"
                                    value="{{ old('date_of_birth', auth()->user()->student?->date_of_birth?->format('Y-m-d')) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-venus-mars mr-1"></i> Giới tính
                                </label>
                                <select name="gender"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Chọn giới tính --</option>
                                    <option value="male"
                                        {{ (auth()->user()->student?->gender ?? old('gender')) === 'male' ? 'selected' : '' }}>
                                        Nam</option>
                                    <option value="female"
                                        {{ (auth()->user()->student?->gender ?? old('gender')) === 'female' ? 'selected' : '' }}>
                                        Nữ</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-university mr-1"></i> Lớp
                                </label>
                                <input type="text" name="class"
                                    value="{{ old('class', auth()->user()->student?->class) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-map-marker-alt mr-1"></i> Địa chỉ thường trú
                                </label>
                                <textarea name="address" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('address', auth()->user()->student?->address) }}</textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-image mr-1"></i> Ảnh đại diện mới
                                </label>
                                <input type="file" name="avatar" accept="image/*"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                @error('avatar')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit"
                                class="px-8 py-4 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-all shadow-lg hover:shadow-xl flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Form 2: Đổi mật khẩu -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-key text-yellow-600 text-2xl mr-3"></i>
                        <h2 class="text-2xl font-bold text-gray-900">Đổi mật khẩu</h2>
                    </div>

                    <form action="{{ route('student.password.update') }}" method="POST">
                        @csrf

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                                @error('current_password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Mật khẩu mới</label>
                                <input type="password" name="password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Xác nhận mật khẩu mới</label>
                                <input type="password" name="password_confirmation" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit"
                                class="px-8 py-4 bg-yellow-600 text-white font-bold rounded-lg hover:bg-yellow-700 transition-all shadow-lg hover:shadow-xl flex items-center">
                                <i class="fas fa-shield-alt mr-2"></i>
                                Cập nhật mật khẩu
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Form 3: Xóa tài khoản (nguy hiểm) -->
                <div class="bg-white rounded-xl shadow-lg p-8 border-l-4 border-red-500">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl mr-3"></i>
                        <h2 class="text-2xl font-bold text-gray-900">Xóa tài khoản</h2>
                    </div>

                    <div class="text-gray-600 mb-6">
                        <p>Khi xóa tài khoản, toàn bộ dữ liệu bao gồm lịch sử đặt phòng, yêu thích sẽ <strong>mất vĩnh
                                viễn</strong>.</p>
                        <p class="mt-2 text-sm">Hành động này <strong>không thể hoàn tác</strong>.</p>
                    </div>

                    <button type="button" onclick="confirmDelete()"
                        class="px-8 py-4 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-all shadow-lg hover:shadow-xl flex items-center">
                        <i class="fas fa-trash-alt mr-2"></i>
                        Xóa tài khoản vĩnh viễn
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal xác nhận xóa tài khoản -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-6xl mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Xác nhận xóa tài khoản?</h3>
                <p class="text-gray-600 mb-8">Bạn có chắc chắn muốn xóa tài khoản này? Hành động này không thể hoàn tác!
                </p>

                <form action="{{ route('student.profile.destroy') }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <div class="space-y-4">
                        <input type="password" name="password" placeholder="Nhập mật khẩu để xác nhận" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg text-center">
                        <div class="flex gap-4 justify-center">
                            <button type="button" onclick="closeModal()"
                                class="px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
                                Hủy bỏ
                            </button>
                            <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Có, xóa tài khoản
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
@endpush
