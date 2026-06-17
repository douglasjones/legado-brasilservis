<?php

namespace App\Controller;

use App\Model\Log;
use App\Model\Modulo;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class ModuloController extends BaseController {
    public function excluir(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";

            if($pk!=""){
                (new Log($this->pdo))->salvar('modulo', $pk);
                (new Modulo($this->pdo))->excluir($pk);
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
    public function salvar(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk'] : "";
            $tipo_modulo_pk = isset($data['tipo_modulo_pk'])? $data['tipo_modulo_pk'] : "";
            $ds_dominio = isset($data['ds_dominio'])? $data['ds_dominio'] : "";
            $ds_modulo = isset($data['ds_modulo'])? $data['ds_modulo'] : "";
            $ds_obs = isset($data['ds_obs'])? $data['ds_obs'] : "";
            
            $modulo =[
                "pk"=>$pk,
                "tipo_modulo_pk"=>$tipo_modulo_pk,
                "ds_dominio"=>$ds_dominio,
                "ds_modulo"=>$ds_modulo,
                "ds_obs"=>$ds_obs
            ];

            $retorno = (new Modulo($this->pdo))->salvar($modulo);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }

    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'modulo/modulo_res.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function cadForm(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $this->view->render($response, 'modulo/modulo_cad.twig',array('pk'=>$pk));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarTodos(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

            $ds_tipo_modulo = isset($data['ds_tipo_modulo'])? $data['ds_tipo_modulo'] : "";

            $retorno = (new Modulo($this->pdo))->listarTodos($ds_tipo_modulo);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarGrid(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $tipo_modulo_pk = isset($data['tipo_modulo_pk'])? $data['tipo_modulo_pk'] : "";

            (new Modulo($this->pdo))->listarGrid($tipo_modulo_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarTipoModulo(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $ds_tipo_modulo = isset($data['ds_tipo_modulo'])? $data['ds_tipo_modulo'] : "";

            $retorno = (new Modulo($this->pdo))->listarTipoModulo($ds_tipo_modulo);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarPk(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";

            $retorno = (new Modulo($this->pdo))->listarPorPk($pk);
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

            $ds_modulo = isset($data['ds_modulo'])? $data['ds_modulo'] : "";

            (new Modulo($this->pdo))->listar_por_ds_conta($ds_modulo);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

}