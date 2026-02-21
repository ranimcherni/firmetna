# Instructions pour corriger la base de données

## ⚠️ IMPORTANT : La base de données n'est PAS encore fixée automatiquement

Vous devez exécuter les migrations manuellement. Voici comment faire :

## Option 1 : Utiliser les migrations Doctrine (RECOMMANDÉ)

### Étape 1 : Créer la migration
```bash
cd firmetna
php bin/console make:migration
```

### Étape 2 : Exécuter la migration
```bash
php bin/console doctrine:migrations:migrate
```

## Option 2 : Exécuter le SQL manuellement

Si les migrations Doctrine ne fonctionnent pas, vous pouvez exécuter le SQL directement :

### Pour MySQL 8.0+ :
```bash
mysql -u votre_utilisateur -p votre_base_de_donnees < migrations/forum_features_migration.sql
```

### Pour MySQL 5.7 ou antérieur :
```bash
mysql -u votre_utilisateur -p votre_base_de_donnees < migrations/forum_features_migration_mysql57.sql
```

### Via phpMyAdmin ou autre outil :
1. Ouvrez votre outil de gestion de base de données
2. Sélectionnez votre base de données
3. Exécutez le contenu du fichier `migrations/forum_features_migration.sql`

## Vérification

Après avoir exécuté les migrations, vérifiez que les tables existent :

```sql
-- Vérifier la table 'like'
SHOW TABLES LIKE 'like';

-- Vérifier la table 'notification'
SHOW TABLES LIKE 'notification';

-- Vérifier les colonnes de 'commentaire'
DESCRIBE commentaire;
-- Vous devriez voir : parent_id et date_modification
```

## Tables qui seront créées/modifiées :

1. **Table `like`** (nouvelle)
   - id
   - user_id (FK vers user)
   - publication_id (FK vers publication)
   - date_creation

2. **Table `notification`** (nouvelle)
   - id
   - destinataire_id (FK vers user)
   - auteur_id (FK vers user)
   - publication_id (FK vers publication, nullable)
   - commentaire_id (FK vers commentaire, nullable)
   - type (VARCHAR 50)
   - lu (BOOLEAN)
   - date_creation

3. **Table `commentaire`** (modifiée)
   - Ajout de `parent_id` (FK vers commentaire, nullable)
   - Ajout de `date_modification` (DATETIME, nullable)

## ⚠️ Erreurs possibles

Si vous obtenez une erreur "Table already exists" :
- C'est normal si vous avez déjà exécuté la migration
- Vous pouvez ignorer cette erreur ou supprimer les tables existantes avant de réexécuter

Si vous obtenez une erreur de clé étrangère :
- Vérifiez que les tables `user`, `publication` et `commentaire` existent
- Vérifiez que les colonnes référencées existent

## Après la migration

Une fois les migrations exécutées :
1. Videz le cache : `php bin/console cache:clear`
2. Testez les fonctionnalités :
   - Créer un like
   - Répondre à un commentaire
   - Vérifier les notifications
