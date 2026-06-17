<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class AnaliseFinanceiraProcesso {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function historicoAnaliseFinanceira($analise_financeira_pk){
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
        $sql.=" SELECT  afp.pk t_pk";
        $sql.="         ,date_format(afp.dt_cadastro, '%d/%m/%Y') t_dt_cadastro";
        $sql.="         ,afp.usuario_cadastro_pk";
        $sql.="         ,CASE WHEN afp.ic_recusa = 1 THEN 'Recusado'";
        $sql.="               WHEN afp.ic_correcao = 1 THEN 'Correção'";
        $sql.="               WHEN afp.ic_aprovacao = 1 THEN 'Aprovação'";
        $sql.="           END t_ic_status";
        $sql.="         ,CASE WHEN afp.ic_recusa = 1 THEN obs_recusa";
        $sql.="               WHEN afp.ic_correcao = 1 THEN obs_correcao";
        $sql.="               WHEN afp.ic_aprovacao = 1 THEN obs_aprovacao";
        $sql.="           END t_obs";
        $sql.="         ,u.ds_usuario t_ds_usuario_cadastro";
        $sql.="   FROM analise_financeira_processos afp";
        $sql.="  inner join usuarios u on u.pk = afp.usuario_cadastro_pk";
        $sql.="  where afp.analise_financeira_pk = $analise_financeira_pk";

        
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

    public function salvar($analise_financeira_processos, $gestor_aprovacao_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql = "";
        $sql.= "select grupos_pk from usuarios where pk = ".$_SESSION['session_user']['par1'];
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $fieldsAnaliseFinanceira = array();
        $fields = array();

        if($analise_financeira_processos['ic_aprovacao'] == 2){
            $fields["ic_aprovacao"] = 1;
        }else if($analise_financeira_processos['ic_aprovacao'] == 3){
            $fields["ic_aprovacao"] = 1;
        }else if($analise_financeira_processos['ic_correcao'] == 4){
            $fields["ic_correcao"] = 1;
        }else if($analise_financeira_processos['ic_recusa'] == 5){
            $fields["ic_recusa"] = 1;
        }else if($analise_financeira_processos['ic_correcao'] == 6){
            $fields["ic_correcao"] = 1;
        }
        
        $fields['obs_recusa'] = $analise_financeira_processos['obs_recusa'];
        $fields['obs_correcao'] = $analise_financeira_processos['obs_correcao'];
        $fields['obs_aprovacao'] = $analise_financeira_processos['obs_aprovacao'];
        $fields['dt_cancelamento'] = $analise_financeira_processos['dt_cancelamento'];
        $fields['obs_cancelamento'] = $analise_financeira_processos['obs_cancelamento'];
        $fields['tipo_nivel_usuario'] = $rows[0]["grupos_pk"];
        $fields['analise_financeira_pk'] = $analise_financeira_processos['analise_financeira_pk'];

        
        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($analise_financeira_processos['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];


            $pk = Util::execInsert("analise_financeira_processos", $fields,$this->pdo);
            /*$retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;*/

            
            if($analise_financeira_processos["ic_aprovacao"] == 2){
                $fieldsAnaliseFinanceira["ic_status"] = $analise_financeira_processos["ic_aprovacao"];
            }else if($analise_financeira_processos["ic_aprovacao"] == 3){
                $fieldsAnaliseFinanceira["ic_status"] = $analise_financeira_processos["ic_aprovacao"];
            }else if($analise_financeira_processos["ic_correcao"] == 4){
                $fieldsAnaliseFinanceira["ic_status"] = $analise_financeira_processos["ic_correcao"];
            }else if($analise_financeira_processos["ic_recusa"] == 5){
                $fieldsAnaliseFinanceira["ic_status"] = $analise_financeira_processos["ic_recusa"];
            }else if($analise_financeira_processos["ic_correcao"] == 6){
                $fieldsAnaliseFinanceira["ic_status"] = $analise_financeira_processos["ic_correcao"];
            }

            $fieldsAnaliseFinanceira["gestor_aprovacao_pk"] = $gestor_aprovacao_pk;
            
            Util::execUpdate("analise_financeira", $fieldsAnaliseFinanceira, " pk = ".$analise_financeira_processos['analise_financeira_pk'],$this->pdo);
            //$pk = $analise_financeira_processos['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("analise_financeira_processos", $fields, " pk = ".$analise_financeira_processos['pk'],$this->pdo);
            $pk = $analise_financeira_processos['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }

        


        return $retorno;

    }

}
