-- TechLife Magazine Database Schema
-- Run this script in your MySQL/MariaDB to create the database

CREATE DATABASE IF NOT EXISTS techlife_magazine CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE techlife_magazine;

-- Articles table
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    excerpt TEXT NOT NULL,
    content TEXT,
    author VARCHAR(100) NOT NULL,
    category ENUM('articles', 'tech', 'lifestyle') NOT NULL DEFAULT 'articles',
    image_url VARCHAR(500) NOT NULL,
    featured TINYINT(1) DEFAULT 0,
    likes_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Likes table (track individual likes)
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (article_id, session_id)
) ENGINE=InnoDB;

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    author_name VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Contact messages table
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert sample articles
INSERT INTO articles (title, excerpt, content, author, category, image_url, featured, likes_count) VALUES
('Le futur du développement web', 'Les nouvelles tendances qui révolutionnent le web...', 'Le développement web évolue rapidement avec l''arrivée de nouvelles technologies comme WebAssembly, les Progressive Web Apps et les frameworks modernes. Ces innovations transforment la façon dont nous construisons et interagissons avec les applications web.', 'Sarah Chen', 'articles', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=400&h=250&fit=crop', 1, 42),
('Robotique et société', 'Comment les robots transforment notre quotidien...', 'Les robots ne sont plus de la science-fiction. Ils s''intègrent progressivement dans notre vie quotidienne, de l''industrie manufacturière aux soins de santé, en passant par l''assistance à domicile.', 'Marcus Robot', 'tech', 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=400&h=250&fit=crop', 1, 38),
('Blockchain expliquée', 'Comprendre la technologie qui change la finance...', 'La blockchain est une technologie de registre distribué qui permet des transactions sécurisées et transparentes sans intermédiaire centralisé. Elle révolutionne non seulement la finance mais aussi de nombreux autres secteurs.', 'Alex Crypto', 'tech', 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=400&h=250&fit=crop', 1, 67),
('Machine Learning et Big Data', 'Comment l''analyse des données transforme les décisions business.', 'Le Machine Learning permet aux entreprises d''extraire des insights précieux de leurs données massives, améliorant ainsi la prise de décision et l''efficacité opérationnelle.', 'Emma Watson', 'articles', 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400&h=250&fit=crop', 0, 89),
('Cybersécurité en 2025', 'Les nouveaux défis de sécurité informatique face aux menaces.', 'Avec l''augmentation des cyberattaques, la sécurité informatique devient une priorité absolue pour les entreprises et les particuliers.', 'Kevin Security', 'tech', 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=400&h=250&fit=crop', 0, 76),
('Cloud Computing Avancé', 'L''infrastructure cloud de nouvelle génération.', 'Le cloud computing continue d''évoluer avec des solutions hybrides et multi-cloud qui offrent plus de flexibilité et de performance.', 'Maria Santos', 'tech', 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=400&h=250&fit=crop', 0, 54),
('Intelligence Artificielle', 'L''IA générative révolutionne la création de contenu.', 'L''intelligence artificielle générative ouvre de nouvelles possibilités créatives, de la génération de texte à la création d''images et de musique.', 'Alan Turing', 'tech', 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=400&h=250&fit=crop', 0, 156),
('Quantum Computing', 'Les ordinateurs quantiques sortent des laboratoires.', 'L''informatique quantique promet de résoudre des problèmes impossibles pour les ordinateurs classiques, ouvrant la voie à des avancées majeures en science et technologie.', 'Marie Curie', 'tech', 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=400&h=250&fit=crop', 0, 134),
('Équilibre Vie Numérique', 'Trouver l''harmonie entre tech et bien-être.', 'Dans un monde hyperconnecté, il est essentiel de trouver un équilibre sain entre notre utilisation de la technologie et notre bien-être mental et physique.', 'Cal Newport', 'lifestyle', 'https://images.unsplash.com/photo-1544027993-37dbfe43562a?w=400&h=250&fit=crop', 0, 143),
('Télétravail Intelligent', 'Optimiser son espace de travail à domicile.', 'Le télétravail est devenu la norme pour de nombreux professionnels. Découvrez comment créer un environnement de travail productif et ergonomique chez vous.', 'Remote Expert', 'lifestyle', 'https://images.unsplash.com/photo-1486312338219-ce68e2c5191a?w=400&h=250&fit=crop', 0, 98),
('Détox Digitale', 'Comment se déconnecter pour mieux se reconnecter.', 'La détox digitale est devenue nécessaire pour préserver notre santé mentale et nos relations sociales dans un monde dominé par les écrans.', 'Wellness Coach', 'lifestyle', 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?w=400&h=250&fit=crop', 0, 87),
('Smart Home 2025', 'La maison connectée de demain.', 'Les technologies domotiques évoluent rapidement, transformant nos maisons en espaces intelligents qui s''adaptent à nos besoins et habitudes.', 'Tech Home', 'lifestyle', 'https://images.unsplash.com/photo-1558002038-1055907df827?w=400&h=250&fit=crop', 0, 112);

-- Insert sample comments
INSERT INTO comments (article_id, author_name, content) VALUES
(1, 'Jean Dupont', 'Excellent article, très informatif !'),
(1, 'Marie Martin', 'J''ai appris beaucoup de choses, merci !'),
(2, 'Pierre Durant', 'La robotique est vraiment fascinante.'),
(3, 'Sophie Lefebvre', 'Enfin une explication claire de la blockchain !');
