<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class Ocorrencia {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function excluir($pk){
        Util::execDelete('ocorrencias', ' pk='.$pk, $this->pdo);
    }
    public function salvar($ocorrencia){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_ocorrencia'] = $ocorrencia['ds_ocorrencia'];
        $fields['tipos_ocorrencias_pk'] = $ocorrencia['tipos_ocorrencias_pk'];
        $fields['processos_etapas_pk'] = $ocorrencia['processos_etapas_pk'];
        $fields['leads_pk'] = $ocorrencia['leads_pk'];
        $fields['ic_recusa'] = $ocorrencia['ic_recusa'];
        $fields['colaborador_pk'] = $ocorrencia['colaborador_pk'];
        if($ocorrencia['dt_prazo_execucao'] !=""){
            $fields['dt_prazo_execucao'] = Util::DataYMD($ocorrencia['dt_prazo_execucao']);
            $fields['dt_visualizacao'] = "sysdate()";
        }


        if($ocorrencia['dt_fechamento'] == 1){
            $fields['dt_fechamento'] = "sysdate()";
        }
        if($ocorrencia['dt_fechamento'] == 2){
            $fields['dt_fechamento'] = " ";
        }

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
        $fields["clientes_pk"] = $ocorrencia['clientes_pk'];
        $fields["obs_execucao"] = $ocorrencia['obs_execucao'];
        $fields["obs_recusa"] = $ocorrencia['obs_recusa'];

        if($ocorrencia['pk']  == ""){
            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("ocorrencias", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }else{
            Util::execUpdate("ocorrencias", $fields, " pk = ".$ocorrencia['pk'],$this->pdo);
            $pk = $ocorrencia['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;
    }


    public function listarOcorrenciasLeadPk($leads_pk){


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
            $sql ="";
            $sql.="SELECT DISTINCT(o.pk)t_pk, o.usuario_cadastro_pk, o.dt_ult_atualizacao, o.usuario_ult_atualizacao_pk ";
            $sql.="       ,l.ds_lead t_ds_lead";
            $sql.="       ,date_format(o.dt_cadastro,'%d/%m/%Y %H:%i:%s')t_dt_cadastro ";
            $sql.="       ,tio.ds_tipo_ocorrencia t_ds_tipo_ocorrencia";
            $sql.="       ,o.ds_ocorrencia t_ds_ocorrencia";
            $sql.="       ,u.ds_usuario t_nome_usuario_cadastro ";
            $sql.="       ,date_format(o.dt_fechamento,'%d/%m/%Y H:%i:%s')t_dt_fechamento ";
            $sql.="       ,r.responsavel_pk t_responsavel_pk";
            $sql.="       ,r.equipes_pk t_equipes_pk";
            $sql.="       ,u1.ds_usuario t_ds_agendado_para ";
            $sql.="       ,e1.ds_equipe t_equipe_agendado_para ";
            $sql.="       ,date_format(r.dt_retorno,'%d/%m/%Y %H:%i:%s')t_dt_retorno ";
            $sql.="       ,r.ds_retorno t_ds_retorno";
            $sql.="       ,date_format(r.dt_termino_retorno,'%d/%m/%Y %H:%i:%s')t_dt_termino_retorno ";
            $sql.="       ,o.tipos_ocorrencias_pk t_tipos_ocorrencias_pk";
            $sql.="       ,o.processos_etapas_pk t_processos_etapas_pk";
            $sql.="       ,date_format(o.dt_prazo_execucao,'%d/%m/%Y')t_dt_prazo_execucao";
            $sql.="       ,o.leads_pk t_leads_pk";
            $sql.="       ,case when o.ic_recusa = 1 then 'Chamado recusado'";
            $sql.="       when o.dt_prazo_execucao is null then 'Não lido'";
            $sql.="       when o.dt_fechamento is not null then 'Finalizado'";
            $sql.="       when o.dt_prazo_execucao >= sysdate() then 'Dentro do prazo'";
            $sql.="       when o.dt_prazo_execucao < sysdate() then 'Chamado atrasado'";
            $sql.="       end t_ds_status";
            $sql.="  from ocorrencias o";
            $sql.="  INNER JOIN leads l on o.leads_pk = l.pk ";
            $sql.="  INNER JOIN usuarios u on o.usuario_cadastro_pk = u.pk ";
            $sql.="  INNER JOIN tipos_ocorrencias tio on o.tipos_ocorrencias_pk = tio.pk ";
            $sql.="  LEFT JOIN retornos r on o.pk = r.ocorrencias_pk ";
            $sql.="  LEFT JOIN usuarios u1 on r.responsavel_pk = u1.pk ";
            $sql.="  LEFT JOIN equipes e1 on r.equipes_pk = e1.pk ";
            $sql.=" where 1=1 ";
            if($leads_pk != ""){
                $sql.=" and o.leads_pk = ".$leads_pk;
            }
            //$sql.=" Group by o.pk ";
            $sql.="       ORDER BY o.pk desc";


            $stmt = $this->pdo->prepare( $sql.$lengthSql );
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            for($i=0;$i<count($rows);$i++){
                $ds_agendado = "";
                if($rows[$i]["t_responsavel_pk"]!=""){
                    $ds_agendado = "Responsável: ".$rows[$i]["t_ds_agendado_para"];
                }
                else if($rows[$i]["t_equipes_pk"]!=""){
                    $ds_agendado = "Equipe: ".$rows[$i]["t_equipe_agendado_para"];
                }

                $rows[$i]['t_agendado_para'] = $ds_agendado;
            }


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

    public function listarOcorrenciaPorPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";
        $sql.="select o.pk t_pk, o.usuario_cadastro_pk, o.dt_ult_atualizacao, o.usuario_ult_atualizacao_pk ";
        $sql.="       ,l.ds_lead t_ds_lead";
        $sql.="       ,date_format(o.dt_cadastro,'%d/%m/%Y %H:%i:%s')t_dt_cadastro ";
        $sql.="       ,tio.ds_tipo_ocorrencia t_ds_tipo_ocorrencia";
        $sql.="       ,o.ds_ocorrencia t_ds_ocorrencia";
        $sql.="       ,o.obs_execucao t_obs_execucao";
        $sql.="       ,o.obs_recusa t_obs_recusa";
        $sql.="       ,o.colaborador_pk t_colaborador_pk";
        $sql.="       ,date_format(o.dt_prazo_execucao,'%d/%m/%Y') t_dt_prazo_execucao";
        $sql.="       ,u.ds_usuario t_nome_usuario_cadastro ";
        $sql.="       ,date_format(o.dt_fechamento,'%d/%m/%Y %H:%i:%s')t_dt_fechamento ";
        $sql.="       ,r.responsavel_pk t_responsavel_pk";
        $sql.="       ,r.equipes_pk t_equipes_pk";
        $sql.="       ,u1.ds_usuario t_nome_agendado_para ";
        $sql.="       ,e1.ds_equipe t_equipe_agendado_para ";
        $sql.="       ,date_format(r.dt_retorno,'%d/%m/%Y %H:%i:%s')t_dt_retorno ";
        $sql.="       ,r.ds_retorno t_ds_retorno";
        $sql.="       ,date_format(r.dt_termino_retorno,'%d/%m/%Y %H:%i:%s')t_dt_termino_retorno ";
        $sql.="       ,o.tipos_ocorrencias_pk t_tipos_ocorrencias_pk";
        $sql.="       ,o.processos_etapas_pk t_processos_etapas_pk";
        $sql.="       ,o.leads_pk t_leads_pk";
        $sql.="       ,r.pk t_retornos_pk";
        $sql.="  from ocorrencias o";
        $sql.="  LEFT JOIN leads l on o.leads_pk = l.pk ";
        $sql.="  INNER JOIN usuarios u on o.usuario_cadastro_pk = u.pk ";
        $sql.="  LEFT JOIN tipos_ocorrencias tio on o.tipos_ocorrencias_pk = tio.pk ";
        $sql.="  LEFT JOIN retornos r on o.pk = r.ocorrencias_pk ";
        $sql.="  LEFT JOIN usuarios u1 on r.responsavel_pk = u1.pk ";
        $sql.="  LEFT JOIN equipes e1 on r.equipes_pk = e1.pk ";
        $sql.=" Where o.pk = ".$pk;
        $sql.=" Group by o.pk ";
        


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function listar_por_ds_ocorrencia($ds_lead,$tipos_ocorrencias_pk,$ic_status,$usuario_cadastro_pk,$dt_cadastro,$dt_cadastro_fim,$usuario_agendado_para,$dt_prazo_execucao_ini,$dt_prazo_execucao_fim,$ic_status_fechamento,$equipes_pk,$colaborador_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $search ="";
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

        if (isset($_GET['search']['value']) and $_GET['search']['value'] != '') {
            $pesq = $_GET['search']['value'];
            $search .= " AND (
                            l.ds_lead LIKE '%".$pesq."%' OR 
                            c.ds_colaborador LIKE '%".$pesq."%' OR 
                            o.pk LIKE '%".$pesq."%' 
                            )";
        }
        $sql="";
        $sql.="SELECT DISTINCT (o.pk) pk, o.usuario_cadastro_pk, o.dt_ult_atualizacao, o.usuario_ult_atualizacao_pk ";
        $sql.="       ,l.ds_lead ";
        $sql.="       ,date_format(o.dt_cadastro,'%d/%m/%Y <br>%H:%i:%s')dt_cadastro ";
        $sql.="       ,tio.ds_tipo_ocorrencia";
        $sql.="       ,o.ds_ocorrencia";
        $sql.="       ,u.ds_usuario nome_usuario_cadastro ";
        $sql.="       ,date_format(o.dt_fechamento,'%d/%m/%Y<br>%H:%i:%s')dt_fechamento ";
        $sql.="       ,u1.ds_usuario nome_agendado_para ";
        $sql.="       ,date_format(r.dt_retorno,'%d/%m/%Y<br>%H:%i:%s')dt_retorno ";
        $sql.="       ,r.ds_retorno ";
        $sql.="       ,date_format(r.dt_termino_retorno,'%d/%m/%Y<br>%H:%i:%s')dt_termino_retorno ";
        $sql.="       ,o.tipos_ocorrencias_pk ";
        $sql.="       ,o.processos_etapas_pk ";
        $sql.="       ,r.responsavel_pk ";
        $sql.="       ,r.equipes_pk ";
        $sql.="       ,o.leads_pk ";
        $sql.="       ,o.clientes_pk";
        $sql.="       ,o.obs_execucao";
        $sql.="       ,o.obs_recusa";
        $sql.="       ,o.dt_prazo_execucao";
        $sql.="       ,o.ic_recusa";
        $sql.="       ,c.ds_colaborador";
        $sql.="       ,o.colaborador_pk";
        $sql.="       ,date_format(o.dt_visualizacao,'%d/%m/%Y %H:%i:%s')dt_visualizacao";
        $sql.="       ,case o.ic_recusa when 1 then 'Chamado Recusado' end ds_recusa";
        $sql.="       ,date_format(o.dt_prazo_execucao,'%d/%m/%Y')dt_prazo_execucao";
        $sql.="       ,o.dt_prazo_execucao dt_prazo_execucao_comp";
        $sql.="  from ocorrencias o";
        $sql.="  LEFT JOIN leads l on o.leads_pk = l.pk ";
        $sql.="  LEFT JOIN usuarios u on o.usuario_cadastro_pk = u.pk ";
        $sql.="  LEFT JOIN tipos_ocorrencias tio on o.tipos_ocorrencias_pk = tio.pk ";
        $sql.="  LEFT JOIN retornos r on o.pk = r.ocorrencias_pk ";
        $sql.="  LEFT JOIN colaboradores c on o.colaborador_pk = c.pk ";
        $sql.="  LEFT JOIN usuarios u1 on r.responsavel_pk = u1.pk ";

        $sql.=" where 1=1 ";
        $sql.= $search;
        //Lead
        if($ds_lead != " "){
            $sql.=" and l.ds_lead like '%".$ds_lead."%' ";
        }
        //Tipo Ocorrencia
        if(!empty($tipos_ocorrencias_pk)){
            $sql.=" and o.tipos_ocorrencias_pk=".$tipos_ocorrencias_pk;
        }
        if(!empty($colaborador_pk)){
            $sql.=" and o.colaborador_pk=".$colaborador_pk;
        }
        if(!empty($equipes_pk)){
            $sql.=" and r.equipes_pk =".$equipes_pk;
        }

        if(!empty($usuario_cadastro_pk)){
            $sql.=" and o.usuario_cadastro_pk=".$usuario_cadastro_pk;
        }

        if($ic_status_fechamento != ""){
            if($ic_status_fechamento==1){
                $sql.=" and dt_prazo_execucao is null";
            }else if($ic_status_fechamento==2){
                $sql.=" and o.dt_prazo_execucao >=".date("Y-m-d") ;
                $sql.=" and o.dt_fechamento is null ";
                $sql.=" and o.ic_recusa !=1 ";
            }else if($ic_status_fechamento==3){
                $sql.=" and o.dt_prazo_execucao < ".date("Y-m-d") ;
            }else if($ic_status_fechamento==4){
                $sql.=" and o.ic_recusa =1 ";
            }else if($ic_status_fechamento==5){
                $sql.=" and o.ic_recusa !=1 ";
                $sql.=" and o.dt_fechamento is not null ";
            }
        }

        /*if(!empty($ic_status_fechamento)){
            $sql.=" and o.ic_status_fechamento=".$ic_status_fechamento;
        }*/
        if(!empty($usuario_agendado_para)){
            $sql.=" and r.responsavel_pk=".$usuario_agendado_para;
        }

        if(!empty($dt_cadastro)){
            $sql.=" and o.dt_cadastro >='".Util::DataYMD($dt_cadastro)." 00:00:00'";
        }

        if(!empty($dt_cadastro_fim)){
            $sql.=" and o.dt_cadastro <='".Util::DataYMD($dt_cadastro_fim)." 23:59:59'";
        }
        if(!empty($dt_prazo_execucao_ini)){
            $sql.=" and o.dt_prazo_execucao >='".Util::DataYMD($dt_prazo_execucao_ini)."'";
        }

        if(!empty($dt_prazo_execucao_fim)){
            $sql.=" and o.dt_prazo_execucao <='".Util::DataYMD($dt_prazo_execucao_fim)."'";
        }

        if(!empty($ic_status)){
            if($ic_status==1){
                $sql.=" and o.dt_fechamento is null";
            }else{
                $sql.=" and o.dt_fechamento is not null";
            }
        }

        $sql.=" order by o.dt_cadastro desc";


        $stmt = $this->pdo->prepare( $sql.$lengthSql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmtCount = $this->pdo->prepare( $sql );
        $stmtCount->execute();
        $rowsCount = $stmtCount->fetchAll(\PDO::FETCH_ASSOC);

        $rows = array();
        for($i = 0; $i < count($rowsCount); $i++){

            $ds_status = "";

            $data1 = $query[$i]['dt_prazo_execucao_comp'];
            $data2 = date("Y-m-d");

            if($query[$i]["ic_recusa"]=="1"){
                $ds_status = "Chamado recusado";
            }elseif ($query[$i]['dt_prazo_execucao']=="") {
                $ds_status = "Não lido";
            }elseif ($query[$i]['dt_fechamento'] !=''){
                $ds_status = "Finalizado";
            }elseif (strtotime($data1) >= strtotime($data2)){
                $ds_status = "Dentro do prazo";
            }elseif (strtotime($data1) < strtotime($data2)){
                $ds_status = "Chamado atrasado";
            }

            $ds_agendado = "";
            if($query[$i]["responsavel_pk"]!=""){
                $ds_agendado = "Responsável: ".$query[$i]["responsavel_pk"];
            }
            else if($query[$i]["equipes_pk"]!=""){
                $ds_agendado = "Equipe: ".$query[$i]["equipes_pk"];
            }

            $rows[] = array(
                "t_pk" => $query[$i]["pk"],
                "t_ds_lead"=>$query[$i]['ds_lead'],
                "t_leads_pk"=>$query[$i]['leads_pk'],
                "t_dt_cadastro"=>$query[$i]['dt_cadastro'],
                "t_ds_tipo_ocorrencia"=>$query[$i]['ds_tipo_ocorrencia'],
                "t_tipos_ocorrencias_pk"=>$query[$i]['tipos_ocorrencias_pk'],
                "t_ds_ocorrencia"=>wordwrap($query[$i]['ds_ocorrencia'], 30, "<br />\n"),
                "t_nome_usuario_cadastro"=>$query[$i]['nome_usuario_cadastro'],
                "t_dt_fechamento"=>$query[$i]['dt_fechamento'],
                "t_agendado_para"=>$ds_agendado,
                "t_dt_retorno"=>$query[$i]['dt_retorno'],
                "t_ds_retorno"=>wordwrap($query[$i]['ds_retorno'], 30, "<br />\n"),
                "t_dt_termino_retorno"=>$query[$i]['dt_termino_retorno'],
                "t_obs_recusa"=>$query[$i]["obs_recusa"],
                "t_ic_recusa"=>$query[$i]["ic_recusa"],
                "t_ds_status"=>$ds_status,
                "t_dt_prazo_execucao"=>$query[$i]['dt_prazo_execucao'],
                "t_clientes_pk"=>$query[$i]['clientes_pk'],
                "t_obs_execucao"=>$query[$i]['obs_execucao'],
                "t_colaborador_pk"=>$query[$i]['colaborador_pk'],
                "t_ds_colaborador"=>$query[$i]['ds_colaborador'],
                "t_dt_visualizacao"=>$query[$i]['dt_visualizacao'],

                "t_functions" => ""
            );
        }




        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        $retorno->iTotalDisplayRecords = count($rowsCount);
        $retorno->iTotalRecords = count($rowsCount);

        echo json_encode($retorno);
        exit(0);

    }
    public function listarDataTableGridCliente($ds_lead,$tipos_ocorrencias_pk,$ic_status,$usuario_cadastro_pk,$dt_cadastro,$dt_cadastro_fim,$usuario_agendado_para,$dt_prazo_execucao_ini,$dt_prazo_execucao_fim,$ic_status_fechamento,$equipes_pk,$colaborador_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $search ="";
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

        if (isset($_GET['search']['value']) and $_GET['search']['value'] != '') {
            $pesq = $_GET['search']['value'];
            $search .= " AND (
                            l.ds_lead LIKE '%".$pesq."%' OR 
                            c.ds_colaborador LIKE '%".$pesq."%' OR 
                            o.pk LIKE '%".$pesq."%' 
                            )";
        }
        $sql="";
        $sql.="SELECT DISTINCT (o.pk) pk, o.usuario_cadastro_pk, o.dt_ult_atualizacao, o.usuario_ult_atualizacao_pk ";
        $sql.="       ,l.ds_lead ";
        $sql.="       ,date_format(o.dt_cadastro,'%d/%m/%Y <br>%H:%i:%s')dt_cadastro ";
        $sql.="       ,tio.ds_tipo_ocorrencia";
        $sql.="       ,o.ds_ocorrencia";
        $sql.="       ,u.ds_usuario nome_usuario_cadastro ";
        $sql.="       ,date_format(o.dt_fechamento,'%d/%m/%Y<br>%H:%i:%s')dt_fechamento ";
        $sql.="       ,u1.ds_usuario nome_agendado_para ";
        $sql.="       ,date_format(r.dt_retorno,'%d/%m/%Y<br>%H:%i:%s')dt_retorno ";
        $sql.="       ,r.ds_retorno ";
        $sql.="       ,date_format(r.dt_termino_retorno,'%d/%m/%Y<br>%H:%i:%s')dt_termino_retorno ";
        $sql.="       ,o.tipos_ocorrencias_pk ";
        $sql.="       ,o.processos_etapas_pk ";
        $sql.="       ,r.responsavel_pk ";
        $sql.="       ,r.equipes_pk ";
        $sql.="       ,o.leads_pk ";
        $sql.="       ,o.clientes_pk";
        $sql.="       ,o.obs_execucao";
        $sql.="       ,o.obs_recusa";
        $sql.="       ,o.dt_prazo_execucao";
        $sql.="       ,o.ic_recusa";
        $sql.="       ,c.ds_colaborador";
        $sql.="       ,o.colaborador_pk";
        $sql.="       ,date_format(o.dt_visualizacao,'%d/%m/%Y %H:%i:%s')dt_visualizacao";
        $sql.="       ,case o.ic_recusa when 1 then 'Chamado Recusado' end ds_recusa";
        $sql.="       ,date_format(o.dt_prazo_execucao,'%d/%m/%Y')dt_prazo_execucao";
        $sql.="       ,o.dt_prazo_execucao dt_prazo_execucao_comp";
        $sql.="  from ocorrencias o";
        $sql.="  INNER JOIN leads l on o.leads_pk = l.pk ";
        $sql.="  LEFT JOIN usuarios u on o.usuario_cadastro_pk = u.pk ";
        $sql.="  LEFT JOIN tipos_ocorrencias tio on o.tipos_ocorrencias_pk = tio.pk ";
        $sql.="  LEFT JOIN retornos r on o.pk = r.ocorrencias_pk ";
        $sql.="  LEFT JOIN colaboradores c on o.colaborador_pk = c.pk ";
        $sql.="  LEFT JOIN usuarios u1 on r.responsavel_pk = u1.pk ";

        $sql.=" where 1=1 ";
        $sql.= $search;
        //Lead
        if($ds_lead != ""){
            $sql.=" and (l.pk=".$ds_lead.")";
        }
        else{
            $sql.=" and (l.pk=".$_SESSION['session_user']['par6']." OR l.leads_pai_pk= ".$_SESSION['session_user']['par6'].")";
        }
        //Tipo Ocorrencia
        if(!empty($tipos_ocorrencias_pk)){
            $sql.=" and o.tipos_ocorrencias_pk=".$tipos_ocorrencias_pk;
        }
        if(!empty($colaborador_pk)){
            $sql.=" and o.colaborador_pk=".$colaborador_pk;
        }
        if(!empty($equipes_pk)){
            $sql.=" and r.equipes_pk =".$equipes_pk;
        }

        if(!empty($usuario_cadastro_pk)){
            $sql.=" and o.usuario_cadastro_pk=".$usuario_cadastro_pk;
        }

        if($ic_status_fechamento != ""){
            if($ic_status_fechamento==1){
                $sql.=" and dt_prazo_execucao is null";
            }else if($ic_status_fechamento==2){
                $sql.=" and o.dt_prazo_execucao >=".date("Y-m-d") ;
                $sql.=" and o.dt_fechamento is null ";
                $sql.=" and o.ic_recusa !=1 ";
            }else if($ic_status_fechamento==3){
                $sql.=" and o.dt_prazo_execucao < ".date("Y-m-d") ;
            }else if($ic_status_fechamento==4){
                $sql.=" and o.ic_recusa =1 ";
            }else if($ic_status_fechamento==5){
                $sql.=" and o.ic_recusa !=1 ";
                $sql.=" and o.dt_fechamento is not null ";
            }
        }

        /*if(!empty($ic_status_fechamento)){
            $sql.=" and o.ic_status_fechamento=".$ic_status_fechamento;
        }*/
        if(!empty($usuario_agendado_para)){
            $sql.=" and r.responsavel_pk=".$usuario_agendado_para;
        }

        if(!empty($dt_cadastro)){
            $sql.=" and o.dt_cadastro >='".DataYMD($dt_cadastro)." 00:00:00'";
        }

        if(!empty($dt_cadastro_fim)){
            $sql.=" and o.dt_cadastro <='".DataYMD($dt_cadastro_fim)." 23:59:59'";
        }
        if(!empty($dt_prazo_execucao_ini)){
            $sql.=" and o.dt_prazo_execucao >='".DataYMD($dt_prazo_execucao_ini)."'";
        }

        if(!empty($dt_prazo_execucao_fim)){
            $sql.=" and o.dt_prazo_execucao <='".DataYMD($dt_prazo_execucao_fim)."'";
        }

        if(!empty($ic_status)){
            if($ic_status==1){
                $sql.=" and o.dt_fechamento is null";
            }else{
                $sql.=" and o.dt_fechamento is not null";
            }
        }

        $sql.=" order by o.dt_cadastro desc";

        $stmt = $this->pdo->prepare( $sql.$lengthSql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmtCount = $this->pdo->prepare( $sql );
        $stmtCount->execute();
        $rowsCount = $stmtCount->fetchAll(\PDO::FETCH_ASSOC);

        $rows = array();
        for($i = 0; $i < count($query); $i++){

            $ds_status = "";

            $data1 = $query[$i]['dt_prazo_execucao_comp'];
            $data2 = date("Y-m-d");

            if($query[$i]["ic_recusa"]=="1"){
                $ds_status = "Chamado recusado";
            }elseif ($query[$i]['dt_prazo_execucao']=="") {
                $ds_status = "Não lido";
            }elseif ($query[$i]['dt_fechamento'] !=''){
                $ds_status = "Finalizado";
            }elseif (strtotime($data1) >= strtotime($data2)){
                $ds_status = "Dentro do prazo";
            }elseif (strtotime($data1) < strtotime($data2)){
                $ds_status = "Chamado atrasado";
            }

            $ds_agendado = "";
            if($query[$i]["responsavel_pk"]!=""){
                $ds_agendado = "Responsável: ".$query[$i]["responsavel_pk"];
            }
            else if($query[$i]["equipes_pk"]!=""){
                $ds_agendado = "Equipe: ".$query[$i]["equipes_pk"];
            }

            $rows[] = array(
                "t_pk" => $query[$i]["pk"],
                "t_ds_lead"=>$query[$i]['ds_lead'],
                "t_leads_pk"=>$query[$i]['leads_pk'],
                "t_dt_cadastro"=>$query[$i]['dt_cadastro'],
                "t_ds_tipo_ocorrencia"=>$query[$i]['ds_tipo_ocorrencia'],
                "t_tipos_ocorrencias_pk"=>$query[$i]['tipos_ocorrencias_pk'],
                "t_ds_ocorrencia"=>wordwrap($query[$i]['ds_ocorrencia'], 30, "<br />\n"),
                "t_nome_usuario_cadastro"=>$query[$i]['nome_usuario_cadastro'],
                "t_dt_fechamento"=>$query[$i]['dt_fechamento'],
                "t_agendado_para"=>$ds_agendado,
                "t_dt_retorno"=>$query[$i]['dt_retorno'],
                "t_ds_retorno"=>wordwrap($query[$i]['ds_retorno'], 30, "<br />\n"),
                "t_dt_termino_retorno"=>$query[$i]['dt_termino_retorno'],
                "t_obs_recusa"=>$query[$i]["obs_recusa"],
                "t_ic_recusa"=>$query[$i]["ic_recusa"],
                "t_ds_status"=>$ds_status,
                "t_dt_prazo_execucao"=>$query[$i]['dt_prazo_execucao'],
                "t_clientes_pk"=>$query[$i]['clientes_pk'],
                "t_obs_execucao"=>$query[$i]['obs_execucao'],
                "t_colaborador_pk"=>$query[$i]['colaborador_pk'],
                "t_ds_colaborador"=>$query[$i]['ds_colaborador'],
                "t_dt_visualizacao"=>$query[$i]['dt_visualizacao'],

                "t_functions" => ""
            );
        }




        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        $retorno->iTotalDisplayRecords = count($rowsCount);
        $retorno->iTotalRecords = count($rowsCount);

        echo json_encode($retorno);
        exit(0);

    }
}
