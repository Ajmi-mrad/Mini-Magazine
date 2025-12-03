# TechLife Magazine 📰

Un site web de magazine technologique moderne avec gestion complète des articles, système de likes/commentaires et panneau d'administration.

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat&logo=docker&logoColor=white)

## 🚀 Fonctionnalités

- **Gestion complète des articles** : Ajout, modification et suppression d'articles
- **Système de likes** : Les visiteurs peuvent liker les articles (un like par session)
- **Commentaires** : Système de commentaires pour chaque article
- **Interface d'administration** : Panneau admin pour publier et gérer le contenu
- **Formulaire de contact** : Enregistrement des messages dans la base de données
- **Design responsive** : Interface moderne adaptée à tous les écrans
- **Catégories** : Articles organisés par catégories (Tech, Lifestyle, Autres)

## 🛠️ Technologies Utilisées

- **Frontend** : HTML5, CSS3, JavaScript (Vanilla)
- **Backend** : PHP 8.0+ (sans framework)
- **Base de données** : MySQL 8.0
- **Conteneurisation** : Docker & Docker Compose

## 📋 Prérequis

- PHP 8.0 ou supérieur avec extension `php-mysql`
- Docker et Docker Compose
- Navigateur web moderne

## ⚙️ Installation

### 1. Cloner le projet

```bash
git clone https://github.com/votre-username/Mini-magazine.git
cd Mini-magazine
```

### 2. Démarrer les conteneurs Docker (MySQL + phpMyAdmin)

```bash
sudo docker compose up -d
```

Cela va créer :
- Un conteneur MySQL sur le port `3306`
- Un conteneur phpMyAdmin sur le port `8080`
- La base de données `techlife_magazine` avec les tables nécessaires

### 3. Démarrer le serveur PHP

```bash
php -S localhost:8000
```

### 4. Accéder à l'application

- **Site principal** : [http://localhost:8000](http://localhost:8000)
- **Panneau admin** : [http://localhost:8000/admin.php](http://localhost:8000/admin.php)
- **phpMyAdmin** : [http://localhost:8080](http://localhost:8080)

## 🗄️ Structure de la Base de Données

```sql
-- Table des articles
articles (id, title, excerpt, content, image, author, category, likes, featured, created_at, updated_at)

-- Table des likes (tracking par session)
likes (id, article_id, session_id, created_at)

-- Table des commentaires
comments (id, article_id, author, email, content, created_at)

-- Table des contacts
contacts (id, name, email, subject, message, created_at)
```

## 📁 Structure du Projet

```
Mini-magazine/
├── index.php              # Page principale du site
├── admin.php              # Panneau d'administration
├── database.sql           # Script de création de la BDD
├── docker-compose.yml     # Configuration Docker
├── css/
│   └── style.css          # Styles CSS
├── js/
│   └── app.js             # JavaScript (interactions, likes, commentaires)
└── php/
    ├── db.php             # Configuration et connexion BDD
    ├── get_articles.php   # Récupération des articles
    ├── interact.php       # Gestion des likes et commentaires
    ├── contact.php        # Traitement du formulaire de contact
    └── manage_articles.php # CRUD des articles (admin)
```

## 🔧 Configuration

Les paramètres de connexion à la base de données se trouvent dans `php/db.php` :

```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'techlife_magazine');
define('DB_USER', 'root');
define('DB_PASS', 'root123');
```

## 💡 Communication JavaScript ↔ PHP

Le projet utilise des formulaires HTML pour la communication entre JavaScript et PHP (sans AJAX) :

1. **Likes** : JavaScript intercepte le clic, anime le bouton, puis soumet un formulaire caché
2. **Commentaires** : Validation côté client, puis soumission du formulaire
3. **Contact** : Formulaire classique avec redirection après traitement

## 📝 Utilisation

### Ajouter un article

1. Accéder au panneau admin : `http://localhost:8000/admin.php`
2. Cliquer sur "+ Nouvel article"
3. Remplir le formulaire (titre, extrait, contenu, image URL, auteur, catégorie)
4. Cocher "Article à la une" si nécessaire
5. Cliquer sur "Publier l'article"

### Gérer les articles existants

- **Modifier** : Cliquer sur l'icône crayon dans la liste des articles
- **Supprimer** : Cliquer sur l'icône poubelle (confirmation requise)

## 🐳 Commandes Docker Utiles

```bash
# Démarrer les conteneurs
sudo docker compose up -d

# Arrêter les conteneurs
sudo docker compose down

# Voir les logs MySQL
sudo docker logs techlife_mysql

# Accéder au shell MySQL
sudo docker exec -it techlife_mysql mysql -u root -proot123 techlife_magazine
```

## 📄 Licence

Ce projet est développé à des fins éducatives.

## 👤 Auteur

Développé par **Ajmi**

---

⭐ N'hésitez pas à mettre une étoile si ce projet vous a été utile !
