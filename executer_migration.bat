@echo off
echo ========================================
echo   EXECUTION DE LA MIGRATION FORUM
echo ========================================
echo.

cd /d "%~dp0"

echo [1/3] Verification de l'etat des migrations...
php bin/console doctrine:migrations:status
echo.

echo [2/3] Execution de la migration...
php bin/console doctrine:migrations:migrate
echo.

echo [3/3] Vidage du cache...
php bin/console cache:clear
echo.

echo ========================================
echo   MIGRATION TERMINEE !
echo ========================================
echo.
echo Vous pouvez maintenant tester les fonctionnalites :
echo - Allez sur /forum
echo - Testez les likes
echo - Testez les reponses aux commentaires
echo - Verifiez les notifications
echo.
pause
