<?php

namespace App\Controller;

use App\Model\Grupo;
use App\Model\Log;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class GrupoController extends BaseController {
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'grupo/grupo_res_form.twig');
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
            $this->view->render($response, 'grupo/grupo_cad_form.twig',array(
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
                (new Log($this->pdo))->salvar('grupo', $pk);
                
                (new Grupo($this->pdo))->excluir($pk);
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

    public function listarTodos(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $entity = new Grupo($this->pdo);
            $retorno = $entity->listarTodos();
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }

    public function listarGrid(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $t_ds_grupo = isset($data['ds_grupo'])? $data['ds_grupo'] : "";

            $retorno = (new Grupo($this->pdo))->listarGrid($t_ds_grupo);
            Json::run($retorno->status, $retorno->data, $retorno->message);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarPermissoesGrupo(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $t_modulos_pk = isset($data['pk'])? $data['pk'] : "";

            $retorno = (new Grupo($this->pdo))->listarPermissoesGrupo($t_modulos_pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }

    public function listarPk(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $retorno = (new Grupo($this->pdo))->listarPorPk($pk);

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
            $ds_grupo = isset($data['ds_grupo'])? $data['ds_grupo'] : "";
            $modulos_grupos = isset($data['modulos_grupos'])?$data['modulos_grupos']: "";

            if($modulos_grupos != "")
                $arrGruposModulosPk = json_decode ($modulos_grupos, true);
            $grupo = [
                "pk"=>$pk,
                "ds_grupo"=>$ds_grupo
            ];
            $retorno = (new Grupo($this->pdo))->salvar($grupo);



            (new Grupo($this->pdo))->excluirGruposModulosPk($retorno->data);

            if(count($arrGruposModulosPk) > 0){
                for($i = 0; $i < count($arrGruposModulosPk); $i++){
                    (new Grupo($this->pdo))->adicionarGruposModulos($retorno->data, $arrGruposModulosPk[$i]['modulos_pk'], $arrGruposModulosPk[$i]["ic_ins"], $arrGruposModulosPk[$i]["ic_upd"], $arrGruposModulosPk[$i]["ic_del"], $arrGruposModulosPk[$i]["ic_cons"]);
                }
            }

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            
            Json::run(false,[],"Já existe um módulo cadastro com esse nome!");
        }
    }
}