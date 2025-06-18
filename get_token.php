<?php
// get_token.php
require_once 'config.php';

// 确保会话已启动
session_start();

// 返回JSON格式的CSRF令牌
header('Content-Type: application/json');
echo json_encode(['token' => generateCSRFToken()]);
?>
