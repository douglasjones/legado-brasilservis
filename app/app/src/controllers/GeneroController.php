<?php

namespace App\Controller;

use App\Model\Log;
use App\Model\Genero;
use App\Utils\Json;
use App\Utils\Session;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Utils\SlimImage;
use App\Utils\SlimStatus;
use Throwable;

final class GeneroController extends BaseController {
    public function listarTodos(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $ds_genero = isset($data['ds_genero'])? $data['ds_genero'] : "";
            $retorno = (new Genero($this->pdo))->listarTodos($ds_genero);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}
