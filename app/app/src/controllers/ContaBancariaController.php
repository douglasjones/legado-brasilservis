<?php

namespace App\Controller;

use App\Model\ContaBancaria;
use App\Model\Log;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class ContaBancariaController extends BaseController {
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'contas_bancarias/contas_bancarias_res_form.twig');
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
            $this->view->render($response, 'contas_bancarias/contas_bancarias_cad_form.twig',array(
                "pk"=>$pk
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarGrid(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $t_bancos_pk = isset($data['bancos_pk'])? $data['bancos_pk'] : "";
            $t_ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $t_ds_conta = isset($data['ds_conta'])? $data['ds_conta'] : "";  
            
            $retorno = (new ContaBancaria($this->pdo))->listarGrid($t_bancos_pk, $t_ds_conta, $t_ic_status);
            
            Json::run($retorno->status, $retorno->data, $retorno->message);
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
            $ds_conta_bancaria = isset($data['ds_conta_bancaria'])?$data['ds_conta_bancaria']: "";
            $ds_agencia = isset($data['ds_agencia'])? $data['ds_agencia'] : "";
            $ds_conta = isset($data['ds_conta'])?$data['ds_conta']: "";
            $tipo_conta_pk = isset($data['tipo_conta_pk'])? $data['tipo_conta_pk'] : "";
            $vl_saldo_inicial = isset($data['vl_saldo_inicial'])?$data['vl_saldo_inicial']: "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $bancos_pk = isset($data['bancos_pk'])?$data['bancos_pk']: "";
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk'] : "";
           
            $conta_bancaria = [
                "pk"=>$pk,
                "ds_conta_bancaria"=>$ds_conta_bancaria,
                "ds_agencia"=>$ds_agencia,
                "ds_conta"=>$ds_conta,
                "tipo_conta_pk"=>$tipo_conta_pk,
                "vl_saldo_inicial"=>$vl_saldo_inicial,
                "ic_status"=>$ic_status,
                "bancos_pk"=>$bancos_pk,
                "empresas_pk"=>$empresas_pk
            ];
            $retorno = (new ContaBancaria($this->pdo))->salvar($conta_bancaria);

            Json::run($retorno->status,$retorno->data,$retorno->message);
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
                (new Log($this->pdo))->salvar('conta_bancaria', $pk);
                
                (new ContaBancaria($this->pdo))->excluir($pk);
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

    public function listarPk(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
           
            $retorno = (new ContaBancaria($this->pdo))->listarPorPk($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
	public function listaPorEmpresa(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $empresa_pk = isset($data['empresa_pk'])? $data['empresa_pk'] : "";
           
            $retorno = (new ContaBancaria($this->pdo))->listaPorEmpresa($empresa_pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarEmpresaContasAtivas(Request $request, Response $response, $args) {
        try{
            $retorno = (new ContaBancaria($this->pdo))->listarEmpresaContasAtivas();
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarContasLancamento(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk'] : "";
            $retorno = (new ContaBancaria($this->pdo))->listarContasLancamento($empresas_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}