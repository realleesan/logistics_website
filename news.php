<?php
// Include database config
require_once 'database/config.php';

// Page meta information
$page_title = "Tin tức";
$page_description = "Cập nhật những tin tức mới nhất về dịch vụ vận chuyển, chính sách và hướng dẫn mua hàng từ Trung Quốc của VINA LOGISTICS.";
$page_keywords = getDynamicKeywords('news');

// Get category filter
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';
$current_category = null;

// Get categories
$categories = getNewsCategories();

// Find current category info
if ($category_filter) {
    foreach ($categories as $cat) {
        if ($cat['slug'] === $category_filter) {
            $current_category = $cat;
            $page_title = "Tin tức - " . $cat['name'];
            $page_description = $cat['description'] ?: $page_description;
            break;
        }
    }
}

// Pagination
$limit = 9; // Number of articles per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build WHERE clause for category filter
$where_clause = "WHERE n.status = 'published'";
$params = [];

if ($category_filter && $current_category) {
    $where_clause .= " AND n.category_id = :category_id";
    $params[':category_id'] = $current_category['id'];
}

// Get total number of articles
try {
    $count_sql = "SELECT COUNT(*) FROM news n " . $where_clause;
    $count_stmt = $pdo->prepare($count_sql);
    foreach ($params as $key => $value) {
        $count_stmt->bindValue($key, $value);
    }
    $count_stmt->execute();
    $total_articles = $count_stmt->fetchColumn();
    $total_pages = ceil($total_articles / $limit);
} catch(PDOException $e) {
    $total_articles = 0;
    $total_pages = 1;
}

// Get articles for current page
try {
    $news_sql = "
        SELECT n.*, nc.name as category_name, nc.slug as category_slug 
        FROM news n 
        LEFT JOIN news_categories nc ON n.category_id = nc.id 
        " . $where_clause . " 
        ORDER BY n.created_at DESC 
        LIMIT :limit OFFSET :offset
    ";
    $news_stmt = $pdo->prepare($news_sql);
    foreach ($params as $key => $value) {
        $news_stmt->bindValue($key, $value);
    }
    $news_stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $news_stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $news_stmt->execute();
    $news_articles = $news_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $news_articles = [];
}

// Include header
include 'includes/header.php';
?>

