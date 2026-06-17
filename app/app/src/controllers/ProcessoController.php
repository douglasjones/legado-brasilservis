<?php

namespace App\Controller;

use App\Model\Processo;
use App\Model\Produto;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class ProcessoController extends BaseController {


    public function listarProcessoLead(Request $request, Response $response, $args) {
        try{

            $data = $request->getQueryParams();
            $entity = new Processo($this->pdo);
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            if($leads_pk!=""){
                $retorno = $entity->listarPorLeadsPk($leads_pk);
                Json::run($retorno->status, $retorno->data, $retorno->message);
            }
            else{
                Json::run(true,[],"");
            }
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}