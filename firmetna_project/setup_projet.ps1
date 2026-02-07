# Script de configuration du projet Firmetna (Symfony)
# À lancer dans PowerShell depuis le dossier du projet ou en indiquant le chemin.
# Utilisation : .\setup_projet.ps1

$ErrorActionPreference = "Stop"
$projectDir = if ($PSScriptRoot) { $PSScriptRoot } else { Get-Location }

Write-Host "=== Projet Firmetna - Installation ===" -ForegroundColor Cyan
Write-Host "Dossier : $projectDir`n" -ForegroundColor Gray

Set-Location $projectDir

# 1. Composer install
Write-Host "[1/5] Installation des dependances PHP (composer install)..." -ForegroundColor Yellow
composer install --no-interaction
if ($LASTEXITCODE -ne 0) { throw "composer install a echoue." }
Write-Host "OK`n" -ForegroundColor Green

# 2. Base de données : créer si elle n'existe pas
Write-Host "[2/5] Creation de la base de donnees si necessaire..." -ForegroundColor Yellow
php bin/console doctrine:database:create --if-not-exists 2>$null
if ($LASTEXITCODE -ne 0) {
    Write-Host "Attention : creation base echouee (peut-etre deja existante ou MySQL non demarre). On continue." -ForegroundColor DarkYellow
}
Write-Host "OK`n" -ForegroundColor Green

# 3. Migrations
Write-Host "[3/5] Execution des migrations (tables)..." -ForegroundColor Yellow
php bin/console doctrine:migrations:migrate --no-interaction
if ($LASTEXITCODE -ne 0) { throw "doctrine:migrations:migrate a echoue. Verifie MySQL et .env (DATABASE_URL)." }
Write-Host "OK`n" -ForegroundColor Green

# 4. Cache clear
Write-Host "[4/5] Vidage du cache Symfony..." -ForegroundColor Yellow
php bin/console cache:clear
Write-Host "OK`n" -ForegroundColor Green

# 5. Assets (optionnel)
Write-Host "[5/5] Installation des assets..." -ForegroundColor Yellow
php bin/console importmap:install 2>$null
php bin/console assets:install public 2>$null
Write-Host "OK`n" -ForegroundColor Green

Write-Host "=== Installation terminee. ===" -ForegroundColor Green
Write-Host ""
Write-Host "Pour lancer le site, execute dans ce meme dossier :" -ForegroundColor Cyan
Write-Host '  php -S localhost:8000 -t public' -ForegroundColor White
Write-Host ""
Write-Host "Puis ouvre dans le navigateur :" -ForegroundColor Cyan
Write-Host "  Page d'accueil : http://localhost:8000/" -ForegroundColor White
Write-Host "  Page login    : http://localhost:8000/login" -ForegroundColor White
Write-Host ""
