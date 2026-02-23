# Why you don't see Partner tables + what to do

## Why your entities are not in the database

- The **SQL file** your teammates sent contains **only their tables**: `user`, `doctrine_migration_versions`, `messenger_messages`.
- Your **Partner** and **PartnerOffer** tables are **not** in that file. They are created by **running a Symfony migration** in your project.

So: import their SQL = you get `user` etc. Run the migration = you get `partner` and `partner_offer`.

---

## What to do now (in this folder: firmetna_partners)

### Step 1 – Install dependencies

In a terminal:

```bash
cd C:\Users\user\Desktop\firmetna_partners
composer install
```

If you get errors about PHP version (e.g. "requires php ^8.2"), run instead:

```bash
composer update
```

### Step 2 – Tell Symfony the "user" migration is already done

Your teammates’ export already created the `user` table. So we only mark that migration as executed:

```bash
php bin/console doctrine:migrations:version "DoctrineMigrations\Version20260128221548" --add --no-interaction
```

### Step 3 – Create your Partner tables

This creates the `partner` and `partner_offer` tables:

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

When it asks "Are you sure?", type **yes** and press Enter.

### Step 4 – Check in phpMyAdmin

Open database **firmetna_new_db**. You should see: `user`, `partner`, `partner_offer`, `doctrine_migration_versions`, `messenger_messages`.
