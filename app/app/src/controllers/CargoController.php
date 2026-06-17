<?php

namespace App\Controller;

use App\Model\Cargo;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class CargoController extends BaseController {


    public function listarTodos(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $entity = new Cargo($this->pdo);
            $ds_cargo = isset($data['ds_cargo'])? $data['ds_cargo'] : "";
            $retorno = $entity->listar_por_ds_cargo($ds_cargo);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}