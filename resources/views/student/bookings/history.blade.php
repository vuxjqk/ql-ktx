{{-- resources/views/student/bookings/history.blade.php --}}
@extends('student.layouts.app')

@section('title', 'Lịch sử đặt phòng')

@pushOnce('styles')
    <style>
        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 1.5rem;
            width: 4px;
            background: #e5e7eb;
            border-radius: 2px;
        }

        .timeline-item {
            position: relative;
            padding-left: 3.5rem;
            padding-bottom: 2rem;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: 0.9rem;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 0 0 4px #e5e7eb;
        }
    </style>
@endPushOnce

@section('content')
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-white mb-4">Lịch sử đặt phòng</h1>
            <p class="text-blue-100 text-lg">Theo dõi trạng thái các yêu cầu đặt phòng, chuyển phòng, gia hạn của bạn</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $summary['pending'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">Đang chờ duyệt</p>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $summary['active'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">Đang ở</p>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-file-contract text-blue-600 text-xl"></i>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $summary['approved'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">Đã duyệt</p>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-history text-gray-600 text-xl"></i>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $bookings->total() }}</p>
                <p class="text-sm text-gray-600">Tổng yêu cầu</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Filters -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-filter mr-2 text-blue-600"></i>Bộ lọc
                    </h3>

                    <form method="GET" action="{{ route('student.bookings.history') }}">
                        <!-- Loại yêu cầu -->
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Loại yêu cầu</label>
                            <select name="type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Tất cả</option>
                                <option value="registration" {{ request('type') == 'registration' ? 'selected' : '' }}>Đăng
                                    ký mới</option>
                                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Chuyển phòng
                                </option>
                                <option value="extension" {{ request('type') == 'extension' ? 'selected' : '' }}>Gia hạn
                                </option>
                            </select>
                        </div>

                        <!-- Trạng thái -->
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                            <select name="status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt
                                </option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối
                                </option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang ở
                                </option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Hết hạn
                                </option>
                                <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Đã hủy
                                </option>
                            </select>
                        </div>

                        <!-- Thời gian -->
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Thời gian</label>
                            <select name="period"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Tất cả thời gian</option>
                                <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>Tháng
                                    này</option>
                                <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Tháng
                                    trước</option>
                                <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>Năm nay
                                </option>
                            </select>
                        </div>

                        <div class="flex space-x-2">
                            <button type="submit"
                                class="flex-1 bg-blue-600 text-white py-2.5 rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                                <i class="fas fa-search mr-1"></i> Lọc
                            </button>
                            <a href="{{ route('student.bookings.history') }}"
                                class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Booking List -->
            <div class="lg:col-span-3">
                @forelse($bookings as $booking)
                    <div
                        class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 mb-6 overflow-hidden">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                                <div>
                                    <div class="flex items-center space-x-3">
                                        <h3 class="text-xl font-bold text-gray-900">
                                            @if ($booking->booking_type === 'registration')
                                                <i class="fas fa-key text-green-600"></i> Đăng ký ở mới
                                            @elseif($booking->booking_type === 'transfer')
                                                <i class="fas fa-exchange-alt text-blue-600"></i> Chuyển phòng
                                            @else
                                                <i class="fas fa-calendar-plus text-purple-600"></i> Gia hạn hợp đồng
                                            @endif
                                        </h3>
                                        @if ($booking->contract)
                                            <span
                                                class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full font-medium">
                                                Hợp đồng #{{ $booking->contract->contract_code }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-gray-600">
                                        <span><i class="fas fa-calendar-alt mr-1"></i>
                                            {{ $booking->created_at->format('d/m/Y H:i') }}</span>
                                        @if ($booking->processed_at)
                                            <span>• Đã xử lý: {{ $booking->processed_at->format('d/m/Y H:i') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3">
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold border
                                    {{ 'status-' . $booking->status }}">
                                        @if ($booking->status === 'pending')
                                            Chờ duyệt
                                        @elseif($booking->status === 'approved')
                                            Đã duyệt
                                        @elseif($booking->status === 'rejected')
                                            Từ chối
                                        @elseif($booking->status === 'active')
                                            Đang ở
                                        @elseif($booking->status === 'expired')
                                            Hết hạn
                                        @elseif($booking->status === 'terminated')
                                            Đã hủy
                                        @endif
                                    </span>

                                    @if ($booking->status === 'active' && $booking->room)
                                        <a href="{{ route('student.rooms.show', $booking->room) }}"
                                            class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                                            Xem phòng →
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Room Info -->
                            @if ($booking->room)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 py-4 border-t border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-16 h-16 bg-gray-200 border-2 border-dashed rounded-xl flex-shrink-0">
                                            @if ($booking->room->images->count() > 0)
                                                <img src="{{ asset('storage/' . $booking->room->images->first()->image_path) }}"
                                                    alt="{{ $booking->room->room_code }}"
                                                    class="w-full h-full object-cover rounded-xl">
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">Phòng {{ $booking->room->room_code }}</p>
                                            <p class="text-sm text-gray-600">
                                                {{ $booking->room->floor->branch->name ?? 'N/A' }} • Tầng
                                                {{ $booking->room->floor->floor_number ?? '?' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="text-sm">
                                        <p class="text-gray-600">Ngày nhận phòng</p>
                                        <p class="font-semibold text-gray-900">
                                            {{ $booking->check_in_date->format('d/m/Y') }}</p>
                                    </div>

                                    <div class="text-sm">
                                        <p class="text-gray-600">Dự kiến trả phòng</p>
                                        <p class="font-semibold text-gray-900">
                                            {{ $booking->expected_check_out_date?->format('d/m/Y') ?? 'Chưa xác định' }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <!-- Timeline -->
                            <div class="timeline relative mt-6">
                                @if ($booking->status === 'pending')
                                    <div class="timeline-item">
                                        <div class="timeline-dot bg-yellow-500"></div>
                                        <div class="bg-yellow-50 p-4 rounded-lg">
                                            <p class="font-medium text-yellow-900">Đã gửi yêu cầu</p>
                                            <p class="text-sm text-yellow-700">{{ $booking->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                @elseif(in_array($booking->status, ['approved', 'active', 'expired', 'terminated']))
                                    <div class="timeline-item">
                                        <div class="timeline-dot bg-green-500"></div>
                                        <div class="bg-green-50 p-4 rounded-lg">
                                            <p class="font-medium text-green-900">Đã gửi yêu cầu</p>
                                            <p class="text-sm text-green-700">{{ $booking->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>

                                    @if ($booking->processed_at)
                                        <div class="timeline-item">
                                            <div
                                                class="timeline-dot {{ $booking->status === 'rejected' ? 'bg-red-500' : 'bg-blue-500' }}">
                                            </div>
                                            <div
                                                class="{{ $booking->status === 'rejected' ? 'bg-red-50' : 'bg-blue-50' }} p-4 rounded-lg">
                                                <p
                                                    class="font-medium {{ $booking->status === 'rejected' ? 'text-red-900' : 'text-blue-900' }}">
                                                    {{ $booking->status === 'rejected' ? 'Bị từ chối' : 'Đã duyệt' }}
                                                </p>
                                                <p
                                                    class="text-sm {{ $booking->status === 'rejected' ? 'text-red-700' : 'text-blue-700' }}">
                                                    Bởi: {{ $booking->processedBy?->name ?? 'Quản trị viên' }} •
                                                    {{ $booking->processed_at->diffForHumans() }}
                                                </p>
                                                @if ($booking->reason)
                                                    <p class="text-sm mt-2 italic">{{ $booking->reason }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    @if ($booking->status === 'active')
                                        <div class="timeline-item">
                                            <div class="timeline-dot bg-green-600"></div>
                                            <div class="bg-green-50 p-4 rounded-lg">
                                                <p class="font-medium text-green-900">Đang ở</p>
                                                <p class="text-sm text-green-700">Từ
                                                    {{ $booking->check_in_date->format('d/m/Y') }}</p>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end space-x-3 mt-4 pt-4 border-t border-gray-100">
                                @if ($booking->contract && $booking->contract->contract_file)
                                    <a href="{{ asset('storage/' . $booking->contract->contract_file) }}" target="_blank"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                                        <i class="fas fa-file-pdf mr-2"></i> Xem hợp đồng
                                    </a>
                                @endif

                                @if ($booking->status === 'pending')
                                    <form action="{{ route('student.bookings.cancel', $booking) }}" method="POST"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Bạn có chắc muốn hủy yêu cầu này?')"
                                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
                                            <i class="fas fa-times mr-2"></i>Hủy yêu cầu
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-md p-16 text-center">
                        <i class="fas fa-calendar-times text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Chưa có yêu cầu đặt phòng</h3>
                        <p class="text-gray-600 mb-6">Khi bạn đăng ký phòng, lịch sử sẽ hiển thị tại đây</p>
                        <a href="{{ route('student.rooms.index') }}"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-search mr-2"></i>Tìm phòng ngay
                        </a>
                    </div>
                @endforelse

                <!-- Pagination -->
                @if ($bookings->hasPages())
                    <div class="mt-8">
                        {{ $bookings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@pushOnce('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto refresh nếu có yêu cầu đang pending (tùy chọn)
            // setInterval(() => location.reload(), 30000);
        });
    </script>
@endPushOnce
