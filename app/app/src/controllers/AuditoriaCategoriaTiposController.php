<?php

namespace App\Controller;

use App\Utils\Json;
use App\Model\Log;
use App\Model\AuditoriaCategoriaTipos;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class AuditoriaCategoriaTiposController extends BaseController {

    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'auditoria_categoria_tipos/auditoria_categoria_tipos_res_form.twig');
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
            $this->view->render($response, 'auditoria_categoria_tipos/auditoria_categoria_tipos_cad_form.twig',array(
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
            $retorno = (new AuditoriaCategoriaTipos($this->pdo))->listarGrid($ds_categoria,$ic_status);

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
            $auditoria_categorias_pk = isset($data['auditoria_categorias_pk'])? $data['auditoria_categorias_pk'] : "";
            $ds_auditoria_categoria_tipo = isset($data['ds_auditoria_categoria_tipo'])? $data['ds_auditoria_categoria_tipo'] : "";   
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";   
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";   
            $produtos_pk = isset($data['produtos_pk'])? $data['produtos_pk'] : "";   
            
            $auditoria_categorias = [
                "pk"=>$pk,
                "auditoria_categorias_pk"=>$auditoria_categorias_pk,
                "ds_auditoria_categoria_tipo"=>$ds_auditoria_categoria_tipo,
                "leads_pk"=>$leads_pk,
                "produtos_pk"=>$produtos_pk,
                "ic_status"=>$ic_status
                ];
            $retorno = (new AuditoriaCategoriaTipos($this->pdo))->salvar($auditoria_categorias);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function salvarItens(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk']: "";
            $ds_categoria_item = isset($data['ds_categoria_item'])? $data['ds_categoria_item'] : "";
            $tipo_item_pk = isset($data['tipo_item_pk'])? $data['tipo_item_pk'] : "";   
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";   
            $auditorias_categorias_pk = isset($data['auditorias_categorias_pk'])? $data['auditorias_categorias_pk'] : "";   
            $auditorias_categorias_tipos_pk = isset($data['auditorias_categorias_tipos_pk'])? $data['auditorias_categorias_tipos_pk'] : "";   
            $ic_obrigatorio = isset($data['ic_obrigatorio'])? $data['ic_obrigatorio'] : "";   
            
            $auditoria_categorias = [
                "pk"=>$pk,
                "ds_categoria_item"=>$ds_categoria_item,
                "tipo_item_pk"=>$tipo_item_pk,
                "ic_status"=>$ic_status,
                "auditorias_categorias_pk"=>$auditorias_categorias_pk,
                "auditorias_categorias_tipos_pk"=>$auditorias_categorias_tipos_pk,
                "ic_obrigatorio"=>$ic_obrigatorio
            ];
            $retorno = (new AuditoriaCategoriaTipos($this->pdo))->salvarItens($auditoria_categorias);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function salvarItensCampos(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $dadosItensCampo = isset($data['dadosItensCampo'])? $data['dadosItensCampo'] : "";

            $retorno = (new AuditoriaCategoriaTipos($this->pdo))->salvarItensCampos($dadosItensCampo);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function atualizarStatus(Request $request, Response $response, $args){
        $data = $request->getQueryParams();
        $strJSONDadosStatus = isset($data['strJSONDadosStatus'])? $data['strJSONDadosStatus']: "";

        $retorno =  (new AuditoriaCategoriaTipos($this->pdo))->atualizarStatus($strJSONDadosStatus);
        Json::run($retorno->status,$retorno->data,$retorno->message);

    }
    public function listarPk(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $retorno = (new AuditoriaCategoriaTipos($this->pdo))->listarPorPk($pk);

            Json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarPorCategoriasTiposPk(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $auditorias_categorias_tipos_pk = isset($data['auditorias_categorias_tipos_pk'])? $data['auditorias_categorias_tipos_pk'] : "";
            $retorno = (new AuditoriaCategoriaTipos($this->pdo))->listarPorCategoriasTiposPk($auditorias_categorias_tipos_pk);

            Json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function excluir(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";
            if($pk!=""){
                (new Log($this->pdo))->salvar('auditoria_categoria_tipos',$pk);
                (new AuditoriaCategoriaTipos($this->pdo))->excluir($pk);
                Json::run(true, [], 'Registro excluido com sucesso!');
            }
            else{
                Json::run(false, [], 'Falha ao excluir registro!');
            }
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    
}