<?php

namespace App\Controller;

use App\Model\Usuario;
use App\Utils\Json;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class MenuController extends BaseController {

    public function administracao(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'menu/administracao.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function operacional(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'menu/operacional.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function supervisao(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'menu/supervisao.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function compra_estoque(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'menu/compra_estoque.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function financeiro(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'menu/financeiro.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function relatorio(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'menu/relatorio.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }


    public function principal(Request $request, Response $response, $args){
        try{
            //PRINCIPAL PARA ACESSO COM PERFIL DE CLIENTE = 5
            if($_SESSION['session_user']['par10']==5){
                $this->view->render($response, 'menu/principal_cliente.twig');
            }
            else{
                $this->view->render($response, 'menu/principal.twig');
            }

        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function comercial(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'menu/comercial.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    
    public function cpainel(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'menu/cpainel.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function rh(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'menu/rh.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}

