<x-app-layout>
    <x-slot name="header">
        {{ __('Quản lý thông báo') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Quản lý thông báo']]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-bell text-blue-600 me-1"></i>
                        {{ __('Quản lý thông báo') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Gửi và quản lý thông báo hệ thống đến người dùng') }}
                    </p>
                </div>
                <x-secondary-button class="!bg-blue-600 !text-white hover:!bg-blue-700" x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-creation')">
                    <i class="fas fa-plus"></i>
                    {{ __('Tạo thông báo mới') }}
                </x-secondary-button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-blue-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-bell text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tổng thông báo') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $notifications->total() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <x-table :title="__('Danh sách thông báo')">
                    <x-thead>
                        <x-tr>
                            <x-th>{{ __('STT') }}</x-th>
                            <x-th>{{ __('Tiêu đề') }}</x-th>
                            <x-th>{{ __('Nội dung') }}</x-th>
                            <x-th>{{ __('Người gửi') }}</x-th>
                            <x-th>{{ __('Ngày gửi') }}</x-th>
                            <x-th>{{ __('Tệp đính kèm') }}</x-th>
                            <x-th>{{ __('Hành động') }}</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($notifications as $index => $notification)
                            <x-tr>
                                <x-td>#{{ $notifications->firstItem() + $index }}</x-td>
                                <x-td class="font-medium">{{ $notification->title }}</x-td>
                                <x-td>{{ Str::limit($notification->content, 50) ?? 'N/A' }}</x-td>
                                <x-td>
                                    <div class="flex items-center gap-2">
                                        @if ($notification->user->avatar)
                                            <img src="{{ asset('storage/' . $notification->user->avatar) }}"
                                                alt="Avatar" class="w-6 h-6 rounded-full object-cover">
                                        @else
                                            <div
                                                class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">
                                                {{ mb_substr($notification->user->name, 0, 2, 'UTF-8') }}
                                            </div>
                                        @endif
                                        {{ $notification->user->name }}
                                    </div>
                                </x-td>
                                <x-td>{{ $notification->created_at->format('d/m/Y H:i') }}</x-td>
                                <x-td>
                                    @if ($notification->attachment)
                                        <a href="{{ asset('storage/' . $notification->attachment) }}" target="_blank"
                                            class="text-blue-600 hover:underline text-sm">
                                            <i class="fas fa-paperclip"></i> {{ __('Xem tệp') }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-sm">{{ __('Không có') }}</span>
                                    @endif
                                </x-td>
                                <x-td>
                                    <x-icon-button :data-delete-url="route('notifications.destroy', $notification)" icon="fas fa-trash" :title="__('Xoá')"
                                        class="!bg-red-500 !text-white hover:!bg-red-600" x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')" />
                                </x-td>
                            </x-tr>
                        @endforeach
                    </x-tbody>
                </x-table>
            </div>

            {{ $notifications->links() }}
        </div>
    </div>

    <x-delete-modal />

    <x-modal name="confirm-creation" :show="$errors->notificationCreation->isNotEmpty()" focusable>
        <form method="post" action="{{ route('notifications.store') }}" class="p-6" enctype="multipart/form-data">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-bell text-blue-600 me-1"></i>
                {{ __('Tạo thông báo mới') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="title-creation" :value="__('Tiêu đề')" icon="fas fa-heading" />
                <x-text-input id="title-creation" class="block mt-1 w-full" type="text" name="title"
                    :value="old('title')" required autofocus autocomplete="title" :placeholder="__('Nhập tiêu đề thông báo')" />
                <x-input-error :messages="$errors->notificationCreation->get('title')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="content-creation" :value="__('Nội dung')" icon="fas fa-align-left" />
                <x-textarea id="content-creation" class="block mt-1 w-full" name="content" rows="4"
                    :value="old('content')" :placeholder="__('Nhập nội dung thông báo (tùy chọn)')" />
                <x-input-error :messages="$errors->notificationCreation->get('content')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="attachment-creation" :value="__('Tệp đính kèm')" icon="fas fa-paperclip" />
                <x-file-input id="attachment-creation" class="block mt-1 w-full" name="attachment" />
                <x-input-error :messages="$errors->notificationCreation->get('attachment')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Huỷ') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    <i class="fas fa-paper-plane"></i>
                    {{ __('Gửi thông báo') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
