# AGENTE IA - PROCEDIMENTO OBRIGATÓRIO DE INÍCIO DE SESSÃO

## Objetivo

Antes de executar qualquer tarefa em qualquer cliente do projeto Migração CRM Legado, o agente deve obrigatoriamente levantar o ambiente real da VPS e validar as informações recebidas.

É proibido assumir caminhos, bancos, containers, volumes, portas ou estruturas apenas com base em documentação local.

Toda informação deve ser confirmada diretamente na VPS.

---

# Regras Obrigatórias

1. Nunca assumir caminhos de produção.
2. Nunca assumir nomes de containers.
3. Nunca assumir banco de dados.
4. Nunca executar SQL sem validação prévia.
5. Nunca sobrescrever arquivos sem confirmar destino.
6. Nunca executar ações em outros clientes.
7. Nunca executar comandos destrutivos sem autorização explícita.
8. Toda validação deve ocorrer antes da implantação.
9. Trabalhar sempre uma tarefa por vez.
10. Aguardar validação do gerente antes de avançar.

---

# Levantamento Inicial Obrigatório

## 1. Identificar containers do cliente

Executar:

```bash
docker ps --format "table {{.Names}}\t{{.Image}}\t{{.Status}}\t{{.Ports}}"
```

Registrar:

* container php
* container nginx
* container mysql

---

## 2. Identificar volume real da aplicação

Substituir o container conforme o cliente.

Exemplo:

```bash
docker inspect brasil-php --format '{{json .Mounts}}'
```

Registrar:

* Source
* Destination
* RW
* Type

Resultado esperado:

Exemplo:

```json
{
  "Source": "/opt/gpros/infra-legado-docker/clients/brasil-servis/app",
  "Destination": "/var/www/html"
}
```

O Source é o caminho oficial da produção.

Somente este caminho poderá ser utilizado nas próximas tarefas.

---

## 3. Identificar banco utilizado

Executar:

```bash
docker exec brasil-mysql env | grep MYSQL
```

Registrar:

* MYSQL_DATABASE
* MYSQL_USER
* MYSQL_PASSWORD

Nunca utilizar credenciais assumidas.

---

## 4. Confirmar existência da tabela antes de qualquer SQL

Exemplo:

```bash
docker exec brasil-mysql mysql -uUSUARIO -pSENHA BANCO -e "SHOW TABLES LIKE 'nome_tabela';"
```

---

## 5. Confirmar estrutura antes de executar ALTER TABLE

Exemplo:

```bash
docker exec brasil-mysql mysql -uUSUARIO -pSENHA BANCO -e "DESCRIBE nome_tabela;"
```

---

## 6. Confirmar existência física dos arquivos

Exemplo:

```bash
ls -lah /CAMINHO_REAL/arquivo.ext
```

Nunca publicar arquivos sem confirmar que o destino existe.

---

# Contexto Atual Conhecido - Brasil Servis

Data de validação: 2026-05-29

Cliente:

```text
brasil-servis
```

Containers:

```text
brasil-nginx
brasil-php
brasil-mysql
```

Path real validado:

```text
/opt/gpros/infra-legado-docker/clients/brasil-servis/app
```

Container PHP:

```text
brasil-php
```

Container MySQL:

```text
brasil-mysql
```

Banco:

```text
gepros1com_brasilservis
```

Usuário:

```text
gepros1com_brasilservis
```

Porta MySQL Host:

```text
3307
```

Porta HTTP:

```text
8083
```

Servidor:

```text
srv1584295
```

IP:

```text
187.127.23.38
```

---

# Processo Obrigatório Antes de Qualquer Publicação

1. Validar containers
2. Validar volume
3. Validar banco
4. Validar tabela
5. Validar estrutura
6. Validar arquivos
7. Fazer backup
8. Aplicar alteração
9. Validar resultado
10. Emitir relatório completo

Nenhuma etapa pode ser pulada.

Qualquer inconsistência encontrada deve interromper imediatamente a execução e gerar uma nova tarefa de validação.
