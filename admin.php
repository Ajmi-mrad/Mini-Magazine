<?php
require_once 'php/db.php';

$pdo = getDBConnection();

// Get action from URL
$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);

// Get messages
$successMessage = '';
$errorMessage = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':
            $successMessage = 'Article ajouté avec succès !';
            break;
        case 'updated':
            $successMessage = 'Article mis à jour avec succès !';
            break;
        case 'deleted':
            $successMessage = 'Article supprimé avec succès !';
            break;
    }
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'validation':
            $errorMessage = 'Veuillez remplir tous les champs obligatoires.';
            break;
        case 'db_error':
            $errorMessage = 'Une erreur de base de données est survenue.';
            break;
        default:
            $errorMessage = 'Une erreur est survenue.';
    }
}

// Fetch all articles for listing
$articles = getArticles();

// Fetch article for editing
$editArticle = null;
if ($action === 'edit' && $editId > 0) {
    $editArticle = getArticleById($editId);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - TechLife Magazine</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <nav class="navbar admin-navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="admin.php"><h2>TechLife Admin</h2></a>
            </div>
            <ul class="nav-menu">
                <li><a href="admin.php" class="nav-link <?= $action === 'list' ? 'active' : '' ?>">Articles</a></li>
                <li><a href="admin.php?action=add" class="nav-link <?= $action === 'add' ? 'active' : '' ?>">Ajouter</a></li>
                <li><a href="index.php" class="nav-link">Voir le site</a></li>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <main class="admin-main">
        <div class="container">
            <?php if ($successMessage): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= h($successMessage) ?>
            </div>
            <?php endif; ?>
            
            <?php if ($errorMessage): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= h($errorMessage) ?>
            </div>
            <?php endif; ?>

            <?php if ($action === 'list'): ?>
            <!-- Articles List -->
            <div class="admin-section">
                <div class="admin-header">
                    <h1>Gestion des articles</h1>
                    <a href="admin.php?action=add" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvel article
                    </a>
                </div>

                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Titre</th>
                                <th>Catégorie</th>
                                <th>Auteur</th>
                                <th>Likes</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($articles)): ?>
                            <tr>
                                <td colspan="8" class="no-data">Aucun article trouvé</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($articles as $article): ?>
                                <tr>
                                    <td><?= $article['id'] ?></td>
                                    <td>
                                        <img src="<?= h($article['image_url']) ?>" alt="" class="table-thumbnail">
                                    </td>
                                    <td><?= h($article['title']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $article['category'] ?>">
                                            <?= ucfirst($article['category']) ?>
                                        </span>
                                    </td>
                                    <td><?= h($article['author']) ?></td>
                                    <td><?= $article['likes_count'] ?></td>
                                    <td>
                                        <?php if ($article['featured']): ?>
                                            <i class="fas fa-star text-warning"></i>
                                        <?php else: ?>
                                            <i class="far fa-star text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions-cell">
                                        <a href="admin.php?action=edit&id=<?= $article['id'] ?>" class="btn btn-sm btn-edit" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="php/manage_articles.php" class="delete-form" onsubmit="return confirmDelete(this)">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $article['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-delete" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <!-- Add/Edit Article Form -->
            <div class="admin-section">
                <div class="admin-header">
                    <h1><?= $action === 'add' ? 'Ajouter un article' : 'Modifier l\'article' ?></h1>
                    <a href="admin.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>

                <form method="POST" action="php/manage_articles.php" class="admin-form" id="article-form">
                    <input type="hidden" name="action" value="<?= $action ?>">
                    <?php if ($editArticle): ?>
                    <input type="hidden" name="id" value="<?= $editArticle['id'] ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Titre *</label>
                            <input type="text" id="title" name="title" required 
                                   value="<?= $editArticle ? h($editArticle['title']) : '' ?>">
                            <span class="error-message" id="title-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="author">Auteur *</label>
                            <input type="text" id="author" name="author" required
                                   value="<?= $editArticle ? h($editArticle['author']) : '' ?>">
                            <span class="error-message" id="author-error"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="category">Catégorie *</label>
                            <select id="category" name="category" required>
                                <option value="articles" <?= ($editArticle && $editArticle['category'] === 'articles') ? 'selected' : '' ?>>Articles</option>
                                <option value="tech" <?= ($editArticle && $editArticle['category'] === 'tech') ? 'selected' : '' ?>>Tech</option>
                                <option value="lifestyle" <?= ($editArticle && $editArticle['category'] === 'lifestyle') ? 'selected' : '' ?>>Lifestyle</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="image_url">URL de l'image *</label>
                            <input type="url" id="image_url" name="image_url" required
                                   placeholder="https://images.unsplash.com/..."
                                   value="<?= $editArticle ? h($editArticle['image_url']) : '' ?>">
                            <span class="error-message" id="image_url-error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="excerpt">Extrait *</label>
                        <textarea id="excerpt" name="excerpt" rows="2" required><?= $editArticle ? h($editArticle['excerpt']) : '' ?></textarea>
                        <span class="error-message" id="excerpt-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="content">Contenu complet</label>
                        <textarea id="content" name="content" rows="8"><?= $editArticle ? h($editArticle['content']) : '' ?></textarea>
                    </div>

                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="featured" value="1" 
                                   <?= ($editArticle && $editArticle['featured']) ? 'checked' : '' ?>>
                            <span class="checkmark"></span>
                            Article en vedette (affiché sur la page d'accueil)
                        </label>
                    </div>

                    <div class="form-group image-preview-group">
                        <label>Aperçu de l'image</label>
                        <div class="image-preview" id="image-preview">
                            <?php if ($editArticle && $editArticle['image_url']): ?>
                            <img src="<?= h($editArticle['image_url']) ?>" alt="Preview">
                            <?php else: ?>
                            <span class="no-preview">Entrez une URL d'image pour voir l'aperçu</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            <?= $action === 'add' ? 'Créer l\'article' : 'Enregistrer les modifications' ?>
                        </button>
                        <a href="admin.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer admin-footer">
        <div class="container">
            <p>&copy; 2025 TechLife Magazine - Panneau d'administration</p>
        </div>
    </footer>

    <script src="js/app.js"></script>
    <script>
        document.getElementById('image_url')?.addEventListener('input', function() {
            const preview = document.getElementById('image-preview');
            const url = this.value.trim();
            
            if (url) {
                preview.innerHTML = '<img src="' + url + '" alt="Preview" onerror="this.parentElement.innerHTML=\'<span class=\\'no-preview\\'>Image non trouvée</span>\'">';
            } else {
                preview.innerHTML = '<span class="no-preview">Entrez une URL d\'image pour voir l\'aperçu</span>';
            }
        });

        function confirmDelete(form) {
            const confirmed = confirm('Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible.');
            if (confirmed) {
                return true;
            }
            return false;
        }

        document.getElementById('article-form')?.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = ['title', 'author', 'excerpt', 'image_url'];
            
            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                const errorEl = document.getElementById(fieldName + '-error');
                
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                    if (errorEl) {
                        errorEl.textContent = 'Ce champ est obligatoire';
                        errorEl.classList.add('show');
                    }
                } else {
                    field.classList.remove('error');
                    if (errorEl) {
                        errorEl.classList.remove('show');
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
            }
        });
    </script>
</body>
</html>
