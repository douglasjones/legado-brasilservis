<?php

namespace App\Model;

use App\Utils\Util;
use App\Model\Lancamento;
use GuzzleHttp\Client;

class Faturamento{

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function salvar($faturamento){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['dt_faturamento_ini'] = Util::DataYMD($faturamento['dt_faturamento_ini']);
        $fields['dt_faturamento_fim'] = Util::DataYMD($faturamento['dt_faturamento_fim']);
        $fields['ic_contrato_fixo'] = $faturamento['ic_contrato_fixo'];
        $fields['ic_contrato_aditivo'] = $faturamento['ic_contrato_aditivo'];
        $fields['ic_contrato_servico_extra'] = $faturamento['ic_contrato_servico_extra'];
        $fields['ic_gerar_boleto'] = $faturamento['ic_gerar_boleto'];
        $fields['ic_gerar_boleto'] = $faturamento['ic_gerar_boleto'];
        $fields['ic_gerar_nota_fiscal'] = $faturamento['ic_gerar_nota_fiscal'];
        $fields['ic_gerar_nota_fatura'] = $faturamento['ic_gerar_nota_fatura'];
        $fields['obs'] = $faturamento['obs'];
        $fields['ic_status'] = $faturamento['ic_status'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($faturamento['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("faturamento", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }else{
            Util::execUpdate("faturamento", $fields, " pk = ".$faturamento['pk'],$this->pdo);
            $pk = $faturamento['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }

        $this->excluirFaturamentoContas($pk);

        $arrConta = explode(',', $faturamento['arrConta']);
        for($i=0; $i<count($arrConta); $i++){
            $conta_pk = $arrConta[$i];
            if($conta_pk > 0){
                $this->salvarFaturamentoContas($pk, $conta_pk);
            }
        }
        return $retorno;
    }

    //CONTAS FATURAMENTO
    public function excluirFaturamentoContas($pk){
        Util::execDelete("faturamento_contas" , " faturamento_pk = ".$pk, $this->pdo);
    }

    //Excluir
    public function excluir($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql = 'select pk from faturamento_contratos where faturamento_pk = '.$pk;
        $query = $this->pdo->prepare( $sql );
        $query->execute();
        $query = $query->fetchAll(\PDO::FETCH_ASSOC); 
        
        for($i=0;$i<count($query);$i++){
            Util::execDelete("faturamento_contratos_itens" , "faturamento_contratos_pk = ".$query[$i]['pk'], $this->pdo);
        }
        Util::execDelete("faturamento_contratos" , " faturamento_pk = ".$pk, $this->pdo);
        Util::execDelete("faturamento_itens" , " faturamento_pk = ".$pk, $this->pdo);
        Util::execDelete("faturamento" , " pk = ".$pk, $this->pdo);
        $retorno->status = true;
        $retorno->message = 'Dados atualizado com sucesso';
        $retorno->data = $pk;
        return $retorno;
    }

    public function salvarFaturamentoContas($faturamento_pk,$contas_pk){        

        $fields["faturamento_pk"]   = $faturamento_pk;
        $fields["contas_pk"]   = $contas_pk;
        $fields["ic_status"]   = 1;

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
        $fields["dt_cadastro"] = "sysdate()";
        $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
        
        Util::execInsert("faturamento_contas", $fields, $this->pdo);
    }

    public function cancelarFaturamento($faturamento_pk){   
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio     

        $sql='';
        $sql.='SELECT lancamentos_pk FROM faturamento_itens WHERE lancamentos_pk <> "" and faturamento_pk ='.$faturamento_pk;
        $query = $this->pdo->prepare($sql);  
        $query->execute();
        $query = $query->fetchAll(\PDO::FETCH_ASSOC); 

        for($i=0;$i<count($query);$i++){
            $fields = array();
            $lancamento_pk = $query[$i]['lancamentos_pk'];
            $fields["ic_status_lancamento"]  = 5;

            $fields["dt_ult_atualizacao"] = "sysdate()";
            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
            
            Util::execUpdate("lancamentos_financeiros", $fields, " pk = ".$lancamento_pk,$this->pdo);
        }

        $fieldsFaturamento = array();
        $fieldsFaturamento["ic_status"] = 3;

        $fieldsFaturamento["dt_ult_atualizacao"] = "sysdate()";
        $fieldsFaturamento["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
        
        Util::execUpdate("faturamento", $fieldsFaturamento, " pk = ".$faturamento_pk,$this->pdo);
        
        $retorno->status = true;
        $retorno->message = 'Dados atualizados com sucesso';
        $retorno->data = $faturamento_pk;

        return $retorno;
    }

    public function salvarItens($jsonItens, $JsonDadosNfse){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        
        $arrItens = json_decode($jsonItens, true);
        $arrDadosNfse = json_decode($JsonDadosNfse, true);
        
        $pk = '';
        
        for($i=0;$i<count($arrItens);$i++){
            $fields = array();

            $fields['vl_total_lancamento'] = $arrItens[$i]['vl_total_lancamento'];
            $fields['faturamento_pk'] = $arrItens[$i]['faturamento_pk'];
            $fields['contas_pk'] = $arrItens[$i]['contas_pk'];
            $fields['leads_pk'] = $arrItens[$i]['leads_pk'];
            $fields['contratos_pk'] = $arrItens[$i]['contratos_pk'];
            if(isset($arrItens[$i]['dt_lancamento_financeiro'])){
                $fields['dt_lancamento_financeiro'] = $arrItens[$i]['dt_lancamento_financeiro'];
            }
            if(isset( $arrItens[$i]['ic_item_validado'])){
                $fields['ic_item_validado'] = $arrItens[$i]['ic_item_validado'];
            }
            if(isset( $arrItens[$i]['ic_item_validado'])){
                $fields['dt_item_valiado'] = $arrItens[$i]['dt_item_valiado'];
            }
            if(isset( $arrItens[$i]['lancamentos_pk'])){
                $fields['lancamentos_pk'] = $arrItens[$i]['lancamentos_pk'];
            }
            if(isset( $arrItens[$i]['ic_status'])){
                $fields['ic_status'] = $arrItens[$i]['ic_status'];
            }
            if(isset( $arrItens[$i]['ic_processamento_lancamento_item_status'])){
                $fields['ic_processamento_lancamento_item_status'] = $arrItens[$i]['ic_processamento_lancamento_item_status'];
            }
            if(isset( $arrItens[$i]['dt_processamento_lancamento_lancamento'])){
                $fields['dt_processamento_lancamento_lancamento'] = $arrItens[$i]['dt_processamento_lancamento_lancamento'];
            }
            $fields['obs_faturamento_contrato'] = $arrItens[$i]['obs_faturamento'];
            $fields['obs_lancamento'] = $arrItens[$i]['obs_lancamento'];
            $fields['obs_corpo_nota'] = $arrItens[$i]['obs_corpo_nota'];
    
    
            $fields["dt_ult_atualizacao"] = "sysdate()";
            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
    
            if($arrItens[$i]['faturamento_itens_pk'] == ""){
    
                $fields["dt_cadastro"] = "sysdate()";
                $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
    
                $pk = Util::execInsert("faturamento_itens", $fields,$this->pdo);
            }
            else{
                Util::execUpdate("faturamento_itens", $fields, " pk = ".$arrItens[$i]['faturamento_itens_pk'],$this->pdo);
                $pk = $arrItens[$i]['faturamento_itens_pk'];
                
            }
        
            $this->salvarDadosNfse( $arrDadosNfse[$i]['prestador_pk'],
                                    $arrDadosNfse[$i]['pk'],
                                    $arrDadosNfse[$i]['fazer_nfse'],
                                    $arrDadosNfse[$i]['faturamento_pk'],
                                    $arrDadosNfse[$i]['tomador_pk'],
                                    $arrDadosNfse[$i]['ds_cpf_cnpj'],
                                    $arrDadosNfse[$i]['ds_descricao_servico'],
                                    $arrDadosNfse[$i]['vl_aliquota'],
                                    $arrDadosNfse[$i]['faturamento_nfse_servicos_pk'],
                                    $arrDadosNfse[$i]['vl_total_servico'],
                                    $arrDadosNfse[$i]['iss_aliquota'],
                                    $arrDadosNfse[$i]['iss_valor'],
                                    $arrDadosNfse[$i]['inss_aliquota'],
                                    $arrDadosNfse[$i]['inss_valor'],
                                    $arrDadosNfse[$i]['pis_aliquota'],
                                    $arrDadosNfse[$i]['pis_valor'],
                                    $arrDadosNfse[$i]['cofins_aliquota'],
                                    $arrDadosNfse[$i]['cofins_valor'],
                                    $arrDadosNfse[$i]['ir_aliquota'],
                                    $arrDadosNfse[$i]['ir_valor'],
                                    $arrDadosNfse[$i]['csll_aliquota'],
                                    $arrDadosNfse[$i]['csll_valor'],
                                    $arrDadosNfse[$i]['iss_retido_tomador'],
                                    $arrDadosNfse[$i]['descricao_nfse'],
                                    $arrDadosNfse[$i]['descricao_nfse_pk'],
                                    $pk                                                                        
                                );
        };

        $retorno->status = true;
        $retorno->message = 'Dados atualizados com sucesso';
        $retorno->data = $pk;

        return $retorno;
        

    }

    public function salvarDadosNfse($prestador_pk,
                                    $pk_nfse,
                                    $fazer_nfse,
                                    $faturamento_pk,
                                    $tomador_pk,
                                    $ds_cpf_cnpj,
                                    $ds_descricao_servico,
                                    $vl_aliquota,
                                    $faturamento_nfse_servicos_pk,
                                    $vl_total_servico,
                                    $iss_aliquota,
                                    $iss_valor,
                                    $inss_aliquota,
                                    $inss_valor,
                                    $pis_aliquota,
                                    $pis_valor,
                                    $cofins_aliquota,
                                    $cofins_valor,
                                    $ir_aliquota,
                                    $ir_valor,
                                    $csll_aliquota,
                                    $csll_valor,
                                    $iss_retido_tomador,
                                    $descricao_nfse,
                                    $descricao_nfse_pk,
                                    $item_pk    
                                ){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        if($descricao_nfse != ''){
            $fieldsDescricao = array();
            $fieldsDescricao['ds_descricao_corpo_nfse'] = $descricao_nfse;
            $fieldsDescricao['ic_status'] = 1;
            $fieldsDescricao["dt_ult_atualizacao"] = "sysdate()";
            $fieldsDescricao["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
            if($pk_nfse == ""){
                $fieldsDescricao["dt_cadastro"] = "sysdate()";
                $fieldsDescricao["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

                $faturamento_nfse_servicos_pk = Util::execInsert("faturamento_nfse_servicos", $fieldsDescricao,$this->pdo);
            }else{
                Util::execUpdate("faturamento_nfse_servicos", $fieldsDescricao, " pk = ".$descricao_nfse_pk,$this->pdo);
            }
        }
        
        if($fazer_nfse == 1){
            $fields = array();

            $fields['prestador_pk'] = $prestador_pk;
            $fields['faturamento_itens_pk'] = $item_pk;
            $fields['tomador_pk'] = $tomador_pk;
            $fields['faturamento_nfse_servicos_pk'] = $faturamento_nfse_servicos_pk;
            $fields['vl_total_servico'] = $vl_total_servico;
            $fields['iss_retido_tomador'] = $iss_retido_tomador;
            $fields['iss_aliquota'] = $iss_aliquota;
            $fields['iss_valor'] = $iss_valor;
            $fields['inss_aliquota'] = $inss_aliquota;
            $fields['inss_valor'] = $inss_valor;
            $fields['pis_aliquota'] = $pis_aliquota;
            $fields['pis_valor'] = $pis_valor;
            $fields['cofins_aliquota'] = $cofins_aliquota;
            $fields['cofins_valor'] = $cofins_valor;
            $fields['ir_aliquota'] = $ir_aliquota;
            $fields['ir_valor'] = $ir_valor;
            $fields['csll_aliquota'] = $csll_aliquota;
            $fields['csll_valor'] = $csll_valor;
            $fields['ic_status'] = 1;

            $fields["dt_ult_atualizacao"] = "sysdate()";
            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

            if($pk_nfse == ""){

                $fields["dt_cadastro"] = "sysdate()";
                $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

                $pk_nfse = Util::execInsert("faturamento_nfse", $fields,$this->pdo);
            }
            else{
                Util::execUpdate("faturamento_nfse", $fields, " pk = ".$pk_nfse,$this->pdo);
                
            }
        }
            
        
        

        $retorno->status = true;
        $retorno->message = 'Dados atualizados com sucesso';
        $retorno->data = $pk;

        return $retorno;
        

    }

    public function salvarContratos($jsonContratos){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $arrContratos = json_decode($jsonContratos, true);
        $pk = '';
        for($i=0;$i<count($arrContratos);$i++){

            $fields = array();
            $fields['ic_tipo_contrato'] = $arrContratos[$i][0]['ic_tipo_contrato'];
            $fields['contratos_pk'] = $arrContratos[$i][0]['contratos_pk'];
            $fields['leads_pk'] = $arrContratos[$i][0]['leads_pk'];
            $fields['faturamento_pk'] = $arrContratos[$i][0]['faturamento_pk'];
            $fields['ic_gerar_nfse'] = $arrContratos[$i][0]['fazer_nfse'];
            $fields['vl_total_contrato'] = $arrContratos[$i][0]['vl_total_lancamento'];
            $fields['ic_status'] = $arrContratos[$i][0]['ic_status'];
            $fields['obs_corpo_nota_fiscal'] = $arrContratos[$i][0]['obs_corpo_nota_fiscal'];
            if($arrContratos[$i][0]['dt_vencimento']!=""){
                $fields['dt_vencimento'] = Util::DataYMD($arrContratos[$i][0]['dt_vencimento']);
            }
            if($arrContratos[$i][0]['dt_faturamento']!=""){
                $fields['dt_faturamento'] = Util::DataYMD($arrContratos[$i][0]['dt_faturamento']);
            }

            $fields["dt_ult_atualizacao"] = "sysdate()";
            $fields["usuario_ult_atualizacao_pk"] =  $_SESSION['session_user']['par1'];

            if($arrContratos[$i][0]['faturamento_contratos_pk']  == ""){
                $fields["dt_cadastro"] = "sysdate()";
                $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
    
                $pk = Util::execInsert("faturamento_contratos", $fields,$this->pdo);
            }else{
                Util::execUpdate("faturamento_contratos", $fields, " pk = ".$arrContratos[$i][0]['faturamento_contratos_pk'] ,$this->pdo);

                $pk = $arrContratos[$i][0]['faturamento_contratos_pk'];
            }

            for($l=0; $l<count($arrContratos[$i][0]['arrItens']); $l++){
                
                $fieldsItens['produtos_servicos_pk'] = $arrContratos[$i][0]['arrItens'][$l]['produto_servico_pk'];
                $fieldsItens['n_qtde_produtos_servicos'] = $arrContratos[$i][0]['arrItens'][$l]['n_qtde_colaborador'];
                $fieldsItens['vl_unitario_produtos_servicos'] = $arrContratos[$i][0]['arrItens'][$l]['vl_unitario_produtos_servicos'];
                $fieldsItens['ds_periodo'] = $arrContratos[$i][0]['arrItens'][$l]['ds_periodo'];
                $fieldsItens['n_qtde_dias_semana'] = $arrContratos[$i][0]['arrItens'][$l]['n_qtde_dias_semana'];
                $fieldsItens['faturamento_contratos_pk'] = $pk;
                $fieldsItens['contratos_pk'] = $arrContratos[$i][0]['arrItens'][$l]['contratos_pk'];
                $fieldsItens['ic_status'] = 1;
        
                $fieldsItens["dt_ult_atualizacao"] = "sysdate()";
                $fieldsItens["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                
                if($arrContratos[$i][0]['arrItens'][$l]['faturamento_contratos_itens_pk'] == ""){
                    $fieldsItens["dt_cadastro"] = "sysdate()";
                    $fieldsItens["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
        
                    $itens_pk = Util::execInsert("faturamento_contratos_itens", $fieldsItens,$this->pdo);
                }else{
                    Util::execUpdate("faturamento_contratos_itens", $fieldsItens, " pk = ".$arrContratos[$i][0]['arrItens'][$l]['faturamento_contratos_itens_pk'] ,$this->pdo);
                }
            
                $fieldsUpdate = array();
                $fieldsUpdate['vl_total_faturamento'] = $arrContratos[$i][0]['vl_total_geral_faturamento'];
                Util::execUpdate("faturamento", $fieldsUpdate, " pk = ".$arrContratos[$i][0]['faturamento_pk'],$this->pdo);


            }
        }
        if($pk > 0){
            
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        
    }


    public function listar_faturamento($empresas_pk,$ic_status,$dt_faturamento_ini,$dt_faturamento_fim,$tipo_contrato_pk,$n_emissoes){
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

        $sql ="";
        $sql.="select f.pk, date_format(f.dt_cadastro, '%d/%m/%y') dt_cadastro, f.usuario_cadastro_pk, f.dt_ult_atualizacao, f.usuario_ult_atualizacao_pk ";
        $sql.="       ,date_format(f.dt_faturamento_ini, '%d/%m/%y') dt_faturamento_ini ";
        $sql.="       ,date_format(f.dt_faturamento_fim, '%d/%m/%y') dt_faturamento_fim ";
        $sql.="       ,f.ic_contrato_fixo ";
        $sql.="       ,f.ic_contrato_aditivo ";
        $sql.="       ,f.ic_contrato_servico_extra ";
        $sql.="       ,f.ic_gerar_boleto ";
        $sql.="       ,f.ic_gerar_nota_fiscal ";
        $sql.="       ,f.ic_processar_faturamento ";
        $sql.="       ,f.obs ";
        $sql.="       ,f.origem_pk ";
        $sql.="       ,f.ic_status";
        $sql.="       ,f.ic_status";
        $sql.="       ,count(fi.lancamentos_pk) n_emissoes";
        $sql.="       ,vl_total_faturamento";
        $sql.="       ,case when f.ic_status = 1 then 'Faturamento Gerado'";
        $sql.="             when f.ic_status = 2 then 'Faturamento Processado'";
        $sql.="             when f.ic_status = 3 then 'Faturamento Cancelado' end ds_status";

        $sql.="  from faturamento f";
        $sql.="   left join faturamento_itens fi on fi.faturamento_pk = f.pk";
        $sql.=" where 1=1 ";
        /*if($empresas_pk != ""){
            $sql.=" and empresas_pk = ".$empresas_pk;
        }*/
        $sql.=" group by fi.faturamento_pk";
        $sql.=" order by f.pk desc ";

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

    public function listarDadosEmissoes($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio


        $sql='';
        $sql.="select f.pk,"; 
        $sql.="     l.ds_lead,";
        $sql.="     fc.contratos_pk,";
        $sql.="     case";
        $sql.="         when fc.ic_tipo_contrato = 1 then ";
        $sql.="          'Contrato Fixo' ";
        $sql.="         when fc.ic_tipo_contrato = 2 then ";	 
        $sql.="            'Contrato Aditivo' ";
        $sql.="         when fc.ic_tipo_contrato = 3 then ";
        $sql.="            'Serviço Extra' ";
        $sql.="     end as ds_tipo_contrato, ";
        $sql.="     fi.lancamentos_pk , ";
        $sql.="      CASE WHEN la.ic_status_lancamento = 1 THEN 'PAGO'";
        $sql.="           WHEN la.ic_status_lancamento = 2 THEN 'PENDENTE'";
        $sql.="           WHEN la.ic_status_lancamento = 3 THEN 'APROVADO'";
        $sql.="           WHEN la.ic_status_lancamento = 4 THEN 'ATRASADO'";
        $sql.="           WHEN la.ic_status_lancamento = 5 THEN 'CANCELADO'";
        $sql.="       END ds_status_pagamento,";
        $sql.="     DATE_FORMAT(l.dt_cadastro, '%d/%m/%Y') dt_lancamento, ";
        $sql.="     DATE_FORMAT(fc.dt_faturamento, '%d/%m/%Y') dt_faturamento, ";
        $sql.="     DATE_FORMAT(fc.dt_vencimento, '%d/%m/%Y') dt_vencimento, ";
        $sql.="     DATE_FORMAT(f.dt_faturamento_ini, '%d/%m/%Y') dt_faturamento_ini, ";
        $sql.="     DATE_FORMAT(f.dt_faturamento_fim, '%d/%m/%Y') dt_faturamento_fim, ";
        $sql.="     fc.vl_total_contrato ";
        $sql.=" from faturamento f ";
        $sql.="     inner join faturamento_contratos fc on	f.pk = fc.faturamento_pk ";
        $sql.="     inner join leads l on fc.leads_pk = l.pk ";
        $sql.="     inner join faturamento_itens fi on 	f.pk = fi.faturamento_pk and fc.contratos_pk = fi.contratos_pk ";
        $sql.="     inner join lancamentos_financeiros la on fi.lancamentos_pk = la.pk ";
        $sql.=" where f.pk =".$pk;
        $sql.=" group by fc.contratos_pk";
        //echo $sql;

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        echo json_encode($retorno);
        exit(0);
    }

    public function listarDadosFaturamento($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql = "";
        $sql.="SELECT f.pk,";
        $sql.="        date_format(f.dt_cadastro,'%d/%m/%Y') dt_cadastro,";
        $sql.="        f.usuario_cadastro_pk, ";
        $sql.="        date_format(f.dt_ult_atualizacao,'%d/%m/%Y') dt_ult_atualizacao,";
        $sql.="        f.usuario_ult_atualizacao_pk,";
        $sql.="        date_format(f.dt_faturamento_ini,'%d/%m/%Y') dt_faturamento_ini,";
        $sql.="        date_format(f.dt_faturamento_fim,'%d/%m/%Y') dt_faturamento_fim,";
        $sql.="        f.dt_faturamento_ini dt_faturamento_base_ini,";
        $sql.="        f.dt_faturamento_fim dt_faturamento_base_fim,";
        $sql.="        f.ic_contrato_fixo,";
        $sql.="        f.ic_contrato_aditivo,";
        $sql.="        f.ic_contrato_servico_extra,";
        $sql.="        f.ic_gerar_fatura,";
        $sql.="        f.ic_gerar_boleto,";
        $sql.="        f.ic_gerar_nota_fiscal,";
        $sql.="        f.ic_processar_faturamento,";
        $sql.="        f.obs,";
        $sql.="        f.ic_status,";
        $sql.="        u.ds_usuario ds_usuario_cadastro,";
        $sql.="        u1.ds_usuario ds_usuario_atualizacao";
        $sql.="       ,case WHEN f.ic_status = 1  THEN 'Faturamento Gerado' 
                                WHEN f.ic_status = 3  THEN 'Faturamento Cancelado' 
                                WHEN f.ic_status = 2  THEN 'Faturamento Processado' 
                        END ds_usatus_faturamento";
        $sql.=" FROM faturamento f";
        $sql.=" INNER JOIN usuarios u on f.usuario_cadastro_pk = u.pk";
        $sql.=" INNER JOIN usuarios u1 on f.usuario_ult_atualizacao_pk = u1.pk";
        $sql.=" WHERE f.pk =".$pk;

        $queryFatutamento = $this->pdo->prepare($sql);  
        $queryFatutamento->execute();
        $queryFatutamento = $queryFatutamento->fetchAll(\PDO::FETCH_ASSOC); 
   
        //Dados de Contas
        $sql ="";
        $sql.="SELECT fc.pk,";
        $sql.="        c.pk contas_pk,";
        $sql.="        c.ds_conta,";
        $sql.="        c.ds_razao_social,";
        $sql.="        c.ds_cpf_cnpj";
        $sql.=" FROM faturamento_contas fc ";
        $sql.=" INNER JOIN contas c ON fc.contas_pk = c.pk";
        $sql.=" WHERE fc.faturamento_pk = ".$pk;
        $sql.="   AND c.ic_status = 1";
        $sql.=" ORDER BY c.ds_razao_social";
        $queryContas = $this->pdo->prepare($sql);  
        $queryContas->execute();
        $queryContas = $queryContas->fetchAll(\PDO::FETCH_ASSOC); 
        
        if(count($queryContas) > 0){
       
            for($i = 0; $i < count($queryContas); $i++){
                //Dados Contas Empresas
                $DadosContas[] = array(
                    "contas_pk" => $queryContas[$i]["contas_pk"],
                    "ds_conta" => $queryContas[$i]["ds_conta"],
                    "ds_razao_social" => $queryContas[$i]["ds_razao_social"],
                    "ds_cpf_cnpj" => $queryContas[$i]["ds_cpf_cnpj"]
                );

                //CONTRATOS
                $sql = "";
                $sql.="SELECT c.pk contratos_pk,";
                $sql.="        date_format(c.dt_cadastro,'%d/%m/%Y') dt_cadastro,";
                $sql.="        c.ds_identificacao_area,";
                $sql.="        c.ic_tipo_contrato,";
                $sql.="        c.vl_contrato,";
                $sql.="        c.empresas_pk contas_contratos_pk,";
                $sql.="        u.ds_usuario ds_usuario_cadastro_contrato,";
                $sql.="        l.pk leads_pk,";
                $sql.="        l.ds_lead,";
                $sql.="        l.ds_razao_social,";
                $sql.="        le.ds_razao_social ds_razao_social_cliente,";
                $sql.="        l.leads_pai_pk,";
                $sql.="        le.ds_lead ds_cliente,";
                $sql.="        l.ds_cpf_cnpj,";
                $sql.="        le.ds_cpf_cnpj ds_cnpj_cpf_cliente,";
                $sql.="        concat(l.ds_endereco,' - ',l.ds_numero,' - Complemento: ',l.ds_complemento,' - Bairro: ',l.ds_bairro,' - Cidade: ',l.ds_cidade,' - Cep: ',l.ds_cep,' - UF:',l.ds_uf )ds_endereco_lead,";
                $sql.="        concat(le.ds_endereco,' - ',le.ds_numero,' - Complemento: ',le.ds_complemento,' - Bairro: ',le.ds_bairro,' - Cidade: ',le.ds_cidade,' - Cep: ',le.ds_cep,' - UF:',le.ds_uf )ds_endereco_pai_lead,";
                $sql.="         CASE";
                $sql.="             WHEN l.ic_tipo_lead = 1 THEN 'Cliente Matris'";
                $sql.="             ELSE 'Posto de Trabalho'";
                $sql.="         END ds_tipo_lead,";
                $sql.="       date_format(c.dt_inicio_contrato,'%d/%m/%Y') dt_inicio_contrato,";
                $sql.="       date_format(c.dt_fim_contrato,'%d/%m/%Y') dt_fim_contrato";
                $sql.="    FROM contratos c";
                $sql.="        INNER JOIN processos_etapas pe ON c.processos_etapas_pk = pe.pk";
                $sql.="        INNER JOIN processos p ON pe.processos_pk = p.pk";
                $sql.="        INNER JOIN leads l on p.leads_pk = l.pk";
                $sql.="        LEFT JOIN leads le on l.leads_pai_pk = le.pk";
                $sql.="        INNER JOIN usuarios u on c.usuario_cadastro_pk = u.pk"; 
                $sql.="    WHERE c.dt_cancelamento IS NULL ";
                $sql.="    AND c.empresas_pk = ".$queryContas[$i]["contas_pk"];
                $sql.="    AND c.dt_inicio_contrato <= sysdate()"; 
                $sql.="    AND c.dt_fim_contrato >= sysdate()"; 
                $sql.="    AND c.dt_cancelamento is null";
                $sql.="    AND l.ic_cliente=1";
                $sql.="   ORDER BY l.leads_pai_pk, l.ds_lead ";
                $queryContratos = $this->pdo->prepare($sql);  
                $queryContratos->execute();
                $queryContratos = $queryContratos->fetchAll(\PDO::FETCH_ASSOC); 
            
                if(count($queryContratos) > 0){
                    for($h = 0; $h < count($queryContratos); $h++){
                        $ds_ideintificacao_contrato = "";
                        if($queryContratos[$h]["ds_identificacao_area"]!=null){
                            $ds_ideintificacao_contrato = $queryContratos[$h]["ds_identificacao_area"];
                        }
                        $DadosContratos[] = array(
                            "contratos_pk" => $queryContratos[$h]["contratos_pk"],
                            "dt_cadastro" => $queryContratos[$h]["dt_cadastro"],
                            "ds_identificacao_area" => $ds_ideintificacao_contrato,
                            "ic_tipo_contrato" => $queryContratos[$h]["ic_tipo_contrato"],
                            "vl_contrato" => $queryContratos[$h]["vl_contrato"]  == null ? "0,00" : $queryContratos[$h]["vl_contrato"],
                            "contas_contratos_pk" => $queryContratos[$h]["contas_contratos_pk"],
                            "ds_usuario_cadastro_contrato" => $queryContratos[$h]["ds_usuario_cadastro_contrato"],
                            "leads_pai_pk" => $queryContratos[$h]["leads_pai_pk"],
                            "leads_pk" => $queryContratos[$h]["leads_pk"],
                            "ds_lead" => $queryContratos[$h]["ds_lead"],    
                            "ds_razao_social" => $queryContratos[$h]["ds_razao_social"] == null ? $queryContratos[$h]["ds_razao_social_cliente"] : $queryContratos[$h]["ds_razao_social"],
                            "ds_cliente" => $queryContratos[$h]["ds_cliente"],
                            "ds_cpf_cnpj" => $queryContratos[$h]["ds_cpf_cnpj"] == null ? $queryContratos[$h]["ds_cnpj_cpf_cliente"] : $queryContratos[$h]["ds_cpf_cnpj"],
                            "ds_endereco_lead" => $queryContratos[$h]["ds_endereco_lead"] == null ? $queryContratos[$h]["ds_endereco_pai_lead"] : $queryContratos[$h]["ds_endereco_lead"],
                            "ds__tipo_lead" => $queryContratos[$h]["ds_tipo_lead"],
                            "dt_inicio_contrato" => $queryContratos[$h]["dt_inicio_contrato"],
                            "dt_fim_contrato" => $queryContratos[$h]["dt_fim_contrato"]        
                        );
                        //CONTRATOS ITENS
                        $sql ="";
                        $sql.="SELECT ci.pk,";
                        $sql.="    ps.pk produto_servico_pk,";
                        $sql.="    ps.ds_produto_servico,";
                        $sql.="    ci.contratos_pk,";  
                        $sql.="    ci.periodo,";                       
                        $sql.="    ci.n_qtde n_qtde_colaborador,";
                        $sql.="    ci.n_qtde_dias_semana,"; 
                        $sql.="    ci.vl_unit,";
                        $sql.="    ci.vl_total";
                        $sql.=" FROM contratos_itens ci";
                        $sql.="    INNER JOIN produtos_servicos ps ON ci.produtos_servicos_pk = ps.pk";
                        $sql.=" WHERE ci.contratos_pk =".$queryContratos[$h]["contratos_pk"];
                        
                        $queryContratosItens = $this->pdo->prepare($sql);
                        $queryContratosItens->execute();
                        $queryContratosItens = $queryContratosItens->fetchAll(\PDO::FETCH_ASSOC);

                        if(count($queryContratosItens) > 0){
                            for($l = 0; $l < count($queryContratosItens); $l++){
                                $DadosContratosItens[] = array(
                                    "contratos_itens_pk" => $queryContratosItens[$l]["pk"],
                                    "contratos_pk" => $queryContratosItens[$l]["contratos_pk"],
                                    "produto_servico_pk" => $queryContratosItens[$l]["produto_servico_pk"],
                                    "ds_servico_prestado" => $queryContratosItens[$l]["ds_produto_servico"],
                                    "n_qtde_colaborador" => $queryContratosItens[$l]["n_qtde_colaborador"],
                                    "ds_escala" => $queryContratosItens[$l]["n_qtde_dias_semana"],
                                    "ds_carga_horaria_dia" => $queryContratosItens[$l]["periodo"],
                                    "vl_unit" => $queryContratosItens[$l]["vl_unit"],
                                    "vl_total" => $queryContratosItens[$l]["vl_total"],
                                );
                            }  
                        }  
                        
                    }  
                } 
            }
        } 

        $result[] = array(
            "pk" => $pk,
            "ds_usuario_cadastro"=>$queryFatutamento[0]['ds_usuario_cadastro'],
            "ds_usuario_atualizacao"=>$queryFatutamento[0]['ds_usuario_atualizacao'],
            "dt_cadastro"=>$queryFatutamento[0]['dt_cadastro'],
            "dt_ult_atualizacao"=>$queryFatutamento[0]['dt_ult_atualizacao'],
            "dt_faturamento_ini"=>$queryFatutamento[0]['dt_faturamento_ini'],
            "dt_faturamento_fim"=>$queryFatutamento[0]['dt_faturamento_fim'],
            "ds_usatus_faturamento"=>$queryFatutamento[0]['ds_usatus_faturamento'],
            "ic_contrato_fixo"=>$queryFatutamento[0]['ic_contrato_fixo'],
            "ic_contrato_aditivo"=>$queryFatutamento[0]['ic_contrato_aditivo'],
            "ic_contrato_servico_extra"=>$queryFatutamento[0]['ic_contrato_servico_extra'],
            "ic_gerar_fatura"=>$queryFatutamento[0]['ic_gerar_fatura'],
            "ic_gerar_boleto"=>$queryFatutamento[0]['ic_gerar_boleto'],
            "ic_gerar_nota_fiscal"=>$queryFatutamento[0]['ic_gerar_nota_fiscal'],
            "obs"=>$queryFatutamento[0]['obs'],
            "DadosContas"=>$DadosContas,
            "DadosContratos"=>$DadosContratos,
            "DadosContratosItens"=>$DadosContratosItens,
        ); 
     

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result;

        return $retorno;
    }

    public function faturamentoCopiar($faturamento_pk, $dt_faturamento_ini, $dt_faturamento_fim){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        //FATURAMENTO
        $sql = "";
        $sql.="SELECT f.pk,";
        $sql.="        date_format(f.dt_cadastro,'%d/%m/%Y') dt_cadastro,";
        $sql.="        f.usuario_cadastro_pk, ";
        $sql.="        date_format(f.dt_ult_atualizacao,'%d/%m/%Y') dt_ult_atualizacao,";
        $sql.="        f.usuario_ult_atualizacao_pk,";
        $sql.="        f.ic_contrato_fixo,";
        $sql.="        f.ic_contrato_aditivo,";
        $sql.="        f.ic_contrato_servico_extra,";
        $sql.="        f.ic_gerar_fatura,";
        $sql.="        f.ic_gerar_boleto,";
        $sql.="        f.ic_gerar_nota_fiscal,";
        $sql.="        f.ic_processar_faturamento,";
        $sql.="        f.vl_total_faturamento,";
        $sql.="        f.obs,";
        $sql.="        f.ic_status";
        $sql.=" FROM faturamento f";
        $sql.=" WHERE f.pk =".$faturamento_pk;
        $queryFatutamento = $this->pdo->prepare($sql);  
        $queryFatutamento->execute();
        $queryFatutamento = $queryFatutamento->fetchAll(\PDO::FETCH_ASSOC); 

        $fields = array();
        $fields['dt_faturamento_ini'] = Util::DataYMD($dt_faturamento_ini);
        $fields['dt_faturamento_fim'] = Util::DataYMD($dt_faturamento_fim);
        $fields['origem_pk'] = $faturamento_pk;
        $fields['ic_contrato_fixo'] = $queryFatutamento[0]['ic_contrato_fixo'];
        $fields['ic_contrato_aditivo'] = $queryFatutamento[0]['ic_contrato_aditivo'];
        $fields['ic_contrato_servico_extra'] = $queryFatutamento[0]['ic_contrato_servico_extra'];
        $fields['ic_gerar_boleto'] = $queryFatutamento[0]['ic_gerar_boleto'];
        $fields['ic_gerar_fatura'] = $queryFatutamento[0]['ic_gerar_fatura'];
        $fields['ic_gerar_nota_fiscal'] = $queryFatutamento[0]['ic_gerar_nota_fiscal'];
        $fields['ic_gerar_nota_fatura'] = $queryFatutamento[0]['ic_gerar_nota_fatura'];
        $fields['ic_processar_faturamento'] = $queryFatutamento[0]['ic_processar_faturamento'];
        $fields['vl_total_faturamento'] = $queryFatutamento[0]['vl_total_faturamento'];
        $fields['obs'] = $queryFatutamento[0]['obs'];
        $fields['ic_status'] = 1;

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        $fields["dt_cadastro"] = "sysdate()";
        $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

        $pk = Util::execInsert("faturamento", $fields,$this->pdo);

        //CONTAS
        $sql ="";
        $sql.="SELECT fc.pk,";
        $sql.="       fc.contas_pk";
        $sql.=" FROM faturamento_contas fc ";
        $sql.=" WHERE fc.faturamento_pk = ".$faturamento_pk;
        $queryContas = $this->pdo->prepare($sql);  
        $queryContas->execute();
        $queryContas = $queryContas->fetchAll(\PDO::FETCH_ASSOC); 
        
        for($i=0;$i<count($queryContas);$i++){
            $fieldsContas = array();
            $fieldsContas["faturamento_pk"] = $pk;
            $fieldsContas["contas_pk"] = $queryContas[$i]['contas_pk'];
            $fieldsContas["ic_status"] = 1;
    
            $fieldsContas["dt_ult_atualizacao"] = "sysdate()";
            $fieldsContas["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
            $fieldsContas["dt_cadastro"] = "sysdate()";
            $fieldsContas["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
            
            $faturamento_contas_pk = Util::execInsert("faturamento_contas", $fieldsContas, $this->pdo);
        }
        
        //ITENS
        $sql ="";
        $sql .="SELECT pk,";
        $sql .="        dt_cadastro,";
        $sql .="        usuario_cadastro_pk,";
        $sql .="        dt_ult_atualizacao,";
        $sql .="        usuario_ult_atualizacao_pk,";
        $sql .="        vl_total_lancamento,";
        $sql .="        faturamento_pk,";
        $sql .="        contas_pk,";
        $sql .="        leads_pk,";
        $sql .="        contratos_pk,";
        $sql .="        dt_lancamento_financeiro,";
        $sql .="        ic_item_validado,";
        $sql .="        dt_item_valiado,";
        $sql .="        lancamentos_pk,";
        $sql .="        ic_status,";
        $sql .="        ic_processamento_lancamento_item_status,";
        $sql .="        dt_processamento_lancamento_lancamento,";
        $sql .="        obs_faturamento_contrato,";
        $sql .="        obs_lancamento,";
        $sql .="        obs_corpo_nota";
        $sql .="   FROM faturamento_itens";
        $sql.="  WHERE faturamento_pk = ".$faturamento_pk;
        $queryItens = $this->pdo->prepare($sql);  
        $queryItens->execute();
        $queryItens = $queryItens->fetchAll(\PDO::FETCH_ASSOC); 
    
        for($c = 0; $c < count($queryItens); $c++){
            $fieldsItens = array();
            $fieldsItens['vl_total_lancamento'] = $queryItens[$c]['vl_total_lancamento'];
            $fieldsItens['faturamento_pk'] = $pk;
            $fieldsItens['contas_pk'] = $queryItens[$c]['contas_pk'];
            $fieldsItens['leads_pk'] = $queryItens[$c]['leads_pk'];
            $fieldsItens['contratos_pk'] = $queryItens[$c]['contratos_pk'];
            $fieldsItens['dt_lancamento_financeiro'] = $queryItens[$c]['dt_lancamento_financeiro'];
            $fieldsItens['ic_item_validado'] = $queryItens[$c]['ic_item_validado'];
            $fieldsItens['dt_item_valiado'] = $queryItens[$c]['dt_item_valiado'];
            $fieldsItens['lancamentos_pk'] = '';
            $fieldsItens['ic_status'] = $queryItens[$c]['ic_status'];
            $fieldsItens['ic_processamento_lancamento_item_status'] = $queryItens[$c]['ic_processamento_lancamento_item_status'];
            $fieldsItens['dt_processamento_lancamento_lancamento'] = $queryItens[$c]['dt_processamento_lancamento_lancamento'];    
            $fieldsItens['obs_faturamento_contrato'] = $queryItens[$c]['obs_faturamento'];
            $fieldsItens['obs_lancamento'] = $queryItens[$c]['obs_lancamento'];
            $fieldsItens['obs_corpo_nota'] = $queryItens[$c]['obs_corpo_nota'];
    
            $fieldsItens["dt_ult_atualizacao"] = "sysdate()";
            $fieldsItens["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
    
            $fieldsItens["dt_cadastro"] = "sysdate()";
            $fieldsItens["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $faturamento_itens_pk = Util::execInsert("faturamento_itens", $fieldsItens,$this->pdo);

            $sql ="";
            $sql .= "SELECT fn.pk,";
            $sql .= "       fn.dt_cadastro,";
            $sql .= "       fn.usuario_cadastro_pk,";
            $sql .= "       fn.dt_ult_atualizacao,";
            $sql .= "       fn.usuario_ult_atualizacao_pk,";
            $sql .= "       fn.prestador_pk,";
            $sql .= "       fn.tomador_pk,";
            $sql .= "       fn.servico_pk,";
            $sql .= "       fn.vl_total_servico,";
            $sql .= "       fn.iss_retido_tomador,";
            $sql .= "       fn.iss_aliquota,";
            $sql .= "       fn.iss_valor,";
            $sql .= "       fn.inss_aliquota,";
            $sql .= "       fn.inss_valor,";
            $sql .= "       fn.pis_aliquota,";
            $sql .= "       fn.pis_valor,";
            $sql .= "       fn.cofins_aliquota,";
            $sql .= "       fn.cofins_valor,";
            $sql .= "       fn.ir_aliquota,";
            $sql .= "       fn.ir_valor,";
            $sql .= "       fn.csll_aliquota,";
            $sql .= "       fn.csll_valor,";
            $sql .= "       fn.faturamento_itens_pk,";
            $sql .= "       fn.ic_status,";
            $sql .= "       fn.id_nfse,";
            $sql .= "       fn.nfse_pk,";
            $sql .= "       fns.ds_descricao_corpo_nfse,";
            $sql .= "       fn.faturamento_nfse_servicos_pk";
            $sql .= "  FROM faturamento_nfse fn";
            $sql .= "  LEFT JOIN faturamento_nfse_servicos fns on fn.faturamento_nfse_servicos_pk = fns.pk";
            $sql .= " WHERE fn.faturamento_itens_pk = ".$queryItens[$c]['pk'];
            $queryContratosNFSE = $this->pdo->prepare($sql);
            $queryContratosNFSE->execute();
            $queryContratosNFSE = $queryContratosNFSE->fetchAll(\PDO::FETCH_ASSOC);

            if(count($queryContratosNFSE)>0){
                $faturamento_nfse_servicos_pk = 0;
                $pk_nfse = 0;

                $fieldsDescricao = array();
                $fieldsDescricao['ds_descricao_corpo_nfse'] = $queryContratosNFSE[0]['ds_descricao_corpo_nfse'];
                $fieldsDescricao['ic_status'] = 1;
                $fieldsDescricao["dt_ult_atualizacao"] = "sysdate()";
                $fieldsDescricao["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                $fieldsDescricao["dt_cadastro"] = "sysdate()";
                $fieldsDescricao["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

                $faturamento_nfse_servicos_pk = Util::execInsert("faturamento_nfse_servicos", $fieldsDescricao,$this->pdo);
                
                $fieldsNfse = array();
                $fieldsNfse['prestador_pk'] = $queryContratosNFSE[0]['prestador_pk'];
                $fieldsNfse['tomador_pk'] = $queryContratosNFSE[0]['tomador_pk'];
                $fieldsNfse['servico_pk'] = $queryContratosNFSE[0]['servico_pk'];
                $fieldsNfse['vl_total_servico'] = $queryContratosNFSE[0]['vl_total_servico'];
                $fieldsNfse['iss_retido_tomador'] = $queryContratosNFSE[0]['iss_retido_tomador'];
                $fieldsNfse['iss_aliquota'] = $queryContratosNFSE[0]['iss_aliquota'];
                $fieldsNfse['iss_valor'] = $queryContratosNFSE[0]['iss_valor'];
                $fieldsNfse['inss_aliquota'] = $queryContratosNFSE[0]['inss_aliquota'];
                $fieldsNfse['inss_valor'] = $queryContratosNFSE[0]['inss_valor'];
                $fieldsNfse['pis_aliquota'] = $queryContratosNFSE[0]['pis_aliquota'];
                $fieldsNfse['pis_valor'] = $queryContratosNFSE[0]['pis_valor'];
                $fieldsNfse['cofins_aliquota'] = $queryContratosNFSE[0]['cofins_aliquota'];
                $fieldsNfse['cofins_valor'] = $queryContratosNFSE[0]['cofins_valor'];
                $fieldsNfse['ir_aliquota'] = $queryContratosNFSE[0]['ir_aliquota'];
                $fieldsNfse['ir_valor'] = $queryContratosNFSE[0]['ir_valor'];
                $fieldsNfse['csll_aliquota'] = $queryContratosNFSE[0]['csll_aliquota'];
                $fieldsNfse['csll_valor'] = $queryContratosNFSE[0]['csll_valor'];
                $fieldsNfse['faturamento_itens_pk'] = $faturamento_itens_pk;
                $fieldsNfse['ic_status'] = $queryContratosNFSE[0]['ic_status'];
                $fieldsNfse['id_nfse'] = $queryContratosNFSE[0]['id_nfse'];
                $fieldsNfse['nfse_pk'] = $queryContratosNFSE[0]['nfse_pk'];
                $fieldsNfse['faturamento_nfse_servicos_pk'] = $faturamento_nfse_servicos_pk;
    
                $fieldsNfse["dt_ult_atualizacao"] = "sysdate()";
                $fieldsNfse["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                $fieldsNfse["dt_cadastro"] = "sysdate()";
                $fieldsNfse["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

                $pk_nfse = Util::execInsert("faturamento_nfse", $fieldsNfse,$this->pdo);
            }
        }
        

        //CONTRATOS
        $sql ="";
        $sql.= "SELECT pk,";
        $sql.= "       dt_cadastro,";
        $sql.= "       usuario_cadastro_pk,";
        $sql.= "       dt_ult_atualizacao,";
        $sql.= "       usuario_ult_atualizacao_pk,";
        $sql.= "       ic_tipo_contrato,";
        $sql.= "       contratos_pk,";
        $sql.= "       leads_pk,";
        $sql.= "       faturamento_pk,";
        $sql.= "       vl_total_contrato,";
        $sql.= "       ic_status,";
        $sql.= "       obs_corpo_nota_fiscal,";
        $sql.= "       dt_vencimento,";
        $sql.= "       dt_faturamento,";
        $sql.= "       ic_gerar_nfse";
        $sql.= "  FROM faturamento_contratos";
        $sql.="  WHERE faturamento_pk = ".$faturamento_pk;
        $queryContratos = $this->pdo->prepare($sql);  
        $queryContratos->execute();
        $queryContratos = $queryContratos->fetchAll(\PDO::FETCH_ASSOC); 
    
        for($h = 0; $h < count($queryContratos); $h++){
            $fieldsContratos = array();
            $fieldsContratos['ic_tipo_contrato'] = $queryContratos[$h]['ic_tipo_contrato'];
            $fieldsContratos['contratos_pk'] = $queryContratos[$h]['contratos_pk'];
            $fieldsContratos['leads_pk'] = $queryContratos[$h]['leads_pk'];
            $fieldsContratos['faturamento_pk'] = $pk;
            $fieldsContratos['vl_total_contrato'] = $queryContratos[$h]['vl_total_contrato'];
            $fieldsContratos['ic_status'] = $queryContratos[$h]['ic_status'];
            $fieldsContratos['obs_corpo_nota_fiscal'] = $queryContratos[$h]['obs_corpo_nota_fiscal'];
            $fieldsContratos['dt_vencimento'] = $queryContratos[$h]['dt_vencimento'];
            $fieldsContratos['dt_faturamento'] = $queryContratos[$h]['dt_faturamento'];
            $fieldsContratos['ic_gerar_nfse'] = $queryContratos[$h]['ic_gerar_nfse'];
            
            $fieldsContratos["dt_ult_atualizacao"] = "sysdate()";
            $fieldsContratos["usuario_ult_atualizacao_pk"] =  $_SESSION['session_user']['par1'];

            $fieldsContratos["dt_cadastro"] = "sysdate()";
            $fieldsContratos["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $faturamento_contratos_pk = Util::execInsert("faturamento_contratos", $fieldsContratos,$this->pdo);
            

            //CONTRATOS ITENS
            $sql ="";
            $sql.=" SELECT pk,";
            $sql.="        dt_cadastro,";
            $sql.="        usuario_cadastro_pk,";
            $sql.="        dt_ult_atualizacao,";
            $sql.="        usuario_ult_atualizacao_pk,";
            $sql.="        produtos_servicos_pk,";
            $sql.="        n_qtde_produtos_servicos,";
            $sql.="        vl_unitario_produtos_servicos,";
            $sql.="        ic_status,";
            $sql.="        contratos_pk,";
            $sql.="        faturamento_contratos_pk,";
            $sql.="        ds_periodo,";
            $sql.="        n_qtde_dias_semana";
            $sql.="   FROM faturamento_contratos_itens";
            $sql.="  WHERE faturamento_contratos_pk = ".$queryContratos[$h]['pk'];
            
            $queryContratosItens = $this->pdo->prepare($sql);
            $queryContratosItens->execute();
            $queryContratosItens = $queryContratosItens->fetchAll(\PDO::FETCH_ASSOC);

            for($l = 0; $l < count($queryContratosItens); $l++){
                $fieldsContratosItens = array();
                $fieldsContratosItens['produtos_servicos_pk'] = $queryContratosItens[$l]['produtos_servicos_pk'];
                $fieldsContratosItens['n_qtde_produtos_servicos'] = $queryContratosItens[$l]['n_qtde_produtos_servicos'];
                $fieldsContratosItens['vl_unitario_produtos_servicos'] = $queryContratosItens[$l]['vl_unitario_produtos_servicos'];
                $fieldsContratosItens['ds_periodo'] = $queryContratosItens[$l]['ds_periodo'];
                $fieldsContratosItens['n_qtde_dias_semana'] = $queryContratosItens[$l]['n_qtde_dias_semana'];
                $fieldsContratosItens['faturamento_contratos_pk'] = $faturamento_contratos_pk;
                $fieldsContratosItens['contratos_pk'] = $queryContratosItens[$l]['contratos_pk'];
                $fieldsContratosItens['ic_status'] = 1;
        
                $fieldsContratosItens["dt_ult_atualizacao"] = "sysdate()";
                $fieldsContratosItens["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                
                $fieldsContratosItens["dt_cadastro"] = "sysdate()";
                $fieldsContratosItens["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
    
                $contratos_itens_pk = Util::execInsert("faturamento_contratos_itens", $fieldsContratosItens,$this->pdo);
            }  
            
        }  
    

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $pk;

        return $retorno;
    }

    public function listarUpdateFaturamento($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $DadosContratos= array();
        $DadosContratosItens= array();
        $DadosContratosNFSE= array();

        $sql = "";
        $sql.="SELECT f.pk,";
        $sql.="        date_format(f.dt_cadastro,'%d/%m/%Y') dt_cadastro,";
        $sql.="        f.usuario_cadastro_pk, ";
        $sql.="        date_format(f.dt_ult_atualizacao,'%d/%m/%Y') dt_ult_atualizacao,";
        $sql.="        f.usuario_ult_atualizacao_pk,";
        $sql.="        date_format(f.dt_faturamento_ini,'%d/%m/%Y') dt_faturamento_ini,";
        $sql.="        date_format(f.dt_faturamento_fim,'%d/%m/%Y') dt_faturamento_fim,";
        $sql.="        f.dt_faturamento_ini dt_faturamento_base_ini,";
        $sql.="        f.dt_faturamento_fim dt_faturamento_base_fim,";
        $sql.="        f.ic_contrato_fixo,";
        $sql.="        f.ic_contrato_aditivo,";
        $sql.="        f.ic_contrato_servico_extra,";
        $sql.="        f.ic_gerar_fatura,";
        $sql.="        f.ic_gerar_boleto,";
        $sql.="        f.ic_gerar_nota_fiscal,";
        $sql.="        f.ic_processar_faturamento,";
        $sql.="        f.obs,";
        $sql.="        f.ic_status,";
        $sql.="        u.ds_usuario ds_usuario_cadastro,";
        $sql.="        u1.ds_usuario ds_usuario_atualizacao";
        $sql.="               ,case WHEN f.ic_status = 1  THEN 'Faturamento Gerado' 
                                    WHEN f.ic_status = 3  THEN 'Faturamento Cancelado' 
                                    WHEN f.ic_status = 2  THEN 'Faturamento Processado' 
                            END ds_usatus_faturamento";
        $sql.=" FROM faturamento f";
        $sql.=" INNER JOIN usuarios u on f.usuario_cadastro_pk = u.pk";
        $sql.=" INNER JOIN usuarios u1 on f.usuario_ult_atualizacao_pk = u1.pk";
        $sql.=" WHERE f.pk =".$pk;

        $queryFatutamento = $this->pdo->prepare($sql);
        $queryFatutamento->execute();
        $queryFatutamento = $queryFatutamento->fetchAll(\PDO::FETCH_ASSOC);
        
        //Dados de Contas
        $sql ="";
        $sql.="SELECT fc.pk,";
        $sql.="        c.pk contas_pk,";
        $sql.="        c.ds_conta,";
        $sql.="        c.ds_razao_social,";
        $sql.="        c.ds_cpf_cnpj";
        $sql.=" FROM faturamento_contas fc ";
        $sql.=" INNER JOIN contas c ON fc.contas_pk = c.pk";
        $sql.=" WHERE fc.faturamento_pk = ".$pk;
        $sql.="   AND c.ic_status = 1";
        $sql.=" ORDER BY c.ds_razao_social";

        
        $queryContas = $this->pdo->prepare($sql);
        $queryContas->execute();
        $queryContas = $queryContas->fetchAll(\PDO::FETCH_ASSOC);

        if(count($queryContas) > 0){
            for($i = 0; $i < count($queryContas); $i++){
                //Dados Contas Empresas
                $DadosContas[] = array(
                    "contas_pk" => $queryContas[$i]["contas_pk"],
                    "ds_conta" => $queryContas[$i]["ds_conta"],
                    "ds_razao_social" => $queryContas[$i]["ds_razao_social"],
                    "ds_cpf_cnpj" => $queryContas[$i]["ds_cpf_cnpj"]
                );

                //CONTRATOS
                $sql = "";
                $sql.="SELECT DISTINCT(c.pk )contratos_pk ,";
                $sql.="        date_format(c.dt_cadastro,'%d/%m/%Y') dt_cadastro,";
                $sql.="        c.ds_identificacao_area,";
                $sql.="        c.ic_tipo_contrato,";
                $sql.="        c.vl_contrato,";
                $sql.="        c.empresas_pk contas_contratos_pk,";
                $sql.="        u.ds_usuario ds_usuario_cadastro_contrato,";
                $sql.="        l.pk leads_pk,";
                $sql.="        l.ds_lead,";
                $sql.="        l.leads_pai_pk,";
                $sql.="        l.ds_razao_social,";
                $sql.="        l.ds_cpf_cnpj,";
                $sql.="        le.ds_razao_social ds_razao_social_cliente,";
                $sql.="        le.ds_lead ds_cliente,";
                $sql.="        le.ds_cpf_cnpj ds_cnpj_cpf_cliente,";
                $sql.="        fc.pk faturamento_contratos_pk,";
                $sql.="        fc.ic_gerar_nfse,";
                $sql.="        date_format(fc.dt_vencimento,'%d/%m/%Y') dt_vencimento,";
                $sql.="        date_format(fc.dt_faturamento,'%d/%m/%Y') dt_faturamento,";
                $sql.="        fi.pk faturamento_itens_pk,";
                $sql.="        fi.obs_faturamento_contrato,";
                $sql.="        fi.obs_lancamento,";
                $sql.="        fi.obs_corpo_nota,";
                $sql.="        fc.ic_status,";
                $sql.="        concat(l.ds_endereco,' - ',l.ds_numero,' - Complemento: ',l.ds_complemento,' - Bairro: ',l.ds_bairro,' - Cidade: ',l.ds_cidade,' - Cep: ',l.ds_cep,' - UF:',l.ds_uf )ds_endereco_lead,";
                $sql.="         CASE";
                $sql.="             WHEN l.ic_tipo_lead = 1 THEN 'Cliente Matris'";
                $sql.="             ELSE 'Posto de Trabalho'";
                $sql.="         END ds_tipo_lead,";
                $sql.="       date_format(c.dt_inicio_contrato,'%d/%m/%Y') dt_inicio_contrato,";
                $sql.="       date_format(c.dt_fim_contrato,'%d/%m/%Y') dt_fim_contrato";
                $sql.="    FROM contratos c";
                $sql.="        INNER JOIN processos_etapas pe ON c.processos_etapas_pk = pe.pk";
                $sql.="        INNER JOIN processos p ON pe.processos_pk = p.pk";
                $sql.="        INNER JOIN leads l on p.leads_pk = l.pk";
                $sql.="         LEFT JOIN leads le on l.pk = le.pk";
                $sql.="        INNER JOIN usuarios u on c.usuario_cadastro_pk = u.pk"; 
                $sql.="        INNER JOIN faturamento_contratos fc ON fc.contratos_pk = c.pk"; 
                $sql.="        INNER JOIN faturamento_itens fi ON fc.contratos_pk = fi.contratos_pk AND fc.faturamento_pk = fi.faturamento_pk"; 
                $sql.="    WHERE c.dt_cancelamento IS NULL ";
                $sql.="    AND c.empresas_pk = ".$queryContas[$i]["contas_pk"];
                //$sql.="    AND l.ic_cliente=1";
                $sql.="    AND fc.faturamento_pk=".$pk;
                $sql.="   GROUP BY c.pk ";
                $sql.="   ORDER BY l.ds_lead ";

                $queryContratos = $this->pdo->prepare($sql);
                $queryContratos->execute();
                $queryContratos = $queryContratos->fetchAll(\PDO::FETCH_ASSOC);

                if(count($queryContratos) > 0){
                    for($h = 0; $h < count($queryContratos); $h++){
                        $ds_ideintificacao_contrato = "";
                        if($queryContratos[$h]["ds_identificacao_area"]!=null){
                            $ds_ideintificacao_contrato = $queryContratos[$h]["ds_identificacao_area"];
                        }
                        $DadosContratos[] = array(
                            "faturamento_contratos_pk" => $queryContratos[$h]["faturamento_contratos_pk"],
                            "faturamento_itens_pk" => $queryContratos[$h]["faturamento_itens_pk"],
                            "obs_faturamento_contrato" => $queryContratos[$h]["obs_faturamento_contrato"],
                            "dt_vencimento" => $queryContratos[$h]["dt_vencimento"],
                            "dt_faturamento" => $queryContratos[$h]["dt_faturamento"],
                            "obs_lancamento" => $queryContratos[$h]["obs_lancamento"],
                            "obs_corpo_nota" => $queryContratos[$h]["obs_corpo_nota"],
                            "contratos_pk" => $queryContratos[$h]["contratos_pk"],
                            "dt_cadastro" => $queryContratos[$h]["dt_cadastro"],
                            "ds_identificacao_area" => $ds_ideintificacao_contrato,
                            "ic_tipo_contrato" => $queryContratos[$h]["ic_tipo_contrato"],
                            "vl_contrato" => $queryContratos[$h]["vl_contrato"]  == null ? "0,00" : $queryContratos[$h]["vl_contrato"],
                            "contas_contratos_pk" => $queryContratos[$h]["contas_contratos_pk"],
                            "ic_status" => $queryContratos[$h]["ic_status"],
                            "ds_usuario_cadastro_contrato" => $queryContratos[$h]["ds_usuario_cadastro_contrato"],
                            "leads_pk" => $queryContratos[$h]["leads_pk"],
                            "leads_pai_pk" => $queryContratos[$h]["leads_pai_pk"],
                            "ds_lead" => $queryContratos[$h]["ds_lead"],    
                            "ds_razao_social" => $queryContratos[$h]["ds_razao_social"] == null ? $queryContratos[$h]["ds_razao_social_cliente"] : $queryContratos[$h]["ds_razao_social"],
                            "ds_cliente" => $queryContratos[$h]["ds_cliente"],
                            "ds_cpf_cnpj" => $queryContratos[$h]["ds_cpf_cnpj"] == null ? $queryContratos[$h]["ds_cnpj_cpf_cliente"] : $queryContratos[$h]["ds_cpf_cnpj"],
                            "ds_endereco_lead" => $queryContratos[$h]["ds_endereco_lead"] == null ? $queryContratos[$h]["ds_endereco_pai_lead"] : $queryContratos[$h]["ds_endereco_lead"],
                            "ds__tipo_lead" => $queryContratos[$h]["ds_tipo_lead"],
                            "ds__tipo_lead" => $queryContratos[$h]["ds_tipo_lead"],
                            "dt_inicio_contrato" => $queryContratos[$h]["dt_inicio_contrato"],
                            "ic_gerar_nfse" => $queryContratos[$h]["ic_gerar_nfse"],
                            "dt_fim_contrato" => $queryContratos[$h]["dt_fim_contrato"]        
                        );

                        //CONTRATOS ITENS
                        $sql ="";
                        $sql.="SELECT fci.pk faturamento_contratos_itens_pk,";
                        $sql.="    ps.pk produto_servico_pk,";
                        $sql.="    ps.ds_produto_servico,";
                        $sql.="    fci.contratos_pk,";  
                        $sql.="    fci.ds_periodo,";                       
                        $sql.="    fci.n_qtde_produtos_servicos,";
                        $sql.="    fci.n_qtde_dias_semana,"; 
                        $sql.="    fci.vl_unitario_produtos_servicos";
                        $sql.=" FROM faturamento_contratos_itens fci";
                        $sql.="    INNER JOIN produtos_servicos ps ON fci.produtos_servicos_pk = ps.pk";
                        $sql.=" WHERE fci.contratos_pk =".$queryContratos[$h]["contratos_pk"];
                        $sql.="   AND fci.faturamento_contratos_pk =".$queryContratos[$h]["faturamento_contratos_pk"];

                        $queryContratosItens = $this->pdo->prepare($sql);
                        $queryContratosItens->execute();
                        $queryContratosItens = $queryContratosItens->fetchAll(\PDO::FETCH_ASSOC);
                        if(count($queryContratosItens) > 0){
                            for($l = 0; $l < count($queryContratosItens); $l++){
                                $vl_total = $queryContratosItens[$l]["n_qtde_produtos_servicos"] * $queryContratosItens[$l]["vl_unitario_produtos_servicos"];
                                $DadosContratosItens[] = array(
                                    "faturamento_contratos_itens_pk" => $queryContratosItens[$l]["faturamento_contratos_itens_pk"],
                                    "contratos_pk" => $queryContratosItens[$l]["contratos_pk"],
                                    "produto_servico_pk" => $queryContratosItens[$l]["produto_servico_pk"],
                                    "ds_servico_prestado" => $queryContratosItens[$l]["ds_produto_servico"],
                                    "n_qtde_colaborador" => $queryContratosItens[$l]["n_qtde_produtos_servicos"],
                                    "ds_escala" => $queryContratosItens[$l]["n_qtde_dias_semana"],
                                    "ds_carga_horaria_dia" => $queryContratosItens[$l]["ds_periodo"],
                                    "vl_unit" => $queryContratosItens[$l]["vl_unitario_produtos_servicos"],
                                    "vl_total" => $vl_total,
                                );
                            }  
                        }  
                    }  
                } 
            }
        }   


        $result[] = array(
            "pk" => $pk,
            "ds_usuario_cadastro"=>$queryFatutamento[0]['ds_usuario_cadastro'],
            "ds_usuario_atualizacao"=>$queryFatutamento[0]['ds_usuario_atualizacao'],
            "dt_cadastro"=>$queryFatutamento[0]['dt_cadastro'],
            "dt_ult_atualizacao"=>$queryFatutamento[0]['dt_ult_atualizacao'],
            "dt_faturamento_ini"=>$queryFatutamento[0]['dt_faturamento_ini'],
            "dt_faturamento_fim"=>$queryFatutamento[0]['dt_faturamento_fim'],
            "ds_usatus_faturamento"=>$queryFatutamento[0]['ds_usatus_faturamento'],
            "ic_contrato_fixo"=>$queryFatutamento[0]['ic_contrato_fixo'],
            "ic_contrato_aditivo"=>$queryFatutamento[0]['ic_contrato_aditivo'],
            "ic_contrato_servico_extra"=>$queryFatutamento[0]['ic_contrato_servico_extra'],
            "ic_gerar_fatura"=>$queryFatutamento[0]['ic_gerar_fatura'],
            "ic_gerar_boleto"=>$queryFatutamento[0]['ic_gerar_boleto'],
            "ic_gerar_nota_fiscal"=>$queryFatutamento[0]['ic_gerar_nota_fiscal'],
            "obs"=>$queryFatutamento[0]['obs'],
            "DadosContas"=>$DadosContas,
            "DadosContratos"=>$DadosContratos,
            "DadosContratosItens"=>$DadosContratosItens,
            "DadosContratosNFSE"=>$DadosContratosNFSE
        );

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result;

        return $retorno;
    }
    public function listarDadosFaturamentoNFSE($faturamento_itens_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        
        $sql ="";
        $sql .= "SELECT fn.pk,";
        $sql .= "       fn.dt_cadastro,";
        $sql .= "       fn.usuario_cadastro_pk,";
        $sql .= "       fn.dt_ult_atualizacao,";
        $sql .= "       fn.usuario_ult_atualizacao_pk,";
        $sql .= "       fn.prestador_pk,";
        $sql .= "       fn.tomador_pk,";
        $sql .= "       fn.servico_pk,";
        $sql .= "       fn.vl_total_servico,";
        $sql .= "       fn.iss_retido_tomador,";
        $sql .= "       fn.iss_aliquota,";
        $sql .= "       fn.iss_valor,";
        $sql .= "       fn.inss_aliquota,";
        $sql .= "       fn.inss_valor,";
        $sql .= "       fn.pis_aliquota,";
        $sql .= "       fn.pis_valor,";
        $sql .= "       fn.cofins_aliquota,";
        $sql .= "       fn.cofins_valor,";
        $sql .= "       fn.ir_aliquota,";
        $sql .= "       fn.ir_valor,";
        $sql .= "       fn.csll_aliquota,";
        $sql .= "       fn.csll_valor,";
        $sql .= "       fn.faturamento_itens_pk,";
        $sql .= "       fn.ic_status,";
        $sql .= "       fn.id_nfse,";
        $sql .= "       fn.nfse_pk,";
        $sql .= "       fns.ds_descricao_corpo_nfse,";
        $sql .= "       fn.faturamento_nfse_servicos_pk";
        $sql .= "  FROM faturamento_nfse fn";
        $sql .= "  LEFT JOIN faturamento_nfse_servicos fns on fn.faturamento_nfse_servicos_pk = fns.pk";
        $sql .= " WHERE fn.faturamento_itens_pk = ".$faturamento_itens_pk;

        $queryContratosNFSE = $this->pdo->prepare($sql);
        $queryContratosNFSE->execute();
        $queryContratosNFSE = $queryContratosNFSE->fetchAll(\PDO::FETCH_ASSOC);
        
        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $queryContratosNFSE;

        return $retorno;
    }
    public function listarDetalhamentoCorpoNota(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

         //CONTRATOS NFSE
        $sql ="";
        $sql .=" SELECT  pk,";
        $sql .="        dt_cadastro,";
        $sql .="        usuario_cadastro_pk,";
        $sql .="        dt_ult_atualizacao,";
        $sql .="        usuario_ult_atualizacao_pk,";
        $sql .="        ds_descricao_corpo_nfse,";
        $sql .="        ic_status";
        $sql .=" FROM faturamento_nfse_servicos";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }
    public function processar($pk){
        
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql = "";
        $sql .= " SELECT fc.leads_pk,";
        $sql .= "        fc.contratos_pk,";
        $sql .= "        fc.ic_status,";
        $sql .= "        fc.faturamento_pk,";
        $sql .= "        fc.dt_faturamento dt_faturamento,";
        $sql .= "        fc.dt_vencimento dt_vencimento";
        $sql .= " FROM faturamento_contratos fc";
        $sql .= " WHERE fc.faturamento_pk = $pk";
        $sql .= "   AND fc.ic_status = 1";
        $query = $this->pdo->prepare($sql);
        $query->execute();
        $query = $query->fetchAll(\PDO::FETCH_ASSOC);

        $sql = "";
        $sql .= "SELECT pk, ds_metodo_pagamento FROM metodos_pagamento WHERE ds_metodo_pagamento = 'Boleto'";
        $queryPagamento = $this->pdo->prepare($sql);
        $queryPagamento->execute();
        $queryPagamento = $queryPagamento->fetchAll(\PDO::FETCH_ASSOC);

        $sql = "";
        $sql .= "SELECT pk, ds_tipo_operacao FROM tipos_operacao WHERE ds_tipo_operacao = 'Receita'";
        $queryTiposOperacao = $this->pdo->prepare($sql);
        $queryTiposOperacao->execute();
        $queryTiposOperacao = $queryTiposOperacao->fetchAll(\PDO::FETCH_ASSOC);
        $ic_contrato = "";
        if(count($query) > 0){
            for($i=0; $i<count($query); $i++){
                $lancamento_pk = '';
                $sql = "";
                $sql .= " SELECT f.pk faturamento_pk,";
                $sql .= "    f.dt_faturamento_ini,";
                $sql .= "    f.dt_faturamento_fim,";
                $sql .= "    f.ic_contrato_fixo,";
                $sql .= "    f.ic_contrato_aditivo,";
                $sql .= "    f.ic_contrato_servico_extra,";
                $sql .= "    fi.vl_total_lancamento,";
                $sql .= "    fi.leads_pk clientes_pk,";
                $sql .= "    fi.obs_lancamento,";
                $sql .= "    fi.contratos_pk,";
                $sql .= "    fi.contas_pk,";
                $sql .= "    fi.pk faturamento_itens_pk";
                $sql .= " FROM faturamento f";
                $sql .= "    INNER JOIN faturamento_itens fi ON fi.faturamento_pk = f.pk ";
                $sql .= "    LEFT JOIN leads l ON l.pk = fi.leads_pk";
                $sql .= " WHERE f.pk = $pk";
                $sql .= "   AND fi.contratos_pk =".$query[$i]['contratos_pk'];
                $queryContratos = $this->pdo->prepare($sql);
                $queryContratos->execute();
                $queryContratos = $queryContratos->fetchAll(\PDO::FETCH_ASSOC);

                if($queryContratos[0]['ic_contrato_fixo'] == 1){
                    $ic_contrato = "CONTRATO FIXO";
                }/*else if($queryContratos[0]['ic_contrato_aditivo'] == 1){
                    $ic_contrato = "";
                }else if($queryContratos[0]['ic_contrato_servico_extra'] == 1){
                    $ic_contrato = "";
                }*/
        
                $sql = "";
                $sql .= "SELECT pk, ds_categoria FROM categorias_financeiras WHERE ds_categoria = '".$ic_contrato."' order by pk desc";
                $queryOperacao = $this->pdo->prepare($sql);
                $queryOperacao->execute();
                $queryOperacao = $queryOperacao->fetchAll(\PDO::FETCH_ASSOC);

                $fields = array();
                $fields["ds_lancamento"] = "Receita Faturamento Cliente";
                $fields["tipo_lancamento_pk"] = 1;
                $fields["categorias_financeiras_pk"] = $queryOperacao[0]['pk'];
                $fields["tipos_operacao_pk"] = $queryTiposOperacao[0]['pk'];
                $fields["dt_faturamento"] = $query[$i]['dt_faturamento'];
                $fields["dt_vencimento"] = $query[$i]['dt_vencimento'];
                $fields["vl_lancamento"] = $queryContratos[0]['vl_total_lancamento'];
                $fields["metodos_pagamento_pk"] = $queryPagamento[0]['pk'];
                $fields["empresa_lancamento_pk"] = $queryContratos[0]['contas_pk'];
                $fields["ic_status_lancamento"] = 2;
                $fields["grupo_lancamento_pk"] = $queryContratos[0]['clientes_pk'];
                $fields["tipo_grupo_lancamento_pk"] = 1;
                $fields["ic_parcela"] = 1;
                //$fields["cliente_lancamento_pk"] = $query[$i]['clientes_pk'];
                $fields["posto_trabalho_lancamento_pk"] = $query[$i]['leads_pk'];
                $fields["contratos_pk"] = $queryContratos[$i]['contratos_pk'];
                $fields["obs_lancamento"] = $queryContratos[$i]['obs_lancamento'];
                $fields["dt_cadastro"] = "sysdate()";
                $fields["usuario_cadastro_pk"] = $_SESSION['session_user']['par1'];
                $fields["dt_ult_atualizacao"] = "sysdate()";
                $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                $lancamento_pk = Util::execInsert("lancamentos_financeiros", $fields,$this->pdo);

                $fieldsUpdate = array();
                $fieldsUpdate['lancamentos_pk'] = $lancamento_pk;
                $fieldsUpdate["dt_ult_atualizacao"] = "sysdate()";
                $fieldsUpdate["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                Util::execUpdate("faturamento_itens", $fieldsUpdate, " pk = ".$queryContratos[0]['faturamento_itens_pk'],$this->pdo);

                
                
            }
            
            $fieldsUpdateFaturamento = array();
            $fieldsUpdateFaturamento['ic_status'] = 2;
            $fieldsUpdateFaturamento["dt_ult_atualizacao"] = "sysdate()";
            $fieldsUpdateFaturamento["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
            Util::execUpdate("faturamento", $fieldsUpdateFaturamento, " pk = ".$query[0]['faturamento_pk'],$this->pdo);

            $mensagem = 'Dados carregados com sucesso';
        }else{
            $mensagem = 'Salve o Faturamento Primeiro!';
        }
        

        $result[] = array(
            "pk" => $pk
        );

        $retorno->status = true;
        $retorno->message = $mensagem;
        $retorno->data = $result;

        return $retorno;
    }
    public function listarContratoFaturamento($contratos_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $retorno->message = '';

        //CONTRATOS
        $sql = "";
        $sql.="SELECT DISTINCT(c.pk )contratos_pk ,";
        $sql.="        date_format(c.dt_cadastro,'%d/%m/%Y') dt_cadastro,";
        $sql.="        c.ds_identificacao_area,";
        $sql.="        c.ic_tipo_contrato,";
        $sql.="        c.vl_contrato,";
        $sql.="        c.empresas_pk contas_contratos_pk,";
        $sql.="        u.ds_usuario ds_usuario_cadastro_contrato,";
        $sql.="        l.pk leads_pk,";
        $sql.="        l.ds_lead,";
        $sql.="        l.leads_pai_pk,";
        $sql.="        l.ds_razao_social,";
        $sql.="        l.ds_cpf_cnpj,";
        $sql.="        concat(l.ds_endereco,' - ',l.ds_numero,' - Complemento: ',l.ds_complemento,' - Bairro: ',l.ds_bairro,' - Cidade: ',l.ds_cidade,' - Cep: ',l.ds_cep,' - UF:',ds_uf )ds_endereco_lead,";
        $sql.="         CASE";
        $sql.="             WHEN l.ic_tipo_lead = 1 THEN 'Cliente Matris'";
        $sql.="             ELSE 'Posto de Trabalho'";
        $sql.="         END ds_tipo_lead,";
        $sql.="       date_format(c.dt_inicio_contrato,'%d/%m/%Y') dt_inicio_contrato,";
        $sql.="       date_format(c.dt_fim_contrato,'%d/%m/%Y') dt_fim_contrato";
        $sql.="    FROM contratos c";
        $sql.="        INNER JOIN processos_etapas pe ON c.processos_etapas_pk = pe.pk";
        $sql.="        INNER JOIN processos p ON pe.processos_pk = p.pk";
        $sql.="        INNER JOIN leads l on p.leads_pk = l.pk";
        $sql.="        INNER JOIN usuarios u on c.usuario_cadastro_pk = u.pk"; 
        $sql.="    WHERE c.pk =".$contratos_pk;
        $sql.="   GROUP BY c.pk ";
        $sql.="   ORDER BY l.ds_lead ";

        $queryContratos = $this->pdo->prepare($sql);
        $queryContratos->execute();
        $queryContratos = $queryContratos->fetchAll(\PDO::FETCH_ASSOC);

        if(count($queryContratos) > 0){
            for($h = 0; $h < count($queryContratos); $h++){
                $ds_ideintificacao_contrato = "";
                if($queryContratos[$h]["ds_identificacao_area"]!=null){
                    $ds_ideintificacao_contrato = $queryContratos[$h]["ds_identificacao_area"];
                }
                $DadosContratos[] = array(
                    "faturamento_contratos_pk" => $queryContratos[$h]["faturamento_contratos_pk"],
                    "faturamento_itens_pk" => $queryContratos[$h]["faturamento_itens_pk"],
                    "obs_faturamento_contrato" => $queryContratos[$h]["obs_faturamento_contrato"],
                    "dt_vencimento" => $queryContratos[$h]["dt_vencimento"],
                    "dt_faturamento" => $queryContratos[$h]["dt_faturamento"],
                    "obs_lancamento" => $queryContratos[$h]["obs_lancamento"],
                    "obs_corpo_nota" => $queryContratos[$h]["obs_corpo_nota"],
                    "contratos_pk" => $queryContratos[$h]["contratos_pk"],
                    "dt_cadastro" => $queryContratos[$h]["dt_cadastro"],
                    "ds_identificacao_area" => $ds_ideintificacao_contrato,
                    "ic_tipo_contrato" => $queryContratos[$h]["ic_tipo_contrato"],
                    "vl_contrato" => $queryContratos[$h]["vl_contrato"]  == null ? "0,00" : $queryContratos[$h]["vl_contrato"],
                    "contas_contratos_pk" => $queryContratos[$h]["contas_contratos_pk"],
                    "ic_status" => $queryContratos[$h]["ic_status"],
                    "ds_usuario_cadastro_contrato" => $queryContratos[$h]["ds_usuario_cadastro_contrato"],
                    "leads_pk" => $queryContratos[$h]["leads_pk"],
                    "leads_pai_pk" => $queryContratos[$h]["leads_pai_pk"],
                    "ds_lead" => $queryContratos[$h]["ds_lead"],    
                    "ds_razao_social" => $queryContratos[$h]["ds_razao_social"],
                    "ds_cpf_cnpj" => $queryContratos[$h]["ds_cpf_cnpj"] == null ? " " : $queryContratos[$h]["ds_cpf_cnpj"],
                    "ds_endereco_lead" => $queryContratos[$h]["ds_endereco_lead"] == null ? " " : $queryContratos[$h]["ds_endereco_lead"],
                    "ds__tipo_lead" => $queryContratos[$h]["ds_tipo_lead"],
                    "ds__tipo_lead" => $queryContratos[$h]["ds_tipo_lead"],
                    "dt_inicio_contrato" => $queryContratos[$h]["dt_inicio_contrato"],
                    "dt_fim_contrato" => $queryContratos[$h]["dt_fim_contrato"]        
                );

                //CONTRATOS ITENS
                $sql ="";
                $sql.="SELECT ci.pk contratos_itens_pk,";
                $sql.="    ps.pk produto_servico_pk,";
                $sql.="    ps.ds_produto_servico,";
                $sql.="    ci.contratos_pk,";  
                $sql.="    ci.periodo,";                       
                $sql.="    ci.n_qtde,";
                $sql.="    ci.n_qtde_dias_semana,"; 
                $sql.="    ci.vl_unit";
                $sql.=" FROM contratos_itens ci";
                $sql.="    INNER JOIN produtos_servicos ps ON ci.produtos_servicos_pk = ps.pk";
                $sql.=" WHERE ci.contratos_pk =".$contratos_pk;

                $queryContratosItens = $this->pdo->prepare($sql);
                $queryContratosItens->execute();
                $queryContratosItens = $queryContratosItens->fetchAll(\PDO::FETCH_ASSOC);
                if(count($queryContratosItens) > 0){
                    for($l = 0; $l < count($queryContratosItens); $l++){
                        $vl_total = $queryContratosItens[$l]["n_qtde"] * $queryContratosItens[$l]["vl_unit"];
                        $DadosContratosItens[] = array(
                            "contratos_itens_pk" => $queryContratosItens[$l]["contratos_itens_pk"],
                            "contratos_pk" => $queryContratosItens[$l]["contratos_pk"],
                            "produto_servico_pk" => $queryContratosItens[$l]["produto_servico_pk"],
                            "ds_servico_prestado" => $queryContratosItens[$l]["ds_produto_servico"],
                            "n_qtde_colaborador" => $queryContratosItens[$l]["n_qtde"],
                            "ds_escala" => $queryContratosItens[$l]["n_qtde_dias_semana"],
                            "ds_carga_horaria_dia" => $queryContratosItens[$l]["periodo"],
                            "vl_unit" => $queryContratosItens[$l]["vl_unit"],
                            "vl_total" => $vl_total,
                        );
                    }  
                }  
            }  
        }  
        $result[] = array(
            "pk" => $contratos_pk,
            "DadosContratos"=>$DadosContratos,
            "DadosContratosItens"=>$DadosContratosItens,
        );

        $retorno->data = $result;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';


        return $retorno;
    }
    
    public function listarContratos($dt_ini_contrato, $dt_fim_contrato, $empresas_pk, $faturamento_pk, $clientes_pk, $posto_trabalho_pk){
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
        $sql ="";
        $sql .="Select ic_contrato_fixo, ic_contrato_servico_extra, ic_contrato_aditivo from faturamento where pk = ".$faturamento_pk;
        $stmtFaturamento = $this->pdo->prepare( $sql );
        $stmtFaturamento->execute();
        $queryFaturamento = $stmtFaturamento->fetchAll(\PDO::FETCH_ASSOC);

        $sql ="";
        $sql.="SELECT c.pk, c.ds_identificacao_area, c.vl_contrato, c.dt_inicio_contrato, c.dt_fim_contrato, c.empresas_pk, l.ds_lead ";
        $sql.="       ,case c.ic_tipo_contrato when 1 then 'Contrato' when 2 then 'Aditivo' when 3 then 'Serviço Extra' end ds_tipo_contrato";
        $sql.="  FROM contratos c";
        $sql.="        INNER JOIN processos_etapas pe ON c.processos_etapas_pk = pe.pk";
        $sql.="        INNER JOIN processos p ON pe.processos_pk = p.pk";
        $sql.="        INNER JOIN leads l on p.leads_pk = l.pk";
        $sql.="        INNER JOIN usuarios u on c.usuario_cadastro_pk = u.pk"; 
        $sql.=" WHERE c.pk NOT IN( SELECT contratos_pk FROM faturamento_contratos where faturamento_pk = ".$faturamento_pk.")";
        $sql.=" AND c.ic_tipo_contrato = 1";
        
        if($dt_ini_contrato != ""){
            $sql.=" AND c.dt_inicio_contrato >= '".Util::DataYMD($dt_ini_contrato)."' ";
        }
        if($dt_fim_contrato != ""){
            $sql.=" AND c.dt_fim_contrato <= '".Util::DataYMD($dt_fim_contrato)."' ";
        }
        if($empresas_pk != ""){
            $sql.=" AND c.empresas_pk = '".$empresas_pk."' ";
        }
        if($clientes_pk != ""){
            $sql.="   AND l.leads_pai_pk =".$clientes_pk;
        }
        if($posto_trabalho_pk != ""){
            $sql.="   AND p.leads_pk =".$posto_trabalho_pk;
        }

        $stmt = $this->pdo->prepare( $sql.$lengthSql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmtCount = $this->pdo->prepare( $sql );
        $stmtCount->execute();
        $rowsCount = $stmtCount->fetchAll(\PDO::FETCH_ASSOC);

        for($i=0;$i<count($rows);$i++){
            $j = $i + 1;

            $result[] = array(
                "n_contador" => $j,
                "pk" => $rows[$i]["pk"],
                "dt_inicio_contrato"=>$rows[$i]['dt_inicio_contrato'],
                "dt_fim_contrato"=>$rows[$i]['dt_fim_contrato'],
                "ds_tipo_contrato"=>$rows[$i]['ds_tipo_contrato'],
                "ds_identificacao_area"=>$rows[$i]['ds_identificacao_area'],
                "ds_lead"=>$rows[$i]['ds_lead'],
                "vl_contrato"=>number_format($rows[$i]['vl_contrato'],2,',','.')
            );
        }

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result;
        $retorno->iTotalDisplayRecords = count($rowsCount);
        $retorno->iTotalRecords = count($rowsCount);

        echo json_encode($retorno);
        exit(0);
    }

}
