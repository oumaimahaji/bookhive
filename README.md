BookShare - Plateforme de Partage de Livres
https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white
https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white
https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white
https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white

📚 Description
BookShare est une plateforme communautaire permettant aux utilisateurs de partager, échanger et emprunter des livres entre particuliers. Développé avec Laravel, ce projet favorise l'accès à la lecture et crée une communauté de lecteurs passionnés.

✨ Fonctionnalités
🔐 Authentification
Inscription et connexion utilisateur

Validation par email

Système de rôles (Utilisateur, Admin)

Mot de passe oublié

📖 Gestion des Livres
Ajout de livres avec couverture, description et catégorie

Recherche et filtrage avancé (titre, auteur, catégorie)

Système de notation et avis (1-5 étoiles)

Géolocalisation des livres disponibles

Statut de disponibilité (disponible, emprunté, réservé)

🔄 Système d'Échanges
Demande d'emprunt avec messagerie intégrée

Suivi du statut des échanges (en attente, accepté, refusé, terminé)

Historique des transactions

Système de confirmation de retour

👥 Gestion des Utilisateurs
Profil personnel avec bibliothèque

Tableau de bord utilisateur

Système de réputation et confiance

Préférences de lecture et notifications

🛠️ Administration
Panel d'administration complet

Gestion des utilisateurs et livres

Modération des contenus

Statistiques d'utilisation

🛠️ Installation
Prérequis
PHP 8.1 ou supérieur

Composer 2.0+

MySQL 8.0 ou supérieur

Node.js 16+ et NPM

Extension PHP : BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PCRE, PDO, Tokenizer, XML

Étapes d'installation
Cloner le repository

bash
git clone https://github.com/votre-username/bookshare.git
cd bookshare
Installer les dépendances PHP

bash
composer install
Installer les dépendances JavaScript

bash
npm install
npm run build
Configurer l'environnement

bash
cp .env.example .env
php artisan key:generate
Configurer la base de données
Éditer le fichier .env :

env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bookshare
DB_USERNAME=your_username
DB_PASSWORD=your_password
Exécuter les migrations et seeders

bash
php artisan migrate --seed
Configurer le stockage des fichiers

bash
php artisan storage:link
Configurer la file d'attente (Optionnel)

bash
# Pour les emails asynchrones
php artisan queue:work
Lancer le serveur de développement

bash
php artisan serve
L'application sera accessible à l'adresse : http://localhost:8000

Comptes par défaut
Après l'installation, ces comptes sont créés :

Admin : admin@bookshare.com / password

Utilisateur : user@bookshare.com / password

🗃️ Structure de la Base de Données
Tables principales
users
id, name, email, password, avatar

phone, address, city, postal_code, latitude, longitude

rating, trust_score, email_verified_at

remember_token, created_at, updated_at

books
id, title, author, isbn, description

cover_image, category_id, user_id, status

condition, location, is_available

created_at, updated_at

categories
id, name, slug, description, created_at, updated_at

exchanges
id, book_id, borrower_id, owner_id

status, request_date, accept_date, return_date

message, rating_owner, rating_borrower

review_owner, review_borrower, created_at, updated_at

reviews
id, book_id, user_id, rating, comment

created_at, updated_at

messages
id, exchange_id, sender_id, receiver_id, message

is_read, created_at, updated_at

🎨 Customisation
Variables d'environnement importantes
env
APP_NAME="BookShare"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null

GOOGLE_MAPS_API_KEY=your_google_maps_api_key
Thème et style
Framework CSS : Bootstrap 5

Icônes : Font Awesome

Cartes : Google Maps API

Upload d'images : Intervention Image

🧪 Tests
bash
# Lancer les tests PHPUnit
php artisan test

# Lancer les tests avec couverture
php artisan test --coverage

# Lancer les tests spécifiques
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Générer un rapport de couverture HTML
php artisan test --coverage-html coverage/
🚀 Déploiement en Production
Préparation
bash
# Optimiser l'application
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Mettre l'application en mode production
# Dans .env : APP_DEBUG=false, APP_ENV=production
Configuration serveur
bash
# Configurer le cron pour les tâches planifiées
# Ajouter cette ligne à votre crontab :
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1

# Configurer les workers de queue (Supervisor recommandé)
php artisan queue:work --queue=high,default --sleep=3 --tries=3
Sécurité
bash
# Générer une clé d'application
php artisan key:generate

# Nettoyer le cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
