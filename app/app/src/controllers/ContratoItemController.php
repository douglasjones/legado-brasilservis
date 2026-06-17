<?php

namespace App\Controller;

use App\Model\ContratoItem;
use App\Model\Log;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class ContratoItemController extends BaseController {
    public function excluir(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";
            if($pk!=""){
                (new Log($this->pdo))->salvar('contrato_item',$pk);
                (new ContratoItem($this->pdo))->excluir($pk);
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
    public function listarContratoItem(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $contratos_pk = isset($data['contrato_pk'])? $data['contrato_pk'] : "";
            if($contratos_pk!=""){
                $retorno = (new ContratoItem($this->pdo))->listarContratoItem($contratos_pk);
                Json::run($retorno->status, $retorno->data, $retorno->message);
            }
            else{
                Json::run(true, [], "");
            }
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function verificaServidoQtdeEscala(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $produtos_servicos_pk = isset($data['produtos_servicos_pk'])? $data['produtos_servicos_pk'] : "";
            $contratos_pk = isset($data['contratos_pk'])? $data['contratos_pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";



            $retorno = (new ContratoItem($this->pdo))->listarItensEscala($contratos_pk,$produtos_servicos_pk,$leads_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);

        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}