<?php

namespace App\Controller;

use App\Model\ConjuntoMaterial;
use App\Model\MovimentacaoEstoque;
use App\Model\Lead;
use App\Utils\Json;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class ConjuntoMaterialController extends BaseController {

    public function listarMovimentarMaterialProd(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $categoria_pk = isset($data['categoria_pk'])? $data['categoria_pk'] : "";
            $produtos_pk = isset($data['produtos_pk'])? $data['produtos_pk'] : "";
            $dt_movimentacao_ini = isset($data['dt_movimentacao_ini'])? $data['dt_movimentacao_ini'] : "";
            $dt_movimentacao_fim = isset($data['dt_movimentacao_fim'])? $data['dt_movimentacao_fim'] : "";
            $contratos_pk = isset($data['contratos_pk'])? $data['contratos_pk'] : "";
            $int_grupo_para_movimentacao_pk = isset($data['grupo_para_movimentacao_pk'])? $data['grupo_para_movimentacao_pk'] : "";

            (new ConjuntoMaterial($this->pdo))->listarMovimentarMaterialProd($colaborador_pk,$leads_pk,$categoria_pk,$produtos_pk,$dt_movimentacao_ini,$dt_movimentacao_fim,$int_grupo_para_movimentacao_pk,$contratos_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function salvar(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $contratos_pk = isset($data['contratos_pk'])? $data['contratos_pk'] : "";
            $pk = (new ConjuntoMaterial($this->pdo))->salvar($data);


            //Materiais Lead
            $materiais_lead = isset($data['materiais_pk'])? $data['materiais_pk'] : "";
            if($materiais_lead != "")
                $arrMateriaisLead = json_decode ($materiais_lead, true);
            foreach($arrMateriaisLead as $v){

                if($v['dt_devolucao']!=""){
                    $dt_devolucao = (Util::DataYMD($v['dt_devolucao']));
                }
                else{
                    $dt_devolucao ="";
                }

                $movimentacao_estoque =[
                    'pk' =>$v['movimentacao_estoque_pk'],
                    'leads_pk' =>$leads_pk,
                    'colaborador_pk' =>$colaborador_pk,
                    'contratos_pk' =>$contratos_pk,
                    'produtos_itens_pk' =>$v['produtos_itens_pk'],
                    'qtde' =>1,
                    'dt_entrega'=>Util::DataYMD($v['dt_entrega']),
                    'conjunto_material_pk'=>$pk->data,
                    'ic_mateiral_carga'=>$v['ic_mateiral_carga'],
                    'dt_devolucao'=>$dt_devolucao,
                    'obs_movimentacao'=>$v['obs_material'],
                    'polos_destino_pk'=>"",
                    'polos_origem_pk'=>"",
                    'grupo_para_movimentacao_pk'=>"",
                ];

                (new MovimentacaoEstoque($this->pdo))->salvar($movimentacao_estoque);
            }




            Json::run($pk->status, $pk->data, $pk->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}

