# Relatorio de Implantacao em Producao VPS para Agente de IA

Data: 2026-05-29

Cliente: `brasil-servis`

Criticidade: `ALTA`

Objetivo deste documento:
- orientar um agente de IA a coordenar a subida das atualizacoes do cliente `brasil-servis` em ambiente de producao VPS;
- informar com precisao o projeto local, os arquivos que compoem o pacote, os destinos esperados na VPS e a ordem de implantacao;
- registrar a alteracao obrigatoria de banco de dados e justificar por que ela precisa ser aplicada antes da subida do codigo;
- incluir explicitamente o modulo de impressao da folha de ponto, que foi alterado;
- reduzir o risco de publicacao parcial, cache antigo, inconsistencias de banco e regressao funcional.

## 1. Escopo do pacote de producao

Este pacote contempla tres frentes funcionais:

1. `Escala`
2. `Reloginho`
3. `Folha de Ponto - impressao`

O pacote deve ser tratado como uma implantacao coordenada de:
- banco de dados;
- backend PHP;
- templates Twig;
- assets JavaScript.

Nao tratar esta entrega como substituicao isolada de um unico arquivo.

## 2. Projeto local de origem

Caminho raiz local do cliente:

`/Volumes/MACWORK/gpros-workspace/03-clients/active/facilities/migracao/infra-legado-docker/clients/brasil-servis/`

Aplicacao local ativa:

`/Volumes/MACWORK/gpros-workspace/03-clients/active/facilities/migracao/infra-legado-docker/clients/brasil-servis/app/`

Documentacao ja existente no projeto:

- `clients/brasil-servis/docs/historico_ajustes_integracao_api.md`
- `clients/brasil-servis/docs/relatorio_subida_vps_2026-05-29.md`

Arquivo SQL de migracao obrigatoria:

- `clients/brasil-servis/database/import/20260527_000001_escala_alternada_brasil_servis.sql`

## 3. Destino esperado na VPS

O caminho de producao foi inferido a partir dos registros do proprio projeto, especialmente `app/public/error_log`.

Caminho base esperado na VPS:

`/home/gepros1com/public_html/crm/facilities/brasilservis/`

Mapeamento esperado:

- local `clients/brasil-servis/app/app/...` -> VPS `/home/gepros1com/public_html/crm/facilities/brasilservis/app/...`
- local `clients/brasil-servis/app/public/...` -> VPS `/home/gepros1com/public_html/crm/facilities/brasilservis/public/...`

## 4. Evidencia tecnica da alteracao de banco obrigatoria

### 4.1. SQL identificado

Arquivo:

`clients/brasil-servis/database/import/20260527_000001_escala_alternada_brasil_servis.sql`

Conteudo:

```sql
ALTER TABLE agenda_colaborador_padrao
  ADD COLUMN dias_escala_alternada INT NULL AFTER fl_escala_alternada,
  ADD COLUMN tipo_escala_alternada INT NULL AFTER dias_escala_alternada;
```

### 4.2. Por que essa migracao e obrigatoria

Os campos `dias_escala_alternada` e `tipo_escala_alternada` nao foram criados por conveniencia. Eles ja fazem parte do fluxo ativo do modulo de escala no codigo local:

- o controller recebe esses campos no salvamento:
  - `app/app/src/controllers/AgendaColaboradorPadraoController.php`
- o model inclui esses campos em insert, update, consulta e validacao:
  - `app/app/src/models/AgendaColaboradorPadrao.php`
- o template exibe os campos na tela:
  - `app/app/templates/escala/agenda_escala_cad_form.twig`
- o JavaScript habilita, valida, carrega e envia os valores:
  - `app/public/assets/js/local/agenda_escala_cad_form.js`

Conclusao operacional:

- se a VPS receber apenas os arquivos PHP, Twig e JS sem a migracao SQL, o sistema novo tentara ler e gravar colunas inexistentes;
- isso pode gerar falha de salvamento, edicao inconsistente de escala ou erro SQL em producao;
- portanto a alteracao de banco deve ocorrer antes da subida do codigo.

### 4.3. Objetivo funcional dos novos campos

Os dois campos suportam a nova regra de `escala alternada`, especialmente para cenarios como:

- `5x1`
- `6x1`
- variacao progressiva/regressiva da alternancia

Sem esses campos, a VPS nao consegue persistir corretamente os detalhes adicionais exigidos pela nova modelagem da escala.

## 5. Arquivos do pacote que devem subir

### 5.1. Modulo Escala

