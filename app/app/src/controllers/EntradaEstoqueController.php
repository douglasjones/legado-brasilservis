<?php

namespace App\Controller;

use App\Model\EntradaEstoque;
use App\Model\Log;
use App\Model\ProdutoItem;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class EntradaEstoqueController extends BaseController {
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'entrada_estoque/entrada_estoque_res_form.twig');
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
            $this->view->render($response, 'entrada_estoque/entrada_estoque_cad_form.twig',array(
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
                (new Log($this->pdo))->salvar('entrada_estoque', $pk);
                
                (new EntradaEstoque($this->pdo))->excluir($pk);
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
            $ds_produto = isset($data['ds_produto'])? $data['ds_produto'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";

            $retorno = (new EntradaEstoque($this->pdo))->listarGrid($ds_produto,$ic_status);

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
            $ds_n_ordem = isset($data['ds_n_ordem'])?$data['ds_n_ordem']: "";
            $obs_entrada_estoque = isset($data['obs_entrada_estoque'])?$data['obs_entrada_estoque']:"";
            $fornecedor_pk = isset($data['fornecedor_pk'])?$data['fornecedor_pk']: "";
            $produtos_pk = isset($data['produtos_pk'])?$data['produtos_pk']:"";
            $qtde = isset($data['qtde'])?$data['qtde']: "";
            $vl_unitario = isset($data['vl_unitario'])?$data['vl_unitario']:"";
            $produtos_itens = isset($data['produtos_itens'])?$data['produtos_itens']:"";
            $arrProdutosItens = isset($data['arrProdutosItens'])? $data['arrProdutosItens'] : "";
            
           /**** SALVA O ESTOQUE *** */
            $entrada_estoque = [
                "pk"=> $pk,
                "ds_n_ordem"=> $ds_n_ordem,
                "obs_entrada_estoque"=> $obs_entrada_estoque,
                "fornecedor_pk"=> $fornecedor_pk,
                "produtos_pk"=> $produtos_pk,
                "qtde"=>$qtde,
                "vl_unitario"=>$vl_unitario,
                
            ];

            $retorno = (new EntradaEstoque($this->pdo))->salvar($entrada_estoque);
            
            /**** SALVA OS PRODUTOS DO ESTOQUE QUANDO O Listar Itens FOR SELECIONADO (CHECADO) *** */
            if($produtos_itens != ""){
             
                $arrProdutosItens = json_decode ($produtos_itens, true);
                
                if(count($arrProdutosItens) > 0){
                    
                    for($i = 0; $i < count($arrProdutosItens); $i++){

                        $produto_item = [
                            "pk"=> $arrProdutosItens[$i]['produtos_itens_pk'],
                            "ds_n_serie"=> $arrProdutosItens[$i]['ds_n_serie'],
                            "vl_item"=> $vl_unitario,
                            "qtde"=> "",
                            "ic_entrega"=> "",
                            "produtos_pk"=> $produtos_pk,
                            "compras_pk"=>"",
                            "entrada_estoque_pk"=> $retorno->data
                        ];

                        (new ProdutoItem($this->pdo))->salvar($produto_item);
                    }
                }
                /**** SALVA OS PRODUTOS DO ESTOQUE QUANDO O Listar Itens NÃAAO É SELECIONADO ( NÃO CHECADO) E PEGA A O FOR PELO VALOR DA QUANTIDADE *** */
                else if($qtde!=""){
                    
                    for($i = 0; $i < $qtde; $i++){
                        $produto_item = [
                            "pk"=> "",
                            "ds_n_serie"=> "",
                            "qtde"=> $qtde,
                            "ic_entrega"=> "",
                            "vl_item"=> $vl_unitario,
                            "produtos_pk"=> $produtos_pk,
                            "compras_pk"=>"",
                            "entrada_estoque_pk"=> $retorno->data
                        ];

                        (new ProdutoItem($this->pdo))->salvar($produto_item);

                    }
                }
            /**** SALVA OS PRODUTOS DO ESTOQUE QUANDO O Listar Itens NÃAAO É SELECIONADO ( NÃO CHECADO) E PEGA A O FOR PELO VALOR DA QUANTIDADE *** */
            }else if($qtde!=""){
                
                for($i = 0; $i < $qtde; $i++){
                    $produto_item = [
                        "pk"=> "",
                        "ds_n_serie"=> "",
                        "vl_item"=> $vl_unitario,
                        "qtde"=> $qtde,
                        "ic_entrega"=> "",
                        "produtos_pk"=> $produtos_pk,
                        "compras_pk"=>"",
                        "entrada_estoque_pk"=> $retorno->data
                    ];

                    (new ProdutoItem($this->pdo))->salvar($produto_item);

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
            $retorno = (new EntradaEstoque($this->pdo))->listarPk($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

}