<!-- Breadcrumb -->
<section style="background: var(--bg-light); padding: 60px 0 40px; margin-top: 80px;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 style="color: var(--text-dark); margin-bottom: 15px;">
                    <?php if ($current_category): ?>
                        Tin tức - <?php echo htmlspecialchars($current_category['name']); ?>
                    <?php else: ?>
                        Tin tức
                    <?php endif; ?>
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 0;">
                        <li class="breadcrumb-item">
                            <a href="/" style="color: var(--text-light);">Trang chủ</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="tin-tuc" style="color: var(--text-light);">Tin tức</a>
                        </li>
                        <?php if ($current_category): ?>
                        <li class="breadcrumb-item active" style="color: var(--primary-color);">
                            <?php echo htmlspecialchars($current_category['name']); ?>
                        </li>
                        <?php else: ?>
                        <li class="breadcrumb-item active" style="color: var(--primary-color);">Tất cả</li>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- News Section -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>
                <?php if ($current_category): ?>
                    <?php echo htmlspecialchars($current_category['name']); ?>
                <?php else: ?>
                    Tin tức mới nhất
                <?php endif; ?>
            </h2>
            <p>
                <?php if ($current_category): ?>
                    <?php echo htmlspecialchars($current_category['description']); ?>
                <?php else: ?>
                    Cập nhật những thông tin mới nhất về dịch vụ, chính sách và hướng dẫn hữu ích
                <?php endif; ?>
            </p>
        </div>
        
        <!-- Category Filter -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="category-filter" style="background: white; padding: 20px; border-radius: 15px; box-shadow: var(--shadow); margin-bottom: 30px;">
                    <h5 style="color: var(--text-dark); margin-bottom: 15px;">
                        <i class="fas fa-filter" style="color: var(--primary-color); margin-right: 8px;"></i>
                        Lọc theo danh mục
                    </h5>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                        <a href="/tin-tuc" 
                           class="category-btn <?php echo !$category_filter ? 'active' : ''; ?>"
                           style="display: inline-block; padding: 8px 16px; border-radius: 20px; text-decoration: none; 
                                  font-size: 0.9rem; transition: all 0.3s ease; border: 2px solid var(--border-color);
                                  <?php echo !$category_filter ? 'background: var(--primary-color); color: var(--text-dark); border-color: var(--primary-color);' : 'background: white; color: var(--text-dark);'; ?>">
                            <i class="fas fa-th"></i> Tất cả
                        </a>
                        <?php foreach ($categories as $category): ?>
                        <a href="/tin-tuc?category=<?php echo urlencode($category['slug']); ?>" 
                           class="category-btn <?php echo $category_filter === $category['slug'] ? 'active' : ''; ?>"
                           style="display: inline-block; padding: 8px 16px; border-radius: 20px; text-decoration: none; 
                                  font-size: 0.9rem; transition: all 0.3s ease; border: 2px solid var(--border-color);
                                  <?php echo $category_filter === $category['slug'] ? 'background: var(--primary-color); color: var(--text-dark); border-color: var(--primary-color);' : 'background: white; color: var(--text-dark);'; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($news_articles)): ?>
            <div class="row">
                <?php foreach ($news_articles as $article): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="news-card">
                        <div class="news-image">
                            <?php
                                $candidateImage = !empty($article['image']) ? $article['image'] : '';
                                $serverPath = $candidateImage ? __DIR__ . '/' . ltrim($candidateImage, '/\\') : '';
                                $resolvedImage = ($candidateImage && file_exists($serverPath)) ? $candidateImage : 'assets/images/index9.png';
                            ?>
                            <img src="<?php echo asset_url($resolvedImage); ?>" 
                                 alt="<?php echo htmlspecialchars($article['title']); ?>"
                                 style="width: 100%; height: 200px; object-fit: cover;">
                        </div>
                        <div class="news-content">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <div class="news-date">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('d/m/Y', strtotime($article['created_at'])); ?>
                                </div>
                                <?php if (!empty($article['category_name'])): ?>
                                <span class="category-tag" style="background: var(--primary-color); color: var(--text-dark); 
                                                                   padding: 3px 8px; border-radius: 10px; font-size: 0.75rem; 
                                                                   font-weight: 600; text-transform: uppercase;">
                                    <?php echo htmlspecialchars($article['category_name']); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <h5 class="news-title">
                                <a href="/tin-tuc/<?php echo htmlspecialchars($article['slug']); ?>" 
                                   style="color: inherit; text-decoration: none;">
                                    <?php echo htmlspecialchars($article['title']); ?>
                                </a>
                            </h5>
                            <p class="news-excerpt">
                                <?php 
                                $excerpt = $article['excerpt'] ?: strip_tags(fix_relative_urls_in_html($article['content']));
                                echo htmlspecialchars(substr($excerpt, 0, 150)) . '...'; 
                                ?>
                            </p>
                            <a href="/tin-tuc/<?php echo htmlspecialchars($article['slug']); ?>" 
                               class="btn btn-outline btn-sm">
                                <i class="fas fa-arrow-right"></i> Đọc thêm
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <nav aria-label="News pagination" style="display: flex; justify-content: center;">
                        <ul class="pagination" style="display: flex; list-style: none; gap: 5px; margin: 0; padding: 0;">
                            <!-- Previous page -->
                            <?php if ($page > 1): ?>
                            <li style="margin: 0;">
                                <a href="/tin-tuc?<?php echo $category_filter ? 'category=' . urlencode($category_filter) . '&' : ''; ?>page=<?php echo $page - 1; ?>" 
                                   style="display: block; padding: 10px 15px; border: 2px solid var(--border-color); 
                                          border-radius: 8px; color: var(--text-dark); text-decoration: none; 
                                          transition: all 0.3s ease;">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <!-- Page numbers -->
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li style="margin: 0;">
                                <a href="/tin-tuc?<?php echo $category_filter ? 'category=' . urlencode($category_filter) . '&' : ''; ?>page=<?php echo $i; ?>" 
                                   style="display: block; padding: 10px 15px; border: 2px solid <?php echo $i == $page ? 'var(--primary-color)' : 'var(--border-color)'; ?>; 
                                          border-radius: 8px; background: <?php echo $i == $page ? 'var(--primary-color)' : 'transparent'; ?>; 
                                          color: <?php echo $i == $page ? 'var(--text-dark)' : 'var(--text-dark)'; ?>; 
                                          text-decoration: none; transition: all 0.3s ease; font-weight: <?php echo $i == $page ? '600' : '400'; ?>;">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <!-- Next page -->
                            <?php if ($page < $total_pages): ?>
                            <li style="margin: 0;">
                                <a href="/tin-tuc?<?php echo $category_filter ? 'category=' . urlencode($category_filter) . '&' : ''; ?>page=<?php echo $page + 1; ?>" 
                                   style="display: block; padding: 10px 15px; border: 2px solid var(--border-color); 
                                          border-radius: 8px; color: var(--text-dark); text-decoration: none; 
                                          transition: all 0.3s ease;">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    
                    <div style="text-align: center; margin-top: 20px; color: var(--text-light);">
                        Trang <?php echo $page; ?> / <?php echo $total_pages; ?> 
                        (<?php echo $total_articles; ?> bài viết)
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
        <?php else: ?>
            <!-- No articles found -->
            <div class="row">
                <div class="col-12">
                    <div style="text-align: center; padding: 80px 20px; background: var(--bg-light); border-radius: 15px;">
                        <i class="fas fa-newspaper" style="font-size: 4rem; color: var(--primary-color); margin-bottom: 20px;"></i>
                        <h3 style="color: var(--text-dark); margin-bottom: 15px;">Chưa có tin tức</h3>
                        <p style="color: var(--text-light); margin-bottom: 30px;">
                            Hiện tại chưa có bài viết nào được đăng tải. Vui lòng quay lại sau.
                        </p>
            <a href="/" class="btn btn-primary">
            <i class="fas fa-home"></i> Về trang chủ
        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Newsletter Subscription -->
