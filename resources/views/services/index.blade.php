<x-app-layout>
    <x-slot name="header">
        {{ __('Quản lý dịch vụ') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Quản lý dịch vụ']]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-concierge-bell text-blue-600 me-1"></i>
                        {{ __('Quản lý dịch vụ') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Quản lý tất cả dịch vụ trong hệ thống') }}</p>
                </div>
                <x-secondary-button class="!bg-blue-600 !text-white !hover:bg-blue-700" x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-creation')">
                    <i class="fas fa-plus"></i>
                    {{ __('Thêm dịch vụ mới') }}
                </x-secondary-button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-blue-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-concierge-bell text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tổng dịch vụ') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalServices }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <x-table :title="__('Danh sách dịch vụ')">
                    <x-thead>
                        <x-tr>
                            <x-th>{{ __('STT') }}</x-th>
                            <x-th>
                                <div class="flex items-center gap-6">
                                    {{ __('Tên dịch vụ') }}
                                    <x-sortable-column :options="['name_asc', 'name_desc']" />
                                </div>
                            </x-th>
                            <x-th>{{ __('Đơn vị') }}</x-th>
                            <x-th>{{ __('Đơn giá') }}</x-th>
                            <x-th>{{ __('Hạn mức miễn phí') }}</x-th>
                            <x-th>{{ __('Bắt buộc') }}</x-th>
                            <x-th>{{ __('Hành động') }}</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($services as $index => $service)
                            <x-tr>
                                <x-td>#{{ $services->firstItem() + $index }}</x-td>
                                <x-td>{{ $service->name }}</x-td>
                                <x-td>{{ $service->unit }}</x-td>
                                <x-td>{{ number_format($service->unit_price, 0, ',', '.') }} VND</x-td>
                                <x-td>{{ $service->free_quota }}</x-td>
                                <x-td>
                                    @if ($service->is_mandatory)
                                        <span class="text-green-600">{{ __('Có') }}</span>
                                    @else
                                        <span class="text-red-600">{{ __('Không') }}</span>
                                    @endif
                                </x-td>
                                <x-td>
                                    <x-icon-button :data-update-url="route('services.update', $service)" :data-name-value="$service->name" :data-unit-value="$service->unit"
                                        :data-unit-price-value="$service->unit_price" :data-free-quota-value="$service->free_quota" :data-is-mandatory-value="$service->is_mandatory ? '1' : '0'" icon="fas fa-edit"
                                        :title="__('Chỉnh sửa')" class="!bg-yellow-500 !text-white !hover:bg-yellow-600"
                                        x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')" />

                                    <x-icon-button :data-delete-url="route('services.destroy', $service)" icon="fas fa-trash" :title="__('Xoá')"
                                        class="!bg-red-500 !text-white !hover:bg-red-600" x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')" />
                                </x-td>
                            </x-tr>
                        @endforeach
                    </x-tbody>
                </x-table>
            </div>

            {{ $services->links() }}
        </div>
    </div>

    <x-delete-modal />

    <x-modal name="confirm-creation" :show="$errors->serviceCreation->isNotEmpty()" focusable>
        <form method="post" action="{{ route('services.store') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-concierge-bell text-blue-600 me-1"></i>
                {{ __('Thông tin dịch vụ') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="name-creation" :value="__('Tên dịch vụ')" icon="fas fa-concierge-bell" />
                <x-text-input id="name-creation" class="block mt-1 w-full" type="text" name="name"
                    :value="old('name')" required autofocus autocomplete="name" :placeholder="__('Nhập tên dịch vụ')" />
                <x-input-error :messages="$errors->serviceCreation->get('name')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="unit-creation" :value="__('Đơn vị')" icon="fas fa-ruler" />
                <x-text-input id="unit-creation" class="block mt-1 w-full" type="text" name="unit"
                    :value="old('unit')" required autocomplete="unit" :placeholder="__('Nhập đơn vị')" />
                <x-input-error :messages="$errors->serviceCreation->get('unit')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="unit_price-creation" :value="__('Đơn giá')" icon="fas fa-money-bill" />
                <x-text-input id="unit_price-creation" class="block mt-1 w-full" type="number" name="unit_price"
                    :value="old('unit_price')" required autocomplete="unit_price" :placeholder="__('Nhập đơn giá')" />
                <x-input-error :messages="$errors->serviceCreation->get('unit_price')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="free_quota-creation" :value="__('Hạn mức miễn phí')" icon="fas fa-gift" />
                <x-text-input id="free_quota-creation" class="block mt-1 w-full" type="number" name="free_quota"
                    :value="old('free_quota', 0)" autocomplete="free_quota" :placeholder="__('Nhập hạn mức miễn phí (nếu có)')" />
                <x-input-error :messages="$errors->serviceCreation->get('free_quota')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="is_mandatory-creation" :value="__('Bắt buộc')" icon="fas fa-exclamation-circle" />
                <x-select id="is_mandatory-creation" class="block mt-1 w-full" :options="[
                    '1' => 'Có',
                    '0' => 'Không',
                ]" name="is_mandatory"
                    :selected="old('is_mandatory', '0')" :placeholder="__('Chọn trạng thái bắt buộc')" />
                <x-input-error :messages="$errors->serviceCreation->get('is_mandatory')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Huỷ') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Lưu') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="confirm-updation" :show="$errors->serviceUpdation->isNotEmpty()" focusable>
        <form id="update-form" method="post" action="{{ session('update_action', '#') }}" class="p-6">
            @csrf
            @method('put')

            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-concierge-bell text-blue-600 me-1"></i>
                {{ __('Thông tin dịch vụ') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="name-updation" :value="__('Tên dịch vụ')" icon="fas fa-concierge-bell" />
                <x-text-input id="name-updation" class="block mt-1 w-full" type="text" name="name"
                    :value="old('name')" required autofocus autocomplete="name" :placeholder="__('Nhập tên dịch vụ')" />
                <x-input-error :messages="$errors->serviceUpdation->get('name')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="unit-updation" :value="__('Đơn vị')" icon="fas fa-ruler" />
                <x-text-input id="unit-updation" class="block mt-1 w-full" type="text" name="unit"
                    :value="old('unit')" required autocomplete="unit" :placeholder="__('Nhập đơn vị')" />
                <x-input-error :messages="$errors->serviceUpdation->get('unit')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="unit_price-updation" :value="__('Đơn giá')" icon="fas fa-money-bill" />
                <x-text-input id="unit_price-updation" class="block mt-1 w-full" type="number" name="unit_price"
                    :value="old('unit_price')" required autocomplete="unit_price" :placeholder="__('Nhập đơn giá')" />
                <x-input-error :messages="$errors->serviceUpdation->get('unit_price')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="free_quota-updation" :value="__('Hạn mức miễn phí')" icon="fas fa-gift" />
                <x-text-input id="free_quota-updation" class="block mt-1 w-full" type="number" name="free_quota"
                    :value="old('free_quota')" autocomplete="free_quota" :placeholder="__('Nhập hạn mức miễn phí (nếu có)')" />
                <x-input-error :messages="$errors->serviceUpdation->get('free_quota')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="is_mandatory-updation" :value="__('Bắt buộc')" icon="fas fa-exclamation-circle" />
                <x-select id="is_mandatory-updation" class="block mt-1 w-full" :options="[
                    '1' => 'Có',
                    '0' => 'Không',
                ]" name="is_mandatory"
                    :selected="old('is_mandatory')" :placeholder="__('Chọn trạng thái bắt buộc')" />
                <x-input-error :messages="$errors->serviceUpdation->get('is_mandatory')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Huỷ') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Cập nhật') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('update-form');
                const nameInput = document.getElementById('name-updation');
                const unitInput = document.getElementById('unit-updation');
                const unitPriceInput = document.getElementById('unit_price-updation');
                const freeQuotaInput = document.getElementById('free_quota-updation');
                const isMandatoryInput = document.getElementById('is_mandatory-updation');

                document.querySelectorAll('[data-update-url]').forEach(btn =>
                    btn.addEventListener('click', () => {
                        form.action = btn.dataset.updateUrl;

                        nameInput.value = btn.dataset.nameValue || '';
                        unitInput.value = btn.dataset.unitValue || '';
                        unitPriceInput.value = btn.dataset.unitPriceValue || '';
                        freeQuotaInput.value = btn.dataset.freeQuotaValue || '';
                        isMandatoryInput.value = btn.dataset.isMandatoryValue || '';
                    })
                );
            });
        </script>
    @endPushOnce
</x-app-layout>
