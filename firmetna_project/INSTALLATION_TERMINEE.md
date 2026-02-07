# Installation effectuée

## Ce qui a été fait

1. **PHP**  
   - Déjà installé (Winget).  
   - Chemin ajouté au PATH utilisateur :  
     `C:\Users\USER\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe`

2. **php.ini**  
   - Créé dans le dossier PHP avec les extensions activées :  
     openssl, curl, mbstring, fileinfo, zip, pdo_mysql, mysqli

3. **Composer**  
   - Installé dans : `C:\ProgramData\ComposerSetup\bin`  
   - Ce dossier a été ajouté au PATH utilisateur.  
   - Fichier `composer.bat` créé.

4. **Projet Symfony**  
   - `composer install` a été exécuté avec succès (133 paquets installés).  
   - Le cache Symfony a été vidé.

5. **Base de données**  
   - La création de la base et les migrations n’ont **pas** pu être exécutées :  
     **MySQL n’est pas démarré** (ou pas installé) sur ton PC.  
   - Ton projet est configuré pour MySQL sur le **port 3307** (voir `.env`).

---

## Ce que tu dois faire maintenant

### 1. Redémarrer Cursor

Ferme Cursor complètement puis rouvre-le, et rouvre le projet.  
Après ça, dans le terminal tu pourras taper `php -v` et `composer -V` sans erreur.

### 2. Démarrer MySQL

- Si tu utilises **XAMPP** : ouvre le panneau de contrôle XAMPP et démarre **MySQL**.  
  Si ta base est sur le port **3307**, configure MySQL pour utiliser ce port (voir `INSTALLATION.md`).
- Si tu as **MySQL** installé autrement : démarre le service MySQL (Services Windows ou `net start mysql`).

### 3. Créer la base et les tables

Une fois MySQL démarré, dans le terminal du projet :

```powershell
cd C:\Users\USER\Desktop\firmetna_project
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
```

### 4. Lancer le site

```powershell
php -S localhost:8000 -t public
```

Puis ouvre dans le navigateur :

- **Page d’accueil :** http://localhost:8000/
- **Page login :** http://localhost:8000/login

---

## Résumé

| Élément        | Statut        |
|----------------|---------------|
| PHP 8.2        | Installé + PATH |
| Composer       | Installé + PATH |
| php.ini        | Créé (openssl, zip, pdo_mysql, etc.) |
| composer install | OK (133 paquets) |
| Cache Symfony  | Vidé |
| Base de données / migrations | À faire après démarrage de MySQL |
