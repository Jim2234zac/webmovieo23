<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'ไม่พบหน้า';
require_once __DIR__ . '/includes/header.php';
?>

<div class="error-container">
    <div class="error-content">
        <h1 class="error-code">404</h1>
        <p class="error-message">ไม่พบหน้าที่คุณกำลังมองหา</p>
        <p style="color: var(--text-secondary); margin-bottom: 24px;">หน้านี้อาจถูกย้ายหรือลบไปแล้ว</p>
        <a href="<?= SITE_URL ?>/index.php" class="btn btn-primary">กลับไปหน้าแรก</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
