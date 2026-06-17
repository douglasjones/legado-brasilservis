<?php

namespace App\Controller;

use App\Model\AgendaColaboradorApontamento;
use App\Model\Colaborador;
use App\Model\Lancamento;
use App\Model\Log;
use App\Model\Ponto;
use App\Model\PontoFolha;
use App\Model\RelatorioComercial;
use App\Model\Supervisor;
use App\Utils\Json;
use App\Utils\Session;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Utils\SlimImage;
use App\Utils\SlimStatus;
use Throwable;

final class RelatorioController extends BaseController {


    public function comercial(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/comercial/menu_relatorio_comercial.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function compra_estoque(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/compras_estoque/menu_relatorio_compra_estoque.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function rh(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/rh/menu_relatorio_rh.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function financeiro(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/financeiro/menu_relatorio_financeiro.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    //OPERACIONAL
    public function operacional(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/operacional/menu_relatorio_operacional.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function pesqAcompanhamentoPontoSintetico(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/operacional/rel_acompanhamento_ponto_sintetico_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function resAcompanhamentoPontoSintetico(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $ic_cliente = isset($data['ic_cliente'])? $data['ic_cliente'] : "";
            $ds_cliente = isset($data['ds_cliente'])? $data['ds_cliente'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $ds_colaborador = isset($data['ds_colaborador'])? $data['ds_colaborador'] : "";
            $ds_periodo = isset($data['ds_periodo'])? $data['ds_periodo'] : "";

            $aux = explode('-', $ds_periodo);
            $dt_periodo_ini = trim($aux[0]);
            $dt_periodo_fim = trim($aux[1])  ;
            $this->view->render($response, 'relatorio/operacional/rel_acompanhamento_ponto_sintetico_res.twig',
                array(
                "ic_cliente"=>$ic_cliente,
                "ds_cliente"=>$ds_cliente,
                "leads_pk"=>$leads_pk,
                "ds_lead"=>$ds_lead,
                "colaborador_pk"=>$colaborador_pk,
                "ds_colaborador"=>$ds_colaborador,
                "ds_periodo"=>$ds_periodo,
                "dt_periodo_ini"=>$dt_periodo_ini,
                "dt_periodo_fim"=>$dt_periodo_fim
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function pesqAcompanhamentoPontoAnalitico(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/operacional/rel_acompanhamento_ponto_analitico_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function resAcompanhamentoPontoAnalitico(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $ic_cliente = isset($data['ic_cliente'])? $data['ic_cliente'] : "";
            $ds_cliente = isset($data['ds_cliente'])? $data['ds_cliente'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $ds_colaborador = isset($data['ds_colaborador'])? $data['ds_colaborador'] : "";
            $ds_periodo = isset($data['ds_periodo'])? $data['ds_periodo'] : "";

            $aux = explode('-', $ds_periodo);
            $dt_periodo_ini = trim($aux[0]);
            $dt_periodo_fim = trim($aux[1])  ;
            $this->view->render($response, 'relatorio/operacional/rel_acompanhamento_ponto_analitico_res.twig',
                array(
                "ic_cliente"=>$ic_cliente,
                "ds_cliente"=>$ds_cliente,
                "leads_pk"=>$leads_pk,
                "ds_lead"=>$ds_lead,
                "colaborador_pk"=>$colaborador_pk,
                "ds_colaborador"=>$ds_colaborador,
                "ds_periodo"=>$ds_periodo,
                "dt_periodo_ini"=>$dt_periodo_ini,
                "dt_periodo_fim"=>$dt_periodo_fim
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function resColaboradorApontamento(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $ds_colaborador = isset($data['ds_colaborador'])? $data['ds_colaborador']: "";
            $ds_tipo_apontamento = isset($data['ds_tipo_apontamento'])? $data['ds_tipo_apontamento']: "";
            $tipo_apontamento_pk = isset($data['tipo_apontamento_pk'])? $data['tipo_apontamento_pk']: "";
            $dt_apontamento_ini = isset($data['dt_apontamento_ini'])? $data['dt_apontamento_ini']: "";
            $dt_apontamento_fim = isset($data['dt_apontamento_fim'])? $data['dt_apontamento_fim']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead']: "";

            $this->view->render($response, 'relatorio/operacional/rel_colaborador_apontamento_res.twig',array(
                "colaborador_pk"=>$colaborador_pk,
                "ds_colaborador" =>$ds_colaborador,
                "ds_tipo_apontamento" =>$ds_tipo_apontamento,
                "tipo_apontamento_pk" =>$tipo_apontamento_pk,
                "dt_apontamento_ini" =>$dt_apontamento_ini,
                "dt_apontamento_fim" =>$dt_apontamento_fim,
                "leads_pk" =>$leads_pk,
                "ds_lead" =>$ds_lead
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqColaboradorApontamento(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/operacional/rel_colaborador_apontamento_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqColaboradorPostoTrabalho(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/operacional/rel_colaborador_posto_trabalho_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoColaboradorPostoTrabalho(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $colaboradores_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $dt_ini = isset($data['dt_ini'])? $data['dt_ini']: "";
            $dt_fim = isset($data['dt_fim'])? $data['dt_fim']: "";
            $ds_colaborador = isset($data['ds_colaborador'])? $data['ds_colaborador']: "";
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead']: "";
            
            $this->view->render($response, 'relatorio/operacional/rel_colaborador_posto_trabalho_res.twig',array(
                "colaboradores_pk"=>$colaboradores_pk,
                "leads_pk" =>$leads_pk,
                "dt_ini" =>$dt_ini,
                "dt_fim" =>$dt_fim,
                "ds_colaborador" =>$ds_colaborador,
                "ds_lead" =>$ds_lead
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqControleFts(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/operacional/rel_controle_ft_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoControleFt(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $dt_ini = isset($data['dt_ini'])? $data['dt_ini']: "";
            $dt_fim = isset($data['dt_fim'])? $data['dt_fim']: "";
            $ds_colaborador = isset($data['ds_colaborador'])? $data['ds_colaborador']: "";
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead']: "";
            
            $this->view->render($response, 'relatorio/operacional/rel_controle_ft_res.twig',array(
                "colaboradores_pk"=>$colaborador_pk,
                "leads_pk" =>$leads_pk,
                "dt_ini" =>$dt_ini,
                "dt_fim" =>$dt_fim,
                "ds_colaborador" =>$ds_colaborador,
                "ds_lead" =>$ds_lead
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqRelEstoqueSintetico(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/compras_estoque/rel_estoque_sintetico_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoEstoqueSintetico(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $categorias_pk = isset($data['categorias_pk'])? $data['categorias_pk']: "";
            $produtos_pk = isset($data['produtos_pk'])? $data['produtos_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead']: "";
            $ds_produto = isset($data['ds_produto'])? $data['ds_produto']: "";
            $ds_categoria = isset($data['ds_categoria'])? $data['ds_categoria']: "";
            
            $this->view->render($response, 'relatorio/compras_estoque/rel_estoque_sintetico_res.twig',array(
                "categorias_pk"=>$categorias_pk,
                "produtos_pk" =>$produtos_pk,
                "leads_pk" =>$leads_pk,
                "ds_lead" =>$ds_lead,
                "ds_produto" =>$ds_produto,
                "ds_categoria" =>$ds_categoria
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqRelMovimentacaoEstoque(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/compras_estoque/rel_movimentacao_estoque_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoMovimentacaoEstoque(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $categorias_pk = isset($data['categorias_pk'])? $data['categorias_pk']: "";
            $colaboradores_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk']: "";
            $produtos_pk = isset($data['produtos_pk'])? $data['produtos_pk']: "";
            $dt_troca_ini = isset($data['dt_troca_ini'])? $data['dt_troca_ini']: "";
            $dt_troca_fim = isset($data['dt_troca_fim'])? $data['dt_troca_fim']: "";
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead']: "";
            $ds_colaborador = isset($data['ds_colaborador'])? $data['ds_colaborador']: "";
            $ds_produto = isset($data['ds_produto'])? $data['ds_produto']: "";
            $ds_categoria = isset($data['ds_categoria'])? $data['ds_categoria']: "";
            
            $this->view->render($response, 'relatorio/compras_estoque/rel_movimentacao_estoque_res.twig',array(
                "leads_pk"=>$leads_pk,
                "categorias_pk" =>$categorias_pk,
                "colaboradores_pk" =>$colaboradores_pk,
                "produtos_pk" =>$produtos_pk,
                "dt_troca_ini" =>$dt_troca_ini,
                "dt_troca_fim" =>$dt_troca_fim,
                "ds_lead" =>$ds_lead,
                "ds_colaborador" =>$ds_colaborador,
                "ds_produto" =>$ds_produto,
                "ds_categoria" =>$ds_categoria
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqRelDadosColaborador(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/rh/rel_dados_colaborador_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoDadosColaborador(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $ic_status = isset($data['ic_status'])? $data['ic_status']: "";
            
            $this->view->render($response, 'relatorio/rh/rel_dados_colaborador_res.twig',array(
                "colaborador_pk" =>$colaborador_pk,
                "ic_status" =>$ic_status
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqRelColaboradorExameCurso(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/rh/rel_colaborador_exame_curso_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoColaboradorExameCurso(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $cursos_pk = isset($data['cursos_pk'])? $data['cursos_pk']: "";
            $colaboradores_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk']: "";
            $dt_execucao_ini = isset($data['dt_execucao_ini'])? $data['dt_execucao_ini']: "";
            $dt_execucao_fim = isset($data['dt_execucao_fim'])? $data['dt_execucao_fim']: "";
            $dt_validacao_ini = isset($data['dt_validacao_ini'])? $data['dt_validacao_ini']: "";
            $dt_validacao_fim = isset($data['dt_validacao_fim'])? $data['dt_validacao_fim']: "";
            
            $this->view->render($response, 'relatorio/rh/rel_colaborador_exame_curso_res.twig',array(
                "cursos_pk" =>$cursos_pk,
                "colaboradores_pk" =>$colaboradores_pk,
                "dt_execucao_ini" =>$dt_execucao_ini,
                "dt_execucao_fim" =>$dt_execucao_fim,
                "dt_validacao_ini" =>$dt_validacao_ini,
                "dt_validacao_fim" =>$dt_validacao_fim
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqRelFluxoCaixa(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/financeiro/rel_fluxo_caixa_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoFluxoCaixa(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $tipo_lancamento_pk = isset($data['tipo_lancamento_pk'])? $data['tipo_lancamento_pk']: "";
            $ic_status_pagamento = isset($data['ic_status_pagamento'])? $data['ic_status_pagamento']: "";
            $ds_tipo_grupo = isset($data['ds_tipo_grupo'])? $data['ds_tipo_grupo']: "";
            $dt_vencimento_ini = isset($data['dt_vencimento_ini'])? $data['dt_vencimento_ini']: "";
            $dt_vencimento_fim = isset($data['dt_vencimento_fim'])? $data['dt_vencimento_fim']: "";
            $dt_pagamento_ini = isset($data['dt_pagamento_ini'])? $data['dt_pagamento_ini']: "";
            $dt_pagamento_fim = isset($data['dt_pagamento_fim'])? $data['dt_pagamento_fim']: "";
            $tipos_operacao_pk_receita = isset($data['tipos_operacao_pk_receita'])? $data['tipos_operacao_pk_receita']: "";
            $contas_bancarias_pk = isset($data['contas_bancarias_pk'])? $data['contas_bancarias_pk']: "";
            $ic_status = isset($data['ic_status'])? $data['ic_status']: "";
            $ds_ic_status = isset($data['ds_ic_status'])? $data['ds_ic_status']: "";
            $ds_usuario_cadastro = isset($data['ds_usuario_cadastro'])? $data['ds_usuario_cadastro']: "";
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk']: "";
            $ds_empresa= isset($data['ds_empresa'])? $data['ds_empresa']: "";
            $ds_grupo_leancamento= isset($data['ds_grupo_leancamento'])? $data['ds_grupo_leancamento']: "";
            $tipo_grupo_pk = isset($data['tipo_grupo_pk'])? $data['tipo_grupo_pk']: "";
            $grupo_leancamento_pk = isset($data['grupo_leancamento_pk'])? $data['grupo_leancamento_pk']: "";
            $usuario_cadastro_pk = isset($data['usuario_cadastro_pk'])? $data['usuario_cadastro_pk']: "";
            $dt_faturamento_ini = isset($data['dt_faturamento_ini'])? $data['dt_faturamento_ini']: "";
            $dt_faturamento_fim = isset($data['dt_faturamento_fim'])? $data['dt_faturamento_fim']: "";
            $plano_contas = isset($data['plano_contas'])? $data['plano_contas']: "";
            $contas_bancarias_pagamento_pk = isset($data['contas_bancarias_pagamento_pk'])? $data['contas_bancarias_pagamento_pk']: "";
            if($tipo_lancamento_pk==0){
                $ds_tipo_lancamento = "Receita e Despesa";
            }
            else if($tipo_lancamento_pk==1){
                $ds_tipo_lancamento = "Receita";
            }
            if($tipo_lancamento_pk==2){
                $ds_tipo_lancamento = "Despesa";
            }




            $retorno = (new Lancamento($this->pdo))->RelatorioLancamento(
                $tipo_lancamento_pk,
                $dt_vencimento_ini,
                $dt_vencimento_fim,
                $ic_status,
                $empresas_pk,
                $tipo_grupo_pk,
                $grupo_leancamento_pk,
                $usuario_cadastro_pk,
                $dt_pagamento_ini,
                $dt_pagamento_fim,
                $dt_pagamento_ini,
                $dt_pagamento_fim,
                $plano_contas,
                $dt_faturamento_ini,
                $dt_faturamento_fim,
                $tipos_operacao_pk_receita,
                 $contas_bancarias_pk,
                $contas_bancarias_pagamento_pk);

                 
            
            $this->view->render($response, 'relatorio/financeiro/rel_fluxo_caixa_res.twig',array(
                "tipo_lancamento_pk" =>$tipo_lancamento_pk,
                "ds_tipo_lancamento" =>$ds_tipo_lancamento,
                "ic_status_pagamento" =>$ic_status_pagamento,
                "dt_vencimento_ini" =>$dt_vencimento_ini,
                "dt_vencimento_fim" =>$dt_vencimento_fim,
                "dt_pagamento_ini" =>$dt_pagamento_ini,
                "dt_pagamento_fim" =>$dt_pagamento_fim,
                "tipos_operacao_pk_receita" =>$tipos_operacao_pk_receita,
                "contas_bancarias_pk" =>$contas_bancarias_pk,
                "ic_status" =>$ic_status,
                "ds_ic_status" =>$ds_ic_status,
                "empresas_pk" =>$empresas_pk,
                "ds_empresa" =>$ds_empresa,
                "ds_grupo_leancamento" =>$ds_grupo_leancamento,
                "tipo_grupo_pk" =>$tipo_grupo_pk,
                "ds_tipo_grupo" =>$ds_tipo_grupo,
                "grupo_leancamento_pk" =>$grupo_leancamento_pk,
                "usuario_cadastro_pk" =>$usuario_cadastro_pk,
                "ds_usuario_cadastro" =>$ds_usuario_cadastro,
                "dt_faturamento_ini" =>$dt_faturamento_ini,
                "dt_faturamento_fim" =>$dt_faturamento_fim,
                "arrDadosRelatorio"=>$retorno->data
            ));
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqRelProposta(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/comercial/rel_propostas_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoProposta(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $leads_clientes_pk = isset($data['leads_clientes_pk'])? $data['leads_clientes_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $ds_cpf_cnpj = isset($data['ds_cpf_cnpj'])? $data['ds_cpf_cnpj']: "";
            $usuario_cadastro_pk = isset($data['usuario_cadastro_pk'])? $data['usuario_cadastro_pk']: "";
            $dt_ini = isset($data['dt_ini'])? $data['dt_ini']: "";
            $dt_fim = isset($data['dt_fim'])? $data['dt_fim']: "";
            $ic_status = isset($data['ic_status'])? $data['ic_status']: "";
            
            $this->view->render($response, 'relatorio/comercial/rel_propostas_res.twig',array(
                "leads_clientes_pk" =>$leads_clientes_pk,
                "leads_pk" =>$leads_pk,
                "ds_cpf_cnpj" =>$ds_cpf_cnpj,
                "usuario_cadastro_pk" =>$usuario_cadastro_pk,
                "dt_ini" =>$dt_ini,
                "dt_fim" =>$dt_fim,
                "ic_status" =>$ic_status
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqRelAcompanhamentoFerias(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/rh/rel_acompanhamento_ferias_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoAcompanhamentoFerias(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $colaboradores_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk']: "";
            $ds_colaboradores = isset($data['ds_colaboradores'])? $data['ds_colaboradores']: "";
            $dt_ini_ferias = isset($data['dt_ini_ferias'])? $data['dt_ini_ferias']: "";
            $dt_fim_ferias = isset($data['dt_fim_ferias'])? $data['dt_fim_ferias']: "";
            
            $this->view->render($response, 'relatorio/rh/rel_acompanhamento_ferias_res.twig',array(
                "colaboradores_pk" =>$colaboradores_pk,
                "ds_colaboradores"=>$ds_colaboradores,
                "dt_ini_ferias" =>$dt_ini_ferias,
                "dt_fim_ferias" =>$dt_fim_ferias
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqRelContrato(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/comercial/rel_contrato_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoContrato(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $empresa_pk = isset($data['empresa_pk'])? $data['empresa_pk']: "";
            $ds_cpf_cnpj = isset($data['ds_cpf_cnpj'])? $data['ds_cpf_cnpj']: "";
            $leads_clientes_pk = isset($data['leads_clientes_pk'])? $data['leads_clientes_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $dt_ini_cadastro = isset($data['dt_ini_cadastro'])? $data['dt_ini_cadastro']: "";
            $dt_fim_cadastro = isset($data['dt_fim_cadastro'])? $data['dt_fim_cadastro']: "";
            $dt_ini_contrato = isset($data['dt_ini_contrato'])? $data['dt_ini_contrato']: "";
            $dt_fim_contrato = isset($data['dt_fim_contrato'])? $data['dt_fim_contrato']: "";
            $usuario_cadastro_pk = isset($data['usuario_cadastro_pk'])? $data['usuario_cadastro_pk']: "";
            $ic_status = isset($data['ic_status'])? $data['ic_status']: "";
            $tp_contrato = isset($data['tp_contrato'])? $data['tp_contrato']: "";
            
            $this->view->render($response, 'relatorio/comercial/rel_contrato_res.twig',array(
                "empresa_pk" =>$empresa_pk,
                "ds_cpf_cnpj" =>$ds_cpf_cnpj,
                "leads_clientes_pk" =>$leads_clientes_pk,
                "leads_pk" =>$leads_pk,
                "dt_ini_cadastro" =>$dt_ini_cadastro,
                "dt_fim_cadastro" =>$dt_fim_cadastro,
                "dt_ini_contrato" =>$dt_ini_contrato,
                "dt_fim_contrato" =>$dt_fim_contrato,
                "usuario_cadastro_pk" =>$usuario_cadastro_pk,
                "ic_status" =>$ic_status,
                "tp_contrato" =>$tp_contrato
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqControleCompra(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/compras_estoque/rel_controle_compra_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoControleCompra(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $ds_empresa = isset($data['ds_empresa'])? $data['ds_empresa']: "";
            $empresa_pk = isset($data['empresa_pk'])? $data['empresa_pk']: "";
            $fornecedor_pk = isset($data['fornecedor_pk'])? $data['fornecedor_pk']: "";
            $ds_fornecedor = isset($data['ds_fornecedor'])? $data['ds_fornecedor']: "";
            $categoria_pk = isset($data['categoria_pk'])? $data['categoria_pk']: "";
            $ds_categoria = isset($data['ds_categoria'])? $data['ds_categoria']: "";
            $tipo_grupo_centro_custo_pk = isset($data['tipo_grupo_centro_custo_pk'])? $data['tipo_grupo_centro_custo_pk']: "";
            $ds_grupo = isset($data['ds_grupo'])? $data['ds_grupo']: "";
            $grupo_lancamento_centro_custo_pk = isset($data['grupo_lancamento_centro_custo_pk'])? $data['grupo_lancamento_centro_custo_pk']: "";
            $ds_centro_custo = isset($data['ds_centro_custo'])? $data['ds_centro_custo']: "";
            $dt_ini_cad = isset($data['dt_ini_cad'])? $data['dt_ini_cad']: "";
            $dt_fim_cad = isset($data['dt_fim_cad'])? $data['dt_fim_cad']: "";
            $dt_ini_compra = isset($data['dt_ini_compra'])? $data['dt_ini_compra']: "";
            $dt_fim_compra = isset($data['dt_fim_compra'])? $data['dt_fim_compra']: "";
            $ic_status = isset($data['ic_status'])? $data['ic_status']: "";
            $ds_status = isset($data['ds_status'])? $data['ds_status']: "";
            
            $this->view->render($response, 'relatorio/compras_estoque/rel_controle_compra_res.twig',array(
                "ds_empresa" =>$ds_empresa,
                "empresa_pk" =>$empresa_pk,
                "fornecedor_pk" =>$fornecedor_pk,
                "ds_fornecedor" =>$ds_fornecedor,
                "categoria_pk" =>$categoria_pk,
                "ds_categoria" =>$ds_categoria,
                "tipo_grupo_centro_custo_pk" =>$tipo_grupo_centro_custo_pk,
                "ds_grupo" =>$ds_grupo,
                "grupo_lancamento_centro_custo_pk" =>$grupo_lancamento_centro_custo_pk,
                "ds_centro_custo" =>$ds_centro_custo,
                "dt_ini_cad" =>$dt_ini_cad,
                "dt_fim_cad" =>$dt_fim_cad,
                "dt_ini_compra" =>$dt_ini_compra,
                "dt_fim_compra" =>$dt_fim_compra,
                "ic_status" =>$ic_status,
                "ds_status" =>$ds_status
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqControleSolicitacaoCompra(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/compras_estoque/rel_controle_solicitacao_compra_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoControleSolicitacaoCompra(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $ds_empresa = isset($data['ds_empresa'])? $data['ds_empresa']: "";
            $empresa_pk = isset($data['empresa_pk'])? $data['empresa_pk']: "";
            $solicitante_pk = isset($data['solicitante_pk'])? $data['solicitante_pk']: "";
            $ds_solicitante = isset($data['ds_solicitante'])? $data['ds_solicitante']: "";
            $usuario_aprovacao_pk = isset($data['usuario_aprovacao_pk'])? $data['usuario_aprovacao_pk']: "";
            $ds_usuario_aprovacao = isset($data['ds_usuario_aprovacao'])? $data['ds_usuario_aprovacao']: "";
            $tipo_grupo_centro_custo_pk = isset($data['tipo_grupo_centro_custo_pk'])? $data['tipo_grupo_centro_custo_pk']: "";
            $ds_grupo = isset($data['ds_grupo'])? $data['ds_grupo']: "";
            $grupo_lancamento_centro_custo_pk = isset($data['grupo_lancamento_centro_custo_pk'])? $data['grupo_lancamento_centro_custo_pk']: "";
            $ds_centro_custo = isset($data['ds_centro_custo'])? $data['ds_centro_custo']: "";
            $dt_ini_cad = isset($data['dt_ini_cad'])? $data['dt_ini_cad']: "";
            $dt_fim_cad = isset($data['dt_fim_cad'])? $data['dt_fim_cad']: "";
            $dt_ini_aprov = isset($data['dt_ini_aprov'])? $data['dt_ini_aprov']: "";
            $dt_fim_aprov = isset($data['dt_fim_aprov'])? $data['dt_fim_aprov']: "";
            $ic_status = isset($data['ic_status'])? $data['ic_status']: "";
            $ds_status = isset($data['ds_status'])? $data['ds_status']: "";
            
            $this->view->render($response, 'relatorio/compras_estoque/rel_controle_solicitacao_compra_res.twig',array(
                "ds_empresa" =>$ds_empresa,
                "empresa_pk" =>$empresa_pk,
                "solicitante_pk" =>$solicitante_pk,
                "ds_solicitante" =>$ds_solicitante,
                "usuario_aprovacao_pk" =>$usuario_aprovacao_pk,
                "ds_usuario_aprovacao" =>$ds_usuario_aprovacao,
                "tipo_grupo_centro_custo_pk" =>$tipo_grupo_centro_custo_pk,
                "ds_grupo" =>$ds_grupo,
                "grupo_lancamento_centro_custo_pk" =>$grupo_lancamento_centro_custo_pk,
                "ds_centro_custo" =>$ds_centro_custo,
                "dt_ini_cad" =>$dt_ini_cad,
                "dt_fim_cad" =>$dt_fim_cad,
                "dt_ini_aprov" =>$dt_ini_aprov,
                "dt_fim_aprov" =>$dt_fim_aprov,
                "ic_status" =>$ic_status,
                "ds_status" =>$ds_status
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqRondas(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/operacional/rel_rondas_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoRondas(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $leads_clientes_pk = isset($data['leads_clientes_pk'])? $data['leads_clientes_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $dt_ini_ronda = isset($data['dt_ini_ronda'])? $data['dt_ini_ronda']: "";
            $dt_fim_ronda = isset($data['dt_fim_ronda'])? $data['dt_fim_ronda']: "";
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead']: "";
            $ds_lead_clientes = isset($data['ds_lead_clientes'])? $data['ds_lead_clientes']: "";
            
            $this->view->render($response, 'relatorio/operacional/rel_rondas_res.twig',array(
                "leads_clientes_pk" =>$leads_clientes_pk,
                "leads_pk" =>$leads_pk,
                "dt_ini_ronda" =>$dt_ini_ronda,
                "dt_fim_ronda" =>$dt_fim_ronda,
                "ds_lead" =>$ds_lead,
                "ds_lead_clientes" =>$ds_lead_clientes
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function receptivoRondasCliente(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $leads_clientes_pk = isset($data['leads_clientes_pk'])? $data['leads_clientes_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $dt_ini_ronda = isset($data['dt_ini_ronda'])? $data['dt_ini_ronda']: "";
            $dt_fim_ronda = isset($data['dt_fim_ronda'])? $data['dt_fim_ronda']: "";
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead']: "";
            $ds_lead_clientes = isset($data['ds_lead_clientes'])? $data['ds_lead_clientes']: "";

            $this->view->render($response, 'partials/cliente/rel_rondas_res.twig',array(
                "leads_clientes_pk" =>$leads_clientes_pk,
                "leads_pk" =>$leads_pk,
                "dt_ini_ronda" =>$dt_ini_ronda,
                "dt_fim_ronda" =>$dt_fim_ronda,
                "ds_lead" =>$ds_lead,
                "ds_lead_clientes" =>$ds_lead_clientes
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqRelReceitaPostoTrabalho(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/financeiro/rel_receita_posto_trabalho_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoReceitaPostoTrabalho(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $leads_clientes_pk = isset($data['leads_clientes_pk'])? $data['leads_clientes_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $contratos_pk_combo = isset($data['contratos_pk_combo'])? $data['contratos_pk_combo']: "";
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead']: "";
            $ds_lead_clientes = isset($data['ds_lead_clientes'])? $data['ds_lead_clientes']: "";
            $ds_contratos = isset($data['ds_contratos'])? $data['ds_contratos']: "";
            
            $this->view->render($response, 'relatorio/financeiro/rel_receita_posto_trabalho_res.twig',array(
                "leads_clientes_pk" =>$leads_clientes_pk,
                "leads_pk" =>$leads_pk,
                "contratos_pk_combo" =>$contratos_pk_combo,
                "ds_lead" =>$ds_lead,
                "ds_lead_clientes" =>$ds_lead_clientes,
                "ds_contratos" =>$ds_contratos
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqRelDespesaPostoTrabalho(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/financeiro/rel_despesa_posto_trabalho_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoDespesaPostoTrabalho(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $leads_clientes_pk = isset($data['leads_clientes_pk'])? $data['leads_clientes_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $contratos_pk_combo = isset($data['contratos_pk_combo'])? $data['contratos_pk_combo']: "";
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead']: "";
            $ds_lead_clientes = isset($data['ds_lead_clientes'])? $data['ds_lead_clientes']: "";
            $ds_contratos = isset($data['ds_contratos'])? $data['ds_contratos']: "";
            
            $this->view->render($response, 'relatorio/financeiro/rel_despesa_posto_trabalho_res.twig',array(
                "leads_clientes_pk" =>$leads_clientes_pk,
                "leads_pk" =>$leads_pk,
                "contratos_pk_combo" =>$contratos_pk_combo,
                "ds_lead" =>$ds_lead,
                "ds_lead_clientes" =>$ds_lead_clientes,
                "ds_contratos" =>$ds_contratos
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqRelTituloPlanoContas(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/financeiro/rel_titulo_plano_contas_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoPlanoContas(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $dt_vencimento_ini = isset($data['dt_vencimento_ini'])? $data['dt_vencimento_ini']: "";
            $dt_vencimento_fim = isset($data['dt_vencimento_fim'])? $data['dt_vencimento_fim']: "";
            $dt_pagamento_ini = isset($data['dt_pagamento_ini'])? $data['dt_pagamento_ini']: "";
            $dt_pagamento_fim = isset($data['dt_pagamento_fim'])? $data['dt_pagamento_fim']: "";
            $ds_empresa = isset($data['ds_empresa'])? $data['ds_empresa']: "";
            $contas_bancarias_pk = isset($data['contas_bancarias_pk'])? $data['contas_bancarias_pk']: "";
            $ds_tipo_grupo = isset($data['ds_tipo_grupo'])? $data['ds_tipo_grupo']: "";
            $ds_grupo_leancamento = isset($data['ds_grupo_leancamento'])? $data['ds_grupo_leancamento']: "";
            $ds_ic_status = isset($data['ds_ic_status'])? $data['ds_ic_status']: "";
            $ds_usuario_cadastro = isset($data['ds_usuario_cadastro'])? $data['ds_usuario_cadastro']: "";
            $ic_status = isset($data['ic_status'])? $data['ic_status']: "";
            $tipos_operacao_pk_receita = isset($data['tipos_operacao_pk_receita'])? $data['tipos_operacao_pk_receita']: "";
            $dt_faturamento_ini = isset($data['dt_faturamento_ini'])? $data['dt_faturamento_ini']: "";
            $dt_faturamento_fim = isset($data['dt_faturamento_fim'])? $data['dt_faturamento_fim']: "";
            
            $this->view->render($response, 'relatorio/financeiro/rel_titulo_plano_contas_res.twig',array(
                "dt_vencimento_ini" =>$dt_vencimento_ini,
                "dt_vencimento_fim" =>$dt_vencimento_fim,
                "dt_pagamento_ini" =>$dt_pagamento_ini,
                "dt_pagamento_fim" =>$dt_pagamento_fim,
                "ds_empresa" =>$ds_empresa,
                "contas_bancarias_pk" =>$contas_bancarias_pk,
                "ds_tipo_grupo" =>$ds_tipo_grupo,
                "ds_grupo_leancamento" =>$ds_grupo_leancamento,
                "ds_ic_status" =>$ds_ic_status,
                "ds_usuario_cadastro" =>$ds_usuario_cadastro,
                "ic_status" =>$ic_status,
                "tipos_operacao_pk_receita" =>$tipos_operacao_pk_receita,
                "dt_faturamento_ini" =>$dt_faturamento_ini,
                "dt_faturamento_fim" =>$dt_faturamento_fim
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqCompraMovimentacaoLead(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/compras_estoque/rel_compra_movimentacao_lead_pesq.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function pesqRelContasPagarPeriodo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/financeiro/pesqRelContasPagarPeriodo.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function receptivoPlanoContasPagarPeriodo(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

         
            $dt_vencimento_ini = isset($data['dt_vencimento_ini'])? $data['dt_vencimento_ini']: "";
            $dt_vencimento_fim = isset($data['dt_vencimento_fim'])? $data['dt_vencimento_fim']: "";
            $tipo_lancamento_pk = isset($data['tipo_lancamento_pk'])? $data['tipo_lancamento_pk']: "";
            $contas_bancarias_pk = isset($data['contas_bancarias_pk'])? $data['contas_bancarias_pk']: "";
            $tipos_operacao_pk_receita = isset($data['tipos_operacao_pk_receita'])? $data['tipos_operacao_pk_receita']: "";
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk']: "";
            $tipo_grupo_pk = isset($data['tipo_grupo_pk'])? $data['tipo_grupo_pk']: "";
            $grupo_leancamento_pk = isset($data['grupo_leancamento_pk'])? $data['grupo_leancamento_pk']: "";

            $arrDados = (new Lancamento($this->pdo))->relContasPagarPeriodo($dt_vencimento_ini,$dt_vencimento_fim,$tipo_lancamento_pk,$contas_bancarias_pk,$tipos_operacao_pk_receita,$empresas_pk,$tipo_grupo_pk, $grupo_leancamento_pk);
            
            
            $ds_empresa = isset($data['ds_empresa'])? $data['ds_empresa']: "";
            $ds_tipo_grupo = isset($data['ds_tipo_grupo'])? $data['ds_tipo_grupo']: "";
            $ds_grupo_leancamento = isset($data['ds_grupo_leancamento'])? $data['ds_grupo_leancamento']: "";

            $this->view->render($response, 'relatorio/financeiro/resRelContasPagarPeriodo.twig',
                array(
                    "ds_empresa"=>$ds_empresa,
                    "ds_tipo_grupo"=>$ds_tipo_grupo,
                    "ds_grupo_lancamento"=>$ds_grupo_leancamento,
                    "dt_periodo"=>$dt_vencimento_ini." - ". $dt_vencimento_fim,
                    "arrDados"=>$arrDados
                )
            );
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoCompraMovimentacaoLead(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $categorias_produto_pk = isset($data['categorias_produto_pk'])? $data['categorias_produto_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $produtos_pk = isset($data['produtos_pk'])? $data['produtos_pk']: "";
            $tipo_operacao_pk = isset($data['tipo_operacao_pk'])? $data['tipo_operacao_pk']: "";
            $dt_ini_compra = isset($data['dt_ini_compra'])? $data['dt_ini_compra']: "";
            $dt_fim_compra = isset($data['dt_fim_compra'])? $data['dt_fim_compra']: "";
            $ds_posto_trabalho = isset($data['ds_posto_trabalho'])? $data['ds_posto_trabalho']: "";
            $ds_categorias_produto = isset($data['ds_categorias_produto'])? $data['ds_categorias_produto']: "";
            $ds_produto = isset($data['ds_produto'])? $data['ds_produto']: "";
            $ds_tipo_operacao = isset($data['ds_tipo_operacao'])? $data['ds_tipo_operacao']: "";

            $this->view->render($response, 'relatorio/compras_estoque/rel_compra_movimentacao_lead_res.twig',array(
                "categorias_produto_pk" =>$categorias_produto_pk,
                "leads_pk" =>$leads_pk,
                "produtos_pk" =>$produtos_pk,
                "tipo_operacao_pk" =>$tipo_operacao_pk,
                "dt_ini_compra" =>$dt_ini_compra,
                "dt_fim_compra" =>$dt_fim_compra,
                "ds_posto_trabalho" =>$ds_posto_trabalho,
                "ds_categorias_produto" =>$ds_categorias_produto,
                "ds_produto" =>$ds_produto,
                "ds_tipo_operacao" =>$ds_tipo_operacao
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function pesqAcompanhamentoBancoHoras(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/rh/pesqAcompanhamentoBancoHoras.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function receptivoAcompanhamentoBancoHoras(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $ic_mes = isset($data['ic_mes'])? $data['ic_mes']: "";
            $ic_ano = isset($data['ic_ano'])? $data['ic_ano']: "";
            $ds_leads = isset($data['ds_leads'])? $data['ds_leads']: "";
            $ds_mes = isset($data['ds_mes'])? $data['ds_mes']: "";
            $ds_colaboradores = isset($data['ds_colaboradores'])? $data['ds_colaboradores']: "";
        
            //PEGAR INFORMAÇÕES DE HORAS
            $arrDadosHoras = (new PontoFolha($this->pdo))->getHorasColaborador($colaborador_pk,$leads_pk,$ic_mes,$ic_ano,$ds_mes);
            
            $this->view->render($response, 'relatorio/rh/resAcompanhamentoBancoHoras.twig',array(
                "arrDados"       =>$arrDadosHoras->data,
                "ds_lead"        => $ds_leads,
                "ds_colaborador" =>$ds_colaboradores,
                "ds_ano"         => $ic_ano,
                "ds_mes"         =>$ds_mes
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function pesqAcompanhamentoFalta(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/rh/pesqAcompanhamentoFalta.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function receptivoAcompanhamentoFalta(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $ic_mes = isset($data['ic_mes'])? $data['ic_mes']: "";
            $ic_ano = isset($data['ic_ano'])? $data['ic_ano']: "";
            $ds_leads = isset($data['ds_leads'])? $data['ds_leads']: "";
            $ds_mes = isset($data['ds_mes'])? $data['ds_mes']: "";
            $ds_colaboradores = isset($data['ds_colaboradores'])? $data['ds_colaboradores']: "";
            $tipo_apontamento_pk = isset($data['tipo_apontamento_pk'])? $data['tipo_apontamento_pk']: "";
            $ds_apontamento = isset($data['ds_apontamento'])? $data['ds_apontamento']: "";
        
          
            $arrDados = (new AgendaColaboradorApontamento($this->pdo))->getRelatorioAcompanhamentoFalta($colaborador_pk,$leads_pk,$ic_mes,$ic_ano,$ds_mes,$tipo_apontamento_pk);
            
            $this->view->render($response, 'relatorio/rh/resAcompanhamentoFalta.twig',array(
                "arrDados"       =>$arrDados->data,
                "ds_lead"        => $ds_leads,
                "ds_colaborador" =>$ds_colaboradores,
                "ds_ano"         => $ic_ano,
                "ds_apontamento"         => $ds_apontamento,
                "ds_mes"         =>$ds_mes
            ));
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function pesqAcompanhamentoSupervisor(Request $request, Response $response, $args){
        try{
            $colaboradores = (new Colaborador($this->pdo))->listarColaboradorLeadCalendario("");
           
            $this->view->render($response, 'relatorio/rh/pesqAcompanhamentoSupervisor.twig',array(
                'colaboradores' => $colaboradores->data
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function receptivoAcompanhamentoSupervisor(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $ic_mes = isset($data['ic_mes'])? $data['ic_mes']: "";
            $ic_ano = isset($data['ic_ano'])? $data['ic_ano']: "";
            $ds_leads = isset($data['ds_leads'])? $data['ds_leads']: "";
            $ds_mes = isset($data['ds_mes'])? $data['ds_mes']: "";
            $ds_colaboradores = isset($data['ds_colaboradores'])? $data['ds_colaboradores']: "";
        
          
            $arrDados = (new Supervisor($this->pdo))->getRelatorioAcompanhamentoSupervisor($colaborador_pk,$leads_pk,$ic_mes,$ic_ano,$ds_mes);
            
            $this->view->render($response, 'relatorio/rh/resAcompanhamentoSupervisor.twig',array(
                "arrDados"       =>$arrDados->data,
                "ds_lead"        => $ds_leads,
                "ds_colaborador" =>$ds_colaboradores,
                "ds_ano"         => $ic_ano,
                "ds_mes"         =>$ds_mes
            ));
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function receptivoFechamento(Request $request, Response $response, $args){
        try{

            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead']: "";
            $ds_colaborador = isset($data['ds_colaborador'])? $data['ds_colaborador']: "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $dt_inicio = isset($data['dt_inicio'])? $data['dt_inicio']: "";
            $dt_fim = isset($data['dt_fim'])? $data['dt_fim']: "";

            $retorno = (new Ponto($this->pdo))->pegarDadosFechamento($leads_pk,$colaborador_pk,$dt_inicio,$dt_fim);
            $this->view->render($response, 'relatorio/rh/relFechamento.twig',
            array(
                "arrDados"=>$retorno,
                "ds_lead"=>$ds_lead,
                "ds_colaborador"=>$ds_colaborador,
                "dt_inicio"=>$dt_inicio,
                "dt_fim"=>$dt_fim
            ));
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function pesqFechamento(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/rh/pesqFechamento.twig');
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function pesqStatusColaborador(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'relatorio/rh/pesqStatusColaborador.twig');
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoStatusColaborador(Request $request, Response $response, $args){
        try{

            $data = $request->getQueryParams();
            $ic_status_app = isset($data['ic_status_app'])? $data['ic_status_app']: "";
            $ic_status = isset($data['ic_status'])? $data['ic_status']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $ds_status = isset($data['ds_status_app'])? $data['ds_status_app']: "";
            $ds_status_sistema = isset($data['ds_status'])? $data['ds_status']: "";
            $ds_leads = isset($data['ds_leads'])? $data['ds_leads']: "";
            $ds_colaboradores = isset($data['ds_colaboradores'])? $data['ds_colaboradores']: "";

            $retorno = (new Colaborador($this->pdo))->relatorioStatusColaborador($ic_status_app,$leads_pk,$colaborador_pk,$ic_status);
       
            $this->view->render($response, 'relatorio/rh/relStatusColaborador.twig',
            array(
                "arrDados"=>$retorno,
                "ds_status"=>$ds_status,
                "ds_status_sistema"=>$ds_status_sistema,
                "ds_lead"=>$ds_leads,
                "ds_colaborador"=>$ds_colaboradores
            ));
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    
}
