<?php
$pageTitle = 'แดชบอร์ด';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/sidebar.php';

$stats = getDashboardStats();
$flash = getFlashMessage();
$recent = fetchAll(
    'SELECT m.*, c.name_th AS category_name FROM movies m
     LEFT JOIN categories c ON m.category_id = c.id
     ORDER BY m.created_at DESC LIMIT 5'
);
?>

<div class="admin-top">
    <h1>แดชบอร์ด</h1>
    <span>สวัสดี, <?= e($_SESSION['username']) ?></span>
</div>

<?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div><?php endif; ?>

<div class="stats-grid">
    <div class="stat-card"><div class="stat-value"><?= number_format($stats['movies']) ?></div><div class="stat-label">หนัง/อนิเมะ</div></div>
    <div class="stat-card"><div class="stat-value"><?= number_format($stats['episodes']) ?></div><div class="stat-label">ตอน</div></div>
    <div class="stat-card"><div class="stat-value"><?= number_format($stats['users']) ?></div><div class="stat-label">ผู้ใช้</div></div>
    <div class="stat-card"><div class="stat-value"><?= number_format($stats['views']) ?></div><div class="stat-label">ยอดดูรวม</div></div>
</div>

<h2 class="admin-subtitle">หนังล่าสุด</h2>
<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>ชื่อ</th><th>หมวด</th><th>สถานะ</th><th>ยอดดู</th></tr></thead>
        <tbody>
        <?php foreach ($recent as $m): ?>
        <tr>
            <td><?= e($m['title_th']) ?></td>
            <td><?= e($m['category_name'] ?? '-') ?></td>
            <td><?= statusBadge($m['status']) ?></td>
            <td><?= number_format($m['views']) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
