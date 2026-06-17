<?php

namespace App\Controller;

use App\Model\NfeApi;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class IssMunicipioController extends BaseController {
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'issMunicipio/receptivo.twig');
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
            $this->view->render($response, 'issMunicipio/cad_form.twig',array(
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
            $ds_uf = isset($data['ds_uf'])?$data['ds_uf']: "";   
            $ds_cidade = isset($data['ds_cidade'])?$data['ds_cidade']: "";   
            $vl_aliquota_iss = isset($data['vl_aliquota_iss'])?$data['vl_aliquota_iss']: "";   
            $ic_status = isset($data['ic_status'])?$data['ic_status']: "";   

            
            $body = [
                "pk"=>$pk,
                "ds_uf"=>$ds_uf,
                "ds_cidade"=>$ds_cidade,
                "vl_aliquota_iss"=>$vl_aliquota_iss,
                "ic_status"=>$ic_status,
                "ds_dominio" =>$_SESSION['session_user']['par11']
            ];
            $retorno = (new NfeApi($this->pdo))->salvarIssMunicipio($body);

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
            $ds_uf = isset($data['ds_uf'])? $data['ds_uf'] : "";
            $ds_cidade = isset($data['ds_cidade'])? $data['ds_cidade'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            
            $retorno = (new NfeApi($this->pdo))->listarGridIssMunicipio($ds_uf,
                                                                        $ds_cidade,
                                                                        $ic_status
                                                                        );

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
            $retorno = (new NfeApi($this->pdo))->listarIssMunicipioPorPk($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function pegarAliquotaPorMunicipio(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $ds_uf = isset($data['ds_uf'])? $data['ds_uf'] : "";
            $ds_cidade = isset($data['ds_cidade'])? $data['ds_cidade'] : "";

            $body = [
                "ds_uf"=>$ds_uf,
                "ds_cidade"=>$ds_cidade,
                "ds_dominio" =>$_SESSION['session_user']['par11']
            ];
            $retorno = (new NfeApi($this->pdo))->pegarAliquotaPorMunicipio($body);
        
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarCidade(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $ds_uf = isset($data['ds_uf'])? $data['ds_uf'] : "";
            $retorno = (new NfeApi($this->pdo))->listarCidade($ds_uf);

            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}