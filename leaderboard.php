<?php
session_start();

// 加载配置
require_once 'config.php';

// 设置安全头
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

try {
    // 连接数据库
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    http_response_code(500);
    die(json_encode(['status' => 'error', 'message' => '数据库连接失败']));
}

// 获取请求动作
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get':
            // 获取排行榜数据
            $stmt = $conn->prepare("SELECT name, score FROM leaderboard ORDER BY score DESC LIMIT 20");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            header('Content-Type: application/json');
            echo json_encode($data);
            break;
            
        case 'add':
            // 添加或更新分数
            // 验证 CSRF 令牌
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                http_response_code(403);
                die(json_encode(['status' => 'error', 'message' => 'CSRF 验证失败']));
            }
            
            // 验证输入
            $name = trim($_POST['name'] ?? '');
            $score = (int)($_POST['score'] ?? 0);
            
            if (empty($name)) {
                $name = '匿名玩家';
            }
            
            if (strlen($name) > 20) {
                http_response_code(400);
                die(json_encode(['status' => 'error', 'message' => '昵称太长']));
            }
            
            if ($score <= 0) {
                http_response_code(400);
                die(json_encode(['status' => 'error', 'message' => '无效的分数']));
            }
            
            // 开始事务
            $conn->beginTransaction();
            
            try {
                // 检查是否已有该用户
                $checkStmt = $conn->prepare("SELECT score FROM leaderboard WHERE name = ?");
                $checkStmt->execute([$name]);
                
                if ($checkStmt->rowCount() > 0) {
                    $row = $checkStmt->fetch(PDO::FETCH_ASSOC);
                    if ($score > $row['score']) {
                        // 若新分数更高则更新
                        $updateStmt = $conn->prepare("UPDATE leaderboard SET score = ? WHERE name = ?");
                        $updateStmt->execute([$score, $name]);
                    }
                } else {
                    // 否则插入新用户
                    $insertStmt = $conn->prepare("INSERT INTO leaderboard (name, score) VALUES (?, ?)");
                    $insertStmt->execute([$name, $score]);
                }
                
                // 提交事务
                $conn->commit();
                
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => '分数已保存']);
                
            } catch (PDOException $e) {
                // 回滚事务
                $conn->rollBack();
                throw $e;
            }
            
            break;
            
        default:
            http_response_code(400);
            die(json_encode(['status' => 'error', 'message' => '未知操作']));
    }
} catch(PDOException $e) {
    http_response_code(500);
    die(json_encode(['status' => 'error', 'message' => '数据库错误: ' . $e->getMessage()]));
} catch(Exception $e) {
    http_response_code(500);
    die(json_encode(['status' => 'error', 'message' => '服务器错误: ' . $e->getMessage()]));
}
?>
