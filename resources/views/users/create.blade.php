<x-app-layout>
    <x-slot name="header">
        {{ __('Thêm nhân viên mới') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý nhân viên', 'url' => route('users.index')],
                ['label' => 'Thêm nhân viên mới'],
            ]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-user-tie text-blue-600 me-1"></i>
                        {{ __('Thêm nhân viên mới') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Thêm nhân viên mới vào hệ thống') }}</p>
                </div>
                <x-secondary-button :href="route('users.index')">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Quay lại') }}
                </x-secondary-button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-info-circle text-blue-600 me-1"></i>
                    {{ __('Thông tin nhân viên') }}
                </h3>

                <form action="{{ route('users.store') }}" method="post" enctype="multipart/form-data"
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Họ tên')" icon="fas fa-user" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                            :value="old('name')" required autofocus autocomplete="name" :placeholder="__('Nhập họ tên')" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email')" icon="fas fa-envelope" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="old('email')" required autocomplete="email" :placeholder="__('Nhập địa chỉ email')" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Mật khẩu')" icon="fas fa-lock" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                            autocomplete="new-password" :placeholder="__('Nếu để trống thì mật khẩu mặc định là email')" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="avatar" :value="__('Ảnh đại diện')" icon="fas fa-image" />
                        <x-file-input id="avatar" class="block mt-1 w-full" name="avatar" />
                        <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="role" :value="__('Vai trò')" icon="fas fa-user-tag" />
                        <x-select id="role" class="block mt-1 w-full" :options="[
                            'admin' => 'Quản trị viên',
                            'staff' => 'Nhân viên',
                        ]" name="role"
                            :selected="old('role')" required :placeholder="__('Chọn vai trò')" />
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <div class="col-span-2">
                        <x-input-label for="branches" :value="__('Chi nhánh')" icon="fas fa-building" />
                        <div class="flex flex-wrap gap-6 mt-1">
                            @foreach ($branches as $id => $name)
                                <label for="branch_{{ $id }}" class="inline-flex items-center">
                                    <input id="branch_{{ $id }}" type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        name="branches[]" value="{{ $id }}" @checked(in_array($id, old('branches', [])))>
                                    <span class="ms-2 text-sm text-gray-600">{{ $name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('branches')" class="mt-2" />
                    </div>

                    <div class="col-span-2 flex items-center justify-end gap-6">
                        <x-secondary-button :href="route('users.index')">
                            <i class="fas fa-arrow-left"></i>
                            {{ __('Quay lại') }}
                        </x-secondary-button>

                        <x-primary-button>
                            <i class="fas fa-save"></i>
                            {{ __('Lưu') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
