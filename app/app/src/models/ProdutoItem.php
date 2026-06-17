<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class ProdutoItem {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function excluir($pk){
        Util::execDelete('produtos_itens'," pk = ".$pk,$this->pdo);
    }

    public function salvar($produto_iten){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_n_serie'] = $produto_iten['ds_n_serie'];
        $fields['qtde'] = $produto_iten['qtde'];
        $fields['vl_item'] = $produto_iten['vl_item'];
        $fields['produtos_pk'] = $produto_iten['produtos_pk'];
        $fields['entrada_estoque_pk'] = $produto_iten['entrada_estoque_pk'];
        $fields['ic_entrega'] = $produto_iten['ic_entrega'];
        $fields['compras_pk'] = $produto_iten['compras_pk'];

        /*f($produto_iten['dt_baixa']!=""){
            $fields['dt_baixa'] = Util::DataYMD($produto_iten['dt_baixa']);
            $fields['usuario_baixa_pk'] = $_SESSION['session_user']['par1'];
        }*/
        
       /* $fields['obs_baixa'] = $produto_iten['obs_baixa'];
        $fields['ds_identificacao'] = $produto_iten['ds_identificacao'];
        $fields['polos_pk'] = $produto_iten['polos_pk'];
        $fields['dt_cancelamento'] = $produto_iten['dt_cancelamento'];
        $fields['ds_motivo_cancelamento'] = $produto_iten['ds_motivo_cancelamento'];

        $fields['ic_entrega'] = $produto_iten['ic_entrega'];*/
        

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
        
        if($produto_iten['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
            
            $pk = Util::execInsert("produtos_itens", $fields,$this->pdo);
            
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("produtos_itens", $fields, " pk = ".$produto_iten['pk'],$this->pdo);
            $pk = $produto_iten['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;
    }

    public function listarPorLeadsPk($leads_pk,$colaborador_pk){


        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pi.pk";

        $sql.="  from produtos_itens pi";
        $sql.="  inner join movimentacao_estoque me on pi.pk = me.produtos_itens_pk";
        $sql.="  where 1=1";
        if($leads_pk!=""){
            $sql.=" and me.leads_pk = $leads_pk ";
        }
        if($colaborador_pk!=""){
            $sql.=" and me.colaborador_pk = $colaborador_pk ";
        }

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }

    public function listarPorPk($pk){


        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql.="       ,ds_n_serie ";
        $sql.="       ,qtde ";
        $sql.="       ,vl_item ";
        $sql.="       ,produtos_pk ";
        $sql.="       ,entrada_estoque_pk";
        $sql.="       ,dt_baixa";
        $sql.="       ,obs_baixa";
        $sql.="       ,usuario_baixa_pk";
        $sql.="       ,ic_entrega";

        $sql.="  from produtos_itens ";
        $sql.=" where pk = $pk ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }
    public function listarPorPkProduto($produtos_pk,$produtos_itens_pk,$strProdutoGrid){


        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio


        //VERIFICA OS PRODUTOS_ITENS QUE JA FORAM MOVIMENTADOS
        $sql="";
        $sql.="select me.produtos_itens_pk from movimentacao_estoque me inner join produtos_itens pi on me.produtos_itens_pk = pi.pk";
        $sql.=" where 1=1 ";
        if($produtos_pk!=""){
            $sql.=" and pi.produtos_pk  = ".$produtos_pk;
        }
        if($produtos_itens_pk!=""){
            $sql.=" and me.produtos_itens_pk not in (".$produtos_itens_pk.")";
        }

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query_movimentacao_estoque = $stmt->fetchAll(\PDO::FETCH_ASSOC);



        //CARREGA A QUANTIDADE DE PRODUTOS CADASTRADA NO ESTOQUE.
        $sql="";
        $sql.="select sum(qtde)qtde from entrada_estoque where 1=1";
        if($produtos_pk!=""){
            $sql.=" and produtos_pk = ".$produtos_pk;
        }

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query_qtde = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $sql ="select pi.pk ";
        $sql.="     ,date_format(pi.dt_cadastro,'%d/%m/%Y') dt_cadastro ";
        $sql.="      ,CONCAT( pi.pk, '-', p.ds_produto,'-', pi.ds_n_serie) as ds_produto_item";
        $sql.="      ,concat(pi.pk,'-',p.ds_produto)ds_produto_item";
        $sql.="     ,pi.usuario_cadastro_pk ";
        $sql.="     ,date_format(pi.dt_ult_atualizacao,'%d/%m/%Y') dt_ult_atualizacao ";
        $sql.="     ,pi.usuario_ult_atualizacao_pk ";
        $sql.="     ,pi.ds_n_serie ";
        $sql.="     ,pi.qtde ";
        $sql.="     ,pi.vl_item ";
        $sql.="     ,pi.produtos_pk ";
        $sql.="     ,pi.entrada_estoque_pk";
        $sql.="       ,pi.dt_baixa";
        $sql.="       ,pi.obs_baixa";
        $sql.="       ,pi.usuario_baixa_pk";
        $sql.="  from produtos_itens pi ";
        $sql.="     inner join produtos p on pi.produtos_pk = p.pk";
        $sql.="     inner join entrada_estoque ee on pi.entrada_estoque_pk = ee.pk";
        $sql.=" where 1=1";
        if($produtos_pk!=""){
            $sql.=" and pi.produtos_pk = ".$produtos_pk;
        }
        if(count($query_movimentacao_estoque) > 0){
            $sql.=" and pi.pk not in (";
            foreach($query_movimentacao_estoque as $v){
                $sql.=$v['produtos_itens_pk'].",";
            }
            $sql.=" 0)";
        }
        if($strProdutoGrid!=""){
            $sql.=" and pi.pk ".$strProdutoGrid;
        }
        if($query_qtde[0]['qtde']==null){
            $sql.=" limit 0";
        }
        else{
            $sql.=" limit ".$query_qtde[0]['qtde'];
        }

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }

    public function listarPorProdutosQtde($produtos_pk,$qtde){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        //VERIFICA OS PRODUTOS_ITENS QUE JA FORAM MOVIMENTADOS
        $sql="";
        $sql.="select me.produtos_itens_pk, CONCAT( pi.pk, '-', p.ds_produto) as ds_produto_item ";
        $sql.=" from movimentacao_estoque me";
        $sql.=" inner join produtos_itens pi on me.produtos_itens_pk = pi.pk";
        $sql.=" inner join produtos p on pi.produtos_pk = p.pk";
        $sql.=" where 1=1 ";
        if($produtos_pk!="") {
            $sql .= " and pi.produtos_pk  = " . $produtos_pk;
        }


        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query_movimentacao_estoque = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $quantidade = $qtde;

        $sql ="";
        $sql.="select pi.pk ";
        $sql.="     ,date_format(pi.dt_cadastro,'%d/%m/%Y') dt_cadastro ";
        $sql.="     ,pi.usuario_cadastro_pk ";
        $sql.="     ,date_format(pi.dt_ult_atualizacao,'%d/%m/%Y') dt_ult_atualizacao ";
        $sql.="     ,pi.usuario_ult_atualizacao_pk ";
        $sql.="     , CONCAT( pi.pk, '-', p.ds_produto) as ds_produto_item";
        $sql.="     ,pi.ds_n_serie ";
        $sql.="     ,pi.qtde ";
        $sql.="     ,pi.vl_item ";
        $sql.="     ,pi.produtos_pk ";
        $sql.="     ,pi.entrada_estoque_pk";
        $sql.="     ,p.ds_produto ";
        $sql.="       ,pi.dt_baixa";
        $sql.="       ,pi.obs_baixa";
        $sql.="       ,pi.usuario_baixa_pk";
        $sql.="  from produtos_itens pi ";
        $sql.="     inner join produtos p on pi.produtos_pk = p.pk";
        $sql.="     inner join entrada_estoque ee on pi.entrada_estoque_pk = ee.pk";
        $sql.=" where 1=1 ";
        if($produtos_pk != ""){
            $sql.=" and pi.produtos_pk  = ".$produtos_pk;
        }

        if(count($query_movimentacao_estoque) > 0){
            $sql.=" and pi.pk not in (";
            for($i=0;$i < count($query_movimentacao_estoque);$i++){
                $sql.=$query_movimentacao_estoque[$i]['produtos_itens_pk'].",";
            }
            $sql.=" 0)";
        }


        $sql.=" order by pi.pk asc ";
        if($quantidade > 0){
            $sql.=" limit ".$quantidade;
        }
        else{
            $sql.=" limit 0";
        }

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }

    public function listarProdutoEstoqueNSerie($entrada_estoque_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_n_serie ";
        $sql.="       ,qtde ";
        $sql.="       ,vl_item ";
        $sql.="       ,produtos_pk ";
        $sql.="       ,entrada_estoque_pk";
        $sql.="       ,dt_baixa";
        $sql.="       ,obs_baixa";
        $sql.="       ,usuario_baixa_pk";
        $sql.="  from produtos_itens ";
        $sql.=" where 1=1 ";
        if($entrada_estoque_pk != ""){
            $sql.=" and entrada_estoque_pk =".$entrada_estoque_pk;
        }
        $sql.=" and ds_n_serie is not null";
        $sql.=" order by ds_n_serie asc ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }

    public function listarProdutoEstoque($entrada_estoque_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_n_serie ";
        $sql.="       ,qtde ";
        $sql.="       ,vl_item ";
        $sql.="       ,produtos_pk ";
        $sql.="       ,entrada_estoque_pk";
        $sql.="       ,dt_baixa";
        $sql.="       ,obs_baixa";
        $sql.="       ,usuario_baixa_pk";
        $sql.="  from produtos_itens ";
        $sql.=" where 1=1 ";
        if($entrada_estoque_pk != ""){
            $sql.=" and entrada_estoque_pk =".$entrada_estoque_pk;
        }
        $sql.=" order by ds_n_serie asc ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }
    public function listarPorCompra($compras_pk){
        //PAGINAÇÃO
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
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
        if($compras_pk!=""){
            $sql ="";
            $sql.="select pi.pk, pi.dt_cadastro, pi.usuario_cadastro_pk, pi.dt_ult_atualizacao, pi.usuario_ult_atualizacao_pk ";
            $sql.="       ,pi.ds_n_serie ";
            $sql.="       ,pi.qtde ";
            $sql.="       ,pi.vl_item ";
            $sql.="       ,pi.produtos_pk ";
            $sql.="       ,pi.entrada_estoque_pk";
            $sql.="       ,pi.dt_baixa";
            $sql.="       ,pi.obs_baixa";
            $sql.="       ,pi.usuario_baixa_pk";
            $sql.="       ,pi.ic_entrega";
            $sql.="       ,case pi.ic_entrega when 1 then 'Não' when 2 then 'Sim' end ds_entrega";
            $sql.="       ,cp.ds_categoria";
            $sql.="       ,p.categorias_produto_pk";
            $sql.="       ,p.ds_produto";
            $sql.="  from produtos_itens pi";
            $sql.="  inner join produtos p on pi.produtos_pk = p.pk";
            $sql.="  inner join categorias_produto cp on p.categorias_produto_pk = cp.pk";
            $sql.=" where 1=1 ";

            $sql.=" and pi.compras_pk = ".$compras_pk;

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

}
