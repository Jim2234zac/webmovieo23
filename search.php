<?php
require_once __DIR__ . '/includes/functions.php';

$keyword = trim($_GET['q'] ?? '');

if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json; charset=UTF-8');
    if (strlen($keyword) < 1) {
        echo json_encode([]);
        exit;
    }
    $results = searchMovies($keyword, 8);
    $data = array_map(fn($m) => [
        'id'       => $m['id'],
        'title'    => $m['title_th'],
        'thumb'    => $m['thumbnail'],
        'url'      => SITE_URL . '/watch.php?id=' . $m['id'],
        'episodes' => (int) ($m['latest_episode'] ?? 0),
    ], $results);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

$pageTitle = $keyword ? 'ค้นหา: ' . $keyword : 'ค้นหา';
require_once __DIR__ . '/includes/header.php';

$movies = $keyword ? searchMovies($keyword) : [];
$hasTopBanner = !empty($bannersTop);
?>

<div class="main-wrapper<?= $hasTopBanner ? ' has-top-banner' : '' ?>">
    <div class="content-layout content-layout--full">
        <main class="main-content">
            <div class="section-header">
                <h2><?= $keyword ? 'ผลการค้นหา: "' . e($keyword) . '"' : 'ค้นหาอนิเมะ' ?></h2>
                <?php if ($keyword): ?>
                    <span class="count-label"><?= count($movies) ?> เรื่อง</span>
                <?php endif; ?>
            </div>

            <?php if (!$keyword): ?>
            <div class="empty-state">
                <p>พิมพ์ชื่ออนิเมะในช่องค้นหาด้านบน</p>
            </div>
            <?php elseif (empty($movies)): ?>
            <div class="empty-state">
                <p>ไม่พบ "<?= e($keyword) ?>"</p>
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
