<?php

namespace App\Controller;

use App\Model\ProdutoServico;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class ProdutoServicoController extends BaseController {

    public function listarFuncaoColaborador(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $colaboradores_pk = isset($data['pk'])? $data['pk'] : "";
            $retorno = (new ProdutoServico($this->pdo))->listarFuncaoColaborador($colaboradores_pk);
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
            $ds_produto_servico = isset($data['ds_produto_servico'])? $data['ds_produto_servico'] : "";
            $ds_cbo = isset($data['ds_cbo'])? $data['ds_cbo'] : "";

            $retorno = (new ProdutoServico($this->pdo))->listar_por_ds_produto_servico($ds_produto_servico,$ds_cbo);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarProdutosContrato(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $contratos_pk = isset($data['contratos_pk'])? $data['contratos_pk'] : "";

            $retorno = (new ProdutoServico($this->pdo))->listarProdutosContrato($contratos_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarQualificacaoColaboradores(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $colaboradores_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk'] : "";

            $retorno = (new ProdutoServico($this->pdo))->listarQualificacaoColaboradores($colaboradores_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}

