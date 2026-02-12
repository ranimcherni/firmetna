# FIRMETNA – Your separate Partners workspace

This folder is **your own copy** of the project for developing the **Partners** module, similar to how your friend works in a separate folder.

- **Location:** `C:\Users\user\Desktop\firmetna_partners`
- **Original project:** `C:\Users\user\Desktop\firmetna_project` (group repo)

Work here; when you're done you can merge your Partners changes back into the main project or share your files with the group.

---

## First-time setup in this folder

1. **Open a terminal** and go to this folder:
   ```bash
   cd C:\Users\user\Desktop\firmetna_partners
   ```

2. **Install PHP dependencies** (creates `vendor/`):
   ```bash
   composer install
   ```

3. **Database**
   - Use XAMPP (MySQL on port 3307, or change in `.env`).
   - Create database **firmetna_new_db** and import the group's SQL export (e.g. `firmetna_new_db_export.sql`) if you haven't already.
   - Mark the old migration as done, then run migrations:
   ```bash
   php bin/console doctrine:migrations:version "DoctrineMigrations\Version20260128221548" --add
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

4. **Run the site**
   ```bash
   php -S localhost:8000 -t public
   ```
   Open **http://localhost:8000** and **http://localhost:8000/partenariats-front** for the Partners page.

---

## Your Partners files in this folder

- `src/Entity/Partner.php` – Partner entity
- `src/Entity/PartnerOffer.php` – PartnerOffer entity
- `src/Repository/PartnerRepository.php`
- `src/Repository/PartnerOfferRepository.php`
- `src/Controller/PartenariatsController.php` – Partners controller
- `migrations/Version20260208120000.php` – partner + partner_offer tables

Full setup details: **PARTNERS_SETUP.md** in this folder.
