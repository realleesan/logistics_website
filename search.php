<?php
// Include database config
require_once 'database/config.php';

// Get search query (robustly handle URL-encoded path segments like /tim-kiem/<term>)
function decode_search_query($value) {
    if (!is_string($value) || $value === '') {
        return '';
    }
    // Decode repeatedly to handle double-encoding from some rewrite rules
    $prev = $value;
    for ($i = 0; $i < 3; $i++) {
        $decoded = urldecode($prev);
        if ($decoded === $prev) {
            break;
        }
        $prev = $decoded;
    }
    return trim($prev);
}

$search_query = isset($_GET['q']) ? decode_search_query($_GET['q']) : '';

// Fallback: extract query from pretty URL like /tim-kiem/<term>
if ($search_query === '') {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    if (is_string($requestUri) && $requestUri !== '') {
        // Remove query string
        $pathOnly = explode('?', $requestUri, 2)[0];
        // Try to match /tim-kiem/<term> or /tim-kiem/<term>/
        if (preg_match('#/tim-kiem/([^/]+)/*$#u', $pathOnly, $m)) {
            $search_query = decode_search_query($m[1]);
        }
    }
}
$search_results = [];
$total_results = 0;

// Page meta information
$page_title = "Kết quả tìm kiếm";
if ($search_query) {
    $page_title = "Kết quả tìm kiếm: \"" . htmlspecialchars($search_query) . "\"";
}
$page_description = "Tìm kiếm thông tin về dịch vụ vận chuyển, tin tức và hướng dẫn tại VINA LOGISTICS.";
$page_keywords = getDynamicKeywords('news') . ', tìm kiếm, search';

// Perform search if query exists
if (!empty($search_query)) {
    $search_results = searchContent($search_query, 50);
    $total_results = count($search_results);
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
                    <?php if ($search_query): ?>
                        Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($search_query); ?>"
                    <?php else: ?>
                        Tìm kiếm
                    <?php endif; ?>
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 0;">
                        <li class="breadcrumb-item">
                                                            <a href="/" style="color: var(--text-light);">Trang chủ</a>
                        </li>
                        <li class="breadcrumb-item active" style="color: var(--primary-color);">Tìm kiếm</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="section">
    <div class="container">
        <!-- Search Form -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="search-main-form" style="background: white; padding: 30px; border-radius: 15px; box-shadow: var(--shadow);">
                    <h3 style="color: var(--text-dark); margin-bottom: 20px;">
                        <i class="fas fa-search" style="color: var(--primary-color); margin-right: 10px;"></i>
                        Tìm kiếm thông tin
                    </h3>
                    <form action="search.php" method="GET">
                        <div style="display: flex; gap: 15px; align-items: center;">
                            <input type="text" 
                                   name="q" 
                                   placeholder="Nhập từ khóa tìm kiếm..." 
                                   value="<?php echo htmlspecialchars($search_query); ?>"
                                   style="flex: 1; padding: 15px 20px; border: 2px solid var(--border-color); 
                                          border-radius: 10px; font-size: 1rem; transition: all 0.3s ease;"
                                   onfocus="this.style.borderColor='var(--primary-color)'"
                                   onblur="this.style.borderColor='var(--border-color)'"
                                   required>
                            <button type="submit" class="btn btn-primary" style="padding: 15px 30px;">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </form>
                    
                    <!-- Quick search suggestions -->
                    <div style="margin-top: 20px;">
                        <p style="color: var(--text-light); margin-bottom: 10px; font-size: 0.9rem;">Tìm kiếm phổ biến:</p>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <a href="search.php?q=vận+chuyển+nhanh" class="search-tag">Vận chuyển nhanh</a>
                            <a href="search.php?q=mua+hàng+taobao" class="search-tag">Mua hàng Taobao</a>
                            <a href="search.php?q=nhập+khẩu+ủy+thác" class="search-tag">Nhập khẩu ủy thác</a>
                            <a href="search.php?q=hướng+dẫn" class="search-tag">Hướng dẫn</a>
                            <a href="search.php?q=khuyến+mãi" class="search-tag">Khuyến mãi</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($search_query): ?>
            <!-- Search Results -->
            <div class="row">
                <div class="col-12">
                    <div style="margin-bottom: 30px;">
                        <h4 style="color: var(--text-dark); margin-bottom: 10px;">
                            Kết quả tìm kiếm
                        </h4>
                        <p style="color: var(--text-light);">
                            Tìm thấy <strong><?php echo $total_results; ?></strong> kết quả cho từ khóa 
                            "<strong><?php echo htmlspecialchars($search_query); ?></strong>"
                        </p>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($search_results)): ?>
                <div class="row">
                    <?php foreach ($search_results as $result): ?>
                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="search-result-item" style="background: white; padding: 20px; border-radius: 12px; 
                                                               box-shadow: var(--shadow); border-left: 4px solid var(--primary-color); 
                                                               transition: all 0.3s ease;">
                            <div style="display: flex; align-items: flex-start; gap: 15px;">
                                <?php if (!empty($result['image'])): ?>
                                <div style="flex-shrink: 0;">
                                    <img src="<?php echo htmlspecialchars($result['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($result['title']); ?>"
                                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                </div>
                                <?php endif; ?>
                                
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                        <i class="<?php echo getContentIcon($result['type']); ?>" 
                                           style="color: var(--primary-color); font-size: 0.9rem;"></i>
                                        <span style="background: var(--primary-color); color: var(--text-dark); 
                                                     padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; 
                                                     text-transform: uppercase; font-weight: 600;">
                                            <?php echo $result['type'] == 'news' ? 'Tin tức' : 'Dịch vụ'; ?>
                                        </span>
                                        <?php if (!empty($result['category_name'])): ?>
                                        <span style="color: var(--text-light); font-size: 0.8rem;">
                                            <?php echo htmlspecialchars($result['category_name']); ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h5 style="margin-bottom: 8px;">
                                        <a href="<?php echo getContentUrl($result['type'], $result['slug']); ?>" 
                                           style="color: var(--text-dark); text-decoration: none; line-height: 1.3;">
                                            <?php echo highlightSearchTerm(htmlspecialchars($result['title']), $search_query); ?>
                                        </a>
                                    </h5>
                                    
                                    <p style="color: var(--text-light); font-size: 0.9rem; margin-bottom: 10px; line-height: 1.4;">
                                        <?php 
                                        $excerpt = createExcerpt($result['excerpt'], 120);
                                        echo highlightSearchTerm(htmlspecialchars($excerpt), $search_query); 
                                        ?>
                                    </p>
                                    
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="color: var(--text-light); font-size: 0.8rem;">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo formatVietnameseDate($result['created_at']); ?>
                                        </span>
                                        <a href="<?php echo getContentUrl($result['type'], $result['slug']); ?>" 
                                           class="btn btn-outline btn-sm">
                                            Xem chi tiết <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- No results found -->
                <div class="row">
                    <div class="col-12">
                        <div style="text-align: center; padding: 60px 20px; background: var(--bg-light); border-radius: 15px;">
                            <i class="fas fa-search" style="font-size: 4rem; color: var(--primary-color); margin-bottom: 20px; opacity: 0.7;"></i>
                            <h3 style="color: var(--text-dark); margin-bottom: 15px;">Không tìm thấy kết quả</h3>
                            <p style="color: var(--text-light); margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto;">
                                Không tìm thấy kết quả nào cho từ khóa "<strong><?php echo htmlspecialchars($search_query); ?></strong>". 
                                Vui lòng thử lại với từ khóa khác hoặc xem các gợi ý bên dưới.
                            </p>
                            
                            <div style="margin-bottom: 30px;">
                                <h5 style="color: var(--text-dark); margin-bottom: 15px;">Gợi ý tìm kiếm:</h5>
                                <ul style="list-style: none; color: var(--text-light); text-align: left; max-width: 400px; margin: 0 auto;">
                                    <li style="margin-bottom: 5px;">• Sử dụng từ khóa ngắn gọn hơn</li>
                                    <li style="margin-bottom: 5px;">• Kiểm tra chính tả</li>
                                    <li style="margin-bottom: 5px;">• Thử các từ đồng nghĩa</li>
                                    <li style="margin-bottom: 5px;">• Tìm kiếm theo danh mục cụ thể</li>
                                </ul>
                            </div>
                            
                            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                                <a href="news.php" class="btn btn-primary">
                                    <i class="fas fa-newspaper"></i> Xem tin tức
                                </a>
                                <a href="services.php" class="btn btn-outline">
                                    <i class="fas fa-truck"></i> Xem dịch vụ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Search Tips -->
