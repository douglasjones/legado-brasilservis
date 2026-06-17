<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class CompraSolicitacaoOrcamentoItem {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listarItensOrcamentoPk($compras_solicitacao_orcamentos_pk){
        
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
        $sql.="select cs.pk, cs.dt_cadastro, cs.usuario_cadastro_pk, cs.dt_ult_atualizacao, cs.usuario_ult_atualizacao_pk  ";
        $sql.="       ,cs.categorias_produto_pk ";
        $sql.="       ,cp.ds_categoria "; 
        $sql.="       ,cs.produtos_pk ";
        $sql.="       ,p.ds_produto ds_produto_itens";
        $sql.="       ,cs.ds_produto ";
        $sql.="       ,cs.qtde_produto ";
        $sql.="       ,cs.vl_unitario ";
        $sql.="       ,cs.compras_solicitacao_orcamentos_pk ";
        $sql.="  from compras_solicitacao_orcamento_itens cs "; 
        $sql.="  inner join categorias_produto cp on cs.categorias_produto_pk = cp.pk";
        $sql.="  inner join produtos p on cs.ds_produto = p.pk";
        if($compras_solicitacao_orcamentos_pk!=""){
            $sql.=" WHERE cs.compras_solicitacao_orcamentos_pk =".$compras_solicitacao_orcamentos_pk;
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

    public function excluirPorSolicitacaoOrcamento($pk){
        Util::execDelete('compras_solicitacao_orcamento_itens', ' compras_solicitacao_orcamentos_pk='.$pk, $this->pdo);
    }
    public function excluir($pk){
        Util::execDelete('compras_solicitacao_orcamento_itens', ' pk='.$pk, $this->pdo);
    }
    public function salvar($compras_solicitacao_orcamento_itens){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        
        $fields = array();
        $fields['categorias_produto_pk'] = $compras_solicitacao_orcamento_itens['categorias_produto_pk'];
        $fields['produtos_pk'] = $compras_solicitacao_orcamento_itens['produtos_pk'];
        $fields['ds_produto'] = $compras_solicitacao_orcamento_itens['ds_produto'];
        $fields['qtde_produto'] = $compras_solicitacao_orcamento_itens['qtde_produto'];
        $fields['vl_unitario'] = $compras_solicitacao_orcamento_itens['vl_unitario'];
        $fields['compras_solicitacao_orcamentos_pk'] = $compras_solicitacao_orcamento_itens['compras_solicitacao_orcamentos_pk'];
        
        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($compras_solicitacao_orcamento_itens['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];


            $pk = Util::execInsert("compras_solicitacao_orcamento_itens", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("compras_solicitacao_orcamento_itens", $fields, " pk = ".$compras_solicitacao_orcamento_itens['pk'],$this->pdo);
            $pk = $compras_solicitacao_orcamento_itens['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }


        $sql = '';
        $sql .= 'Select c.pk compras_pk from compras_solicitacao_orcamentos cso';
        $sql .= ' inner join compras c on c.compra_solicitacao_pk = cso.compra_solicitacao_pk ';
        $sql .= ' where cso.pk ='.$compras_solicitacao_orcamento_itens['compras_solicitacao_orcamentos_pk'];
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if(!empty($query)){
            $compras_pk = $query[0]['compras_pk'];

            $produtos_pk = $compras_solicitacao_orcamento_itens['produtos_pk'];
            $qtde = $compras_solicitacao_orcamento_itens['qtde_produto'];
            $vl_item = $compras_solicitacao_orcamento_itens['vl_unitario'];

            if($compras_pk > 0){
                (new Compra($this->pdo))->salvarProduto('', $compras_pk, $produtos_pk, $qtde, $vl_item, '','');
            }
        }

        return $retorno;

    }


   }
