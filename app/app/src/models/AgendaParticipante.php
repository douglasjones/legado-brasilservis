<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class AgendaParticipante {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function excluir($pk){
        Util::execDelete('agendas_participantes'," pk = ".$pk,$this->pdo);
    }

    public function salvar($agenda_participante){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio



        $fields = array();
        $fields['ic_tipo_participante'] = $agenda_participante['ic_tipo_participante'];
        $fields['participante_pk'] = $agenda_participante['participante_pk'];
        $fields['ds_email'] = $agenda_participante['ds_email'];
        $fields['ds_cel'] = $agenda_participante['ds_cel'];
        $fields['agendas_pk'] = $agenda_participante['agendas_pk'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];


        if($agenda_participante['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   =  $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("agendas_participantes", $fields,$this->pdo);

            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("agendas_participantes", $fields, " pk = ".$agenda_participante['pk'],$this->pdo);
            $pk = $agenda_participante['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }

        return $retorno;

    }

    public function carregarParicipantes($ic_tipo_participante, $leads_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql = "";
        if($ic_tipo_participante == 1){
            $sql.= "SELECT pk participante_pk, ds_contato ds_participante, ds_email, ds_cel";
            $sql.= "  FROM contatos";
            $sql.= " where leads_pk =".$leads_pk;


        }else if($ic_tipo_participante == 2){
            $sql.= "SELECT pk participante_pk, ds_usuario ds_participante, ds_email, ds_cel";
            $sql.= "  FROM usuarios";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;

    }

    public function listar_por_participante_pk($ic_tipo_participante, $participante_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql = "";
        if($ic_tipo_participante == 1){
            $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
            $sql.="       ,ds_contato ds_participante";
            $sql.="       ,ds_email ";
            $sql.="       ,ds_cel ";
            $sql.="       ,leads_pk";

            $sql.="  from contatos ";
            $sql.=" where 1=1 ";
            if($participante_pk!=""){
                $sql.=" and pk = ".$participante_pk;
            }


        }else if($ic_tipo_participante == 2){
            $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
            $sql.="       ,ds_usuario ds_participante ";
            $sql.="       ,ds_login ";
            $sql.="       ,ds_senha ";
            $sql.="       ,ds_email ";
            $sql.="       ,ds_cel ";
            $sql.="       ,ic_status ";
            $sql.="       ,grupos_pk ";
            $sql.="       ,leads_pk";

            $sql.="  from usuarios ";
            $sql.=" where 1=1 ";
            if($participante_pk!=""){
                $sql.=" and pk = ".$participante_pk;
            }
            $sql.=" order by ds_usuario asc ";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;

    }

    public function listar_por_agendas_pk($agendas_pk){
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


        if($agendas_pk != ""){
            $sql ="";
            $sql.="select ap.pk as t_pk, ap.dt_cadastro, ap.usuario_cadastro_pk, ap.dt_ult_atualizacao, ap.usuario_ult_atualizacao_pk ";
            $sql.="       ,ap.ic_tipo_participante  t_ic_tipo_participante";
            $sql.="       ,ap.participante_pk  t_participante_pk";
            $sql.="       ,ap.ds_email  t_ds_email";
            $sql.="       ,ap.ds_cel  t_ds_cel";
            $sql.="       ,ap.agendas_pk  t_agendas_pk";
            $sql.="       ,case when ap.ic_tipo_participante = 1 then ds_contato ";
            $sql.="             when ap.ic_tipo_participante = 2 then ds_usuario ";
            $sql.="             end t_ds_participante ";
            $sql.="  from agendas_participantes ap";
            $sql.="  left join usuarios u on ap.participante_pk = u.pk ";
            $sql.="  left join contatos c on ap.participante_pk = c.pk ";
            $sql.=" where 1=1 ";
            $sql.=" and ap.agendas_pk = ".$agendas_pk;
            $sql.=" order by ap.ic_tipo_participante asc ";

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
        }else{
            $retorno->status = true;
            $retorno->message = 'Dados carregados com sucesso';
            $retorno->data = [];
            $retorno->iTotalDisplayRecords = 0;
            $retorno->iTotalRecords = 0;
        }

        echo json_encode($retorno);
        exit(0);
    }


}
