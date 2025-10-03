<x-app-layout>
    <x-slot name="header">
        Báo sửa chữa
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Báo sửa chữa']]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-tools text-blue-600"></i>
                        Báo sửa chữa
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Gửi yêu cầu sửa chữa cho phòng của bạn</p>
                </div>
                <div class="flex items-center gap-4">
                    <x-secondary-button :href="route('repairs.index')">
                        <i class="fas fa-history"></i>
                        Lịch sử báo cáo
                    </x-secondary-button>
                    <x-secondary-button :href="url('/')">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại
                    </x-secondary-button>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 leading-tight mb-4">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    Thông tin yêu cầu sửa chữa
                </h3>

                <form action="{{ route('student.repairs.store') }}" method="post" enctype="multipart/form-data"
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    @csrf

                    <div>
                        <x-input-label for="room_id" value="Phòng" icon="fas fa-door-open" />
                        <x-select id="room_id" class="block mt-1 w-full" name="room_id" :options="$rooms->pluck('room_code', 'id')->toArray()"
                            :selected="old('room_id')" required placeholder="Chọn phòng" />
                        <x-input-error :messages="$errors->get('room_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="type" value="Loại sửa chữa" icon="fas fa-wrench" />
                        <x-select id="type" class="block mt-1 w-full" :options="[
                            'electric' => 'Điện',
                            'water' => 'Nước',
                            'furniture' => 'Đồ nội thất',
                            'other' => 'Khác',
                        ]" name="type"
                            :selected="old('type')" required placeholder="Chọn loại sửa chữa" />
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div class="col-span-2">
                        <x-input-label for="description" value="Mô tả vấn đề" icon="fas fa-align-left" />
                        <x-textarea id="description" class="block mt-1 w-full" name="description" :value="old('description')"
                            required placeholder="Mô tả chi tiết vấn đề cần sửa chữa" />
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="col-span-2">
                        <x-input-label for="photo" value="Ảnh minh họa (nếu có)" icon="fas fa-image" />
                        <x-text-input id="photo" class="block mt-1 w-full" type="file" name="photo"
                            accept="image/jpeg,image/png,image/jpg" />
                        <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                    </div>

                    <div class="col-span-2 flex items-center justify-end gap-6">
                        <x-secondary-button :href="url('/')">
                            <i class="fas fa-arrow-left"></i>
                            Hủy
                        </x-secondary-button>

                        <x-primary-button>
                            <i class="fas fa-save"></i>
                            Gửi yêu cầu
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
