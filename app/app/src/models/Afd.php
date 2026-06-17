<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;
use Throwable;

class Afd {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function excluir($pk){
        Util::execDelete('beneficios', ' pk='.$pk, $this->pdo);
    }

    public function salvar($afd){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        try{
            $fields = array();
            $fields['contas_pk'] = $afd['contas_pk'];
            $fields['leads_pk'] = $afd['leads_pk'];
            $fields['colaborador_pk'] = $afd['colaborador_pk'];
            $fields['doc'] = $afd['doc'];
            $fields['dt_ini'] = Util::DataYMD($afd['dt_periodo_ini']);
            $fields['dt_fim'] = Util::DataYMD($afd['dt_periodo_fim']);
        
            $fields["dt_ult_atualizacao"] = "sysdate()";
            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];


            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];


            $pk = Util::execInsert("afd", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;

            return $retorno;
        }
        catch(Throwable $e){
            print_r($e->getMessage());
            die();
        }

    }


    public function listarGrid(){
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
        $sql.="select a.pk , date_format(a.dt_cadastro,'%d/%m/%Y')dt_cadastro, a.usuario_cadastro_pk, a.dt_ult_atualizacao, a.usuario_ult_atualizacao_pk ";
        $sql.="       ,co.ds_razao_social ";
        $sql.="       ,c.ds_colaborador ";
        $sql.="       ,l.ds_lead ";
        $sql.="       ,date_format(a.dt_ini,'%d/%m/%Y')dt_ini";
        $sql.="       ,date_format(a.dt_fim,'%d/%m/%Y')dt_fim";
        $sql.="       ,a.doc ";
        $sql.="  from afd a ";
        $sql.="  inner join contas co on a.contas_pk = co.pk";
        $sql.="  inner join leads l on a.leads_pk = l.pk";
        $sql.="  inner join colaboradores c on a.colaborador_pk = c.pk";

        $sql.=" where 1=1 ";
        $sql.=" order by pk asc ";
      

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

    public function listarPorPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio


        $sql ="";
        $sql.="select pk,doc";

        $sql.="  from afd ";
        if($pk!=""){
            $sql.=" where pk = $pk ";
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
