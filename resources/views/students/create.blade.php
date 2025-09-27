<x-app-layout>
    <x-slot name="header">
        Thêm sinh viên mới
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý sinh viên', 'url' => route('students.index')],
                ['label' => 'Thêm sinh viên mới'],
            ]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-graduation-cap text-blue-600"></i>
                        Thêm sinh viên mới
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Thêm sinh viên mới vào hệ thống</p>
                </div>
                <x-secondary-button :href="route('students.index')">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </x-secondary-button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    Thông tin sinh viên
                </h3>

                <form action="{{ route('students.store') }}" method="post" enctype="multipart/form-data"
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    @csrf

                    <div>
                        <x-input-label for="student_code" value="MSSV" icon="fas fa-id-card" />
                        <x-text-input id="student_code" class="block mt-1 w-full" type="text" name="student_code"
                            :value="old('student_code')" required autofocus autocomplete="student_code" placeholder="Nhập MSSV" />
                        <x-input-error :messages="$errors->get('student_code')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="name" value="Tên" icon="fas fa-user" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                            :value="old('name')" required autocomplete="name" placeholder="Nhập tên" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="class" value="Lớp" icon="fas fa-chalkboard" />
                        <x-text-input id="class" class="block mt-1 w-full" type="text" name="class"
                            :value="old('class')" autocomplete="class" placeholder="Nhập lớp" />
                        <x-input-error :messages="$errors->get('class')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="cohort" value="Khóa" icon="fas fa-layer-group" />
                        <x-text-input id="cohort" class="block mt-1 w-full" type="number" name="cohort"
                            :value="old('cohort')" autocomplete="cohort" placeholder="Nhập khoá" />
                        <x-input-error :messages="$errors->get('cohort')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="major" value="Ngành" icon="fas fa-graduation-cap" />
                        <x-text-input id="major" class="block mt-1 w-full" type="text" name="major"
                            :value="old('major')" autocomplete="major" placeholder="Nhập ngành" />
                        <x-input-error :messages="$errors->get('major')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="graduated" value="Đã tốt nghiệp chưa?" icon="fas fa-user-graduate" />
                        <x-select id="graduated" class="block mt-1 w-full" :options="[
                            1 => 'Rồi',
                            0 => 'Chưa',
                        ]" name="graduated"
                            :selected="old('graduated')" />
                        <x-input-error :messages="$errors->get('graduated')" class="mt-2" />
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

                    <div class="col-span-2 flex items-center justify-end gap-6">
                        <x-secondary-button :href="route('students.index')">
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
