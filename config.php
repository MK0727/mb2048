<?php
// 数据库连接配置
$host = "localhost";
$dbname = "bread2048";
$user = "bread2048";
$pass = "mx123456";

// 数据库配置数组
$dbConfig = [
    'host' => $host,
    'dbname' => $dbname,
    'user' => $user,
    'pass' => $pass
];

// 生成CSRF令牌
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        // 使用更安全的随机数生成方法
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// 验证CSRF令牌
function verifyCSRFToken($token) {
    // 添加时间戳验证，令牌15分钟内有效
    $tokenValidityTime = 900; // 15分钟
    
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    if (!isset($_SESSION['csrf_token_time'])) {
        $_SESSION['csrf_token_time'] = time();
    }
    
    // 检查令牌是否过期
    if (time() - $_SESSION['csrf_token_time'] > $tokenValidityTime) {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

// 启动会话（如果未启动）
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
