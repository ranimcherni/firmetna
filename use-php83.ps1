# Run this script at the start of your PowerShell session so "php" uses PHP 8.3.
# Usage: . .\use-php83.ps1   (note the dot and space at the start)
# Then run: php -v   (should show 8.3)

$php83 = "C:\Users\user\Desktop\php83"
if (Test-Path "$php83\php.exe") {
    $env:Path = "$php83;$env:Path"
    Write-Host "PHP 8.3 is now first in PATH for this terminal." -ForegroundColor Green
    & "$php83\php.exe" -v
} else {
    Write-Host "PHP 8.3 not found at $php83" -ForegroundColor Red
}
