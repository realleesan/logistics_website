<?php
// Check if user is logged in
requireAdminLogin();

// Get current page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? ADMIN_PREFIX . $page_title : 'Admin Panel - Vina Logistics'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../logo.jpg">
    
    <!-- CSS Files -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    
    <!-- Admin specific CSS -->
    <style>
        :root {
            --primary-color: #8bc34a;
            --primary-dark: #689f38;
            --primary-light: #a5d6a7;
            --secondary-color: #2c3e50;
            --text-dark: #2c3e50;
            --text-light: #666666;
            --bg-light: #f8f9fa;
            --bg-white: #ffffff;
            --border-color: #e9ecef;
            --admin-sidebar: #2c3e50;
            --admin-sidebar-active: #34495e;
        }
        
        body {
            background-color: var(--bg-light);
            font-family: 'Poppins', sans-serif;
        }
        
        /* Admin Header */
        .admin-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 70px;
        }
        
        .admin-navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-logo {
            display: flex;
            align-items: center;
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 700;
            font-size: 1.4rem;
        }
        
        .admin-logo img {
            height: 40px;
            margin-right: 10px;
            border-radius: 50%;
        }
        
        .admin-nav {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 20px;
            align-items: center;
        }
        
        .admin-nav-link {
            color: var(--text-dark);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .admin-nav-link:hover,
        .admin-nav-link.active {
            background: rgba(255,255,255,0.2);
            color: var(--text-dark);
        }
        
        .admin-user-menu {
            position: relative;
        }
        
        .user-dropdown {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            padding: 8px 16px;
            color: var(--text-dark);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .user-dropdown:hover {
            background: rgba(255,255,255,0.2);
            color: var(--text-dark);
        }
        
        /* Content Area */
        .admin-content {
            margin-top: 70px;
            min-height: calc(100vh - 70px);
            padding: 30px 0;
        }
        
        /* Cards */
        .admin-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 30px;
        }
        
        .admin-card .card-header {
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-color) 100%);
            border: none;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px 25px;
            color: var(--text-dark);
            font-weight: 600;
        }
        
        .admin-card .card-body {
            padding: 25px;
        }
        
        /* Buttons */
        .btn-admin-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            color: var(--text-dark);
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-admin-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 195, 74, 0.3);
            color: var(--text-dark);
        }
        
        .btn-admin-secondary {
            background: var(--secondary-color);
            border: none;
            color: white;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-admin-secondary:hover {
            background: #34495e;
            transform: translateY(-2px);
            color: white;
        }
        
        /* Tables */
        .table-admin {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        
        .table-admin thead th {
            background: var(--primary-color);
            color: var(--text-dark);
            border: none;
            font-weight: 600;
            padding: 15px;
        }
        
        .table-admin tbody td {
            border-color: #f8f9fa;
            padding: 15px;
            vertical-align: middle;
        }
        
        .table-admin tbody tr:hover {
            background: #f8f9fa;
        }
        
        /* Status badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-published {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-draft {
            background: #fff3cd;
            color: #856404;
        }
        
        /* Action buttons */
        .btn-action {
            padding: 6px 10px;
            margin: 0 2px;
            border-radius: 6px;
            border: none;
            font-size: 12px;
            cursor: pointer;
        }
        
        .btn-action-edit {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-action-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-action-view {
            background: #17a2b8;
            color: white;
        }
        
        /* Forms */
        .form-admin .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-admin .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(139, 195, 74, 0.15);
        }
        
        .form-admin .form-label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .admin-nav {
                display: none;
            }
            
            .admin-content {
                padding: 20px 0;
            }
            
            .admin-card .card-body {
                padding: 20px;
            }
        }
    </style>
    
    <!-- Additional CSS for specific pages -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link href="<?php echo $css; ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="container-fluid">
            <nav class="admin-navbar">
                <!-- Logo -->
                <a href="index.php" class="admin-logo">
                    <img src="../logo.jpg" alt="Vina Logistics Logo">
                    <span>Vina Logistics Admin</span>
                </a>

                <!-- Main Navigation -->
                <ul class="admin-nav d-none d-md-flex">
                    <li>
                        <a href="index.php" class="admin-nav-link <?php echo ($current_page == 'index') ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="news.php" class="admin-nav-link <?php echo (strpos($current_page, 'news') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-newspaper me-2"></i>Tin tức
                        </a>
                    </li>
                    <li>
                        <a href="categories.php" class="admin-nav-link <?php echo (strpos($current_page, 'categories') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-tags me-2"></i>Danh mục
                        </a>
                    </li>
                    <li>
                        <a href="services.php" class="admin-nav-link <?php echo (strpos($current_page, 'services') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-cogs me-2"></i>Dịch vụ
                        </a>
                    </li>
                    <li>
                        <a href="keywords.php" class="admin-nav-link <?php echo (strpos($current_page, 'keywords') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-key me-2"></i>Từ khóa
                        </a>
                    </li>
                    <li>
                        <a href="contacts.php" class="admin-nav-link <?php echo (strpos($current_page, 'contacts') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-envelope me-2"></i>Liên hệ
                        </a>
                    </li>
                </ul>

                <!-- User Menu -->
                <div class="admin-user-menu">
                    <div class="dropdown">
                        <a href="#" class="user-dropdown dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i>
                            <?php echo $_SESSION['admin_username']; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../" target="_blank"><i class="fas fa-external-link-alt me-2"></i>Xem website</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="admin-content">
        <div class="container-fluid">
            <!-- Display messages -->
            <?php
            $message = getMessage();
            if ($message):
            ?>
                <div class="alert alert-<?php echo $message['type'] === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show">
                    <i class="fas fa-<?php echo $message['type'] === 'error' ? 'exclamation-triangle' : 'check-circle'; ?> me-2"></i>
                    <?php echo htmlspecialchars($message['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?> 