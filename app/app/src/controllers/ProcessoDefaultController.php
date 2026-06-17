<?php

namespace App\Controller;

use App\Model\ProcessoDefault;
use App\Model\Log;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class ProcessoDefaultController extends BaseController {
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'processo_default/processo_default_res_form.twig');
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
            $this->view->render($response, 'processo_default/processo_default_cad_form.twig',array(
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
            
            $ds_processo_default = isset($data['ds_processo_default'])? $data['ds_processo_default'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $retorno = (new ProcessoDefault($this->pdo))->listarGrid($ds_processo_default,$ic_status);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
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

                $qtde = (new ProcessoDefault($this->pdo))->verificarContratoProcesso($pk);
               
                if($qtde->data > 0){
                    Json::run(false, [], 'Esse processo já está cadastro em um contrato.');
                }
                else{
                    (new Log($this->pdo))->salvar('processo_default', $pk);
                
                    (new ProcessoDefault($this->pdo))->excluirProcessosDefaultEtapasPk($pk);
                    
                    (new ProcessoDefault($this->pdo))->excluirProcessosDefaultModulosPk($pk);
                    
                    (new ProcessoDefault($this->pdo))->excluir($pk);
                    Json::run(true, [], 'Registro excluído com sucesso!');
                }
                
                
               
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
            $ds_processo_default = isset($data['ds_processo_default'])?$data['ds_processo_default']: "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $arrProcessoModulo = isset($data['arrProcessoModulo'])? $data['arrProcessoModulo'] : "";
            $arrProcessoEtapa = isset($data['arrProcessoEtapa'])? $data['arrProcessoEtapa'] : "";

            if($pk!=""){
                $ic_acao = "upd";
            }
            else{
                $ic_acao = "ins";
            }

            if($arrProcessoEtapa != "")
                $arrProcessosEtapasDefaultPk = json_decode ($arrProcessoEtapa, true);
    
            if($arrProcessoModulo != "")
                $arrProcessosModulosDefaultPk = json_decode ($arrProcessoModulo, true);
           
            $processo_default = [
                "pk"=>$pk,
                "ds_processo_default"=>$ds_processo_default,
                "ic_status"=>$ic_status
            ];

            $retorno = (new ProcessoDefault($this->pdo))->salvar($processo_default, $ic_acao);

            (new ProcessoDefault($this->pdo))->excluirProcessosDefaultEtapasPk($retorno->data);

            (new ProcessoDefault($this->pdo))->excluirProcessosDefaultModulosPk($retorno->data);

            if(count($arrProcessosEtapasDefaultPk) > 0){
                for($i = 0; $i < count($arrProcessosEtapasDefaultPk); $i++){
                    (new ProcessoDefault($this->pdo))->adicionarProcessosDefaultEtapas($retorno->data, $arrProcessosEtapasDefaultPk[$i]['ds_processo_default_etapa'], $arrProcessosEtapasDefaultPk[$i]["n_ordem_etapa"]);
                }
            }
            if(count($arrProcessosModulosDefaultPk) > 0){
                for($i = 0; $i < count($arrProcessosModulosDefaultPk); $i++){
                    (new ProcessoDefault($this->pdo))->adicionarProcessosDefaultModulos($retorno->data, $arrProcessosModulosDefaultPk[$i]['modulo_pk'], $arrProcessosModulosDefaultPk[$i]['n_ordem_modulo'], $processo_default->getic_status());                       
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
            $retorno = (new ProcessoDefault($this->pdo))->listarPk($pk);
            Json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarProcessoDefaultPk(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
           
            $pk = isset($data['pk'])? $data['pk'] : "";
            $retorno = (new ProcessoDefault($this->pdo))->listarProcessoDefaultPk($pk);
            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarModulosProcessoDefaultPk(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $retorno = (new ProcessoDefault($this->pdo))->listarModulosProcessoDefaultPk($pk);
            Json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    
}