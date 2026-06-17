<?php

namespace App\Controller;

use App\Model\CategoriaFinanceira;
use App\Model\Colaborador;
use App\Model\Conta;
use App\Model\Lancamento;
use App\Model\NfeApi;
use App\Model\Lead;
use App\Model\Log;
use App\Model\MetodoPagamento;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class LancamentoController extends BaseController {

    public function listaItensGrupoLeads(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
           
            $retorno = (new Lancamento($this->pdo))->listaItensGrupoLeads($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listaItensGrupoColaboradores(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
           
            $retorno = (new Lancamento($this->pdo))->listaItensGrupoColaboradores($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listaItensGrupoFornecedores(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";

            $retorno = (new Lancamento($this->pdo))->listaItensGrupoFornecedores($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

public function excluir(Request $request, Response $response, $args){
        try {
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";

            if($pk!=""){
                (new Log($this->pdo))->salvar('lancamentos_financeiros',$pk);

                (new Lancamento($this->pdo))->excluir($pk);
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
    public function usuarioFinanceiroReceptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'lancamento/financeiro_usuario_lancamento_res_form.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function contasPagarReceberReceptivo(Request $request, Response $response, $args){
        try{

            $arrColaborador = (new Colaborador($this->pdo))->listarTodos("");
            $arrLeads = (new Lead($this->pdo))->listaLeadsClientes();
            $arrConta = (new Conta($this->pdo))->listarTodos();
            $arrMetodoPagamento = (new MetodoPagamento($this->pdo))->listar_por_ds_metodo_pagamento();
           
            $this->view->render($response, 'lancamento/financeiro_contas_pagar_receber_res_form.twig',array(
                "arrColaborador" => $arrColaborador,
                "arrLeads" => $arrLeads,
                "arrConta" =>$arrConta,
                "arrMetodoPagamento" =>$arrMetodoPagamento,
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function impressaoLancamento(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $this->view->render($response, 'lancamento/impressao_lancamento.twig', array('pk'=>$data['pk']));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarImpressao(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            
            $retorno = (new Lancamento($this->pdo))->listarImpressao($pk);
            
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarLancamentosUsuarios(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $ic_status_pagamento = isset($data['ic_status_pagamento'])? $data['ic_status_pagamento'] : "";
            $usuario_cadastro_pk = isset($data['usuario_cadastro_pk'])? $data['usuario_cadastro_pk'] : "";
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk'] : "";
            $contas_pk = isset($data['contas_pk'])? $data['contas_pk'] : "";
            $tipo_grupo_pk = isset($data['tipo_grupo_pk'])? $data['tipo_grupo_pk'] : "";
            $grupo_lancamento_pk = isset($data['grupo_lancamento_pk'])? $data['grupo_lancamento_pk'] : "";
            $dt_cadastro_ini = isset($data['dt_cadastro_ini'])? $data['dt_cadastro_ini'] : "";
            $dt_cadastro_fim = isset($data['dt_cadastro_fim'])? $data['dt_cadastro_fim'] : "";
            $dt_faturamento_ini = isset($data['dt_faturamento_ini'])? $data['dt_faturamento_ini'] : "";
            $dt_faturamento_fim = isset($data['dt_faturamento_fim'])? $data['dt_faturamento_fim'] : "";
            $dt_vencimento_ini = isset($data['dt_vencimento_ini'])? $data['dt_vencimento_ini'] : "";
            $dt_vencimento_fim = isset($data['dt_vencimento_fim'])? $data['dt_vencimento_fim'] : "";
            $dt_pagamento_ini = isset($data['dt_pagamento_ini'])? $data['dt_pagamento_ini'] : "";
            $dt_pagamento_fim = isset($data['dt_pagamento_fim'])? $data['dt_pagamento_fim'] : "";
            $ic_status_analise = isset($data['ic_status_analise'])? $data['ic_status_analise'] : "";
            $ds_num_documento = isset($data['ds_num_documento'])? $data['ds_num_documento'] : "";
            $ic_tipo_num_documento = isset($data['ic_tipo_num_documento'])? $data['ic_tipo_num_documento'] : "";

            $retorno = (new Lancamento($this->pdo))->listarLancamentosUsuarios($pk, $ic_status_pagamento, $usuario_cadastro_pk, $empresas_pk, $contas_pk,$tipo_grupo_pk, $grupo_lancamento_pk, $dt_cadastro_ini, $dt_cadastro_fim, $dt_faturamento_ini, $dt_faturamento_fim, $dt_vencimento_ini, $dt_vencimento_fim, $dt_pagamento_ini, $dt_pagamento_fim, $ic_status_analise, $ds_num_documento, $ic_tipo_num_documento);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarLancamentoPk(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";

            $retorno = (new Lancamento($this->pdo))->listarLancamentoPk($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function salvar(Request $request, Response $response, $args) {
        try{
            //$data = $request->getQueryParams();
            $data = $_POST;

            $pk = isset($data['pk'])? $data['pk'] : "";
            $ds_lancamento = isset($data['ds_lancamento'])? $data['ds_lancamento'] : "";
            $tipo_lancamento_pk = isset($data['tipo_lancamento_pk'])? $data['tipo_lancamento_pk'] : "";
            $categorias_financeiras_pk = isset($data['categorias_financeiras_pk'])? $data['categorias_financeiras_pk'] : "";
            $tipos_operacao_pk = isset($data['tipos_operacao_pk'])? $data['tipos_operacao_pk'] : "";
            $tipo_grupo_pk = isset($data['tipo_grupo_pk'])? $data['tipo_grupo_pk'] : "";
            $grupo_lancamento_pk = isset($data['grupo_lancamento_pk'])? $data['grupo_lancamento_pk'] : "";
            $cliente_lancamento_pk = isset($data['cliente_lancamento_pk'])? $data['cliente_lancamento_pk'] : "";
            $posto_trabalho_lancamento_pk = isset($data['posto_trabalho_lancamento_pk'])? $data['posto_trabalho_lancamento_pk'] : "";
            $contratos_pk = isset($data['contratos_pk'])? $data['contratos_pk'] : "";
            $metodos_pagamento_pk = isset($data['metodos_pagamento_pk'])? $data['metodos_pagamento_pk'] : "";
            $empresa_lancamento_pk = isset($data['empresa_lancamento_pk'])? $data['empresa_lancamento_pk'] : "";
            $contas_bancarias_pk = isset($data['contas_bancarias_pk'])? $data['contas_bancarias_pk'] : "";
            $ic_status_lancamento = isset($data['ic_status_lancamento'])? $data['ic_status_lancamento'] : "";
            $dt_pagamento = isset($data['dt_pagamento'])? $data['dt_pagamento'] : "";
            $obs_lancamento = isset($data['obs_lancamento'])? $data['obs_lancamento'] : "";
            $ds_num_documento = isset($data['ds_num_documento'])? $data['ds_num_documento'] : "";
            $ic_tipo_num_documento = isset($data['ic_tipo_num_documento'])? $data['ic_tipo_num_documento'] : "";
            $qtde_parcelas_pk = isset($data['qtde_parcelas_pk'])? $data['qtde_parcelas_pk'] : "";
            $dt_faturamento = isset($data['dt_faturamento'])? Util::DataYMD($data['dt_faturamento']) : "";
            $dt_vencimento = isset($data['dt_vencimento'])?  Util::DataYMD($data['dt_vencimento']) : "";
            $vl_lancamento = isset($data['vl_lancamento'])? $data['vl_lancamento'] : "";
            $vl_parcial = isset($data['vl_parcial'])? $data['vl_parcial'] : "";
            $arrParcelas = isset($data['arrParcelas'])? $data['arrParcelas'] : "";
            $doc_lancamento = isset($data['doc_lancamento'])? $data['doc_lancamento'] : "";
            $grupo_lancamento_fornecedor_pk = isset($data['grupo_lancamento_fornecedor_pk'])? $data['grupo_lancamento_fornecedor_pk'] : "";
            
            //EMITIR NOTA
            $arrNfse = isset($data['arrNfse'])? $data['arrNfse'] : "";


            $lancamento =[
                "pk"=>$pk,
                "ds_lancamento"=>$ds_lancamento,
                "tipo_lancamento_pk"=>$tipo_lancamento_pk,
                "categorias_financeiras_pk"=>$categorias_financeiras_pk,
                "tipos_operacao_pk"=>$tipos_operacao_pk,
                "tipo_grupo_pk"=>$tipo_grupo_pk,
                "grupo_lancamento_pk"=>$grupo_lancamento_pk,
                "cliente_lancamento_pk"=>$cliente_lancamento_pk,
                "posto_trabalho_lancamento_pk"=>$posto_trabalho_lancamento_pk,
                "contratos_pk"=>$contratos_pk,
                "metodos_pagamento_pk"=>$metodos_pagamento_pk,
                "empresa_lancamento_pk"=>$empresa_lancamento_pk,
                "contas_bancarias_pk"=>$contas_bancarias_pk,
                "ic_status_lancamento"=>$ic_status_lancamento,
                "dt_pagamento"=>$dt_pagamento,
                "obs_lancamento"=>$obs_lancamento,
                "ds_num_documento"=>$ds_num_documento,
                "ic_tipo_num_documento"=>$ic_tipo_num_documento,
                "qtde_parcelas_pk"=>$qtde_parcelas_pk,
                "dt_faturamento"=>$dt_faturamento,
                "dt_vencimento"=>$dt_vencimento,
                "vl_lancamento"=>$vl_lancamento,
                "vl_parcial"=>$vl_parcial,
                "arrParcelas"=>$arrParcelas,
                "doc_lancamento"=>$doc_lancamento,
                "grupo_lancamento_fornecedor_pk"=>$grupo_lancamento_fornecedor_pk
            ];

            $retorno = (new Lancamento($this->pdo))->salvar($lancamento);


            $arrDados = json_decode($arrNfse, true);
  
            if(count($arrDados)> 0){

                if(isset($arrDados[0]['tomador_pk'])){
                    // Obtém o valor do campo 'texto' e remove quebras de linha
                    $discriminacao = str_replace("\n", "", $arrDados[0]['descricao_nfse']);
                
                


                    //PEGAR TODOS OS DADOS DO PRESTADOR
                    $arrDadosPrestador = (new NfeApi($this->pdo))->contaConfigConsultaPk($arrDados[0]['prestador_pk']);
                    $arrDadosRazaoSocial = (new Lead($this->pdo))->listarPorPk($arrDados[0]['tomador_pk']);
                    
                    
                    $controle = [
                        "pk" => "",
                        "tipoNotaFiscalEletronica" => 1,
                        "prestador" => $arrDados[0]['prestador_pk'],
                        "naturezaOperacao" => $arrDados[0]['naturezaOperacao'],
                        "razaoSocial" => $arrDados[0]['tomador_pk'],
                        "cnpj" => $arrDados[0]['cnpj'],
                        "cep" => $arrDados[0]['cep'],
                        "estado" => $arrDados[0]['estado'],
                        "cidade" => $arrDados[0]['cidade'],
                        "bairro" => $arrDados[0]['bairro'],
                        "tipoLogradouro" => $arrDados[0]['tipoLogradouro'],
                        "logradouro" => $arrDados[0]['logradouro'],
                        "numero" => $arrDados[0]['numero'],
                        "complemento" => $arrDados[0]['complemento'],
                        "inscricaoMunicipal" => $arrDados[0]['inscricaoMunicipal'],
                        "inscricaoEstadual" => $arrDados[0]['inscricaoEstadual'],
                        "retidoTomador" => $arrDados[0]['retidoTomador'],
                        "email" => $arrDados[0]['email'],
                        "codigo" => $arrDados[0]['codigo_servico_pk'],
                        "descricaoServico" => $arrDados[0]['descricaoServico'],
                        "aliquota" => $arrDados[0]['aliquota'],
                        "codigo_tributacao"=>$arrDados[0]['codigo_tributacao'],
                        "discriminacao" => $discriminacao,
                        "valorServico" => '150',//$arrDados[0]['vl_lancamento'],
                        "numeroRPS" => "",
                        "serieRPS" => "",
                        "dataEmissaoRPS" => "",
                        "valorDeducao" => "",
                        "listaServicoConsulta" => $arrDados[0]['codigo_servico_pk'],
                        "arrDadosPrestador"=>$arrDadosPrestador->data,
                        "arrDadosRazaoSocial"=>$arrDadosRazaoSocial->data[0],
                        "iss_aliquota"=>$arrDados[0]['iss_aliquota'],
                        "iss_valor"=>$arrDados[0]['iss_valor'],
                        "inss_aliquota"=>$arrDados[0]['inss_aliquota'],
                        "inss_valor"=>$arrDados[0]['inss_valor'],
                        "pis_aliquota"=>$arrDados[0]['pis_aliquota'],
                        "pis_valor"=>$arrDados[0]['pis_valor'],
                        "cofins_aliquota"=>$arrDados[0]['cofins_aliquota'],
                        "cofins_valor"=>$arrDados[0]['cofins_valor'],
                        "ir_aliquota"=>$arrDados[0]['ir_aliquota'],
                        "ir_valor"=>$arrDados[0]['ir_valor'],
                        "csll_aliquota"=>$arrDados[0]['csll_aliquota'],
                        "csll_valor"=>$arrDados[0]['csll_valor']
                    ];

                  

                    (new NfeApi($this->pdo))->salvarControleNfse($controle);

              
                }

                
            }

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }
    public function migrarBaseFinanceira(Request $request, Response $response, $args) {
        try{
            $retorno = (new Lancamento($this->pdo))->migrarBaseFinanceira();

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }
    public function listarExtratoMes(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk'] : "";
            $contas_bancarias_pk = isset($data['contas_bancarias_pk'])? $data['contas_bancarias_pk'] : "";
            $dt_cadastro_ini = isset($data['dt_cadastro_ini'])? $data['dt_cadastro_ini'] : "";
            $dt_cadastro_fim = isset($data['dt_cadastro_fim'])? $data['dt_cadastro_fim'] : "";
            $dt_faturamento_ini = isset($data['dt_faturamento_ini'])? $data['dt_faturamento_ini'] : "";
            $dt_faturamento_fim = isset($data['dt_faturamento_fim'])? $data['dt_faturamento_fim'] : "";
            $dt_vencimento_ini = isset($data['dt_vencimento_ini'])? $data['dt_vencimento_ini'] : "";
            $dt_vencimento_fim = isset($data['dt_vencimento_fim'])? $data['dt_vencimento_fim'] : "";
            $dt_pagamento_ini = isset($data['dt_pagamento_ini'])? $data['dt_pagamento_ini'] : "";
            $dt_pagamento_fim = isset($data['dt_pagamento_fim'])? $data['dt_pagamento_fim'] : "";
            $ds_ano = isset($data['ds_ano'])? $data['ds_ano'] : "";
            $ds_mes = isset($data['ds_mes'])? $data['ds_mes'] : "";

            $retorno = (new Lancamento($this->pdo))->listarExtratoMes($empresas_pk, $contas_bancarias_pk, $dt_cadastro_ini,$dt_cadastro_fim,$dt_faturamento_ini,$dt_faturamento_fim,$dt_vencimento_ini,$dt_vencimento_fim,$dt_pagamento_ini,$dt_pagamento_fim, $ds_ano, $ds_mes);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarHistoricoParcial(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $lancamentos_financeiros_pk = isset($data['lancamentos_financeiros_pk'])? $data['lancamentos_financeiros_pk'] : "";

            $retorno = (new Lancamento($this->pdo))->listarHistoricoParcial($lancamentos_financeiros_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarReceita(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk'] : "";
            $pk = isset($data['pk'])? $data['pk'] : "";
            $ic_status_lancamento = isset($data['ic_status_lancamento'])? $data['ic_status_lancamento'] : "";
            $ds_lancamento = isset($data['ds_lancamento'])? $data['ds_lancamento'] : "";
            $usuario_cadastro_pk = isset($data['usuario_cadastro_pk'])? $data['usuario_cadastro_pk'] : "";
            $tipo_grupo_lancamento_pk = isset($data['tipo_grupo_lancamento_pk'])? $data['tipo_grupo_lancamento_pk'] : "";
            $grupo_lancamento_pk = isset($data['grupo_lancamento_pk'])? $data['grupo_lancamento_pk'] : "";
            
            $ds_num_documento = isset($data['ds_num_documento'])? $data['ds_num_documento'] : "";
            $ic_tipo_num_documento = isset($data['ic_tipo_num_documento'])? $data['ic_tipo_num_documento'] : "";
            $categorias_financeiras_pk = isset($data['categorias_financeiras_pk'])? $data['categorias_financeiras_pk'] : "";
            $tipo_lancamento_pk = isset($data['tipo_lancamento_pk'])? $data['tipo_lancamento_pk'] : "";
            $tipos_operacao_pk = isset($data['tipos_operacao_pk'])? $data['tipos_operacao_pk'] : "";
            $contas_bancarias_pk = isset($data['contas_bancarias_pk'])? $data['contas_bancarias_pk'] : "";

            $dt_cadastro = isset($data['dt_cadastro'])? $data['dt_cadastro'] : "";
            $dt_faturamento = isset($data['dt_faturamento'])? $data['dt_faturamento'] : "";
            $dt_vencimento = isset($data['dt_vencimento'])? $data['dt_vencimento'] : "";
            $dt_pagamento = isset($data['dt_pagamento'])? $data['dt_pagamento'] : "";
           


            if($dt_cadastro!=" - "){
                $auxC = explode('-', $dt_cadastro);
                $dt_cadastro_ini = trim($auxC[0]);
                $dt_cadastro_fim = trim($auxC[1]);
            }
            else{
                $dt_cadastro_ini ="";
                $dt_cadastro_fim = "";
            }


            if($dt_faturamento!=" - "){
                $auxF = explode('-', $dt_faturamento);
                $dt_faturamento_ini = trim($auxF[0]);
                $dt_faturamento_fim = trim($auxF[1]);

            }
            else{
                $dt_faturamento_ini ="";
                $dt_faturamento_fim = "";
            }

            if($dt_vencimento!=" - "){
                $auxV = explode('-', $dt_vencimento);
                $dt_vencimento_ini = trim($auxV[0]);
                $dt_vencimento_fim = trim($auxV[1]);

            }
            else{
                $dt_vencimento_ini ="";
                $dt_vencimento_fim = "";
            }

            if($dt_pagamento!=" - "){
                $auxP = explode('-', $dt_pagamento);
                $dt_pagamento_ini = trim($auxP[0]);
                $dt_pagamento_fim = trim($auxP[1]);
            }
            else{
                $dt_pagamento_ini ="";
                $dt_pagamento_fim = "";
            }

            $retorno = (new Lancamento($this->pdo))->listarReceita($pk,$ic_status_lancamento, $ds_lancamento,$usuario_cadastro_pk,$empresas_pk,$tipo_grupo_lancamento_pk,$grupo_lancamento_pk,$dt_cadastro_ini,$dt_cadastro_fim,$dt_faturamento_ini,$dt_faturamento_fim,$dt_vencimento_ini,$dt_vencimento_fim,$dt_pagamento_ini,$dt_pagamento_fim,$ds_num_documento,$ic_tipo_num_documento, $categorias_financeiras_pk, $tipo_lancamento_pk, $tipos_operacao_pk, $contas_bancarias_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarDespesa(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();

            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk'] : "";
            $pk = isset($data['pk'])? $data['pk'] : "";
            $ic_status_lancamento = isset($data['ic_status_lancamento'])? $data['ic_status_lancamento'] : "";
            $ds_lancamento = isset($data['ds_lancamento'])? $data['ds_lancamento'] : "";
            $usuario_cadastro_pk = isset($data['usuario_cadastro_pk'])? $data['usuario_cadastro_pk'] : "";
            $tipo_grupo_lancamento_pk = isset($data['tipo_grupo_lancamento_pk'])? $data['tipo_grupo_lancamento_pk'] : "";
            $grupo_lancamento_pk = isset($data['grupo_lancamento_pk'])? $data['grupo_lancamento_pk'] : "";
            
            $ds_num_documento = isset($data['ds_num_documento'])? $data['ds_num_documento'] : "";
            $ic_tipo_num_documento = isset($data['ic_tipo_num_documento'])? $data['ic_tipo_num_documento'] : "";
            $categorias_financeiras_pk = isset($data['categorias_financeiras_pk'])? $data['categorias_financeiras_pk'] : "";
            $tipo_lancamento_pk = isset($data['tipo_lancamento_pk'])? $data['tipo_lancamento_pk'] : "";
            $tipos_operacao_pk = isset($data['tipos_operacao_pk'])? $data['tipos_operacao_pk'] : "";
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk'] : "";
            $contas_bancarias_pk = isset($data['contas_bancarias_pk'])? $data['contas_bancarias_pk'] : "";


            $dt_cadastro = isset($data['dt_cadastro'])? $data['dt_cadastro'] : "";
            $dt_faturamento = isset($data['dt_faturamento'])? $data['dt_faturamento'] : "";
            $dt_vencimento = isset($data['dt_vencimento'])? $data['dt_vencimento'] : "";
            $dt_pagamento = isset($data['dt_pagamento'])? $data['dt_pagamento'] : "";
           


            if($dt_cadastro!=" - "){
                $auxC = explode('-', $dt_cadastro);
                $dt_cadastro_ini = trim($auxC[0]);
                $dt_cadastro_fim = trim($auxC[1]);
            }
            else{
                $dt_cadastro_ini ="";
                $dt_cadastro_fim = "";
            }


            if($dt_faturamento!=" - "){
                $auxF = explode('-', $dt_faturamento);
                $dt_faturamento_ini = trim($auxF[0]);
                $dt_faturamento_fim = trim($auxF[1]);

            }
            else{
                $dt_faturamento_ini ="";
                $dt_faturamento_fim = "";
            }

            if($dt_vencimento!=" - "){
                $auxV = explode('-', $dt_vencimento);
                $dt_vencimento_ini = trim($auxV[0]);
                $dt_vencimento_fim = trim($auxV[1]);

            }
            else{
                $dt_vencimento_ini ="";
                $dt_vencimento_fim = "";
            }

            if($dt_pagamento!=" - "){
                $auxP = explode('-', $dt_pagamento);
                $dt_pagamento_ini = trim($auxP[0]);
                $dt_pagamento_fim = trim($auxP[1]);
            }
            else{
                $dt_pagamento_ini ="";
                $dt_pagamento_fim = "";
            }

            $retorno = (new Lancamento($this->pdo))->listarDespesa($pk,$ic_status_lancamento, $ds_lancamento, $usuario_cadastro_pk,$empresas_pk,$tipo_grupo_lancamento_pk,$grupo_lancamento_pk,$dt_cadastro_ini,$dt_cadastro_fim,$dt_faturamento_ini,$dt_faturamento_fim,$dt_vencimento_ini,$dt_vencimento_fim,$dt_pagamento_ini,$dt_pagamento_fim,$ds_num_documento,$ic_tipo_num_documento, $categorias_financeiras_pk, $tipo_lancamento_pk, $tipos_operacao_pk, $empresas_pk, $contas_bancarias_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {

            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarDashboard(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();

            $dt_vencimento = isset($data['dt_vencimento'])? $data['dt_vencimento'] : "";
            $ic_receita_despesa = isset($data['ic_receita_despesa'])? $data['ic_receita_despesa'] : "";
           

            if($dt_vencimento!=" - "){
                $auxV = explode('-', $dt_vencimento);
                $dt_vencimento_ini = trim($auxV[0]);
                $dt_vencimento_fim = trim($auxV[1]);

            }
            else{
                $dt_vencimento_ini ="";
                $dt_vencimento_fim = "";
            }

            $vencidos = (new Lancamento($this->pdo))->listarVencidos($ic_receita_despesa);
            $vencidosHoje = (new Lancamento($this->pdo))->listarVencidosHoje($ic_receita_despesa,$dt_vencimento_ini,$dt_vencimento_fim);
            $aVencer = (new Lancamento($this->pdo))->listarAVencer($ic_receita_despesa,$dt_vencimento_ini,$dt_vencimento_fim);
            $recebidos = (new Lancamento($this->pdo))->listarRecebidos($ic_receita_despesa,$dt_vencimento_ini,$dt_vencimento_fim);
            $valorTotal = $vencidos + $vencidosHoje + $aVencer + $recebidos;


           

            $arrDados = [
                "vencidos" =>$vencidos,
                "vencidosHoje" =>$vencidosHoje,
                "aVencer" =>$aVencer,
                "recebidos" =>$recebidos,
                "valorTotal" =>$valorTotal,
            ];
            Json::run(true, $arrDados, "Carregado com sucesso!");
        } catch (Throwable $th) {

            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarLancamento(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk'] : "";
            $pk = isset($data['pk'])? $data['pk'] : "";
            $ic_status_lancamento = isset($data['ic_status_lancamento'])? $data['ic_status_lancamento'] : "";
            $ds_lancamento = isset($data['ds_lancamento'])? $data['ds_lancamento'] : "";
            $usuario_cadastro_pk = isset($data['usuario_cadastro_pk'])? $data['usuario_cadastro_pk'] : "";
            $tipo_grupo_lancamento_pk = isset($data['tipo_grupo_lancamento_pk'])? $data['tipo_grupo_lancamento_pk'] : "";
            $grupo_lancamento_pk = isset($data['grupo_lancamento_pk'])? $data['grupo_lancamento_pk'] : "";
             $ds_num_documento = isset($data['ds_num_documento'])? $data['ds_num_documento'] : "";
            $ic_tipo_num_documento = isset($data['ic_tipo_num_documento'])? $data['ic_tipo_num_documento'] : "";
            $categorias_financeiras_pk = isset($data['categorias_financeiras_pk'])? $data['categorias_financeiras_pk'] : "";
            $tipo_lancamento_pk = isset($data['tipo_lancamento_pk'])? $data['tipo_lancamento_pk'] : "";
            $tipos_operacao_pk = isset($data['tipos_operacao_pk'])? $data['tipos_operacao_pk'] : "";
            $contas_bancarias_pk = isset($data['contas_bancarias_pk'])? $data['contas_bancarias_pk'] : "";
            $dt_cadastro = isset($data['dt_cadastro'])? $data['dt_cadastro'] : "";
            $dt_faturamento = isset($data['dt_faturamento'])? $data['dt_faturamento'] : "";
            $dt_vencimento = isset($data['dt_vencimento'])? $data['dt_vencimento'] : "";
            $dt_pagamento = isset($data['dt_pagamento'])? $data['dt_pagamento'] : "";
           


            if($dt_cadastro!=" - "){
                $auxC = explode('-', $dt_cadastro);
                $dt_cadastro_ini = trim($auxC[0]);
                $dt_cadastro_fim = trim($auxC[1]);
            }
            else{
                $dt_cadastro_ini ="";
                $dt_cadastro_fim = "";
            }


            if($dt_faturamento!=" - "){
                $auxF = explode('-', $dt_faturamento);
                $dt_faturamento_ini = trim($auxF[0]);
                $dt_faturamento_fim = trim($auxF[1]);

            }
            else{
                $dt_faturamento_ini ="";
                $dt_faturamento_fim = "";
            }

            if($dt_vencimento!=" - "){
                $auxV = explode('-', $dt_vencimento);
                $dt_vencimento_ini = trim($auxV[0]);
                $dt_vencimento_fim = trim($auxV[1]);

            }
            else{
                $dt_vencimento_ini ="";
                $dt_vencimento_fim = "";
            }

            if($dt_pagamento!=" - "){
                $auxP = explode('-', $dt_pagamento);
                $dt_pagamento_ini = trim($auxP[0]);
                $dt_pagamento_fim = trim($auxP[1]);
            }
            else{
                $dt_pagamento_ini ="";
                $dt_pagamento_fim = "";
            }
            
            
            
           
         


            $retorno = (new Lancamento($this->pdo))->listarLancamento($pk,$ic_status_lancamento, $ds_lancamento, $usuario_cadastro_pk,$empresas_pk,$tipo_grupo_lancamento_pk,$grupo_lancamento_pk,$dt_cadastro_ini,$dt_cadastro_fim,$dt_faturamento_ini,$dt_faturamento_fim,$dt_vencimento_ini,$dt_vencimento_fim,$dt_pagamento_ini,$dt_pagamento_fim,$ds_num_documento,$ic_tipo_num_documento, $categorias_financeiras_pk, $tipo_lancamento_pk, $tipos_operacao_pk, $contas_bancarias_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarDataTableReceitaDespesaConciliacao(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $contas_bancarias_pk = isset($data['contas_bancarias_pk'])? $data['contas_bancarias_pk'] : "";
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk'] : "";
            $dt_periodo_ini = isset($data['dt_periodo_ini'])? $data['dt_periodo_ini'] : "";
            $dt_periodo_fim = isset($data['dt_periodo_fim'])? $data['dt_periodo_fim'] : "";

           (new Lancamento($this->pdo))->listarExtratoConciliacao($empresas_pk,$contas_bancarias_pk,$dt_periodo_ini,$dt_periodo_fim);

        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function RelatorioLancamento(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $tipo_lancamento_pk = isset($data['tipo_lancamento_pk'])? $data['tipo_lancamento_pk']: "";
            $dt_vencimento_ini = isset($data['dt_vencimento_ini'])? $data['dt_vencimento_ini']: "";
            $dt_vencimento_fim = isset($data['dt_vencimento_fim'])? $data['dt_vencimento_fim']: "";
            $ic_status_pagamento = isset($data['ic_status_pagamento'])? $data['ic_status_pagamento']: "";
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk']: "";
            $tipo_grupo_pk = isset($data['tipo_grupo_pk'])? $data['tipo_grupo_pk']: "";
            $grupo_leancamento_pk = isset($data['grupo_leancamento_pk'])? $data['grupo_leancamento_pk']: "";
            $usuario_cadastro_pk = isset($data['usuario_cadastro_pk'])? $data['usuario_cadastro_pk']: "";
            $dt_lancamento_ini = isset($data['dt_lancamento_ini'])? $data['dt_lancamento_ini']: "";
            $dt_lancamento_fim = isset($data['dt_lancamento_fim'])? $data['dt_lancamento_fim']: ""; 
            $dt_pagamento_ini = isset($data['dt_pagamento_ini'])? $data['dt_pagamento_ini']: "";
            $dt_pagamento_fim = isset($data['dt_pagamento_fim'])? $data['dt_pagamento_fim']: ""; 
            $plano_contas = isset($data['plano_contas'])? $data['plano_contas']: "";
            $dt_faturamento_ini = isset($data['dt_faturamento_ini'])? $data['dt_faturamento_ini']: "";
            $dt_faturamento_fim = isset($data['dt_faturamento_fim'])? $data['dt_faturamento_fim']: "";
            $contas_bancarias_pk = isset($data['contas_bancarias_pk'])? $data['contas_bancarias_pk']: "";
            
            $tipos_operacao_pk_receita = isset($data['tipos_operacao_pk_receita'])? $data['tipos_operacao_pk_receita']: "";
            $retorno = (new Lancamento($this->pdo))->RelatorioLancamento($tipo_lancamento_pk,$dt_vencimento_ini,$dt_vencimento_fim,$ic_status_pagamento,$empresas_pk,$tipo_grupo_pk,$grupo_leancamento_pk,$usuario_cadastro_pk,$dt_lancamento_ini,$dt_lancamento_fim,$dt_pagamento_ini,$dt_pagamento_fim,$plano_contas,$dt_faturamento_ini,$dt_faturamento_fim,$tipos_operacao_pk_receita, $contas_bancarias_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function relReceitaPostoTrabalho(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $leads_clientes_pk = isset($data['leads_clientes_pk'])? $data['leads_clientes_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $contratos_pk_combo = isset($data['contratos_pk_combo'])? $data['contratos_pk_combo']: "";

            $retorno = (new Lancamento($this->pdo))->relReceitaPostoTrabalho($leads_pk,$leads_clientes_pk,$contratos_pk_combo);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function relDespesaPostoTrabalho(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $leads_clientes_pk = isset($data['leads_clientes_pk'])? $data['leads_clientes_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $contratos_pk_combo = isset($data['contratos_pk_combo'])? $data['contratos_pk_combo']: "";

            $retorno = (new Lancamento($this->pdo))->relDespesaPostoTrabalho($leads_pk,$leads_clientes_pk,$contratos_pk_combo);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function relLancamentoPlanoConta(Request $request, Response $response, $args) {

        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio

            $data = $request->getQueryParams();
            $dt_vencimento_ini = isset($data['dt_vencimento_ini'])? $data['dt_vencimento_ini']: "";
            $dt_vencimento_fim = isset($data['dt_vencimento_fim'])? $data['dt_vencimento_fim']: "";
            $tipos_operacao_pk_receita = isset($data['tipos_operacao_pk_receita'])? $data['tipos_operacao_pk_receita']: "";

            $query = (new Lancamento($this->pdo))->relLancamentoPlanoConta($dt_vencimento_ini,$dt_vencimento_fim,$tipos_operacao_pk_receita);
            json::run($query->status,$query->data,$query->message);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function cargaTabelaLancamentos(Request $request, Response $response, $args) {

        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio
            
            $query = (new Lancamento($this->pdo))->cargaTabelaLancamentos();
            json::run($query->status,$query->data,$query->message);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

}