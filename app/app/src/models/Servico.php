<?php

namespace App\Model;

use App\Utils\Session;
use App\Utils\Util;
use App\Utils\Validation;

class Servico {

	public $pdo;

	public function __construct($pdo) {
		$this->pdo = $pdo;
	}

    public function excluir($pk){
        
        Util::execDelete('colaboradores_produtos_servicos', ' produtos_servicos_pk='.$pk, $this->pdo);
        Util::execDelete('contratos_itens', ' produtos_servicos_pk='.$pk, $this->pdo);
        Util::execDelete('produtos_servicos', ' pk='.$pk, $this->pdo);
    }

    public function listarGrid($ds_produto, $ic_status){
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
                            pk LIKE '%".$pesq."%' OR
                            ds_produto_servico LIKE '%".$pesq."%' OR
                            ds_cbo LIKE '%".$pesq."%' 
                        )";
        }
        

        $sql ="";
        $sql.="select pk t_pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_produto_servico t_ds_produto_servico";
        $sql.="       ,ds_cbo t_ds_cbo";
        $sql.="       ,CASE ic_status when 1 then 'Ativo' when 2 then 'Inativo' end t_ds_status";
        $sql.="  from produtos_servicos ";
        $sql.=" where 1=1 ";
        $sql.=$search;
        if($ds_produto != ""){
            $sql.=" and ds_produto_servico like '%".$ds_produto."%' ";
        }
         if($ic_status != ""){
            $sql.=" and ic_status =".$ic_status;
        }
        $sql.=" order by ds_produto_servico asc ";
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

    public function salvar($servico){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_produto_servico'] = $servico['ds_produto_servico'];
        $fields['ds_cbo'] = $servico['ds_cbo'];
        $fields['ic_status'] = $servico['ic_status'];
        $fields['vl_servico'] = $servico['vl_servico'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
        
        if($servico['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("produtos_servicos", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("produtos_servicos", $fields, " pk = ".$servico['pk'],$this->pdo);
            $pk = $servico['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;

    }

    public function listarPorPk($pk) {
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql.="       ,ds_produto_servico ";
        $sql.="       ,ds_cbo ";
        $sql.="       ,vl_servico ";
        $sql.="  from produtos_servicos ";
        $sql.=" where pk = $pk ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

}
