# 👑 HIEU CEO - Master Website Interface & Theme Hub

> **Hệ Thống Quản Lý Giao Diện Website & Trình Tùy Biến Trực Quan Chuẩn CEO**  
> Xây dựng với kiến trúc hiện đại bằng **PHP 8.2+** và **MySQL**, tích hợp thư mục lưu trữ tập trung `projects/`, tính năng tải lên tệp nén `.ZIP` dự án tự động giải nén & đăng ký hiển thị, công nghệ kính mờ Glassmorphism, chuyển động Animations siêu mượt 60FPS, và bộ giả lập thiết bị 3-in-1 (Desktop iMac, iPad Pro, iPhone 15 Pro).

---

## 🌟 1. Tổng Quan Dự Án & Tính Năng Nổi Bật

**HIEU CEO Theme Hub** là giải pháp phần mềm quản lý và tùy biến giao diện website được phát triển dành riêng cho Ban Điều Hành (CEO / CDO / Giám đốc Công nghệ). Hệ thống cho phép:

1. **Thư mục lưu trữ dự án tập trung (`projects/`)**:
   - Tất cả các website/giao diện được tổ chức gọn gàng trong thư mục `c:\Users\tranv\Desktop\DoAnWebsite\projects\`.
   - Bao gồm sẵn các bộ dự án: `HieuWeb01` (Thời trang), `HieuWeb02` (Sách), `HieuWeb03` (Nội thất), `HieuWeb04` (Công nghệ), `HieuWeb05` (Gym/Fitness), `HieuCyberPortfolio` (Tech SaaS).
2. **Tính năng tải lên & Tự động hiển thị dự án (`admin/project-upload.php`)**:
   - Hỗ trợ kéo-thả tệp nén `.ZIP` của bất kỳ website nào (PHP, HTML5/CSS3/JS, Bootstrap, Tailwind).
   - Tự động giải nén an toàn vào `projects/<ten_du_an>/`.
   - Tự động phát hiện tệp entry (`index.php` hoặc `index.html`), đọc tiêu đề từ `README.md`.
   - Đăng ký tự động vào cơ sở dữ liệu `themes` và hiển thị tức thì trên Trang chủ CEO Portal và Bảng điều khiển Admin.
3. **Quản lý thư mục dự án chuyên sâu (`admin/projects.php`)**:
   - Xem toàn bộ danh sách thư mục trong `projects/`, dung lượng disk size, số lượng tập tin.
   - Trạng thái hiển thị (Đã đăng ký vs Chưa đăng ký), nút 1-Click "Đăng Ký Lên Trang Chủ", "Xem Thử" và "Xóa Thư Mục".
4. **Trình giả lập thiết bị đa nền tảng (`theme-preview.php`)**:
   - Mô phỏng chân thực giao diện trên màn hình Desktop Studio Display (1280px), iPad Pro (768px), và iPhone 15 Pro (390px với Dynamic Island) kèm chế độ xoay ngang/dọc và phóng thu tỉ lệ.
5. **Trình tùy biến màu sắc & Section trực quan (`customizer.php`)**:
   - Tinh chỉnh bảng màu thương hiệu (Primary, Secondary, Accent, Background), thay đổi Typography (Outfit, Plus Jakarta Sans, Montserrat, Cinzel), độ bo góc viền và bật/tắt các Section trong thời gian thực (Real-time Live Sync).
6. **Phân hệ Thống kê & A/B Testing CEO (`admin/analytics.php`)**:
   - Biểu đồ đo lường chỉ số chuyển đổi (Conversion Rate), lưu lượng truy cập (Pageviews), phân bổ thiết bị người dùng và tốc độ tải trang Core Web Vitals (LCP, FID, CLS).
7. **Kho linh kiện UI Kit sang trọng (`admin/components.php`)**:
   - Thư viện nút bấm Neon phát sáng, thẻ 3D Tilt xoay theo con trỏ chuột, thẻ thống kê nhấp nháy LED, thanh điều hướng kính nổi sẵn sàng 1-click Copy Code.

---

## 🔐 2. Danh Sách Tài Khoản Điều Hành Mẫu

| Phân Quyền | Tên Người Dùng | Email Đăng Nhập | Mật Khẩu Mặc Định | Thẩm Quyền |
| :--- | :--- | :--- | :--- | :--- |
| 👑 **CEO (Chief Executive Officer)** | **HIEU TRAN** | `ceo@hieu.vn` | `admin123` | Toàn quyền kiểm soát hệ thống, kích hoạt theme, phân quyền |
| 🎨 **CDO (Chief Design Officer)** | **Elena Vance** | `cdo@hieu.vn` | `admin123` | Quản lý giao diện, tùy biến màu sắc/font, UI Kit |
| 💻 **Lead Architect** | **Alex Thorne** | `dev@hieu.vn` | `admin123` | Tùy biến mã CSS/JS, quản lý Section, dọn dẹp cache |
| 👁️ **VIP Observer** | **Khách Thăm Quan** | `guest@hieu.vn` | `admin123` | Trải nghiệm xem trước và đánh giá giao diện |

---

## 🚀 3. Hướng Dẫn Cài Đặt & Khởi Chạy

### Bước 1: Khởi động XAMPP
- Mở **XAMPP Control Panel** và bật **Apache** và **MySQL**.

### Bước 2: Khởi tạo Cơ Sở Dữ Liệu Tự Động
- Chạy lệnh khởi tạo cơ sở dữ liệu `hieu_ceo_db` với 10 bảng và dữ liệu mẫu đầy đủ:
```bash
php database/init_database.php
```

### Bước 3: Chạy Kiểm Thử Hệ Thống Nghiêm Ngặt
- Chạy bộ kiểm thử tự động toàn diện kiểm tra kết nối DB, cú pháp PHP, API và phân quyền:
```bash
php test_system.php
```

### Bước 4: Khởi Động Máy Chủ Web
- Mở Terminal tại thư mục gốc dự án và chạy:
```bash
php -S 127.0.0.1:8099
```

### Bước 5: Trải Nghiệm Ứng Dụng
- **Cổng Khám Phá Website Đã Đăng Tải (`explore.php`)**: [http://127.0.0.1:8099/explore.php](http://127.0.0.1:8099/explore.php)
- **Trang chủ CEO Portal & Showcase**: [http://127.0.0.1:8099/index.php](http://127.0.0.1:8099/index.php)
- **Kho Thư Mục Dự Án (`projects/`)**: [http://127.0.0.1:8099/admin/projects.php](http://127.0.0.1:8099/admin/projects.php)
- **Tải Lên Dự Án Mới (.ZIP)**: [http://127.0.0.1:8099/admin/project-upload.php](http://127.0.0.1:8099/admin/project-upload.php)
- **Bảng điều khiển CEO Admin**: [http://127.0.0.1:8099/admin/index.php](http://127.0.0.1:8099/admin/index.php)
- **Trình giả lập thiết bị (Simulator)**: [http://127.0.0.1:8099/theme-preview.php?theme_id=1](http://127.0.0.1:8099/theme-preview.php?theme_id=1)
- **Trình tùy biến giao diện trực quan**: [http://127.0.0.1:8099/customizer.php?theme_id=1](http://127.0.0.1:8099/customizer.php?theme_id=1)

---

## 📂 4. Sơ Đồ Cấu Trúc Thư Mục Dự Án

```
c:\Users\tranv\Desktop\DoAnWebsite\
├── projects/                          # 📁 Thư mục lưu trữ tập trung các dự án website
│   ├── HieuWeb01/                     # Thời trang & May mặc
│   ├── HieuWeb02/                     # Sách & Thư viện số
│   ├── HieuWeb03/                     # Nội thất Bắc Âu
│   ├── HieuWeb04/                     # Công nghệ & AI
│   ├── HieuWeb05/                     # Gym & Thể hình Elite
│   └── ...                            # Các dự án tải lên tự động
├── .antigravityrules                  # Quy tắc tùy biến UI/UX
├── README.md                          # Tài liệu hướng dẫn toàn diện
├── index.php                          # Master CEO Portal & Live Showcase
├── login.php                          # Cổng đăng nhập xác thực CEO & Ban Điều Hành
├── logout.php                         # Xử lý đăng xuất an toàn
├── theme-preview.php                  # Trình giả lập thiết bị (Desktop, Tablet, Mobile)
├── customizer.php                     # Trình tùy biến màu sắc, font chữ & Section trực quan
├── test_system.php                    # Bộ kiểm thử tự động toàn diện
├── config/
│   ├── database.php                   # Kết nối cơ sở dữ liệu PDO an toàn
│   ├── helper.php                     # Hàm bảo mật, Quản lý projects/, Zip & Helpers
│   ├── auth_admin.php                 # 🛡️ Quản lý Quyền Truy Cập Quản Trị & Ban Điều Hành (Admin Guard)
│   └── auth_user.php                  # 👤 Quản lý Quyền Truy Cập Người Dùng & Khách Hàng (User Guard)
├── user-login.php                     # Cổng đăng nhập dành riêng cho Thành viên & Khách hàng
├── database/
│   ├── schema.sql                     # Cấu trúc 10 bảng CSDL MySQL
│   ├── seed.sql                       # Dữ liệu mẫu chuẩn CEO
│   └── init_database.php              # Script CLI tự động nạp và kiểm tra CSDL
├── api/
│   ├── themes.php                     # REST API quản lý & kích hoạt theme tức thì
│   ├── customize.php                  # REST API lưu cấu hình tùy biến
│   ├── analytics.php                  # REST API cấp dữ liệu biểu đồ phân tích
│   ├── system.php                     # REST API xóa cache, sức khỏe hệ thống & sao lưu
│   └── upload_project.php             # REST API tải lên, giải nén ZIP & đăng ký dự án
├── admin/
│   ├── index.php                      # Bảng điều khiển CEO Executive Dashboard & KPI
│   ├── themes.php                     # Quản lý danh sách giao diện (CRUD, Activate, Clone)
│   ├── projects.php                   # Quản lý kho thư mục dự án projects/
│   ├── project-upload.php             # Giao diện kéo thả tải lên dự án (.ZIP)
│   ├── theme-add.php                  # Thêm mới giao diện website
│   ├── theme-edit.php                 # Chỉnh sửa thông số giao diện
│   ├── components.php                 # Thư viện UI Kit & Copy Code
│   ├── analytics.php                  # Phân tích chuyên sâu & so sánh A/B Testing
│   ├── users.php                      # Quản lý phân quyền ban điều hành RBAC
│   ├── logs.php                       # Nhật ký kiểm toán hệ thống
│   └── settings.php                   # Cài đặt toàn cục, Dọn cache & Backup CSDL
└── assets/
    ├── css/
    │   ├── ceo-core.css               # Design Tokens, Glassmorphism, Luxury Dark/Light
    │   ├── animations.css             # Chuyển động 60FPS, Keyframes, Micro-interactions
    │   └── preview.css                # Khung mô phỏng thiết bị iPhone, iPad, iMac
    └── js/
        ├── ceo-app.js                 # Engine tương tác, Toast, Modal, 3D Tilt, Counter
        ├── customizer.js              # Real-time CSS Variable Injector & Sync
        └── preview-simulator.js       # Bộ điều khiển chuyển đổi kích thước khung xem
├── push.bat                       # Tự động hóa Git 1-Click: add, commit "update", push
└── push.ps1                       # Kịch bản PowerShell tự động hóa đẩy mã nguồn lên GitHub
```

---

*Phát triển và hoàn thiện bởi Chuyên gia Thiết kế Website Chuẩn CEO.*
