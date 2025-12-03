<?php
/**
 * Get Articles API
 * Returns articles data for JavaScript to read via iframe
 * TechLife Magazine
 */

require_once 'db.php';

// Get parameters
$category = $_GET['category'] ?? null;
$featured = isset($_GET['featured']) && $_GET['featured'] === '1';
$format = $_GET['format'] ?? 'html';

// Fetch articles
$articles = getArticles($category, $featured);

// If JSON format requested (for iframe reading)
if ($format === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    
    // Add hasLiked status to each article
    foreach ($articles as &$article) {
        $article['hasLiked'] = hasLiked($article['id']);
        $article['formatted_date'] = formatDate($article['created_at']);
    }
    
    echo json_encode(['success' => true, 'articles' => $articles], JSON_UNESCAPED_UNICODE);
    exit;
}

// HTML format - render article cards directly
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
<div class="articles-data" id="articles-data">
<?php if (empty($articles)): ?>
    <div class="no-articles">
        <p>Aucun article disponible pour le moment.</p>
    </div>
<?php else: ?>
    <?php foreach ($articles as $article): 
        $hasLiked = hasLiked($article['id']);
        $likedClass = $hasLiked ? 'liked' : '';
        $heartIcon = $hasLiked ? 'fas fa-heart' : 'far fa-heart';
    ?>
    <article class="article-card" data-id="<?= $article['id'] ?>">
        <img src="<?= h($article['image_url']) ?>" alt="<?= h($article['title']) ?>">
        <div class="article-content">
            <h3><?= h($article['title']) ?></h3>
            <p><?= h($article['excerpt']) ?></p>
            <div class="article-meta">
                <span class="author"><?= h($article['author']) ?></span>
                <span class="date"><?= formatDate($article['created_at']) ?></span>
            </div>
            <div class="article-actions">
                <form method="POST" action="php/interact.php" class="like-form">
                    <input type="hidden" name="action" value="like">
                    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                    <input type="hidden" name="redirect" value="<?= h($_SERVER['HTTP_REFERER'] ?? 'index.php') ?>">
                    <button type="submit" class="like-btn <?= $likedClass ?>" data-id="<?= $article['id'] ?>">
                        <i class="<?= $heartIcon ?>"></i>
                        <span class="like-count"><?= $article['likes_count'] ?></span>
                    </button>
                </form>
                <button type="button" class="comment-btn" data-id="<?= $article['id'] ?>">
                    <i class="far fa-comment"></i>
                    <span class="comment-count"><?= $article['comments_count'] ?></span>
                </button>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
<?php endif; ?>
</div>
</body>
</html>
