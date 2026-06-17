<?php

namespace App\Model;

use App\Utils\AES;
use App\Utils\Util;

class Log {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }


    public function salvar($ds_modulo,$pk_modulo){

        $fields = array();
        $fields['ds_modulo'] = $ds_modulo;
        $fields['pk_modulo'] = $pk_modulo;
        $fields["dt_cadastro"] = "sysdate()";
        $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
        Util::execInsert("log_exclusao", $fields,$this->pdo);

    }

}
