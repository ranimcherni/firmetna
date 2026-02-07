# Script : completer l'installation PHP et Composer et les ajouter au PATH
# Execute en PowerShell (clic droit -> Executer avec PowerShell) ou dans le terminal.
# Si besoin : Set-ExecutionPolicy -ExecutionPolicy Bypass -Scope Process -Force

$ErrorActionPreference = "Stop"

Write-Host "=== Installation PHP + Composer (completion) ===" -ForegroundColor Cyan

# ----- 1. Trouver PHP ou l'installer -----
$phpPath = $null
$phpExe = Get-Command php -ErrorAction SilentlyContinue
if ($phpExe) {
    $phpPath = Split-Path $phpExe.Source
    Write-Host "[OK] PHP trouve : $phpPath" -ForegroundColor Green
} else {
    # Chercher dans les emplacements courants (winget, etc.)
    $searchPaths = @(
        "C:\Program Files\PHP",
        "C:\php",
        "$env:LOCALAPPDATA\Programs\PHP",
        "$env:ProgramFiles\PHP",
        "${env:ProgramFiles(x86)}\PHP",
        "$env:LOCALAPPDATA\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe"
    )
    foreach ($dir in $searchPaths) {
        if (Test-Path "$dir\php.exe") {
            $phpPath = $dir
            Write-Host "[OK] PHP trouve : $phpPath" -ForegroundColor Green
            break
        }
    }
}

if (-not $phpPath) {
    Write-Host "[?] PHP non trouve. Installation avec winget..." -ForegroundColor Yellow
    winget install PHP.PHP.8.2 --accept-package-agreements --accept-source-agreements
    # Winget ajoute souvent PHP au PATH systeme; les chemins classiques :
    $searchPaths = @("C:\Program Files\PHP", "C:\php")
    foreach ($dir in $searchPaths) {
        if (Test-Path "$dir\php.exe") {
            $phpPath = $dir
            Write-Host "[OK] PHP installe : $phpPath" -ForegroundColor Green
            break
        }
    }
    if (-not $phpPath) {
        Write-Host "[ERREUR] PHP toujours introuvable. Verifie une installation manuelle." -ForegroundColor Red
        exit 1
    }
}

# Ajouter PHP au PATH utilisateur pour que Cursor le voie
$userPath = [Environment]::GetEnvironmentVariable("Path", "User")
if ($userPath -notlike "*$phpPath*") {
    [Environment]::SetEnvironmentVariable("Path", "$userPath;$phpPath", "User")
    Write-Host "[OK] PHP ajoute au PATH utilisateur." -ForegroundColor Green
}
$env:Path = "$env:Path;$phpPath"

# Verifier qu'on peut lancer php
& "$phpPath\php.exe" -v
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERREUR] php -v a echoue." -ForegroundColor Red
    exit 1
}

# ----- 2. Installer Composer -----
$composerDir = "C:\ProgramData\ComposerSetup\bin"
$composerPhar = "$composerDir\composer.phar"
$composerBat = "$composerDir\composer.bat"

if (Test-Path $composerPhar) {
    Write-Host "[OK] Composer deja present : $composerPhar" -ForegroundColor Green
} else {
    Write-Host "[...] Telechargement de Composer..." -ForegroundColor Yellow
    $tempDir = $env:TEMP
    $installer = "$tempDir\composer-setup.php"
    try {
        Invoke-WebRequest -Uri "https://getcomposer.org/installer" -OutFile $installer -UseBasicParsing
    } catch {
        Write-Host "[ERREUR] Telechargement echoue. Verifie ta connexion internet." -ForegroundColor Red
        exit 1
    }

    New-Item -ItemType Directory -Force -Path $composerDir | Out-Null
    Write-Host "[...] Installation de Composer..." -ForegroundColor Yellow
    & "$phpPath\php.exe" $installer --install-dir=$composerDir --filename=composer.phar
    Remove-Item $installer -Force -ErrorAction SilentlyContinue
    if (-not (Test-Path $composerPhar)) {
        Write-Host "[ERREUR] composer.phar non cree." -ForegroundColor Red
        exit 1
    }
    Write-Host "[OK] Composer installe." -ForegroundColor Green
}

# Fichier .bat pour appeler composer
Set-Content -Path $composerBat -Value '@php "%~dp0composer.phar" %*' -Encoding ASCII
Write-Host "[OK] composer.bat cree." -ForegroundColor Green

# Ajouter Composer au PATH utilisateur
$userPath = [Environment]::GetEnvironmentVariable("Path", "User")
if ($userPath -notlike "*$composerDir*") {
    [Environment]::SetEnvironmentVariable("Path", "$userPath;$composerDir", "User")
    Write-Host "[OK] Composer ajoute au PATH utilisateur." -ForegroundColor Green
}
$env:Path = "$env:Path;$composerDir"

# Verifier
Write-Host ""
Write-Host "=== Verification ===" -ForegroundColor Cyan
& "$phpPath\php.exe" -v
& "$composerDir\composer.phar" -V

Write-Host ""
Write-Host "=== Termine. ===" -ForegroundColor Green
Write-Host "Ferme Cursor completement, rouvre-le, ouvre le projet, puis dans le terminal tape :" -ForegroundColor Yellow
Write-Host "  php -v" -ForegroundColor White
Write-Host "  composer -V" -ForegroundColor White
Write-Host "  .\setup_projet.ps1" -ForegroundColor White
Write-Host ""
