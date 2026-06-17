<?php

namespace App\Controller;

use App\Model\PropostaFacilitiesItem;
use App\Utils\Json;
use App\Utils\Util;
use App\Model\Lead;
use App\Model\Contato;
use App\Model\Processo;
use App\Model\ProcessoDefault;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class PropostaFacilitiesItemController extends BaseController {

    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'lead/lead_res.twig',array('ic_abertura'=>1));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function cadForm(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'lead/lead_cad_form.twig',array("leads_pk"=> '', "ic_processo_comercial"=> 2, "processo_default_configuracao_pk"=> ''));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function salvar(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $arrGrupos = isset($data['arrGrupos'])? $data['arrGrupos'] : "";
            $arrGrupos = json_decode($arrGrupos, true);
            $retorno = (new PropostaFacilitiesItem($this->pdo))->salvar($arrGrupos);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}

