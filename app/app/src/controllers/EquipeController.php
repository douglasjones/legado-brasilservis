<?php

namespace App\Controller;

use App\Utils\Util;
use App\Utils\Json;
use App\Model\Log;
use App\Model\Equipe;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class EquipeController extends BaseController {

    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'equipe/equipe_res_form.twig');
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
                (new Log($this->pdo))->salvar('equipes', $pk);
                
                (new Equipe($this->pdo))->excluir($pk);
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

    
    public function cadForm(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk']: "";
            $this->view->render($response, 'equipe/equipe_cad_form.twig',array(
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
            $ds_equipe = isset($data['ds_equipe'])? $data['ds_equipe'] : "";
            $retorno = (new Equipe($this->pdo))->listarGrid($ds_equipe);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarTodos(Request $request, Response $response, $args) {
        try{
            $entity = new Equipe($this->pdo);
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $ds_equipe = isset($data['ds_equipe'])? $data['ds_equipe'] : "";
            $retorno = $entity->listar_por_ds_equipe($ds_equipe);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarResponsavelEquipe(Request $request, Response $response, $args) {
        try{
            $entity = new Equipe($this->pdo);
            $data = $request->getQueryParams();
            $solicitante_pk = isset($data['solicitante_pk'])? $data['solicitante_pk'] : "";
            $retorno = $entity->listarEquipePorUsuario($solicitante_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
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
            $ds_equipe = isset($data['ds_equipe'])?$data['ds_equipe']: "";
            $equipes_usuarios = isset($data['equipes_usuarios'])?$data['equipes_usuarios']:"";

            if($equipes_usuarios != "")
                $arrEquipesUsuariosPk = json_decode($equipes_usuarios, true);
            
            $equipe = [
                "pk"=>$pk,
                "ds_equipe"=>$ds_equipe
            ];
            $retorno = (new Equipe($this->pdo))->salvar($equipe);

            if($pk > 0){
                $equipes_pk = $pk;
            }
            else{
                $equipes_pk = $retorno->data;
            }

            (new Equipe($this->pdo))->excluirEquipeUsuario($equipes_pk);

            if(count($arrEquipesUsuariosPk) > 0){
                for($i = 0; $i < count($arrEquipesUsuariosPk); $i++){
                    (new Equipe($this->pdo))->adicionarEquipesUsuarios($equipes_pk, $arrEquipesUsuariosPk[$i]['usuarios_pk'],$arrEquipesUsuariosPk[$i]['ic_bko'],$arrEquipesUsuariosPk[$i]['ic_supervisor']);
                }
            }
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
            $retorno = (new Equipe($this->pdo))->listarPorPk($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarEquipesUsuarios(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $retorno = (new Equipe($this->pdo))->listar_usuarios_equipe($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarEquipeUsuarioLogado(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $retorno = (new Equipe($this->pdo))->listarEquipeUsuarioLogado();

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}