# Estrutura Padrao do Cliente

Data: 2026-03-31

## Estrutura final

```text
clients/brasil-servis/
├── app/
├── source/
│   ├── tar/
│   └── referencias/
├── infra/
│   ├── docker/
│   ├── docker-compose.yml
│   └── scripts/
├── database/
│   ├── dump.sql.gz
│   └── import/
├── docs/
└── logs/
```

## Funcao de cada pasta

- `app/`: aplicacao ativa usada pelos containers Docker
- `source/tar/`: fonte oficial do legado vinda do arquivo `brasilservis_code.tar.gz`
- `source/referencias/`: reservado para materiais auxiliares futuros, preservando o padrao do projeto
- `infra/`: operacao Docker do cliente, incluindo compose, Dockerfile, nginx e scripts locais
- `database/`: dump do banco e pasta auxiliar de importacao
- `docs/`: documentacao operacional do cliente
- `logs/`: logs operacionais gerados por importacao e validacoes locais

## Fonte oficial e app ativa

Fonte oficial do cliente:

- `source/tar/brasilservis/`
- `source/tar/brasilservis_code.tar.gz`

App ativa em Docker:

- `app/`

## Rastreabilidade da reorganizacao

Movimentos executados:

- `./brasilservis_code.tar.gz` -> `source/tar/brasilservis_code.tar.gz`
- `./facilitiesgepros1com_brasilservis.sql.gz` -> `database/dump.sql.gz`
- `source/tar/brasilservis_code.tar.gz` extraido em `source/tar/brasilservis/`
- `source/tar/brasilservis/` copiado para `app/`

Nenhum arquivo do legado foi apagado. Os artefatos brutos foram realocados para a estrutura padrao do cliente.
