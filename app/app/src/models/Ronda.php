<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class Ronda {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function relRondas($leads_pk,$leads_clientes_pk,$dt_ini_ronda,$dt_fim_ronda){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql=" select";
        $sql.="    ll.ds_lead ds_cliente,";
        $sql.="    r.leads_pk ds_lead,";
        $sql.="    r.local_ronda_pk ds_local_ronda,";
        $sql.="    date_format(r.dt_cadastro, '%d/%m/%Y')dt_ronda,";
        $sql.="    date_format(r.dt_cadastro, '%H:%i:%s')hr_ronda,";
        $sql.="    r.ds_ronda ds_obs";
        $sql.=" from ronda r";
        $sql.=" LEFT join leads l on r.leads_pk = l.ds_lead";
        $sql.=" left join leads ll on l.leads_pai_pk = ll.pk";
        $sql.=" where 1=1 ";

        if($leads_clientes_pk!=" "){
            if($leads_pk!=" "){
                $sql.=" and (l.ds_lead LIKE '%".$leads_pk."%' OR
                    ll.ds_lead LIKE '%".$leads_pk."%' )";
                $sql.=" and (l.ds_lead LIKE '%".$leads_clientes_pk."%' OR
                        ll.ds_lead LIKE '%".$leads_clientes_pk."%') ";
            }
            else{
                $sql.=" and (l.ds_lead LIKE '%".$leads_clientes_pk."%' OR
                    ll.ds_lead LIKE '%".$leads_clientes_pk."%' )    ";
            }

        }
        if($leads_pk!=" "){
            $sql.=" and (l.ds_lead LIKE '%".$leads_pk."%' OR
                    ll.ds_lead LIKE '%".$leads_pk."%' )";
        }
        if($dt_ini_ronda!=""){
            $sql.=" and r.dt_cadastro between '".Util::DataYMD($dt_ini_ronda)." 00:00:00' and '".Util::DataYMD($dt_fim_ronda)." 23:59:59'";
        }

       
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        $retorno->iTotalDisplayRecords = count($rows);
        $retorno->iTotalRecords = count($rows);
    
        echo json_encode($retorno);
        exit(0);
    }

}
