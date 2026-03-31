# Project Description: EOLF Trading Sales and Inventory System

## Updates
20-May-2025 Updates
1. Added stock reconciliation feature
2. Added inventory bad order feature
3. Update monthly sales graph in dashboard
   - Sales is based on completed and paid orders
4. Added validation of duplicated order
5. Added summary in orders index
6. Added totals column in order index

## Docker Local Development

This project can be run fully inside Docker so local development does not depend on the host PHP version.
The provided setup uses:

- **app**: PHP 8.3 FPM + Composer + Node.js
- **web**: Nginx
- **db**: MariaDB 10.6

MariaDB was chosen because the available SQL dump files were generated from MariaDB and should import with less friction.
Laravel still uses the normal `mysql` driver in `.env.docker`.

### First-time setup

```bash
cp .env.docker .env
docker compose build
docker compose up -d
```

Install PHP and JS dependencies inside the container:

```bash
docker compose exec app composer install
docker compose exec app npm install
```

Generate the Laravel app key and create the storage symlink:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan storage:link
```

### Database setup

The repository does **not** contain the full schema as migrations, so bootstrap the database from one of the SQL dumps in `database/`.
Recommended default:

- `database/eolf_new.sql`

Manual import command:

```bash
docker compose exec -T db mariadb -ueolf -psecret eolf_new < database/eolf_new.sql
```

If you need the larger backup instead, import `database/backup-2025-05-13-09-00-03.sql` the same way.

### Run the app

Open:

- <http://localhost:8080>

Useful commands:

```bash
# Start containers
docker compose up -d

# Stop containers
docker compose down

# Rebuild app image after Dockerfile changes
docker compose build app

# Laravel commands
docker compose exec app php artisan migrate
docker compose exec app php artisan test

# Frontend
docker compose exec app npm run dev -- --host 0.0.0.0
docker compose exec app npm run build
```

### Notes

- Composer should be run inside the `app` container.
- `vendor/` and `node_modules/` are stored in Docker volumes so the host runtime does not interfere.
- App container uses PHP 8.3 to avoid the current host PHP 8.4 compatibility issue.
- If an existing `.env` has production-like values, review it before reusing it locally.
