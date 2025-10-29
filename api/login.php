<?php
require_once __DIR__ . '/../src/db.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
$u = $_POST['username'] ?? '';
$p = $_POST['password'] ?? '';
$db = get_db();
$stmt = $db->prepare('SELECT id,username,password,role FROM users WHERE username = :u');
$stmt->execute([':u'=>$u]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row && password_verify($p, $row['password'])) {
    $_SESSION['user'] = ['id'=>$row['id'],'username'=>$row['username'],'role'=>$row['role']];
    echo json_encode(['success'=>true]);
} else {
    http_response_code(401); echo json_encode(['error'=>'invalid']);
}
