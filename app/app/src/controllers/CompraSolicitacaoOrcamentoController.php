<?php

namespace App\Controller;

use App\Model\Log;
use App\Model\CompraSolicitacaoOrcamento;
use App\Model\CompraSolicitacaoOrcamentoItem;
use App\Utils\Json;
use App\Utils\Session;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class CompraSolicitacaoOrcamentoController extends BaseController {

    public function excluir(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";

            (new CompraSolicitacaoOrcamentoItem($this->pdo))->excluirPorSolicitacaoOrcamento($pk);
            (new CompraSolicitacaoOrcamento($this->pdo))->excluir($pk);
            Json::run(true, [], 'Registro excluído com sucesso!');

            }catch(Throwable $th){
            return $response->withJson((object)[
                'error'=>$th->getMessage()
            ],500,[]);
        }
    }
    
    public function vinculaSolicitacaoOrcamento(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            
            $compras_solicitacao_orcamentos_pk = isset($data['compras_solicitacao_orcamentos_pk'])? $data['compras_solicitacao_orcamentos_pk'] : "";
            $compras_solicitacao_pk = isset($data['compras_solicitacao_pk'])? $data['compras_solicitacao_pk'] : "";

            (new CompraSolicitacaoOrcamento($this->pdo))->vinculaSolicitacaoOrcamento($compras_solicitacao_orcamentos_pk,$compras_solicitacao_pk);
           
            Json::run(true, [], 'Registro salvo com sucesso!');
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
           
            $compra_solicitacao_pk = isset($data['compra_solicitacao_pk'])? $data['compra_solicitacao_pk'] : "";

            (new CompraSolicitacaoOrcamento($this->pdo))->listarGrid($compra_solicitacao_pk);

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
            $fornecedor_pk = isset($data['fornecedor_pk'])? $data['fornecedor_pk']: "";
            $dt_pevisao_entrega = isset($data['dt_pevisao_entrega'])?$data['dt_pevisao_entrega']: "";
            $vl_frete = isset($data['vl_frete'])?$data['vl_frete']:"";
            $vl_total = isset($data['vl_total'])? $data['vl_total']: "";
            $obs_orcamento = isset($data['obs_orcamento'])?$data['obs_orcamento']: "";
            $obs_aprovacao = isset($data['obs_aprovacao'])?$data['obs_aprovacao']: "";
            $ic_status = isset($data['ic_status'])?$data['ic_status']:"";
            $compra_solicitacao_pk = isset($data['compra_solicitacao_pk'])?$data['compra_solicitacao_pk']:"";
      
            $compra_solicitacao_orcamento = [
                "pk"=>$pk,
                "fornecedor_pk"=>$fornecedor_pk,
                "dt_pevisao_entrega"=>$dt_pevisao_entrega,
                "vl_frete"=>$vl_frete,
                "vl_total"=>$vl_total,
                "obs_orcamento"=>$obs_orcamento,
                "obs_aprovacao"=>$obs_aprovacao,
                "ic_status"=>$ic_status,
                "compra_solicitacao_pk"=>$compra_solicitacao_pk
            ];
            
            $retorno = (new CompraSolicitacaoOrcamento($this->pdo))->salvar($compra_solicitacao_orcamento);

            
        
        Json::run($retorno->status,$retorno->data,$retorno->message);
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
            $compra_solicitacao_pk = isset($data['compra_solicitacao_pk'])? $data['compra_solicitacao_pk']: "";
            $usuario_aprovacao_pk = isset($data['usuario_aprovacao_pk'])? Util::soNumeros($data['usuario_aprovacao_pk']): "";

            $this->view->render($response, 'compra_solicitacao/compras_solicitacao_orcamentos_cad_form.twig',array(
                "pk"=>$pk,
                "compra_solicitacao_pk"=>$compra_solicitacao_pk,
                "usuario_aprovacao_pk"=>$usuario_aprovacao_pk
            ));
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

            $retorno = (new CompraSolicitacaoOrcamento($this->pdo))->listarPk($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}
