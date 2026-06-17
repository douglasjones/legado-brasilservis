<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class PlanoConta {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listarGrid($ds_tipo_operacao,$categorias_financeiras_pk, $ic_status){
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
                            cf.ds_categoria LIKE '%".$pesq."%' OR
                            tp.ds_tipo_operacao LIKE '%".$pesq."%'  OR
                            tp.pk LIKE '%".$pesq."%' 
                        )";
        }

        $sql ="";
        $sql.="select tp.pk t_pk, tp.dt_cadastro, tp.usuario_cadastro_pk, tp.dt_ult_atualizacao, tp.usuario_ult_atualizacao_pk ";
        $sql.="       ,tp.ds_tipo_operacao t_ds_tipo_operacao ";
        $sql.="       ,case when tp.ic_status  = 1 then 'Ativo' ELSE 'Desativado' END t_ds_status ";
        $sql.="       ,tp.categorias_financeiras_pk t_categorias_financeiras_pk ";
        $sql.="       ,cf.ds_categoria t_ds_categoria ";
        
        $sql.="  from tipos_operacao tp ";
        $sql.=" inner join categorias_financeiras cf on tp.categorias_financeiras_pk = cf.pk ";
        $sql.=" where 1=1 ";
        
        if($categorias_financeiras_pk != ""){
            $sql.=" and tp.categorias_financeiras_pk = ".$categorias_financeiras_pk;
        }
        
        if($ds_tipo_operacao != ""){
            $sql.=" and tp.ds_tipo_operacao like '%".$ds_tipo_operacao."%' ";
        }
        
        if($ic_status != ""){
            $sql.=" and tp.ic_status = ".$ic_status;
        }
        $sql.=$search;
        $sql.=" order by tp.ds_tipo_operacao asc ";
       
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

    public function salvar($plano_conta){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_tipo_operacao'] = $plano_conta['ds_tipo_operacao'];
        $fields['ic_status'] = $plano_conta['ic_status'];
        $fields['categorias_financeiras_pk'] = $plano_conta['categorias_financeiras_pk'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($plano_conta['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];


            $pk = Util::execInsert("tipos_operacao", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("tipos_operacao", $fields, " pk = ".$plano_conta['pk'],$this->pdo);
            $pk = $plano_conta['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;

    }

    public function listarPorPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        
        $sql ="select pk ";
        $sql.="      , date_format(dt_cadastro,'%d/%m/%Y') dt_cadastro ";
        $sql.="      , usuario_cadastro_pk ";
        $sql.="      , date_format(dt_ult_atualizacao,'%d/%m/%Y') dt_ult_atualizacao ";
        $sql.="      , usuario_ult_atualizacao_pk ";

        $sql.="       ,ds_tipo_operacao ";
        $sql.="       ,ic_status ";
        $sql.="       ,categorias_financeiras_pk ";


        $sql.="  from tipos_operacao ";
        $sql.=" where pk = $pk ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function excluir($pk){
        Util::execDelete('tipos_operacao', ' pk='.$pk, $this->pdo);
    }

    public function listaPorCategoria($categorias_financeiras_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        
        $sql ="";
        $sql.="select toc.pk, toc.dt_cadastro, toc.usuario_cadastro_pk, toc.dt_ult_atualizacao, toc.usuario_ult_atualizacao_pk  ";
        $sql.="       ,concat(cf.ds_categoria,' - ',toc.ds_tipo_operacao )ds_tipo_operacao";
        $sql.="       ,toc.ic_status ";
        $sql.="       ,toc.categorias_financeiras_pk ";

        $sql.="  from tipos_operacao toc";
        $sql.="  inner join categorias_financeiras cf on cf.pk = toc.categorias_financeiras_pk";        
        $sql.=" where cf.ic_status =  1";
        if(!empty($categorias_financeiras_pk)){
            $sql.=" AND toc.categorias_financeiras_pk = $categorias_financeiras_pk ";
        }            
        $sql.=" order by cf.ds_categoria,toc.ds_tipo_operacao ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listarTodos(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        
        $sql ="";
        $sql.="select toc.pk, toc.dt_cadastro, toc.usuario_cadastro_pk, toc.dt_ult_atualizacao, toc.usuario_ult_atualizacao_pk  ";
        $sql.="       ,concat(cf.ds_categoria,' - ',toc.ds_tipo_operacao )ds_tipo_operacao";
        $sql.="       ,toc.ic_status ";
        $sql.="       ,toc.categorias_financeiras_pk ";

        $sql.="  from tipos_operacao toc";
        $sql.="  INNER join categorias_financeiras cf on cf.pk = toc.categorias_financeiras_pk";        
        $sql.=" where cf.ic_status =  1";     
        $sql.=" order by cf.ds_categoria,toc.ds_tipo_operacao ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }


}
