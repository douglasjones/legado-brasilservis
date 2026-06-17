<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class ContaBancaria {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listarGrid($bancos_pk, $ds_conta, $ic_status){
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
        $search = "";
        if (isset($_GET['search']['value']) and $_GET['search']['value'] != '') {
            $pesq = $_GET['search']['value'];
            $search .= " AND (
                            cb.pk LIKE '%".$pesq."%' OR
                            c.ds_banco LIKE '%".$pesq."%' OR
                            cb.ds_conta_bancaria LIKE '%".$pesq."%' OR
                            co.ds_razao_social LIKE '%".$pesq."%' 
                        )";
        }

        $sql ="";
        $sql.="select cb.pk t_pk, cb.dt_cadastro, cb.usuario_cadastro_pk, cb.dt_ult_atualizacao, cb.usuario_ult_atualizacao_pk ";
        $sql.="       ,cb.ds_conta_bancaria t_ds_conta_bancaria ";
        $sql.="       ,cb.ds_agencia t_ds_agencia ";
        $sql.="       ,cb.ds_conta t_ds_conta ";
        $sql.="       ,cb.tipo_conta_pk  ";
        $sql.="       ,cb.vl_saldo_inicial t_vl_saldo_inicial ";
        $sql.="       ,cb.ic_status  ";
        $sql.="       ,cb.bancos_pk ";
        $sql.="       ,cb.empresas_pk";
        $sql.="       ,co.ds_razao_social t_ds_empresa";
        $sql.="       ,c.ds_banco t_ds_banco ";
        $sql.="       ,Case";
        $sql.="         WHEN cb.ic_status = 1 THEN  'Ativa'";
        $sql.="         WHEN cb.ic_status = 2 THEN  'Desativada'";
        $sql.="         END t_ds_status";
        $sql.="       ,Case ";
        $sql.="         WHEN cb.tipo_conta_pk = 1 THEN  'Conta Corrente'";
        $sql.="         WHEN cb.tipo_conta_pk = 2 THEN  'Poupanbça'";
        $sql.="         WHEN cb.tipo_conta_pk = 3 THEN  'Investimento'";       
        $sql.="         WHEN cb.tipo_conta_pk = 4 THEN  'Caixinha'";       
        $sql.="         END t_ds_tipo_conta";
        $sql.="  from contas_bancarias cb ";
        $sql.="  left join bancos c on c.pk = cb.bancos_pk ";
        $sql.="  inner join contas co on co.pk = cb.empresas_pk ";
        $sql.=" where 1=1 ";
        $sql.=$search;

        if($bancos_pk != ""){
             $sql.=" and cb.bancos_pk =".$bancos_pk;
        }
                
        if($ds_conta != ""){
            $sql.=" and cb.ds_conta like '%".$ds_conta."%' ";
        }
        
         if($ic_status != ""){
             $sql.=" and cb.ic_status =".$ic_status;
        }
        
        $sql.=" order by cb.ds_conta_bancaria asc ";

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
    public function salvar($conta_bancaria){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_conta_bancaria'] = $conta_bancaria['ds_conta_bancaria'];
        $fields['ds_agencia'] = $conta_bancaria['ds_agencia'];
        $fields['ds_conta'] = $conta_bancaria['ds_conta'];
        $fields['tipo_conta_pk'] = $conta_bancaria['tipo_conta_pk'];
        $fields['vl_saldo_inicial'] = $conta_bancaria['vl_saldo_inicial'];
        $fields['ic_status'] = $conta_bancaria['ic_status'];
        $fields['bancos_pk'] = $conta_bancaria['bancos_pk'];
        $fields['empresas_pk'] = $conta_bancaria['empresas_pk'];

        
        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($conta_bancaria['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];


            $pk = Util::execInsert("contas_bancarias", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("contas_bancarias", $fields, " pk = ".$conta_bancaria['pk'],$this->pdo);
            $pk = $conta_bancaria['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;

    }

    public function excluir($pk){
        Util::execDelete('contas_bancarias', ' pk='.$pk, $this->pdo);
    }

    public function listarPorPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

            $sql ="select pk ";
            $sql.="      , date_format(dt_cadastro,'%d/%m/%Y') dt_cadastro ";
            $sql.="      , usuario_cadastro_pk ";
            $sql.="      , date_format(dt_ult_atualizacao,'%d/%m/%Y') dt_ult_atualizacao ";
            $sql.="      , usuario_ult_atualizacao_pk ";
    
            $sql.="       ,ds_conta_bancaria ";
            $sql.="       ,ds_agencia ";
            $sql.="       ,ds_conta ";
            $sql.="       ,tipo_conta_pk ";
            $sql.="       ,vl_saldo_inicial ";
            $sql.="       ,ic_status ";
            $sql.="       ,bancos_pk ";
            $sql.="       ,empresas_pk";
    
    
            $sql.="  from contas_bancarias ";
            $sql.=" where pk = $pk ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

	public function listaPorEmpresa($empresa_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $result = [];
        $sql ="";
        $sql.="select cb.pk,cb.ds_conta_bancaria ";
        $sql.="       ,cb.ds_conta ";
        $sql.="       ,cb.ds_agencia ";
        $sql.="       ,b.ds_banco ";
        $sql.="  from contas_bancarias cb ";
        $sql.="  inner join contas c on c.pk = cb.empresas_pk ";   
        $sql.="  left join bancos b on b.pk = cb.bancos_pk ";
        $sql.=" where 1=1 ";
        $sql.=" and cb.ic_status = 1 "; 
        if(!empty($empresa_pk)){
            $sql .=" AND cb.empresas_pk=".$empresa_pk;
        }  
        $sql.=" group by cb.pk,c.pk ";

        $query = $this->pdo->prepare($sql);
        $query->execute();
        $query = $query->fetchAll(\PDO::FETCH_ASSOC);

        for($i = 0; $i < count($query); $i++){
            if($query[$i]['ds_banco'] != ""){
                $ds_conta = $query[$i]['ds_banco']." - AG:".$query[$i]['ds_agencia']." - CC:".$query[$i]['ds_conta'];
            }else{
                $ds_conta = "Caixinha";
            }
            $result[] = array(
                "pk" => $query[$i]["pk"],
                "ds_conta"=>$ds_conta           
            );
        }

        $retorno->data = $result;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
   
    public function listarEmpresaContasAtivas(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql="";
        $sql.="select cb.pk,cb.ds_conta_bancaria ";
        $sql.="       ,cb.ds_conta ";
        $sql.="       ,cb.ds_agencia ";
        $sql.="       ,b.ds_banco ";
        $sql.="  from contas_bancarias cb ";
        $sql.="  inner join contas c on c.pk = cb.empresas_pk ";   
        $sql.="  left join bancos b on b.pk = cb.bancos_pk ";
        $sql.=" where 1=1 ";
        $sql.=" and cb.ic_status = 1 "; 
        $sql.=" group by cb.pk,c.pk ";
        $query = $this->pdo->prepare($sql);
        $query->execute();
        $query = $query->fetchAll(\PDO::FETCH_ASSOC);

        for($i = 0; $i < count($query); $i++){
            if($query[$i]['ds_banco'] != ""){
                $ds_conta = $query[$i]['ds_banco']." - AG:".$query[$i]['ds_agencia']." - CC:".$query[$i]['ds_conta'];
            }else{
                $ds_conta = "Caixinha";
            }
            $result[] = array(
                "pk" => $query[$i]["pk"],
                "ds_conta"=>$ds_conta           
            );
        }

        $retorno->data = $result;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function listarContasLancamento($empresas_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select cb.pk, cb.dt_cadastro, cb.usuario_cadastro_pk, cb.dt_ult_atualizacao, cb.usuario_ult_atualizacao_pk  ";
        $sql.="       ,cb.ds_conta_bancaria ";
        $sql.="       ,cb.ds_agencia ";
        $sql.="       ,cb.ds_conta ";
        $sql.="       ,cb.tipo_conta_pk ";
        $sql.="       ,cb.vl_saldo_inicial ";
        $sql.="       ,cb.ic_status ";
        $sql.="       ,cb.bancos_pk ";
        $sql.="       ,cb.empresas_pk";
        $sql.="       ,concat (b.ds_banco,' - AG:',cb.ds_agencia,' - Cont:',cb.ds_conta) ds_dados_conta ";
        $sql.="  from contas_bancarias cb ";
        $sql.="  left join bancos b on cb.bancos_pk = b.pk ";
        $sql.=" where 1=1";
        if($empresas_pk!=""){
            $sql.=" and cb.empresas_pk = ".$empresas_pk;
        }
        
        $sql.=" group by cb.pk";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function listarValorInicial($empresas_pk, $contas_bancarias_pk){

        $sql ="";
        $sql.="SELECT cb.vl_saldo_inicial";
        $sql.="  FROM contas_bancarias cb";
        $sql.=" WHERE cb.empresas_pk =".$empresas_pk;
        $sql.="   AND cb.pk = ".$contas_bancarias_pk;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $rows;
    }
}
