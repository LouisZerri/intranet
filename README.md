# Intranet Collaborateurs

Application intranet d'entreprise développée avec Laravel pour la gestion des collaborateurs, missions, formations, clients et processus commerciaux.

## 📋 Table des matières

- [À propos](#-à-propos)
- [Fonctionnalités](#-fonctionnalités)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Utilisation](#-utilisation)
- [Structure du projet](#-structure-du-projet)
- [Technologies utilisées](#-technologies-utilisées)
- [Rôles et permissions](#-rôles-et-permissions)
- [Licence](#-licence)

## 🎯 À propos

Cette application intranet est conçue pour centraliser la gestion de votre équipe et optimiser les processus internes de l'entreprise. Elle offre un espace dédié pour chaque collaborateur avec des fonctionnalités adaptées selon les rôles.

## ✨ Fonctionnalités

### 🔐 Authentification
- Connexion par email/mot de passe
- Gestion des sessions et permissions par rôle

### 👥 Gestion des utilisateurs
- Système de rôles (Collaborateur, Manager, Administrateur)
- Profils utilisateurs avec informations professionnelles
- Gestion de la hiérarchie (managers et subordonnés)
- Avatars personnalisés

### 💼 Module Commercial
- **Clients** : Gestion complète de la base clients
- **Devis** : Création, édition et suivi des devis avec génération PDF
- **Factures** : Gestion des factures, paiements et relances
- **Calcul URSSAF** : Calcul automatique des charges et CA net
- Export Excel des données commerciales

### 📋 Missions
- Attribution et suivi des missions
- Statuts (en cours, terminée, annulée)
- Suivi des deadlines et missions en retard
- Calcul du chiffre d'affaires par mission

### 📚 Formations
- Catalogue de formations disponible
- Demandes de formation avec validation
- Suivi des heures de formation par collaborateur
- Gestion des fichiers de formation (intégration Google Drive)
- Tableau de bord des formations complétées

### 📢 Communication
- Actualités d'entreprise
- Commandes de produits de communication
- Suivi des commandes et statuts
- Export et historique

### 👔 Recrutement
- Gestion des candidats
- Suivi des candidatures
- Coordination avec les managers

### 📖 Documentation
- FAQ
- Ressources documentaires
- Base de connaissances partagée

### 📊 Tableaux de bord
- Dashboard personnalisé selon le rôle
- KPIs et statistiques en temps réel
- Suivi des objectifs et performances

## 🔧 Prérequis

- **PHP** >= 8.2
- **Composer**
- **Node.js** >= 18.x et **npm**
- **Base de données** MySQL
- **Serveur web** Apache

## 📦 Installation

1. **Cloner le dépôt**

```bash
git clone https://github.com/LouisZerri/intranet.git
cd intranet
```

2. **Installer les dépendances PHP**

```bash
composer install
```

3. **Installer les dépendances JavaScript**

```bash
npm install
```

4. **Configurer l'environnement**

```bash
cp .env.example .env
php artisan key:generate
```

5. **Configurer la base de données**

Éditez le fichier `.env` et configurez votre base de données :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=intranet
DB_USERNAME=root
DB_PASSWORD=
```

6. **Lancer les migrations et seeders**

```bash
php artisan migrate
php artisan db:seed
```

7. **Créer les liens symboliques pour le stockage**

```bash
php artisan storage:link
```

8. **Compiler les assets**

Pour le développement :
```bash
npm run dev
```

Pour la production :
```bash
npm run build
```

9. **Lancer le serveur de développement**

```bash
php artisan serve
```

L'application sera accessible à l'adresse : `http://localhost:8000`

## ⚙️ Configuration

### Variables d'environnement importantes

Dans le fichier `.env`, configurez :

- **Application**
  - `APP_NAME` : Nom de l'application
  - `APP_ENV` : Environnement (local, production)
  - `APP_DEBUG` : Mode debug (true/false)
  - `APP_URL` : URL de l'application

- **Base de données**
  - `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

- **Google OAuth** (optionnel)
  - `GOOGLE_CLIENT_ID`
  - `GOOGLE_CLIENT_SECRET`
  - `GOOGLE_REDIRECT_URI`

- **Google Drive** (optionnel)
  - `GOOGLE_DRIVE_FOLDER_ID`
  - Configuration des credentials Google API

- **Mail**
  - `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, etc.

### Commandes utiles

```bash
# Nettoyer le cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Optimiser pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Lancer les tests
php artisan test

# Mode développement avec logs et queue
composer dev
```

## 🚀 Utilisation

### Première connexion

1. Accédez à l'URL de l'application
2. Connectez-vous avec les identifiants créés via les seeders

### Navigation

L'interface s'adapte automatiquement selon votre rôle :
- **Collaborateur** : Accès à vos missions, formations, actualités
- **Manager** : Gestion de votre équipe + toutes les fonctionnalités collaborateur
- **Administrateur** : Accès complet à toutes les fonctionnalités


## 🛠 Technologies utilisées

### Backend
- **Laravel** 12.x - Framework PHP
- **PHP** 8.2+ - Langage de programmation
- **MySQL** - Base de données

### Frontend
- **Tailwind CSS** 4.x - Framework CSS
- **Vite** - Build tool et bundler
- **Alpine.js** - Framework JavaScript léger (via CDN)

### Bibliothèques PHP
- **barryvdh/laravel-dompdf** - Génération de PDF
- **maatwebsite/excel** - Export Excel
- **google/apiclient** - Intégration Google (OAuth, Drive)


## 👤 Rôles et permissions

### Collaborateur
- Consultation de ses propres données
- Gestion de ses missions assignées
- Consultation des actualités
- Demandes de formation
- Commandes de communication
- Gestion de son profil

### Manager
- Toutes les permissions collaborateur
- Gestion de son équipe (collaborateurs assignés)
- Vue d'ensemble des performances de l'équipe
- Gestion des actualités
- Gestion des formations
- Statistiques d'équipe

### Administrateur
- Accès complet à toutes les fonctionnalités
- Gestion des utilisateurs et rôles
- Configuration de l'application
- Gestion des formations
- Gestion des produits de communication
- Accès aux statistiques globales

## 🔒 Sécurité

- Hashage des mots de passe avec bcrypt
- Protection CSRF sur tous les formulaires
- Validation des entrées utilisateur
- Middleware d'authentification et autorisation
- Protection des routes par rôle

## 📝 Licence

Ce projet est sous licence [MIT](LICENSE).

## 👨‍💻 Auteur

Développé pour la gestion interne d'entreprise.
