<x-app-layout>
    <x-slot name="header">
        Quản lý chi nhánh
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Quản lý chi nhánh']]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-building text-blue-800"></i>
                        Quản lý chi nhánh
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Quản lý tất cả chi nhánh trong hệ thống</p>
                </div>
                <x-secondary-button class="bg-blue-600 hover:bg-blue-700 text-white" x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-creation')">
                    <i class="fas fa-plus"></i>
                    Thêm chi nhánh
                </x-secondary-button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="flex items-center space-x-6">
                        <div class="shadow-sm rounded-lg bg-blue-100 p-3">
                            <i class="fas fa-building text-blue-800 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tổng chi nhánh</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalBranches }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <x-table title="Danh sách chi nhánh">
                    <x-thead>
                        <x-tr>
                            <x-th>STT</x-th>
                            <x-th>Tên chi nhánh</x-th>
                            <x-th>Địa chỉ</x-th>
                            <x-th>Hành động</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($branches as $index => $branch)
                            <x-tr>
                                <x-td>#{{ $branches->firstItem() + $index }}</x-td>
                                <x-td>{{ $branch->name }}</x-td>
                                <x-td>{{ $branch->address ?? 'N/A' }}</x-td>
                                <x-td>
                                    <x-icon-button :data-update-url="route('branches.update', $branch)" :data-name-value="$branch->name" :data-address-value="$branch->address"
                                        title="Chỉnh sửa" class="bg-yellow-500 hover:bg-yellow-600 text-white"
                                        x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')">
                                        <i class="fas fa-edit"></i>
                                    </x-icon-button>
                                    <x-icon-button :data-delete-url="route('branches.destroy', $branch)" title="Xoá"
                                        class="bg-red-500 hover:bg-red-600 text-white" x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')">
                                        <i class="fas fa-trash"></i>
                                    </x-icon-button>
                                </x-td>
                            </x-tr>
                        @endforeach
                    </x-tbody>
                </x-table>
            </div>

            {{ $branches->links() }}
        </div>
    </div>

    <x-modal name="confirm-creation" :show="$errors->branchCreation->isNotEmpty()" focusable>
        <form method="post" action="{{ route('branches.store') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-building text-blue-800"></i>
                Thông tin chi nhánh
            </h2>

            <div class="mt-6">
                <x-input-label for="name-creation" value="Tên" icon="fas fa-building" />
                <x-text-input id="name-creation" class="block mt-1 w-full" type="text" name="name"
                    :value="old('name')" required autofocus autocomplete="name" placeholder="Nhập tên" />
                <x-input-error :messages="$errors->branchCreation->get('name')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="address-creation" value="Địa chỉ" icon="fas fa-map-marker-alt" />
                <x-text-input id="address-creation" class="block mt-1 w-full" type="text" name="address"
                    :value="old('address')" required autocomplete="address" placeholder="Nhập địa chỉ" />
                <x-input-error :messages="$errors->branchCreation->get('address')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Huỷ
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    Lưu
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="confirm-updation" :show="$errors->branchUpdation->isNotEmpty()" focusable>
        <form id="update-form" method="post" action="{{ session('update_action', '#') }}" class="p-6">
            @csrf
            @method('put')

            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-building text-blue-800"></i>
                Thông tin chi nhánh
            </h2>

            <div class="mt-6">
                <x-input-label for="name" value="Tên" icon="fas fa-building" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"
                    required autofocus autocomplete="name" placeholder="Nhập tên" />
                <x-input-error :messages="$errors->branchUpdation->get('name')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="address" value="Địa chỉ" icon="fas fa-map-marker-alt" />
                <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')"
                    required autocomplete="address" placeholder="Nhập địa chỉ" />
                <x-input-error :messages="$errors->branchUpdation->get('address')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Huỷ
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    Cập nhật
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-delete-modal />

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('update-form');
                const nameInput = document.getElementById('name');
                const addressInput = document.getElementById('address');

                const oldName = @json(old('name'));
                const oldAddress = @json(old('address'));

                document.querySelectorAll('[data-update-url]').forEach(button => {
                    button.addEventListener('click', () => {
                        form.action = button.getAttribute('data-update-url');

                        const name = button.getAttribute('data-name-value');
                        const address = button.getAttribute('data-address-value');

                        nameInput.value = name;
                        addressInput.value = address;
                    });
                });
            });
        </script>
    @endPushOnce
</x-app-layout>
