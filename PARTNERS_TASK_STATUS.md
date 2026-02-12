# Partenariats – État d’avancement vs objectifs du jour

Ce document aligne **les exigences du projet** avec ce qui est **déjà fait** et ce qu’il **reste à faire** pour le module Partenariats.

---

## 1. Exigences du jour vs statut

### 1.1 Template Front Office + Back Office avec liens fonctionnels

| Exigence | Statut | Détail |
|----------|--------|--------|
| **Template Front** pour les pages du module | ✅ Fait | `templates/partenariats/index.html.twig` – liste des partenaires, hero, cartes, boutons. |
| **Template Back** pour les pages du module | ✅ Fait | `templates/admin/partner/` – index, new, edit, _form. |
| **Liens fonctionnels entre les pages** | ✅ Fait | Accueil → Découvrir → Partenariats ; Front Partenariats → « Gérer » / « Modifier » / « Supprimer » (admin) → back office ; sidebar admin → Partenariats. |

**À prévoir :** page **détail** partenaire (front) « Voir plus » → une page dédiée (optionnel pour « toutes les pages » si vous considérez que la liste suffit).

---

### 1.2 Entités + CRUD avec au moins une relation

| Élément | Statut | Détail |
|---------|--------|--------|
| **Entité Partner** | ✅ Fait | Table `partner`, champs, contraintes. |
| **Entité PartnerOffer** | ✅ Fait | Table `partner_offer`, **relation ManyToOne vers Partner** (au moins une relation ✅). |
| **CRUD Partner** | ✅ Fait | Back : liste, créer, modifier, supprimer. Front : liste + liens admin (Modifier / Supprimer). |
| **CRUD PartnerOffer** | ❌ À faire | Back : liste, créer, modifier, supprimer les offres (liées à un partenaire). Front : affichage des offres (ex. sur détail partenaire). |

**Où on en est :**  
- **Partner** : entité + CRUD complets (back + intégration front).  
- **PartnerOffer** : entité + relation existent ; il reste à **faire le CRUD** (formulaire, contrôleur admin, templates) et, si besoin, l’affichage côté front.

---

### 1.3 Contrôles de saisie côté serveur (pas HTML/JS)

| Exigence | Statut | Détail |
|----------|--------|--------|
| **Validation serveur Partner** | ✅ Fait | `Partner.php` : `Assert\NotBlank`, `Assert\Length`, `Assert\Choice`, `Assert\Email`. Formulaire `PartnerType` sans validation côté client exclusive. |
| **Validation serveur PartnerOffer** | ✅ Fait (entité) | `PartnerOffer.php` : `Assert\NotBlank`, `Assert\Choice`, `Assert\Length`. |
| **Formulaires** | ✅ (Partner) / À faire (Offer) | Pas de validation **uniquement** en HTML/JS ; on s’appuie sur les contraintes Symfony (entité + formulaire). À reproduire pour le formulaire des offres. |

**À vérifier partout :** ne pas ajouter de validation **uniquement** en HTML (ex. `required`) ou en JavaScript ; garder la validation dans les **entités** et **formulaires Symfony**.

---

### 1.4 Fonctionnalités avancées (recherche, tri, API…)

| Exigence | Statut | Détail |
|----------|--------|--------|
| **Recherche** | ✅ Fait (Partner) | Back office Partenariats : champ recherche (nom, email, description) + filtre par statut. |
| **Tri** | ✅ Fait (Partner) | Liste partenaires triée par nom (ASC). |
| **Recherche / tri PartnerOffer** | ❌ À faire | Quand le CRUD offres existera : recherche (titre, type) et tri (date, statut). |
| **Intégration API** (ou autre avancé) | ❌ À faire | À définir : ex. API externe (données partenaire, taux de change…) ou petit endpoint REST pour votre module. |

---

## 2. Récap : où on en est

- **Partner (1ère entité)**  
  - Template front + back ✅  
  - CRUD complet ✅  
  - Relation (Partner → PartnerOffer) ✅  
  - Validation serveur ✅  
  - Recherche + tri ✅  

