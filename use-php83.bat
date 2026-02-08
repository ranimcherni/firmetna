@echo off
REM Double-click this file to open a terminal where "php" is PHP 8.3.
set "PATH=C:\Users\user\Desktop\php83;%PATH%"
echo PHP 8.3 is now first in PATH for this window.
php -v
echo.
echo You are in: %CD%
echo Run your commands here, e.g.:
echo   composer install
echo   php bin/console doctrine:migrations:migrate --no-interaction
echo   php -S localhost:8000 -t public
echo.
cmd /k
  cd C:\Users\user\Desktop\firmetna_partners
  php -S localhost:8000 -t public
  