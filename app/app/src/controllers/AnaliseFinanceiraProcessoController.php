<?php

namespace App\Controller;

use App\Model\AnaliseFinanceira;
use App\Utils\Json;
use App\Model\Log;
use App\Model\AnaliseFinanceiraProcesso;
use App\Model\EnviarEmail;
use App\Model\Usuario;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class AnaliseFinanceiraProcessoController extends BaseController {

    public function historicoAnaliseFinanceira(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            
            $analise_financeira_pk = isset($data['analise_financeira_pk'])? $data['analise_financeira_pk']  : "";
            
            $retorno = (new AnaliseFinanceiraProcesso($this->pdo))->historicoAnaliseFinanceira($analise_financeira_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
            

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
            $tipo_nivel_usuario = isset($data['tipo_nivel_usuario'])? $data['tipo_nivel_usuario']: "";
            $ic_recusa = isset($data['ic_recusa'])? $data['ic_recusa']: "";
            $obs_recusa = isset($data['obs_recusa'])? $data['obs_recusa']: "";
            $ic_correcao = isset($data['ic_correcao'])? $data['ic_correcao']: "";
            $obs_correcao = isset($data['obs_correcao'])? $data['obs_correcao']: "";
            $ic_aprovacao = isset($data['ic_aprovacao'])? $data['ic_aprovacao']: "";
            $obs_aprovacao = isset($data['obs_aprovacao'])? $data['obs_aprovacao']: "";
            $dt_cancelamento = isset($data['dt_cancelamento'])? $data['dt_cancelamento']: "";
            $obs_cancelamento = isset($data['obs_cancelamento'])? $data['obs_cancelamento']: "";
            $analise_financeira_pk = isset($data['analise_financeira_pk'])? $data['analise_financeira_pk']: "";
            $gestor_aprovacao_pk = isset($data['gestor_aprovacao_pk'])? $data['gestor_aprovacao_pk']: "";

            $analise_financeira_processos = [
                "pk"=>$pk,
                "tipo_nivel_usuario"=>$tipo_nivel_usuario,
                "ic_recusa"=>$ic_recusa,
                "obs_recusa"=>$obs_recusa,
                "ic_correcao"=>$ic_correcao,
                "obs_correcao"=>$obs_correcao,
                "ic_aprovacao"=>$ic_aprovacao,
                "obs_aprovacao"=>$obs_aprovacao,
                "dt_cancelamento"=>$dt_cancelamento,
                "obs_cancelamento"=>$obs_cancelamento,
                "analise_financeira_pk"=>$analise_financeira_pk
            ];

            
            
            $retorno = (new AnaliseFinanceiraProcesso($this->pdo))->salvar($analise_financeira_processos, $gestor_aprovacao_pk);
            
            $dadosCadastroAnalise = (new AnaliseFinanceira($this->pdo))->listarDadosAnaliseFinanceira($analise_financeira_pk);
    
            //var_dump($dadosCadastroAnalise);
            $dadosUsuarioLogado = (new Usuario($this->pdo))->listarGruposUsuario("");
    
            $dadosUsuarioCadastro = (new Usuario($this->pdo))->listarDadosUsuario($dadosCadastroAnalise->data[0]['usuario_cadastro_lancamento_pk']);
            $texto = "";
            //'Analista Financeiro'
            /*if($dadosUsuarioLogado->data[0]['grupos_pk'] == 14){
    
                
                if($ic_recusa !== ''){    
                    $Assunto = "Gepros Analista Financeiro - Lancamento Financeiro Recusado ";
                    $texto .="<div style='text-align:center'><b>Gepros - Lan&ccedil;amento Financeiro Recusado</b></div>";
                    $texto .="<div style='text-align:center'> O Lan&ccedil;amento ".$dadosCadastroAnalise->data[0]['lancamentos_pk']." foi recusado pelo setor An&aacute;lise Financeira.</div>";             
                    $texto .="<div style='text-align:center'>$obs_recusa</div>";
                }else if($ic_aprovacao !== ''){
                    $Assunto = "Gepros Analista Financeiro - Lancamento Financeiro Aprovado Analista";
                    $texto .="<div style='text-align:center'><b>Gepros - Solicita&ccedil;&atilde;o de Corre&ccedil;&atilde;o Lan&ccedil;amento Financeiro</b></div>";
                    $texto .="<div style='text-align:center'>O Lan&ccedil;amento ".$dadosCadastroAnalise->data[0]['lancamentos_pk']." foi aprovado pelo setor An&aacute;lise Financeira.</div>";
                    $texto .="<div style='text-align:center'>$obs_aprovacao</div>";
                }else if($ic_correcao !== ''){
                    $Assunto = "Gepros Analista Financeiro - Solicitacao de Correcao Lancamento";
                    $texto .="<div style='text-align:center'><b>Gepros - Solicita&ccedil;&atilde;o de Corre&ccedil;&atilde;o Lan&ccedil;amento Financeiro</b></div>";
                    $texto .="<div style='text-align:center'>O Lan&ccedil;amento ".$dadosCadastroAnalise->data[0]['lancamentos_pk']." requer sua aten&ccedil;&atilde;o, existem corre&ccedil;&otilde;es para serem feitas solicitadas pelo setor An&aacute;lise Financeira.</div>";
                    $texto .="<div style='text-align:center'>$obs_correcao</div>";
                }
            //'Controller'
            }else if($dadosUsuarioLogado->data[0]['grupos_pk'] == 9){*/
                if($ic_recusa !== ''){    
                    $Assunto = "Gepros Gestor - Lancamento Financeiro Recusado ";
                    $texto .="<div style='text-align:center'><b>Gepros - Lan&ccedil;amento Financeiro Recusado</b></div>";
                    $texto .="<div style='text-align:center'> O Lan&ccedil;amento ".$dadosCadastroAnalise->data[0]['lancamentos_pk']." foi recusado pelo setor Gestor.</div>";             
                    $texto .="<div style='text-align:center'>$obs_recusa</div>";
                }else if($ic_aprovacao !== ''){
                    $Assunto = "Gepros Gestor - Lancamento Financeiro Aprovado";
                    $texto .="<div style='text-align:center'><b>Gepros - Solicita&ccedil;&atilde;o de Corre&ccedil;&atilde;o Lan&ccedil;amento Financeiro</b></div>";
                    $texto .="<div style='text-align:center'>O Lan&ccedil;amento ".$dadosCadastroAnalise->data[0]['lancamentos_pk']." foi aprovado pelo setor Gestor.</div>";
                    $texto .="<div style='text-align:center'>$obs_aprovacao</div>";
                }else if($ic_correcao !== ''){
                    $Assunto = "Gepros Gestor - Solicitacao de Correcao Lancamento Financeiro ";
                    $texto .="<div style='text-align:center'><b>Gepros - Solicita&ccedil;&atilde;o de Corre&ccedil;&atilde;o Lan&ccedil;amento Financeiro</b></div>";
                    $texto .="<div style='text-align:center'>O Lan&ccedil;amento ".$dadosCadastroAnalise->data[0]['lancamentos_pk']." requer sua aten&ccedil;&atilde;o, existem corre&ccedil;&otilde;es para serem feitas solicitadas pelo setor Gestor.</div>";
                    $texto .="<div style='text-align:center'>$obs_correcao</div>";
                }
            //}
            
            $emailpara = $dadosUsuarioCadastro->data[0]['ds_email'];

            $returnEmail = (new EnviarEmail())->enviarEmail($emailpara,$Assunto,$texto);
        
    
            Json::run($retorno->status, $retorno->data." - ".$returnEmail, $retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    
}