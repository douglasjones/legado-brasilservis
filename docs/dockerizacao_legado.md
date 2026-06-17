# Dockerizacao do Legado Brasil Servis

Data: 2026-03-31

## Origem dos arquivos utilizados

- codigo legado: `source/tar/brasilservis_code.tar.gz`
- raiz oficial extraida: `source/tar/brasilservis/`
- dump do banco: `database/dump.sql.gz`
- referencia operacional adotada: `clients/america-servis/`

## Estrutura final adotada

- app executavel em `app/`
- fonte oficial preservada em `source/tar/brasilservis/`
- stack Docker em `infra/`
- dump do cliente em `database/`

## Stack definida

- PHP: 7.3
- MySQL: 5.7
- nginx: 1.24-alpine
- containers: `brasil-php`, `brasil-nginx`, `brasil-mysql`

## Portas usadas

- aplicacao HTTP: `8083`
- MySQL host -> container: `3307:3306`

## Ajuste tecnico minimo realizado

- arquivo alterado: `app/app/settings.php`
- motivo: permitir que o legado continue usando os mesmos dados de banco, mas com `DB_HOST`, `DB_USER`, `DB_PASSWORD` e `DB_NAME` configuraveis por ambiente no Docker
- fallback preservado: valores originais do legado permanecem como padrao quando as variaveis nao existem
- arquivo alterado: `infra/docker/mysql/conf.d/import.cnf`
- motivo: remover `max_allowed_packet` do escopo de cliente generico porque o entrypoint do MySQL 5.7 chama `mysqladmin`, que falhava na inicializacao do container com essa opcao

## Como subir o ambiente

```bash
cd clients/brasil-servis/infra
./scripts/up.sh
```

## Como derrubar o ambiente

```bash
cd clients/brasil-servis/infra
./scripts/down.sh
```

## Como validar logs

```bash
cd clients/brasil-servis/infra
./scripts/logs.sh
```

## Como importar o dump

```bash
cd clients/brasil-servis/infra
./scripts/import_dump.sh
```

## Root e pontos de execucao validados estruturalmente

- document root nginx: `/var/www/html/public`
- front controller: `app/public/index.php`
- bootstrap Slim: `app/app/settings.php`, `app/app/dependencies.php`, `app/app/routes.php`, `app/app/routes-api.php`
- php-fpm esperado: `php:7.3-fpm-buster`

## Validacoes executadas

- `docker compose config`: compose valido
- `docker compose up -d`: stack criada com sucesso
- `docker exec brasil-php php -v`: PHP `7.3.33`
- `curl http://127.0.0.1:8083/`: resposta `302 Location: /login`
- `docker exec brasil-mysql mysql -ugepros1com_brasilservis -pgepros15082008 -e "SELECT 1" gepros1com_brasilservis`: conexao validada
- `docker compose ps`: `brasil-mysql` em estado `healthy`

## Pendencias encontradas

- nao foi executada a importacao completa do dump de `3.6G`; a stack esta pronta e o script de importacao foi preparado em `infra/scripts/import_dump.sh`
- o dump permanece apenas em `.sql.gz`; o `.sql` sera materializado pelo script de importacao quando necessario
