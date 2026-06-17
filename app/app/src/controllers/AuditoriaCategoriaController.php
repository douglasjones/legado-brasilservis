<?php

namespace App\Controller;

use App\Utils\Json;
use App\Model\Log;
use App\Model\AuditoriaCategoria;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class AuditoriaCategoriaController extends BaseController {

    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'auditoria_categoria/auditoria_categoria_res_form.twig');
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
            $this->view->render($response, 'auditoria_categoria/auditoria_categoria_cad_form.twig',array(
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
            $ds_categoria = isset($data['ds_categoria'])? $data['ds_categoria'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $retorno = (new AuditoriaCategoria($this->pdo))->listarGrid($ds_categoria,$ic_status);

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
            $retorno = (new AuditoriaCategoria($this->pdo))->listarPorPk($pk);

            Json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarTodos(Request $request, Response $response, $args) {
        try{
            $retorno = (new AuditoriaCategoria($this->pdo))->listarTodos();

            Json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
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
            $ds_categoria = isset($data['ds_categoria'])? $data['ds_categoria'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";   
            
            $auditoria_categorias = [
                "pk"=>$pk,
                "ds_categoria"=>$ds_categoria,
                "ic_status"=>$ic_status,
                ];
            $retorno = (new AuditoriaCategoria($this->pdo))->salvar($auditoria_categorias);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    
}