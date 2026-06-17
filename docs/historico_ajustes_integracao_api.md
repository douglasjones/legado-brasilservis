# Historico de Ajustes de Integracao com API Externa

Data: 2026-04-23

Cliente: `brasil-servis`

Objetivo deste arquivo:
- registrar os problemas encontrados na integracao com a API externa
- documentar as correcoes aplicadas no cliente
- servir como base de replicacao para outros clientes com a mesma estrutura legado

## 1. Liberacao de acesso ao App Ponto

### Problema encontrado

Na tela de colaborador, o botao `Liberar Acesso` exibia sucesso, mas a liberacao nao era refletida corretamente.

Causa identificada:
- a tela consultava o status na API externa
- o botao de liberacao atualizava apenas a base local
- o fluxo misturava `pk` externo com `pk` local

Isso criava um falso sucesso:
- o backend retornava sucesso local
- a tela recarregava consultando a API externa
- como a API nao era atualizada, o status permanecia pendente

### Ajuste aplicado

Foi alterado o fluxo para:
- enviar `api_pk` e `colaborador_pk` a partir da tela de colaborador
- chamar a API externa `action=liberarAcesso`
- atualizar a base local somente como reflexo complementar
- manter fallback local para nao quebrar a tela administrativa existente

### Arquivos alterados

- `clients/brasil-servis/app/public/assets/js/local/colaborador_cad_form.js`
- `clients/brasil-servis/app/app/src/controllers/SolicitacaoAcessoAppController.php`
- `clients/brasil-servis/app/app/src/models/SolicitacaoAcessoApp.php`

### Observacao para replicacao

Se outro cliente consultar status de liberacao na API externa, o fluxo de escrita tambem precisa atualizar a API externa.
Nao pode haver leitura em uma fonte e escrita em outra sem sincronizacao explicita.

## 2. Sincronizacao de colaborador com API externa

### Problema encontrado

O colaborador era sincronizado com a API externa usando dados do request bruto, nao os dados finais normalizados apos a regra de negocio do backend.

Exemplos do risco:
- demissao alterava `ic_status` e `ic_funcionario` localmente
- mesmo assim o payload externo podia usar os valores anteriores enviados pelo formulario

### Ajuste aplicado

Foi alterado o metodo de sincronizacao para:
- buscar o colaborador ja salvo no banco local
- montar o payload externo a partir do estado persistido
- enviar para a API apenas o estado final normalizado

### Arquivo alterado

- `clients/brasil-servis/app/app/src/models/Colaborador.php`

### Observacao para replicacao

A integracao externa deve usar o estado final salvo no banco, e nao o payload original do form.
Isso evita divergencia entre sistema local e servidor externo.

## 3. Exclusao indevida do colaborador no servidor externo

### Problema encontrado

No fluxo de `excluir`, o sistema:
- marcava o colaborador como inativo localmente
- e em seguida chamava `excluirColaboradorApp` na API externa

Isso apagava o colaborador no servidor externo em situacoes em que o esperado era apenas desativar ou demitir.

### Ajuste aplicado

O fluxo foi alterado para:
- nao excluir mais o colaborador no servidor externo ao fazer exclusao logica local
- sincronizar o status final do colaborador com a API externa

### Arquivo alterado

- `clients/brasil-servis/app/app/src/models/Colaborador.php`

### Observacao para replicacao

Separar claramente os conceitos:
- desativar/demitir = atualizar status
- excluir no servidor externo = operacao especifica e excepcional

## 4. Bug em `empresas_pk`

### Problema encontrado

No carregamento de dados do colaborador, o campo `empresas_pk` estava sendo preenchido com `ds_conta`, ou seja, descricao textual da conta, em vez do identificador numerico da empresa.

Risco:
- em fluxos de reenvio ou regravacao, a empresa podia ser contaminada com valor errado
- o CNPJ enviado para a API externa podia ser de outro cliente

### Ajuste aplicado

