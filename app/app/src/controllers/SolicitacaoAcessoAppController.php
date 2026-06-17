<?php

namespace App\Controller;

use App\Model\Log;
use App\Model\Empresa;
use App\Model\Lead;
use App\Model\SolicitacaoAcessoApp;
use App\Utils\Json;
use App\Utils\Session;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Utils\SlimImage;
use App\Utils\SlimStatus;
use Throwable;

final class SolicitacaoAcessoAppController extends BaseController {
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'solicitacao_acesso_app/solicitacao_acesso_app_res.twig');
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
                (new Log($this->pdo))->salvar('solicitacao_acesso_app', $pk);
                
                (new SolicitacaoAcessoApp($this->pdo))->excluir($pk);
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

    public function listarSolicitacoes(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $ds_pin = isset($data['ds_pin'])? $data['ds_pin'] : "";
            $ds_re = isset($data['ds_re'])? $data['ds_re'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
           
            $retorno = (new SolicitacaoAcessoApp($this->pdo))->listarGrid($colaborador_pk,$ds_pin,$ds_re,$ic_status);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function buscarTodosBase64(Request $request, Response $response, $args) {

        try{
            $retorno = (new SolicitacaoAcessoApp($this->pdo))->buscarTodosBase64();
            json::run($retorno->status,$retorno->data,$retorno->message);
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
            $ds_pin = isset($data['ds_pin'])?$data['ds_pin']: "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $id_cliente = isset($data['id_cliente'])? $data['id_cliente'] : "";
            $ds_imagem = isset($data['ds_imagem'])? $data['ds_imagem'] : "";
            $dt_solit_liberacao = isset($data['dt_solit_liberacao'])? $data['dt_solit_liberacao'] : "";
            $ds_aparelho = isset($data['ds_aparelho'])? $data['ds_aparelho'] : "";
            $dt_liberacao = isset($data['dt_liberacao'])? $data['dt_liberacao'] : "";
            $usuario_aprovacao_pk = isset($data['usuario_aprovacao_pk'])? $data['usuario_aprovacao_pk'] : "";
            $obs = isset($data['obs'])? $data['obs'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";

            $solicitacao_acesso_app = [
                "pk"=>$pk,
                "ds_pin"=>$ds_pin,
                "colaborador_pk"=>$colaborador_pk,
                "id_cliente"=>$id_cliente,
                "ds_imagem"=>$ds_imagem,
                "dt_solit_liberacao"=>$dt_solit_liberacao,
                "ds_aparelho"=>$ds_aparelho,
                "dt_liberacao"=>$dt_liberacao,
                "usuario_aprovacao_pk"=>$usuario_aprovacao_pk,
                "obs"=>$obs,
                "ic_status"=>$ic_status
            ];
            
            $retorno = (new SolicitacaoAcessoApp($this->pdo))->salvar($solicitacao_acesso_app);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
         }
    }

    public function liberarAcesso(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk']: "";
            $api_pk = isset($data['api_pk'])? $data['api_pk']: "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";

            $solicitacao_acesso_app = [
                "pk"=>$pk,
                "api_pk"=>$api_pk,
                "colaborador_pk"=>$colaborador_pk,
                "ic_status"=>$ic_status
            ];
            
            $retorno = (new SolicitacaoAcessoApp($this->pdo))->liberarAcesso($solicitacao_acesso_app);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
         }
    }
    public function refazerNovoRegistro(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk']: "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";

            $solicitacao_acesso_app = [
                "pk"=>$pk,
                "ic_status"=>$ic_status
            ];
            
            $retorno = (new SolicitacaoAcessoApp($this->pdo))->refazerNovoRegistro($solicitacao_acesso_app);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
         }
    }
}
