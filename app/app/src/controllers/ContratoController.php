<?php

namespace App\Controller;

use App\Model\Contrato;
use App\Model\ContratoDadosFaturamento;
use App\Model\Lancamento;
use App\Model\Log;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class ContratoController extends BaseController {
    public function cadForm(Request $request, Response $response, $args){
        try{

            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk'] : "";

            $this->view->render($response, 'contrato/contrato_operacional_cad_form.twig',
                array("pk"=> $pk)
            );

        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function excluir(Request $request, Response $response, $args)
    {
        try {
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";

            if($pk!=""){
                (new Log($this->pdo))->salvar('contratos',$pk);

                (new Contrato($this->pdo))->excluir($pk);
                Json::run(true, [], 'Registro excluido com sucesso!');
            }
            else{

                Json::run(false, [], 'Falha ao excluir registro!');
            }
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function excluirProdutosItens(Request $request, Response $response, $args)
    {
        try {
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";

            if($pk!=""){

                (new Contrato($this->pdo))->excluirProdutosItens($pk);
                Json::run(true, [], 'Registro excluido com sucesso!');
            }
            else{

                Json::run(false, [], 'Falha ao excluir registro!');
            }
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function excluirProdutosItensPk(Request $request, Response $response, $args)
    {
        try {
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";

            if($pk!=""){

                (new Contrato($this->pdo))->excluirProdutosItensPk($pk);
                Json::run(true, [], 'Registro excluido com sucesso!');
            }
            else{

                Json::run(false, [], 'Falha ao excluir registro!');
            }
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function receptivo(Request $request, Response $response, $args) {
        try{

            $this->view->render($response, 'contrato/contrato_operacional_res_form.twig',array());
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function salvar(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $contratos_itens = isset($data['contratos_itens'])? $data['contratos_itens'] : "";
            $contrato_dados_faturamento = isset($data['contrato_dados_faturamento'])? $data['contrato_dados_faturamento'] : "";
            //CONTRATOS ITENS
            $arrContratoItens = [];
            if($contratos_itens != ""){
                $arrContratoItens = json_decode ($contratos_itens, true);
            }
            //DADOS FATURAMENTO
            $arrContratoDadosFaturamento = [];
            if($contrato_dados_faturamento != ""){
                $arrContratoDadosFaturamento = json_decode ($contrato_dados_faturamento, true);
            }
            //CONTRATOS
            $pk = isset($data['pk'])? $data['pk'] : "";
            $dt_inicio_contrato = isset($data['dt_inicio_contrato'])? $data['dt_inicio_contrato'] : "";
            $dt_fim_contrato = isset($data['dt_fim_contrato'])? $data['dt_fim_contrato'] : "";
            $processos_etapas_pk = isset($data['processos_etapas_pk'])? $data['processos_etapas_pk'] : "";
            $ic_tipo_contrato = isset($data['ic_tipo_contrato'])? $data['ic_tipo_contrato'] : "";
            $contrato_pai_pk = isset($data['contrato_pai_pk'])? $data['contrato_pai_pk'] : "";
            $dt_cancelamento = isset($data['dt_cancelamento'])? $data['dt_cancelamento'] : "";
            $ds_obs_motivo_cancelamento = isset($data['ds_obs_motivo_cancelamento'])? $data['ds_obs_motivo_cancelamento'] : "";
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk'] : "";
            $ic_lancar_financeiro = isset($data['ic_lancar_financeiro'])? $data['ic_lancar_financeiro'] : "";
            $qtde_parcelas_pk = isset($data['qtde_parcelas_pk'])? $data['qtde_parcelas_pk'] : "";
            $vl_total_mao_obra = isset($data['vl_total_mao_obra'])? $data['vl_total_mao_obra'] : "";
            $ds_identificacao_area = isset($data['ds_identificacao_area'])? $data['ds_identificacao_area'] : "";
            $vl_contrato = isset($data['vl_contrato'])? $data['vl_contrato'] : "";
            $metodos_pagamento_pk = isset($data['metodos_pagamento_pk'])? $data['metodos_pagamento_pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";

            if($dt_inicio_contrato !=""){
                $dt_inicio_contrato_formatado = Util::DataYMD($dt_inicio_contrato);
            }
            else{
                $dt_inicio_contrato_formatado = "sysdate()";
            }
            if($dt_fim_contrato !=""){
                $dt_fim_contrato_formatado = Util::DataYMD($dt_fim_contrato);
            }
            else{
                $dt_fim_contrato_formatado = "sysdate()";
            }

            $contrato =[
                "pk"=>$pk,
                "dt_inicio_contrato"=>$dt_inicio_contrato_formatado,
                "dt_fim_contrato"=>$dt_fim_contrato_formatado,
                "processos_etapas_pk"=>$processos_etapas_pk,
                "ic_tipo_contrato"=>$ic_tipo_contrato,
                "contratos_pk"=>$contrato_pai_pk,
                "dt_cancelamento"=>$dt_cancelamento,
                "ds_obs_motivo_cancelamento"=>$ds_obs_motivo_cancelamento,
                "empresas_pk"=>$empresas_pk,
                "ic_lancar_financeiro"=>$ic_lancar_financeiro,
                "qtde_parcelas_pk"=>$qtde_parcelas_pk,
                "vl_total_mao_obra"=>$vl_total_mao_obra,
                "ds_identificacao_area"=>$ds_identificacao_area,
                "vl_contrato"=>$vl_contrato
            ];

            $retorno = (new Contrato($this->pdo))->salvar($contrato);

            if($pk==""){
                $contratos_pk = $retorno->data;
            }
            else{
                $contratos_pk = $pk;
            }

            
            if(count($arrContratoItens) > 0){
                //REMOVER CONTRATOS 
                //(new Contrato($this->pdo))->deleteContratoItensPorContratoPk($contratos_pk);
                for($i = 0; $i < count($arrContratoItens); $i++){
                    //tira ponto e a virgula para salvar o valor no BD
                    $valor_unitario= ($arrContratoItens[$i]["vl_unit"]);
                    $valor_total= $arrContratoItens[$i]['n_qtde'] * $arrContratoItens[$i]["vl_unit"];

                    (new Contrato($this->pdo))->adicionarContratoItens($arrContratoItens[$i]['contratos_itens_pk'],$contratos_pk,$arrContratoItens[$i]['n_qtde_dias_semana'], $arrContratoItens[$i]['n_qtde'], $valor_unitario, $valor_total, $arrContratoItens[$i]["produtos_servicos_pk"],$arrContratoItens[$i]["periodo"],$arrContratoItens[$i]["vl_mao_obra"]);
                }
            }

            

            if(count($arrContratoDadosFaturamento) > 0){
                (new Contrato($this->pdo))->excluirContratoDadosFaturamento($contratos_pk);
                for($i = 0; $i < count($arrContratoDadosFaturamento); $i++){
                    $contrato_dados_faturamento = [
                        "pk"=>"",
                        "metodos_pagamento_pk"=>$metodos_pagamento_pk,
                        "num_parcela"=>$arrContratoDadosFaturamento[$i]['num_parcela'],
                        "dt_pagamento"=>Util::DataYMD($arrContratoDadosFaturamento[$i]['dt_pagamento']),
                        "dt_faturamento"=>Util::DataYMD($arrContratoDadosFaturamento[$i]['dt_faturamento']),
                        "vl_servico"=>($arrContratoDadosFaturamento[$i]['vl_pagamento']),
                        "contratos_pk"=>$contratos_pk,
                    ];

                    (new ContratoDadosFaturamento($this->pdo))->salvar($contrato_dados_faturamento);
                }
            }
            //Lançar no financeiro
            if($pk==""){
                if($ic_lancar_financeiro==1){
                    if(count($arrContratoDadosFaturamento) > 0){
                        for($i = 0; $i < count($arrContratoDadosFaturamento); $i++){
                            $conta_bancaria_pk =  (new Lancamento($this->pdo))->listaContaEmpresa($empresas_pk);
                            $lancamento = [
                                "pk"=>"",
                                "operacao_pk"=>1,
                                "tipos_operacao_pk"=>1002,
                                "empresas_pk"=>$empresas_pk,
                                "contas_bancarias_pk"=>$conta_bancaria_pk->data['pk'],
                                "ds_lancamento"=>"Serviço Extra",
                                "tipo_grupo_pk"=>1,
                                "grupo_leancamento_pk"=>$leads_pk,
                                "metodos_pagamento_pk"=>$metodos_pagamento_pk,
                                "vl_lancamento"=>$arrContratoDadosFaturamento[$i]['vl_pagamento'],
                                "dt_vencimento"=>$arrContratoDadosFaturamento[$i]['dt_pagamento'],
                                "dt_faturamento"=>$arrContratoDadosFaturamento[$i]['dt_faturamento'],
                                "ic_status_pagamento"=>2,
                                "contratos_pk"=>$contratos_pk,
                            ];
                            (new Lancamento($this->pdo))->salvarLancamentoByContrato($lancamento);
                        }
                    }
                }
            }

            Json::run($retorno->status, $contratos_pk, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }
    public function salvarProdutosItens(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk'] : "";
            $categorias_produto_pk = isset($data['categorias_produto_pk'])? $data['categorias_produto_pk'] : "";
            $produtos_pk = isset($data['produtos_pk'])? $data['produtos_pk'] : "";
            $n_qtde_item = isset($data['n_qtde_item'])? $data['n_qtde_item'] : "";
            $vl_item_produto = isset($data['vl_item_produto'])? $data['vl_item_produto'] : "";

            $retorno = (new Contrato($this->pdo))->salvarProdutosItens($pk,$categorias_produto_pk,$produtos_pk,$n_qtde_item,$vl_item_produto);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }

    public function listarContratoOperacional(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $leads_postotrabalho_pk = isset($data['leads_postotrabalho_pk'])? $data['leads_postotrabalho_pk'] : "";
            $ic_tipo_contrato = isset($data['ic_tipo_contrato'])? $data['ic_tipo_contrato'] : "";
            $dt_inicio_contrato = isset($data['dt_inicio_contrato'])? $data['dt_inicio_contrato'] : "";
            $dt_fim_contrato = isset($data['dt_fim_contrato'])? $data['dt_fim_contrato'] : "";
            $dt_recisao_contrato_ini = isset($data['dt_recisao_contrato_ini'])? $data['dt_recisao_contrato_ini'] : "";
            $dt_recisao_contrato_fim = isset($data['dt_recisao_contrato_fim'])? $data['dt_recisao_contrato_fim'] : "";
            $dt_cancelamento_ini = isset($data['dt_cancelamento_ini'])? $data['dt_cancelamento_ini'] : "";
            $dt_cancelamento_fim = isset($data['dt_cancelamento_fim'])? $data['dt_cancelamento_fim'] : "";
            $leads_clientes_pk = isset($data['leads_clientes_pk'])? $data['leads_clientes_pk'] : "";

            (new Contrato($this->pdo))->listarContratoOperacional($pk,$leads_postotrabalho_pk,$ic_tipo_contrato,$dt_inicio_contrato,$dt_fim_contrato,$dt_recisao_contrato_ini,$dt_recisao_contrato_fim,$dt_cancelamento_ini,$dt_cancelamento_fim,$leads_clientes_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarPkCad(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";

            $retorno = (new Contrato($this->pdo))->listarPkCad($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarProdutosItens(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";

            $retorno = (new Contrato($this->pdo))->listarProdutosItens($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarContratoPai(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $contratos_pk = isset($data['contratos_pk'])? $data['contratos_pk'] : "";
            $contrato_pai_pk = isset($data['contrato_pai_pk'])? $data['contrato_pai_pk'] : "";

            $retorno = (new Contrato($this->pdo))->listar_contrato_pai($leads_pk,$contratos_pk,$contrato_pai_pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarLeadsPk(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";

            $retorno = (new Contrato($this->pdo))->listarLeadsPk($leads_pk);

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

            $retorno = (new Contrato($this->pdo))->listarPk($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listaColaboradorContratos(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $retorno = (new Contrato($this->pdo))->listaColaboradorContratosFinanceiro($leads_pk,$colaborador_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listaLeadContratos(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $retorno = (new Contrato($this->pdo))->listaLeadContratosFinanceiro($leads_pk,$colaborador_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
	public function listarContratos(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $empresa_pk = isset($data['empresa_pk'])? $data['empresa_pk'] : "";
            $cliente_pk = isset($data['cliente_pk'])? $data['cliente_pk'] : "";
            
            $retorno = (new Contrato($this->pdo))->listarContratos($leads_pk, $empresa_pk, $cliente_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function relContrato(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $empresa_pk = isset($data['empresa_pk'])? $data['empresa_pk'] : "";
            $leads_clientes_pk = isset($data['leads_clientes_pk'])? $data['leads_clientes_pk']  : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']  : "";
            $dt_ini_cadastro = isset($data['dt_ini_cadastro'])? $data['dt_ini_cadastro']  : "";
            $dt_fim_cadastro = isset($data['dt_fim_cadastro'])? $data['dt_fim_cadastro']  : "";
            $dt_ini_contrato = isset($data['dt_ini_contrato'])? $data['dt_ini_contrato']  : "";
            $dt_fim_contrato = isset($data['dt_fim_contrato'])? $data['dt_fim_contrato']  : "";
            $usuario_cadastro_pk = isset($data['usuario_cadastro_pk'])? $data['usuario_cadastro_pk']  : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status']  : "";
            $ds_cpf_cnpj = isset($data['ds_cpf_cnpj'])? $data['ds_cpf_cnpj']  : "";
            $tp_contrato = isset($data['tp_contrato'])? $data['tp_contrato']  : "";
            
          (new Contrato($this->pdo))->relContrato($empresa_pk,$leads_clientes_pk,$leads_pk,$dt_ini_cadastro, $dt_fim_cadastro, $dt_ini_contrato, $dt_fim_contrato, $usuario_cadastro_pk, $ic_status, $tp_contrato,$ds_cpf_cnpj);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}