<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class Genero {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }


    public function listarTodos($ds_genero){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_genero ";

        $sql.="  from generos ";
        $sql.=" where 1=1 ";
        if($ds_genero != ""){
            $sql.=" and ds_genero like '%".$ds_genero."%' ";
        }
        $sql.=" order by ds_genero asc ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }
}
