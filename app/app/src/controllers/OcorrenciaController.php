<?php

namespace App\Controller;

use App\Model\Agenda;
use App\Model\AgendaParticipante;
use App\Model\Documento;
use App\Model\Log;
use App\Model\Ocorrencia;
use App\Model\Retorno;
use App\Utils\Json;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class OcorrenciaController extends BaseController {
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'ocorrencia/ocorrencia_operacional_res_form.twig');
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
                (new Log($this->pdo))->salvar('ocorrencias', $pk);

                (new Ocorrencia($this->pdo))->excluir($pk);
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
    public function salvar(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk'] : "";
            $ds_ocorrencia = isset($data['ds_ocorrencia'])? $data['ds_ocorrencia'] : "";
            $tipos_ocorrencias_pk = isset($data['tipos_ocorrencias_pk'])? $data['tipos_ocorrencias_pk'] : "";
            $processos_etapas_pk = isset($data['processos_etapas_pk'])? $data['processos_etapas_pk'] : "";
            $dt_fechamento = isset($data['dt_fechamento'])? $data['dt_fechamento'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $ic_recusa = isset($data['ic_recusa'])? $data['ic_recusa'] : "";
            $dt_prazo_execucao = isset($data['dt_prazo_execucao'])? $data['dt_prazo_execucao'] : "";
            $clientes_pk = isset($data['clientes_pk'])? $data['clientes_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $obs_execucao = isset($data['obs_execucao'])? $data['obs_execucao'] : "";
            $obs_recusa = isset($data['obs_recusa'])? $data['obs_recusa'] : "";
            $agenda_retorno_pk = isset($data['agenda_retorno_pk'])? $data['agenda_retorno_pk'] : "";
            $dt_retorno = isset($data['dt_retorno'])? $data['dt_retorno'] : "";
            $hr_retorno = isset($data['hr_retorno'])? $data['hr_retorno'] : "";
            $equipes_pk = isset($data['equipes_pk'])? $data['equipes_pk'] : "";
            $responsavel_pk = isset($data['responsavel_pk'])? $data['responsavel_pk'] : "";
            $ds_retorno = isset($data['ds_retorno'])? $data['ds_retorno'] : "";
            $agenda_retorno = isset($data['agenda_retorno'])? $data['agenda_retorno'] : "";
            $dt_termino_retorno = isset($data['dt_termino_retorno'])? $data['dt_termino_retorno'] : "";
            $hr_termino_retorno = isset($data['hr_termino_retorno'])? $data['hr_termino_retorno'] : "";
            $tipo_lembrete_pk = isset($data['tipo_lembrete_pk'])? $data['tipo_lembrete_pk'] : "";

            $doc_oc = isset($data['doc_oc'])? $data['doc_oc'] : "";
            if($pk!=""){
                $ic_acao = "upd";
            }
            else{
                $ic_acao = "ins";
            }

            $ocorrencia = [
                "pk"=>$pk,
                "ds_ocorrencia"=>$ds_ocorrencia,
                "tipos_ocorrencias_pk"=>$tipos_ocorrencias_pk,
                "processos_etapas_pk"=>$processos_etapas_pk,
                "dt_fechamento"=>$dt_fechamento,
                "leads_pk"=>$leads_pk,
                "ic_recusa"=>$ic_recusa,
                "dt_prazo_execucao"=>$dt_prazo_execucao,
                "clientes_pk"=>$clientes_pk,
                "colaborador_pk"=>$colaborador_pk,
                "obs_execucao"=>$obs_execucao,
                "obs_recusa"=>$obs_recusa
            ];

            $retorno = (new Ocorrencia($this->pdo))->salvar($ocorrencia);

            if($pk!=""){
                $ocorrencias_pk = $pk;
            }
            else{
                $ocorrencias_pk = $retorno->data;
            }

            if($doc_oc != "")
                $arrDocOc = json_decode ($doc_oc, true);


            if(count($arrDocOc) > 0){
                for($i = 0; $i < count($arrDocOc); $i++){
                    if($arrDocOc[$i]['doc_oc_pk']!="Não existem Dados cadastrados"){
                        if($arrDocOc[$i]['doc_oc_pk']!="Carregando..."){

                            $documento =[
                                "pk"=>  "",
                                "pk_doc_bd"=>  $arrDocOc[$i]['doc_oc_pk'],
                                "ds_documento"=>  $arrDocOc[$i]['ds_documento'],
                                "ds_nome_original"=>  $arrDocOc[$i]['ds_nome_original'],
                                "leads_pk"=>  $leads_pk,
                                "agendas_pk"=>  "",
                                "ds_obs"=>  "",
                                "colaboradores_pk"=>  "",
                                "contratos_pk"=>  "",
                                "ic_tipo_documento"=>  "",
                                "ocorrencias_pk"=>  $ocorrencias_pk,
                                "agenda_colaborador_tarefa_pk"=>  "",
                                "lancamentos_pk"=>  "",
                                "compras_pk"=>  "",
                            ];

                            (new Documento($this->pdo))->salvar($documento);
                        }
                    }
                }
            }

            if($dt_retorno!='' || $dt_termino_retorno==1){
                $data_formatRetorno = "";
                if($dt_retorno!=""){
                    $data_formatRetorno = (Util::DataYMD(trim($dt_retorno))." ".$hr_retorno);
                }
                $retornoOc = [
                  "pk"=>$agenda_retorno_pk,
                  "equipes_pk"=>$equipes_pk,
                  "dt_retorno"=>$data_formatRetorno,
                  "responsavel_pk"=>$responsavel_pk,
                  "dt_termino_retorno"=>$dt_termino_retorno,
                  "ds_retorno"=>$ds_retorno,
                  "tipo_lembrete_pk"=>$tipo_lembrete_pk,
                  "ocorrencias_pk"=>$ocorrencias_pk,
                ];
                (new Retorno($this->pdo))->salvar($retornoOc);

            }
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarOcorrenciasLeadPk(Request $request, Response $response, $args) {
        $data = $request->getQueryParams();
        $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";

        (new Ocorrencia($this->pdo))->listarOcorrenciasLeadPk($leads_pk);

        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
    public function listarDataTableGrid(Request $request, Response $response, $args) {
        $data = $request->getQueryParams();

        $ds_lead = isset($data['ds_lead'])? $data['ds_lead'] : "";
        $tipos_ocorrencias_pk = isset($data['tipos_ocorrencias_pk'])? $data['tipos_ocorrencias_pk'] : "";
        $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
        $usuario_cadastro_pk = isset($data['usuario_cadastro_pk'])? $data['usuario_cadastro_pk'] : "";
        $dt_cadastro = isset($data['dt_cadastro'])? $data['dt_cadastro'] : "";
        $dt_prazo_execucao_ini = isset($data['dt_prazo_execucao_ini'])? $data['dt_prazo_execucao_ini'] : "";
        $dt_prazo_execucao_fim = isset($data['dt_prazo_execucao_fim'])? $data['dt_prazo_execucao_fim'] : "";
        $ic_status_fechamento = isset($data['ic_status_fechamento'])? $data['ic_status_fechamento'] : "";
        $equipes_pk = isset($data['equipes_pk'])? $data['equipes_pk'] : "";
        $dt_cadastro_fim = isset($data['dt_cadastro_fim'])? $data['dt_cadastro_fim'] : "";
        $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
        $usuario_agendado_para = isset($data['usuario_agendado_para'])? $data['usuario_agendado_para'] : "";

        (new Ocorrencia($this->pdo))->listar_por_ds_ocorrencia($ds_lead,$tipos_ocorrencias_pk,$ic_status,$usuario_cadastro_pk,$dt_cadastro,$dt_cadastro_fim,$usuario_agendado_para,$dt_prazo_execucao_ini,$dt_prazo_execucao_fim,$ic_status_fechamento,$equipes_pk,$colaborador_pk);

        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
    public function listarDataTableGridCliente(Request $request, Response $response, $args) {
        $data = $request->getQueryParams();

        $ds_lead = isset($data['ds_lead'])? $data['ds_lead'] : "";
        $tipos_ocorrencias_pk = isset($data['tipos_ocorrencias_pk'])? $data['tipos_ocorrencias_pk'] : "";
        $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
        $usuario_cadastro_pk = isset($data['usuario_cadastro_pk'])? $data['usuario_cadastro_pk'] : "";
        $dt_cadastro = isset($data['dt_cadastro'])? $data['dt_cadastro'] : "";
        $dt_prazo_execucao_ini = isset($data['dt_prazo_execucao_ini'])? $data['dt_prazo_execucao_ini'] : "";
        $dt_prazo_execucao_fim = isset($data['dt_prazo_execucao_fim'])? $data['dt_prazo_execucao_fim'] : "";
        $ic_status_fechamento = isset($data['ic_status_fechamento'])? $data['ic_status_fechamento'] : "";
        $equipes_pk = isset($data['equipes_pk'])? $data['equipes_pk'] : "";
        $dt_cadastro_fim = isset($data['dt_cadastro_fim'])? $data['dt_cadastro_fim'] : "";
        $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
        $usuario_agendado_para = isset($data['usuario_agendado_para'])? $data['usuario_agendado_para'] : "";

        (new Ocorrencia($this->pdo))->listarDataTableGridCliente($ds_lead,$tipos_ocorrencias_pk,$ic_status,$usuario_cadastro_pk,$dt_cadastro,$dt_cadastro_fim,$usuario_agendado_para,$dt_prazo_execucao_ini,$dt_prazo_execucao_fim,$ic_status_fechamento,$equipes_pk,$colaborador_pk);

        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
    public function listarPorPk(Request $request, Response $response, $args) {
        $entity = new Ocorrencia($this->pdo);
        $data = $request->getQueryParams();
        $pk = isset($data['pk'])? $data['pk'] : "";
        $retorno = $entity->listarOcorrenciaPorPk($pk);
        Json::run($retorno->status, $retorno->data, $retorno->message);
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}

