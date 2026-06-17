<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class Retorno {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function salvar($retorno){
        $return = new \StdClass; //Estrutura de retorno para controller
        $return->status = false; //Retorno setado status como false
        $return->data = []; //Retorno data setado como vazio


        $fields = array();
        $fields['dt_retorno'] = $retorno['dt_retorno'];
        $fields['equipes_pk'] = $retorno['equipes_pk'];
        $fields['responsavel_pk'] = $retorno['responsavel_pk'];
        $fields['ds_retorno'] = $retorno['ds_retorno'];
        $fields['ocorrencias_pk'] = $retorno['ocorrencias_pk'];
        $fields['tipo_lembrete_pk'] = $retorno['tipo_lembrete_pk'];

        if($retorno['dt_termino_retorno']== 1){
            $fields['dt_termino_retorno'] = "sysdate()";
        }
        if($retorno['dt_termino_retorno']== 2){
            $fields['dt_termino_retorno'] = " ";
        }

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];;

        if($retorno['pk']  == ""){
            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("retornos", $fields,$this->pdo);
            $return->status = true;
            $return->message = 'Dados cadastrados com sucesso';
            $return->data = $pk;
        }else{
            Util::execUpdate("retornos", $fields, " pk = ".$retorno['pk'],$this->pdo);
            $pk = $retorno['pk'];
            $return->status = true;
            $return->message = 'Dados atualizado com sucesso';
            $return->data = $pk;
        }

        return $return;
    }


    public function listarOcorrenciasLeadPk($leads_pk){


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

        if($leads_pk != ""){
            $sql ="";
            $sql.="SELECT DISTINCT(o.pk)t_pk, o.usuario_cadastro_pk, o.dt_ult_atualizacao, o.usuario_ult_atualizacao_pk ";
            $sql.="       ,l.ds_lead t_ds_lead";
            $sql.="       ,date_format(o.dt_cadastro,'%d/%m/%Y %H:%i:%s')t_dt_cadastro ";
            $sql.="       ,tio.ds_tipo_ocorrencia t_ds_tipo_ocorrencia";
            $sql.="       ,o.ds_ocorrencia t_ds_ocorrencia";
            $sql.="       ,u.ds_usuario t_ds_usuario_cadastro ";
            $sql.="       ,date_format(o.dt_fechamento,'%d/%m/%Y H:%i:%s')t_dt_fechamento ";
            $sql.="       ,r.responsavel_pk t_responsavel_pk";
            $sql.="       ,r.equipes_pk t_equipes_pk";
            $sql.="       ,u1.ds_usuario t_ds_agendado_para ";
            $sql.="       ,e1.ds_equipe t_equipe_agendado_para ";
            $sql.="       ,date_format(r.dt_retorno,'%d/%m/%Y %H:%i:%s')t_dt_retorno ";
            $sql.="       ,r.ds_retorno t_ds_retorno";
            $sql.="       ,date_format(r.dt_termino_retorno,'%d/%m/%Y %H:%i:%s')t_dt_termino_retorno ";
            $sql.="       ,o.tipos_ocorrencias_pk t_tipos_ocorrencias_pk";
            $sql.="       ,o.processos_etapas_pk t_processos_etapas_pk";
            $sql.="       ,date_format(o.dt_prazo_execucao,'%d/%m/%Y')t_dt_prazo_execucao";
            $sql.="       ,o.leads_pk t_leads_pk";
            $sql.="       ,case when o.ic_recusa = 1 then 'Chamado recusado'";
            $sql.="       when o.dt_prazo_execucao is null then 'Não lido'";
            $sql.="       when o.dt_fechamento is not null then 'Finalizado'";
            $sql.="       when o.dt_prazo_execucao >= sysdate() then 'Dentro do prazo'";
            $sql.="       when o.dt_prazo_execucao < sysdate() then 'Chamado atrasado'";
            $sql.="       end t_ds_status";
            $sql.="  from ocorrencias o";
            $sql.="  INNER JOIN leads l on o.leads_pk = l.pk ";
            $sql.="  INNER JOIN usuarios u on o.usuario_cadastro_pk = u.pk ";
            $sql.="  INNER JOIN tipos_ocorrencias tio on o.tipos_ocorrencias_pk = tio.pk ";
            $sql.="  LEFT JOIN retornos r on o.pk = r.ocorrencias_pk ";
            $sql.="  LEFT JOIN usuarios u1 on r.responsavel_pk = u1.pk ";
            $sql.="  LEFT JOIN equipes e1 on r.equipes_pk = e1.pk ";
            $sql.=" where 1=1 ";
            if($leads_pk != ""){
                $sql.=" and o.leads_pk = ".$leads_pk;
            }
            //$sql.=" Group by o.pk ";
            $sql.="       ORDER BY o.pk desc";


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
        else{
            $retorno->status = true;
            $retorno->message = 'Dados carregados com sucesso';
            $retorno->data = [];
            $retorno->iTotalDisplayRecords = 0;
            $retorno->iTotalRecords = 0;
        }


        echo json_encode($retorno);
        exit(0);
    }

    public function listarPorOcorrenciasPk($ocorrencias_pk){


        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql.="       ,date_format(dt_retorno, '%d/%m/%Y ') dt_retorno";
        $sql.="       ,date_format(dt_retorno, '%H:%i:%s') hr_retorno";
        $sql.="       ,date_format(dt_termino_retorno, '%d/%m/%Y ') dt_termino_retorno";
        $sql.="       ,date_format(dt_termino_retorno, '%H:%i:%s') hr_termino_retorno";
        $sql.="       ,equipes_pk ";
        $sql.="       ,responsavel_pk ";
        $sql.="       ,ds_retorno ";
        $sql.="       ,ocorrencias_pk ";
        $sql.="       ,tipo_lembrete_pk ";
        $sql.="  from retornos ";
        $sql.=" where ocorrencias_pk = $ocorrencias_pk ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }
}
