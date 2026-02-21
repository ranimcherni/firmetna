@echo off
chcp 65001 >nul
echo ========================================
echo   VERIFICATION DE L'INSTALLATION
echo ========================================
echo.

cd /d "%~dp0"

echo [1] Verification de PHP...
php -v
if %errorlevel% neq 0 (
    echo ✗ PHP NON INSTALLE
) else (
    echo ✓ PHP INSTALLE
)
echo.

echo [2] Verification de Composer...
composer --version
if %errorlevel% neq 0 (
    echo ✗ COMPOSER NON INSTALLE
) else (
    echo ✓ COMPOSER INSTALLE
)
echo.

echo [3] Verification du dossier vendor...
if exist "vendor" (
    echo ✓ DEPENDANCES INSTALLEES
) else (
    echo ✗ DEPENDANCES NON INSTALLEES
    echo   Executez: composer install
)
echo.

echo [4] Verification de MySQL...
php -r "try { new PDO('mysql:host=127.0.0.1:3307', 'root', ''); echo '✓ MySQL ACCESSIBLE\n'; } catch(Exception \$e) { echo '✗ MySQL NON ACCESSIBLE\n'; }"
echo.

echo [5] Verification de la base de donnees...
php bin/console doctrine:database:create --if-not-exists --no-interaction 2>nul
if %errorlevel% equ 0 (
    echo ✓ BASE DE DONNEES OK
) else (
    echo ✗ PROBLEME AVEC LA BASE DE DONNEES
)
echo.

echo [6] Verification des migrations...
php bin/console doctrine:migrations:status --no-interaction 2>nul
if %errorlevel% equ 0 (
    echo ✓ MIGRATIONS CONFIGUREES
) else (
    echo ✗ PROBLEME AVEC LES MIGRATIONS
)
echo.

echo ========================================
echo   VERIFICATION TERMINEE
echo ========================================
echo.
pause
