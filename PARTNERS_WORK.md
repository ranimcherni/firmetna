# Partenariats – Ouvrir la page et travailler (front + back)

Guide rapide pour **ouvrir** le projet et **travailler** sur la page Partenariats (front et back office).

---

## 1. Ouvrir le projet et les pages Partenariats

### Démarrer le serveur

Dans un terminal (avec **PHP 8.3** – par ex. en double-cliquant sur `use-php83.bat` ou en lançant `. .\use-php83.ps1` en PowerShell) :

```bat
cd C:\Users\user\Desktop\firmetna_partners
php -S localhost:8000 -t public
```

Gardez cette fenêtre ouverte. Le site tourne sur **http://127.0.0.1:8000**.

### URLs Partenariats

| Page | URL |
|------|-----|
| **Front (public)** – liste des partenaires | http://127.0.0.1:8000/partenariats-front |
| **Back office (admin)** – gestion des partenaires | http://127.0.0.1:8000/admin/partenariats |

Pour le back office, il faut être **connecté en tant qu’admin** (login puis menu « Partenariats » dans la sidebar).

---

## 2. Où travailler dans le code

### Front (page publique)

- **Contrôleur :** `src/Controller/PartenariatsController.php`  
  – Liste des partenaires, filtres, etc.
- **Template :** `templates/partenariats/index.html.twig`  
  – Mise en page, cartes partenaires, style. **Boutons prêts pour le CRUD** (classes réutilisables) :
  - **`.btn-partner-primary`** – CTA principal (ex. « Voir plus », « Enregistrer »).
  - **`.btn-partner-outline`** – Action secondaire (ex. « Contacter », « Annuler »).
  - **`.btn-partner-ghost`** – Action tertiaire (ex. « Modifier »).
  - **`.btn-partner-danger`** – Suppression (dans un formulaire POST + CSRF).

### Back office (admin)

- **Contrôleur :** `src/Controller/Admin/PartnerController.php`  
  – Liste, création, modification, suppression des partenaires.
- **Formulaire :** `src/Form/PartnerType.php`  
  – Champs du formulaire partenaire (nom, type, email, statut, etc.).
- **Templates admin :**
  - `templates/admin/partner/index.html.twig` – liste + recherche/filtres
  - `templates/admin/partner/new.html.twig` – création
  - `templates/admin/partner/edit.html.twig` – modification
  - `templates/admin/partner/_form.html.twig` – champs communs du formulaire

### Données (entités)

- **Partenaire :** `src/Entity/Partner.php`  
  – Champs : name, type, email, phone, address, description, website, logoUrl, status, createdAt.
- **Offre partenaire :** `src/Entity/PartnerOffer.php`  
  – Lié à un `Partner` ; type (Donation, Sponsorship, Product, Service, Other), title, description, amount, offerDate, status.
- **Repositories :** `src/Repository/PartnerRepository.php`, `src/Repository/PartnerOfferRepository.php`.

---

## 3. Idées pour la suite

- **Front :** détail d’un partenaire (nouvelle route + template), filtres par type/statut, mise en avant des offres.
- **Back :** gestion des **offres** par partenaire (CRUD `PartnerOffer` dans l’admin, ou sous-formulaire dans le formulaire partenaire).
- **Design :** adapter les couleurs / blocs dans `partenariats/index.html.twig` et dans les templates admin pour rester cohérent avec le reste du site.

---

## 4. Rappel : PHP 8.3

Pour les commandes (migrations, cache, etc.), utilisez un terminal où `php -v` affiche **8.3** (par ex. en lançant d’abord `use-php83.bat` ou `. .\use-php83.ps1`).