Foi corrigido o retorno para manter `empresas_pk` com o valor real do PK da empresa.

### Arquivo alterado

- `clients/brasil-servis/app/app/src/models/Colaborador.php`

### Observacao para replicacao

Verificar em outros clientes qualquer atribuicao semelhante a:
- `empresas_pk => ds_conta`
- `contas_pk => ds_conta`
- ou qualquer uso de descricao textual onde deveria haver PK numerico

## 5. Consulta de colaborador na API usando apenas CPF

### Problema encontrado

As consultas de status do app ponto estavam sendo feitas usando apenas `ds_cpf`.

Risco:
- se a API externa identificar colaborador por `CPF + CNPJ`, o sistema local pode consultar o registro errado
- isso explica sintomas como:
  - retorno de outro cliente
  - perda de referencia da empresa correta
  - status de app associado ao vinculo errado

### Situacao atual

Essa parte foi analisada e documentada, mas nao foi alterada ainda neste atendimento.

### Arquivos analisados

- `clients/brasil-servis/app/app/src/models/Colaborador.php`

### Pendencia para replicacao

Confirmar com a API externa se estes endpoints aceitam `ds_cnpj_conta`:
- `action=getImagemLiberacaoApp`
- `action=consultaAcessoApp`

Se suportar, incluir `CPF + CNPJ` nas consultas.

## 6. Registro de ponto vindo do servidor externo

### Local identificado

O ponto vindo do servidor externo entra por rotas publicas:
- `POST /registrarPontoApp`
- `POST /sincronizarPontoApp`

Arquivos de entrada:
- `clients/brasil-servis/app/app/routes.php`
- `clients/brasil-servis/app/app/src/controllers/WebPontoApiController.php`
- `clients/brasil-servis/app/app/src/models/Ponto.php`

### Problemas encontrados no fluxo

#### 6.1. Body poderia nao ser lido corretamente

O controller usava `getParsedBody()` diretamente.

Risco:
- se a API externa enviasse JSON bruto, o body poderia chegar vazio dependendo do ambiente legado

#### 6.2. Latitude e longitude eram obrigatorias

O endpoint rejeitava o registro se `ds_latitude` e `ds_longitude` nao viessem.

Risco:
- registros reais de ponto podiam falhar por falta de geolocalizacao

#### 6.3. Campo `ic_ponto_fora_turno` inconsistente

Havia mistura entre:
- `icPontoForaTurno`
- `ic_ponto_fora_turno`

Risco:
- o valor nao chegava corretamente ao banco
- ou podia tentar gravar coluna com nome incorreto

#### 6.4. Imagem invalida bloqueava o registro

Se a imagem viesse nula, truncada, fora do formato esperado ou falhasse na recompressao, o fluxo podia cair no `catch` antes do `INSERT`.

### Ajustes aplicados

Foi implementado:
- parser de request com fallback para JSON bruto
- latitude/longitude opcionais
- leitura correta de `ic_ponto_fora_turno` em `snake_case` e `camelCase`
- tolerancia a imagem invalida, sem bloquear a gravacao do ponto

### Arquivos alterados

- `clients/brasil-servis/app/app/src/controllers/WebPontoApiController.php`
- `clients/brasil-servis/app/app/src/models/Ponto.php`

## 7. Reducao do tamanho da imagem do ponto

### Problema encontrado

O sistema apenas recomprimia a imagem em JPEG, mas mantinha a resolucao original.

Risco:
- imagens continuavam pesadas
- armazenamento desnecessariamente grande
- visualizacao mais lenta

### Ajuste aplicado

## 8. Reloginho em turno noturno ancorado pela data da escala

Data: 2026-06-02

### Problema encontrado

No modulo `reloginho / acompanhamento de ponto`, colaboradores com turno noturno ainda podiam ter os registros quebrados entre dois dias civis.

Cenario reportado:
- inicio do expediente no dia `10`
- intervalo, retorno e termino na madrugada do dia `11`
- comportamento incorreto:
  - parte das batidas aparecia na linha do dia `11`
