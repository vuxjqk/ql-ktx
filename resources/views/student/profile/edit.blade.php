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
                    <form action="{{ route('student.avatar.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div x-data="avatarPreview()" class="relative inline-block">

                            <!-- Preview -->
                            <template x-if="preview">
                                <img :src="preview"
                                    class="w-40 h-40 rounded-full object-cover mx-auto border-4 border-blue-100 shadow-xl">
                            </template>

                            <template x-if="!preview">
                                @if (auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                        class="w-40 h-40 rounded-full object-cover mx-auto border-4 border-blue-100 shadow-xl">
                                @else
                                    <div
                                        class="h-40 w-40 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 
                                            flex items-center justify-center text-white font-bold text-5xl mx-auto border-4 border-blue-100 shadow-xl">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </template>

                            <!-- Chọn ảnh -->
                            <label for="avatar-upload"
                                class="absolute bottom-2 right-2 bg-blue-600 text-white p-3 rounded-full cursor-pointer hover:bg-blue-700 transition-all shadow-lg">
                                <i class="fas fa-camera"></i>
                            </label>

                            <input type="file" id="avatar-upload" name="avatar" accept="image/*"
                                @change="showPreview; $refs.submitBtn.classList.remove('hidden')" class="hidden">

                            <!-- Nút submit icon -->
                            <button type="submit" x-ref="submitBtn"
                                class="hidden absolute top-2 right-2 bg-green-600 text-white p-3 rounded-full hover:bg-green-700 transition-all shadow-lg">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </form>

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
                                @error('student_code')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
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
                                @error('date_of_birth')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
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
                                @error('gender')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-university mr-1"></i> Lớp
                                </label>
                                <input type="text" name="class"
                                    value="{{ old('class', auth()->user()->student?->class) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                @error('class')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-map-marker-alt mr-1"></i> Địa chỉ thường trú
                                </label>
                                <textarea name="address" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('address', auth()->user()->student?->address) }}</textarea>
                                @error('address')
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
                        @method('PATCH')

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                                @error('current_password', 'updatePassword')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Mật khẩu mới</label>
                                <input type="password" name="password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                                @error('password', 'updatePassword')
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

                    <button type="button" x-data=""
                        x-on:click="$dispatch('open-modal', 'confirm-delete-account')"
                        class="px-8 py-4 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-all shadow-lg hover:shadow-xl flex items-center">
                        <i class="fas fa-trash-alt mr-2"></i>
                        Xóa tài khoản vĩnh viễn
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal xác nhận xóa tài khoản – dùng Alpine.js (x-modal) --}}
    <x-modal name="confirm-delete-account" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="p-8 text-center">
            <!-- Icon cảnh báo -->
            <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-exclamation-triangle text-red-600 text-5xl"></i>
            </div>

            <!-- Tiêu đề & mô tả -->
            <h3 class="text-2xl font-bold text-gray-900 mb-4">
                Xác nhận xóa tài khoản?
            </h3>
            <p class="text-gray-600 mb-8 leading-relaxed">
                Bạn có chắc chắn muốn xóa tài khoản này?<br>
                <strong class="text-red-600">Hành động này không thể hoàn tác!</strong><br>
                Tất cả dữ liệu sẽ bị xóa vĩnh viễn.
            </p>

            <!-- Form xác nhận bằng mật khẩu -->
            <form action="{{ route('student.profile.destroy') }}" method="POST" class="space-y-6">
                @csrf
                @method('DELETE')

                <div>
                    <input type="password" name="password" required autocomplete="current-password"
                        placeholder="Nhập mật khẩu để xác nhận xóa"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-center text-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    @error('password', 'userDeletion')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nút hành động -->
                <div class="flex gap-4 justify-center">
                    <button type="button" x-on:click="$dispatch('close')"
                        class="px-6 py-3 bg-gray-300 text-gray-800 font-medium rounded-lg hover:bg-gray-400 transition">
                        Hủy bỏ
                    </button>

                    <button type="submit"
                        class="px-6 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition shadow-lg">
                        Có, xóa tài khoản
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
@endsection


@pushOnce('scripts')
    <script>
        function avatarPreview() {
            return {
                preview: null,
                showPreview(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    this.preview = URL.createObjectURL(file);
                }
            }
        }
    </script>
@endPushOnce
