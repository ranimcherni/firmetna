@echo off
chcp 65001 >nul
echo ========================================
echo   TEST DE CONNEXION MYSQL
echo ========================================
echo.

echo Test avec le port 3306 (defaut MySQL)...
php -r "try { new PDO('mysql:host=127.0.0.1:3306', 'root', ''); echo '✓ MySQL accessible sur le port 3306\n'; } catch(Exception $e) { echo '✗ Port 3306: ' . $e->getMessage() . '\n'; }"
echo.

echo Test avec le port 3307 (votre config)...
php -r "try { new PDO('mysql:host=127.0.0.1:3307', 'root', ''); echo '✓ MySQL accessible sur le port 3307\n'; } catch(Exception $e) { echo '✗ Port 3307: ' . $e->getMessage() . '\n'; }"
echo.

echo ========================================
echo   VERIFICATION TERMINEE
echo ========================================
echo.
echo Si les deux tests echouent :
echo 1. Demarrez MySQL (XAMPP/WAMP)
echo 2. Verifiez le port dans votre fichier .env
echo.
pause
