<?php

namespace App\Controller;

use App\Model\Log;
use App\Model\Servico;
use App\Utils\Json;
use App\Utils\Session;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Utils\SlimImage;
use App\Utils\SlimStatus;
use Throwable;

final class ServicoController extends BaseController {
    public function receptivo(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
           
            $rh = isset($data['rh'])? $data['rh'] : "";
            $this->view->render($response, 'servico/produto_servico_res.twig',array(
                "rh"=>$rh
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
                (new Log($this->pdo))->salvar('servico', $pk);
                
                (new Servico($this->pdo))->excluir($pk);
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

    public function salvar(Request $request, Response $response, $args){
            try{
                $data = $request->getQueryParams();
                $pk = isset($data['pk'])? $data['pk']: "";
                $ds_produto_servico = isset($data['ds_produto_servico'])?$data['ds_produto_servico']: "";
                $ds_cbo = isset($data['ds_cbo'])? $data['ds_cbo'] : "";
                $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
                $vl_servico = isset($data['vl_servico'])? $data['vl_servico'] : "";
                
                $servico = [
                    "pk"=>$pk,
                    "ds_produto_servico"=>$ds_produto_servico,
                    "ds_cbo"=>$ds_cbo,
                    "ic_status"=>$ic_status,
                    "vl_servico"=>$vl_servico,
                ];
                $retorno = (new Servico($this->pdo))->salvar($servico);
    
                Json::run($retorno->status,$retorno->data,$retorno->message);
            } catch (Throwable $th) {
                return $response->withJson((object)[
                    'error' => $th->getMessage()
                ], 500, []);
             }
    }

    public function listarGrid(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $ds_produto = isset($data['ds_produto'])? $data['ds_produto'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $retorno = (new Servico($this->pdo))->listarGrid($ds_produto,$ic_status);


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
            $rh = isset($data['rh'])? $data['rh'] : "";
            $this->view->render($response, 'servico/produto_servico_cad.twig',array(
                "pk"=>$pk,
                "rh"=>$rh
            ));
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
            $entity = new Servico($this->pdo);
            $retorno = $entity->listarPorPk($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }
    
}
