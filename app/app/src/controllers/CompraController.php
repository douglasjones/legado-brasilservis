<?php

namespace App\Controller;

use App\Model\Documento;
use App\Model\Log;
use App\Model\Compra;
use App\Model\Conta;
use App\Model\Fornecedor;
use App\Model\MetodoPagamento;
use App\Model\Produto;
use App\Utils\Json;
use App\Utils\Session;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Utils\SlimImage;
use App\Utils\SlimStatus;
use Throwable;

final class CompraController extends BaseController {
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'compra/compra_res_form.twig');
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
            $this->view->render($response, 'compra/compra_cad_form.twig',array(
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
                (new Log($this->pdo))->salvar('compra', $pk);

                (new Compra($this->pdo))->excluir($pk);
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
            $fornecedor_pk = isset($data['fornecedor_pk'])? $data['fornecedor_pk'] : "";
            $categorias_pk = isset($data['categorias_pk'])? $data['categorias_pk'] : "";
            $ds_numero_nota = isset($data['ds_numero_nota'])? $data['ds_numero_nota'] : "";
            $contas_pk = isset($data['contas_pk'])? $data['contas_pk'] : "";
            $dt_cadastro_ini = isset($data['dt_cadastro_ini'])? $data['dt_cadastro_ini'] : "";
            $dt_cadastro_fim = isset($data['dt_cadastro_fim'])? $data['dt_cadastro_fim'] : "";

            (new Compra($this->pdo))->listarGrid($fornecedor_pk,$categorias_pk,$ds_numero_nota,$contas_pk,$dt_cadastro_ini,$dt_cadastro_fim);

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
            $fornecedor_pk = isset($data['fornecedor_pk'])?$data['fornecedor_pk']: "";
            $categoria_pk = isset($data['categoria_pk'])?$data['categoria_pk']: "";
            $conta_pk = isset($data['conta_pk'])?$data['conta_pk']: "";
            $dt_pagamento = isset($data['dt_pagamento'])? $data['dt_pagamento'] : "";
            $metodos_pagamento_pk = isset($data['metodos_pagamento_pk'])? $data['metodos_pagamento_pk'] : "";
            $qtde_parcelas = isset($data['qtde_parcelas'])? $data['qtde_parcelas'] : "";
            $ds_numero_nota= isset($data['ds_numero_nota'])? $data['ds_numero_nota'] : "";
            $vl_notafiscal= isset($data['vl_notafiscal'])? $data['vl_notafiscal'] : "";
            $vl_frete= isset($data['vl_frete'])? $data['vl_frete'] : "";
            $dt_entrega= isset($data['dt_entrega'])? $data['dt_entrega'] : "";
            $grupo_lancamento_centro_custo_pk= isset($data['grupo_lancamento_centro_custo_pk'])? $data['grupo_lancamento_centro_custo_pk'] : "";
            $centro_custo_pk= isset($data['centro_custo_pk'])? $data['centro_custo_pk'] : "";
            $ic_status= isset($data['ic_status'])? $data['ic_status'] : "";
            $documentos_pk= isset($data['documentos_pk'])? $data['documentos_pk'] : "";

            $compra = [
                "pk"=>$pk,
                "fornecedor_pk"=>$fornecedor_pk,
                "categoria_pk"=>$categoria_pk,
                "conta_pk"=>$conta_pk,
                "metodos_pagamento_pk"=>$metodos_pagamento_pk,
                "qtde_parcelas"=>$qtde_parcelas,
                "ds_numero_nota"=>$ds_numero_nota,
                "vl_pagamento"=>$vl_notafiscal+$vl_frete,
                "vl_notafiscal"=>$vl_notafiscal,
                "vl_frete"=>$vl_frete,
                "dt_pagamento"=>($dt_pagamento),
                "dt_entrega"=>($dt_entrega),
                "grupo_lancamento_centro_custo_pk"=>$grupo_lancamento_centro_custo_pk,
                "centro_custo_pk"=>$centro_custo_pk,
                "compra_solicitacao_pk"=>"",
                "ic_status"=>$ic_status,
            ];

            $retorno = (new Compra($this->pdo))->salvar($compra);


            if($documentos_pk != "")
                $arrDocs = json_decode ($documentos_pk, true);


            if(count($arrDocs) > 0){
                for($i = 0; $i < count($arrDocs); $i++){
                    (new Documento($this->pdo))->updateDocCompra($retorno->data,$arrDocs[$i]['pk']);
                }
            }

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function salvarProduto(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk']: "";
            $compras_pk = isset($data['compras_pk'])?$data['compras_pk']: "";
            $produtos_pk = isset($data['produtos_pk'])?$data['produtos_pk']: "";
            $qtde = isset($data['qtde'])?$data['qtde']: "";
            $vl_item = isset($data['vl_item'])? $data['vl_item'] : "";
            $ic_entrega = isset($data['ic_entrega'])? $data['ic_entrega'] : "";
            $ic_status= isset($data['ic_status'])? $data['ic_status'] : "";
            $fornecedor_pk= isset($data['fornecedor_pk'])? $data['fornecedor_pk'] : "";
            
            $retorno = (new Compra($this->pdo))->salvarProduto($pk, $compras_pk, $produtos_pk, $qtde, $vl_item, $ic_entrega, $ic_status,$fornecedor_pk);


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
            $entity = new Compra($this->pdo);
            $retorno = $entity->listarPorPk($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }

    public function relControleCompra(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $empresa_pk = isset($data['empresa_pk'])? $data['empresa_pk'] : "";
            $fornecedor_pk = isset($data['fornecedor_pk'])? $data['fornecedor_pk']  : "";
            $categoria_pk = isset($data['categoria_pk'])? $data['categoria_pk']  : "";
            $tipo_grupo_centro_custo_pk = isset($data['tipo_grupo_centro_custo_pk'])? $data['tipo_grupo_centro_custo_pk']  : "";
            $grupo_lancamento_centro_custo_pk = isset($data['grupo_lancamento_centro_custo_pk'])? $data['grupo_lancamento_centro_custo_pk']  : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status']  : "";
            $dt_ini_cad = isset($data['dt_ini_cad'])? $data['dt_ini_cad']  : "";
            $dt_fim_cad = isset($data['dt_fim_cad'])? $data['dt_fim_cad']  : "";
            $dt_ini_compra = isset($data['dt_ini_compra'])? $data['dt_ini_compra']  : "";
            $dt_fim_compra = isset($data['dt_fim_compra'])? $data['dt_fim_compra']  : "";
            
          (new Compra($this->pdo))->relControleCompra($empresa_pk,$fornecedor_pk,$categoria_pk,$tipo_grupo_centro_custo_pk, $grupo_lancamento_centro_custo_pk, $ic_status, $dt_ini_cad, $dt_fim_cad, $dt_ini_compra, $dt_fim_compra);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function relControleSolicitacaoCompra(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $empresa_pk = isset($data['empresa_pk'])? $data['empresa_pk'] : "";
            $solicitante_pk = isset($data['solicitante_pk'])? $data['solicitante_pk']  : "";
            $usuario_aprovacao_pk = isset($data['usuario_aprovacao_pk'])? $data['usuario_aprovacao_pk']  : "";
            $tipo_grupo_centro_custo_pk = isset($data['tipo_grupo_centro_custo_pk'])? $data['tipo_grupo_centro_custo_pk']  : "";
            $grupo_lancamento_centro_custo_pk = isset($data['grupo_lancamento_centro_custo_pk'])? $data['grupo_lancamento_centro_custo_pk']  : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status']  : "";
            $dt_ini_cad = isset($data['dt_ini_cad'])? $data['dt_ini_cad']  : "";
            $dt_fim_cad = isset($data['dt_fim_cad'])? $data['dt_fim_cad']  : "";
            $dt_ini_aprov = isset($data['dt_ini_aprov'])? $data['dt_ini_aprov']  : "";
            $dt_fim_aprov = isset($data['dt_fim_aprov'])? $data['dt_fim_aprov']  : "";
            
          (new Compra($this->pdo))->relControleSolicitacaoCompra($empresa_pk,$solicitante_pk,$usuario_aprovacao_pk,$tipo_grupo_centro_custo_pk, $grupo_lancamento_centro_custo_pk, $ic_status, $dt_ini_cad, $dt_fim_cad, $dt_ini_aprov, $dt_fim_aprov);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function lerXml(Request $request, Response $response, $args) {
        try {
            $uploadedFiles = $request->getUploadedFiles();

            if (!isset($uploadedFiles['arquivo_xml'])) {
                return $response->withJson([
                    'status' => false,
                    'result' => 'Nenhum arquivo enviado.'
                ]);
            }

            $xmlFile = $uploadedFiles['arquivo_xml'];

            if ($xmlFile->getError() !== UPLOAD_ERR_OK) {
                return $response->withJson([
                    'status' => false,
                    'result' => 'Erro ao fazer upload.'
                ]);
            }

            // Conteúdo do XML
            $xmlContent = $xmlFile->getStream()->getContents();
            $xmlObj = simplexml_load_string($xmlContent, "SimpleXMLElement", LIBXML_NOCDATA);

            if ($xmlObj === false) {
                return $response->withJson([
                    'status' => false,
                    'result' => 'Erro ao interpretar XML.'
                ]);
            }

            // Array para facilitar manipulação
            $data = json_decode(json_encode($xmlObj), true);

            // Modelagem do retorno
            $infNFe = $data['NFe']['infNFe'];

            // --- Mapa dos meios de pagamento (tPag -> descrição) ---
            $mapaPagamento = [
                "01" => "Dinheiro",
                "02" => "Cheque",
                "03" => "Cartão de Crédito",
                "04" => "Cartão de Débito",
                "05" => "Crédito Loja",
                "10" => "Vale Alimentação",
                "11" => "Vale Refeição",
                "12" => "Vale Presente",
                "13" => "Vale Combustível",
                "15" => "Boleto Bancário",
                "16" => "Depósito Bancário",
                "17" => "PIX",
                "18" => "Transferência Bancária",
                "19" => "Programa de Fidelidade",
                "90" => "Sem Pagamento",
                "99" => "Outros"
            ];

            $tipoPag = $infNFe['pag']['detPag']['tPag'] ?? null;

            $model = [
                "nota" => [
                    "numero"   => $infNFe['ide']['nNF'] ?? null,
                    "serie"    => $infNFe['ide']['serie'] ?? null,
                    "emissao"  => $infNFe['ide']['dhEmi'] ?? null,
                    "natureza" => $infNFe['ide']['natOp'] ?? null,
                ],
                "emitente" => [
                    "cnpj"  => $infNFe['emit']['CNPJ'] ?? null,
                    "nome"  => $infNFe['emit']['xNome'] ?? null,
                    "ie"    => $infNFe['emit']['IE'] ?? null,
                    "pk"    => ((new Fornecedor($this->pdo))->verificarFornecedorPorCNPJXML(
                                    $infNFe['emit']['CNPJ'] ?? null,
                                    $infNFe['emit']['xNome'] ?? null
                                ))
                ],
                "destinatario" => [
                    "cnpj"  => $infNFe['dest']['CNPJ'] ?? null,
                    "nome"  => $infNFe['dest']['xNome'] ?? null,
                    "ie"    => $infNFe['dest']['IE'] ?? null,
                    "pk"    => (new Conta($this->pdo))->verificarContaPorCNPJ($infNFe['dest']['CNPJ'] ?? null)
                ],
                "produtos" => [],
                "total" => [
                    "valor_produtos" => number_format($infNFe['total']['ICMSTot']['vProd'] ?? 0, 2, ',', ''),
                    "valor_nota"     => number_format($infNFe['total']['ICMSTot']['vNF'] ?? 0, 2, ',', ''),
                    "valor_frete"     => number_format($infNFe['transp']['vFrete'] ?? 0, 2, ',', ''),
                ],
                "pagamento" => [
                    "tipo" => $tipoPag,
                    "descricao" => $mapaPagamento[$tipoPag] ?? "Desconhecido",
                    "valor" => number_format($infNFe['pag']['detPag']['vPag'] ?? 0, 2, ',', '') ?? null,
                    "pk"    => (new MetodoPagamento($this->pdo))->verificarFormaPagamentoXML($mapaPagamento[$tipoPag]) // chamada da função
                ]
            ];

            // Monta lista de produtos
            if(isset($infNFe['det'])) {
                $det = isset($infNFe['det'][0]) ? $infNFe['det'] : [$infNFe['det']];

                foreach($det as $item) {
                    $prod = $item['prod'];
                    $model['produtos'][] = [
                        "categoria"         => 1 ?? null,
                        "descricaoCategoria"=> "Categoria Padrão (XML)" ?? null,
                        "codigo"            => $prod['cProd'] ?? null,
                        "descricao"         => $prod['xProd'] ?? null,
                        "quantidade"        => number_format($prod['qCom'] ?? 0, 0, ',', ''), // apenas inteiro, formato BR
                        "valor_unitario"    => number_format($prod['vUnCom'] ?? 0, 2, ',', ''), // 2 casas decimais, vírgula
                        "valor_total"       => number_format($prod['vProd'] ?? 0, 2, ',', ''),  // 2 casas decimais, vírgula
                        "pk"                => (new Produto($this->pdo))->verificarProdutoPorNomeXML($prod['xProd'] ?? null)
                    ];
            }
        }
        return $response->withJson([
            'status' => true,
            'result' => $model
        ]);

    } catch (\Exception $e) {
        return $response->withJson([
            'status' => false,
            'result' => 'Erro: ' . $e->getMessage()
        ]);
    }
}






}
