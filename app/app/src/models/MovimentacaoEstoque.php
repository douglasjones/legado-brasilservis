<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class MovimentacaoEstoque {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function excluir($pk){
        Util::execDelete('movimentacao_estoque'," pk = ".$pk,$this->pdo);
    }

    public function salvar($movimentacao_estoque){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio


        $fields = array();
        $fields['leads_pk'] = $movimentacao_estoque['leads_pk'];
        $fields['colaborador_pk'] = $movimentacao_estoque['colaborador_pk'];
        $fields['produtos_itens_pk'] = $movimentacao_estoque['produtos_itens_pk'];
        $fields['qtde'] = $movimentacao_estoque['qtde'];
        $fields['dt_entrega'] = $movimentacao_estoque['dt_entrega'];
        $fields['obs_movimentacao'] = $movimentacao_estoque['obs_movimentacao'];
        $fields['conjunto_material_pk'] = $movimentacao_estoque['conjunto_material_pk'];
        $fields['ic_mateiral_carga'] = $movimentacao_estoque['ic_mateiral_carga'];
        $fields['polos_destino_pk'] = $movimentacao_estoque['polos_destino_pk'];
        $fields['polos_origem_pk'] = $movimentacao_estoque['polos_origem_pk'];
        $fields['grupo_para_movimentacao_pk'] = $movimentacao_estoque['grupo_para_movimentacao_pk'];
        $fields['contratos_pk'] = $movimentacao_estoque['contratos_pk'];
        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($movimentacao_estoque['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
            $pk = Util::execInsert("movimentacao_estoque", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            $fields['dt_devolucao'] = $movimentacao_estoque['dt_devolucao'];
            Util::execUpdate("movimentacao_estoque", $fields, " pk = ".$movimentacao_estoque['pk'],$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados alterados com sucesso';
            $retorno->data = $movimentacao_estoque['pk'];
        }

        return $retorno;

    }
    public function listar_por_pk($leads_pk,$colaborador_pk,$conjunto_material_pk,$contratos_pk){
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
        if($conjunto_material_pk!="" ){
            $sql ="";
            $sql.="select me.pk, me.dt_cadastro, me.usuario_cadastro_pk, me.dt_ult_atualizacao, me.usuario_ult_atualizacao_pk ";
            $sql.="       ,me.leads_pk ";
            $sql.="       ,me.colaborador_pk ";
            $sql.="       ,me.produtos_itens_pk ";
            $sql.="       ,CONCAT( pi.pk, '-', p.ds_produto) as ds_produto_item ";
            $sql.="       ,me.qtde ";
            $sql.="       ,me.obs_movimentacao obs_material ";
            $sql.="       ,me.conjunto_material_pk";
            $sql.="       ,me.ic_mateiral_carga";
            $sql.="       ,case me.ic_mateiral_carga when 1 then 'Sim' when 2 then 'Não' end ds_ic_mateiral_carga";
            $sql.="       ,date_format(me.dt_entrega,'%d/%m/%Y') dt_entrega ";
            $sql.="       ,date_format(me.dt_devolucao,'%d/%m/%Y') dt_devolucao ";
            $sql.="       ,p.ds_produto ";
            $sql.="       ,p.pk produtos_pk ";
            $sql.="       ,cp.pk categorias_produto_pk ";
            $sql.="       ,cp.ds_categoria ds_categorias_produto";
            $sql.="       ,pi.pk produtos_itens_pk ";
            $sql.="       ,pi.ds_n_serie ";
            $sql.="  from movimentacao_estoque me ";
            $sql.="     INNER JOIN produtos_itens pi ON me.produtos_itens_pk = pi.pk";
            $sql.="     INNER JOIN produtos p on p.pk = pi.produtos_pk";
            $sql.="     INNER JOIN categorias_produto cp on p.categorias_produto_pk = cp.pk ";
            $sql.=" where 1=1 ";
            if($leads_pk != ""){
                $sql.=" and me.leads_pk = ".$leads_pk;
            }
            if($colaborador_pk != ""){
                $sql.=" and me.colaborador_pk = ".$colaborador_pk;
            }
            if($contratos_pk != ""){
                $sql.=" and me.contratos_pk = ".$contratos_pk;
            }
            if($conjunto_material_pk != ""){
                $sql.=" and me.conjunto_material_pk = ".$conjunto_material_pk;
            }
            $sql.=" order by me.leads_pk desc ";


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
    public function listar_impressao($leads_pk,$colaborador_pk,$conjunto_material_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        if($conjunto_material_pk!="" ){
            $sql ="";
            $sql.="select me.pk, me.dt_cadastro, me.usuario_cadastro_pk, me.dt_ult_atualizacao, me.usuario_ult_atualizacao_pk ";
            $sql.="       ,me.leads_pk ";
            $sql.="       ,me.colaborador_pk ";
            $sql.="       ,CONCAT( pi.pk, '-', p.ds_produto,'-', pi.ds_n_serie) as ds_produto_item";
            $sql.="       ,me.produtos_itens_pk ";
            $sql.="       ,me.qtde ";
            $sql.="       ,me.ic_mateiral_carga";
            $sql.="       ,me.obs_movimentacao obs_material ";
            $sql.="       ,me.conjunto_material_pk";
            $sql.="       ,date_format(me.dt_entrega,'%d/%m/%Y') dt_entrega ";
            $sql.="       ,date_format(me.dt_devolucao,'%d/%m/%Y') dt_devolucao ";
            $sql.="       ,p.ds_produto ";
            $sql.="       ,p.pk produtos_pk ";
            $sql.="       ,cp.pk categorias_produto_pk ";
            $sql.="       ,cp.ds_categoria ds_categorias_produto";
            $sql.="       ,pi.pk produtos_itens_pk ";
            $sql.="       ,pi.ds_n_serie ";
            $sql.="  from movimentacao_estoque me ";
            $sql.="     INNER JOIN produtos_itens pi ON me.produtos_itens_pk = pi.pk";
            $sql.="     INNER JOIN produtos p on p.pk = pi.produtos_pk";
            $sql.="     INNER JOIN categorias_produto cp on p.categorias_produto_pk = cp.pk ";
            $sql.=" where 1=1 ";
            if($leads_pk != "" && $leads_pk != "null"){
                $sql.=" and me.leads_pk = ".$leads_pk;
            }
            if($colaborador_pk != ""){
                $sql.=" and me.colaborador_pk = ".$colaborador_pk;
            }
            if($conjunto_material_pk != ""){
                $sql.=" and me.conjunto_material_pk = ".$conjunto_material_pk;
            }
            $sql.=" order by me.leads_pk desc ";


            $stmt = $this->pdo->prepare( $sql );
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

    public function RelatorioEstoqueMinimoAtual($categorias_pk,$produtos_pk,$leads_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select ee.pk,";
        $sql.="       p.ds_produto";
        $sql.="       ,c.ds_categoria";
        $sql.="       ,p.pk produtos_pk";
        $sql.="       ,(ee.qtde) qtde_inicial";
        $sql.="       ,(p.qtde_minima) qtde_minima";
        $sql.="       ,date_format(ee.dt_cadastro,'%d/%m/%Y')dt_cadastro_estoque";
        $sql.="  from produtos p";
        $sql.="       left join entrada_estoque ee  ON ee.produtos_pk = p.pk";
        $sql.="       left join produtos_itens pi ON p.pk = pi.produtos_pk";
        $sql.="       left join categorias_produto c ON p.categorias_produto_pk = c.pk";
        $sql.="       left join movimentacao_estoque me ON pi.pk = me.produtos_itens_pk";

        $sql.=" where 1=1 ";
       
        if($categorias_pk!=""){
            $sql.=" and c.pk = ".$categorias_pk;
        }
        if($produtos_pk!=""){
            $sql.=" and p.pk = ".$produtos_pk;
        }
        if($leads_pk != ""){
            $sql.=" and me.leads_pk = ".$leads_pk;
        }
       
        $sql.=" group by p.pk";
        $sql.=" order by p.ds_produto ";
       
        $stmt = $this->pdo->prepare( $sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        $retorno->iTotalDisplayRecords = count($rows);
        $retorno->iTotalRecords = count($rows);

        return $retorno;

    }

    public function RelatorioEstoque($categorias_pk,$produtos_pk,$entrada_estoque_pk,$leads_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="SELECT c.ds_categoria,";
        $sql.="       p.ds_produto,";        
        $sql.="       COUNT(DISTINCT me.pk) qtde_movimentado";
        $sql.="  FROM movimentacao_estoque me";
        $sql.="       INNER JOIN produtos_itens pi ON me.produtos_itens_pk = pi.pk";
        $sql.="       INNER JOIN produtos p ON pi.produtos_pk = p.pk";
        $sql.="       LEFT JOIN categorias_produto c ON p.categorias_produto_pk = c.pk";     
        $sql.=" where 1=1 ";
        if($categorias_pk!=""){
            $sql.=" and c.pk = ".$categorias_pk;
        }
        if($produtos_pk!=""){
            $sql.=" and p.pk = ".$produtos_pk;
        }
        if($leads_pk!=""){
            $sql.=" and me.leads_pk = ".$leads_pk;
        }
        if($entrada_estoque_pk!=""){
            //$sql.=" and pi.entrada_estoque_pk = ".$entrada_estoque_pk;
        }
        if($produtos_pk!=""){
            $sql.=" GROUP BY p.pk";
        }

        $stmt = $this->pdo->prepare( $sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        $retorno->iTotalDisplayRecords = count($rows);
        $retorno->iTotalRecords = count($rows);

        return $retorno;

    }
    
    public function relCompraMovimentacaoLead($leads_pk, $tipo_operacao_pk, $produtos_pk,$categorias_produto_pk,$dt_ini_compra,$dt_fim_compra){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql = "";
        $sql .= "SELECT me.pk, ";
        $sql .= "       cpc.ds_categoria, ";
        $sql .= "       p.ds_produto, ";
        $sql .= "       'Movimentação de Estoque' tipo_operacao, ";
        $sql .= "       concat('-', sum(pi2.qtde)) qtde, ";
        $sql .= "       l.ds_lead, ";
        $sql .= "       sum(ee.vl_unitario) valor, ";
        $sql .= "       DATE_FORMAT(me.dt_cadastro, '%d/%m/%Y') dt_movimentacao_compra, ";
        $sql .= "       ''  ds_numero_nota";
        $sql .= "   FROM movimentacao_estoque me ";
        $sql .= "        INNER JOIN produtos_itens pi2 ON me.produtos_itens_pk = pi2.pk ";
        $sql .= "        INNER JOIN produtos p ON pi2.produtos_pk = p.pk ";
        $sql .= "        INNER JOIN leads l ON me.leads_pk = l.pk ";
        $sql .= "         LEFT JOIN categorias_produto cpc ON p.categorias_produto_pk = cpc.pk ";
        $sql .= "        INNER JOIN entrada_estoque ee ON ee.produtos_pk = p.pk ";
        $sql .= "  WHERE 1=1";
        if($categorias_produto_pk!=""){
            $sql.=" and cpc.pk = ".$categorias_produto_pk;
        }
        if($leads_pk!=""){
            $sql.=" and l.pk = ".$leads_pk;
        }
        if($tipo_operacao_pk==1){
            $sql.=" and me.pk IS NULL";
        }
        if($produtos_pk!=""){
            $sql.=" and p.pk = ".$produtos_pk;
        }
        if($dt_ini_compra != '' and $dt_fim_compra != ""){
            $sql.=" and me.dt_cadastro BETWEEN '".Util::DataYMD($dt_ini_compra)." 00:00:00' AND '".Util::DataYMD($dt_fim_compra)." 23:59:59'";
        }
        $sql.=" GROUP BY p.pk, DATE_FORMAT(me.dt_cadastro, '%d/%m/%Y'), l.pk";

        $sql .= "   UNION SELECT c.pk, ";
        $sql .= "                cpc.ds_categoria, ";
        $sql .= "                p.ds_produto, ";
        $sql .= "                'Compras' tipo_operacao, ";
        $sql .= "                sum(pi2.qtde) qtde, ";
        $sql .= "                l.ds_lead, ";
        $sql .= "                sum(c.vl_pagamento) valor, ";
        $sql .= "                DATE_FORMAT(c.dt_cadastro, '%d/%m/%Y') dt_movimentacao_compra, ";
        $sql .= "                c.ds_numero_nota ";
        $sql .= "   FROM compras c ";
        $sql .= "        LEFT JOIN produtos_itens pi2 ON pi2.compras_pk = c.pk ";
        $sql .= "        LEFT JOIN produtos p ON pi2.produtos_pk = p.pk ";
        $sql .= "        LEFT JOIN categorias_produto cpc ON p.categorias_produto_pk = cpc.pk ";
        $sql .= "       INNER JOIN leads l ON c.centro_custo_pk = l.pk ";
        $sql .= "        LEFT JOIN entrada_estoque ee ON ee.produtos_pk = p.pk ";
        $sql .= "  WHERE 1=1";
        if($categorias_produto_pk!=""){
            $sql.=" and cpc.pk = ".$categorias_produto_pk;
        }
        if($leads_pk!=""){
            $sql.=" and l.pk = ".$leads_pk;
        }
        if($tipo_operacao_pk==2){
            $sql.=" and c.pk IS NULL";
        }
        if($produtos_pk!=""){
            $sql.=" and p.pk = ".$produtos_pk;
        }
        if($dt_ini_compra != '' and $dt_fim_compra != ""){
            $sql.=" and c.dt_cadastro BETWEEN '".Util::DataYMD($dt_ini_compra)." 00:00:00' AND '".Util::DataYMD($dt_fim_compra)." 23:59:59'";
        }
        $sql.=" GROUP BY p.pk, DATE_FORMAT(c.dt_cadastro, '%d/%m/%Y'), l.pk";

        
        $stmt = $this->pdo->prepare( $sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        $retorno->iTotalDisplayRecords = count($rows);
        $retorno->iTotalRecords = count($rows);

        return $retorno;

    }
}
