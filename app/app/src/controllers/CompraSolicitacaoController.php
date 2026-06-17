<?php

namespace App\Controller;

use App\Model\Log;
use App\Model\CompraSolicitacao;
use App\Utils\Json;
use App\Utils\Session;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class CompraSolicitacaoController extends BaseController {

    public function excluir(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";
            (new CompraSolicitacao($this->pdo))->excluir($pk);
            Json::run(true, [], 'Registro excluído com sucesso!');

            }catch(Throwable $th){
            return $response->withJson((object)[
                'error'=>$th->getMessage()
            ],500,[]);
        }
    }

    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'compra_solicitacao/compra_solicitacao_res_form.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function cadForm(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $compra_solicitacao_pk = isset($data['compra_solicitacao_pk'])? $data['compra_solicitacao_pk']: "";
            $usuario_aprovacao_pk = isset($data['usuario_aprovacao_pk'])? $data['usuario_aprovacao_pk']: "";
            $this->view->render($response, 'compra_solicitacao/compra_solicitacao_cad_form.twig',array(
                "compra_solicitacao_pk"=>$compra_solicitacao_pk,
                "usuario_aprovacao_pk"=>$usuario_aprovacao_pk
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
            
            $empresa_pk = isset($data['empresa_pk'])? $data['empresa_pk'] : "";
            $solicitante_pk = isset($data['solicitante_pk'])? $data['solicitante_pk'] : "";
            $usuario_aprovacao_pk = isset($data['usuario_aprovacao_pk'])? $data['usuario_aprovacao_pk'] : "";
            $tipo_grupo_centro_custo_pk = isset($data['tipo_grupo_centro_custo_pk'])? $data['tipo_grupo_centro_custo_pk'] : "";
            $grupo_lancamento_centrocusto_pk = isset($data['grupo_lancamento_centrocusto_pk'])? $data['grupo_lancamento_centrocusto_pk'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $dt_solicitacao_ini = isset($data['dt_solicitacao_ini'])? $data['dt_solicitacao_ini'] : "";
            $dt_solicitacao_fim = isset($data['dt_solicitacao_fim'])? $data['dt_solicitacao_fim'] : "";
            
            $dt_aprovacao_ini = isset($data['dt_aprovacao_ini'])? $data['dt_aprovacao_ini'] : "";
            $dt_aprovacao_fim = isset($data['dt_aprovacao_fim'])? $data['dt_aprovacao_fim'] : "";
            
            

            (new CompraSolicitacao($this->pdo))->listarGrid($empresa_pk,$solicitante_pk,$usuario_aprovacao_pk,$tipo_grupo_centro_custo_pk,$grupo_lancamento_centrocusto_pk,$ic_status,$dt_solicitacao_ini,$dt_solicitacao_fim,$dt_aprovacao_ini,$dt_aprovacao_fim);

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
            $solicitante_pk = isset($data['solicitante_pk'])? $data['solicitante_pk']: "";
            $ds_compra_solicitacao = isset($data['ds_compra_solicitacao'])? $data['ds_compra_solicitacao']: "";
            $dt_solicitacao = isset($data['dt_solicitacao'])? $data['dt_solicitacao']: "";
            $obs_solicitacao = isset($data['obs_solicitacao'])? $data['obs_solicitacao']: "";
            $usuario_aprovacao_pk = isset($data['usuario_aprovacao_pk'])? $data['usuario_aprovacao_pk']: "";
            $dt_aprovacao = isset($data['dt_aprovacao'])? $data['dt_aprovacao']: "";
            $obs_aprovacao = isset($data['obs_aprovacao'])? $data['obs_aprovacao']: "";
            $tipo_grupo_centro_custo_pk = isset($data['tipo_grupo_centro_custo_pk'])? $data['tipo_grupo_centro_custo_pk']: "";
            $grupo_lancamento_centrocusto_pk = isset($data['grupo_lancamento_centrocusto_pk'])? $data['grupo_lancamento_centrocusto_pk']: "";
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk']: "";

            $compra_solicitacao = [
                "pk"=>$pk,
                "solicitante_pk"=>$solicitante_pk,
                "ds_compra_solicitacao"=>$ds_compra_solicitacao,
                "dt_solicitacao"=>$dt_solicitacao,
                "obs_solicitacao"=>$obs_solicitacao,
                "usuario_aprovacao_pk"=>$usuario_aprovacao_pk,
                "dt_aprovacao"=>$dt_aprovacao,
                "obs_aprovacao"=>$obs_aprovacao,
                "tipo_grupo_centro_custo_pk"=>$tipo_grupo_centro_custo_pk,
                "grupo_lancamento_centrocusto_pk"=>$grupo_lancamento_centrocusto_pk,
                "empresas_pk"=>$empresas_pk
            ];
            $retorno = (new CompraSolicitacao($this->pdo))->salvar($compra_solicitacao);

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
           
            $retorno = (new CompraSolicitacao($this->pdo))->listarPk($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

}
