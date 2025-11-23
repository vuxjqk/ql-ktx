<x-app-layout>
    <x-slot name="header">
        {{ __('Chỉnh sửa sinh viên') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý sinh viên', 'url' => route('students.index')],
                ['label' => 'Chỉnh sửa sinh viên'],
            ]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-user-graduate text-blue-600 me-1"></i>
                        {{ __('Chỉnh sửa sinh viên') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Cập nhật thông tin sinh viên trong hệ thống') }}</p>
                </div>
                <x-secondary-button :href="route('students.index')">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Quay lại') }}
                </x-secondary-button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-info-circle text-blue-600 me-1"></i>
                    {{ __('Thông tin sinh viên') }}
                </h3>

                <form action="{{ route('students.update', $user) }}" method="post" enctype="multipart/form-data"
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    @csrf
                    @method('put')

                    <div>
                        <x-input-label for="name" :value="__('Họ tên')" icon="fas fa-user" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                            :value="old('name', $user->name)" required autofocus autocomplete="name" :placeholder="__('Nhập họ tên')" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email')" icon="fas fa-envelope" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="old('email', $user->email)" required autocomplete="email" :placeholder="__('Nhập địa chỉ email')" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Mật khẩu mới (nếu muốn đổi)')" icon="fas fa-lock" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                            autocomplete="new-password" :placeholder="__('Để trống nếu không đổi mật khẩu')" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="avatar" :value="__('Ảnh đại diện mới (nếu muốn đổi)')" icon="fas fa-image" />
                        <x-file-input id="avatar" class="block mt-1 w-full" name="avatar" />
                        @if ($user->avatar)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                                    class="w-16 h-16 rounded-full object-cover">
                            </div>
                        @endif
                        <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="student_code" :value="__('Mã sinh viên')" icon="fas fa-id-card" />
                        <x-text-input id="student_code" class="block mt-1 w-full" type="text" name="student_code"
                            :value="old('student_code', $user->student?->student_code)" required autocomplete="student_code" :placeholder="__('Nhập mã sinh viên')" />
                        <x-input-error :messages="$errors->get('student_code')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="class" :value="__('Lớp')" icon="fas fa-chalkboard-teacher" />
                        <x-text-input id="class" class="block mt-1 w-full" type="text" name="class"
                            :value="old('class', $user->student?->class)" autocomplete="class" :placeholder="__('Nhập lớp (nếu có)')" />
                        <x-input-error :messages="$errors->get('class')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="date_of_birth" :value="__('Ngày sinh')" icon="fas fa-calendar-alt" />
                        <x-text-input id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth"
                            :value="old(
                                'date_of_birth',
                                $user->student->date_of_birth ? $user->student->date_of_birth->format('Y-m-d') : '',
                            )" autocomplete="date_of_birth" />
                        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="gender" :value="__('Giới tính')" icon="fas fa-venus-mars" />
                        <x-select id="gender" class="block mt-1 w-full" :options="[
                            'male' => 'Nam',
                            'female' => 'Nữ',
                        ]" name="gender"
                            :selected="old('gender', $user->student->gender)" :placeholder="__('Chọn giới tính')" />
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="phone" :value="__('Số điện thoại')" icon="fas fa-phone" />
                        <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone"
                            :value="old('phone', $user->student->phone)" autocomplete="phone" :placeholder="__('Nhập số điện thoại (nếu có)')" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div class="col-span-2">
                        <x-input-label for="address" :value="__('Địa chỉ')" icon="fas fa-map-marker-alt" />
                        <x-textarea id="address" class="block mt-1 w-full" name="address" :value="old('address', $user->student->address)"
                            :placeholder="__('Nhập địa chỉ (nếu có)')" />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="col-span-2 flex items-center justify-end gap-6">
                        <x-secondary-button :href="route('students.index')">
                            <i class="fas fa-arrow-left"></i>
                            {{ __('Quay lại') }}
                        </x-secondary-button>

                        <x-primary-button>
                            <i class="fas fa-save"></i>
                            {{ __('Cập nhật') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
