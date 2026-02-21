# 🔧 Solution : Erreur "vendor/autoload_runtime.php not found"

## ❌ Problème

L'erreur indique que le dossier `vendor` n'existe pas. Cela signifie que les dépendances Composer n'ont pas été installées.

## ✅ Solution

### Méthode 1 : Script automatique (Windows)

**Double-cliquez sur :**
```
installer_dependances.bat
```

### Méthode 2 : Commande manuelle

```bash
# 1. Aller dans le dossier du projet
cd firmetna

# 2. Installer les dépendances
composer install
```

**Cela va :**
- Télécharger toutes les dépendances PHP
- Créer le dossier `vendor/`
- Générer l'autoloader

---

## 📋 Étapes complètes pour démarrer

### 1. Installer les dépendances
```bash
cd firmetna
composer install
```

### 2. Créer la base de données
```bash
php bin/console doctrine:database:create
```

### 3. Exécuter les migrations
```bash
php bin/console doctrine:migrations:migrate
```

### 4. Vider le cache
```bash
php bin/console cache:clear
```

### 5. Démarrer le serveur
```bash
symfony server:start
# OU
php -S localhost:8000 -t public
```

---

## ⚠️ Si Composer n'est pas installé

### Installer Composer sur Windows :

1. **Téléchargez Composer** :
   - Allez sur : https://getcomposer.org/download/
   - Téléchargez `Composer-Setup.exe`

2. **Installez Composer** :
   - Exécutez l'installateur
   - Suivez les instructions
   - Assurez-vous que PHP est dans votre PATH

3. **Vérifiez l'installation** :
   ```bash
   composer --version
   ```

---

## 🔍 Vérifications

### Vérifier que PHP est installé :
```bash
php -v
```

### Vérifier que Composer est installé :
```bash
composer --version
```

### Vérifier que le dossier vendor existe après installation :
```bash
# Le dossier vendor/ devrait apparaître dans firmetna/
dir vendor
```

---

## 📁 Structure attendue après installation

```
firmetna/
├── vendor/              ← Ce dossier doit exister après composer install
│   ├── autoload.php
│   ├── autoload_runtime.php
│   └── ...
├── public/
│   └── index.php
├── src/
├── composer.json
└── ...
```

---

## 🐛 Erreurs courantes

### "composer: command not found"
→ Composer n'est pas installé ou pas dans le PATH

### "PHP version too low"
→ Installez PHP 8.1 ou supérieur

### "Memory limit exhausted"
→ Augmentez la mémoire PHP :
```bash
php -d memory_limit=512M composer install
```

---

## ✅ Après l'installation

Une fois `composer install` terminé avec succès :
1. Le dossier `vendor/` sera créé
2. L'erreur disparaîtra
3. Vous pourrez démarrer le serveur

---

**Exécutez `composer install` et le problème sera résolu ! 🚀**