- comportamento esperado:
  - todas as batidas do turno devem permanecer na linha do dia `10`

### Causa identificada

O backend do `Brasil Servis` ainda tinha trechos que:
- consultavam ponto noturno usando janelas fixas simplificadas
- ou voltavam a priorizar a consulta normal por `DATE(dt_hora_ponto)` mesmo quando a jornada cruzava meia-noite

Isso fazia o sistema misturar:
- `dia civil do timestamp`
- `dia operacional da escala`

### Base de comparacao

Foi usada como referencia a correcao aplicada no cliente:
- `america-servis`

O mesmo problema funcional ja havia sido tratado la:
- turno noturno deve ser sempre agrupado pela `dt_escala`
- a madrugada deve permanecer vinculada ao dia de inicio do expediente

### Ajuste aplicado

No `Brasil Servis`, a correcao foi adaptada sem sobrescrever as customizacoes locais de escala:

- `clients/brasil-servis/app/app/src/models/PontoFolha.php`
  - criada janela operacional noturna com base nos horarios reais da escala
  - criado fluxo proprio para fechamento da linha do dia em turno noturno
  - impedido que a consulta normal do dia civil reassuma o controle quando o expediente cruza meia-noite

- `clients/brasil-servis/app/app/src/models/Ponto.php`
  - historico do reloginho alinhado com a mesma regra de turno noturno
  - consulta noturna passou a usar a janela operacional da escala
  - consulta diurna voltou a considerar o dia civil completo apenas para turno normal

### Resultado esperado apos o ajuste

- se o expediente iniciar em `10/05/2026`
- e terminar na madrugada de `11/05/2026`
- entrada, intervalo, retorno e saida devem aparecer todos na linha de `10/05/2026`

### Validacoes executadas

- `php -l clients/brasil-servis/app/app/src/models/PontoFolha.php`
- `php -l clients/brasil-servis/app/app/src/models/Ponto.php`

Foi criado um helper central no model `Ponto` para:
- reduzir a largura maxima para `1280px`
- manter proporcao
- converter para JPEG
- aplicar qualidade `75`

Esse tratamento foi aplicado nos fluxos:
- `salvarPontoDeskTop`
- `salvarPontoApp`
- `sincronizarPontoApp`

### Arquivo alterado

- `clients/brasil-servis/app/app/src/models/Ponto.php`

### Efeito esperado

- arquivo menor
- qualidade visual ainda aceitavel para consulta normal
- nao e so compressao: agora tambem ha reducao de resolucao

## 8. Arquivos alterados neste atendimento

- `clients/brasil-servis/app/public/assets/js/local/colaborador_cad_form.js`
- `clients/brasil-servis/app/app/src/controllers/SolicitacaoAcessoAppController.php`
- `clients/brasil-servis/app/app/src/models/SolicitacaoAcessoApp.php`
- `clients/brasil-servis/app/app/src/models/Colaborador.php`
- `clients/brasil-servis/app/app/src/controllers/WebPontoApiController.php`
- `clients/brasil-servis/app/app/src/models/Ponto.php`

## 9. Ordem sugerida para replicacao em outros clientes

1. Corrigir fluxo de liberacao do app para API externa.
2. Corrigir sincronizacao de colaborador usando estado final salvo.
3. Impedir exclusao indevida no servidor externo.
4. Corrigir retorno incorreto de `empresas_pk`.
5. Ajustar endpoints de ponto para aceitar payload robusto.
6. Aplicar reducao e normalizacao das imagens do ponto.
7. Validar se a API externa suporta `CPF + CNPJ` nas consultas.

## 10. Pendencias ainda abertas

- confirmar suporte da API externa a `ds_cnpj_conta` nas consultas de status
- validar em outros clientes se existe a mesma mistura entre base local e API externa
- validar no ambiente Docker se a extensao GD esta presente ou se o fallback atual cobre todos os casos necessarios

---

