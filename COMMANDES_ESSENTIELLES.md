# ⚡ Commandes essentielles - Guide rapide

## 🚀 DÉMARRER LE PROJET (3 étapes)

### 1. Installer les dépendances (première fois seulement)
```bash
cd firmetna
composer install
```

### 2. Configurer la base de données
```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```

### 3. Démarrer le serveur
```bash
# Option A : Symfony CLI (recommandé)
symfony server:start

# Option B : PHP built-in server
php -S localhost:8000 -t public
```

**Puis ouvrez :** http://localhost:8000

---

## 📋 COMMANDES COURANTES

### Base de données
```bash
# Créer la base
php bin/console doctrine:database:create

# Migrations
php bin/console doctrine:migrations:migrate
php bin/console doctrine:migrations:status

# Vérifier le schéma
php bin/console doctrine:schema:validate
```

### Cache
```bash
# Vider le cache
php bin/console cache:clear

# Vider le cache de production
php bin/console cache:clear --env=prod
```

### Routes
```bash
# Lister toutes les routes
php bin/console debug:router

# Routes du forum
php bin/console debug:router | grep forum
```

### Composer
```bash
# Installer les dépendances
composer install

# Mettre à jour
composer update

# Optimiser
composer dump-autoload --optimize
```

---

## 🎯 SCRIPTS AUTOMATIQUES (Windows)

### Tout configurer automatiquement
```bash
# Double-cliquez sur :
demarrer_projet.bat
```

### Exécuter seulement les migrations
```bash
# Double-cliquez sur :
executer_migration.bat
```

---

## 🔍 VÉRIFICATIONS

### Vérifier que tout fonctionne
```bash
# PHP version
php -v

# Composer
composer --version

# Routes
php bin/console debug:router

# Base de données
php bin/console doctrine:schema:validate
```

---

## 🐛 PROBLÈMES COURANTS

### "Class not found"
```bash
composer dump-autoload
```

### "Database connection failed"
→ Vérifiez MySQL est démarré
→ Vérifiez `.env` (DATABASE_URL)

### "Port already in use"
```bash
# Changer le port
symfony server:start --port=8001
```

### "Permission denied" (Linux/Mac)
```bash
chmod -R 777 var/
```

---

## 📁 FICHIERS IMPORTANTS

- `.env` → Configuration (base de données, secrets)
- `public/index.php` → Point d'entrée
- `config/services.yaml` → Services Symfony
- `migrations/` → Migrations base de données

---

## ✅ CHECKLIST RAPIDE

```bash
# 1. Dépendances
composer install

# 2. Base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 3. Cache
php bin/console cache:clear

# 4. Serveur
symfony server:start
```

**C'est tout ! 🎉**
