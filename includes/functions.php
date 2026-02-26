<?php
/**
 * Helper functions cho VINA LOGISTICS
 */

// Error handling for functions
function handleFunctionError($function, $error) {
    $logMessage = date('Y-m-d H:i:s') . " - FUNCTION ERROR in $function: " . $error . "\n";
    error_log($logMessage, 3, __DIR__ . '/../logs/error.log');
    return false;
}

/**
 * Lấy keywords động từ database
 */
function getDynamicKeywords($category = 'general') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT keyword 
            FROM keywords 
            WHERE status = 'active' 
            AND (category = :category OR category = 'general')
            ORDER BY priority DESC, keyword ASC
        ");
        $stmt->bindParam(':category', $category);
        $stmt->execute();
        
        $keywords = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return implode(', ', $keywords);
    } catch(PDOException $e) {
        handleFunctionError('getDynamicKeywords', $e->getMessage());
        // Fallback keywords nếu có lỗi database
        return 'vận chuyển hàng hóa, logistics, nhập khẩu ủy thác, mua hàng Trung Quốc, Taobao, 1688, VINA LOGISTICS';
    }
}

/**
 * Lấy danh sách categories tin tức
 */
function getNewsCategories() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT * 
            FROM news_categories 
            WHERE status = 'active' 
            ORDER BY sort_order ASC, name ASC
        ");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        handleFunctionError('getNewsCategories', $e->getMessage());
        return [];
    }
}

/**
 * Tìm kiếm trong database
 */
