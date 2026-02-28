@echo off
REM Opens a terminal where "php" points to PHP 8.3 for this project.
set "PATH=C:\Users\user\Desktop\php83;%PATH%"
cd /d "C:\Users\user\Desktop\firmetna_partners"

echo PHP 8.3 is now first in PATH for this window.
php -v
echo.
echo Project folder: %CD%
echo.
echo To start the server run:  php -S localhost:8000 -t public
echo Or double-click START_SERVER.bat instead.
echo.
cmd /k