<?php

namespace App\Controller;

use App\Model\Log;
use App\Model\Empresa;
use App\Model\Lead;
use App\Model\Usuario;
use App\Utils\Json;
use App\Utils\Session;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Utils\SlimImage;
use App\Utils\SlimStatus;
use Throwable;

final class UsuarioController extends BaseController {

    public function excluir(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";

            if($pk!=""){
                (new Log($this->pdo))->salvar('usuario', $pk);
                
                (new Usuario($this->pdo))->excluir($pk);
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

    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'usuario/usuario_res_form.twig');
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
            $contas_pk = isset($data['contas_pk'])?$data['contas_pk']: "";
            $ds_usuario = isset($data['ds_usuario'])? $data['ds_usuario'] : "";
            $ds_login = isset($data['ds_login'])? $data['ds_login'] : "";
            $ds_senha = isset($data['ds_senha'])? $data['ds_senha'] : "";
            $ds_email = isset($data['ds_email'])? $data['ds_email'] : "";
            $ds_cel = isset($data['ds_cel'])? $data['ds_cel'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";   
            $grupos_pk = isset($data['grupos_pk'])? $data['grupos_pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data ['leads_pk'] : "";  
            
            $usuario = [
                "pk"=>$pk,
                "contas_pk"=>$contas_pk,
                "ds_usuario"=>$ds_usuario,
                "ds_login"=>$ds_login,
                "ds_senha"=>$ds_senha,
                "ds_email"=>$ds_email,
                "ds_cel"=>$ds_cel,
                "ic_status"=>$ic_status,
                "grupos_pk"=>$grupos_pk,
                "leads_pk"=>$leads_pk,
            ];
            $retorno = (new Usuario($this->pdo))->salvar($usuario);

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
            $this->view->render($response, 'usuario/usuario_cad_form.twig',array(
                "pk"=>$pk
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function edit(Request $request, Response $response, $args){
        try{
            $pk = isset($args['pk'])? $args['pk']: "";
            $this->view->render($response, 'usuario/usuario_cad_form.twig',array(
                "pk"=>$pk
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarSupervisor(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $ds_usuario = isset($data['ds_usuario'])? $data['ds_usuario'] : "";
            $retorno = (new Usuario($this->pdo))->listar_supervisor($ds_usuario);

            Json::run($retorno->status, $retorno->data, $retorno->message);
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
            $retorno = (new Usuario($this->pdo))->listarPorPk($pk);

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
            $ds_usuario = isset($data['ds_usuario'])? $data['ds_usuario'] : "";
            $retorno = (new Usuario($this->pdo))->listar_por_ds_usuario_ativo($ds_usuario);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarUsuarioLogado(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $retorno = (new Usuario($this->pdo))->listarUsuarioLogado();

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarTodosSemAdm(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $retorno = (new Usuario($this->pdo))->listarTodosSemAdm();

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarAdmSistema(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $retorno = (new Usuario($this->pdo))->listarAdmSistema();

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function verificarPermissao(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $ds_dominio_modulo = isset($data['ds_dominio_modulo'])? $data['ds_dominio_modulo'] : "";
            $ic_acao = isset($data['ic_acao'])? $data['ic_acao'] : "";
            $retorno = (new Usuario($this->pdo))->verificarPermissao($ds_dominio_modulo,$ic_acao);

            if($retorno->data > 0){
                Json::run(true, [], "Você tem permissão");
            }
            else{
                Json::run(false, [], "Você Não tem permissão");
            }
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function verificarPermissaoMenu(Request $request, Response $response, $args) {
        try{



            $arrMenuComercial = (new Usuario($this->pdo))->verificarPermissaoMenu("menu_comercial", "cons");
            $arrMenuOperacional = (new Usuario($this->pdo))->verificarPermissaoMenu("menu_operacional", "cons");
            $arrMenuCompraEstoque = (new Usuario($this->pdo))->verificarPermissaoMenu("menu_cadastros", "cons");
            $arrMenuRh = (new Usuario($this->pdo))->verificarPermissaoMenu("menu_rh", "cons");
            $arrMenuFinanceiro = (new Usuario($this->pdo))->verificarPermissaoMenu("menu_financeiro", "cons");
            $arrMenuRelatorio = (new Usuario($this->pdo))->verificarPermissaoMenu("menu_relatorios", "cons");
            $arrMenuAdministracao = (new Usuario($this->pdo))->verificarPermissaoMenu("menu_administracao", "cons");
            $arrMenuCpainel = (new Usuario($this->pdo))->verificarPermissaoMenu("menu_cpainel", "cons");


            $retorno = [
                "arrMenuComercial"=>$arrMenuComercial->data,
                "arrMenuOperacional"=>$arrMenuOperacional->data,
                "arrMenuCompraEstoque"=>$arrMenuCompraEstoque->data,
                "arrMenuRh"=>$arrMenuRh->data,
                "arrMenuFinanceiro"=>$arrMenuFinanceiro->data,
                "arrMenuRelatorio"=>$arrMenuRelatorio->data,
                "arrMenuAdministracao"=>$arrMenuAdministracao->data,
                "arrMenuCpainel"=>$arrMenuCpainel->data,
            ];
            Json::run(true, $retorno, "Você tem permissão");

        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarGrid(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $ds_usuario = isset($data['ds_usuario'])? $data['ds_usuario'] : "";
            $contas_pk = isset($data['contas_pk'])? $data['contas_pk'] : "";
            $grupos_pk = isset($data['grupos_pk'])? $data['grupos_pk'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $retorno = (new Usuario($this->pdo))->listarGrid($ds_usuario,$ic_status,$contas_pk,$grupos_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarTodosGestores(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $retorno = (new Usuario($this->pdo))->listarTodosGestores();
            Json::run($retorno->status, $retorno->data, $retorno->message);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarTodosAnalistas(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $retorno = (new Usuario($this->pdo))->listarTodosAnalistas();
            Json::run($retorno->status, $retorno->data, $retorno->message);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    

    public function listarGruposUsuario(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $usuario_pk = isset($data['usuario_pk'])? $data['usuario_pk'] : "";
            $retorno = (new Usuario($this->pdo))->listarGruposUsuario($usuario_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

}
