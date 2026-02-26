<?php
// Include database config
require_once 'database/config.php';

// Debug logging
if (defined('APP_DEBUG') && APP_DEBUG) {
    error_log("News detail page accessed for slug: " . (isset($_GET['slug']) ? $_GET['slug'] : 'none'));
}

// Get slug from URL
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
    // Redirect to news page if no slug provided
    header('Location: /tin-tuc');
    exit;
}

// Get article by slug
try {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log("Attempting to fetch article with slug: " . $slug);
    }
    
    $article_stmt = $pdo->prepare("SELECT * FROM news WHERE slug = :slug AND status = 'published'");
    $article_stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
    $article_stmt->execute();
    $article = $article_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log("Article found: " . ($article ? 'yes' : 'no'));
        if ($article) {
            error_log("Article title: " . $article['title']);
        }
    }
} catch(PDOException $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log("Database error: " . $e->getMessage());
    }
    $article = null;
}

if (!$article) {
    // Article not found, redirect to 404 or news page
    header('Location: /tin-tuc');
    exit;
}

// Get related articles (3 latest articles excluding current one)
try {
    $related_stmt = $pdo->prepare("SELECT * FROM news WHERE slug != :slug AND status = 'published' ORDER BY created_at DESC LIMIT 3");
    $related_stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
    $related_stmt->execute();
    $related_articles = $related_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $related_articles = [];
}

// Page meta information
$page_title = htmlspecialchars($article['title']);
$page_description = htmlspecialchars($article['excerpt'] ?: substr(strip_tags($article['content']), 0, 160));

// Include header
include 'includes/header.php';
?>

