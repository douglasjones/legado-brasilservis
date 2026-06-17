<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class ContaConfigNota {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function excluir($pk){
        Util::execDelete('contas_dados_config_nota'," pk = ".$pk,$this->pdo);
    }

    public function salvar($contaConfigNota){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_inscricao_estatual'] = $contaConfigNota['ds_inscricao_estatual'];
        $fields['ic_regime_tributacao'] = $contaConfigNota['ic_regime_tributacao'];
        $fields['ic_regime_tributacao_especial'] = $contaConfigNota['ic_regime_tributacao_especial'];
        $fields['ic_incentivo_cultural'] = $contaConfigNota['ic_incentivo_cultural'];
        $fields['ic_incentivo_fiscal'] = $contaConfigNota['ic_incentivo_fiscal'];
        $fields['ds_ddd'] = $contaConfigNota['ds_ddd'];
        $fields['ds_tel'] = $contaConfigNota['ds_tel'];
        $fields['ds_email'] = $contaConfigNota['ds_email'];
        $fields['ds_nome_arquivo_certificado'] = $contaConfigNota['ds_nome_arquivo_certificado'];
        $fields['ds_link_arquivo_certificado'] = $contaConfigNota['ds_link_arquivo_certificado'];
        $fields['ds_nome_certificado'] = $contaConfigNota['ds_nome_certificado'];
        $fields['ds_id'] = $contaConfigNota['ds_id'];
        $fields['dt_criacao_certificado'] = $contaConfigNota['dt_criacao_certificado'] != ''?Util::DataYMD($contaConfigNota['dt_criacao_certificado']):'';
        $fields['dt_vencimento_certificado'] = $contaConfigNota['dt_vencimento_certificado'] != ''?Util::DataYMD($contaConfigNota['dt_vencimento_certificado']):'';
        $fields['ds_ult_numero_nota'] = $contaConfigNota['ds_ult_numero_nota'];
        $fields['ds_serie_nota'] = $contaConfigNota['ds_serie_nota'];
        $fields['ds_obs'] = $contaConfigNota['ds_obs'];
        $fields['ic_status'] = $contaConfigNota['ic_status'];
        $fields['contas_pk'] = $contaConfigNota['contas_pk'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($contaConfigNota['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   =  $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("contas_dados_config_nota", $fields,$this->pdo);
            
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("contas_dados_config_nota", $fields, " pk = ".$contaConfigNota['pk'],$this->pdo);
            $pk = $contaConfigNota['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;

    }
    public function listarPorPk($pk) {
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql = "";
        $sql .= "select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql .= "       ,ds_inscricao_estatual ";
        $sql .= "       ,ic_regime_tributacao ";
        $sql .= "       ,ic_regime_tributacao_especial ";
        $sql .= "       ,ic_incentivo_cultural ";
        $sql .= "       ,ic_incentivo_fiscal ";
        $sql .= "       ,ds_ddd ";
        $sql .= "       ,ds_tel ";
        $sql .= "       ,ds_email ";
        $sql .= "       ,ds_nome_arquivo_certificado ";
        $sql .= "       ,ds_link_arquivo_certificado ";
        $sql .= "       ,ds_nome_certificado ";
        $sql .= "       ,ds_id ";
        $sql .= "       ,dt_criacao_certificado ";
        $sql .= "       ,dt_vencimento_certificado ";
        $sql .= "       ,ds_ult_numero_nota ";
        $sql .= "       ,ds_serie_nota ";
        $sql .= "       ,ds_obs ";
        $sql .= "       ,ic_status ";
        $sql .= "       ,contas_pk ";
        $sql .= "  from contas_dados_config_nota ";
        $sql .= " Where 1=1 ";
        if($pk!=""){
            $sql .= " and pk = $pk ";
        }

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }

}
