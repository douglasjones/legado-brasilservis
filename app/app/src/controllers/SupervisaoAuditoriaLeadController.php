<?php

namespace App\Controller;

use App\Utils\Json;
use App\Model\Log;
use App\Model\SupervisaoAuditoriaLead;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class SupervisaoAuditoriaLeadController extends BaseController {

    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'supervisao_auditoria_lead/supervisao_auditoria_lead_res_form.twig');
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
            $this->view->render($response, 'supervisao_auditoria_lead/supervisao_auditoria_lead_cad_form.twig',array(
                "pk"=>$pk
            ));
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
            $auditorias_categorias_pk = isset($data['auditorias_categorias_pk'])?$data['auditorias_categorias_pk']: "";
            $auditoria_categoria_tipos_pk = isset($data['auditoria_categoria_tipos_pk'])? $data['auditoria_categoria_tipos_pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $ds_localizacao = isset($data['ds_localizacao'])? $data['ds_localizacao'] : "";
            
            $supervisao_auditoria_lead = [
                "pk"=>$pk,
                "auditorias_categorias_pk"=>$auditorias_categorias_pk,
                "auditoria_categoria_tipos_pk"=>$auditoria_categoria_tipos_pk,
                "leads_pk"=>$leads_pk,
                "ds_localizacao"=>$ds_localizacao
            ];
            
            $retorno = (new SupervisaoAuditoriaLead($this->pdo))->salvar($supervisao_auditoria_lead);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
         }
    }
    public function listarPorCategoriasTiposSupervisao(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $auditorias_categorias_tipos_pk = isset($data['auditorias_categorias_tipos_pk'])? $data['auditorias_categorias_tipos_pk'] : "";
            $retorno = (new SupervisaoAuditoriaLead($this->pdo))->listarPorCategoriasTiposSupervisao($auditorias_categorias_tipos_pk);

            Json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}