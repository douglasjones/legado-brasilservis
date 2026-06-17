<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class ContratoItem {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function excluir($pk){
        Util::execDelete('agenda_colaborador_padrao'," contratos_itens_pk = ".$pk,$this->pdo);
        Util::execDelete('contratos_itens'," pk = ".$pk,$this->pdo);
    }

    public function listarContratoItem($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql.="       ,n_qtde ";
        $sql.="       ,vl_unit ";
        $sql.="       ,vl_total ";
        $sql.="       ,contratos_pk ";
        $sql.="       ,produtos_servicos_pk ";
        $sql.="       ,n_qtde_dias_semana";
        $sql.="       ,periodo";
        $sql.="       ,vl_mao_obra";

        $sql.="  from contratos_itens ";
        if($pk!=""){
            $sql.=" where contratos_pk = $pk ";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listarItensEscala($contratos_pk,$produtos_servicos_pk,$leads_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";
        $sql.=" SELECT ci.pk,";
        $sql.="       ps.ds_produto_servico,";
        $sql.="       ci.n_qtde,";
        $sql.="       ci.n_qtde_dias_semana";
        $sql.=" FROM contratos_itens ci";
        $sql.="     INNER JOIN contratos c ON ci.contratos_pk = c.pk";
        $sql.="     INNER JOIN produtos_servicos ps ON ci.produtos_servicos_pk = ps.pk";
        $sql.="       inner join processos_etapas pe on c.processos_etapas_pk = pe.pk";
        $sql.="       inner join processos p on pe.processos_pk = p.pk";
        $sql.=" where 1=1 ";
        if($leads_pk!=""){
            $sql.=" and p.leads_pk=".$leads_pk;
        }
        if($contratos_pk!=""){
            $sql.=" AND ci.contratos_pk =".$contratos_pk;
        }

        if($produtos_servicos_pk!=""){
            $sql.=" AND ci.produtos_servicos_pk =".$produtos_servicos_pk;
        }

        $sql.=" ORDER BY ci.pk";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $qtde_servico_item_contrato = 0;
        $dias_escala = "";

        $query0 = $this->verificaServidoQtdeEscala($contratos_pk,$produtos_servicos_pk);

        if(count($query) > 0){
            for($i = 0; $i < count($query); $i++){
                $qtde_servico_item_contrato +=  $query[$i]["n_qtde"];
                $dias_escala = $query[$i]["n_qtde_dias_semana"];
            }
            for($i = 0; $i < count($query); $i++){
                for($j = 0; $j < count($query0->data); $j++){
                    $rows[] = array(
                        "contratos_itens_pk" => $query[$i]["pk"],
                        "ds_produto_servico" => $query[$i]["ds_produto_servico"],
                        "n_qtde" => $query[$i]["n_qtde"],
                        "n_qtde_dias_semana" => $query[$i]["n_qtde_dias_semana"],
                        "qtde_servico_escala" => $query0->data[$j]["qtde_servico_escala"]+1,
                        "qtde_servico_item_contrato" => $qtde_servico_item_contrato,
                        "dias_escala" =>$dias_escala
                    );
                }
            }
        }
        else{
            $rows = [];
        }


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function verificaServidoQtdeEscala($contratos_pk,$produtos_servicos_pk){

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql="";
        $sql.="SELECT count(a.pk)qtde_servico_escala";
        $sql.=" FROM agenda_colaborador_padrao a";
        $sql.=" inner join colaboradores c on a.colaboradores_pk = c.pk";
        $sql.=" WHERE 1=1";
        if($contratos_pk!=""){
            $sql.=" AND     a.contratos_pk = ".$contratos_pk;
        }
        if($produtos_servicos_pk!=""){
            $sql.=" AND a.produtos_servicos_pk = ".$produtos_servicos_pk;
        }
        $sql.=" AND a.dt_cancelamento IS NULL";
        $sql.=" AND a.dt_fim_agenda >= sysdate()";
        $sql.=" AND c.ic_status=1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);



        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
}
