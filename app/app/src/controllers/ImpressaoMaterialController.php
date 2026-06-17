<?php

namespace App\Controller;

use App\Model\Colaborador;
use App\Model\ProdutoServico;
use App\Model\Usuario;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ImpressaoMaterialController extends BaseController {

    public function abrirImpressao(Request $request, Response $response){
        try{
            $data = $request->getQueryParams();
            if($data['pk']!=""){
                $dadosColanorador = (new Colaborador($this->pdo))->listarPk($data['pk']);
            }
            else{
                $dadosColanorador = [];
            }
            $dadosProdutoServico = (new ProdutoServico($this->pdo))->listarFuncaoColaborador($data['pk']);
            $dadosUsuario = (new Usuario($this->pdo))->listarUsuarioLogado();

            $this->view->render($response, 'partials/impressao_material.twig',
                array('pk'=>$data['pk'],
                    'leads_pk'=>$data['leads_pk'],
                    'conjunto_material_pk'=>$data['conjunto_material_pk'],
                    'local'=>$data['local'],
                    'dadosColaborador'=>$dadosColanorador->data,
                    'ds_produto_servico_impr'=>$dadosProdutoServico->data['ds_produto_servico'],
                    'ds_usuario_logado'=>$dadosUsuario->data['ds_usuario'],
                ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

}

