# Fix: "Metadata storage is not up to date" after sync

If `doctrine:migrations:sync-metadata-storage` succeeds but `doctrine:migrations:version ... --add` still says "metadata storage is not up to date", use one of these approaches.

---

## Option 1: Clear cache, then sync and version (try this first)

In the project folder (with PHP 8.3 in PATH), run **all in one line**:

```bat
php bin/console cache:clear && php bin/console doctrine:migrations:sync-metadata-storage && php bin/console doctrine:migrations:version "DoctrineMigrations\Version20260128221548" --add --no-interaction
```

If that works, then run:

```bat
php bin/console doctrine:migrations:migrate --no-interaction
```

---

## Option 2: Manual fix in the database (if Option 1 still fails) – **SAFE (keeps all your friends’ records)**

Your imported database has **4 migration records** in `doctrine_migration_versions`. Do **not** use SQL that inserts only one row – that would delete those 4 records. Use the **safe** SQL below: it recreates the table with the structure Doctrine expects and **re-inserts all 4 rows** with the same values, so you don’t lose anything.

### Step 1: Open MySQL (phpMyAdmin)

Select the database from your `.env` (e.g. **firmetna_new_db**).

### Step 2: Run this SQL (safe – keeps all 4 rows)

This drops the table, recreates it with the correct structure, and **re-inserts all 4 migration records** from your friends’ import (same `version`, `executed_at`, `execution_time`).

```sql
DROP TABLE IF EXISTS doctrine_migration_versions;

CREATE TABLE doctrine_migration_versions (
    version VARCHAR(191) NOT NULL,
    executed_at DATETIME DEFAULT NULL,
    execution_time INT DEFAULT NULL,
    PRIMARY KEY (version)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES
('DoctrineMigrations\\Version20260128221548', '2026-02-02 10:19:40', 261),
('DoctrineMigrations\\Version20260204211921', '2026-02-05 12:11:09', 387),
('DoctrineMigrations\\Version20260205000000', '2026-02-05 12:11:09', 10),
('DoctrineMigrations\\Version20260207120000', '2026-02-07 13:40:02', 66);
```

Note: each version value uses **two backslashes** `\\` in SQL (so MySQL stores one backslash).

### Step 3: Run migrations

Back in the project folder (with PHP 8.3):

```bat
php bin/console doctrine:migrations:migrate --no-interaction
```

Only the **Partners** migration (Version20260208120000) will run and create the `partner` and `partner_offer` tables. The 4 existing migration records stay as they are.

---

## Summary

| Option | When to use |
|--------|-------------|
| **Option 1** | First try: clear cache + sync + version in one go. |
| **Option 2** | If Option 1 still fails: recreate the table in MySQL, **re-insert all 4 rows** (safe – nothing lost), then run `migrate`. |
