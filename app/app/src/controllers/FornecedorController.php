<?php

namespace App\Controller;

use App\Model\Log;
use App\Model\Fornecedor;
use App\Utils\Json;
use App\Utils\Session;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Utils\SlimImage;
use App\Utils\SlimStatus;
use Throwable;

final class FornecedorController extends BaseController {
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'fornecedor/fornecedor_res_form.twig');
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
            $this->view->render($response, 'fornecedor/fornecedor_cad_form.twig',array(
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
                (new Log($this->pdo))->salvar('fornecedor', $pk);
                
                (new Fornecedor($this->pdo))->excluir($pk);
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
            $t_ds_fornecedor = isset($data['ds_fornecedor'])? $data['ds_fornecedor'] : "";
            $t_ic_status = isset($data['ic_status'])? $data['ic_status'] : "";

            $retorno = (new Fornecedor($this->pdo))->listarGrid($t_ds_fornecedor,$t_ic_status);

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
            $ds_fornecedor = isset($data['ds_fornecedor'])?$data['ds_fornecedor']: "";
            $ds_ddd = isset($data['ds_ddd'])? $data['ds_ddd'] : "";
            $ds_tel = isset($data['ds_tel'])? $data['ds_tel'] : "";
            $ds_email = isset($data['ds_email'])? $data['ds_email'] : "";
            $categorias_produto_pk = isset($data['categorias_produto_pk'])? $data['categorias_produto_pk'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $ds_cpf_cnpj = isset($data['ds_cpf_cnpj'])? $data['ds_cpf_cnpj']:"";
            $ds_razao_social = isset($data['ds_razao_social'])? $data['ds_razao_social'] : "";   
            $ds_endereco = isset($data['ds_endereco'])? $data['ds_endereco'] : "";
            $ds_numero = isset($data['ds_numero'])? $data ['ds_numero'] : ""; 
            $ds_complemento = isset($data['ds_complemento'])? $data['ds_complemento'] : "";
            $ds_bairro = isset($data['ds_bairro'])? $data['ds_bairro'] : "";
            $ds_cidade = isset($data['ds_cidade'])? $data['ds_cidade'] : "";   
            $ds_uf = isset($data['ds_uf'])? $data['ds_uf'] : "";
            $ds_cep = isset($data['ds_cep'])? $data ['ds_cep'] : "";  
            $ds_contato = isset($data['ds_contato'])? $data['ds_contato'] : "";
            $tipo_conta_bancaria = isset($data['tipo_conta_bancaria'])? $data['tipo_conta_bancaria'] : "";
            $ds_agencia = isset($data['ds_agencia'])? $data['ds_agencia'] : "";   
            $ds_conta = isset($data['ds_conta'])? $data['ds_conta'] : "";
            $ds_digito = isset($data['ds_digito'])? $data ['ds_digito'] : "";
            $bancos_pk = isset($data['bancos_pk'])? $data['bancos_pk'] : "";
            $vl_salario = isset($data['vl_salario'])? $data['vl_salario'] : "";   
            $ds_pix = isset($data['ds_pix'])? $data['ds_pix'] : "";
            $ds_favorecido_pix = isset($data['ds_favorecido_pix'])? $data ['ds_favorecido_pix'] : "";  
        
            $fornecedor = [
                "pk"=>$pk,
                "ds_fornecedor"=>$ds_fornecedor,
                "ds_ddd"=>$ds_ddd,
                "ds_tel"=>$ds_tel,
                "ds_email"=>$ds_email,
                "categorias_produto_pk"=>$categorias_produto_pk,
                "ic_status"=>$ic_status,
                "ds_cpf_cnpj"=>$ds_cpf_cnpj,
                "ds_razao_social"=>$ds_razao_social,
                "ds_endereco"=>$ds_endereco,
                "ds_numero"=>$ds_numero,
                "ds_complemento"=>$ds_complemento,
                "ds_bairro"=>$ds_bairro,
                "ds_cidade"=>$ds_cidade,
                "ds_uf"=>$ds_uf,
                "ds_cep"=>$ds_cep,
                "ds_contato"=>$ds_contato,
                "tipo_conta_bancaria"=>$tipo_conta_bancaria,
                "ds_agencia"=>$ds_agencia,
                "ds_conta"=>$ds_conta,
                "ds_digito"=>$ds_digito,
                "bancos_pk"=>$bancos_pk,
                "vl_salario"=>$vl_salario,
                "ds_pix"=>$ds_pix,
                "ds_favorecido_pix"=>$ds_favorecido_pix,
            ];
            $retorno = (new Fornecedor($this->pdo))->salvar($fornecedor);

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
            $retorno = (new Fornecedor($this->pdo))->listarPorPk($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarTodos(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $retorno = (new Fornecedor($this->pdo))->listarTodos();

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarCpfCnpjFornecedor(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $retorno = (new Fornecedor($this->pdo))->listarCpfCnpjFornecedor();

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    
    public function listarPorCategoria(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $categorias_produto_pk = isset($data['categorias_produto_pk'])? $data['categorias_produto_pk'] : "";
            $retorno = (new Fornecedor($this->pdo))->listarPorCategoria($categorias_produto_pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}