<section class="section" style="background: var(--bg-light);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h3 style="color: var(--text-dark); margin-bottom: 20px;">
                    <i class="fas fa-bell" style="color: var(--primary-color); margin-right: 10px;"></i>
                    Đăng ký nhận tin
                </h3>
                <p style="color: var(--text-light); margin-bottom: 30px; font-size: 1.1rem;">
                    Đăng ký để nhận thông báo về những tin tức, ưu đãi và hướng dẫn mới nhất từ VINA LOGISTICS
                </p>
                
                <form style="background: white; padding: 20px; border-radius: 15px; box-shadow: var(--shadow); max-width: 500px; margin: 0 auto;">
                    <div style="display: flex; gap: 10px;">
                        <input type="email" 
                               placeholder="Nhập email của bạn..." 
                               required
                               style="flex: 1; padding: 15px; border: 2px solid var(--border-color); border-radius: 10px; 
                                      font-size: 1rem; transition: all 0.3s ease;"
                               onfocus="this.style.borderColor='var(--primary-color)'"
                               onblur="this.style.borderColor='var(--border-color)'">
                        <button type="submit" class="btn btn-primary" style="padding: 15px 25px;">
                            <i class="fas fa-paper-plane"></i> Đăng ký
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Related Services -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Có thể bạn quan tâm</h2>
            <p>Các dịch vụ nổi bật của chúng tôi</p>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: var(--shadow); text-align: center;">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); 
                                border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-shipping-fast" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <h5 style="color: var(--text-dark); margin-bottom: 15px;">Vận chuyển nhanh</h5>
                    <p style="color: var(--text-light); margin-bottom: 20px; font-size: 0.9rem;">
                        Dịch vụ vận chuyển express với thời gian giao hàng siêu nhanh
                    </p>
                    <a href="dich-vu" class="btn btn-outline btn-sm">Tìm hiểu thêm</a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: var(--shadow); text-align: center;">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); 
                                border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-shopping-cart" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <h5 style="color: var(--text-dark); margin-bottom: 15px;">Mua hàng Trung Quốc</h5>
                    <p style="color: var(--text-light); margin-bottom: 20px; font-size: 0.9rem;">
                        Hỗ trợ mua hộ từ Taobao, Tmall, 1688 với giá tốt nhất
                    </p>
                    <a href="dich-vu" class="btn btn-outline btn-sm">Tìm hiểu thêm</a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: var(--shadow); text-align: center;">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); 
                                border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-file-import" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <h5 style="color: var(--text-dark); margin-bottom: 15px;">Nhập khẩu ủy thác</h5>
                    <p style="color: var(--text-light); margin-bottom: 20px; font-size: 0.9rem;">
                        Dịch vụ nhập khẩu trọn gói với thủ tục nhanh gọn
                    </p>
                    <a href="dich-vu" class="btn btn-outline btn-sm">Tìm hiểu thêm</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?> 