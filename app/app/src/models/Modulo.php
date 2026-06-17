<?php

namespace App\Model;

use App\Utils\Session;
use App\Utils\Util;
use App\Utils\Validation;

class Modulo {

	public $pdo;

	public function __construct($pdo) {
		$this->pdo = $pdo;
	}

    public function listarTodos($ds_tipo_modulo){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio


        $sql ="";
        $sql.="select m.pk ";
        $sql.="       ,m.ds_dominio";
        $sql.="       ,m.tipo_modulo_pk";
        $sql.="       ,tm.ds_tipo_modulo";
        $sql.="       ,m.ds_modulo";
        $sql.="  from modulos m ";
        $sql.="  left join tipos_modulos tm ON tm.pk = m.tipo_modulo_pk";
        if($ds_tipo_modulo != ""){
            $sql.=" and tm.ds_tipo_modulo like '%".$ds_tipo_modulo."%' ";
        }
        $sql.=" where 1=1 ";
        $sql.=" order by m.ds_modulo asc ";


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function salvar($lead){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['tipo_modulo_pk'] = $lead['tipo_modulo_pk'];
        $fields['ds_dominio'] = $lead['ds_dominio'];
        $fields['ds_modulo'] = $lead['ds_modulo'];
        $fields['ds_obs'] = $lead['ds_obs'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($lead['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   =  $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("modulos", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("modulos", $fields, " pk = ".$lead['pk'],$this->pdo);
            $pk = $lead['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;

    }
    public function excluir($pk){
        Util::execDelete('modulos', ' pk='.$pk, $this->pdo);
    }

    public function listarGrid($tipo_modulo_pk){
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
                             tm.ds_tipo_modulo like '%".$pesq."%'
                        )";
        }

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_modulo ";
        $sql.="       ,ds_dominio ";
        $sql.="  from modulos ";
        $sql.=" where 1=1 ";
        if($tipo_modulo_pk != ""){
            $sql.=" and tipo_modulo_pk = ".$tipo_modulo_pk;
        }
        $sql.=" order by ds_modulo asc ";


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
    public function listarTipoModulo($ds_tipo_modulo){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk ";
        $sql.="       ,ds_tipo_modulo ";
        $sql.="  from tipos_modulos ";
        if($ds_tipo_modulo != ""){
            $sql.=" and ds_tipo_modulo like '%".$ds_tipo_modulo."%' ";
        }
        $sql.=" order by ds_tipo_modulo asc ";

        


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function listarPorPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select m.pk ";
        $sql.="       ,m.ds_dominio";
        $sql.="       ,m.tipo_modulo_pk";
        $sql.="       ,tm.ds_tipo_modulo";
        $sql.="       ,m.ds_obs";
        $sql.="  from modulos m ";
        $sql.="  left join tipos_modulos tm ON tm.pk = m.tipo_modulo_pk";
        $sql.=" where 1=1 ";
        if($pk != ""){
            $sql.=" and m.pk = ".$pk;
        }
        

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
}
