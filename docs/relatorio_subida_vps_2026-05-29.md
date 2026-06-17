# Relatorio de Subida para VPS - Brasil Servis

Data: 2026-05-29

Cliente: `brasil-servis`

Objetivo deste arquivo:
- consolidar os ultimos desenvolvimentos que precisam subir para a VPS
- reunir em um unico lugar os modulos `Escala`, `Reloginho` e `Folha de Ponto impressao`
- informar caminhos locais e destinos esperados na VPS sem ambiguidade

## 1. Projeto local

Caminho raiz do cliente no workspace local:

`/Volumes/MACWORK/gpros-workspace/03-clients/active/facilities/migracao/infra-legado-docker/clients/brasil-servis/`

App local que corresponde ao legado ativo:

`/Volumes/MACWORK/gpros-workspace/03-clients/active/facilities/migracao/infra-legado-docker/clients/brasil-servis/app/`

## 2. Caminho de destino na VPS

O caminho de producao abaixo foi inferido a partir dos registros de erro presentes no proprio projeto, em:

`app/public/error_log`

Caminho base esperado na VPS:

`/home/gepros1com/public_html/crm/facilities/brasilservis/`

Mapeamento esperado:

- local `.../clients/brasil-servis/app/app/...` -> VPS `/home/gepros1com/public_html/crm/facilities/brasilservis/app/...`
- local `.../clients/brasil-servis/app/public/...` -> VPS `/home/gepros1com/public_html/crm/facilities/brasilservis/public/...`

## 3. Modulos que devem subir

### 3.1. Escala

Origem da documentacao:
- `docs/historico_ajustes_integracao_api.md`
- secao `15. Correcao na edicao de escalas com vinculo legado inconsistente`

Resumo funcional:
- corrige a edicao de escala quando o vinculo legado entre `contratos_itens_pk` e `produtos_servicos_pk` esta inconsistente
- preserva servico e colaborador salvos no registro mesmo quando os combos derivados do contrato nao trazem esses valores
- ao salvar, mantem o `contratos_itens_pk` original se nao houver item compativel no contrato

Arquivos locais que devem subir:

1. `app/app/src/models/AgendaColaboradorPadrao.php`
2. `app/public/assets/js/local/agenda_escala_cad_form.js`
3. `app/app/templates/escala/agenda_escala_cad_form.twig`

Destino na VPS:

1. `/home/gepros1com/public_html/crm/facilities/brasilservis/app/src/models/AgendaColaboradorPadrao.php`
2. `/home/gepros1com/public_html/crm/facilities/brasilservis/public/assets/js/local/agenda_escala_cad_form.js`
3. `/home/gepros1com/public_html/crm/facilities/brasilservis/app/templates/escala/agenda_escala_cad_form.twig`

Observacao importante:
- o proprio historico registra alteracao de cache bust do JS de `v=14` para `v=15`
- por isso o `.twig` precisa subir junto com o `.js`

### 3.2. Reloginho

Origem da documentacao:
- `docs/historico_ajustes_integracao_api.md`
- secao `11. Reloginho abrindo com datas no local e vazio na producao`
- secao `16. Correcao de parsererror no reloginho`
- secao `17. Correcao no carregamento de batida isolada no reloginho`

Resumo funcional:
- preenche corretamente `Data Inicio` e `Data Fim` no modal do reloginho em producao
- evita `parsererror` quando ainda nao existe folha de ponto no periodo
- permite mostrar batida isolada, por exemplo so saida no dia, sem descartar o registro
- melhora a selecao entre consulta normal e noturna

Arquivos locais que devem subir:

1. `app/app/templates/colaborador/colaborador_res_form.twig`
2. `app/app/templates/conta/colaborador/colaborador_res_form.twig`
3. `app/app/src/models/PontoFolha.php`

Destino na VPS:

1. `/home/gepros1com/public_html/crm/facilities/brasilservis/app/templates/colaborador/colaborador_res_form.twig`
2. `/home/gepros1com/public_html/crm/facilities/brasilservis/app/templates/conta/colaborador/colaborador_res_form.twig`
3. `/home/gepros1com/public_html/crm/facilities/brasilservis/app/src/models/PontoFolha.php`

Observacao importante:
- o historico registra alteracao de cache bust do JS de `v=14` para `v=15` nesses templates
- por isso os templates precisam subir junto para forcar o navegador a buscar a versao nova do asset

### 3.3. Folha de Ponto impressao

Status da documentacao:
- este ajuste nao estava descrito explicitamente no `historico_ajustes_integracao_api.md`
- ele foi confirmado diretamente no codigo do cliente

