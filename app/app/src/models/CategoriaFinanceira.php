<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class CategoriaFinanceira {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listarGrid($ds_categoria){
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
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_categoria ";
        $sql.="       ,ic_status ";

        $sql.="  from categorias_financeiras ";
        $sql.=" where 1=1 ";
        if($ds_categoria != ""){
            $sql.=" and ds_categoria_financeira like '%".$ds_categoria."%' ";
        }
        $sql.=" order by ds_categoria asc ";

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

    public function listarPorPlano($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        
        $sql ="";
        $sql.="select cf.pk,";
        $sql.="       cf.ds_categoria";
        $sql.="  from tipos_operacao toc";
        $sql.="  inner join categorias_financeiras cf on cf.pk = toc.categorias_financeiras_pk";        
        $sql.=" where cf.ic_status =  1";
        if(!empty($pk)){
            $sql.=" AND toc.pk = $pk ";
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



}
