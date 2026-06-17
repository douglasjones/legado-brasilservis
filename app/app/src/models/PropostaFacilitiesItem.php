<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class PropostaFacilitiesItem {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function salvar($arrGrupos){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = true; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $retorno->message = ""; //Retorno data setado como vazio

        for($i=0; $i<count($arrGrupos);$i++){
            $fields = array();
            $fields['ds_percentual'] = $arrGrupos[$i]['ds_percentual'];
            $fields['ds_valor'] = ($arrGrupos[$i]['ds_valor']);
            if($arrGrupos[$i]['ic_status'] != 'undefined'){

                $fields['ic_status'] = $arrGrupos[$i]['ic_status'];
            }
            else{
                $fields['ic_status'] = "";
            }
            $fields['propostas_facilities_label_pk'] = $arrGrupos[$i]['propostas_facilities_label_pk'];
            $fields['propostas_facilities_grupos_subgrupos_pk'] = $arrGrupos[$i]['propostas_facilities_grupos_subgrupos_pk'];
            $fields['propostas_facilities_pk'] = $arrGrupos[$i]['propostas_facilities_pk'];

            $fields["dt_ult_atualizacao"] = "sysdate()";
            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

            if($arrGrupos[$i]['pk'] == ""){

                $fields["dt_cadastro"] = "sysdate()";
                $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

                $pk = Util::execInsert("propostas_facilities_itens", $fields,$this->pdo);
                $retorno->status = true;
                $retorno->message = 'Dados cadastrados com sucesso';
                $retorno->data = $pk;
            }
            else{
                Util::execUpdate("propostas_facilities_itens", $fields, " pk = ".$arrGrupos[$i]['pk'],$this->pdo);
                $pk = $arrGrupos[$i]['pk'];
                $retorno->status = true;
                $retorno->message = 'Dados atualizado com sucesso';
                $retorno->data = $pk;
            }
        }
        return $retorno;
    }

}