Arquivos locais:

1. `clients/brasil-servis/app/app/src/models/AgendaColaboradorPadrao.php`
2. `clients/brasil-servis/app/public/assets/js/local/agenda_escala_cad_form.js`
3. `clients/brasil-servis/app/app/templates/escala/agenda_escala_cad_form.twig`

Destinos na VPS:

1. `/home/gepros1com/public_html/crm/facilities/brasilservis/app/src/models/AgendaColaboradorPadrao.php`
2. `/home/gepros1com/public_html/crm/facilities/brasilservis/public/assets/js/local/agenda_escala_cad_form.js`
3. `/home/gepros1com/public_html/crm/facilities/brasilservis/app/templates/escala/agenda_escala_cad_form.twig`

Justificativa:

- o template de escala local referencia `agenda_escala_cad_form.js?v=24`;
- o JS contem validacao e envio dos novos campos `dias_escala_alternada` e `tipo_escala_alternada`;
- o model usa esses campos em persistencia e leitura;
- publicar apenas parte desse conjunto aumenta muito o risco de tela e backend ficarem fora de sincronia.

### 5.2. Modulo Reloginho

Arquivos locais:

1. `clients/brasil-servis/app/app/templates/colaborador/colaborador_res_form.twig`
2. `clients/brasil-servis/app/app/templates/conta/colaborador/colaborador_res_form.twig`
3. `clients/brasil-servis/app/app/src/models/PontoFolha.php`

Destinos na VPS:

1. `/home/gepros1com/public_html/crm/facilities/brasilservis/app/templates/colaborador/colaborador_res_form.twig`
2. `/home/gepros1com/public_html/crm/facilities/brasilservis/app/templates/conta/colaborador/colaborador_res_form.twig`
3. `/home/gepros1com/public_html/crm/facilities/brasilservis/app/src/models/PontoFolha.php`

Justificativa:

- os templates locais referenciam `colaborador_res_form.js?v=15`;
- o historico do cliente registra que esse pacote corrige preenchimento de datas, ausencia de `parsererror` e exibicao de batida isolada;
- o backend de `PontoFolha.php` faz parte do comportamento validado localmente para este fluxo.

### 5.3. Modulo Folha de Ponto - impressao

Arquivo local alterado:

1. `clients/brasil-servis/app/public/assets/js/local/ponto_folha_print_form.js`

Destino na VPS:

1. `/home/gepros1com/public_html/crm/facilities/brasilservis/public/assets/js/local/ponto_folha_print_form.js`

Evidencia funcional:

- o arquivo local `ponto_folha_print_form.js` nao contem mais o bloco `TOTAL DE HORAS`;
- os arquivos abaixo ainda contem esse bloco:
  - `clients/brasil-servis/app/public/assets/js/local/ponto_folha_print_fechamento_form.js`
  - `clients/brasil-servis/app/public/assets/js/local/ponto_folha_print_periodo_colaborador_form.js`

Interpretacao segura:

- a alteracao confirmada neste pacote vale para a impressao principal da folha;
- nao assumir que fechamento e impressao por periodo/colaborador devem mudar, porque o codigo local ainda preserva `TOTAL DE HORAS` nesses dois arquivos;
- se houver demanda futura para retirar esse bloco desses outros modos de impressao, isso deve ser tratado como escopo separado.

## 6. Observacao critica sobre cache no modulo de impressao

Os templates locais de impressao principal ainda apontam para:

- `app/app/templates/ponto_folha/ponto_folha_print_form.twig` -> `ponto_folha_print_form.js?v=14`
- `app/app/templates/ocorrencia/ponto_folha/ponto_folha_print_form.twig` -> `ponto_folha_print_form.js?v=14`

Implicacao:

- o JS de impressao foi alterado, mas nao foi identificado bump de versao nesses templates;
- em producao, isso cria risco real de navegador ou proxy continuar entregando o asset antigo mesmo apos substituicao do arquivo.

Instrucao obrigatoria para o agente de IA:

- apos publicar `ponto_folha_print_form.js`, executar validacao com recarga forcada de navegador;
- se a VPS ou a estacao do usuario mantiver cache agressivo, tratar a invalidacao de cache como parte obrigatoria da implantacao;
- nao encerrar a implantacao sem validar visualmente que a linha `TOTAL DE HORAS` realmente deixou de aparecer na impressao principal.

Observacao:

- este relatorio nao pressupoe alteracao adicional de codigo nesses templates;
- ele apenas registra que o risco de cache existe e precisa ser controlado durante a publicacao.

