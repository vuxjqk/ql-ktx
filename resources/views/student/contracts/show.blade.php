{{-- resources/views/student/contracts/show.blade.php --}}
@extends('student.layouts.app')

@section('title', 'Xem & Ký hợp đồng điện tử')

@pushOnce('styles')
    <style>
        .pdf-container {
            height: 75vh;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            background: #f9fafb;
        }

        .agree-box {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
        }

        .btn-agree {
            background: linear-gradient(to right, #10b981, #059669);
            color: white;
            font-weight: bold;
            padding: 1rem 2.5rem;
            font-size: 1.125rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        .btn-agree:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(16, 185, 129, 0.4);
        }

        .btn-agree:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .checkbox-custom {
            width: 24px;
            height: 24px;
            cursor: pointer;
        }
    </style>
@endpushOnce

@section('content')
    <div class="bg-gray-50 py-4 border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-sm">
                <ol class="inline-flex items-center space-x-2">
                    <li><a href="{{ route('student.home') }}" class="text-gray-600 hover:text-blue-600"><i
                                class="fas fa-home"></i></a></li>
                    <li><i class="fas fa-chevron-right text-gray-400 mx-2"></i></li>
                    <li><a href="{{ route('student.bookings.index') }}" class="text-gray-600 hover:text-blue-600">Quản lý
                            phòng ở</a></li>
                    <li><i class="fas fa-chevron-right text-gray-400 mx-2"></i></li>
                    <li class="text-gray-900 font-medium">Ký hợp đồng</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-8 text-center">
                <h1 class="text-3xl font-bold flex items-center justify-center">
                    <i class="fas fa-file-contract mr-4 text-5xl"></i>
                    HỢP ĐỒNG THUÊ PHÒNG KÝ TÚC XÁ
                </h1>
                <p class="mt-4 text-lg opacity-90">
                    Mã hợp đồng: <strong>{{ $contract->contract_code }}</strong><br>
                    Phòng: <strong>{{ $contract->booking->room->room_code }}</strong> -
                    {{ $contract->booking->room->floor->branch->name }}
                </p>
            </div>

            <div class="p-8 space-y-10">
                <!-- PDF Viewer -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">
                        <i class="fas fa-file-pdf text-red-600 mr-3"></i>Nội dung hợp đồng
                    </h2>
                    <div class="pdf-container">
                        <iframe
                            src="{{ route('student.contracts.export', $contract) }}#toolbar=0&navpanes=0&scrollbar=0&view=FitH"
                            width="100%" height="100%" style="border: none;">
                            Trình duyệt không hỗ trợ xem PDF.
                            <a href="{{ route('student.contracts.export', $contract) }}" class="text-blue-600 underline">Tải
                                về tại đây</a>
                        </iframe>
                    </div>
                    <p class="text-center text-sm text-gray-600 mt-4">
                        <i class="fas fa-info-circle mr-1"></i>
                        Vui lòng đọc kỹ toàn bộ nội dung hợp đồng trước khi xác nhận
                    </p>
                </div>

                <!-- Form đồng ý -->
                <form action="{{ route('student.contracts.agree', $contract) }}" method="POST">
                    @csrf

                    <div class="agree-box">
                        <div class="max-w-2xl mx-auto">
                            <div class="flex items-center justify-center space-x-4 mb-6">
                                <input type="checkbox" id="agree_checkbox" name="agreed" value="1" required
                                    class="checkbox-custom text-emerald-600 focus:ring-emerald-500 rounded"
                                    onchange="this.form.querySelector('button[type=submit]').disabled = !this.checked">
                                <label for="agree_checkbox"
                                    class="text-lg font-semibold text-gray-800 cursor-pointer leading-relaxed">
                                    Tôi đã đọc kỹ, hiểu rõ và <span class="text-emerald-600 underline">đồng ý toàn bộ</span>
                                    nội dung hợp đồng thuê phòng ký túc xá.
                                </label>
                            </div>

                            <p class="text-sm text-gray-600 mb-8">
                                Việc xác nhận đồng ý có giá trị pháp lý như ký tay. Hợp đồng sẽ được lưu trữ và bạn có thể
                                tải lại bất kỳ lúc nào.
                            </p>

                            <button type="submit" disabled class="btn-agree inline-flex items-center space-x-3">
                                <i class="fas fa-download text-xl"></i>
                                <span>Xác nhận & Tải hợp đồng đã ký</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