<section class="section" style="background: var(--bg-light);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h3 style="color: var(--text-dark); margin-bottom: 20px;">
                    <i class="fas fa-lightbulb" style="color: var(--primary-color); margin-right: 10px;"></i>
                    Mẹo tìm kiếm hiệu quả
                </h3>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div style="background: white; padding: 20px; border-radius: 10px; height: 100%;">
                            <i class="fas fa-search-plus" style="color: var(--primary-color); font-size: 2rem; margin-bottom: 15px;"></i>
                            <h6 style="color: var(--text-dark); margin-bottom: 10px;">Từ khóa cụ thể</h6>
                            <p style="color: var(--text-light); font-size: 0.9rem;">Sử dụng từ khóa cụ thể như "vận chuyển express", "mua hàng Taobao"</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div style="background: white; padding: 20px; border-radius: 10px; height: 100%;">
                            <i class="fas fa-quote-left" style="color: var(--primary-color); font-size: 2rem; margin-bottom: 15px;"></i>
                            <h6 style="color: var(--text-dark); margin-bottom: 10px;">Tìm cụm từ</h6>
                            <p style="color: var(--text-light); font-size: 0.9rem;">Đặt cụm từ trong dấu ngoặc kép để tìm chính xác</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div style="background: white; padding: 20px; border-radius: 10px; height: 100%;">
                            <i class="fas fa-filter" style="color: var(--primary-color); font-size: 2rem; margin-bottom: 15px;"></i>
                            <h6 style="color: var(--text-dark); margin-bottom: 10px;">Lọc theo loại</h6>
                            <p style="color: var(--text-light); font-size: 0.9rem;">Kết quả được phân loại theo tin tức và dịch vụ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.search-tag {
    display: inline-block;
    padding: 5px 12px;
    background: var(--bg-light);
    color: var(--text-dark);
    border-radius: 15px;
    font-size: 0.85rem;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
}

.search-tag:hover {
    background: var(--primary-color);
    color: var(--text-dark);
    border-color: var(--primary-color);
}

.search-result-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
}

mark {
    background: #ffeb3b;
    padding: 0 2px;
    border-radius: 2px;
}
</style>

<?php include 'includes/footer.php'; ?> 