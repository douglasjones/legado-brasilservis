#!/usr/bin/env bash

set -Eeuo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
CLIENT_DIR="$(cd "${ROOT_DIR}/.." && pwd)"
BACKUP_DIR="${CLIENT_DIR}/database"
LOG_DIR="${CLIENT_DIR}/logs"
CONTAINER_NAME="${CONTAINER_NAME:-brasil-mysql}"
DB_NAME="${DB_NAME:-gepros1com_brasilservis}"
DB_USER="${DB_USER:-root}"
DB_PASSWORD="${DB_PASSWORD:-SENHA}"
DB_CHARSET="${DB_CHARSET:-latin1}"
DB_COLLATION="${DB_COLLATION:-latin1_swedish_ci}"
DUMP_SQL="${DUMP_SQL:-${BACKUP_DIR}/dump.sql}"
DUMP_GZ="${DUMP_GZ:-${BACKUP_DIR}/dump.sql.gz}"
CONTAINER_TMP_PATH="${CONTAINER_TMP_PATH:-/tmp/brasilservis_dump.sql}"
PRECHECK_LOG="${LOG_DIR}/import-precheck.log"
FULL_LOG="${LOG_DIR}/import-full.log"
MONITOR_LOG="${LOG_DIR}/import-monitor.log"
MYSQL_HOST="${MYSQL_HOST:-127.0.0.1}"
MYSQL_PING_RETRIES="${MYSQL_PING_RETRIES:-120}"
MYSQL_PING_SLEEP="${MYSQL_PING_SLEEP:-5}"
MONITOR_INTERVAL="${MONITOR_INTERVAL:-5}"
MONITOR_ITERATIONS="${MONITOR_ITERATIONS:-0}"
VERIFY_SCRIPT="${ROOT_DIR}/scripts/verificar_dump.sh"

mkdir -p "${LOG_DIR}"

log() {
  printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*"
}

fail() {
  log "ERRO: $*" >&2
  exit 1
}

usage() {
  cat <<EOF
Uso:
  ./scripts/import_dump.sh [caminho_dump.sql|caminho_dump.sql.gz]
  ./scripts/import_dump.sh --monitor

Ambiente configuravel:
  CONTAINER_NAME=${CONTAINER_NAME}
  DB_NAME=${DB_NAME}
  DB_USER=${DB_USER}
  DB_PASSWORD=******
  DUMP_SQL=${DUMP_SQL}
  DUMP_GZ=${DUMP_GZ}
  CONTAINER_TMP_PATH=${CONTAINER_TMP_PATH}
EOF
}

run_monitor() {
  local iteration=1
  : > "${MONITOR_LOG}"

  while :; do
    {
      log "Monitoracao do import"
      docker compose ps
      docker exec "${CONTAINER_NAME}" mysql -u"${DB_USER}" -p"${DB_PASSWORD}" -Nse "SHOW FULL PROCESSLIST;"
      docker exec "${CONTAINER_NAME}" mysql -u"${DB_USER}" -p"${DB_PASSWORD}" -Nse \
        "SELECT COUNT(*) AS total_tabelas FROM information_schema.tables WHERE table_schema='${DB_NAME}';"
      docker exec "${CONTAINER_NAME}" mysql -u"${DB_USER}" -p"${DB_PASSWORD}" -Nse \
        "SHOW TABLES FROM \`${DB_NAME}\` LIKE 'usuarios';"
      printf '\n'
    } 2>&1 | tee -a "${MONITOR_LOG}"

    if (( MONITOR_ITERATIONS > 0 && iteration >= MONITOR_ITERATIONS )); then
      break
    fi

    iteration=$((iteration + 1))
    sleep "${MONITOR_INTERVAL}"
  done
}

pick_dump() {
  local candidate="${1:-}"

  if [[ -n "${candidate}" ]]; then
    [[ -f "${candidate}" ]] || fail "Dump informado nao encontrado: ${candidate}"
    printf '%s\n' "${candidate}"
    return
  fi

  if [[ -f "${DUMP_SQL}" ]]; then
    printf '%s\n' "${DUMP_SQL}"
    return
  fi

  if [[ -f "${DUMP_GZ}" ]]; then
    printf '%s\n' "${DUMP_GZ}"
    return
  fi

  fail "Nenhum dump .sql ou .sql.gz encontrado em ${BACKUP_DIR}"
}

