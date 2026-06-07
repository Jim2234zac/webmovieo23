<?php
$pageTitle = 'แดชบอร์ด';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/sidebar.php';

$stats = getDashboardStats();
$flash = getFlashMessage();

// รับค่าการค้นหา
$search = trim($_REQUEST['search'] ?? '');

// ถ้ามีการค้นหา แสดงผลลัพธ์การค้นหา
if ($search !== '') {
    $movies = fetchAll(
        'SELECT m.*, c.name_th AS category_name FROM movies m
         LEFT JOIN categories c ON m.category_id = c.id
         WHERE m.title_th LIKE ? OR m.title LIKE ?
         ORDER BY m.created_at DESC',
        ["%$search%", "%$search%"]
    );
} else {
    // แสดงหนังล่าสุด 5 เรื่อง
    $movies = fetchAll(
        'SELECT m.*, c.name_th AS category_name FROM movies m
         LEFT JOIN categories c ON m.category_id = c.id
         ORDER BY m.created_at DESC LIMIT 5'
    );
}
?>

<div class="admin-top">
    <h1>แดชบอร์ด</h1>
    <span>สวัสดี, <?= e($_SESSION['username']) ?></span>
</div>

<?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div><?php endif; ?>

<div class="panel">
    <form method="GET" action="index.php" class="form-inline">
        <div class="form-group">
            <label>ค้นหาหนัง</label>
            <input type="text" name="search" value="<?= e($search) ?>" placeholder="ชื่อไทย หรือ ชื่ออังกฤษ">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">ค้นหา</button>
            <?php if ($search !== ''): ?>
            <a href="index.php" class="btn btn-outline">รีเซ็ต</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="stats-grid">
    <div class="stat-card"><div class="stat-value"><?= number_format($stats['movies']) ?></div><div class="stat-label">หนัง/อนิเมะ</div></div>
    <div class="stat-card"><div class="stat-value"><?= number_format($stats['episodes']) ?></div><div class="stat-label">ตอน</div></div>
    <div class="stat-card"><div class="stat-value"><?= number_format($stats['users']) ?></div><div class="stat-label">ผู้ใช้</div></div>
    <div class="stat-card"><div class="stat-value"><?= number_format($stats['views']) ?></div><div class="stat-label">ยอดดูรวม</div></div>
</div>

<h2 class="admin-subtitle"><?= $search !== '' ? 'ผลการค้นหา (' . count($movies) . ' รายการ)' : 'หนังล่าสุด' ?></h2>
<div class="table-wrap">
    <table class="data-table" id="movies-table">
        <thead><tr><th>ชื่อ</th><th>หมวด</th><th>สถานะ</th><th>ยอดดู</th></tr></thead>
        <tbody id="movies-body">
        <?php foreach ($movies as $m): ?>
        <tr data-movie-id="<?= $m['id'] ?>">
            <td><?= e($m['title_th']) ?></td>
            <td><?= e($m['category_name'] ?? '-') ?></td>
            <td><?= statusBadge($m['status']) ?></td>
            <td class="view-count" data-views="<?= $m['views'] ?>"><?= number_format($m['views']) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
// Real-time view count updates
function updateStats() {
    fetch('api_stats.php?search=<?= e($search) ?>')
        .then(response => response.json())
        .then(data => {
            // Update stats cards
            document.querySelector('.stat-card:nth-child(1) .stat-value').textContent = data.stats.movies.toLocaleString();
            document.querySelector('.stat-card:nth-child(2) .stat-value').textContent = data.stats.episodes.toLocaleString();
            document.querySelector('.stat-card:nth-child(3) .stat-value').textContent = data.stats.users.toLocaleString();
            document.querySelector('.stat-card:nth-child(4) .stat-value').textContent = data.stats.views.toLocaleString();

            // Update movie view counts
            const movieRows = document.querySelectorAll('#movies-body tr');
            const movieMap = {};
            data.movies.forEach(m => {
                movieMap[m.id] = m.views;
            });

            movieRows.forEach(row => {
                const movieId = row.dataset.movieId;
                const viewCell = row.querySelector('.view-count');
                if (movieMap[movieId] !== undefined && viewCell) {
                    const oldViews = parseInt(viewCell.dataset.views);
                    const newViews = movieMap[movieId];
                    if (newViews !== oldViews) {
                        viewCell.dataset.views = newViews;
                        viewCell.textContent = newViews.toLocaleString();
                        viewCell.style.color = '#00d4ff';
                        setTimeout(() => {
                            viewCell.style.color = '';
                        }, 1000);
                    }
                }
            });
        })
        .catch(error => console.error('Error updating stats:', error));
}

// Update every 5 seconds
setInterval(updateStats, 5000);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