Resumo funcional:
- na impressao da folha de ponto foi retirada a linha de `TOTAL DE HORAS`
- na implementacao atual, a folha impressa principal nao monta mais esse bloco de totais no HTML gerado por JavaScript

Arquivo local que deve subir:

1. `app/public/assets/js/local/ponto_folha_print_form.js`

Destino na VPS:

1. `/home/gepros1com/public_html/crm/facilities/brasilservis/public/assets/js/local/ponto_folha_print_form.js`

Evidencia tecnica no codigo:
- o arquivo `ponto_folha_print_form.js` nao contem mais o bloco `TOTAL DE HORAS`
- em contraste, os arquivos abaixo ainda contem esse bloco:
  - `app/public/assets/js/local/ponto_folha_print_fechamento_form.js`
  - `app/public/assets/js/local/ponto_folha_print_periodo_colaborador_form.js`

Leitura pratica:
- se o ajuste desejado e somente remover a linha de total da impressao principal da folha, o arquivo relevante e `ponto_folha_print_form.js`
- se tambem for necessario remover a linha de total da impressao por fechamento ou da impressao por periodo/colaborador, sera preciso ajustar e subir tambem os outros dois arquivos acima

## 4. Pacote consolidado para subir

Se o objetivo for publicar exatamente os ultimos desenvolvimentos citados neste atendimento, o pacote minimo de arquivos e:

1. `app/app/src/models/AgendaColaboradorPadrao.php`
2. `app/public/assets/js/local/agenda_escala_cad_form.js`
3. `app/app/templates/escala/agenda_escala_cad_form.twig`
4. `app/app/templates/colaborador/colaborador_res_form.twig`
5. `app/app/templates/conta/colaborador/colaborador_res_form.twig`
6. `app/app/src/models/PontoFolha.php`
7. `app/public/assets/js/local/ponto_folha_print_form.js`

## 5. Lista pronta com origem e destino

1. Local: `/Volumes/MACWORK/gpros-workspace/03-clients/active/facilities/migracao/infra-legado-docker/clients/brasil-servis/app/app/src/models/AgendaColaboradorPadrao.php`
   VPS: `/home/gepros1com/public_html/crm/facilities/brasilservis/app/src/models/AgendaColaboradorPadrao.php`

2. Local: `/Volumes/MACWORK/gpros-workspace/03-clients/active/facilities/migracao/infra-legado-docker/clients/brasil-servis/app/public/assets/js/local/agenda_escala_cad_form.js`
   VPS: `/home/gepros1com/public_html/crm/facilities/brasilservis/public/assets/js/local/agenda_escala_cad_form.js`

3. Local: `/Volumes/MACWORK/gpros-workspace/03-clients/active/facilities/migracao/infra-legado-docker/clients/brasil-servis/app/app/templates/escala/agenda_escala_cad_form.twig`
   VPS: `/home/gepros1com/public_html/crm/facilities/brasilservis/app/templates/escala/agenda_escala_cad_form.twig`

4. Local: `/Volumes/MACWORK/gpros-workspace/03-clients/active/facilities/migracao/infra-legado-docker/clients/brasil-servis/app/app/templates/colaborador/colaborador_res_form.twig`
   VPS: `/home/gepros1com/public_html/crm/facilities/brasilservis/app/templates/colaborador/colaborador_res_form.twig`

5. Local: `/Volumes/MACWORK/gpros-workspace/03-clients/active/facilities/migracao/infra-legado-docker/clients/brasil-servis/app/app/templates/conta/colaborador/colaborador_res_form.twig`
   VPS: `/home/gepros1com/public_html/crm/facilities/brasilservis/app/templates/conta/colaborador/colaborador_res_form.twig`

6. Local: `/Volumes/MACWORK/gpros-workspace/03-clients/active/facilities/migracao/infra-legado-docker/clients/brasil-servis/app/app/src/models/PontoFolha.php`
   VPS: `/home/gepros1com/public_html/crm/facilities/brasilservis/app/src/models/PontoFolha.php`

7. Local: `/Volumes/MACWORK/gpros-workspace/03-clients/active/facilities/migracao/infra-legado-docker/clients/brasil-servis/app/public/assets/js/local/ponto_folha_print_form.js`
   VPS: `/home/gepros1com/public_html/crm/facilities/brasilservis/public/assets/js/local/ponto_folha_print_form.js`

## 6. Ordem recomendada de publicacao

1. Subir primeiro os arquivos PHP e Twig:
   - `AgendaColaboradorPadrao.php`
   - `PontoFolha.php`
   - `agenda_escala_cad_form.twig`
   - `colaborador_res_form.twig`
   - `conta/colaborador_res_form.twig`

2. Subir depois os arquivos JS:
   - `agenda_escala_cad_form.js`
   - `ponto_folha_print_form.js`

