#!/usr/bin/env bash
# =====================================================================
# CarModel ERP — Setup iniziale VPS (Ubuntu 22.04/24.04)
# Droplet DigitalOcean 1vCPU/512MB/10GB — Frankfurt
# Lanciare come ROOT, una volta sola.
# =====================================================================
set -euo pipefail

DOMAIN="${1:-}"           # primo argomento: dominio (es. ./setup-server.sh alecar.it)
APP_DIR="/var/www/carmodel"
DB_NAME="carmodel"
DB_USER="carmodel"
DB_PASS="$(openssl rand -base64 24 | tr -d '+/=' | cut -c1-20)"

if [ -z "$DOMAIN" ]; then
  echo "Uso: $0 <dominio>"
  echo "Esempio: $0 alecar.it"
  exit 1
fi

echo "═══════════════════════════════════════════════════════════════════"
echo " Setup VPS per CarModel ERP"
echo " Dominio:    $DOMAIN"
echo " App dir:    $APP_DIR"
echo " DB name:    $DB_NAME"
echo " DB user:    $DB_USER"
echo " DB pass:    $DB_PASS  ← SALVA QUESTA PASSWORD"
echo "═══════════════════════════════════════════════════════════════════"
read -p "Premi INVIO per continuare o CTRL+C per annullare..."

# ─── 1. Sistema base + swap 2GB (essenziale con 512MB RAM) ──────────
echo "▶ Aggiornamento sistema..."
export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get upgrade -y

if ! swapon --show | grep -q '/swapfile'; then
  echo "▶ Creazione swap 2GB..."
  fallocate -l 2G /swapfile
  chmod 600 /swapfile
  mkswap /swapfile
  swapon /swapfile
  echo '/swapfile none swap sw 0 0' >> /etc/fstab
  sysctl vm.swappiness=10
  echo 'vm.swappiness=10' > /etc/sysctl.d/99-swap.conf
fi

# ─── 2. Pacchetti necessari ─────────────────────────────────────────
echo "▶ Installazione pacchetti..."
apt-get install -y \
  software-properties-common ca-certificates lsb-release apt-transport-https \
  curl wget unzip git ufw fail2ban \
  nginx \
  mariadb-server \
  certbot python3-certbot-nginx

# PHP 8.2 da repo Ondrej (più recente)
add-apt-repository -y ppa:ondrej/php
apt-get update -y
apt-get install -y \
  php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml \
  php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl php8.2-imagick

# Composer
if ! command -v composer >/dev/null; then
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

# ─── 3. PHP-FPM tuning per 512MB ────────────────────────────────────
echo "▶ Tuning PHP-FPM per RAM bassa..."
POOL="/etc/php/8.2/fpm/pool.d/www.conf"
sed -i 's/^pm = .*/pm = ondemand/' $POOL
sed -i 's/^pm.max_children = .*/pm.max_children = 4/' $POOL
sed -i 's/^pm.start_servers = .*/pm.start_servers = 1/' $POOL
sed -i 's/^pm.min_spare_servers = .*/pm.min_spare_servers = 1/' $POOL
sed -i 's/^pm.max_spare_servers = .*/pm.max_spare_servers = 2/' $POOL
sed -i 's/^;pm.process_idle_timeout = .*/pm.process_idle_timeout = 30s/' $POOL
sed -i 's/^;pm.max_requests = .*/pm.max_requests = 200/' $POOL

# php.ini
PHP_INI="/etc/php/8.2/fpm/php.ini"
sed -i 's/^memory_limit = .*/memory_limit = 256M/' $PHP_INI
sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 32M/' $PHP_INI
sed -i 's/^post_max_size = .*/post_max_size = 40M/' $PHP_INI
sed -i 's/^max_execution_time = .*/max_execution_time = 120/' $PHP_INI
sed -i 's/^;opcache.enable=.*/opcache.enable=1/' $PHP_INI
sed -i 's/^;opcache.memory_consumption=.*/opcache.memory_consumption=64/' $PHP_INI
sed -i 's/^;opcache.max_accelerated_files=.*/opcache.max_accelerated_files=10000/' $PHP_INI

