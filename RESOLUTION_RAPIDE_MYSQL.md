# ⚡ Résolution rapide - Erreur MySQL

## ❌ Votre erreur

```
SQLSTATE[HY000] [2002] No connection could be made because the target machine actively refused it
```

**Cela signifie : MySQL n'est pas démarré ou le port est incorrect.**

---

## ✅ SOLUTION RAPIDE (3 étapes)

### Étape 1 : Démarrer MySQL

**Si vous utilisez XAMPP :**
1. Ouvrez **XAMPP Control Panel**
2. Cliquez sur **Start** pour **MySQL**
3. Attendez que ça passe en vert

**Si vous utilisez WAMP :**
1. Cliquez sur l'icône WAMP
2. **Start All Services**

---

### Étape 2 : Vérifier le port MySQL

Votre fichier `.env` utilise le port **3307**, mais MySQL utilise généralement le port **3306**.

**Testez quel port fonctionne :**

Double-cliquez sur :
```
test_mysql_connection.bat
```

---

### Étape 3 : Corriger le port si nécessaire

**Si le test montre que MySQL est sur le port 3306 :**

1. Ouvrez le fichier `.env` à la racine du projet
2. Trouvez cette ligne :
   ```env
   DATABASE_URL="mysql://root:@127.0.0.1:3307/firmetna_new_db?serverVersion=8.0&charset=utf8mb4"
   ```
3. Changez `3307` en `3306` :
   ```env
   DATABASE_URL="mysql://root:@127.0.0.1:3306/firmetna_new_db?serverVersion=8.0&charset=utf8mb4"
   ```

---

## 🎯 Ensuite, réessayez

```bash
php bin/console doctrine:migrations:migrate
```

---

## 📋 Checklist

- [ ] MySQL est démarré (vérifié dans XAMPP/WAMP)
- [ ] Le port dans `.env` est correct (3306 ou 3307)
- [ ] La connexion fonctionne (testé avec `test_mysql_connection.bat`)

---

**Une fois MySQL démarré et le port corrigé, les migrations fonctionneront ! 🚀**
