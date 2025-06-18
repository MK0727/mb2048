<?php
// 引入配置文件
require_once 'config.php';

// 使用配置文件中的信息建立数据库连接
$conn = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['pass'], $dbConfig['dbname']);
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

$action = $_GET['action'];

if ($action == 'get') {
    $sql = "SELECT name, score FROM leaderboard ORDER BY score DESC LIMIT 500";
    $result = $conn->query($sql);
    $data = array();
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
}
else if ($action == 'add') {
    // 验证 CSRF 令牌
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        http_response_code(403);
        die("CSRF 验证失败");
    }

    $name = $conn->real_escape_string($_POST['name']);
    $score = intval($_POST['score']);

    // 查询是否已有该用户
    $check_sql = "SELECT score FROM leaderboard WHERE name = '$name'";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($score > $row['score']) {
            // 若新分数更高则更新
            $update_sql = "UPDATE leaderboard SET score = $score WHERE name = '$name'";
            $conn->query($update_sql);
        }
    } else {
        // 否则插入新用户
        $insert_sql = "INSERT INTO leaderboard (name, score) VALUES ('$name', $score)";
        $conn->query($insert_sql);
    }

    echo "OK";
}

$conn->close();
?>
