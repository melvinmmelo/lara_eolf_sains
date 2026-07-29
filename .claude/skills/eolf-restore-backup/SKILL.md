---
name: eolf-restore-backup
description: Restore a production EOLF database backup into the local shared_mysql `eolf` DB. Use when the user asks to "restore a backup", "pull yesterday's/latest backup", "refresh local DB from prod", or similar for the EOLF project. Copies the dump read-only from the VPS, snapshots the current local DB first, then imports.
---

# EOLF — Restore Production Backup to Local

Pulls a production database dump from the EOLF VPS and restores it into the local
`shared_mysql` → `eolf` database. The production cron writes one dump per day named
`backup-YYYY-MM-DD-HH-MM-SS.sql` into the project's `storage/app/backups/` folder.

## Safety contract (do not break)

- **The VPS is READ-ONLY.** Only `ssh ls` and `scp` (download) are ever run against it.
  Never modify, move, or delete anything on the server.
- **Always snapshot local first.** `restore` dumps the current local DB to a timestamped
  file in the download dir before importing, and aborts if that snapshot is < 1 MB. This
  makes every restore reversible.
- **Confirm before importing.** The import overwrites the local `eolf` DB. Show the user the
  resolved backup file (name + date) and get a yes before running `restore`.

## How to use

The helper script lives next to this file: `restore.sh`.

1. **See what's available** (read-only):
   ```bash
   .claude/skills/eolf-restore-backup/restore.sh list
   ```

2. **Confirm** with the user which backup they want. Default is `latest`; they may say
   "yesterday", "today", or a specific date.

3. **Restore** (download → snapshot local → import → verify):
   ```bash
   .claude/skills/eolf-restore-backup/restore.sh restore latest
   #                                                       ^ latest | yesterday | today | YYYY-MM-DD
   ```
   To only copy the file down without importing, use `download` instead of `restore`.

4. **Report** the verification counts the script prints (tables, inbounds, customers,
   latest order date) and tell the user where the safety snapshot was saved, plus the
   one-line revert command the script echoes.

## After a restore

If the user is mid-feature with pending migrations not yet on prod (e.g. a new column),
the restored prod DB won't have them — offer to re-apply:
```bash
docker exec eolf_app php artisan migrate
```

## Configuration (defaults match this machine)

All overridable via env vars; see the header of `restore.sh`. Defaults:

| Var | Default | Notes |
|-----|---------|-------|
| `EOLF_VPS_SSH` | `other` | SSH alias for the EOLF VPS, key-based auth (host details in your local `~/.ssh/config`) |
| `EOLF_REMOTE_BACKUP_DIR` | `/var/www/eolffoodtradingopc2.logiainnov.com/storage/app/backups` | prod dump folder |
| `EOLF_DOWNLOAD_DIR` | `~/Downloads` | where dumps + snapshots are written |
| `EOLF_MYSQL_CONTAINER` | `shared_mysql` | shared local MySQL stack container |
| `EOLF_MYSQL_USER` / `EOLF_MYSQL_PASS` | from project `.env` (`DB_USERNAME` / `DB_PASSWORD`) | **no hardcoded default** — this repo is public; the script aborts if neither env nor `.env` supplies them |
| `EOLF_DB` | from project `.env` (`DB_DATABASE`), else `eolf` | target DB |

## Notes

- EOLF is **not** on the `iqao` Hostinger VPS — it lives on the `other` VPS. Don't connect
  to `iqao` for this.
- The production dumps are plain `mysqldump` files (per-table `DROP TABLE IF EXISTS` +
  recreate, no `CREATE/DROP DATABASE`), so they import straight into the existing `eolf`
  schema.
