<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class Processo {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function salvar($processo){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['leads_pk'] = $processo['leads_pk'];
        $fields['processos_default_pk'] = $processo['processos_default_pk'];
        $fields['ds_processo'] = $processo['ds_processo'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($processo['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   =  $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("processos", $fields,$this->pdo);
            //Inclui as etapas
            $sql = "";
            $sql.="insert into processos_etapas (dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk, ds_processo_etapa, processos_pk) ";
            $sql.="select SYSDATE(), ".$_SESSION['session_user']['par1'].", SYSDATE(), ".$_SESSION['session_user']['par1'].", ds_processo_default_etapa, $pk ";
            $sql.="  from processos_default_etapas ";
            $sql.=" where processos_default_pk = ".$processo['processos_default_pk'];

            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();


            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("processos", $fields, " pk = ".$processo['pk'],$this->pdo);
            $pk = $processo['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;

    }


    public function verificarQtdeLead($leads_pk){


        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select count(0)qtde";
        $sql.="  from processos p";
        $sql.=" where p.leads_pk = $leads_pk ";
        $sql.=" and ds_processo = 'Operacional'";


        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows[0]['qtde'];

        return $retorno;
    }
    public function listarPorLeadsPk($leads_pk){


        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select p.pk t_pk, p.dt_cadastro, p.usuario_cadastro_pk, p.dt_ult_atualizacao, p.usuario_ult_atualizacao_pk  ";
        $sql.="       ,p.ds_processo ";
        $sql.="       ,p.processos_default_pk ";
        $sql.="       ,p.leads_pk ";
        $sql.="       ,pe.pk processos_etapas_pk ";

        $sql.="  from processos_etapas pe ";
        $sql.="  inner join processos p on pe.processos_pk = p.pk";
        $sql.=" where leads_pk = $leads_pk ";
        $sql.=" group by p.pk";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = ($rows);

        return $retorno;
    }


}
