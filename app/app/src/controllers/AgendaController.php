<?php

namespace App\Controller;

use App\Model\Log;
use App\Utils\Json;
use App\Utils\Util;
use App\Model\Agenda;
use App\Model\Documento;
use App\Model\AgendaParticipante;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class AgendaController extends BaseController {

    public function excluir(Request $request, Response $response, $args)
    {
        try {
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";
            if($pk!=""){
                (new Log($this->pdo))->salvar('agenda',$pk);
                (new Agenda($this->pdo))->excluir($pk);
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

            $doc_agenda = isset($data['doc_agenda'])? $data['doc_agenda'] : "";
            $arrDocAgenda = [];
            if($doc_agenda != ""){
                $arrDocAgenda = json_decode ($doc_agenda, true);
            }
           
            $participantes_agenda = isset($data['participantes_agenda'])? $data['participantes_agenda'] : "";
            $arrParticipanteAgenda = [];
            if($participantes_agenda != ""){
                $arrParticipanteAgenda = json_decode ($participantes_agenda, true);
            }

            $pk = isset($data['pk'])? $data['pk'] : "";
            $tipo_agendas_pk = isset($data['tipo_agendas_pk'])? $data['tipo_agendas_pk'] : "";
            $dt_ini_agenda = isset($data['dt_ini_agenda'])? $data['dt_ini_agenda'] : "";
            $hr_ini_agenda = isset($data['hr_ini_agenda'])? $data['hr_ini_agenda'] : "";
            $dt_fim_agenda = isset($data['dt_fim_agenda'])? $data['dt_fim_agenda'] : "";
            $hr_fim_agenda = isset($data['hr_fim_agenda'])? $data['hr_fim_agenda'] : "";
            $ic_lembrete = isset($data['ic_lembrete'])? $data['ic_lembrete'] : "";
            $ic_repetir = isset($data['ic_repetir'])? $data['ic_repetir'] : "";
            $ds_link_reuniao = isset($data['ds_link_reuniao'])? $data['ds_link_reuniao'] : "";
            $ds_cep = isset($data['ds_cep'])? $data['ds_cep'] : "";
            $ds_endereco = isset($data['ds_endereco'])? $data['ds_endereco'] : "";
            $ds_complemento = isset($data['ds_complemento'])? $data['ds_complemento'] : "";
            $ds_numero = isset($data['ds_numero'])? $data['ds_numero'] : "";
            $ds_bairro = isset($data['ds_bairro'])? $data['ds_bairro'] : "";
            $ds_cidade = isset($data['ds_cidade'])? $data['ds_cidade'] : "";
            $ds_uf = isset($data['ds_uf'])? $data['ds_uf'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $ds_obs = isset($data['obs_classificacao'])? $data['obs_classificacao'] : "";
            $agendas_reagendamento_pk = isset($data['agendas_reagendamento_pk'])? $data['agendas_reagendamento_pk'] : "";
            $ds_obs_reagendamento = isset($data['ds_obs_reagendamento'])? $data['ds_obs_reagendamento'] : "";
            $motivo_cancelameto_pk = isset($data['motivo_cancelameto_pk'])? $data['motivo_cancelameto_pk'] : "";
            $classificacao_pk = isset($data['classificacao_pk'])? $data['classificacao_pk'] : "";
            $obs_classificacao = isset($data['obs_classificacao'])? $data['obs_classificacao'] : "";

            $agenda =[
                "pk"=>$pk,
                "tipo_agendas_pk"=>$tipo_agendas_pk,
                "dt_ini_agenda_ini"=>Util::DataYMD($dt_ini_agenda)." ".$hr_ini_agenda.":00",
                "dt_hr_agenda_fim"=>Util::DataYMD($dt_fim_agenda)." ".$hr_fim_agenda.":00",
                "ic_lembrete"=>$ic_lembrete,
                "ic_repetir"=>$ic_repetir,
                "ds_link_reuniao"=>$ds_link_reuniao,
                "ds_cep"=>$ds_cep,
                "ds_endereco"=>$ds_endereco,
                "ds_complemento"=>$ds_complemento,
                "ds_numero"=>$ds_numero,
                "ds_bairro"=>$ds_bairro,
                "ds_cidade"=>$ds_cidade,
                "ds_uf"=>$ds_uf,
                "leads_pk"=>$leads_pk,
                "ic_status"=>$ic_status,
                "ds_obs"=>$ds_obs,
                "agendas_reagendamento_pk"=>$agendas_reagendamento_pk,
                "ds_obs_reagendamento"=>$ds_obs_reagendamento,
                "motivo_cancelameto_pk"=>$motivo_cancelameto_pk,
                "classificacao_pk"=>$classificacao_pk,
                "obs_classificacao"=>$obs_classificacao,
            ];

            $retorno = (new Agenda($this->pdo))->salvar($agenda);
            
            if(count($arrDocAgenda) > 0){
                for($i = 0; $i < count($arrDocAgenda); $i++){
                    if($arrDocAgenda[$i]['pk_doc_bd']!="Não existem Dados cadastrados"){
                        if($arrDocAgenda[$i]['pk_doc_bd']!="Carregando..."){

                            $documento =[
                                "pk"=>  "",
                                "ds_documento"=>  $arrDocAgenda[$i]['ds_documento'],
                                "ds_nome_original"=>  $arrDocAgenda[$i]['ds_nome_original'],
                                "leads_pk"=>  $leads_pk,
                                "agendas_pk"=>  $retorno->data,
                                "pk_doc_pd"=>  $arrDocAgenda[$i]['pk_doc_bd']
                            ];
                            (new Documento($this->pdo))->salvarDocumentoAgenda($documento);
                        }
                    }
                }
            }

            if(count($arrParticipanteAgenda) > 0){
                for($i = 0; $i < count($arrParticipanteAgenda); $i++){
                    if($arrParticipanteAgenda[$i]['participante_agenda_pk']!="Não existem Dados cadastrados"){
                        if($arrParticipanteAgenda[$i]['participante_agenda_pk']!="Carregando..."){

                            $agendas_participantes =[
                                "pk"=>  $arrParticipanteAgenda[$i]['participante_agenda_pk'],
                                "ds_email"=>  $arrParticipanteAgenda[$i]['ds_email'],
                                "ds_cel"=>  $arrParticipanteAgenda[$i]['ds_cel'],
                                "participante_pk"=>  $arrParticipanteAgenda[$i]['participante_pk'],
                                "ic_tipo_participante"=>  $arrParticipanteAgenda[$i]['ic_tipo_participante'],
                                "agendas_pk"=>  $retorno->data
                            ];



                            (new AgendaParticipante($this->pdo))->salvar($agendas_participantes);
                        }
                    }
                }
            }
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

            $retorno = (new Agenda($this->pdo))->listarPorPk($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarDataTable(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";

            $retorno = (new Agenda($this->pdo))->listarTodosPorLeadsPk($leads_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}

