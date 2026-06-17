<?php

namespace App\Controller;

use App\Utils\Json;
use App\Model\Log;
use App\Model\AnaliseFinanceira;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class AnaliseFinanceiraController extends BaseController {

    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'analise_financeira/analise_financeira_res_form.twig');
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
            $lancamento_old_pk = isset($data['lancamento_old_pk'])? $data['lancamento_old_pk']: "";
            $this->view->render($response, 'analise_financeira/analise_financeira_cad_form.twig',array(
                "pk"=>$pk,
                "lancamento_old_pk"=>$lancamento_old_pk
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
                (new Log($this->pdo))->salvar('analise_financeira', $pk);
                
                (new AnaliseFinanceira($this->pdo))->excluir($pk);
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

    public function listarGrid(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $lancamento_pk = isset($data['lancamento_pk'])? $data['lancamento_pk']  : "";
            $dt_cadastro_ini = isset($data['dt_cadastro_ini'])? $data['dt_cadastro_ini']  : "";
            $dt_cadastro_fim = isset($data['dt_cadastro_fim'])? $data['dt_cadastro_fim']  : "";
            $dt_aprovacao_ini = isset($data['dt_aprovacao_ini'])? $data['dt_aprovacao_ini']  : "";
            $dt_aprovacao_fim = isset($data['dt_aprovacao_fim'])? $data['dt_aprovacao_fim']  : "";
            $dt_correcao_ini = isset($data['dt_correcao_ini'])? $data['dt_correcao_ini']  : "";
            $dt_correcao_fim = isset($data['dt_correcao_fim'])? $data['dt_correcao_fim']  : "";
            $dt_recusa_ini = isset($data['dt_recusa_ini'])? $data['dt_recusa_ini']  : "";
            $dt_recusa_fim = isset($data['dt_recusa_fim'])? $data['dt_recusa_fim']  : "";
            $usuario_cadastro_lancamento_pk = isset($data['usuario_cadastro_lancamento_pk'])? $data['usuario_cadastro_lancamento_pk']  : "";
            $usuario_cadastro_gestor_pk = isset($data['usuario_cadastro_gestor_pk'])? $data['usuario_cadastro_gestor_pk']  : "";
            $usuario_cadastro_analista_pk = isset($data['usuario_cadastro_analista_pk'])? $data['usuario_cadastro_analista_pk']  : "";
            $dt_vencimento_ini = isset($data['dt_vencimento_ini'])? $data['dt_vencimento_ini']  : "";
            $dt_vencimento_fim = isset($data['dt_vencimento_fim'])? $data['dt_vencimento_fim']  : "";
            
            $retorno = (new AnaliseFinanceira($this->pdo))->listarGrid($ic_status, $lancamento_pk, $dt_cadastro_ini, $dt_cadastro_fim, $dt_aprovacao_ini, $dt_aprovacao_fim, $dt_correcao_ini, $dt_correcao_fim, $dt_recusa_ini, $dt_recusa_fim, $dt_vencimento_ini, $dt_vencimento_fim, $usuario_cadastro_lancamento_pk, $usuario_cadastro_analista_pk, $usuario_cadastro_gestor_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);


            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
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
            
            $retorno = (new AnaliseFinanceira($this->pdo))->listarPorPk($pk);
            
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    
}