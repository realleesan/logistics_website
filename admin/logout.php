<?php
session_start();

// Xóa tất cả session
session_destroy();

// Chuyển hướng về trang chủ người dùng
header('Location: ../index.php');
exit();
?> 