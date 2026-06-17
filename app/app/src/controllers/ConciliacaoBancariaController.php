<?php

namespace App\Controller;

use App\Model\ConciliacaoBancaria;
use App\Model\Log;
use App\Model\Conciliacao;
use App\Utils\Json;
use App\Utils\Session;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Utils\SlimImage;
use App\Utils\SlimStatus;
use Throwable;

final class ConciliacaoBancariaController extends BaseController {
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'conciliacao_bancaria/conciliacao_bancaria_res_form.twig');
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
            $this->view->render($response, 'conciliacao_bancaria/conciliacao_bancaria_cad_form.twig',array(
                "pk"=>$pk
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function excluir(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";

            if($pk!=""){
                (new Log($this->pdo))->salvar('financeiro_conciliacao_banco', $pk);

                (new ConciliacaoBancaria($this->pdo))->excluir($pk);
                Json::run(true, [], 'Registro excluído com sucesso!');
            }else{
                Json::run(false, [], 'Falha ao excluir registro!');
            }
        }catch(Throwable $th){
            return $response->withJson((object)[
                'error'=>$th->getMessage()
            ],500,[]);
        }
    }
    public function listarGrid(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $fornecedor_pk = isset($data['fornecedor_pk'])? $data['fornecedor_pk'] : "";

            (new ConciliacaoBancaria($this->pdo))->listarGrid();

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarDataTableItens(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $financeiro_conciliacao_banco_pk = isset($data['financeiro_conciliacao_banco_pk'])? $data['financeiro_conciliacao_banco_pk'] : "";

            (new ConciliacaoBancaria($this->pdo))->listarDataTableItens($financeiro_conciliacao_banco_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarPk(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";

            $retorno = (new ConciliacaoBancaria($this->pdo))->listarPk($pk);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function salvar(Request $request, Response $response, $args) {
        try{
            if($_FILES[0]['type'] != "application/octet-stream"){
                json::run(false,[],'Arquivo precisa ser do tipo .OFX .');
            }
            $data = $_POST;

            $pk = isset($data['pk'])? $data['pk'] : "";
            $ds_obs = isset($data['ds_obs'])? $data['ds_obs'] : "";
            $contas_bancarias_pk = isset($data['contas_pk'])? $data['contas_pk'] : "";
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk'] : "";


            $name = $_FILES[0]['name'];

            $arrDados = (new ConciliacaoBancaria($this->pdo))->abrirArquivoOfx($_FILES[0]);

            //PEGANDO ARRAY E JOGANDO NAS VARIAVEIS.
            $vl_saldo_conta = $arrDados['saldoConta'];
            $dt_ini_periodo_saldo = $arrDados['dtPeriodoInicio'];
            $dt_fim_periodo_saldo = $arrDados['dtPeriodoFim'];


            $conciliacao_bancaria =[
              "pk"=>$pk,
              "vl_saldo_conta"=>$vl_saldo_conta,
              "dt_ini_periodo_saldo"=>$dt_ini_periodo_saldo,
              "dt_fim_periodo_saldo"=>$dt_fim_periodo_saldo,
              "ds_obs"=>$ds_obs,
              "ic_status"=>1,
              "contas_bancarias_pk"=>$contas_bancarias_pk,
              "empresas_pk"=>$empresas_pk,
            ];

            if($pk==""){
                $retorno = (new ConciliacaoBancaria($this->pdo))->salvar($conciliacao_bancaria);

                //SALVAR EXTRATO POR ITEM
                foreach($arrDados['arrExtratoItens'] as $v){
                    if(floatval($v['valor']) < 0){
                        //CRÉDITO
                        $ic_tipo_transacao = 1;
                    }
                    else{
                        //DEBITO
                        $ic_tipo_transacao = 2;
                    }

                    $desctipoTransacao = $v['tipoTransacao'];
                    $dataTransacao = $v['dtTransacao'];

                    $valor = str_replace("-","",$v['valor']);
                    $estabelecimento = $v['nomeEstabelecimento'];
                    $cod_verificacao_transacao = $v['codTransacao'];

                    $financeiro_import_lancamento_itens =[
                        "ic_tipo_transacao"=>$ic_tipo_transacao,
                        "dt_transacao"=>$dataTransacao,
                        "vl_transacao"=>$valor,
                        "cod_verificacao_transacao"=>$cod_verificacao_transacao,
                        "ds_estabelecimento" =>$estabelecimento,
                        "financeiro_conciliacao_banco_pk"=>$retorno->data
                    ];
                    (new ConciliacaoBancaria($this->pdo))->salvarItens($financeiro_import_lancamento_itens);
                }
            }
            else{
                $retorno =  (new ConciliacaoBancaria($this->pdo))->salvar($conciliacao_bancaria);
            }
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function salvarConciliacaoLancamento(Request $request, Response $response, $args) {
        try{

            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $lancamentos_pk = isset($data['lancamentos_pk'])? $data['lancamentos_pk'] : "";
            $financeiro_conciliacao_banco_itens_pk = isset($data['financeiro_conciliacao_banco_itens_pk'])? $data['financeiro_conciliacao_banco_itens_pk'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $obs = isset($data['obs'])? $data['obs'] : "";


            $financeiro_conciliacao_lancamentos = [
                "pk"=>$pk,
                "lancamentos_pk"=>$lancamentos_pk,
                "financeiro_conciliacao_banco_itens_pk" =>$financeiro_conciliacao_banco_itens_pk,
                "ic_status"=>$ic_status,
                "obs"=>$obs
            ];

            $retorno = (new ConciliacaoBancaria($this->pdo))->salvarConciliacaoLancamento($financeiro_conciliacao_lancamentos);

            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }


}
