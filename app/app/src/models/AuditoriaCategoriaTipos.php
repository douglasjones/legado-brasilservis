<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class AuditoriaCategoriaTipos {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function excluir($pk){
        Util::execDelete('auditoria_categorias_tipos', ' pk='.$pk, $this->pdo);
    }

    public function salvar($auditoria_categorias){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['auditoria_categorias_pk'] = $auditoria_categorias['auditoria_categorias_pk'];
        $fields['ds_auditoria_categoria_tipo'] = $auditoria_categorias['ds_auditoria_categoria_tipo'];
        $fields['ic_status'] = $auditoria_categorias['ic_status'];
        $fields['leads_pk'] = $auditoria_categorias['leads_pk'];
        $fields['produtos_pk'] = $auditoria_categorias['produtos_pk'];
        
        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($auditoria_categorias['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];


            $pk = Util::execInsert("auditoria_categorias_tipos", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("auditoria_categorias_tipos", $fields, " pk = ".$auditoria_categorias['pk'],$this->pdo);
            $pk = $auditoria_categorias['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;

    }

    public function salvarItens($auditoria_categorias_itens){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_categoria_item'] = $auditoria_categorias_itens['ds_categoria_item'];
        $fields['tipo_item_pk'] = $auditoria_categorias_itens['tipo_item_pk'];
        if($auditoria_categorias_itens['ic_status'] == ""){
            $fields['ic_status'] = 1;
        }else{
            $fields['ic_status'] = $auditoria_categorias_itens['ic_status'];
        }
        $fields['auditorias_categorias_pk'] = $auditoria_categorias_itens['auditorias_categorias_pk'];
        $fields['auditorias_categorias_tipos_pk'] = $auditoria_categorias_itens['auditorias_categorias_tipos_pk'];
        $fields['ic_obrigatorio'] = $auditoria_categorias_itens['ic_obrigatorio'];


        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($auditoria_categorias_itens['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("auditoria_categorias_itens", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("auditoria_categorias_itens", $fields, " pk = ".$auditoria_categorias_itens['pk'],$this->pdo);
            $pk = $auditoria_categorias_itens['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;

    }

    public function salvarItensCampos($dadosItensCampo){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $arrDados = json_decode ($dadosItensCampo, true);

        for($i=0; $i < count($arrDados); $i++){
            $fields = array();
            $fields['ds_item_dados'] = $arrDados[$i]['ds_item'];
            $fields['auditorias_categorias_itens_pk'] = $arrDados[$i]['itens_pk'];
            $fields['tipo_item_pk'] = $arrDados[$i]['tipo_item_pk'];

            $fields["dt_ult_atualizacao"] = "sysdate()";
            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("auditorias_categoria_itens_dados", $fields,$this->pdo);
        }
        $retorno->status = true;
        $retorno->message = 'Dados cadastrados com sucesso';
        $retorno->data = $pk;

        return $retorno;
        
    }

    public function atualizarStatus($strJSONDadosStatus){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $arrDados = json_decode ($strJSONDadosStatus, true);
        for($i=0;$i<count($arrDados);$i++){
            $fields = array();
            $fields["dt_ult_atualizacao"] = "sysdate()";
            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
            $fields['ic_status'] = $arrDados[$i]['ic_status'];
            
            Util::execUpdate("auditoria_categorias_itens", $fields, " pk = ".$arrDados[$i]['auditoria_categorias_itens_pk'],$this->pdo);
            $pk = Util::execUpdate("auditorias_categoria_itens_dados", $fields, " auditorias_categorias_itens_pk = ".$arrDados[$i]['auditoria_categorias_itens_pk'],$this->pdo);
        }  

        $retorno->status = true;
        $retorno->message = 'Dados cadastrados com sucesso';
        $retorno->data = $pk;
        return $retorno;
    }

    public function listarGrid($ds_categoria, $ic_status){
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
        $sql.="select act.pk t_pk, act.dt_cadastro, act.usuario_cadastro_pk, act.dt_ult_atualizacao, act.usuario_ult_atualizacao_pk ";
        $sql.="       ,ac.ds_categoria t_ds_categoria ";
        $sql.="       ,act.ds_auditoria_categoria_tipo t_ds_tipos_categoria ";
        $sql.="       ,case ac.ic_status when 1 then 'Ativo' when 2 then 'Inativo' end t_ic_status";

        $sql.="  from auditoria_categorias_tipos act ";
        $sql.="  inner join auditoria_categorias ac ON act.auditoria_categorias_pk = ac.pk ";
        $sql.=" where 1=1 ";
        if($ds_categoria != ""){
            $sql.=" and ac.ds_categoria like '%".$ds_categoria."%' ";
        }
        if($ic_status!=""){
            $sql.=" and ac.ic_status = ".$ic_status;
        }
        $sql.=" order by ds_categoria asc ";

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

    public function listarPorPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select act.pk, act.dt_cadastro, act.usuario_cadastro_pk, act.dt_ult_atualizacao, act.usuario_ult_atualizacao_pk ";
        $sql.="       ,act.auditoria_categorias_pk ";
        $sql.="       ,act.ds_auditoria_categoria_tipo ";
        $sql.="       ,act.ic_status ";
        $sql.="       ,case ac.ic_status when 1 then 'Ativo' when 2 then 'Inativo' end ds_status";

        $sql.="  from auditoria_categorias_tipos act ";
        $sql.="  inner join auditoria_categorias ac ON act.auditoria_categorias_pk = ac.pk ";
        if($pk!=""){
            $sql.=" where act.pk = $pk ";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso!';
        return $retorno;
    }

    public function listarPorCategoriasTiposPk($auditorias_categorias_tipos_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $result = [];

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
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        for($i=0; $i<count($rows);$i++){
            $sql ="";
            $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
            $sql.="       ,ds_item_dados ";
            $sql.="       ,ic_status";
            $sql.="       ,auditorias_categorias_itens_pk ";
            $sql.="       ,tipo_item_pk ";
            $sql.="  from auditorias_categoria_itens_dados ";
            $sql.=" where auditorias_categorias_itens_pk = ".$rows[$i]["pk"];
            $queryItensDados = $this->pdo->prepare($sql);
            $queryItensDados->execute();
            $rowsItensDados = $queryItensDados->fetchAll(\PDO::FETCH_ASSOC);
            $result[] = array(
                "pk" => $rows[$i]["pk"],
                "ds_categoria_item"=>$rows[$i]['ds_categoria_item'],
                "tipo_item_pk"=>$rows[$i]['tipo_item_pk'],
                "ds_tipo_item"=>$rows[$i]['ds_tipo_item'],
                "ic_status"=>$rows[$i]['ic_status'],
                "auditorias_categorias_pk"=>$rows[$i]['auditorias_categorias_pk'],
                "auditorias_categorias_tipos_pk"=>$rows[$i]['auditorias_categorias_tipos_pk'],
                "ic_obrigatorio"=>$rows[$i]['ic_obrigatorio'],
                "itensDados"=>$rowsItensDados,
                "ds_ic_obrigatorio"=>$rows[$i]['ds_ic_obrigatorio']
            );
        }


        $retorno->data = $result;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    

}
