<?php

namespace App\Controller;

use App\Model\Produto;
use App\Model\Log;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class ProdutoController extends BaseController {
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'produtos/produtos_res_form.twig');
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
            $this->view->render($response, 'produtos/produtos_cad_form.twig',array(
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
                (new Log($this->pdo))->salvar('produto', $pk);
                
                (new Produto($this->pdo))->excluir($pk);
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


    public function listarPorCategoria(Request $request, Response $response, $args) {
        try{
            $entity = new Produto($this->pdo);
            $data = $request->getQueryParams();
            $categorias_produto_pk = isset($data['categorias_produto_pk'])? $data['categorias_produto_pk'] : "";
            $retorno = $entity->listar_por_categorias($categorias_produto_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
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
            $t_ds_produto = isset($data['ds_produto'])? $data['ds_produto'] : "";
            $t_ds_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $retorno = (new Produto($this->pdo))->listarGrid($t_ds_produto,$t_ds_status);

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
            $ds_produto = isset($data['ds_produto'])?$data['ds_produto']: "";
            $obs = isset($data['obs'])? $data['obs'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $categorias_produto_pk = isset($data['categorias_produto_pk'])? $data['categorias_produto_pk'] : "";
            $tipo_unidade_pk = isset($data['tipo_unidade_pk'])? $data['tipo_unidade_pk'] : "";
            $ic_tempo_troca = isset($data['ic_tempo_troca'])? $data['ic_tempo_troca'] : "";
            $qtde_minima = isset($data['qtde_minima'])? $data['qtde_minima'] : "";
        
            $produto = [
                "pk"=>$pk,
                "ds_produto"=>$ds_produto,
                "obs"=>$obs,
                "ic_status"=>$ic_status,
                "categorias_produto_pk"=>$categorias_produto_pk,
                "tipo_unidade_pk"=>$tipo_unidade_pk,
                "ic_tempo_troca"=>$ic_tempo_troca,
                "qtde_minima"=>$qtde_minima
            ];
            $retorno = (new Produto($this->pdo))->salvar($produto);


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
            $retorno = (new Produto($this->pdo))->listarPorPk($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarTodosComTempoTroca(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $ds_produto = isset($data['ds_produto'])? $data['ds_produto'] : "";
            $retorno = (new Produto($this->pdo))->listarTodosComTempoTroca($ds_produto);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

}