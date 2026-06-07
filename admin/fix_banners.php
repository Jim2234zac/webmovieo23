<?php
require_once __DIR__ . '/../includes/functions.php';

// ตั้งค่ารูป default สำหรับแต่ละตำแหน่ง
$defaultImages = [
    'top'    => 'https://picsum.photos/seed/banner-top/728/90',
    'bottom' => 'https://picsum.photos/seed/banner-bottom/728/90',
    'left'   => 'https://picsum.photos/seed/banner-left/160/600',
    'right'  => 'https://picsum.photos/seed/banner-right/160/600'
];

// อัปเดตแบนเนอร์ที่ไม่มี image_url
foreach ($defaultImages as $position => $imageUrl) {
    execute(
        'UPDATE banners SET image_url = ? WHERE position = ? AND (image_url IS NULL OR image_url = "")',
        [$imageUrl, $position]
    );
}

// ไปกลับหน้า banners
redirect('http://localhost:8080/admin/banners.php');
