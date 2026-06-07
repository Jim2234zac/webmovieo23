<?php
require_once __DIR__ . '/includes/functions.php';

$movieId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$episodeNum = isset($_GET['ep']) ? (int) $_GET['ep'] : 1;

$movie = getMovieById($movieId);
if (!$movie) redirect(SITE_URL . '/index.php');

$episodes = getEpisodes($movieId);
if (empty($episodes)) redirect(SITE_URL . '/index.php');

$currentEpisode = getEpisode($movieId, $episodeNum) ?: $episodes[0];
$episodeNum = (int) $currentEpisode['episode_number'];
$sources = getVideoSources((int) $currentEpisode['id']);

incrementViews($movieId);

$pageTitle = $movie['title_th'] . ' ตอนที่ ' . $episodeNum;
require_once __DIR__ . '/includes/header.php';

$servers = [];
$qualities = [];
foreach ($sources as $src) {
    $servers[$src['server_name']] = $src['server_name'];
    $qualities[$src['quality']] = $src['quality'];
}
$defaultSource = $sources[0] ?? null;
$hasTopBanner = !empty($bannersTop);
?>

<div class="watch-container<?= $hasTopBanner ? ' has-top-banner' : '' ?>">
    <div class="video-section">
        <div class="video-player">
            <iframe id="video-frame"
                    src="<?= e($defaultSource['embed_url'] ?? '') ?>"
                    allowfullscreen
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
            </iframe>
        </div>

        <?php if ($sources): ?>
        <div class="player-controls">
            <div class="control-group">
                <span class="control-label">เซิร์ฟเวอร์</span>
                <div class="btn-group" id="server-buttons">
                    <?php $si = 0; foreach ($servers as $name): ?>
                    <button type="button"
                            class="ctrl-btn server-btn <?= $si === 0 ? 'active' : '' ?>"
                            data-server="<?= e($name) ?>">
                        Server <?= $si + 1 ?> <?= e($name) ?>
                    </button>
                    <?php $si++; endforeach; ?>
                </div>
            </div>
            <div class="control-group">
                <span class="control-label">คุณภาพ</span>
                <div class="btn-group" id="quality-buttons">
                    <?php foreach (['480p', '720p', '1080p'] as $q): if (!isset($qualities[$q])) continue; ?>
                    <button type="button"
                            class="ctrl-btn quality-btn <?= ($defaultSource && $defaultSource['quality'] === $q) ? 'active' : '' ?>"
                            data-quality="<?= $q ?>">
                        <?= $q ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <script id="video-sources-data" type="application/json">
            <?= json_encode($sources, JSON_UNESCAPED_UNICODE) ?>
        </script>
        <?php endif; ?>
    </div>

    <div class="watch-info">
        <h1><?= e($movie['title_th']) ?> — ตอนที่ <?= $episodeNum ?></h1>
        <?php if ($currentEpisode['episode_title']): ?>
            <p class="ep-title"><?= e($currentEpisode['episode_title']) ?></p>
        <?php endif; ?>
        <div class="watch-meta">
            <?= statusBadge($movie['status']) ?>
            <?= subtitleBadge($currentEpisode['subtitle_type']) ?>
            <?php if ($movie['category_name']): ?><span>📂 <?= e($movie['category_name']) ?></span><?php endif; ?>
            <?php if ($movie['year']): ?><span>📅 <?= (int) $movie['year'] ?></span><?php endif; ?>
            <span>👁 <?= number_format($movie['views']) ?> ครั้ง</span>
        </div>
        <?php if ($movie['description']): ?>
            <p class="watch-desc"><?= e($movie['description']) ?></p>
        <?php endif; ?>
    </div>

    <div class="episode-list">
        <h3>รายการตอน (<?= count($episodes) ?> ตอน)</h3>
        <div class="episode-grid">
            <?php foreach ($episodes as $ep): ?>
            <a href="<?= SITE_URL ?>/watch.php?id=<?= $movieId ?>&ep=<?= $ep['episode_number'] ?>"
               class="episode-btn <?= $ep['episode_number'] == $episodeNum ? 'active' : '' ?>">
                <?= $ep['episode_number'] ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
