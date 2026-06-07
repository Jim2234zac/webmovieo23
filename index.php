<?php
$pageTitle = 'หน้าแรก';
require_once __DIR__ . '/includes/header.php';

$categoryId = isset($_GET['cat']) ? (int) $_GET['cat'] : null;
$status = $_GET['status'] ?? null;

$sql = 'SELECT m.*, c.name_th AS category_name,
        (SELECT MAX(episode_number) FROM episodes WHERE movie_id = m.id) AS latest_episode,
        (SELECT subtitle_type FROM episodes WHERE movie_id = m.id ORDER BY episode_number DESC LIMIT 1) AS latest_subtitle
        FROM movies m LEFT JOIN categories c ON m.category_id = c.id WHERE 1=1';
$params = [];

if ($categoryId) {
    $sql .= ' AND m.category_id = ?';
    $params[] = $categoryId;
}
if ($status && in_array($status, ['ongoing', 'completed'])) {
    $sql .= ' AND m.status = ?';
    $params[] = $status;
}

$sql .= ' ORDER BY m.created_at DESC LIMIT 48';
$movies = fetchAll($sql, $params);
$hasTopBanner = !empty($bannersTop);
?>

<div class="main-wrapper<?= $hasTopBanner ? ' has-top-banner' : '' ?>">
    <div class="content-layout">
        <aside class="sidebar">
            <h3>หมวดหมู่</h3>
            <ul class="category-list">
                <li>
                    <a href="<?= SITE_URL ?>/index.php" class="<?= !$categoryId ? 'active' : '' ?>">ทั้งหมด</a>
                </li>
                <?php foreach ($categories as $cat): ?>
                <li>
                    <a href="<?= SITE_URL ?>/index.php?cat=<?= $cat['id'] ?>"
                       class="<?= $categoryId == $cat['id'] ? 'active' : '' ?>">
                        <?= e($cat['name_th']) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <main class="main-content">
            <div class="section-header">
                <h2>
                    <?php if ($status === 'ongoing'): ?>กำลังฉาย
                    <?php elseif ($status === 'completed'): ?>จบแล้ว
                    <?php elseif ($categoryId):
                        foreach ($categories as $c) {
                            if ($c['id'] == $categoryId) echo e($c['name_th']);
                        }
                    else: ?>อนิเมะล่าสุด<?php endif; ?>
                </h2>
                <span class="count-label"><?= count($movies) ?> เรื่อง</span>
            </div>

            <?php if (empty($movies)): ?>
            <div class="empty-state">
                <p>ไม่พบอนิเมะ</p>
            </div>
            <?php else: ?>
            <div class="movie-grid">
                <?php foreach ($movies as $movie): echo renderMovieCard($movie); endforeach; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
