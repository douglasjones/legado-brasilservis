<?php

namespace App\Controller;

use App\Model\ControleNfse;
use App\Model\Conta;
use App\Model\Lead;
use App\Model\Log;
use App\Model\NfeApi;
use App\Utils\Util;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class ControleNfseController extends BaseController {
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'controle_nfe/controle_nfse_res_form.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function receptivoFake(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'controle_nfe/fake.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function cadForm(Request $request, Response $response, $args){
        try{
            $pk = isset($data['pk'])? $data['pk'] : "";
            $this->view->render($response, 'controle_nfe/controle_nfse_cad_form.twig', array('pk'=>$pk));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarGrid(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $ds_numero_nfse = isset($data['ds_numero_nfse'])? $data['ds_numero_nfse'] : "";
            $ds_prestador = isset($data['ds_prestador'])? $data['ds_prestador'] : "";   
 
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : ""; 
            $ds_tomador = isset($data['ds_tomador'])? $data['ds_tomador'] : "";   
            $dt_emissao_ini = isset($data['dt_emissao_ini'])? ($data['dt_emissao_ini']) : "";   
            $dt_emissao_fim = isset($data['dt_emissao_fim'])? ($data['dt_emissao_fim']) : "";   
            $dt_cancelamento_ini = isset($data['dt_cancelamento_ini'])? ($data['dt_cancelamento_ini']) : "";   
            $dt_cancelamento_fim = isset($data['dt_cancelamento_fim'])? ($data['dt_cancelamento_fim']) : "";   
        
            $body = [
                "ds_numero_nfse" => $ds_numero_nfse,
                "ds_prestador" => $ds_prestador,
                "ic_status" => $ic_status,
                "ds_tomador" => $ds_tomador,
                "dt_emissao_ini" => $dt_emissao_ini,
                "dt_emissao_fim" => $dt_emissao_fim,
                "dt_cancelamento_ini" => $dt_cancelamento_ini,
                "dt_cancelamento_fim" => $dt_cancelamento_fim,
                "ds_dominio"=>$_SESSION['session_user']['par11']
            ];
            $retorno = (new NfeApi($this->pdo))->listarNfse($body);
            
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function salvar(Request $request, Response $response, $args) {
        
        try{
            $data = $_POST;
            
            $pk = isset($data['pk'])? $data['pk'] : "";
            $tipoNotaFiscalEletronica = isset($data['tipoNotaFiscalEletronica'])? $data['tipoNotaFiscalEletronica'] : "";
            $prestador = isset($data['prestador'])? $data['prestador'] : "";
            $naturezaOperacao = isset($data['naturezaOperacao'])? $data['naturezaOperacao'] : "";
            $razaoSocial = isset($data['razaoSocial'])? $data['razaoSocial'] : "";
            $cnpj = isset($data['cnpj'])? $data['cnpj'] : "";
            $cep = isset($data['cep'])? $data['cep'] : "";
            $estado = isset($data['estado'])? $data['estado'] : "";
            $cidade = isset($data['cidade'])? $data['cidade'] : "";
            $bairro = isset($data['bairro'])? $data['bairro'] : "";
            $tipoLogradouro = isset($data['tipoLogradouro'])? $data['tipoLogradouro'] : "";
            $logradouro = isset($data['logradouro'])? $data['logradouro'] : "";
            $numero = isset($data['numero'])? $data['numero'] : "";
            $complemento = isset($data['complemento'])? $data['complemento'] : "";
            $inscricaoMunicipal = isset($data['inscricaoMunicipal'])? $data['inscricaoMunicipal'] : "";
            $inscricaoEstadual = isset($data['inscricaoEstadual'])? $data['inscricaoEstadual'] : "";
            $retidoTomador = isset($data['retidoTomador'])? $data['retidoTomador'] : "";
            $email = isset($data['email'])? $data['email'] : "";
            $listaServicoConsulta = isset($data['listaServicoConsulta'])? $data['listaServicoConsulta'] : "";
            $codigo = isset($data['codigo'])? $data['codigo'] : "";
            $descricaoServico = isset($data['descricaoServico'])? $data['descricaoServico'] : "";
            $aliquota = isset($data['aliquota'])? $data['aliquota'] : "";
            $codigo_tributacao = isset($data['codigo_tributacao'])? $data['codigo_tributacao'] : "";
            $discriminacao = isset($data['discriminacao'])? $data['discriminacao'] : "";
            $valorServico = isset($data['valorServico'])? $data['valorServico'] : "";
            $valorDeducao = isset($data['valorDeducao'])? $data['valorDeducao'] : "";
            $numeroRPS = isset($data['numeroRPS'])? $data['numeroRPS'] : "";
            $serieRPS = isset($data['serieRPS'])? $data['serieRPS'] : "";
            $dataEmissaoRPS = isset($data['dataEmissaoRPS'])? $data['dataEmissaoRPS'] : "";
            $iss_aliquota = isset($data['iss_aliquota'])? $data['iss_aliquota'] : 0;
            $iss_valor = isset($data['iss_valor'])? $data['iss_valor'] : 0;
            $inss_aliquota = isset($data['inss_aliquota'])? $data['inss_aliquota'] : 0;
            $inss_valor = isset($data['inss_valor'])? $data['inss_valor'] : 0;
            $pis_aliquota = isset($data['pis_aliquota'])? $data['pis_aliquota'] : 0;
            $pis_valor = isset($data['pis_valor'])? $data['pis_valor'] : 0;
            $cofins_aliquota = isset($data['cofins_aliquota'])? $data['cofins_aliquota'] : 0;
            $cofins_valor = isset($data['cofins_valor'])? $data['cofins_valor'] : 0;
            $ir_aliquota = isset($data['ir_aliquota'])? $data['ir_aliquota'] : 0;
            $ir_valor = isset($data['ir_valor'])? $data['ir_valor'] : 0;
            $csll_aliquota = isset($data['csll_aliquota'])? $data['csll_aliquota'] : 0;
            $csll_valor = isset($data['csll_valor'])? $data['csll_valor'] : 0;
            $dt_vencimento = isset($data['dt_vencimento'])? $data['dt_vencimento'] : 0;
            $dt_competencia = isset($data['dt_competencia'])? $data['dt_competencia'] : 0;
            $vl_liquido = isset($data['vl_liquido'])? $data['vl_liquido'] : 0;

            // Obtém o valor do campo 'texto' e remove quebras de linha
            $discriminacao = str_replace("\n", "", $discriminacao);
          
         


            //PEGAR TODOS OS DADOS DO PRESTADOR
            $arrDadosPrestador = (new NfeApi($this->pdo))->contaConfigConsultaPk($prestador);

            $arrDadosRazaoSocial = (new Lead($this->pdo))->listarPorPk($razaoSocial);
            
              
            $controle = [
                "pk" => "",
                "tipoNotaFiscalEletronica" => $tipoNotaFiscalEletronica,
                "prestador" => $prestador,
                "naturezaOperacao" => $naturezaOperacao,
                "razaoSocial" => $razaoSocial,
                "cnpj" => $cnpj,
                "cep" => $cep,
                "estado" => $estado,
                "cidade" => $cidade,
                "bairro" => $bairro,
                "tipoLogradouro" => $tipoLogradouro,
                "logradouro" => $logradouro,
                "numero" => $numero,
                "complemento" => $complemento,
                "inscricaoMunicipal" => $inscricaoMunicipal,
                "inscricaoEstadual" => $inscricaoEstadual,
                "retidoTomador" => $retidoTomador,
                "email" => $email,
                "codigo" => $codigo,
                "descricaoServico" => $descricaoServico,
                "aliquota" => $aliquota,
                "codigo_tributacao"=>$codigo_tributacao,
                "discriminacao" => $discriminacao,
                "valorServico" => $valorServico,
                "numeroRPS" => $numeroRPS,
                "serieRPS" => $serieRPS,
                "dataEmissaoRPS" => $dataEmissaoRPS,
                "valorDeducao" => $valorDeducao,
                "listaServicoConsulta" => $listaServicoConsulta,
                "arrDadosPrestador"=>$arrDadosPrestador->data,
                "arrDadosRazaoSocial"=>$arrDadosRazaoSocial->data[0],
                "iss_aliquota"=>$iss_aliquota,
                "iss_valor"=>$iss_valor,
                "inss_aliquota"=>$inss_aliquota,
                "inss_valor"=>$inss_valor,
                "pis_aliquota"=>$pis_aliquota,
                "pis_valor"=>$pis_valor,
                "cofins_aliquota"=>$cofins_aliquota,
                "cofins_valor"=>$cofins_valor,
                "ir_aliquota"=>$ir_aliquota,
                "ir_valor"=>$ir_valor,
                "csll_aliquota"=>$csll_aliquota,
                "csll_valor"=>$csll_valor,
                "dt_vencimento"=>$dt_vencimento,
                "dt_competencia"=>$dt_competencia,
                "vl_liquido"=>number_format($vl_liquido, 2, '.', '')
            ];


            
            

            $retorno = (new NfeApi($this->pdo))->salvarControleNfse($controle);


            
            
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {

            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }


    public function downloadNfse(Request $request, Response $response, $args) {
        try{
            

            $id_notas = $args['id_notas']; 
            
        
            $retorno = (new NfeApi($this->pdo))->downloadNfse($id_notas);
            
            //Json::run(true, [], "Download com sucesso!");
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function downloadNfseLancamento(Request $request, Response $response, $args) {
        try{
            

            $notas_pk = $args['notas_pk']; 
            
        
            $retorno = (new NfeApi($this->pdo))->downloadNfseLancamento($notas_pk);
            
            //Json::run(true, [], "Download com sucesso!");
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function exibirXML(Request $request, Response $response, $args) {
        try{
            

            $id_notas = $args['id_notas']; 
            
        
            $retorno = (new NfeApi($this->pdo))->exibirXML($id_notas);
            
            //Json::run(true, [], "Download com sucesso!");
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function cancelarNota(Request $request, Response $response, $args) {
        try{
            
            $data = $request->getQueryParams();
            $id_notas = $data['id_notas']; 
            $pk = $data['pk']; 

            
        
            $retorno = (new NfeApi($this->pdo))->cancelarNota($id_notas,$pk);
            
            Json::run(true, [], "Cancelado com sucesso!");
            //return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    
}