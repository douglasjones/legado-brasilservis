<?php
namespace App\Controller;

use App\Model\Log;
use App\Utils\Json;
use App\Model\MovimentacaoEstoque;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class MovimentacaoEstoqueController extends BaseController
{
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'movimentacao_estoque/movimentacao_estoque_res.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function cadForm(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $this->view->render($response, 'modulo/modulo_cad.twig',array('pk'=>$pk));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listar_por_pk_conjunto(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk']) ? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk']) ? $data['colaborador_pk'] : "";
            $conjunto_material_pk = isset($data['conjunto_material_pk']) ? $data['conjunto_material_pk'] : "";
            $contratos_pk = isset($data['contratos_pk']) ? $data['contratos_pk'] : "";

            (new MovimentacaoEstoque($this->pdo))->listar_por_pk($leads_pk,$colaborador_pk,$conjunto_material_pk,$contratos_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listar_impressao(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk']) ? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk']) ? $data['colaborador_pk'] : "";
            $conjunto_material_pk = isset($data['conjunto_material_pk']) ? $data['conjunto_material_pk'] : "";

            (new MovimentacaoEstoque($this->pdo))->listar_impressao($leads_pk,$colaborador_pk,$conjunto_material_pk);

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
            if($pk!=""){
                (new Log($this->pdo))->salvar('movimentacao_estoque',$pk);
                (new MovimentacaoEstoque($this->pdo))->excluir($pk);
                Json::run(true, [], 'Registro excluido com sucesso!');
            }
            else{

                Json::run(false, [], 'Falha ao excluir registro!');
            }
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }
    public function salvar(Request $request, Response $response, $args)
    {
        try{

            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";
            $leads_pk = isset($data['leads_pk']) ? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk']) ? $data['colaborador_pk'] : "";
            $produtos_itens_pk = isset($data['produtos_itens_pk']) ? $data['produtos_itens_pk'] : "";
            $qtde = isset($data['qtde']) ? $data['qtde'] : "";
            $polos_origem_pk = isset($data['polos_origem_pk']) ? $data['polos_origem_pk'] : "";
            $grupo_para_movimentacao_pk = isset($data['grupo_para_movimentacao_pk']) ? $data['grupo_para_movimentacao_pk'] : "";
            $polos_destino_pk = isset($data['polos_destino_pk']) ? $data['polos_destino_pk'] : "";
            $dt_entrega = isset($data['dt_entrega']) ? $data['dt_entrega'] : "";
            $dt_devolucao = isset($data['dt_devolucao']) ? $data['dt_devolucao'] : "";
            $obs_movimentacao = isset($data['obs_movimentacao']) ? $data['obs_movimentacao'] : "";
            $conjunto_material_pk = isset($data['conjunto_material_pk']) ? $data['conjunto_material_pk'] : "";
            $ic_mateiral_carga = isset($data['ic_mateiral_carga']) ? $data['ic_mateiral_carga'] : "";
            $contratos_pk = isset($data['contratos_pk']) ? $data['contratos_pk'] : "";
            if($dt_devolucao!=""){
                $dt_devolucaof = (Util::DataYMD($dt_devolucao));
            }
            else{
                $dt_devolucaof ="";
            }

            $movimentacao_estoque =[
                "pk"=>$pk,
                "leads_pk"=>$leads_pk,
                "grupo_para_movimentacao_pk"=>$grupo_para_movimentacao_pk,
                "colaborador_pk"=>$colaborador_pk,
                "produtos_itens_pk"=>$produtos_itens_pk,
                "qtde"=>1,
                "polos_origem_pk"=>$polos_origem_pk,
                "polos_destino_pk"=>$polos_destino_pk,
                "dt_entrega"=>(Util::DataYMD($dt_entrega)),
                "dt_devolucao"=>$dt_devolucaof,
                "obs_movimentacao"=>$obs_movimentacao,
                "conjunto_material_pk"=>$conjunto_material_pk,
                "ic_mateiral_carga"=>$ic_mateiral_carga,
                "contratos_pk"=>$contratos_pk
            ];
            $retorno = (new MovimentacaoEstoque($this->pdo))->salvar($movimentacao_estoque);
            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }

    public function RelatorioEstoque(Request $request, Response $response, $args) {
        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio
            

            $data = $request->getQueryParams();
            $categorias_pk = isset($data['categorias_pk'])? $data['categorias_pk'] : "";
            $produtos_pk = isset($data['produtos_pk'])? $data['produtos_pk']  : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']  : "";
            
            $query = (new MovimentacaoEstoque($this->pdo))->RelatorioEstoqueMinimoAtual($categorias_pk,$produtos_pk,$leads_pk);

            if(count($query->data) > 0){
                for($i = 0; $i < count($query->data); $i++){
                
                        $qtde_movimentado = 0;                          
                        $query1 = (new MovimentacaoEstoque($this->pdo))->RelatorioEstoque($categorias_pk,$query->data[$i]["produtos_pk"],$query->data[$i]["pk"],$leads_pk);
                        
                        if(count($query1->data) > 0){
                            
                            for($j = 0; $j < count($query1->data); $j++){
                                $mysql_data1[] = array(
                                    $qtde_movimentado = $query1->data[$j]["qtde_movimentado"],                
                                );                            
                            }
                        }                     
                        if($query->data[$i]['qtde_inicial']==null){
                            $qtde_inicial = 0;
                        }
                        else{
                            $qtde_inicial = $query->data[$i]['qtde_inicial'];
                        }
                                          
                    $mysql_data[] = array(
                        "t_pk" => $query->data[$i]["pk"],
                        "dt_cad_estoque"=>$query->data[$i]['dt_cadastro_estoque'],
                        "qtde_movimentado"=>$qtde_movimentado,
                        "qtde_inicial"=>$query->data[$i]['qtde_inicial'],
                        "qtde_minima"=>$query->data[$i]['qtde_minima'],
                        "ds_produto"=>$query->data[$i]['ds_produto'],
                        "ds_categoria"=>$query->data[$i]['ds_categoria'],
                        "qtde_atual"=>$qtde_inicial - $qtde_movimentado,


                        "t_functions" => ""
                    );
                }
            }
            else{
                $mysql_data = [];
            }
            

            $retorno->status = true;
            $retorno->message = 'Dados carregados com sucesso';
            $retorno->data = $mysql_data;
            $retorno->iTotalDisplayRecords = count($mysql_data);
            $retorno->iTotalRecords = count($mysql_data);
            
            echo json_encode($retorno);
            exit(0);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function relatorioMovimentacaoEstoqueTroca(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk']) ? $data['leads_pk'] : "";
            $colaboradores_pk = isset($data['colaboradores_pk']) ? $data['colaboradores_pk'] : "";
            $produtos_pk = isset($data['produtos_pk']) ? $data['produtos_pk'] : "";
            $categorias_pk = isset($data['categorias_pk']) ? $data['categorias_pk'] : "";
            $dt_troca_ini = isset($data['dt_troca_ini']) ? $data['dt_troca_ini'] : "";
            $dt_troca_fim = isset($data['dt_troca_fim']) ? $data['dt_troca_fim'] : "";

            (new MovimentacaoEstoque($this->pdo))->relatorioMovimentacaoEstoqueTroca($leads_pk,$colaboradores_pk,$produtos_pk,$categorias_pk,$dt_troca_ini,$dt_troca_fim);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function relCompraMovimentacaoLead(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk']) ? $data['leads_pk'] : "";
            $tipo_operacao_pk = isset($data['tipo_operacao_pk']) ? $data['tipo_operacao_pk'] : "";
            $produtos_pk = isset($data['produtos_pk']) ? $data['produtos_pk'] : "";
            $categorias_produto_pk = isset($data['categorias_produto_pk']) ? $data['categorias_produto_pk'] : "";
            $dt_ini_compra = isset($data['dt_ini_compra']) ? $data['dt_ini_compra'] : "";
            $dt_fim_compra = isset($data['dt_fim_compra']) ? $data['dt_fim_compra'] : "";

            $retorno = (new MovimentacaoEstoque($this->pdo))->relCompraMovimentacaoLead($leads_pk, $tipo_operacao_pk, $produtos_pk,$categorias_produto_pk,$dt_ini_compra,$dt_fim_compra);

            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    
}

