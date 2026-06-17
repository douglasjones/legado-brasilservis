<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use Throwable;

class Conta {

    public $pdo;

    private $client; // Removido o tipo de retorno
    protected $history = []; // Removido o tipo de retorno

    public function __construct($pdo) {
        $this->pdo = $pdo;

        $stack = HandlerStack::create(new CurlMultiHandler());

        $stack->push(Middleware::retry(
            (function () {
                return function (
                    $retries,
                    \GuzzleHttp\Psr7\Request $request,
                    \GuzzleHttp\Psr7\Response $response = null,
                    \GuzzleHttp\Exception\RequestException $exception = null
                ) {
                    if ($retries >= 5) {
                        return false;
                    }

                    if ($exception !== null) {
                        return true;
                    }

                    if ($response !== null) {
                        if ($response->getStatusCode() >= 500 && $response->getStatusCode() <= 599) {
                            return true;
                        }
                    }

                    return false;
                };
            })(),
            (function () {
                return function ($numberOfRetries) {
                    return 1000 * $numberOfRetries;
                };
            })() // Delay do retry
        ));

        $history = Middleware::history($this->history);
        $stack->push($history);

        $this->client = new Client([
            'http_errors'      => false,
            'connect_timeout'  => 10.0,
            'timeout'          => 60.0,
            'force_ip_resolve' => 'v4',
            'verify'           => false,
            'handler'          => $stack
        ]);
    }
    public function excluir($pk){
        Util::execDelete('contas'," pk = ".$pk,$this->pdo);
    }
	public function updateAll($status){
        $fields = array();
        $fields['ic_status'] = $status;
        Util::execUpdate("contas", $fields, " pk > 0 ",$this->pdo);
    }

    public function salvar($conta){
        try{
             $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = true; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio

            $fields = array();
            $fields['tipo_conta_pk'] = $conta['tipo_conta_pk'];
            $fields['ds_tipo_pessoa'] = $conta['ds_tipo_pessoa'];
            $fields['ds_conta'] = $conta['ds_conta'];
            $fields['ds_razao_social'] = $conta['ds_razao_social'];
            $fields['ds_cpf_cnpj'] = $conta['ds_cpf_cnpj'];
            $fields['ds_cnae'] = $conta['ds_cnae'];
            $fields['ds_rg'] = $conta['ds_rg'];
            $fields['ds_tel'] = $conta['ds_tel'];
            $fields['ds_email'] = $conta['ds_email'];
            $fields['ds_cel'] = $conta['ds_cel'];

            $fields['ds_cep'] = $conta['ds_cep'];
            $fields['ds_endereco'] = $conta['ds_endereco'];
            $fields['ds_numero'] = $conta['ds_numero'];
            $fields['ds_complemento'] = $conta['ds_complemento'];
            $fields['ds_bairro'] = $conta['ds_bairro'];
            $fields['ds_cidade'] = $conta['ds_cidade'];
            $fields['ds_uf'] = $conta['ds_uf'];
            $fields['dt_ativacao'] =  $conta['dt_ativacao'] != '' ? Util::DataYMD($conta['dt_ativacao']) : '';
            $fields['dt_cancelamento'] = $conta['dt_cancelamento'] != '' ? Util::DataYMD($conta['dt_cancelamento']) : '';
            $fields['ic_status'] = $conta['ic_status'];
            $fields['id_cliente'] = $conta['id_cliente'];
            $fields['ds_img_cliente'] = $conta['ds_img_cliente'];
            $fields['ic_preencher_folha'] = $conta['ic_preencher_folha'];
            $fields['ic_teto_gastos'] = $conta['ic_teto_gastos'];
            $fields['ic_analise_financeira'] = $conta['ic_analise_financeira'];
            $fields['ic_faturamento'] = $conta['ic_faturamento'];
            $fields['ic_nf_gerar'] = $conta['ic_nf_gerar'];
            $fields['ic_boleto'] = $conta['ic_boleto'];
            $fields['ds_dominio'] = $conta['ds_dominio'];
            $fields['ds_cei'] = $conta['ds_cei'];

            $fields["dt_ult_atualizacao"] = "sysdate()";
            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

            if($conta['pk']  == ""){

                $fields["dt_cadastro"] = "sysdate()";
                $fields["usuario_cadastro_pk"]   =  $_SESSION['session_user']['par1'];

                $pk = Util::execInsert("contas", $fields,$this->pdo);
                
                $retorno->status = true;
                $retorno->message = 'Dados cadastrados com sucesso';
                $retorno->data = $pk;
                //SERVIDOR
                $this->registrarContaCliente($retorno->data);
            }
            else{
                Util::execUpdate("contas", $fields, " pk = ".$conta['pk'],$this->pdo);
                $pk = $conta['pk'];
                $retorno->status = true;
                $retorno->message = 'Dados atualizado com sucesso';
                $retorno->data = $pk;
                //SERVIDOR
                $this->atualizarContaCliente($retorno->data);
            }     
            return $retorno;
        }
        catch(Throwable $th){
            print_r($th->getMessage());
            die();
        }
       

    }