## 7. Ordem obrigatoria de implantacao em producao

### 7.1. Preparacao

1. Confirmar janela de publicacao com impacto controlado.
2. Confirmar acesso de escrita na VPS.
3. Fazer backup dos arquivos atuais que serao sobrescritos.
4. Fazer backup do banco de producao antes de qualquer SQL.

### 7.2. Banco de dados

1. Aplicar o SQL `20260527_000001_escala_alternada_brasil_servis.sql` na base de producao.
2. Confirmar que as colunas `dias_escala_alternada` e `tipo_escala_alternada` existem em `agenda_colaborador_padrao`.
3. So prosseguir para subida de codigo depois dessa confirmacao.

### 7.3. Backend e templates

Subir primeiro:

1. `AgendaColaboradorPadrao.php`
2. `PontoFolha.php`
3. `agenda_escala_cad_form.twig`
4. `colaborador_res_form.twig`
5. `conta/colaborador_res_form.twig`

### 7.4. Assets JavaScript

Subir depois:

1. `agenda_escala_cad_form.js`
2. `ponto_folha_print_form.js`

### 7.5. Fechamento de publicacao

1. Garantir que os arquivos foram sobrescritos exatamente nos destinos corretos.
2. Limpar cache aplicavel no fluxo operacional.
3. Realizar validacao funcional imediatamente apos a subida.

## 8. Tabela consolidada origem -> destino

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

8. Local: `/Volumes/MACWORK/gpros-workspace/03-clients/active/facilities/migracao/infra-legado-docker/clients/brasil-servis/database/import/20260527_000001_escala_alternada_brasil_servis.sql`
   VPS/DB: aplicar na base de producao antes da subida do codigo

## 9. Checklist de validacao obrigatoria apos a subida

### 9.1. Escala

1. Abrir `agenda_colaborador_padrao/cadFormEscala`.
2. Editar uma escala que ja exista.
3. Confirmar que servico e colaborador continuam preenchidos mesmo em vinculo legado inconsistente.
4. Validar que os campos de escala alternada aparecem e funcionam quando aplicavel.
5. Salvar e confirmar que nao houve erro SQL nem perda de vinculo.

### 9.2. Reloginho

1. Abrir `/colaborador/receptivo?local=1`.
2. Confirmar preenchimento automatico de `Data Inicio` e `Data Fim`.
3. Testar colaborador sem folha fechada no periodo e confirmar ausencia de `parsererror`.
4. Testar caso com batida isolada e confirmar que o registro aparece.

### 9.3. Folha de Ponto - impressao principal

1. Abrir a impressao principal da folha de ponto.
2. Forcar recarga do navegador.
3. Confirmar que a linha `TOTAL DE HORAS` nao aparece mais nessa impressao.
4. Validar que o restante do layout e da impressao continua funcional.

## 10. Riscos se a implantacao for parcial

1. Sem o SQL, o modulo de escala pode falhar ao ler ou gravar colunas inexistentes.
2. Sem os templates junto dos arquivos JS/PHP, o frontend pode continuar com referencias antigas ou comportamento incompleto.
3. Sem controle de cache na impressao, a producao pode aparentar nao ter recebido a alteracao mesmo com o JS novo publicado.
4. Se o agente publicar apenas `PontoFolha.php` ou apenas um JS isolado, o pacote fica funcionalmente incompleto.

## 11. Procedimento de rollback minimo

Se houver falha relevante em producao:

1. restaurar os arquivos substituidos a partir do backup feito antes da publicacao;
2. interromper novos testes de usuarios finais;
3. avaliar se o rollback tambem exige restauracao do banco ou apenas manter as novas colunas sem uso;
4. registrar qual etapa falhou: banco, backend, template, JS ou cache.

Observacao importante:

- a simples existencia das novas colunas no banco nao obriga rollback de schema se o codigo antigo continuar compativel;
- ainda assim, a decisao deve ser tomada com cautela pelo responsavel da producao.

## 12. Instrucao final ao agente de IA

Executar esta implantacao como `pacote coordenado de producao`.

Nao reduzir a entrega a um unico arquivo.

Nao subir primeiro o codigo para depois pensar no banco.

Nao encerrar a atividade sem:

1. confirmacao da migracao SQL;
2. confirmacao da copia correta dos arquivos;
3. validacao funcional de `Escala`, `Reloginho` e `Folha de Ponto - impressao principal`;
4. validacao especifica de cache no modulo de impressao.
