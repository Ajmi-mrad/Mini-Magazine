<?php
/**
 * Article Management Handler
 * Handles CRUD operations for articles (Admin)
 * TechLife Magazine
 */

require_once 'db.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin.php?error=invalid_request");
    exit;
}

$pdo = getDBConnection();

if (!$pdo) {
    header("Location: ../admin.php?error=db_error");
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            addArticle($pdo);
            break;
            
        case 'edit':
            editArticle($pdo);
            break;
            
        case 'delete':
            deleteArticle($pdo);
            break;
            
        default:
            header("Location: ../admin.php?error=invalid_action");
            exit;
    }
} catch (PDOException $e) {
    error_log("Article management error: " . $e->getMessage());
    header("Location: ../admin.php?error=db_error");
    exit;
}

/**
 * Add new article
 */
function addArticle($pdo) {
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $category = $_POST['category'] ?? 'articles';
    $imageUrl = trim($_POST['image_url'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Validation
    if (empty($title) || empty($excerpt) || empty($author) || empty($imageUrl)) {
        header("Location: ../admin.php?error=validation&action=add");
        exit;
    }
    
    // Validate category
    $validCategories = ['articles', 'tech', 'lifestyle'];
    if (!in_array($category, $validCategories)) {
        $category = 'articles';
    }
    
    // Insert article
    $stmt = $pdo->prepare("INSERT INTO articles (title, excerpt, content, author, category, image_url, featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $excerpt, $content, $author, $category, $imageUrl, $featured]);
    
    header("Location: ../admin.php?success=added");
    exit;
}

/**
 * Edit existing article
 */
function editArticle($pdo) {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $category = $_POST['category'] ?? 'articles';
    $imageUrl = trim($_POST['image_url'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Validation
    if ($id <= 0 || empty($title) || empty($excerpt) || empty($author) || empty($imageUrl)) {
        header("Location: ../admin.php?error=validation&action=edit&id=" . $id);
        exit;
    }
    
    // Validate category
    $validCategories = ['articles', 'tech', 'lifestyle'];
    if (!in_array($category, $validCategories)) {
        $category = 'articles';
    }
    
    // Update article
    $stmt = $pdo->prepare("UPDATE articles SET title = ?, excerpt = ?, content = ?, author = ?, category = ?, image_url = ?, featured = ? WHERE id = ?");
    $stmt->execute([$title, $excerpt, $content, $author, $category, $imageUrl, $featured, $id]);
    
    header("Location: ../admin.php?success=updated");
    exit;
}

/**
 * Delete article
 */
function deleteArticle($pdo) {
    $id = (int)($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        header("Location: ../admin.php?error=invalid_id");
        exit;
    }
    
    // Delete article (comments and likes will be deleted via CASCADE)
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: ../admin.php?success=deleted");
    exit;
}
?>
