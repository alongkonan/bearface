<?php
require_once __DIR__ . '/../src/db.php';
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    http_response_code(401);
    echo json_encode(['error' => 'unauth']); exit;
}
$title = $_POST['title'] ?? null;
$start = $_POST['start_time'] ?? null;
$end = $_POST['end_time'] ?? null;
if (!$title) { echo json_encode(['error'=>'missing title']); exit; }
$db = get_db();
$stmt = $db->prepare('INSERT INTO sessions (teacher_id,title,start_time,end_time) VALUES (:t,:title,:s,:e)');
try{
    $stmt->execute([':t'=>$_SESSION['user']['id'], ':title'=>$title, ':s'=>$start, ':e'=>$end]);
    echo json_encode(['success'=>true]);
}catch(Exception $e){
    http_response_code(500); echo json_encode(['error'=>$e->getMessage()]);
}
