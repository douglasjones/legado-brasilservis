<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class ControleNfse {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listarGrid($body){
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
                            ds_categoria LIKE '%".$pesq."%'
                        )";
        }
        
        $sql ="";
        $sql.=" SELECT pk,";
        $sql.="     ds_nome_fantasia,";
        $sql.="     ds_razao_social,";
        $sql.="     ds_cnpj,";
        $sql.="     date_format(dt_cadastro, '%d/%m/%Y') dt_cadastro,";
        $sql.="     date_format(dt_criacao_certificado, '%d/%m/%Y') dt_cadastro,";
        $sql.="     ic_status";
        $sql.=" FROM contas_dados_config_nota";
        $sql.=" WHERE 1=1 ";
        if($ds_categoria != ""){
            $sql.=" AND contas_pk like '%".$ds_categoria."%' ";
        }
        if($ic_status != ""){
            $sql.=" AND ic_status = ".$ic_status;
        } 

       // $sql.= $search;
        $sql.=" order by pk asc ";
        
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

    public function salvar($certificados_empresas){
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio
    
            $fields = array();
            $fields['ds_razao_social'] = $certificados_empresas['ds_razao_social'];
            $fields['ds_nome_fantasia'] = $certificados_empresas['ds_nome_fantasia'];
            $fields['ds_cnpj'] = $certificados_empresas['ds_cnpj'];
            $fields['ds_inscricao_estadual'] = $certificados_empresas['ds_inscricao_estadual'];
            $fields['ds_inscricao_municipal'] = $certificados_empresas['ds_inscricao_municipal'];
            $fields['ic_regime_tributario'] = $certificados_empresas['ic_regime_tributario'];
            $fields['ic_incentivo_fiscal'] = $certificados_empresas['ic_incentivo_fiscal'];
            $fields['ic_incentivo_cultural'] = $certificados_empresas['ic_incentivo_cultural'];
            $fields['ic_regime_tibutario_especial'] = $certificados_empresas['ic_regime_tibutario_especial'];
            $fields['ds_tel'] = $certificados_empresas['ds_tel'];
            $fields['ds_email'] = $certificados_empresas['ds_email'];
            $fields['ds_cep'] = $certificados_empresas['ds_cep'];
            $fields['ds_endereco'] = $certificados_empresas['ds_endereco'];
            $fields['ds_numero'] = $certificados_empresas['ds_numero'];
            $fields['ds_complemento'] = $certificados_empresas['ds_complemento'];
            $fields['ds_bairro'] = $certificados_empresas['ds_bairro'];
            $fields['arq_certificado'] = $certificados_empresas['arq_certificado'];
            $fields['id_certificado'] = $certificados_empresas['id_certificado'];
            $fields['ds_senha_certificado'] = $certificados_empresas['ds_senha_certificado'];
            $fields['dt_criacao_certificado'] = $certificados_empresas['dt_criacao_certificado'];
            $fields['dt_vencimento_certificado'] = $certificados_empresas['dt_vencimento_certificado'];
            $fields['ds_login_prefeitura'] = $certificados_empresas['ds_login_prefeitura'];
            $fields['ds_senha_prefeitura'] = $certificados_empresas['ds_senha_prefeitura'];
            $fields['ds_ult_numero_nota'] = $certificados_empresas['ds_ult_numero_nota'];
            $fields['ds_serie_nota'] = $certificados_empresas['ds_serie_nota'];
            $fields['ds_lote_nota'] = $certificados_empresas['ds_lote_nota'];
            $fields['ic_status'] = $certificados_empresas['ic_status'];
            
            $fields["dt_ult_atualizacao"] = "sysdate()";
            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
    
            if($certificados_empresas['pk']  == ""){
    
                $fields["dt_cadastro"] = "sysdate()";
                $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
    
    
                $pk = Util::execInsert("certificados_empresas", $fields,$this->pdo);
                $retorno->status = true;
                $retorno->message = 'Dados cadastrados com sucesso';
                $retorno->data = $pk;
            }
            else{
                Util::execUpdate("certificados_empresas", $fields, " pk = ".$certificados_empresas['pk'],$this->pdo);
                $pk = $certificados_empresas['pk'];
                $retorno->status = true;
                $retorno->message = 'Dados atualizado com sucesso';
                $retorno->data = $pk;
            }
            return $retorno;
                        
        
    }

    
    public function excluir($pk){
        Util::execDelete('certificados_empresas', ' pk='.$pk, $this->pdo);
    }


}
