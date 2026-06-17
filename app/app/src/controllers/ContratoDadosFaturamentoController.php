<?php

namespace App\Controller;

use App\Model\Cargo;
use App\Model\ContratoDadosFaturamento;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class ContratoDadosFaturamentoController extends BaseController {


    public function listarGridContratoDadosFaturamento(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $entity = new ContratoDadosFaturamento($this->pdo);
            $contratos_pk = isset($data['contrato_pk'])? $data['contrato_pk'] : "";
            $entity->listarGridContratoDadosFaturamento($contratos_pk);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function addMes(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $entity = new ContratoDadosFaturamento($this->pdo);
            $dt_base = isset($data['dt_base'])? $data['dt_base'] : "";
            $mes = isset($data['mes'])? $data['mes'] : "";
            $retorno = $entity->addMes($dt_base,$mes);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}