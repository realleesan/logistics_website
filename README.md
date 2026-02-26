# Vina Logistics Website

Website chính thức của Công ty TNHH Thương mại và Dịch vụ XNK Trường Vina - Vina Logistics.

## 🚀 Hướng dẫn Triển khai (Deploy)

### Yêu cầu hệ thống
- PHP 7.4 hoặc cao hơn
- MySQL 5.7 hoặc cao hơn
- Apache/Nginx web server
- SSL certificate (khuyến nghị)

### Bước 1: Chuẩn bị Hosting
1. Đăng ký hosting có hỗ trợ PHP và MySQL
2. Tạo database và user database
3. Cấu hình domain trỏ về hosting
4. **Cài đặt SSL certificate** (Let's Encrypt hoặc từ hosting provider)

### Bước 2: Upload Files
1. Upload toàn bộ file lên thư mục public_html (hoặc www)
2. Đảm bảo cấu trúc thư mục được giữ nguyên

### Bước 3: Cấu hình Environment
1. Copy file `env.example` thành `.env`:
```bash
cp env.example .env
```

2. Chỉnh sửa file `.env` với thông tin production:
```env
# Database Configuration
DB_HOST=localhost
DB_NAME=vina_logistics
DB_USER=your_db_user
DB_PASS=your_db_password

# SMTP Configuration
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your_email@gmail.com
SMTP_PASSWORD=your_app_password

# Application Settings
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### Bước 4: Cài đặt Dependencies
```bash
composer install
```

### Bước 5: Cấu hình Database
1. Tạo database trên hosting
2. Import file `database/database.sql` vào database

### Bước 6: Kích hoạt HTTPS
1. **Uncomment HTTPS redirect trong .htaccess:**
```apache
# Force HTTPS (uncomment when deploying to production)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

2. **Cập nhật domain trong SEO files:**
   - `robots.txt` - thay `yourdomain.com`
   - `sitemap.xml` - thay `yourdomain.com`

### Bước 7: Cấu hình Email
1. **Nếu dùng Gmail SMTP:**
   - Bật 2-Factor Authentication cho Gmail
   - Tạo App Password
   - Điền App Password vào `.env`

2. **Nếu dùng Hosting SMTP:**
   - Lấy thông tin SMTP từ hosting provider
   - Cập nhật trong `.env`

### Bước 8: Bảo mật
1. Thay đổi password admin trong `.env`
2. Đảm bảo file `.env` không public
3. Cấu hình firewall và security headers
4. Kiểm tra file permissions

### Bước 9: Kiểm tra
1. Test website hoạt động bình thường với HTTPS
2. Test form liên hệ gửi email
3. Test admin panel
4. Kiểm tra SEO và performance
5. Test responsive design

## 📁 Cấu trúc thư mục

```
truongvinalogistics/
├── admin/                 # Admin panel
├── assets/               # CSS, JS, Images
├── database/             # Database config và SQL
├── includes/             # PHP includes
├── lib/                  # PHPMailer library
├── logs/                 # Error logs
├── index.php            # Trang chủ
├── about.php            # Giới thiệu
├── services.php         # Dịch vụ
├── news.php             # Tin tức
├── contact.php          # Liên hệ
├── .htaccess           # URL rewriting & HTTPS
├── robots.txt          # SEO
├── sitemap.xml         # SEO
├── .env                # Environment variables
├── .gitignore          # Git ignore rules
├── composer.json       # Composer dependencies
└── README.md           # This file
```

## 🔧 Cấu hình quan trọng

### Database Configuration
- File: `database/config.php`
- Sử dụng `.env` để quản lý cấu hình
- Tự động detect localhost/production

### Email Configuration
- File: `.env`
- Cần điền App Password từ Gmail
- Hoặc cấu hình SMTP hosting

### Admin Panel
- URL: `https://yourdomain.com/admin/`
- Username: admin
- Password: vinalogistics2024 (cần thay đổi)

## 📧 Email Setup

### Gmail SMTP Setup
1. Đăng nhập Gmail
2. Vào Google Account > Security
3. Bật 2-Step Verification
4. Tạo App Password cho "Mail"
5. Sao chép 16-ký tự password
6. Dán vào `.env`

### Test Email
- Form liên hệ sẽ gửi email cho admin
- Email cảm ơn sẽ gửi cho khách hàng
- Kiểm tra spam folder nếu không nhận được

## 🔒 Bảo mật

### Files cần bảo vệ
- `.env` - chứa thông tin nhạy cảm
- `admin/config.php` - cấu hình admin
- `database/` - thông tin database

### Security Headers
- Đã cấu hình trong `.htaccess`
- X-Content-Type-Options
- X-Frame-Options
- X-XSS-Protection
- HTTPS redirect

## 📈 SEO Optimization

### Files SEO
- `robots.txt` - Hướng dẫn crawler
- `sitemap.xml` - Sitemap cho search engines
- Meta tags trong header
- Open Graph tags
- HTTPS protocol

### Cần cập nhật
- Domain trong `robots.txt`
- Domain trong `sitemap.xml`
- Open Graph image URL

## 🚨 Troubleshooting

### Lỗi Database Connection
- Kiểm tra thông tin database trong `.env`
- Đảm bảo database đã được tạo
- Kiểm tra user permissions

### Lỗi Email
- Kiểm tra SMTP configuration trong `.env`
- Đảm bảo App Password đúng
- Kiểm tra firewall/security settings

### Lỗi 404
- Kiểm tra .htaccess có được enable
- Đảm bảo mod_rewrite được bật
- Kiểm tra file permissions

### Lỗi HTTPS
- Đảm bảo SSL certificate đã cài đặt
- Kiểm tra HTTPS redirect trong .htaccess
- Test mixed content issues

## 📞 Support

Nếu gặp vấn đề, liên hệ:
- Email: lp.logistics.docs@gmail.com
- Phone: 0587.363636

## 📝 Changelog

### Version 1.1.0
- Added HTTPS support
- Environment variables management
- Enhanced security
- SEO optimization
- Production deployment guide

### Version 1.0.0
- Initial release
- Complete website functionality
- Admin panel
- Email system
- SEO optimization
- Mobile responsive design 