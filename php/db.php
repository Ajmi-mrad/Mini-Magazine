<?php
/**
 * Database Connection Configuration
 * TechLife Magazine
 */

// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'techlife_magazine');
define('DB_USER', 'root');
define('DB_PASS', 'root123');
define('DB_CHARSET', 'utf8mb4');

// Start session for tracking likes
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate unique session ID for like tracking
if (!isset($_SESSION['visitor_id'])) {
    $_SESSION['visitor_id'] = bin2hex(random_bytes(16));
}

/**
 * Get PDO database connection
 * @return PDO|null
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            return null;
        }
    }
    
    return $pdo;
}

/**
 * Sanitize output for HTML
 * @param string $data
 * @return string
 */
function h($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Get all articles with optional category filter
 * @param string|null $category
 * @param bool $featuredOnly
 * @return array
 */
function getArticles($category = null, $featuredOnly = false) {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    $sql = "SELECT a.*, 
            (SELECT COUNT(*) FROM comments c WHERE c.article_id = a.id) as comments_count
            FROM articles a WHERE 1=1";
    $params = [];
    
    if ($category) {
        $sql .= " AND a.category = ?";
        $params[] = $category;
    }
    
    if ($featuredOnly) {
        $sql .= " AND a.featured = 1";
    }
    
    $sql .= " ORDER BY a.created_at DESC";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching articles: " . $e->getMessage());
        return [];
    }
}

/**
 * Get single article by ID
 * @param int $id
 * @return array|null
 */
function getArticleById($id) {
    $pdo = getDBConnection();
    if (!$pdo) return null;
    
    try {
        $stmt = $pdo->prepare("SELECT a.*, 
            (SELECT COUNT(*) FROM comments c WHERE c.article_id = a.id) as comments_count
            FROM articles a WHERE a.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching article: " . $e->getMessage());
        return null;
    }
}

/**
 * Get comments for an article
 * @param int $articleId
 * @return array
 */
function getComments($articleId) {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM comments WHERE article_id = ? ORDER BY created_at DESC");
        $stmt->execute([$articleId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching comments: " . $e->getMessage());
        return [];
    }
}

/**
 * Check if current session has liked an article
 * @param int $articleId
 * @return bool
 */
function hasLiked($articleId) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    
    $sessionId = $_SESSION['visitor_id'] ?? '';
    if (empty($sessionId)) return false;
    
    try {
        $stmt = $pdo->prepare("SELECT 1 FROM likes WHERE article_id = ? AND session_id = ?");
        $stmt->execute([$articleId, $sessionId]);
        return $stmt->fetch() !== false;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Format date to French format
 * @param string $date
 * @return string
 */
function formatDate($date) {
    $months = [
        1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr', 5 => 'Mai', 6 => 'Juin',
        7 => 'Juil', 8 => 'Août', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc'
    ];
    
    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = $months[(int)date('n', $timestamp)];
    $year = date('Y', $timestamp);
    
    return "$day $month $year";
}
?>
