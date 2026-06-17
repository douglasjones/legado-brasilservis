<?php

namespace App\Controller;

use App\Utils\Json;
use App\Model\Log;
use App\Model\TetoGasto;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class TetoGastoController extends BaseController {

    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'teto_gasto/teto_gasto_res_form.twig');
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
            $this->view->render($response, 'teto_gasto/teto_gasto_cad_form.twig',array(
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
            $tipo_grupo_pk = isset($data['tipo_grupo_pk'])? $data['tipo_grupo_pk'] : "";
            $posto_trabalho_pk = isset($data['posto_trabalho_pk'])? $data['posto_trabalho_pk'] : "";
            $contratos_pk = isset($data['contratos_pk'])? $data['contratos_pk'] : "";
            $grupo_leancamento_pk = isset($data['grupo_leancamento_pk'])? $data['grupo_leancamento_pk'] : "";
            $grupo_lancamento_centro_custo_pk = isset($data['grupo_lancamento_centro_custo_pk'])? $data['grupo_lancamento_centro_custo_pk'] : "";
            $ds_ano_vigente_teto = isset($data['ds_ano_vigente_teto'])? $data['ds_ano_vigente_teto'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";

            $retorno = (new TetoGasto($this->pdo))->listarGrid($tipo_grupo_pk, $posto_trabalho_pk, $contratos_pk, $grupo_leancamento_pk, $grupo_lancamento_centro_custo_pk, $ds_ano_vigente_teto, $ic_status);


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
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk']: "";
            $tipo_grupo_pk = isset($data['tipo_grupo_pk'])?$data['tipo_grupo_pk']: "";
            $grupo_leancamento_pk = isset($data['grupo_leancamento_pk'])? $data['grupo_leancamento_pk'] : "";

            $leads_posto_trabalho_pk = isset($data['leads_posto_trabalho_pk'])? $data['leads_posto_trabalho_pk']: "";
            $contratos_pk = isset($data['contratos_pk'])?$data['contratos_pk']: "";
            $colaborador_posto_trabalho_pk = isset($data['colaborador_posto_trabalho_pk'])? $data['colaborador_posto_trabalho_pk'] : "";
            
            $colaborador_contratos_pk = isset($data['colaborador_contratos_pk'])? $data['colaborador_contratos_pk']: "";
            $fornecedor_posto_trabalho_pk = isset($data['fornecedor_posto_trabalho_pk'])?$data['fornecedor_posto_trabalho_pk']: "";
            $fornecedor_contratos_pk = isset($data['fornecedor_contratos_pk'])? $data['fornecedor_contratos_pk']: "";
            
            $vl_total_teto = isset($data['vl_total_teto'])? $data['vl_total_teto']: "";
            $vl_utilizado_atual = isset($data['vl_utilizado_atual'])?$data['vl_utilizado_atual']: "";
            $ic_status = isset($data['ic_status'])? $data['ic_status']: "";
            
            $obs = isset($data['obs'])? $data['obs']: "";
            $ds_ano_vigente_teto = isset($data['ds_ano_vigente_teto'])?$data['ds_ano_vigente_teto']: "";
            $grupo_lancamento_centro_custo_pk = isset($data['grupo_lancamento_centro_custo_pk'])? $data['grupo_lancamento_centro_custo_pk']: "";
            
            $teto_gasto = [
                "pk"=>$pk,
                "empresas_pk"=>$empresas_pk,
                "tipo_grupo_pk"=>$tipo_grupo_pk,
                "grupo_leancamento_pk"=>$grupo_leancamento_pk,
                "leads_posto_trabalho_pk"=>$leads_posto_trabalho_pk,
                "contratos_pk"=>$contratos_pk,
                "colaborador_posto_trabalho_pk"=>$colaborador_posto_trabalho_pk,
                "colaborador_contratos_pk"=>$colaborador_contratos_pk,
                "fornecedor_posto_trabalho_pk"=>$fornecedor_posto_trabalho_pk,
                "fornecedor_contratos_pk"=>$fornecedor_contratos_pk,
                "vl_total_teto"=>$vl_total_teto,
                "vl_utilizado_atual"=>$vl_utilizado_atual,
                "ic_status"=>$ic_status,
                "obs"=>$obs,
                "ds_ano_vigente_teto"=>$ds_ano_vigente_teto,
                "grupo_lancamento_centro_custo_pk"=>$grupo_lancamento_centro_custo_pk,
            ];
            $retorno = (new TetoGasto($this->pdo))->salvar($teto_gasto);

            Json::run($retorno->status,$retorno->data,$retorno->message);
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
                (new Log($this->pdo))->salvar('teto_gasto', $pk);
                
                (new TetoGasto($this->pdo))->excluir($pk);
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

    public function listarPk(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $retorno = (new TetoGasto($this->pdo))->listarPorPk($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    
}