<?php

namespace App\Middleware;
use App\Utils\Session;
use App\Model\Conta;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

class Authentication extends BaseMiddleware
{

    public function __invoke(ServerRequestInterface $requestInterface, Response $responseInterface, callable $next)
    {
        $user = Session::getSession('session_user');
        
        if(isset($user['par1'])){
            $conta = (new Conta($this->pdo))->listarTodos();
            if($user['par1']==1){
                return $next($requestInterface, $responseInterface);
            }
            else{
                
                if(count($conta->data)==0){
                    return $responseInterface->withRedirect('/login');
                }
                else{
                    return $next($requestInterface, $responseInterface);
                }
            }
        }
        else{
            return $responseInterface->withRedirect('/login');
        }
    }
}
