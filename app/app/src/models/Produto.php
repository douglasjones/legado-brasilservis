<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class Produto {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function excluir($pk){
        Util::execDelete('produtos_itens', ' produtos_pk='.$pk, $this->pdo);
        Util::execDelete('produtos', ' pk='.$pk, $this->pdo);
    }

    public function listar_por_categorias($categorias_produto_pk){


        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select p.pk, p.dt_cadastro, p.usuario_cadastro_pk, p.dt_ult_atualizacao, p.usuario_ult_atualizacao_pk ";
        $sql.="       ,p.ds_produto ";
        $sql.="       ,p.obs ";
        $sql.="       ,p.ic_status ";
        $sql.="       ,CASE  WHEN p.ic_status=1 THEN 'Ativo' ELSE 'Desativado' END ds_status  ";
        $sql.="       ,p.categorias_produto_pk ";
        $sql.="       ,p.tipo_unidade_pk ";
        $sql.="       ,p.ic_tempo_troca";
        $sql.="       ,p.qtde_minima";
        $sql.="       ,cp.ds_categoria ";
        $sql.="  from produtos p ";
        $sql.="  left join categorias_produto cp on p.categorias_produto_pk = cp.pk";
        $sql.=" where 1=1 ";
        if($categorias_produto_pk != ""){
            $sql.=" and p.categorias_produto_pk =".$categorias_produto_pk;
        }
        $sql.=" order by p.ds_produto asc ";



        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
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
        $sql.="select p.pk t_pk, p.dt_cadastro, p.usuario_cadastro_pk, p.dt_ult_atualizacao, p.usuario_ult_atualizacao_pk ";
        $sql.="       ,p.ds_produto t_ds_produto";
        $sql.="       ,p.obs t_obs";
        $sql.="       ,p.ic_status t_ic_status";
        $sql.="       ,CASE  WHEN p.ic_status=1 THEN 'Ativo' ELSE 'Desativado' END t_ds_status  ";
        $sql.="       ,p.categorias_produto_pk ";
        $sql.="       ,p.tipo_unidade_pk t_tipo_unidade_pk";
        $sql.="       ,p.ic_tempo_troca t_ic_tempo_troca";
        $sql.="       ,p.qtde_minima t_qtde_minima";
        $sql.="       ,cp.ds_categoria t_ds_categoria";
        $sql.="  from produtos p ";
        $sql.="  left join categorias_produto cp on p.categorias_produto_pk = cp.pk";
        $sql.=" where 1=1 ";
        if($ds_produto != ""){
            $sql.=" and p.ds_produto like '%".$ds_produto."%' ";
        }
        if($ic_status != ""){
            $sql.=" and p.ic_status =" .$ic_status;
        }
        $sql.=$search;

        $sql.=" order by p.ds_produto asc ";

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

    public function salvar($produto){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_produto'] = $produto['ds_produto'];
        $fields['obs'] = $produto['obs'];
        $fields['ic_status'] = $produto['ic_status'];
        $fields['categorias_produto_pk'] = $produto['categorias_produto_pk'];
        $fields['tipo_unidade_pk'] = $produto['tipo_unidade_pk'];
        $fields['ic_tempo_troca'] = $produto['ic_tempo_troca'];
        $fields['qtde_minima'] = $produto['qtde_minima'];
        
        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($produto['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];


            $pk = Util::execInsert("produtos", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("produtos", $fields, " pk = ".$produto['pk'],$this->pdo);
            $pk = $produto['pk'];
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
        $sql.="       ,ds_produto ";
        $sql.="       ,obs ";
        $sql.="       ,ic_status ";
        $sql.="       ,categorias_produto_pk ";
        $sql.="       ,tipo_unidade_pk ";
        $sql.="       ,ic_tempo_troca";
        $sql.="       ,qtde_minima";

        $sql.="  from produtos ";
        $sql.=" where pk = $pk ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function verificarProdutoPorNomeXML($ds_produto) {
        $retorno = new \StdClass;
        $retorno->status = false;
        $retorno->data = [];

        // 🔎 Verifica se já existe o produto pelo nome
        $sql = "SELECT pk 
                FROM produtos 
                WHERE ds_produto = :ds_produto 
                AND ic_status = 1 
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':ds_produto', $ds_produto);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row) {
            $pk = $row['pk'];
        } else {
            // Se não encontrou, insere o novo produto
            $fields = array();
            $fields["dt_ult_atualizacao"] = "sysdate()";
            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
            $fields['ds_produto']            = $ds_produto;
            $fields['ic_status']             = 1;
            $fields['categorias_produto_pk'] = 99;

            $pk = Util::execInsert("produtos", $fields, $this->pdo);
        }

        return $pk;
    }

    public function listarTodosComTempoTroca($ds_produto){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select p.pk, p.dt_cadastro, p.usuario_cadastro_pk, p.dt_ult_atualizacao, p.usuario_ult_atualizacao_pk ";
        $sql.="       ,p.ds_produto ";
        $sql.="       ,p.obs ";
        $sql.="       ,p.ic_status ";
        $sql.="       ,CASE  WHEN p.ic_status=1 THEN 'Ativo' ELSE 'Desativado' END ds_status  ";
        $sql.="       ,p.categorias_produto_pk ";
        $sql.="       ,p.tipo_unidade_pk ";
        $sql.="       ,p.ic_tempo_troca";
        $sql.="       ,p.qtde_minima";
        $sql.="       ,cp.ds_categoria ";
        $sql.="  from produtos p ";
        $sql.="  left join categorias_produto cp on p.categorias_produto_pk = cp.pk";
        $sql.=" where 1=1 ";
        if($ds_produto != ""){
            $sql.=" and p.ds_produto like '%".$ds_produto."%' ";
        }
        $sql.=" and p.ic_tempo_troca is not null";
        $sql.=" order by p.ds_produto asc ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

}
