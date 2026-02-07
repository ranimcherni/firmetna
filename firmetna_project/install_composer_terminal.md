# Installation Composer dans le terminal (Cursor)

Ouvre le terminal dans Cursor : **Terminal → Nouveau terminal** (ou Ctrl+ù).

Colle les commandes **une par une** (ou bloc par bloc si indiqué).

---

## 1. Aller dans Téléchargements

```powershell
cd $env:USERPROFILE\Downloads
```

*(Si tu es en CMD au lieu de PowerShell, utilise : `cd %USERPROFILE%\Downloads`)*

---

## 2. Télécharger l’installeur Composer

**PowerShell :**
```powershell
Invoke-WebRequest -Uri https://getcomposer.org/installer -OutFile composer-setup.php -UseBasicParsing
```

**Ou en CMD :**
```cmd
curl -o composer-setup.php https://getcomposer.org/installer
```

---

## 3. Lancer l’installation

```powershell
php composer-setup.php
```

*(Si "php" n’est pas reconnu : ferme Cursor, rouvre-le pour que le PATH soit à jour, ou installe PHP avec winget puis réessaie.)*

---

## 4. Créer le dossier et déplacer Composer

**PowerShell :**
```powershell
New-Item -ItemType Directory -Force -Path "C:\ProgramData\ComposerSetup\bin"
Move-Item -Force composer.phar "C:\ProgramData\ComposerSetup\bin\composer.phar"
```

**CMD :**
```cmd
mkdir "C:\ProgramData\ComposerSetup\bin"
move composer.phar "C:\ProgramData\ComposerSetup\bin\composer.phar"
```

---

## 5. Créer le fichier composer.bat

**PowerShell :**
```powershell
Set-Content -Path "C:\ProgramData\ComposerSetup\bin\composer.bat" -Value '@php "%~dp0composer.phar" %*'
```

**CMD :**
```cmd
echo @php "%~dp0composer.phar" %%* > "C:\ProgramData\ComposerSetup\bin\composer.bat"
```

---

## 6. Ajouter Composer au PATH

1. Touche **Windows** → tape **variables d’environnement**
2. **Modifier les variables d’environnement système**
3. **Variables d’environnement** → dans "Variables système" sélectionne **Path** → **Modifier** → **Nouveau**
4. Ajoute : `C:\ProgramData\ComposerSetup\bin` → **OK** partout
5. **Ferme le terminal** dans Cursor puis **Terminal → Nouveau terminal**

---

## 7. Vérifier

```powershell
composer -V
```

Tu dois voir : `Composer version 2.x.x`

---

## 8. Lancer le setup du projet

```powershell
cd C:\Users\USER\Desktop\firmetna_project
.\setup_projet.ps1
```
