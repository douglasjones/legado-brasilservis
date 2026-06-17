<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;
use App\Model\ContratoItem;
use Throwable;

class Contrato {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function excluir($contratos_pk){
        Util::execDelete("contrato_dados_faturamento", " contratos_pk = ".$contratos_pk,$this->pdo);
        Util::execDelete("contratos_produtos_itens", " contratos_pk = ".$contratos_pk,$this->pdo);
        Util::execDelete("movimentacao_estoque", " contratos_pk = ".$contratos_pk,$this->pdo);
        Util::execDelete("conjunto_material", " contratos_pk = ".$contratos_pk,$this->pdo);
        Util::execDelete("lancamentos", " contratos_pk = ".$contratos_pk,$this->pdo);
        Util::execDelete('lancamentos_financeiros', 'contratos_pk = '.$contratos_pk,$this->pdo);
        Util::execDelete("contratos_itens", " contratos_pk = ".$contratos_pk,$this->pdo);
        Util::execDelete("contratos", " pk = ".$contratos_pk,$this->pdo);
    }
    public function deleteContratoItensPorContratoPk($contratos_pk){
        Util::execDelete("contratos_itens", " contratos_pk = ".$contratos_pk,$this->pdo);
 
    }
    public function excluirProdutosItens($contratos_pk){
        Util::execDelete("contratos_produtos_itens", " contratos_pk = ".$contratos_pk,$this->pdo);
    }
    public function excluirProdutosItensPk($pk){
        Util::execDelete("contratos_produtos_itens", " pk = ".$pk,$this->pdo);
    }
    public function excluirContratoDadosFaturamento($contratos_pk){
        Util::execDelete("contrato_dados_faturamento", " contratos_pk = ".$contratos_pk,$this->pdo);
    }
    public function salvar($contrato){
        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio
            if($contrato['processos_etapas_pk']==null){
                //MAIORIA DOS ERROS DE CONTRATO
                return $retorno;
            }
            $fields = array();
            $fields['dt_inicio_contrato'] = $contrato['dt_inicio_contrato'];
            $fields['dt_fim_contrato'] = $contrato['dt_fim_contrato'];
            $fields['processos_etapas_pk'] = $contrato['processos_etapas_pk'];
            $fields['ic_tipo_contrato'] = $contrato['ic_tipo_contrato'];
            $fields['contratos_pk'] = $contrato['contratos_pk'];
            $fields['empresas_pk'] = $contrato['empresas_pk'];
            $fields['ic_lancar_financeiro'] = $contrato['ic_lancar_financeiro'];
            $fields['qtde_parcelas_pk'] = $contrato['qtde_parcelas_pk'];
            $fields['vl_total_mao_obra'] = $contrato['vl_total_mao_obra'];
            $fields['ds_identificacao_area'] = $contrato['ds_identificacao_area'];
            $fields['vl_contrato'] = ($contrato['vl_contrato']);

            if($contrato['dt_cancelamento']== 1){
                $fields['dt_cancelamento'] = "sysdate()";
            }
            if($contrato['dt_cancelamento']== 2){
                $fields['dt_cancelamento'] = " ";
            }
            $fields['ds_obs_motivo_cancelamento'] = $contrato['ds_obs_motivo_cancelamento'];

            $fields["dt_ult_atualizacao"] = "sysdate()";
            $fields["usuario_ult_atualizacao_pk"]   = $_SESSION['session_user']['par1'];

            if($contrato['pk']  == ""){
                $fields["dt_cadastro"] = "sysdate()";
                $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

                $pk = Util::execInsert("contratos", $fields,$this->pdo);
                $retorno->status = true;
                $retorno->message = 'Dados cadastrados com sucesso';
                $retorno->data = $pk;
            }else{
                Util::execUpdate("contratos", $fields, " pk = ".$contrato['pk'],$this->pdo);
                $pk = $contrato['pk'];
                $retorno->status = true;
                $retorno->message = 'Dados atualizado com sucesso';
                $retorno->data = $pk;
            }
            return $retorno;
        }
        catch(Throwable $e){
            print_r($e->getMessage());
            die();
        }
        
    }
    public function salvarProdutosItens($pk,$categorias_produto_pk,$produtos_pk,$n_qtde_item,$vl_item_produto){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['contratos_pk'] = $pk;
        $fields['categorias_produto_pk'] = $categorias_produto_pk;
        $fields['produtos_pk'] = $produtos_pk;
        $fields['n_qtde_item'] = $n_qtde_item;
        $fields['vl_item_produto'] = $vl_item_produto;
        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"]   = $_SESSION['session_user']['par1'];
        $fields["dt_cadastro"] = "sysdate()";
        $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

        $pk = Util::execInsert("contratos_produtos_itens", $fields,$this->pdo);
        $retorno->status = true;
        $retorno->message = 'Dados cadastrados com sucesso';
        $retorno->data = $pk;
        return $retorno;
    }
    public function adicionarContratoItens($contratos_itens_pk,$contratos_pk,$n_qtde_dias_semana, $n_qtde, $vl_unit, $vl_total, $produtos_servicos_pk,$periodo,$vl_mao_obra){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
        $fields["dt_cadastro"] = "sysdate()";
        $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

        $fields['contratos_pk'] = $contratos_pk;
        $fields['n_qtde'] = $n_qtde;
        $fields['n_qtde_dias_semana'] = $n_qtde_dias_semana;
        $fields['vl_unit'] = $vl_unit;
        $fields['vl_total'] = $vl_total;
        $fields['produtos_servicos_pk'] = $produtos_servicos_pk;
        $fields['periodo'] = $periodo;
        $fields['vl_mao_obra'] = $vl_mao_obra;

        if($contratos_itens_pk  == ""){
            $pk = Util::execInsert("contratos_itens", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }else{
            Util::execUpdate("contratos_itens", $fields, " pk = ".$contratos_itens_pk,$this->pdo);
            $pk = $contratos_itens_pk;
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;
    }


    public function listarContratoOperacional($pk,$leads_postotrabalho_pk,$ic_tipo_contrato,$dt_inicio_contrato,$dt_fim_contrato,$dt_recisao_contrato_ini,$dt_recisao_contrato_fim,$dt_cancelamento_ini,$dt_cancelamento_fim,$leads_clientes_pk){


        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $result = [];

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
        $search ="";
        if (isset($_GET['search']['value']) and $_GET['search']['value'] != '') {
            $pesq = $_GET['search']['value'];
            $search .= " AND (
                        l.ds_lead LIKE '%".$pesq."%' OR 
                        c.ds_identificacao_area LIKE '%".$pesq."%' OR 
                        c.pk LIKE '%".$pesq."%'
                        )";
        }


        $sql ="";
        $sql.="select c.pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk ";
        $sql.="       ,date_format(c.dt_inicio_contrato,'%d/%m/%Y') dt_inicio_contrato";
        $sql.="       ,date_format(c.dt_fim_contrato,'%d/%m/%Y') dt_fim_contrato";
        $sql.="       ,c.processos_etapas_pk ";
        $sql.="       ,case c.ic_tipo_contrato when 1 then 'Contrato' when 2 then 'Aditivo' when 3 then 'Serviço Extra' end ds_tipo_contrato";
        $sql.="       ,c.ic_tipo_contrato ";
        $sql.="       ,c.contratos_pk";
        $sql.="       ,date_format(c.dt_cancelamento,'%d/%m/%Y')dt_cancelamento";
        $sql.="       ,c.ds_obs_motivo_cancelamento";
        $sql.="       ,c.empresas_pk";
        $sql.="       ,c.ic_lancar_financeiro";
        $sql.="       ,c.vl_total_mao_obra";
        $sql.="       ,c.qtde_parcelas_pk";
        $sql.="       ,cdf.metodos_pagamento_pk";
        $sql.="       ,SUBSTRING(l.ds_lead,1,32) ds_lead";
        $sql.="       ,p.pk processos_pk";
        $sql.="       ,l.pk leads_pk";
        $sql.="       ,co.ds_razao_social ds_empresa";
        $sql.="       ,sum(ci.vl_total)vl_total ";
        $sql.="       ,c.ds_identificacao_area";
        $sql.="       ,l.ic_tipo_lead";
        $sql.="       ,case l.ic_tipo_lead when 1 then 'Cliente' when 2 then 'Posto de Trabalho' end ds_tipo_lead";
        $sql.="       ,l.leads_pai_pk ";
        $sql.="       ,c.vl_contrato ";
        $sql.="  from contratos c ";
        $sql.="       left join contratos_itens ci on ci.contratos_pk = c.pk";
        $sql.="       left join contrato_dados_faturamento cdf on cdf.contratos_pk = c.pk";
        $sql.="       INNER join processos_etapas pe on c.processos_etapas_pk = pe.pk";
        $sql.="       INNER join processos p on pe.processos_pk = p.pk";
        $sql.="       INNER join leads l on p.leads_pk = l.pk";
        $sql.="       left join contas co on c.empresas_pk = co.pk";
        $sql.=" where 1=1 ";
        $sql.= $search;
        if($pk!=""){
            $sql.=" and c.pk=".$pk;
        }

        if($leads_clientes_pk!="" && !$leads_postotrabalho_pk!=""){
            $sql .= " and (p.leads_pk = " . $leads_clientes_pk . " OR l.leads_pai_pk = " . $leads_clientes_pk . ")";
        }

        if(!$leads_clientes_pk!="" && $leads_postotrabalho_pk!=""){
            $sql.=" and p.leads_pk = ".$leads_postotrabalho_pk;
        }

        if($leads_clientes_pk!="" && $leads_postotrabalho_pk!=""){
            $sql .= " and (l.leads_pai_pk = " . $leads_clientes_pk . " and p.leads_pk = " . $leads_postotrabalho_pk . ")";
        }
        if($ic_tipo_contrato!=""){
            $sql.=" and c.ic_tipo_contrato=".$ic_tipo_contrato;
        }
        if($dt_inicio_contrato!=""){
            $sql.=" and c.dt_inicio_contrato between'".Util::DataYMD($dt_inicio_contrato)."' and '".Util::DataYMD($dt_fim_contrato)."'";
        }
        if($dt_recisao_contrato_ini!=""){
            $sql.=" and c.dt_recisao_contrato between'".Util::DataYMD($dt_recisao_contrato_ini)."' and '".Util::DataYMD($dt_recisao_contrato_fim)."'";
        }
        if($dt_cancelamento_ini!=""){
            $sql.=" and c.dt_cancelamento between'".Util::DataYMD($dt_cancelamento_ini)."' and '".Util::DataYMD($dt_cancelamento_fim)."'";
        }

        $sql.=" group by c.pk ";
        $sql.=" order by c.dt_cadastro desc";
 

        $stmt = $this->pdo->prepare( $sql.$lengthSql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $stmtCount = $this->pdo->prepare( $sql );
        $stmtCount->execute();
        $rowsCount = $stmtCount->fetchAll(\PDO::FETCH_ASSOC);

        for($i=0;$i<count($rows);$i++){
            $rows1 = (new ContratoItem($this->pdo))->listarContratoItem($rows[$i]["pk"]);
            $v_vl_total = 0;

            if(!empty($rows1->data)){
                for($j = 0; $j < count($rows1->data); $j++){
                    $v_vl_total += $rows1->data[$j]['vl_total'];
                }
            }

            $result[] = array(
                "t_pk" => $rows[$i]["pk"],
                "t_dt_inicio_contrato"=>$rows[$i]['dt_inicio_contrato'],
                "t_dt_fim_contrato"=>$rows[$i]['dt_fim_contrato'],
                "t_processos_etapas_pk"=>$rows[$i]['processos_etapas_pk'],
                "t_ic_tipo_contrato"=>$rows[$i]['ic_tipo_contrato'],
                "t_contratos_pk"=>$rows[$i]['contratos_pk'],
                "t_empresas_pk"=>$rows[$i]['empresas_pk'],
                "t_ds_empresa"=>$rows[$i]['ds_empresa'],
                "t_ds_tipo_contrato"=>$rows[$i]['ds_tipo_contrato'],
                "t_dt_cancelamento"=>$rows[$i]['dt_cancelamento'],
                "t_qtde_parcelas_pk"=>$rows[$i]['qtde_parcelas_pk'],
                "t_vl_total_mao_obra"=>number_format($rows[$i]['vl_total_mao_obra'],2,',','.'),
                "t_vl_contrato"=>number_format($rows[$i]['vl_contrato'],2,',','.'),
                "t_ic_lancar_financeiro"=>$rows[$i]['ic_lancar_financeiro'],
                "t_metodos_pagamento_pk"=>$rows[$i]['metodos_pagamento_pk'],
                "t_ds_obs_motivo_cancelamento"=>$rows[$i]['ds_obs_motivo_cancelamento'],
                "t_ds_lead"=>$rows[$i]['ds_lead'],
                "t_leads_postotrabalho_pk"=>$rows[$i]['leads_pk'],
                "t_processos_pk"=>$rows[$i]['processos_pk'],
                "t_ds_identificacao_area"=>$rows[$i]['ds_identificacao_area'],
                "t_ic_tipo_lead"=>$rows[$i]['ic_tipo_lead'],
                "t_ds_tipo_lead"=>$rows[$i]['ds_tipo_lead'],
                "t_leads_cliente_pk"=>$rows[$i]['leads_pai_pk'],
                "t_vl_total"=>number_format($v_vl_total,2,',','.'),
                "t_functions" => ""
            );
        }

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result;
        $retorno->iTotalDisplayRecords = count($rowsCount);
        $retorno->iTotalRecords = count($rowsCount);


        echo json_encode($retorno);
        exit(0);
    }
    public function listarPkCad($pk){


        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $result = [];

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
        $search ="";
        if (isset($_GET['search']['value']) and $_GET['search']['value'] != '') {
            $pesq = $_GET['search']['value'];
            $search .= " AND (
                        l.ds_lead LIKE '%".$pesq."%' OR 
                        c.ds_identificacao_area LIKE '%".$pesq."%' OR 
                        c.pk LIKE '%".$pesq."%'
                        )";
        }


        $sql ="";
        $sql.="select c.pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk ";
        $sql.="       ,date_format(c.dt_inicio_contrato,'%d/%m/%Y') dt_inicio_contrato";
        $sql.="       ,date_format(c.dt_fim_contrato,'%d/%m/%Y') dt_fim_contrato";
        $sql.="       ,c.processos_etapas_pk ";
        $sql.="       ,case c.ic_tipo_contrato when 1 then 'Contrato' when 2 then 'Aditivo' when 3 then 'Serviço Extra' end ds_tipo_contrato";
        $sql.="       ,c.ic_tipo_contrato ";
        $sql.="       ,c.contratos_pk";
        $sql.="       ,date_format(c.dt_cancelamento,'%d/%m/%Y')dt_cancelamento";
        $sql.="       ,c.ds_obs_motivo_cancelamento";
        $sql.="       ,c.empresas_pk";
        $sql.="       ,c.ic_lancar_financeiro";
        $sql.="       ,c.vl_total_mao_obra";
        $sql.="       ,c.qtde_parcelas_pk";
        $sql.="       ,cdf.metodos_pagamento_pk";
        $sql.="       ,SUBSTRING(l.ds_lead,1,32) ds_lead";
        $sql.="       ,p.pk processos_pk";
        $sql.="       ,l.pk leads_pk";
        $sql.="       ,co.ds_razao_social ds_empresa";
        $sql.="       ,sum(ci.vl_total)vl_total ";
        $sql.="       ,c.ds_identificacao_area";
        $sql.="       ,l.ic_tipo_lead";
        $sql.="       ,case l.ic_tipo_lead when 1 then 'Cliente' when 2 then 'Posto de Trabalho' end ds_tipo_lead";
        $sql.="       ,l.leads_pai_pk ";
        $sql.="       ,c.vl_contrato ";
        $sql.="  from contratos c ";
        $sql.="       left join contratos_itens ci on ci.contratos_pk = c.pk";
        $sql.="       left join contrato_dados_faturamento cdf on cdf.contratos_pk = c.pk";
        $sql.="       LEFT join processos_etapas pe on c.processos_etapas_pk = pe.pk";
        $sql.="       LEFT join processos p on pe.processos_pk = p.pk";
        $sql.="       LEFT join leads l on p.leads_pk = l.pk";
        $sql.="       left join contas co on c.empresas_pk = co.pk";
        $sql.=" where 1=1 ";
        $sql.= $search;
        if($pk!=""){
            $sql.=" and c.pk=".$pk;
        }

        $sql.=" group by c.pk ";
        $sql.=" order by c.dt_cadastro desc";


        $stmt = $this->pdo->prepare( $sql.$lengthSql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $stmtCount = $this->pdo->prepare( $sql );
        $stmtCount->execute();
        $rowsCount = $stmtCount->fetchAll(\PDO::FETCH_ASSOC);

        for($i=0;$i<count($rows);$i++){
            $rows1 = (new ContratoItem($this->pdo))->listarContratoItem($rows[$i]["pk"]);
            $v_vl_total = 0;

            if(!empty($rows1->data)){
                for($j = 0; $j < count($rows1->data); $j++){
                    $v_vl_total += $rows1->data[$j]['vl_total'];
                }
            }

            $result[] = array(
                "t_pk" => $rows[$i]["pk"],
                "t_dt_inicio_contrato"=>$rows[$i]['dt_inicio_contrato'],
                "t_dt_fim_contrato"=>$rows[$i]['dt_fim_contrato'],
                "t_processos_etapas_pk"=>$rows[$i]['processos_etapas_pk'],
                "t_ic_tipo_contrato"=>$rows[$i]['ic_tipo_contrato'],
                "t_contratos_pk"=>$rows[$i]['contratos_pk'],
                "t_empresas_pk"=>$rows[$i]['empresas_pk'],
                "t_ds_empresa"=>$rows[$i]['ds_empresa'],
                "t_ds_tipo_contrato"=>$rows[$i]['ds_tipo_contrato'],
                "t_dt_cancelamento"=>$rows[$i]['dt_cancelamento'],
                "t_qtde_parcelas_pk"=>$rows[$i]['qtde_parcelas_pk'],
                "t_vl_total_mao_obra"=>number_format($rows[$i]['vl_total_mao_obra'],2,',','.'),
                "t_vl_contrato"=>number_format($rows[$i]['vl_contrato'],2,',','.'),
                "t_ic_lancar_financeiro"=>$rows[$i]['ic_lancar_financeiro'],
                "t_metodos_pagamento_pk"=>$rows[$i]['metodos_pagamento_pk'],
                "t_ds_obs_motivo_cancelamento"=>$rows[$i]['ds_obs_motivo_cancelamento'],
                "t_ds_lead"=>$rows[$i]['ds_lead'],
                "t_leads_postotrabalho_pk"=>$rows[$i]['leads_pk'],
                "t_processos_pk"=>$rows[$i]['processos_pk'],
                "t_ds_identificacao_area"=>$rows[$i]['ds_identificacao_area'],
                "t_ic_tipo_lead"=>$rows[$i]['ic_tipo_lead'],
                "t_ds_tipo_lead"=>$rows[$i]['ds_tipo_lead'],
                "t_leads_cliente_pk"=>$rows[$i]['leads_pai_pk'],
                "t_vl_total"=>number_format($v_vl_total,2,',','.'),
                "t_functions" => ""
            );
        }

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result[0];
        $retorno->iTotalDisplayRecords = count($rowsCount);
        $retorno->iTotalRecords = count($rowsCount);


        return $retorno;
    }

    public function listarProdutosItens($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = true; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $retorno->message = '';

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

        if($pk!=""){
            $sql ="";
            $sql.="SELECT cpi.pk,";
            $sql.="        cp.ds_categoria,";
            $sql.="        cpi.categorias_produto_pk,";
            $sql.="        cpi.produtos_pk,";
            $sql.="        p.ds_produto,";
            $sql.="        cpi.n_qtde_item,";
            $sql.="        cpi.vl_item_produto,";
            $sql.="        cpi.contratos_pk";
            $sql.=" FROM contratos_produtos_itens cpi";
            $sql.="      INNER JOIN categorias_produto cp ON cpi.categorias_produto_pk = cp.pk";
            $sql.="      INNER JOIN produtos p ON cpi.produtos_pk = p.pk";
            $sql.=" WHERE cpi.contratos_pk =".$pk;
            $sql.=" ORDER BY cpi.pk";


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
    public function listar_contrato_pai($leads_pk,$contratos_pk,$contrato_pai_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $retorno->message = '';

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
        if($leads_pk!=""){
            $sql.=" and p.leads_pk=".$leads_pk;
        }
        if($contrato_pai_pk!=""){
            $sql.="   and and c.contratos_pk IS NULL OR c.contratos_pk=".$contrato_pai_pk;
        }
        else{
            if($contratos_pk!=""){
                $sql.="   and c.pk not in(".$contratos_pk.")";
            }
            $sql.="   and c.contratos_pk IS NULL";
        }

        $sql.="   group by c.pk";
        $sql.=" order by c.pk asc ";



        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';


        return $retorno;
    }
    public function listarLeadsPk($leads_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $retorno->message = '';

        $sql ="";
        $sql.="select c.pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk  ";
        $sql.="       ,c.dt_inicio_contrato ";
        $sql.="       ,c.dt_fim_contrato ";
        $sql.="       ,c.processos_etapas_pk ";
        $sql.="       ,c.ic_tipo_contrato ";
        $sql.="       ,c.contratos_pk ";
        $sql.="       ,concat('Contrato ',c.pk)ds_combo_contrato";
        $sql.="       ,c.dt_cancelamento";
        $sql.="       ,c.ds_obs_motivo_cancelamento";
        $sql.="       ,p.pk processos_pk";
        $sql.="       ,c.empresas_pk";
        $sql.="       ,c.ds_identificacao_area";
        $sql.="  from contratos c ";
        //$sql.="       inner join contratos_itens ci on ci.contratos_pk = c.pk";
        $sql.="       LEFT join contratos_itens ci on ci.contratos_pk = c.pk";
        $sql.="       left join colaboradores_produtos_servicos cps on ci.produtos_servicos_pk = cps.produtos_servicos_pk";
        $sql.="       left join processos_etapas pe on c.processos_etapas_pk = pe.pk";
        $sql.="       left join processos p on pe.processos_pk = p.pk";
        $sql.=" where 1=1 ";
        if($leads_pk!=""){
            $sql.=" and p.leads_pk=".$leads_pk;
        }
        $sql.=" group by c.pk";



        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';


        return $retorno;
    }
    public function listarPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $retorno->message = '';

        $sql ="";
        $sql.="select c.pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk ";
        $sql.="       ,DATE_FORMAT(c.dt_inicio_contrato,'%d/%m/%Y') dt_inicio_contrato";
        $sql.="       ,DATE_FORMAT(c.dt_fim_contrato,'%d/%m/%Y') dt_fim_contrato";
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
        if($pk!=""){
            $sql.=" and c.pk=".$pk;
        }

        $sql.="   group by c.pk";
        $sql.=" order by c.pk asc ";



        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';


        return $retorno;
    }

    public function listaColaboradorContratos($leads_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $retorno->message = '';

        $sql ="";
        $sql.="SELECT c.pk,";
        $sql.="    case WHEN c.ic_tipo_contrato =1 THEN";
        $sql.="      concat('FIXO',' - Cód:',c.pk,' - Periódo:',date_format(c.dt_inicio_contrato, '%d/%m/%Y'),' - ',date_format(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="     WHEN c.ic_tipo_contrato = 2 THEN";
        $sql.="      concat('Aditivo',' - Cód:',c.pk,' - Periódo:',date_format(c.dt_inicio_contrato, '%d/%m/%Y'),' - ',date_format(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="    WHEN c.ic_tipo_contrato =3 THEN";
        $sql.="      concat('EXTRA :',c.ds_identificacao_area)";        
        $sql.="    END ds_contrato";
        $sql.=" FROM contratos c";
        $sql.="   INNER JOIN processos_etapas pe ON c.processos_etapas_pk = pe.pk";
        $sql.="   INNER JOIN processos p ON pe.processos_pk = p.pk";
        $sql.=" WHERE p.leads_pk =".$leads_pk;
        $sql.=" AND dt_fim_contrato > sysdate()";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';


        return $retorno;
    }

    public function listaLeadContratos($leads_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $retorno->message = '';

        $sql ="";
        $sql.="SELECT c.pk,";
        $sql.="    case WHEN c.ic_tipo_contrato =1 THEN";
        $sql.="      concat('FIXO  - Cód:',c.pk,' - Periódo:',date_format(c.dt_inicio_contrato, '%d/%m/%Y'),' - ',date_format(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="     WHEN c.ic_tipo_contrato = 2 THEN";
        $sql.="      concat('Aditivo- Cód:', c.pk ,' - Periódo:',date_format(c.dt_inicio_contrato, '%d/%m/%Y'),' - ',date_format(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="    WHEN c.ic_tipo_contrato =3 THEN";
        $sql.="      concat('EXTRA- Cód:', c.pk , ' - ' ,c.ds_identificacao_area)";        
        $sql.="    END ds_contrato";
        $sql.=" FROM contratos c";
        $sql.="   INNER JOIN processos_etapas pe ON c.processos_etapas_pk = pe.pk";
        $sql.="   INNER JOIN processos p ON pe.processos_pk = p.pk";
        $sql.=" WHERE p.leads_pk =".$leads_pk;
        $sql.=" and c.dt_inicio_contrato <= sysdate()";
        $sql.=" and c.dt_fim_contrato >= sysdate()";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';


        return $retorno;
    }
    public function listaLeadContratosFinanceiro($leads_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $retorno->message = '';

        $sql ="";
        $sql.="SELECT c.pk,";
        $sql.="    case WHEN c.ic_tipo_contrato =1 THEN";
        $sql.="      concat('FIXO  - Cód:',c.pk,' - Periódo:',date_format(c.dt_inicio_contrato, '%d/%m/%Y'),' - ',date_format(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="     WHEN c.ic_tipo_contrato = 2 THEN";
        $sql.="      concat('Aditivo- Cód:', c.pk ,' - Periódo:',date_format(c.dt_inicio_contrato, '%d/%m/%Y'),' - ',date_format(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="    WHEN c.ic_tipo_contrato =3 THEN";
        $sql.="      concat('EXTRA- Cód:', c.pk , ' - ' ,c.ds_identificacao_area)";
        $sql.="    END ds_contrato";
        $sql.=" FROM contratos c";
        $sql.="   INNER JOIN processos_etapas pe ON c.processos_etapas_pk = pe.pk";
        $sql.="   INNER JOIN processos p ON pe.processos_pk = p.pk";
        $sql.=" WHERE p.leads_pk =".$leads_pk;
        $sql.=" and c.dt_inicio_contrato <= sysdate()";
        $sql.=" and c.dt_fim_contrato >= sysdate()";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $mysql_data = array();
        if(count($query) > 0){
            for($i = 0; $i < count($query); $i++){
                $ds_contrato_posto = "";
                $ds_contrato_posto = $query[$i]['ds_contrato'];
                $ds_produto_servico = "";

                $queryProdutoServico = (new ProdutoServico($this->pdo))->listarProdutosContrato($query[$i]['pk']);

                $ds_produto_servico =  $queryProdutoServico->data[0]['ds_produto_servico'];

                if(!empty($ds_produto_servico )){
                    $ds_contrato_posto = $ds_contrato_posto." Serviços: ".$ds_produto_servico;
                }

                $mysql_data[] = array(
                    "pk" => $query[$i]["pk"],
                    "ds_contrato"=>$ds_contrato_posto
                );
            }
        }



        $retorno->data = $mysql_data;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';

        return $retorno;

        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';


        return $retorno;
    }

    public function listaColaboradorContratosFinanceiro($leads_pk,$colaborador_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = true; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";
        $sql.="SELECT c.pk,";
        $sql.="    case WHEN c.ic_tipo_contrato =1 THEN";
        $sql.="      concat('FIXO',' - Cód:',c.pk,' - Periódo:',date_format(c.dt_inicio_contrato, '%d/%m/%Y'),' - ',date_format(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="     WHEN c.ic_tipo_contrato = 2 THEN";
        $sql.="      concat('Aditivo',' - Cód:',c.pk,' - Periódo:',date_format(c.dt_inicio_contrato, '%d/%m/%Y'),' - ',date_format(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="    WHEN c.ic_tipo_contrato =3 THEN";
        $sql.="      concat('EXTRA :',c.ds_identificacao_area)";
        $sql.="    END ds_contrato";
        $sql.=" FROM contratos c";
        $sql.="   INNER JOIN processos_etapas pe ON c.processos_etapas_pk = pe.pk";
        $sql.="   INNER JOIN processos p ON pe.processos_pk = p.pk";
        $sql.=" WHERE p.leads_pk =".$leads_pk;
        $sql.=" AND dt_fim_contrato > sysdate()";


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $mysql_data = array();
        if(count($query) > 0){
            for($i = 0; $i < count($query); $i++){
                $ds_contrato_posto = "";
                $ds_contrato_posto = $query[$i]['ds_contrato'];
                $ds_produto_servico = "";

                $queryProdutoServico = (new ProdutoServico($this->pdo))->listarProdutosContrato($query[$i]['pk']);

                $ds_produto_servico =  $queryProdutoServico->data[0]['ds_produto_servico'];

                if(!empty($ds_produto_servico )){
                    $ds_contrato_posto = $ds_contrato_posto." Serviços: ".$ds_produto_servico;
                }

                $mysql_data[] = array(
                    "pk" => $query[$i]["pk"],
                    "ds_contrato"=>$ds_contrato_posto
                );
            }
        }



        $retorno->data = $mysql_data;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';

        return $retorno;
    }

    public function relContrato($empresa_pk,$leads_clientes_pk,$leads_pk,$dt_ini_cadastro, $dt_fim_cadastro, $dt_ini_contrato, $dt_fim_contrato, $usuario_cadastro_pk, $ic_status, $tp_contrato,$ds_cpf_cnpj){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        
        $sql ="";
        $sql.="SELECT";
        $sql.="     ct.ds_razao_social ds_empresa,";
        $sql.="     ll.ds_lead ds_cliente,";
        $sql.="     l.ds_lead ds_posto_trabalho,";
        $sql.="     u.ds_usuario,";
        $sql.="     date_format(c.dt_cadastro,'%d/%m/%Y')dt_cadastro,";
        $sql.="     date_format(c.dt_inicio_contrato,'%d/%m/%Y')dt_inicio_contrato,";
        $sql.="     date_format(c.dt_fim_contrato,'%d/%m/%Y')dt_fim_contrato,";
        $sql.="     case c.ic_tipo_contrato when 1 then 'Contrato Novo' when 2 then 'Contrato Aditivo' when 3 then 'Contrato Extra' end ds_tipo_contrato,";
        $sql.="     CASE WHEN c.dt_cancelamento IS NULL THEN 'Ativa' ELSE 'Cancelado' END  ds_status,";
        $sql.="     c.vl_contrato";
        $sql.="   from contratos c";
        $sql.="   INNER JOIN contas ct on c.empresas_pk = ct.pk";
        $sql.="   inner join processos_etapas pe on c.processos_etapas_pk = pe.pk";
        $sql.="   inner join processos p on pe.processos_pk = p.pk";
        $sql.="   inner join leads l on p.leads_pk = l.pk";
        $sql.="   left join leads ll on ll.leads_pai_pk = l.pk";
        $sql.=" inner join usuarios u on c.usuario_cadastro_pk = u.pk";
        $sql.=" WHERE 1=1";
        
        if($empresa_pk!=""){
            $sql.=" and ct.pk = ".$empresa_pk;
         }
        if($leads_clientes_pk!=""){
            $sql.=" and ll.pk = ".$leads_clientes_pk;
         }
        if($leads_pk!=""){
            $sql.=" and l.pk = ".$leads_pk;
         }
         if($ds_cpf_cnpj!=""){
            $sql.=" and l.ds_cpf_cnpj = '".$ds_cpf_cnpj."'";
         }

        if($dt_ini_cadastro!=""){
            $sql.=" and c.dt_cadastro between '".Util::dataYMD($dt_ini_cadastro)." 00:00:00' and '".Util::dataYMD($dt_fim_cadastro)." 23:59:59'";
        }
        if($dt_ini_contrato!=""){
            $sql.=" and c.dt_inicio_contrato >= '".Util::dataYMD($dt_ini_contrato)." 00:00:00'";
        }
        if($dt_fim_contrato!=""){
            $sql.=" and c.dt_fim_contrato <= '".Util::dataYMD($dt_fim_contrato)." 23:59:59'";
        }
        if($usuario_cadastro_pk!=""){
            $sql.=" and u.pk =" .$usuario_cadastro_pk;
        }
        if($ic_status!=""){
            if($ic_status==1){
                $sql.=" and c.dt_cancelamento IS NULL " ;
            }
            if($ic_status==2){
                $sql.=" and c.dt_cancelamento IS NOT NULL " ;
            }
            
        }
        if($tp_contrato!=""){
            $sql.=" and c.ic_tipo_contrato =" .$tp_contrato;
        }

        
        $stmt = $this->pdo->prepare( $sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        $retorno->iTotalDisplayRecords = count($rows);
        $retorno->iTotalRecords = count($rows);

        echo json_encode($retorno);
        exit(0);
    }

public function listarContratos($leads_pk, $empresa_pk, $cliente_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $retorno->message = '';

        $sql ="";
        $sql.="SELECT c.pk,";
        $sql.="    case WHEN c.ic_tipo_contrato =1 THEN";
        $sql.="      concat('FIXO  - Cód:',c.pk,' - Periódo:',date_format(c.dt_inicio_contrato, '%d/%m/%Y'),' - ',date_format(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="     WHEN c.ic_tipo_contrato = 2 THEN";
        $sql.="      concat('Aditivo- Cód:', c.pk ,' - Periódo:',date_format(c.dt_inicio_contrato, '%d/%m/%Y'),' - ',date_format(c.dt_fim_contrato, '%d/%m/%Y'))";
        $sql.="    WHEN c.ic_tipo_contrato =3 THEN";
        $sql.="      concat('EXTRA- Cód:', c.pk , ' - ' ,c.ds_identificacao_area)";        
        $sql.="    END ds_contrato";
        $sql.=" FROM contratos c";
        $sql.="   INNER JOIN processos_etapas pe ON c.processos_etapas_pk = pe.pk";
        $sql.="   INNER JOIN processos p ON pe.processos_pk = p.pk";
        $sql.=" WHERE c.empresas_pk =".$empresa_pk;
        $sql.="   AND p.leads_pk =".$leads_pk;
        $sql.="   AND c.dt_inicio_contrato <= sysdate()"; 
        $sql.="   AND c.dt_fim_contrato >= sysdate()"; 

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';


        return $retorno;
    }
}
