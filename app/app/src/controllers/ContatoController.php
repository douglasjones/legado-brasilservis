<?php

namespace App\Controller;

use App\Model\Log;
use App\Utils\Json;
use App\Utils\Util;
use App\Model\Contato;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class ContatoController extends BaseController {

    public function excluir(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";
            if($pk!=""){
                (new Log($this->pdo))->salvar('contato',$pk);
                (new Contato($this->pdo))->excluir($pk);
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

    public function salvar(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $ds_contato = isset($data['ds_contato'])? $data['ds_contato'] : "";
            $ds_email = isset($data['ds_email'])? $data['ds_email'] : "";
            $ds_cel = isset($data['ds_cel'])? $data['ds_cel'] : "";
            $ds_tel = isset($data['ds_tel'])? $data['ds_tel'] : "";
            $ic_whatsapp = isset($data['ic_whatsapp'])? $data['ic_whatsapp'] : "";
            $cargos_pk = isset($data['cargos_pk'])? $data['cargos_pk'] : "";

            $contato =[
                "pk"=>$pk,
                "leads_pk"=>$leads_pk,
                "ds_contato"=>$ds_contato,
                "ds_email"=>$ds_email,
                "ds_cel"=>$ds_cel,
                "ds_tel"=>$ds_tel,
                "ic_whatsapp"=>$ic_whatsapp,
                "cargos_pk"=>$cargos_pk,
            ];

            $retorno = (new Contato($this->pdo))->salvar($contato);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarDataTable(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead'] : "";
            $ds_lead_grid = isset($data['ds_lead_grid'])? $data['ds_lead_grid'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $supervisores_pk = isset($data['supervisores_pk'])? $data['supervisores_pk'] : "";
            $responsavel_pk = isset($data['responsavel_pk'])? $data['responsavel_pk'] : "";
            $ic_tipo_lead = isset($data['ic_tipo_lead'])? $data['ic_tipo_lead'] : "";
            $leads_pai_pk = isset($data['leads_pai_pk'])? $data['leads_pai_pk'] : "";
            $leads_clientes_pk = isset($data['leads_clientes_pk'])? $data['leads_clientes_pk'] : "";

            (new Lead($this->pdo))->listar_por_ds_lead($ds_lead,$ic_status,$supervisores_pk,$responsavel_pk,$ds_lead_grid,$ic_tipo_lead, $leads_pai_pk, $leads_clientes_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarContatoLead(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";

            (new Contato($this->pdo))->carregarPorLeadsPk($leads_pk);

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

            $retorno = (new Lead($this->pdo))->listarPorPk($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarTodos(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead'] : "";

            $retorno = (new Lead($this->pdo))->listarTodos($ds_lead);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarTodosClientes(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pai_pk = isset($data['leads_pai_pk'])? $data['leads_pai_pk'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $ic_tipo_lead = isset($data['ic_tipo_lead'])? $data['ic_tipo_lead'] : "";

            $retorno = (new Lead($this->pdo))->listar_por_ds_lead_pai_pk($ic_tipo_lead, $leads_pai_pk,$ic_status);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarLeadPai(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();

            $retorno = (new Lead($this->pdo))->listarLeadPai();
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function verificarCNPJ(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $cpfcnpj = isset($data['ds_cpf_cnpj'])? $data['ds_cpf_cnpj'] : "";
            $retorno = (new Lead($this->pdo))->verificarCNPJ($cpfcnpj);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}

