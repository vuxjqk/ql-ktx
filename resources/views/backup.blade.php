<x-app-layout>
    <x-slot name="header">
        {{ __('Sao lưu & Phục hồi') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Sao lưu & Phục hồi']]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-shield-alt text-blue-600 me-1"></i>
                        {{ __('Sao lưu & Phục hồi') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Quản lý bản sao lưu dữ liệu hệ thống') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Thống kê nhanh -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <i class="fas fa-hdd text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tổng bản sao lưu') }}</p>
                            <p class="font-semibold text-xl text-gray-800">{{ $backups->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-green-100 p-3 rounded-lg">
                            <i class="fas fa-clock text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Bản sao lưu mới nhất') }}</p>
                            <p class="font-semibold text-sm text-gray-800">
                                {{ $backups->first()['date'] ?? __('Chưa có') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-yellow-100 p-3 rounded-lg">
                            <i class="fas fa-database text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Dung lượng tổng') }}</p>
                            <p class="font-semibold text-xl text-gray-800">
                                {{ number_format($backups->sum(fn($b) => (float) str_replace(' MB', '', $b['size'])), 2) }}
                                MB
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hành động nhanh -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-800">
                            <i class="fas fa-plus-circle text-green-600 me-1"></i>
                            {{ __('Tạo bản sao lưu mới') }}
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ __('Sao lưu toàn bộ cơ sở dữ liệu và tệp hệ thống ngay lập tức') }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('backup.store') }}" class="inline">
                        @csrf
                        <x-primary-button class="bg-green-600 hover:bg-green-700">
                            <i class="fas fa-download"></i>
                            {{ __('Tạo sao lưu') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>

            <!-- Danh sách bản sao lưu -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
                        <i class="fas fa-list-alt text-blue-600 me-1"></i>
                        {{ __('Danh sách bản sao lưu') }}
                    </h3>

                    @if ($backups->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Tên file') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Kích thước') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Ngày tạo') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Hành động') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($backups as $backup)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-file-archive text-blue-600"></i>
                                                    {{ $backup['name'] }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $backup['size'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="flex items-center gap-1">
                                                    <i class="fas fa-calendar-alt text-gray-400"></i>
                                                    {{ $backup['date'] }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('backup.download', $backup['name']) }}"
                                                        class="text-blue-600 hover:text-blue-900 transition-colors"
                                                        title="{{ __('Tải xuống') }}">
                                                        <i class="fas fa-download"></i>
                                                    </a>

                                                    <form method="POST"
                                                        action="{{ route('backup.destroy', $backup['name']) }}"
                                                        class="inline"
                                                        onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa bản sao lưu này?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-900 transition-colors"
                                                            title="{{ __('Xóa') }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-database text-gray-400 text-5xl mb-4"></i>
                            <p class="text-gray-500 text-lg">{{ __('Chưa có bản sao lưu nào') }}</p>
                            <p class="text-gray-400 text-sm mt-1">
                                {{ __('Nhấn nút "Tạo sao lưu" để bắt đầu sao lưu dữ liệu') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Hướng dẫn phục hồi -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-yellow-600 text-xl mt-1"></i>
                    <div>
                        <h4 class="font-semibold text-yellow-900 mb-2">
                            {{ __('Hướng dẫn phục hồi dữ liệu') }}
                        </h4>
                        <ol class="text-sm text-yellow-800 space-y-1 list-decimal list-inside">
                            <li>{{ __('Tải xuống bản sao lưu bạn muốn phục hồi') }}</li>
                            <li>{{ __('Giải nén file .zip để lấy file .sql và các thư mục') }}</li>
                            <li>{{ __('Sử dụng lệnh Artisan:') }}
                                <code class="bg-yellow-100 px-2 py-1 rounded text-xs font-mono">
                                    php artisan backup:restore --file=ten_file_backup.zip
                                </code>
                            </li>
                            <li>{{ __('Hoặc thực hiện thủ công qua phpMyAdmin và sao chép file') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