<!-- Breadcrumb -->
<section style="background: var(--bg-light); padding: 60px 0 40px; margin-top: 80px;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 0 0 20px 0;">
                        <li class="breadcrumb-item">
                            <a href="/" style="color: var(--text-light);">Trang chủ</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="/tin-tuc" style="color: var(--text-light);">Tin tức</a>
                        </li>
                        <li class="breadcrumb-item active" style="color: var(--primary-color);">
                            <?php echo htmlspecialchars($article['title']); ?>
                        </li>
                    </ol>
                </nav>
                <h1 style="color: var(--text-dark); margin-bottom: 15px; line-height: 1.3;">
                    <?php echo htmlspecialchars($article['title']); ?>
                </h1>
                <div style="color: var(--text-light); margin-bottom: 20px;">
                    <i class="fas fa-calendar" style="margin-right: 8px;"></i>
                    <?php echo date('d/m/Y H:i', strtotime($article['created_at'])); ?>
                    <span style="margin: 0 10px;">•</span>
                    <i class="fas fa-user" style="margin-right: 8px;"></i>
                    VINA LOGISTICS
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Article Content -->
<section class="section">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8 mb-4">
                <article style="background: white; padding: 40px; border-radius: 15px; box-shadow: var(--shadow);">
                    <!-- Featured Image -->
                    <?php
                        // Áp dụng cơ chế fallback giống danh sách tin tức
                        $candidateImage = !empty($article['image']) ? $article['image'] : '';
                        $serverPath = $candidateImage ? __DIR__ . '/' . ltrim($candidateImage, '/\\') : '';
                        $resolvedImage = ($candidateImage && file_exists($serverPath)) ? $candidateImage : 'assets/images/index9.png';
                    ?>
                    <div style="margin-bottom: 30px;">
                        <img src="<?php echo asset_url($resolvedImage); ?>" 
                             alt="<?php echo htmlspecialchars($article['title']); ?>"
                             style="width: 100%; height: 400px; object-fit: cover; border-radius: 15px;">
                    </div>
                    
                    <!-- Article Content -->
                    <div class="article-content" style="color: var(--text-light); line-height: 1.8; font-size: 1.1rem;">
                        <?php echo fix_relative_urls_in_html($article['content']); ?>
                    </div>
                    
                    <!-- Tags/Categories (if needed) -->
                    <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid var(--border-color);">
                        <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <span style="color: var(--text-dark); font-weight: 600; margin-right: 10px;">
                                <i class="fas fa-tags" style="margin-right: 5px;"></i>Từ khóa:
                            </span>
                            <span style="background: var(--bg-light); color: var(--text-dark); padding: 5px 15px; 
                                         border-radius: 20px; font-size: 0.9rem;">Vận chuyển</span>
                            <span style="background: var(--bg-light); color: var(--text-dark); padding: 5px 15px; 
                                         border-radius: 20px; font-size: 0.9rem;">Logistics</span>
                            <span style="background: var(--bg-light); color: var(--text-dark); padding: 5px 15px; 
                                         border-radius: 20px; font-size: 0.9rem;">Trung Quốc</span>
                        </div>
                    </div>
                    
                    <!-- Social Share -->
                    <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid var(--border-color);">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span style="color: var(--text-dark); font-weight: 600;">
                                <i class="fas fa-share-alt" style="margin-right: 5px;"></i>Chia sẻ:
                            </span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                               target="_blank"
                               style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; 
                                      background: #3b5998; color: white; border-radius: 50%; text-decoration: none; 
                                      transition: transform 0.3s ease;"
                               onmouseover="this.style.transform='scale(1.1)'"
                               onmouseout="this.style.transform='scale(1)'">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($article['title']); ?>&url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                               target="_blank"
                               style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; 
                                      background: #1da1f2; color: white; border-radius: 50%; text-decoration: none; 
                                      transition: transform 0.3s ease;"
                               onmouseover="this.style.transform='scale(1.1)'"
                               onmouseout="this.style.transform='scale(1)'">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="mailto:?subject=<?php echo urlencode($article['title']); ?>&body=<?php echo urlencode('Xem bài viết: ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                               style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; 
                                      background: var(--primary-color); color: var(--text-dark); border-radius: 50%; text-decoration: none; 
                                      transition: transform 0.3s ease;"
                               onmouseover="this.style.transform='scale(1.1)'"
                               onmouseout="this.style.transform='scale(1)'">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </article>
                
                <!-- Navigation to Previous/Next Article -->
                <div style="margin-top: 30px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <a href="/tin-tuc" 
                           style="background: white; padding: 20px; border-radius: 15px; box-shadow: var(--shadow); 
                                  text-decoration: none; color: var(--text-dark); transition: all 0.3s ease;
                                  display: flex; align-items: center;"
                           onmouseover="this.style.transform='translateY(-2px)'"
                           onmouseout="this.style.transform='translateY(0)'">
                            <i class="fas fa-arrow-left" style="color: var(--primary-color); margin-right: 15px; font-size: 1.5rem;"></i>
                            <div>
                                <div style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 5px;">Quay lại</div>
                                <div style="font-weight: 600;">Trang tin tức</div>
                            </div>
                        </a>
                        
                        <a href="/lien-he" 
                           style="background: white; padding: 20px; border-radius: 15px; box-shadow: var(--shadow); 
                                  text-decoration: none; color: var(--text-dark); transition: all 0.3s ease;
                                  display: flex; align-items: center; justify-content: flex-end; text-align: right;"
                           onmouseover="this.style.transform='translateY(-2px)'"
                           onmouseout="this.style.transform='translateY(0)'">
                            <div>
                                <div style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 5px;">Cần tư vấn?</div>
                                <div style="font-weight: 600;">Liên hệ ngay</div>
                            </div>
                            <i class="fas fa-arrow-right" style="color: var(--primary-color); margin-left: 15px; font-size: 1.5rem;"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Contact Widget -->
                <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: var(--shadow); margin-bottom: 30px;">
                    <h5 style="color: var(--text-dark); margin-bottom: 20px; text-align: center;">
                        <i class="fas fa-phone" style="color: var(--primary-color); margin-right: 10px;"></i>
                        Cần hỗ trợ?
                    </h5>
                    <p style="color: var(--text-light); text-align: center; margin-bottom: 25px;">
                        Liên hệ ngay với chúng tôi để được tư vấn miễn phí
                    </p>
                    <div style="text-align: center;">
                        <a href="tel:<?php echo str_replace([' ', '.', '-'], '', COMPANY_PHONE); ?>" 
                           class="btn btn-primary" style="margin-bottom: 15px; width: 100%;">
                            <i class="fas fa-phone"></i> <?php echo COMPANY_PHONE; ?>
                        </a>
                        <a href="/lien-he" class="btn btn-outline" style="width: 100%;">
                            <i class="fas fa-envelope"></i> Gửi tin nhắn
                        </a>
                    </div>
                </div>
                
                <!-- Related Articles -->
                <?php if (!empty($related_articles)): ?>
                <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: var(--shadow); margin-bottom: 30px;">
                    <h5 style="color: var(--text-dark); margin-bottom: 25px;">
                        <i class="fas fa-newspaper" style="color: var(--primary-color); margin-right: 10px;"></i>
                        Bài viết liên quan
                    </h5>
                    
                    <?php foreach ($related_articles as $related): ?>
                    <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color);">
                        <h6 style="margin-bottom: 10px;">
                            <a href="/tin-tuc/<?php echo htmlspecialchars($related['slug']); ?>" 
                               style="color: var(--text-dark); text-decoration: none; line-height: 1.4;"
                               onmouseover="this.style.color='var(--primary-color)'"
                               onmouseout="this.style.color='var(--text-dark)'">
                                <?php echo htmlspecialchars($related['title']); ?>
                            </a>
                        </h6>
                        <div style="color: var(--text-light); font-size: 0.9rem;">
                            <i class="fas fa-calendar" style="margin-right: 5px;"></i>
                            <?php echo date('d/m/Y', strtotime($related['created_at'])); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="/tin-tuc" class="btn btn-outline btn-sm">
                            <i class="fas fa-list"></i> Xem tất cả
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Services Widget -->
                <div style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); 
                            padding: 30px; border-radius: 15px; text-align: center; color: var(--text-dark);">
                    <h5 style="margin-bottom: 20px; color: var(--text-dark);">
                        <i class="fas fa-truck" style="margin-right: 10px;"></i>
                        Dịch vụ của chúng tôi
                    </h5>
                    <p style="margin-bottom: 25px; color: var(--text-dark);">
                        Khám phá các dịch vụ vận chuyển chuyên nghiệp
                    </p>
                    <a href="dich-vu" 
                       class="btn" 
                       style="background: var(--text-dark); color: var(--primary-color); border: none;">
                        <i class="fas fa-arrow-right"></i> Xem dịch vụ
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Article content styling */
.article-content h2, .article-content h3, .article-content h4 {
    color: var(--text-dark);
    margin: 30px 0 20px 0;
}

.article-content p {
    margin-bottom: 20px;
    text-align: justify;
}

.article-content ul, .article-content ol {
    margin: 20px 0;
    padding-left: 30px;
}

.article-content li {
    margin-bottom: 10px;
}

.article-content blockquote {
    background: var(--bg-light);
    border-left: 4px solid var(--primary-color);
    padding: 20px;
    margin: 30px 0;
    border-radius: 10px;
    font-style: italic;
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    margin: 20px 0;
}

.article-content a {
    color: var(--primary-color);
    font-weight: 500;
}

.article-content a:hover {
    color: var(--primary-dark);
}
</style>

<?php include 'includes/footer.php'; ?> 