- **PartnerOffer (2ème entité)**  
  - Entité + relation ✅  
  - CRUD (back + affichage front) ❌ **← prochaine étape**  
  - Validation serveur (entité déjà prête) ✅  
  - Recherche / tri ❌ (à faire avec le CRUD)  

- **Liens entre pages** ✅ pour ce qui existe.  
- **API ou autre fonctionnalité avancée** ❌ à ajouter après le CRUD offres.

---

## 3. Prochaines étapes (ordre recommandé)

### Étape 1 – CRUD PartnerOffer (priorité)

- **Back office**
  - Créer `PartnerOfferType` (formulaire avec partenaire, type, titre, description, montant, date, statut).
  - Créer `Admin\PartnerOfferController` (ou intégrer dans un sous-onglet « Offres » par partenaire) :
    - Liste des offres (avec filtre par partenaire, type, statut).
    - Créer une offre (choix du partenaire).
    - Modifier / supprimer une offre.
  - Templates admin : `admin/partner_offer/index.html.twig`, `new.html.twig`, `edit.html.twig`, `_form.html.twig`.
- **Lien avec Partner**  
  - Depuis la fiche partenaire (back), lien « Gérer les offres » ou liste des offres + « Ajouter une offre » (avec partenaire pré-rempli).
- **Front (optionnel mais utile)**  
  - Page détail partenaire « Voir plus » qui affiche les offres du partenaire (lecture seule).

C’est **la même logique** que pour Partner : une entité, un CRUD back, des liens depuis le front et depuis le back.

### Étape 2 – Vérification validation (côté serveur uniquement)

- S’assurer qu’aucun champ critique ne dépend **uniquement** de `required` en HTML ou de la validation JavaScript.
- Garder toutes les règles dans les **entités** (`Assert\*`) et, si besoin, dans les **formulaires** Symfony.

### Étape 3 – Recherche et tri pour PartnerOffer

- Dans la liste back des offres : champ de recherche (titre, type, partenaire), filtre par type/statut, tri (date, partenaire, statut).

### Étape 4 – Fonctionnalité avancée (API ou autre)

- **Option A – API externe :** ex. appeler une API (météo, taux de change pour les montants, géolocalisation…) et afficher le résultat sur une page Partenariats.
- **Option B – API exposée :** ex. endpoint `GET /api/partenariats` ou `GET /api/partenaires/{id}` pour fournir les données en JSON (pour une app mobile ou un partenaire technique).
- **Option C – Autre avancé :** export CSV/Excel des partenaires ou des offres, ou génération de PDF (fiche partenaire).

Choisir **une** de ces options selon ce que vous voulez montrer dans le module.

---

## 4. Checklist rapide (à cocher au fur et à mesure)

- [x] Template front Partenariats
- [x] Template back Partenariats (Partner)
- [x] Liens fonctionnels (accueil → partenariats → admin)
- [x] Entité Partner + relation vers PartnerOffer
- [x] Entité PartnerOffer + relation vers Partner
- [x] CRUD Partner (back + liens front)
- [x] Validation serveur Partner (entité)
- [x] Recherche + tri Partner (back)
- [ ] **CRUD PartnerOffer (back, puis affichage front si besoin)**
- [ ] Validation formulaire PartnerOffer (côté serveur uniquement)
- [ ] Recherche + tri PartnerOffer
- [ ] Page détail partenaire « Voir plus » (front, optionnel)
- [ ] Intégration API ou autre fonctionnalité avancée

---

## 5. En résumé

- **Aujourd’hui on a :** 1 entité (Partner) avec CRUD complet, templates front/back, liens, validation serveur, recherche et tri.  
- **Il reste à faire pour « finir le module » au sens du sujet :**  
  1) **CRUD pour la 2ème entité (PartnerOffer)** – même principe que Partner ;  
  2) **Recherche/tri** sur les offres ;  
  3) **Une** fonctionnalité avancée (API ou autre).  

En enchaînant dans cet ordre (CRUD offres → recherche/tri offres → API/avancé), vous avancez vite et de façon claire.
