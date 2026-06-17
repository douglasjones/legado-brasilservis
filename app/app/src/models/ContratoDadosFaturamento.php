<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class ContratoDadosFaturamento {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function salvar($contrato_dados_faturamento){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['metodos_pagamento_pk'] = $contrato_dados_faturamento['metodos_pagamento_pk'];
        $fields['num_parcela'] = $contrato_dados_faturamento['num_parcela'];
        $fields['dt_pagamento'] = $contrato_dados_faturamento['dt_pagamento'];
        $fields['dt_faturamento'] = $contrato_dados_faturamento['dt_faturamento'];
        $fields['vl_servico'] = $contrato_dados_faturamento['vl_servico'];
        $fields['contratos_pk'] = $contrato_dados_faturamento['contratos_pk'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($contrato_dados_faturamento['pk']  == ""){
            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("contrato_dados_faturamento", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }else{
            Util::execUpdate("contrato_dados_faturamento", $fields, " pk = ".$contrato_dados_faturamento['pk'],$this->pdo);
            $pk = $contrato_dados_faturamento['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;
    }

    public function listarGridContratoDadosFaturamento($contratos_pk){
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
                            l.ds_lead LIKE '%".$pesq."%' OR
                            ll.ds_lead LIKE '%".$pesq."%' 
                        )";
        }

        if($contratos_pk!=""){
            $sql ="";
            $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
            $sql.="       ,metodos_pagamento_pk ";
            $sql.="       ,num_parcela ";
            $sql.="       ,date_format(dt_pagamento,'%d/%m/%Y')dt_pagamento";
            $sql.="       ,vl_servico ";
            $sql.="       ,contratos_pk ";
            $sql.="       ,date_format(dt_faturamento,'%d/%m/%Y')dt_faturamento";

            $sql.="  from contrato_dados_faturamento ";
            $sql.=" where contratos_pk = $contratos_pk ";
            $sql.=" order by dt_pagamento asc";


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
    public function addMes($dt_base,$mes)
    {
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        if ($dt_base == "") {
            $sql = "";
            $sql .= "SELECT date_format(DATE_ADD(CURRENT_DATE(), INTERVAL " . $mes . " MONTH),'%d/%m/%Y')dt_base; ";
        } else {
            $sql = "";
            $sql .= "SELECT date_format(DATE_ADD('" . DataYMD($dt_base) . "', INTERVAL " . $mes . " MONTH),'%d/%m/%Y')dt_base; ";
        }


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        return $retorno;
    }

}
