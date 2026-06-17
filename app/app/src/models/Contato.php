<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class Contato {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function excluir($pk){
        Util::execDelete('contatos'," pk = ".$pk,$this->pdo);
    }
    public function salvar($contato){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_contato'] = $contato['ds_contato'];
        $fields['ds_cel'] = $contato['ds_cel'];
        $fields['ic_whatsapp'] = $contato['ic_whatsapp'];
        $fields['ds_email'] = $contato['ds_email'];
        $fields['ds_tel'] = $contato['ds_tel'];
        $fields['cargos_pk'] = $contato['cargos_pk'];
        $fields['leads_pk'] = $contato['leads_pk'];


        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($contato['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("contatos", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{

            Util::execUpdate("contatos", $fields, " pk = ".$contato['pk'],$this->pdo);

            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $contato['pk'];
        }
        return $retorno;

    }


    public function carregarPorLeadsPk($leads_pk){


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
            $sql= "";
            $sql.="select c.pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk ";
            $sql.="       ,c.ds_contato ";
            $sql.="       ,c.ds_cel ";
            $sql.="       ,case c.ic_whatsapp when 1 then 'Sim' when 2 then 'Não' end ds_whatsapp ";
            $sql.="       ,c.ic_whatsapp ";
            $sql.="       ,c.ds_email ";
            $sql.="       ,c.ds_tel ";
            $sql.="       ,c.cargos_pk ";
            $sql.="       ,cg.ds_cargo ds_cargos_pk ";
            $sql.="       ,c.leads_pk ";
            $sql.="  from contatos c";
            $sql.="     left join cargos cg on c.cargos_pk = cg.pk";
            $sql.=" where 1=1 ";
            if($leads_pk != ""){
                $sql.=" and leads_pk  =".$leads_pk;
            }
            $sql.=" order by ds_contato asc ";

            $stmt = $this->pdo->prepare( $sql.$lengthSql );
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            //COUNT
            $sqlCount ="";
            $sqlCount.="select count(*) TOTALLINHAS";
            $sqlCount.="  from contatos c";
            $sqlCount.="     left join cargos cg on c.cargos_pk = cg.pk";
            $sqlCount.=" where 1=1 ";
            if($leads_pk != ""){
                $sqlCount.=" and leads_pk  =".$leads_pk;
            }

            $stmtCount = $this->pdo->prepare( $sqlCount );
            $stmtCount->execute();
            $rowsCount = $stmtCount->fetchAll(\PDO::FETCH_ASSOC);

            $retorno->status = true;
            $retorno->message = 'Dados carregados com sucesso';
            $retorno->data = $rows;
            $retorno->iTotalDisplayRecords = $rowsCount[0]['TOTALLINHAS'];
            $retorno->iTotalRecords = $rowsCount[0]['TOTALLINHAS'];

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
}
