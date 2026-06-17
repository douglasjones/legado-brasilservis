# Repositorio Git do BRASIL-SERVIS

## Objetivo

Isolar o cliente `brasil-servis` em um repositorio Git proprio, separado do monorepo `infra-legado-docker`.

## Repositorio remoto

```text
https://github.com/douglasjones/legado-brasilservis.git
```

## Branch principal

```text
main
```

## Estrutura versionada

Entram no Git:

```text
app/
docs/
infra/
.gitignore
VERSION
```

Nao entram no Git:

```text
database/
source/
logs/
app/vendor/
app/node_modules/
app/storage/
app/uploads/
app/app/src/docs/
app/.env
infra/dumps/
*.sql
*.sql.gz
*.tar
*.tar.gz
*.zip
```

## Fluxo local correto

Trabalhar sempre dentro de:

```bash
cd clients/brasil-servis
```

Comandos principais:

```bash
git status
git add -A
git commit -m "fix: descricao"
git push origin main
```

## VERSION

O arquivo `VERSION` deve ser atualizado a cada entrega relevante com:

```text
<commit>
<data hora>
<mensagem resumida>
```

## Procedimento recomendado na VPS

Na VPS, o Git do cliente deve existir somente no diretorio do `brasil-servis`:

```bash
cd /opt/gpros/infra-legado-docker/clients/brasil-servis
git init -b main
git remote add origin https://github.com/douglasjones/legado-brasilservis.git
git fetch origin
git checkout -B main origin/main
```

Depois disso, o deploy do cliente deve considerar apenas esse repositorio.

## Regra operacional

```text
BRASIL-SERVIS publica BRASIL-SERVIS
BRASIL-SERVIS reverte BRASIL-SERVIS
BRASIL-SERVIS audita BRASIL-SERVIS
```
