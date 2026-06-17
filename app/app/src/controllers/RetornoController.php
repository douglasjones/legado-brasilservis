<?php

namespace App\Controller;

use App\Utils\Json;
use App\Model\Retorno;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class RetornoController extends BaseController {


    public function listarOcorrenciasPk(Request $request, Response $response, $args) {
        try{
            $entity = new Retorno($this->pdo);
            $data = $request->getQueryParams();
            $ocorrencias_pk = isset($data['ocorrencias_pk'])? $data['ocorrencias_pk'] : "";
            $retorno = $entity->listarPorOcorrenciasPk($ocorrencias_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}