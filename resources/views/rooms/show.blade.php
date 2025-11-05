<x-app-layout>
    <x-slot name="header">
        {{ __('Chi tiết phòng') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý phòng', 'url' => route('rooms.index')],
                ['label' => 'Chi tiết phòng'],
            ]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-door-open text-blue-600 me-1"></i>
                        {{ __('Chi tiết phòng') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Xem thông tin chi tiết về phòng') }}</p>
                </div>
                <x-secondary-button :href="route('rooms.index')">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Quay lại') }}
                </x-secondary-button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-info-circle text-blue-600 me-1"></i>
                    {{ __('Thông tin phòng') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <x-input-label :value="__('Mã phòng')" icon="fas fa-tag" />
                        <p class="mt-1 text-gray-800">{{ $room->room_code }}</p>
                    </div>
                    <div>
                        <x-input-label :value="__('Chi nhánh')" icon="fas fa-building" />
                        <p class="mt-1 text-gray-800">{{ $room->floor->branch->name }}</p>
                    </div>
                    <div>
                        <x-input-label :value="__('Tầng')" icon="fas fa-layer-group" />
                        <p class="mt-1 text-gray-800">{{ 'Tầng ' . $room->floor->floor_number }}</p>
                    </div>
                    <div>
                        <x-input-label :value="__('Loại tầng')" icon="fas fa-venus-mars" />
                        <p class="mt-1 text-gray-800">
                            {{ $room->floor->gender_type === 'male' ? 'Nam' : ($room->floor->gender_type === 'female' ? 'Nữ' : 'Hỗn hợp') }}
                        </p>
                    </div>
                    <div>
                        <x-input-label :value="__('Giá theo ngày')" icon="fas fa-money-bill" />
                        <p class="mt-1 text-gray-800">{{ number_format($room->price_per_day, 0, ',', '.') }} VND</p>
                    </div>
                    <div>
                        <x-input-label :value="__('Giá theo tháng')" icon="fas fa-money-bill" />
                        <p class="mt-1 text-gray-800">{{ number_format($room->price_per_month, 0, ',', '.') }} VND</p>
                    </div>
                    <div>
                        <x-input-label :value="__('Sức chứa')" icon="fas fa-users" />
                        <p class="mt-1 text-gray-800">{{ $room->capacity }}</p>
                    </div>
                    <div>
                        <x-input-label :value="__('Số người hiện tại')" icon="fas fa-user-check" />
                        <p class="mt-1 text-gray-800">{{ $room->current_occupancy }}</p>
                    </div>
                    <div>
                        <x-input-label :value="__('Trạng thái')" icon="fas fa-toggle-on" />
                        <p class="mt-1 text-gray-800">
                            @if ($room->is_active)
                                <span class="text-green-600">{{ __('Hoạt động') }}</span>
                            @else
                                <span class="text-red-600">{{ __('Không hoạt động') }}</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <x-input-label :value="__('Mô tả')" icon="fas fa-info" />
                        <p class="mt-1 text-gray-800">{{ $room->description ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <x-input-label :value="__('Lượt yêu thích')" icon="fas fa-heart" />
                        <p class="mt-1 text-red-600">
                            {{ $room->favourites_count }}
                            <i class="fas fa-heart"></i>
                        </p>
                    </div>
                    <div>
                        <x-input-label :value="__('Đánh giá trung bình')" icon="fas fa-star" />
                        <p class="mt-1 text-gray-800 flex items-center gap-1">
                            @if ($room->reviews_avg_rating)
                                @php
                                    $rating = round($room->reviews_avg_rating * 2) / 2;
                                @endphp

                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $rating)
                                        <i class="fas fa-star text-yellow-400"></i>
                                    @elseif ($i - 0.5 == $rating)
                                        <i class="fas fa-star-half-alt text-yellow-400"></i>
                                    @else
                                        <i class="far fa-star text-gray-400"></i>
                                    @endif
                                @endfor

                                <span class="ms-1 text-sm text-gray-600">({{ number_format($rating, 1) }})</span>
                            @else
                                <span class="text-gray-500">{{ __('Chưa có đánh giá') }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-span-2">
                        <x-input-label :value="__('Dịch vụ')" icon="fas fa-concierge-bell" />
                        <div class="flex flex-wrap gap-6 mt-1">
                            @forelse ($room->services as $service)
                                <p class="text-gray-800">{{ $service->name }}</p>
                            @empty
                                <p class="text-gray-400 italic">{{ __('Không có dịch vụ nào') }}</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="col-span-2">
                        <x-input-label :value="__('Tiện ích')" icon="fas fa-water" />
                        <div class="flex flex-wrap gap-6 mt-1">
                            @forelse ($room->amenities as $amenity)
                                <p class="text-gray-800">{{ $amenity->name }}</p>
                            @empty
                                <p class="text-gray-400 italic">{{ __('Không có tiện ích nào') }}</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="col-span-2">
                        <x-input-label :value="__('Hình ảnh phòng')" icon="fas fa-image" />
                        <div class="flex flex-wrap gap-6 mt-1">
                            @forelse ($room->images as $index => $image)
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                    alt="Image {{ $index + 1 }}" class="w-32 h-32 object-cover">
                            @empty
                                <p class="text-gray-400 italic">{{ __('Không có hình ảnh nào') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <x-table :title="__('Danh sách đang cư trú')">
                    <x-thead>
                        <x-tr>
                            <x-th>{{ __('STT') }}</x-th>
                            <x-th>{{ __('Người đang cư trú') }}</x-th>
                            <x-th>{{ __('Hình thức') }}</x-th>
                            <x-th>{{ __('Thời gian cư trú') }}</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($room->activeBookings as $index => $booking)
                            <x-tr>
                                <x-td>#{{ $index + 1 }}</x-td>
                                <x-td>
                                    <div class="flex items-center gap-2">
                                        @if ($booking->user->avatar)
                                            <img src="{{ asset('storage/' . $booking->user->avatar) }}" alt="Avatar"
                                                class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div
                                                class="w-8 h-8 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">
                                                {{ mb_substr($booking->user->name, 0, 2, 'UTF-8') }}
                                            </div>
                                        @endif
                                        <div class="grid">
                                            <span class="font-semibold">{{ $booking->user->name }}</span>
                                            <span class="text-sm">
                                                {{ __('MSSV: ') . $booking->user->student->student_code }}
                                            </span>
                                            <span class="text-sm">
                                                {{ __('Giới tính: ') . ($booking->user->student->gender === 'male' ? 'Nam' : ($booking->user->student->gender === 'female' ? 'Nữ' : 'Khác')) }}
                                            </span>
                                        </div>
                                    </div>
                                </x-td>
                                <x-td>
                                    <div class="grid">
                                        <span>{{ __('Hình thức đăng ký: ') . ($booking->booking_type === 'registration' ? 'Đăng ký mới' : ($booking->booking_type === 'transfer' ? 'Chuyển phòng' : 'Gia hạn')) }}</span>
                                        <span>{{ __('Hình thức thuê: ') . ($booking->rental_type === 'daily' ? 'Theo ngày' : 'Theo tháng') }}</span>
                                    </div>
                                </x-td>
                                <x-td>
                                    {{ $booking->check_in_date->format('d/m/Y') }} -
                                    {{ $booking->expected_check_out_date->format('d/m/Y') }}
                                </x-td>
                            </x-tr>
                        @endforeach
                    </x-tbody>
                </x-table>
            </div>
        </div>
    </div>
</x-app-layout>
