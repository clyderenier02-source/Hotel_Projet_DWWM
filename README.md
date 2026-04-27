# Eden Palm

Site de réservation en ligne pour un hôtel, développé avec Symfony et PHP 8.4.

## Prérequis
- PHP 8.4
- Composer
- MySQL
- Symfony CLI


## Installation en local

### 1. Créer le projet
symfony new eden-palm --webapp
cd eden-palm

### 2. Configurer l'environnement
Modifier la variable suivante dans le fichier `.env` :

DATABASE_URL="mysql://user:password@127.0.0.1:3306/eden_palm"

### 3. Créer la base de données et exécuter les migrations
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate

### 4. Lancer le projet
Dans deux terminaux séparés :

# Terminal 1
symfony serve

# Terminal 2
php bin/console sass:build --watch

Le site est accessible sur `http://localhost:8000`

--------------------------------------------

## Déploiement en production sur AlwaysData

### 1. Se connecter au serveur en SSH
ssh moncompte@ssh-moncompte.alwaysdata.net

### 2. Cloner le projet
cd ~/www
git clone https://github.com/ton-repo/eden-palm.git
cd eden-palm

### 3. Installer les dépendances en mode production
composer install --no-dev --optimize-autoloader

### 4. Créer le fichier .env.local sur le serveur

Ce fichier contient les identifiants de production. Il n'est pas versionné sur Git donc les informations sensibles ne sont jamais exposées.
APP_ENV=prod
APP_SECRET=ta_clé_secrète
DATABASE_URL="mysql://moncompte_user:motdepasse@mysql-moncompte.alwaysdata.net:3306/moncompte_nomprojet?serverVersion=mariadb-11.4.0&charset=utf8mb4"

### 5. Compiler les assets
php bin/console sass:build
php bin/console asset-map:compile --watch

### 6. Exécuter les migrations
php bin/console doctrine:migrations:migrate --env=prod

--------------------------------------------

## Mises à jour

Depuis votre ordinateur local :
git push

Sur le serveur :
git pull
composer install --no-dev --optimize-autoloader
php bin/console doctrine:migrations:migrate --env=prod

--------------------------------------------

## Stack technique

- Symfony PHP 8.4
- Doctrine ORM
- MySQL
- Twig
- SCSS via Asset Mapper
- JavaScript natif + Stimulus + Turbo
- PHPUnit pour les tests
