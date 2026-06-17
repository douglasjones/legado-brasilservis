<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class Cargo {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listar_por_ds_cargo($ds_cargo){


        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_cargo ";

        $sql.="  from cargos ";
        $sql.=" where 1=1 ";
        if($ds_cargo != ""){
            $sql.=" and ds_cargo like '%".$ds_cargo."%' ";
        }
        $sql.=" order by ds_cargo asc ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }
}
