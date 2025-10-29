<?php
// init_db.php - Run once to create the SQLite database and seed admin/teacher
require_once __DIR__ . '/src/db.php';

$db = get_db();

// Create tables
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    role TEXT NOT NULL
);");

$db->exec("CREATE TABLE IF NOT EXISTS students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    student_number TEXT UNIQUE NOT NULL,
    descriptor TEXT
);");

$db->exec("CREATE TABLE IF NOT EXISTS sessions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    teacher_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    start_time TEXT,
    end_time TEXT,
    FOREIGN KEY(teacher_id) REFERENCES users(id)
);");

$db->exec("CREATE TABLE IF NOT EXISTS attendance (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    session_id INTEGER NOT NULL,
    student_id INTEGER NOT NULL,
    checkin_time TEXT NOT NULL,
    verified INTEGER NOT NULL DEFAULT 0,
    FOREIGN KEY(session_id) REFERENCES sessions(id),
    FOREIGN KEY(student_id) REFERENCES students(id)
);");

// Seed admin and teacher if not exists
$stmt = $db->prepare('SELECT COUNT(*) as c FROM users');
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row['c'] == 0) {
    $insert = $db->prepare('INSERT INTO users (username,password,role) VALUES (:u,:p,:r)');
    $insert->execute([':u' => 'admin', ':p' => password_hash('admin123', PASSWORD_DEFAULT), ':r' => 'admin']);
    $insert->execute([':u' => 'teacher', ':p' => password_hash('teacher123', PASSWORD_DEFAULT), ':r' => 'teacher']);
    echo "Seeded admin and teacher (username/password: admin/admin123, teacher/teacher123)\n";
} else {
    echo "Users already present, skipping seed.\n";
}

echo "Database initialized at data/database.sqlite\n";

?>
