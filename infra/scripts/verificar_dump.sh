#!/usr/bin/env bash

set -Eeuo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
CLIENT_DIR="$(cd "${ROOT_DIR}/.." && pwd)"
BACKUP_DIR="${CLIENT_DIR}/database"
DEFAULT_SQL="${BACKUP_DIR}/dump.sql"
DEFAULT_GZ="${BACKUP_DIR}/dump.sql.gz"

log() {
  printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*"
}

fail() {
  log "ERRO: $*" >&2
  exit 1
}

pick_dump() {
  local candidate="${1:-}"

  if [[ -n "${candidate}" ]]; then
    [[ -f "${candidate}" ]] || fail "Dump informado nao encontrado: ${candidate}"
    printf '%s\n' "${candidate}"
    return
  fi

  if [[ -f "${DEFAULT_SQL}" ]]; then
    printf '%s\n' "${DEFAULT_SQL}"
    return
  fi

  if [[ -f "${DEFAULT_GZ}" ]]; then
    printf '%s\n' "${DEFAULT_GZ}"
    return
  fi

  fail "Nenhum dump encontrado em ${BACKUP_DIR}"
}

ensure_gzip_valid() {
  local dump_path="$1"
  if [[ "${dump_path}" == *.gz ]]; then
    log "Validando integridade gzip"
    gzip -t "${dump_path}" || fail "Arquivo gzip invalido: ${dump_path}"
  fi
}

report_file_info() {
  local dump_path="$1"
  log "Arquivo analisado: ${dump_path}"
  ls -lh "${dump_path}"
  stat -f 'bytes=%z modified=%Sm' "${dump_path}"
}

search_pattern() {
  local label="$1"
  local pattern="$2"
  local dump_path="$3"

  log "${label}"
  if [[ "${dump_path}" == *.gz ]]; then
    if ! gzip -cd "${dump_path}" | grep -nE "${pattern}" | head; then
      true
    fi
  else
    if ! grep -nE "${pattern}" "${dump_path}" | head; then
      true
    fi
  fi
}

tail_dump() {
  local dump_path="$1"
  log "Ultimas 50 linhas"
  if [[ "${dump_path}" == *.gz ]]; then
    gzip -cd "${dump_path}" | tail -n 50
  else
    tail -n 50 "${dump_path}"
  fi
}

last_non_empty_line() {
  local dump_path="$1"
  if [[ "${dump_path}" == *.gz ]]; then
    gzip -cd "${dump_path}" | awk 'NF{line=$0} END{print line}'
  else
    awk 'NF{line=$0} END{print line}' "${dump_path}"
  fi
}

assert_usuarios_definition() {
  local dump_path="$1"

  if [[ "${dump_path}" == *.gz ]]; then
    if gzip -cd "${dump_path}" | grep -qE 'CREATE TABLE .*usuarios'; then
      log "CREATE TABLE usuarios: encontrado"
    else
      log "CREATE TABLE usuarios: nao encontrado"
      log "o dump nao contem a criacao da tabela usuarios; a ausencia dela no banco pode ser problema do dump e nao da importacao"
    fi

    if gzip -cd "${dump_path}" | grep -qE 'INSERT INTO .*usuarios'; then
      log "INSERT INTO usuarios: encontrado"
    else
      log "INSERT INTO usuarios: nao encontrado"
    fi
  else
    if grep -qE 'CREATE TABLE .*usuarios' "${dump_path}"; then
      log "CREATE TABLE usuarios: encontrado"
    else
      log "CREATE TABLE usuarios: nao encontrado"
      log "o dump nao contem a criacao da tabela usuarios; a ausencia dela no banco pode ser problema do dump e nao da importacao"
    fi

    if grep -qE 'INSERT INTO .*usuarios' "${dump_path}"; then
      log "INSERT INTO usuarios: encontrado"
    else
      log "INSERT INTO usuarios: nao encontrado"
    fi
  fi
}

assert_not_truncated() {
  local dump_path="$1"
  local last_line

  last_line="$(last_non_empty_line "${dump_path}")"
  log "Ultima linha nao vazia: ${last_line}"

  case "${last_line}" in
    "-- Dump completed on "*)
      log "Indicacao de finalizacao do dump encontrada"
      ;;
    *";")
      log "Final do arquivo termina com ponto e virgula"
      ;;
    *)
      log "Sinal de truncamento detectado: o arquivo termina abruptamente"
      fail "Interrompido por suspeita de dump truncado"
      ;;
  esac
}

main() {
  local dump_path

  dump_path="$(pick_dump "${1:-}")"
  [[ -s "${dump_path}" ]] || fail "Arquivo vazio: ${dump_path}"

  ensure_gzip_valid "${dump_path}"
  report_file_info "${dump_path}"
  search_pattern "Ocorrencias de CREATE TABLE usuarios" 'CREATE TABLE .*usuarios' "${dump_path}"
  search_pattern "Ocorrencias de INSERT INTO usuarios" 'INSERT INTO .*usuarios' "${dump_path}"
  search_pattern "Ocorrencias de CREATE DATABASE" '^CREATE DATABASE' "${dump_path}"
  search_pattern "Ocorrencias de USE" '^USE ' "${dump_path}"
  assert_usuarios_definition "${dump_path}"
  tail_dump "${dump_path}"
  assert_not_truncated "${dump_path}"
}

main "$@"
