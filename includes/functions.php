<?php
require_once __DIR__ . '/db.php';

function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function getBanners(string $position): array
{
    return fetchAll(
        'SELECT * FROM banners
         WHERE position = ? AND active = 1
         AND (expire_date IS NULL OR expire_date >= CURDATE())
         ORDER BY id DESC',
        [$position]
    );
}

function getCategories(): array
{
    return fetchAll('SELECT * FROM categories ORDER BY name_th ASC');
}

function getLatestMovies(int $limit = 24, ?int $categoryId = null): array
{
    $sql = 'SELECT m.*, c.name_th AS category_name,
            (SELECT MAX(episode_number) FROM episodes WHERE movie_id = m.id) AS latest_episode,
            (SELECT subtitle_type FROM episodes WHERE movie_id = m.id ORDER BY episode_number DESC LIMIT 1) AS latest_subtitle
            FROM movies m
            LEFT JOIN categories c ON m.category_id = c.id';
    $params = [];

    if ($categoryId) {
        $sql .= ' WHERE m.category_id = ?';
        $params[] = $categoryId;
    }

    $sql .= ' ORDER BY m.created_at DESC LIMIT ?';
    $params[] = $limit;

    return fetchAll($sql, $params);
}

function getMovieById(int $id): ?array
{
    return fetchOne(
        'SELECT m.*, c.name_th AS category_name
         FROM movies m LEFT JOIN categories c ON m.category_id = c.id
         WHERE m.id = ?',
        [$id]
    );
}

function getEpisodes(int $movieId): array
{
    return fetchAll(
        'SELECT * FROM episodes WHERE movie_id = ? ORDER BY episode_number ASC',
        [$movieId]
    );
}

function getEpisode(int $movieId, int $episodeNumber): ?array
{
    return fetchOne(
        'SELECT * FROM episodes WHERE movie_id = ? AND episode_number = ?',
        [$movieId, $episodeNumber]
    );
}

function getVideoSources(int $episodeId): array
{
    return fetchAll(
        'SELECT * FROM video_sources WHERE episode_id = ? ORDER BY sort_order ASC, id ASC',
        [$episodeId]
    );
}

function incrementViews(int $movieId): void
{
    execute('UPDATE movies SET views = views + 1 WHERE id = ?', [$movieId]);
}

function searchMovies(string $keyword, int $limit = 48): array
{
    $kw = '%' . trim($keyword) . '%';
    return fetchAll(
        'SELECT m.*, c.name_th AS category_name,
         (SELECT MAX(episode_number) FROM episodes WHERE movie_id = m.id) AS latest_episode,
         (SELECT subtitle_type FROM episodes WHERE movie_id = m.id ORDER BY episode_number DESC LIMIT 1) AS latest_subtitle
         FROM movies m LEFT JOIN categories c ON m.category_id = c.id
         WHERE m.title LIKE ? OR m.title_th LIKE ? OR m.description LIKE ?
         ORDER BY m.views DESC LIMIT ?',
        [$kw, $kw, $kw, $limit]
    );
}

function subtitleBadge(string $type): string
{
    return match ($type) {
        'sub'  => '<span class="badge badge-sub">ซับไทย</span>',
        'dub'  => '<span class="badge badge-dub">พากย์ไทย</span>',
        'both' => '<span class="badge badge-both">ซับ+พากย์</span>',
        default => '',
    };
}

function statusBadge(string $status): string
{
    return match ($status) {
        'ongoing'   => '<span class="badge badge-ongoing">กำลังฉาย</span>',
        'completed' => '<span class="badge badge-completed">จบแล้ว</span>',
        default     => '',
    };
}

function uploadImage(array $file, string $prefix = 'img'): ?string
{
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($file['type'], $allowed)) return null;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) return null;
    if ($file['size'] > 5242880) return null;
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
    $filename = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $filename)) {
        return UPLOAD_URL . $filename;
    }
    return null;
}

function flashMessage(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlashMessage(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function generateCsrfToken(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function getDashboardStats(): array
{
    return [
        'movies'     => (int) fetchOne('SELECT COUNT(*) AS c FROM movies')['c'],
        'episodes'   => (int) fetchOne('SELECT COUNT(*) AS c FROM episodes')['c'],
        'categories' => (int) fetchOne('SELECT COUNT(*) AS c FROM categories')['c'],
        'users'      => (int) fetchOne('SELECT COUNT(*) AS c FROM users')['c'],
        'banners'    => (int) fetchOne('SELECT COUNT(*) AS c FROM banners WHERE active = 1')['c'],
        'views'      => (int) fetchOne('SELECT COALESCE(SUM(views),0) AS c FROM movies')['c'],
    ];
}

function renderMovieCard(array $movie): string
{
    ob_start();
    ?>
    <article class="movie-card">
        <a href="<?= SITE_URL ?>/watch.php?id=<?= $movie['id'] ?>">
            <div class="movie-thumb">
                <img src="<?= e($movie['thumbnail'] ?: 'https://picsum.photos/seed/default/300/400') ?>"
                     alt="<?= e($movie['title_th']) ?>" loading="lazy">
                <div class="movie-badges">
                    <?= statusBadge($movie['status']) ?>
                    <?php if (!empty($movie['latest_subtitle'])): ?>
                        <?= subtitleBadge($movie['latest_subtitle']) ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="movie-info">
                <h3><?= e($movie['title_th']) ?></h3>
                <div class="episode-info">
                    <?php if ($movie['latest_episode']): ?>
                        ตอนที่ <?= (int) $movie['latest_episode'] ?>
                    <?php else: ?>ยังไม่มีตอน<?php endif; ?>
                    <?php if ($movie['year']): ?> · <?= (int) $movie['year'] ?><?php endif; ?>
                </div>
                <div class="views">👁 <?= number_format($movie['views']) ?> ครั้ง</div>
            </div>
        </a>
    </article>
    <?php
    return ob_get_clean();
}
