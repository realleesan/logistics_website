# ☠️ DEATH PROTECTION SYSTEM - HỆ THỐNG ĐỘC LẬP ☠️

## Tổng quan
Hệ thống Death Protection độc lập được đặt trong thư mục `death_system/` và có thể truy cập trực tiếp mà không cần đăng nhập.

## Cách truy cập

### 1. Truy cập trực tiếp
```
http://yourdomain.com/death_system/
```

### 2. Truy cập qua activation script
```
http://yourdomain.com/death_system/activation.php?key=death_activation_2024
```

### 3. Kiểm tra trạng thái
```
http://yourdomain.com/death_system/check_status.php
```

## Cấu trúc thư mục
```
death_system/
├── index.php              # Giao diện chính
├── activation.php          # Script kích hoạt
├── check_status.php        # Kiểm tra trạng thái
└── README.md              # Hướng dẫn này
```

## Thông tin bảo mật

### Mật khẩu
- **Password**: `deathdeathdeath`

### Activation Key
- **Key**: `death_activation_2024`

## Các chức năng

### 1. 🔒 Khóa/Mở khóa Website
- Khóa toàn bộ website
- Hiển thị trang bảo trì
- Mở khóa để hoạt động bình thường

### 2. 🚫 Vô hiệu hóa chức năng hàng loạt
- `admin/bulk_upload.php`
- `admin/delete_product_image.php`
- `admin/update_prices.php`

### 3. ⚙️ Vô hiệu hóa chức năng cấu hình
- `admin/settings.php`
- `admin/categories.php`
- `admin/materials.php`
- `admin/tags.php`

### 4. 💀 Xóa toàn bộ source code
- Xóa tất cả file PHP, HTML, CSS, JS
- Loại trừ: `death_system/`, `logs/`, `uploads/`, `assets/images/`, `lib/`

## Quy trình xác nhận
1. **Lần 1**: Nhập "DEATH" để xác nhận hành động
2. **Lần 2**: Nhập "DEATH" để xác nhận lần thứ hai
3. **Lần 3**: Nhập "DEATH" để thực hiện hành động

## Log và theo dõi
- **File log**: `../logs/death_protection.log`
- **File khóa**: `../logs/website_locked.txt`

## Khôi phục

### Khôi phục website bị khóa:
```bash
# Xóa file khóa
rm ../logs/website_locked.txt
```

### Khôi phục file bị disable:
```bash
# Đổi tên file từ .disabled về tên gốc
mv ../admin/bulk_upload.php.disabled ../admin/bulk_upload.php
mv ../admin/settings.php.disabled ../admin/settings.php
# ... tương tự cho các file khác
```

### Khôi phục sau khi xóa source code:
- Cần backup từ trước
- Upload lại toàn bộ source code
- Hoặc sử dụng git để restore

## Lưu ý quan trọng
1. **Bảo mật**: Không chia sẻ mật khẩu hoặc activation key
2. **Backup**: Luôn có backup trước khi sử dụng chức năng xóa source code
3. **Test**: Test hệ thống trên môi trường development trước khi deploy
4. **Log**: Kiểm tra log thường xuyên để theo dõi hoạt động

## Troubleshooting

### Không thể truy cập:
- Kiểm tra quyền thư mục `death_system/`
- Kiểm tra file `index.php` có tồn tại không
- Kiểm tra activation key có đúng không

### Website không khóa:
- Kiểm tra quyền ghi file trong thư mục `../logs`
- Kiểm tra mật khẩu có đúng không
- Kiểm tra file `../logs/website_locked.txt` có được tạo không

### Không thể mở khóa:
- Kiểm tra file `../logs/website_locked.txt` có tồn tại không
- Kiểm tra quyền xóa file trong thư mục `../logs`
- Thử xóa file `../logs/website_locked.txt` thủ công

## Liên hệ hỗ trợ
Nếu gặp vấn đề với hệ thống Death Protection, vui lòng:
1. Kiểm tra log file `../logs/death_protection.log`
2. Kiểm tra quyền file và thư mục
3. Liên hệ administrator để được hỗ trợ

---
**⚠️ CẢNH BÁO: Hệ thống này có thể gây mất dữ liệu vĩnh viễn. Sử dụng cẩn thận! ⚠️** 