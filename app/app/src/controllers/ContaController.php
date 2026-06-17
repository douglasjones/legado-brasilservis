<?php

namespace App\Controller;

use App\Model\Conta;
use App\Model\ContaConfigNota;
use App\Model\NfeApi;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class ContaController extends BaseController {

    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'conta/conta_res.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function excluir(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";
            if($pk!=""){
                (new Conta($this->pdo))->excluir($pk);
                Json::run(true, [], 'Registro excluido com sucesso!');
            }
            else{

                Json::run(false, [], 'Falha ao excluir registro!');
            }
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
	public function ativar(Request $request, Response $response, $args)
    {
        try{
            
            (new Conta($this->pdo))->updateAll(1);
            Json::run(true, [], 'Registro excluido com sucesso!');

        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function Desativar(Request $request, Response $response, $args)
    {
        try{
            
            (new Conta($this->pdo))->updateAll(2);
            Json::run(true, [], 'Registro excluido com sucesso!');

        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function salvar(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            

            $pk = isset($data['pk'])? $data['pk'] : "";
            $ds_tipo_pessoa = isset($data['ds_tipo_pessoa'])? $data['ds_tipo_pessoa'] : "";
            $ds_conta = isset($data['ds_conta'])? $data['ds_conta'] : "";
            $ds_razao_social = isset($data['ds_razao_social'])? $data['ds_razao_social'] : "";
            $ds_cpf_cnpj = isset($data['ds_cpf_cnpj'])? $data['ds_cpf_cnpj'] : "";
            $ds_cnae = isset($data['ds_cnae'])? $data['ds_cnae'] : "";
            $ds_rg = isset($data['ds_rg'])? $data['ds_rg'] : "";
            $ds_tel = isset($data['ds_tel'])? $data['ds_tel'] : "";
            $ds_email = isset($data['ds_email'])? $data['ds_email'] : "";
            $ds_cel = isset($data['ds_cel'])? $data['ds_cel'] : "";
            $ds_cep = isset($data['ds_cep'])? $data['ds_cep'] : "";
            $ds_endereco = isset($data['ds_endereco'])? $data['ds_endereco'] : "";
            $ds_numero = isset($data['ds_numero'])? $data['ds_numero'] : "";
            $ds_complemento = isset($data['ds_complemento'])? $data['ds_complemento'] : "";
            $ds_bairro = isset($data['ds_bairro'])? $data['ds_bairro'] : "";
            $ds_cidade = isset($data['ds_cidade'])? $data['ds_cidade'] : "";
            $ds_uf = isset($data['ds_uf'])? $data['ds_uf'] : "";
            $dt_ativacao = isset($data['dt_ativacao'])? $data['dt_ativacao'] : "";
            $dt_cancelamento = isset($data['dt_cancelamento'])? $data['dt_cancelamento'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $id_cliente = isset($data['id_cliente'])? $data['id_cliente'] : "";
            $ds_img_cliente = isset($data['ds_img_cliente'])? $data['ds_img_cliente'] : "";
            $tipo_conta_pk = isset($data['tipo_conta_pk'])? $data['tipo_conta_pk'] : "";
            $ic_preencher_folha = isset($data['ic_preencher_folha'])? $data['ic_preencher_folha'] : "";
            $ic_teto_gastos = isset($data['ic_teto_gastos'])? $data['ic_teto_gastos'] : "";
            $ic_analise_financeira = isset($data['ic_analise_financeira'])? $data['ic_analise_financeira'] : "";
            $ic_faturamento = isset($data['ic_faturamento'])? $data['ic_faturamento'] : "";
            $ic_nf_gerar = isset($data['ic_nf_gerar'])? $data['ic_nf_gerar'] : "";
            $ic_boleto = isset($data['ic_boleto'])? $data['ic_boleto'] : "";
            $ds_dominio = isset($data['ds_dominio'])? $data['ds_dominio'] : "";
            $ds_cei = isset($data['ds_cei'])? $data['ds_cei'] : "";
            
            $conta =[
                "pk"=>$pk,
                "ds_tipo_pessoa"=>$ds_tipo_pessoa,
                "ds_conta"=>$ds_conta,
                "ds_razao_social"=>$ds_razao_social,
                "ds_cpf_cnpj"=>$ds_cpf_cnpj,
                "ds_cnae"=>$ds_cnae,
                "ds_rg"=>$ds_rg,
                "ds_tel"=>$ds_tel,
                "ds_email"=>$ds_email,
                "ds_cel"=>$ds_cel,
                "ds_cep"=>$ds_cep,
                "ds_endereco"=>$ds_endereco,
                "ds_numero"=>$ds_numero,
                "ds_complemento"=>$ds_complemento,
                "ds_bairro"=>$ds_bairro,
                "ds_cidade"=>$ds_cidade,
                "ds_uf"=>$ds_uf,
                "dt_ativacao"=>$dt_ativacao,
                "dt_cancelamento"=>$dt_cancelamento,
                "ic_status"=>$ic_status,
                "id_cliente"=>$id_cliente,
                "ds_img_cliente"=>$ds_img_cliente,
                "tipo_conta_pk"=>$tipo_conta_pk,
                "ic_preencher_folha"=>$ic_preencher_folha,
                "ic_teto_gastos"=>$ic_teto_gastos,
                "ic_analise_financeira"=>$ic_analise_financeira,
                "ic_faturamento"=>$ic_faturamento,
                "ic_nf_gerar"=>$ic_nf_gerar,
                "ic_boleto"=>$ic_boleto,
                "ds_dominio"=>$ds_dominio,
                "ds_cei"=>$ds_cei
            ];

            $retorno = (new Conta($this->pdo))->salvar($conta);
            //if($pk!=""){
               /* $body = [
                    'ds_dominio'  => $ds_dominio,
                    'contas_origem_pk'  => $pk,
                    'ds_lead'  => $ds_conta,
                    'ds_razao_social'  => $ds_razao_social,
                    'ic_status'  => $ic_nf_gerar,
                    'ds_cnpj' => $ds_cpf_cnpj
                ];
    
                $retornoContaLead = (new NfeApi())->contaLeadSalvar($body);*/
                Json::run($retorno->status,$retorno->data,$retorno->message);
            
            //}
            //if($tipo_conta_pk == 1){
            //}else{
                //Json::run($retorno->status,$retorno->data,$retorno->message);
            //}
            
        
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }
    public function carregarLogo(Request $request, Response $response, $args) {
        try{
            $entity = new Conta($this->pdo);
            $retorno = $entity->listarTodos();
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }
    public function listarDataTable(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();

            $ic_tipo_lead = isset($data['ic_tipo_lead'])? $data['ic_tipo_lead'] : "";
            $ds_conta = isset($data['ds_conta'])? $data['ds_conta'] : "";
            $ds_razao_social = isset($data['ds_razao_social'])? $data['ds_razao_social'] : "";
            $ds_cpf_cnpj = isset($data['ds_cpf_cnpj'])? $data['ds_cpf_cnpj'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";

            (new Conta($this->pdo))->listar_por_ds_conta($ic_tipo_lead, $ds_conta, $ds_razao_social, $ds_cpf_cnpj, $ic_status);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarPk(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $entity = new Conta($this->pdo);
            $retorno = $entity->listarPorPk($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }
    public function editarConta(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";

            $this->view->render($response, 'conta/conta_cad.twig',array(
                    'pk'=>$pk
                )
            );
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function verificarConta(Request $request, Response $response, $args) {
        try{
            $entity = new Conta($this->pdo);
            $retorno = $entity->listarTodos();
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }
    public function listarTodos(Request $request, Response $response, $args) {
        try{
            $entity = new Conta($this->pdo);
            $retorno = $entity->listarTodos();
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }
    public function listarEmpresasCnpj(Request $request, Response $response, $args) {
        try{
            $entity = new Conta($this->pdo);
            $retorno = $entity->listarEmpresasCnpj();
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }
    public function configModulo(Request $request, Response $response, $args) {
        try{
            $entity = new Conta($this->pdo);
            $retorno = $entity->configModulo();
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }
    public function verificaContaPrincipal(Request $request, Response $response, $args) {
        try{
            $entity = new Conta($this->pdo);
            $retorno = $entity->verificaContaPrincipal();
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }

}