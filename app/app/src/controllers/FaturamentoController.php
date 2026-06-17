<?php

namespace App\Controller;

use App\Model\Log;
use App\Model\Faturamento;
use App\Utils\Json;
use App\Utils\Session;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Utils\SlimImage;
use App\Utils\SlimStatus;
use Throwable;

final class FaturamentoController extends BaseController {
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'faturamento/faturamento_res_form.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function faturamentoItens(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $acao = isset($data['acao'])? $data['acao'] : "";
            $faturamento_pk = isset($data['faturamento_pk'])? $data['faturamento_pk'] : "";
            $this->view->render($response, 'faturamento/faturamento_item_res_form.twig',array("faturamento_pk"=> $faturamento_pk, "acao"=> $acao));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
	public function faturamentoCopiar(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk'] : "";
            $dt_faturamento_ini = isset($data['dt_faturamento_ini'])? $data['dt_faturamento_ini'] : "";
            $dt_faturamento_fim = isset($data['dt_faturamento_fim'])? $data['dt_faturamento_fim'] : "";
            $retorno = (new Faturamento($this->pdo))->faturamentoCopiar($pk, $dt_faturamento_ini, $dt_faturamento_fim);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarEmissoes(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $this->view->render($response, 'faturamento/faturamento_emissoes_res_form.twig',array("pk"=> $pk));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarDadosEmissoes(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk'] : "";
            $retorno = (new Faturamento($this->pdo))->listarDadosEmissoes($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarDadosFaturamento(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk'] : "";
            $retorno = (new Faturamento($this->pdo))->listarDadosFaturamento($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarDetalhamentoCorpoNota(Request $request, Response $response, $args){
        try{
            $retorno = (new Faturamento($this->pdo))->listarDetalhamentoCorpoNota();
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    
    public function listarUpdateFaturamento(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk'] : "";
            $retorno = (new Faturamento($this->pdo))->listarUpdateFaturamento($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarDataTable(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk'] : "";
            $dt_faturamento_ini = isset($data['dt_faturamento_ini'])? $data['dt_faturamento_ini'] : "";
            $dt_faturamento_fim = isset($data['dt_faturamento_fim'])? $data['dt_faturamento_fim'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $tipo_contrato_pk = isset($data['tipo_contrato_pk'])? $data['tipo_contrato_pk'] : "";
            $n_emissoes = isset($data['n_emissoes'])? $data['n_emissoes'] : "";

            (new Faturamento($this->pdo))->listar_faturamento($empresas_pk,$ic_status,$dt_faturamento_ini,$dt_faturamento_fim,$tipo_contrato_pk,$n_emissoes);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function cadForm(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk']: "";
            $this->view->render($response, 'faturamento/faturamento_cad_form.twig',array(
                "pk"=>$pk
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function salvar(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk']: ""; 
            $dt_faturamento_ini = isset($data['dt_faturamento_ini'])? $data['dt_faturamento_ini']: ""; 
            $dt_faturamento_fim = isset($data['dt_faturamento_fim'])? $data['dt_faturamento_fim']: ""; 
            $ic_contrato_fixo = isset($data['ic_contrato_fixo'])? $data['ic_contrato_fixo']: ""; 
            $ic_contrato_aditivo = isset($data['ic_contrato_aditivo'])? $data['ic_contrato_aditivo']: ""; 
            $ic_contrato_servico_extra = isset($data['ic_contrato_servico_extra'])? $data['ic_contrato_servico_extra']: ""; 
            $ic_gerar_boleto = isset($data['ic_gerar_boleto'])? $data['ic_gerar_boleto']: ""; 
            $ic_gerar_nota_fiscal = isset($data['ic_gerar_nota_fiscal'])? $data['ic_gerar_nota_fiscal']: ""; 
            $ic_gerar_nota_fatura = isset($data['ic_gerar_nota_fatura'])? $data['ic_gerar_nota_fatura']: ""; 
            $obs = isset($data['obs'])? $data['obs']: ""; 
            $ic_status = isset($data['ic_status'])? $data['ic_status']: ""; 
            $arrConta = isset($data['arrConta'])? $data['arrConta']: ""; 
            
            $faturamento = [
                "pk"=>$pk,
                "dt_faturamento_ini"=>$dt_faturamento_ini,
                "dt_faturamento_fim"=>$dt_faturamento_fim,
                "ic_contrato_fixo"=>$ic_contrato_fixo,
                "ic_contrato_aditivo"=>$ic_contrato_aditivo,
                "ic_contrato_servico_extra"=>$ic_contrato_servico_extra,
                "ic_gerar_boleto"=>$ic_gerar_boleto,
                "ic_gerar_nota_fiscal"=>$ic_gerar_nota_fiscal,
                "ic_gerar_nota_fatura"=>$ic_gerar_nota_fatura,
                "obs"=>$obs,
                "arrConta"=>$arrConta,
                "ic_status"=>$ic_status
            ];
            $retorno = (new Faturamento($this->pdo))->salvar($faturamento);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
	public function cancelarFaturamento(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk']: ""; 
            
            $retorno = (new Faturamento($this->pdo))->cancelarFaturamento($pk);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function excluir(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk']: ""; 
            
            $retorno = (new Faturamento($this->pdo))->excluir($pk);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function processar(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk']: ""; 
            
            $retorno = (new Faturamento($this->pdo))->processar($pk);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function salvarItensContratos(Request $request, Response $response, $args){
        try{
            $data = $request->getParsedBody();

            $JsonItens = isset($data['JsonItens'])? $data['JsonItens']: ""; 
            $JsonContratos = isset($data['JsonContratos'])? $data['JsonContratos']: ""; 
            $JsonDadosNfse = isset($data['JsonDadosNfse'])? $data['JsonDadosNfse']: ""; 
            
            $retorno = (new Faturamento($this->pdo))->salvarItens($JsonItens, $JsonDadosNfse);
            $retornoContratos = (new Faturamento($this->pdo))->salvarContratos($JsonContratos);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    
    public function listarContratoFaturamento(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $contratos_pk = isset($data['contratos_pk'])? $data['contratos_pk'] : "";

            $retorno = (new Faturamento($this->pdo))->listarContratoFaturamento($contratos_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarContratos(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $dt_ini_contrato = isset($data['dt_ini_contrato'])? $data['dt_ini_contrato'] : "";
            $dt_fim_contrato = isset($data['dt_fim_contrato'])? $data['dt_fim_contrato'] : "";
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk'] : "";
            $faturamento_pk = isset($data['faturamento_pk'])? $data['faturamento_pk'] : "";
            $cliente_pk = isset($data['cliente_pk'])? $data['cliente_pk'] : "";
            $posto_trabalho_pk = isset($data['posto_trabalho_pk'])? $data['posto_trabalho_pk'] : "";

            $retorno = (new Faturamento($this->pdo))->listarContratos($dt_ini_contrato, $dt_fim_contrato, $empresas_pk, $faturamento_pk, $cliente_pk, $posto_trabalho_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarDadosFaturamentoNFSE(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $faturamento_item_pk = isset($data['faturamento_item_pk'])? $data['faturamento_item_pk'] : "";

            $retorno = (new Faturamento($this->pdo))->listarDadosFaturamentoNFSE($faturamento_item_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

}
