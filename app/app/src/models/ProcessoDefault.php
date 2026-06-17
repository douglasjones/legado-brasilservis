<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class ProcessoDefault {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function excluir($pk){
        Util::execDelete('processos', ' processos_default_pk='.$pk, $this->pdo);
        Util::execDelete('processos_default', ' pk='.$pk, $this->pdo);
    }


    function excluirProcessosDefaultEtapasPk($processo_default_pk){
        Util::execDelete('processo_default_configuracao', ' processos_default_pk='.$processo_default_pk, $this->pdo);
        Util::execDelete('processos_default_etapas', ' processos_default_pk='.$processo_default_pk, $this->pdo);
        Util::execDelete('processo_default_configuracao', ' processos_default_pk='.$processo_default_pk, $this->pdo);
        //echo $this->db->getLastSQL();
    }

    function excluirProcessosDefaultModulosPk($processo_default_pk){
        
        Util::execDelete('processos_defalt_config_modulos', ' processos_default_pk='.$processo_default_pk, $this->pdo);
    }

    public function verificarContratoProcesso($pk){

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select c.pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk ";
        $sql.="       ,c.dt_inicio_contrato ";
        $sql.="       ,c.dt_fim_contrato ";
        $sql.="       ,c.processos_etapas_pk ";
        $sql.="       ,c.ic_tipo_contrato ";
        $sql.="       ,c.contratos_pk ";
        $sql.="       ,concat('Contrato ',c.pk)ds_combo_contrato";
        $sql.="       ,c.dt_cancelamento";
        $sql.="       ,c.ds_obs_motivo_cancelamento";
        $sql.="       ,c.empresas_pk";
        $sql.="  from contratos c";
        $sql.="       inner join processos_etapas pe on c.processos_etapas_pk = pe.pk";
        $sql.="       inner join processos p on pe.processos_pk = p.pk";
        $sql.=" where 1=1 ";
        $sql.=" and p.processos_default_pk= ".$pk;

        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = count($rows);

        return $retorno;
    }

    public function salvar($processo_default){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_processo_default'] = $processo_default['ds_processo_default'];
        $fields['ic_status'] = $processo_default['ic_status'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($processo_default['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];


            $pk = Util::execInsert("processos_default", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("processos_default", $fields, " pk = ".$processo_default['pk'],$this->pdo);
            $pk = $processo_default['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;

    }

    public function adicionarProcessosDefaultEtapas($processo_default_pk, $ds_processo_default_etapa, $n_ordem_etapa){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        
        $fields = array();
        $fields["dt_cadastro"] = "sysdate()";
        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_cadastro_pk"] = $_SESSION['session_user']['par1'];
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
        $fields['ds_processo_default_etapa'] = $ds_processo_default_etapa;
        $fields['n_ordem_etapa'] = $n_ordem_etapa;
        $fields['processos_default_pk'] = $processo_default_pk;
        $fields['equipes_pk'] = " ";


        $pk = Util::execInsert("processos_default_etapas", $fields,$this->pdo);
        $retorno->status = true;
        $retorno->message = 'Dados cadastrados com sucesso';
        $retorno->data = $pk;
       
        return $retorno;

    }

    public function adicionarProcessosDefaultModulos($processo_default_pk, $modulo_pk, $n_ordem_modulo, $ic_status){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields["dt_cadastro"] = "sysdate()";
        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_cadastro_pk"] =  $_SESSION['session_user']['par1'];
        $fields["usuario_ult_atualizacao_pk"] =  $_SESSION['session_user']['par1'];
        $fields['ic_status'] = $ic_status;
        $fields['processos_default_modulos_pk'] = $modulo_pk;
        $fields['n_ordem'] = $n_ordem_modulo;
        $fields['processos_default_pk'] = $processo_default_pk;
    

        $pk = Util::execInsert("processos_defalt_config_modulos", $fields,$this->pdo);
        $retorno->status = true;
        $retorno->message = 'Dados cadastrados com sucesso';
        $retorno->data = $pk;
       
        return $retorno;

    }

    public function listarTodos(){

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_processo_default ";
        $sql.="       ,case ic_status when 1 then 'Ativo' when 2 then 'Inativo' end ic_status ";

        $sql.="  from processos_default ";
        $sql.=" where 1=1 ";
        $sql.=" order by ds_processo_default asc ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows[0];

        return $retorno;
    }

    public function listarGrid($ds_processo_default, $ic_status){

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
        $sql.="select pk t_pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_processo_default t_ds_processo_default ";
        $sql.="       ,case ic_status when 1 then 'Ativo' when 2 then 'Inativo' end t_ic_status ";

        $sql.="  from processos_default ";
        $sql.=" where 1=1 ";
        if($ds_processo_default != ""){
            $sql.=" and ds_processo_default like '%".$ds_processo_default."%' ";
        }
        if($ic_status != ""){
            $sql.=" and ic_status = ".$ic_status;
        }
        $sql.=" order by ds_processo_default asc ";

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

    


    public function listarPk($pk){

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql.="       ,ds_processo_default ";
        $sql.="       ,ic_status ";

        $sql.="  from processos_default ";
        $sql.=" where pk = $pk ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows[0];

        return $retorno;
    }

    public function listarProcessoDefaultPk($processo_default_pk){

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_processo_default_etapa ";
        $sql.="       ,n_ordem_etapa ";
        $sql.="       ,processos_default_pk ";

        $sql.="  from processos_default_etapas ";
        $sql.=" where 1=1 ";
        if($processo_default_pk != ""){
            $sql.=" and processos_default_pk  = ".$processo_default_pk;
        }
        $sql.=" order by n_ordem_etapa asc ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }

    public function listarModulosProcessoDefaultPk($processo_default_pk){

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="SELECT pdcm.processos_default_modulos_pk modulos_pk, pdcm.n_ordem";
        $sql.="  from processos_defalt_config_modulos pdcm";
        $sql.="  LEFT JOIN processos_default_modulos pdm on pdcm.processos_default_modulos_pk = pdm.pk";
        $sql.=" where pdcm.processos_default_pk = $processo_default_pk ";
        $sql.=" order by pdcm.n_ordem asc ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }   

}
