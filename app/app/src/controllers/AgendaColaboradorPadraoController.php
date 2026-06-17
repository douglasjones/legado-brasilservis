<?php

namespace App\Controller;

use App\Model\AgendaColaboradorPadrao;
use App\Model\Cargo;
use App\Model\Log;
use App\Utils\Util;
use App\Utils\Json;
use DateTime;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class AgendaColaboradorPadraoController extends BaseController {
    public function excluir(Request $request, Response $response, $args)
    {
        try {
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";
            if($pk!=""){
                (new Log($this->pdo))->salvar('agenda_colaborador_padrao',$pk);
                (new AgendaColaboradorPadrao($this->pdo))->excluir($pk);
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
            $this->view->render($response, 'agenda_calendario/calendario_escala.twig',array('ic_abertura'=>1));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function receptivoEscala(Request $request, Response $response, $args) {
        try{

            $data = $request->getQueryParams();
            $local = isset($data['local'])? $data['local']: "";
            $this->view->render($response, 'escala/agenda_escala_res_form.twig',array(
                "local"=>$local
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function cadFormEscala(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $local = isset($data['local'])? $data['local']: "";
            $this->view->render($response, 'escala/agenda_escala_cad_form.twig',array(
                "pk"=>$pk,
                "leads_pk"=>$leads_pk,
                "local"=>$local
            ));
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function salvar(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk']: "";
            $leads_pk = isset($data['leads_pk'])?$data['leads_pk']: "";
            $contratos_pk = isset($data['contratos_pk'])? $data['contratos_pk'] : "";
            $dt_inicio_agenda = isset($data['dt_inicio_agenda'])?$data['dt_inicio_agenda']: "";
            $dt_fim_agenda = isset($data['dt_fim_agenda'])? $data['dt_fim_agenda'] : "";
            $produtos_servicos_pk = isset($data['produtos_servicos_pk'])?$data['produtos_servicos_pk']: "";
            $colaboradores_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk'] : "";
            $processos_etapas_pk = isset($data['processos_etapas_pk'])?$data['processos_etapas_pk']: "";
            $contratos_itens_pk = isset($data['contratos_itens_pk'])? $data['contratos_itens_pk'] : "";
            $turnos_pk = isset($data['turnos_pk'])? $data['turnos_pk'] : "";
            $hr_inicio_expediente = isset($data['hr_inicio_expediente'])? $data['hr_inicio_expediente'] : "";
            $hr_termino_expediente = isset($data['hr_termino_expediente'])? $data['hr_termino_expediente'] : "";
            $hr_saida_intervalo = isset($data['hr_saida_intervalo'])? $data['hr_saida_intervalo'] : "";
            $hr_retorno_intervalo = isset($data['hr_retorno_intervalo'])? $data['hr_retorno_intervalo'] : "";
            $ic_folga_inverter = isset($data['ic_folga_inverter'])? $data['ic_folga_inverter'] : "";
            $tipo_escala = isset($data['tipo_escala'])? $data['tipo_escala'] : "";
            $ic_intrajornada = isset($data['ic_intrajornada'])? $data['ic_intrajornada'] : "";
            $ic_dom = isset($data['ic_dom'])? $data['ic_dom'] : "";
            $ic_seg = isset($data['ic_seg'])? $data['ic_seg'] : "";
            $ic_ter = isset($data['ic_ter'])? $data['ic_ter'] : "";
            $ic_qua = isset($data['ic_qua'])? $data['ic_qua'] : "";
            $ic_qui = isset($data['ic_qui'])? $data['ic_qui'] : "";
            $ic_sex = isset($data['ic_sex'])? $data['ic_sex'] : "";
            $ic_sab = isset($data['ic_sab'])? $data['ic_sab'] : "";
            $ic_dom_folga = isset($data['ic_dom_folga'])? $data['ic_dom_folga'] : "";
            $ic_seg_folga = isset($data['ic_seg_folga'])? $data['ic_seg_folga'] : "";
            $ic_ter_folga = isset($data['ic_ter_folga'])? $data['ic_ter_folga'] : "";
            $ic_qua_folga = isset($data['ic_qua_folga'])? $data['ic_qua_folga'] : "";
            $ic_qui_folga = isset($data['ic_qui_folga'])? $data['ic_qui_folga'] : "";
            $ic_sex_folga = isset($data['ic_sex_folga'])? $data['ic_sex_folga'] : "";
            $ic_sab_folga = isset($data['ic_sab_folga'])? $data['ic_sab_folga'] : "";
            $dom_turnos_pk = isset($data['dom_turnos_pk'])? $data['dom_turnos_pk'] : "";
            $seg_turnos_pk = isset($data['seg_turnos_pk'])? $data['seg_turnos_pk'] : "";
            $ter_turnos_pk = isset($data['ter_turnos_pk'])? $data['ter_turnos_pk'] : "";
            $qua_turnos_pk = isset($data['qua_turnos_pk'])? $data['qua_turnos_pk'] : "";
            $qui_turnos_pk = isset($data['qui_turnos_pk'])? $data['qui_turnos_pk'] : "";
            $sex_turnos_pk = isset($data['sex_turnos_pk'])? $data['sex_turnos_pk'] : "";
            $sab_turnos_pk = isset($data['sab_turnos_pk'])? $data['sab_turnos_pk'] : "";
            $hr_turno_dom = isset($data['hr_turno_dom'])? $data['hr_turno_dom'] : "";
            $hr_turno_seg = isset($data['hr_turno_seg'])? $data['hr_turno_seg'] : "";
            $hr_turno_ter = isset($data['hr_turno_ter'])? $data['hr_turno_ter'] : "";
            $hr_turno_qua = isset($data['hr_turno_qua'])? $data['hr_turno_qua'] : "";
            $hr_turno_qui = isset($data['hr_turno_qui'])? $data['hr_turno_qui'] : "";
            $hr_turno_sex = isset($data['hr_turno_sex'])? $data['hr_turno_sex'] : "";
            $hr_turno_sab = isset($data['hr_turno_sab'])? $data['hr_turno_sab'] : "";
            $hr_turno_dom_saida = isset($data['hr_turno_dom_saida'])? $data['hr_turno_dom_saida'] : "";
            $hr_turno_seg_saida = isset($data['hr_turno_seg_saida'])? $data['hr_turno_seg_saida'] : "";
            $hr_turno_ter_saida = isset($data['hr_turno_ter_saida'])? $data['hr_turno_ter_saida'] : "";
            $hr_turno_qua_saida = isset($data['hr_turno_qua_saida'])? $data['hr_turno_qua_saida'] : "";
            $hr_turno_qui_saida = isset($data['hr_turno_qui_saida'])? $data['hr_turno_qui_saida'] : "";
            $hr_turno_sex_saida = isset($data['hr_turno_sex_saida'])? $data['hr_turno_sex_saida'] : "";
            $hr_turno_sab_saida = isset($data['hr_turno_sab_saida'])? $data['hr_turno_sab_saida'] : "";
            $hr_intervalo_dom = isset($data['hr_intervalo_dom'])? $data['hr_intervalo_dom'] : "";
            $hr_intervalo_seg = isset($data['hr_intervalo_seg'])? $data['hr_intervalo_seg'] : "";
            $hr_intervalo_ter = isset($data['hr_intervalo_ter'])? $data['hr_intervalo_ter'] : "";
            $hr_intervalo_qua = isset($data['hr_intervalo_qua'])? $data['hr_intervalo_qua'] : "";
            $hr_intervalo_qui = isset($data['hr_intervalo_qui'])? $data['hr_intervalo_qui'] : "";
            $hr_intervalo_sex = isset($data['hr_intervalo_sex'])? $data['hr_intervalo_sex'] : "";
            $hr_intervalo_sab = isset($data['hr_intervalo_sab'])? $data['hr_intervalo_sab'] : "";
            $hr_intervalo_saida_dom = isset($data['hr_intervalo_saida_dom'])? $data['hr_intervalo_saida_dom'] : "";
            $hr_intervalo_saida_seg = isset($data['hr_intervalo_saida_seg'])? $data['hr_intervalo_saida_seg'] : "";
            $hr_intervalo_saida_ter = isset($data['hr_intervalo_saida_ter'])? $data['hr_intervalo_saida_ter'] : "";
            $hr_intervalo_saida_qua = isset($data['hr_intervalo_saida_qua'])? $data['hr_intervalo_saida_qua'] : "";
            $hr_intervalo_saida_qui = isset($data['hr_intervalo_saida_qui'])? $data['hr_intervalo_saida_qui'] : "";
            $hr_intervalo_saida_sex = isset($data['hr_intervalo_saida_sex'])? $data['hr_intervalo_saida_sex'] : "";
            $hr_intervalo_saida_sab = isset($data['hr_intervalo_saida_sab'])? $data['hr_intervalo_saida_sab'] : "";
            $dt_cancelamento = isset($data['dt_cancelamento'])? $data['dt_cancelamento'] : "";
            $ds_motivo_cancelamento = isset($data['ds_motivo_cancelamento'])? $data['ds_motivo_cancelamento'] : "";
            $n_qtde_dias_semana = isset($data['n_qtde_dias_semana'])? $data['n_qtde_dias_semana'] : "";
            $ic_preenchimento_automatico = isset($data['ic_preenchimento_automatico'])? $data['ic_preenchimento_automatico'] : "";
            $ic_nao_repetir = isset($data['ic_nao_repetir'])? $data['ic_nao_repetir'] : "";
            $fl_escala_alternada = isset($data['fl_escala_alternada'])? $data['fl_escala_alternada'] : "";
            $dias_escala_alternada = isset($data['dias_escala_alternada'])? $data['dias_escala_alternada'] : "";
            $tipo_escala_alternada = isset($data['tipo_escala_alternada'])? $data['tipo_escala_alternada'] : "";
            $dias_escala_servico = isset($data['dias_escala_servico'])? $data['dias_escala_servico'] : "";
            $hr_total_expediente = isset($data['hr_total_expediente'])? $data['hr_total_expediente'] : "";
            $hr_jornada_trabalho_intervalo = isset($data['hr_jornada_trabalho_intervalo'])? $data['hr_jornada_trabalho_intervalo'] : "";
            $ic_tempo_antes_ponto = isset($data['ic_tempo_antes_ponto'])? $data['ic_tempo_antes_ponto'] : "";
            $ic_ponto_fora_horario = isset($data['ic_ponto_fora_horario'])? $data['ic_ponto_fora_horario'] : "";
            $confirmar_nova_escala = isset($data['confirmar_nova_escala'])? $data['confirmar_nova_escala'] : "";

            $agenda_colaborador_padrao = [
                "pk"=>$pk,
                "leads_pk"=>$leads_pk,
                "contratos_pk"=>$contratos_pk,
                "dt_inicio_agenda"=>Util::DataYMD($dt_inicio_agenda),
                "dt_fim_agenda"=>Util::DataYMD($dt_fim_agenda),
                "produtos_servicos_pk"=>$produtos_servicos_pk,
                "colaboradores_pk"=>$colaboradores_pk,
                "processos_etapas_pk"=>$processos_etapas_pk,
                "contratos_itens_pk"=>$contratos_itens_pk,
                "turnos_pk"=>$turnos_pk,
                "hr_inicio_expediente"=>$hr_inicio_expediente,
                "hr_termino_expediente"=>$hr_termino_expediente,
                "hr_saida_intervalo"=>$hr_saida_intervalo,
                "hr_retorno_intervalo"=>$hr_retorno_intervalo,
                "ic_folga_inverter"=>$ic_folga_inverter,
                "tipo_escala"=>$tipo_escala,
                "ic_intrajornada"=>$ic_intrajornada,
                "ic_dom"=>$ic_dom,
                "ic_seg"=>$ic_seg,
                "ic_ter"=>$ic_ter,
                "ic_qua"=>$ic_qua,
                "ic_qui"=>$ic_qui,
                "ic_sex"=>$ic_sex,
                "ic_sab"=>$ic_sab,
                "ic_dom_folga"=>$ic_dom_folga,
                "ic_seg_folga"=>$ic_seg_folga,
                "ic_ter_folga"=>$ic_ter_folga,
                "ic_qua_folga"=>$ic_qua_folga,
                "ic_qui_folga"=>$ic_qui_folga,
                "ic_sex_folga"=>$ic_sex_folga,
                "ic_sab_folga"=>$ic_sab_folga,
                "dom_turnos_pk"=>$dom_turnos_pk,
                "seg_turnos_pk"=>$seg_turnos_pk,
                "ter_turnos_pk"=>$ter_turnos_pk,
                "qua_turnos_pk"=>$qua_turnos_pk,
                "qui_turnos_pk"=>$qui_turnos_pk,
                "sex_turnos_pk"=>$sex_turnos_pk,
                "sab_turnos_pk"=>$sab_turnos_pk,
                "hr_turno_dom"=>$hr_turno_dom,
                "hr_turno_seg"=>$hr_turno_seg,
                "hr_turno_ter"=>$hr_turno_ter,
                "hr_turno_qua"=>$hr_turno_qua,
                "hr_turno_qui"=>$hr_turno_qui,
                "hr_turno_sex"=>$hr_turno_sex,
                "hr_turno_sab"=>$hr_turno_sab,
                "hr_turno_dom_saida"=>$hr_turno_dom_saida,
                "hr_turno_seg_saida"=>$hr_turno_seg_saida,
                "hr_turno_ter_saida"=>$hr_turno_ter_saida,
                "hr_turno_qua_saida"=>$hr_turno_qua_saida,
                "hr_turno_qui_saida"=>$hr_turno_qui_saida,
                "hr_turno_sex_saida"=>$hr_turno_sex_saida,
                "hr_turno_sab_saida"=>$hr_turno_sab_saida,
                "hr_intervalo_dom"=>$hr_intervalo_dom,
                "hr_intervalo_seg"=>$hr_intervalo_seg,
                "hr_intervalo_ter"=>$hr_intervalo_ter,
                "hr_intervalo_qua"=>$hr_intervalo_qua,
                "hr_intervalo_qui"=>$hr_intervalo_qui,
                "hr_intervalo_sex"=>$hr_intervalo_sex,
                "hr_intervalo_sab"=>$hr_intervalo_sab,
                "hr_intervalo_saida_dom"=>$hr_intervalo_saida_dom,
                "hr_intervalo_saida_seg"=>$hr_intervalo_saida_seg,
                "hr_intervalo_saida_ter"=>$hr_intervalo_saida_ter,
                "hr_intervalo_saida_qua"=>$hr_intervalo_saida_qua,
                "hr_intervalo_saida_qui"=>$hr_intervalo_saida_qui,
                "hr_intervalo_saida_sex"=>$hr_intervalo_saida_sex,
                "hr_intervalo_saida_sab"=>$hr_intervalo_saida_sab,
                "dt_cancelamento"=>$dt_cancelamento,
                "ds_motivo_cancelamento"=>$ds_motivo_cancelamento,
                "n_qtde_dias_semana"=>$n_qtde_dias_semana,
                "ic_preenchimento_automatico"=>$ic_preenchimento_automatico,
                "ic_nao_repetir"=>$ic_nao_repetir,
                "dias_escala_servico"=>$dias_escala_servico,
                "fl_escala_alternada"=>$fl_escala_alternada,
                "dias_escala_alternada"=>$dias_escala_alternada,
                "tipo_escala_alternada"=>$tipo_escala_alternada,
                "hr_total_expediente"=>$hr_total_expediente,
                "hr_jornada_trabalho_intervalo"=>$hr_jornada_trabalho_intervalo,
                "ic_ponto_fora_horario"=>$ic_ponto_fora_horario,
                "ic_tempo_antes_ponto"=>$ic_tempo_antes_ponto,
                "confirmar_nova_escala"=>$confirmar_nova_escala,
            ];

            $retorno = (new AgendaColaboradorPadrao($this->pdo))->salvar($agenda_colaborador_padrao);

            if (!$retorno->status && !empty($retorno->requires_confirmation)) {
                return $response->withJson((object)[
                    'status' => false,
                    'message' => $retorno->message,
                    'requires_confirmation' => true,
                    'data' => $retorno->data
                ], 200);
            }

            return $response->withJson((object)[
                'status' => (bool) $retorno->status,
                'message' => $retorno->message,
                'data' => $retorno->data
            ], 200);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function escalaDadosColaborador(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $entity = new AgendaColaboradorPadrao($this->pdo);
            $agenda_colaborador_padrao_pk = isset($data['agenda_colaborador_padrao_pk'])? $data['agenda_colaborador_padrao_pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaboradores_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk'] : "";
            $dt_periodo_ini = isset($data['dt_periodo_ini'])? $data['dt_periodo_ini'] : "";
            $dt_periodo_fim = isset($data['dt_periodo_fim'])? $data['dt_periodo_fim'] : "";
            $n_qtde_dias_semana = isset($data['n_qtde_dias_semana'])? $data['n_qtde_dias_semana'] : "";
            $tipo_escala = isset($data['tipo_escala'])? $data['tipo_escala'] : "";
            $fl_escala_alternada = isset($data['fl_escala_alternada'])? $data['fl_escala_alternada'] : "";
            $dias_escala_servico = isset($data['n_qtde_dias_semana'])? $data['n_qtde_dias_semana'] : "";
            
           
            $retorno = $entity->escalaDadosColaborador(
                $colaboradores_pk, 
                $dt_periodo_ini, 
                $dt_periodo_fim, 
                $dias_escala_servico, 
                $leads_pk, 
                $agenda_colaborador_padrao_pk, 
                $tipo_escala,
                $fl_escala_alternada,
                $dias_escala_servico
            );
            return $response->withJson((object)[
                'status' => (bool) $retorno->status,
                'message' => $retorno->message,
                'data' => $retorno->data
            ], 200);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function lisarEscalaEditar(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $entity = new AgendaColaboradorPadrao($this->pdo);
            $pk = isset($data['pk'])? $data['pk'] : "";

            $retorno = $entity->lisarEscalaEditar($pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function consultarEscalaContratosItens(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $entity = new AgendaColaboradorPadrao($this->pdo);

            $contratos_itens_pk = isset($data['contratos_itens_pk'])? $data['contratos_itens_pk'] : "";
            $contratos_pk = isset($data['contratos_pk'])? $data['contratos_pk'] : "";
            $retorno = $entity->consultarEscalaContratosItens($contratos_pk,$contratos_itens_pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarTurno(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $entity = new AgendaColaboradorPadrao($this->pdo);

            $retorno = $entity->listarTurno();

            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarEscalasPostosColaborador(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $entity = new AgendaColaboradorPadrao($this->pdo);

            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $dt_apontamento = isset($data['dt_apontamento'])? $data['dt_apontamento'] : "";
            $retorno = $entity->listarEscalasPostosColaborador($colaborador_pk, $dt_apontamento);

            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function calendarioDados(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $produtos_servicos_pk = isset($data['produtos_servicos_pk'])? $data['produtos_servicos_pk'] : "";
            $n_qtde_dias_semana = isset($data['n_qtde_dias_semana'])? $data['n_qtde_dias_semana'] : "";
            $tipo_escala_pk = isset($data['tipo_escala_pk'])? $data['tipo_escala_pk'] : "";
            $escala_pesq_agenda = isset($data['escala_pesq_agenda'])? $data['escala_pesq_agenda'] : "";
            $dt_ini = date('Y-m-d', strtotime($data['start']));
            $dt_fim = date('Y-m-d', strtotime($data['end']));

            $entity = new AgendaColaboradorPadrao($this->pdo);

            $entity->calendarioDados($dt_ini,$dt_fim,$leads_pk,$colaborador_pk,$n_qtde_dias_semana,$tipo_escala_pk,$escala_pesq_agenda,$produtos_servicos_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarEscalasResPadrao(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk_pesq = isset($data['leads_pk_pesq'])? $data['leads_pk_pesq'] : "";
            $colaborador_pk_pesq_agenda = isset($data['colaborador_pk_pesq_agenda'])? $data['colaborador_pk_pesq_agenda'] : "";
            $escala_pesq_agenda = isset($data['escala_pesq_agenda'])? $data['escala_pesq_agenda'] : "";
            $produtos_pesq_agenda = isset($data['escala_pesq_agenda'])? $data['produtos_pesq_agenda'] : "";
            $ic_status_pesq_agenda = isset($data['ic_status_pesq_agenda'])? $data['ic_status_pesq_agenda'] : "";
            $tipo_escala_pesq_agenda = isset($data['tipo_escala_pesq_agenda'])? $data['tipo_escala_pesq_agenda'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $processos_pk = isset($data['processos_pk'])? $data['processos_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $turno_base_pk_pesq = isset($data['turno_base_pk_pesq'])? $data['turno_base_pk_pesq'] : "";

            $entity = new AgendaColaboradorPadrao($this->pdo);

            $entity->listarEscalasResPadrao($leads_pk,$processos_pk,$colaborador_pk,$leads_pk_pesq,$colaborador_pk_pesq_agenda,$escala_pesq_agenda,$tipo_escala_pesq_agenda,$produtos_pesq_agenda,$ic_status_pesq_agenda,$turno_base_pk_pesq);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function processa_escala(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $entity = new AgendaColaboradorPadrao($this->pdo);
            $retorno = $entity->processa_escala();
            
            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function lisarEscalasResPadraoColaborador(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";

            $entity = new AgendaColaboradorPadrao($this->pdo);

            $entity->lisarEscalasResPadraoColaborador($colaborador_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function calendarioDadosEscala(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $produtos_servicos_pk = isset($data['produtos_servicos_pk'])? $data['produtos_servicos_pk'] : "";
            $n_qtde_dias_semana = isset($data['n_qtde_dias_semana'])? $data['n_qtde_dias_semana'] : "";
            $dt_fim = date('Y-m-d', strtotime($data['end']));
            $dt_ini = date('Y-m-d', strtotime($data['start']));
            $entity = new AgendaColaboradorPadrao($this->pdo);

            $entity->calendarioDadosEscala($dt_fim,$leads_pk,$colaborador_pk,$n_qtde_dias_semana,$produtos_servicos_pk,1,$dt_ini);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function verificaOutraEscalaColaborador(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk'] : "";

            $entity = new AgendaColaboradorPadrao($this->pdo);
            $retorno = $entity->verificaOutraEscalaColaborador($colaborador_pk);

            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function cancelarEscalasDemissao(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
          
            $colaborador_pk = isset($data['colaborador_pk']) ? $data['colaborador_pk'] : "";
            
            $retorno = (new AgendaColaboradorPadrao($this->pdo))->cancelarEscalasDemissao($colaborador_pk);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
           
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function updateDataEscalaColaborador(Request $request, Response $response, $args) {

        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaborador_pk']) ? $data['colaborador_pk'] : "";
            $dt_atual = isset($data['dt_atual']) ? $data['dt_atual'] : "";
            $nova_data = isset($data['nova_data']) ? $data['nova_data'] : "";
            $leads_pk = isset($data['leads_pk']) ? $data['leads_pk'] : "";

            $retorno = (new AgendaColaboradorPadrao($this->pdo))->updateDataEscalaColaborador($colaborador_pk,$dt_atual,$nova_data,$leads_pk);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listaPostoXColaboradores(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();

            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaboradores_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk'] : "";
           
            (new AgendaColaboradorPadrao($this->pdo))->listaPostoXColaboradores($leads_pk,$colaboradores_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function pegarPostoDeTrabalhoPorLeadEColaborador(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();

            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaboradores_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk'] : "";
           
            $retorno = (new AgendaColaboradorPadrao($this->pdo))->pegarPostoDeTrabalhoPorLeadEColaborador($leads_pk,$colaboradores_pk);

            json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function pegarPostoByColaboradorPorMesAno(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();

            //$ic_mes = isset($data['ic_mes'])? $data['ic_mes'] : "";
            //$ic_ano = isset($data['ic_ano'])? $data['ic_ano'] : "";
            $dt_inicio = isset($data['dt_inicio'])? $data['dt_inicio'] : "";
            $dt_fim = isset($data['dt_fim'])? $data['dt_fim'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
           
            $retorno = (new AgendaColaboradorPadrao($this->pdo))->pegarPostoByColaboradorPorMesAno($dt_inicio,$dt_fim,$colaborador_pk);

            json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function testeXml(Request $request, Response $response, $args) {
        try{
            // Exemplo de uso
            $xmlBase64 = 'PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+PG5mZVByb2MgeG1sbnM9Imh0dHA6Ly93d3cucG9ydGFsZmlzY2FsLmluZi5ici9uZmUiIHZlcnNhbz0iNC4wMCI+PE5GZT48aW5mTkZlIElkPSJORmUzNTI1MDYwMDQ5NTU5MzAwMDEwNDU1MDAxMDAwMjg0OTM0MTAxMjgwMTE2MCIgdmVyc2FvPSI0LjAwIj48aWRlPjxjVUY+MzU8L2NVRj48Y05GPjAxMjgwMTE2PC9jTkY+PG5hdE9wPlZFTkRBIE1FUkMgQURRIE9VIFJFQyBERSBURVJDRUlST1M8L25hdE9wPjxtb2Q+NTU8L21vZD48c2VyaWU+MTwvc2VyaWU+PG5ORj4yODQ5MzQ8L25ORj48ZGhFbWk+MjAyNS0wNi0wNFQxNTo0MToxNC0wMzowMDwvZGhFbWk+PHRwTkY+MTwvdHBORj48aWREZXN0PjE8L2lkRGVzdD48Y011bkZHPjM1MTg4MDA8L2NNdW5GRz48dHBJbXA+MTwvdHBJbXA+PHRwRW1pcz4xPC90cEVtaXM+PGNEVj4wPC9jRFY+PHRwQW1iPjE8L3RwQW1iPjxmaW5ORmU+MTwvZmluTkZlPjxpbmRGaW5hbD4xPC9pbmRGaW5hbD48aW5kUHJlcz4xPC9pbmRQcmVzPjxwcm9jRW1pPjA8L3Byb2NFbWk+PHZlclByb2M+VjIuMzAzPC92ZXJQcm9jPjwvaWRlPjxlbWl0PjxDTlBKPjAwNDk1NTkzMDAwMTA0PC9'.
            'DTlBKPjx4Tm9tZT40QSBDT01FUkNJQUwgRUxFVFJJQ0EgTFREQTwveE5vbWU+PGVuZGVyRW1pdD48eExncj5SIFBFIEpPQU8gQUxWQVJFUywgNTk4PC94TGdyPjxucm8+NTk4PC9ucm8+PHhCYWlycm8+VklMQSBSRU5BVEE8L3hCYWlycm8+PGNNdW4+MzUxODgwMDwvY011bj48eE11bj5HVUFSVUxIT1M8L3hNdW4+PFVGPlNQPC9VRj48Q0VQPjA3MDU2MDAwPC9DRVA+PGNQYWlzPjEwNTg8L2NQYWlzPjx4UGFpcz5CcmFzaWw8L3hQYWlzPjxmb25lPjExMzQyOTA5MDA8L2ZvbmU+PC9lbmRlckVtaXQ+PElFPjMzNjQwMjE3NTExNDwvSUU+PENSVD4zPC9DUlQ+PC9lbWl0PjxkZXN0PjxDTlBKPjIyMTI0NjI0MDAwMTUzPC9DTlBKPjx4Tm9tZT5WSVNUT1JJWkEgU0VSVklDT1MgVEVSQ0VJUklaQURPUyBFSVJFTEkgTUUtMDYzMDg5PC94Tm9tZT48ZW5kZXJEZXN0Pjx4TGdyPlJVQSBET1VUT1IgV0FTSElOR1RPTiBMVUlaLCA2MDI8L3hMZ3I+PG5ybz42MDI8L25ybz48eEJhaXJybz5KQVJESU0gU0FOVEEgRlJBTkNJU0NBPC94QmFpcnJvPjxjTXVuPjM1MTg4MDA8L2NNdW4+PHhNdW4+R1VBUlVMSE9TPC94TXVuPjxVRj5TUDwvVUY+PENFUD4wNzAxMzAyMDwvQ0VQPjxjUGFpcz4xMDU4PC9jUGFpcz48Zm9uZT4xMTk1ODQ2NTI0MDwvZm9uZT48L2VuZGVyRGVzdD48aW5kSUVEZXN0Pjk8L2luZElFRGVzdD48ZW1haWw+YXJpYW5pLmNvcnJlaWFAZ3J1cG92aXN0b3JpemEuY29tLmJyPC9lbWFpbD48L2Rlc3Q+PGF1dFhNTD48Q05QSj42MTYwNTI3NTAwMDE0MzwvQ05QSj48L2F1dFhNTD48ZGV0IG5JdGVtPSIxIj48cHJvZD48Y1Byb2Q+MjIwNTgwMDM1NDwvY1Byb2Q+PGNFQU4+Nzg5NjAzOTcwMjAwNzwvY0VBTj48eFByb2Q+UkVTSVNURU5DSUEgRkVSUk8gU09MREEgNzBXIDEyN1YgMjAwIEZBTUU8L3hQcm9kPjxOQ00+ODUxNTkwMDA8L05DTT48Q0ZPUD41MTAyPC9DRk9QPjx1Q29tPlBDPC91Q29tPjxxQ29tPjEuMDAwMDwvcUNvbT48dlVuQ29tPjM0LjgwMDAwMDAwMDA8L3ZVbkNvbT48dlByb2Q+MzQuODA8L3ZQcm9kPjxjRUFOVHJpYj43ODk2MDM5NzAyMDA3PC9jRUFOVHJpYj48dVRyaWI+UEM8L3VUcmliPjxxVHJpYj4xLjAwMDA8L3FUcmliPjx2VW5UcmliPjM0LjgwMDAwMDAwMDA8L3ZVblRyaWI+PGluZFRvdD4xPC9pbmRUb3Q+PC9wcm9kPjxpbXBvc3RvPjx2VG90VHJpYj4xMC45NDwvdlRvdFRyaWI+PElDTVM+PElDTVMwMD48b3JpZz4wPC9vcmlnPjxDU1Q+MDA8L0NTVD48bW9kQkM+MzwvbW9kQkM+PHZCQz4zNC44MDwvdkJDPjxwSUNNUz4xOC4wMDwvcElDTVM+PHZJQ01TPjYuMjY8L3ZJQ01TPjwvSUNNUzAwPjwvSUNNUz48SVBJPjxjRW5xPjk5OTwvY0VucT48SVBJTlQ+PENTVD41MzwvQ1NUPjwvSVBJTlQ+PC9JUEk+PFBJUz48UElTQWxpcT48Q1NUPjAxPC9DU1Q+PHZCQz4zNC44MDwvdkJDPjxwUElTPjEuNjU8L3BQSVM+PHZQSVM+MC41NzwvdlBJUz48L1BJU0FsaXE+PC9QSVM+PENPRklOUz48Q09GSU5TQWxpcT48Q1NUPjAxPC9DU1Q+PHZCQz4zNC44MDwvdkJDPjxwQ09GSU5TPjcuNjA8L3BDT0ZJTlM+PHZDT0ZJTlM+Mi42NDwvdkNPRklOUz48L0NPRklOU0FsaXE+PC9DT0ZJTlM+PC9pbXBvc3RvPjxpbmZBZFByb2Q+MjAwPC9pbmZBZFByb2Q+PC9kZXQ+PGRldCBuSXRlbT0iMiI+PHByb2Q+PGNQcm9kPjIwODYwMDIxNDI8L2NQcm9kPjxjRUFOPjc4OTk1NjM5MDc3MTk8L2NFQU4+PHhQcm9kPkNBQk8gUFAgUFJFVE8gNTAwViAzWDIsNTBNTSBDT1JGSU88L3hQcm9kPjxOQ00+ODU0NDQ5MDA8L05DTT48Q0VTVD4xMjAwNzAwPC9DRVNUPjxpbmRFc2NhbGE+UzwvaW5kRXNjYWxhPjxDRk9QPjU0MDU8L0NGT1A+PHVDb20+TVQ8L3VDb20+PHFDb20+NTAuMDAwMDwvcUNvbT48dlVuQ29tPjguNTYwMDAwMDAwMDwvdlVuQ29tPjx2UHJvZD40MjguMDA8L3ZQcm9kPjxjRUFOVHJpYj43ODk5NTYzOTA3NzE5PC9jRUFOVHJpYj48dVRyaWI+TVQ8L3VUcmliPjxxVHJpYj41MC4wMDAwPC9xVHJpYj48dlVuVHJpYj44LjU2MDAwMDAwMDA8L3ZVblRyaWI+PGluZFRvdD4xPC9pbmRUb3Q+PC9wcm9kPjxpbXBvc3RvPjx2VG90VHJpYj4xMzQuNjE8L3ZUb3RUcmliPjxJQ01TPjxJQ01TNjA+PG9yaWc+MDwvb3JpZz48Q1NUPjYwPC9DU1Q+PHZCQ1NUUmV0PjQxOC41MjwvdkJDU1RSZXQ+PHBTVD4xOC4wMDwvcFNUPjx2SUNNU1N1YnN0aXR1dG8+NTMuMDU8L3ZJQ01TU3Vic3RpdHV0bz48dklDTVNTVFJldD4yMi4yODwvdklDTVNTVFJldD48L0lDTVM2MD48L0lDTVM+PElQST48Y0VucT45OTk8L2NFbnE+PElQSU5UPjxDU1Q+NTM8L0NTVD48L0lQSU5UPjwvSVBJPjxQSVM+PFBJU0FsaXE+PENTVD4wMTwvQ1NUPjx2QkM+NDI4LjAwPC92QkM+PHBQSVM+MS42NTwvcFBJUz48dlBJUz43LjA2PC92UElTPjwvUElTQWxpcT48L1BJUz48Q09GSU5TPjxDT0ZJTlNBbGlxPjxDU1Q+MDE8L0NTVD48dkJDPjQyOC4wMDwvdkJDPjxwQ09GSU5TPjcuNjA8L3BDT0ZJTlM+PHZDT0ZJTlM+MzIuNTM8L3ZDT0ZJTlM+PC9DT0ZJTlNBbGlxPjwvQ09GSU5TPjwvaW1wb3N0bz48aW5mQWRQcm9kPkIwMTY3Ti1QVDwvaW5mQWRQcm9kPjwvZGV0PjxkZXQgbkl0ZW09IjMiPjxwcm9kPjxjUHJvZD42MzY2NzAwMzU0PC9jUHJvZD48Y0VBTj43ODk2MDM5NzE0ODE5PC9jRUFOPjx4UHJvZD5QTFVHIDJQK1QgMTBBIDkwLiBDWiAxNDgxIEZBTUU8L3hQcm9kPjxOQ00+ODUzNjkwOTA8L05DTT48Q0VTVD4xMjAwNDAwPC9DRVNUPjxpbmRFc2NhbGE+UzwvaW5kRXNjYWxhPjxDRk9QPjU0MDU8L0NGT1A+PHVDb20+UEM8L3VDb20+PHFDb20+Mi4wMDAwPC9xQ29tPjx2VW5Db20+OC41MDAwMDAwMDAwPC92VW5Db20+PHZQcm9kPjE3LjAwPC92UHJvZD48Y0VBTlRyaWI+Nzg5NjAzOTcxNDgxOTwvY0VBTlRyaWI+PHVUcmliPlBDPC91VHJpYj48cVRyaWI+Mi4wMDAwPC9xVHJpYj48dlVuVHJpYj44LjUwMDAwMDAwMDA8L3ZVblRyaWI+PGluZFRvdD4xPC9pbmRUb3Q+PC9wcm9kPjxpbXBvc3RvPjx2VG90VHJpYj42LjEyPC92VG90VHJpYj48SUNNUz48SUNNUzYwPjxvcmlnPjA8L29yaWc+PENTVD42MDwvQ1NUPjx2QkNTVFJldD4xNi43OTwvdkJDU1RSZXQ+PHBTVD4xOC4wMDwvcFNUPjx2SUNNU1N1YnN0aXR1dG8+MS44NDwvdklDTVNTdWJzdGl0dXRvPjx2SUNNU1NUUmV0PjEuMTk8L3ZJQ01TU1RSZXQ+PC9JQ01TNjA+PC9JQ01TPjxJUEk+PGNFbnE+OTk5PC9jRW5xPjxJUElOVD48Q1NUPjUzPC9DU1Q+PC9JUElOVD48L0lQST48UElTPjxQSVNBbGlxPjxDU1Q+MDE8L0NTVD48dkJDPjE3LjAwPC92QkM+PHBQSVM+MS42NTwvcFBJUz48dlBJUz4wLjI4PC92UElTPjwvUElTQWxpcT48L1BJUz48Q09GSU5TPjxDT0ZJTlNBbGlxPjxDU1Q+MDE8L0NTVD48dkJDPjE3LjAwPC92QkM+PHBDT0ZJTlM+Ny42MDwvcENPRklOUz48dkNPRklOUz4xLjI5PC92Q09GSU5TPjwvQ09GSU5TQWxpcT48L0NPRklOUz48L2ltcG9zdG8+PGluZkFkUHJvZD4wODMxNDgxMDwvaW5mQWRQcm9kPjwvZGV0PjxkZXQgbkl0ZW09IjQiPjxwcm9kPjxjUHJvZD4yNTExOTAwMzU0PC9jUHJvZD48Y0VBTj43ODk2MDM5NzE3Mjc4PC9jRUFOPjx4UHJvZD5QTFVHIEZFTUVBIDJQK1QgQ1ogMTBBIDE3MjcgRkFNRTwveFByb2Q+PE5DTT44NTM2NjkxMDwvTkNNPjxDRVNUPjEyMDA0MDA8L0NFU1Q+PGluZEVzY2FsYT5TPC9pbmRFc2NhbGE+PENGT1A+NTQwNTwvQ0ZPUD48dUNvbT5QQzwvdUNvbT48cUNvbT4yLjAwMDA8L3FDb20+PHZVbkNvbT4xMS4xNTAwMDAwMDAwPC92VW5Db20+PHZQcm9kPjIyLjMwPC92UHJvZD48Y0VBTlRyaWI+Nzg5NjAzOTcxNzI3ODwvY0VBTlRyaWI+PHVUcmliPlBDPC91VHJpYj48cVRyaWI+Mi4wMDAwPC9xVHJpYj48dlVuVHJpYj4xMS4xNTAwMDAwMDAwPC92VW5UcmliPjxpbmRUb3Q+MTwvaW5kVG90PjwvcHJvZD48aW1wb3N0bz48dlRvdFRyaWI+OC4wMjwvdlRvdFRyaWI+PElDTVM+PElDTVM2MD48b3JpZz4wPC9vcmlnPjxDU1Q+NjA8L0NTVD48dkJDU1RSZXQ+MjAuNzI8L3ZCQ1NUUmV0PjxwU1Q+MTguMDA8L3BTVD48dklDTVNTdWJzdGl0dXRvPjIuMjc8L3ZJQ01TU3Vic3RpdHV0bz48dklDTVNTVFJldD4xLjQ2PC92SUNNU1NUUmV0PjwvSUNNUzYwPjwvSUNNUz48SVBJPjxjRW5xPjk5OTwvY0VucT48SVBJTlQ+PENTVD41MzwvQ1NUPjwvSVBJTlQ+PC9JUEk+PFBJUz48UElTQWxpcT48Q1NUPjAxPC9DU1Q+PHZCQz4yMi4zMDwvdkJDPjxwUElTPjEuNjU8L3BQSVM+PHZQSVM+MC4zNzwvdlBJUz48L1BJU0FsaXE+PC9QSVM+PENPRklOUz48Q09GSU5TQWxpcT48Q1NUPjAxPC9DU1Q+PHZCQz4yMi4zMDwvdkJDPjxwQ09GSU5TPjcuNjA8L3BDT0ZJTlM+PHZDT0ZJTlM+MS42OTwvdkNPRklOUz48L0NPRklOU0FsaXE+PC9DT0ZJTlM+PC9pbXBvc3RvPjxpbmZBZFByb2Q+MDgzMTcyNzA8L2luZkFkUHJvZD48L2RldD48ZGV0IG5JdGVtPSI1Ij48cHJvZD48Y1Byb2Q+MjMyMTEwMDM4NjwvY1Byb2Q+PGNFQU4+Nzg5MDI3NDAwMDcwNjwvY0VBTj48eFByb2Q+UExBQ0EgUFMtMDY2IE1BU0NVTElOTyAxNVgyMENNIEVOQ0FSVDwveFByb2Q+PE5DTT4zOTIwMzAwMDwvTkNNPjxDRVNUPjEwMDA5MDA8L0NFU1Q+PGluZEVzY2FsYT5TPC9pbmRFc2NhbGE+PENGT1A+NTQwNTwvQ0ZPUD48dUNvbT5QQzwvdUNvbT48cUNvbT4zLjAwMDA8L3FDb20+PHZVbkNvbT42LjcwMDAwMDAwMDA8L3ZVbkNvbT48dlByb2Q+MjAuMTA8L3ZQcm9kPjxjRUFOVHJpYj43ODkwMjc0MDAwNzA2PC9jRUFOVHJpYj48dVRyaWI+UEM8L3VUcmliPjxxVHJpYj4zLjAwMDA8L3FUcmliPjx2VW5UcmliPjYuNzAwMDAwMDAwMDwvdlVuVHJpYj48aW5kVG90PjE8L2luZFRvdD48L3Byb2Q+PGltcG9zdG8+PHZUb3RUcmliPjYuNzQ8L3ZUb3RUcmliPjxJQ01TPjxJQ01TNjA+PG9yaWc+MDwvb3JpZz48Q1NUPjYwPC9DU1Q+PHZCQ1NUUmV0PjAuMDA8L3ZCQ1NUUmV0PjxwU1Q+MTguMDA8L3BTVD48dklDTVNTdWJzdGl0dXRvPjAuMDA8L3ZJQ01TU3Vic3RpdHV0bz48dklDTVNTVFJldD4wLjAwPC92SUNNU1NUUmV0PjwvSUNNUzYwPjwvSUNNUz48SVBJPjxjRW5xPjk5OTwvY0VucT48SVBJTlQ+PENTVD41MzwvQ1NUPjwvSVBJTlQ+PC9JUEk+PFBJUz48UElTQWxpcT48Q1NUPjAxPC9DU1Q+PHZCQz4yMC4xMDwvdkJDPjxwUElTPjEuNjU8L3BQSVM+PHZQSVM+MC4zMzwvdlBJUz48L1BJU0FsaXE+PC9QSVM+PENPRklOUz48Q09GSU5TQWxpcT48Q1NUPjAxPC9DU1Q+PHZCQz4yMC4xMDwvdkJDPjxwQ09GSU5TPjcuNjA8L3BDT0ZJTlM+PHZDT0ZJTlM+MS41MzwvdkNPRklOUz48L0NPRklOU0FsaXE+PC9DT0ZJTlM+PC9pbXBvc3RvPjxpbmZBZFByb2Q+UFM2NjwvaW5mQWRQcm9kPjwvZGV0PjxkZXQgbkl0ZW09IjYiPjxwcm9kPjxjUHJvZD4yMjU4NDAwMzg2PC9jUHJvZD48Y0VBTj43ODkwMjc0MDAwNjkwPC9jRUFOPjx4UHJvZD5QTEFDQSBQUy0wNjUgRkVNSU5JTk8gMTVYMjBDTSBFTkNBUlQ8L3hQcm9kPjxOQ00+MzkyMDMwMDA8L05DTT48Q0VTVD4xMDAwOTAwPC9DRVNUPjxpbmRFc2NhbGE+UzwvaW5kRXNjYWxhPjxDRk9QPjU0MDU8L0NGT1A+PHVDb20+Q1I8L3VDb20+PHFDb20+My4wMDAwPC9xQ29tPjx2VW5Db20+Ni43MDAwMDAwMDAwPC92VW5Db20+PHZQcm9kPjIwLjEwPC92UHJvZD48Y0VBTlRyaWI+Nzg5MDI3NDAwMDY5MDwvY0VBTlRyaWI+PHVUcmliPkNSPC91VHJpYj48cVRyaWI+My4wMDAwPC9xVHJpYj48dlVuVHJpYj42LjcwMDAwMDAwMDA8L3ZVblRyaWI+PGluZFRvdD4xPC9pbmRUb3Q+PC9wcm9kPjxpbXBvc3RvPjx2VG90VHJpYj42Ljc0PC92VG90VHJpYj48SUNNUz48SUNNUzYwPjxvcmlnPjA8L29yaWc+PENTVD42MDwvQ1NUPjx2QkNTVFJldD4wLjAwPC92QkNTVFJldD48cFNUPjE4LjAwPC9wU1Q+PHZJQ01TU3Vic3RpdHV0bz4wLjAwPC92SUNNU1N1YnN0aXR1dG8+PHZJQ01TU1RSZXQ+MC4wMDwvdklDTVNTVFJldD48L0lDTVM2MD48L0lDTVM+PElQST48Y0VucT45OTk8L2NFbnE+PElQSU5UPjxDU1Q+NTM8L0NTVD48L0lQSU5UPjwvSVBJPjxQSVM+PFBJU0FsaXE+PENTVD4wMTwvQ1NUPjx2QkM+MjAuMTA8L3ZCQz48cFBJUz4xLjY1PC9wUElTPjx2UElTPjAuMzM8L3ZQSVM+PC9QSVNBbGlxPjwvUElTPjxDT0ZJTlM+PENPRklOU0FsaXE+PENTVD4wMTwvQ1NUPjx2QkM+MjAuMTA8L3ZCQz48cENPRklOUz43LjYwPC9wQ09GSU5TPjx2Q09GSU5TPjEuNTM8L3ZDT0ZJTlM+PC9DT0ZJTlNBbGlxPjwvQ09GSU5TPjwvaW1wb3N0bz48aW5mQWRQcm9kPlBTNjU8L2luZkFkUHJvZD48L2RldD48ZGV0IG5JdGVtPSI3Ij48cHJvZD48Y1Byb2Q+MTk2MzEwMDIwMTwvY1Byb2Q+PGNFQU4+Nzg5NjU1MTMwMjA5OTwvY0VBTj48eFByb2Q+UEFTVEEgU09MREEgMTEwRyBCRVNUPC94UHJvZD48TkNNPjM4MTAxMDIwPC9OQ00+PENGT1A+NTEwMjwvQ0ZPUD48dUNvbT5QQzwvdUNvbT48cUNvbT4xLjAwMDA8L3FDb20+PHZVbkNvbT41Ni40MDAwMDAwMDAwPC92VW5Db20+PHZQcm9kPjU2LjQwPC92UHJvZD48Y0VBTlRyaWI+Nzg5NjU1MTMwMjA5OTwvY0VBTlRyaWI+PHVUcmliPlBDPC91VHJpYj48cVRyaWI+MS4wMDAwPC9xVHJpYj48dlVuVHJpYj41Ni40MDAwMDAwMDAwPC92VW5UcmliPjxpbmRUb3Q+MTwvaW5kVG90PjwvcHJvZD48aW1wb3N0bz48dlRvdFRyaWI+MjAuMDg8L3ZUb3RUcmliPjxJQ01TPjxJQ01TMDA+PG9yaWc+NDwvb3JpZz48Q1NUPjAwPC9DU1Q+PG1vZEJDPjM8L21vZEJDPjx2QkM+NTYuNDA8L3ZCQz48cElDTVM+MTguMDA8L3BJQ01TPjx2SUNNUz4xMC4xNTwvdklDTVM+PC9JQ01TMDA+PC9JQ01TPjxJUEk+PGNFbnE+OTk5PC9jRW5xPjxJUElOVD48Q1NUPjUzPC9DU1Q+PC9JUElOVD48L0lQST48UElTPjxQSVNBbGlxPjxDU1Q+MDE8L0NTVD48dkJDPjU2LjQwPC92QkM+PHBQSVM+MS42NTwvcFBJUz48dlBJUz4wLjkzPC92UElTPjwvUElTQWxpcT48L1BJUz48Q09GSU5TPjxDT0ZJTlNBbGlxPjxDU1Q+MDE8L0NTVD48dkJDPjU2LjQwPC92QkM+PHBDT0ZJTlM+Ny42MDwvcENPRklOUz48dkNPRklOUz40LjI5PC92Q09GSU5TPjwvQ09GSU5TQWxpcT48L0NPRklOUz48L2ltcG9zdG8+PC9kZXQ+PGRldCBuSXRlbT0iOCI+PHByb2Q+PGNQcm9kPjE5NjQwMDAyMDE8L2NQcm9kPjxjRUFOPjc4OTY1NTEzMDAxMzI8L2NFQU4+PHhQcm9kPlNPTERBIFRVQk8gQVpVTCAyNUcgQkVTVDwveFByb2Q+PE5DTT44MDAzMDAwMDwvTkNNPjxDRk9QPjUxMDI8L0NGT1A+PHVDb20+UEM8L3VDb20+PHFDb20+MS4wMDAwPC9xQ29tPjx2VW5Db20+MzcuNDUwMDAwMDAwMDwvdlVuQ29tPjx2UHJvZD4zNy40NTwvdlByb2Q+PGNFQU5UcmliPjc4OTY1NTEzMDAxMzI8L2NFQU5UcmliPjx1VHJpYj5QQzwvdVRyaWI+PHFUcmliPjEuMDAwMDwvcVRyaWI+PHZVblRyaWI+MzcuNDUwMDAwMDAwMDwvdlVuVHJpYj48aW5kVG90PjE8L2luZFRvdD48L3Byb2Q+PGltcG9zdG8+PHZUb3RUcmliPjEzLjIzPC92VG90VHJpYj48SUNNUz48SUNNUzAwPjxvcmlnPjQ8L29yaWc+PENTVD4wMDwvQ1NUPjxtb2RCQz4zPC9tb2RCQz48dkJDPjM3LjQ1PC92QkM+PHBJQ01TPjE4LjAwPC9wSUNNUz48dklDTVM+Ni43NDwvdklDTVM+PC9JQ01TMDA+PC9JQ01TPjxJUEk+PGNFbnE+OTk5PC9jRW5xPjxJUElOVD48Q1NUPjUzPC9DU1Q+PC9JUElOVD48L0lQST48UElTPjxQSVNBbGlxPjxDU1Q+MDE8L0NTVD48dkJDPjM3LjQ1PC92QkM+PHBQSVM+MS42NTwvcFBJUz48dlBJUz4wLjYyPC92UElTPjwvUElTQWxpcT48L1BJUz48Q09GSU5TPjxDT0ZJTlNBbGlxPjxDU1Q+MDE8L0NTVD48dkJDPjM3LjQ1PC92QkM+PHBDT0ZJTlM+Ny42MDwvcENPRklOUz48dkNPRklOUz4yLjg1PC92Q09GSU5TPjwvQ09GSU5TQWxpcT48L0NPRklOUz48L2ltcG9zdG8+PC9kZXQ+PGRldCBuSXRlbT0iOSI+PHByb2Q+PGNQcm9kPjIyNzEwMzkxNDM8L2NQcm9kPjxjRUFOPjc4OTY1NjU4NTAwMzY8L2NFQU4+PHhQcm9kPlBMQUNBIE1PRFVMQVIgNFgyIENFR0EgQlJBTkNBIEFMVU1CUkEgUFJPPC94UHJvZD48TkNNPjM5MjU5MDkwPC9OQ00+PENGT1A+NTEwMjwvQ0ZPUD48dUNvbT5QQzwvdUNvbT48cUNvbT40LjAwMDA8L3FDb20+PHZVbkNvbT42LjQ2MDAwMDAwMDA8L3ZVbkNvbT48dlByb2Q+MjUuODQ8L3ZQcm9kPjxjRUFOVHJpYj43ODk2NTY1ODUwMDM2PC9jRUFOVHJpYj48dVRyaWI+UEM8L3VUcmliPjxxVHJpYj40LjAwMDA8L3FUcmliPjx2VW5UcmliPjYuNDYwMDAwMDAwMDwvdlVuVHJpYj48aW5kVG90PjE8L2luZFRvdD48bkZDST5BMkU5RTdEQS05M0RELTRBMjMtOTkyMy02NkE4Mzc3NkQ0MjY8L25GQ0k+PC9wcm9kPjxpbXBvc3RvPjx2VG90VHJpYj4xMC42NzwvdlRvdFRyaWI+PElDTVM+PElDTVMwMD48b3JpZz41PC9vcmlnPjxDU1Q+MDA8L0NTVD48bW9kQkM+MzwvbW9kQkM+PHZCQz4yNS44NDwvdkJDPjxwSUNNUz4xOC4wMDwvcElDTVM+PHZJQ01TPjQuNjU8L3ZJQ01TPjwvSUNNUzAwPjwvSUNNUz48SVBJPjxjRW5xPjk5OTwvY0VucT48SVBJTlQ+PENTVD41MzwvQ1NUPjwvSVBJTlQ+PC9JUEk+PFBJUz48UElTQWxpcT48Q1NUPjAxPC9DU1Q+PHZCQz4yNS44NDwvdkJDPjxwUElTPjEuNjU8L3BQSVM+PHZQSVM+MC40MzwvdlBJUz48L1BJU0FsaXE+PC9QSVM+PENPRklOUz48Q09GSU5TQWxpcT48Q1NUPjAxPC9DU1Q+PHZCQz4yNS44NDwvdkJDPjxwQ09GSU5TPjcuNjA8L3BDT0ZJTlM+PHZDT0ZJTlM+MS45NjwvdkNPRklOUz48L0NPRklOU0FsaXE+PC9DT0ZJTlM+PC9pbXBvc3RvPjxpbmZBZFByb2Q+ODUwMDM8L2luZkFkUHJvZD48L2RldD48ZGV0IG5JdGVtPSIxMCI+PHByb2Q+PGNQcm9kPjIyODE3MzkxNDM8L2NQcm9kPjxjRUFOPjc4OTY1NjU4NTAwMDU8L2NFQU4+PHhQcm9kPlBMQUNBIE1PRFVMQVIgNFgyIEhPUiBCUkFOQ0EgQUxVTUJSQSBQUk88L3hQcm9kPjxOQ00+MzkyNTkwOTA8L05DTT48Q0ZPUD41MTAyPC9DRk9QPjx1Q29tPlBDPC91Q29tPjxxQ29tPjQuMDAwMDwvcUNvbT48dlVuQ29tPjYuNDYwMDAwMDAwMDwvdlVuQ29tPjx2UHJvZD4yNS44NDwvdlByb2Q+PGNFQU5UcmliPjc4OTY1NjU4NTAwMDU8L2NFQU5UcmliPjx1VHJpYj5QQzwvdVRyaWI+PHFUcmliPjQuMDAwMDwvcVRyaWI+PHZVblRyaWI+Ni40NjAwMDAwMDAwPC92VW5UcmliPjxpbmRUb3Q+MTwvaW5kVG90PjxuRkNJPjdDREU3QkNDLUVGMEUtNDIxRC1BQTVBLTU1MkIyQ0FEM0U5MzwvbkZDST48L3Byb2Q+PGltcG9zdG8+PHZUb3RUcmliPjEwLjY3PC92VG90VHJpYj48SUNNUz48SUNNUzAwPjxvcmlnPjU8L29yaWc+PENTVD4wMDwvQ1NUPjxtb2RCQz4zPC9tb2RCQz48dkJDPjI1Ljg0PC92QkM+PHBJQ01TPjE4LjAwPC9wSUNNUz48dklDTVM+NC42NTwvdklDTVM+PC9JQ01TMDA+PC9JQ01TPjxJUEk+PGNFbnE+OTk5PC9jRW5xPjxJUElOVD48Q1NUPjUzPC9DU1Q+PC9JUElOVD48L0lQST48UElTPjxQSVNBbGlxPjxDU1Q+MDE8L0NTVD48dkJDPjI1Ljg0PC92QkM+PHBQSVM+MS42NTwvcFBJUz48dlBJUz4wLjQzPC92UElTPjwvUElTQWxpcT48L1BJUz48Q09GSU5TPjxDT0ZJTlNBbGlxPjxDU1Q+MDE8L0NTVD48dkJDPjI1Ljg0PC92QkM+PHBDT0ZJTlM+Ny42MDwvcENPRklOUz48dkNPRklOUz4xLjk2PC92Q09GSU5TPjwvQ09GSU5TQWxpcT48L0NPRklOUz48L2ltcG9zdG8+PGluZkFkUHJvZD44NTAwMDwvaW5mQWRQcm9kPjwvZGV0PjxkZXQgbkl0ZW09IjExIj48cHJvZD48Y1Byb2Q+MjY0NDMwMDA0NTwvY1Byb2Q+PGNFQU4+U0VNIEdUSU48L2NFQU4+PHhQcm9kPkZJVEEgRFVQTEEgRkFDRSBGSVhBIEZPUlRFIFZIQiAxNVgzMyAzTTwveFByb2Q+PE5DTT4zNTA2MTA5MDwvTkNNPjxDRk9QPjUxMDI8L0NGT1A+PHVDb20+Ukw8L3VDb20+PHFDb20+MS4wMDAwPC9xQ29tPjx2VW5Db20+NjYuODUwMDAwMDAwMDwvdlVuQ29tPjx2UHJvZD42Ni44NTwvdlByb2Q+PGNFQU5UcmliPlNFTSBHVElOPC9jRUFOVHJpYj48dVRyaWI+Ukw8L3VUcmliPjxxVHJpYj4xLjAwMDA8L3FUcmliPjx2VW5UcmliPjY2Ljg1MDAwMDAwMDA8L3ZVblRyaWI+PGluZFRvdD4xPC9pbmRUb3Q+PG5GQ0k+RjE0RUREODgtMjY4OC00NjU2LTgyNzItRTE1OUEzMkZERjQ4PC9uRkNJPjwvcHJvZD48aW1wb3N0bz48dlRvdFRyaWI+MjMuOTc8L3ZUb3RUcmliPjxJQ01TPjxJQ01TMDA+PG9yaWc+NTwvb3JpZz48Q1NUPjAwPC9DU1Q+PG1vZEJDPjM8L21vZEJDPjx2QkM+NjYuODU8L3ZCQz48cElDTVM+MTguMDA8L3BJQ01TPjx2SUNNUz4xMi4wMzwvdklDTVM+PC9JQ01TMDA+PC9JQ01TPjxJUEk+PGNFbnE+OTk5PC9jRW5xPjxJUElOVD48Q1NUPjUzPC9DU1Q+PC9JUElOVD48L0lQST48UElTPjxQSVNBbGlxPjxDU1Q+MDE8L0NTVD48dkJDPjY2Ljg1PC92QkM+PHBQSVM+MS42NTwvcFBJUz48dlBJUz4xLjEwPC92UElTPjwvUElTQWxpcT48L1BJUz48Q09GSU5TPjxDT0ZJTlNBbGlxPjxDU1Q+MDE8L0NTVD48dkJDPjY2Ljg1PC92QkM+PHBDT0ZJTlM+Ny42MDwvcENPRklOUz48dkNPRklOUz41LjA4PC92Q09GSU5TPjwvQ09GSU5TQWxpcT48L0NPRklOUz48L2ltcG9zdG8+PGluZkFkUHJvZD5IQjAwNDY2OTgzMjwvaW5mQWRQcm9kPjwvZGV0PjxkZXQgbkl0ZW09IjEyIj48cHJvZD48Y1Byb2Q+MTUwNTYzOTE0MzwvY1Byb2Q+PGNFQU4+Nzg5NjU2NTg1MDEyODwvY0VBTj48eFByb2Q+TU9EVUxPIFBBUkFMRUxPIDI1MFYgMTBBIEJSQU5DTyBBTFVNQlJBIFBSTzwveFByb2Q+PE5DTT44NTM2NTA5MDwvTkNNPjxDRVNUPjEyMDA0MDA8L0NFU1Q+PGluZEVzY2FsYT5TPC9pbmRFc2NhbGE+PENGT1A+NTQwNTwvQ0ZPUD48dUNvbT5QQzwvdUNvbT48cUNvbT40LjAwMDA8L3FDb20+PHZVbkNvbT4xMi4wMzAwMDAwMDAwPC92VW5Db20+PHZQcm9kPjQ4LjEyPC92UHJvZD48Y0VBTlRyaWI+Nzg5NjU2NTg1MDEyODwvY0VBTlRyaWI+PHVUcmliPlBDPC91VHJpYj48cVRyaWI+NC4wMDAwPC9xVHJpYj48dlVuVHJpYj4xMi4wMzAwMDAwMDAwPC92VW5UcmliPjxpbmRUb3Q+MTwvaW5kVG90PjxuRkNJPkI1NkFGQUM4LTcyNkYtNDQ1OS1BRTE5LTg4QzEwNzdFMUY4MjwvbkZDST48L3Byb2Q+PGltcG9zdG8+PHZUb3RUcmliPjI0LjEyPC92VG90VHJpYj48SUNNUz48SUNNUzYwPjxvcmlnPjU8L29yaWc+PENTVD42MDwvQ1NUPjx2QkNTVFJldD40NC40MjwvdkJDU1RSZXQ+PHBTVD4xOC4wMDwvcFNUPjx2SUNNU1N1YnN0aXR1dG8+NS4xNjwvdklDTVNTdWJzdGl0dXRvPjx2SUNNU1NUUmV0PjIuODM8L3ZJQ01TU1RSZXQ+PC9JQ01TNjA+PC9JQ01TPjxJUEk+PGNFbnE+OTk5PC9jRW5xPjxJUElOVD48Q1NUPjUzPC9DU1Q+PC9JUElOVD48L0lQST48UElTPjxQSVNBbGlxPjxDU1Q+MDE8L0NTVD48dkJDPjQ4LjEyPC92QkM+PHBQSVM+MS42NTwvcFBJUz48dlBJUz4wLjc5PC92UElTPjwvUElTQWxpcT48L1BJUz48Q09GSU5TPjxDT0ZJTlNBbGlxPjxDU1Q+MDE8L0NTVD48dkJDPjQ4LjEyPC92QkM+PHBDT0ZJTlM+Ny42MDwvcENPRklOUz48dkNPRklOUz4zLjY2PC92Q09GSU5TPjwvQ09GSU5TQWxpcT48L0NPRklOUz48L2ltcG9zdG8+PGluZkFkUHJvZD44NTAxMjwvaW5mQWRQcm9kPjwvZGV0PjxkZXQgbkl0ZW09IjEzIj48cHJvZD48Y1Byb2Q+NTMyMDAzOTE0MzwvY1Byb2Q+PGNFQU4+Nzg5NjU2NTg1MDMzMzwvY0VBTj48eFByb2Q+Q09OSlVOVE8gNFgyIElOVC5TSU1QTEVTIDEwQSAyNTBWIEFMVU1CUkEgQlIgUFJPPC94UHJvZD48TkNNPjg1MzY1MDkwPC9OQ00+PENFU1Q+MTIwMDQwMDwvQ0VTVD48aW5kRXNjYWxhPlM8L2luZEVzY2FsYT48Q0ZPUD41NDA1PC9DRk9QPjx1Q29tPlBDPC91Q29tPjxxQ29tPjQuMDAwMDwvcUNvbT48dlVuQ29tPjEwLjg1MDAwMDAwMDA8L3ZVbkNvbT48dlByb2Q+NDMuNDA8L3ZQcm9kPjxjRUFOVHJpYj43ODk2NTY1ODUwMzMzPC9jRUFOVHJpYj48dVRyaWI+UEM8L3VUcmliPjxxVHJpYj40LjAwMDA8L3FUcmliPjx2VW5UcmliPjEwLjg1MDAwMDAwMDA8L3ZVblRyaWI+PGluZFRvdD4xPC9pbmRUb3Q+PG5GQ0k+RTc4NUI3NTktMkIwRi00RUEyLThFRTQtQzAwNDZBRTU2MDM3PC9uRkNJPjwvcHJvZD48aW1wb3N0bz48dlRvdFRyaWI+MjEuNzU8L3ZUb3RUcmliPjxJQ01TPjxJQ01TNjA+PG9yaWc+NTwvb3JpZz48Q1NUPjYwPC9DU1Q+PHZCQ1NUUmV0PjQwLjA4PC92QkNTVFJldD48cFNUPjE4LjAwPC9wU1Q+PHZJQ01TU3Vic3RpdHV0bz40LjY2PC92SUNNU1N1YnN0aXR1dG8+PHZJQ01TU1RSZXQ+Mi41NjwvdklDTVNTVFJldD48L0lDTVM2MD48L0lDTVM+PElQST48Y0VucT45OTk8L2NFbnE+PElQSU5UPjxDU1Q+NTM8L0NTVD48L0lQSU5UPjwvSVBJPjxQSVM+PFBJU0FsaXE+PENTVD4wMTwvQ1NUPjx2QkM+NDMuNDA8L3ZCQz48cFBJUz4xLjY1PC9wUElTPjx2UElTPjAuNzI8L3ZQSVM+PC9QSVNBbGlxPjwvUElTPjxDT0ZJTlM+PENPRklOU0FsaXE+PENTVD4wMTwvQ1NUPjx2QkM+NDMuNDA8L3ZCQz48cENPRklOUz43LjYwPC9wQ09GSU5TPjx2Q09GSU5TPjMuMzA8L3ZDT0ZJTlM+PC9DT0ZJTlNBbGlxPjwvQ09GSU5TPjwvaW1wb3N0bz48aW5mQWRQcm9kPjg1MDMzPC9pbmZBZFByb2Q+PC9kZXQ+PHRvdGFsPjxJQ01TVG90Pjx2QkM+MjQ3LjE4PC92QkM+PHZJQ01TPjQ0LjQ4PC92SUNNUz48dklDTVNEZXNvbj4wLjAwPC92SUNNU0Rlc29uPjx2RkNQPjAuMDA8L3ZGQ1A+PHZCQ1NUPjAuMDA8L3ZCQ1NUPjx2U1Q+MC4wMDwvdlNUPjx2RkNQU1Q+MC4wMDwvdkZDUFNUPjx2RkNQU1RSZXQ+MC4wMDwvdkZDUFNUUmV0Pjx2UHJvZD44NDYuMjA8L3ZQcm9kPjx2RnJldGU+MC4wMDwvdkZyZXRlPjx2U2VnPjAuMDA8L3ZTZWc+PHZEZXNjPjAuMDA8L3ZEZXNjPjx2SUk+MC4wMDwvdklJPjx2SVBJPjAuMDA8L3ZJUEk+PHZJUElEZXZvbD4wLjAwPC92SVBJRGV2b2w+PHZQSVM+MTMuOTY8L3ZQSVM+PHZDT0ZJTlM+NjQuMzE8L3ZDT0ZJTlM+PHZPdXRybz4wLjAwPC92T3V0cm8+PHZORj44NDYuMjA8L3ZORj48dlRvdFRyaWI+Mjk3LjY2PC92VG90VHJpYj48L0lDTVNUb3Q+PC90b3RhbD48dHJhbnNwPjxtb2RGcmV0ZT45PC9tb2RGcmV0ZT48L3RyYW5zcD48cGFnPjxkZXRQYWc+PHRQYWc+MDM8L3RQYWc+PHZQYWc+ODQ2LjIwPC92UGFnPjxjYXJkPjx0cEludGVncmE+MTwvdHBJbnRlZ3JhPjxDTlBKPjAxMDI3MDU4MDAwMTkxPC9DTlBKPjx0QmFuZD4wMzwvdEJhbmQ+PGNBdXQ+MDAwNTAwMjAzPC9jQXV0PjwvY2FyZD48L2RldFBhZz48L3BhZz48aW5mQWRpYz48aW5mQ3BsPkVNUFJFU0E6IDAwMSAgVkVOREVET1I6IDEwMjAgSk9TRSBQSVJFUyBEQSBTSUxWQSBGSUxITyAgRk9STUEgUEFHQU1FTlRPOiBDVC1DQVJUQU8gIElDTVMgcmVjb2xoaWRvIGFudGVyaW9ybWVudGUgcG9yIFN1Yi5UcmliLiBDb25mLiBBUlQuMzEzLVkgZG8gUklDTVMvU1AuICBWQUwgQVBST1ggVFJJQlVUT1M6IFIkIDI5Nyw2NyBQRVJDRU5UVUFMOiAzNSwxOCUgIFRSSUIgQVBST1ggUiQ6IDE1MCw4NCBGRUQgMTQ2LDgxIEVTVCAgRk9OVEU6IElCUFQvZW1wcmVzb21ldHJvLmNvbS5icjwvaW5mQ3BsPjwvaW5mQWRpYz48aW5mUmVzcFRlYz48Q05QSj43MTgyNzM0OTAwMDE0MDwvQ05QSj48eENvbnRhdG8+U0hYIElORk9STcOBVElDQSBMVERBPC94Q29udGF0bz48ZW1haWw+c2h4QHNoeC5jb20uYnI8L2VtYWlsPjxmb25lPjAxNjMzMzE2NTcwPC9mb25lPjwvaW5mUmVzcFRlYz48L2luZk5GZT48U2lnbmF0dXJlIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjIj48U2lnbmVkSW5mbz48Q2Fub25pY2FsaXphdGlvbk1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnL1RSLzIwMDEvUkVDLXhtbC1jMTRuLTIwMDEwMzE1I'.
            'i8+PFNpZ25hdHVyZU1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyNyc2Etc2hhMSIvPjxSZWZlcmVuY2UgVVJJPSIjTkZlMzUyNTA2MDA0OTU1OTMwMDAxMDQ1NTAwMTAwMDI4NDkzNDEwMTI4MDExNjAiPjxUcmFuc2Zvcm1zPjxUcmFuc2Zvcm0gQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjZW52ZWxvcGVkLXNpZ25hdHVyZSIvPjxUcmFuc2Zvcm0gQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy14bWwtYzE0bi0yMDAxMDMxNSIvPjwvVHJhbnNmb3Jtcz48RGlnZXN0TWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI3NoYTEiLz48RGlnZXN0VmFsdWU+Z0xpUmlXVnBYalNBcUpvRWZPcm04bVpjNENBPTwvRGlnZXN0VmFsdWU+PC9SZWZlcmVuY2U+PC9TaWduZWRJbmZvPjxTaWduYXR1cmVWYWx1ZT5kNHdLTTBINE4vajBicGlNZFpja3lMYVRXcE5RY083MHN1NFpRMW5jZFp1eHJiSjZjSG5wNGd1bmJUQ1ZGWXI5aTdXT1RGOXNQQ3ppJiMxMzsKNGdQQzRKQXdScEE2S2d2NGVDYzBQQjFWWWxyZ1FMVy83R3NkQkswVGxzUkZkTUl5VEhNc0tmaUtQTnBZbUtCZ1hJaFR4bjlwTlpMZiYjMTM7CkNSTng4NmhCaUdLMjVMekQrV2p5cXlYL2tTWExQUlBiRHZaOG5zNDN6TmZSSi9HUmVZcHc4a283V3l3L1VVcW1ocG9VMDA0SFl1RHUmIzEzOwpBazBoTC9TbkgvSmpoZ09xbGpqbzh6R0QvQ01ybTNOUU5VWW95dHp4VElEQzh0R2NRS1NBUVc5ZG9XVDMwRjc4a1NEaENncjNpMDNtJiMxMzsKRWVGZFZUMnhmTklRUVlUMGVvcTNoblJpZkhpUXJ1c2JVcXJ3WFE9PTwvU2lnbmF0dXJlVmFsdWU+PEtleUluZm8+PFg1MDlEYXRhPjxYNTA5Q2VydGlmaWNhdGU+TUlJSGJqQ0NCVmFnQXdJQkFnSU1WNXlCcDlheks3eEw3TTFETUEwR0NTcUdTSWIzRFFFQkN3VUFNSUdPTVFzd0NRWURWUVFHRXdKQyYjMTM7ClVqRVRNQkVHQTFVRUNnd0tTVU5RTFVKeVlYTnBiREU3TURrR0ExVUVDd3d5VTJWeWRtbGpieUJHWldSbGNtRnNJR1JsSUZCeWIyTmwmIzEzOwpjM05oYldWdWRHOGdaR1VnUkdGa2IzTWdMU0JUUlZKUVVrOHhMVEFyQmdOVkJBTU1KRUYxZEc5eWFXUmhaR1VnUTJWeWRHbG1hV05oJiMxMzsKWkc5eVlTQkJURlJGVWs1QlZFbFdSVEFlRncweU5ERXlNRGt4TXpVNU1qQmFGdzB5TlRFeU1Ea3hNelU1TWpCYU1JSUJDakVMTUFrRyYjMTM7CkExVUVCaE1DUWxJeEN6QUpCZ05WQkFnTUFsTlFNUkl3RUFZRFZRUUhEQWxIVlVGU1ZVeElUMU14RXpBUkJnTlZCQW9NQ2tsRFVDMUMmIzEzOwpjbUZ6YVd3eEdUQVhCZ05WQkFzTUVIWnBaR1Z2WTI5dVptVnlaVzVqYVdFeEZ6QVZCZ05WQkFzTURqSTVOVGMwTkRneU1EQXdNVGMxJiMxMzsKTVJzd0dRWURWUVFMREJKUVpYTnpiMkVnU25WeWFXUnBZMkVnUVRFeEVUQVBCZ05WQkFzTUNFRlNTRVZNVUVWU01TMHdLd1lEVlFRTCYjMTM7CkRDUkJkWFJ2Y21sa1lXUmxJRU5sY25ScFptbGpZV1J2Y21FZ1FVeFVSVkpPUVZSSlZrVXhNakF3QmdOVkJBTU1LVFJCSUVOUFRVVlMmIzEzOwpRMGxCVENCRlRFVlVVa2xEUVNCTVZFUkJPakF3TkRrMU5Ua3pNREF3TVRBME1JSUJJakFOQmdrcWhraUc5dzBCQVFFRkFBT0NBUThBJiMxMzsKTUlJQkNnS0NBUUVBa0tIL2p5MzlPYlNCL253TGJLZ2dvUlJIbTViKzcxQkR4eEtYYVdMTGFTdzFkcHNHRTJEWFd2bFVjd2NvUU1oYiYjMTM7Ckh6Zmp4cGRGa1EzTkxHVTdVMnF1cTJUdjFoWkRMTm16bWJ0Zk42K01URjRPa0NQRmZzemhGNkoyQy91RVhqTW1wZlpyam01SWFGVEomIzEzOwpRNnhyTmMxZXJYNWFlZWhTc3BONm03TGZuVWY4MDNPd2Jsd2VvTVZnbllhSU9LU0ZId2k2Y084eVJrc1dnVGZyVkhOdzhqQXZiazQ4JiMxMzsKQmltbGpOME1GRVFtNmcxcGw5Q0dlRVRPdjNYR0dlQ0Flc29aT0FkTlBqZFpiZ1ZhUWRucGNkbTltU3prUEZzQ0NPYU5wRHUrL21zYSYjMTM7CmxVbDBSRlRDMURiOUErVHczbklYQ1ptd25sbGw2dWZyNGNqQ0VKcXlJay9HeVVNazFRSURBUUFCbzRJQ1N6Q0NBa2N3SHdZRFZSMGomIzEzOwpCQmd3Rm9BVWxReHMvdzFTSTZWWTc1SWlUWTdaTCtYb29nd3dnWWdHQTFVZEh3U0JnREIrTUR5Z09xQTRoalpvZEhSd09pOHZjbVZ3JiMxMzsKYjNOcGRHOXlhVzh1YzJWeWNISnZMbWR2ZGk1aWNpOXNZM0l2WVdOaGJIUmxjbTVoZEdsMlpTNWpjbXd3UHFBOG9EcUdPR2gwZEhBNiYjMTM7Ckx5OWpaWEowYVdacFkyRmtiM015TG5ObGNuQnlieTVuYjNZdVluSXZiR055TDJGallXeDBaWEp1WVhScGRtVXVZM0pzTUZZR0NDc0cmIzEzOwpBUVVGQndFQkJFb3dTREJHQmdnckJnRUZCUWN3QW9ZNmFIUjBjRG92TDNKbGNHOXphWFJ2Y21sdkxuTmxjbkJ5Ynk1bmIzWXVZbkl2JiMxMzsKWTJGa1pXbGhjeTloWTJGc2RHVnlibUYwYVhabExuQTNZakNCc2dZRFZSMFJCSUdxTUlHbm9EZ0dCV0JNQVFNRW9DOEVMVEV4TURJeCYjMTM7Ck9UVXlOVGczTXpRek9EUTRNRFF3TURBd01EQXdNREF3TURBd01EQXdNREF3TURBd01EQXdNS0FmQmdWZ1RBRURBcUFXQkJSQlRrbE0mIzEzOwpWRTlPSUVwUFUwVWdSRUVnUTFKVldxQVpCZ1ZnVEFFREE2QVFCQTR3TURRNU5UVTVNekF3TURFd05LQVhCZ1ZnVEFFREI2QU9CQXd3JiMxMzsKTURBd01EQXdNREF3TURDQkZteHBZMkZBTkdGbGJHVjBjbWxqWVM1amIyMHVZbkl3RGdZRFZSMFBBUUgvQkFRREFnWGdNQjBHQTFVZCYjMTM7CkpRUVdNQlFHQ0NzR0FRVUZCd01DQmdnckJnRUZCUWNEQkRCZEJnTlZIU0FFVmpCVU1GSUdCbUJNQVFJQmVqQklNRVlHQ0NzR0FRVUYmIzEzOwpCd0lCRmpwb2RIUndPaTh2Y21Wd2IzTnBkRzl5YVc4dWMyVnljSEp2TG1kdmRpNWljaTlrYjJOekwyUndZMkZqWVd4MFpYSnVZWFJwJiMxMzsKZG1VdWNHUm1NQTBHQ1NxR1NJYjNEUUVCQ3dVQUE0SUNBUUNhWHlmd1dRbGFib0Yra253MGZsR2Rnd0Nna0l4cnQ5R1NBczl5TFI5QiYjMTM7CnJpQmRCaG9JTmNEV25vVHBVYXdSL2tIdmJpOTJvNlRCOU5WM0txS3lqdytYcndndExmOFB0LzBUWDE2ZXBRbTVSUDNuQ01ycXRoeGUmIzEzOwpBalJwQnNxTUwvSDM1blhyNmU4NTJDcTJLZXZCaGNyb1ZhOThqMHdMcWxIUk5yb1RzUWNjS29LcEtuSXNSVk1sVnhMNnFIQUl5Sk90JiMxMzsKeTZVbVg0MFl0cWx5bkhTVEpXdUs3MjlKYmRYTjY0TnR2UjRNcml4b2hBcXJBY1QzNHNoanZCa1VNZHlZRWpkRjA2SUVBVUpsbzlTUyYjMTM7Cmt2Z0pRRlE3UEsxc2RRNE9nRlZrOFdKVDllZ2RicHJzZU0ya0E2K2tYZ3VlRjc2TFk2RU9oODZNNjJpTHZRYkVOY0NPNyt1WE02dUomIzEzOwovVUNxaFd6TTVNWVlaZXl3NEpLclpKREtOU25ORGdVN1ZDUzZJMStEQXlTWG5xaERKN3BtTWI2dnk1MEpEaThJR05UczMwUUcrTmNVJiMxMzsKREt4ODBjbTlHaVdVa0dxaCtUZ2pNdHlxQldoejhRc2I1aUhHc050b05IelQrQldacFZTUGN1VDNQbnE3ZFRZYXNzNjVNTlpjT0xwYSYjMTM7CmVVcFdRd1ZOdGxEcURRejVUOVNVdEkwWGZ2Wm5LbkRhMW1JUXlzZDlOZXJ6QmtReU9hcnlTRTQ2bGNVd1F4angvUlI3Qys3S042Z1QmIzEzOwpEMmEyQUdSUS9QeDNST09Ddk80dVV2Ui9DVW1NWU9oR000MDYyRVRhQjlZb1R2eXpkUkNrL2IyaHR3bktTNks2ci9mMERFaGFHNEZNJiMxMzsKSTJRNjFobnI0QUNNdDJSNGo5b1c0dnpiV0xGem9NTEhIdz09PC9YNTA5Q2VydGlmaWNhdGU+PC9YNTA5RGF0YT48L0tleUluZm8+PC9TaWduYXR1cmU+PC9ORmU+PHByb3RORmUgdmVyc2FvPSI0LjAwIj4KICAgICAgICA8aW5mUHJvdD4KICAgICAgICAgICAgPHRwQW1iPjE8L3RwQW1iPgogICAgICAgICAgICA8dmVyQXBsaWM+U1BfTkZFX1BMMDA5X1Y0PC92ZXJBcGxpYz4KICAgICAgICAgICAgPGNoTkZlPjM1MjUwNjAwNDk1NTkzMDAwMTA0NTUwMDEwMDAyODQ5MzQxMDEyODAxMTYwPC9jaE5GZT4KICAgICAgICAgICAgPGRoUmVjYnRvPjIwMjUtMDYtMDRUMTU6NDE6MTYtMDM6MDA8L2RoUmVjYnRvPgogICAgICAgICAgICA8blByb3Q+MTM1MjUxNTAwNjE1NjAyPC9uUHJvdD4KICAgICAgICAgICAgPGRpZ1ZhbD5nTGlSaVdWcFhqU0FxSm9FZk9ybThtWmM0Q0E9PC9kaWdWYWw+CiAgICAgICAgICAgIDxjU3RhdD4xMDA8L2NTdGF0PgogICAgICAgICAgICA8eE1vdGl2bz5BdXRvcml6YWRvIG8gdXNvIGRhIE5GLWU8L3hNb3Rpdm8+CiAgICAgICAgPC9pbmZQcm90PgogICAgPC9wcm90TkZlPjwvbmZlUHJvYz4='; // base64 de <exemplo><nome>John Doe</nome></exemplo>
            $jsonXml =  Util::base64XmlParaJson($xmlBase64);
            header('Content-Type: application/json; charset=utf-8');
            echo $jsonXml;
            die();

            json::run(true,$jsonXml,"Teste");
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

}
