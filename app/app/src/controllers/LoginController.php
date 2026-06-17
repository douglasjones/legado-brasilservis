<?php

namespace App\Controller;
use App\Model\Login;
use App\Utils\Session;
use App\Utils\Util;
use App\Utils\Json;
use App\Model\Usuario;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class LoginController extends BaseController {

	public function login(Request $request, Response $response, $args) {
        try{
            // Pega o horário atual no formato H:i:s
            session_destroy();
            Session::cleanSession();
            $entity = new Login($this->pdo);
            $retorno = $entity->login($request->getParsedBody());
            $this->view->render($response, 'login/login.twig');
            return $response;
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
	}

	public function apiLogoff(Request $request, Response $response, $args) {
        try{
            $entity = new Login($this->pdo);
            $retorno = $entity->apiLogoff();
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

	public function apiLogin(Request $request, Response $response, $args) {
        try{
            
            $entity = new Login($this->pdo);
            $retorno = $entity->login($request->getParsedBody());
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
	}
    public function verificarTrocaSenha(Request $request, Response $response, $args) {
        try{
            
            $entity = new Login($this->pdo);
            $retorno = $entity->verificarTrocaSenha($request->getParsedBody());
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
	}

    

    public function updateSenha(Request $request, Response $response, $args) {
        try{
            $data = $request->getParsedBody();

            if($data['password1']!=$data['password2']){
                Json::run(false, [], "Por favor, informe senhas iguais!");
                 return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
            }
            
            $entity = new Login($this->pdo);
            $retorno = $entity->UpdateSenha($data);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
	}
}