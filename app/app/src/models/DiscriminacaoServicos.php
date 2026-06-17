<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class DiscriminacaoServicos{

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function excluir($pk){
        Util::execDelete('nfse_discriminacao_servico', ' pk='.$pk, $this->pdo);
    }

    public function listarGrid($ds_discriminacao_servico){
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
        $sql.="select pk t_pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_discriminacao_servico t_ds_discriminacao_servico";
        $sql.="       ,ic_status t_ic_status";
        $sql.="       ,case ic_status when 1 then 'Ativo' when 2 then 'Inativo' end t_ds_status";

        $sql.="  from nfse_discriminacao_servico ";
        $sql.=" where 1=1 ";
        if($ds_discriminacao_servico != ""){
            $sql.=" and ds_discriminacao_servico like '%".$ds_discriminacao_servico."%' ";
        }
        $sql.=" order by ds_discriminacao_servico asc ";

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

    public function salvar($discriminacao_servico){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        
        $fields = array();
        $fields['ds_discriminacao_servico'] = $discriminacao_servico['ds_discriminacao_servico'];
        $fields['ic_status'] = $discriminacao_servico['ic_status'];
        
        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($discriminacao_servico['pk']  == ""){
            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("nfse_discriminacao_servico", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("nfse_discriminacao_servico", $fields, " pk = ".$discriminacao_servico['pk'],$this->pdo);
            $pk = $discriminacao_servico['pk'];
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
        $sql.="       ,ds_discriminacao_servico ";
        $sql.="       ,ic_status ";

        $sql.="  from nfse_discriminacao_servico ";
        $sql.=" where pk = $pk ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $rows[0]['ds_discriminacao_servico'] = str_replace("||", "\n", $rows[0]['ds_discriminacao_servico']);
        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function listarDiscriminacao(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";
        $sql.="select pk ";
        $sql.="       ,ds_discriminacao_servico ";
        $sql.="  from nfse_discriminacao_servico ";
        $sql.=" where ic_status = 1 ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

}
