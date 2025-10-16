<x-app-layout>
    <x-slot name="header">
        {{ __('Thông tin sinh viên') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý sinh viên', 'url' => route('students.index')],
                ['label' => __('Thông tin sinh viên')],
            ]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-user-graduate text-blue-600 me-1"></i>
                        {{ __('Thông tin sinh viên') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Xem chi tiết thông tin sinh viên') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <x-secondary-button :href="route('students.index')">
                        <i class="fas fa-arrow-left"></i>
                        {{ __('Quay lại') }}
                    </x-secondary-button>

                    <x-secondary-button :href="route('bills.index', $user)" class="!bg-blue-500 !text-white !hover:bg-blue-600">
                        <i class="fas fa-file-invoice-dollar"></i>
                        {{ __('Hoá đơn') }}
                    </x-secondary-button>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-info-circle text-blue-600 me-1"></i>
                    {{ __('Chi tiết sinh viên') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div class="flex items-center gap-4">
                        @if ($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                                class="w-16 h-16 rounded-full object-cover">
                        @else
                            <div
                                class="w-16 h-16 rounded-full bg-blue-600 text-white text-xl font-bold flex items-center justify-center">
                                {{ mb_substr($user->name, 0, 2, 'UTF-8') }}
                            </div>
                        @endif
                        <div>
                            <h4 class="font-semibold text-lg text-gray-800">{{ $user->name }}</h4>
                            <p class="text-sm text-gray-600">{{ __('Mã sinh viên') }}:
                                {{ $user->student->student_code ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label :value="__('Email')" icon="fas fa-envelope" />
                        <p class="mt-1 text-gray-800">{{ $user->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <x-input-label :value="__('Lớp')" icon="fas fa-chalkboard-teacher" />
                        <p class="mt-1 text-gray-800">{{ $user->student->class ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <x-input-label :value="__('Ngày sinh')" icon="fas fa-calendar-alt" />
                        <p class="mt-1 text-gray-800">
                            {{ $user->student->date_of_birth ? $user->student->date_of_birth->format('d/m/Y') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <x-input-label :value="__('Giới tính')" icon="fas fa-venus-mars" />
                        <p class="mt-1 text-gray-800">
                            {{ $user->student->gender === 'male' ? 'Nam' : ($user->student->gender === 'female' ? 'Nữ' : 'Khác') }}
                        </p>
                    </div>
                    <div>
                        <x-input-label :value="__('Số điện thoại')" icon="fas fa-phone" />
                        <p class="mt-1 text-gray-800">{{ $user->student->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <x-input-label :value="__('Địa chỉ')" icon="fas fa-map-marker-alt" />
                        <p class="mt-1 text-gray-800">{{ $user->student->address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
