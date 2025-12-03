<?php
require_once 'db.php';

$pdo = getDBConnection();

if (!$pdo) {
    die("Erreur de connexion à la base de données");
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$articleId = (int)($_POST['article_id'] ?? $_GET['article_id'] ?? 0);
$redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? '../index.php';

if (strpos($redirect, 'http') === 0 && strpos($redirect, $_SERVER['HTTP_HOST']) === false) {
    $redirect = '../index.php';
}

try {
    switch ($action) {
        case 'like':
            handleLike($pdo, $articleId);
            break;
            
        case 'unlike':
            handleUnlike($pdo, $articleId);
            break;
            
        case 'comment':
            $authorName = trim($_POST['author_name'] ?? '');
            $content = trim($_POST['content'] ?? '');
            handleComment($pdo, $articleId, $authorName, $content);
            break;
            
        default:
            break;
    }
} catch (PDOException $e) {
    error_log("Interaction error: " . $e->getMessage());
}

header("Location: " . $redirect);
exit;

function handleLike($pdo, $articleId) {
    $sessionId = $_SESSION['visitor_id'] ?? '';
    
    if (empty($sessionId) || $articleId <= 0) {
        return false;
    }
    
    $stmt = $pdo->prepare("SELECT 1 FROM likes WHERE article_id = ? AND session_id = ?");
    $stmt->execute([$articleId, $sessionId]);
    
    if ($stmt->fetch()) {
        return handleUnlike($pdo, $articleId);
    }
    }
    
    $stmt = $pdo->prepare("INSERT INTO likes (article_id, session_id) VALUES (?, ?)");
    $stmt->execute([$articleId, $sessionId]);
    
    $stmt = $pdo->prepare("UPDATE articles SET likes_count = likes_count + 1 WHERE id = ?");
    $stmt->execute([$articleId]);
    
    return true;
}

function handleUnlike($pdo, $articleId) {
    $sessionId = $_SESSION['visitor_id'] ?? '';
    
    if (empty($sessionId) || $articleId <= 0) {
        return false;
    }
    
    $stmt = $pdo->prepare("DELETE FROM likes WHERE article_id = ? AND session_id = ?");
    $stmt->execute([$articleId, $sessionId]);
    
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("UPDATE articles SET likes_count = GREATEST(likes_count - 1, 0) WHERE id = ?");
        $stmt->execute([$articleId]);
    }
    
    return true;
}

function handleComment($pdo, $articleId, $authorName, $content) {
    if ($articleId <= 0 || empty($authorName) || empty($content)) {
        return false;
    }
    
    $authorName = substr($authorName, 0, 100);
    $content = substr($content, 0, 1000);
    
    $stmt = $pdo->prepare("INSERT INTO comments (article_id, author_name, content) VALUES (?, ?, ?)");
    $stmt->execute([$articleId, $authorName, $content]);
    
    return true;
}
?>
