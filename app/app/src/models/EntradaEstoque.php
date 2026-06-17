<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class EntradaEstoque {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function excluir($pk){
        Util::execDelete('entrada_estoque', ' pk='.$pk, $this->pdo);
    }

    public function listarGrid($ds_produto,$ic_status){
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
                            p.ds_produto LIKE '%".$pesq."%'
                        )";
        }
        
        $sql ="";
        $sql.="select ee.pk t_pk";
        $sql.="       ,date_format(ee.dt_cadastro,'%d/%m/%Y') t_dt_cadastro ";
        $sql.="       ,ee.usuario_cadastro_pk, ee.dt_ult_atualizacao, ee.usuario_ult_atualizacao_pk ";
        $sql.="       ,ee.ds_n_ordem t_ds_n_ordem";
        $sql.="       ,ee.obs_entrada_estoque t_obs_entrada_estoque";
        $sql.="       ,ee.fornecedor_pk ";
        $sql.="       ,ee.produtos_pk ";
        $sql.="       ,ee.qtde t_qtde";
        $sql.="       ,ee.vl_unitario";
        $sql.="       ,f.ds_fornecedor  t_ds_fornecedor";
        $sql.="       ,p.ds_produto  t_ds_produto";
        $sql.="  from entrada_estoque ee";
        $sql.="  left join fornecedor f on ee.fornecedor_pk  = f.pk ";
        $sql.="  left join produtos p on ee.produtos_pk = p.pk ";
        $sql.=" where 1=1 ";
        if($ds_produto != ""){
            $sql.=" and p.ds_produto like '%".$ds_produto."%' ";
        }
        if($ic_status != ""){
            $sql.=" and p.ic_status = ".$ic_status;
        }
        $sql.= $search;
        $sql.=" order by ee.ds_n_ordem asc ";  
        

        $stmt = $this->pdo->prepare( $sql.$lengthSql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmtCount = $this->pdo->prepare( $sql );
        $stmtCount->execute();
        $rowsCount = $stmtCount->fetchAll(\PDO::FETCH_ASSOC);
        
        for($i = 0; $i<count($rows);$i++){
            $sql ="";
            $sql.="select sum(me.qtde) qtde from movimentacao_estoque me 
            inner join produtos_itens pi on me.produtos_itens_pk = pi.pk
            inner join produtos p on pi.produtos_pk = p.pk 
            where p.pk =  ".$rows[$i]['produtos_pk'];
            
            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $rowsMovi = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $rows[$i]['t_qtde'] = $rows[$i]['t_qtde'] - intval($rowsMovi[0]['qtde']);
            if(intval($rows[$i]['t_qtde'])< 0 ){
                $rows[$i]['t_qtde'] = 0;
            }
        }

        

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        $retorno->iTotalDisplayRecords = count($rowsCount);
        $retorno->iTotalRecords = count($rowsCount);
        
        echo json_encode($retorno);
        exit(0);
    }

    public function salvar($entrada_estoque){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_n_ordem'] = $entrada_estoque['ds_n_ordem'];
        $fields['obs_entrada_estoque'] = $entrada_estoque['obs_entrada_estoque'];
        $fields['fornecedor_pk'] = $entrada_estoque['fornecedor_pk'];
        $fields['produtos_pk'] = $entrada_estoque['produtos_pk'];
        $fields['qtde'] = $entrada_estoque['qtde'];
        $valor = str_replace (".", "", $entrada_estoque['vl_unitario']);
        $valor = str_replace (",", ".", $valor);
        $fields['vl_unitario'] = $valor;
        

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($entrada_estoque['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("entrada_estoque", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("entrada_estoque", $fields, " pk = ".$entrada_estoque['pk'],$this->pdo);
            $pk = $entrada_estoque['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;
    }

    public function listarPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select ee.pk, ee.dt_cadastro, ee.usuario_cadastro_pk, ee.dt_ult_atualizacao, ee.usuario_ult_atualizacao_pk  ";
        $sql.="       ,ee.ds_n_ordem ";
        $sql.="       ,ee.obs_entrada_estoque ";
        $sql.="       ,ee.fornecedor_pk ";
        $sql.="       ,ee.produtos_pk ";
        $sql.="       ,ee.qtde ";
        $sql.="       ,ee.vl_unitario";
        $sql.="       ,p.categorias_produto_pk";
        $sql.="  from entrada_estoque ee";
        $sql.="       inner join produtos p on ee.produtos_pk = p.pk";
        $sql.=" where ee.pk = $pk ";
       
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }

   }