ensure_sql_dump() {
  local source_dump="$1"

  case "${source_dump}" in
    *.sql)
      [[ -s "${source_dump}" ]] || fail "Arquivo SQL vazio: ${source_dump}"
      printf '%s\n' "${source_dump}"
      ;;
    *.sql.gz)
      log "Validando integridade gzip: ${source_dump}"
      gzip -t "${source_dump}" || fail "Falha na validacao gzip do dump: ${source_dump}"

      log "Descompactando dump para ${DUMP_SQL}"
      rm -f "${DUMP_SQL}"
      gunzip -c "${source_dump}" > "${DUMP_SQL}"
      [[ -s "${DUMP_SQL}" ]] || fail "Arquivo SQL gerado vazio: ${DUMP_SQL}"
      printf '%s\n' "${DUMP_SQL}"
      ;;
    *)
      fail "Formato nao suportado: ${source_dump}"
      ;;
  esac
}

wait_for_mysql() {
  local attempt=1

  log "Aguardando container ${CONTAINER_NAME} ficar pronto"
  docker compose ps 2>&1 | tee -a "${FULL_LOG}"

  while (( attempt <= MYSQL_PING_RETRIES )); do
    if docker exec "${CONTAINER_NAME}" mysqladmin --no-defaults ping \
      -h "${MYSQL_HOST}" \
      -u"${DB_USER}" \
      -p"${DB_PASSWORD}" \
      --silent >/dev/null 2>&1; then
      log "MySQL respondeu ao ping"
      return
    fi

    sleep "${MYSQL_PING_SLEEP}"
    attempt=$((attempt + 1))
  done

  fail "MySQL nao respondeu apos $((MYSQL_PING_RETRIES * MYSQL_PING_SLEEP)) segundos"
}

copy_dump_to_container() {
  local sql_dump="$1"

  log "Copiando dump para ${CONTAINER_NAME}:${CONTAINER_TMP_PATH}"
  docker cp "${sql_dump}" "${CONTAINER_NAME}:${CONTAINER_TMP_PATH}"
  docker exec "${CONTAINER_NAME}" ls -lh "${CONTAINER_TMP_PATH}" 2>&1 | tee -a "${FULL_LOG}"
}

run_import() {
  log "Iniciando importacao dentro do container com SOURCE em sessao unica"
  docker exec -i "${CONTAINER_NAME}" sh -lc "
    mysql \
      -u\"${DB_USER}\" \
      -p\"${DB_PASSWORD}\" \
      --default-character-set=\"${DB_CHARSET}\" <<'SQL'
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET ${DB_CHARSET} COLLATE ${DB_COLLATION};
USE \`${DB_NAME}\`;
SET SESSION sql_log_bin=0;
SET SESSION autocommit=0;
SET SESSION unique_checks=0;
SET SESSION foreign_key_checks=0;
SOURCE ${CONTAINER_TMP_PATH};
COMMIT;
SET SESSION foreign_key_checks=1;
SET SESSION unique_checks=1;
SET SESSION autocommit=1;
SQL
  " 2>&1 | tee -a "${FULL_LOG}"
}

post_validate() {
  {
    log "Pos-validacao do banco"
    docker exec "${CONTAINER_NAME}" mysql -u"${DB_USER}" -p"${DB_PASSWORD}" -Nse \
      "SHOW TABLES FROM \`${DB_NAME}\` LIKE 'usuarios';"
    docker exec "${CONTAINER_NAME}" mysql -u"${DB_USER}" -p"${DB_PASSWORD}" -Nse \
      "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_NAME}';"
  } 2>&1 | tee -a "${FULL_LOG}"
}

main() {
  local arg="${1:-}"
  local chosen_dump sql_dump

  if [[ "${arg}" == "--help" || "${arg}" == "-h" ]]; then
    usage
    exit 0
  fi

  if [[ "${arg}" == "--monitor" ]]; then
    run_monitor
    exit 0
  fi

  : > "${FULL_LOG}"
  chosen_dump="$(pick_dump "${arg}")"
  sql_dump="$(ensure_sql_dump "${chosen_dump}")"

  "${VERIFY_SCRIPT}" "${sql_dump}" 2>&1 | tee "${PRECHECK_LOG}"

  wait_for_mysql
  copy_dump_to_container "${sql_dump}"
  run_import
  post_validate
}

main "$@"
