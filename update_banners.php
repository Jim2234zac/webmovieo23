<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

// อัปเดตแบนเนอร์ให้ใช้ placeholder images
$bannerImages = [
    'top'    => 'http://localhost:8080/placeholder.php?w=728&h=90&text=BANNER+TOP',
    'bottom' => 'http://localhost:8080/placeholder.php?w=728&h=90&text=BANNER+BOTTOM',
    'left'   => 'http://localhost:8080/placeholder.php?w=160&h=600&text=LEFT',
    'right'  => 'http://localhost:8080/placeholder.php?w=160&h=600&text=RIGHT'
];

foreach ($bannerImages as $position => $imageUrl) {
    execute(
        'UPDATE banners SET image_url = ? WHERE position = ?',
        [$imageUrl, $position]
    );
}

echo "✓ อัปเดตแบนเนอร์เรียบร้อย!";
