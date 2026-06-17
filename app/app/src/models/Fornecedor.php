<?php

namespace App\Model;

use App\Utils\Session;
use App\Utils\Util;
use App\Utils\Validation;

class Fornecedor {

	public $pdo;

	public function __construct($pdo) {
		$this->pdo = $pdo;
	}

    public function excluir($pk){
        Util::execDelete('fornecedor', ' pk='.$pk, $this->pdo);
    }

    public function listarGrid($ds_fornecedor,$ic_status){
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
                            f.ds_fornecedor LIKE '%".$pesq."%'
                        )";
        }
        
        $sql ="";
        $sql.="select f.pk t_pk, f.dt_cadastro, f.usuario_cadastro_pk, f.dt_ult_atualizacao, f.usuario_ult_atualizacao_pk ";
        $sql.="       ,f.ds_fornecedor t_ds_fornecedor";
        $sql.="       ,f.ds_ddd t_ds_ddd";
        $sql.="       ,f.ds_tel t_ds_tel";
        $sql.="       ,f.ds_email t_ds_email";
        $sql.="       ,f.categorias_produto_pk ";
        $sql.="       ,f.ic_status t_ic_status";
        $sql.="       ,f.ds_cpf_cnpj";
        $sql.="       ,f.ds_razao_social";
        $sql.="       ,f.ds_endereco";
        $sql.="       ,f.ds_numero";
        $sql.="       ,f.ds_complemento";
        $sql.="       ,f.ds_bairro";
        $sql.="       ,f.ds_cidade";
        $sql.="       ,f.ds_uf";
        $sql.="       ,f.ds_cep";
        $sql.="       ,f.ds_contato";
        $sql.="       ,f.tipo_conta_bancaria";
        $sql.="       ,f.ds_agencia";
        $sql.="       ,f.ds_conta";
        $sql.="       ,f.bancos_pk";
        $sql.="       ,f.ds_digito";
        $sql.="       ,f.vl_salario";
        $sql.="       ,cp.ds_categoria t_ds_categoria";
        $sql.="  from fornecedor f";
        $sql.="  left join categorias_produto cp on f.categorias_produto_pk = cp.pk  ";
        $sql.=" where 1=1 ";
        if($ds_fornecedor != ""){
            $sql.=" and f.ds_fornecedor like '%".$ds_fornecedor."%' ";
        }
        if($ic_status != ""){
            $sql.=" and f.ic_status = ".$ic_status;
        }
        $sql.=$search;
        $sql.=" order by f.ds_fornecedor asc ";

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

    public function salvar($fornecedor){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_fornecedor'] = $fornecedor['ds_fornecedor'];
        $fields['ds_ddd'] = $fornecedor['ds_ddd'];
        $fields['ds_tel'] = $fornecedor['ds_tel'];
        $fields['ds_email'] = $fornecedor['ds_email'];
        $fields['categorias_produto_pk'] = $fornecedor['categorias_produto_pk'];
        $fields['ic_status'] = $fornecedor['ic_status'];
        $fields['ds_cpf_cnpj'] = $fornecedor['ds_cpf_cnpj'];
        $fields['ds_razao_social'] = $fornecedor['ds_razao_social'];
        $fields['ds_endereco'] = $fornecedor['ds_endereco'];
        $fields['ds_numero'] = $fornecedor['ds_numero'];
        $fields['ds_complemento'] = $fornecedor['ds_complemento'];
        $fields['ds_bairro'] = $fornecedor['ds_bairro'];
        $fields['ds_cidade'] = $fornecedor['ds_cidade'];
        $fields['ds_uf'] = $fornecedor['ds_uf'];
        $fields['ds_cep'] = $fornecedor['ds_cep'];
        $fields['ds_contato'] = $fornecedor['ds_contato'];
        $fields['tipo_conta_bancaria'] = $fornecedor['tipo_conta_bancaria'];
        $fields['ds_agencia'] = $fornecedor['ds_agencia'];
        $fields['ds_conta'] = $fornecedor['ds_conta'];
        $fields['bancos_pk'] = $fornecedor['bancos_pk'];
        $fields['ds_digito'] = $fornecedor['ds_digito'];
        $fields['vl_salario'] = $fornecedor['vl_salario'];
        $fields['ds_pix'] = $fornecedor['ds_pix'];
        $fields['ds_favorecido_pix'] = $fornecedor['ds_favorecido_pix'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($fornecedor['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];


            $pk = Util::execInsert("fornecedor", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("fornecedor", $fields, " pk = ".$fornecedor['pk'],$this->pdo);
            $pk = $fornecedor['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;

    }

    public function listarPorPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql.="       ,ds_fornecedor ";
        $sql.="       ,ds_ddd ";
        $sql.="       ,ds_tel ";
        $sql.="       ,ds_email ";
        $sql.="       ,categorias_produto_pk ";
        $sql.="       ,ic_status ";
        $sql.="       ,ds_cpf_cnpj";
        $sql.="       ,ds_razao_social";
        $sql.="       ,ds_endereco";
        $sql.="       ,ds_numero";
        $sql.="       ,ds_complemento";
        $sql.="       ,ds_bairro";
        $sql.="       ,ds_cidade";
        $sql.="       ,ds_uf";
        $sql.="       ,ds_cep";
        $sql.="       ,ds_contato";
        $sql.="       ,tipo_conta_bancaria";
        $sql.="       ,ds_agencia";
        $sql.="       ,ds_conta";
        $sql.="       ,bancos_pk";
        $sql.="       ,ds_digito";
        $sql.="       ,vl_salario";
        $sql.="       ,ds_pix";
        $sql.="       ,ds_favorecido_pix";
        //coloca as duas colunas de pix e favorecido igual está no banco de dados

        $sql.="  from fornecedor ";
        $sql.=" where pk = $pk ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function listarPorCategoria($categorias_produto_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select f.pk, f.dt_cadastro, f.usuario_cadastro_pk, f.dt_ult_atualizacao, f.usuario_ult_atualizacao_pk ";
        $sql.="       ,f.ds_fornecedor ";
        $sql.="       ,f.ds_ddd ";
        $sql.="       ,f.ds_tel ";
        $sql.="       ,f.ds_email ";
        $sql.="       ,f.categorias_produto_pk ";
        $sql.="       ,f.ic_status ";
        $sql.="       ,f.ds_cpf_cnpj";
        $sql.="       ,f.ds_razao_social";
        $sql.="       ,f.ds_endereco";
        $sql.="       ,f.ds_numero";
        $sql.="       ,f.ds_complemento";
        $sql.="       ,f.ds_bairro";
        $sql.="       ,f.ds_cidade";
        $sql.="       ,f.ds_uf";
        $sql.="       ,f.ds_cep";
        $sql.="       ,f.ds_contato";
        $sql.="       ,f.tipo_conta_bancaria";
        $sql.="       ,f.ds_agencia";
        $sql.="       ,f.ds_conta";
        $sql.="       ,f.bancos_pk";
        $sql.="       ,f.ds_digito";
        $sql.="       ,f.vl_salario";
        $sql.="       ,cp.ds_categoria ";
        $sql.="  from fornecedor f";
        $sql.="  left join categorias_produto cp on f.categorias_produto_pk = cp.pk  ";
        $sql.=" where 1=1 ";
        if($categorias_produto_pk != ""){
            $sql.=" and f.categorias_produto_pk = ".$categorias_produto_pk;
        }
        $sql.=" order by f.ds_fornecedor asc ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function listarTodos(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql.="       ,ds_fornecedor ";
        $sql.="       ,ds_ddd ";
        $sql.="       ,ds_tel ";
        $sql.="       ,ds_email ";
        $sql.="       ,categorias_produto_pk ";
        $sql.="       ,ic_status ";
        $sql.="       ,ds_cpf_cnpj";
        $sql.="       ,ds_razao_social";
        $sql.="       ,ds_endereco";
        $sql.="       ,ds_numero";
        $sql.="       ,ds_complemento";
        $sql.="       ,ds_bairro";
        $sql.="       ,ds_cidade";
        $sql.="       ,ds_uf";
        $sql.="       ,ds_cep";
        $sql.="       ,ds_contato";
        $sql.="       ,tipo_conta_bancaria";
        $sql.="       ,ds_agencia";
        $sql.="       ,ds_conta";
        $sql.="       ,bancos_pk";
        $sql.="       ,ds_digito";
        $sql.="       ,vl_salario";
        $sql.="       ,ds_pix";
        $sql.="       ,ds_favorecido_pix";
        //coloca as duas colunas de pix e favorecido igual está no banco de dados

        $sql.="  from fornecedor ";


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listarCpfCnpjFornecedor(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk";
        $sql.="       ,ds_cpf_cnpj";

        $sql.="  from fornecedor ";
        $sql.="  where ds_cpf_cnpj <> '' ";


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    private function formatarCNPJ($cnpj) {
        $cnpj = preg_replace('/\D/', '', $cnpj); // só números
        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $cnpj);
    }
    public function verificarFornecedorPorCNPJXML($cnpj,$ds_razao_social){

       // Normaliza para buscar (sem máscara)
        $cnpjNumerico = preg_replace('/\D/', '', $cnpj);

        // Query de busca
        $sql  = "SELECT pk
                FROM fornecedor
                WHERE REPLACE(REPLACE(REPLACE(ds_cpf_cnpj, '.', ''), '-', ''), '/', '') = :cnpj";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':cnpj', $cnpjNumerico);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row) {
            $pk = $row['pk'];
        } else {
            // 🔹 Formata antes de salvar
            $cnpjFormatado = $this->formatarCNPJ($cnpjNumerico);

            $fields = array();
            $fields["dt_ult_atualizacao"]     = date('Y-m-d H:i:s');
            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

            $fields["dt_cadastro"]            = date('Y-m-d H:i:s');
            $fields["usuario_cadastro_pk"]    = $_SESSION['session_user']['par1'];
            $fields['ds_fornecedor']          = $ds_razao_social;
            $fields['ic_status']              = 1;
            $fields['ds_cpf_cnpj']            = $cnpjFormatado; // ✅ agora com máscara
            $fields['ds_razao_social']        = $ds_razao_social;

            $pk = Util::execInsert("fornecedor", $fields, $this->pdo);
        }

        return $pk;

    }
}
