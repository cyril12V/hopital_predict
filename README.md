# README du projet

## Vue d'ensemble
Ce projet est une application web basée sur Laravel intégrant un tableau de bord Windmill. L'application combine les puissantes capacités backend de Laravel avec l'interface utilisateur élégante et responsive du tableau de bord Windmill.

## Stack technologique
- **Laravel** - Framework d'application web PHP
- **TailwindCSS** - Framework CSS utilitaire
- **Alpine.js** - Framework JavaScript léger pour les interactions UI
- **Windmill Dashboard** - Modèle de tableau de bord d'administration

## Installation
### 1. Cloner le dépôt
```sh
git clone <URL_DU_DEPOT>
cd <NOM_DU_PROJET>
```

### 2. Installer les dépendances PHP
```sh
composer install
```

### 3. Installer les dépendances JavaScript
```sh
npm install
```

### 4. Créer une copie du fichier d'environnement
```sh
cp .env.example .env
```

### 5. Générer la clé d'application
```sh
php artisan key:generate
```

### 6. Configurer la base de données
Modifier le fichier `.env` pour définir les paramètres de connexion à la base de données.

### 7. Exécuter les migrations
```sh
php artisan migrate
```

## Développement
### 1. Démarrer le serveur de développement
```sh
php artisan serve
```

### 2. Surveiller les modifications et compiler les assets
```sh
npm run dev
```

## Compilation pour la production
Compiler les assets pour la production avec la commande :
```sh
npm run build
```

## Structure du projet
- **app/** - Contient le code PHP de l'application
- **config/** - Fichiers de configuration de l'application
- **database/** - Migrations et seeders de la base de données
- **resources/** - Fichiers de vue bruts et assets non compilés
- **routes/** - Routes de l'application
- **windmill/** - Templates et assets du tableau de bord Windmill

## Fonctionnalités
- Mise en page responsive du tableau de bord
- Bascule entre thème clair/sombre
- Graphiques et tableaux interactifs
- Authentification des utilisateurs
- Barre de navigation latérale adaptée aux mobiles
- Composants personnalisables

## Crédits
- **Laravel** - Le Framework PHP pour les Artisans du Web
- **Windmill UI** - Template HTML de tableau de bord avec TailwindCSS
- **TailwindCSS** - Un framework CSS utilitaire

## Licence
Ce projet est sous licence MIT.

