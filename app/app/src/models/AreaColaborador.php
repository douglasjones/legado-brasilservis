<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;
use PDO;

class AreaColaborador {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }


    public function buscarColaborador($id_empresa,$id_colaborador){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio


        $sql ="";
        $sql.="     SELECT c.pk colaborador_pk,";
        $sql.="        c.ds_colaborador,";
        $sql.="        c.ds_rg,";
        $sql.="        c.ds_cpf,";
        $sql.="        c.ds_pin,";
        $sql.="        co.pk cliente_pk,";
        $sql.="        co.id_cliente,";
        $sql.="        co.ds_conta,";
        $sql.="        co.ds_razao_social,";
        $sql.="        co.ds_cpf_cnpj,";
        $sql.="        psa.ic_status ic_status_solicitacao_app,";
        $sql.="        psa.pk novo_cadastro_pk,";
        $sql.="        psa.ds_link_imagem_cadastro";
        $sql.="     FROM colaboradores c ";
        $sql.="         INNER JOIN contas co ON c.empresas_pk = co.pk";
        $sql.="         LEFT JOIN ponto_solicitacao_liberacao_app psa on c.pk = psa.colaborador_pk";
        $sql.="     WHERE c.pk = ".$id_colaborador;
        $sql.="     AND c.ic_status = 1";
        $sql.="     AND co.ic_status = 1";
       // $sql.="     AND co.id_cliente = '".$id_empresa."'";



        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

}
