# Installation du projet Firmetna (PC neuf)

Suis ces étapes **dans l’ordre**. Tu peux utiliser **PowerShell** en tant qu’administrateur si besoin.

---

## Étape 1 : Installer PHP 8.1 ou plus

Ton projet demande PHP >= 8.1.

### Option A – Avec Winget (recommandé sur Windows 10/11)

Ouvre **PowerShell** et exécute :

```powershell
winget install PHP.PHP.8.2 --accept-package-agreements --accept-source-agreements
```

Ferme puis rouvre PowerShell après l’installation.

### Option B – Téléchargement manuel

1. Va sur : https://windows.php.net/download/
2. Télécharge **PHP 8.2** (ou 8.1) en **VS16 x64 Non Thread Safe** (zip).
3. Décompresse dans `C:\php` (ou un autre dossier).
4. Ajoute ce dossier au **PATH** :
   - Paramètres → Système → À propos → Paramètres système avancés → Variables d’environnement
   - Dans "Variables système", sélectionne **Path** → Modifier → Nouveau → `C:\php` (ou ton chemin) → OK.

### Vérifier PHP

```powershell
php -v
```

Tu dois voir une version 8.1 ou 8.2 (ou plus).

---

## Étape 2 : Installer Composer

Composer gère les dépendances PHP du projet.

### Avec l’installeur officiel

Dans **PowerShell** :

```powershell
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
Move-Item composer.phar "C:\ProgramData\ComposerSetup\bin\composer.phar"
```

Puis ajoute au **PATH** : `C:\ProgramData\ComposerSetup\bin`

Ou, si tu préfères un dossier utilisateur :

```powershell
New-Item -ItemType Directory -Force -Path "$env:APPDATA\Composer\bin" | Out-Null
Move-Item composer.phar "$env:APPDATA\Composer\bin\composer.phar"
```

Et ajoute au PATH : `%APPDATA%\Composer\bin`

### Vérifier Composer

Ferme et rouvre PowerShell, puis :

```powershell
composer -V
```

---

## Étape 3 : MySQL (base de données)

Ton projet utilise MySQL sur le **port 3307** avec l’utilisateur **root** et la base **firmetna_new_db**.

### Option A – XAMPP (simple)

1. Télécharge XAMPP : https://www.apachefriends.org/
2. Installe, lance le **Control Panel**, démarre **MySQL**.
3. Par défaut MySQL est sur le port **3306**. Pour utiliser le **port 3307** :
   - Ouvre `C:\xampp\mysql\bin\my.ini` (ou le fichier de config MySQL indiqué par XAMPP),
   - Cherche `port=3306` et remplace par `port=3307`,
   - Redémarre MySQL dans le Control Panel.
4. Ouvre **phpMyAdmin** (http://localhost/phpmyadmin), crée une base nommée **firmetna_new_db** (ou on la créera avec une commande plus bas).

### Option B – MySQL Community Server

1. Télécharge : https://dev.mysql.com/downloads/installer/
2. Choisis "Developer Default" et pendant l’installation configure le port **3307** si tu veux garder la config du projet.
3. Crée une base **firmetna_new_db** (ou utilise la commande Symfony ci-dessous).

---

## Étape 4 : Commandes dans le projet

Ouvre **PowerShell**, va dans le dossier du projet puis exécute les commandes **une par une**.

### 4.1 Aller dans le projet

```powershell
cd "C:\Users\USER\Desktop\firmetna_project"
```

### 4.2 Installer les dépendances PHP (Symfony, etc.)

```powershell
composer install
```

Réponds **n** si on te demande de créer un fichier pour les clés (sauf si tu veux configurer les clés Symfony).

### 4.3 Créer le fichier d’environnement local (optionnel)

Si tu changes le mot de passe MySQL ou le port, copie `.env` vers `.env.local` et modifie dedans :

```powershell
Copy-Item .env .env.local
# Puis édite .env.local (DATABASE_URL) avec ton mot de passe / port si besoin
```

Dans ton `.env` actuel, la base est :

- **URL** : `mysql://root:@127.0.0.1:3307/firmetna_new_db?serverVersion=8.0&charset=utf8mb4`
- Donc : utilisateur **root**, pas de mot de passe, port **3307**, base **firmetna_new_db**.  
  Si ta base est sur le port 3306, change `3307` en `3306` dans `.env` ou `.env.local`.

### 4.4 Créer la base de données (si elle n’existe pas)

```powershell
php bin/console doctrine:database:create --if-not-exists
```

### 4.5 Exécuter les migrations (tables)

```powershell
php bin/console doctrine:migrations:migrate --no-interaction
```

### 4.6 Vider le cache Symfony

```powershell
php bin/console cache:clear
```

### 4.7 (Optionnel) Charger des données de test

Si tu as des fixtures :

```powershell
php bin/console doctrine:fixtures:load --no-interaction
```

(À faire seulement si ton projet est prévu pour et que tu veux des données de démo.)

### 4.8 Lancer le serveur pour voir le site

```powershell
php -S localhost:8000 -t public
```

Garde cette fenêtre ouverte. Dans le navigateur :

- **Page d’accueil** : http://localhost:8000/
- **Page de login** : http://localhost:8000/login

---

## Récapitulatif des liens

| Page        | URL                        |
|------------|----------------------------|
| Accueil    | http://localhost:8000/     |
| Login      | http://localhost:8000/login |
| Inscription | http://localhost:8000/register |

---

## En cas d’erreur

- **"php n’est pas reconnu"** : PHP n’est pas installé ou pas dans le PATH (réinstalle ou corrige le PATH).
- **"composer n’est pas reconnu"** : idem pour Composer.
- **Erreur de connexion MySQL** : vérifie que MySQL tourne (XAMPP ou service Windows) et que le **port** (3307 ou 3306) et le **nom de la base** dans `.env` / `.env.local` sont corrects.
- **Erreur lors de `composer install`** : vérifie que tu as bien PHP 8.1+ (`php -v`).

Une fois PHP, Composer et MySQL installés, les commandes dont tu as besoin pour le projet sont surtout :  
`composer install` → `doctrine:database:create --if-not-exists` → `doctrine:migrations:migrate --no-interaction` → `cache:clear` → `php -S localhost:8000 -t public`.
