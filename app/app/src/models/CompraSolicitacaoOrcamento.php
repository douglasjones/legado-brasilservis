<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class CompraSolicitacaoOrcamento {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    function excluir($pk){
        Util::execDelete('compras_solicitacao_orcamentos', ' pk='.$pk, $this->pdo);
    }

    public function vinculaSolicitacaoOrcamento($pk,$compras_solicitacao_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['compra_solicitacao_pk'] = $compras_solicitacao_pk;
   
        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        Util::execUpdate("compras_solicitacao_orcamentos", $fields, " pk = ".$pk,$this->pdo);
        $retorno->status = true;
        $retorno->message = 'Dados atualizado com sucesso';
        $retorno->data = $pk;
        
        return $retorno;

    }

    public function listarGrid($compra_solicitacao_pk){
        
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = true; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $retorno->iTotalDisplayRecords = 0;
        $retorno->iTotalRecords = 0;
       
        //PAGINAÇÃO
        if($compra_solicitacao_pk!=""){
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
            $sql.=" SELECT cso.pk t_compras_solicitacao_orcamentos_pk,";
            $sql.=" f.ds_fornecedor t_ds_fornecedor,";
            $sql.=" cso.fornecedor_pk t_fornecedor_pk,";
            $sql.=" date_format(cso.dt_pevisao_entrega, '%d/%m/%Y') t_dt_pevisao_entrega,";
            $sql.=" cso.vl_frete t_vl_frete,";
            $sql.=" cso.vl_total t_vl_total,";
            $sql.=" cso.ic_status t_ic_status,";
            $sql.=" CASE ";
            $sql.="    WHEN cso.ic_status = 1 THEN 'Em analise'";
            $sql.="    WHEN cso.ic_status = 2 THEN 'Aprovado' ";
            $sql.="    WHEN cso.ic_status = 3 THEN 'Reprovado' ";
            $sql.=" END t_ds_status, ";
            $sql.=" cso.compra_solicitacao_pk ";
            $sql.=" FROM compras_solicitacao_orcamentos cso ";
            $sql.="      LEFT JOIN fornecedor f ON cso.fornecedor_pk = f.pk ";
            if($compra_solicitacao_pk!=""){
                $sql.=" WHERE cso.compra_solicitacao_pk =".$compra_solicitacao_pk;
            }
            else{
                $sql.=" WHERE cso.compra_solicitacao_pk is null";
            }
            $sql.=" order by cso.pk desc ";


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
        }


        echo json_encode($retorno);
        exit(0);
    }

    public function salvar($compras_solicitacao_orcamentos){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['fornecedor_pk'] = $compras_solicitacao_orcamentos['fornecedor_pk'];
        $fields['dt_pevisao_entrega'] = Util::DataYMD($compras_solicitacao_orcamentos['dt_pevisao_entrega']);
        $fields['vl_frete'] = $compras_solicitacao_orcamentos['vl_frete'];
        $fields['vl_total'] = $compras_solicitacao_orcamentos['vl_total'];
        $fields['obs_orcamento'] = $compras_solicitacao_orcamentos['obs_orcamento'];
        $fields['ic_status'] = $compras_solicitacao_orcamentos['ic_status'];
        $fields['compra_solicitacao_pk'] = $compras_solicitacao_orcamentos['compra_solicitacao_pk'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($compras_solicitacao_orcamentos['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("compras_solicitacao_orcamentos", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("compras_solicitacao_orcamentos", $fields, " pk = ".$compras_solicitacao_orcamentos['pk'],$this->pdo);
            $pk = $compras_solicitacao_orcamentos['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }

        $compra_solicitacao_pk = $compras_solicitacao_orcamentos['compra_solicitacao_pk'];
        $ic_status = $compras_solicitacao_orcamentos['ic_status'];
        $vl_total = ($compras_solicitacao_orcamentos['vl_total']);
        $vl_frete = ($compras_solicitacao_orcamentos['vl_frete']);
        $dt_previsao_entrega = $compras_solicitacao_orcamentos['dt_pevisao_entrega'];
        $fornecedor_pk = $compras_solicitacao_orcamentos['fornecedor_pk'];

        $sql = '';
        $sql .= 'select  grupo_lancamento_centrocusto_pk';
        $sql .= '       ,tipo_grupo_centro_custo_pk ';
        $sql .= '       ,empresas_pk ';
        $sql .= '  from compras_solicitacao';
        $sql .= '  where pk ='. $compra_solicitacao_pk;
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if($ic_status == 2){

            $fieldsCompraSolicicacao = array();
            $fieldsCompraSolicicacao['usuario_aprovacao_pk'] = $_SESSION['session_user']['par1'];
            $fieldsCompraSolicicacao['dt_aprovacao'] = "sysdate()";

            Util::execUpdate("compras_solicitacao", $fieldsCompraSolicicacao, " pk = ".$compra_solicitacao_pk,$this->pdo);

            $compra = [
                "pk"=>"",
                "fornecedor_pk"=>$fornecedor_pk,
                "categoria_pk"=>"",
                "conta_pk"=>$query[0]['empresas_pk'],
                "metodos_pagamento_pk"=>"",
                "qtde_parcelas"=>"",
                "ds_numero_nota"=>"",
                "centro_custo_pk"=>"",
                "vl_pagamento"=>$vl_total + $vl_frete,
                "vl_notafiscal"=>$vl_total,
                "vl_frete"=>$vl_frete,
                "dt_pagamento"=>"",
                "dt_entrega"=>($dt_previsao_entrega),
                "grupo_lancamento_centro_custo_pk"=>$query[0]['grupo_lancamento_centrocusto_pk'],
                "compra_solicitacao_pk"=>$compra_solicitacao_pk,
                "ic_status"=>2,
            ];

            (new Compra($this->pdo))->salvar($compra);
        }

        if($ic_status==3){
            $fieldsReprovado = array();
            $fieldsReprovado['usuario_ult_atualizacao_pk'] = $_SESSION['session_user']['par1'];
            $fieldsReprovado['dt_ult_atualizacao'] = "sysdate()";
            $fieldsReprovado['ic_status'] = 3;

            Util::execUpdate("compras_solicitacao", $fieldsReprovado, " pk = ".$compra_solicitacao_pk,$this->pdo);
        }
        return $retorno;
    }

    public function listarPk($pk){

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql.="       ,fornecedor_pk ";
        $sql.="       ,date_format(dt_pevisao_entrega,'%d/%m/%Y')dt_pevisao_entrega ";
        $sql.="       ,vl_frete ";
        $sql.="       ,vl_total ";
        $sql.="       ,obs_orcamento ";
        $sql.="       ,ic_status ";
        $sql.="       ,compra_solicitacao_pk ";

        $sql.="  from compras_solicitacao_orcamentos ";
        $sql.=" where pk = $pk ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true; //Retorno setado status como false
        $retorno->data = $rows; //Retorno data setado como vazio
        $retorno->message = "Dados Carregados com sucesso!"; //Retorno data setado como vazio

        return $retorno;

    }

}
