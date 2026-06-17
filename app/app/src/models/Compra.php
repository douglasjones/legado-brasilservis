<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;
use Throwable;

class Compra{

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function excluir($pk){
        Util::execDelete('produtos_itens', ' compras_pk='.$pk, $this->pdo);
        Util::execDelete('documentos', ' compras_pk='.$pk, $this->pdo);
        Util::execDelete('lancamentos', ' compras_pk='.$pk, $this->pdo);
        Util::execDelete('compras', ' pk='.$pk, $this->pdo);
    }

    public function listarGrid($fornecedor_pk,$categorias_pk,$ds_numero_nota,$empresas_pk,$dt_cadastro_ini,$dt_cadastro_fim){
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
                            cp.pk LIKE '%".$pesq."%' OR
                            f.ds_fornecedor LIKE '%".$pesq."%' OR
                            c.ds_categoria LIKE '%".$pesq."%' OR
                            cp.ds_numero_nota LIKE '%".$pesq."%' OR
                            ct.ds_conta LIKE '%".$pesq."%'
                        )";
        }


        $sql ="";
        $sql.="select cp.pk, date_format(cp.dt_cadastro,'%d/%m/%Y')dt_cadastro, cp.usuario_cadastro_pk, cp.dt_ult_atualizacao, cp.usuario_ult_atualizacao_pk ";
        $sql.="       ,cp.fornecedor_pk ";
        $sql.="       ,cp.categoria_pk ";
        $sql.="       ,cp.conta_pk ";
        $sql.="       ,date_format(cp.dt_pagamento,'%d/%m/%Y')dt_pagamento";
        $sql.="       ,cp.vl_pagamento ";
        $sql.="       ,cp.metodos_pagamento_pk ";
        $sql.="       ,cp.qtde_parcelas ";
        $sql.="       ,cp.ds_numero_nota,cp.ds_link_notafiscal ";
        $sql.="       ,cp.ds_link_notafiscal ";
        $sql.="       ,date_format(cp.dt_notafiscal,'%d/%m/%Y')dt_notafiscal";
        $sql.="       ,cp.vl_notafiscal ";
        $sql.="       ,cp.vl_frete ";
        $sql.="       ,date_format(cp.dt_entrega,'%d/%m/%Y')dt_entrega";
        $sql.="       ,cp.ic_entregue ";
        $sql.="       ,cp.obs ";
        $sql.="       ,cp.grupo_lancamento_centro_custo_pk ";
        $sql.="       ,cp.centro_custo_pk ";
        $sql.="       ,c.ds_categoria";
        $sql.="       ,f.ds_fornecedor";
        $sql.="       ,ct.ds_conta";

        $sql.="  from compras cp";
        $sql.="       left join categorias_produto c on cp.categoria_pk = c.pk";
        $sql.="       inner join fornecedor f on cp.fornecedor_pk = f.pk";
        $sql.="       inner join contas ct on cp.conta_pk = ct.pk";
        $sql.=" where 1=1 ";
        $sql.=$search;
        if($fornecedor_pk!=""){
            $sql.=" and cp.fornecedor_pk = ".$fornecedor_pk;
        }
        if($categorias_pk!=""){
            $sql.=" and cp.categoria_pk= ".$categorias_pk;
        }
        if($ds_numero_nota!=""){
            $sql.=" and cp.ds_numero_nota like '%".$ds_numero_nota."%'";
        }
        if($empresas_pk!=""){
            $sql.=" and cp.conta_pk= ".$empresas_pk;
        }
        if($dt_cadastro_ini!=""){
            $sql.=" and cp.dt_pagamento between '".Util::DataYMD($dt_cadastro_ini)."' and '".Util::DataYMD($dt_cadastro_fim)."'";
        }

        $sql.=" order by cp.dt_pagamento asc ";

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

    public function salvar($compra){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['fornecedor_pk'] = $compra['fornecedor_pk'];
        $fields['categoria_pk'] = $compra['categoria_pk'];
        $fields['conta_pk'] = $compra['conta_pk'];
        if($compra['dt_pagamento']!=""){
            $fields['dt_pagamento'] = Util::DataYMD($compra['dt_pagamento']);
        }

        $fields['vl_pagamento'] = $compra['vl_pagamento'];
        $fields['metodos_pagamento_pk'] = $compra['metodos_pagamento_pk'];
        $fields['qtde_parcelas'] = $compra['qtde_parcelas'];
        $fields['ds_numero_nota'] = $compra['ds_numero_nota'];;

        $fields['vl_notafiscal'] = $compra['vl_notafiscal'];
        $fields['vl_frete'] = $compra['vl_frete'];

        if($compra['dt_entrega']!=""){
            $fields['dt_entrega'] = Util::DataYMD($compra['dt_entrega']);
        }
        $fields['grupo_lancamento_centro_custo_pk'] = $compra['grupo_lancamento_centro_custo_pk'];
        $fields['centro_custo_pk'] = $compra['centro_custo_pk'];
        $fields['ic_entregue'] = $compra['ic_status'];
        $fields['compra_solicitacao_pk'] = $compra['compra_solicitacao_pk'];


        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($compra['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("compras", $fields,$this->pdo);

            
        }
        else{
            Util::execUpdate("compras", $fields, " pk = ".$compra['pk'],$this->pdo);
            $pk = $compra['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }

        $compras_pk = $pk;
        $qtde_parcelas = $compra['qtde_parcelas'];
        $dt_pagamento = $compra['dt_pagamento'];
        $conta_pk = $compra['conta_pk'];
        $fornecedor_pk = $compra['fornecedor_pk'];
        $centro_custo_pk = $compra['centro_custo_pk'];
        $grupo_lancamento_centro_custo_pk = $compra['grupo_lancamento_centro_custo_pk'];
        $vl_notafiscal = $compra['vl_notafiscal'];
        $metodos_pagamento_pk = $compra['metodos_pagamento_pk'];
        $ic_status = $compra['ic_status'];
      
        if($ic_status == 1){
            
            //SALVAR LANÇAMENTOS
            if($compra['pk']==""){
                if($qtde_parcelas>1){
                    for($i=0;$i<$qtde_parcelas;$i++){

                        $dt_parcelas = $this->listarDataDiff($dt_pagamento,$i);


                        $conta_bancaria_pk =(new Lancamento($this->pdo))->listaContaEmpresa($conta_pk);
                        $c_bancaria = "";
                        if(!empty($conta_bancaria_pk->data)){

                            $c_bancaria = $conta_bancaria_pk->data['pk'];
                        }
                        $lancamento = [
                            "pk"=>"",
                            "operacao_pk"=>1,
                            "tipos_operacao_pk"=>1020,
                            "empresas_pk"=>$conta_pk,
                            "contas_bancarias_pk"=>$c_bancaria,
                            "ds_lancamento"=>"Compras",
                            "tipo_grupo_pk"=>3,
                            "grupo_leancamento_pk"=>$fornecedor_pk,
                            "ds_num_documento"=>$compra['ds_numero_nota'],
                            "grupo_lancamento_centro_custo_pk"=>$grupo_lancamento_centro_custo_pk,

                            "vl_lancamento"=>($vl_notafiscal/$qtde_parcelas),
                            "metodos_pagamento_pk"=>($metodos_pagamento_pk),
                            "ic_status_pagamento"=>2,
                            "compras_pk"=>$compras_pk,
                            "dt_vencimento"=>$dt_parcelas['dt_pagamento'],
                        ];
                        (new Lancamento($this->pdo))->salvarCompra($lancamento);
                    }
                }
                else{
                    $conta_bancaria_pk =(new Lancamento($this->pdo))->listaContaEmpresa($conta_pk);

                    $c_bancaria = "";
                    if(!empty($conta_bancaria_pk->data)){

                        $c_bancaria = $conta_bancaria_pk->data['pk'];
                    }
                    $lancamento = [
                        "pk"=>"",
                        "operacao_pk"=>1,
                        "tipos_operacao_pk"=>1020,
                        "empresas_pk"=>$conta_pk,
                        "contas_bancarias_pk"=>$c_bancaria,
                        "ds_lancamento"=>"Compras",
                        "tipo_grupo_pk"=>3,
                        "grupo_leancamento_pk"=>$fornecedor_pk,
                        "ds_num_documento"=>$compra['ds_numero_nota'],
                        "grupo_lancamento_centro_custo_pk"=>$grupo_lancamento_centro_custo_pk,

                        "vl_lancamento"=>($vl_notafiscal),
                        "metodos_pagamento_pk"=>($metodos_pagamento_pk),
                        "ic_status_pagamento"=>2,
                        "compras_pk"=>$compras_pk,
                        "dt_vencimento"=>($dt_pagamento),
                    ];
                    (new Lancamento($this->pdo))->salvarCompra($lancamento);

                }

            }
        }
        $retorno->status = true;
        $retorno->message = 'Dados cadastrados com sucesso';
        $retorno->data = $compras_pk;
        return $retorno;

    }
    public function salvarProduto($pk, $compras_pk, $produtos_pk, $qtde, $vl_item, $ic_entrega, $ic_status,$fornecedor_pk){
        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio
            $pk_entrada_estoque = '';
            if($pk!=""){
                $queryAnt = (new ProdutoItem($this->pdo))->listarPorPk($pk);

                $pk_entrada_estoque = $queryAnt->data[0]['entrada_estoque_pk'];
            }

            $produto_item = [
                "pk"=> $pk,
                "ds_n_serie"=> "",
                "vl_item"=> $vl_item,
                "qtde"=> $qtde,
                "ic_entrega"=> $ic_entrega,
                "produtos_pk"=> $produtos_pk,
                "entrada_estoque_pk"=> "",
                "compras_pk"=>$compras_pk
            ];

            (new ProdutoItem($this->pdo))->salvar($produto_item);

            //VERIFICAR SE FAZ CADASTRO EM ENTRADA ESTOQUE
            if($ic_entrega==2){
                if($ic_status == 1){


                    $entrada_estoque = [
                        "pk"=> $pk_entrada_estoque,
                        "ds_n_ordem"=> "",
                        "obs_entrada_estoque"=> "",
                        "fornecedor_pk"=> $fornecedor_pk,
                        "produtos_pk"=> $produtos_pk,
                        "qtde"=>$qtde,
                        "vl_unitario"=>$vl_item,

                    ];

                    $entrada_estoque_pk = (new EntradaEstoque($this->pdo))->salvar($entrada_estoque);

                    if($qtde!=""){
                        for($i = 0; $i < $qtde; $i++){
                            $produto_item = [
                                "pk"=> $pk,
                                "ds_n_serie"=> "",
                                "vl_item"=> $vl_item,
                                "qtde"=> "",
                                "ic_entrega"=> "",
                                "produtos_pk"=> $produtos_pk,
                                "compras_pk"=>"",
                                "entrada_estoque_pk"=> $entrada_estoque_pk->data
                            ];

                            (new ProdutoItem($this->pdo))->salvar($produto_item);

                        }
                    }
                }

            }
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = [];
            return $retorno;
        }
        catch(Throwable $th){
            print_r($th->getMessage());
            die();
        }
        
    }

    public function listarDataDiff($data_base,$i){

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql="SELECT DATE_ADD('".Util::DataYMD($data_base)."', INTERVAL ".($i)." MONTH)dt_pagamento";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows[0];
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno->data;
    }

    public function listarTodosAtivo(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_curso ";
        $sql.="       ,ic_status ";

        $sql.="  from cursos ";
        $sql.=" where 1=1 ";
        $sql.=" and ic_status = 1";
        $sql.=" order by ds_curso asc ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function listarPorPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql.="       ,fornecedor_pk ";
        $sql.="       ,categoria_pk ";
        $sql.="       ,conta_pk ";
        $sql.="       ,date_format(dt_pagamento,'%d/%m/%Y') dt_pagamento";
        $sql.="       ,vl_pagamento ";
        $sql.="       ,metodos_pagamento_pk ";
        $sql.="       ,qtde_parcelas ";
        $sql.="       ,ds_numero_nota";
        $sql.="       ,ds_link_notafiscal";
        $sql.="       ,date_format(dt_notafiscal,'%d/%m/%Y') dt_notafiscal";
        $sql.="       ,vl_notafiscal ";
        $sql.="       ,vl_frete ";
        $sql.="       ,date_format(dt_entrega,'%d/%m/%Y') dt_entrega";
        $sql.="       ,ic_entregue ";
        $sql.="       ,obs ";
        $sql.="       ,grupo_lancamento_centro_custo_pk ";
        $sql.="       ,centro_custo_pk ";
        $sql.="       ,ic_entregue ";

        $sql.="  from compras ";
        $sql.=" where pk = $pk ";


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if(count($query) > 0){
            for($i = 0; $i < count($query); $i++){
                $mysql_data[] = array(
                    "pk" => $query[$i]["pk"],
                    "fornecedor_pk"=>$query[$i]['fornecedor_pk'],
                    "categoria_pk"=>$query[$i]['categoria_pk'],
                    "conta_pk"=>$query[$i]['conta_pk'],
                    "dt_pagamento"=>$query[$i]['dt_pagamento'],
                    "vl_pagamento"=>number_format($query[$i]['vl_pagamento'] , 2, ',', '.'),
                    "metodos_pagamento_pk"=>$query[$i]['metodos_pagamento_pk'],
                    "qtde_parcelas"=>$query[$i]['qtde_parcelas'],
                    "ds_numero_nota"=>$query[$i]['ds_numero_nota'],
                    "ds_link_notafiscal"=>$query[$i]['ds_link_notafiscal'],
                    "dt_notafiscal"=>$query[$i]['dt_notafiscal'],
                    "vl_notafiscal"=>number_format($query[$i]['vl_notafiscal'] , 2, ',', '.'),
                    "vl_frete"=>number_format($query[$i]['vl_frete'] , 2, ',', '.'),
                    "dt_entrega"=>$query[$i]['dt_entrega'],
                    "ic_entregue"=>$query[$i]['ic_entregue'],
                    "obs"=>$query[$i]['obs'],
                    "grupo_lancamento_centro_custo_pk"=>$query[$i]['grupo_lancamento_centro_custo_pk'],
                    "ic_status"=>$query[$i]['ic_entregue'],
                    "centro_custo_pk"=>$query[$i]['centro_custo_pk']
                );
            }
        }
        else{
            $mysql_data = [];
        }

        $retorno->data = $mysql_data;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;

    }

    public function relControleCompra($empresa_pk,$fornecedor_pk,$categoria_pk,$tipo_grupo_centro_custo_pk, $grupo_lancamento_centro_custo_pk, $ic_status, $dt_ini_cad, $dt_fim_cad, $dt_ini_compra, $dt_fim_compra){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        
        $sql="select  ct.ds_razao_social ds_empresa,";
        $sql.="                f.ds_fornecedor ds_fornecedor,";
        $sql.="                c.compra_solicitacao_pk,";
        $sql.="                cat.ds_categoria ds_categoria,";
        $sql.="                date_format(c.dt_cadastro, '%d/%m/%Y')dt_cadastro,";
        $sql.="                date_format(c.dt_pagamento, '%d/%m/%Y')dt_pagamento,";
        $sql.="                date_format(c.dt_entrega, '%d/%m/%Y')dt_entrega,";
        $sql.="                c.vl_pagamento,";
        $sql.="                c.ds_numero_nota,";
        $sql.="                 u.ds_usuario ds_usuario_cadastro,";
        $sql.="                 c.grupo_lancamento_centro_custo_pk,";
        $sql.="                c.centro_custo_pk,";
        $sql.="                 l.ds_lead,";
        $sql.="                cl.ds_colaborador,";
        $sql.="                CASE c.centro_custo_pk WHEN 1 then 'Posto de Trabalho' when 2 then 'Colaboradores' when 3 then 'Fornecedor' end ds_grupo_centro_custo";
        $sql.="        from compras c";
        $sql.="        INNER JOIN contas ct on c.conta_pk = ct.pk";
        $sql.="        left join fornecedor f on c.fornecedor_pk = f.pk";
        $sql.="        inner join categorias_produto cat on c.categoria_pk = cat.pk";
        $sql.="         INNER JOIN usuarios u on c.usuario_cadastro_pk = u.pk";
        $sql.="        LEFT JOIN leads l on c.grupo_lancamento_centro_custo_pk  = l.pk";
        $sql.="        LEFT JOIN colaboradores cl on c.grupo_lancamento_centro_custo_pk  = cl.pk";
        $sql.="        WHERE 1=1";

        if($empresa_pk!=""){
            $sql.=" and ct.pk = ".$empresa_pk;
        }
        if($fornecedor_pk!=""){
            $sql.=" and f.pk = ".$fornecedor_pk;
        }
        if($categoria_pk!=""){
            $sql.=" and cat.pk = ".$categoria_pk;
        }
        if($tipo_grupo_centro_custo_pk!=""){
            $sql.=" and c.centro_custo_pk = ".$tipo_grupo_centro_custo_pk;
        }
        if($grupo_lancamento_centro_custo_pk!=""){
            $sql.=" and c.grupo_lancamento_centro_custo_pk = ".$grupo_lancamento_centro_custo_pk;
        }
        if($ic_status!=""){
            $sql.=" and c.ic_entregue = ".$ic_status;
        }
        if($dt_ini_cad!=""){
            $sql.=" and c.dt_cadastro between '".Util::DataYMD($dt_ini_cad)." 00:00:00' and '".Util::DataYMD($dt_fim_cad)." 23:59:59'";
        }
        if($dt_ini_compra!=""){
            $sql.=" and c.dt_pagamento between '".Util::DataYMD($dt_ini_compra)."' and '".Util::DataYMD($dt_fim_compra)."'";
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

    public function relControleSolicitacaoCompra($empresa_pk,$solicitante_pk,$usuario_aprovacao_pk,$tipo_grupo_centro_custo_pk, $grupo_lancamento_centro_custo_pk, $ic_status, $dt_ini_cad, $dt_fim_cad, $dt_ini_aprov, $dt_fim_aprov){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        
        $sql="select cs.pk,";
        $sql.="      ct.ds_razao_social,";
        $sql.="      us.ds_usuario ds_solicitante,";
        $sql.="      ua.ds_usuario ds_aprovador,";
        $sql.="      date_format(cs.dt_solicitacao,'%d/%m/%Y')dt_solicitacao,";
        $sql.="      date_format(cs.dt_aprovacao,'%d/%m/%Y')dt_aprovacao,";
        $sql.="      l.ds_lead,";
        $sql.="      cl.ds_colaborador,";
        $sql.="      f.ds_fornecedor,";
        $sql.="      case cs.tipo_grupo_centro_custo_pk when 1 then 'Posto de trabalho' when 2 then 'Colaborador' when 3 then 'Fornecedor' end ds_tipo_grupo,";
        $sql.="      cs.tipo_grupo_centro_custo_pk";
        $sql.="  from compras_solicitacao cs";
        $sql.=" inner join contas ct on cs.empresas_pk = ct.pk";
        $sql.=" LEFT JOIN usuarios us on cs.solicitante_pk = us.pk";
        $sql.=" LEFT JOIN usuarios ua on cs.usuario_aprovacao_pk = ua.pk";
        $sql.=" INNER JOIN compras_solicitacao_orcamentos cso on cs.pk = cso.compra_solicitacao_pk";
        $sql.=" LEFT JOIN leads l on cs.grupo_lancamento_centrocusto_pk = l.pk";
        $sql.=" left join colaboradores cl on cs.grupo_lancamento_centrocusto_pk = cl.pk";
        $sql.=" left join fornecedor f on cs.grupo_lancamento_centrocusto_pk = f.pk";
        $sql.=" where 1=1";
        if($empresa_pk!=""){
            $sql.=" and ct.pk = ".$empresa_pk;
        }
        if($solicitante_pk!=""){
            $sql.=" and cs.solicitante_pk = ".$solicitante_pk;
        }
        if($usuario_aprovacao_pk!=""){
            $sql.=" and cs.usuario_aprovacao_pk = ".$usuario_aprovacao_pk;
        }
        if($tipo_grupo_centro_custo_pk!=""){
            $sql.=" and cs.tipo_grupo_centro_custo_pk = ".$tipo_grupo_centro_custo_pk;
        }
        if($grupo_lancamento_centro_custo_pk!=""){
            $sql.=" and cs.grupo_lancamento_centrocusto_pk=".$grupo_lancamento_centro_custo_pk;
        }
        if($ic_status!=""){
            $sql.=" and cso.ic_status= ".$ic_status;
        }
        if($dt_ini_cad!=""){
            $sql.=" and cs.dt_cadastro between '".Util::DataYMD($dt_ini_cad)." 00:00:00' and '".Util::DataYMD($dt_fim_cad)." 23:59:59'";
        }

        if($dt_ini_aprov!=""){
            $sql.=" and cs.dt_aprovacao between '".Util::DataYMD($dt_ini_aprov)." 00:00:00' and '".Util::DataYMD($dt_fim_aprov)." 23:59:59'";
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

}
