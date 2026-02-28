# 🔧 Solution : Erreur de connexion MySQL

## ❌ Problème

```
SQLSTATE[HY000] [2002] No connection could be made because the target machine actively refused it
```

Cette erreur signifie que **MySQL n'est pas démarré** ou que les **paramètres de connexion sont incorrects**.

---

## ✅ Solutions

### Solution 1 : Démarrer MySQL (RECOMMANDÉ)

#### Si vous utilisez XAMPP :
1. Ouvrez le **XAMPP Control Panel**
2. Cliquez sur **Start** pour **MySQL**
3. Attendez que le statut passe à **Running** (vert)

#### Si vous utilisez WAMP :
1. Ouvrez le **WAMP Server**
2. Cliquez sur **Start All Services**
3. Vérifiez que MySQL est vert

#### Si vous utilisez MySQL standalone :
```bash
# Windows (Service)
net start MySQL80
# OU
net start MySQL

# Vérifier le statut
sc query MySQL80
```

---

### Solution 2 : Vérifier les paramètres de connexion

Vérifiez votre fichier `.env` :

```env
DATABASE_URL="mysql://root:@127.0.0.1:3307/firmetna_new_db?serverVersion=8.0&charset=utf8mb4"
```

**Paramètres à vérifier :**
- `root` = votre utilisateur MySQL (peut être différent)
- `@` = votre mot de passe MySQL (vide ici, mais peut nécessiter un mot de passe)
- `127.0.0.1:3307` = host et port MySQL
  - **Port par défaut MySQL** : `3306`
  - **Votre config** : `3307`
- `firmetna_new_db` = nom de votre base de données

---

### Solution 3 : Corriger le port MySQL

Si MySQL utilise le port **3306** (par défaut), modifiez `.env` :

```env
# Changez de :
DATABASE_URL="mysql://root:@127.0.0.1:3307/firmetna_new_db?serverVersion=8.0&charset=utf8mb4"

# À :
DATABASE_URL="mysql://root:@127.0.0.1:3306/firmetna_new_db?serverVersion=8.0&charset=utf8mb4"
```

---

### Solution 4 : Tester la connexion MySQL

Testez si MySQL répond :

```bash
# Test de connexion simple
php -r "try { new PDO('mysql:host=127.0.0.1:3306', 'root', ''); echo 'MySQL OK\n'; } catch(Exception \$e) { echo 'Erreur: ' . \$e->getMessage() . '\n'; }"
```

---

### Solution 5 : Exécuter le SQL manuellement (ALTERNATIVE)

Si vous ne pouvez pas démarrer MySQL maintenant, vous pouvez exécuter le SQL directement plus tard :

1. **Démarrez MySQL** (quand vous le pourrez)
2. **Ouvrez phpMyAdmin** ou votre outil SQL préféré
3. **Sélectionnez votre base de données** (`firmetna_new_db`)
4. **Exécutez le fichier** : `migrations/forum_features_migration.sql`

---

## 🔍 Vérifications étape par étape

### Étape 1 : Vérifier que MySQL est démarré

```bash
# Windows - Vérifier le service
sc query MySQL80

# OU vérifier dans le gestionnaire de tâches
# Cherchez "mysqld.exe" ou "mysql.exe"
```

### Étape 2 : Vérifier le port MySQL

Dans votre fichier `.env`, le port est `3307`. Vérifiez quel port MySQL utilise réellement :

**Méthode 1 : Via XAMPP/WAMP**
- Regardez dans le panneau de contrôle
- Le port est généralement affiché

**Méthode 2 : Via ligne de commande**
```bash
netstat -an | findstr :3306
netstat -an | findstr :3307
```

### Étape 3 : Tester la connexion

```bash
# Test avec port 3306 (défaut)
php -r "new PDO('mysql:host=127.0.0.1:3306', 'root', '');"

# Test avec port 3307 (votre config)
php -r "new PDO('mysql:host=127.0.0.1:3307', 'root', '');"
```

---

## 📋 Checklist de résolution

- [ ] MySQL est démarré (vérifié dans XAMPP/WAMP)
- [ ] Le port dans `.env` correspond au port MySQL réel
- [ ] L'utilisateur MySQL est correct (`root` ou autre)
- [ ] Le mot de passe MySQL est correct (vide `@` ou avec mot de passe)
- [ ] La base de données `firmetna_new_db` existe (ou sera créée)

---

## 🎯 Actions immédiates

### Option A : Démarrer MySQL et réessayer

1. **Démarrez MySQL** (XAMPP/WAMP)
2. **Vérifiez le port** dans `.env`
3. **Réessayez** :
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

### Option B : Modifier le port dans .env

Si MySQL utilise le port **3306** :

1. **Ouvrez** `firmetna/.env`
2. **Changez** `3307` en `3306`
3. **Réessayez** :
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

### Option C : Exécuter le SQL manuellement

1. **Démarrez MySQL** quand vous le pourrez
2. **Ouvrez phpMyAdmin**
3. **Exécutez** le fichier `migrations/forum_features_migration.sql`

---

## ⚠️ Note importante

**Les migrations ne peuvent pas s'exécuter sans MySQL démarré.**

Vous devez :
1. ✅ Démarrer MySQL
2. ✅ Vérifier/corriger le port dans `.env`
3. ✅ Puis exécuter les migrations

---

**Une fois MySQL démarré, réessayez la commande de migration ! 🚀**
