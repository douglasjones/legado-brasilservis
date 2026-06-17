<?php

namespace App\Controller;

use App\Model\AgendaColaboradorApontamento;
use App\Model\Log;
use App\Utils\Json;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class AgendaColaboradorApontamentoController extends BaseController {
    public function desabilitarApontamento(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();

            $apontamento_pk = isset($data['apontamento_pk']) ? $data['apontamento_pk'] : "";
            if($apontamento_pk!=""){
             
                (new AgendaColaboradorApontamento($this->pdo))->desabilitarApontamento($apontamento_pk);
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
    public function excluir(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk']) ? $data['pk'] : "";
            $tipo_apontamento_pk = isset($data['tipo_apontamento_pk']) ? $data['tipo_apontamento_pk'] : "";
            $apontamento_ponto_pk = isset($data['apontamento_ponto_pk']) ? $data['apontamento_ponto_pk'] : "";

            if($pk!=""){
                (new Log($this->pdo))->salvar('agenda_colaborador_apontamento', $pk);

                (new AgendaColaboradorApontamento($this->pdo))->excluir($pk,$tipo_apontamento_pk, $apontamento_ponto_pk);
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
    public function salvar(Request $request, Response $response, $args){
        try{
            $entity = new AgendaColaboradorApontamento($this->pdo);
            $data = $request->getQueryParams();
        
            $tipo_ponto_pk = isset($data['tipo_ponto_pk'])? $data['tipo_ponto_pk']: "";
            $ds_obs_ponto = isset($data['ds_obs_ponto'])?$data['ds_obs_ponto']: "";
            $hr_sistema = isset($data['hr_sistema'])? $data['hr_sistema'] : "";
            $hr_manual = isset($data['hr_manual'])?$data['hr_manual']: "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $leads_pk = isset($data['leads_pk'])?$data['leads_pk']: "";
            $agenda_colaborador_padrao_pk = isset($data['agenda_colaborador_padrao_pk'])? $data['agenda_colaborador_padrao_pk'] : "";
            $tipo_apontamento_pk = isset($data['tipo_apontamento_pk'])?$data['tipo_apontamento_pk']: "";
            $dt_ponto = isset($data['dt_ponto'])? $data['dt_ponto'] : "";
            $ds_pin = isset($data['ds_pin'])? $data['ds_pin'] : "";
            $dt_apontamento = isset($data['dt_apontamento'])? $data['dt_apontamento'] : "";
            $motivo_falta_pk = isset($data['motivo_falta_pk'])? $data['motivo_falta_pk'] : "";
            $colaborador_cobertura_falta_pk = isset($data['colaborador_cobertura_falta_pk'])? $data['colaborador_cobertura_falta_pk'] : "";
            $ds_obs_falta = isset($data['ds_obs_falta'])? $data['ds_obs_falta'] : "";
            $vl_ft_falta = isset($data['vl_ft_falta'])? $data['vl_ft_falta'] : "";
            $motivo_cobertura_falta_pk = isset($data['motivo_cobertura_falta_pk'])? $data['motivo_cobertura_falta_pk'] : "";
            $dt_falta = isset($data['dt_falta'])? $data['dt_falta'] : "";
            $motivo_ft_pk = isset($data['motivo_ft_pk'])? $data['motivo_ft_pk'] : "";
            $motivo_folga_pk = isset($data['motivo_folga_pk'])? $data['motivo_folga_pk'] : "";
            $lead_cobertura_pk = isset($data['lead_cobertura_pk'])? $data['lead_cobertura_pk'] : "";
            $ds_obs_folga = isset($data['ds_obs_folga'])? $data['ds_obs_folga'] : "";
            $dt_folga = isset($data['dt_folga'])? $data['dt_folga'] : "";
            $vl_ft = isset($data['vl_ft'])? $data['vl_ft'] : "";
            $colaborador_cobertura_troca_escala_pk = isset($data['colaborador_cobertura_troca_escala_pk'])? $data['colaborador_cobertura_troca_escala_pk'] : "";
            $motivos_troca_escala_pk = isset($data['motivos_troca_escala_pk'])? $data['motivos_troca_escala_pk'] : "";
            $ds_obs_troca_escala = isset($data['ds_obs_troca_escala'])? $data['ds_obs_troca_escala'] : "";
            $dt_troca_escala = isset($data['dt_troca_escala'])? $data['dt_troca_escala'] : "";
            $motivo_afastamento_pk = isset($data['motivo_afastamento_pk'])? $data['motivo_afastamento_pk'] : "";
            $dt_ini_afastamento = isset($data['dt_ini_afastamento'])? $data['dt_ini_afastamento'] : "";
            $dt_fim_afastamento = isset($data['dt_fim_afastamento'])? $data['dt_fim_afastamento'] : "";
            $colaborador_cobertura_afastamento_pk = isset($data['colaborador_cobertura_afastamento_pk'])? $data['colaborador_cobertura_afastamento_pk'] : "";
            $ds_obs_afastamento = isset($data['ds_obs_afastamento'])? $data['ds_obs_afastamento'] : "";
            $dt_ini_ferias = isset($data['dt_ini_ferias'])? $data['dt_ini_ferias'] : "";
            $dt_fim_ferias = isset($data['dt_fim_ferias'])? $data['dt_fim_ferias'] : "";
            $colaborador_cobertura_ferias_pk = isset($data['colaborador_cobertura_ferias_pk'])? $data['colaborador_cobertura_ferias_pk'] : "";
            $ds_obs_ferias = isset($data['ds_obs_ferias'])? $data['ds_obs_ferias'] : "";
            $produtos_servicos_pk = isset($data['produtos_servicos_pk'])? $data['produtos_servicos_pk'] : "";
            $dt_ini_servico_extra = isset($data['dt_ini_servico_extra'])? $data['dt_ini_servico_extra'] : "";
            $hr_ini_servico_extra = isset($data['hr_ini_servico_extra'])? $data['hr_ini_servico_extra'] : "";
            $dt_fim_servico_extra = isset($data['dt_fim_servico_extra'])? $data['dt_fim_servico_extra'] : "";
            $hr_fim_servico_extra = isset($data['hr_fim_servico_extra'])? $data['hr_fim_servico_extra'] : "";
            $vl_servico_extra = isset($data['vl_servico_extra'])? $data['vl_servico_extra'] : "";
            $vl_mao_obra_servico_extra = isset($data['vl_mao_obra_servico_extra'])? $data['vl_mao_obra_servico_extra'] : "";
            $obs_servico_extra = isset($data['obs_servico_extra'])? $data['obs_servico_extra'] : "";
            $tipo_disciplina_pk = isset($data['tipo_disciplina_pk'])? $data['tipo_disciplina_pk'] : "";
            $dt_disciplina = isset($data['dt_disciplina'])? $data['dt_disciplina'] : "";
            $obs = isset($data['obs'])? $data['obs'] : "";
            $dt_inicio_atestado = isset($data['dt_inicio_atestado'])? $data['dt_inicio_atestado'] : "";
            $dt_fim_atestado = isset($data['dt_fim_atestado'])? $data['dt_fim_atestado'] : "";
            $hr_ini_declaracao = isset($data['hr_ini_declaracao'])? $data['hr_ini_declaracao'] : "";
            $hr_fimi_declaracao = isset($data['hr_fimi_declaracao'])? $data['hr_fimi_declaracao'] : "";
            //CRIA AS VARIAVEIS AQUI
            
            
            $horario = $entity->consultarHorario();
            if($hr_manual == ''){
                $dt_apontamento = (Util::DataYMD($dt_apontamento) ." ".$horario);
            }else{
                $dt_apontamento = (Util::DataYMD($dt_apontamento) ." ".$hr_manual.":00");
            }
            $apontamento = [
                "pk"=>"",
                "leads_pk"=>$leads_pk,
                "tipo_apontamento_pk"=>$tipo_apontamento_pk,
                "colaborador_pk"=>$colaborador_pk,
                "agenda_colaborador_padrao_pk"=>$agenda_colaborador_padrao_pk,
                "dt_apontamento"=>$dt_apontamento,
            ];

            $retorno = $entity->salvar($apontamento);

            if($tipo_apontamento_pk == 1){
                if($hr_sistema == 1){
                    $hr_ponto = "sysdate()";
                }else{
                    $hr_ponto = $hr_manual;
                }
                $apontamentoPonto = [
                    "pk"=>"",
                    "colaborador_pk"=>$colaborador_pk,
                    "agenda_colaborador_apontamento_pk"=>$retorno->data,
                    "tipo_ponto_pk"=>$tipo_ponto_pk,
                    "dt_apontamento"=>$dt_apontamento,
                    "hr_ponto"=>$hr_ponto,
                    "ds_obs_ponto"=>$ds_obs_ponto,
                    "ds_pin"=>$ds_pin,
                    "dt_ponto"=>Util::DataYMD($dt_ponto),
                ];

                $entity->salvarPonto($apontamentoPonto);
            }
            if($tipo_apontamento_pk == 2){
                //PASSA ELAS PRA CÁ 

                if($dt_inicio_atestado!=""){
                    $dt_inicio_atestado = Util::DataYMD($dt_inicio_atestado);
                }
                if($dt_fim_atestado!=""){
                    $dt_fim_atestado = Util::DataYMD($dt_fim_atestado);
                }
                $apontamentoFalta = [
                    "pk"=>"",
                    "ds_obs_falta"=>$ds_obs_falta,
                    "agenda_colaborador_apontamento_pk"=>$retorno->data,
                    "colaborador_cobertura_falta_pk"=>$colaborador_cobertura_falta_pk,
                    "motivo_falta_pk"=>$motivo_falta_pk,
                    "dt_falta"=>Util::DataYMD($dt_falta),
                    "lead_pk"=>$leads_pk,
                    "motivo_cobertura_pk"=>$motivo_cobertura_falta_pk,
                    "lead_cobertura_pk"=>$lead_cobertura_pk,
                    "vl_ft_falta"=>$vl_ft_falta,
                    "dt_inicio_atestado"=>$dt_inicio_atestado,
                    "dt_fim_atestado"=>$dt_fim_atestado,
                    "hr_ini_declaracao"=>$hr_ini_declaracao,
                    "hr_fimi_declaracao"=>$hr_fimi_declaracao
                ];
                $entity->salvarFalta($apontamentoFalta);
            }
            if($tipo_apontamento_pk == 3){

                $apontamentoFalta = [
                    "pk"=>"",
                    "agenda_colaborador_apontamento_pk"=>$retorno->data,
                    "motivo_folga_pk"=>$motivo_folga_pk,
                    "motivo_ft_pk"=>$motivo_ft_pk,
                    "ds_obs_folga"=>$ds_obs_folga,
                    "dt_folga"=>Util::DataYMD($dt_folga),
                    "lead_cobertura_pk"=>$lead_cobertura_pk,
                    "vl_ft"=>$vl_ft
                ];
                $entity->salvarFolga($apontamentoFalta);
            }
            if($tipo_apontamento_pk == 4){

                $apontamentoTrocaEscala = [
                    "pk"=>"",
                    "agenda_colaborador_apontamento_pk"=>$retorno->data,
                    "ds_obs_troca_escala"=>$ds_obs_troca_escala,
                    "motivos_troca_escala_pk"=>$motivos_troca_escala_pk,
                    "colaborador_cobertura_troca_escala_pk"=>$colaborador_cobertura_troca_escala_pk,
                    "dt_troca_escala"=>Util::DataYMD($dt_troca_escala),
                ];
                $entity->salvarTrocaEscala($apontamentoTrocaEscala);
            }
            if($tipo_apontamento_pk == 5){

                $apontamentoAfastamento = [
                    "pk"=>"",
                    "agenda_colaborador_apontamento_pk"=>$retorno->data,
                    "motivo_afastamento_pk"=>$motivo_afastamento_pk,
                    "colaborador_cobertura_afastamento_pk"=>$colaborador_cobertura_afastamento_pk,
                    "ds_obs_afastamento"=>$ds_obs_afastamento,
                    "dt_ini_afastamento"=>Util::DataYMD($dt_ini_afastamento),
                    "dt_fim_afastamento"=>Util::DataYMD($dt_fim_afastamento),
                ];
                $entity->salvarAfastamento($apontamentoAfastamento);
            }
            if($tipo_apontamento_pk == 6){

                $apontamentoFerias = [
                    "pk"=>"",
                    "agenda_colaborador_apontamento_pk"=>$retorno->data,
                    "colaborador_cobertura_ferias_pk"=>$colaborador_cobertura_ferias_pk,
                    "ds_obs_ferias"=>$ds_obs_ferias,
                    "dt_ini_ferias"=>Util::DataYMD($dt_ini_ferias),
                    "dt_fim_ferias"=>Util::DataYMD($dt_fim_ferias),
                ];
                $entity->salvarFerias($apontamentoFerias);
            }
            if($tipo_apontamento_pk == 7){

                $apontamentoServicoExtra = [
                    "pk"=>"",
                    "colaborador_pk"=>$colaborador_pk,
                    "leads_pk"=>$leads_pk,
                    "vl_servico"=>$vl_servico_extra,
                    "vl_mao_obra"=>$vl_mao_obra_servico_extra,
                    "produtos_servicos_pk"=>$produtos_servicos_pk,
                    "obs"=>$obs_servico_extra,
                    "dt_periodo_ini"=>(Util::DataYMD($dt_ini_servico_extra) ." ".$hr_ini_servico_extra.":00"),
                    "dt_periodo_fim"=>(Util::DataYMD($dt_fim_servico_extra) ." ".$hr_fim_servico_extra.":00")
                ];
                $entity->salvarServicoExtra($apontamentoServicoExtra);
            }
            if($tipo_apontamento_pk == 8){

                $apontamentoDisciplina = [
                    "pk"=>"",
                    "tipo_disciplina_pk"=>$tipo_disciplina_pk,
                    "dt_disciplina"=>Util::DataYMD($dt_disciplina),
                    "colaborador_pk"=>$colaborador_pk,
                    "agenda_colaborador_pk"=>$retorno->data,
                    "leads_pk"=>$leads_pk,
                    "obs"=>$obs
                ];

               
                $entity->salvarDisciplina($apontamentoDisciplina);
            }


            Json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function salvarApontamentoReloginho(Request $request, Response $response, $args){
        try{
            $entity = new AgendaColaboradorApontamento($this->pdo);
            $data = $request->getQueryParams();
        
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $agenda_colaborador_pk = isset($data['agenda_colaborador_pk'])? $data['agenda_colaborador_pk'] : "";
            $dt_apontamento = isset($data['dt_apontamento'])? $data['dt_apontamento'] : "";
            $tipo_apontamento_pk = isset($data['tipo_apontamento_pk'])? $data['tipo_apontamento_pk'] : "";
            $hr_ini_expediente = isset($data['hr_ini_expediente'])? $data['hr_ini_expediente'] : "";
            $hr_ini_intervalo = isset($data['hr_ini_intervalo'])? $data['hr_ini_intervalo'] : "";
            $hr_fim_intervalo = isset($data['hr_fim_intervalo'])? $data['hr_fim_intervalo'] : "";
            $hr_fim_expediente = isset($data['hr_fim_expediente'])? $data['hr_fim_expediente'] : "";
            $hr_faltantes = isset($data['hr_faltantes'])? $data['hr_faltantes'] : "";
            $hr_excedentes = isset($data['hr_excedentes'])? $data['hr_excedentes'] : "";
            $hr_trabalhadas = isset($data['hr_trabalhadas'])? $data['hr_trabalhadas'] : "";
            $motivo_falta_pk = isset($data['motivo_falta_pk'])? $data['motivo_falta_pk'] : "";
            $motivo_afastamento_pk = isset($data['motivo_afastamento_pk'])? $data['motivo_afastamento_pk'] : "";
            $feriado_pk = isset($data['feriado_pk'])? $data['feriado_pk'] : "";
            //CRIA AS VARIAVEIS AQUI

            $apontamento = [
                "pk"=>"",
                "leads_pk"=>$leads_pk,
                "tipo_apontamento_pk"=>$tipo_apontamento_pk,
                "colaborador_pk"=>$colaborador_pk,
                "agenda_colaborador_padrao_pk"=>$agenda_colaborador_pk,
                "dt_apontamento"=>$dt_apontamento,
            ];

            $retorno = $entity->salvar($apontamento);

            $apontamento = [
                "pk"=>"",
                "leads_pk"=>$leads_pk,
                "tipo_apontamento_pk"=>$tipo_apontamento_pk,
                "colaborador_pk"=>$colaborador_pk,
                "agenda_colaborador_padrao_pk"=>$retorno->data,
                "dt_apontamento"=>$dt_apontamento,
                "hr_ini_expediente"=>$hr_ini_expediente,
                "hr_ini_intervalo"=>$hr_ini_intervalo,
                "hr_fim_intervalo"=>$hr_fim_intervalo,
                "hr_fim_expediente"=>$hr_fim_expediente,
                "hr_faltantes"=>$hr_faltantes,
                "hr_excedentes"=>$hr_excedentes,
                "hr_trabalhadas"=>$hr_trabalhadas,
                "motivo_falta_pk"=>$motivo_falta_pk,
                "motivo_afastamento_pk"=>$motivo_afastamento_pk,
                "feriado_pk"=>$feriado_pk,
            ];

            $retorno = $entity->salvarApontamentoReloginho($apontamento);
            
           

            

           

            Json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function salvarValidadoReloginho(Request $request, Response $response, $args){
        try{
            $entity = new AgendaColaboradorApontamento($this->pdo);
            $data = $request->getQueryParams();
        
            $pk = isset($data['pk'])? $data['pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $dt_hora_ponto = isset($data['dt_hora_ponto'])? $data['dt_hora_ponto'] : "";
            $ic_verificado = isset($data['ic_verificado'])? $data['ic_verificado'] : "";
            //CRIA AS VARIAVEIS AQUI

            $verificado = [
                "pk"=>$data['pk'],
                "leads_pk"=>$leads_pk,
                "dt_hora_ponto"=>$dt_hora_ponto,
                "colaborador_pk"=>$colaborador_pk,
                "ic_verificado"=>$ic_verificado
            ];

            $retorno = $entity->salvarValidadoReloginho($verificado);
           

            Json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }




    public function listarApontamentoColaboradorDia(Request $request, Response $response, $args) {
        try{

            $data = $request->getQueryParams();
            $entity = new AgendaColaboradorApontamento($this->pdo);
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $dt_apontamento = isset($data['dt_apontamento'])? $data['dt_apontamento'] : "";

            $retorno = $entity->listarApontamentoColaboradorDia($colaborador_pk,$dt_apontamento);

            Json::run($retorno->status, $retorno->data, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarDisciplina(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $ds_tipo_disciplina = isset($data['ds_tipo_dsciplna'])? $data['ds_tipo_dsciplna'] : "";
            $retorno = (new AgendaColaboradorApontamento($this->pdo))->listarDisciplina($ds_tipo_disciplina);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function relApontamento(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $tipo_apontamento_pk = isset($data['tipo_apontamento_pk'])? $data['tipo_apontamento_pk']  : "";
            $dt_ini = isset($data['dt_ini'])? $data['dt_ini']  : "";
            $dt_fim = isset($data['dt_fim'])? $data['dt_fim']  : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']  : "";
            
          (new AgendaColaboradorApontamento($this->pdo))->relApontamento($colaborador_pk,$tipo_apontamento_pk,$dt_ini,$dt_fim,$leads_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function relControleFt(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk'] : "";
            $dt_ini = isset($data['dt_ini'])? $data['dt_ini']  : "";
            $dt_fim = isset($data['dt_fim'])? $data['dt_fim']  : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']  : "";
            
          (new AgendaColaboradorApontamento($this->pdo))->relControleFt($colaborador_pk,$dt_ini,$dt_fim,$leads_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}