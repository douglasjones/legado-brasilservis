<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class Agenda {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function excluir($pk){
        Util::execDelete('agendas'," pk = ".$pk,$this->pdo);
    }
    public function salvar($agenda){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $ds_cep = "";
        $ds_endereco = "";
        $ds_complemento = "";
        $ds_numero = "";
        $ds_bairro = "";
        $ds_cidade = "";
        $ds_uf = "";


        if($agenda['ds_endereco'] != 'undefined'){
            $ds_cep = $agenda['ds_cep'];
            $ds_endereco = $agenda['ds_endereco'];
            $ds_complemento = $agenda['ds_complemento'];
            $ds_numero = $agenda['ds_numero'];
            $ds_bairro = $agenda['ds_bairro'];
            $ds_cidade = $agenda['ds_cidade'];
            $ds_uf = $agenda['ds_uf'];
        }else{
            if($agenda['leads_pk'] != '' && ($agenda['tipo_agendas_pk'] == 1 || $agenda['tipo_agendas_pk'] == 5)){
                $sql ="";
                $sql.="select pk";
                $sql.="       ,ds_cep ";
                $sql.="       ,ds_endereco ";
                $sql.="       ,ds_numero ";
                $sql.="       ,ds_complemento ";
                $sql.="       ,ds_bairro ";
                $sql.="       ,ds_cidade ";
                $sql.="       ,ds_uf ";
                $sql.="  from leads ";
                $sql.="  where pk =".$agenda['leads_pk'];
                $stmt = $this->pdo->prepare( $sql );
                $stmt->execute();
                $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $ds_cep = $query[0]['ds_cep'];
                $ds_endereco = $query[0]['ds_endereco'];
                $ds_complemento = $query[0]['ds_complemento'];
                $ds_numero = $query[0]['ds_numero'];
                $ds_bairro = $query[0]['ds_bairro'];
                $ds_cidade = $query[0]['ds_cidade'];
                $ds_uf = $query[0]['ds_uf'];
            }
        }

        $fields = array();
        $fields['tipo_agendas_pk'] = $agenda['tipo_agendas_pk'];
        $fields['dt_ini_agenda_ini'] = $agenda['dt_ini_agenda_ini'];
        $fields['dt_hr_agenda_fim'] = $agenda['dt_hr_agenda_fim'];
        if($agenda['ic_lembrete'] != 'undefined'){
            $fields['ic_lembrete'] = $agenda['ic_lembrete'];
        }
        if($agenda['ic_repetir'] != 'undefined'){
            $fields['ic_repetir'] = $agenda['ic_repetir'];
        }
        if($agenda['ds_link_reuniao'] != 'undefined'){
            $fields['ds_link_reuniao'] = $agenda['ds_link_reuniao'];
        }
        $fields['ds_cep'] = $ds_cep;
        $fields['ds_endereco'] = $ds_endereco;
        $fields['ds_complemento'] = $ds_complemento;
        $fields['ds_numero'] = $ds_numero;
        $fields['ds_bairro'] = $ds_bairro;
        $fields['ds_cidade'] = $ds_cidade;
        $fields['ds_uf'] = $ds_uf;
        $fields['leads_pk'] = $agenda['leads_pk'];
        $fields['ic_status'] = $agenda['ic_status'];
        $fields['ds_obs'] = $agenda['ds_obs'];
        $fields['agendas_reagendamento_pk'] = $agenda['agendas_reagendamento_pk'];
        $fields['ds_obs_reagendamento'] = $agenda['ds_obs_reagendamento'];
        $fields['motivo_cancelameto_pk'] = $agenda['motivo_cancelameto_pk'];
        $fields['classificacao_pk'] = $agenda['classificacao_pk'];
        $fields['obs_classificacao'] = $agenda['obs_classificacao'];


        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($agenda['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   =  $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("agendas", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("agendas", $fields, " pk = ".$agenda['pk'],$this->pdo);
            $pk = $agenda['pk'];
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
        $sql.="       ,tipo_agendas_pk ";
        $sql.="       ,date_format(dt_ini_agenda_ini, '%d/%m/%Y') dt_agenda_ini";
        $sql.="       ,date_format(dt_hr_agenda_fim, '%d/%m/%Y') dt_agenda_fim";
        $sql.="       ,date_format(dt_ini_agenda_ini, '%H:%i') hr_agenda_ini";
        $sql.="       ,date_format(dt_hr_agenda_fim, '%H:%i') hr_agenda_fim";
        $sql.="       ,ic_lembrete ";
        $sql.="       ,ic_repetir ";
        $sql.="       ,ds_link_reuniao ";
        $sql.="       ,ds_cep ";
        $sql.="       ,ds_endereco ";
        $sql.="       ,ds_complemento ";
        $sql.="       ,ds_numero ";
        $sql.="       ,ds_bairro ";
        $sql.="       ,ds_cidade ";
        $sql.="       ,ds_uf ";
        $sql.="       ,leads_pk ";
        $sql.="       ,ic_status ";
        $sql.="       ,ds_obs ";
        $sql.="       ,agendas_reagendamento_pk ";
        $sql.="       ,ds_obs_reagendamento ";
        $sql.="       ,motivo_cancelameto_pk ";
        $sql.="       ,classificacao_pk ";
        $sql.="       ,obs_classificacao ";

        $sql.="  from agendas ";
        $sql.=" where pk = $pk ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function listar_por_tipo_agendas_pk($leads_pk,$tipo_agenda_pk,$ic_status){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $result = [];
        $sql ="";
        $sql.="select a.pk, a.dt_cadastro, a.usuario_cadastro_pk, a.dt_ult_atualizacao, a.usuario_ult_atualizacao_pk ";
        $sql.="       ,SUBSTRING_INDEX(SUBSTRING_INDEX(ds_usuario, ' ', 1), ' ', -1)  AS ds_usuario ";
        $sql.="       ,a.tipo_agendas_pk ";
        $sql.="       ,a.dt_ini_agenda_ini ";
        $sql.="       ,a.dt_hr_agenda_fim ";
        $sql.="       ,date_format(a.dt_ini_agenda_ini, '%d/%m/%Y') dt_agenda_ini";
        $sql.="       ,date_format(a.dt_hr_agenda_fim, '%d/%m/%Y') dt_agenda_fim";
        $sql.="       ,date_format(a.dt_ini_agenda_ini, '%H:%i') hr_agenda_fim";
        $sql.="       ,date_format(a.dt_hr_agenda_fim, '%H:%i') hr_agenda_ini";
        $sql.="       ,a.ic_lembrete ";
        $sql.="       ,a.ic_repetir ";
        $sql.="       ,a.ds_link_reuniao ";
        $sql.="       ,a.ds_cep ";
        $sql.="       ,a.ds_endereco ";
        $sql.="       ,a.ds_complemento ";
        $sql.="       ,a.ds_numero ";
        $sql.="       ,a.ds_bairro ";
        $sql.="       ,a.ds_cidade ";
        $sql.="       ,a.ds_uf ";
        $sql.="       ,a.leads_pk ";
        $sql.="       ,SUBSTRING_INDEX(SUBSTRING_INDEX(l.ds_lead, ' ', 1), ' ', -1)  AS ds_lead";
        $sql.="       ,a.ic_status ";
        $sql.="       ,a.ds_obs ";
        $sql.="       ,a.agendas_reagendamento_pk ";
        $sql.="       ,a.ds_obs_reagendamento ";
        $sql.="       ,a.motivo_cancelameto_pk ";
        $sql.="       ,a.classificacao_pk ";
        $sql.="       ,a.obs_classificacao ";
        $sql.="       ,case when a.tipo_agendas_pk = 1 then '#68C39F' ";
        $sql.="             when a.tipo_agendas_pk = 2 then '#6F42C1' ";
        $sql.="             when a.tipo_agendas_pk = 3 then '#ffff00' ";
        $sql.="             when a.tipo_agendas_pk = 4 then '#ff9933' ";
        $sql.="             when a.tipo_agendas_pk = 5 then '#ff5050' ";
        $sql.="             when a.tipo_agendas_pk = 6 then '#3399ff' ";
        $sql.="        end color ";
        $sql.="       ,case when a.tipo_agendas_pk = 1 then '#fff' ";
        $sql.="             when a.tipo_agendas_pk = 2 then '#fff' ";
        $sql.="             when a.tipo_agendas_pk = 3 then '#000' ";
        $sql.="             when a.tipo_agendas_pk = 4 then '#fff' ";
        $sql.="             when a.tipo_agendas_pk = 5 then '#fff' ";
        $sql.="             when a.tipo_agendas_pk = 6 then '#fff' ";
        $sql.="        end textColor ";
        $sql.="       ,case when a.classificacao_pk = 1 then 'bi bi-check-all' ";
        $sql.="             when a.ic_status = 2 then 'bi bi-check-lg' ";
        $sql.="             when a.ic_status = 3 then 'bi bi-x' ";
        $sql.="        else '' end icon ";

        $sql.="  from agendas a";
        $sql.="  left join usuarios u on u.pk = a.usuario_cadastro_pk";
        $sql.="  left JOIN leads l ON l.pk = a.leads_pk";
        $sql.=" where 1=1 ";
        if($leads_pk != ""){
            $sql.=" and a.leads_pk = ".$leads_pk." ";
        }
        if($tipo_agenda_pk != ""){
            $sql.=" and a.tipo_agendas_pk = ".$tipo_agenda_pk." ";
        }
        if($ic_status != ""){
            $sql.=" and a.ic_status = ".$ic_status." ";
        }
        $sql.=" order by a.tipo_agendas_pk asc ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if(count($query)>0){
            for($i = 0; $i < count($query); $i++){
                $sqlParticipantes ="";
                $sqlParticipantes .="SELECT participante_pk ";
                $sqlParticipantes .="  FROM agendas_participantes ap";
                $sqlParticipantes .="       LEFT JOIN usuarios u on u.pk = ap.participante_pk ";
                $sqlParticipantes .="       LEFT JOIN contatos c on c.pk = ap.participante_pk ";
                $sqlParticipantes .=" WHERE ap.agendas_pk = ".$query[$i]['pk'];
                $sqlParticipantes .="   AND (u.pk = ".$_SESSION['session_user']['par1']." || c.pk = ".$_SESSION['session_user']['par1'].")";

                $stmt = $this->pdo->prepare($sqlParticipantes);
                $stmt->execute();
                $queryParticipantes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                if(count($queryParticipantes)>0 || $query[$i]["usuario_cadastro_pk"]==$_SESSION['session_user']['par1']){
                    $result[] = array(
                        "icon" => $query[$i]["icon"],
                        "title" => $query[$i]["hr_agenda_ini"]."-".$query[$i]["ds_usuario"]." ".$query[$i]["ds_lead"],
                        "start" => $query[$i]['dt_ini_agenda_ini'],
                        "end"=> $query[$i]['dt_hr_agenda_fim'],
                        "color"=> $query[$i]['color'],
                        "textColor"=> $query[$i]['textColor'],
                        "id" => $query[$i]["pk"]
                    );
                }
            }
        }


        $retorno->data = $result;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }



    public function listarTodosPorLeadsPk($leads_pk){
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
        $sql.="select a.pk, a.dt_cadastro, a.usuario_cadastro_pk, a.dt_ult_atualizacao, a.usuario_ult_atualizacao_pk ";
        $sql.="       ,u.ds_usuario ";
        $sql.="       ,a.tipo_agendas_pk ";
        $sql.="       ,date_format(a.dt_ini_agenda_ini, '%d/%m/%Y %H:%i') dt_ini_agenda_ini";
        $sql.="       ,date_format(a.dt_cadastro, '%d/%m/%Y') dt_cadastro";
        $sql.="       ,a.dt_hr_agenda_fim ";
        $sql.="       ,a.ic_lembrete ";
        $sql.="       ,a.ic_repetir ";
        $sql.="       ,a.ds_link_reuniao ";
        $sql.="       ,a.ds_cep ";
        $sql.="       ,a.ds_endereco ";
        $sql.="       ,a.ds_complemento ";
        $sql.="       ,a.ds_numero ";
        $sql.="       ,a.ds_bairro ";
        $sql.="       ,a.ds_cidade ";
        $sql.="       ,a.ds_uf ";
        $sql.="       ,a.leads_pk ";
        $sql.="       ,a.ic_status ";
        $sql.="       ,a.ds_obs ";
        $sql.="       ,a.agendas_reagendamento_pk ";
        $sql.="       ,a.ds_obs_reagendamento ";
        $sql.="       ,case when a.tipo_agendas_pk = 1 then 'REUNIÃO PRESENCIAL' ";
        $sql.="             when a.tipo_agendas_pk = 2 then 'REUNIÃO POR VIDEO CHAMADA' ";
        $sql.="             when a.tipo_agendas_pk = 3 then 'LEMBRETE' ";
        $sql.="             when a.tipo_agendas_pk = 4 then 'RETORNO' ";
        $sql.="             when a.tipo_agendas_pk = 5 then 'TAREFA' ";
        $sql.="             when a.tipo_agendas_pk = 6 then 'PESSOAL' ";
        $sql.="        end ds_tipo_agendas ";
        $sql.="       ,case when a.ic_status = 1 then 'Agenda Pendente' ";
        $sql.="             when a.ic_status = 2 then 'Agenda Concluída' ";
        $sql.="             when a.ic_status = 3 then 'Agenda Cancelada' ";
        $sql.="        end ds_status ";
        $sql.="  from agendas a ";
        $sql.=" inner join usuarios u on a.usuario_cadastro_pk = u.pk ";
        $sql.=" where 1=1 ";
        $sql.="   and a.leads_pk =".$leads_pk;
        $sql.=" order by a.tipo_agendas_pk asc ";



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
}
