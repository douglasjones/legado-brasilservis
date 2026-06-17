<?php

namespace App\Controller;

use App\Model\DiscriminacaoServicos;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class DiscriminacaoServicosController extends BaseController {
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'discriminacao_servicos/discriminacao_servicos_res_form.twig');
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
            $this->view->render($response, 'discriminacao_servicos/discriminacao_servicos_cad_form.twig',array(
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
                (new DiscriminacaoServicos($this->pdo))->excluir($pk);
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
            $ds_discriminacao_servico = isset($data['ds_discriminacao_servico'])?$data['ds_discriminacao_servico']: "";   
            $ic_status = isset($data['ic_status'])?$data['ic_status']: "";   

            $ds_discriminacao_servico = str_replace("\n", "||", $ds_discriminacao_servico);
            
            $discriminacao_servico = [
                "pk"=>$pk,
                "ds_discriminacao_servico"=>$ds_discriminacao_servico,
                "ic_status"=>$ic_status
            ];
            $retorno = (new DiscriminacaoServicos($this->pdo))->salvar($discriminacao_servico);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarGrid(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $ds_discriminacao_servico = isset($data['ds_discriminacao_servico'])? $data['ds_discriminacao_servico'] : "";
            $retorno = (new DiscriminacaoServicos($this->pdo))->listarGrid($ds_discriminacao_servico);

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
            $retorno = (new DiscriminacaoServicos($this->pdo))->listarPorPk($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarDiscriminacao(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $ds_discriminacao_servico = isset($data['ds_discriminacao_servico'])? $data['ds_discriminacao_servico'] : "";
            $retorno = (new DiscriminacaoServicos($this->pdo))->listarDiscriminacao();

            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
        }
    }
}