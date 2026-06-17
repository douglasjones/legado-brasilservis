<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class SupervisaoAuditoriaLead {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function salvar($supervisao_auditoria_lead){
        $return = new \StdClass; //Estrutura de supervisao_auditoria_lead para controller
        $return->status = false; //supervisao_auditoria_lead setado status como false
        $return->data = []; //supervisao_auditoria_lead data setado como vazio


        $fields = array();
        $fields['auditorias_categorias_pk'] = $supervisao_auditoria_lead['auditorias_categorias_pk'];
        $fields['auditorias_categorias_tipos_pk'] = $supervisao_auditoria_lead['auditoria_categoria_tipos_pk'];
        $fields['leads_pk'] = $supervisao_auditoria_lead['leads_pk'];
        $fields['ds_localizacao'] = $supervisao_auditoria_lead['ds_localizacao'];


        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($supervisao_auditoria_lead['pk'] == ""){
            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("supervisao_auditorias", $fields,$this->pdo);
            $return->status = true;
            $return->message = 'Dados cadastrados com sucesso';
            $return->data = $pk;
        }else{
            Util::execUpdate("supervisao_auditorias", $fields, " pk = ".$supervisao_auditoria_lead['pk'],$this->pdo);
            $pk = $supervisao_auditoria_lead['pk'];
            $return->status = true;
            $return->message = 'Dados atualizado com sucesso';
            $return->data = $pk;
        }

        return $return;
    }
    public function listarPorCategoriasTiposSupervisao($auditorias_categorias_tipos_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        
        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql.="       ,ds_categoria_item ";
        $sql.="       ,tipo_item_pk";
        $sql.="       , case";
        $sql.="         when tipo_item_pk = 1 then 'Lista Suspensa'";
        $sql.="         when tipo_item_pk = 2 then 'Texto'";
        $sql.="         when tipo_item_pk = 3 then 'Checkbox'";
        $sql.="         when tipo_item_pk = 4 then 'Textarea'";
        $sql.="         end ds_tipo_item";
        $sql.="       ,ic_status ";
        $sql.="       ,auditorias_categorias_pk ";
        $sql.="       ,auditorias_categorias_tipos_pk ";
        $sql.="       ,ic_obrigatorio ";
        $sql.="       ,case ";
        $sql.="        when ic_obrigatorio = 1 then 'Sim' ";
        $sql.="        else 'Não' ";
        $sql.="         end ds_ic_obrigatorio";
        $sql.="  from auditoria_categorias_itens ";
        $sql.=" where auditorias_categorias_tipos_pk = $auditorias_categorias_tipos_pk ";
        $sql.=" and ic_status = 1";
        $query = $this->pdo->prepare($sql);
        $query->execute();
        $query = $query->fetchAll(\PDO::FETCH_ASSOC);

        for($i=0; $i<count($query);$i++){
            $sql ="";
            $sql.="select pk auditorias_categoria_itens_dados_pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
            $sql.="       ,ds_item_dados ";
            $sql.="       ,ic_status";
            $sql.="       ,auditorias_categorias_itens_pk ";
            $sql.="       ,tipo_item_pk ";
            $sql.="  from auditorias_categoria_itens_dados ";
            $sql.=" where auditorias_categorias_itens_pk = ".$query[$i]["pk"];
            $sql.=" and ic_status = 1";
            $queryItensDados = $this->pdo->prepare($sql);
            $queryItensDados->execute();
            $queryItensDados = $queryItensDados->fetchAll(\PDO::FETCH_ASSOC);

                $result[] = array(
                    "pk" => $query[$i]["pk"],
                    "ds_categoria_item"=>$query[$i]['ds_categoria_item'],
                    "tipo_item_pk"=>$query[$i]['tipo_item_pk'],
                    "ds_tipo_item"=>$query[$i]['ds_tipo_item'],
                    "ic_status"=>$query[$i]['ic_status'],
                    "auditorias_categorias_pk"=>$query[$i]['auditorias_categorias_pk'],
                    "auditorias_categorias_tipos_pk"=>$query[$i]['auditorias_categorias_tipos_pk'],
                    "ic_obrigatorio"=>$query[$i]['ic_obrigatorio'],
                    "itensDados"=>$queryItensDados,
                    "ds_ic_obrigatorio"=>$query[$i]['ds_ic_obrigatorio']
                );
        }
        
        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result;

        return $retorno;
    }


}
