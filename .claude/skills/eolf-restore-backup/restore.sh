#!/usr/bin/env bash
#
# eolf-restore-backup — pull a production EOLF DB backup from the VPS and
# restore it into the local shared_mysql `eolf` database.
#
# SAFETY CONTRACT:
#   * The VPS is touched READ-ONLY (ssh `ls`, scp download). Nothing on the
#     server is ever modified or deleted.
#   * Before any destructive import, the current local DB is dumped to a
#     timestamped snapshot in the download dir. The import aborts if that
#     snapshot looks invalid (< 1 MB), so a restore is always reversible.
#
# Usage:
#   restore.sh list                      # list backups available on the VPS
#   restore.sh download [SELECTOR]       # copy a backup to the local download dir only
#   restore.sh restore  [SELECTOR]       # download + snapshot local + import + verify
#
#   SELECTOR (default: latest):
#     latest          newest backup on the VPS
#     yesterday       backup dated yesterday
#     today           backup dated today
#     YYYY-MM-DD       backup for a specific date (newest of that day)
#
# Override any of these via environment variables:
#   EOLF_VPS_SSH              ssh host/alias            (default: other)
#   EOLF_REMOTE_BACKUP_DIR    remote backups dir        (default: project storage/app/backups)
#   EOLF_DOWNLOAD_DIR         local dir for files       (default: ~/Downloads)
#   EOLF_MYSQL_CONTAINER      docker mysql container    (default: shared_mysql)
#   EOLF_MYSQL_USER           mysql user                (default: DB_USERNAME from project .env)
#   EOLF_MYSQL_PASS           mysql password            (default: DB_PASSWORD from project .env)
#   EOLF_DB                   target database name      (default: from project .env, else eolf)
#
# Credentials are NEVER hardcoded here (this repo is public). They resolve from the
# environment or the gitignored project .env; the script aborts if neither supplies them.
#
set -euo pipefail

# ---- config -----------------------------------------------------------------
VPS_SSH="${EOLF_VPS_SSH:-other}"
REMOTE_DIR="${EOLF_REMOTE_BACKUP_DIR:-/var/www/eolffoodtradingopc2.logiainnov.com/storage/app/backups}"
DL_DIR="${EOLF_DOWNLOAD_DIR:-$HOME/Downloads}"
CONTAINER="${EOLF_MYSQL_CONTAINER:-shared_mysql}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../../.." && pwd)"

# Read a KEY=value out of the gitignored project .env (empty if absent).
env_get() {
  [ -f "$PROJECT_ROOT/.env" ] || return 0
  grep -E "^$1=" "$PROJECT_ROOT/.env" | head -1 | cut -d= -f2- | tr -d '"'"'"' ' || true
}

# Credentials: env override > project .env. Never a literal default — see header.
DB_USER="${EOLF_MYSQL_USER:-$(env_get DB_USERNAME)}"
DB_PASS="${EOLF_MYSQL_PASS:-$(env_get DB_PASSWORD)}"

# DB name: explicit override > project .env > eolf
DB_NAME="${EOLF_DB:-$(env_get DB_DATABASE)}"
DB_NAME="${DB_NAME:-eolf}"

SSH_OPTS=(-o ConnectTimeout=20 -o BatchMode=yes)

# ---- helpers ----------------------------------------------------------------
die()  { echo "ERROR: $*" >&2; exit 1; }
info() { echo ">> $*"; }

ssh_ro() { ssh "${SSH_OPTS[@]}" "$VPS_SSH" "$@"; }

# strip the well-known "Using a password on the command line" noise
mysql_admin()    { docker exec -i  -e MYSQL_PWD="$DB_PASS" "$CONTAINER" mysql    -u"$DB_USER" "$@"; }
mysqldump_admin(){ docker exec     -e MYSQL_PWD="$DB_PASS" "$CONTAINER" mysqldump -u"$DB_USER" "$@"; }

require_container() {
  docker ps --filter "name=^${CONTAINER}$" --format '{{.Names}}' | grep -q "^${CONTAINER}$" \
    || die "container '$CONTAINER' is not running (start the shared MySQL stack first)"
}

require_creds() {
  [ -n "$DB_USER" ] && [ -n "$DB_PASS" ] || die \
"no MySQL credentials. This script keeps none (public repo). Supply them via either:
  * the project .env (DB_USERNAME / DB_PASSWORD), or
  * EOLF_MYSQL_USER=... EOLF_MYSQL_PASS=... $0 $*"
}

