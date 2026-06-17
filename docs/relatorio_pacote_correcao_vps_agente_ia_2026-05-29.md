# Relatorio Tecnico de Pacote para VPS - Agente de IA

Data: 2026-05-29

Cliente: `brasil-servis`

Objetivo:
- consolidar o pacote tecnico que o agente de IA deve validar e publicar na VPS;
- separar o pacote minimo para corrigir o incidente atual da `Escala`;
- separar o pacote completo das alteracoes recentes identificadas localmente;
- indicar origem local e destino exato na VPS para cada arquivo.

## 1. Contexto do incidente atual

O incidente atual esta no fluxo:

```text
/agenda_colaborador_padrao/cadFormEscala
POST /api/agenda_colaborador_padrao/salvar
```

O problema observado em producao foi:

- a `model` nova detecta alteracao estrutural da escala;
- a resposta deveria voltar com `requires_confirmation: true`;
- a producao respondeu apenas com `status: false` e `message`;
- isso comprovou deploy parcial anterior.

## 2. Caminho local do projeto

Raiz local:

```text
/Volumes/MACWORK/gpros-workspace/03-clients/active/facilities/migracao/infra-legado-docker/clients/brasil-servis/
```

App local:

```text
/Volumes/MACWORK/gpros-workspace/03-clients/active/facilities/migracao/infra-legado-docker/clients/brasil-servis/app/
```

## 3. Caminho esperado na VPS

Base esperada da aplicacao em producao:

```text
/home/gepros1com/public_html/crm/facilities/brasilservis/
```

Mapeamento:

- local `clients/brasil-servis/app/app/...` -> VPS `/home/gepros1com/public_html/crm/facilities/brasilservis/app/...`
- local `clients/brasil-servis/app/public/...` -> VPS `/home/gepros1com/public_html/crm/facilities/brasilservis/public/...`
- local `clients/brasil-servis/database/...` -> arquivo de apoio para aplicacao manual no banco

## 4. Pacote A - minimo obrigatorio para corrigir o incidente atual da Escala

Este pacote e o minimo tecnico para o agente de IA alinhar o fluxo de confirmacao estrutural da escala.

### 4.1. Banco

Origem local:

```text
clients/brasil-servis/database/import/20260527_000001_escala_alternada_brasil_servis.sql
```

Aplicacao:

- aplicar na base de producao se ainda nao estiver aplicado.

SQL:

```sql
ALTER TABLE agenda_colaborador_padrao
  ADD COLUMN dias_escala_alternada INT NULL AFTER fl_escala_alternada,
  ADD COLUMN tipo_escala_alternada INT NULL AFTER dias_escala_alternada;
```

### 4.2. Backend obrigatorio

1. Origem local:
```text
clients/brasil-servis/app/app/src/controllers/AgendaColaboradorPadraoController.php
```
Destino VPS:
```text
/home/gepros1com/public_html/crm/facilities/brasilservis/app/src/controllers/AgendaColaboradorPadraoController.php
```

2. Origem local:
```text
clients/brasil-servis/app/app/src/models/AgendaColaboradorPadrao.php
```
Destino VPS:
```text
/home/gepros1com/public_html/crm/facilities/brasilservis/app/src/models/AgendaColaboradorPadrao.php
```

### 4.3. Frontend obrigatorio

3. Origem local:
```text
clients/brasil-servis/app/public/assets/js/local/agenda_escala_cad_form.js
```
Destino VPS:
```text
/home/gepros1com/public_html/crm/facilities/brasilservis/public/assets/js/local/agenda_escala_cad_form.js
```

4. Origem local:
```text
clients/brasil-servis/app/app/templates/escala/agenda_escala_cad_form.twig
```
Destino VPS:
```text
/home/gepros1com/public_html/crm/facilities/brasilservis/app/templates/escala/agenda_escala_cad_form.twig
```

### 4.4. Complemento obrigatorio para variacoes de tela

Estes arquivos tambem devem ser considerados parte do pacote da Escala, porque foram alterados localmente e participam de variacoes do mesmo fluxo:

5. Origem local:
```text
clients/brasil-servis/app/app/templates/conta/escala/agenda_escala_cad_form.twig
```
Destino VPS:
```text
/home/gepros1com/public_html/crm/facilities/brasilservis/app/templates/conta/escala/agenda_escala_cad_form.twig
```

6. Origem local:
```text
clients/brasil-servis/app/public/assets/js/local/colaborador_controle_escala_cad_form.js
```
Destino VPS:
```text
/home/gepros1com/public_html/crm/facilities/brasilservis/public/assets/js/local/colaborador_controle_escala_cad_form.js
```

### 4.5. Resultado esperado do Pacote A

Depois da publicacao do Pacote A:

- o endpoint `POST /api/agenda_colaborador_padrao/salvar` deve retornar `requires_confirmation: true` quando houver alteracao estrutural;
- a tela deve conseguir confirmar a operacao;
- a escala anterior deve ser cancelada e a nova criada corretamente.

## 5. Pacote B - pacote completo das alteracoes recentes identificadas localmente

Este pacote inclui o Pacote A e adiciona os arquivos alterados recentemente de `Folha de Ponto` e `Impressao`.

