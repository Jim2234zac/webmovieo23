<?php
$pageTitle = 'จัดการตอน';
require_once __DIR__ . '/includes/auth.php';

$movieId = (int) ($_GET['movie_id'] ?? 0);
$movie = getMovieById($movieId);
if (!$movie) redirect(SITE_URL . '/admin/movies.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_episode') {
        $epNum = (int) ($_POST['episode_number'] ?? 0);
        $epTitle = trim($_POST['episode_title'] ?? '');
        $subType = in_array($_POST['subtitle_type'] ?? '', ['sub','dub','both']) ? $_POST['subtitle_type'] : 'sub';
        if ($epNum) {
            execute(
                'INSERT INTO episodes (movie_id, episode_number, episode_title, subtitle_type) VALUES (?,?,?,?)
                 ON DUPLICATE KEY UPDATE episode_title=VALUES(episode_title), subtitle_type=VALUES(subtitle_type)',
                [$movieId, $epNum, $epTitle, $subType]
            );
            $epId = (int) fetchOne('SELECT id FROM episodes WHERE movie_id=? AND episode_number=?', [$movieId, $epNum])['id'];
            $urls = [
                ['Streamtape', trim($_POST['url_streamtape'] ?? ''), '480p', 1],
                ['Doodstream', trim($_POST['url_doodstream'] ?? ''), '720p', 2],
                ['Google Drive', trim($_POST['url_google_drive'] ?? ''), '1080p', 3],
            ];
            foreach ($urls as [$srv, $url, $q, $ord]) {
                if ($url) {
                    execute(
                        'INSERT INTO video_sources (episode_id, server_name, embed_url, quality, sort_order) VALUES (?,?,?,?,?)',
                        [$epId, $srv, $url, $q, $ord]
                    );
                }
            }
            flashMessage('success', 'บันทึกตอนเรียบร้อย');
        }
    }

    if ($action === 'delete_episode') {
        execute('DELETE FROM episodes WHERE id = ? AND movie_id = ?', [(int) $_POST['episode_id'], $movieId]);
        flashMessage('success', 'ลบตอนเรียบร้อย');
    }

    if ($action === 'add_source') {
        execute(
            'INSERT INTO video_sources (episode_id, server_name, embed_url, quality, sort_order) VALUES (?,?,?,?,?)',
            [(int) $_POST['episode_id'], trim($_POST['server_name']), trim($_POST['embed_url']), $_POST['quality'], (int) $_POST['sort_order']]
        );
        flashMessage('success', 'เพิ่ม video source เรียบร้อย');
    }

    if ($action === 'delete_source') {
        execute('DELETE FROM video_sources WHERE id = ?', [(int) $_POST['source_id']]);
        flashMessage('success', 'ลบ source เรียบร้อย');
    }

    redirect(SITE_URL . '/admin/episodes.php?movie_id=' . $movieId);
}

require_once __DIR__ . '/includes/sidebar.php';
$flash = getFlashMessage();
$episodes = getEpisodes($movieId);
?>

<div class="admin-top">
    <h1>ตอน: <?= e($movie['title_th']) ?></h1>
    <a href="movies.php" class="btn btn-outline btn-sm">← กลับ</a>
</div>
<?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div><?php endif; ?>

<div class="admin-form" style="margin-bottom:24px;">
    <h2>เพิ่ม/แก้ไขตอน</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add_episode">
        <div class="form-row">
            <div class="form-group"><label>ตอนที่</label><input type="number" name="episode_number" min="1" required></div>
            <div class="form-group"><label>ชื่อตอน</label><input name="episode_title" placeholder="ตอนที่ 1"></div>
            <div class="form-group">
                <label>ซับ/พากย์</label>
                <select name="subtitle_type"><option value="sub">ซับไทย</option><option value="dub">พากย์ไทย</option><option value="both">ทั้งคู่</option></select>
            </div>
        </div>
        <div class="form-group"><label>Streamtape URL (480p)</label><input name="url_streamtape" placeholder="https://..."></div>
        <div class="form-group"><label>Doodstream URL (720p)</label><input name="url_doodstream" placeholder="https://..."></div>
        <div class="form-group"><label>Google Drive URL (1080p)</label><input name="url_google_drive" placeholder="https://..."></div>
        <button type="submit" class="btn btn-primary">บันทึกตอน</button>
    </form>
</div>

<?php foreach ($episodes as $ep):
    $sources = getVideoSources((int) $ep['id']);
?>
<div class="table-wrap" style="margin-bottom:20px;">
    <div style="padding:12px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border-color);">
        <strong>ตอนที่ <?= $ep['episode_number'] ?> — <?= e($ep['episode_title'] ?? '') ?></strong>
        <?= subtitleBadge($ep['subtitle_type']) ?>
        <form method="POST" onsubmit="return confirm('ลบตอนนี้?')">
            <input type="hidden" name="action" value="delete_episode">
            <input type="hidden" name="episode_id" value="<?= $ep['id'] ?>">
            <button class="btn btn-danger btn-sm">ลบตอน</button>
        </form>
    </div>
    <table class="data-table">
        <thead><tr><th>Server</th><th>Quality</th><th>URL</th><th>ลบ</th></tr></thead>
        <tbody>
        <?php foreach ($sources as $src): ?>
        <tr>
            <td><?= e($src['server_name']) ?></td>
            <td><?= e($src['quality']) ?></td>
            <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;"><?= e($src['embed_url']) ?></td>
            <td>
                <form method="POST" style="display:inline">
                    <input type="hidden" name="action" value="delete_source">
                    <input type="hidden" name="source_id" value="<?= $src['id'] ?>">
                    <button class="btn btn-danger btn-sm">ลบ</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
