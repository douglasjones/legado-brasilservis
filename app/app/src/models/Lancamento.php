<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

set_time_limit(0);
ini_set('memory_limit', '256M');

class Lancamento {
    
    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }



    public function salvarCompra($lancamento){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_lancamento'] = $lancamento['ds_lancamento'];
        $fields['tipo_lancamento_pk'] = $lancamento['operacao_pk'];
        $fields['tipos_operacao_pk'] = $lancamento['tipos_operacao_pk'];
        $fields['tipo_grupo_lancamento_pk'] = $lancamento['tipo_grupo_pk'];
        $fields['grupo_lancamento_pk'] = $lancamento['grupo_leancamento_pk'];

        $fields['cliente_lancamento_pk'] = $lancamento['grupo_lancamento_centro_custo_pk'];
        $fields['posto_trabalho_lancamento_pk'] = $lancamento['grupo_lancamento_centro_custo_pk'];


        $fields['ds_num_documento'] = $lancamento['ds_num_documento'];
        $fields['dt_vencimento'] = trim($lancamento['dt_vencimento']);
        $fields['vl_lancamento'] = ($lancamento['vl_lancamento']);
        $fields['metodos_pagamento_pk'] = $lancamento['metodos_pagamento_pk'];
        $fields['empresa_lancamento_pk'] = $lancamento['empresas_pk'];
        $fields['contas_bancarias_pk'] = $lancamento['contas_bancarias_pk'];
        $fields['ic_status_lancamento'] = $lancamento['ic_status_pagamento'];
        $fields['ic_parcela'] = 1;
        $fields['ds_num_documento'] = $lancamento['ds_num_documento'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($lancamento['pk'] == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("lancamentos_financeiros", $fields,$this->pdo);

            $ic_analise_financeira = (new Conta($this->pdo))->configModulo();
            $ic_analise_financeira = $ic_analise_financeira->data[0]['ic_analise_financeira'];
            if($ic_analise_financeira == 1 && $lancamento['operacao_pk'] != 1){

                $fieldsAnalise = array();
                $fieldsAnalise['lancamentos_pk'] = $pk;
                $fieldsAnalise['usuario_cadastro_lancamento_pk'] = $_SESSION['session_user']['par1'];
                $fieldsAnalise['ic_status'] = 1;

                $fieldsAnalise["dt_ult_atualizacao"] = "sysdate()";
                $fieldsAnalise["usuario_ult_atualizacao_pk"] =$_SESSION['session_user']['par1'];

                $fieldsAnalise["dt_cadastro"] = "sysdate()";
                $fieldsAnalise["usuario_cadastro_pk"]   =$_SESSION['session_user']['par1'];
            
                $pk_analise_financeira = Util::execInsert("analise_financeira", $fieldsAnalise,$this->pdo);

            }

            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("lancamentos_financeiros", $fields, " pk = ".$lancamento['pk'],$this->pdo);
            $pk = $lancamento['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;

        }

    }

    public function salvarLancamentoByContrato($lancamento){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['operacao_pk'] = $lancamento['operacao_pk'];
        $fields['tipos_operacao_pk'] = $lancamento['tipos_operacao_pk'];
        $fields['empresas_pk'] = $lancamento['empresas_pk'];
        $fields['contas_bancarias_pk'] = $lancamento['contas_bancarias_pk'];
        $fields['tipo_grupo_pk'] = $lancamento['tipo_grupo_pk'];
        $fields['grupo_leancamento_pk'] = $lancamento['grupo_leancamento_pk'];
        $fields['metodos_pagamento_pk'] = $lancamento['metodos_pagamento_pk'];
        $fields['vl_lancamento'] = ($lancamento['vl_lancamento']);
        $fields['ds_lancamento'] = ($lancamento['ds_lancamento']);
        $fields['dt_vencimento'] = Util::DataYMD($lancamento['dt_vencimento']);
        if($lancamento['dt_faturamento']!=""){
            $fields['dt_faturamento'] = Util::DataYMD($lancamento['dt_faturamento']);
        }
        $fields['ic_status_pagamento'] = $lancamento['ic_status_pagamento'];
        $fields['contratos_pk'] = $lancamento['contratos_pk'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"]   = $_SESSION['session_user']['par1'];

        if($lancamento['pk'] == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("lancamentos_financeiros", $fields,$this->pdo);

            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("lancamentos_financeiros", $fields, " pk = ".$lancamento['pk'],$this->pdo);
            $pk = $lancamento['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;
    }

	public function salvar($lancamento){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fieldsFornecedor = array();
        $arrParcelas = json_decode($lancamento['arrParcelas'], true);
        $doc_lancamento = json_decode($lancamento['doc_lancamento'], true);
        
        for($i=0;$i<count($arrParcelas);$i++){
            if($lancamento['grupo_lancamento_fornecedor_pk'] != ''){
                $fieldsFornecedor['ds_fornecedor'] = $lancamento['grupo_lancamento_fornecedor_pk'];
                $fieldsFornecedor['ic_status'] = 1;
                $fieldsFornecedor["dt_ult_atualizacao"] = "sysdate()";
                $fieldsFornecedor["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                $fieldsFornecedor["dt_cadastro"] = "sysdate()";
                $fieldsFornecedor["usuario_cadastro_pk"]   =  $_SESSION['session_user']['par1'];
                $fornecedor_pk = Util::execInsert("fornecedor", $fieldsFornecedor,$this->pdo);
            }

            $vl_lancamento = 0;
            $fields['ds_lancamento'] = $lancamento['ds_lancamento'];
            $fields['tipo_lancamento_pk'] = $lancamento['tipo_lancamento_pk'];
            $fields['categorias_financeiras_pk'] = $lancamento['categorias_financeiras_pk'];
            $fields['tipos_operacao_pk'] = $lancamento['tipos_operacao_pk'];
            $fields['tipo_grupo_lancamento_pk'] = $lancamento['tipo_grupo_pk'];
            if($lancamento['tipo_grupo_pk'] == 3 && $lancamento['grupo_lancamento_fornecedor_pk'] != ''){
                $fields['grupo_lancamento_pk'] = $fornecedor_pk;
            }else{
                $fields['grupo_lancamento_pk'] = $lancamento['grupo_lancamento_pk'];
            }
            $fields['cliente_lancamento_pk'] = $lancamento['cliente_lancamento_pk'];
            $fields['posto_trabalho_lancamento_pk'] = $lancamento['posto_trabalho_lancamento_pk'];
            $fields['contratos_pk'] = $lancamento['contratos_pk'];
            $fields['metodos_pagamento_pk'] = $lancamento['metodos_pagamento_pk'];
            $fields['empresa_lancamento_pk'] = $lancamento['empresa_lancamento_pk'];
            $fields['contas_bancarias_pk'] = $lancamento['contas_bancarias_pk'];
            $fields['ic_status_lancamento'] = $lancamento['ic_status_lancamento'];
            if($lancamento['ic_status_lancamento']!=6){
                if($lancamento['dt_pagamento']!=""){
                    $fields['dt_pagamento'] = Util::DataYMD($lancamento['dt_pagamento']);
                    $fields['ic_status_lancamento'] = 1;
                }
            }

            $fields['obs_lancamento'] = $lancamento['obs_lancamento'];
            $fields['ds_num_documento'] = $lancamento['ds_num_documento'];
            $fields['ic_tipo_num_documento'] = $lancamento['ic_tipo_num_documento'];
            $fields['ic_parcela'] = $arrParcelas[$i]['parcelas_pk'];
            $fields['dt_faturamento'] = Util::DataYMD($arrParcelas[$i]['dt_faturamento']);
            $fields['dt_vencimento'] = Util::DataYMD($arrParcelas[$i]['dt_vencimento']);

            $fields['vl_lancamento'] = $arrParcelas[$i]['vl_lancamento'];
            $vl_lancamento = $arrParcelas[$i]['vl_lancamento'];

    
            $fields["dt_ult_atualizacao"] = "sysdate()";
            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
    
            if($lancamento['pk']  == ""){
    
                $fields["dt_cadastro"] = "sysdate()";
                $fields["usuario_cadastro_pk"]   =  $_SESSION['session_user']['par1'];
    
                $pk = Util::execInsert("lancamentos_financeiros", $fields,$this->pdo);

                $ic_analise_financeira = (new Conta($this->pdo))->configModulo();
                $ic_analise_financeira = $ic_analise_financeira->data[0]['ic_analise_financeira'];
                if($ic_analise_financeira == 1 && $lancamento['tipo_lancamento_pk'] != 1){
                    $fieldsAnalise = array();
                    $fieldsAnalise['lancamentos_pk'] = $pk;
                    $fieldsAnalise['usuario_cadastro_lancamento_pk'] = $_SESSION['session_user']['par1'];
                    $fieldsAnalise['ic_status'] = 1;
                    $fieldsAnalise['obs'] = $lancamento['obs_lancamento'];

                    $fieldsAnalise["dt_ult_atualizacao"] = "sysdate()";
                    $fieldsAnalise["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

                    $fieldsAnalise["dt_cadastro"] = "sysdate()";
                    $fieldsAnalise["usuario_cadastro_pk"]  = $_SESSION['session_user']['par1'];
                    $pk_analise_financeira = Util::execInsert("analise_financeira", $fieldsAnalise,$this->pdo);
                }

                $retorno->status = true;
                $retorno->message = 'Dados cadastrados com sucesso';
                $retorno->data = $pk;
            }
            else{
                $pk = $lancamento['pk'];

                $sql = "SELECT pk,";
                $sql .= "        dt_cadastro,";
                $sql .= "        usuario_cadastro_pk,";
                $sql .= "        dt_ult_atualizacao,";
                $sql .= "        usuario_ult_atualizacao_pk,";
                $sql .= "        dt_baixa_parcial,";
                $sql .= "        vl_baixa_parcial,";
                $sql .= "        lancamentos_financeiros_pk";
                $sql .= "    FROM lancamentos_financeiros_baixa_parcial";
                $sql .= "    WHERE lancamentos_financeiros_pk = ".$pk;
                $sql .= "    ORDER BY pk DESC";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $vl_baixa_parcial = 0;
                
                if(count($rows) > 0){
                    for($l=0; $l<count($rows);$l++){
                        $vl_baixa_parcial += $rows[$l]['vl_baixa_parcial'];
                    }
                }

                $vl_baixa_parcial = $vl_baixa_parcial + $lancamento['vl_parcial'];
                
                if(floatval($vl_baixa_parcial) == floatval($vl_lancamento)){
                    $fields['ic_status_lancamento'] = 1;
                    $fields['dt_pagamento'] = Util::DataYMD($lancamento['dt_pagamento']);
                }
                $fields["dt_ult_atualizacao"] = "sysdate()";
                $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

                Util::execUpdate("lancamentos_financeiros", $fields, " pk = ".$lancamento['pk'],$this->pdo);

                if($lancamento['ic_status_lancamento'] == 6){
                    if(($rows[0]['dt_baixa_parcial'] != Util::DataYMD($lancamento['dt_pagamento'])) || ($rows[0]['vl_baixa_parcial'] != $lancamento['vl_parcial'])){
                        $fieldsParcial = array();
                        $fieldsParcial['lancamentos_financeiros_pk'] = $pk;
                        if($lancamento['dt_pagamento']!=""){
                            $fieldsParcial['dt_baixa_parcial'] = Util::DataYMD($lancamento['dt_pagamento']);
                        }
                        $fieldsParcial['vl_baixa_parcial'] = $lancamento['vl_parcial'];
                        $fieldsParcial['empresa_lancamento_pk'] = $lancamento['empresa_lancamento_pk'];
                        $fieldsParcial['contas_bancarias_pk'] = $lancamento['contas_bancarias_pk'];
    
                        $fieldsParcial["dt_ult_atualizacao"] = "sysdate()";
                        $fieldsParcial["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                        $fieldsParcial["dt_cadastro"] = "sysdate()";
                        $fieldsParcial["usuario_cadastro_pk"]  = $_SESSION['session_user']['par1'];
                        $pk_lancamento_parcial = Util::execInsert("lancamentos_financeiros_baixa_parcial", $fieldsParcial,$this->pdo);
                    }
                }else if($lancamento['ic_status_lancamento'] == 1){
                    if(count($rows)>0){
                        $fieldsParcial = array();
                        $fieldsParcial['lancamentos_financeiros_pk'] = $pk;
                        if($lancamento['dt_pagamento']!=""){
                            $fieldsParcial['dt_baixa_parcial'] = Util::DataYMD($lancamento['dt_pagamento']);
                        }
                        $fieldsParcial['vl_baixa_parcial'] = $lancamento['vl_parcial'];
                        $fieldsParcial['empresa_lancamento_pk'] = $lancamento['empresa_lancamento_pk'];
                        $fieldsParcial['contas_bancarias_pk'] = $lancamento['contas_bancarias_pk'];
    
                        $fieldsParcial["dt_ult_atualizacao"] = "sysdate()";
                        $fieldsParcial["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                        $fieldsParcial["dt_cadastro"] = "sysdate()";
                        $fieldsParcial["usuario_cadastro_pk"]  = $_SESSION['session_user']['par1'];
                        $pk_lancamento_parcial = Util::execInsert("lancamentos_financeiros_baixa_parcial", $fieldsParcial,$this->pdo);
                    }
                    
                }


                $retorno->status = true;
                $retorno->message = 'Dados atualizado com sucesso';
                $retorno->data = $pk;
            }

            if($lancamento['pk']==""){
                if(count($doc_lancamento) > 0){
                    for($l = 0; $l < count($doc_lancamento); $l++){
                        if($doc_lancamento[$l]['pk_doc_bd']!="Nenhum registro disponível na tabela"){
                            $fieldsDocumentos = array();
                            $fieldsDocumentos['ds_documento'] = $doc_lancamento[$l]['ds_documento'];
                            $fieldsDocumentos['ds_nome_original'] = $doc_lancamento[$l]['ds_nome_original'];
                            $fieldsDocumentos['lancamentos_pk'] = $pk;
                            $fieldsDocumentos['pk_doc_bd'] = $doc_lancamento[$l]['pk_doc_bd'];

                            $fieldsDocumentos["dt_ult_atualizacao"] = "sysdate()";
                            $fieldsDocumentos["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];


                            $fieldsDocumentos["dt_cadastro"] = "sysdate()";
                            $fieldsDocumentos["usuario_cadastro_pk"]   =  $_SESSION['session_user']['par1'];



                            //QUERY PARA VERIFICAR SE A PK_DOC_BD
                            $sql="SELECT * FROM documentos d INNER JOIN tbl_docs t on t.docsId = d.pk_doc_bd WHERE  t.docsId = ".$doc_lancamento[$l]['pk_doc_bd'];

                            $stmt = $this->pdo->prepare($sql);
                            $stmt->execute();
                            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                            if(count($rows)==0){
                                Util::execInsert("documentos", $fieldsDocumentos, $this->pdo);
                            }
                        }
                    }
                }
            }

            
        }
        
        return $retorno;

    }
    
    public function migrarBaseFinanceira(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        
        $sql = "truncate lancamentos_financeiros";
        $truncate = $this->pdo->prepare($sql);
        $truncate->execute();

        $fields = array();
        $sql ="";
        $sql.="SELECT pk,dt_cadastro,usuario_cadastro_pk,dt_ult_atualizacao,";
        $sql.="       dt_vencimento,";
        $sql.="       ds_lancamento,";
        $sql.="       vl_lancamento,";
        $sql.="       operacao_pk,";
        $sql.="       tipo_grupo_pk,";
        $sql.="       grupo_leancamento_pk,";
        $sql.="       ic_status_pagamento,";
        $sql.="       obs_lancamento,";
        $sql.="       dt_competencia,";
        $sql.="       n_documento,";
        $sql.="       tipos_operacao_pk,";
        $sql.="       metodos_pagamento_pk,";
        $sql.="       usuario_ult_atualizacao_pk,";
        $sql.="       contas_bancarias_pk,";
        $sql.="       empresas_pk,";
        $sql.="       tipo_grupo_centro_custo_pk,";
        $sql.="       grupo_lancamento_centro_custo_pk,";
        $sql.="       ds_ocorrencia,";
        $sql.="       dt_pagamento,";
        $sql.="       contratos_pk,";
        $sql.="       compras_pk,";
        $sql.="       dt_faturamento,";
        $sql.="       categoria_operacao_pk,";
        $sql.="       leads_clientes_pk,";
        $sql.="       leads_posto_trabalho_pk,";
        $sql.="       colaborador_posto_trabalho_pk,";
        $sql.="       colaborador_contratos_pk,";
        $sql.="       fornecedor_posto_trabalho_pk,";
        $sql.="       fornecedor_contratos_pk,";
        $sql.="       colaborador_pk,";
        $sql.="       fornecedor_pk,";
        $sql.="       parcela_pk,";
        $sql.="       ic_tipo_num_documento,";
        $sql.="       ds_num_documento";
        $sql.="  FROM lancamentos";
        $sql.=" ORDER BY pk ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

       
        while($rows = $stmt->fetch(\PDO::FETCH_ASSOC)){
            switch ($rows['tipo_grupo_pk']) {
                case 1:
                    $grupo_lancamento_pk = $rows['leads_clientes_pk'] != '' ? $rows['leads_clientes_pk'] : $rows['grupo_leancamento_pk'];
                    $posto_trabalho_lancamento_pk = $rows['leads_posto_trabalho_pk'];
                    $contratos_pk = $rows['leads_posto_trabalho_pk'];
                    break;
                case 2:
                    $grupo_lancamento_pk = $rows['colaborador_pk'] != '' ? $rows['colaborador_pk'] : $rows['grupo_leancamento_pk'];
                    $posto_trabalho_lancamento_pk = $rows['colaborador_posto_trabalho_pk'];
                    $contratos_pk = $rows['colaborador_contratos_pk'];
                    break;
                case 3:
                    $grupo_lancamento_pk = $rows['fornecedor_pk'] != '' ? $rows['fornecedor_pk'] : $rows['grupo_leancamento_pk'];
                    $posto_trabalho_lancamento_pk = $rows['fornecedor_posto_trabalho_pk'];
                    $contratos_pk = $rows['fornecedor_contratos_pk'];
                    break;
            }   

            $parcela_pk = $rows['parcela_pk'] != '' ? $rows['parcela_pk'] : 1;
            
            $fields['pk'] = $rows['pk'];
            $fields['ds_lancamento'] = str_replace("'","",$rows['ds_lancamento']);
            $fields['tipo_lancamento_pk'] = $rows['operacao_pk'];
            $fields['categorias_financeiras_pk'] = $rows['categoria_operacao_pk'];
            $fields['tipos_operacao_pk'] = $rows['tipos_operacao_pk'];
            $fields['tipo_grupo_lancamento_pk'] = $rows['tipo_grupo_pk'];
            $fields['grupo_lancamento_pk'] = $grupo_lancamento_pk;
            $fields['cliente_lancamento_pk'] = $rows['leads_clientes_pk'];
            $fields['posto_trabalho_lancamento_pk'] = $posto_trabalho_lancamento_pk;
            $fields['contratos_pk'] = $contratos_pk;
            if($rows['metodos_pagamento_pk'] == 7){
                $fields['metodos_pagamento_pk'] = 6;
            }else{
                $fields['metodos_pagamento_pk'] = $rows['metodos_pagamento_pk'];
            }
            $fields['empresa_lancamento_pk'] = $rows['empresas_pk'];
            $fields['contas_bancarias_pk'] = $rows['contas_bancarias_pk'];
            $fields['ic_status_lancamento'] = $rows['ic_status_pagamento'];
            $fields['dt_pagamento'] = $rows['dt_pagamento'];
            
            
            $fields['obs_lancamento'] = str_replace("'","",$rows['obs_lancamento']);
            
            $fields['ds_num_documento'] = str_replace("'","",$rows['ds_num_documento']);
            $fields['ic_tipo_num_documento'] = $rows['ic_tipo_num_documento'];
            $fields['ic_parcela'] = $parcela_pk;
            $fields['dt_faturamento'] = $rows['dt_faturamento'];
            $fields['dt_vencimento'] = $rows['dt_vencimento'];
            $fields['vl_lancamento'] = $rows['vl_lancamento'];
            $fields["dt_ult_atualizacao"] = $rows['dt_ult_atualizacao'];
            $fields["usuario_ult_atualizacao_pk"] = $rows['usuario_ult_atualizacao_pk'];
            $fields["dt_cadastro"] = $rows['dt_cadastro'];
            $fields["usuario_cadastro_pk"] = $rows['usuario_cadastro_pk'];
            $fields["ic_migracao"] = 1;
            
            $pk = Util::execInsert("lancamentos_financeiros", $fields,$this->pdo);
        }

        $retorno->status = true;
        $retorno->message = 'Dados atualizado com sucesso';
        $retorno->data = $pk;

        return $retorno;
    }


    public function excluir($pk){
        Util::execDelete('documentos'," lancamentos_pk = ".$pk, $this->pdo);
        Util::execDelete('lancamentos_financeiros'," pk = ".$pk, $this->pdo);
        //Util::execDelete('lancamentos'," pk = ".$pk, $this->pdo);
    }
    public function listaContaEmpresa($contas_pk)
    {
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk";
        $sql.="  from contas_bancarias ";
        $sql.=" where empresas_pk =  ".$contas_pk;
        $sql.=" order by pk asc";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        if(count($rows)>0){
            $retorno->data = $rows[0];
        }
        else{
            $retorno->data = [];
        }
        return $retorno;
    }
    public function listaItensGrupoEquipes($tipo_grupo_pk){
        
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";
        $sql.="select e.pk, e.dt_cadastro, e.usuario_cadastro_pk, e.dt_ult_atualizacao, e.usuario_ult_atualizacao_pk ";
        $sql.="       ,e.ds_equipe ";
        $sql.="  from equipes e";
        $sql.=" where 1=1 ";
        if($tipo_grupo_pk!=""){
            $sql.=" and e.pk =".$tipo_grupo_pk;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        return $retorno;
    }

    public function listarcontratos($contratos_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";    
        $sql.="SELECT c.pk,";
        $sql.="    case WHEN c.ic_tipo_contrato =1 THEN";
        $sql.="      concat('FIXO',' - Cód:',c.pk,' - Periódo:',date_format(c.dt_inicio_contrato, '%d/%m/%Y'),' - ',date_format(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="     WHEN c.ic_tipo_contrato = 2 THEN";
        $sql.="      concat('Aditivo',' - Cód:',c.pk,' - Periódo:',date_format(c.dt_inicio_contrato, '%d/%m/%Y'),' - ',date_format(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="    WHEN c.ic_tipo_contrato =3 THEN";
        $sql.="      concat('EXTRA',' - Cód:',c.pk,' - Periódo:',date_format(c.dt_inicio_contrato, '%d/%m/%Y'),' - ',date_format(c.dt_fim_contrato, '%d/%m/%Y'))";        
        $sql.="    END ds_contrato";
        $sql.=" FROM contratos c";
        $sql.="   left join contratos_itens ci on c.pk = ci.contratos_pk";
        $sql.=" WHERE c.pk =".$contratos_pk;
        $sql.=" Group by c.pk";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        return $retorno;
        
        
    }
    public function listaItensGrupoLeads($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";
        $sql.="select l.pk, l.dt_cadastro, l.usuario_cadastro_pk, l.dt_ult_atualizacao, l.usuario_ult_atualizacao_pk ";
        $sql.="       , case WHEN l.ic_tipo_lead = 1 THEN concat(l.ds_lead,' - Cliente') 
                        ELSE
                        concat(l.ds_lead,' - Posto de Trabalho')
                    END ds_lead";
        $sql.="  from leads l";
        
        $sql.=" where 1=1 ";
        if($pk!=""){
            $sql.=" and l.pk =".$pk;
        }
        $sql.=" Order by l.ds_lead";
     

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        return $retorno;
    }

    public function listaItensGrupoColaboradores($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        
        $sql ="";
        $sql.="select c.pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk ";
        $sql.="       ,c.ds_colaborador ";
        $sql.="       ,c.ds_cpf ";
        $sql.="       ,c.ds_agencia";
        $sql.="       ,c.ds_conta";
        $sql.="       ,ct.ds_razao_social";
        $sql.="       ,c.ds_digito";
        $sql.="       ,c.ds_pix";
        $sql.="       ,c.ds_conta_favorecido";
        $sql.="       ,b.ds_banco";
        $sql.="  from colaboradores c";
        $sql.="  left join bancos b on c.bancos_pk = b.pk";
        $sql.="  left join contas ct on ct.pk = c.empresas_pk";
        $sql.=" where 1=1 ";
        if($pk!=""){
            $sql.=" and c.pk =".$pk;
        }
        
        $sql.=" Order by c.ds_colaborador";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        return $retorno;
    }
    public function listaItensGrupoFornecedores($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select f.pk, f.dt_cadastro, f.usuario_cadastro_pk, f.dt_ult_atualizacao, f.usuario_ult_atualizacao_pk ";
        $sql.="       ,f.ds_fornecedor ";
        $sql.="       ,f.ds_cpf_cnpj";
        $sql.="       ,f.ds_agencia";
        $sql.="       ,f.ds_conta";
        $sql.="       ,f.ds_digito";
        $sql.="       ,f.ds_pix";
        $sql.="       ,f.ds_favorecido_pix";
        $sql.="       ,b.ds_banco";
        $sql.="  from fornecedor f";
        $sql.="  left join bancos b on f.bancos_pk = b.pk";
        $sql.=" where 1=1 ";
        if($pk!=""){
            $sql.=" and f.pk =".$pk;
        }
        $sql.=" order by f.ds_fornecedor";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        return $retorno;
    }

	public function listarLancamentoPk($pk){

        $retorno = new \StdClass; 
        $retorno->status = false; 
        $retorno->data = []; 

        $sql = "
            SELECT 
                lf.pk, lf.dt_cadastro, lf.usuario_cadastro_pk, lf.dt_ult_atualizacao, lf.usuario_ult_atualizacao_pk, 
                lf.ds_lancamento, lf.tipos_operacao_pk, lf.categorias_financeiras_pk, lf.tipo_lancamento_pk, 
                lf.tipo_grupo_lancamento_pk, lf.grupo_lancamento_pk, lf.cliente_lancamento_pk, lf.posto_trabalho_lancamento_pk, 
                lf.contratos_pk, lf.ic_parcela, date_format(lf.dt_faturamento, '%d/%m/%Y') dt_faturamento, 
                date_format(lf.dt_vencimento, '%d/%m/%Y') dt_vencimento, lf.vl_lancamento, lf.metodos_pagamento_pk, 
                lf.empresa_lancamento_pk, lf.contas_bancarias_pk, lf.ic_status_lancamento, 
                date_format(lf.dt_pagamento, '%d/%m/%Y') dt_pagamento, lf.obs_lancamento, 
                lf.ic_tipo_num_documento, lf.ds_num_documento, 
                COUNT(lfbp.pk) count_baixa_parcial, SUM(lfbp.vl_baixa_parcial) vl_total_baixa
            FROM lancamentos_financeiros lf
            LEFT JOIN lancamentos_financeiros_baixa_parcial lfbp 
            ON lf.pk = lfbp.lancamentos_financeiros_pk
            WHERE lf.pk = :pk
            GROUP BY lf.pk
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':pk', $pk, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Garante que o array não está vazio antes de tentar acessar $rows[0]
        if (!empty($rows)) {
            $rows[0]['grupos_pk'] = $_SESSION['session_user']['par10'];
        }

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows[0];
        return $retorno;

    }

    public function listarHistoricoParcial($lancamentos_financeiros_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $extrato = [];
        if($lancamentos_financeiros_pk!=""){
            $sql ="";
            $sql .= "SELECT lfbp.pk,";
            $sql .= "       lfbp.dt_cadastro,";
            $sql .= "       lfbp.usuario_cadastro_pk,";
            $sql .= "       lfbp.dt_ult_atualizacao,";
            $sql .= "       lfbp.usuario_ult_atualizacao_pk,";
            $sql .= "       date_format(lfbp.dt_baixa_parcial, '%d/%m/%Y') dt_baixa_parcial,";
            $sql .= "       lfbp.vl_baixa_parcial,";
            $sql .= "       lfbp.empresa_lancamento_pk,";
            $sql .= "       lfbp.contas_bancarias_pk,";
            $sql .= "       lfbp.lancamentos_financeiros_pk,";
            $sql .= "       c.ds_conta ds_empresa,";
            $sql .= "       cb.ds_conta_bancaria";
            $sql .= "    FROM lancamentos_financeiros_baixa_parcial lfbp";
            $sql .="     LEFT JOIN contas c on lfbp.empresa_lancamento_pk = c.pk";
            $sql .="     LEFT JOIN contas_bancarias cb on lfbp.contas_bancarias_pk = cb.pk";
            $sql .= "   WHERE lfbp.lancamentos_financeiros_pk = ".$lancamentos_financeiros_pk;

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $retorno->data = $rows;
            $retorno->status = true;
            $retorno->message = 'Dados Salvos com sucesso !';
        }
        else{
            $retorno->data = [];
            $retorno->status = true;
            $retorno->message = 'Dados Salvos com sucesso !';
        }
        
        return $retorno;
        
    }
    public function listarLancamentosUsuarios($pk, $ic_status_pagamento, $usuario_cadastro_pk, $empresas_pk, $contas_pk,$tipo_grupo_pk, $grupo_lancamento_pk, $dt_cadastro_ini, $dt_cadastro_fim, $dt_faturamento_ini, $dt_faturamento_fim, $dt_vencimento_ini, $dt_vencimento_fim, $dt_pagamento_ini, $dt_pagamento_fim, $ic_status_analise, $ds_num_documento, $ic_tipo_num_documento){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $extrato = [];

        
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
        $sql.="SELECT l.pk,";
        $sql.="      date_format(l.dt_cadastro, '%d/%m/%Y %H:%i')dt_cadastro,";
        $sql.="      c.ds_conta ds_empresa,";
        $sql.="      cb.ds_conta_bancaria,";
        $sql.="      SUBSTRING(u.ds_usuario,1, 15) ds_usuario,";
        $sql.="      date_format(l.dt_vencimento, '%d/%m/%Y')dt_vencimento,";
        $sql.="      date_format(l.dt_faturamento, '%d/%m/%Y') dt_faturamento,";
        $sql.="      date_format(l.dt_pagamento, '%d/%m/%Y') dt_pagamento,";
        $sql.="      CASE WHEN l.ic_status_lancamento = 1 THEN 'PAGO'";
        $sql.="           WHEN l.ic_status_lancamento = 2 THEN 'PENDENTE'";
        $sql.="           WHEN l.ic_status_lancamento = 3 THEN 'APROVADO'";
        $sql.="           WHEN l.ic_status_lancamento = 4 THEN 'ATRASADO'";
        $sql.="           WHEN l.ic_status_lancamento = 5 THEN 'CANCELADO'";
        $sql.="       END ds_status_pagamento,";
        $sql.="      l.ic_status_lancamento,";
        $sql.="      CASE l.tipo_lancamento_pk";
        $sql.="           WHEN 1 THEN 'Receita'";
        $sql.="           WHEN 2 THEN 'Despesa Fixa'";
        $sql.="           WHEN 3 THEN 'Despesa Variada'";
        $sql.="           WHEN 4 THEN 'Imposto'";
        $sql.="           WHEN 5 THEN 'Transferência'";
        $sql.="           WHEN 6 THEN 'Caixinha'";
        $sql.="       END ds_operacao,";
        $sql.="      l.tipos_operacao_pk,";
        $sql.="      top.ds_tipo_operacao,";
        $sql.="      CASE l.tipo_grupo_lancamento_pk";
        $sql.="          WHEN 1 THEN '(Clientes)'";
        $sql.="          WHEN 2 THEN 'Colaboradores'";
        $sql.="          WHEN 3 THEN 'Fornecedores'";
        $sql.="          WHEN 4 THEN 'Outros'";
        $sql.="       END  ds_tipo_grupo,";
        $sql.="       mp.ds_metodo_pagamento,";
        $sql.="       l.vl_lancamento,";
        $sql.="       l.tipo_grupo_lancamento_pk,";
        $sql.="       l.tipo_lancamento_pk,";
        $sql.="       l.ds_lancamento,";
        $sql.="       l.grupo_lancamento_pk";
        $sql.="       ,CASE";
        $sql.="          WHEN af.ic_status = 1 THEN 'Não Analisado'";
        $sql.="          WHEN af.ic_status = 2 THEN 'Aprovado Analista'";
        $sql.="          WHEN af.ic_status = 3 THEN 'Aprovado Gestor'";
        $sql.="          WHEN af.ic_status = 4 THEN 'Correção Solicitada'";
        $sql.="          WHEN af.ic_status = 5 THEN 'Recusado'";
        $sql.="          WHEN af.ic_status = 6 THEN 'Correção Feita'";
        $sql.="          WHEN af.ic_status = 7 THEN 'Cancelado'";
        $sql.="          END ds_status_analise";
        $sql.="       ,CASE";
        $sql.="          WHEN l.tipo_grupo_lancamento_pk = 1 THEN le.ds_lead";
        $sql.="          WHEN l.tipo_grupo_lancamento_pk = 2 THEN co.ds_colaborador";
        $sql.="          WHEN l.tipo_grupo_lancamento_pk = 3 THEN fo.ds_fornecedor";
        $sql.="          END ds_recebido_pago_origem";
        $sql.="       ,l.vl_lancamento";
        $sql.="       ,CASE";
        $sql.="          WHEN l.dt_vencimento <= sysdate() THEN l.vl_lancamento";
        $sql.="          END vl_lancamento_pendente";
    
        $sql.=" FROM lancamentos_financeiros l";
        $sql.="     LEFT JOIN contas c on l.empresa_lancamento_pk = c.pk";
        $sql.="     LEFT JOIN contas_bancarias cb on l.contas_bancarias_pk = cb.pk";
        $sql.="     INNER JOIN metodos_pagamento mp ON l.metodos_pagamento_pk = mp.pk";
        $sql.="     LEFT JOIN tipos_operacao top ON l.tipos_operacao_pk = top.pk";
        $sql.="     INNER JOIN usuarios u ON l.usuario_cadastro_pk = u.pk";
        $sql.="     LEFT JOIN analise_financeira af ON l.pk = af.lancamentos_pk";
        $sql.="     LEFT JOIN leads le ON l.grupo_lancamento_pk = le.pk and tipo_grupo_lancamento_pk = 1";
        $sql.="     LEFT JOIN colaboradores co ON l.grupo_lancamento_pk = co.pk and tipo_grupo_lancamento_pk = 2";
        $sql.="     LEFT JOIN fornecedor fo ON l.grupo_lancamento_pk = fo.pk and tipo_grupo_lancamento_pk = 3";
        $sql.=" WHERE 1=1";
        $ic_analise_financeira = (new Conta($this->pdo))->configModulo();
        $ic_analise_financeira = $ic_analise_financeira->data[0]['ic_analise_financeira'];
        if(!empty($ic_status_analise)){
            $sql.=" AND af.ic_status = $ic_status_analise";
        }

        if(!empty($dt_vencimento_ini) and !empty($dt_vencimento_fim)){
            $sql.=" AND l.dt_vencimento >= '".Util::DataYMD($dt_vencimento_ini)." 00:00:00'";
            $sql.=" AND l.dt_vencimento <= '".Util::DataYMD($dt_vencimento_fim)." 23:59:00'";
        }elseif (!empty($dt_cadastro_ini) and !empty($dt_cadastro_fim)) {
            $sql.=" AND l.dt_cadastro >= '".Util::DataYMD($dt_cadastro_ini)." 00:00:00'";
            $sql.=" AND l.dt_cadastro <= '".Util::DataYMD($dt_cadastro_fim)." 23:59:00'"; 
        }elseif (!empty($dt_faturamento_ini) and !empty($dt_faturamento_fim)) {
            $sql.=" AND l.dt_faturamento >= '".Util::DataYMD($dt_faturamento_ini)." 00:00:00'";
            $sql.=" AND l.dt_faturamento <= '".Util::DataYMD($dt_faturamento_fim)." 23:59:00'"; 
        }elseif (!empty($dt_pagamento_ini) and !empty($dt_pagamento_fim)) {
            $sql.=" AND l.dt_pagamento >= '".Util::DataYMD($dt_pagamento_ini)." 00:00:00'";
            $sql.=" AND l.dt_pagamento <= '".Util::DataYMD($dt_pagamento_fim)." 23:59:00'"; 
        }elseif(empty($pk)){
            //if(empty($ic_status_pagamento)){   
                if(!empty($dt_vencimento_ini) and !empty($dt_vencimento_fim)){       
                    $sql.=" AND l.dt_vencimento >= '".Util::DataYMD($dt_vencimento_ini)." 00:00:00'";
                    $sql.=" AND l.dt_vencimento <= '".Util::DataYMD($dt_vencimento_fim)." 23:59:00'";  
                }
            /* }else{
                if($ic_status_pagamento==1){
                    $sql.=" AND l.dt_pagamento is not null";
                }elseif($ic_status_pagamento==4){
                    $sql.=" AND l.dt_pagamento is null";
                    $sql.=" AND l.dt_vencimento < sysdate()";
                    $sql.=" AND l.ic_status_pagamento<>1";
                }elseif($ic_status_pagamento==2){
                    $sql.=" AND l.ic_status_pagamento=".$ic_status_pagamento;
                    $sql.=" AND l.dt_pagamento is null";
                }elseif($ic_status_pagamento==2){
                    $sql.=" AND l.ic_status_pagamento=".$ic_status_pagamento;
                    $sql.=" AND l.dt_pagamento is null";    
                }else{
                    $sql.=" AND l.ic_status_pagamento=".$ic_status_pagamento;
                }  
            }   */                                    
        }

        if(!empty($ic_status_pagamento)){
            $sql.=" AND l.ic_status_pagamento=".$ic_status_pagamento;
        }  
        
        if(!empty($pk)){
            $sql.=" AND l.pk=".$pk;
        }
        
        if(!empty($usuario_cadastro_pk)){
            $sql.=" AND l.usuario_cadastro_pk=".$usuario_cadastro_pk;
        }
        
        if(!empty($empresas_pk)){
            $sql.=" AND l.empresa_lancamento_pk=".$empresas_pk;
        }

        if(!empty($contas_pk)){
            $sql.=" AND l.contas_bancarias_pk=".$contas_pk;
        }
        
        if(!empty($tipo_grupo_pk)){
            $sql.=" AND l.tipo_grupo_lancamento_pk=".$tipo_grupo_pk;
        }

        if(!empty($grupo_lancamento_pk)){
            $sql.=" AND l.grupo_lancamento_pk=".$grupo_lancamento_pk;
        }
        if(!empty($ds_num_documento)){
            $sql.=" AND l.ds_num_documento like '%".$ds_num_documento."%'";
        }
        if(!empty($ic_tipo_num_documento)){
            $sql.=" AND l.ic_tipo_num_documento = ".$ic_tipo_num_documento;
        }
        $sql.=" ORDER BY l.dt_vencimento desc";

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

    public function listaLeads($tipo_grupo_pk){

        $sql ="";
        $sql.="select l.pk, l.ds_cpf_cnpj, l.dt_cadastro, l.usuario_cadastro_pk, l.dt_ult_atualizacao, l.usuario_ult_atualizacao_pk ";
        $sql.="       , case WHEN l.ic_tipo_lead = 1 THEN concat(l.ds_lead,' - Cliente') 
                        ELSE
                        concat(l.ds_lead,' - Posto de Trabalho')
                    END ds_lead";
        $sql.="  from leads l";
        $sql.=" where 1=1 ";
        if($tipo_grupo_pk!=""){
            $sql.=" and l.pk =".$tipo_grupo_pk;
        }
        $sql.=" Order by l.ds_lead";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $rows;
    }

    public function listarImpressao($pk){

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $ds_recebido_de = "";
        $ds_recebido_de_centro_custo = "";
        $ds_lancamento_posto_trabalho = "";

        $sql ="";
        $sql.="select l.obs_lancamento obs ";
        $sql.="       ,l.pk lancamentos_pk";
        $sql.="       ,l.tipos_operacao_pk";
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
        $sql.="       ,date_format(l.dt_cadastro,'%d/%m/%Y') dt_cadastro";
        $sql.="       ,l.ic_parcela";
        $sql.="       ,l.grupo_lancamento_pk";
        $sql.="       ,CASE";
        $sql.="          WHEN l.tipo_grupo_lancamento_pk = 1 THEN 'Cliente(s)'";
        $sql.="          WHEN l.tipo_grupo_lancamento_pk = 2 THEN 'Colaboradores'";
        $sql.="          WHEN l.tipo_grupo_lancamento_pk = 3 THEN 'Fornecedores'";
        $sql.="          END ds_tipo_grupo";
        $sql.="       ,l.tipo_grupo_lancamento_pk";
        $sql.="       ,l.grupo_lancamento_pk";
        $sql.="       ,u.ds_usuario";
        $sql.="       ,mp.ds_metodo_pagamento";
        $sql.="       ,c.ds_conta";
        $sql.="       ,cb.vl_saldo_inicial";
        $sql.="       ,le.ds_lead ds_cliente ";
        $sql.="       ,top.ds_tipo_operacao";
        $sql.="       ,co.ds_razao_social";
        $sql.="       ,l.obs_lancamento";
        $sql.="       ,l.empresa_lancamento_pk";
        $sql.="       ,CASE";
        $sql.="          WHEN l.tipo_grupo_lancamento_pk = 1 THEN le.ds_lead";
        $sql.="          WHEN l.tipo_grupo_lancamento_pk = 2 THEN cl.ds_colaborador";
        $sql.="          WHEN l.tipo_grupo_lancamento_pk = 3 THEN fo.ds_fornecedor";
        $sql.="          END ds_recebido_pago_origem";
        $sql.="        ,date_format(l.dt_faturamento, '%d/%m/%Y') dt_faturamento";
        $sql.="       ,CASE WHEN l.ic_status_lancamento = 1 THEN 'PAGO'";
        $sql.="           WHEN l.ic_status_lancamento = 2 THEN 'PENDENTE'";
        $sql.="           WHEN l.ic_status_lancamento = 3 THEN 'APROVADO'";
        $sql.="           WHEN l.ic_status_lancamento = 4 THEN 'ATRASADO'";
        $sql.="           WHEN l.ic_status_lancamento = 5 THEN 'CANCELADO'";
        $sql.="       END ds_status_pagamento";
        $sql.="       ,l.ic_status_lancamento";
        $sql.="       ,concat(cb.ds_conta_bancaria, ' - Agência: ', cb.ds_agencia, ' - Conta: ', cb.ds_conta) ds_conta_bancaria";
        $sql.="  from lancamentos_financeiros l";
        $sql.="  LEFT JOIN usuarios u ON l.usuario_cadastro_pk = u.pk";
        $sql.="  LEFT JOIN metodos_pagamento mp ON l.metodos_pagamento_pk = mp.pk";
        $sql.="  LEFT JOIN contas c ON l.empresa_lancamento_pk = c.pk";
        $sql.="  LEFT JOIN contas_bancarias cb ON l.contas_bancarias_pk = cb.pk";
        $sql.="  LEFT join contas co on l.empresa_lancamento_pk = co.pk";    
        $sql.="  LEFT JOIN leads le ON l.grupo_lancamento_pk = le.pk and tipo_grupo_lancamento_pk = 1";
        $sql.="  LEFT JOIN colaboradores cl ON l.grupo_lancamento_pk = cl.pk and tipo_grupo_lancamento_pk = 2";
        $sql.="  LEFT JOIN fornecedor fo ON l.grupo_lancamento_pk = fo.pk and tipo_grupo_lancamento_pk = 3";
        $sql.="  LEFT JOIN ocorrencias o on o.leads_pk = l.pk ";
        $sql.="  LEFT JOIN tipos_operacao top ON l.tipos_operacao_pk = top.pk";
        $sql.=" where l.pk = $pk ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        

        //Posto de trabalho
        $lancamento_posto_trabalho_pk = $rows[0]['posto_trabalho_lancamento_pk'];
        
        $queryLead = $this->listaItensGrupoLeads($rows[0]['grupo_lancamento_pk']);
        $ds_recebido_de_centro_custo = "";
        //CENTRO CUSTO
        if(count($queryLead->data)>0){
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

        
        if(!empty($lancamento_posto_trabalho_pk)){
            $queryPostoTrabalho = $this->listaItensGrupoLeads($lancamento_posto_trabalho_pk);
            $ds_lancamento_posto_trabalho = $queryPostoTrabalho->data[0]['ds_lead'];              
        }

        //Contratos
        $lancamento_contrato_pk = "";
        $ds_produto_servico = "";               
        $lancamento_contrato_pk = $rows[0]['contratos_pk'];
        
        $ds_lancamento_contrato= "";
        if(!empty($lancamento_contrato_pk)){
            $queryContrato = $this->listarcontratos($lancamento_contrato_pk);
            $ds_lancamento_contrato = $queryContrato->data[0]['ds_contrato'];  
            $queryProdutoServico = (new ProdutoServico($this->pdo))->listarProdutosContrato($lancamento_contrato_pk);
            $ds_produto_servico =  $queryProdutoServico->data[0]['ds_produto_servico'];   

            if(!empty($ds_produto_servico)){
                $ds_lancamento_contrato  = $ds_lancamento_contrato." Serviço: ".$ds_produto_servico; 
            }                                   
        }

        $mysql_data[] = array(
            "lancamentos_pk" => $rows[0]["lancamentos_pk"],
            "dt_vencimento"=>$rows[0]['dt_vencimento'],
            "ds_usuario_cadastro"=>$rows[0]['ds_usuario'],
            "dt_cadastro"=>$rows[0]['dt_cadastro'],
            "vl_inicial_conta"=>$rows[0]['vl_saldo_inicial'],
            "dt_pagamento"=>$rows[0]['dt_pagamento'],
            "vl_saldo"=>number_format((($rows[0]['vl_lancamento']-$rows[0]['vl_lancamento']) ),2,",","."),
            "ds_lancamento"=>$rows[0]['ds_lancamento'],
            "vl_lancamento"=>($rows[0]['vl_lancamento']),
            "operacao_pk"=>$rows[0]['tipo_lancamento_pk'],
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
            "empresas_pk"=>$rows[0]['empresa_lancamento_pk'],
            "ds_razao_social"=>$rows[0]['ds_razao_social'],
            "ds_dados_conta"=>$rows[0]['ds_conta_bancaria'],
            "grupo_lancamento_centro_custo_pk"=>$rows[0]['grupo_lancamento_pk'],
            //"ds_ocorrencia"=>$rows[0]['ds_ocorrencia'],
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

    public function listarColaboradores($tipo_grupo_pk){

        $sql ="";
        $sql.="select c.pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk ";
        $sql.="       ,c.ds_colaborador ";
        $sql.="       ,c.ds_cpf ";
        $sql.="       ,c.ds_agencia";
        $sql.="       ,c.ds_conta";
        $sql.="       ,c.ds_digito";
        $sql.="       ,c.ds_pix";
        $sql.="       ,c.ds_conta_favorecido";
        $sql.="       ,b.ds_banco";
        $sql.="  from colaboradores c";
        $sql.="  left join bancos b on c.bancos_pk = b.pk";
        $sql.=" where 1=1 ";
        if($tipo_grupo_pk!=""){
            $sql.=" and c.pk =".$tipo_grupo_pk;
        }
        $sql.=" Order by c.ds_colaborador";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $rows;
    }

    public function listarFornecedores($tipo_grupo_pk){

        $sql ="";
        $sql.="select f.pk, f.dt_cadastro, f.usuario_cadastro_pk, f.dt_ult_atualizacao, f.usuario_ult_atualizacao_pk ";
        $sql.="       ,f.ds_fornecedor ";
        $sql.="       ,f.ds_cpf_cnpj";
        $sql.="       ,f.ds_agencia";
        $sql.="       ,f.ds_conta";
        $sql.="       ,f.ds_digito";
        $sql.="       ,f.ds_pix";
        $sql.="       ,f.ds_favorecido_pix";
        $sql.="       ,b.ds_banco";
        $sql.="  from fornecedor f";
        $sql.="  left join bancos b on f.bancos_pk = b.pk";
        $sql.=" where 1=1 ";
        if($tipo_grupo_pk!=""){
            $sql.=" and f.pk =".$tipo_grupo_pk;
        }
        $sql.=" order by f.ds_fornecedor";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $rows;
    }
    public function listaPrimeiroLancamento($empresas_pk,$contas_bancarias_pk){
        $sql ="";
        $sql.="SELECT date_format(MIN(l.dt_vencimento), '%m') mes_pri_vencimento";
        $sql.="      ,date_format(MIN(l.dt_vencimento), '%Y') ano_pri_vencimento";
        $sql.=" FROM lancamentos_financeiros l";
        $sql.=" WHERE l.empresa_lancamento_pk = ".$empresas_pk;
        $sql.="   AND l.contas_bancarias_pk = ".$contas_bancarias_pk;
        $sql.="   AND  DATE_FORMAT(l.dt_vencimento, '%m') <> 00";
        $sql.="   AND  DATE_FORMAT(l.dt_vencimento, '%Y') <> 0000";
        $sql.=" ORDER BY l.dt_vencimento";


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $rows;

    }
    public function listarExtrato($empresas_pk,$contas_bancarias_pk,$mes_pri_vencimento,$ano_pri_vencimento, $ds_mes,$ds_ano){
        $sql ="";
        $sql.="SELECT l.pk,";
        $sql.="        date_format(l.dt_cadastro, '%d/%m/%Y') dt_cadastro,";
        $sql.="        date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento,";
        $sql.="        date_format(l.dt_pagamento, '%d/%m/%Y') dt_pagamento,";
        $sql.="        date_format(l.dt_faturamento, '%d/%m/%Y') dt_faturamento,";
        $sql.="        CASE l.tipos_operacao_pk";
        $sql.="           WHEN 1 THEN 'Receita'";
        $sql.="           WHEN 2 THEN 'Despesa Fixa'";
        $sql.="           WHEN 3 THEN 'Despesa Variada'";
        $sql.="           WHEN 4 THEN 'Imposto'";
        $sql.="           WHEN 5 THEN 'Transferência'";
        $sql.="           WHEN 6 THEN 'Caixinha'";
        $sql.="        END ds_operacao,";
        $sql.="        CASE l.tipo_grupo_lancamento_pk";
        $sql.="           WHEN 1 THEN '(Clientes)'";
        $sql.="           WHEN 2 THEN 'Colaboradores'";
        $sql.="           WHEN 3 THEN 'Fornecedores'";
        $sql.="           WHEN 4 THEN 'Outros'";
        $sql.="        END ds_tipo_grupo,";
        $sql.="        mp.ds_metodo_pagamento,";
        $sql.="        l.tipos_operacao_pk,";
        $sql.="        l.tipo_grupo_lancamento_pk,";
        $sql.="        l.tipo_lancamento_pk,";
        $sql.="        l.vl_lancamento,";
        $sql.="        u.ds_usuario,";
        $sql.="        l.grupo_lancamento_pk,"; 
        $sql.="        top.ds_tipo_operacao,"; 
        $sql.="        l.ic_status_lancamento"; 
        $sql.=" FROM lancamentos_financeiros l";
        $sql.="      INNER JOIN metodos_pagamento mp ON l.metodos_pagamento_pk = mp.pk";
        $sql.="      iNNER JOIN tipos_operacao top on l.tipos_operacao_pk = top.pk";
        $sql.="      LEFT JOIN usuarios u ON l.usuario_cadastro_pk = u.pk";
        $sql.=" WHERE l.empresa_lancamento_pk = ".$empresas_pk;
        $sql.="       AND l.contas_bancarias_pk = ".$contas_bancarias_pk;
        $sql.="       AND l.dt_vencimento >='".$ano_pri_vencimento."-".$mes_pri_vencimento."-01 00:00:00'";
        $sql.="       AND l.dt_vencimento <='".$ds_ano."-".$ds_mes."-".cal_days_in_month(CAL_GREGORIAN, $ds_mes , $ds_ano)." 23:59:59'";
        $sql.=" ORDER BY l.dt_vencimento";
        //echo $sql;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $rows;
    }
    public function listarReceita($pk,$ic_status_lancamento,$ds_lancamento, $usuario_cadastro_pk,$empresas_pk,$tipo_grupo_lancamento_pk,$grupo_lancamento_pk,$dt_cadastro_ini,$dt_cadastro_fim,$dt_faturamento_ini,$dt_faturamento_fim,$dt_vencimento_ini,$dt_vencimento_fim,$dt_pagamento_ini,$dt_pagamento_fim,  $ds_num_documento,$ic_tipo_num_documento, $categorias_financeiras_pk, $tipo_lancamento_pk, $tipos_operacao_pk, $contas_bancarias_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="SELECT l.pk,";
        $sql.="      date_format(l.dt_cadastro, '%d/%m/%Y %H:%i')dt_cadastro,";
        $sql.="      c.ds_conta ds_empresa,";
        $sql.="      cb.ds_conta_bancaria,";
        $sql.="      SUBSTRING(u.ds_usuario,1, 15) ds_usuario,";
        $sql.="      date_format(l.dt_vencimento, '%d/%m/%Y')dt_vencimento,";
        $sql.="      date_format(l.dt_faturamento, '%d/%m/%Y') dt_faturamento,";
        $sql.="      date_format(l.dt_pagamento, '%d/%m/%Y') dt_pagamento,";
        $sql.="      CASE l.ic_status_lancamento";
        $sql.="        WHEN 1 THEN 'PAGO'";
        $sql.="        WHEN 2 THEN 'PENDENTE'";
        $sql.="        WHEN 3 THEN 'APROVADO'";
        $sql.="        WHEN 4 THEN 'ATRASADO'";
        $sql.="        WHEN 5 THEN 'CANCELADO'";
        $sql.="        WHEN 6 THEN 'BAIXA PARCIAL'";
        $sql.="      END ds_status_pagamento,";
        $sql.="      l.ic_status_lancamento,";
        $sql.="      CASE l.tipo_lancamento_pk";
        $sql.="          WHEN 1 THEN 'Receita'";
        $sql.="          WHEN 2 THEN 'Despesa Fixa'";
        $sql.="          WHEN 3 THEN 'Despesa Variada'";
        $sql.="          WHEN 4 THEN 'Imposto'";
        $sql.="          WHEN 5 THEN 'Transfer ncia'";
        $sql.="          WHEN 6 THEN 'Caixinha'";
        $sql.="      END ds_operacao,";
        $sql.="      top.ds_tipo_operacao,";
        $sql.="      CASE l.tipo_grupo_lancamento_pk";
        $sql.="          WHEN 1 THEN '(Clientes)'";
        $sql.="          WHEN 2 THEN 'Colaboradores'";
        $sql.="          WHEN 3 THEN 'Fornecedores'";
        $sql.="          WHEN 4 THEN 'Outros'";
        $sql.="       END  ds_tipo_grupo,";
        $sql.="       mp.ds_metodo_pagamento,";
        $sql.="       l.tipo_lancamento_pk,";
        $sql.="       l.tipo_grupo_lancamento_pk,";
        $sql.="       l.vl_lancamento,";
        $sql.="       l.ds_lancamento,";
        $sql.="       l.grupo_lancamento_pk,";
        $sql.="       l.nfse_pk,";
        $sql.="       SUM(lfbp.vl_baixa_parcial) vl_baixa_parcial";
        $sql.=" FROM lancamentos_financeiros l";
        $sql.="     LEFT JOIN contas c on l.empresa_lancamento_pk = c.pk";
        $sql.="     LEFT JOIN contas_bancarias cb on l.contas_bancarias_pk = cb.pk";
        $sql.="     LEFT JOIN metodos_pagamento mp ON l.metodos_pagamento_pk = mp.pk";
        $sql.="     LEFT JOIN tipos_operacao top ON l.tipos_operacao_pk = top.pk";
        $sql.="     LEFT JOIN usuarios u ON l.usuario_cadastro_pk = u.pk";
        $sql.="     LEFT JOIN lancamentos_financeiros_baixa_parcial lfbp ON lfbp.lancamentos_financeiros_pk = l.pk";
        $sql.=" WHERE l.tipo_lancamento_pk = 1";
        
        if(!empty($dt_vencimento_ini) and !empty($dt_vencimento_fim)){

            $sql.=" AND l.dt_vencimento >= '".Util::DataYMD($dt_vencimento_ini)." 00:00:00'";
            $sql.=" AND l.dt_vencimento <= '".Util::DataYMD($dt_vencimento_fim)." 23:59:00'";
            // Status
            /*if($ic_status_lancamento==1){
                $sql.=" AND l.dt_pagamento is not null";
            }elseif($ic_status_lancamento==4){
                $sql.=" AND l.dt_pagamento is null";
                $sql.=" AND l.dt_vencimento < sysdate()";
                $sql.=" AND l.ic_status_lancamento<>1";
            }elseif($ic_status_lancamento==2){
                $sql.=" AND l.ic_status_lancamento=".$ic_status_lancamento;
                $sql.=" AND l.dt_pagamento is null";
            }elseif($ic_status_lancamento==2){
                $sql.=" AND l.ic_status_lancamento=".$ic_status_lancamento;
                $sql.=" AND l.dt_pagamento is null";    
            } */  

        }elseif (!empty($dt_cadastro_ini) and !empty($dt_cadastro_fim)) {
            $sql.=" AND l.dt_cadastro >= '".Util::DataYMD($dt_cadastro_ini)." 00:00:00'";
            $sql.=" AND l.dt_cadastro <= '".Util::DataYMD($dt_cadastro_fim)." 23:59:00'"; 

            // Status
            /*if($ic_status_lancamento==1){
                $sql.=" AND l.dt_pagamento is not null";
            }elseif($ic_status_lancamento==4){
                $sql.=" AND l.dt_pagamento is null";
                $sql.=" AND l.dt_vencimento < sysdate()";
                $sql.=" AND l.ic_status_lancamento<>1";
            }elseif($ic_status_lancamento==2){
                $sql.=" AND l.ic_status_lancamento=".$ic_status_lancamento;
                $sql.=" AND l.dt_pagamento is null";
            }elseif($ic_status_lancamento==2){
                $sql.=" AND l.ic_status_lancamento=".$ic_status_lancamento;
                $sql.=" AND l.dt_pagamento is null";    
            }*/
        }elseif (!empty($dt_faturamento_ini) and !empty($dt_faturamento_fim)) {
            $sql.=" AND l.dt_faturamento >= '".Util::DataYMD($dt_faturamento_ini)." 00:00:00'";
            $sql.=" AND l.dt_faturamento <= '".Util::DataYMD($dt_faturamento_fim)." 23:59:00'"; 

            // Status
            /*if($ic_status_lancamento==1){
                $sql.=" AND l.dt_pagamento is not null";
            }elseif($ic_status_lancamento==4){
                $sql.=" AND l.dt_pagamento is null";
                $sql.=" AND l.dt_vencimento < sysdate()";
                $sql.=" AND l.ic_status_lancamento<>1";
            }elseif($ic_status_lancamento==2){
                $sql.=" AND l.ic_status_lancamento=".$ic_status_lancamento;
                $sql.=" AND l.dt_pagamento is null";
            }elseif($ic_status_lancamento==2){
                $sql.=" AND l.ic_status_lancamento=".$ic_status_lancamento;
                $sql.=" AND l.dt_pagamento is null";    
            }*/

        }elseif (!empty($dt_pagamento_ini) and !empty($dt_pagamento_fim)) {
            $sql.=" AND l.dt_pagamento >= '".Util::DataYMD($dt_pagamento_ini)." 00:00:00'";
            $sql.=" AND l.dt_pagamento <= '".Util::DataYMD($dt_pagamento_fim)." 23:59:00'"; 

            // Status
            /*if($ic_status_lancamento==1){
                $sql.=" AND l.dt_pagamento is not null";
            }elseif($ic_status_lancamento==4){
                $sql.=" AND l.dt_pagamento is null";
                $sql.=" AND l.dt_vencimento < sysdate()";
                $sql.=" AND l.ic_status_lancamento<>1";
            }elseif($ic_status_lancamento==2){
                $sql.=" AND l.ic_status_lancamento=".$ic_status_lancamento;
                $sql.=" AND l.dt_pagamento is null";
            }elseif($ic_status_lancamento==2){
                $sql.=" AND l.ic_status_lancamento=".$ic_status_lancamento;
                $sql.=" AND l.dt_pagamento is null";    
            }*/

        }elseif(empty($pk)){
            //if(empty($ic_status_lancamento)){   
                if(!empty($dt_vencimento_ini) and !empty($dt_vencimento_fim)){       
                    $sql.=" AND l.dt_vencimento >= '".Util::DataYMD($dt_vencimento_ini)." 00:00:00'";
                    $sql.=" AND l.dt_vencimento <= '".Util::DataYMD($dt_vencimento_fim)." 23:59:00'";  
                }
            /*}else{
                if($ic_status_lancamento==1){
                    $sql.=" AND l.dt_pagamento is not null";
                }elseif($ic_status_lancamento==4){
                    $sql.=" AND l.dt_pagamento is null";
                    $sql.=" AND l.dt_vencimento < sysdate()";
                    $sql.=" AND l.ic_status_lancamento<>1";
                }elseif($ic_status_lancamento==2){
                    $sql.=" AND l.ic_status_lancamento=".$ic_status_lancamento;
                    $sql.=" AND l.dt_pagamento is null";
                }elseif($ic_status_lancamento==2){
                    $sql.=" AND l.ic_status_lancamento=".$ic_status_lancamento;
                    $sql.=" AND l.dt_pagamento is null";    
                }    
            }    */                                   
        }
        
        if(!empty($ic_status_lancamento)){
            $sql.=" AND l.ic_status_lancamento=".$ic_status_lancamento;
        }  
        if(!empty($pk)){
            $sql.=" AND l.pk=".$pk;
        }
        if(!empty($usuario_cadastro_pk)){
            $sql.=" AND l.usuario_cadastro_pk=".$usuario_cadastro_pk;
        }
        if(!empty($empresas_pk)){
            $sql.=" AND l.empresa_lancamento_pk=".$empresas_pk;
        }
        if(!empty($contas_bancarias_pk)){
            $sql.=" AND l.contas_bancarias_pk=".$contas_bancarias_pk;
        }
        if(!empty($tipo_grupo_lancamento_pk)){
            $sql.=" AND l.tipo_grupo_lancamento_pk=".$tipo_grupo_lancamento_pk;
        }
        if(!empty($grupo_lancamento_pk)){
            $sql.=" AND l.grupo_lancamento_pk=".$grupo_lancamento_pk;
        }
        if(!empty($ds_num_documento)){
            $sql.=" AND l.ds_num_documento like '%".$ds_num_documento."%'";
        }
        if(!empty($ds_lancamento)){
            $sql.=" AND l.ds_lancamento like '%".$ds_lancamento."%'";
        }
        if(!empty($ic_tipo_num_documento)){
            $sql.=" AND l.ic_tipo_num_documento = ".$ic_tipo_num_documento;
        }
        if(!empty($tipos_operacao_pk)){
            $sql.=" AND l.tipos_operacao_pk = ".$tipos_operacao_pk;
        }
        if(!empty($categorias_financeiras_pk)){
            $sql.=" AND l.categorias_financeiras_pk = ".$categorias_financeiras_pk;
        }
        if(!empty($tipo_lancamento_pk)){
            $sql.=" AND l.tipo_lancamento_pk = ".$tipo_lancamento_pk;
        }
        if(!empty($tipos_operacao_pk)){
            $sql.=" AND l.tipos_operacao_pk = ".$tipos_operacao_pk;
        }

        $sql.=" GROUP BY l.pk";
        $sql.=" ORDER BY l.dt_vencimento asc";
    

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $result = [];
        if(count($query) > 0){
            $vl_receita = 0.00;
            $vl_receita_pendente = 0.00;
            $vl_total_dia = 0.00;
            $vl_receita_pendente_dia = 0.00;
            $vl_receita_dia = 0.00;
            $vl_lancamento_pendente = 0.00;
            for($i = 0; $i < count($query); $i++){
                if($query[$i]['tipo_grupo_lancamento_pk']==1){
                    $queryLead = $this->listaLeads($query[$i]['grupo_lancamento_pk']);
                    $ds_recebido_de = $queryLead[0]['ds_lead'];
                    $ds_cpf_cnpj = $queryLead[0]['ds_cpf_cnpj'];
                }else if($query[$i]['tipo_grupo_lancamento_pk']==2){
                    $queryLead = $this->listarColaboradores($query[$i]['grupo_lancamento_pk']);
                    $ds_recebido_de = $queryLead[0]['ds_colaborador'];
                    $ds_cpf_cnpj = $queryLead[0]['ds_cpf'];
                }else if($query[$i]['tipo_grupo_lancamento_pk']==3){
                    $queryLead = $this->listarFornecedores($query[$i]['grupo_lancamento_pk']);
                    $ds_recebido_de = $queryLead[0]['ds_fornecedor'];
                    $ds_cpf_cnpj = $queryLead[0]['ds_cpf_cnpj'];
                }

                if($query[$i]["tipo_lancamento_pk"]==1){
                    $vl_receita += $query[$i]["vl_lancamento"];
                }
                    
                if($query[$i]["tipo_lancamento_pk"]==1 and $query[$i]["dt_pagamento"]==""){
                    $vl_receita_pendente += $query[$i]["vl_lancamento"];
                    if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                        $vl_receita_pendente = $vl_receita_pendente - $query[$i]["vl_baixa_parcial"];
                    }
                }    
                

                $data = $query[$i]["dt_vencimento"];
                $l = $i ;
                if((count($query)-1) == $i){
                    $a = $i;
                }
                else{
                    $a = $i + 1;
                }
                
                $data_anterior = $query[$l]["dt_vencimento"];
                if($data_anterior == null){
                    $data_anterior = "";
                }
                $proxima_data = $query[$a]["dt_vencimento"];
                if($proxima_data == null){
                    $proxima_data = "";
                }
                $vl_lancamento = $query[$i]["vl_lancamento"];

                if($data_anterior == $data){
                    $vl_total_dia += $query[$i]["vl_lancamento"];
                    $vl_lancamento_pendente = 0.00;
                    if($query[$i]["tipo_lancamento_pk"]==1){
                        $vl_receita_dia += $query[$i]["vl_lancamento"];
                    }        
                    if($query[$i]["tipo_lancamento_pk"]==1 and $query[$i]["dt_pagamento"]==""){
                        $vl_receita_pendente_dia += $query[$i]["vl_lancamento"];
                        $vl_lancamento_pendente = $query[$i]["vl_lancamento"];
                        if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                            $vl_receita_pendente_dia = $vl_receita_pendente_dia - $query[$i]["vl_baixa_parcial"];
                            $vl_lancamento_pendente = $query[$i]["vl_lancamento"] - $query[$i]["vl_baixa_parcial"];
                        }
                    }    
                    
                }else{
                    $vl_receita_pendente_dia = 0.00;
                    $vl_receita_dia = 0.00;
                    $vl_lancamento_pendente = 0.00;
                    $vl_total_dia = $query[$i]["vl_lancamento"];
                    if($query[$i]["tipo_lancamento_pk"]==1){
                        $vl_receita_dia = $query[$i]["vl_lancamento"];
                    }       
                    if($query[$i]["tipo_lancamento_pk"]==1 and $query[$i]["dt_pagamento"]==""){
                        $vl_receita_pendente_dia = $query[$i]["vl_lancamento"];
                        $vl_lancamento_pendente = $query[$i]["vl_lancamento"];
                        if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                            $vl_receita_pendente_dia = $vl_receita_pendente_dia - $query[$i]["vl_baixa_parcial"];
                            $vl_lancamento_pendente = $query[$i]["vl_lancamento"] - $query[$i]["vl_baixa_parcial"];
                        }
                    }    
                    
                }
                
                $receita[] = array(
                    "pk" => $query[$i]["pk"],
                    "dt_cadastro" => $query[$i]["dt_cadastro"],
                    "nfse_pk" => $query[$i]["nfse_pk"],
                    "ds_empresa" => $query[$i]["ds_empresa"],
                    "ds_conta_bancaria" => $query[$i]["ds_conta_bancaria"],
                    "ds_status_pagamento" => $query[$i]["ds_status_pagamento"],                    
                    "dt_vencimento" => $query[$i]["dt_vencimento"],
                    "dt_faturamento" => $query[$i]["dt_faturamento"],
                    "dt_pagamento" => $query[$i]["dt_pagamento"],
                    "ds_operacao" => $query[$i]["ds_operacao"],
                    "ds_lancamento" => $query[$i]["ds_lancamento"],
                    "ds_tipo_grupo" => $query[$i]["ds_tipo_grupo"],                    
                    "ds_recebido_pago_origem" => $ds_recebido_de,
                    "ds_cpf_cnpj" => $ds_cpf_cnpj,
                    "ds_metodo_pagamento" => $query[$i]["ds_metodo_pagamento"],                    
                    "vl_lancamento" =>  number_format($vl_lancamento, 2, ',', '.'),                            
                    "vl_lancamento_pendente" =>  number_format($vl_lancamento_pendente, 2, ',', '.'),                            
                    "ds_usuario" => $query[$i]["ds_usuario"],
                    "operacao_pk" => $query[$i]["tipo_lancamento_pk"],
                    "ds_tipo_operacao" => $query[$i]["ds_tipo_operacao"],
                    "vl_receita_pendente_dia" =>  number_format($vl_receita_pendente_dia, 2, ',', '.'),
                    "vl_total_dia" =>  number_format($vl_total_dia, 2, ',', '.'),
                    "vl_receita_dia" =>  number_format($vl_receita_dia, 2, ',', '.'),
                    "proxima_data" => $proxima_data
                );
            } 
            
            $result[] = array(             
                "vl_total_receita" =>  number_format($vl_receita, 2, ',', '.'),
                "vl_total_receita_pendente" =>  number_format($vl_receita_pendente, 2, ',', '.'),
                "DadosReceita"=>$receita
            );
        }

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result;
        return $retorno;
    }
    public function listarDespesa($pk,$ic_status_lancamento,$ds_lancamento,$usuario_cadastro_pk,$empresas_pk,$tipo_grupo_lancamento_pk,$grupo_lancamento_pk,$dt_cadastro_ini,$dt_cadastro_fim,$dt_faturamento_ini,$dt_faturamento_fim,$dt_vencimento_ini,$dt_vencimento_fim,$dt_pagamento_ini,$dt_pagamento_fim,  $ds_num_documento,$ic_tipo_num_documento, $categorias_financeiras_pk, $tipo_lancamento_pk, $tipos_operacao_pk, $empresa_despesa_pk, $contas_lancamento_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $result = [];
        $sql ="";
        $sql.="SELECT l.pk,";
        $sql.="      date_format(l.dt_cadastro, '%d/%m/%Y %H:%i')dt_cadastro,";
        $sql.="      c.ds_conta ds_empresa,";
        $sql.="      cb.ds_conta_bancaria,";
        $sql.="      SUBSTRING(u.ds_usuario,1, 15) ds_usuario,";
        $sql.="      date_format(l.dt_vencimento, '%d/%m/%Y')dt_vencimento,";
        $sql.="      date_format(l.dt_faturamento, '%d/%m/%Y') dt_faturamento,";
        $sql.="      date_format(l.dt_pagamento, '%d/%m/%Y') dt_pagamento,";
        $sql.="      CASE l.ic_status_lancamento";
        $sql.="        WHEN 1 THEN 'PAGO'";
        $sql.="        WHEN 2 THEN 'PENDENTE'";
        $sql.="        WHEN 3 THEN 'APROVADO'";
        $sql.="        WHEN 4 THEN 'ATRASADO'";
        $sql.="        WHEN 5 THEN 'CANCELADO'";
        $sql.="        WHEN 6 THEN 'BAIXA PARCIAL'";
        $sql.="      END ds_status_pagamento,";
        $sql.="      l.ic_status_lancamento,";
        $sql.="      CASE l.tipo_lancamento_pk";
        $sql.="          WHEN 1 THEN 'Receita'";
        $sql.="          WHEN 2 THEN 'Despesa Fixa'";
        $sql.="          WHEN 3 THEN 'Despesa Variada'";
        $sql.="          WHEN 4 THEN 'Imposto'";
        $sql.="          WHEN 5 THEN 'Transferência'";
        $sql.="          WHEN 6 THEN 'Caixinha'";
        $sql.="      END ds_operacao,";
        $sql.="      top.ds_tipo_operacao,";
        $sql.="      CASE l.tipo_grupo_lancamento_pk";
        $sql.="          WHEN 1 THEN '(Clientes)'";
        $sql.="          WHEN 2 THEN 'Colaboradores'";
        $sql.="          WHEN 3 THEN 'Fornecedores'";
        $sql.="          WHEN 4 THEN 'Outros'";
        $sql.="       END  ds_tipo_grupo,";
        $sql.="       mp.ds_metodo_pagamento,";
        $sql.="       l.tipo_lancamento_pk,";
        $sql.="       l.ic_status_pagamento,";
        $sql.="       l.tipo_grupo_lancamento_pk,";
        $sql.="       l.vl_lancamento,";
        $sql.="       l.ds_lancamento,";
        $sql.="       l.grupo_lancamento_pk,";
        $sql.="       l.nfse_pk,";
        $sql.="       SUM(lfbp.vl_baixa_parcial) vl_baixa_parcial";
        $sql.=" FROM lancamentos_financeiros l";
        $sql.="     LEFT JOIN contas c on l.empresa_lancamento_pk = c.pk";
        $sql.="     LEFT JOIN contas_bancarias cb on l.contas_bancarias_pk = cb.pk";
        $sql.="     LEFT JOIN metodos_pagamento mp ON l.metodos_pagamento_pk = mp.pk";
        $sql.="     LEFT JOIN tipos_operacao top ON l.tipos_operacao_pk = top.pk";
        $sql.="     LEFT JOIN usuarios u ON l.usuario_cadastro_pk = u.pk";
        $sql.="     LEFT JOIN analise_financeira af ON af.lancamentos_pk = l.pk";
        $sql.="     LEFT JOIN lancamentos_financeiros_baixa_parcial lfbp ON lfbp.lancamentos_financeiros_pk = l.pk";
        $sql.=" WHERE l.tipo_lancamento_pk <> 1";
        $ic_analise_financeira = (new Conta($this->pdo))->configModulo();
        $ic_analise_financeira = $ic_analise_financeira->data[0]['ic_analise_financeira'];
        
        if($ic_analise_financeira == 1){
            $sql.=" AND (af.ic_status= 3 or";
            $sql.="      af.ic_status is null)";
            
        }
        
        if(!empty($dt_vencimento_ini) and !empty($dt_vencimento_fim)){

            $sql.=" AND l.dt_vencimento >= '".Util::DataYMD($dt_vencimento_ini)." 00:00:00'";
            $sql.=" AND l.dt_vencimento <= '".Util::DataYMD($dt_vencimento_fim)." 23:59:00'";
            
        }
        if (!empty($dt_cadastro_ini) and !empty($dt_cadastro_fim)) {
            $sql.=" AND l.dt_cadastro >= '".Util::DataYMD($dt_cadastro_ini)." 00:00:00'";
            $sql.=" AND l.dt_cadastro <= '".Util::DataYMD($dt_cadastro_fim)." 23:59:00'"; 

        }
        if (!empty($dt_faturamento_ini) and !empty($dt_faturamento_fim)) {
            $sql.=" AND l.dt_faturamento >= '".Util::DataYMD($dt_faturamento_ini)." 00:00:00'";
            $sql.=" AND l.dt_faturamento <= '".Util::DataYMD($dt_faturamento_fim)." 23:59:00'"; 

        }
        if (!empty($dt_pagamento_ini) and !empty($dt_pagamento_fim)) {
            $sql.=" AND l.dt_pagamento >= '".Util::DataYMD($dt_pagamento_ini)." 00:00:00'";
            $sql.=" AND l.dt_pagamento <= '".Util::DataYMD($dt_pagamento_fim)." 23:59:00'"; 

        }if(empty($pk)){
            if(!empty($dt_vencimento_ini) and !empty($dt_vencimento_fim)){       
                $sql.=" AND l.dt_vencimento >= '".Util::DataYMD($dt_vencimento_ini)." 00:00:00'";
                $sql.=" AND l.dt_vencimento <= '".Util::DataYMD($dt_vencimento_fim)." 23:59:00'";  
            }                                  
        }
        
        if(!empty($ic_status_lancamento)){
            $sql.=" AND l.ic_status_lancamento=".$ic_status_lancamento;
        }
        if(!empty($pk)){
            $sql.=" AND l.pk=".$pk;
        }
        if(!empty($usuario_cadastro_pk)){
            $sql.=" AND l.usuario_cadastro_pk=".$usuario_cadastro_pk;
        }
        if(!empty($empresas_pk)){
            $sql.=" AND l.empresa_lancamento_pk=".$empresas_pk;
        }
        if(!empty($tipo_grupo_lancamento_pk)){
            $sql.=" AND l.tipo_grupo_lancamento_pk=".$tipo_grupo_lancamento_pk;
        }
        if(!empty($grupo_lancamento_pk)){
            $sql.=" AND l.grupo_lancamento_pk=".$grupo_lancamento_pk;
        }
        if(!empty($ds_num_documento)){
            $sql.=" AND l.ds_num_documento like '%".$ds_num_documento."%'";
        }
        if(!empty($ds_lancamento)){
            $sql.=" AND l.ds_lancamento like '%".$ds_lancamento."%'";
        }
        if(!empty($ic_tipo_num_documento)){
            $sql.=" AND l.ic_tipo_num_documento = ".$ic_tipo_num_documento;
        }
        if(!empty($categorias_financeiras_pk)){
            $sql.=" AND l.categorias_financeiras_pk = ".$categorias_financeiras_pk;
        }
        if(!empty($tipo_lancamento_pk)){
            $sql.=" AND l.tipo_lancamento_pk = ".$tipo_lancamento_pk;
        }
        if(!empty($tipos_operacao_pk)){
            $sql.=" AND l.tipos_operacao_pk = ".$tipos_operacao_pk;
        }
        if(!empty($tipo_grupo_lancamento_pk)){
            $sql.=" AND l.tipo_grupo_lancamento_pk = ".$tipo_grupo_lancamento_pk;
        }
        if(!empty($empresa_despesa_pk)){
            $sql.=" AND l.empresa_lancamento_pk = ".$empresa_despesa_pk;
        }
        if(!empty($contas_lancamento_pk)){
            $sql.=" AND l.contas_bancarias_pk = ".$contas_lancamento_pk;
        }

        $sql.=" GROUP BY l.pk";
        $sql.=" ORDER BY l.dt_pagamento asc";

        

     
       
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if(count($query) > 0){
            $vl_despesa = 0;
            $vl_despesa_pendente = 0;
            $vl_total_dia = 0;
            $vl_despesa_pendente_dia = 0;
            $vl_despesa_dia = 0;
            $vl_lancamento_pendente = 0.00;
            for($i = 0; $i < count($query); $i++){
                if($query[$i]['tipo_grupo_lancamento_pk']==1){
                    $queryLead = $this->listaLeads($query[$i]['grupo_lancamento_pk']);
                    $ds_recebido_de = $queryLead[0]['ds_lead'];
                    $ds_cpf_cnpj = $queryLead[0]['ds_cpf_cnpj'];
                }else if($query[$i]['tipo_grupo_lancamento_pk']==2){
                    $queryLead = $this->listarColaboradores($query[$i]['grupo_lancamento_pk']);
                    $ds_recebido_de = $queryLead[0]['ds_colaborador'];
                    $ds_cpf_cnpj = $queryLead[0]['ds_cpf'];
                }else if($query[$i]['tipo_grupo_lancamento_pk']==3){
                    $queryLead = $this->listarFornecedores($query[$i]['grupo_lancamento_pk']);
                    $ds_recebido_de = $queryLead[0]['ds_fornecedor'];
                    $ds_cpf_cnpj = $queryLead[0]['ds_cpf_cnpj'];
                }

                if($query[$i]["tipo_lancamento_pk"]!=1){
                    $vl_despesa += $query[$i]["vl_lancamento"];
                    /*if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                        $vl_despesa = $query[$i]["vl_lancamento"] - $query[$i]["vl_baixa_parcial"];
                    }*/
                }
                
                if($query[$i]["tipo_lancamento_pk"]!=1 && $query[$i]["ic_status_pagamento"]!=1 && $query[$i]["dt_pagamento"]==""){                  
                    $vl_despesa_pendente += $query[$i]["vl_lancamento"];
                    if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                        $vl_despesa_pendente = $vl_despesa_pendente - $query[$i]["vl_baixa_parcial"];
                    }
                }    
                

                $data = $query[$i]["dt_vencimento"];
                $l = $i ;
                if((count($query)-1) == $i){
                    $a = $i;
                }
                else{
                    $a = $i + 1;
                }
                
                
                $data_anterior = $query[$l]["dt_vencimento"];
                if($data_anterior == null){
                    $data_anterior = "";
                }
                $proxima_data = $query[$a]["dt_vencimento"];
                if($proxima_data == null){
                    $proxima_data = "";
                }

                if($data_anterior == $data){

                    $vl_total_dia += $query[$i]["vl_lancamento"];
                    if($query[$i]["tipo_lancamento_pk"]!=1){
                        $vl_despesa_dia += $query[$i]["vl_lancamento"];
                    } 
                    if($query[$i]["tipo_lancamento_pk"]!=1 && $query[$i]["ic_status_lancamento"]!=1 && $query[$i]["dt_pagamento"]==""){                  
                        $vl_despesa_pendente_dia += $query[$i]["vl_lancamento"];
                        if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                            $vl_despesa_pendente_dia = $vl_despesa_pendente_dia - $query[$i]["vl_baixa_parcial"];
                            $vl_lancamento_pendente = $query[$i]["vl_lancamento"] - $query[$i]["vl_baixa_parcial"];
                        }
                    }else{
                        $vl_lancamento_pendente = 0.00;
                    }  
                }else{
                    $vl_total_dia = $query[$i]["vl_lancamento"];
                    $vl_despesa_pendente_dia = "";
                    $vl_despesa_dia = "";
                    $vl_lancamento_pendente = 0.00;
                    if($query[$i]["tipo_lancamento_pk"]!=1){
                        $vl_despesa_dia += $query[$i]["vl_lancamento"];
                    }
                    if($query[$i]["dt_vencimento"] <=  date('d/m/Y')){         
                        if($query[$i]["tipo_lancamento_pk"]!=1 && $query[$i]["ic_status_lancamento"]!=1 && $query[$i]["dt_pagamento"]==""){                  
                            $vl_despesa_pendente_dia = $query[$i]["vl_lancamento"];
                            if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                                $vl_despesa_pendente_dia = $query[$i]["vl_lancamento"] - $query[$i]["vl_baixa_parcial"];
                                $vl_lancamento_pendente = $query[$i]["vl_lancamento"] - $query[$i]["vl_baixa_parcial"];
                            }
                        }    
                    }   
                }
                
                $despesa[] = array(
                    "pk" => $query[$i]["pk"],
                    "dt_cadastro" => $query[$i]["dt_cadastro"],
                    "nfse_pk" => $query[$i]["nfse_pk"],
                    "ds_empresa" => $query[$i]["ds_empresa"],
                    "ds_conta_bancaria" => $query[$i]["ds_conta_bancaria"],
                    "ds_status_pagamento" => $query[$i]["ds_status_pagamento"],                    
                    "dt_vencimento" => $query[$i]["dt_vencimento"],
                    "dt_faturamento" => $query[$i]["dt_faturamento"],
                    "dt_pagamento" => $query[$i]["dt_pagamento"],
                    "ds_operacao" => $query[$i]["ds_operacao"],
                    "ds_lancamento" => $query[$i]["ds_lancamento"],
                    "ds_tipo_grupo" => $query[$i]["ds_tipo_grupo"],                    
                    "proxima_data" => $proxima_data,
                    "ds_recebido_pago_origem" => $ds_recebido_de,
                    "ds_cpf_cnpj" => $ds_cpf_cnpj,
                    "ds_metodo_pagamento" => $query[$i]["ds_metodo_pagamento"],                    
                    "vl_lancamento" =>  number_format($query[$i]["vl_lancamento"], 2, ',', '.'),                             
                    "vl_lancamento_pendente" =>  number_format($vl_lancamento_pendente, 2, ',', '.'),                             
                    "ds_usuario" => $query[$i]["ds_usuario"],
                    "operacao_pk" => $query[$i]["tipo_lancamento_pk"],
                    "ds_tipo_operacao" => $query[$i]["ds_tipo_operacao"],
                    "ic_status_pagamento" => $query[$i]["ic_status_pagamento"], 
                    "vl_despesa_pendente_dia" =>  number_format($vl_despesa_pendente_dia, 2, ',', '.'),
                    "vl_despesa_dia" =>  number_format($vl_despesa_dia, 2, ',', '.'),
                );
            } 
            
            $result[] = array(             
                "vl_total_despesa" =>  number_format($vl_despesa, 2, ',', '.'),
                "vl_total_despesa_pendente" =>  number_format($vl_despesa_pendente, 2, ',', '.'),
                "DadosDespesa"=>$despesa
            );
        }

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result;
        return $retorno;
    }

    public function listarLancamento($pk,$ic_status_lancamento, $ds_lancamento, $usuario_cadastro_pk,$empresas_pk,$tipo_grupo_lancamento_pk,$grupo_lancamento_pk,$dt_cadastro_ini,$dt_cadastro_fim,$dt_faturamento_ini,$dt_faturamento_fim,$dt_vencimento_ini,$dt_vencimento_fim,$dt_pagamento_ini,$dt_pagamento_fim,  $ds_num_documento,$ic_tipo_num_documento, $categorias_financeiras_pk, $tipo_lancamento_pk, $tipos_operacao_pk, $contas_bancarias_pk){        
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $result = [];
        
        $sql="";
        $sql.="SELECT l.pk,";
        $sql.="      date_format(l.dt_cadastro, '%d/%m/%Y %H:%i')dt_cadastro,";
        $sql.="      c.ds_conta ds_empresa,";
        $sql.="      cb.ds_conta_bancaria,";
        $sql.="      SUBSTRING(u.ds_usuario,1, 15) ds_usuario,";
        $sql.="      date_format(l.dt_vencimento, '%d/%m/%Y')dt_vencimento,";
        $sql.="      date_format(l.dt_faturamento, '%d/%m/%Y') dt_faturamento,";
        $sql.="      date_format(l.dt_pagamento, '%d/%m/%Y') dt_pagamento,";
        $sql.="      CASE l.ic_status_lancamento";
        $sql.="        WHEN 1 THEN 'PAGO'";
        $sql.="        WHEN 2 THEN 'PENDENTE'";
        $sql.="        WHEN 3 THEN 'APROVADO'";
        $sql.="        WHEN 4 THEN 'ATRASADO'";
        $sql.="        WHEN 5 THEN 'CANCELADO'";
        $sql.="        WHEN 6 THEN 'BAIXA PARCIAL'";
        $sql.="      END ds_status_pagamento,";
        $sql.="      l.ic_status_lancamento,";
        $sql.="      CASE l.tipo_lancamento_pk";
        $sql.="          WHEN 1 THEN 'Receita'";
        $sql.="          WHEN 2 THEN 'Despesa Fixa'";
        $sql.="          WHEN 3 THEN 'Despesa Variada'";
        $sql.="          WHEN 4 THEN 'Imposto'";
        $sql.="          WHEN 5 THEN 'Transferência'";
        $sql.="          WHEN 6 THEN 'Caixinha'";
        $sql.="      END ds_operacao,";
        $sql.="      top.ds_tipo_operacao,";
        $sql.="      CASE l.tipo_grupo_lancamento_pk";
        $sql.="          WHEN 1 THEN '(Clientes)'";
        $sql.="          WHEN 2 THEN 'Colaboradores'";
        $sql.="          WHEN 3 THEN 'Fornecedores'";
        $sql.="          WHEN 4 THEN 'Outros'";
        $sql.="       END  ds_tipo_grupo,";
        $sql.="       mp.ds_metodo_pagamento,";
        $sql.="       l.tipo_lancamento_pk,";
        $sql.="       l.tipo_grupo_lancamento_pk,";
        $sql.="       l.vl_lancamento,";
        $sql.="       l.ds_lancamento,";
        $sql.="       l.ic_status_pagamento,";
        $sql.="       l.grupo_lancamento_pk,";
        $sql.="       l.nfse_pk,";
        $sql.="       SUM(lfbp.vl_baixa_parcial) vl_baixa_parcial";
        $sql.=" FROM lancamentos_financeiros l";
        $sql.="     LEFT JOIN contas c on l.empresa_lancamento_pk = c.pk";
        $sql.="     LEFT JOIN contas_bancarias cb on l.contas_bancarias_pk = cb.pk";
        $sql.="    INNER JOIN metodos_pagamento mp ON l.metodos_pagamento_pk = mp.pk";
        $sql.="     LEFT JOIN tipos_operacao top ON l.tipos_operacao_pk = top.pk";
        $sql.="    INNER JOIN usuarios u ON l.usuario_cadastro_pk = u.pk";
        $sql.="     LEFT JOIN analise_financeira af ON l.pk = af.lancamentos_pk";        
        $sql.="     LEFT JOIN lancamentos_financeiros_baixa_parcial lfbp ON lfbp.lancamentos_financeiros_pk = l.pk";
        $sql.=" WHERE 1=1";
        $ic_analise_financeira = (new Conta($this->pdo))->configModulo();
        $ic_analise_financeira = $ic_analise_financeira->data[0]['ic_analise_financeira'];
        if($ic_analise_financeira == 1){
            $sql.=" and (af.ic_status is null || af.ic_status = 3)";
        }
        if(!empty($dt_vencimento_ini) and !empty($dt_vencimento_fim)){
            $sql.=" AND l.dt_vencimento >= '".Util::DataYMD($dt_vencimento_ini)." 00:00:00'";
            $sql.=" AND l.dt_vencimento <= '".Util::DataYMD($dt_vencimento_fim)." 23:59:00'";

        }elseif (!empty($dt_cadastro_ini) and !empty($dt_cadastro_fim)) {
            $sql.=" AND l.dt_cadastro >= '".Util::DataYMD($dt_cadastro_ini)." 00:00:00'";
            $sql.=" AND l.dt_cadastro <= '".Util::DataYMD($dt_cadastro_fim)." 23:59:00'"; 

        }elseif (!empty($dt_faturamento_ini) and !empty($dt_faturamento_fim)) {
            $sql.=" AND l.dt_faturamento >= '".Util::DataYMD($dt_faturamento_ini)." 00:00:00'";
            $sql.=" AND l.dt_faturamento <= '".Util::DataYMD($dt_faturamento_fim)." 23:59:00'"; 

        }elseif (!empty($dt_pagamento_ini) and !empty($dt_pagamento_fim)) {
            $sql.=" AND l.dt_pagamento >= '".Util::DataYMD($dt_pagamento_ini)." 00:00:00'";
            $sql.=" AND l.dt_pagamento <= '".Util::DataYMD($dt_pagamento_fim)." 23:59:00'"; 

        }elseif(empty($pk)){
            if(!empty($dt_vencimento_ini) and !empty($dt_vencimento_fim)){       
                $sql.=" AND l.dt_vencimento >= '".Util::DataYMD($dt_vencimento_ini)." 00:00:00'";
                $sql.=" AND l.dt_vencimento <= '".Util::DataYMD($dt_vencimento_fim)." 23:59:00'";  
            }                                  
        }
        if(!empty($ic_status_lancamento)){
            $sql.=" AND l.ic_status_lancamento=".$ic_status_lancamento;
        }
        if(!empty($tipo_grupo_lancamento_pk)){
            $sql.=" AND l.tipo_grupo_lancamento_pk=".$tipo_grupo_lancamento_pk;
        }
        if(!empty($grupo_lancamento_pk)){
            $sql.=" AND l.grupo_lancamento_pk=".$grupo_lancamento_pk;
        }
        if(!empty($pk)){
            $sql.=" AND l.pk=".$pk;
        }
        if(!empty($usuario_cadastro_pk)){
            $sql.=" AND l.usuario_cadastro_pk=".$usuario_cadastro_pk;
        }
        if(!empty($empresas_pk)){
            $sql.=" AND l.empresa_lancamento_pk=".$empresas_pk;
        }
        if(!empty($contas_bancarias_pk)){
            $sql.=" AND l.contas_bancarias_pk=".$contas_bancarias_pk;
        }
        if(!empty($tipo_grupo_pk)){
            $sql.=" AND l.tipo_grupo_pk=".$tipo_grupo_pk;
        }
        if(!empty($grupo_leancamento_pk)){
            $sql.=" AND l.grupo_lancamento_pk=".$grupo_leancamento_pk;
        }
        if(!empty($ds_num_documento)){
            $sql.=" AND l.ds_num_documento like '%".$ds_num_documento."%'";
        }
        if(!empty($ds_lancamento)){
            $sql.=" AND l.ds_lancamento like '%".$ds_lancamento."%'";
        }
        if(!empty($ic_tipo_num_documento)){
            $sql.=" AND l.ic_tipo_num_documento = ".$ic_tipo_num_documento;
        }
        if(!empty($categorias_financeiras_pk)){
            $sql.=" AND l.categorias_financeiras_pk = ".$categorias_financeiras_pk;
        }
        if(!empty($tipo_lancamento_pk)){
            $sql.=" AND l.tipo_lancamento_pk = ".$tipo_lancamento_pk;
        }
        if(!empty($tipo_grupo_lancamento_pk)){
            $sql.=" AND l.tipo_grupo_lancamento_pk = ".$tipo_grupo_lancamento_pk;
        }
        $sql.=" GROUP BY l.pk";
        $sql.=" ORDER BY l.dt_vencimento desc";
        

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if(count($query) > 0){
            $vl_despesa = 0;
            $vl_receita = 0;
            $vl_despesa_pendente = 0;
            $vl_receita_pendente = 0;
            $vl_pendente_dia = 0;
            $vl_pendente = 0;
            $vl_total_dia = 0;
            for($i = 0; $i < count($query); $i++){
                if($query[$i]['tipo_grupo_lancamento_pk']==1){
                    $queryLead = $this->listaLeads($query[$i]['grupo_lancamento_pk']);
                    $ds_recebido_de = $queryLead[0]['ds_lead'];
                    $ds_cpf_cnpj = $queryLead[0]['ds_cpf_cnpj'];
                }else if($query[$i]['tipo_grupo_lancamento_pk']==2){
                    $queryLead = $this->listarColaboradores($query[$i]['grupo_lancamento_pk']);
                    $ds_recebido_de = $queryLead[0]['ds_colaborador'];
                    $ds_cpf_cnpj = $queryLead[0]['ds_cpf'];
                }else if($query[$i]['tipo_grupo_lancamento_pk']==3){
                    $queryLead = $this->listarFornecedores($query[$i]['grupo_lancamento_pk']);
                    $ds_recebido_de = $queryLead[0]['ds_fornecedor'];
                    $ds_cpf_cnpj = $queryLead[0]['ds_cpf_cnpj'];
                }
                
                
                if($query[$i]["tipo_lancamento_pk"]!=1){
                    $vl_despesa += $query[$i]["vl_lancamento"];
                }
                
                if($query[$i]["tipo_lancamento_pk"]==1){
                    $vl_receita += $query[$i]["vl_lancamento"];
                }
                
                if($query[$i]["tipo_lancamento_pk"]!=1 and $query[$i]["ic_status_pagamento"]!=1 and $query[$i]["dt_pagamento"]==""){                  
                    $vl_despesa_pendente += $query[$i]["vl_lancamento"];
                    if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                        $vl_despesa_pendente = $vl_despesa_pendente - $query[$i]["vl_baixa_parcial"];
                    }
                }    
                if($query[$i]["tipo_lancamento_pk"]==1 and $query[$i]["ic_status_pagamento"]!=1 and $query[$i]["dt_pagamento"]==""){                  
                    $vl_receita_pendente += $query[$i]["vl_lancamento"];
                    if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                        $vl_receita_pendente = $vl_receita_pendente - $query[$i]["vl_baixa_parcial"];
                    }
                }    
                

                $data = $query[$i]["dt_vencimento"];
                $l = $i ;
                if((count($query)-1) == $i){
                    $a = $i;
                }
                else{
                    $a = $i + 1;
                }
                
                $data_anterior = $query[$l]["dt_vencimento"];
                if($data_anterior == null){
                    $data_anterior = "";
                }
                $proxima_data = $query[$a]["dt_vencimento"];
                if($proxima_data == null){
                    $proxima_data = "";
                }


                if($data_anterior == $data){
                    $vl_total_dia += $query[$i]["vl_lancamento"];
                    $vl_pendente = 0;
                    
                    if( $query[$i]["ic_status_lancamento"]!=1 and $query[$i]["dt_pagamento"]==""){                  
                        $vl_pendente_dia += $query[$i]["vl_lancamento"];
                        $vl_pendente = $query[$i]["vl_lancamento"];
                        if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                            $vl_pendente_dia = $vl_pendente_dia - $query[$i]["vl_baixa_parcial"];
                            $vl_pendente = $vl_pendente - $query[$i]["vl_baixa_parcial"];
                        }
                    }     
                }else{
                    $vl_total_dia = $query[$i]["vl_lancamento"];
                    $vl_pendente_dia = 0;
                    $vl_pendente = 0;
                    
                    if( $query[$i]["ic_status_lancamento"]!=1 and $query[$i]["dt_pagamento"]==""){                  
                        $vl_pendente_dia = $query[$i]["vl_lancamento"];
                        $vl_pendente = $query[$i]["vl_lancamento"];
                        if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                            $vl_pendente_dia = $query[$i]["vl_lancamento"] - $query[$i]["vl_baixa_parcial"];
                            $vl_pendente = $vl_pendente - $query[$i]["vl_baixa_parcial"];
                        }
                    }     
                }


                
                $lancamento[] = array(
                    "pk" => $query[$i]["pk"],
                    "dt_cadastro" => $query[$i]["dt_cadastro"],
                    "ds_empresa" => $query[$i]["ds_empresa"],
                    "ds_conta_bancaria" => $query[$i]["ds_conta_bancaria"],
                    "ds_status_pagamento" => $query[$i]["ds_status_pagamento"],                    
                    "ds_lancamento" => $query[$i]["ds_lancamento"],                    
                    "dt_vencimento" => $query[$i]["dt_vencimento"],
                    "dt_faturamento" => $query[$i]["dt_faturamento"],
                    "dt_pagamento" => $query[$i]["dt_pagamento"],
                    "ds_operacao" => $query[$i]["ds_operacao"],
                    "ds_tipo_grupo" => $query[$i]["ds_tipo_grupo"],
                    "ds_recebido_pago_origem" => $ds_recebido_de,
                    "ds_cpf_cnpj" => $ds_cpf_cnpj,
                    "ds_metodo_pagamento" => $query[$i]["ds_metodo_pagamento"],                    
                    "vl_lancamento" =>  number_format($query[$i]["vl_lancamento"], 2, ',', '.'),
                    "vl_pendente" =>  number_format($vl_pendente, 2, ',', '.'),
                    "vl_lancamento_dia" =>  number_format($vl_total_dia, 2, ',', '.'),
                    "vl_lancamento_pendente_dia" =>  number_format($vl_pendente_dia, 2, ',', '.'),
                    "ds_usuario" => $query[$i]["ds_usuario"],
                    "operacao_pk" => $query[$i]["tipo_lancamento_pk"],
                    "ds_tipo_operacao" => $query[$i]["ds_tipo_operacao"],
                    "proxima_data" => $proxima_data
                );

            } 
            
            $result[] = array(             
                "vl_total_lancamento" =>  number_format($vl_receita - $vl_despesa, 2, ',', '.'),
                "vl_total_lancamento_pendente" =>  number_format($vl_receita_pendente - $vl_despesa_pendente, 2, ',', '.'),
                "DadosLancamento"=>$lancamento
            );
        }

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result;
        return $retorno;
    }
    public function listarVencidos($ic_receita_despesa){        
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $result = [];
        
        $sql = "";
        $sql .= "SELECT SUM(l.vl_lancamento) as vl_lancamento ";
        $sql .= "FROM lancamentos_financeiros l ";
        $sql .= "LEFT JOIN analise_financeira af ON l.pk = af.lancamentos_pk ";
        $sql .= "WHERE 1=1 ";

        $ic_analise_financeira = (new Conta($this->pdo))->configModulo();
        $ic_analise_financeira = $ic_analise_financeira->data[0]['ic_analise_financeira'];

        if ($ic_analise_financeira == 1) {
            $sql .= " AND (af.ic_status IS NULL OR af.ic_status = 3) ";
        }

        if ($ic_receita_despesa > 1) {
            $sql .= " AND l.tipo_lancamento_pk > 1 ";
        } else {
            $sql .= " AND l.tipo_lancamento_pk = 1 ";
        }

        $sql .= " AND DATE(l.dt_vencimento) <= CURDATE() ";
        $sql .= " AND l.dt_pagamento IS NULL ";
       

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetch(\PDO::FETCH_ASSOC);

        $retorno->message = 'Dados carregados com sucesso';

        if ($query && $query['vl_lancamento'] !== null) {
            $retorno->data = $query['vl_lancamento'];
            return $query['vl_lancamento'];
        } else {
            return 0;
        }

    }
    public function listarVencidosHoje($ic_receita_despesa,$dt_vencimento_ini,$dt_vencimento_fim){        
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $result = [];
        
        $sql="";
        $sql.="SELECT ";
        $sql.="       sum(l.vl_lancamento)vl_lancamento";
        $sql.=" FROM lancamentos_financeiros l";
        $sql.="     LEFT JOIN analise_financeira af ON l.pk = af.lancamentos_pk";        
        $sql.=" WHERE 1=1";
        $ic_analise_financeira = (new Conta($this->pdo))->configModulo();
        $ic_analise_financeira = $ic_analise_financeira->data[0]['ic_analise_financeira'];
        if($ic_analise_financeira == 1){
            $sql.=" and (af.ic_status is null || af.ic_status = 3)";
        }

        if($ic_receita_despesa>1){
            $sql.=" and l.tipo_lancamento_pk >1";
        }
        else{
            $sql.=" and l.tipo_lancamento_pk  = 1";
        }
              
        
        if(!empty($dt_vencimento_ini) and !empty($dt_vencimento_fim)){       
            $sql.=" AND l.dt_vencimento between '".Util::DataYMD($dt_vencimento_ini)." 00:00:00' and '".Util::DataYMD($dt_vencimento_fim)." 23:59:00'";
        }                                  
        
        $sql.=" AND l.dt_pagamento is null";                           
        

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        if(count($query)>0){
            $retorno->data = $query[0]['vl_lancamento'];
            return $query[0]['vl_lancamento'];
        }
        else{
            return 0;
        }
    }
    public function listarAVencer($ic_receita_despesa,$dt_vencimento_ini,$dt_vencimento_fim){        
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $result = [];
        $firstDayOfMonth = date('Y-m-01');
        $lastDayOfMonth = date('Y-m-t');
        $sql="";
        $sql.="SELECT ";
        $sql.="       sum(l.vl_lancamento)vl_lancamento";
        $sql.=" FROM lancamentos_financeiros l";
        $sql.="     LEFT JOIN analise_financeira af ON l.pk = af.lancamentos_pk";        
        $sql.=" WHERE 1=1";
        $ic_analise_financeira = (new Conta($this->pdo))->configModulo();
        $ic_analise_financeira = $ic_analise_financeira->data[0]['ic_analise_financeira'];
        if($ic_analise_financeira == 1){
            $sql.=" and (af.ic_status is null || af.ic_status = 3)";
        }

        if($ic_receita_despesa>1){
            $sql.=" and l.tipo_lancamento_pk >1";
        }
        else{
            $sql.=" and l.tipo_lancamento_pk  = 1";
        }
              
        
        if(!empty($dt_vencimento_ini) and !empty($dt_vencimento_fim)){       
            $sql.=" AND l.dt_vencimento > '".Util::DataYMD($dt_vencimento_ini)." 00:00:00'";
            $sql.=" AND l.dt_vencimento <= '".$lastDayOfMonth." 23:59:00'";  
        }                                  
        
        $sql.=" AND l.dt_pagamento is null";                           
   

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        if(count($query)>0){
            $retorno->data = $query[0]['vl_lancamento'];
            return $query[0]['vl_lancamento'];
        }
        else{
            return 0;
        }
    }
    public function listarRecebidos($ic_receita_despesa,$dt_vencimento_ini,$dt_vencimento_fim){        
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $result = [];
        $firstDayOfMonth = date('Y-m-01');
        $lastDayOfMonth = date('Y-m-t');
        $sql="";
        $sql.="SELECT ";
        $sql.="       sum(l.vl_lancamento)vl_lancamento";
        $sql.=" FROM lancamentos_financeiros l";
        $sql.="     LEFT JOIN analise_financeira af ON l.pk = af.lancamentos_pk";        
        $sql.=" WHERE 1=1";
        $ic_analise_financeira = (new Conta($this->pdo))->configModulo();
        $ic_analise_financeira = $ic_analise_financeira->data[0]['ic_analise_financeira'];
        if($ic_analise_financeira == 1){
            $sql.=" and (af.ic_status is null || af.ic_status = 3)";
        }

        if($ic_receita_despesa>1){
            $sql.=" and l.tipo_lancamento_pk > 1";
        }
        else{
            $sql.=" and l.tipo_lancamento_pk  = 1";
        }
              
        
        if(!empty($dt_vencimento_ini) and !empty($dt_vencimento_fim)){       
            $sql.=" AND l.dt_vencimento >= '".$firstDayOfMonth." 00:00:00'";
            $sql.=" AND l.dt_vencimento <= '".$lastDayOfMonth." 23:59:00'";  
        }                                  
        
        $sql.=" AND l.dt_pagamento is not null";                           
        
        
        
   

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        if(count($query)>0){
            $retorno->data = $query[0]['vl_lancamento'];
            return $query[0]['vl_lancamento'];
        }
        else{
            return 0;
        }
        
    }

    public function listarExtratoMes($empresas_pk, $contas_bancarias_pk, $dt_cadastro_ini,$dt_cadastro_fim,$dt_faturamento_ini,$dt_faturamento_fim,$dt_vencimento_ini,$dt_vencimento_fim,$dt_pagamento_ini,$dt_pagamento_fim, $ds_ano, $ds_mes){

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $result = [];

        $queryValorInicial = (new ContaBancaria($this->pdo))->listarValorInicial($empresas_pk,$contas_bancarias_pk);

        if(count($queryValorInicial) > 0){

            $vl_saldo_inicial = $queryValorInicial[0]['vl_saldo_inicial'];
            $ds_mes_anterior = intval($ds_mes) - 1;
            if($ds_mes_anterior<10){
                $ds_mes_anterior = "0".$ds_mes_anterior;
            }
            $ds_ano_anterior = $ds_ano;
            if($ds_mes_anterior == 0){
                $ds_mes_anterior = 12;
                $ds_ano_anterior = intval($ds_ano) - 1;
            }

            //verifico o primeiro mes de lancamento
            $queryPriLancamento = $this->listaPrimeiroLancamento($empresas_pk,$contas_bancarias_pk);

            $mes_pri_vencimento = "";
            $ano_pri_vencimento = "";
            if(count($queryPriLancamento) > 0){
                $mes_pri_vencimento = $queryPriLancamento[0]['mes_pri_vencimento'];
                $ano_pri_vencimento = $queryPriLancamento[0]['ano_pri_vencimento'];
            }

            $queryMesAnterior = [];
            if($mes_pri_vencimento != '' && $ano_pri_vencimento != ''){
                $queryMesAnterior = $this->listarExtrato($empresas_pk,$contas_bancarias_pk,$mes_pri_vencimento,$ano_pri_vencimento, $ds_mes_anterior, $ds_ano_anterior);
            }

            $vl_receita_mes_anterior = 0.00;
            $vl_despesa_mes_anterior = 0.00;
            $vl_saldo_mes_anterior = 0.00;

            for($l = 0; $l < count($queryMesAnterior); $l++){

                if($queryMesAnterior[$l]["tipo_lancamento_pk"]==1){
                    $vl_receita_mes_anterior += $queryMesAnterior[$l]["vl_lancamento"];
                }

                if($queryMesAnterior[$l]["tipo_lancamento_pk"]!=1){
                    $vl_despesa_mes_anterior += $queryMesAnterior[$l]["vl_lancamento"];
                }
            }
            $vl_total_mes_anterior = $vl_receita_mes_anterior - $vl_despesa_mes_anterior;

            $sql ="";
            $sql.="SELECT l.pk, date_format(l.dt_cadastro, '%d/%m/%Y %H:%i') dt_cadastro";
            $sql.="       ,date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento ";
            $sql.="       ,date_format(l.dt_pagamento, '%d/%m/%Y') dt_pagamento";
            $sql.="       ,date_format(l.dt_faturamento, '%d/%m/%Y') dt_faturamento";
            $sql.="       ,CASE l.tipo_lancamento_pk";
            $sql.="        WHEN 1 THEN 'Receita'";
            $sql.="        WHEN 2 THEN 'Despesa Fixa'";
            $sql.="        WHEN 3 THEN 'Despesa Variada'";
            $sql.="        WHEN 4 THEN 'Imposto'";
            $sql.="        WHEN 5 THEN 'Transferência'";
            $sql.="        WHEN 6 THEN 'Caixinha'";
            $sql.="         END ds_operacao";
            $sql.="       ,CASE l.tipo_grupo_lancamento_pk";
            $sql.="        WHEN 1 THEN 'Clientes'";
            $sql.="        WHEN 2 THEN 'Colaboradores'";
            $sql.="        WHEN 3 THEN 'Fornecedores'";
            $sql.="        WHEN 4 THEN 'Outros'";
            $sql.="         END ds_tipo_grupo";
            $sql.="       ,mp.ds_metodo_pagamento";
            $sql.="       ,l.vl_lancamento";
            $sql.="       ,l.ic_status_lancamento"; 
            $sql.="       ,l.ds_lancamento";
            $sql.="       ,l.tipo_lancamento_pk";
            $sql.="       ,l.tipo_lancamento_pk";
            $sql.="       ,l.tipos_operacao_pk";
            $sql.="       ,u.ds_usuario";
            $sql.="       ,l.grupo_lancamento_pk";
            $sql.="       ,l.tipo_grupo_lancamento_pk";
            $sql.="       ,top.ds_tipo_operacao";
            $sql.="       ,l.ic_status_lancamento";
            $sql.="       ,SUM(lfbp.vl_baixa_parcial) vl_baixa_parcial";
            $sql.="  FROM lancamentos_financeiros l";
            $sql.="  INNER JOIN metodos_pagamento mp ON l.metodos_pagamento_pk = mp.pk";
            $sql.="  INNER JOIN tipos_operacao top on l.tipos_operacao_pk = top.pk";
            $sql.="  INNER JOIN usuarios u ON l.usuario_cadastro_pk = u.pk";
            $sql.="   LEFT JOIN lancamentos_financeiros_baixa_parcial lfbp ON lfbp.lancamentos_financeiros_pk = l.pk";
            $sql.=" WHERE l.empresa_lancamento_pk = ".$empresas_pk;
            $sql.="       AND l.contas_bancarias_pk = ".$contas_bancarias_pk;
            $sql.="       AND l.dt_vencimento >='".$ds_ano."-".$ds_mes."-01 00:00:00'";
            $sql.="       AND l.dt_vencimento <='".$ds_ano."-".$ds_mes."-".cal_days_in_month(CAL_GREGORIAN, $ds_mes , $ds_ano)." 23:59:59'";
            
            if (!empty($dt_cadastro_ini) and !empty($dt_cadastro_fim)) {
                $sql.=" AND l.dt_cadastro >= '".Util::DataYMD($dt_cadastro_ini)." 00:00:00'";
                $sql.=" AND l.dt_cadastro <= '".Util::DataYMD($dt_cadastro_fim)." 23:59:00'"; 

            }
            if (!empty($dt_faturamento_ini) and !empty($dt_faturamento_fim)) {
                $sql.=" AND l.dt_faturamento >= '".Util::DataYMD($dt_faturamento_ini)." 00:00:00'";
                $sql.=" AND l.dt_faturamento <= '".Util::DataYMD($dt_faturamento_fim)." 23:59:00'"; 

            }
            if (!empty($dt_pagamento_ini) and !empty($dt_pagamento_fim)) {
                $sql.=" AND l.dt_pagamento >= '".Util::DataYMD($dt_pagamento_ini)." 00:00:00'";
                $sql.=" AND l.dt_pagamento <= '".Util::DataYMD($dt_pagamento_fim)." 23:59:00'"; 
            }

            $sql.=" GROUP BY l.pk";
            $sql.=" ORDER BY l.dt_vencimento";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $vl_receita = 0.00;
            $vl_despesa = 0.00;
            $vl_total = 0.00;
            $vl_total_dia = 0.00;
            $vl_receita_dia = 0.00;
            $vl_despesa_dia = 0.00; 
            $extrato = [];
            for($i = 0; $i < count($query); $i++){

                if($query[$i]['tipo_grupo_lancamento_pk']==1){
                    $queryLead = $this->listaLeads($query[$i]['grupo_lancamento_pk']);
                    $ds_recebido_de = $queryLead[0]['ds_lead'];
                    $ds_cpf_cnpj = $queryLead[0]['ds_cpf_cnpj'];
                }else if($query[$i]['tipo_grupo_lancamento_pk']==2){
                    $queryLead = $this->listarColaboradores($query[$i]['grupo_lancamento_pk']);
                    $ds_recebido_de = $queryLead[0]['ds_colaborador'];
                    $ds_cpf_cnpj = $queryLead[0]['ds_cpf'];
                }else if($query[$i]['tipo_grupo_lancamento_pk']==3){
                    $queryLead = $this->listarFornecedores($query[$i]['grupo_lancamento_pk']);
                    $ds_recebido_de = $queryLead[0]['ds_fornecedor'];
                    $ds_cpf_cnpj = $queryLead[0]['ds_cpf_cnpj'];
                }

                if($query[$i]["tipo_lancamento_pk"]==1){
                    $vl_receita += $query[$i]["vl_lancamento"];
                    if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                        $vl_receita = $vl_receita - $query[$i]["vl_baixa_parcial"];
                    }
                }
                if($query[$i]["tipo_lancamento_pk"]!=1){
                    $vl_despesa += $query[$i]["vl_lancamento"];
                    if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                        $vl_despesa = $vl_despesa - $query[$i]["vl_baixa_parcial"];
                    }
                }

                $vl_total += $query[$i]["vl_lancamento"];
                $data = $query[$i]["dt_vencimento"];
                //$l = $i;
                $l = $i ;
                if((count($query)-1) == $i){
                    $a = $i;
                }
                else{
                    $a = $i + 1;
                }

                $data_anterior = $query[$l]["dt_vencimento"];
                if($data_anterior == null){
                    $data_anterior = "";
                }
                $proxima_data = $query[$a]["dt_vencimento"];
                if($proxima_data == null){
                    $proxima_data = "";
                }

                $vl_lancamento = $query[$i]["vl_lancamento"];

                if($data_anterior == $data){
                    $vl_total_dia += $query[$i]["vl_lancamento"];
                    if($query[$i]["tipo_lancamento_pk"]==1){
                        $vl_receita_dia += $query[$i]["vl_lancamento"];
                        if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                            $vl_lancamento = $query[$i]["vl_lancamento"] - $query[$i]["vl_baixa_parcial"];
                            $vl_receita_dia = $vl_receita_dia - $query[$i]["vl_baixa_parcial"];
                        }
                    }else if($query[$i]["tipo_lancamento_pk"]!=1){
                        $vl_despesa_dia += $query[$i]["vl_lancamento"];
                        if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                            $vl_lancamento = $query[$i]["vl_lancamento"] - $query[$i]["vl_baixa_parcial"];
                            $vl_despesa_dia = $vl_despesa_dia - $query[$i]["vl_baixa_parcial"];
                        }
                    }
                }else{         
                    $vl_total_dia = $query[$i]["vl_lancamento"];
                    $vl_receita_dia = 0.00;
                    $vl_despesa_dia = 0.00; 

                    if($query[$i]["tipo_lancamento_pk"]==1){
                        $vl_receita_dia = $query[$i]["vl_lancamento"];
                        if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                            $vl_lancamento = $query[$i]["vl_lancamento"] - $query[$i]["vl_baixa_parcial"];
                            $vl_receita_dia = $query[$i]["vl_lancamento"] - $query[$i]["vl_baixa_parcial"];
                        }
                    }else if($query[$i]["tipo_lancamento_pk"]!=1){
                        $vl_despesa_dia = $query[$i]["vl_lancamento"];
                        if($query[$i]["vl_lancamento"] > $query[$i]["vl_baixa_parcial"]){
                            $vl_lancamento = $query[$i]["vl_lancamento"] - $query[$i]["vl_baixa_parcial"];
                            $vl_despesa_dia = $query[$i]["vl_lancamento"] - $query[$i]["vl_baixa_parcial"];
                        }
                    }
                }

                $vl_total_saldo_dia = $vl_receita_dia - $vl_despesa_dia;

                $extrato[] = array(
                    "pk" => $query[$i]["pk"],
                    "dt_cadastro" => $query[$i]["dt_cadastro"],
                    "dt_vencimento" => $query[$i]["dt_vencimento"],
                    "dt_faturamento" => $query[$i]["dt_faturamento"],
                    "ds_lancamento" => $query[$i]["ds_lancamento"],
                    "dt_pagamento" => $query[$i]["dt_pagamento"],
                    "ds_operacao" => $query[$i]["ds_operacao"],
                    "ds_tipo_grupo" => $query[$i]["ds_tipo_grupo"],
                    "ds_recebido_pago_origem" => $ds_recebido_de,
                    "ds_cpf_cnpj" => $ds_cpf_cnpj,
                    "ds_metodo_pagamento" => $query[$i]["ds_metodo_pagamento"],
                    "vl_lancamento" =>  number_format($vl_lancamento, 2, ',', '.'),
                    "ds_usuario" => $query[$i]["ds_usuario"],
                    "tipos_operacao_pk" => $query[$i]["tipos_operacao_pk"],
                    "ic_status_pagamento" => $query[$i]["ic_status_lancamento"],
                    "ds_tipo_operacao" => $query[$i]["ds_tipo_operacao"],
                    "tipo_lancamento_pk" => $query[$i]["tipo_lancamento_pk"],
                    "vl_baixa_parcial" => $query[$i]["vl_baixa_parcial"],
                    "total_dia" =>  number_format($vl_total_dia, 2, ',', '.'),
                    "receita_dia" =>  number_format($vl_receita_dia, 2, ',', '.'),
                    "despesa_dia" =>  number_format($vl_despesa_dia, 2, ',', '.'),
                    "vl_total_saldo_dia" =>  number_format($vl_total_saldo_dia, 2, ',', '.'),
                    "proxima_data" => $proxima_data
                );

                
            }

        }else{
            $extrato = [];
        }

        $vl_saldo_mes_anterior = $vl_receita_mes_anterior - $vl_despesa_mes_anterior;
        $vl_total_saldo_mes = $vl_receita - $vl_despesa;
        $vl_saldo_atual = $vl_saldo_mes_anterior + $vl_total_saldo_mes;

        $result[] = array(
            "vl_inicial_conta" => $vl_saldo_inicial,                 
            "vl_total_receita" => number_format($vl_receita, 2, ',', '.'),
            "vl_total_despesa" => number_format($vl_despesa, 2, ',', '.'),
            "vl_total" => number_format($vl_receita - $vl_despesa, 2, ',', '.'), 
            "vl_total_saldo_mes" => number_format($vl_receita - $vl_despesa, 2, ',', '.'),
            "vl_saldo_mes_anterior" => number_format($vl_total_mes_anterior, 2, ',', '.'),
            "vl_saldo_atual" => number_format($vl_saldo_atual, 2, ',', '.'),
            "DadosExtrato"=>$extrato
        );

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result;
        return $retorno;
    }

    public function listarExtratoConciliacao($empresas_pk,$contas_bancarias_pk,$dt_periodo_ini,$dt_periodo_fim){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $result = [];
        $mysql_data = [];

        //LANCAMENTO NOVO
        $sql ="";
        $sql.="SELECT l.pk, date_format(l.dt_cadastro, '%d/%m/%Y %H:%i') dt_cadastro";
        $sql.="       ,date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento ";
        $sql.="       ,date_format(l.dt_pagamento, '%d/%m/%Y') dt_pagamento";
        $sql.="       ,date_format(l.dt_faturamento, '%d/%m/%Y') dt_faturamento";
        $sql.="       ,CASE l.tipo_lancamento_pk";
        $sql.="        WHEN 1 THEN 'Receita'";
        $sql.="        WHEN 2 THEN 'Despesa Fixa'";
        $sql.="        WHEN 3 THEN 'Despesa Variada'";
        $sql.="        WHEN 4 THEN 'Imposto'";
        $sql.="        WHEN 5 THEN 'Transferência'";
        $sql.="        WHEN 6 THEN 'Caixinha'";
        $sql.="         END ds_operacao";
        $sql.="       ,CASE l.tipo_grupo_lancamento_pk";
        $sql.="        WHEN 1 THEN 'Clientes'";
        $sql.="        WHEN 2 THEN 'Colaboradores'";
        $sql.="        WHEN 3 THEN 'Fornecedores'";
        $sql.="        WHEN 4 THEN 'Outros'";
        $sql.="         END ds_tipo_grupo";
        $sql.="       ,mp.ds_metodo_pagamento";
        $sql.="       ,l.vl_lancamento";
        $sql.="       ,l.ic_status_lancamento";
        $sql.="       ,l.ds_lancamento";
        $sql.="       ,l.tipo_lancamento_pk";
        $sql.="       ,l.tipo_lancamento_pk";
        $sql.="       ,l.tipos_operacao_pk";
        $sql.="       ,u.ds_usuario";
        $sql.="       ,l.grupo_lancamento_pk";
        $sql.="       ,l.tipo_grupo_lancamento_pk";
        $sql.="       ,top.ds_tipo_operacao";
        $sql.="       ,l.ic_status_lancamento";
        $sql.="        ,fl.financeiro_conciliacao_banco_itens_pk";
        $sql.="        ,fl.ic_status ic_status_conciliacao";
        $sql.="  FROM lancamentos_financeiros l";
        $sql.="  INNER JOIN metodos_pagamento mp ON l.metodos_pagamento_pk = mp.pk";
        $sql.="  INNER JOIN tipos_operacao top on l.tipos_operacao_pk = top.pk";
        $sql.="  LEFT JOIN usuarios u ON l.usuario_cadastro_pk = u.pk";
        $sql.="  Left JOIN financeiro_conciliacao_lancamentos fl ON fl.lancamentos_pk = l.pk";
        $sql.=" WHERE 1=1";
        $sql.="       AND l.dt_vencimento >='".Util::DataYMD($dt_periodo_ini)." 00:00:00'";
        $sql.="       AND l.dt_vencimento <='".Util::DataYMD($dt_periodo_fim)." 23:59:59'";
        $sql.=" ORDER BY l.dt_vencimento";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if(count($query) > 0){
            $vl_receita = 0;
            $vl_despesa = 0;
            $vl_total = 0;
            $vl_total_dia = 0;
            $vl_receita_dia = 0;
            $vl_despesa_dia = 0;
            for($i = 0; $i < count($query); $i++){

                if($query[$i]["tipos_operacao_pk"]==1){
                    $vl_receita += $query[$i]["vl_lancamento"];
                }

                if($query[$i]["tipos_operacao_pk"]!=1){
                    $vl_despesa += $query[$i]["vl_lancamento"];
                }
                if($query[$i]['tipo_grupo_pk']==1){
                    $queryLead = $this->listaItensGrupoLeads($query[$i]['grupo_lancamento_pk']);
                    $ds_recebido_de = $queryLead->data[0]['ds_lead'];
                }else if($query[$i]['tipo_grupo_pk']==2){
                    $queryLead = $this->listaItensGrupoColaboradores($query[$i]['grupo_lancamento_pk']);
                    $ds_recebido_de = $queryLead->data[0]['ds_colaborador'];
                }else if($query[$i]['tipo_grupo_pk']==3){
                    $queryLead = $this->listaItensGrupoFornecedores($query[$i]['grupo_lancamento_pk']);
                    $ds_recebido_de = $queryLead->data[0]['ds_fornecedor'];
                }



                $vl_total += $query[$i]["vl_lancamento"];
                $data = $query[$i]["dt_vencimento"];
                $l = $i ;
                if((count($query)-1) == $i){
                    $a = $i;
                }
                else{
                    $a = $i + 1;
                }
                $data_anterior = $query[$l]["dt_vencimento"];
                if($data_anterior == null){
                    $data_anterior = "";
                }
                $proxima_data = $query[$a]["dt_vencimento"];
                if($proxima_data == null){
                    $proxima_data = "";
                }

                if($data_anterior == $data){
                    $vl_total_dia += $query[$i]["vl_lancamento"];
                    if($query[$i]["tipo_lancamento_pk"]==1){
                        $vl_receita_dia += $query[$i]["vl_lancamento"];
                    }else if($query[$i]["tipo_lancamento_pk"]!=1){
                        $vl_despesa_dia += $query[$i]["vl_lancamento"];
                    }
                }else{
                    $vl_total_dia = $query[$i]["vl_lancamento"];
                    $vl_receita_dia = "";
                    $vl_despesa_dia = "";

                    if($query[$i]["tipo_lancamento_pk"]==1){
                        $vl_receita_dia += $query[$i]["vl_lancamento"];
                    }else if($query[$i]["tipo_lancamento_pk"]!=1){
                        $vl_despesa_dia += $query[$i]["vl_lancamento"];
                    }

                }

                $mysql_data[] = array(
                    "pk" => $query[$i]["pk"],
                    "dt_cadastro" => $query[$i]["dt_cadastro"],
                    "dt_vencimento" => $query[$i]["dt_vencimento"],
                    "financeiro_conciliacao_banco_itens_pk" => $query[$i]["financeiro_conciliacao_banco_itens_pk"],
                    "ds_recebido_pago_origem" => $ds_recebido_de,
                    "dt_faturamento" => $query[$i]["dt_faturamento"],
                    "ds_lancamento" => $query[$i]["ds_lancamento"],
                    "dt_pagamento" => $query[$i]["dt_pagamento"],
                    "ds_operacao" => $query[$i]["ds_operacao"],
                    "ds_tipo_grupo" => $query[$i]["ds_tipo_grupo"],
                    "ds_metodo_pagamento" => $query[$i]["ds_metodo_pagamento"],
                    "vl_lancamento" =>  number_format($query[$i]["vl_lancamento"], 2, ',', '.'),
                    "ds_usuario" => $query[$i]["ds_usuario"],
                    "operacao_pk" => $query[$i]["tipo_lancamento_pk"],
                    "ic_status_pagamento" => $query[$i]["ic_status_lancamento"],
                    "ic_status_conciliacao" =>$query[$i]['ic_status_conciliacao'],
                    "ds_tipo_operacao" => $query[$i]["ds_tipo_operacao"],
                    "total_dia" =>  number_format($vl_total_dia, 2, ',', '.'),
                    "receita_dia" =>  number_format($vl_receita_dia, 2, ',', '.'),
                    "despesa_dia" =>  number_format($vl_despesa_dia, 2, ',', '.'),
                    "vl_total" =>  number_format($vl_total, 2, ',', '.'),
                    "vl_total_saldo_dia" =>  number_format(($vl_receita_dia - $vl_despesa_dia), 2, ',', '.'),
                    "proxima_data" => $proxima_data,

                    "t_functions" => ""
                );
            }
        }



        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $mysql_data;
        $retorno->iTotalDisplayRecords = count($mysql_data);
        $retorno->iTotalRecords = count($mysql_data);
        echo json_encode($retorno);
        exit(0);
    }

    public function RelatorioLancamento($tipo_lancamento_pk,$dt_vencimento_ini,$dt_vencimento_fim,$ic_status_pagamento,$empresas_pk,$tipo_grupo_pk,$grupo_leancamento_pk,$usuario_cadastro_pk,$dt_lancamento_ini,$dt_lancamento_fim,$dt_pagamento_ini,$dt_pagamento_fim,$plano_contas,$dt_faturamento_ini,$dt_faturamento_fim,$tipos_operacao_pk_receita, $contas_bancarias_pk,$contas_bancarias_pagamento_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $mysql_data = [];


        //LANCAMENTO NOVO
        $sql ="";
        $sql.="SELECT distinct(l.pk), date_format(l.dt_cadastro, '%d/%m/%Y %H:%i') dt_cadastro";
        $sql.="       ,date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento ";
        $sql.="       ,date_format(l.dt_pagamento, '%d/%m/%Y') dt_pagamento";
        $sql.="       ,date_format(l.dt_faturamento, '%d/%m/%Y') dt_faturamento";
        $sql.="       ,CASE l.tipo_lancamento_pk";
        $sql.="        WHEN 1 THEN 'Receita'";
        $sql.="        WHEN 2 THEN 'Despesa Fixa'";
        $sql.="        WHEN 3 THEN 'Despesa Variada'";
        $sql.="        WHEN 4 THEN 'Imposto'";
        $sql.="        WHEN 5 THEN 'Transfer ncia'";
        $sql.="        WHEN 6 THEN 'Caixinha'";
        $sql.="         END ds_operacao";
        $sql.="       ,CASE l.tipo_grupo_lancamento_pk";
        $sql.="        WHEN 1 THEN 'Clientes'";
        $sql.="        WHEN 2 THEN 'Colaboradores'";
        $sql.="        WHEN 3 THEN 'Fornecedores'";
        $sql.="        WHEN 4 THEN 'Outros'";
        $sql.="         END ds_tipo_grupo";
        $sql.="       ,mp.ds_metodo_pagamento";
        $sql.="       ,l.vl_lancamento";
        $sql.="       ,l.ic_status_lancamento";
        $sql.="       ,l.ds_lancamento";
        $sql.="       ,l.tipo_lancamento_pk";
        $sql.="       ,l.tipo_lancamento_pk";
        $sql.="       ,l.tipos_operacao_pk";
        $sql.="       ,u.ds_usuario";
        $sql.="       ,l.grupo_lancamento_pk";
        $sql.="       ,l.tipo_grupo_lancamento_pk";
        $sql.="       ,top.ds_tipo_operacao";
        $sql.="       ,l.ic_status_lancamento";
        $sql.="       ,l.obs_lancamento";
        $sql.="       ,l.empresa_lancamento_pk";
        $sql.="       ,l.ds_num_documento";
        $sql.="       ,l.contas_bancarias_pk";
        $sql.="       ,l.metodos_pagamento_pk";
        $sql.="       ,top.ds_tipo_operacao ";
        $sql.="       ,cb.ds_conta_bancaria ";
        $sql.="       ,mp.ds_metodo_pagamento";
        $sql.="       ,co.ds_razao_social";
        $sql.="       ,cb.vl_saldo_inicial";
        $sql.="       ,cb.ds_conta ds_conta_bancaria_pagamento";
        $sql.="       ,cb.ds_agencia ";
        $sql.="       ,b.ds_banco ";
        $sql.="       ,cb.ds_conta_bancaria";
        $sql.="       ,u.ds_usuario";
        $sql.="       ,case l.ic_status_lancamento when 1 then 'Pago' when 2 then 'Pendente' when 3 then 'Aprovado' when 4 then 'Atrasado' when 5 then 'Cancelado'  end ds_status_pagamento";
        $sql.="  FROM lancamentos_financeiros l";
        $sql.="  left join tipos_operacao top on l.tipos_operacao_pk = top.pk";
        $sql.="  left join contas_bancarias cb on l.contas_bancarias_pk = cb.pk";
        $sql.="  inner join contas ct on ct.pk = cb.empresas_pk ";   
        $sql.="  left join bancos b on b.pk = cb.bancos_pk ";
        $sql.="  left join metodos_pagamento mp on l.metodos_pagamento_pk = mp.pk";
        $sql.="  left join contas co on l.empresa_lancamento_pk = co.pk";
        $sql.="  inner join usuarios u on l.usuario_cadastro_pk = u.pk";
        $sql.=" where 1=1 ";
        if($tipo_lancamento_pk == 1){
            $sql.=" and l.tipo_lancamento_pk = 1";
        }
        else if($tipo_lancamento_pk == 2){
            $sql.=" and l.tipo_lancamento_pk > 1";
        }
        if($empresas_pk != ""){
            $sql.=" and l.empresa_lancamento_pk = ".$empresas_pk;
        }
        if($tipo_grupo_pk != ""){
            $sql.=" and l.tipo_grupo_lancamento_pk = ".$tipo_grupo_pk;
        }
        if($grupo_leancamento_pk != ""){
            $sql.=" and l.grupo_lancamento_pk = ".$grupo_leancamento_pk;
        }
        if($usuario_cadastro_pk != ""){
            $sql.=" and l.usuario_cadastro_pk = ".$usuario_cadastro_pk;
        }
        if($ic_status_pagamento != ""){
            $sql.=" and l.ic_status_lancamento = ".$ic_status_pagamento;
        }
        if ($contas_bancarias_pk != "" && $contas_bancarias_pagamento_pk == "") {
            // Só tem contas_bancarias_pk
            $sql .= " AND l.contas_bancarias_pk = " . (int)$contas_bancarias_pk;
        } elseif ($contas_bancarias_pagamento_pk != "" && $contas_bancarias_pk == "") {
            // Só tem contas_bancarias_pagamento_pk
            $sql .= " AND l.contas_bancarias_pk = " . (int)$contas_bancarias_pagamento_pk;
        } elseif ($contas_bancarias_pk != "" && $contas_bancarias_pagamento_pk != "") {
            // Se os dois vierem preenchidos
            $sql .= " AND (l.contas_bancarias_pk = " . (int)$contas_bancarias_pk .
                    " AND l.contas_bancarias_pk = " . (int)$contas_bancarias_pagamento_pk . ")";
        }

        if(!empty($tipos_operacao_pk_receita)){
            $sql.=" and l.tipos_operacao_pk=".$tipos_operacao_pk_receita;
        }
        if($dt_pagamento_ini=="" ){
            $sql.=" and (l.dt_pagamento BETWEEN DATE_SUB(CURDATE(), INTERVAL 12 MONTH) AND CURDATE() or l.dt_pagamento is null)";
        }
        if($dt_vencimento_ini!=""){
            $sql.=" and l.dt_vencimento between '".Util::DataYMD($dt_vencimento_ini)."' and '".Util::DataYMD($dt_vencimento_fim)."'";
        }

        if($dt_faturamento_ini!=""){
            $sql.=" and l.dt_faturamento between '".Util::DataYMD($dt_faturamento_ini)."' and '".Util::DataYMD($dt_faturamento_fim)."'";
        }

        if($dt_pagamento_ini!=""){
            $sql.=" and l.dt_pagamento between '".Util::DataYMD($dt_pagamento_ini)."' and '".Util::DataYMD($dt_pagamento_fim)."'";
        }

        if(!empty($plano_contas)){
            $sql.=" and top.pk=".$plano_contas;
        }

        $sql.=" group by l.pk";
        $sql.=" order by l.dt_vencimento asc ";
    
        

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if(count($query) > 0){
            $vl_receita = 0;
            $vl_despesa = 0;
            $vl_total = 0;
            $vl_total_dia = 0;
            $vl_receita_dia = 0;
            $vl_despesa_dia = 0;
            $ds_recebido_de = "";
            $ds_recebido_de_centro_custo ="";
            for($i = 0; $i < count($query); $i++){

                if($query[$i]["tipos_operacao_pk"]==1){
                    $vl_receita += $query[$i]["vl_lancamento"];
                }

                if($query[$i]["tipos_operacao_pk"]!=1){
                    $vl_despesa += $query[$i]["vl_lancamento"];
                }
                if($query[$i]['tipo_grupo_lancamento_pk']!=""){
                    if($query[$i]['tipo_grupo_lancamento_pk']==1){
                        $queryLead = $this->listaItensGrupoLeads($query[$i]['grupo_lancamento_pk']);
                        if(isset($queryLead->data[0]['ds_lead'])){
                            $ds_recebido_de = $queryLead->data[0]['ds_lead'];
                        }
                    }else if($query[$i]['tipo_grupo_lancamento_pk']==2){
                        $queryLead = $this->listaItensGrupoColaboradores($query[$i]['grupo_lancamento_pk']);
                        if(isset($queryLead->data[0]['ds_colaborador'])){
                            $ds_recebido_de = $queryLead->data[0]['ds_colaborador'];
                            $ds_recebido_de_centro_custo = $queryLead->data[0]['ds_razao_social'];
                          
                        }
                    }else if($query[$i]['tipo_grupo_lancamento_pk']==3){
                        $queryLead = $this->listaItensGrupoFornecedores($query[$i]['grupo_lancamento_pk']);
                        if(isset($queryLead->data[0]['ds_fornecedor'])){
                            $ds_recebido_de = $queryLead->data[0]['ds_fornecedor'];
                        }
                    }
                }

                /*if($query[$i]['grupo_lancamento_pk']!=""){
                    //CENTRO CUSTO
                    $queryLead = $this->listaItensGrupoLeads($query[$i]['grupo_lancamento_pk']);
                    if(count( $queryLead->data)>0){
                        $ds_recebido_de_centro_custo = $queryLead->data[0]['ds_lead'];
                    }
                    else{
                        $ds_recebido_de_centro_custo ="";
                    }
                }*/
                $ds_conta_bancaria_pagamento = "";
                if($query[$i]['ds_banco'] != ""){
                    $ds_conta_bancaria_pagamento = $query[$i]['ds_banco']." - AG:".$query[$i]['ds_agencia']." - CC:".$query[$i]['ds_conta'];
                }else{
                    $ds_conta_bancaria_pagamento = "Caixinha";
                }





                $vl_total += $query[$i]["vl_lancamento"];
                $data = $query[$i]["dt_vencimento"];
                $l = $i ;
                if((count($query)-1) == $i){
                    $a = $i;
                }
                else{
                    $a = $i + 1;
                }
                $data_anterior = $query[$l]["dt_vencimento"];
                if($data_anterior == null){
                    $data_anterior = "";
                }
                $proxima_data = $query[$a]["dt_vencimento"];
                if($proxima_data == null){
                    $proxima_data = "";
                }

                if($data_anterior == $data){
                    $vl_total_dia += $query[$i]["vl_lancamento"];
                    if($query[$i]["tipo_lancamento_pk"]==1){
                        $vl_receita_dia += $query[$i]["vl_lancamento"];
                    }else if($query[$i]["tipo_lancamento_pk"]!=1){
                        $vl_despesa_dia += $query[$i]["vl_lancamento"];
                    }
                }else{
                    $vl_total_dia = $query[$i]["vl_lancamento"];
                    $vl_receita_dia = "";
                    $vl_despesa_dia = "";

                    if($query[$i]["tipo_lancamento_pk"]==1){
                        $vl_receita_dia += $query[$i]["vl_lancamento"];
                    }else if($query[$i]["tipo_lancamento_pk"]!=1){
                        $vl_despesa_dia += $query[$i]["vl_lancamento"];
                    }

                }


                $mysql_data[] = array(
                    "t_pk" => $query[$i]["pk"],
                    "t_dt_vencimento"=>$query[$i]['dt_vencimento'],
                    "t_dt_competencia"=>"",
                    "t_dt_faturamento"=>$query[$i]['dt_faturamento'],
                    "t_dt_pagamento"=>$query[$i]['dt_pagamento'],
                    "t_vl_inicial_conta"=>$query[$i]['vl_saldo_inicial'],
                    //"t_vl_saldo"=>number_format((($queryReceita[0]['vl_lancamento']-$queryDespesas[0]['vl_lancamento']) ),2,",","."),
                    "t_ds_lancamento"=>$query[$i]['ds_lancamento'],
                    "t_vl_lancamento"=>($query[$i]['vl_lancamento']),
                    "t_operacao_pk"=>$query[$i]['tipo_lancamento_pk'],
                    "t_ds_operacao"=>$query[$i]['ds_operacao'],
                    "t_tipo_grupo_pk"=>$query[$i]['tipo_grupo_lancamento_pk'],
                    "t_ds_tipo_grupo"=>$query[$i]['ds_tipo_grupo'],
                    "t_grupo_leancamento_pk"=>$query[$i]['grupo_lancamento_pk'],
                    "t_ic_status_pagamento"=>$query[$i]['ic_status_lancamento'],
                    "t_ds_status_pagamento"=>$query[$i]['ds_status_pagamento'],
                    "t_obs_lancamento"=>$query[$i]['obs_lancamento'],
                    "t_n_documento"=>$query[$i]['ds_num_documento'],
                    "t_contas_bancarias_pk"=>$query[$i]['contas_bancarias_pk'],
                    "t_tipos_operacao_pk"=>$query[$i]['tipos_operacao_pk'],
                    "t_metodos_pagamento_pk"=>$query[$i]['metodos_pagamento_pk'],
                    "t_ds_metodo_pagamento"=>$query[$i]['ds_metodo_pagamento'],
                    "t_ds_conta_bancaria"=>$query[$i]['ds_conta_bancaria'],
                    "t_ds_tipo_operacao"=>$query[$i]['ds_tipo_operacao'],
                    "t_empresas_pk"=>$query[$i]['empresa_lancamento_pk'],
                    "t_ds_razao_social"=>$query[$i]['ds_razao_social'],
                    "tipo_grupo_centro_custo_pk"=>"",
                    "t_ds_tipo_grupo_centro_custo"=>"",
                    "grupo_lancamento_centro_custo_pk"=>"",
                    "ds_ocorrencia"=>"",
                    "ds_usuario"=>$query[$i]['ds_usuario'],
                    "dt_faturamento"=>$query[$i]['dt_faturamento'],
                    "t_ds_recebido_de"=>$ds_recebido_de,
                    "t_vl_total"=>$vl_total,
                    "t_ds_conta_bancaria_pagamento"=>$ds_conta_bancaria_pagamento,
                    "t_ds_recebido_de_centro_custo"=>$ds_recebido_de_centro_custo,

                    "t_functions" => ""
                );
            }
        }


        $retorno->data = $mysql_data;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        $retorno->iTotalDisplayRecords = count($mysql_data);
        $retorno->iTotalRecords = count($mysql_data);
        return $retorno;

    }

    public function relReceitaPostoTrabalho($leads_pk,$leads_clientes_pk,$contratos_pk_combo){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $mysql_data = [];
       
        //VERIFICANDO NA TABELA ANTIGA
        $sql=" select ";
        $sql.="    ll.ds_lead ds_cliente,";
        $sql.="    l.ds_lead,";
        $sql.="    concat('Contrato - ',c.pk)ds_contrato,";
        $sql.="    c.vl_contrato,";
        $sql.="    lt.vl_lancamento";
        $sql.=" from lancamentos lt ";
        $sql.=" inner join leads l on lt.leads_posto_trabalho_pk = l.pk";
        $sql.=" left join leads ll on l.leads_pai_pk = ll.pk";
        $sql.=" inner join contratos c on lt.contratos_pk = c.pk";
        $sql.=" where lt.operacao_pk = 1";
        $sql.=" and c.dt_cancelamento is null";
        if($leads_pk!=""){
            $sql.=" and l.pk = ".$leads_pk;
        }
        if($leads_clientes_pk!=""){
            $sql.=" and ll.pk = ".$leads_clientes_pk;
        }
        if($contratos_pk_combo!=""){
            $sql.=" and c.pk = ".$contratos_pk_combo;
        }
       
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if(count($query)>0){
            for($i=0;$i< count($query);$i++){
                $mysql_data[] = array( 
                    "ds_cliente"=>$query[$i]['ds_cliente'],
                    "ds_lead"=>$query[$i]['ds_lead'],
                    "ds_contrato"=>$query[$i]['ds_contrato'],
                    "vl_contrato"=>$query[$i]['vl_contrato'],
                    "vl_receita"=>$query[$i]['vl_lancamento']
                );
            }
        }

        //VERIFICANDO NA TABELA NOVA
        $sql=" select ";
        $sql.="    ll.ds_lead ds_cliente,";
        $sql.="    l.ds_lead,";
        $sql.="    concat('Contrato - ',c.pk)ds_contrato,";
        $sql.="    c.vl_contrato,";
        $sql.="    lf.vl_lancamento";
        $sql.=" from lancamentos_financeiros lf";
        $sql.=" inner join leads l on lf.posto_trabalho_lancamento_pk = l.pk";
        $sql.=" left join leads ll on l.leads_pai_pk = ll.pk";
        $sql.=" inner join contratos c on lf.contratos_pk = c.pk";
        $sql.=" where lf.tipo_lancamento_pk = 1";
        $sql.=" and c.dt_cancelamento is null";
        if($leads_pk!=""){
            $sql.=" and l.pk = ".$leads_pk;
        }
        if($leads_clientes_pk!=""){
            $sql.=" and ll.pk = ".$leads_clientes_pk;
        }
        if($contratos_pk_combo!=""){
            $sql.=" and c.pk = ".$contratos_pk_combo;
        }
       
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $queryN = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if(count($queryN)>0){
            for($i=0;$i< count($queryN);$i++){
                $mysql_data[] = array( 
                    "ds_cliente"=>$queryN[$i]['ds_cliente'],
                    "ds_lead"=>$queryN[$i]['ds_lead'],
                    "ds_contrato"=>$queryN[$i]['ds_contrato'],
                    "vl_contrato"=>$queryN[$i]['vl_contrato'],
                    "vl_receita"=>$queryN[$i]['vl_lancamento']
                );
            }
        }


        
        $retorno->data = $mysql_data;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        $retorno->iTotalDisplayRecords = count($mysql_data);
        $retorno->iTotalRecords = count($mysql_data);
    
        echo json_encode($retorno);
        exit(0);
    }

    public function relDespesaPostoTrabalho($leads_pk,$leads_clientes_pk,$contratos_pk_combo){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $mysql_data = [];
       
        //VERIFICANDO NA TABELA ANTIGA
        $sql=" select ";
        $sql.="    ll.ds_lead ds_cliente,";
        $sql.="    l.ds_lead,";
        $sql.="    concat('Contrato - ',c.pk)ds_contrato,";
        $sql.="    c.vl_contrato,";
        $sql.="        CASE lt.operacao_pk";
        $sql.="           WHEN 1 THEN 'Receita'";
        $sql.="           WHEN 2 THEN 'Despesa Fixa'";
        $sql.="           WHEN 3 THEN 'Despesa Variada'";
        $sql.="           WHEN 4 THEN 'Imposto'";
        $sql.="           WHEN 5 THEN 'Transferência'";
        $sql.="           WHEN 6 THEN 'Caixinha'";
        $sql.="        END";
        $sql.="           ds_operacao,";
        $sql.="    lt.vl_lancamento";
        $sql.=" from lancamentos lt ";
        $sql.=" inner join leads l on lt.leads_posto_trabalho_pk = l.pk";
        $sql.=" left join leads ll on l.leads_pai_pk = ll.pk";
        $sql.=" inner join contratos c on lt.contratos_pk = c.pk";
        $sql.=" where lt.operacao_pk != 1";
        $sql.=" and c.dt_cancelamento is null";
        if($leads_pk!=""){
            $sql.=" and l.pk = ".$leads_pk;
        }
        if($leads_clientes_pk!=""){
            $sql.=" and ll.pk = ".$leads_clientes_pk;
        }
        if($contratos_pk_combo!=""){
            $sql.=" and c.pk = ".$contratos_pk_combo;
        }
       
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if(count($query)>0){
            for($i=0;$i< count($query);$i++){
                $mysql_data[] = array( 
                    "ds_cliente"=>$query[$i]['ds_cliente'],
                    "ds_lead"=>$query[$i]['ds_lead'],
                    "ds_contrato"=>$query[$i]['ds_contrato'],
                    "vl_contrato"=>$query[$i]['vl_contrato'],
                    "vl_despesa"=>$query[$i]['vl_lancamento'],
                    "ds_tipo_despesa"=>$query[$i]['ds_operacao']
                );
            }
        }

        //VERIFICANDO NA TABELA NOVA
        $sql=" select ";
        $sql.="    ll.ds_lead ds_cliente,";
        $sql.="    l.ds_lead,";
        $sql.="    concat('Contrato - ',c.pk)ds_contrato,";
        $sql.="    c.vl_contrato,";
        $sql.="        CASE lf.tipo_lancamento_pk";
        $sql.="           WHEN 1 THEN 'Receita'";
        $sql.="           WHEN 2 THEN 'Despesa Fixa'";
        $sql.="           WHEN 3 THEN 'Despesa Variada'";
        $sql.="           WHEN 4 THEN 'Imposto'";
        $sql.="           WHEN 5 THEN 'Transferência'";
        $sql.="           WHEN 6 THEN 'Caixinha'";
        $sql.="        END";
        $sql.="           ds_operacao,";
        $sql.="    lf.vl_lancamento";
        $sql.=" from lancamentos_financeiros lf";
        $sql.=" inner join leads l on lf.posto_trabalho_lancamento_pk = l.pk";
        $sql.=" left join leads ll on l.leads_pai_pk = ll.pk";
        $sql.=" inner join contratos c on lf.contratos_pk = c.pk";
        $sql.=" where lf.tipo_lancamento_pk != 1";
        $sql.=" and c.dt_cancelamento is null";
        if($leads_pk!=""){
            $sql.=" and l.pk = ".$leads_pk;
        }
        if($leads_clientes_pk!=""){
            $sql.=" and ll.pk = ".$leads_clientes_pk;
        }
        if($contratos_pk_combo!=""){
            $sql.=" and c.pk = ".$contratos_pk_combo;
        }
       
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $queryN = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if(count($queryN)>0){
            for($i=0;$i< count($queryN);$i++){
                $mysql_data[] = array( 
                    "ds_cliente"=>$queryN[$i]['ds_cliente'],
                    "ds_lead"=>$queryN[$i]['ds_lead'],
                    "ds_contrato"=>$queryN[$i]['ds_contrato'],
                    "vl_contrato"=>$queryN[$i]['vl_contrato'],
                    "vl_despesa"=>$queryN[$i]['vl_lancamento'],
                    "ds_tipo_despesa"=>$queryN[$i]['ds_operacao']
                );
            }
        }


        
        $retorno->data = $mysql_data;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        $retorno->iTotalDisplayRecords = count($mysql_data);
        $retorno->iTotalRecords = count($mysql_data);
    
        echo json_encode($retorno);
        exit(0);
    }

    public function relLancamentoPlanoConta($dt_vencimento_ini,$dt_vencimento_fim,$tipos_operacao_pk_receita){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $mysql_data = [];

        $sql ="";
        $sql.=" select";
        $sql.="     top.pk tipos_operacao_pk ";
        $sql.="     ,top.ds_tipo_operacao ";
        $sql.=" from lancamentos_financeiros  l";
        $sql.="     inner join tipos_operacao top on l.tipos_operacao_pk = top.pk ";
        $sql.=" where 1=1";
        if(!empty($dt_vencimento_ini)){
            $sql.=" and l.dt_vencimento >='".Util::DataYMD($dt_vencimento_ini)." 00:00:00'";
        }
        if(!empty($dt_vencimento_fim)){
            $sql.=" and l.dt_vencimento <='".Util::DataYMD($dt_vencimento_fim)." 23:59:59'";
        }
        if(!empty($tipos_operacao_pk_receita)){
            $sql.=" and l.tipos_operacao_pk=".$tipos_operacao_pk_receita;
        }
        $sql.=" and l.dt_vencimento is not null";
        $sql.=" group by l.tipos_operacao_pk ";
        $sql.=" order by top.ds_tipo_operacao";



        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if(count($query) > 0){

            for($i = 0; $i < count($query); $i++){

                $ds_tipo_operacao = $query[$i]["ds_tipo_operacao"];

                if(!empty($ds_tipo_operacao)){

                    $sql ="";
                    $sql.=" select";
                    $sql.="     date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento";
                    $sql.="     ,l.ds_lancamento ds_lancamento";
                    $sql.="     ,l.ic_parcela parcela_pk";
                    $sql.="     ,sum(l.vl_lancamento) vl_lancamento";
                    $sql.="     ,l.tipos_operacao_pk";
                    $sql.=" from lancamentos_financeiros l";
                    $sql.="     where l.tipos_operacao_pk=". $query[$i]["tipos_operacao_pk"];
                    if(!empty($dt_vencimento_ini)){
                        $sql.=" and l.dt_vencimento >='".Util::DataYMD($dt_vencimento_ini)." 00:00:00'";
                    }
                    if(!empty($dt_vencimento_fim)){
                        $sql.=" and l.dt_vencimento <='".Util::DataYMD($dt_vencimento_fim)." 23:59:59'";
                    }
                    $sql.=" and l.dt_vencimento is not null";
                    $sql.=" group by l.ds_lancamento";
                    $sql.=" Order by l.dt_vencimento";



                    // echo $sql."<br>";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute();
                    $queryLinha = $stmt->fetchAll(\PDO::FETCH_ASSOC);


                    if(count($queryLinha) > 0){
                        $vlTotal = 0;
                        $arrayDados = [];
                        for($a = 0; $a < count($queryLinha); $a++){
                            //echo $a." - ".$queryLinha[$a]['dt_vencimento'].' - '.$queryLinha[$a]['ds_lancamento'].' - '.$queryLinha[$a]['parcela_pk'].' - '.$queryLinha[$a]['vl_lancamento']."<br>";
                            $arrayDados[]= array (

                                "dt_vencimento"=>$queryLinha[$a]['dt_vencimento'],
                                "ds_lancamento"=>$queryLinha[$a]['ds_lancamento'],
                                "parcela_pk"=>$queryLinha[$a]['parcela_pk'],
                                "vl_lancamento"=>$queryLinha[$a]['vl_lancamento'],
                            );
                            $vlTotal += $queryLinha[$a]["vl_lancamento"];
                        }

                        $mysql_data[] = array (
                            'ds_tipo_operacao'=>$ds_tipo_operacao,
                            'DadosLinha'=>$arrayDados,
                            'VlTotal'=>$vlTotal
                        );
                    }

                }
            }

        }

        $retorno->data = $mysql_data;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        $retorno->iTotalDisplayRecords = count($mysql_data);
        $retorno->iTotalRecords = count($mysql_data);

        return $retorno;
    }

    public function cargaTabelaLancamentos(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $mysql_data = [];
        $sql ="";
        $sql.="SELECT   cl.Identificadorfornecedor,";
        $sql.="         cl.Nomefornecedor,";
        $sql.="         cl.Codigoreferencia,";
        $sql.="         cl.Datadecompetencia,";
        $sql.="         cl.Datadevencimento,";
        $sql.="         cl.Dataprevista,";
        $sql.="         cl.Recorrencia,";
        $sql.="         cl.Descricao,";
        $sql.="         cl.Agendado,";
        $sql.="         cl.Valororiginalparcela,";
        $sql.="         cl.Formadepagamento,";
        $sql.="         cl.Valorpagoparcela,";
        $sql.="         cl.Jurosrealizado,";
        $sql.="         cl.Multarealizado,";
        $sql.="         cl.Descontorealizado,";
        $sql.="         cl.Valortotalpagoparcela,";
        $sql.="         cl.Valorparcelaaberto,";
        $sql.="         cl.Jurosprevisto,";
        $sql.="         cl.Multaprevisto,";
        $sql.="         cl.Descontoprevisto,";
        $sql.="         cl.Valortotalparcelaaberto,";
        $sql.="         cl.Contabancaria,";
        $sql.="         cl.Datadoultimopagamento,";
        $sql.="         cl.Observacoes,";
        $sql.="         cl.Categoria1,";
        $sql.="         cl.ValornaCategoria1,";
        $sql.="         cl.CentrodeCusto1,";
        $sql.="         cl.ValornoCentrodeCusto1,";
        $sql.="         cl.Formadepagamento,";
        $sql.="         cl.conta_bancaria_pk,";
        $sql.="         cl.contas_pk,";
        $sql.="         t.pk tipo_operacao_pk,";
        $sql.="         CASE WHEN cl.Formadepagamento = 'Débito automático' THEN 3";
        $sql.="              WHEN cl.Formadepagamento = 'Boleto bancário' THEN 5";
        $sql.="              ELSE 3";
        $sql.="         END metodo_pagamento_pk";
        $sql.="     FROM cargaLancamentos cl"; 
        $sql.="     LEFT JOIN tipos_operacao t ON t.ds_tipo_operacao = cl.Nomefornecedor"; 
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        for($i=0; $i<count($query); $i++){
            $descontorealizado = "";
            $jurosprevisto = "";
            $multaprevisto = "";
            $descontoprevisto = "";
            
            $fields['ds_lancamento'] = "Lançamento Carga Conta Azul x Gepros";
            $fields['tipo_lancamento_pk'] = 3;
            $fields['categorias_financeiras_pk'] = 4;
            $fields['tipos_operacao_pk'] = $query[$i]['tipo_operacao_pk'];
            $fields['tipo_grupo_lancamento_pk'] = 3;
            $fields['grupo_lancamento_pk'] = 5;
            
            $fields['metodos_pagamento_pk'] = $query[$i]['metodo_pagamento_pk'];
            if($query[$i]['Datadoultimopagamento']!='' && $query[$i]['Valortotalparcelaaberto']==0){
                $fields['ic_status_lancamento'] = 1;
            }else{
                $fields['ic_status_lancamento'] = 2;
            }

            if($query[$i]['Datadoultimopagamento']!=""){
                $fields['dt_pagamento'] = Util::DataYMD($query[$i]['Datadoultimopagamento']);
                $fields['ic_status_lancamento'] = 1;
            }else{
                $fields['dt_pagamento'] = "";
            }
    
            if($query[$i]['Descontorealizado']!= 0){
                $descontorealizado = ' Desconto Realizado:'.$query[$i]['Descontorealizado'];
            }
            if($query[$i]['Descontorealizado']!= 0){
                $jurosprevisto = ' Juros Previstos:'.$query[$i]['Jurosprevisto'];
            }
            if($query[$i]['Multaprevisto']!= 0){
                $multaprevisto = ' Multa Prevista:'.$query[$i]['Multaprevisto'];
            }
            if($query[$i]['Descontoprevisto']!= 0){
                $descontoprevisto = ' Multa Prevista:'.$query[$i]['Descontoprevisto'];
            }

            $fields['obs_lancamento'] = $query[$i]['Descricao']." ".$descontorealizado.$jurosprevisto.$multaprevisto.$descontoprevisto;

            
            $fields['empresa_lancamento_pk'] = $query[$i]['contas_pk'];
            $fields['contas_bancarias_pk'] = $query[$i]['conta_bancaria_pk'];

            if($query[$i]['Recorrencia'] != ''){
                $fields['ic_parcela'] = 4;
            }else{
                $fields['ic_parcela'] = 1;
            }
            
            $fields['dt_faturamento'] = Util::DataYMD($query[$i]['Datadecompetencia']);
            $fields['dt_vencimento'] = Util::DataYMD($query[$i]['Datadevencimento']);
    
            $fields['vl_lancamento'] = $query[$i]['Valororiginalparcela'];
    
            $fields["dt_ult_atualizacao"] = "sysdate()";
            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
    
            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   =  $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("lancamentos_financeiros", $fields,$this->pdo);

            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
            
        }
        
        return $retorno;
    }


    public function relContasPagarPeriodo($dt_vencimento_ini,$dt_vencimento_fim,$tipo_lancamento_pk,$contas_bancarias_pk,$tipos_operacao_pk_receita,$empresas_pk,$tipo_grupo_pk, $grupo_leancamento_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $mysql_data = [];
        $arrDados = [];


        //LANCAMENTO NOVO
        $sql ="";
        $sql.="SELECT l.dt_vencimento";
        $sql.="       ,date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento_format ";
        $sql.="       ,sum(l.vl_lancamento) vl_total_despesa_periodo ";
        $sql.="  FROM lancamentos_financeiros l";
        $sql.="  left join tipos_operacao top on l.tipos_operacao_pk = top.pk";
        $sql.="  left join contas_bancarias cb on l.contas_bancarias_pk = cb.pk";
        $sql.="  left join metodos_pagamento mp on l.metodos_pagamento_pk = mp.pk";
        $sql.="  left join contas co on l.empresa_lancamento_pk = co.pk";
        $sql.="  inner join usuarios u on l.usuario_cadastro_pk = u.pk";
        $sql.=" where 1=1 ";
       if($tipo_lancamento_pk == 2){
            $sql.=" and l.tipo_lancamento_pk > 1";
        }
        if($empresas_pk != ""){
            $sql.=" and l.empresa_lancamento_pk = ".$empresas_pk;
        }
        if($tipo_grupo_pk != ""){
            $sql.=" and l.tipo_grupo_lancamento_pk = ".$tipo_grupo_pk;
        }
        if($grupo_leancamento_pk != ""){
            $sql.=" and l.grupo_lancamento_pk = ".$grupo_leancamento_pk;
        }
        if($contas_bancarias_pk != ""){
            $sql.=" and l.contas_bancarias_pk = ".$contas_bancarias_pk;
        }
        if(!empty($tipos_operacao_pk_receita)){
            $sql.=" and l.tipos_operacao_pk=".$tipos_operacao_pk_receita;
        }
        if($dt_vencimento_ini!=""){
            $sql.=" and l.dt_vencimento between '".Util::DataYMD($dt_vencimento_ini)."' and '".Util::DataYMD($dt_vencimento_fim)."'";
        }


        if(!empty($plano_contas)){
            $sql.=" and top.pk=".$plano_contas;
        }
        //$sql.=" and l.ic_status_lancamento = 1";

        $sql.=" group by l.dt_vencimento";
        $sql.=" order by l.dt_vencimento asc ";
        

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $vl_despesa = 0;
        if(count($query) > 0){
           
            for($i=0;$i<count($query);$i++){

                $mysql_data = [];
                $sql ="";
                $sql.="SELECT distinct(l.pk)";
                $sql.="       ,CASE l.tipo_lancamento_pk";
                $sql.="        WHEN 1 THEN 'Receita'";
                $sql.="        WHEN 2 THEN 'Despesa Fixa'";
                $sql.="        WHEN 3 THEN 'Despesa Variada'";
                $sql.="        WHEN 4 THEN 'Imposto'";
                $sql.="        WHEN 5 THEN 'Transferência'";
                $sql.="        WHEN 6 THEN 'Caixinha'";
                $sql.="         END ds_operacao";
                $sql.="       ,CASE l.tipo_grupo_lancamento_pk";
                $sql.="        WHEN 1 THEN 'Clientes'";
                $sql.="        WHEN 2 THEN 'Colaboradores'";
                $sql.="        WHEN 3 THEN 'Fornecedores'";
                $sql.="        WHEN 4 THEN 'Outros'";
                $sql.="         END ds_tipo_grupo";
                $sql.="       ,l.vl_lancamento";
                $sql.="       ,l.tipo_lancamento_pk";
                $sql.="       ,l.tipos_operacao_pk";
                
                $sql.="       ,l.grupo_lancamento_pk";
                $sql.="       ,l.tipo_grupo_lancamento_pk";
                $sql.="       ,top.ds_tipo_operacao";
                $sql.="       ,cb.ds_conta_bancaria ";
                $sql.="  FROM lancamentos_financeiros l";
                $sql.="  left join tipos_operacao top on l.tipos_operacao_pk = top.pk";
                $sql.="  left join contas_bancarias cb on l.contas_bancarias_pk = cb.pk";
                $sql.="  left join metodos_pagamento mp on l.metodos_pagamento_pk = mp.pk";
                $sql.="  left join contas co on l.empresa_lancamento_pk = co.pk";
                $sql.="  inner join usuarios u on l.usuario_cadastro_pk = u.pk";
                $sql.=" where 1=1 ";
                if($tipo_lancamento_pk == 2){
                    $sql.=" and l.tipo_lancamento_pk > 1";
                }
                if($empresas_pk != ""){
                    $sql.=" and l.empresa_lancamento_pk = ".$empresas_pk;
                }
                if($tipo_grupo_pk != ""){
                    $sql.=" and l.tipo_grupo_lancamento_pk = ".$tipo_grupo_pk;
                }
                if($grupo_leancamento_pk != ""){
                    $sql.=" and l.grupo_lancamento_pk = ".$grupo_leancamento_pk;
                }
                if($contas_bancarias_pk != ""){
                    $sql.=" and l.contas_bancarias_pk = ".$contas_bancarias_pk;
                }
                if(!empty($tipos_operacao_pk_receita)){
                    $sql.=" and l.tipos_operacao_pk=".$tipos_operacao_pk_receita;
                }
                
                $sql.=" and l.dt_vencimento between '".($query[$i]['dt_vencimento'])."' and '".($query[$i]['dt_vencimento'])."'";
                

                if(!empty($plano_contas)){
                    $sql.=" and top.pk=".$plano_contas;
                }

                //$sql.=" and l.ic_status_lancamento = 1";

                $sql.=" group by l.pk";
                $sql.=" order by l.dt_vencimento asc ";

           
            
                

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                $query1 = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                if(count($query1) > 0){
                    
                    for($l = 0; $l < count($query1); $l++){
                       
                        if($query1[$l]['tipo_grupo_lancamento_pk']!=""){
                            if($query1[$l]['tipo_grupo_lancamento_pk']==1){
                                $queryLead = $this->listaItensGrupoLeads($query1[$l]['grupo_lancamento_pk']);
                                if(isset($queryLead->data[0]['ds_lead'])){
                                    $ds_recebido_de = $queryLead->data[0]['ds_lead'];
                                }
                            }else if($query1[$l]['tipo_grupo_lancamento_pk']==2){
                                $queryLead = $this->listaItensGrupoColaboradores($query1[$l]['grupo_lancamento_pk']);
                                if(isset($queryLead->data[0]['ds_colaborador'])){
                                    $ds_recebido_de = $queryLead->data[0]['ds_colaborador'];
                                }
                            }else if($query1[$l]['tipo_grupo_lancamento_pk']==3){
                                $queryLead = $this->listaItensGrupoFornecedores($query1[$l]['grupo_lancamento_pk']);
                                if(isset($queryLead->data[0]['ds_fornecedor'])){
                                    $ds_recebido_de = $queryLead->data[0]['ds_fornecedor'];
                                }
                            }
                        }


                        $mysql_data[] = array(
                            "pk" => $query1[$l]["pk"],
                            "ds_recebido" => $ds_recebido_de,
                            "ds_tipo_grupo" => $query1[$l]['ds_tipo_grupo'],
                            "ds_plano_contas" => $query1[$l]['ds_tipo_operacao'],
                            "vl_despesa" => "R$ ". number_format((($query1[$l]['vl_lancamento']) ),2,",","."),
                        );

                    }
                    
                }

                $arrDados[] = array(
                    "dt_vencimento" => $query[$i]["dt_vencimento_format"],
                    "vl_total_despesa_data" => "Total: R$ ".number_format((($query[$i]["vl_total_despesa_periodo"]) ),2,",","."),
                    "vl_total_despesa" => $query[$i]["vl_total_despesa_periodo"],
                    "arrDados" => $mysql_data,
                );
            }
            
        }


        return $arrDados;

    }

}
