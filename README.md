# 🚀 Symfony Final Project - Système de Gestion de Produits avec API

Un **projet** Symfony complet avec authentification, gestion de produits, notifications en temps réel et API REST sécurisée.

## 📋 **Table** des matières

- [Fonctionnalités](#-fonctionnalités)
- [Technologies utilisées](#-technologies-utilisées)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Données de test](#-données-de-test)
- [Utilisation](#-utilisation)
- [API Documentation](#-api-documentation)
- [Tests](#-tests)
- [Structure du projet](#-structure-du-projet)

## ✨ Fonctionnalités

### 🔐 Authentification & Autorisation
- Système d'inscription/connexion sécurisé
- Rôles utilisateur (USER, ADMIN)
- JWT pour l'API
- Gestion des sessions web

### 📦 Gestion des Produits
- CRUD complet des produits
- Système de points pour les achats
- Catégorisation des produits
- Interface admin dédiée

### 🔔 Notifications
- Notifications en temps réel
- Types : produits, blocage, réactivation, achats, points
- Interface de gestion pour les admins

### 🌐 API REST
- API Platform intégrée
- Routes sécurisées pour les admins
- Documentation Swagger automatique
- Authentification par session web

### 🚀 Messaging Asynchrone
- Symfony Messenger pour les tâches asynchrones
- Queues pour les notifications
- Traitement en arrière-plan
- Interface de monitoring des messages

## 🛠 Technologies utilisées

- **Framework** : Symfony 7.x
- **Base de données** : MySQL
- **API** : API Platform
- **Authentification** : Lexik JWT Bundle
- **Messaging** : Symfony Messenger
- **Frontend** : Bootstrap 5, Twig
- **Tests** : PHPUnit
- **Fixtures** : Doctrine Fixtures Bundle

## 📦 Installation

### Prérequis
- PHP 8.1+
- Composer
- MySQL 8.0+
- Symfony CLI (optionnel)

### 1. Cloner le projet
```bash
git clone https://github.com/votre-username/symfony-final-project.git
cd symfony-final-project
```

### 2. Installer les dépendances
```bash
composer install
```

### 3. Configuration de l'environnement
```bash
# Copier le fichier d'environnement
cp .env .env.local

# Éditer .env.local avec vos paramètres
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/final_project_courses?serverVersion=8.0.32&charset=utf8mb4"
```

### 4. Génération des clés JWT
```bash
php bin/console lexik:jwt:generate-keypair
```

### 5. Base de données
```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Charger les fixtures (données de test)
php bin/console doctrine:fixtures:load
```

### 6. Démarrer le serveur
```bash
symfony server:start
# ou
php -S localhost:8000 -t public/
```

### 7. (Optionnel) Démarrer les workers Messenger
```bash
# Dans un terminal séparé - pour traiter les messages asynchrones
php bin/console messenger:consume async

# Ou avec supervision pour redémarrage automatique
php bin/console messenger:consume async --time-limit=3600
```

## ⚙️ Configuration

### Variables d'environnement importantes
```env
# Base de données
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/final_project_courses"

# JWT
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=votre_passphrase
```

## 👥 Données de test

Les fixtures créent automatiquement des utilisateurs de test avec différents rôles :

### 🔧 Administrateurs
| Email | Mot de passe | Rôle | Points |
|-------|--------------|------|---------|
| `admin@example.com` | `admin123` | ROLE_ADMIN | 5000 |
| `admin2@example.com` | `admin123` | ROLE_ADMIN | 3000 |

### 👤 Utilisateurs normaux
| Email | Mot de passe | Rôle | Points |
|-------|--------------|------|---------|
| `user1@example.com` | `password123` | ROLE_USER | 1500 |
| `user2@example.com` | `password123` | ROLE_USER | 2000 |
| `user3@example.com` | `password123` | ROLE_USER | 800 |
| `marie.dupont@example.com` | `password123` | ROLE_USER | 1200 |
| `jean.martin@example.com` | `password123` | ROLE_USER | 900 |

### 📦 Données générées
- **50 produits** répartis dans différentes catégories
- **Notifications** pour tester le système
- **Achats** simulés avec historique
- **Points** distribués selon l'activité

## 🎯 Utilisation

### Interface Web

1. **Accédez à** `http://localhost:8000`
2. **Connectez-vous** avec un compte de test
3. **Explorez** les fonctionnalités selon votre rôle

### Comptes recommandés pour les tests

#### Pour tester les fonctionnalités admin :
```
Email: admin@example.com
Mot de passe: admin123
```

#### Pour tester les fonctionnalités utilisateur :
```
Email: user1@example.com
Mot de passe: password123
```

## 🌐 API Documentation

### Accès à la documentation
1. **Connectez-vous** en tant qu'admin sur le site web
2. **Cliquez** sur "API Docs" dans la navigation
3. **Testez** les routes directement dans l'interface

### Routes API disponibles

#### 📋 Collection des produits de l'admin
```http
GET /api/produits/my-products
```
Retourne tous les produits créés par l'admin connecté.

#### 📄 Produit individuel de l'admin
```http
GET /api/produits/{id}/my-product
```
Retourne un produit spécifique créé par l'admin connecté.

### Authentification API
L'API utilise la **session web** - pas besoin de token JWT séparé si vous êtes connecté sur le site.

### Exemple de réponse
```json
{
  "total": 2,
  "products": [
    {
      "id": 1,
      "nom": "Laptop Gaming",
      "prix": 1500,
      "category": "Informatique",
      "description": "Ordinateur portable haute performance",
      "createdAt": "2024-01-15 10:30:00",
      "updatedAt": "2024-01-15 10:30:00",
      "createdBy": {
        "id": 1,
        "nom": "Admin",
        "prenom": "Super"
      }
    }
  ]
}
```

## 🧪 Tests

### Lancer tous les tests
```bash
php bin/phpunit
```

### Tests par catégorie
```bash
# Tests des entités
php bin/phpunit tests/Entity/

# Tests des contrôleurs
php bin/phpunit tests/Controller/

# Tests de l'API
php bin/phpunit tests/Api/

# Tests des services
php bin/phpunit tests/Service/
```

### Coverage
```bash
php bin/phpunit --coverage-html coverage/
```

## 📁 Structure du projet

```
src/
├── Command/              # Commandes Symfony personnalisées
├── Controller/
│   ├── Admin/           # Contrôleurs administration
│   ├── Api/             # Contrôleurs API REST
│   └── ...              # Contrôleurs publics
├── Entity/              # Entités Doctrine
├── EventListener/       # Event listeners
├── Repository/          # Repositories Doctrine
├── Security/            # Authentificateurs personnalisés
└── Service/             # Services métier

tests/
├── Api/                 # Tests API
├── Controller/          # Tests contrôleurs
├── Entity/              # Tests entités
└── Service/             # Tests services

config/
├── packages/            # Configuration des bundles
├── routes/              # Configuration des routes
└── jwt/                 # Clés JWT

templates/               # Templates Twig
public/                  # Assets publics
```

## 🎨 Fonctionnalités avancées

### 🔔 Système de notifications
- Notifications automatiques lors des actions importantes
- Interface de gestion pour les admins
- Types personnalisables

### 💰 Système de points
- Points gagnés lors d'activités
- Dépense de points pour les achats
- Historique des transactions

### 🚀 Symfony Messenger
- **Messages asynchrones** : Traitement en arrière-plan des notifications
- **Queues configurables** : Transport sync/async selon les besoins
- **Monitoring** : Interface admin pour surveiller les messages
- **Retry automatique** : Gestion intelligente des échecs
- **Types de messages** :
  - `NotificationMessage` : Envoi de notifications
  - `ProductCreatedMessage` : Actions post-création de produit
  - `UserStatusChangedMessage` : Changements de statut utilisateur

#### Commandes Messenger utiles
```bash
# Consommer les messages en attente
php bin/console messenger:consume async

# Voir les messages échoués
php bin/console messenger:failed:show

# Relancer les messages échoués
php bin/console messenger:failed:retry

# Statistiques des transports
php bin/console messenger:stats
```

### 🛡️ Sécurité
- Hachage sécurisé des mots de passe
- Protection CSRF
- Validation des données
- Contrôle d'accès granulaire

## 🤝 Contribution

1. **Fork** le projet
2. **Créez** une branche pour votre fonctionnalité
3. **Committez** vos changements
4. **Pushez** vers la branche
5. **Ouvrez** une Pull Request

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 🙏 Remerciements

- [Symfony](https://symfony.com/) pour le framework
- [API Platform](https://api-platform.com/) pour l'API REST
- [Bootstrap](https://getbootstrap.com/) pour l'interface utilisateur

---

💡 **Conseil** : Ce projet est conçu à des fins pédagogiques. N'hésitez pas à explorer le code, modifier les fonctionnalités et l'adapter à vos besoins !

🐛 **Bug ou question** ? Ouvrez une [issue](https://github.com/votre-username/symfony-final-project/issues) !****