Data: 2026-04-24

## 11. Reloginho abrindo com datas no local e vazio na producao

### Problema encontrado

Na tela:
- `/colaborador/receptivo?local=1`

o ambiente local preenchia automaticamente:
- `Data Inicio`
- `Data Fim`

mas a producao abria o modal do reloginho com os campos vazios.

### Causa identificada

O HTML do modal nasce com os campos vazios no template, e o preenchimento automatico acontece no JavaScript:
- `clients/brasil-servis/app/public/assets/js/local/colaborador_res_form.js`

No `document.ready`, esse arquivo define:
- primeiro dia do mes em `#dt_ini_reloginho`
- ultimo dia do mes em `#dt_fim_reloginho`

Conclusao:
- o problema nao estava no backend do modal
- a indicacao mais forte era diferenca de versao do asset JS na VPS
- havia risco de cache do navegador/asset mantendo versao antiga

### Ajuste aplicado

Foi alterado o `cache bust` do include do arquivo JS:
- de `?v=14`
- para `?v=15`

### Arquivos alterados

- `clients/brasil-servis/app/app/templates/colaborador/colaborador_res_form.twig`
- `clients/brasil-servis/app/app/templates/conta/colaborador/colaborador_res_form.twig`

### Observacao para replicacao

Quando houver sintoma de interface diferente entre local e producao, verificar:
- arquivo JS realmente publicado na VPS
- versao do query string no template
- cache do navegador

## 12. Prova do erro de registro de ponto na producao via Postman

### Contexto

Foi realizado teste real contra a URL de producao:
- `https://appbrasilservis.gepros1.com.br/registrarPontoApp`

com payload JSON contendo:
- `contas_pk`
- `leads_pk`
- `colaborador_pk`
- `agenda_colaborador_padrao_pk`
- `dt_hora_ponto`
- `tipo_ponto_pk`
- `ds_latitude`
- `ds_longitude`
- `img_ponto`

### Resultado observado

O endpoint respondeu:
- `result = false`
- `ic_status = 2`
- `message = "Call to undefined function App\\Model\\imagecreatefromstring()"`

### Conclusao tecnica

Isso confirmou objetivamente que:
- a requisicao estava chegando corretamente na producao
- o erro acontecia dentro do processamento da imagem
- a VPS estava sem suporte funcional a GD, ou pelo menos sem a funcao `imagecreatefromstring`
- o `INSERT` em `ponto` nao acontecia por causa da falha anterior no processamento da imagem

Essa evidencia elimina a hipotese de falha no app como causa primaria desse caso especifico.

## 13. Fallback para ambiente sem GD no `Ponto.php`

### Problema encontrado

Mesmo com as correcoes anteriores, a funcao de normalizacao de imagem ainda podia tentar usar recursos de GD em ambientes onde essas funcoes nao existissem.

Risco:
- a VPS continuar falhando no registro do ponto
- especialmente em ambientes legados sem extensao GD habilitada

### Ajuste aplicado

Foi endurecido o helper `normalizePointImage()` para:
- verificar existencia de `imagecreatefromstring`
- verificar existencia de `imagejpeg`
- verificar existencia de `imagesx`
- verificar existencia de `imagesy`
- verificar existencia de `imagecreatetruecolor`
- verificar existencia de `imagecopyresampled`

Se qualquer uma dessas funcoes nao existir:
- o sistema nao aborta
- mantem `img_ponto` original
- segue com o `INSERT` do ponto

### Arquivo alterado

- `clients/brasil-servis/app/app/src/models/Ponto.php`

### Observacao para replicacao

Esse fallback reduz dependencia de ambiente, mas nao substitui a correcao estrutural do PHP.
O ideal continua sendo:
- ambiente com GD instalada
- e codigo tolerante a ausencia dessa extensao

## 14. Arquivos alterados em 2026-04-24

