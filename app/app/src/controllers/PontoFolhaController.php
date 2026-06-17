<?php

namespace App\Controller;

use App\Model\Ponto;
use App\Model\PontoFolha;
use App\Utils\Json;
use App\Utils\Session;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Utils\SlimImage;
use App\Utils\SlimStatus;
use Throwable;

final class PontoFolhaController extends BaseController {

    public function excluirFolhaColaborador(Request $request, Response $response, $args)
    {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk']) ? $data['colaborador_pk'] : "";
            $dt_periodo_ini = isset($data['dt_periodo_ini']) ? $data['dt_periodo_ini'] : "";
            $dt_periodo_fim = isset($data['dt_periodo_fim']) ? $data['dt_periodo_fim'] : "";

            if($pk!=""){

                (new PontoFolha($this->pdo))->excluirFolhaColaborador($pk,$colaborador_pk,$dt_periodo_ini,$dt_periodo_fim);
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
    public function receptivoPontoFolha(Request $request, Response $response, $args){
        try{
            
            $this->view->render($response, 'ponto_folha/ponto_folha_res_form.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function receptivoPontoFolhaFechamento(Request $request, Response $response, $args){
        try{
           
            $this->view->render($response, 'ponto_folha/ponto_folha_fechamento_res_form.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function registrosCad(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            if($pk!=""){
                //RH
                if($_SESSION['session_user']['par10']==11 ){
                    if($leads_pk==1588 || $leads_pk==1529){
                        $this->view->render($response, 'theme/acesso-restrito.twig');
                    }
                    else{
                        $this->view->render($response, 'ponto_folha/ponto_folha_registros_cad_form.twig',array('pk'=>$pk, 'leads_pk'=>$leads_pk, 'colaborador_pk'=>$colaborador_pk));
                    }
                }
                else{
                    $this->view->render($response, 'ponto_folha/ponto_folha_registros_cad_form.twig',array('pk'=>$pk, 'leads_pk'=>$leads_pk, 'colaborador_pk'=>$colaborador_pk));
                }
            }
            else{    
                $this->view->render($response, 'ponto_folha/ponto_folha_registros_cad_form.twig',array('pk'=>$pk, 'leads_pk'=>$leads_pk, 'colaborador_pk'=>$colaborador_pk));
            }
           
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoPrint(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $relatorio_banco_horas = isset($data['relatorio_banco_horas'])? $data['relatorio_banco_horas'] : "";
            $reloginho = isset($data['reloginho'])? $data['reloginho'] : "";

            $this->view->render($response, 'ponto_folha/ponto_folha_print_form.twig',array(
                'pk'=>$pk, 
                'leads_pk'=>$leads_pk, 
                'colaborador_pk'=>$colaborador_pk, 
                'relatorio_banco_horas'=>$relatorio_banco_horas,
                'reloginho'=>$reloginho
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function receptivoPrintFechamento(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $dt_periodo_ini = isset($data['dt_periodo_ini'])? $data['dt_periodo_ini'] : "";
            $dt_periodo_fim = isset($data['dt_periodo_fim'])? $data['dt_periodo_fim'] : "";
            $relatorio_banco_horas = isset($data['relatorio_banco_horas'])? $data['relatorio_banco_horas'] : "";
            $reloginho = isset($data['reloginho'])? $data['reloginho'] : "";
            $this->view->render($response, 'ponto_folha/ponto_folha_print_fechamento_form.twig',array(
                'pk'=>$pk, 
                'leads_pk'=>$leads_pk, 
                'colaborador_pk'=>$colaborador_pk, 
                'dt_periodo_ini'=>$dt_periodo_ini, 
                'dt_periodo_fim'=>$dt_periodo_fim, 
                'relatorio_banco_horas'=>$relatorio_banco_horas,
                'reloginho'=>$reloginho
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function receptivoPrintByColaboradorPeriodo(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

           
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $dt_periodo_ini = isset($data['dt_inicio'])? $data['dt_inicio'] : "";
            $dt_periodo_fim = isset($data['dt_fim'])? $data['dt_fim'] : "";
            $reloginho = isset($data['reloginho'])? $data['reloginho'] : "";

            $this->view->render($response, 'ponto_folha/ponto_folha_print_periodo_colaborador_form.twig',array(
                'colaborador_pk'=>$colaborador_pk, 
                'dt_periodo_ini'=>$dt_periodo_ini,
                'dt_periodo_fim'=>$dt_periodo_fim,
                'reloginho'=>$reloginho
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }


    public function colaboradoresCad(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";

            $this->view->render($response, 'ponto_folha/ponto_folha_registros_res_form.twig',array('pk'=>$pk, 'leads_pk'=>$leads_pk, 'colaborador_pk'=>$colaborador_pk));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function colaboradoresCadFechamento(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $dt_periodo_ini = isset($data['dt_periodo_ini'])? $data['dt_periodo_ini'] : "";
            $dt_periodo_fim = isset($data['dt_periodo_fim'])? $data['dt_periodo_fim'] : "";
            

            $this->view->render($response, 'ponto_folha/ponto_folha_registros_fechamento_res_form.twig',
            array('pk'=>$pk, 'leads_pk'=>$leads_pk, 'colaborador_pk'=>$colaborador_pk,"dt_periodo_ini"=>$dt_periodo_ini,"dt_periodo_fim"=>$dt_periodo_fim));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function cadForm(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk'] : "";
            $this->view->render($response, 'ponto_folha/ponto_folha_cad_form.twig',array('pk'=>$pk));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function salvar(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk']: "";
            $dt_periodo_ini = isset($data['dt_periodo_ini'])? $data['dt_periodo_ini']: "";
            $dt_periodo_fim = isset($data['dt_periodo_fim'])? $data['dt_periodo_fim']: "";
            $obs = isset($data['obs'])? $data['obs']: "";
            $leads_colaboradores = isset($data['leads_colaboradores'])? $data['leads_colaboradores']: "";
            $folha_ponto = isset($data['folha_ponto'])? $data['folha_ponto']: "";

            $pontoFolha = [
                "pk"=>"",
                "empresas_pk"=>$empresas_pk,
                "dt_periodo_ini"=>$dt_periodo_ini,
                "dt_periodo_fim"=>$dt_periodo_fim,
                "obs"=>$obs,
                "leads_colaboradores"=>$leads_colaboradores,
                "folha_ponto"=>$folha_ponto
            ];


            $retorno = (new PontoFolha($this->pdo))->salvar($pontoFolha);


            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function regerar(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

            $pk = isset($data['pk'])? $data['pk']: "";
            $dt_periodo_ini = isset($data['dt_periodo_ini'])? $data['dt_periodo_ini']: "";
            $dt_periodo_fim = isset($data['dt_periodo_fim'])? $data['dt_periodo_fim']: "";
            $arrColaborador = isset($data['arrColaborador'])? $data['arrColaborador']: "";

            $retorno = (new PontoFolha($this->pdo))->regerar($pk, $dt_periodo_ini, $dt_periodo_fim,$arrColaborador);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
        }
    }
    public function salvarFolhaFinalizada(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();

            $ic_status = isset($data['ic_status'])? $data['ic_status']: "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk']: "";
            $pk = isset($data['pk'])? $data['pk']: "";

            $pontoFolhaFinalizada = [
                "ic_status"=>$ic_status,
                "colaborador_pk"=>$colaborador_pk,
                "pk"=>$pk
            ];

            $retorno = (new PontoFolha($this->pdo))->salvarFolhaFinalizada($pontoFolhaFinalizada);
            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function salvarRegistros(Request $request, Response $response, $args){
        try{
            $data = $request->getParsedBody();

            $arrDadosRegistros = isset($data['arrDadosRegistros'])? $data['arrDadosRegistros']: "";

            $retorno = (new PontoFolha($this->pdo))->salvarRegistros($arrDadosRegistros);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function alterarRegistrosFolhaPonto(Request $request, Response $response, $args){
        try{
            $data = $request->getParsedBody();

            $arrDadosRegistros = isset($data['arrDadosRegistros'])? $data['arrDadosRegistros']: "";

            $retorno = (new PontoFolha($this->pdo))->alterarRegistrosFolhaPonto($arrDadosRegistros);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function gerarFolhaPontoByRelogio(Request $request, Response $response, $args){
        try{
            $data = $request->getParsedBody();

            $arrDados = isset($data['arrDados'])? $data['arrDados']: "";
            
            $retorno = (new PontoFolha($this->pdo))->gerarFolhaPontoByRelogio($arrDados);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function finalizarFolhaByReloginho(Request $request, Response $response, $args){
        try{
            $data = $request->getParsedBody();

            $arrDados = isset($data['arrDados'])? $data['arrDados']: "";

            $retorno = (new PontoFolha($this->pdo))->finalizarFolhaByReloginho($arrDados);

            Json::run($retorno->status,$retorno->data,$retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarGrid(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $dt_periodo_ini = isset($data['dt_periodo_ini'])? $data['dt_periodo_ini'] : "";
            $dt_periodo_fim = isset($data['dt_periodo_fim'])? $data['dt_periodo_fim'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";

            $retorno = (new PontoFolha($this->pdo))->listarGrid($empresas_pk,$leads_pk,$colaborador_pk,$dt_periodo_ini, $dt_periodo_fim, $ic_status);
            
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarModalPonto(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $dt_ponto = isset($data['dt_ponto'])? $data['dt_ponto'] : "";

            $retorno = (new PontoFolha($this->pdo))->listarModalPonto($colaborador_pk, $dt_ponto);
            
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarFolhaPorPeriodoByLeads(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $dt_periodo_ini = isset($data['dt_periodo_ini'])? $data['dt_periodo_ini'] : "";
            $dt_periodo_fim = isset($data['dt_periodo_fim'])? $data['dt_periodo_fim'] : "";

            $retorno = (new PontoFolha($this->pdo))->listarFolhaPorPeriodoByLeads($leads_pk, $dt_periodo_ini,$dt_periodo_fim);
            
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarDadosImpressao(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $ponto_folha_pk = isset($data['ponto_folha_pk'])? $data['ponto_folha_pk'] : "";

            $retorno = (new PontoFolha($this->pdo))->listarDadosImpressao($leads_pk, $colaborador_pk, $ponto_folha_pk);
            
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarConsultaPontoColaborador(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $agenda_colaborador_padrao_pk = isset($data['agenda_colaborador_pk'])? $data['agenda_colaborador_pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            /*$ic_mes = isset($data['ic_mes'])? $data['ic_mes'] : "";
            $ic_ano = isset($data['ic_ano'])? $data['ic_ano'] : "";
            $ultimoDiaMes = cal_days_in_month(CAL_GREGORIAN, $ic_mes, $ic_ano);

            $dt_periodo_ini = $ic_ano."-".$ic_mes."-01";
            $dt_periodo_fim = $ic_ano."-".$ic_mes."-".$ultimoDiaMes;*/

            $dt_periodo_ini  = Util::DataYMD($data['dt_inicio']);
            $dt_periodo_fim  = Util::DataYMD($data['dt_fim']);

            $retorno = (new PontoFolha($this->pdo))->listarConsultaPontoColaborador($leads_pk, $colaborador_pk, $dt_periodo_ini,$dt_periodo_fim,$agenda_colaborador_padrao_pk);
            
            /*if($_SESSION['session_user']['par10']==11 ){
                if($leads_pk==1588 || $leads_pk==1529){
                    json::run(true,[],"Você não tem permissão para acessar ! ");
                    die();
                }
            }*/ 
            json::run(true,$retorno,"");
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarPontoFolhaPK(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $ponto_folha_pk = isset($data['ponto_folha_pk'])? $data['ponto_folha_pk'] : "";

            $retorno = (new PontoFolha($this->pdo))->listarPontoFolhaPK($ponto_folha_pk);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarFolhasRegistros(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";

            $retorno = (new PontoFolha($this->pdo))->listarFolhasRegistros($pk);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarRegistros(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";

            $retorno = (new PontoFolha($this->pdo))->listarRegistros($pk, $leads_pk, $colaborador_pk);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarFolhaPorPeridoColaborador(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $dt_periodo_ini = isset($data['dt_periodo_ini'])? $data['dt_periodo_ini'] : "";
            $dt_periodo_fim = isset($data['dt_periodo_fim'])? $data['dt_periodo_fim'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";

            $retorno = (new PontoFolha($this->pdo))->listarFolhaPorPeridoColaborador($dt_periodo_ini, $dt_periodo_fim, $colaborador_pk);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
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
                
                (new PontoFolha($this->pdo))->excluir($pk);
                Json::run(true, [], 'Registro excluído com sucesso!');
            }else{
                Json::run(false, [], 'Falha ao excluir registro!');
            }
        }catch(Throwable $th){
            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error'=>$th->getMessage()
            ],500,[]);
        }
    }


    

}
