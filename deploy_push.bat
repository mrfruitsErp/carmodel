@echo off
cd /d C:\xampp\htdocs\carmodel
git add -A
git commit -m "Fix UniqueConstraintViolation in salva(), add azienda_sdi field, dynamic footer SDI"
git push origin main
echo DONE
pause
