<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class TetoGastoItem {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function excluir($pk){
        Util::execDelete('teto_gastos_itens', ' pk='.$pk, $this->pdo);
    }

    public function fcTotal($pk, $vl_teto_anual, $vl_teto_mensal,  $dt_ini_teto, $dt_fim_teto){
      

        $vl_total_teto_anual_cadastrado = 0;
        $vl_total_teto_anual = 0;
        $vl_teto_mensal_total = 0;
        $vl_total_teto = "";
        $diffMes = "";
        $mensagem = "";
        
        $sql ="";
        $sql.="select tg.pk";
        $sql.="       ,TIMESTAMPDIFF(month, '".$dt_ini_teto."', '".$dt_fim_teto."') diffMes";
        $sql.="       ,tg.vl_total_teto ";
        $sql.="  from teto_gastos tg ";
        $sql.=" where pk = $pk ";
        
        
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $vl_total_teto =  $rows[0]['vl_total_teto'];
        $diffMes = $rows[0]['diffMes'];

        $sql ="";
        $sql.="select tgi.pk";
        $sql.="       ,tgi.vl_teto_anual ";
        $sql.="       ,tgi.vl_teto_mensal ";
        $sql.="       ,tgi.vl_teto_anual_atual ";
        $sql.="       ,tgi.vl_teto_mensal_atual ";

        $sql.="  from teto_gastos_itens tgi ";
        $sql.="  inner join teto_gastos tg on tgi.teto_gastos_pk = tg.pk ";
        $sql.=" where teto_gastos_pk = $pk ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows1 = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        for($i=0;$i<count($rows1);$i++){
            $vl_total_teto_anual_cadastrado += $rows1[$i]['vl_teto_anual'];
        }

        if($vl_total_teto_anual_cadastrado <= $vl_total_teto){
            $vl_total_teto_anual = $vl_teto_anual + $vl_total_teto_anual_cadastrado;
            if($vl_total_teto_anual > $vl_total_teto){
                $mensagem = "A soma dos valores anuais não pode superar o valor do teto de gastos.";
               
            }
        }else{
            $mensagem = "Valor anual não pode superar o valor do teto total de gastos.";
          
        }
        if($vl_teto_mensal != ""){
            if($vl_teto_mensal > $vl_teto_anual){
                $mensagem = "Valor mensal não pode superar o valor do teto anual.";
               
            }
            $vl_teto_mensal_total = $vl_teto_mensal * $diffMes;
            if($vl_teto_mensal_total > $vl_teto_anual){
                $mensagem = "A soma do valor mensal não pode superar o valor do teto anual.";
                
                
            }
            
        }

        return $mensagem;
    }



    public function listarGrid($teto_gastos_pk){
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
        $sql.="select tgi.pk t_pk, date_format(tgi.dt_cadastro,'%d/%m/%Y') dt_cadastro, tgi.usuario_cadastro_pk, date_format(tgi.dt_ult_atualizacao,'%d/%m/%Y') dt_ult_atualizacao, tgi.usuario_ult_atualizacao_pk ";
        $sql.="       ,tgi.operacao_pk t_operacao_pk";
        $sql.="       ,tgi.categoria_operacao_pk ";
        $sql.="       ,tgi.tipos_operacao_pk ";
        $sql.="       ,CASE
                        WHEN tgi.tipos_operacao_pk = 7 THEN 'Custo Fixo'
                        WHEN tgi.tipos_operacao_pk = 8 THEN 'Custo Variável'
                        WHEN tgi.tipos_operacao_pk = 2 THEN 'Despesa Fixa'
                        WHEN tgi.tipos_operacao_pk = 3 THEN 'Despesa Variável'
                        WHEN tgi.tipos_operacao_pk = 4 THEN 'Imposto'
                        WHEN tgi.tipos_operacao_pk = 5 THEN 'Transferência'
                        WHEN tgi.tipos_operacao_pk = 6 THEN 'Caixinha'
                        END ds_tipos_operacao ";
        $sql.="       ,date_format(tgi.dt_ini_teto ,'%d/%m/%Y') t_dt_ini_teto";
        $sql.="       ,date_format(tgi.dt_fim_teto ,'%d/%m/%Y') t_dt_fim_teto";
        $sql.="       ,tgi.vl_teto_anual t_vl_teto_anual";
        $sql.="       ,tgi.vl_teto_mensal t_vl_teto_mensal";
        $sql.="       ,tgi.ic_teto_mensal ";
        $sql.="       ,tgi.vl_teto_anual_atual ";
        $sql.="       ,tgi.vl_teto_mensal_atual ";
        $sql.="       ,tgi.ic_status ";
        $sql.="       ,tgi.obs ";
        $sql.="       ,tgi.teto_gastos_pk ";
        $sql.="       ,cf.ds_categoria t_categoria_operacao_pk ";
        $sql.="       ,top.ds_tipo_operacao  t_tipos_operacao_pk";

        $sql.="  from teto_gastos_itens tgi";
        $sql.="  left join categorias_financeiras cf on cf.pk = tgi.categoria_operacao_pk";
        $sql.="  left join tipos_operacao top on top.pk = tgi.operacao_pk ";
        $sql.=" where 1=1 ";
        if($teto_gastos_pk != ""){
            $sql.=" and teto_gastos_pk = ".$teto_gastos_pk;
        }
        $sql.=" order by operacao_pk asc ";

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

    public function salvar($teto_gasto_item){
        
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
       
        $vl_teto_anual = ($teto_gasto_item['vl_teto_anual']);
        $vl_teto_mensal = ($teto_gasto_item['vl_teto_mensal']);
        $dt_ini_teto = Util::dataYMD($teto_gasto_item['dt_ini_teto']);
        $dt_fim_teto = Util::dataYMD($teto_gasto_item['dt_fim_teto']);
        
        $mensagem = $this->fcTotal($teto_gasto_item['teto_gastos_pk'], $vl_teto_anual, $vl_teto_mensal, $dt_ini_teto, $dt_fim_teto);
        
        if($mensagem != ""){
            $retorno->status = false;
            $retorno->message = $mensagem;
            $retorno->data = [];
            return $retorno;
        }
        $fields = array();
        $fields['operacao_pk'] = $teto_gasto_item['operacao_pk'];
        $fields['categoria_operacao_pk'] = $teto_gasto_item['categoria_operacao_pk'];
        $fields['tipos_operacao_pk'] = $teto_gasto_item['tipos_operacao_pk'];
        $fields['dt_ini_teto'] = $dt_ini_teto;
        $fields['dt_fim_teto'] = $dt_fim_teto;
        $fields['vl_teto_anual'] = $vl_teto_anual;
        $fields['vl_teto_mensal'] = $vl_teto_mensal;
        $fields['ic_teto_mensal'] = $teto_gasto_item['ic_teto_mensal'];
        $fields['vl_teto_anual_atual'] = ($teto_gasto_item['vl_teto_anual_atual']);
        $fields['vl_teto_mensal_atual'] = ($teto_gasto_item['vl_teto_mensal_atual']);
        $fields['ic_status'] = $teto_gasto_item['ic_status'];
        $fields['obs'] = $teto_gasto_item['obs'];
        $fields['teto_gastos_pk'] = $teto_gasto_item['teto_gastos_pk'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($teto_gasto_item['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("teto_gastos_itens", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("teto_gastos_itens", $fields, " pk = ".$teto_gasto_item['pk'],$this->pdo);
            $pk = $teto_gasto_item['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;
    }
}
    
