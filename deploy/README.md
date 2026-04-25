# Deploy CarModel ERP — Istruzioni complete

**Stack server:** Ubuntu 22.04/24.04 · nginx · PHP 8.2-FPM · MariaDB 10.11 · Let's Encrypt

**Server:** DigitalOcean droplet `ubuntu-s-1vcpu-512mb-10gb-fra1-01` — IP `142.93.99.245`

**Repo:** https://github.com/mrfruitsErp/carmodel.git (branch `main`)

---

## Step 1 — DNS Register.it

Prima di tutto, punta il dominio al server. Su [register.it](https://www.register.it):

1. Login → **Gestione Domini** → seleziona il tuo dominio
2. **DNS Manager** (o "Gestione DNS")
3. Cancella eventuali record A esistenti e crea questi due:

| Tipo | Nome | Valore | TTL |
|------|------|--------|-----|
| A | `@` (o vuoto) | `142.93.99.245` | 3600 |
| A | `www` | `142.93.99.245` | 3600 |

4. Salva. La propagazione DNS richiede da **5 minuti a 4 ore**. Verifica con:
   ```
   nslookup alecar.it
   ```
   Quando ti risponde con `142.93.99.245` sei a posto.

---

## Step 2 — Setup iniziale del server (una volta sola)

Dal tuo PC Windows, apri PowerShell o Git Bash e collegati:

```bash
ssh root@142.93.99.245
```

Sul server (sei già in `/var/www/carmodel/`), copio prima i file di deploy dal tuo PC:

**Dal tuo PC (PowerShell, dalla cartella del progetto):**
```powershell
cd C:\xampp\htdocs\carmodel
scp -r deploy/ root@142.93.99.245:/root/
```

**Sul server:**
```bash
chmod +x /root/deploy/setup-server.sh
bash /root/deploy/setup-server.sh alecar.it
```

Lo script fa tutto: swap 2GB, PHP, MariaDB, nginx, firewall, php-fpm tuned per 512MB.

A fine esecuzione vedrai le credenziali del database — **copiale subito** (sono anche salvate in `/root/carmodel-credentials.txt`).

---

## Step 3 — Deploy key GitHub (per `git pull` senza password)

Sul server:

```bash
# 1. Genera la chiave SSH dedicata al deploy
ssh-keygen -t ed25519 -f /root/.ssh/github_deploy -N "" -C "deploy@carmodel"

# 2. Mostra la chiave pubblica (la copierai su GitHub)
cat /root/.ssh/github_deploy.pub
```

Vai su https://github.com/mrfruitsErp/carmodel/settings/keys/new e:
- **Title:** `Production server (DO Frankfurt)`
- **Key:** incolla la chiave pubblica appena mostrata
- ❌ **NON spuntare** "Allow write access" (deploy key in sola lettura per sicurezza)
- **Add key**

Configura SSH per usare quella chiave con GitHub:

```bash
cat >> /root/.ssh/config <<'EOF'
Host github.com
  HostName github.com
  User git
  IdentityFile /root/.ssh/github_deploy
  StrictHostKeyChecking no
EOF
chmod 600 /root/.ssh/config
```

Test:
```bash
ssh -T git@github.com
# Deve rispondere: "Hi mrfruitsErp/carmodel! You've successfully authenticated..."
```

---

## Step 4 — Clone del progetto

```bash
# Svuota la cartella se contiene roba di prove precedenti
rm -rf /var/www/carmodel/{,.[!.],..?}* 2>/dev/null || true

cd /var/www
git clone git@github.com:mrfruitsErp/carmodel.git carmodel
cd /var/www/carmodel
chown -R www-data:www-data /var/www/carmodel
```

---

## Step 5 — Configura `.env` produzione

```bash
cp /var/www/carmodel/deploy/.env.production.example /var/www/carmodel/.env
nano /var/www/carmodel/.env
```

Sostituisci almeno:
- `APP_URL=https://alecar.it`
- `DB_PASSWORD=...` (dalla password generata in `/root/carmodel-credentials.txt`)
- `SESSION_DOMAIN=.alecar.it`
- `SANCTUM_STATEFUL_DOMAINS=alecar.it,www.alecar.it`
- `MAIL_FROM_ADDRESS="noreply@alecar.it"`

Salva (`CTRL+O`, `INVIO`, `CTRL+X`).

---

## Step 6 — Primo deploy

```bash
chmod +x /var/www/carmodel/deploy/deploy.sh
bash /var/www/carmodel/deploy/deploy.sh
```

Lo script: pulla il codice, installa Composer, genera APP_KEY, fa migrate, ottimizza cache, ricarica php-fpm.

Test: visita `http://alecar.it` (ancora HTTP). Dovrebbe rispondere il sito.

---

## Step 7 — HTTPS con Let's Encrypt

Dopo che il DNS è propagato (`nslookup alecar.it` risponde con `142.93.99.245`):

```bash
certbot --nginx -d alecar.it -d www.alecar.it
```

Certbot ti chiederà:
- email per notifiche di scadenza
- accettare i termini
- se reindirizzare HTTP→HTTPS → **scegli 2 (Redirect)**

Fatto. Visita `https://alecar.it` e dovresti vedere il lucchetto verde. Il rinnovo del certificato è automatico ogni 60 giorni.

---

## Deploy successivi (workflow quotidiano)

Dal tuo PC:

```powershell
cd C:\xampp\htdocs\carmodel
git add -A
git commit -m "messaggio della modifica"
git push origin main
```

Poi sul server (basta un comando):

```bash
ssh root@142.93.99.245 "bash /var/www/carmodel/deploy/deploy.sh"
```

### Alias SSH (opzionale, comodissimo)

Sul tuo PC Windows in `C:\Users\Admin\.ssh\config` aggiungi:

```
Host carmodel-prod
  HostName 142.93.99.245
  User root
  Port 22
```

Da quel momento basta scrivere:

```powershell
ssh carmodel-prod
ssh carmodel-prod "bash /var/www/carmodel/deploy/deploy.sh"
```

---

## Rollback (se un deploy va male)

```bash
cd /var/www/carmodel
git log --oneline -10                 # trova l'hash del commit precedente
git reset --hard <HASH_PRECEDENTE>
bash deploy/deploy.sh
```

---

## Troubleshooting

| Problema | Soluzione |
|----------|-----------|
| `502 Bad Gateway` | `systemctl status php8.2-fpm` — se è down: `systemctl restart php8.2-fpm` |
| Out of memory durante composer | `composer install` con `COMPOSER_MEMORY_LIMIT=-1` |
| Permission denied su storage | `chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache` |
| MySQL connection refused | `systemctl status mariadb` |
| Sito mostra ancora pagina vecchia | `php artisan optimize:clear` poi `bash deploy/deploy.sh` |
| Logs Laravel | `tail -f /var/www/carmodel/storage/logs/laravel.log` |
| Logs nginx | `tail -f /var/log/nginx/error.log` |
| Swap usage | `free -h` (la swap deve esistere e venire usata) |

---

## Note importanti

- **Backup database**: aggiungi un cron giornaliero. Esempio:
  ```bash
  crontab -e
  # Aggiungi:
  0 3 * * * mysqldump -u carmodel -p'PASSWORD' carmodel | gzip > /root/backup-carmodel-$(date +\%Y\%m\%d).sql.gz && find /root/backup-carmodel-*.sql.gz -mtime +14 -delete
  ```

- **Spazio disco**: 10GB sono pochi. Monitora con `df -h`. Le foto dei veicoli vanno via in fretta. Considera un volume DigitalOcean aggiuntivo (€1/mese per 10GB).

- **Upgrade RAM**: se il sito cresce, considera il droplet 1GB ($6/mese). Con 512MB sei al limite, soprattutto durante migrazioni o composer install.
