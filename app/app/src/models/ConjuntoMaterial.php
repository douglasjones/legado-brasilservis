<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class ConjuntoMaterial {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function salvar($conjunto_material){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['colaborador_pk'] = $conjunto_material['colaborador_pk'];
        $fields['leads_pk'] = $conjunto_material['leads_pk'];
        $fields['contratos_pk'] = $conjunto_material['contratos_pk'];
        $fields['ds_conjunto_material'] = $conjunto_material['ds_conjunto_material'];


        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($conjunto_material['pk'] == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("conjunto_material", $fields,$this->pdo);

            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("conjunto_material", $fields, " pk = ".$conjunto_material['pk'],$this->pdo);

            $retorno->status = true;
            $retorno->message = 'Dados alterados com sucesso';
            $retorno->data = $conjunto_material['pk'];
        }
        return $retorno;
    }
    public function listarMovimentarMaterialProd($colaborador_pk,$leads_pk,$categoria_pk,$produtos_pk,$dt_movimentacao_ini,$dt_movimentacao_fim,$grupo_para_movimentacao_pk,$contratos_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $mysql_data = [];

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
        $sql.="select me.pk,";
        $sql.=" me.grupo_para_movimentacao_pk,";
        $sql.=" cm.pk conjunto_material_pk,";
        $sql.=" p.categorias_produto_pk categoria_pk ,";
        $sql.=" cm.ds_conjunto_material,";
        $sql.=" date_format(me.dt_cadastro,'%d/%m/%Y')dt_cadastro,";
        $sql.=" case me.grupo_para_movimentacao_pk when 1 then 'Colaborador' when 2 then 'Posto de Trabalho' end ds_grupo_movimentado,";
        $sql.=" me.colaborador_pk,me.leads_pk,me.contratos_pk,cp.ds_categoria,pi.pk produto_iten_pk, p.ds_produto,sum(me.qtde)qtde";

        $sql.="  from movimentacao_estoque me";
        $sql.="  inner join conjunto_material cm  on cm.pk = me.conjunto_material_pk";
        $sql.="  inner join produtos_itens pi on me.produtos_itens_pk = pi.pk";
        $sql.="  inner join produtos p on pi.produtos_pk = p.pk";
        $sql.="  inner join categorias_produto cp on p.categorias_produto_pk = cp.pk";
        $sql.="  where 1=1";
        if($colaborador_pk!=""){
            $sql.=" and me.colaborador_pk = $colaborador_pk ";
        }
        if($leads_pk!=""){
            $sql.=" and me.leads_pk = $leads_pk ";
        }
        if($contratos_pk!=""){
            $sql.=" and me.contratos_pk = $contratos_pk ";
        }
        if($categoria_pk!=""){
            $sql.=" and cp.pk= $categoria_pk ";
        }
        if($produtos_pk!=""){
            $sql.=" and p.pk= $produtos_pk ";
        }
        if($dt_movimentacao_ini!=""){
            $sql.=" and me.dt_cadastro between '".Util::DataYMD($dt_movimentacao_ini)." 00:00:00' and '".Util::DataYMD($dt_movimentacao_fim)." 23:59:59'";
        }
        if($grupo_para_movimentacao_pk!=""){
            if($grupo_para_movimentacao_pk==1){
                $sql.=" and cm.leads_pk is null";
            }
            if($grupo_para_movimentacao_pk==2){
                $sql.=" and cm.colaborador_pk is null";
            }
        }
        $sql.= " group by cm.pk";
        $sql.= " order by cm.dt_cadastro desc";




        $stmt = $this->pdo->prepare( $sql.$lengthSql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $stmtCount = $this->pdo->prepare($sql);
        $stmtCount->execute();
        $rowsCount = $stmtCount->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($rows as $v){
            $ds_movimentado = "";
            $ds_grupo_movimentado = "";
            $grupo_para_movimentacao_pk = "";


            if($v['grupo_para_movimentacao_pk']==""){
                if($v['colaborador_pk']!=""){
                    $grupo_para_movimentacao_pk = 1;
                    $ds_grupo_movimentado = "Colaborador";
                }
                else if($v['leads_pk']!=""){
                    $grupo_para_movimentacao_pk = 2;
                    $ds_grupo_movimentado = "Posto de Trabalho";
                }
            }
            else{
                $grupo_para_movimentacao_pk = $v['grupo_para_movimentacao_pk'];
            }

            if($v['colaborador_pk']!=""){
                $queryc = $this->pegarNomeColaborador($v['colaborador_pk']);
                $ds_movimentado = $queryc->data['ds_colaborador'];
                $ds_grupo_movimentado = "Colaborador";
            }
            else if($v['leads_pk']!=""){
                $queryl = $this->pegarNomeLead($v['leads_pk']);
                $ds_movimentado =$queryl->data['ds_lead'];
                $ds_grupo_movimentado = "Posto de Trabalho";
            }
            $queryQtde = $this->listarQtde($v['conjunto_material_pk']);


            $mysql_data[] = array(
                "pk" => $v["pk"],
                "grupo_para_movimentacao_pk"=>$grupo_para_movimentacao_pk,
                "leads_pk"=>$v['leads_pk'],
                "colaborador_pk"=>$v['colaborador_pk'],
                "ds_conjunto_material"=>$v['ds_conjunto_material'],
                "conjunto_material_pk"=>$v['conjunto_material_pk'],
                "ds_grupo_movimentado"=>$ds_grupo_movimentado,
                "ds_movimentado"=>$ds_movimentado,
                "ds_categoria"=>$v['ds_categoria'],
                "contratos_pk"=>$v['contratos_pk'],
                "categoria_pk"=>$v['categoria_pk'],
                "qtde"=>$v['qtde'],

                "dt_cadastro"=>$v['dt_cadastro'],
                "ds_produto"=>$v['produto_iten_pk']." - ".$v['ds_produto']
            );
        }

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $mysql_data;
        $retorno->iTotalDisplayRecords = count($rowsCount);
        $retorno->iTotalRecords = count($rowsCount);

        echo json_encode($retorno);
        exit(0);
    }


    public function pegarNomeColaborador($colaborador_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select c.ds_colaborador";
        $sql.="  from colaboradores c";
        if($colaborador_pk!=""){
            $sql.=" where c.pk = $colaborador_pk ";
        }


        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        if(isset($rows[0])){
            $retorno->data = $rows[0];
        }
        else{
            $retorno->data = [];
        }

        return $retorno;

    }
    public function pegarNomeLead($leads_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select l.ds_lead";

        $sql.="  from leads l";
        if($leads_pk!=""){
            $sql.=" where l.pk = $leads_pk ";
        }

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows[0];

        return $retorno;

    }
    public function listarQtde($conjunto_material_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select count(0) qtde";

        $sql.="  from movimentacao_estoque me";
        $sql.="  where 1=1";
        if($conjunto_material_pk!=""){
            $sql.=" and me.conjunto_material_pk = $conjunto_material_pk ";
        }

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows[0];

        return $retorno;

    }


}
