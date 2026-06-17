<?php

namespace App\Controller;

use App\Model\PlanoConta;
use App\Model\Log;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class PlanoContaController extends BaseController {
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'plano_contas/plano_contas_res_form.twig');
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
            $this->view->render($response, 'plano_contas/plano_contas_cad_form.twig',array(
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
            $t_ds_tipo_operacao = isset($data['ds_tipo_operacao'])? $data['ds_tipo_operacao'] : "";
            $t_ds_status = isset($data['ds_status'])? $data['ds_status'] : "";
            $t_ds_categoria = isset($data['ds_categoria'])? $data['ds_categoria'] : "";  
            
            $retorno = (new PlanoConta($this->pdo))->listarGrid($t_ds_tipo_operacao, $t_ds_status, $t_ds_categoria);
            
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
            $ds_tipo_operacao = isset($data['ds_tipo_operacao'])?$data['ds_tipo_operacao']: "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $categorias_financeiras_pk = isset($data['categorias_financeiras_pk'])?$data['categorias_financeiras_pk']: "";
           
            $plano_conta = [
                "pk"=>$pk,
                "ds_tipo_operacao"=>$ds_tipo_operacao,
                "ic_status"=>$ic_status,
                "categorias_financeiras_pk"=>$categorias_financeiras_pk
            ];
            $retorno = (new PlanoConta($this->pdo))->salvar($plano_conta);

            Json::run($retorno->status,$retorno->data,$retorno->message);
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
           
            $retorno = (new PlanoConta($this->pdo))->listarPorPk($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listaPorCategoria(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $pk = isset($data['categorias_financeiras_pk'])? $data['categorias_financeiras_pk'] : "";

            $retorno = (new PlanoConta($this->pdo))->listaPorCategoria($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarTodos(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            
            $retorno = (new PlanoConta($this->pdo))->listarTodos();

            Json::run($retorno->status, $retorno->data, $retorno->message);
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
                (new Log($this->pdo))->salvar('plano_conta', $pk);
                
                (new PlanoConta($this->pdo))->excluir($pk);
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

}