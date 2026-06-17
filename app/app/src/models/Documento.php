<?php

namespace App\Model;


use App\Utils\Util;
use GuzzleHttp\Client;
use PDO;
use Throwable;

class Documento {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function excluir($pk,$pk_doc_bd){
        Util::execDelete('documentos'," pk = ".$pk,$this->pdo);
        Util::execDelete("tbl_docs"," docsId = ".$pk_doc_bd,$this->pdo);
    }
    public function excluirDocBd($pk_doc_bd){
        Util::execDelete("tbl_docs"," docsId = ".$pk_doc_bd,$this->pdo);
    }
    public function salvar($documento){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio



        $fields = array();
        $fields['ds_documento'] = $documento['ds_documento'];
        $fields['ds_obs'] = $documento['ds_obs'];
        $fields['ds_nome_original'] = $documento['ds_nome_original'];
        $fields['colaboradores_pk'] = $documento['colaboradores_pk'];
        $fields['leads_pk'] = $documento['leads_pk'];
        $fields['contratos_pk'] = $documento['contratos_pk'];
        $fields['ocorrencias_pk'] = $documento['ocorrencias_pk'];
        $fields['agenda_colaborador_tarefa_pk'] = $documento['agenda_colaborador_tarefa_pk'];
        $fields['lancamentos_pk'] = $documento['lancamentos_pk'];
        $fields['compras_pk'] = $documento['compras_pk'];
        $fields['agendas_pk'] = $documento['agendas_pk'];
        $fields['ic_tipo_documento'] = $documento['ic_tipo_documento'];
        $fields['pk_doc_bd'] = $documento['pk_doc_bd'];


        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($documento['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   =  $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("documentos", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("documentos", $fields, " pk = ".$documento['pk'],$this->pdo);
            $pk = $documento['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }

        return $retorno;

    }

    public function salvarDocumentoAgenda($documento){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio



        $fields = array();
        $fields['ds_documento'] = $documento['ds_documento'];
        $fields['ds_nome_original'] = $documento['ds_nome_original'];
        $fields['agendas_pk'] = $documento['agendas_pk'];
        $fields['leads_pk'] = $documento['leads_pk'];
        $fields['pk_doc_bd'] = $documento['pk_doc_pd'];


        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($documento['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   =  $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("documentos", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("documentos", $fields, " pk = ".$documento['pk'],$this->pdo);
            $pk = $documento['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }

        return $retorno;

    }
    public function updateDocCompra($compras_pk,$pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio



        $fields = array();

        $fields['compras_pk'] = $compras_pk;

        Util::execUpdate("documentos", $fields, " pk = ".$pk,$this->pdo);

        $retorno->status = true;
        $retorno->message = 'Dados atualizado com sucesso';
        $retorno->data = $pk;


        return $retorno;

    }
    public function updateDocColaboradores($colaborador_pk,$pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio



        $fields = array();

        $fields['colaboradores_pk'] = $colaborador_pk;

        Util::execUpdate("documentos", $fields, " pk = ".$pk,$this->pdo);

        $retorno->status = true;
        $retorno->message = 'Dados atualizado com sucesso';
        $retorno->data = $pk;


        return $retorno;

    }

    public function salvarDocumentoBd($files,$diretorio){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        

        $arquivo = $files[0]["name"]; 
        $tamanho = $files[0]["size"];
        $tipo    = $files[0]["type"];
        $diretorioArquivo = $diretorio.$arquivo;

        
        $fp = fopen($diretorioArquivo, "rb");

        $imgData = fread($fp, $tamanho);
        $imgData = addslashes($imgData);
        fclose($fp);

        $fields = array();
        $fields['docsType'] = $tipo;
        $fields['docsData'] = $imgData;


        $pk = Util::execInsert("tbl_docs", $fields,$this->pdo);

        $retorno->status = true;
        $retorno->message = 'Dados cadastrados com sucesso';
        $retorno->data = $pk;

        return $retorno;
    }
    

    public function listarQuantidadeDocumentosAgendas(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";
        $sql.="select count(*) total from documentos where agendas_pk is not null";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function pegarDocumentoBd($pk_doc_bd){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";
        $sql.="select docsId,docsType,docsData from tbl_docs where docsId = ".$pk_doc_bd;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

     public function listarQuantidadeDocumentosLead($leads_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";
        $sql.="select count(*) total from documentos where leads_pk = $leads_pk";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listarQuantidadeDocumentosCompra($compras_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";
        $sql.="select count(*) total from documentos where compras_pk = $compras_pk";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listarQuantidadeDocumentosColaborador($colaborador_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";
        $sql.="select count(*) total from documentos where colaboradores_pk = $colaborador_pk";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function listarDocumentosAgenda($agendas_pk){
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
        if($agendas_pk != "") {

            $sql = "";
            $sql .= "select d.pk t_pk, d.dt_cadastro, d.usuario_cadastro_pk, d.dt_ult_atualizacao, d.usuario_ult_atualizacao_pk ";
            $sql .= "       ,d.ds_documento t_ds_documento ";
            $sql .= "       ,d.ds_obs ";
            $sql .= "       ,d.ds_nome_original t_ds_nome_original ";
            $sql .= "       ,d.leads_pk ";
            $sql .= "       ,d.contratos_pk ";
            $sql .= "       ,d.ocorrencias_pk ";
            $sql .= "       ,d.agenda_colaborador_tarefa_pk";
            $sql .= "       ,d.lancamentos_pk";
            $sql.="         ,d.pk_doc_bd";

            $sql .= "  from documentos d";
            $sql .= " where 1=1 ";
            $sql .= " and d.agendas_pk =" . $agendas_pk;
            $sql .= " order by d.ds_documento asc ";


            $stmt = $this->pdo->prepare($sql . $lengthSql);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmtCount = $this->pdo->prepare($sql);
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
    public function listarDocumentosOcorrencia($ocorrencias_pk){
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
        if($ocorrencias_pk != "") {

            $sql = "";
            $sql .= "select d.pk t_pk, d.dt_cadastro, d.usuario_cadastro_pk, d.dt_ult_atualizacao, d.usuario_ult_atualizacao_pk ";
            $sql .= "       ,d.ds_documento t_ds_documento ";
            $sql .= "       ,d.ds_obs ";
            $sql .= "       ,d.ds_nome_original t_ds_nome_original ";
            $sql .= "       ,d.leads_pk ";
            $sql .= "       ,d.contratos_pk ";
            $sql .= "       ,d.ocorrencias_pk ";
            $sql .= "       ,d.agenda_colaborador_tarefa_pk";
            $sql .= "       ,d.lancamentos_pk";
            $sql.="         ,d.pk_doc_bd";
            $sql .= "  from documentos d";
            $sql .= " where 1=1 ";
            $sql .= " and d.ocorrencias_pk =" . $ocorrencias_pk;
            $sql .= " order by d.ds_documento asc ";


            $stmt = $this->pdo->prepare($sql . $lengthSql);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmtCount = $this->pdo->prepare($sql);
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
    public function listarDocumentosCompra($compras_pk){
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


            $sql = "";
            $sql .= "select d.pk t_pk, d.dt_cadastro, d.usuario_cadastro_pk, d.dt_ult_atualizacao, d.usuario_ult_atualizacao_pk ";
            $sql .= "       ,d.ds_documento t_ds_documento ";
            $sql .= "       ,d.ds_obs ";
            $sql .= "       ,d.ds_nome_original t_ds_nome_original ";
            $sql .= "       ,d.leads_pk ";
            $sql .= "       ,d.contratos_pk ";
            $sql .= "       ,d.ocorrencias_pk ";
            $sql .= "       ,d.agenda_colaborador_tarefa_pk";
            $sql .= "       ,d.lancamentos_pk";
            $sql.="         ,d.pk_doc_bd";
            $sql .= "  from documentos d";
            $sql .= " where 1=1 ";
            if($compras_pk != "") {
                $sql .= " and d.compras_pk =" . $compras_pk;
            }
            else{
                $sql .= " and d.compras_pk = 0";
            }
            $sql .= " order by d.ds_documento asc ";


            $stmt = $this->pdo->prepare($sql . $lengthSql);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmtCount = $this->pdo->prepare($sql);
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
    public function listarDocumentosLead($leads_pk){
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
        if($leads_pk != "") {

            $sql = "";
            $sql .= "select d.pk t_pk, d.dt_cadastro, d.usuario_cadastro_pk, d.dt_ult_atualizacao, d.usuario_ult_atualizacao_pk ";
            $sql .= "       ,d.ds_documento t_ds_documento ";
            $sql .= "       ,d.ds_obs t_ds_obs";
            $sql .= "       ,d.ds_nome_original t_ds_nome_original ";
            $sql .= "       ,d.leads_pk ";
            $sql .= "       ,d.contratos_pk ";
            $sql .= "       ,d.ocorrencias_pk ";
            $sql .= "       ,d.agenda_colaborador_tarefa_pk";
            $sql .= "       ,d.lancamentos_pk";
            $sql.="         ,d.pk_doc_bd";
            $sql .= "  from documentos d";
            $sql .= " where 1=1 ";
            $sql .= " and d.leads_pk =" . $leads_pk;
            $sql .= " order by d.ds_documento asc ";


            $stmt = $this->pdo->prepare($sql . $lengthSql);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmtCount = $this->pdo->prepare($sql);
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
    public function listarDocumentosColaborador($colaboradores_pk){
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

        $sql = "";
        $sql .= "select d.pk t_pk, d.dt_cadastro, d.usuario_cadastro_pk, d.dt_ult_atualizacao, d.usuario_ult_atualizacao_pk ";
        $sql .= "       ,d.ds_documento t_ds_documento ";
        $sql .= "       ,d.ds_obs t_ds_obs";
        $sql .= "       ,d.ds_nome_original t_ds_nome_original ";
        $sql .= "       ,d.leads_pk ";
        $sql .= "       ,d.contratos_pk ";
        $sql .= "       ,d.ocorrencias_pk ";
        $sql .= "       ,d.agenda_colaborador_tarefa_pk";
        $sql .= "       ,d.lancamentos_pk";
        $sql.="         ,d.pk_doc_bd";
        $sql .= "  from documentos d";
        $sql .= " where 1=1 ";
        if($colaboradores_pk != "") {
            $sql .= " and d.colaboradores_pk =" . $colaboradores_pk;
        }
        else{
            $sql .= " and d.colaboradores_pk = 0";
        }
        $sql .= " order by d.ds_documento asc ";


        $stmt = $this->pdo->prepare($sql . $lengthSql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmtCount = $this->pdo->prepare($sql);
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

public function listarQuantidadeDocumentosLancamento($lancamentos_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";
        $sql.="select count(*) total from documentos";
        if($lancamentos_pk!=""){
            $sql.=" where lancamentos_pk = $lancamentos_pk";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

public function listarDocumentosLancamentos($lancamentos_pk){
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
            $sql .= "select d.pk t_pk,  d.usuario_cadastro_pk, d.dt_ult_atualizacao, d.usuario_ult_atualizacao_pk ";
            $sql .= "       ,date_format(d.dt_cadastro, '%d/%m/%Y') dt_cadastro";
            $sql .= "       ,d.ds_documento t_ds_documento ";
            $sql .= "       ,d.ds_obs t_ds_obs";
            $sql .= "       ,d.ds_nome_original t_ds_nome_original ";
            $sql .= "       ,d.leads_pk ";
            $sql .= "       ,d.contratos_pk ";
            $sql .= "       ,d.ocorrencias_pk ";
            $sql .= "       ,d.agenda_colaborador_tarefa_pk";
            $sql .= "       ,d.lancamentos_pk";
            $sql.="         ,d.pk_doc_bd";
            $sql.="  from documentos d";
            $sql.=" where 1=1 ";
            if($lancamentos_pk != "") {
                $sql.=" and d.lancamentos_pk =".$lancamentos_pk;
            }
            else{
                $sql.=" and d.lancamentos_pk = 0";
            }

            $sql.=" order by d.ds_documento asc ";



            $stmt = $this->pdo->prepare($sql . $lengthSql);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmtCount = $this->pdo->prepare($sql);
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

    public function listarDocumentoClienteLead($leads_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = [];
        $retorno->iTotalDisplayRecords = 0;
        $retorno->iTotalRecords = 0;
       

        if($leads_pk!=""){
            $sql="";
            $sql .= "select d.pk, d.dt_cadastro, d.usuario_cadastro_pk, d.dt_ult_atualizacao, d.usuario_ult_atualizacao_pk ";
            $sql .= "       ,d.ds_documento  ";
            $sql .= "       ,d.ds_obs ";
            $sql .= "       ,d.ds_nome_original  ";
            $sql .= "       ,d.leads_pk ";
            $sql .= "       ,d.contratos_pk ";
            $sql .= "       ,d.ocorrencias_pk ";
            $sql .= "       ,d.agenda_colaborador_tarefa_pk";
            $sql .= "       ,d.lancamentos_pk";
            $sql.="         ,d.pk_doc_bd";
            $sql .= "  from documentos d";
            $sql.=" where d.leads_pk=".$leads_pk;
            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $retorno->data = $rows;
            $retorno->iTotalDisplayRecords = count($rows);
            $retorno->iTotalRecords = count($rows);
        }
        echo json_encode($retorno);
        exit(0);
    }


    public function getDocumentosApp($data){
        $retorno = new \StdClass;
        $retorno->success = false;
        $retorno->message = 'Nenhum dado encontrado.';
        $retorno->data = [];

        $sql  = "SELECT ";
        $sql .= "    d.pk_doc_bd, ";
        $sql .= "    d.ds_documento, ";
        $sql .= "    DATE_FORMAT(d.dt_cadastro, '%d/%m/%Y') AS dt_cadastro, ";
        $sql .= "    d.usuario_cadastro_pk, ";
        $sql .= "    u.ds_usuario ";
        $sql .= "FROM documentos d ";
        $sql .= "INNER JOIN usuarios u ON d.usuario_cadastro_pk = u.pk ";
        $sql .= "WHERE d.pk_doc_bd > 0 ";
        $sql .= "AND d.ic_status = 2 ";
        $sql .= "AND d.dt_assinatura is null ";
        // Se quiser usar filtro de colaborador:
        // $sql .= "AND d.colaboradores_pk = :colaborador_pk ";
        $sql .= " LIMIT 30 OFFSET 30 ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->success = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        

        return $retorno;
    }
    public function getDocumentoByIdApp($data) {
        try {
            $retorno = new \StdClass;
            $retorno->success = false;
            $retorno->message = 'Nenhum dado encontrado.';
            $retorno->data = [];

            $sql = "SELECT td.docsType, td.docsData FROM tbl_docs td WHERE td.docsId = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $data->documento_id, \PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if ($rows && count($rows) > 0) {
                $row = $rows[0];
                $retorno->success = true;
                $retorno->message = 'Documento carregado com sucesso';
                $retorno->data = [
                    'docsType' => $row['docsType'] ?? 'application/pdf',
                    'docsData' => base64_encode($row['docsData'])
                ];
            }

        } catch (\Throwable $th) {
            $retorno->success = false;
            $retorno->message = 'Erro: ' . $th->getMessage();
            $retorno->data = [];
        }

        return $retorno;
    }

    public function getAssinaturaColaborador($data){
        $retorno = new \StdClass;
        $retorno->success = false;
        $retorno->message = 'Nenhum dado encontrado.';
        $retorno->data = [];

        $sql  = "
                SELECT `pk`, 
                       `dt_cadastro`,
                       `colaboradores_pk`,
                       `assinatura` 
                FROM `colaborador_assinatura` 
            WHERE colaboradores_pk= ".$data->colaborador_pk;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $retorno->success = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        

        return $retorno;
    }
    public function salvarAssinaturaColaborador($data){


        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio



        $fields = array();
        $fields['colaboradores_pk'] = $data->colaborador_pk;
        $fields['assinatura'] = $data->assinatura;
        $fields["dt_cadastro"] = "sysdate()";

        $pk = Util::execInsert("colaborador_assinatura", $fields,$this->pdo);
        $retorno->status = true;
        $retorno->message = 'Dados cadastrados com sucesso';
        $retorno->data = $pk;

        return $retorno;
    }
    public function updateDocAssinado($id,$pdfAssinadoBase64,$data){


        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio



        $fields = array();
        $fields['ic_status'] = 1;
        $fields["dt_assinatura"] = "sysdate()";

        Util::execUpdate("documentos", $fields, " pk_doc_bd = ".$id,$this->pdo);

        $tipo = "application/pdf";
        $stmt = $this->pdo->prepare("UPDATE tbl_docs SET docsData = :pdf, docsType = :tipo WHERE docsId = :id");
        $stmt->bindParam(':pdf', $pdfAssinadoBase64, PDO::PARAM_LOB);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':id', $data->documento_id);
        $stmt->execute();

        return $retorno;
    }



}
