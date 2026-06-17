<?php

namespace App\Controller;

use App\Model\CategoriaFinanceira;
use App\Model\Log;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class CategoriaFinanceiraController extends BaseController {
    public function listarTodos(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $t_ds_categoria = isset($data['ds_categoria'])? $data['ds_categoria'] : "";
            $t_ic_status = isset($data['ic_status'])? $data['ic_status'] : "";   
            $retorno = (new CategoriaFinanceira($this->pdo))->listarGrid($t_ds_categoria, $t_ic_status);
            
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarPorPlano(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $tipos_operacao_pk = isset($data['tipos_operacao_pk'])? $data['tipos_operacao_pk'] : "";
            $retorno = (new CategoriaFinanceira($this->pdo))->listarPorPlano($tipos_operacao_pk);
            
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}