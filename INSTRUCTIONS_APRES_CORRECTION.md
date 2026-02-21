# ✅ Correction effectuée - Instructions

## 🔧 Ce qui a été corrigé

**Fichier :** `.env` (à la racine du projet)

**Changement :**
- ❌ Avant : Port `3307`
- ✅ Après : Port `3306` (port par défaut MySQL)

---

## 🎯 MAINTENANT, VOUS DEVEZ :

### Étape 1 : Démarrer MySQL ⚠️ IMPORTANT

**Si vous utilisez XAMPP :**
1. Ouvrez **XAMPP Control Panel**
2. Cliquez sur **Start** pour **MySQL**
3. Attendez que le statut passe à **Running** (vert)

**Si vous utilisez WAMP :**
1. Cliquez sur l'icône **WAMP** dans la barre des tâches
2. Cliquez sur **Start All Services**

**Si vous utilisez MySQL standalone :**
```bash
net start MySQL80
# OU
net start MySQL
```

---

### Étape 2 : Vérifier la connexion

Testez que MySQL fonctionne :

```bash
# Option A : Script automatique
# Double-cliquez sur :
test_mysql_connection.bat

# Option B : Commande manuelle
php -r "try { new PDO('mysql:host=127.0.0.1:3306', 'root', ''); echo '✓ MySQL OK\n'; } catch(Exception \$e) { echo '✗ Erreur: ' . \$e->getMessage() . '\n'; }"
```

---

### Étape 3 : Exécuter les migrations

Une fois MySQL démarré et la connexion vérifiée :

```bash
php bin/console doctrine:migrations:migrate
```

**Si demandé, tapez `yes` pour confirmer.**

---

## ⚠️ Si ça ne fonctionne toujours pas

### Vérifier le port réel de MySQL

1. **Dans XAMPP :** Le port est affiché dans le panneau de contrôle
2. **Dans WAMP :** Cliquez sur l'icône → MySQL → Utiliser le port
3. **Via ligne de commande :**
   ```bash
   netstat -an | findstr :3306
   netstat -an | findstr :3307
   ```

### Si votre MySQL utilise vraiment le port 3307

Remettez le port 3307 dans `.env` :
```env
DATABASE_URL="mysql://root:@127.0.0.1:3307/firmetna_new_db?serverVersion=8.0&charset=utf8mb4"
```

---

## 📋 Checklist

- [ ] MySQL est démarré (vérifié dans XAMPP/WAMP)
- [ ] Le port dans `.env` est `3306` (ou le port réel de votre MySQL)
- [ ] La connexion fonctionne (testé)
- [ ] Les migrations sont exécutées

---

## ✅ Résumé

1. ✅ **Port corrigé** dans `.env` : `3307` → `3306`
2. ⏳ **À faire maintenant** : Démarrer MySQL
3. ⏳ **Ensuite** : Exécuter `php bin/console doctrine:migrations:migrate`

---

**La correction est faite ! Démarrez MySQL et réessayez ! 🚀**
