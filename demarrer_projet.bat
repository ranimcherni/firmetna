@echo off
echo ========================================
echo   DEMARRAGE DU PROJET FIRMETNA
echo ========================================
echo.

cd /d "%~dp0"

echo [1/5] Verification de PHP...
php -v
if %errorlevel% neq 0 (
    echo ERREUR: PHP n'est pas installe ou pas dans le PATH
    pause
    exit /b 1
)
echo.

echo [2/5] Installation des dependances Composer...
if not exist "vendor" (
    echo Installation des dependances...
    composer install
) else (
    echo Dependances deja installees.
)
echo.

echo [3/5] Verification de la base de donnees...
php bin/console doctrine:database:create --if-not-exists
echo.

echo [4/5] Execution des migrations...
php bin/console doctrine:migrations:migrate --no-interaction
echo.

echo [5/5] Vidage du cache...
php bin/console cache:clear
echo.

echo ========================================
echo   PROJET PRET !
echo ========================================
echo.
echo Pour demarrer le serveur, executez :
echo   symfony server:start
echo.
echo OU
echo   php -S localhost:8000 -t public
echo.
echo Puis ouvrez : http://localhost:8000
echo.
pause
