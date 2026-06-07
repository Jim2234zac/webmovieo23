<?php
header('Content-Type: application/json');
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$stats = getDashboardStats();

// รับค่าการค้นหา
$search = trim($_REQUEST['search'] ?? '');

// ถ้ามีการค้นหา แสดงผลลัพธ์การค้นหา
if ($search !== '') {
    $movies = fetchAll(
        'SELECT m.id, m.title_th, m.views, c.name_th AS category_name, m.status FROM movies m
         LEFT JOIN categories c ON m.category_id = c.id
         WHERE m.title_th LIKE ? OR m.title LIKE ?
         ORDER BY m.created_at DESC',
        ["%$search%", "%$search%"]
    );
} else {
    // แสดงหนังล่าสุด 5 เรื่อง
    $movies = fetchAll(
        'SELECT m.id, m.title_th, m.views, c.name_th AS category_name, m.status FROM movies m
         LEFT JOIN categories c ON m.category_id = c.id
         ORDER BY m.created_at DESC LIMIT 5'
    );
}

echo json_encode([
    'stats' => $stats,
    'movies' => $movies,
    'search' => $search
]);
