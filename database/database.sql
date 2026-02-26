-- Database schema cho Vina Logistics
CREATE DATABASE IF NOT EXISTS vina_logistics CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vina_logistics;

-- Bảng danh mục tin tức
CREATE TABLE news_categories (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(100) NOT NULL,
  slug varchar(100) NOT NULL UNIQUE,
  description text DEFAULT NULL,
  sort_order int(11) DEFAULT 0,
  status enum('active','inactive') DEFAULT 'active',
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_slug (slug),
  INDEX idx_status (status),
  INDEX idx_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng từ khóa động
CREATE TABLE keywords (
  id int(11) NOT NULL AUTO_INCREMENT,
  keyword varchar(255) NOT NULL,
  category enum('general','news','services','seo') DEFAULT 'general',
  priority int(11) DEFAULT 1,
  status enum('active','inactive') DEFAULT 'active',
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_category (category),
  INDEX idx_status (status),
  INDEX idx_priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng tin tức (cập nhật thêm category_id)
CREATE TABLE news (
  id int(11) NOT NULL AUTO_INCREMENT,
  title varchar(255) NOT NULL,
  slug varchar(255) NOT NULL UNIQUE,
  image varchar(255) DEFAULT NULL,
  content longtext NOT NULL,
  excerpt text DEFAULT NULL,
  category_id int(11) DEFAULT NULL,
  tags varchar(500) DEFAULT NULL,
  views int(11) DEFAULT 0,
  featured tinyint(1) DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  status enum('published','draft') DEFAULT 'published',
  PRIMARY KEY (id),
  INDEX idx_slug (slug),
  INDEX idx_status (status),
  INDEX idx_category (category_id),
  INDEX idx_featured (featured),
  INDEX idx_views (views),
  FOREIGN KEY (category_id) REFERENCES news_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng dịch vụ
CREATE TABLE services (
  id int(11) NOT NULL AUTO_INCREMENT,
  title varchar(255) NOT NULL,
  slug varchar(255) NOT NULL UNIQUE,
  description text NOT NULL,
  image varchar(255) DEFAULT NULL,
  icon varchar(255) DEFAULT NULL,
  sort_order int(11) DEFAULT 0,
  status enum('active','inactive') DEFAULT 'active',
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_slug (slug),
  INDEX idx_status (status),
  INDEX idx_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng liên hệ
CREATE TABLE contacts (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  phone varchar(20) DEFAULT NULL,
  subject varchar(255) DEFAULT NULL,
  message text NOT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status enum('new','read','replied') DEFAULT 'new',
  PRIMARY KEY (id),
  INDEX idx_status (status),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu cho bảng danh mục tin tức
INSERT INTO news_categories (name, slug, description, sort_order) VALUES
('Tin tức', 'tin-tuc', 'Những tin tức mới nhất về công ty và ngành logistics', 1),
('Hướng dẫn', 'huong-dan', 'Các hướng dẫn chi tiết về quy trình, cách thức sử dụng dịch vụ', 2),
('Chính sách', 'chinh-sach', 'Thông tin về các chính sách, quy định của công ty', 3),
('Khuyến mãi', 'khuyen-mai', 'Các chương trình ưu đãi, khuyến mãi đặc biệt', 4);

-- Dữ liệu mẫu cho bảng từ khóa
INSERT INTO keywords (keyword, category, priority) VALUES
('vận chuyển hàng hóa', 'general', 5),
('logistics Việt Nam', 'general', 5),
('nhập khẩu ủy thác', 'services', 4),
('mua hàng Trung Quốc', 'services', 4),
('vận chuyển xách tay', 'services', 4),
('Taobao', 'services', 3),
('1688', 'services', 3),
('Tmall', 'services', 3),
('vận chuyển đường biển', 'services', 3),
('vận chuyển đường bộ', 'services', 3),
('Vina Logistics', 'general', 5),
('dịch vụ logistics', 'general', 4),
('hướng dẫn mua hàng', 'news', 3),
('chính sách vận chuyển', 'news', 3),
('ưu đãi vận chuyển', 'news', 3);

-- Dữ liệu mẫu cho bảng dịch vụ
INSERT INTO services (title, slug, description, image, icon, sort_order) VALUES
('Vận chuyển đường bộ', 'van-chuyen-duong-bo', 'Dịch vụ vận chuyển nội địa và quốc tế bằng xe tải với mạng lưới rộng khắp.', 'assets/images/index5.png', 'fas fa-truck', 3),
('Nhập khẩu ủy thác', 'nhap-khau-uy-thac', 'Dịch vụ nhập khẩu ủy thác trọn gói từ Trung Quốc với thủ tục nhanh gọn.', 'assets/images/index6.png', 'fas fa-file-import', 5),
('Vận chuyển đường biển', 'van-chuyen-duong-bien', 'Vận chuyển hàng hóa bằng đường biển với chi phí tối ưu, phù hợp cho hàng hóa khối lượng lớn.', 'assets/images/index4.png', 'fas fa-ship', 2),
('Vận chuyển xách tay nhanh 2 chiều Trung Quốc - Việt Nam', 'van-chuyen-hang-khong', 'Dịch vụ vận chuyển xách tay nhanh 2 chiều Trung Quốc - Việt Nam nhanh chóng, an toàn và tiết kiệm thời gian cho khách hàng.', 'assets/images/index3.png', 'fas fa-plane', 1),
('Đặt hàng Trung Quốc và Thanh toán cho Nhà cung cấp', 'mua-hang-trung-quoc', 'Hỗ trợ mua hàng và thanh toán đa dạng theo yêu cầu của khách hàng.', 'assets/images/index7.png', 'fas fa-shopping-cart', 6);

-- Cập nhật dữ liệu mẫu cho bảng tin tức với category_id
INSERT INTO news (title, slug, image, content, excerpt, category_id, tags) VALUES
('Khai trương dịch vụ vận chuyển express mới', 'khai-truong-dich-vu-van-chuyen-express-moi', 'assets/images/index9.png',
'<p>Vina Logistics vui mừng thông báo khai trương dịch vụ vận chuyển express mới với thời gian giao hàng cực nhanh từ Trung Quốc về Việt Nam chỉ trong 3-5 ngày làm việc.</p><p>Dịch vụ này được thiết kế đặc biệt cho các khách hàng có nhu cầu giao hàng gấp, đảm bảo hàng hóa được vận chuyển an toàn và nhanh chóng.</p>', 
'Vina Logistics khai trương dịch vụ vận chuyển express mới với thời gian giao hàng siêu nhanh chỉ 3-5 ngày từ Trung Quốc.', 1, 'express,vận chuyển nhanh,dịch vụ mới'),

('Ưu đãi đặc biệt tháng 12 - Giảm 20% phí vận chuyển', 'uu-dai-dac-biet-thang-12-giam-20-phi-van-chuyen', 'assets/images/index10.png',
'<p>Nhân dịp cuối năm, Vina Logistics triển khai chương trình ưu đãi đặc biệt dành cho tất cả khách hàng với mức giảm giá lên đến 20% phí vận chuyển.</p><p>Chương trình áp dụng cho tất cả các tuyến vận chuyển từ Trung Quốc về Việt Nam, thời gian áp dụng từ 01/12/2024 đến 31/12/2024.</p>',
'Chương trình ưu đãi cuối năm với mức giảm giá lên đến 20% phí vận chuyển cho tất cả dịch vụ.', 4, 'khuyến mãi,ưu đãi,giảm giá'),

('Hướng dẫn mua hàng Taobao cho người mới bắt đầu', 'huong-dan-mua-hang-taobao-cho-nguoi-moi-bat-dau', 'assets/images/index11.png',
'<p>Taobao là một trong những sàn thương mại điện tử lớn nhất Trung Quốc với hàng triệu sản phẩm đa dạng và giá cả hấp dẫn.</p><p>Vina Logistics hướng dẫn chi tiết cách đặt hàng Taobao từ A-Z, từ việc tìm kiếm sản phẩm, đàm phán giá cả đến vận chuyển về Việt Nam.</p>',
'Hướng dẫn chi tiết cách mua hàng trên Taobao từ A-Z dành cho người mới bắt đầu kinh doanh online.', 2, 'hướng dẫn,taobao,mua hàng trung quốc'),

('Chính sách bảo hiểm hàng hóa mới 2024', 'chinh-sach-bao-hiem-hang-hoa-moi-2024', 'assets/images/index12.png',
'<p>Để đảm bảo quyền lợi tốt nhất cho khách hàng, Vina Logistics áp dụng chính sách bảo hiểm hàng hóa mới từ tháng 1/2024.</p><p>Chính sách này bao gồm bảo hiểm 100% giá trị hàng hóa trong quá trình vận chuyển với mức phí bảo hiểm chỉ 0.5% giá trị hàng hóa.</p>',
'Chính sách bảo hiểm hàng hóa mới 2024 với mức bảo hiểm 100% giá trị hàng hóa.', 3, 'chính sách,bảo hiểm,hàng hóa'),

('Hướng dẫn đặt hàng 1688.com hiệu quả nhất', 'huong-dan-dat-hang-1688-hieu-qua-nhat', 'assets/images/index5.png',
'<p>1688.com là sàn thương mại điện tử B2B hàng đầu Trung Quốc với giá sỉ cực kỳ hấp dẫn.</p><p>Bài viết này sẽ hướng dẫn cách tìm kiếm nhà cung cấp uy tín, đàm phán giá cả và đặt hàng số lượng lớn để tối ưu chi phí.</p><p>Vina Logistics hỗ trợ toàn bộ quy trình từ đặt hàng đến nhận hàng tại Việt Nam.</p>',
'Hướng dẫn đặt hàng 1688.com từ A-Z, tìm nhà cung cấp uy tín và tối ưu chi phí nhập hàng.', 2, 'hướng dẫn,1688,nhập hàng sỉ'),

('Thông báo cập nhật bảng giá vận chuyển mới', 'thong-bao-cap-nhat-bang-gia-van-chuyen-moi', 'assets/images/index6.png',
'<p>Kính gửi quý khách hàng, Vina Logistics thông báo cập nhật bảng giá vận chuyển mới áp dụng từ ngày 15/01/2024.</p><p>Bảng giá mới được điều chỉnh phù hợp với tình hình thị trường và nhằm mang lại dịch vụ tốt nhất cho khách hàng.</p><p>Chi tiết bảng giá xin liên hệ hotline: 0123.456.789</p>',
'Thông báo cập nhật bảng giá vận chuyển mới từ ngày 15/01/2024, chi tiết liên hệ hotline.', 3, 'thông báo,bảng giá,chính sách'),

('Kinh nghiệm mua hàng Tmall an toàn và tiết kiệm', 'kinh-nghiem-mua-hang-tmall-an-toan-va-tiet-kiem', 'assets/images/index7.png',
'<p>Tmall là sàn thương mại điện tử cao cấp của Alibaba với các thương hiệu chính hãng và chất lượng đảm bảo.</p><p>Bài viết chia sẻ những kinh nghiệm thực tế trong việc mua hàng Tmall, cách nhận biết hàng chính hãng và tránh các rủi ro khi mua sắm online.</p>',
'Chia sẻ kinh nghiệm mua hàng Tmall an toàn, cách nhận biết hàng chính hãng và tránh rủi ro.', 2, 'kinh nghiệm,tmall,mua hàng an toàn'),

('Chương trình khuyến mãi Tết Nguyên Đán 2024', 'chuong-trinh-khuyen-mai-tet-nguyen-dan-2024', 'assets/images/index8.png',
'<p>Chào đón Xuân Giáp Thìn 2024, Vina Logistics triển khai chương trình khuyến mãi đặc biệt dành cho khách hàng.</p><p>Ưu đãi lên đến 30% phí vận chuyển cho đơn hàng đầu tiên, tặng kèm dịch vụ đóng gói chuyên nghiệp miễn phí.</p><p>Thời gian áp dụng từ 01/02/2024 đến 29/02/2024.</p>',
'Chương trình khuyến mãi Tết 2024 với ưu đãi lên đến 30% phí vận chuyển và nhiều quà tặng hấp dẫn.', 4, 'tết 2024,khuyến mãi,ưu đãi tết'),

('Hướng dẫn khai báo hải quan hàng nhập khẩu', 'huong-dan-khai-bao-hai-quan-hang-nhap-khau', 'assets/images/index9.png',
'<p>Quy trình khai báo hải quan là bước quan trọng trong việc nhập khẩu hàng hóa từ nước ngoài về Việt Nam.</p><p>Vina Logistics hướng dẫn chi tiết các thủ tục cần thiết, giấy tờ chuẩn bị và cách tính thuế nhập khẩu để khách hàng có thể chủ động trong việc nhập khẩu.</p>',
'Hướng dẫn đầy đủ quy trình khai báo hải quan, thủ tục và cách tính thuế nhập khẩu.', 2, 'hướng dẫn,hải quan,nhập khẩu,thủ tục'),

('Chính sách đổi trả hàng hóa mới', 'chinh-sach-doi-tra-hang-hoa-moi', 'assets/images/index10.png',
'<p>Nhằm nâng cao chất lượng dịch vụ và đảm bảo quyền lợi khách hàng, Vina Logistics áp dụng chính sách đổi trả hàng hóa mới.</p><p>Khách hàng có thể đổi trả hàng trong vòng 7 ngày kể từ khi nhận hàng với điều kiện hàng hóa còn nguyên vẹn.</p><p>Chi phí vận chuyển đổi trả sẽ được Vina Logistics hỗ trợ 50%.</p>',
'Chính sách đổi trả mới cho phép khách hàng đổi trả trong 7 ngày với hỗ trợ 50% phí vận chuyển.', 3, 'chính sách,đổi trả,quyền lợi khách hàng'),

('Dịch vụ vận chuyển COD toàn quốc', 'dich-vu-van-chuyen-cod-toan-quoc', 'assets/images/index11.png',
'<p>Vina Logistics mở rộng dịch vụ COD (Cash on Delivery) ra toàn quốc, giúp người bán hàng online dễ dàng tiếp cận khách hàng khắp Việt Nam.</p><p>Dịch vụ COD với phí hợp lý, thu hộ nhanh chóng và báo cáo chi tiết giúp shop online phát triển kinh doanh hiệu quả.</p>',
'Dịch vụ COD toàn quốc mới giúp shop online mở rộng thị trường với phí hợp lý và thu hộ nhanh.', 1, 'COD,vận chuyển toàn quốc,thu hộ'),

('Khuyến mãi mùa hè - Miễn phí đóng gói', 'khuyen-mai-mua-he-mien-phi-dong-goi', 'assets/images/index12.png',
'<p>Chào mừng mùa hè 2024, Vina Logistics triển khai chương trình khuyến mãi "Mùa hè rực rỡ" với nhiều ưu đãi hấp dẫn.</p><p>Miễn phí dịch vụ đóng gói chuyên nghiệp cho tất cả đơn hàng từ 500.000 VNĐ, tặng kèm bảo hiểm hàng hóa miễn phí.</p><p>Áp dụng từ 01/06/2024 đến 31/08/2024.</p>',
'Khuyến mãi mùa hè với miễn phí đóng gói và bảo hiểm cho đơn hàng từ 500k, áp dụng cả hè.', 4, 'mùa hè 2024,miễn phí đóng gói,bảo hiểm miễn phí'); 