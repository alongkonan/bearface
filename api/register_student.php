<?php
// API: register student with descriptor (teacher only ideally)
require_once __DIR__ . '/../src/db.php';
session_start();
// simple auth: allow if logged in and teacher
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    http_response_code(401);
    echo json_encode(['error' => 'unauth']);
    exit;
}
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data || empty($data['name']) || empty($data['student_number']) || empty($data['descriptor'])) {
    echo json_encode(['error' => 'invalid payload']); exit;
}
$db = get_db();
$stmt = $db->prepare('INSERT INTO students (name, student_number, descriptor) VALUES (:n,:sn,:d)');
try {
    $stmt->execute([':n'=>$data['name'], ':sn'=>$data['student_number'], ':d' => json_encode($data['descriptor'])]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