systemctl restart php8.2-fpm

# ─── 4. MariaDB: harden + crea DB + user ───────────────────────────
echo "▶ Configurazione MariaDB..."
systemctl enable --now mariadb

# Hardening minimo (root via socket, no anonymous, no remote root)
mysql -u root <<SQL
DELETE FROM mysql.user WHERE User='';
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost','127.0.0.1','::1');
DROP DATABASE IF EXISTS test;
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
SQL

# Tuning MariaDB per 512MB
cat > /etc/mysql/mariadb.conf.d/99-low-ram.cnf <<EOF
[mysqld]
innodb_buffer_pool_size = 64M
innodb_log_buffer_size  = 8M
key_buffer_size         = 8M
max_connections         = 30
query_cache_size        = 0
performance_schema      = OFF
EOF
systemctl restart mariadb

# ─── 5. Nginx: virtual host ────────────────────────────────────────
echo "▶ Configurazione nginx..."
cat > /etc/nginx/sites-available/carmodel <<EOF
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN www.$DOMAIN;
    root $APP_DIR/public;
    index index.php index.html;

    client_max_body_size 32M;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    gzip on;
    gzip_vary on;
    gzip_types text/plain text/css text/javascript application/javascript application/json image/svg+xml;
    gzip_min_length 1024;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 120;
    }

    location ~ /\.(?!well-known).* { deny all; }
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
}
EOF

ln -sf /etc/nginx/sites-available/carmodel /etc/nginx/sites-enabled/carmodel
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx

# ─── 6. Firewall ───────────────────────────────────────────────────
echo "▶ Configurazione firewall (UFW)..."
ufw --force reset
ufw default deny incoming
ufw default allow outgoing
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

# ─── 7. Cartelle progetto + permessi ───────────────────────────────
mkdir -p $APP_DIR
chown -R www-data:www-data $APP_DIR

# ─── 8. Salva credenziali ──────────────────────────────────────────
cat > /root/carmodel-credentials.txt <<EOF
═══════════════════════════════════════════════════════════════════
 CarModel ERP — Credenziali server
═══════════════════════════════════════════════════════════════════
Dominio:     $DOMAIN
App dir:     $APP_DIR
DB name:     $DB_NAME
DB user:     $DB_USER
DB pass:     $DB_PASS
DB host:     127.0.0.1
DB port:     3306

PROSSIMI PASSI:
  1. Configura DNS Register.it: record A @ → IP server, A www → IP server
  2. Genera deploy key SSH per GitHub:
       ssh-keygen -t ed25519 -f /root/.ssh/github_deploy -N ""
       cat /root/.ssh/github_deploy.pub
     Aggiungi la pubblica come Deploy Key (read-only) al repo
       https://github.com/mrfruitsErp/carmodel/settings/keys
     Aggiungi a ~/.ssh/config:
       Host github.com
         HostName github.com
         User git
         IdentityFile /root/.ssh/github_deploy
         StrictHostKeyChecking no
  3. Clone progetto:
       cd /var/www && git clone git@github.com:mrfruitsErp/carmodel.git
       chown -R www-data:www-data $APP_DIR
  4. Configura .env (copia da deploy/.env.production.example)
  5. Lancia: $APP_DIR/deploy/deploy.sh
  6. Una volta che il DNS è propagato:
       certbot --nginx -d $DOMAIN -d www.$DOMAIN
═══════════════════════════════════════════════════════════════════
EOF
chmod 600 /root/carmodel-credentials.txt

echo
echo "✅ Setup base completato."
echo "📄 Credenziali salvate in: /root/carmodel-credentials.txt"
echo
cat /root/carmodel-credentials.txt
