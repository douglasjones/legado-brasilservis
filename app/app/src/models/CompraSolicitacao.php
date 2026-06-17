<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class CompraSolicitacao {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    function excluir($pk){
        Util::execDelete('compras_solicitacao_orcamentos', ' compra_solicitacao_pk='.$pk, $this->pdo);
        Util::execDelete('compras_solicitacao', ' pk='.$pk, $this->pdo);
    }

    public function listarGrid($empresa_pk,$solicitante_pk,$usuario_aprovacao_pk,$tipo_grupo_centro_custo_pk,$grupo_lancamento_centrocusto_pk,$ic_status,$dt_solicitacao_ini,$dt_solicitacao_fim,$dt_aprovacao_ini,$dt_aprovacao_fim){
        
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
                            c.ds_conta LIKE '%".$pesq."%' OR
                            u.ds_usuario LIKE '%".$pesq."%' OR
                            u1.ds_usuario LIKE '%".$pesq."%' OR
                            cs.ds_compra_solicitacao LIKE '%".$pesq."%'
                        )";
        }
       
        $sql =""; 
        $sql.=" SELECT cs.pk,";
        $sql.="       c.ds_conta ds_empresa,";
        $sql.="       u.ds_usuario ds_solicitante,";
        $sql.="       cs.solicitante_pk,";
        $sql.="       cs.ds_compra_solicitacao,";
        $sql.="       date_format(cs.dt_solicitacao, '%d/%m/%Y') dt_solicitacao,";
        //$sql.="       CASE";
        //$sql.="          WHEN cs.dt_aprovacao  = 1 THEN 'Em Análise'";
        //$sql.="          WHEN cs.ic_status = 2 THEN 'Aprovada'";
        //$sql.="          WHEN cs.ic_status = 3 THEN 'Reprovada'";
        //$sql.="       END  ds_status,";        
        $sql.="       case WHEN cs.dt_aprovacao is null"; 
        $sql.="          THEN 'Em Análise'";
        $sql.="          ELSE 'Aprovado'";
        $sql.="       END ds_status,";
        
        $sql.="       u1.ds_usuario ds_usuario_aprovacao,";
        $sql.="       cs.usuario_aprovacao_pk,";
        $sql.="       date_format(cs.dt_aprovacao, '%d/%m/%Y') dt_aprovacao,";
        $sql.="       CASE";
        $sql.="          WHEN cs.tipo_grupo_centro_custo_pk = 1 THEN 'Leads (Clientes)'";
        $sql.="          WHEN cs.tipo_grupo_centro_custo_pk = 2 THEN 'Colaboradores'";
        $sql.="          WHEN cs.tipo_grupo_centro_custo_pk = 4 THEN 'Centros de Custos'";
        $sql.="       END  ds_tipo_grupo_centro_custo,";   
        $sql.="       cs.tipo_grupo_centro_custo_pk, ";
        $sql.="       cs.grupo_lancamento_centrocusto_pk";
        $sql.=" FROM compras_solicitacao cs";
        $sql.="     LEFT JOIN contas c ON cs.empresas_pk = c.pk";
        $sql.="     LEFT JOIN usuarios u ON cs.solicitante_pk = u.pk";
        $sql.="     LEFT JOIN usuarios u1 ON cs.usuario_aprovacao_pk = u1.pk";
       
        $sql.="  WHERE 1=1 ";
        $sql.=$search;
        if(!empty($empresa_pk)){
            $sql.="  AND cs.empresas_pk=".$empresa_pk;
        }
        if(!empty($solicitante_pk)){
            $sql.="  AND cs.solicitante_pk=".$solicitante_pk;
        }
        if(!empty($usuario_aprovacao_pk)){
            $sql.="  AND cs.usuario_aprovacao_pk=".$usuario_aprovacao_pk;
        }                
        if(!empty($tipo_grupo_centro_custo_pk)){
            $sql.="  AND cs.tipo_grupo_centro_custo_pk=".$tipo_grupo_centro_custo_pk;
        }           
        if(!empty($grupo_lancamento_centrocusto_pk)){
            $sql.="  AND cs.grupo_lancamento_centrocusto_pk=".$grupo_lancamento_centrocusto_pk;
        }         
        if(!empty($ic_status)){
            $sql.="  AND cs.ic_status=".$ic_status;
        }    
        if(!empty($dt_solicitacao_ini)){
            $sql.="  AND cs.dt_solicitacao >='".$dt_solicitacao_ini." 00:00:00'";
        } 
        if(!empty($dt_solicitacao_fim)){
            $sql.="  AND cs.dt_solicitacao <='".$dt_solicitacao_fim." 23:59:59'";
        } 
        if(!empty($dt_aprovacao_ini)){
            $sql.="  AND cs.dt_aprovacao >='".$dt_aprovacao_ini." 00:00:00'";
        } 
        if(!empty($dt_aprovacao_fim)){
            $sql.="  AND cs.dt_aprovacao <='".$dt_aprovacao_fim." 23:59:59'";
        } 
        $sql.=" Order by cs.pk desc";   

       

        

        $stmt = $this->pdo->prepare( $sql.$lengthSql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        if(count($query) > 0){                      
            for($i = 0; $i < count($query); $i++){
         
                $query0 = $this->listarCentroCusto($query[$i]['pk'],$query[$i]['tipo_grupo_centro_custo_pk'],$query[$i]['grupo_lancamento_centrocusto_pk']);
                if(count($query0)>0){
                    $ds_grupo_lancamento = $query0[0]['ds_grupo_lancamento_centrocusto'];
                }
                else{
                    $ds_grupo_lancamento = "";
                }
                $mysql_data[] = array(
                    "t_pk" => $query[$i]["pk"],
                    "t_ds_empresa"=>$query[$i]['ds_empresa'],
                    "t_ds_solicitante"=>$query[$i]['ds_solicitante'],
                    "t_ds_compra_solicitacao"=>$query[$i]['ds_compra_solicitacao'],
                    "t_dt_solicitacao"=>$query[$i]['dt_solicitacao'],
                    "t_ds_status"=>$query[$i]['ds_status'],
                    "t_ds_usuario_aprovacao"=>$query[$i]['ds_usuario_aprovacao'],
                    "t_dt_aprovacao"=>$query[$i]['dt_aprovacao'],   
                    "t_ds_tipo_grupo_centro_custo"=>$query[$i]['ds_tipo_grupo_centro_custo'],
                    "t_ds_grupo_lancamento_centrocusto"=>$ds_grupo_lancamento,
                    "t_solicitante_pk"=>$query[$i]['solicitante_pk'],
                    "t_usuario_aprovacao_pk"=>$query[$i]['usuario_aprovacao_pk'],                    
                    
                    "t_functions" => ""
                );
            }
        }
        else{
            $mysql_data = [];
        }

        $stmtCount = $this->pdo->prepare( $sql );
        $stmtCount->execute();
        $rowsCount = $stmtCount->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $mysql_data;
        $retorno->iTotalDisplayRecords = count($rowsCount);
        $retorno->iTotalRecords = count($rowsCount);

        echo json_encode($retorno);
        exit(0);
    }



    public function listarCentroCusto($pk,$tipo_grupo_centro_custo_pk,$grupo_lancamento_centrocusto_pk){

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select cs.pk ";
        if($tipo_grupo_centro_custo_pk==1){
            $sql.="       ,l.ds_lead ds_grupo_lancamento_centrocusto";
        }
        if($tipo_grupo_centro_custo_pk==2){
            $sql.="       ,c.ds_colaborador ds_grupo_lancamento_centrocusto";
        }        

        if($tipo_grupo_centro_custo_pk==3){
            $sql.="       ,f.ds_fornecedor ds_grupo_lancamento_centrocusto";
        }   

        $sql.="  from compras_solicitacao cs ";
        if($tipo_grupo_centro_custo_pk==1){
            $sql.=" INNER JOIN leads l on l.pk = cs.grupo_lancamento_centrocusto_pk";
        }
        if($tipo_grupo_centro_custo_pk==2){
            $sql.=" INNER JOIN colaboradores c on c.pk = cs.grupo_lancamento_centrocusto_pk";
        }
        if($tipo_grupo_centro_custo_pk==2){
            $sql.=" INNER JOIN fornecedor f on f.pk = cs.grupo_lancamento_centrocusto_pk";
        }
        $sql.=" where cs.pk=".$pk;

        if($tipo_grupo_centro_custo_pk==1){
            $sql.=" AND l.pk = ".$grupo_lancamento_centrocusto_pk;
        }
        if($tipo_grupo_centro_custo_pk==2){
            $sql.=" and c.pk =".$grupo_lancamento_centrocusto_pk;
        }
        if($tipo_grupo_centro_custo_pk==2){
            $sql.=" AND f.pk = ".$grupo_lancamento_centrocusto_pk;
        }
        $sql.=" order by cs.pk desc";

        

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $query;

        return  $retorno->data;

    }

    public function salvar($compra_solicitacao){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['solicitante_pk'] = $compra_solicitacao['solicitante_pk'];
        $fields['ds_compra_solicitacao'] = $compra_solicitacao['ds_compra_solicitacao'];
        $fields['dt_solicitacao'] = $compra_solicitacao['dt_solicitacao'] != '' ? Util::DataYMD($compra_solicitacao['dt_solicitacao']) : '';
        $fields['obs_solicitacao'] = $compra_solicitacao['obs_solicitacao'];
        $fields['usuario_aprovacao_pk'] = $compra_solicitacao['usuario_aprovacao_pk'];
        $fields['dt_aprovacao'] = $compra_solicitacao['dt_aprovacao'] != '' ? Util::DataYMD($compra_solicitacao['dt_aprovacao']) : '';
        $fields['obs_aprovacao'] = $compra_solicitacao['obs_aprovacao'];
        $fields['tipo_grupo_centro_custo_pk'] = $compra_solicitacao['tipo_grupo_centro_custo_pk'];
        $fields['grupo_lancamento_centrocusto_pk'] = $compra_solicitacao['grupo_lancamento_centrocusto_pk'];
        $fields['empresas_pk'] = $compra_solicitacao['empresas_pk'];
        
        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($compra_solicitacao['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];


            $pk = Util::execInsert("compras_solicitacao", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("compras_solicitacao", $fields, " pk = ".$compra_solicitacao['pk'],$this->pdo);
            $pk = $compra_solicitacao['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;

    }

    public function listarPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        
        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql.="       ,empresas_pk ";
        $sql.="       ,solicitante_pk ";
        $sql.="       ,ds_compra_solicitacao ";
        $sql.="       ,date_format(dt_solicitacao, '%d/%m/%Y') dt_solicitacao ";        
        $sql.="       ,obs_solicitacao ";
        $sql.="       ,usuario_aprovacao_pk ";
        $sql.="       ,date_format(dt_aprovacao, '%d/%m/%Y') dt_aprovacao";
        $sql.="       ,obs_aprovacao ";
        $sql.="       ,tipo_grupo_centro_custo_pk ";
        $sql.="       ,grupo_lancamento_centrocusto_pk ";

        $sql.="  from compras_solicitacao ";
        $sql.=" where pk = $pk ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

   }
