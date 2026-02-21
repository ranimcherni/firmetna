@echo off
chcp 65001 >nul
echo ========================================
echo   CREATION DU DOSSIER UPLOAD
echo ========================================
echo.

cd /d "%~dp0"

echo Creation du dossier public/uploads/publications...
if not exist "public\uploads\publications" (
    mkdir "public\uploads\publications" 2>nul
    if %errorlevel% equ 0 (
        echo ✓ Dossier cree avec succes
    ) else (
        echo ✗ Erreur lors de la creation du dossier
    )
) else (
    echo ✓ Dossier existe deja
)
echo.

echo Verification des permissions...
echo Le dossier doit etre accessible en ecriture pour PHP
echo.
pause