function searchContent($query, $limit = 20) {
    global $pdo;
    
    $results = [];
    $searchTerm = '%' . $query . '%';
    
    try {
        // Tìm kiếm trong tin tức
        $newsStmt = $pdo->prepare("
            SELECT 
                'news' as type,
                n.id,
                n.title,
                n.slug,
                n.excerpt,
                n.image,
                n.created_at,
                nc.name as category_name,
                nc.slug as category_slug
            FROM news n
            LEFT JOIN news_categories nc ON n.category_id = nc.id
            WHERE (n.status = 'published' OR n.status = 1 OR n.status = '1') 
            AND (
                n.title LIKE :search 
                OR n.content LIKE :search 
                OR n.excerpt LIKE :search
                OR n.tags LIKE :search
            )
            ORDER BY n.created_at DESC
            LIMIT :limit
        ");
        $newsStmt->bindParam(':search', $searchTerm);
        $newsStmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $newsStmt->execute();
        
        $newsResults = $newsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Tìm kiếm trong dịch vụ
        $servicesStmt = $pdo->prepare("
            SELECT 
                'service' as type,
                id,
                title,
                slug,
                description as excerpt,
                image,
                created_at
            FROM services 
            WHERE (
                status = 'active' OR status = 1 OR status = '1'
            )
            AND (
                title LIKE :search 
                OR description LIKE :search
            )
            ORDER BY sort_order ASC, title ASC
            LIMIT :limit
        ");
        $servicesStmt->bindParam(':search', $searchTerm);
        $servicesStmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $servicesStmt->execute();
        
        $servicesResults = $servicesStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Kết hợp kết quả
        $results = array_merge($newsResults, $servicesResults);
        
        // Sắp xếp theo thời gian
        usort($results, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_slice($results, 0, $limit);
        
    } catch(PDOException $e) {
        handleFunctionError('searchContent', $e->getMessage());
        return [];
    }
}

/**
 * Highlight từ khóa tìm kiếm
 */
function highlightSearchTerm($text, $searchTerm) {
    if (empty($searchTerm)) {
        return $text;
    }
    
    $searchTerms = explode(' ', $searchTerm);
    foreach ($searchTerms as $term) {
        $term = trim($term);
        if (!empty($term)) {
            $text = preg_replace('/(' . preg_quote($term, '/') . ')/i', '<mark>$1</mark>', $text);
        }
    }
    
    return $text;
}

/**
 * Lấy URL cho content
 */
function getContentUrl($type, $slug) {
    switch ($type) {
        case 'news':
            return 'tin-tuc/' . $slug;
        case 'service':
            return 'dich-vu/' . $slug;
        default:
            return $slug;
    }
}

/**
 * Lấy icon cho content type
 */
function getContentIcon($type) {
    switch ($type) {
        case 'news':
            return 'fas fa-newspaper';
        case 'service':
            return 'fas fa-truck';
        default:
            return 'fas fa-file';
    }
}

/**
 * Format ngày tháng tiếng Việt
 */
function formatVietnameseDate($date) {
    $timestamp = strtotime($date);
    $vietnameseMonths = [
        1 => 'Tháng 1', 2 => 'Tháng 2', 3 => 'Tháng 3', 4 => 'Tháng 4',
        5 => 'Tháng 5', 6 => 'Tháng 6', 7 => 'Tháng 7', 8 => 'Tháng 8',
        9 => 'Tháng 9', 10 => 'Tháng 10', 11 => 'Tháng 11', 12 => 'Tháng 12'
    ];
    
    $day = date('d', $timestamp);
    $month = $vietnameseMonths[date('n', $timestamp)];
    $year = date('Y', $timestamp);
    
    return "$day $month, $year";
}

/**
 * Tạo excerpt từ content
 */
function createExcerpt($content, $length = 150) {
    $excerpt = strip_tags($content);
    $excerpt = preg_replace('/\s+/', ' ', $excerpt);
    
    if (strlen($excerpt) > $length) {
        $excerpt = substr($excerpt, 0, $length);
        $excerpt = substr($excerpt, 0, strrpos($excerpt, ' ')) . '...';
    }
    
    return $excerpt;
}

/**
 * Lấy thông báo tin tức
 */
function getNewsNotifications($limit = 10) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT title 
            FROM news 
            WHERE status = 'published' 
            ORDER BY created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $news = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Nếu không có tin tức, trả về thông báo mặc định
        if (empty($news)) {
            return ['Chào mừng bạn đến với VINA LOGISTICS!'];
        }
        
        return $news;
        
    } catch(PDOException $e) {
        handleFunctionError('getNewsNotifications', $e->getMessage());
        return ['Chào mừng bạn đến với VINA LOGISTICS!'];
    }
}

/**
 * Chuẩn hóa URL tài nguyên (ảnh/js/css) để luôn truy cập đúng tuyệt đối
 * - Giữ nguyên nếu là URL bắt đầu bằng http(s)
 * - Thêm '/' đầu nếu là đường dẫn tương đối
 * - Nếu có APP_URL thì ghép thành APP_URL + path tuyệt đối
 */
function asset_url($path) {
    if (empty($path)) {
        return '';
    }

    // Bỏ khoảng trắng thừa
    $path = trim($path);

    // Trả nguyên nếu đã là URL đầy đủ
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    // Chuẩn hóa ./
    if (strpos($path, './') === 0) {
        $path = substr($path, 2);
    }

    // Nếu chỉ là tên file (không có '/') thì map theo phần mở rộng
    if (strpos($path, '/') === false) {
        // Một số tệp nằm ở root dự án
        $rootFiles = ['logo.jpg', 'logo-removebg.png', 'qr_code.jpg'];
        if (in_array(strtolower($path), $rootFiles, true)) {
            $path = '/' . $path;
        } else {
        if (preg_match('/\.(png|jpe?g|gif|svg|webp)$/i', $path)) {
            $path = 'assets/images/' . $path;
        } elseif (preg_match('/\.js$/i', $path)) {
            $path = 'assets/js/' . $path;
        } elseif (preg_match('/\.css$/i', $path)) {
            $path = 'assets/css/' . $path;
        }
        }
    }

    // Đảm bảo có dấu '/'
    if ($path[0] !== '/') {
        $path = '/' . $path;
    }

    // Ghép với APP_URL nếu có
    if (defined('APP_URL') && APP_URL) {
        return rtrim(APP_URL, '/') . $path;
    }

    return $path;
}

/**
 * Sửa các URL tương đối trong HTML (src, href) thành URL tuyệt đối hợp lệ
 * - Bỏ qua mailto:, tel:, data:, #, http(s) và //
 * - Nếu không có dấu '/' đầu, thêm '/'
 * - Nếu có APP_URL, ghép đầy đủ domain
 */
function fix_relative_urls_in_html($html) {
    if (!is_string($html) || $html === '') {
        return $html;
    }

    $pattern = '/\b(src|href)\s*=\s*([\"\'])\s*([^\"\'>\s]+)\s*\2/i';

    $callback = function ($matches) {
        $attr = $matches[1];
        $quote = $matches[2];
        $url  = trim($matches[3]);

        // Bỏ qua nếu là URL tuyệt đối hoặc schema đặc biệt
        if (
            $url === '' ||
            preg_match('#^(https?:)?//#i', $url) ||
            preg_match('#^(mailto:|tel:|data:|\#)#i', $url)
        ) {
            return $matches[0];
        }

        // Chuẩn hóa các đường dẫn bắt đầu bằng ./
        if (strpos($url, './') === 0) {
            $url = substr($url, 2);
        }

        // Nếu là file ảnh/js/css chỉ là tên file (không có '/') thì map tới thư mục assets tương ứng
        if (strpos($url, '/') === false) {
            // Một số tệp nằm ở root dự án
            $rootFiles = ['logo.jpg', 'logo-removebg.png', 'qr_code.jpg'];
            if (in_array(strtolower($url), $rootFiles, true)) {
                // giữ ở root
            } else {
            if (preg_match('/\.(png|jpe?g|gif|svg|webp)$/i', $url)) {
                $url = 'assets/images/' . $url;
            } elseif (preg_match('/\.js$/i', $url)) {
                $url = 'assets/js/' . $url;
            } elseif (preg_match('/\.css$/i', $url)) {
                $url = 'assets/css/' . $url;
            }
            }
        }

        // Đảm bảo path bắt đầu bằng '/'
        if ($url[0] !== '/') {
            $url = '/' . $url;
        }

        // Sửa các trường hợp sai phổ biến: '/tin-tuc/assets/...'
        if (preg_match('#^/tin-tuc/(assets/.*)$#i', $url, $m)) {
            $url = '/' . $m[1];
        }

        // Ghép với APP_URL nếu có
        if (defined('APP_URL') && APP_URL) {
            $url = rtrim(APP_URL, '/') . $url;
        }

        return $attr . '=' . $quote . $url . $quote;
    };

    return preg_replace_callback($pattern, $callback, $html);
}
?> 