<?php

namespace App\Controller;

use App\Model\Colaborador;
use App\Model\Log;
use App\Model\ProdutoServico;
use App\Model\Usuario;
use App\Utils\Json;
use App\Model\PropostaFacilities;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class PropostaFacilitiesController extends BaseController {

    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'proposta_facilities/proposta_facilities_res_form.twig',array('ic_abertura'=>1));
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
                (new Log($this->pdo))->salvar('propostas_facilities',$pk);
                (new PropostaFacilities($this->pdo))->excluir($pk);
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
    public function salvar(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $ic_versao = isset($data['ic_versao'])? $data['ic_versao'] : "";
            $ds_versao = isset($data['ds_versao'])? $data['ds_versao'] : "";
            $ds_numero_proposta = isset($data['ds_numero_proposta'])? $data['ds_numero_proposta'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $ic_tipo_proposta = isset($data['ic_tipo_proposta'])? $data['ic_tipo_proposta'] : "";
            $produtos_servicos_pk = isset($data['produtos_servicos_pk'])? $data['produtos_servicos_pk'] : "";
            $ds_qtde_efetivo = isset($data['ds_qtde_efetivo'])? $data['ds_qtde_efetivo'] : "";
            $ds_qtde_hr_semanais = isset($data['ds_qtde_hr_semanais'])? $data['ds_qtde_hr_semanais'] : "";
            $ic_escala = isset($data['ic_escala'])? $data['ic_escala'] : "";
            $convencao_coletiva_pk = isset($data['convencao_coletiva_pk'])? $data['convencao_coletiva_pk'] : "";
            $dt_base_categoria = isset($data['dt_base_categoria'])? $data['dt_base_categoria'] : "";
            $ds_num_registro_mte = isset($data['ds_num_registro_mte'])? $data['ds_num_registro_mte'] : "";
            $vl_salario_piso_categoria = isset($data['vl_salario_piso_categoria'])? $data['vl_salario_piso_categoria'] : "";
            $vl_total_proposta = isset($data['vl_total_proposta'])? $data['vl_total_proposta'] : "";
            $vl_total_percentual_proposta = isset($data['vl_total_percentual_proposta'])? $data['vl_total_percentual_proposta'] : "";
            $usuario_responsavel_comercial_pk = isset($data['usuario_responsavel_comercial_pk'])? $data['usuario_responsavel_comercial_pk'] : "";
            $dt_envio_da_proposta = isset($data['dt_envio_da_proposta'])? $data['dt_envio_da_proposta'] : "";
            $dt_previsao_fechamento = isset($data['dt_previsao_fechamento'])? $data['dt_previsao_fechamento'] : "";
            $dt_fechamento = isset($data['dt_fechamento'])? $data['dt_fechamento'] : "";
            $dt_cancelamento = isset($data['dt_cancelamento'])? $data['dt_cancelamento'] : "";
            $obs_motivo_cancelamento = isset($data['obs_motivo_cancelamento'])? $data['obs_motivo_cancelamento'] : "";
            $obs_proposta = isset($data['obs_proposta'])? $data['obs_proposta'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $contratos_pk = isset($data['contratos_pk'])? $data['contratos_pk'] : "";
            $proposta_facilities_pai_pk = isset($data['pk'])? $data['pk'] : "";


            if($ic_versao > 0){
                $pk = '';
            }

            $propostas_facilities = [
                "pk"=>$pk,
                "ic_versao"=>$ic_versao,
                "ds_versao"=>$ds_versao,
                "ds_numero_proposta"=>$ds_numero_proposta,
                "leads_pk"=>$leads_pk,
                "ic_tipo_proposta"=>$ic_tipo_proposta,
                "produtos_servicos_pk"=>$produtos_servicos_pk,
                "ds_qtde_efetivo"=>$ds_qtde_efetivo,
                "ds_qtde_hr_semanais"=>$ds_qtde_hr_semanais,
                "ic_escala"=>$ic_escala,
                "convencao_coletiva_pk"=>$convencao_coletiva_pk,
                "dt_base_categoria"=>$dt_base_categoria,
                "ds_num_registro_mte"=>$ds_num_registro_mte,
                "vl_salario_piso_categoria"=>$vl_salario_piso_categoria,
                "vl_total_proposta"=>$vl_total_proposta,
                "vl_total_percentual_proposta"=>$vl_total_percentual_proposta,
                "usuario_responsavel_comercial_pk"=>$usuario_responsavel_comercial_pk,
                "dt_envio_da_proposta"=>$dt_envio_da_proposta,
                "dt_previsao_fechamento"=>$dt_previsao_fechamento,
                "dt_fechamento"=>$dt_fechamento,
                "dt_cancelamento"=>$dt_cancelamento,
                "obs_motivo_cancelamento"=>$obs_motivo_cancelamento,
                "obs_proposta"=>$obs_proposta,
                "ic_status"=>$ic_status,
                "contratos_pk"=>$contratos_pk,
                "proposta_facilities_pai_pk"=>$proposta_facilities_pai_pk,
            ];

            $retorno = (new PropostaFacilities($this->pdo))->salvar($propostas_facilities, $ic_versao);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function abrirPropostaSelecao(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $ic_versao = isset($data['ic_versao'])? $data['ic_versao'] : "";
            $ic_abertura = isset($data['ic_abertura'])? $data['ic_abertura'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";

            $this->view->render($response, 'proposta_facilities/proposta_selecao_form.twig',array(
                    'pk'=>$pk,
                    'ic_versao'=>$ic_versao,
                    'ic_abertura'=>$ic_abertura,
                    'leads_pk'=>$leads_pk
                )
            );
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function abrirPropostaDetalhada(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $ic_versao = isset($data['ic_versao'])? $data['ic_versao'] : "";
            $ic_abertura = isset($data['ic_abertura'])? $data['ic_abertura'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";


            $ic_versao = isset($data['ic_versao'])? $data['ic_versao'] : "";

            $this->view->render($response, 'proposta_facilities/proposta_detalhada_cad_form.twig',array(
                    'pk'=>$pk,
                    'ic_versao'=>$ic_versao,
                    'ic_abertura'=>$ic_abertura,
                    'leads_pk'=>$leads_pk
                )
            );
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarPropostaDetalhada(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";

            $retorno = (new PropostaFacilities($this->pdo))->listarPropostaDetalhada();

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function pegarDadosItens(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";

            $retorno = (new PropostaFacilities($this->pdo))->pegarDadosItens();

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarDadosPropostaDetalhada(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";

            $retorno = (new PropostaFacilities($this->pdo))->listarDadosPropostaDetalhada($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarImpressaoProposta(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";

            $retorno = (new PropostaFacilities($this->pdo))->listarImpressaoProposta($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarDataTablePk(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $usuario_cadastro_pk = isset($data['usuario_cadastro_pk'])? $data['usuario_cadastro_pk'] : "";
            $usuario_responsavel_comercial_pk = isset($data['usuario_responsavel_comercial_pk'])? $data['usuario_responsavel_comercial_pk'] : "";
            $dt_cadastro = isset($data['dt_cadastro'])? $data['dt_cadastro'] : "";

            (new PropostaFacilities($this->pdo))->listar_por_ds_versao_pk($leads_pk,$ic_status,$usuario_cadastro_pk,$usuario_responsavel_comercial_pk,$dt_cadastro);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function abrirImpressao(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $ic_versao = isset($data['ic_versao'])? $data['ic_versao'] : "";
            $ic_abertura = isset($data['ic_abertura'])? $data['ic_abertura'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";


            $this->view->render($response, 'partials/proposta_facilities_impressao.twig',
                array('pk'=>$pk,
                    'ic_versao'=>$ic_versao,
                    'ic_abertura'=>$ic_abertura,
                    'leads_pk'=>$leads_pk
                )
            );
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function RelatorioProposta(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_clientes_pk = isset($data['leads_clientes_pk'])? $data['leads_clientes_pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $usuario_cadastro_pk = isset($data['usuario_cadastro_pk'])? $data['usuario_cadastro_pk'] : "";
            $dt_ini = isset($data['dt_ini'])? $data['dt_ini'] : "";
            $dt_fim = isset($data['dt_fim'])? $data['dt_fim'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";

            (new PropostaFacilities($this->pdo))->RelatorioProposta($leads_clientes_pk, $leads_pk, $usuario_cadastro_pk, $dt_ini, $dt_fim, $ic_status);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}