<x-app-layout>
    <x-slot name="header">
        Chỉnh sửa nhân viên
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý nhân viên', 'url' => route('users.index')],
                ['label' => 'Chỉnh sửa nhân viên'],
            ]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-user-tie text-blue-800"></i>
                        Chỉnh sửa nhân viên
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Cập nhật thông tin nhân viên trong hệ thống</p>
                </div>
                <x-secondary-button :href="route('users.index')">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </x-secondary-button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-info-circle text-blue-800"></i>
                    Thông tin nhân viên
                </h3>

                <form action="{{ route('users.update', $user) }}" method="post" enctype="multipart/form-data"
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Tên" icon="fas fa-user" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                            :value="old('name', $user->name)" required autofocus autocomplete="name" placeholder="Nhập tên" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" icon="fas fa-envelope" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="old('email', $user->email)" required autocomplete="email" placeholder="Nhập email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" value="Mật khẩu mới (nếu muốn đổi)" icon="fas fa-lock" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                            autocomplete="password" placeholder="Để trống nếu không đổi mật khẩu" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="date_of_birth" value="Ngày sinh" icon="fas fa-calendar-alt" />
                        <x-text-input id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth"
                            :value="old('date_of_birth', $user->date_of_birth)" placeholder="Nhập ngày sinh" />
                        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="gender" value="Giới tính" icon="fas fa-venus-mars" />
                        <x-select id="gender" class="block mt-1 w-full" :options="[
                            'male' => 'Nam',
                            'female' => 'Nữ',
                        ]" name="gender"
                            :selected="old('gender', $user->gender)" placeholder="Chọn giới tính" />
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="phone" value="Số điện thoại" icon="fas fa-phone" />
                        <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone"
                            :value="old('phone', $user->phone)" autocomplete="phone" placeholder="Nhập số điện thoại" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="address" value="Địa chỉ" icon="fas fa-map-marker-alt" />
                        <x-text-input id="address" class="block mt-1 w-full" type="text" name="address"
                            :value="old('address', $user->address)" autocomplete="address" placeholder="Nhập địa chỉ" />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="flex-1">
                            <x-input-label for="avatar" value="Ảnh đại diện mới (nếu muốn đổi)" icon="fas fa-image" />
                            <x-file-input id="avatar" class="block mt-1 w-full" name="avatar"
                                accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml" />
                            <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                        </div>
                        <div>
                            @if ($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                                    class="w-16 h-16 rounded-full object-cover">
                            @else
                                <div
                                    class="w-16 h-16 rounded-full bg-blue-500 flex items-center justify-center font-bold text-2xl text-white">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    @can('is-super-admin')
                        <div>
                            <x-input-label for="role" value="Vai trò" icon="fas fa-user-tag" />
                            <x-select id="role" class="block mt-1 w-full" :options="[
                                'admin' => 'Quản trị viên',
                                'staff' => 'Nhân viên',
                            ]" name="role"
                                :selected="old('role', $user->role)" required placeholder="Chọn vai trò" />
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
                            Cập nhật
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
