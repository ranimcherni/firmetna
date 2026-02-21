# 🚀 Exécuter les migrations - Guide rapide

## ✅ La migration est prête !

Le fichier de migration `Version20260220120000.php` a été créé avec succès.

## 📝 Étapes à suivre :

### Option 1 : Via la ligne de commande Symfony (RECOMMANDÉ)

```bash
# 1. Aller dans le dossier du projet
cd firmetna

# 2. Vérifier l'état des migrations
php bin/console doctrine:migrations:status

# 3. Exécuter la migration
php bin/console doctrine:migrations:migrate

# Si demandé, tapez "yes" pour confirmer
```

### Option 2 : Via phpMyAdmin ou autre outil SQL

Si la commande Symfony ne fonctionne pas, vous pouvez exécuter le SQL directement :

1. Ouvrez votre outil de gestion de base de données (phpMyAdmin, MySQL Workbench, etc.)
2. Sélectionnez votre base de données
3. Exécutez le contenu du fichier `migrations/forum_features_migration.sql`

## 🔍 Vérification après migration

### Vérifier que les tables existent :

```sql
-- Vérifier la table 'like'
SHOW TABLES LIKE 'like';

-- Vérifier la table 'notification'  
SHOW TABLES LIKE 'notification';

-- Vérifier les colonnes de 'commentaire'
DESCRIBE commentaire;
-- Vous devriez voir : parent_id et date_modification
```

### Vérifier via Symfony :

```bash
php bin/console doctrine:schema:validate
```

Si tout est OK, vous verrez : "The mapping files are correct."

## 🎯 Après la migration

1. **Vider le cache** :
   ```bash
   php bin/console cache:clear
   ```

2. **Tester les fonctionnalités** :
   - Allez sur `/forum`
   - Testez les likes
   - Testez les réponses aux commentaires
   - Vérifiez les notifications

## ⚠️ En cas d'erreur

### Erreur "Table already exists"
- C'est normal si vous avez déjà exécuté la migration
- Vous pouvez ignorer cette erreur

### Erreur de clé étrangère
- Vérifiez que les tables `user`, `publication` et `commentaire` existent
- Vérifiez que les colonnes référencées existent

### Erreur "Migration already executed"
- La migration a déjà été exécutée
- Tout est OK, vous pouvez passer aux tests

## ✅ Checklist

- [ ] Migration exécutée avec succès
- [ ] Tables `like` et `notification` créées
- [ ] Colonnes `parent_id` et `date_modification` ajoutées à `commentaire`
- [ ] Cache vidé
- [ ] Tests effectués

---

**La migration est prête à être exécutée !** 🎉
