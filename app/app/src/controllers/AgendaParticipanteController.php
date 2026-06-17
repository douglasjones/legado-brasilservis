<?php

namespace App\Controller;

use App\Model\Log;
use App\Utils\Json;
use App\Model\AgendaParticipante;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class AgendaParticipanteController extends BaseController {

    public function excluir(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";
            if($pk!=""){
                (new Log($this->pdo))->salvar('agendas_participantes',$pk);
                (new AgendaParticipante($this->pdo))->excluir($pk);
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

    public function carregarParicipantes(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $ic_tipo_participante = isset($data['ic_tipo_participante'])? $data['ic_tipo_participante'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $retorno = (new AgendaParticipante($this->pdo))->carregarParicipantes($ic_tipo_participante, $leads_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function carregarParicipantePorParticipantePk(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $ic_tipo_participante = isset($data['ic_tipo_participante'])? $data['ic_tipo_participante'] : "";
            $participante_pk = isset($data['participante_pk'])? $data['participante_pk'] : "";
            $retorno = (new AgendaParticipante($this->pdo))->listar_por_participante_pk($ic_tipo_participante, $participante_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarDataTable(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();

            $agendas_pk = isset($data['agendas_pk'])? $data['agendas_pk'] : "";

            (new AgendaParticipante($this->pdo))->listar_por_agendas_pk($agendas_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

}

