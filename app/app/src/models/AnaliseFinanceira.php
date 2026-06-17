<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class AnaliseFinanceira {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function excluir($pk){
        Util::execDelete('analise_financeira', ' pk='.$pk, $this->pdo);
    }

    public function listarGrid($ic_status, $lancamento_pk, $dt_cadastro_ini, $dt_cadastro_fim, $dt_aprovacao_ini, $dt_aprovacao_fim, $dt_correcao_ini, $dt_correcao_fim, $dt_recusa_ini, $dt_recusa_fim, $dt_vencimento_ini, $dt_vencimento_fim, $usuario_cadastro_lancamento_pk, $usuario_cadastro_analista_pk, $usuario_cadastro_gestor_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio


       //PAGINAÇÃO
        if(isset($_GET['start']) && $_GET['start']!=0){
            $displayStart = $_GET['start'];
        }
        else{
            $displayStart = 0;
        }

        if(isset($_GET['length'])){
            $displayRange = $_GET['length'];
            $lengthSql = " LIMIT ".intval($displayRange)." OFFSET ".intval($displayStart);
        }
        else{
            $lengthSql = " ";
        }
        $search = "";
        if (isset($_GET['search']['value']) and $_GET['search']['value'] != '') {
            $pesq = $_GET['search']['value'];
            $search .= " AND (
                            af.pk LIKE '%".$pesq."%' OR
                            u.ds_usuario LIKE '%".$pesq."%' OR
                            af.lancamentos_pk LIKE '%".$pesq."%' 
                        )";
        }

        $sql ="";
        $sql .=" SELECT af.pk t_pk,";
        $sql .="     DATE_FORMAT(af.dt_cadastro, '%d/%m/%Y') dt_cadastro_financeiro,";
        $sql .="     DATE_FORMAT(l.dt_cadastro, '%d/%m/%Y') dt_cadastro_lancamento,";
        $sql .="     af.dt_cadastro,";
        $sql .="     af.usuario_cadastro_pk,";
        $sql .="     MAX(afp.pk) pk,";
        $sql .="     af.lancamentos_pk t_lancamentos_pk,";
        $sql .="     af.ic_status,";
        $sql .="     CASE";
        $sql .="         WHEN af.ic_status = 1 THEN 'Não Analisado'";
        $sql .="         WHEN af.ic_status = 2 THEN 'Aprovado Analista'";
        $sql .="         WHEN af.ic_status = 3 THEN 'Aprovado Gestor'";
        $sql .="         WHEN af.ic_status = 4 THEN 'Correção Solicitada'";
        $sql .="         WHEN af.ic_status = 5 THEN 'Recusado'";
        $sql .="         WHEN af.ic_status = 6 THEN 'Correção Feita'";
        $sql .="         WHEN af.ic_status = 7 THEN 'Cancelado'";
        $sql .="     END ic_status,";
        $sql .="     CASE";
        $sql .="         WHEN afp.ic_recusa = 1 THEN DATE_FORMAT(afp.dt_cadastro, '%d/%m/%Y')";
        $sql .="     END dt_recusa,";
        $sql .="     CASE";
        $sql .="         WHEN afp.ic_aprovacao = 1 THEN DATE_FORMAT(afp.dt_cadastro, '%d/%m/%Y')";
        $sql .="     END dt_aprovacao,";
        $sql .="     CASE";
        $sql .="         WHEN afp.ic_correcao = 1 THEN DATE_FORMAT(afp.dt_cadastro, '%d/%m/%Y')";
        $sql .="     END dt_correcao,";
        $sql .="     af.gestor_aprovacao_pk,";
        $sql .="     af.usuario_cadastro_lancamento_pk,";
        $sql .="     u.ds_usuario t_usuario_cadastro_lancamento_pk,";
        $sql .="     af.dt_cancelamento t_dt_lancamento,";
        $sql .="     afp.dt_cadastro dt_cadastro_processos,";
        $sql .="     afp.usuario_cadastro_pk";
        $sql .=" FROM analise_financeira af";
        $sql .="         INNER JOIN";
        $sql .="     lancamentos_financeiros l ON af.lancamentos_pk = l.pk";
        $sql .="         LEFT JOIN";
        $sql .="     analise_financeira_processos afp ON afp.analise_financeira_pk = af.pk";
        $sql .="         LEFT JOIN";
        $sql .="     usuarios u ON af.usuario_cadastro_lancamento_pk = u.pk";
        $sql .="         LEFT JOIN";
        $sql .="     usuarios usu ON afp.usuario_cadastro_pk = usu.pk";
        $sql .=" WHERE";
        $sql .="     1 = 1";
        $sql .=$search;

        if($ic_status != ""){
            $sql.=" AND af.ic_status = '".$ic_status."' ";
        }
        if($lancamento_pk != ""){
            $sql.=" AND l.pk = '".$lancamento_pk."' ";
        }

        if($dt_cadastro_ini != ""){
            $sql.=" AND af.dt_cadastro <= '".Util::DataYMD($dt_cadastro_ini)."' ";
        }
        if($dt_cadastro_fim != ""){
            $sql.=" AND af.dt_cadastro <= '".Util::DataYMD($dt_cadastro_fim)."' ";
        }

        if($dt_aprovacao_ini != ""){
            $sql.=" AND afp.dt_cadastro >= '".Util::DataYMD($dt_aprovacao_ini)."' ";
            $sql.=" AND afp.ic_aprovacao = '1' ";
        }
        if($dt_aprovacao_fim != ""){
            $sql.=" AND afp.dt_cadastro <= '".Util::DataYMD($dt_aprovacao_fim)."' ";
            $sql.=" AND afp.ic_aprovacao = '1' ";
        }

        if($dt_correcao_ini != ""){
            $sql.=" AND afp.dt_cadastro >= '".Util::DataYMD($dt_correcao_ini)."' ";
            $sql.=" AND afp.ic_correcao = '1' ";
        }
        if($dt_correcao_fim != ""){
            $sql.=" AND afp.dt_cadastro <= '".Util::DataYMD($dt_correcao_fim)."' ";
            $sql.=" AND afp.ic_correcao = '1' ";
        }

        if($dt_recusa_ini != ""){
            $sql.=" AND afp.dt_cadastro >= '".Util::DataYMD($dt_recusa_ini)."' ";
            $sql.=" AND afp.ic_recusa = '1' ";
        }
        if($dt_recusa_fim != ""){
            $sql.=" AND afp.dt_cadastro >= '".Util::DataYMD($dt_recusa_fim)."' ";
            $sql.=" AND afp.ic_recusa = '1' ";
        }

        if($dt_vencimento_ini != ""){
            $sql.=" AND l.dt_vencimento >= '".Util::DataYMD($dt_vencimento_ini)."' ";            
        }
        if($dt_vencimento_fim != ""){
            $sql.=" AND l.dt_vencimento <= '".Util::DataYMD($dt_vencimento_fim)."' ";            
        }

        if($usuario_cadastro_lancamento_pk != ""){
            $sql.=" AND af.usuario_cadastro_lancamento_pk = '".$usuario_cadastro_lancamento_pk."' ";
        }
        if($usuario_cadastro_analista_pk != ""){
            $sql.=" AND afp.usuario_cadastro_pk = '".$usuario_cadastro_analista_pk."' ";
        }
        if($usuario_cadastro_gestor_pk != ""){
            $sql.=" AND afp.usuario_cadastro_pk = '".$usuario_cadastro_gestor_pk."' ";
        }

        if($_SESSION['session_user']['par8'] == "Gestor"){
            $sql.=" AND af.gestor_aprovacao_pk = '".$_SESSION['session_user']['par1']."' ";
            $sql.=" AND (af.ic_status = 2 || af.ic_status = 3)";
        }
        $sql .=" GROUP BY af.pk ";

        $sql .=" ORDER BY dt_cadastro DESC";
    

        $stmt = $this->pdo->prepare( $sql.$lengthSql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmtCount = $this->pdo->prepare( $sql );
        $stmtCount->execute();
        $rowsCount = $stmtCount->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        $retorno->iTotalDisplayRecords = count($rowsCount);
        $retorno->iTotalRecords = count($rowsCount);

        echo json_encode($retorno);
        exit(0);
    }

    public function listarPorPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $ds_recebido_de = "";
        $ds_recebido_de_centro_custo = "";                
        $ds_agencia = "";                
        $ds_conta = "";                
        $ds_digito = "";                
        $ds_banco = "";
        $ds_lancamento_posto_trabalho = "";

        $sql ="";
        $sql.="select af.pk, date_format(af.dt_cadastro,'%d/%m/%Y') dt_cadastro, af.usuario_cadastro_pk  ";
        //$sql.="       ,l.pk lancamento_pk ";
        $sql.="       ,af.lancamentos_pk ";
        $sql.="       ,af.usuario_cadastro_lancamento_pk ";
        $sql.="       ,af.ic_status ";
        $sql.="       ,af.gestor_aprovacao_pk ";
        $sql.="       ,l.obs_lancamento obs ";
        $sql.="       ,date_format(af.dt_cancelamento,'%d/%m/%Y') dt_cancelamento";
        $sql.="       ,l.tipos_operacao_pk";
        $sql.="       ,l.ds_lancamento";
        $sql.="       ,l.contratos_pk";
        $sql.="       ,l.tipo_lancamento_pk";
        $sql.="       ,CASE";
        $sql.="          WHEN l.tipo_lancamento_pk = 1 THEN 'Receita'";
        $sql.="          WHEN l.tipo_lancamento_pk = 2 THEN 'Despesa Fixa'";
        $sql.="          WHEN l.tipo_lancamento_pk = 3 THEN 'Despesa Variável'";
        $sql.="          WHEN l.tipo_lancamento_pk = 4 THEN 'Imposto'";
        $sql.="          WHEN l.tipo_lancamento_pk = 5 THEN 'Transferência'";
        $sql.="          WHEN l.tipo_lancamento_pk = 6 THEN 'Caixinha'";
        $sql.="          WHEN l.tipo_lancamento_pk = 7 THEN 'Custo Fixo'";
        $sql.="          END ds_operacao";
        $sql.="       ,l.metodos_pagamento_pk";
        $sql.="       ,l.empresa_lancamento_pk";
        $sql.="       ,l.contas_bancarias_pk";
        $sql.="       ,l.vl_lancamento";
        $sql.="       ,l.posto_trabalho_lancamento_pk";
        $sql.="       ,l.cliente_lancamento_pk";
        $sql.="       ,l.contratos_pk";
        $sql.="       ,date_format(l.dt_vencimento,'%d/%m/%Y') dt_vencimento";
        $sql.="       ,date_format(l.dt_pagamento,'%d/%m/%Y') dt_pagamento";
        $sql.="       ,l.ic_parcela";
        $sql.="       ,l.grupo_lancamento_pk";
        $sql.="       ,CASE";
        $sql.="          WHEN l.tipo_grupo_lancamento_pk = 1 THEN 'Cliente(s)'";
        $sql.="          WHEN l.tipo_grupo_lancamento_pk = 2 THEN 'Colaboradores'";
        $sql.="          WHEN l.tipo_grupo_lancamento_pk = 3 THEN 'Fornecedores'";
        $sql.="          END ds_tipo_grupo";
        $sql.="       ,l.tipo_grupo_lancamento_pk";
        $sql.="       ,l.tipo_lancamento_pk operacao_pk";
        $sql.="       ,l.grupo_lancamento_pk";
        $sql.="       ,u.ds_usuario";
        $sql.="       ,mp.ds_metodo_pagamento";
        $sql.="       ,c.ds_conta";
        $sql.="       ,cb.vl_saldo_inicial";
        $sql.="       ,le.ds_lead ds_cliente ";
        $sql.="       ,top.ds_tipo_operacao";
        $sql.="       ,co.ds_razao_social";
        $sql.="       ,l.obs_lancamento";
        $sql.="       ,l.empresa_lancamento_pk empresas_pk";
        $sql.="       ,l.grupo_lancamento_pk grupo_lancamento_centro_custo_pk";
        $sql.="        ,date_format(l.dt_faturamento, '%d/%m/%Y') dt_faturamento";
        $sql.="       ,o.ds_ocorrencia";
        $sql.="       ,CASE WHEN l.ic_status_lancamento = 1 THEN 'PAGO'";
        $sql.="           WHEN l.ic_status_lancamento = 2 THEN 'PENDENTE'";
        $sql.="           WHEN l.ic_status_lancamento = 3 THEN 'APROVADO'";
        $sql.="           WHEN l.ic_status_lancamento = 4 THEN 'ATRASADO'";
        $sql.="           WHEN l.ic_status_lancamento = 5 THEN 'CANCELADO'";
        $sql.="       END ds_status_pagamento";
        $sql.="       ,l.ic_status_lancamento";
        $sql.="       ,concat(cb.ds_conta_bancaria, ' - Agência: ', cb.ds_agencia, ' - Conta: ', cb.ds_conta) ds_conta_bancaria";
        $sql.="  from analise_financeira af";
        $sql.="  INNER JOIN lancamentos_financeiros l on af.lancamentos_pk = l.pk";
        $sql.="  LEFT JOIN analise_financeira_processos afp ON afp.analise_financeira_pk = af.pk";
        $sql.="  LEFT JOIN usuarios u ON l.usuario_cadastro_pk = u.pk";
        $sql.="  LEFT JOIN metodos_pagamento mp ON l.metodos_pagamento_pk = mp.pk";
        $sql.="  LEFT JOIN contas c ON l.empresa_lancamento_pk = c.pk";
        $sql.="  LEFT JOIN contas_bancarias cb ON l.contas_bancarias_pk = cb.pk";
        $sql.="  LEFT join contas co on l.empresa_lancamento_pk = co.pk";
        $sql.="  LEFT JOIN leads le on l.cliente_lancamento_pk = le.pk and le.ic_cliente = 1 AND le.ic_tipo_lead = 1";    
        $sql.="  LEFT JOIN ocorrencias o on o.leads_pk = l.pk ";
        $sql.="  LEFT JOIN tipos_operacao top ON l.tipos_operacao_pk = top.pk";
        $sql.=" where af.pk = $pk ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if($rows[0]['tipo_grupo_lancamento_pk']==1){
            $queryLead = (new Lancamento($this->pdo))->listaItensGrupoLeads($rows[0]['grupo_lancamento_pk']);
            $ds_recebido_de = $queryLead->data[0]['ds_lead'];   
        }
        else if($rows[0]['tipo_grupo_lancamento_pk']==2){
            $queryLead = (new Lancamento($this->pdo))->listaItensGrupoColaboradores($rows[0]['grupo_lancamento_pk']);
            $ds_recebido_de = $queryLead->data[0]['ds_colaborador'];
            $ds_agencia = $queryLead->data[0]['ds_agencia'];         
            $ds_conta = $queryLead->data[0]['ds_conta'];            
            $ds_digito = $queryLead->data[0]['ds_digito'];             
            $ds_banco = $queryLead->data[0]['ds_banco'];
            $ds_pix = $queryLead->data[0]['ds_pix'];
            $ds_favorecido_pix = $queryLead->data[0]['ds_conta_favorecido'];
        }
        else if($rows[0]['tipo_grupo_lancamento_pk']==3){
            $queryLead = (new Lancamento($this->pdo))->listaItensGrupoFornecedores($rows[0]['grupo_lancamento_pk']);
            $ds_recebido_de = $queryLead->data[0]['ds_fornecedor'];
            $ds_agencia = $queryLead->data[0]['ds_agencia'];         
            $ds_conta = $queryLead->data[0]['ds_conta'];            
            $ds_digito = $queryLead->data[0]['ds_digito'];             
            $ds_banco = $queryLead->data[0]['ds_banco'];
            $ds_pix = $queryLead->data[0]['ds_pix'];
            $ds_favorecido_pix = $queryLead->data[0]['ds_favorecido_pix'];
        }

        
        //Posto de trabalho
        $lancamento_posto_trabalho_pk = $rows[0]['posto_trabalho_lancamento_pk'];
        
        $queryLead = (new Lancamento($this->pdo))->listaItensGrupoLeads($rows[0]['grupo_lancamento_pk']);
        //CENTRO CUSTO
        if(isset($queryLead->data[0])){
            if($rows[0]['tipo_grupo_lancamento_pk']==1){
                $ds_recebido_de_centro_custo = $queryLead->data[0]['ds_lead'];
            }else if($rows[0]['tipo_grupo_lancamento_pk']==2){
                $ds_recebido_de_centro_custo = $queryLead->data[0]['ds_lead'];
            }else if($rows[0]['tipo_grupo_lancamento_pk']==3){
                $ds_recebido_de_centro_custo = $queryLead->data[0]['ds_lead'];
            }else if($rows[0]['tipo_grupo_lancamento_pk']==4){
                $ds_recebido_de_centro_custo = $queryLead->data[0]['ds_equipe'];
            }
        }
        else{
            $ds_recebido_de_centro_custo ="";
        }

        
        if(!empty($lancamento_posto_trabalho_pk)){
            $queryPostoTrabalho = (new Lancamento($this->pdo))->listaItensGrupoLeads($lancamento_posto_trabalho_pk );
            $ds_lancamento_posto_trabalho = $queryPostoTrabalho->data[0]['ds_lead'];              
        }

        //Contratos
        $lancamento_contrato_pk = "";
        $ds_produto_servico = "";               
        $lancamento_contrato_pk = $rows[0]['contratos_pk'];
        
        $ds_lancamento_contrato= "";
        if(!empty($lancamento_contrato_pk)){
            $queryContrato = (new Lancamento($this->pdo))->listarcontratos($lancamento_contrato_pk);
            $ds_lancamento_contrato = $queryContrato->data[0]['ds_contrato'];  
            $queryProdutoServico = (new ProdutoServico($this->pdo))->listarProdutosContrato($lancamento_contrato_pk);
            $ds_produto_servico =  $queryProdutoServico->data[0]['ds_produto_servico'];   

            if(!empty($ds_produto_servico)){
                $ds_lancamento_contrato  = $ds_lancamento_contrato." Serviço:".$ds_produto_servico; 
            }                                   
        }

        $mysql_data[] = array(
            "lancamentos_pk" => $rows[0]["lancamentos_pk"],
            "ds_agencia"=>$ds_agencia,
            "ds_conta"=>$ds_conta,
            "ds_digito"=>$ds_digito,
            "ds_banco"=>$ds_banco,
            
            "dt_vencimento"=>$rows[0]['dt_vencimento'],
            "ds_usuario_cadastro"=>$rows[0]['ds_usuario'],
            "dt_cadastro"=>$rows[0]['dt_cadastro'],
            "vl_inicial_conta"=>$rows[0]['vl_saldo_inicial'],
            "dt_pagamento"=>$rows[0]['dt_pagamento'],
            "vl_saldo"=>number_format((($rows[0]['vl_lancamento']-$rows[0]['vl_lancamento']) ),2,",","."),
            "ds_lancamento"=>$rows[0]['ds_lancamento'],
            "vl_lancamento"=>($rows[0]['vl_lancamento']),
            "operacao_pk"=>$rows[0]['operacao_pk'],
            "ds_operacao"=>$rows[0]['ds_operacao'],
            "ds_tipo_operacao"=>$rows[0]['ds_tipo_operacao'],
            "tipo_grupo_lancamento_pk"=>$rows[0]['tipo_grupo_lancamento_pk'],
            "ds_tipo_grupo"=>$rows[0]['ds_tipo_grupo'],
            //"ds_tipo_grupo_centro_custo"=>$rows[0]['ds_tipo_grupo_centro_custo'],
            "grupo_lancamento_pk"=>$rows[0]['grupo_lancamento_pk'],
            "ic_status_pagamento"=>$rows[0]['ic_status_lancamento'],
            "ds_status_pagamento"=>$rows[0]['ds_status_pagamento'],
            "obs_lancamento"=>$rows[0]['obs_lancamento'],
            "contas_bancarias_pk"=>$rows[0]['contas_bancarias_pk'],
            "tipos_operacao_pk"=>$rows[0]['tipos_operacao_pk'],
            "metodos_pagamento_pk"=>$rows[0]['metodos_pagamento_pk'],
            "ds_metodo_pagamento"=>$rows[0]['ds_metodo_pagamento'],
            "ds_conta_bancaria"=>$rows[0]['ds_conta_bancaria'],
            "ds_tipo_operacao"=>$rows[0]['ds_tipo_operacao'],
            "empresas_pk"=>$rows[0]['empresas_pk'],
            "ds_razao_social"=>$rows[0]['ds_razao_social'],
            "ds_dados_conta"=>$rows[0]['ds_conta_bancaria'],
            "ds_pix"=>$ds_pix,
            "ds_favorecido_pix"=>$ds_favorecido_pix,
            "grupo_lancamento_centro_custo_pk"=>$rows[0]['grupo_lancamento_centro_custo_pk'],
            "ds_ocorrencia"=>$rows[0]['ds_ocorrencia'],
            "dt_pagamento"=>$rows[0]['dt_pagamento'],
            "contratos_pk"=>$rows[0]['contratos_pk'],
            "ds_usuario"=>$rows[0]['ds_usuario'],
            "dt_faturamento"=>$rows[0]['dt_faturamento'],
            "parcela_pk"=>$rows[0]['ic_parcela'],
            "ds_recebido_de"=>$ds_recebido_de,
            "ds_recebido_de_centro_custo"=>$ds_recebido_de_centro_custo,
            "ds_cliente"=>$rows[0]['ds_cliente'],
            "ds_lancamento_posto_trabalho"=>$ds_lancamento_posto_trabalho,   
            "obs"=>$rows[0]['obs'],   
            "ds_lancamento_contrato"=>$ds_lancamento_contrato
        );
        

        $retorno->data = $mysql_data;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function listarDadosAnaliseFinanceira($pk){
        
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,lancamentos_pk ";
        $sql.="       ,usuario_cadastro_lancamento_pk ";
        $sql.="       ,ic_status ";
        $sql.="       ,gestor_aprovacao_pk ";
        $sql.="       ,obs ";
        $sql.="       ,dt_cancelamento ";

        $sql.="  from analise_financeira ";
        $sql.=" where pk = $pk ";
        $sql.=" order by lancamentos_pk asc ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;

    }
    
}
