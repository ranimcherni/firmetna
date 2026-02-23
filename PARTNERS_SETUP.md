# FIRMETNA – Partners page – Step-by-step setup (your part)

FIRMETNA is a Symfony site (front + back office) for selling agricultural products. You are in charge of the **Partners** page. Your teammates handle Forum, User/Login, etc.

This guide tells you, step by step:
1. How to **import the database file** your teammates sent you (so you have their tables: user, etc.).
2. How to **add your 2 tables** (partners list + offerings/donors).
3. How to **open and run the project** on your PC with the console.

---

## 1. Prerequisites on your PC

- **PHP 8.2 or higher** – This project (Symfony 7) needs PHP 8.2+. XAMPP often ships with PHP 8.1; if you get “Dependencies are missing” or “syntax error, unexpected token” when running `php bin/console`, your PHP is too old. **Fix:** install a XAMPP version with PHP 8.2+, or install [PHP 8.2](https://windows.php.net/download/) and put it first in your system PATH so `php -v` shows 8.2+. If you installed PHP 8.3 (e.g. Desktop\php83) but `php -v` still shows 8.1: **double‑click `use-php83.bat`** in the project folder to open a terminal where `php` is 8.3.
- **Composer** – [getcomposer.org](https://getcomposer.org).
- **XAMPP** – Start **Apache** and **MySQL** when you work. The project uses MySQL on port **3307** and database **firmetna_new_db** (you can change the port in `.env` if yours is different, e.g. 3306).

---

## 2. Open the project in the console

1. Open **PowerShell** or **Command Prompt**.
2. Go to **your** project folder (adjust if your path is different):
   ```bash
   cd C:\Users\user\Desktop\firmetna_partners
   ```
3. Install dependencies (first time only):
   ```bash
   composer install
   ```

---

## 3. Import your teammates’ database file

You need the database **firmetna_new_db** with the tables from the export they sent you (e.g. `firmetna_new_db_export.sql`).

### Option A – Using phpMyAdmin (XAMPP)

1. Start **Apache** and **MySQL** in XAMPP.
2. Open **http://localhost/phpmyadmin** (or the port where you run phpMyAdmin).
3. Create a database named **firmetna_new_db** (if it does not exist), and set collation to **utf8mb4_general_ci**.
4. Select **firmetna_new_db** → **Import**.
5. Choose the `.sql` file your teammate sent you.
6. If the file was saved as **UTF-16**, the import may fail or show strange characters. In that case:
   - Open the `.sql` file in Notepad++, choose **Encoding → Convert to UTF-8**, save, then import again.
7. Click **Go** to run the import. You should see tables like **user**, **doctrine_migration_versions**, **messenger_messages**.

### Option B – Using MySQL command line

1. Start MySQL in XAMPP.
2. If your MySQL is on port **3307** (as in `.env`):
   ```bash
   "C:\xampp\mysql\bin\mysql.exe" -u root -P 3307 -e "CREATE DATABASE IF NOT EXISTS firmetna_new_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```
3. Import the SQL file (adjust path and port):
   ```bash
   "C:\xampp\mysql\bin\mysql.exe" -u root -P 3307 firmetna_new_db < "C:\Users\user\Desktop\firmetna_partners\firmetna_new_db_export.sql"
   ```
   If you get encoding errors, convert the `.sql` file to UTF-8 (e.g. with Notepad++) and try again.

### Check `.env` database URL

Your project uses:

- Host: `127.0.0.1`
- Port: **3307**
- Database: **firmetna_new_db**
- User: **root**, no password (empty after `root:`)

If your XAMPP MySQL uses another port (e.g. 3306), edit `.env`:

```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/firmetna_new_db?serverVersion=8.0&charset=utf8mb4"
```

---

## 4. Mark the existing migration as already executed

The export already created the **user** (and other) tables. So we must tell Symfony that the **first** migration is already applied, to avoid errors when running migrations.

In the project folder, run:

```bash
php bin/console doctrine:migrations:version "DoctrineMigrations\Version20260128221548" --add
```

This marks `Version20260128221548` as executed without running it again.

---

## 5. Run the migration that adds the Partners tables

This creates the **partner** and **partner_offer** tables:

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

If it asks for confirmation, type `yes` or run again with `--no-interaction` as above.

You should see the migration `Version20260208120000` (or similar) executed.

---

## 6. Open the project in the browser

From the project folder, start the dev server.

**Option 1 – PHP built-in server (no extra install):**
```bash
php -S localhost:8000 -t public
```

**Option 2 – Symfony CLI (if installed):**
```bash
symfony serve
```

Then open in your browser: **http://127.0.0.1:8000** (or the URL shown in the console).

- **Partners (front) page:** http://127.0.0.1:8000/partenariats-front

---

## Summary – do these in order

| Step | What to do |
|------|------------|
| 1 | Install PHP, Composer, XAMPP. Start Apache + MySQL in XAMPP. |
| 2 | Open console → `cd C:\Users\user\Desktop\firmetna_partners` → `composer install` |
| 3 | Create database **firmetna_new_db** in XAMPP; **import** the `.sql` file your teammates sent (convert to UTF-8 if import fails). |
| 4 | In `.env`, set `DATABASE_URL` (port 3307 or 3306 depending on your XAMPP). |
| 5 | In project folder: `php bin/console doctrine:migrations:version "DoctrineMigrations\Version20260128221548" --add` |
| 6 | Then: `php bin/console doctrine:migrations:migrate --no-interaction` |
| 7 | Start the site: `php -S localhost:8000 -t public` → open **http://127.0.0.1:8000** in the browser. |

---

## Your 2 entities (your 2 tables)

You were asked for **2 tables**: one for the list of partners, one for offerings/donors. The project already has exactly that:

1. **Partner** (table `partner`) – **List of partners**  
   - Name, type (Entreprise, ONG, Coopérative, Association, etc.), email, phone, address, description, website, logo, status, created_at.

2. **PartnerOffer** (table `partner_offer`) – **Offerings / donations** from each partner  
   - Linked to one **Partner**.  
   - Type: Donation, Sponsorship, Product, Service, Other.  
   - Title, description, optional amount (money), date, status.

So: one table for partners, one for their offers/donations, with a relation between them. You can build your Partners pages (list, create, edit) using the entities in `src/Entity/Partner.php`, `src/Entity/PartnerOffer.php` and the existing `PartenariatsController`.
