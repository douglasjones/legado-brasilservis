<?php

namespace App\Controller;

use App\Model\Agenda;
use App\Model\AgendaColaboradorPadrao;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class AgendaCalendarioController extends BaseController {


    public function receptivo(Request $request, Response $response, $args) {
        try{
            $this->view->render($response, 'agenda_calendario/agenda_calendario.twig',array('ic_abertura'=>1));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarEventos(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $entity = new Agenda($this->pdo);
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $tipo_agenda_pk = isset($data['tipo_agenda_pk'])? $data['tipo_agenda_pk'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";


            $retorno = $entity->listar_por_tipo_agendas_pk($leads_pk,$tipo_agenda_pk,$ic_status);
            return $response->withStatus(200)->withJson($retorno->data);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}