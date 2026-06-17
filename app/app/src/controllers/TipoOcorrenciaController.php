<?php

namespace App\Controller;

use App\Utils\Json;
use App\Model\Log;
use App\Model\TipoOcorrencia;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class TipoOcorrenciaController extends BaseController {

    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'tipo_ocorrencia/tipo_ocorrencia_res_form.twig');
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
                (new Log($this->pdo))->salvar('tipo_ocorrencia', $pk);
                
                (new TipoOcorrencia($this->pdo))->excluir($pk);
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
            $this->view->render($response, 'tipo_ocorrencia/tipo_ocorrencia_cad_form.twig',array(
                "pk"=>$pk
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }


    public function listarTodos(Request $request, Response $response, $args) {
        try{
            $entity = new TipoOcorrencia($this->pdo);
            $retorno = $entity->listarTodos();
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
            $ds_tipo_ocorrencia = isset($data['ds_tipo_ocorrencia'])? $data['ds_tipo_ocorrencia'] : "";
            $ic_fechar_ocorrencia_auto = isset($data['ic_fechar_ocorrencia_auto'])? $data['ic_fechar_ocorrencia_auto'] : "";
            $retorno = (new TipoOcorrencia($this->pdo))->listarGrid($ds_tipo_ocorrencia, $ic_fechar_ocorrencia_auto);


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
            $ds_tipo_ocorrencia = isset($data['ds_tipo_ocorrencia'])?$data['ds_tipo_ocorrencia']: "";
            $ic_fechar_ocorrencia_auto = isset($data['ic_fechar_ocorrencia_auto'])? $data['ic_fechar_ocorrencia_auto'] : "";
            
            $tipo_ocorrencia = [
                "pk"=>$pk,
                "ds_tipo_ocorrencia"=>$ds_tipo_ocorrencia,
                "ic_fechar_ocorrencia_auto"=>$ic_fechar_ocorrencia_auto
            ];
            $retorno = (new TipoOcorrencia($this->pdo))->salvar($tipo_ocorrencia);

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
            $retorno = (new TipoOcorrencia($this->pdo))->listarPorPk($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}