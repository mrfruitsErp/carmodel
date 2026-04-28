@echo off
cd /d C:\xampp\htdocs\carmodel
git add -A
git commit -m "Fix public routes: remove Route::domain() constraint, fix redirect to /login on alecar.it"
git push origin main
echo DONE
pause
