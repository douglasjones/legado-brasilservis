<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class TetoGasto {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function excluir($pk){
        Util::execDelete('teto_gastos_itens', ' teto_gastos_pk='.$pk, $this->pdo);
        Util::execDelete('teto_gastos', ' pk='.$pk, $this->pdo);
    }

    public function listarGrid($tipo_grupo_pk, $posto_trabalho_pk, $contratos_pk, $grupo_leancamento_pk, $ds_ano_vigente_teto, $ic_status, $grupo_lancamento_centro_custo_pk){
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
                            tg.pk LIKE '%".$pesq."%' OR
                            le.ds_lead LIKE '%".$pesq."%' OR
                            l.ds_lead LIKE '%".$pesq."%' OR
                            c.pk LIKE '%".$pesq."%' OR
                            tg.ds_ano_vigente_teto LIKE '%".$pesq."%' 
                        )";
        }
        $sql ="";
        $sql.=" SELECT  tg.pk t_pk,";
        $sql.="         tg.tipo_grupo_pk t_tipo_grupo_pk,";
        $sql.="         tg.vl_total_teto t_vl_total_teto,";
        $sql.="         tg.vl_utilizado_atual t_vl_utilizado_atual,";
        $sql.="         tg.ic_status,";
        $sql.="         tg.ds_ano_vigente_teto t_ds_ano_vigente_teto,";
        $sql.="         tg.grupo_lancamento_centro_custo_pk, tg.contratos_pk , l.ds_lead,";
        $sql.="         CASE";
        $sql.="         WHEN c.ic_tipo_contrato = 1 THEN CONCAT('FIXO',  ' - Cód:', c.pk,  ' - Periódo:', DATE_FORMAT(c.dt_inicio_contrato, '%d/%m/%Y'), ' - ', DATE_FORMAT(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="         WHEN c.ic_tipo_contrato = 2 THEN CONCAT('Aditivo',  ' - Cód:', c.pk, ' - Periódo:',  DATE_FORMAT(c.dt_inicio_contrato, '%d/%m/%Y'), ' - ', DATE_FORMAT(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="         WHEN c.ic_tipo_contrato = 3 THEN CONCAT('EXTRA :', c.ds_identificacao_area)";
        $sql.="          END t_contratos_pk,";
        $sql.="         CASE
                        WHEN tg.tipo_grupo_pk = 1 THEN 'Cliente'
                        WHEN tg.tipo_grupo_pk = 2 THEN 'Colaborador'
                        WHEN tg.tipo_grupo_pk = 3 THEN 'Fornecedor'
                        END t_tipo_grupo_pk , ";
        $sql.="         CASE
                        WHEN tg.ic_status = 1 THEN 'Ativo'
                        WHEN tg.ic_status = 2 THEN 'Inativo'
                        END t_ic_status, ";
        $sql.="         tg.grupo_leancamento_pk,";
        $sql.="         le.ds_lead ds_grupo_lancamento, ";
        $sql.="         l.ds_lead t_leads_posto_trabalho_pk ";
        $sql.="  FROM teto_gastos tg";
        $sql.="  LEFT JOIN leads l ON tg.leads_posto_trabalho_pk  = l.pk AND l.ic_tipo_lead = 2 ";
        $sql.="  LEFT JOIN contratos c ON tg.contratos_pk = c.pk";
        $sql.="  LEFT JOIN processos_etapas pe ON c.processos_etapas_pk = pe.pk";
        $sql.="  LEFT JOIN processos p ON pe.processos_pk = p.pk";
        $sql.="  LEFT JOIN leads le ON tg.grupo_leancamento_pk = le.pk AND le.ic_tipo_lead = 1";
        $sql.=" where 1=1";
        $sql.=$search;
        if($tipo_grupo_pk == 1 || $tipo_grupo_pk == ""){
            $sql.="     and tg.tipo_grupo_pk = 1";
            if($posto_trabalho_pk != ""){
                $sql.="     and tg.leads_posto_trabalho_pk = $posto_trabalho_pk";
            }
            if($contratos_pk != ""){
                $sql.="     and tg.contratos_pk = $contratos_pk";
            }
            if($grupo_leancamento_pk != ""){
                $sql.="     and tg.grupo_leancamento_pk = $grupo_leancamento_pk";
            }
            if($ds_ano_vigente_teto != ""){
                $sql.="     and tg.ds_ano_vigente_teto like '%$ds_ano_vigente_teto%'";
            }
            if($ic_status != ""){
                $sql.="     and tg.ic_status = $ic_status";
            }
        }else{
            $sql.="     and tg.tipo_grupo_pk = 0";
        }
        $sql.=" union ";
        $sql.=" SELECT  tg.pk t_pk,";
        $sql.="         tg.tipo_grupo_pk t_tipo_grupo_pk,";
        $sql.="         tg.vl_total_teto t_vl_total_teto,";
        $sql.="         tg.vl_utilizado_atual t_vl_utilizado_atual,";
        $sql.="         tg.ic_status t_ic_status,";
        $sql.="         tg.ds_ano_vigente_teto t_ds_ano_vigente_teto,";
        $sql.="         tg.grupo_lancamento_centro_custo_pk, tg.colaborador_contratos_pk t_contratos_pk, l.ds_lead,";
        $sql.="         CASE";
        $sql.="         WHEN c.ic_tipo_contrato = 1 THEN CONCAT('FIXO',  ' - Cód:', c.pk,  ' - Periódo:', DATE_FORMAT(c.dt_inicio_contrato, '%d/%m/%Y'), ' - ', DATE_FORMAT(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="         WHEN c.ic_tipo_contrato = 2 THEN CONCAT('Aditivo',  ' - Cód:', c.pk, ' - Periódo:',  DATE_FORMAT(c.dt_inicio_contrato, '%d/%m/%Y'), ' - ', DATE_FORMAT(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="         WHEN c.ic_tipo_contrato = 3 THEN CONCAT('EXTRA :', c.ds_identificacao_area)";
        $sql.="          END t_contratos_pk,";
        $sql.="         CASE
                        WHEN tg.tipo_grupo_pk = 1 THEN 'Cliente'
                        WHEN tg.tipo_grupo_pk = 2 THEN 'Colaborador'
                        WHEN tg.tipo_grupo_pk = 3 THEN 'Fornecedor'
                        END t_tipo_grupo_pk, ";
        $sql.="          CASE
                        WHEN tg.ic_status = 1 THEN 'Ativo'
                        WHEN tg.ic_status = 2 THEN 'Inativo'
                        END t_ic_status, ";                
        $sql.="         tg.grupo_leancamento_pk ,";
        $sql.="         co.ds_colaborador ds_grupo_lancamento, ";
        $sql.="         l.ds_lead t_leads_posto_trabalho_pk ";
        $sql.="  FROM teto_gastos tg";
        $sql.="  LEFT JOIN leads l ON tg.colaborador_posto_trabalho_pk = l.pk ";
        $sql.="  LEFT JOIN contratos c ON tg.colaborador_contratos_pk = c.pk";
        $sql.="  LEFT JOIN processos_etapas pe ON c.processos_etapas_pk = pe.pk";
        $sql.="  LEFT JOIN processos p ON pe.processos_pk = p.pk";
        $sql.="  LEFT JOIN colaboradores co ON tg.grupo_leancamento_pk = co.pk";
        $sql.=" where 1=1";
        if($tipo_grupo_pk == 2 || $tipo_grupo_pk == ""){
            $sql.="     and tg.tipo_grupo_pk = 2";
            if($posto_trabalho_pk != ""){
                $sql.="     and tg.leads_posto_trabalho_pk  = $posto_trabalho_pk";
            }
            if($contratos_pk != ""){
                $sql.="     and tg.contratos_pk = $contratos_pk";
            }
            if($grupo_leancamento_pk != ""){
                $sql.="     and tg.grupo_leancamento_pk = $grupo_leancamento_pk";
            }
            if($ds_ano_vigente_teto != ""){
                $sql.="     and tg.ds_ano_vigente_teto like '%$ds_ano_vigente_teto%'";
            }
            if($ic_status != ""){
                $sql.="     and tg.ic_status = $ic_status";
            }
        }else{
            $sql.="     and tg.tipo_grupo_pk = 0";
        }
        $sql.=" union";
        $sql.=" SELECT  tg.pk t_pk,";
        $sql.="         tg.tipo_grupo_pk t_tipo_grupo_pk,";
        $sql.="         tg.vl_total_teto t_vl_total_teto,";
        $sql.="         tg.vl_utilizado_atual t_vl_utilizado_atual,";
        $sql.="         tg.ic_status t_ic_status,";
        $sql.="         tg.ds_ano_vigente_teto t_ds_ano_vigente_teto,";
        $sql.="         tg.grupo_lancamento_centro_custo_pk, tg.fornecedor_posto_trabalho_pk, l.ds_lead,";
        $sql.="         CASE";
        $sql.="         WHEN c.ic_tipo_contrato = 1 THEN CONCAT('FIXO',  ' - Cód:', c.pk,  ' - Periódo:', DATE_FORMAT(c.dt_inicio_contrato, '%d/%m/%Y'), ' - ', DATE_FORMAT(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="         WHEN c.ic_tipo_contrato = 2 THEN CONCAT('Aditivo',  ' - Cód:', c.pk, ' - Periódo:',  DATE_FORMAT(c.dt_inicio_contrato, '%d/%m/%Y'), ' - ', DATE_FORMAT(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="         WHEN c.ic_tipo_contrato = 3 THEN CONCAT('EXTRA :', c.ds_identificacao_area)";
        $sql.="          END t_contratos_pk,"; 
        $sql.="         CASE
                        WHEN tg.tipo_grupo_pk = 1 THEN 'Cliente'
                        WHEN tg.tipo_grupo_pk = 2 THEN 'Colaborador'
                        WHEN tg.tipo_grupo_pk = 3 THEN 'Fornecedor'
                        END t_tipo_grupo_pk, ";
        $sql.="          CASE
                        WHEN tg.ic_status = 1 THEN 'Ativo'
                        WHEN tg.ic_status = 2 THEN 'Inativo'
                        END t_ic_status , ";   
        $sql.="         tg.grupo_leancamento_pk ,";
        $sql.="         f.ds_fornecedor ds_grupo_lancamento, ";
        $sql.="         l.ds_lead t_leads_posto_trabalho_pk ";
        $sql.=" FROM teto_gastos tg";
        $sql.=" LEFT JOIN leads l ON tg.fornecedor_posto_trabalho_pk = l.pk ";
        $sql.=" LEFT JOIN contratos c ON tg.fornecedor_contratos_pk = c.pk";
        $sql.=" LEFT JOIN processos_etapas pe ON c.processos_etapas_pk = pe.pk";
        $sql.=" LEFT JOIN processos p ON pe.processos_pk = p.pk";
        $sql.=" LEFT JOIN fornecedor f ON tg.grupo_leancamento_pk = f.pk";
        $sql.=" where 1=1";
        if($tipo_grupo_pk == 3 || $tipo_grupo_pk == ""){
            $sql.="     and tg.tipo_grupo_pk = 3";
            if($posto_trabalho_pk != ""){
                $sql.="     and tg.leads_posto_trabalho_pk = $posto_trabalho_pk";
            }
            if($grupo_lancamento_centro_custo_pk != ""){
                $sql.="     and tg.grupo_lancamento_centro_custo_pk = $grupo_lancamento_centro_custo_pk";
            }
            if($contratos_pk != ""){
                $sql.="     and tg.contratos_pk = $contratos_pk";
            }
            if($grupo_leancamento_pk != ""){
                $sql.="     and tg.grupo_leancamento_pk = $grupo_leancamento_pk";
            }
            if($ds_ano_vigente_teto != ""){
                $sql.="     and tg.ds_ano_vigente_teto like '%$ds_ano_vigente_teto%'";
            }
            if($ic_status != ""){
                $sql.="     and tg.ic_status = $ic_status";
            }
        }else{
            $sql.="     and tg.tipo_grupo_pk = 0";
        }
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

    public function salvar($teto_gasto){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
       
        $fields = array();
        $fields['empresas_pk'] = $teto_gasto['empresas_pk'];
        $fields['tipo_grupo_pk'] = $teto_gasto['tipo_grupo_pk'];
        $fields['grupo_leancamento_pk'] = $teto_gasto['grupo_leancamento_pk'];
        $fields['leads_posto_trabalho_pk'] = $teto_gasto['leads_posto_trabalho_pk'];
        $fields['contratos_pk'] = $teto_gasto['contratos_pk'];
        $fields['colaborador_posto_trabalho_pk'] = $teto_gasto['colaborador_posto_trabalho_pk'];
        $fields['colaborador_contratos_pk'] = $teto_gasto['colaborador_contratos_pk'];
        $fields['fornecedor_posto_trabalho_pk'] = $teto_gasto['fornecedor_posto_trabalho_pk'];
        $fields['fornecedor_contratos_pk'] = $teto_gasto['fornecedor_contratos_pk'];
        $fields['vl_total_teto'] = $teto_gasto['vl_total_teto'];
        $fields['vl_utilizado_atual'] = '0.00';
        $fields['ic_status'] = $teto_gasto['ic_status'];
        $fields['obs'] = $teto_gasto['obs'];
        $fields['ds_ano_vigente_teto'] = $teto_gasto['ds_ano_vigente_teto'];
        $fields['grupo_lancamento_centro_custo_pk'] = $teto_gasto['grupo_lancamento_centro_custo_pk'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($teto_gasto['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("teto_gastos", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("teto_gastos", $fields, " pk = ".$teto_gasto['pk'],$this->pdo);
            $pk = $teto_gasto['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;
    }

    public function listarPorPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql.="       ,empresas_pk ";
        $sql.="       ,tipo_grupo_pk ";
        $sql.="       ,grupo_leancamento_pk ";
        $sql.="       ,leads_posto_trabalho_pk ";
        $sql.="       ,contratos_pk ";
        $sql.="       ,colaborador_posto_trabalho_pk ";
        $sql.="       ,colaborador_contratos_pk ";
        $sql.="       ,fornecedor_posto_trabalho_pk ";
        $sql.="       ,fornecedor_contratos_pk ";
        $sql.="       ,vl_total_teto ";
        $sql.="       ,ic_status ";
        $sql.="       ,obs ";
        $sql.="       ,ds_ano_vigente_teto ";
        $sql.="       ,grupo_lancamento_centro_custo_pk ";
        $sql.="       ,vl_utilizado_atual ";

        $sql.="  from teto_gastos ";
        $sql.=" where pk = $pk ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }
}