- `clients/brasil-servis/app/app/templates/colaborador/colaborador_res_form.twig`
- `clients/brasil-servis/app/app/templates/conta/colaborador/colaborador_res_form.twig`
- `clients/brasil-servis/app/app/src/models/Ponto.php`

## 15. Correcao na edicao de escalas com vinculo legado inconsistente

### Contexto

Na tela `agenda_colaborador_padrao/cadFormEscala`, a edicao da escala `pk=34` carregava datas e horarios, mas deixava em branco os combos de servico e colaborador.

O registro no banco estava com:
- `colaboradores_pk=2883`
- `produtos_servicos_pk=179`
- `contratos_itens_pk=1302`

Porem o item de contrato `1302` aponta para outro produto/servico. Com isso, os combos derivados do contrato nao traziam o servico salvo na agenda e o formulario podia perder o vinculo ao salvar.

### Ajuste aplicado

- O endpoint de edicao passou a retornar tambem `ds_produto_servico`, `ds_colaborador` e `contratos_itens_pk`.
- O JS da tela agora preserva a opcao salva no registro quando ela nao aparece nos combos derivados do contrato.
- Ao salvar uma edicao nessa situacao, o JS mantem o `contratos_itens_pk` original se a validacao do contrato nao encontrar um item compativel com o servico legado.
- Atualizado o cache bust do JS de `v=14` para `v=15`.

### Arquivos alterados

- `clients/brasil-servis/app/app/src/models/AgendaColaboradorPadrao.php`
- `clients/brasil-servis/app/public/assets/js/local/agenda_escala_cad_form.js`
- `clients/brasil-servis/app/app/templates/escala/agenda_escala_cad_form.twig`

## 16. Correcao de `parsererror` no reloginho

### Contexto

No modulo do reloginho, ao pesquisar ponto do colaborador `2883` no periodo `01/04/2026` a `30/04/2026`, o navegador exibia `Falhou a requisicao: parsererror`.

### Causa

O endpoint `ponto_folha/listarConsultaPontoColaborador` retornava HTTP 200, mas antes do JSON eram emitidos warnings HTML do PHP:
- `Illegal string offset 'pk'`
- `Illegal string offset 'ic_status'`

Isso ocorria quando ainda nao existia folha de ponto para o colaborador no periodo. O helper `getPontoFolhaByColaboradorPeriodoColaborador()` retornava string vazia, e o fluxo tentava acessar esse retorno como array.

### Ajuste aplicado

- O retorno vazio do helper passou a ser array vazio.
- A montagem da resposta do reloginho passou a proteger `ponto_folha_pk` e `ic_status_ponto_folha_pk` com fallback vazio.
- Validado que a chamada do reloginho retorna JSON limpo, sem warnings antes da resposta.

### Arquivo alterado

- `clients/brasil-servis/app/app/src/models/PontoFolha.php`

## 17. Correcao no carregamento de batida isolada no reloginho

### Contexto

Apos corrigir o `parsererror`, o reloginho carregava a grade do periodo, mas nao exibia o unico registro existente do colaborador `2883` em abril de 2026.

No banco havia uma batida em `23/04/2026 17:46:59`, tipo `2` termino do expediente, vinculada ao lead `1531` e agenda `34`.

### Causa

A consulta de ponto em escala normal exigia a existencia de uma batida de entrada, tipo `1`, no mesmo dia. Quando existia apenas a saida, a consulta descartava o registro.

### Ajuste aplicado

- A consulta de escala normal passou a permitir batidas isoladas no dia.
- A selecao entre consulta normal e noturna passou a verificar se o turno do dia e noturno pelo `turno_pk` diario ou por horario que cruza meia-noite.
- Para turno noturno, foi preservada a regra de so aceitar a consulta normal quando ela tiver entrada e saida; caso contrario, usa a consulta noturna.
- Validado que o periodo `01/04/2026` a `30/04/2026` retorna o registro de `23/04/2026` com `ponto_term_expediente=17:46`.

### Arquivo alterado

- `clients/brasil-servis/app/app/src/models/PontoFolha.php`
