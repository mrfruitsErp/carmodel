@echo off
cd /d C:\xampp\htdocs\carmodel
git add -A
git commit -m "Add sezione Legale in settings sito_web, pagine legali dinamiche da DB"
git push origin main
echo.
echo === PUSH COMPLETATO ===
echo Sul server:
echo   cd /var/www/carmodel ^&^& git pull origin main ^&^& php artisan cache:clear ^&^& php artisan view:clear
echo.
pause
