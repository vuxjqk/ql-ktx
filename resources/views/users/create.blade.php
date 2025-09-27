<x-app-layout>
    <x-slot name="header">
        Thêm nhân sự mới
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý nhân sự', 'url' => route('users.index')],
                ['label' => 'Thêm nhân sự mới'],
            ]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-user-tie text-blue-600"></i>
                        Thêm nhân sự mới
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Thêm nhân sự mới vào hệ thống</p>
                </div>
                <x-secondary-button :href="route('users.index')">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </x-secondary-button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    Thông tin nhân sự
                </h3>

                <form action="{{ route('users.store') }}" method="post" enctype="multipart/form-data"
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Tên" icon="fas fa-user" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                            :value="old('name')" required autofocus autocomplete="name" placeholder="Nhập tên" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" icon="fas fa-envelope" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="old('email')" required autocomplete="email" placeholder="Nhập email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" value="Mật khẩu" icon="fas fa-lock" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                            autocomplete="password" placeholder="Nếu để trống thì mật khẩu mặc định là email" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="date_of_birth" value="Ngày sinh" icon="fas fa-calendar-alt" />
                        <x-text-input id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth"
                            :value="old('date_of_birth')" />
                        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="gender" value="Giới tính" icon="fas fa-venus-mars" />
                        <x-select id="gender" class="block mt-1 w-full" :options="[
                            'male' => 'Nam',
                            'female' => 'Nữ',
                        ]" name="gender"
                            :selected="old('gender')" placeholder="Chọn giới tính" />
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="phone" value="Số điện thoại" icon="fas fa-phone" />
                        <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone"
                            :value="old('phone')" autocomplete="phone" placeholder="Nhập số điện thoại" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="address" value="Địa chỉ" icon="fas fa-map-marker-alt" />
                        <x-text-input id="address" class="block mt-1 w-full" type="text" name="address"
                            :value="old('address')" autocomplete="address" placeholder="Nhập địa chỉ" />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="avatar" value="Ảnh đại diện" icon="fas fa-image" />
                        <x-file-input id="avatar" class="block mt-1 w-full" name="avatar"
                            accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml" />
                        <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                    </div>

                    @can('is-super-admin')
                        <div>
                            <x-input-label for="role" value="Vai trò" icon="fas fa-user-tag" />
                            <x-select id="role" class="block mt-1 w-full" :options="[
                                'admin' => 'Quản trị viên',
                                'staff' => 'Nhân viên',
                            ]" name="role"
                                :selected="old('role')" required placeholder="Chọn vai trò" />
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>
                    @endcan

                    <div class="col-span-2 flex items-center justify-end gap-6">
                        <x-secondary-button :href="route('users.index')">
                            <i class="fas fa-arrow-left"></i>
                            Quay lại
                        </x-secondary-button>

                        <x-primary-button>
                            <i class="fas fa-save"></i>
                            Lưu
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
