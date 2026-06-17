<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class Supervisor {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function getRelatorioAcompanhamentoSupervisor($colaborador_pk,$leads_pk,$ic_mes,$ic_ano,$ds_mes){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        // Primeiro dia do mês atual
        $primeiroDiaMesAtual = date("Y-m-d", strtotime("$ic_ano-$ic_mes-01"));

        // Último dia do mês atual
        $ultimoDiaMesAtual = date("Y-m-t", strtotime("$ic_ano-$ic_mes-01"));

        $sql ="";
        $sql.="SELECT 
                c.ds_colaborador,
                l.ds_lead,
                DATE_FORMAT(s.dt_hr_registro, '%d/%m/%Y') AS dt_registro,
                MIN(CASE WHEN s.ic_tipo_registro = 1 THEN TIME(s.dt_hr_registro) END) AS horario_entrada,
                MAX(CASE WHEN s.ic_tipo_registro = 2 THEN TIME(s.dt_hr_registro) END) AS horario_saida
            FROM controle_supervisao s 
            INNER JOIN leads l ON s.posto_trabalho_pk = l.pk
            INNER JOIN colaboradores c ON s.colaborador_pk = c.pk";
        $sql.=" where s.dt_hr_registro BETWEEN '".$primeiroDiaMesAtual." 00:00:00' and '".$ultimoDiaMesAtual." 23:59:59'";    
        if($leads_pk!=""){
            $sql.=" and l.pk = $leads_pk ";
        }
        if($colaborador_pk!=""){
            $sql.=" and c.pk = $colaborador_pk ";
        }
        $sql.="GROUP BY c.ds_colaborador, l.pk";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

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
