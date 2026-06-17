<?php

namespace App\Controller;

use App\Utils\Json;
use App\Model\Ronda;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class RondaController extends BaseController {


    public function relRondas(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $leads_clientes_pk = isset($data['leads_clientes_pk'])? $data['leads_clientes_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $dt_ini_ronda = isset($data['dt_ini_ronda'])? $data['dt_ini_ronda']: "";
            $dt_fim_ronda = isset($data['dt_fim_ronda'])? $data['dt_fim_ronda']: "";

            $retorno = (new Ronda($this->pdo))->relRondas($leads_pk,$leads_clientes_pk,$dt_ini_ronda,$dt_fim_ronda);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

}