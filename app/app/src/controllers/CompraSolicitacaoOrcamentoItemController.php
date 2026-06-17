<?php

namespace App\Controller;

use App\Model\Log;
use App\Model\CompraSolicitacaoOrcamentoItem;
use App\Utils\Json;
use App\Utils\Session;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class CompraSolicitacaoOrcamentoItemController extends BaseController {

    public function listarItensOrcamentoPk(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $compras_solicitacao_orcamentos_pk = isset($data['compras_solicitacao_orcamentos_pk'])? $data['compras_solicitacao_orcamentos_pk'] : "";
            
            (new CompraSolicitacaoOrcamentoItem($this->pdo))->listarItensOrcamentoPk($compras_solicitacao_orcamentos_pk);

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

            (new CompraSolicitacaoOrcamentoItem($this->pdo))->excluir($pk);
            Json::run(true, [], 'Registro excluído com sucesso!');

            }catch(Throwable $th){
            return $response->withJson((object)[
                'error'=>$th->getMessage()
            ],500,[]);
        }
    }
    public function excluirPorSolicitacaoOrcamento(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $compras_solicitacao_orcamentos_pk = isset($data['compras_solicitacao_orcamentos_pk']) ? $data['compras_solicitacao_orcamentos_pk'] : "";

            (new CompraSolicitacaoOrcamentoItem($this->pdo))->excluirPorSolicitacaoOrcamento($compras_solicitacao_orcamentos_pk);
            Json::run(true, [], 'Registro excluído com sucesso!');

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
            $categorias_produto_pk = isset($data['categorias_produto_pk'])? $data['categorias_produto_pk']: "";
            $produtos_pk = isset($data['produtos_pk'])?$data['produtos_pk']: "";
            $ds_produto = isset($data['ds_produto'])? $data['ds_produto'] : "";
            $qtde_produto = isset($data['qtde_produto'])? $data['qtde_produto']: "";
            $vl_unitario = isset($data['vl_unitario'])?$data['vl_unitario']: "";
            $compras_solicitacao_orcamentos_pk = isset($data['compras_solicitacao_orcamentos_pk'])? $data['compras_solicitacao_orcamentos_pk'] : "";  
            
            $compra_solicitacao_orcamento_item = [
                "pk"=>$pk,
                "categorias_produto_pk"=>$categorias_produto_pk,
                "produtos_pk"=>$produtos_pk,
                "ds_produto"=>$ds_produto,
                "qtde_produto"=>$qtde_produto,
                "vl_unitario"=>$vl_unitario,
                "compras_solicitacao_orcamentos_pk"=>$compras_solicitacao_orcamentos_pk
            ];
            
            $retorno = (new CompraSolicitacaoOrcamentoItem($this->pdo))->salvar($compra_solicitacao_orcamento_item);
           
            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }


}