    public function listarTodos(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $SQL = " select  pk 
                        ,dt_cadastro
                        , usuario_cadastro_pk
                        , dt_ult_atualizacao
                        , usuario_ult_atualizacao_pk 
                        ,ds_tipo_pessoa
                        ,ds_conta 
                        ,ds_razao_social 
                        ,ds_cpf_cnpj 
                        ,ds_cnae 
                        ,ds_rg 
                        ,ds_tel 
                        ,ds_cel 
                        ,ds_email 
                        ,ds_cel 
                        ,ds_cep 
                        ,ds_endereco 
                        ,ds_numero 
                        ,ds_complemento 
                        ,ds_bairro 
                        ,ds_cidade 
                        ,dt_ativacao 
                        ,dt_cancelamento 
                        ,ic_status 
                        ,id_cliente 
                        ,ds_img_cliente 
                        ,tipo_conta_pk 
                        ,ic_preencher_folha 
                        ,ic_teto_gastos 
                        ,ic_analise_financeira 
                        ,ic_faturamento 
                        ,ic_nf_gerar 
                        ,ic_boleto 
                from contas 
                where ic_status=1 
                order by ds_tipo_pessoa asc";
        $stmt = $this->pdo->prepare( $SQL );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }
    public function listarEmpresasCnpj(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $SQL = "select pk 
                    ,dt_cadastro
                    , usuario_cadastro_pk
                    , dt_ult_atualizacao
                    , usuario_ult_atualizacao_pk 
                    ,ds_tipo_pessoa 
                    ,concat(ds_razao_social,' - CNPJ:',ds_cpf_cnpj) ds_conta
                from contas 
                where ic_status=1 
                order by ds_tipo_pessoa asc";
        $stmt = $this->pdo->prepare( $SQL );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }
    public function listarPorPk($pk) {
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql = "";
        $sql .= "select c.pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk,  ";
        $sql .= "       c.ds_tipo_pessoa,";
        $sql .= "       c.ds_conta,";
        $sql .= "       c.ds_razao_social,";
        $sql .= "       c.ds_cpf_cnpj,";
        $sql .= "       c.ds_cei,";
        $sql .= "       c.ds_cnae,";
        $sql .= "       c.ds_rg,";
        $sql .= "       c.ds_tel,";
        $sql .= "       c.ds_email,";
        $sql .= "       c.ds_cel,";
        $sql .= "       c.ds_cep,";
        $sql .= "       c.ds_endereco,";
        $sql .= "       c.ds_numero,";
        $sql .= "       c.ds_complemento,";
        $sql .= "       c.ds_bairro,";
        $sql .= "       c.ds_cidade,";
        $sql .= "       c.ds_uf,";
        $sql .= "       DATE_FORMAT(c.dt_ativacao, '%d/%m/%Y') dt_ativacao,";
        $sql .= "       DATE_FORMAT(c.dt_cancelamento, '%d/%m/%Y') dt_cancelamento,";
        $sql .= "       c.ic_status,";
        $sql .= "       c.id_cliente,";
        $sql .= "       c.ds_img_cliente,";
        $sql .= "       c.tipo_conta_pk,";
        $sql .= "       c.ic_preencher_folha,";
        $sql .= "       c.ic_teto_gastos,";
        $sql .= "       c.ic_analise_financeira,";
        $sql .= "       c.ic_faturamento,";
        $sql .= "       c.ic_nf_gerar,";
        $sql .= "       c.ic_boleto,";
        $sql .= "       c.ds_dominio";
        $sql .= "  from contas c";
        $sql .= " Where c.ic_status  = 1 ";
        if($pk!=""){
            $sql .= " and c.pk = $pk ";
        }

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }
    public function verificarContaPorCNPJ($cnpj) {
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql = "";
        $sql .= "select c.pk";
        $sql .= "  from contas c";
        $sql .= " Where REPLACE(REPLACE(REPLACE(c.ds_cpf_cnpj, '.', ''), '-', ''), '/', '') = '".$cnpj. "'";
       
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
       
        return $rows[0]['pk'];
    }
    
