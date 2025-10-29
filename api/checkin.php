<?php
require_once __DIR__ . '/../src/db.php';
// public endpoint: student posts descriptor, student_number, session_id
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data || empty($data['student_number']) || empty($data['session_id']) || empty($data['descriptor'])) {
    http_response_code(400); echo json_encode(['error'=>'invalid payload']); exit;
}
$db = get_db();
$stmt = $db->prepare('SELECT id,descriptor FROM students WHERE student_number = :sn');
$stmt->execute([':sn'=>$data['student_number']]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$student) { http_response_code(404); echo json_encode(['error'=>'student not found']); exit; }
$stored = json_decode($student['descriptor'], true);
if (!$stored) { http_response_code(500); echo json_encode(['error'=>'no descriptor enrolled']); exit; }

// compute Euclidean distance
function dist($a,$b){
    $s=0.0; for($i=0;$i<count($a);$i++) { $d = $a[$i]-$b[$i]; $s += $d*$d; } return sqrt($s);
}
$incoming = $data['descriptor'];
if (count($incoming) !== count($stored)) { http_response_code(400); echo json_encode(['error'=>'descriptor size mismatch']); exit; }
$d = dist($incoming, $stored);
$threshold = 0.6; // typical face-api threshold
$verified = $d <= $threshold ? 1 : 0;

$ins = $db->prepare('INSERT INTO attendance (session_id, student_id, checkin_time, verified) VALUES (:s,:st,:t,:v)');
$ins->execute([':s'=>$data['session_id'], ':st'=>$student['id'], ':t'=>date('c'), ':v'=>$verified]);

echo json_encode(['success'=>true, 'distance'=>$d, 'verified'=>$verified]);
