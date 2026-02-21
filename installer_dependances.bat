@echo off
echo ========================================
echo   INSTALLATION DES DEPENDANCES COMPOSER
echo ========================================
echo.

cd /d "%~dp0"

echo Verification de Composer...
composer --version
if %errorlevel% neq 0 (
    echo.
    echo ERREUR: Composer n'est pas installe !
    echo.
    echo Installez Composer depuis : https://getcomposer.org/download/
    echo.
    pause
    exit /b 1
)
echo.

echo Installation des dependances Composer...
echo Cela peut prendre quelques minutes...
echo.
composer install
echo.

if %errorlevel% equ 0 (
    echo ========================================
    echo   INSTALLATION TERMINEE AVEC SUCCES !
    echo ========================================
    echo.
    echo Vous pouvez maintenant demarrer le projet :
    echo   symfony server:start
    echo   OU
    echo   php -S localhost:8000 -t public
    echo.
) else (
    echo ========================================
    echo   ERREUR LORS DE L'INSTALLATION
    echo ========================================
    echo.
    echo Verifiez :
    echo - PHP est installe et dans le PATH
    echo - Composer est installe
    echo - Vous avez une connexion Internet
    echo.
)
echo.
pause
