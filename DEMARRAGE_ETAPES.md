# Démarrer le projet et voir les pages Partenariats

Suivre les étapes **dans l’ordre**. Si une étape échoue, ne pas passer à la suivante.

---

## Étape 1 : Démarrer MySQL (base de données)

Le projet utilise MySQL/MariaDB. Il doit tourner avant le site.

- **Si vous utilisez XAMPP :** ouvrir XAMPP Control Panel → bouton **Start** à côté de **MySQL**. Attendre que MySQL soit en vert.
- **Si MySQL/MariaDB est installé en service :** il est peut‑être déjà démarré. Sinon, le démarrer dans “Services” Windows.

Sans MySQL, les pages ne chargeront pas correctement.

---

## Étape 2 : Ouvrir un terminal dans le projet

1. Ouvrir l’**Explorateur de fichiers**.
2. Aller dans : `C:\Users\user\Desktop\firmetna_partners`.
3. Dans la barre d’adresse, taper `cmd` puis Entrée.  
   Une fenêtre **Invite de commandes** s’ouvre, déjà dans le bon dossier.

*(Ou : menu Démarrer → taper “cmd” → Entrée, puis taper :  
`cd /d C:\Users\user\Desktop\firmetna_partners`)*

---

## Étape 3 : Utiliser PHP 8.3 et installer les dépendances (si besoin)

Dans la même fenêtre, taper :

```bat
C:\Users\user\Desktop\php83\php.exe -v
```

Vous devez voir une ligne du type `PHP 8.3.x`.  
Si vous voyez une autre version (ex. 8.1), les commandes suivantes doivent utiliser le chemin complet :

```bat
set PATH=C:\Users\user\Desktop\php83;%PATH%
```

Puis installer les dépendances (une seule fois en général) :

```bat
composer install
```

*(Si `composer` n’est pas reconnu, utiliser :  
`C:\Users\user\Desktop\php83\php.exe` là où on vous dit d’utiliser `php`. Par exemple :  
`C:\Users\user\Desktop\php83\php.exe C:\chemin\vers\composer.phar install` dans le dossier du projet.)*

---

## Étape 4 : Lancer le serveur web

**Option A – Script tout-en-un (recommandé)**  
Dans l’Explorateur, aller dans `C:\Users\user\Desktop\firmetna_partners` et **double-cliquer** sur :

**`START_SERVER.bat`**

Une fenêtre noire s’ouvre avec “Demarrage du serveur” et une ligne du type :  
`PHP 8.3.x Development Server (http://localhost:8000) started`.  
**Ne pas fermer cette fenêtre** tant que vous testez le site.

**Option B – À la main dans le terminal**  
Dans le terminal (dossier `firmetna_partners`) :

```bat
set PATH=C:\Users\user\Desktop\php83;%PATH%
cd /d C:\Users\user\Desktop\firmetna_partners
php -S localhost:8000 -t public
```

Même message que ci‑dessus. Garder la fenêtre ouverte.

---

## Étape 5 : Ouvrir le site dans le navigateur

Ouvrir **Chrome, Edge ou Firefox** et aller à :

**http://localhost:8000**

(Important : bien **http://** et **localhost:8000**, pas un chemin du type `C:\...` ni `file://...`.)

- Si vous voyez la page d’accueil du site → le serveur fonctionne.
- Si “Impossible d’accéder au site” ou “Connexion refusée” → la fenêtre du serveur (étape 4) est-elle encore ouverte ? Relancer l’étape 4.

---

## Étape 6 : Pages à tester (Partenariats)

Une fois sur **http://localhost:8000** :

| Ce que vous voulez voir | Adresse à taper ou lien à cliquer |
|--------------------------|------------------------------------|
| Page d’accueil | http://localhost:8000/ |
| Liste des partenaires (cartes) | http://localhost:8000/partenariats-front |
| Détail d’un partenaire (“Voir plus”) | Sur la liste partenariats, cliquer **“Voir plus”** sur une carte (ou http://localhost:8000/partenariats-front/1 en remplaçant 1 par un vrai ID) |
| Connexion admin | http://localhost:8000/login |
| Liste partenaires (admin) | http://localhost:8000/admin/partenariats |
| Export CSV partenaires | http://localhost:8000/admin/partenariats/export/csv |
| Liste offres (admin) | http://localhost:8000/admin/partenariats/offres |
| Export CSV offres | http://localhost:8000/admin/partenariats/offres/export/csv |

Pour les pages **/admin/...** il faut être connecté.  
Identifiants admin (si vous avez fait la commande de création d’admin) :  
- Email : **admin@firmetna.com**  
- Mot de passe : **admin123**

Si le compte admin n’existe pas, dans un **autre** terminal (sans arrêter le serveur) :

```bat
cd /d C:\Users\user\Desktop\firmetna_partners
set PATH=C:\Users\user\Desktop\php83;%PATH%
php bin/console app:setup-admin
```

Puis vous reconnecter sur http://localhost:8000/login.

---

## En cas de problème

- **“Connexion refusée”** → Vérifier que la fenêtre du serveur (START_SERVER.bat ou `php -S ...`) est ouverte et qu’il n’y a pas d’erreur en rouge. Relancer l’étape 4.
- **Page blanche ou erreur 500** → Regarder le **terminal** où tourne le serveur : l’erreur PHP s’affiche souvent là. Vérifier aussi que MySQL est démarré (étape 1).
- **Erreur base de données** → Vérifier dans `.env` que `DATABASE_URL` correspond à votre MySQL (utilisateur, mot de passe, base `firmetna_new_db`). Puis éventuellement :  
  `php bin/console doctrine:migrations:migrate --no-interaction`
- **Les liens ne “chargent” pas** → S’assurer d’utiliser **http://localhost:8000/...** dans la barre d’adresse du navigateur, pas un double-clic sur un fichier .html.

Une fois ces étapes faites, vous devriez pouvoir voir toutes les pages Partenariats (front + admin) et les exports CSV.