# Resolve a SELECTOR to a remote basename (e.g. backup-2026-06-19-01-00-02.sql)
resolve_remote() {
  local sel="${1:-latest}" pat
  case "$sel" in
    latest|"")  pat="backup-*.sql" ;;
    yesterday)  pat="backup-$(date -d 'yesterday' +%Y-%m-%d)-*.sql" ;;
    today)      pat="backup-$(date +%Y-%m-%d)-*.sql" ;;
    [0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]) pat="backup-$sel-*.sql" ;;
    *) die "invalid selector '$sel' (use: latest | yesterday | today | YYYY-MM-DD)" ;;
  esac
  local name
  name="$(ssh_ro "ls -1 $REMOTE_DIR/$pat 2>/dev/null | sort | tail -1 | xargs -r basename")" \
    || die "could not reach VPS '$VPS_SSH' (check your SSH config / key)"
  [ -n "$name" ] || die "no backup matching '$sel' found in $REMOTE_DIR"
  printf '%s\n' "$name"
}

# ---- subcommands ------------------------------------------------------------
cmd_list() {
  info "backups on $VPS_SSH:$REMOTE_DIR (newest first)"
  ssh_ro "ls -laht --time-style=long-iso $REMOTE_DIR/backup-*.sql 2>/dev/null | head -20" \
    || die "could not reach VPS '$VPS_SSH'"
}

cmd_download() {
  local name; name="$(resolve_remote "${1:-latest}")"
  mkdir -p "$DL_DIR"
  info "downloading $name -> $DL_DIR/ (read-only copy from VPS)"
  scp "${SSH_OPTS[@]}" "$VPS_SSH:$REMOTE_DIR/$name" "$DL_DIR/" >/dev/null
  ls -lh --time-style=long-iso "$DL_DIR/$name"
  printf '%s\n' "$DL_DIR/$name"   # last line = local path, for cmd_restore
}

cmd_restore() {
  require_container
  require_creds
  local name local_path snap ts
  name="$(resolve_remote "${1:-latest}")"
  local_path="$DL_DIR/$name"

  mkdir -p "$DL_DIR"
  info "[1/5] downloading $name (read-only from VPS)"
  scp "${SSH_OPTS[@]}" "$VPS_SSH:$REMOTE_DIR/$name" "$DL_DIR/" >/dev/null
  ls -lh --time-style=long-iso "$local_path"

  ts="$(date +%Y%m%d_%H%M%S)"
  snap="$DL_DIR/${DB_NAME}_local_pre-restore_${ts}.sql"
  info "[2/5] snapshotting current local '$DB_NAME' -> $snap"
  mysqldump_admin --single-transaction --quick --no-tablespaces "$DB_NAME" > "$snap" \
    2> >(grep -v "Using a password" >&2 || true)

  local sz; sz="$(stat -c%s "$snap")"
  info "[3/5] snapshot is $sz bytes; verifying before destructive import"
  [ "$sz" -gt 1000000 ] || die "snapshot < 1 MB, refusing to import (current data may be lost). Snapshot: $snap"

  info "[4/5] importing $name into local '$DB_NAME'"
  mysql_admin "$DB_NAME" < "$local_path" 2> >(grep -v "Using a password" >&2 || true)

  info "[5/5] verifying restored data"
  mysql_admin -N -e "SELECT CONCAT('tables=',COUNT(*)) FROM information_schema.tables WHERE table_schema='$DB_NAME';" \
    2> >(grep -v "Using a password" >&2 || true)
  mysql_admin "$DB_NAME" -N -e "
    SELECT CONCAT('inbounds=',  COUNT(*)) FROM inbounds;
    SELECT CONCAT('customers=', COUNT(*)) FROM customers;
    SELECT CONCAT('latest_inbound_created=', IFNULL(MAX(created_at),'n/a')) FROM inbounds;
  " 2> >(grep -v "Using a password" >&2 || true) || true

  echo
  info "DONE. Restored '$name' into local '$DB_NAME'."
  info "Revert with: docker exec -i -e MYSQL_PWD=\"\$DB_PASSWORD\" $CONTAINER mysql -u$DB_USER $DB_NAME < $snap"
}

# ---- dispatch ---------------------------------------------------------------
case "${1:-restore}" in
  list)              cmd_list ;;
  download)          cmd_download "${2:-latest}" ;;
  restore)           cmd_restore  "${2:-latest}" ;;
  -h|--help|help)    sed -n '2,36p' "${BASH_SOURCE[0]}" ;;
  *) die "unknown command '$1' (use: list | download | restore | help)" ;;
esac
