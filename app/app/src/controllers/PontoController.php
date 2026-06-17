<?php

namespace App\Controller;

use App\Model\Log;
use App\Model\Empresa;
use App\Model\Lead;
use App\Model\Ponto;
use App\Utils\Json;
use App\Utils\Session;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Utils\SlimImage;
use App\Utils\SlimStatus;
use Throwable;

final class PontoController extends BaseController {

    
    public function receptivoPontoAtraso(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'ponto/ponto_atraso_cad_form.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function receptivoAcompanhamentoPontoDiario(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'ponto/ponto_acompanhamento_diario.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function popUpAtraso(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $dt_ini = isset($data['dt_ini'])? $data['dt_ini']: "";
            $dt_fim = isset($data['dt_fim'])? $data['dt_fim']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $turnos_pk = isset($data['turnos_pk'])? $data['turnos_pk']: "";
            $funcao_pk = isset($data['funcao_pk'])? $data['funcao_pk']: "";

            $diasemana_numero = date('w', strtotime(Util::DataYMD($dt_ini)));
            $retorno = (new Ponto($this->pdo))->PopUpAtraso(
                $dt_ini,
                $dt_fim,
                $diasemana_numero,
                "",
                $leads_pk,
                $colaborador_pk,
                $turnos_pk,
                $funcao_pk
            );

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
        }
    }
    public function acompanhamentoPontoDiario(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $turnos_pk = isset($data['leads_pk'])? $data['turnos_pk']: "";
            $dt_pesquisa = isset($data['dt_pesquisa'])? $data['dt_pesquisa']: "";

            $retorno = (new Ponto($this->pdo))->acompanhamentoPontoDiario($colaborador_pk,$leads_pk,$turnos_pk,$dt_pesquisa);

            json::run(true,$retorno,"");
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
        }
    }
    public function listarColaborador(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $dt_ini = isset($data['dt_ini'])? $data['dt_ini']: "";
            $dt_fim = isset($data['dt_fim'])? $data['dt_fim']: "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $ic_cliente = isset($data['ic_cliente'])? $data['ic_cliente']: "";

            $retorno = (new Ponto($this->pdo))->listarColaborador($colaborador_pk,$dt_ini,$dt_fim,$leads_pk,$ic_cliente);

            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function relatorioPontoSinteticaAntigo(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $dt_ini = isset($data['dt_ini'])? $data['dt_ini']: "";
            $dt_fim = isset($data['dt_fim'])? $data['dt_fim']: "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $qtde_lead_colaborador = isset($data['qtde_lead_colaborador'])? $data['qtde_lead_colaborador']: "";
            $dt_ponto = isset($data['dt_ponto'])? $data['dt_ponto']: "";
            $ic_inverter_folga = isset($data['ic_inverter_folga'])? $data['ic_inverter_folga']: "";

            $retorno = (new Ponto($this->pdo))->relatorioPontoSinteticaAntigo($leads_pk,$colaborador_pk,$dt_ini,$dt_fim,$qtde_lead_colaborador);

            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function relatorioPonto(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $dt_ini = isset($data['dt_ini'])? $data['dt_ini']: "";
            $dt_fim = isset($data['dt_final'])? $data['dt_final']: "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $qtde_lead_colaborador = isset($data['qtde_lead_colaborador'])? $data['qtde_lead_colaborador']: "";
            $dt_ponto = isset($data['dt_ponto'])? $data['dt_ponto']: "";
            $ic_inverter_folga = isset($data['ic_inverter_folga'])? $data['ic_inverter_folga']: "";

            $retorno = (new Ponto($this->pdo))->relatorioPonto($leads_pk,$colaborador_pk,$dt_ini,$dt_fim,$qtde_lead_colaborador);

            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function reloginhoHistoricoPonto(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $dt_ini = isset($data['dt_ini'])? $data['dt_ini']: "";
            $dt_fim = isset($data['dt_final'])? $data['dt_final']: "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";;
            $agenda_colaborador_padrao_pk = isset($data['agenda_colaborador_padrao_pk'])? $data['agenda_colaborador_padrao_pk']: "";;

            $retorno = (new Ponto($this->pdo))->reloginhoHistoricoPonto($leads_pk,$colaborador_pk,$dt_ini,$dt_fim,$agenda_colaborador_padrao_pk);

            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function validarImgPonto(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk']: "";

            $retorno = (new Ponto($this->pdo))->validarImgPonto($pk);

            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function relAcompanhamentoPontoSintetico(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $ic_cliente = isset($data['ic_cliente'])? $data['ic_cliente']: "";
            $dt_periodo_ini = isset($data['dt_periodo_ini'])? $data['dt_periodo_ini']: "";
            $dt_periodo_fim = isset($data['dt_periodo_fim'])? $data['dt_periodo_fim']: "";

            $retorno = (new Ponto($this->pdo))->relAcompanhamentoPontoSintetico($leads_pk,$colaborador_pk,$ic_cliente,$dt_periodo_ini,$dt_periodo_fim);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    
    public function diminuirImgPonto(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $retorno = (new Ponto($this->pdo))->diminuirImgPonto(0,10000);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }


    public function pegarDadosFechamento(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $dt_inicio = isset($data['dt_inicio'])? $data['dt_inicio']: "";
            $dt_fim = isset($data['dt_fim'])? $data['dt_fim']: "";

            $retorno = (new Ponto($this->pdo))->pegarDadosFechamento($leads_pk,$colaborador_pk,$dt_inicio,$dt_fim);
            json::run(true,$retorno,"Dados carregados com sucesso !!!");
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

}
