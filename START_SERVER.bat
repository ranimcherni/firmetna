@echo off
title FIRMETNA - Partenariats
cd /d "C:\Users\user\Desktop\firmetna_partners"

REM Use PHP 8.3 by full path so XAMPP/system PHP is never used
set "PHP_EXE=C:\Users\user\Desktop\php83\php.exe"
if not exist "%PHP_EXE%" (
    echo ERREUR: PHP 8.3 introuvable dans: C:\Users\user\Desktop\php83\
    echo Verifiez que php.exe existe dans ce dossier.
    pause
    exit /b 1
)

echo.
echo ========================================
echo   FIRMETNA - Demarrage du serveur
echo ========================================
echo.

"%PHP_EXE%" -v
echo.
echo Dossier: %CD%
echo Serveur: http://localhost:8000
echo.
echo Pages: partenariats-front, /login, /admin/partenariats, /admin/partenariats/offres
echo Gardez cette fenetre ouverte. Arreter: Ctrl+C
echo ========================================
echo.

"%PHP_EXE%" -S localhost:8000 -t public

pause