3. Garantir que os arquivos sobrescrevam exatamente os destinos acima, sem mudar nomes nem pastas.

## 7. Validacao minima apos subida

### Escala
- abrir `agenda_colaborador_padrao/cadFormEscala`
- editar uma escala com vinculo legado inconsistente
- confirmar que servico e colaborador aparecem preenchidos
- salvar e validar que o vinculo nao e perdido

### Reloginho
- abrir `/colaborador/receptivo?local=1`
- confirmar preenchimento automatico de `Data Inicio` e `Data Fim`
- consultar colaborador sem folha fechada no periodo e validar ausencia de `parsererror`
- consultar caso com batida isolada e confirmar que o registro aparece

### Folha de Ponto impressao
- abrir a impressao principal da folha de ponto
- confirmar que a linha `TOTAL DE HORAS` nao aparece mais nessa impressao

## 8. Observacao final para o agente de subida

Nao limitar a subida apenas ao arquivo `PontoFolha.php`.
Para este pacote, existem mudancas de backend, template e JavaScript.
Se os `.twig` nao subirem junto com os `.js`, a VPS pode continuar servindo assets antigos por causa de cache do navegador ou do include anterior.

## 9. Procedimento critico para producao VPS

Este pacote nao e apenas troca de arquivo. Ele inclui uma alteracao estrutural no banco que precisa ser aplicada antes da publicaçao do codigo.

### 9.1. Alteracao de banco obrigatoria

Arquivo de migracao local:
- `clients/brasil-servis/database/import/20260527_000001_escala_alternada_brasil_servis.sql`

SQL aplicado:

```sql
ALTER TABLE agenda_colaborador_padrao
  ADD COLUMN dias_escala_alternada INT NULL AFTER fl_escala_alternada,
  ADD COLUMN tipo_escala_alternada INT NULL AFTER dias_escala_alternada;
```

Por que isso e obrigatorio:
- os campos `dias_escala_alternada` e `tipo_escala_alternada` foram criados para suportar a nova regra de escala alternada (5x1/6x1, progressiva/regressiva);
- sem esses campos, o backend e o frontend passam a gravar dados inconsistentes ou a falhar na edicao estrutural da escala;
- a VPS precisa receber essa alteracao antes de subir o codigo PHP/JS/Twig, pois o app passa a ler esses campos no fluxo de edicao.

### 9.2. Ordem correta de implantacao na VPS

1. Fazer backup do banco de producao.
2. Aplicar o SQL acima na base da VPS.
3. Subir os arquivos PHP e Twig primeiro.
4. Subir os arquivos JS depois.
5. Limpar cache do navegador e validar em ambiente real.

### 9.3. Pacote final recomendado para o agente de subida

Backend / model:
- `app/app/src/models/AgendaColaboradorPadrao.php`
- `app/app/src/models/PontoFolha.php`

Frontend / templates:
- `app/app/templates/escala/agenda_escala_cad_form.twig`
- `app/app/templates/colaborador/colaborador_res_form.twig`
- `app/app/templates/conta/colaborador/colaborador_res_form.twig`

Assets JS:
- `app/public/assets/js/local/agenda_escala_cad_form.js`
- `app/public/assets/js/local/ponto_folha_print_form.js`

Banco:
- `database/import/20260527_000001_escala_alternada_brasil_servis.sql`

### 9.4. Validacao minima obrigatoria apos a subida

#### Escala
- abrir `agenda_colaborador_padrao/cadFormEscala` na VPS;
- editar uma escala com vinculo legado inconsistente;
- confirmar que servico e colaborador continuam preenchidos;
- salvar e garantir que o vinculo nao e perdido.

#### Reloginho
- abrir `/colaborador/receptivo?local=1`;
- confirmar que os campos `Data Inicio` e `Data Fim` preenchem automaticamente;
- validar que nao ocorre `parsererror` quando nao existe folha de ponto fechada no periodo;
- validar que uma batida isolada continua aparecendo.

#### Folha de Ponto - impressao
- abrir a impressao principal da folha de ponto;
- confirmar que a linha `TOTAL DE HORAS` nao aparece nessa impressao;
- validar que o fluxo de impressao continua funcionando sem quebra visual.

### 9.5. Risco operacional se esse procedimento for ignorado

- se a migracao SQL nao for aplicada, o codigo novo pode falhar ao salvar escala ou ao ler os novos campos;
- se os templates/JS nao forem subidos juntos, a VPS pode continuar servindo versoes antigas do asset;
- se o modulo de impressao nao for atualizado, a exibicao principal da folha continua diferente do que foi validado localmente.

Esse relatorio deve ser tratado como pacote de producao completo, nao como troca parcial de um unico arquivo.
