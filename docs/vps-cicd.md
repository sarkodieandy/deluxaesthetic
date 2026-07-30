# VPS CI/CD deployment

Every push to `main` runs the test suite, builds the Vite assets, creates a
production release, uploads it over SSH, runs migrations, and atomically moves
`/var/www/deluxaesthetic/current` to the new release.

## 1. Provision the Ubuntu server

Run these commands as `root` in the VPS console:

```bash
apt update
apt upgrade -y
apt install -y nginx mysql-server supervisor certbot python3-certbot-nginx \
    curl unzip git \
    php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml \
    php8.3-curl php8.3-zip php8.3-bcmath php8.3-gd php8.3-intl

fallocate -l 2G /swapfile
chmod 600 /swapfile
mkswap /swapfile
swapon /swapfile
echo '/swapfile none swap sw 0 0' >> /etc/fstab

mkdir -p /var/www/deluxaesthetic/releases
mkdir -p /var/www/deluxaesthetic/shared/storage
chown -R deploy:www-data /var/www/deluxaesthetic
chmod -R 775 /var/www/deluxaesthetic/shared/storage
```

Create the production database and a database user with a strong unique
password:

```bash
mysql
```

```sql
CREATE DATABASE deluxaesthetic CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'deluxaesthetic'@'localhost' IDENTIFIED BY 'REPLACE_WITH_A_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON deluxaesthetic.* TO 'deluxaesthetic'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 2. Create the server-only environment file

From the project directory on the local computer:

```bash
scp .env.example deploy@198.54.112.18:/var/www/deluxaesthetic/shared/.env
ssh deploy@198.54.112.18
nano /var/www/deluxaesthetic/shared/.env
```

At minimum, set:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://deluxeaestheticclinic.com
APP_KEY=
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=deluxaesthetic
DB_USERNAME=deluxaesthetic
DB_PASSWORD=REPLACE_WITH_THE_DATABASE_PASSWORD

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=database
QUEUE_SCHEDULED_DRAIN=false

PAYMENT_MOCK=false
PAYSTACK_PUBLIC_KEY=REPLACE_WITH_PAYSTACK_PUBLIC_KEY
PAYSTACK_SECRET_KEY=REPLACE_WITH_PAYSTACK_SECRET_KEY
PAYSTACK_CALLBACK_URL=https://deluxeaestheticclinic.com/checkout/callback
```

Generate `APP_KEY` without placing the environment file in a release:

```bash
php -r 'echo "base64:".base64_encode(random_bytes(32)).PHP_EOL;'
```

Copy the printed value into `APP_KEY`.

## 3. Configure the deploy SSH key

On the local computer:

```bash
ssh-keygen -t ed25519 -C "github-actions-deluxaesthetic" -f ~/.ssh/deluxaesthetic_github_actions -N ""
ssh-copy-id -i ~/.ssh/deluxaesthetic_github_actions.pub deploy@198.54.112.18
ssh-keyscan -H 198.54.112.18
```

In the GitHub repository, create an environment named `production`. Add these
environment secrets:

- `VPS_HOST`: `198.54.112.18`
- `VPS_PORT`: `22`
- `VPS_USER`: `deploy`
- `VPS_SSH_KEY`: the complete contents of
  `~/.ssh/deluxaesthetic_github_actions`
- `VPS_KNOWN_HOSTS`: the verified output from `ssh-keyscan -H 198.54.112.18`

Add an environment variable named `PRODUCTION_URL`. Leave it blank for the
first deployment, then set it to `https://deluxeaestheticclinic.com` after SSL
is active.

## 4. Configure Nginx, Supervisor, and scheduler

From the project directory on the local computer:

```bash
scp deploy/nginx.conf deploy@198.54.112.18:/tmp/deluxaesthetic-nginx.conf
scp deploy/supervisor.conf deploy@198.54.112.18:/tmp/deluxaesthetic-supervisor.conf
```

Then use the VPS console as `root`:

```bash
cp /tmp/deluxaesthetic-nginx.conf /etc/nginx/sites-available/deluxaesthetic
ln -s /etc/nginx/sites-available/deluxaesthetic /etc/nginx/sites-enabled/deluxaesthetic
rm /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx

cp /tmp/deluxaesthetic-supervisor.conf /etc/supervisor/conf.d/deluxaesthetic-worker.conf
supervisorctl reread
supervisorctl update

crontab -u deploy -e
```

Add this cron entry:

```cron
* * * * * cd /var/www/deluxaesthetic/current && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

Once DNS points to the VPS and the first release is live:

```bash
certbot --nginx -d deluxeaestheticclinic.com -d www.deluxeaestheticclinic.com
```

Then set GitHub's `PRODUCTION_URL` environment variable and rerun the workflow
from the Actions page.

## 5. Deploy

Commit the workflow and deployment files, then push:

```bash
git add .github/workflows/deploy-production.yml deploy config/queue.php routes/console.php .env.example docs/vps-cicd.md
git commit -m "Add production CI/CD deployment"
git push origin main
```

The workflow keeps the five newest releases. If its health check fails, it
restores the previous `current` symlink automatically.

## Alternative: direct Git remote deployment

This option does not use GitHub Actions. A push directly to the VPS builds and
activates the release:

```bash
git push production main
```

On the VPS, run as `root` once. Install Node.js 22 before creating the remote:

```bash
curl -fsSL https://deb.nodesource.com/setup_22.x -o /tmp/nodesource_setup.sh
bash /tmp/nodesource_setup.sh
apt install -y nodejs git
mkdir -p /var/repo/deluxaesthetic.git
git init --bare /var/repo/deluxaesthetic.git
chown -R deploy:www-data /var/repo/deluxaesthetic.git
```

Verify that the installed Node version is 22 or newer:

```bash
node --version
npm --version
```

From the local project directory, install the deployment hook:

```bash
scp deploy/post-receive deploy@198.54.112.18:/tmp/deluxaesthetic-post-receive
ssh deploy@198.54.112.18 \
    'cp /tmp/deluxaesthetic-post-receive /var/repo/deluxaesthetic.git/hooks/post-receive && chmod 755 /var/repo/deluxaesthetic.git/hooks/post-receive'
```

Add the production remote locally:

```bash
git remote add production deploy@198.54.112.18:/var/repo/deluxaesthetic.git
git push production main
```

Future direct pushes to `production` automatically run Composer, build the
Vite assets, run database migrations, switch the `current` symlink, restart
the queue worker, and retain the five newest releases.
