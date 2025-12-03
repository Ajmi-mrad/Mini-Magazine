<?php
/**
 * TechLife Magazine - Main Page
 * Dynamic PHP version with database integration
 */

require_once 'php/db.php';

// Get current page
$currentPage = $_GET['page'] ?? 'home';
$validPages = ['home', 'articles', 'tech', 'lifestyle', 'contact'];
if (!in_array($currentPage, $validPages)) {
    $currentPage = 'home';
}

// Get messages from URL
$successMessage = isset($_GET['success']) ? 'Message envoyé avec succès !' : '';
$errorMessage = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'validation':
            $errorMessage = 'Veuillez corriger les erreurs dans le formulaire.';
            break;
        case 'db_error':
            $errorMessage = 'Une erreur est survenue. Veuillez réessayer.';
            break;
        default:
            $errorMessage = 'Une erreur est survenue.';
    }
}

// Fetch featured articles for home page
$featuredArticles = getArticles(null, true);
if (count($featuredArticles) < 3) {
    // If not enough featured, get latest articles
    $featuredArticles = array_slice(getArticles(), 0, 3);
}

// Fetch articles based on current page
$pageArticles = [];
if ($currentPage === 'articles') {
    $pageArticles = getArticles('articles');
} elseif ($currentPage === 'tech') {
    $pageArticles = getArticles('tech');
} elseif ($currentPage === 'lifestyle') {
    $pageArticles = getArticles('lifestyle');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechLife Magazine</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php"><h2>TechLife</h2></a>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php?page=home" class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>">Accueil</a></li>
                <li><a href="index.php?page=articles" class="nav-link <?= $currentPage === 'articles' ? 'active' : '' ?>">Articles</a></li>
                <li><a href="index.php?page=tech" class="nav-link <?= $currentPage === 'tech' ? 'active' : '' ?>">Tech</a></li>
                <li><a href="index.php?page=lifestyle" class="nav-link <?= $currentPage === 'lifestyle' ? 'active' : '' ?>">Lifestyle</a></li>
                <li><a href="index.php?page=contact" class="nav-link <?= $currentPage === 'contact' ? 'active' : '' ?>">Contact</a></li>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <main id="main-content">
        <!-- HOME PAGE -->
        <section id="home" class="page <?= $currentPage === 'home' ? 'active' : '' ?>">
            <div class="hero">
                <div class="carousel-container">
                    <div class="carousel">
                        <div class="carousel-item active">
                            <img src="https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=1200&h=600&fit=crop" alt="Tech Innovation">
                            <div class="carousel-content">
                                <h1>L'avenir de la technologie</h1>
                                <p>Découvrez les dernières innovations qui façonnent notre monde</p>
                                <a href="index.php?page=tech" class="cta-btn">Lire plus</a>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1200&h=600&fit=crop" alt="Data Science">
                            <div class="carousel-content">
                                <h1>Intelligence Artificielle</h1>
                                <p>Comment l'IA révolutionne notre quotidien</p>
                                <a href="index.php?page=tech" class="cta-btn">Explorer</a>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200&h=600&fit=crop" alt="Digital Life">
                            <div class="carousel-content">
                                <h1>Vie Digitale</h1>
                                <p>Equilibrer technologie et bien-être au quotidien</p>
                                <a href="index.php?page=lifestyle" class="cta-btn">Découvrir</a>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-nav">
                        <button class="carousel-prev"><i class="fas fa-chevron-left"></i></button>
                        <button class="carousel-next"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <div class="carousel-dots">
                        <span class="dot active" data-slide="0"></span>
                        <span class="dot" data-slide="1"></span>
                        <span class="dot" data-slide="2"></span>
                    </div>
                </div>
            </div>

            <section class="featured-articles">
                <div class="container">
                    <h2>Articles populaires</h2>
                    <div class="articles-grid">
                        <?php foreach (array_slice($featuredArticles, 0, 3) as $article): 
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
                                    <form method="POST" action="php/interact.php" class="like-form" id="like-form-<?= $article['id'] ?>">
                                        <input type="hidden" name="action" value="like">
                                        <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                        <input type="hidden" name="redirect" value="index.php?page=<?= $currentPage ?>">
                                        <button type="submit" class="like-btn <?= $likedClass ?>" data-id="<?= $article['id'] ?>">
                                            <i class="<?= $heartIcon ?>"></i>
                                            <span class="like-count"><?= $article['likes_count'] ?></span>
                                        </button>
                                    </form>
                                    <button type="button" class="comment-btn" data-id="<?= $article['id'] ?>" onclick="openCommentModal(<?= $article['id'] ?>)">
                                        <i class="far fa-comment"></i>
                                        <span class="comment-count"><?= $article['comments_count'] ?? 0 ?></span>
                                    </button>
                                </div>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section class="testimonials">
                <div class="container">
                    <h2>Ce que disent nos lecteurs</h2>
                    <div class="testimonials-slider">
                        <div class="testimonial active">
                            <div class="testimonial-content">
                                <p>"TechLife m'aide à rester à jour avec les dernières innovations. Contenu de qualité !"</p>
                                <div class="testimonial-author">
                                    <img src="https://images.unsplash.com/photo-1494790108755-2616b332c1cb?w=60&h=60&fit=crop&crop=face" alt="Marie Dubois">
                                    <div>
                                        <h4>Marie Dubois</h4>
                                        <span>Développeuse Full-Stack</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="testimonial">
                            <div class="testimonial-content">
                                <p>"Excellents articles sur l'IA et la robotique. Une ressource indispensable pour ma veille technologique."</p>
                                <div class="testimonial-author">
                                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&h=60&fit=crop&crop=face" alt="Thomas Martin">
                                    <div>
                                        <h4>Thomas Martin</h4>
                                        <span>Ingénieur IA</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="testimonial">
                            <div class="testimonial-content">
                                <p>"Interface moderne et contenu accessible. Parfait pour comprendre les enjeux tech actuels."</p>
                                <div class="testimonial-author">
                                    <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=60&h=60&fit=crop&crop=face" alt="Lisa Chen">
                                    <div>
                                        <h4>Lisa Chen</h4>
                                        <span>Product Manager</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </section>

        <!-- ARTICLES PAGE -->
        <section id="articles" class="page <?= $currentPage === 'articles' ? 'active' : '' ?>">
            <div class="container">
                <h1>Tous les articles</h1>
                <div class="articles-grid" id="articles-content">
                    <?php if ($currentPage === 'articles'): ?>
                        <?php if (empty($pageArticles)): ?>
                            <div class="no-articles">
                                <p>Aucun article disponible pour le moment.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($pageArticles as $article): 
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
                                            <input type="hidden" name="redirect" value="index.php?page=<?= $currentPage ?>">
                                            <button type="submit" class="like-btn <?= $likedClass ?>" data-id="<?= $article['id'] ?>">
                                                <i class="<?= $heartIcon ?>"></i>
                                                <span class="like-count"><?= $article['likes_count'] ?></span>
                                            </button>
                                        </form>
                                        <button type="button" class="comment-btn" data-id="<?= $article['id'] ?>" onclick="openCommentModal(<?= $article['id'] ?>)">
                                            <i class="far fa-comment"></i>
                                            <span class="comment-count"><?= $article['comments_count'] ?? 0 ?></span>
                                        </button>
                                    </div>
                                </div>
                            </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- TECH PAGE -->
        <section id="tech" class="page <?= $currentPage === 'tech' ? 'active' : '' ?>">
            <div class="container">
                <h1>Technologie</h1>
                <div class="articles-grid" id="tech-content">
                    <?php if ($currentPage === 'tech'): ?>
                        <?php if (empty($pageArticles)): ?>
                            <div class="no-articles">
                                <p>Aucun article tech disponible pour le moment.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($pageArticles as $article): 
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
                                            <input type="hidden" name="redirect" value="index.php?page=<?= $currentPage ?>">
                                            <button type="submit" class="like-btn <?= $likedClass ?>" data-id="<?= $article['id'] ?>">
                                                <i class="<?= $heartIcon ?>"></i>
                                                <span class="like-count"><?= $article['likes_count'] ?></span>
                                            </button>
                                        </form>
                                        <button type="button" class="comment-btn" data-id="<?= $article['id'] ?>" onclick="openCommentModal(<?= $article['id'] ?>)">
                                            <i class="far fa-comment"></i>
                                            <span class="comment-count"><?= $article['comments_count'] ?? 0 ?></span>
                                        </button>
                                    </div>
                                </div>
                            </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- LIFESTYLE PAGE -->
        <section id="lifestyle" class="page <?= $currentPage === 'lifestyle' ? 'active' : '' ?>">
            <div class="container">
                <h1>Lifestyle Digital</h1>
                <div class="articles-grid" id="lifestyle-content">
                    <?php if ($currentPage === 'lifestyle'): ?>
                        <?php if (empty($pageArticles)): ?>
                            <div class="no-articles">
                                <p>Aucun article lifestyle disponible pour le moment.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($pageArticles as $article): 
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
                                            <input type="hidden" name="redirect" value="index.php?page=<?= $currentPage ?>">
                                            <button type="submit" class="like-btn <?= $likedClass ?>" data-id="<?= $article['id'] ?>">
                                                <i class="<?= $heartIcon ?>"></i>
                                                <span class="like-count"><?= $article['likes_count'] ?></span>
                                            </button>
                                        </form>
                                        <button type="button" class="comment-btn" data-id="<?= $article['id'] ?>" onclick="openCommentModal(<?= $article['id'] ?>)">
                                            <i class="far fa-comment"></i>
                                            <span class="comment-count"><?= $article['comments_count'] ?? 0 ?></span>
                                        </button>
                                    </div>
                                </div>
                            </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- CONTACT PAGE -->
        <section id="contact" class="page <?= $currentPage === 'contact' ? 'active' : '' ?>">
            <div class="container">
                <h1>Nous contacter</h1>
                
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
                
                <div class="contact-form-container">
                    <form id="contact-form" class="contact-form" method="POST" action="php/contact.php">
                        <div class="form-group">
                            <label for="name">Nom complet</label>
                            <input type="text" id="name" name="name" required>
                            <span class="error-message" id="name-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                            <span class="error-message" id="email-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="subject">Sujet</label>
                            <select id="subject" name="subject" required>
                                <option value="">Choisissez un sujet</option>
                                <option value="info">Demande d'information</option>
                                <option value="collaboration">Collaboration</option>
                                <option value="feedback">Feedback</option>
                                <option value="autre">Autre</option>
                            </select>
                            <span class="error-message" id="subject-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                            <span class="error-message" id="message-error"></span>
                        </div>

                        <button type="submit" class="submit-btn">
                            <span class="btn-text">Envoyer le message</span>
                            <span class="btn-loading">Envoi en cours...</span>
                        </button>
                    </form>

                    <div class="contact-info">
                        <h3>Informations de contact</h3>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>contact@techlife-magazine.com</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span>+216 73 824 918</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Sousse Rue 2 mars</span>
                        </div>
                        <div class="admin-link">
                            <a href="admin.php"><i class="fas fa-cog"></i> Administration</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Comment Modal -->
    <div id="comment-modal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeCommentModal()">&times;</span>
            <h2>Ajouter un commentaire</h2>
            <form id="comment-form" method="POST" action="php/interact.php">
                <input type="hidden" name="action" value="comment">
                <input type="hidden" name="article_id" id="comment-article-id" value="">
                <input type="hidden" name="redirect" value="index.php?page=<?= $currentPage ?>">
                
                <div class="form-group">
                    <label for="author_name">Votre nom</label>
                    <input type="text" id="author_name" name="author_name" required>
                </div>
                
                <div class="form-group">
                    <label for="comment_content">Votre commentaire</label>
                    <textarea id="comment_content" name="content" rows="4" required></textarea>
                </div>
                
                <button type="submit" class="submit-btn">Publier le commentaire</button>
            </form>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>TechLife Magazine</h3>
                    <p>Votre source d'information sur les dernières innovations technologiques et leur impact sur notre vie quotidienne.</p>
                </div>
                <div class="footer-section">
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="index.php?page=home">Accueil</a></li>
                        <li><a href="index.php?page=articles">Articles</a></li>
                        <li><a href="index.php?page=tech">Tech</a></li>
                        <li><a href="index.php?page=contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Suivez-nous</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 TechLife Magazine. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="js/app.js"></script>
</body>
</html>
