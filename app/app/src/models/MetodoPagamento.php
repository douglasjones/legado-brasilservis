<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class MetodoPagamento {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listar_por_ds_metodo_pagamento() {
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_metodo_pagamento ";
        $sql.="       ,ic_status ";

        $sql.="  from metodos_pagamento ";
        $sql.=" where ic_status = 1 ";
        $sql.=" order by ds_metodo_pagamento asc ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }
    public function verificarFormaPagamentoXML($ds_metodo_pagamento) {
        $retorno = new \StdClass;
        $retorno->status = false;
        $retorno->data = [];

        // 🔎 Verifica se já existe o método de pagamento
        $sql = "SELECT pk 
                FROM metodos_pagamento 
                WHERE ds_metodo_pagamento = :ds_metodo_pagamento
                AND ic_status = 1 
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':ds_metodo_pagamento', $ds_metodo_pagamento);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row) {
            $pk = $row['pk'];
        } else {
            // Se não encontrou, insere
            $fields = array();
            $fields['ds_metodo_pagamento'] = $ds_metodo_pagamento;
            $fields['ic_status'] = 1;
            $fields["dt_ult_atualizacao"] = "sysdate()";
            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
            $pk = Util::execInsert("metodos_pagamento", $fields, $this->pdo);
        }

        return $pk;
    }


}
