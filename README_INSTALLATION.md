# 🚀 Installation et démarrage du projet FIRMETNA

## ⚡ Installation rapide (Windows)

**Double-cliquez simplement sur :**
```
TOUT_INSTALLER.bat
```

Ce script va automatiquement :
1. ✅ Vérifier PHP et Composer
2. ✅ Installer toutes les dépendances
3. ✅ Créer la base de données
4. ✅ Exécuter les migrations
5. ✅ Vider le cache

---

## 📋 Installation manuelle

### Étape 1 : Installer les dépendances
```bash
cd firmetna
composer install
```

### Étape 2 : Configurer la base de données

Vérifiez votre fichier `.env` :
```env
DATABASE_URL="mysql://root:@127.0.0.1:3307/firmetna_new_db?serverVersion=8.0&charset=utf8mb4"
```

### Étape 3 : Créer la base de données
```bash
php bin/console doctrine:database:create
```

### Étape 4 : Exécuter les migrations
```bash
php bin/console doctrine:migrations:migrate
```

### Étape 5 : Vider le cache
```bash
php bin/console cache:clear
```

### Étape 6 : Démarrer le serveur
```bash
# Option A : Symfony CLI (recommandé)
symfony server:start

# Option B : PHP built-in server
php -S localhost:8000 -t public
```

Puis ouvrez : **http://localhost:8000**

---

## 🔧 Prérequis

- ✅ PHP 8.1 ou supérieur
- ✅ Composer installé
- ✅ MySQL/MariaDB démarré
- ✅ Extensions PHP : pdo_mysql, mbstring, intl

---

## 🐛 Résolution de problèmes

### Erreur "vendor/autoload_runtime.php not found"
→ Exécutez : `composer install`

### Erreur "Database connection failed"
→ Vérifiez que MySQL est démarré
→ Vérifiez les paramètres dans `.env`

### Erreur "Port already in use"
→ Changez le port : `symfony server:start --port=8001`

### Erreur "Composer not found"
→ Installez Composer : https://getcomposer.org/download/

---

## ✅ Vérification

Après l'installation, vérifiez :

```bash
# Vérifier PHP
php -v

# Vérifier Composer
composer --version

# Vérifier les routes
php bin/console debug:router

# Vérifier la base de données
php bin/console doctrine:schema:validate
```

---

## 📁 Structure du projet

```
firmetna/
├── public/          # Point d'entrée web
├── src/             # Code source PHP
├── templates/       # Templates Twig
├── config/          # Configuration
├── migrations/      # Migrations base de données
├── vendor/          # Dépendances (créé après composer install)
└── var/             # Cache et logs
```

---

## 🎯 Commandes utiles

```bash
# Vider le cache
php bin/console cache:clear

# Voir les routes
php bin/console debug:router

# État des migrations
php bin/console doctrine:migrations:status

# Créer une nouvelle migration
php bin/console make:migration
```

---

**Utilisez `TOUT_INSTALLER.bat` pour une installation automatique ! 🚀**
