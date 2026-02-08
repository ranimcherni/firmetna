# Partenariats – Prochaines étapes (Front + Back office)

Ce document explique **où en est le projet**, **la différence Front / Back office**, et **ce qu’il reste à faire**.

---

## 1. Ce qui existe déjà

### Front (page publique) – `/partenariats-front`
- **Liste** des partenaires (cartes avec nom, type, statut, description, boutons « Voir plus » et « Contacter »).
- **Aucun** formulaire d’ajout / modification / suppression sur cette page.
- Tout le monde peut voir la liste (accès public).

### Back office (admin) – `/admin/partenariats`
- **CRUD complet** déjà en place :
  - **C**reate : « Ajouter un partenaire » → formulaire de création.
  - **R**ead : liste des partenaires + recherche / filtre.
  - **U**pdate : bouton « Modifier » sur chaque partenaire → formulaire d’édition.
  - **D**elete : bouton « Supprimer » (avec confirmation).
- **Templates dédiés** : `templates/admin/partner/` (index, new, edit, _form).
- **Accès** : réservé aux utilisateurs avec le rôle **ROLE_ADMIN** (voir section 3).

Donc : **tu as déjà un CRUD complet**, mais il est **uniquement dans le back office**. Pas besoin de « créer un autre CRUD » côté admin : il est déjà là.

---

## 2. Faut-il un autre template et un autre CRUD en back office ?

**Non.**  
- Le **back office** a **un seul** espace Partenariats : **un** jeu de templates (`admin/partner/`) et **un** CRUD (PartnerController dans Admin).  
- On ne duplique pas le CRUD. On peut en revanche **donner accès** à ce back office depuis le front (bouton « Gérer les partenariats » pour les admins).

**Rôle de chaque côté :**
- **Front** : affichage pour les visiteurs (liste, détail d’un partenaire) + éventuellement un lien « Gérer les partenariats » pour les admins qui mène vers le back office.
- **Back office** : gestion complète (ajout / modification / suppression). C’est là que tu fais tout le CRUD.

---

## 3. Avoir accès au back office dans ton projet (toi, pas seulement « user »)

Aujourd’hui, **seuls les comptes avec le rôle ROLE_ADMIN** peuvent accéder à `/admin` (donc à `/admin/partenariats`).

Pour **avoir toi-même accès** au back office Partenariats :

1. **Avoir un compte admin**  
   Soit tu en crées un à la main en base, soit tu utilises une commande du projet si elle existe (ex. `php bin/console app:setup-admin` ou équivalent).  
   Le compte doit avoir le rôle **ROLE_ADMIN**.

2. **Se connecter** sur le site (page de login).

3. **Aller sur le back office Partenariats**  
   - Soit en allant directement sur : **http://localhost:8000/admin/partenariats**  
   - Soit via le menu (sidebar admin) : lien **« Partenariats »** une fois connecté en admin.

Pour que ce soit plus clair pour toi dans le projet, on peut ajouter sur la **page front** Partenariats un bouton **« Gérer les partenariats »** visible **uniquement pour les admins** et qui pointe vers `/admin/partenariats`. Comme ça, tu as l’accès au CRUD depuis la page publique sans retenir l’URL du back office.

---

## 4. Prochaines étapes recommandées

### Étape A – Accès au back office depuis le front (déjà prévu dans le code)
- Sur la page **partenariats-front**, afficher un bouton **« Gérer les partenariats »** (ou « Ajouter / Modifier / Supprimer des partenaires ») **uniquement si l’utilisateur est admin**.
- Ce bouton redirige vers **`/admin/partenariats`**.
- Ainsi : **pas de deuxième CRUD**, juste un **lien** depuis le front vers le CRUD existant en back office.

### Étape B – Page « Voir plus » (détail d’un partenaire) sur le front
- Créer une route du type `/partenariats-front/{id}` (ex. `app_partenariats_show`).
- Dans le contrôleur front (PartenariatsController), une action `show(Partner $partner)` qui affiche **un** partenaire.
- Un template `partenariats/show.html.twig` avec toutes les infos du partenaire (nom, type, description, email, site, offres éventuelles, etc.).
- Sur la liste, le bouton **« Voir plus »** de chaque carte pointe vers cette page (lien vers `app_partenariats_show`, avec l’`id` du partenaire).

### Étape C – Boutons Modifier / Supprimer sur le front (optionnel)
- Si tu veux que les **admins** voient « Modifier » et « Supprimer » **directement sur la page front** (sur chaque carte partenaire) :
  - Afficher ces boutons seulement si `is_granted('ROLE_ADMIN')`.
  - « Modifier » → lien vers `app_admin_partner_edit` avec l’`id` du partenaire (même formulaire qu’en back office).
  - « Supprimer » → formulaire POST vers `app_admin_partner_delete` (même action qu’en back office), avec token CSRF et confirmation.
- Aucun nouveau template CRUD : tu réutilises les **routes et templates admin** existants.

### Étape D – (Plus tard) Offres partenaires (PartnerOffer)
- En back office : gestion des **offres** par partenaire (liste, ajout, modification, suppression), soit dans une section dédiée, soit dans la fiche partenaire (sous-formulaire).
- Sur le front : afficher les offres sur la page détail partenaire (étape B).

---

## 5. Résumé

| Où | Quoi | Statut |
|----|------|--------|
| **Front** | Liste des partenaires | Fait |
| **Front** | Bouton « Gérer les partenariats » (admins) → back office | À ajouter (lien simple) |
| **Front** | Page « Voir plus » (détail partenaire) | À faire (route + template) |
| **Front** | Boutons Modifier / Supprimer (admins, optionnel) | Liens vers admin possibles |
| **Back office** | CRUD Partenariats (liste, ajout, modification, suppression) | Déjà fait |
| **Accès back office** | Compte ROLE_ADMIN + connexion + menu ou URL | À faire de ton côté (compte admin) |

En pratique :  
- **Back office** = l’endroit où tu fais tout le CRUD ; **un seul** template/CRUD Partenariats.  
- **Front** = liste + détail + liens vers le back office pour les admins.  
- Pour « avoir accès dans le projet », il suffit d’avoir **un compte admin** et, si tu veux, un bouton sur la page front qui mène à `/admin/partenariats`.

Si tu veux, on peut enchaîner en détaillant le code exact pour :  
- le bouton « Gérer les partenariats » sur le front (avec `is_granted('ROLE_ADMIN')`),  
- la route + action `show` + template `partenariats/show.html.twig` pour « Voir plus ».
