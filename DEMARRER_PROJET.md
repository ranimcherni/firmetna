# 🚀 Comment démarrer votre projet FIRMETNA

## 📋 Prérequis

Avant de démarrer, assurez-vous d'avoir :
- ✅ PHP 8.1 ou supérieur
- ✅ Composer installé
- ✅ MySQL/MariaDB en cours d'exécution
- ✅ Serveur web (Apache/Nginx) OU Symfony CLI

---

## 🎯 ÉTAPES POUR DÉMARRER LE PROJET

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

**Ajustez selon votre configuration :**
- `root` = votre utilisateur MySQL
- `@` = votre mot de passe (vide ici)
- `127.0.0.1:3307` = votre host et port MySQL
- `firmetna_new_db` = nom de votre base de données

### Étape 3 : Créer la base de données (si elle n'existe pas)

```bash
php bin/console doctrine:database:create
```

### Étape 4 : Exécuter les migrations

```bash
# Vérifier l'état
php bin/console doctrine:migrations:status

# Exécuter toutes les migrations
php bin/console doctrine:migrations:migrate
```

**OU** double-cliquez sur `executer_migration.bat` (Windows)

### Étape 5 : Vider le cache

```bash
php bin/console cache:clear
```

### Étape 6 : Démarrer le serveur

#### Option A : Symfony CLI (RECOMMANDÉ)
```bash
symfony server:start
```
Puis ouvrez : http://localhost:8000

#### Option B : PHP Built-in Server
```bash
php -S localhost:8000 -t public
```
Puis ouvrez : http://localhost:8000

#### Option C : Avec XAMPP/WAMP
1. Configurez votre serveur web pour pointer vers le dossier `public/`
2. Accédez à : http://localhost/firmetna/public/

---

## 🔧 COMMANDES UTILES

### Vérifier la configuration
```bash
# Vérifier PHP
php -v

# Vérifier Composer
composer --version

# Vérifier les routes
php bin/console debug:router

# Vérifier le schéma de la base de données
php bin/console doctrine:schema:validate
```

### Gérer la base de données
```bash
# Créer la base de données
php bin/console doctrine:database:create

# Supprimer la base de données (ATTENTION!)
php bin/console doctrine:database:drop --force

# Créer les tables depuis les entités
php bin/console doctrine:schema:update --force

# Voir l'état des migrations
php bin/console doctrine:migrations:status

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```

### Cache et optimisation
```bash
# Vider le cache
php bin/console cache:clear

# Vider le cache de production
php bin/console cache:clear --env=prod

# Optimiser l'autoloader
composer dump-autoload --optimize
```

---

## 🐛 RÉSOLUTION DE PROBLÈMES

### Erreur "Database connection failed"
→ Vérifiez que MySQL est démarré
→ Vérifiez les paramètres dans `.env`

### Erreur "Class not found"
→ Exécutez : `composer dump-autoload`

### Erreur "Permission denied"
→ Sur Linux/Mac : `chmod -R 777 var/`

### Erreur "Port already in use"
→ Changez le port : `symfony server:start -d --port=8001`

### Erreur "Migration failed"
→ Vérifiez que la base de données existe
→ Vérifiez les permissions MySQL

---

## 📁 STRUCTURE DU PROJET

```
firmetna/
├── public/          # Point d'entrée web
├── src/             # Code source PHP
├── templates/       # Templates Twig
├── config/          # Configuration
├── migrations/      # Migrations base de données
├── var/             # Cache, logs (généré automatiquement)
└── vendor/          # Dépendances Composer
```

---

## ✅ CHECKLIST DE DÉMARRAGE

- [ ] PHP 8.1+ installé
- [ ] Composer installé
- [ ] MySQL démarré
- [ ] `composer install` exécuté
- [ ] Base de données créée
- [ ] Migrations exécutées
- [ ] Cache vidé
- [ ] Serveur démarré
- [ ] Site accessible sur http://localhost:8000

---

## 🎯 ACCÈS AU SITE

Une fois le serveur démarré :
- **URL principale** : http://localhost:8000
- **Forum** : http://localhost:8000/forum
- **Admin** : http://localhost:8000/admin (si configuré)

---

## 🚀 DÉMARRAGE RAPIDE (Script Windows)

Double-cliquez sur :
```
executer_migration.bat
```

Puis démarrez le serveur :
```bash
symfony server:start
```

---

**Bon développement ! 🎉**
