<?php

namespace App\Controller;

use App\Model\ProdutoItem;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class ProdutoItemController extends BaseController {

    public function excluir(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";
            if($pk!=""){
                (new ProdutoItem($this->pdo))->excluir($pk);
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
    public function listarPorPkProdutoNotIn(Request $request, Response $response, $args) {
        try{
            $entity = new ProdutoItem($this->pdo);
            $data = $request->getQueryParams();
            $produtos_pk = isset($data['produtos_pk'])? $data['produtos_pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $produtos_itens_pk = isset($data['produtos_itens_pk'])? $data['produtos_itens_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";

            $str = "";


            $queryl = $entity->listarPorLeadsPk($leads_pk,$colaborador_pk);
            $str .= "not in (";
            if($produtos_itens_pk==""){
                foreach($queryl->data as $v){
                    $str .= $v['pk'].",";
                }
            }
            $str .= "0)";
            $retorno = $entity->listarPorPkProduto($produtos_pk,$produtos_itens_pk,$str);

            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarPorProdutosQtde(Request $request, Response $response, $args) {
        try{
            $entity = new ProdutoItem($this->pdo);
            $data = $request->getQueryParams();
            $produtos_pk = isset($data['produtos_pk'])? $data['produtos_pk'] : "";
            $qtde = isset($data['qtde'])? $data['qtde'] : "";
            $retorno = $entity->listarPorProdutosQtde($produtos_pk,$qtde);

            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }


    public function listarPorCompra(Request $request, Response $response, $args) {
        try{
            $entity = new ProdutoItem($this->pdo);
            $data = $request->getQueryParams();
            $compras_pk = isset($data['compras_pk'])? $data['compras_pk'] : "";

            $retorno = (new ProdutoItem($this->pdo))->listarPorCompra($compras_pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarProdutoEstoque(Request $request, Response $response, $args) {
        try{
            $entity = new ProdutoItem($this->pdo);
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            
            $retorno = (new ProdutoItem($this->pdo))->listarProdutoEstoque($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}