### 5.1. Escala

1. `clients/brasil-servis/app/app/src/controllers/AgendaColaboradorPadraoController.php`
   -> `/home/gepros1com/public_html/crm/facilities/brasilservis/app/src/controllers/AgendaColaboradorPadraoController.php`

2. `clients/brasil-servis/app/app/src/models/AgendaColaboradorPadrao.php`
   -> `/home/gepros1com/public_html/crm/facilities/brasilservis/app/src/models/AgendaColaboradorPadrao.php`

3. `clients/brasil-servis/app/public/assets/js/local/agenda_escala_cad_form.js`
   -> `/home/gepros1com/public_html/crm/facilities/brasilservis/public/assets/js/local/agenda_escala_cad_form.js`

4. `clients/brasil-servis/app/app/templates/escala/agenda_escala_cad_form.twig`
   -> `/home/gepros1com/public_html/crm/facilities/brasilservis/app/templates/escala/agenda_escala_cad_form.twig`

5. `clients/brasil-servis/app/app/templates/conta/escala/agenda_escala_cad_form.twig`
   -> `/home/gepros1com/public_html/crm/facilities/brasilservis/app/templates/conta/escala/agenda_escala_cad_form.twig`

6. `clients/brasil-servis/app/public/assets/js/local/colaborador_controle_escala_cad_form.js`
   -> `/home/gepros1com/public_html/crm/facilities/brasilservis/public/assets/js/local/colaborador_controle_escala_cad_form.js`

7. `clients/brasil-servis/database/import/20260527_000001_escala_alternada_brasil_servis.sql`
   -> aplicar no banco de producao, se ainda nao aplicado

### 5.2. Folha de Ponto / Impressao

8. `clients/brasil-servis/app/app/src/controllers/PontoFolhaController.php`
   -> `/home/gepros1com/public_html/crm/facilities/brasilservis/app/src/controllers/PontoFolhaController.php`

9. `clients/brasil-servis/app/app/src/models/PontoFolha.php`
   -> `/home/gepros1com/public_html/crm/facilities/brasilservis/app/src/models/PontoFolha.php`

10. `clients/brasil-servis/app/app/templates/ponto_folha/ponto_folha_print_form.twig`
    -> `/home/gepros1com/public_html/crm/facilities/brasilservis/app/templates/ponto_folha/ponto_folha_print_form.twig`

11. `clients/brasil-servis/app/public/assets/js/local/ponto_folha_print_form.js`
    -> `/home/gepros1com/public_html/crm/facilities/brasilservis/public/assets/js/local/ponto_folha_print_form.js`

12. `clients/brasil-servis/app/app/templates/ponto_folha/ponto_folha_registros_res_form.twig`
    -> `/home/gepros1com/public_html/crm/facilities/brasilservis/app/templates/ponto_folha/ponto_folha_registros_res_form.twig`

13. `clients/brasil-servis/app/public/assets/js/local/ponto_folha_registros_res_form.js`
    -> `/home/gepros1com/public_html/crm/facilities/brasilservis/public/assets/js/local/ponto_folha_registros_res_form.js`

## 6. Ordem tecnica de implantacao

### 6.1. Confirmacao inicial

O agente de IA deve primeiro confirmar:

1. qual e o caminho real servido pela web;
2. se existe apenas uma copia do projeto na VPS;
3. se o banco ja contem as colunas:
   - `dias_escala_alternada`
   - `tipo_escala_alternada`

### 6.2. Ordem de subida

1. Aplicar SQL no banco, se necessario.
2. Subir controllers e models.
3. Subir templates Twig.
4. Subir arquivos JS.
5. Reiniciar/recarregar o runtime PHP da aplicacao se houver OPcache ou PHP-FPM em uso.

## 7. Validacao obrigatoria apos a subida

### 7.1. Escala

Validar:

1. abrir `agenda_colaborador_padrao/cadFormEscala`;
2. editar uma escala com alteracao estrutural;
3. observar no `Network` a resposta de `/api/agenda_colaborador_padrao/salvar`;
4. confirmar que o JSON contem:

```json
{
  "requires_confirmation": true
}
```

5. confirmar a operacao na tela;
6. validar que a escala anterior foi cancelada e a nova criada.

### 7.2. Folha de Ponto / Impressao

Validar:

1. abrir a impressao principal da folha de ponto;
2. confirmar que a linha `TOTAL DE HORAS` nao aparece mais na impressao principal;
3. validar que a tela continua carregando sem erro;
4. fazer recarga forcada do navegador se houver suspeita de cache.

## 8. Risco operacional se o pacote for parcial

Se o agente publicar apenas parte do pacote:

- o backend pode responder com estrutura antiga;
- o frontend pode continuar sem conseguir confirmar alteracao estrutural;
- variacoes de tela podem continuar inconsistentes;
- a impressao da folha pode permanecer divergente do que foi validado localmente.

## 9. Recomendacao final ao agente de IA

Se o objetivo imediato for encerrar o incidente atual da Escala, publicar no minimo o `Pacote A`.

Se o objetivo for alinhar a producao com as alteracoes recentes identificadas localmente no cliente, publicar o `Pacote B`.

Nao considerar o pacote antigo como completo sem essa revisao.