    public function listar_por_ds_conta($ic_tipo_lead, $ds_conta, $ds_razao_social, $ds_cpf_cnpj, $ic_status){
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
                            ds_conta LIKE '%".$pesq."%' 
                        )";
        }

        $sql = "";
        $sql .= "select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql .= "       ,CASE ds_tipo_pessoa WHEN 1 THEN 'PF' WHEN 2 THEN 'PJ' END ds_tipo_pessoa";        
        $sql .= "       ,ds_conta ";
        $sql .= "       ,ds_razao_social ";
        $sql .= "       ,ds_cpf_cnpj ";
        $sql .= "       ,ds_cnae ";
        $sql .= "       ,ds_rg ";
        $sql .= "       ,ds_tel ";
        $sql .= "       ,ds_email ";
        $sql .= "       ,ds_cel ";
        $sql .= "       ,ds_cep ";
        $sql .= "       ,ds_endereco ";
        $sql .= "       ,ds_numero ";
        $sql .= "       ,ds_complemento ";
        $sql .= "       ,ds_bairro ";
        $sql .= "       ,ds_cidade ";
		$sql .= "		,ds_img_cliente ";
		$sql .= "		,tipo_conta_pk ";
        $sql .= "       ,date_format(dt_ativacao,'%d/%m/%Y')dt_ativacao";
        $sql .= "       ,dt_cancelamento ";
        $sql .= "       ,CASE ic_status WHEN 1 THEN 'Ativo' WHEN 2 THEN 'Inativo' END ic_status"; 
        $sql .= "       ,CASE tipo_conta_pk WHEN 1 THEN 'Principal' WHEN 2 THEN 'Secundária' END ds_tipo_conta"; 
        $sql .= "		,ic_preencher_folha ";
        $sql .= "		,ic_teto_gastos ";
        $sql .= "		,ic_analise_financeira ";
        $sql .= "		,ic_faturamento ";
        $sql .= "		,ic_nf_gerar ";
        $sql .= "		,ic_boleto ";

        $sql .= "  from contas ";
        $sql .= " where 1=1 ";
        $sql.= $search;
        if ($ic_tipo_lead != "") {
            $sql .= " and ds_tipo_pessoa =" . $ic_tipo_lead ;
        }
        
        if ($ds_conta != "") {
            $sql .= " and ds_conta like '%" . $ds_conta."%'"; ;
        }
        
        if ($ds_razao_social != "") {
            $sql .= " and ds_razao_social like '%" . $ds_razao_social."%'"; 
        }
        
        if ($ds_cpf_cnpj != "") {
            $sql .= " and ds_cpf_cnpj = '" . $ds_cpf_cnpj."'"; 
        }
        
        if ($ic_status != "") {
            $sql .= " and ic_status = " . $ic_status; 
        }        
        
        $sql .= " order by ds_tipo_pessoa asc ";


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

    public function configModulo() {

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql = "";
        $sql .= "SELECT";
        $sql .= "		ic_preencher_folha ";
        $sql .= "		,ic_teto_gastos ";
        $sql .= "		,ic_analise_financeira ";
        $sql .= "		,ic_faturamento ";
        $sql .= "		,ic_nf_gerar ";
        $sql .= "		,ic_boleto ";
        $sql .= "  FROM contas ";
        $sql .= " WHERE tipo_conta_pk = 1";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }

    public function verificaContaPrincipal() {

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql = "";
        $sql .= "SELECT pk";
        $sql .= "  FROM contas ";
        $sql .= " WHERE tipo_conta_pk = 1";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }
    public function pegarPkPorRazaoSocial($ds_razao_social) {

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql = "";
        $sql .= "SELECT pk";
        $sql .= "  FROM contas ";
        $sql .= " WHERE ds_razao_social = '".$ds_razao_social."'";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $rows[0]['pk'];
    }


    public function registrarContaCliente($pk){
        $sql ="";
        $sql = "";
        $sql .= "select c.pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk,  ";
        $sql .= "       c.ds_tipo_pessoa,";
        $sql .= "       c.ds_conta,";
        $sql .= "       c.ds_razao_social,";
        $sql .= "       c.ds_cpf_cnpj,";
        $sql .= "       c.ds_cei,";
        $sql .= "       c.ds_cnae,";
        $sql .= "       c.ds_rg,";
        $sql .= "       c.ds_tel,";
        $sql .= "       c.ds_email,";
        $sql .= "       c.ds_cel,";
        $sql .= "       c.ds_cep,";
        $sql .= "       c.ds_endereco,";
        $sql .= "       c.ds_numero,";
        $sql .= "       c.ds_complemento,";
        $sql .= "       c.ds_bairro,";
        $sql .= "       c.ds_cidade,";
        $sql .= "       c.ds_uf,";
        $sql .= "       DATE_FORMAT(c.dt_ativacao, '%d/%m/%Y') dt_ativacao,";
        $sql .= "       DATE_FORMAT(c.dt_cancelamento, '%d/%m/%Y') dt_cancelamento,";
        $sql .= "       c.ic_status,";
        $sql .= "       c.id_cliente,";
        $sql .= "       c.ds_img_cliente,";
        $sql .= "       c.tipo_conta_pk,";
        $sql .= "       c.ic_preencher_folha,";
        $sql .= "       c.ic_teto_gastos,";
        $sql .= "       c.ic_analise_financeira,";
        $sql .= "       c.ic_faturamento,";
        $sql .= "       c.ic_nf_gerar,";
        $sql .= "       c.ic_boleto,";
        $sql .= "       c.ds_dominio";
        $sql .= "  from contas c";
        $sql .= " Where 1 = 1 ";
        if($pk!=""){
            $sql .= " and c.pk = $pk ";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        // Obter o nome do host (ex: www.example.com)
        $host = $_SERVER['HTTP_HOST'];
        // Montar a URL completa
        $currentUrl = $protocol . $host;

        $body = [
            'ds_razao_social'  => $rows[0]['ds_razao_social'],
            'ds_lead'  => $rows[0]['ds_conta'],
            'ds_cpf_cnpj'  => $rows[0]['ds_cpf_cnpj'],
            'id_cliente'  => $rows[0]['id_cliente'],
            'ic_status'  => $rows[0]['ic_status'],
            'contas_pk'  => $pk,
            'ds_link' =>$currentUrl,
        ];

        $request = $this->client->request('POST','https://webservice.gepros6.com.br/work/action.php?action=registrarContaCliente', [
            'json'=>$body
        ]);
    
        $code = $request->getStatusCode();
        $response = $request->getBody()->getContents();
        $data =  json_decode($response);
        
        return true;

    }   
    public function atualizarContaCliente($pk){
        $sql ="";
        $sql = "";
        $sql .= "select c.pk, c.dt_cadastro, c.usuario_cadastro_pk, c.dt_ult_atualizacao, c.usuario_ult_atualizacao_pk,  ";
        $sql .= "       c.ds_tipo_pessoa,";
        $sql .= "       c.ds_conta,";
        $sql .= "       c.ds_razao_social,";
        $sql .= "       c.ds_cpf_cnpj,";
        $sql .= "       c.ds_cei,";
        $sql .= "       c.ds_cnae,";
        $sql .= "       c.ds_rg,";
        $sql .= "       c.ds_tel,";
        $sql .= "       c.ds_email,";
        $sql .= "       c.ds_cel,";
        $sql .= "       c.ds_cep,";
        $sql .= "       c.ds_endereco,";
        $sql .= "       c.ds_numero,";
        $sql .= "       c.ds_complemento,";
        $sql .= "       c.ds_bairro,";
        $sql .= "       c.ds_cidade,";
        $sql .= "       c.ds_uf,";
        $sql .= "       DATE_FORMAT(c.dt_ativacao, '%d/%m/%Y') dt_ativacao,";
        $sql .= "       DATE_FORMAT(c.dt_cancelamento, '%d/%m/%Y') dt_cancelamento,";
        $sql .= "       c.ic_status,";
        $sql .= "       c.id_cliente,";
        $sql .= "       c.ds_img_cliente,";
        $sql .= "       c.tipo_conta_pk,";
        $sql .= "       c.ic_preencher_folha,";
        $sql .= "       c.ic_teto_gastos,";
        $sql .= "       c.ic_analise_financeira,";
        $sql .= "       c.ic_faturamento,";
        $sql .= "       c.ic_nf_gerar,";
        $sql .= "       c.ic_boleto,";
        $sql .= "       c.ds_dominio";
        $sql .= "  from contas c";
        $sql .= " Where 1 = 1 ";
        if($pk!=""){
            $sql .= " and c.pk = $pk ";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        // Obter o nome do host (ex: www.example.com)
        $host = $_SERVER['HTTP_HOST'];
        // Montar a URL completa
        $currentUrl = $protocol . $host;

        $body = [
            'ds_razao_social'  => $rows[0]['ds_razao_social'],
            'ds_lead'  => $rows[0]['ds_conta'],
            'ds_cpf_cnpj'  => $rows[0]['ds_cpf_cnpj'],
            'id_cliente'  => $rows[0]['id_cliente'],
            'ic_status'  => $rows[0]['ic_status'],
            'contas_pk'  => $pk,
            'ds_link' =>$currentUrl,
        ];

        $request = $this->client->request('POST','https://webservice.gepros6.com.br/work/action.php?action=atualizarContaCliente', [
            'json'=>$body
        ]);
    
        $code = $request->getStatusCode();
        $response = $request->getBody()->getContents();
        $data =  json_decode($response);


        return true;

    }   

}
