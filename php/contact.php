<?php
/**
 * Contact Form Handler
 * Processes contact form submissions
 * TechLife Magazine
 */

require_once 'db.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php?page=contact&error=invalid_request");
    exit;
}

$pdo = getDBConnection();

if (!$pdo) {
    header("Location: ../index.php?page=contact&error=db_error");
    exit;
}

// Get and sanitize form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation
$errors = [];

if (strlen($name) < 2) {
    $errors[] = 'name';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'email';
}

$validSubjects = ['info', 'collaboration', 'feedback', 'autre'];
if (!in_array($subject, $validSubjects)) {
    $errors[] = 'subject';
}

if (strlen($message) < 10) {
    $errors[] = 'message';
}

// If there are errors, redirect back
if (!empty($errors)) {
    $errorString = implode(',', $errors);
    header("Location: ../index.php?page=contact&error=validation&fields=" . urlencode($errorString));
    exit;
}

// Save to database
try {
    $stmt = $pdo->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message]);
    
    // Success - redirect with success message
    header("Location: ../index.php?page=contact&success=1");
    exit;
    
} catch (PDOException $e) {
    error_log("Contact form error: " . $e->getMessage());
    header("Location: ../index.php?page=contact&error=db_error");
    exit;
}
?>
