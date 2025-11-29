<x-app-layout>
    <x-slot name="header">
        {{ __('Cài đặt hệ thống') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Cài đặt hệ thống']]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-cogs text-blue-600 me-1"></i>
                        {{ __('Cài đặt hệ thống') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ __('Cấu hình toàn cục và tùy chỉnh hệ thống quản lý ký túc xá') }}</p>
                </div>
            </div>

            <!-- Tổng quan hệ thống -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-server text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">{{ __('Trạng thái hệ thống') }}</p>
                                <p class="font-semibold text-lg text-gray-800">{{ __('Hoạt động') }}</p>
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ __('Online') }}
                        </span>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-code-branch text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">{{ __('Phiên bản') }}</p>
                                <p class="font-semibold text-lg text-gray-800">v{{ app()->version() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <i class="fas fa-clock text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">{{ __('Thời gian hoạt động') }}</p>
                                <p class="font-semibold text-lg text-gray-800" id="uptime">Đang tải...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cấu hình chung -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-sliders-h text-blue-600"></i>
                    {{ __('Cấu hình chung') }}
                </h3>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="system_name" :value="__('Tên hệ thống')" icon="fas fa-building" />
                            <x-text-input id="system_name" name="system_name" type="text" class="mt-1 block w-full"
                                :value="old('system_name', config('app.name'))" placeholder="Hệ thống quản lý ký túc xá ABC" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('Tên hiển thị trên toàn hệ thống') }}</p>
                        </div>

                        <div>
                            <x-input-label for="timezone" :value="__('Múi giờ')" icon="fas fa-globe" />
                            <x-select id="timezone" name="timezone" class="mt-1 block w-full" :options="[
                                'Asia/Ho_Chi_Minh' => 'GMT+7 (Hà Nội)',
                                'Asia/Bangkok' => 'GMT+7 (Bangkok)',
                                'UTC' => 'UTC',
                            ]"
                                :selected="old('timezone', config('app.timezone'))" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('Múi giờ mặc định cho hệ thống') }}</p>
                        </div>

                        <div>
                            <x-input-label for="date_format" :value="__('Định dạng ngày')" icon="fas fa-calendar-alt" />
                            <x-select id="date_format" name="date_format" class="mt-1 block w-full" :options="[
                                'd/m/Y' => 'dd/mm/yyyy (31/12/2025)',
                                'Y-m-d' => 'yyyy-mm-dd (2025-12-31)',
                                'd-m-Y' => 'dd-mm-yyyy (31-12-2025)',
                            ]"
                                :selected="old('date_format', 'd/m/Y')" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('Định dạng hiển thị ngày tháng') }}</p>
                        </div>

                        <div>
                            <x-input-label for="currency" :value="__('Đơn vị tiền tệ')" icon="fas fa-coins" />
                            <x-select id="currency" name="currency" class="mt-1 block w-full" :options="[
                                'VND' => 'Việt Nam Đồng (VNĐ)',
                                'USD' => 'US Dollar ($)',
                                'EUR' => 'Euro (€)',
                            ]"
                                :selected="old('currency', 'VND')" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('Đơn vị tiền tệ mặc định') }}</p>
                        </div>

                        <div>
                            <x-input-label for="maintenance_mode" :value="__('Chế độ bảo trì')" icon="fas fa-tools" />
                            <div class="mt-1 flex items-center">
                                <input id="maintenance_mode" type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    name="maintenance_mode" {{ old('maintenance_mode', false) ? 'checked' : '' }} />
                                <label for="maintenance_mode" class="ml-2 text-sm text-gray-600">
                                    {{ __('Kích hoạt chế độ bảo trì') }}
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Tạm thời vô hiệu hóa truy cập người dùng') }}
                            </p>
                        </div>

                        <div>
                            <x-input-label for="backup_schedule" :value="__('Lịch sao lưu tự động')" icon="fas fa-clock" />
                            <x-select id="backup_schedule" name="backup_schedule" class="mt-1 block w-full"
                                :options="[
                                    'daily' => 'Hàng ngày',
                                    'weekly' => 'Hàng tuần',
                                    'monthly' => 'Hàng tháng',
                                    'disabled' => 'Tắt',
                                ]" :selected="old('backup_schedule', 'daily')" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('Tự động sao lưu dữ liệu định kỳ') }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>
                            <i class="fas fa-save"></i>
                            {{ __('Lưu cấu hình') }}
                        </x-primary-button>
                    </div>
                </div>
            </div>

            <!-- Cấu hình ký túc xá -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-home text-blue-600"></i>
                    {{ __('Cấu hình ký túc xá') }}
                </h3>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="check_in_time" :value="__('Giờ nhận phòng')" icon="fas fa-sign-in-alt" />
                            <x-text-input id="check_in_time" name="check_in_time" type="time"
                                class="mt-1 block w-full" :value="old('check_in_time', '14:00')" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('Thời gian mặc định nhận phòng') }}</p>
                        </div>

                        <div>
                            <x-input-label for="check_out_time" :value="__('Giờ trả phòng')" icon="fas fa-sign-out-alt" />
                            <x-text-input id="check_out_time" name="check_out_time" type="time"
                                class="mt-1 block w-full" :value="old('check_out_time', '12:00')" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('Thời gian mặc định trả phòng') }}</p>
                        </div>

                        <div>
                            <x-input-label for="max_booking_days" :value="__('Số ngày đặt trước tối đa')" icon="fas fa-calendar-plus" />
                            <x-text-input id="max_booking_days" name="max_booking_days" type="number"
                                class="mt-1 block w-full" :value="old('max_booking_days', 30)" min="1" max="365" />
                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('Sinh viên có thể đặt phòng trước bao nhiêu ngày') }}</p>
                        </div>

                        <div>
                            <x-input-label for="min_stay_days" :value="__('Số ngày ở tối thiểu')" icon="fas fa-calendar-week" />
                            <x-text-input id="min_stay_days" name="min_stay_days" type="number"
                                class="mt-1 block w-full" :value="old('min_stay_days', 1)" min="1" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('Thời gian ở tối thiểu cho mỗi đặt phòng') }}
                            </p>
                        </div>

                        <div>
                            <x-input-label for="deposit_rate" :value="__('Tỷ lệ đặt cọc (%)')" icon="fas fa-percentage" />
                            <x-text-input id="deposit_rate" name="deposit_rate" type="number"
                                class="mt-1 block w-full" :value="old('deposit_rate', 50)" min="0" max="100"
                                step="1" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('Phần trăm tiền phòng cần đặt cọc') }}</p>
                        </div>

                        <div>
                            <x-input-label for="late_checkout_fee" :value="__('Phí trả phòng muộn (VNĐ/giờ')" icon="fas fa-clock" />
                            <x-text-input id="late_checkout_fee" name="late_checkout_fee" type="number"
                                class="mt-1 block w-full" :value="old('late_checkout_fee', 50000)" min="0" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('Phạt khi trả phòng trễ giờ quy định') }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>
                            <i class="fas fa-home"></i>
                            {{ __('Cập nhật quy định ký túc xá') }}
                        </x-primary-button>
                    </div>
                </div>
            </div>

            <!-- Cấu hình bảo mật -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-shield-alt text-red-600"></i>
                    {{ __('Cấu hình bảo mật') }}
                </h3>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="password_expiry" :value="__('Thời hạn mật khẩu (ngày)')" icon="fas fa-key" />
                            <x-text-input id="password_expiry" name="password_expiry" type="number"
                                class="mt-1 block w-full" :value="old('password_expiry', 90)" min="30" max="365" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('Yêu cầu đổi mật khẩu định kỳ') }}</p>
                        </div>

                        <div>
                            <x-input-label for="login_attempts" :value="__('Số lần đăng nhập sai tối đa')"
                                icon="fas fa-exclamation-triangle" />
                            <x-text-input id="login_attempts" name="login_attempts" type="number"
                                class="mt-1 block w-full" :value="old('login_attempts', 5)" min="3" max="10" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('Khóa tài khoản tạm thời nếu vượt quá') }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <input id="require_2fa" type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    name="require_2fa" {{ old('require_2fa', false) ? 'checked' : '' }} />
                                <label for="require_2fa" class="ml-2 text-sm text-gray-600">
                                    {{ __('Yêu cầu xác thực hai yếu tố (2FA)') }}
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Tăng cường bảo mật cho tất cả tài khoản') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-danger-button>
                            <i class="fas fa-lock"></i>
                            {{ __('Cập nhật bảo mật') }}
                        </x-danger-button>
                    </div>
                </div>
            </div>

            <!-- Nhật ký hệ thống -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-history text-gray-600"></i>
                    {{ __('Nhật ký hoạt động hệ thống') }}
                </h3>

                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-600"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ __('Hệ thống khởi động') }}</p>
                                <p class="text-xs text-gray-500">{{ now()->subMinutes(5)->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">{{ __('Khởi động thành công') }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-sync text-blue-600"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ __('Cập nhật cấu hình') }}</p>
                                <p class="text-xs text-gray-500">{{ now()->subHours(2)->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">{{ __('Thay đổi múi giờ') }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ __('Cảnh báo dung lượng') }}</p>
                                <p class="text-xs text-gray-500">{{ now()->subDays(1)->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">{{ __('80% dung lượng đĩa') }}</span>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <x-secondary-button class="text-sm">
                        <i class="fas fa-eye"></i>
                        {{ __('Xem toàn bộ nhật ký') }}
                    </x-secondary-button>
                </div>
            </div>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Cập nhật thời gian hoạt động
                function updateUptime() {
                    const startTime = new Date('{{ config('app.start_time', now()) }}');
                    const now = new Date();
                    const diff = now - startTime;

                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

                    let uptime = '';
                    if (days > 0) uptime += days + ' ngày ';
                    if (hours > 0) uptime += hours + ' giờ ';
                    uptime += minutes + ' phút';

                    document.getElementById('uptime').textContent = uptime;
                }

                updateUptime();
                setInterval(updateUptime, 60000); // Cập nhật mỗi phút
            });
        </script>
    @endPushOnce
</x-app-layout>
