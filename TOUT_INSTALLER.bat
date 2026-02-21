@echo off
chcp 65001 >nul
echo ========================================
echo   INSTALLATION COMPLETE DU PROJET
echo ========================================
echo.

cd /d "%~dp0"

echo [1/7] Verification de PHP...
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo ERREUR: PHP n'est pas installe ou pas dans le PATH
    echo Installez PHP 8.1+ depuis https://www.php.net/downloads.php
    pause
    exit /b 1
)
php -v
echo ✓ PHP OK
echo.

echo [2/7] Verification de Composer...
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERREUR: Composer n'est pas installe
    echo Installez Composer depuis https://getcomposer.org/download/
    pause
    exit /b 1
)
composer --version
echo ✓ Composer OK
echo.

echo [3/7] Installation des dependances Composer...
echo Cela peut prendre 2-5 minutes...
if not exist "vendor" (
    composer install --no-interaction
    if %errorlevel% neq 0 (
        echo ERREUR lors de l'installation des dependances
        pause
        exit /b 1
    )
    echo ✓ Dependances installees
) else (
    echo ✓ Dependances deja installees
)
echo.

echo [4/7] Verification de la connexion MySQL...
echo Test de connexion a la base de donnees...
php -r "try { new PDO('mysql:host=127.0.0.1:3307', 'root', ''); echo 'Connexion MySQL OK\n'; } catch(Exception \$e) { echo 'ERREUR: MySQL non accessible\n'; exit(1); }"
if %errorlevel% neq 0 (
    echo ATTENTION: Impossible de se connecter a MySQL
    echo Verifiez que MySQL est demarre sur le port 3307
    echo Vous pouvez continuer, mais la base de donnees ne sera pas configuree
    echo.
    set SKIP_DB=1
) else (
    echo ✓ MySQL accessible
    set SKIP_DB=0
)
echo.

if %SKIP_DB%==0 (
    echo [5/7] Creation de la base de donnees...
    php bin/console doctrine:database:create --if-not-exists --no-interaction 2>nul
    echo ✓ Base de donnees creee ou deja existante
    echo.

    echo [6/7] Execution des migrations...
    php bin/console doctrine:migrations:migrate --no-interaction
    if %errorlevel% neq 0 (
        echo ATTENTION: Erreur lors des migrations
        echo Vous pouvez les executer manuellement plus tard
    ) else (
        echo ✓ Migrations executees
    )
    echo.
) else (
    echo [5/7] Etape base de donnees sautee (MySQL non accessible)
    echo [6/7] Etape migrations sautee (MySQL non accessible)
    echo.
)

echo [7/7] Vidage du cache...
php bin/console cache:clear --no-interaction
echo ✓ Cache vide
echo.

echo ========================================
echo   INSTALLATION TERMINEE !
echo ========================================
echo.
echo Pour demarrer le serveur, executez :
echo.
echo   symfony server:start
echo.
echo OU
echo.
echo   php -S localhost:8000 -t public
echo.
echo Puis ouvrez votre navigateur sur :
echo   http://localhost:8000
echo.
echo ========================================
pause
