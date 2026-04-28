@echo off
cd /d C:\xampp\htdocs\carmodel
git config core.autocrlf false
git add -A --force
git status
git diff --cached --stat
git commit -m "Add tab Legale in settings, pagine legali dinamiche, fix Setting::get tenant pubblico"
git push origin main
echo.
echo === FATTO ===
pause
