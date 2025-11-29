# HỆ THỐNG QUẢN LÝ KÝ TÚC XÁ SINH VIÊN HUIT  
**Đồ án tốt nghiệp ngành Công nghệ Thông tin**  
**Trường Đại học Công Thương TP. Hồ Chí Minh (HUIT)**  

**Nhóm: KLCN_TH138**  
- Trần Anh Vũ  
- Vũ Đình Ân  
- Trần Huỳnh Đức Anh

---

### MÔ TẢ DỰ ÁN
Hệ thống quản lý ký túc xá hiện đại, thân thiện, giúp sinh viên dễ dàng tìm phòng, đặt phòng, thanh toán trực tuyến, báo sửa chữa, nhận thông báo realtime và quản lý toàn bộ hoạt động KTX một cách khoa học.

### TÍNH NĂNG NỔI BẬT
| Vai trò           | Tính năng chính |
|-------------------|----------------------------------------------------------|
| **Sinh viên**     | Tìm & xem phòng chi tiết, Đặt phòng, Thanh toán VNPay, Gia hạn/Chấm dứt hợp đồng, Báo sửa chữa, Nhận thông báo, Đánh giá phòng, Liên hệ hỗ trợ, Đăng nhập Google/FB/GitHub |
| **Quản trị viên / Nhân viên** | Quản lý chi nhánh – tầng – phòng, Quản lý sinh viên, Tạo hóa đơn tự động, Theo dõi thanh toán, Quản lý dịch vụ điện/nước, Xử lý yêu cầu sửa chữa, Gửi thông báo, Sao lưu dữ liệu, Báo cáo thống kê |
| **Hệ thống**      | Đa ngôn ngữ (Việt – Anh), Chatbot hỗ trợ 24/7, Responsive 100%, Giao diện hiện đại (Tailwind + Alpine.js) |

---

### YÊU CẦU HỆ THỐNG
- PHP ≥ 8.1
- Laravel 10.x hoặc 11.x
- MySQL 8.0 hoặc MariaDB
- Node.js & NPM (để compile assets)
- Composer
- VNPay Sandbox (nếu muốn test thanh toán)

---

### CÀI ĐẶT NHANH (5 PHÚT)

```bash
# 1. Clone dự án
git clone https://github.com/vuxjqk/ql-ktx.git
cd ql-ktx

# 2. Cài đặt dependencies
composer install
cp .env.example .env
php artisan key:generate

# 3. Cấu hình .env
# Sửa các thông tin sau:
DB_DATABASE=ql-ktx
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=no-reply@ktx.huit.edu.vn

# VNPay (nếu dùng)
VNPAY_TMN_CODE=...
VNPAY_HASH_SECRET=...
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html

# 4. Chạy migration + seeder
php artisan migrate --seed

# 5. Cài đặt frontend
npm install && npm run build

# 6. Chạy server
php artisan serve
# Truy cập: http://localhost:8000

TÀI KHOẢN MẪU (SAU KHI SEED)
Vai trò: Super Admin
Email: test@example.com
Mật khẩu: password
Ghi chú: Toàn quyền

/app/Http/Controllers/Student    → Controller sinh viên
/resources/views/student         → Giao diện sinh viên
/resources/views                 → Giao diện quản trị
/resources/views/auth            → Login, Register, Reset password
/resources/views/emails          → Template email
/database/seeders                → Dữ liệu mẫu