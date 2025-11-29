@extends('student.layouts.app')

@section('title', 'Về chúng tôi')

@push('styles')
    <style>
        .member-card {
            transition: all 0.4s ease;
            backdrop-filter: blur(12px);
        }

        .member-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }

        .avatar-circle {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            overflow: hidden;
            border: 6px solid white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .gradient-title {
            background: linear-gradient(to right, #3B82F6, #8B5CF6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
@endpush

@section('content')
    <!-- Hero -->
    <div class="relative bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 py-24 overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-30"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
                Hệ thống Quản lý Ký túc xá Sinh viên
            </h1>
            <p class="text-xl md:text-2xl text-blue-100 max-w-4xl mx-auto leading-relaxed">
                Đồ án tốt nghiệp ngành Công nghệ Thông tin<br>
                Trường Đại học Công Thương TP. Hồ Chí Minh
            </p>
            <div class="mt-10 flex justify-center gap-4">
                <div class="bg-white/20 backdrop-blur-sm rounded-full px-8 py-4">
                    <span class="text-white font-bold text-lg">2025</span>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-full px-8 py-4">
                    <span class="text-white font-bold text-lg">Nhóm: KLCN_TH138</span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Giới thiệu dự án -->
        <div class="text-center mb-20">
            <h2 class="text-4xl font-bold text-gray-900 mb-6 gradient-title">
                Chúng tôi là ai?
            </h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Chúng tôi là 3 sinh viên năm cuối ngành
                <strong>Công nghệ Thông tin</strong> của
                <strong>Trường Đại học Công Thương TP. Hồ Chí Minh</strong>.
                Với niềm đam mê công nghệ và mong muốn cải thiện trải nghiệm ở ký túc xá cho chính mình và các bạn sinh
                viên, chúng tôi đã cùng nhau xây dựng hệ thống quản lý ký túc xá hiện đại, thân thiện và thông minh này.
            </p>
        </div>

        <!-- Thành viên nhóm -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-20">
            <!-- Thành viên 1 – Leader -->
            <div class="member-card bg-white rounded-2xl shadow-lg overflow-hidden text-center">
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-8">
                    <div class="avatar-circle mx-auto">
                        <img src="{{ asset('storage/logo/huit.jpg') }}" alt="Trần Anh Vũ"
                            class="w-full h-full object-cover">
                    </div>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Trần Anh Vũ</h3>
                    <p class="text-blue-600 font-semibold mb-4">Nhóm trưởng – Full-stack Developer</p>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">
                        Chịu trách nhiệm thiết kế kiến trúc hệ thống, phát triển backend Laravel, tích hợp VNPay,
                        xây dựng dashboard quản trị và các tính năng cốt lõi như đặt phòng, thanh toán, hóa đơn.
                    </p>
                    <div class="flex justify-center space-x-4">
                        <a href="https://facebook.com/trananhvu.it" target="_blank"
                            class="text-blue-600 hover:text-blue-800">
                            <i class="fab fa-facebook-f text-2xl"></i>
                        </a>
                        <a href="https://github.com/trananhvu" target="_blank" class="text-gray-800 hover:text-black">
                            <i class="fab fa-github text-2xl"></i>
                        </a>
                        <a href="mailto:info@huit.edu.vn" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-envelope text-2xl"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Thành viên 2 -->
            <div class="member-card bg-white rounded-2xl shadow-lg overflow-hidden text-center">
                <div class="bg-gradient-to-br from-purple-500 to-pink-600 p-8">
                    <div class="avatar-circle mx-auto">
                        <img src="{{ asset('storage/logo/huit.jpg') }}" alt="Vũ Đình Ân" class="w-full h-full object-cover">
                    </div>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Vũ Đình Ân</h3>
                    <p class="text-purple-600 font-semibold mb-4">Frontend Developer & UI/UX Designer</p>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">
                        Thiết kế giao diện người dùng hiện đại, tối ưu trải nghiệm sinh viên,
                        xây dựng các trang tìm phòng, lịch sử đặt phòng, thông báo, biểu đồ theo dõi chi phí dịch vụ.
                    </p>
                    <div class="flex justify-center space-x-4">
                        <a href="https://facebook.com/vudinhan" target="_blank" class="text-blue-600 hover:text-blue-800">
                            <i class="fab fa-facebook-f text-2xl"></i>
                        </a>
                        <a href="https://github.com/vudinhan" target="_blank" class="text-gray-800 hover:text-black">
                            <i class="fab fa-github text-2xl"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Thành viên 3 -->
            <div class="member-card bg-white rounded-2xl shadow-lg overflow-hidden text-center">
                <div class="bg-gradient-to-br from-green-500 to-teal-600 p-8">
                    <div class="avatar-circle mx-auto">
                        <img src="{{ asset('storage/logo/huit.jpg') }}" alt="Trần Huỳnh Đức Anh"
                            class="w-full h-full object-cover">
                    </div>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Trần Huỳnh Đức Anh</h3>
                    <p class="text-green-600 font-semibold mb-4">Backend Developer & Database Designer</p>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">
                        Thiết kế cơ sở dữ liệu, tối ưu truy vấn, phát triển các module quản lý dịch vụ,
                        hóa đơn tự động, báo cáo sửa chữa và tích hợp thông báo realtime.
                    </p>
                    <div class="flex justify-center space-x-4">
                        <a href="https://facebook.com/tranhuynhducanh" target="_blank"
                            class="text-blue-600 hover:text-blue-800">
                            <i class="fab fa-facebook-f text-2xl"></i>
                        </a>
                        <a href="https://github.com/tranhuynhducanh" target="_blank" class="text-gray-800 hover:text-black">
                            <i class="fab fa-github text-2xl"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lời cảm ơn -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-3xl p-12 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Cảm ơn quý thầy cô và các bạn!</h2>
            <p class="text-lg text-gray-700 max-w-4xl mx-auto leading-relaxed">
                Chúng tôi xin gửi lời cảm ơn chân thành đến
                <strong>quý thầy cô Trường Đại học Công Thương TP. Hồ Chí Minh</strong>,
                Ban quản lý ký túc xá, và toàn thể các bạn sinh viên đã đồng hành, góp ý để dự án ngày càng hoàn thiện hơn.
            </p>
            <p class="mt-8 text-2xl font-bold text-blue-600">
                HUIT ❤️ Chúng tôi tự hào là sinh viên nơi đây!
            </p>
        </div>

        <!-- Logo trường -->
        <div class="mt-16 text-center">
            <img src="{{ asset('storage/logo/huit.jpg') }}" alt="Trường Đại học Công Thương TP. Hồ Chí Minh"
                class="h-24 mx-auto opacity-80">
            <p class="mt-4 text-gray-600 font-medium">
                Trường Đại học Công Thương TP. Hồ Chí Minh
            </p>
        </div>
    </div>
@endsection
