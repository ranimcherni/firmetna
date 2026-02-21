# ⚡ Commandes rapides à exécuter

## 🎯 EXÉCUTER LA MIGRATION (CHOISISSEZ UNE OPTION)

### Option 1 : Script automatique (Windows)
```bash
# Double-cliquez sur le fichier :
executer_migration.bat
```

### Option 2 : Commandes manuelles
```bash
# 1. Aller dans le dossier
cd firmetna

# 2. Vérifier l'état
php bin/console doctrine:migrations:status

# 3. Exécuter la migration
php bin/console doctrine:migrations:migrate

# 4. Vider le cache
php bin/console cache:clear
```

### Option 3 : SQL direct (si Symfony ne fonctionne pas)
1. Ouvrez phpMyAdmin ou votre outil SQL
2. Sélectionnez votre base de données
3. Exécutez le fichier : `migrations/forum_features_migration.sql`

---

## ✅ VÉRIFICATION

### Vérifier que tout fonctionne :
```bash
# Vérifier le schéma
php bin/console doctrine:schema:validate
```

### Vérifier les routes :
```bash
# Voir les routes du forum
php bin/console debug:router | grep forum
```

---

## 🧪 TESTS RAPIDES

1. **Likes** : `/forum` → Cliquez sur le bouton "J'aime"
2. **Réponses** : Ouvrez une publication → Cliquez "Répondre" sous un commentaire
3. **Recherche** : `/forum` → Utilisez la barre de recherche
4. **Notifications** : Likez/Commentez avec un autre compte → Vérifiez le badge

---

## 📞 EN CAS DE PROBLÈME

### Erreur "Migration already executed"
→ C'est OK, la migration a déjà été exécutée

### Erreur "Table already exists"
→ Les tables existent déjà, tout est OK

### Erreur de connexion à la base de données
→ Vérifiez votre fichier `.env` et les paramètres de connexion

---

**C'est tout ! Exécutez la migration et c'est prêt ! 🚀**
