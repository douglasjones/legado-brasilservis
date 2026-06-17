<?php

namespace App\Controller;

use App\Utils\Json;
use App\Model\Log;
use App\Model\TetoGastoItem;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class TetoGastoItemController extends BaseController {
    public function listarGrid(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $teto_gastos_pk = isset($data['teto_gastos_pk'])? $data['teto_gastos_pk'] : "";
            
            $retorno = (new TetoGastoItem($this->pdo))->listarGrid($teto_gastos_pk);
            Json::run($retorno->status,$retorno->data,$retorno->message);

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
            $operacao_pk = isset($data['operacao_pk'])? $data['operacao_pk']: "";
            $categoria_operacao_pk = isset($data['categoria_operacao_pk'])?$data['categoria_operacao_pk']: "";
            $tipos_operacao_pk = isset($data['tipos_operacao_pk'])? $data['tipos_operacao_pk'] : "";

            $dt_ini_teto = isset($data['dt_ini_teto'])? $data['dt_ini_teto']: "";
            $dt_fim_teto = isset($data['dt_fim_teto'])?$data['dt_fim_teto']: "";
            $vl_teto_anual = isset($data['vl_teto_anual'])? $data['vl_teto_anual'] : "";
            
            $vl_teto_mensal = isset($data['vl_teto_mensal'])? $data['vl_teto_mensal']: "";
            $ic_teto_mensal = isset($data['ic_teto_mensal'])?$data['ic_teto_mensal']: "";
            $vl_teto_anual_atual = isset($data['vl_teto_anual_atual'])? $data['vl_teto_anual_atual']: "";
            
            $vl_teto_mensal_atual = isset($data['vl_teto_mensal_atual'])? $data['vl_teto_mensal_atual']: "";
            $ic_status = isset($data['ic_status'])?$data['ic_status']: "";            
            $obs = isset($data['obs'])? $data['obs']: "";

            $teto_gastos_pk = isset($data['teto_gastos_pk'])?$data['teto_gastos_pk']: "";
            
            $teto_gasto_item = [
                
                "pk"=>$pk,
                "operacao_pk"=>$operacao_pk,
                "categoria_operacao_pk"=>$categoria_operacao_pk,
                "tipos_operacao_pk"=>$tipos_operacao_pk,
                "dt_ini_teto"=>$dt_ini_teto,
                "dt_fim_teto"=>$dt_fim_teto,
                "vl_teto_anual"=>$vl_teto_anual,
                "vl_teto_mensal"=>$vl_teto_mensal,
                "ic_teto_mensal"=>$ic_teto_mensal,
                "vl_teto_anual_atual"=>$vl_teto_anual_atual,
                "vl_teto_mensal_atual"=>$vl_teto_mensal_atual,
                "ic_status"=>$ic_status,
                "obs"=>$obs,
                "teto_gastos_pk"=>$teto_gastos_pk
            ];

            
            $retorno = (new TetoGastoItem($this->pdo))->salvar($teto_gasto_item);

            
            Json::run($retorno->status,$retorno->data,$retorno->message);
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
                (new Log($this->pdo))->salvar('teto_gasto_item', $pk);
                
                (new TetoGastoItem($this->pdo))->excluir($pk);
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

    
}