<?php

namespace App\Controller;

use App\Model\CertificadosEmpresas;
use App\Model\Log;
use App\Utils\Util;
use App\Model\NfeApi;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class CertificadosEmpresasController extends BaseController {

    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'certificados_empresas/certificados_empresas_res_form.twig');
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
            $this->view->render($response, 'certificados_empresas/certificados_empresas_cad_form.twig',array(
                "pk"=>$pk
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function contaConfigConsulta(Request $request, Response $response, $args){
        try{
            
            $data = $request->getQueryParams();
            $contas_origem_pk = isset($data['contas_origem_pk'])? $data['contas_origem_pk'] : "";
            $ds_cnpj = isset($data['ds_cnpj'])? $data['ds_cnpj'] : "";
            $ds_dominio = $_SESSION['session_user']['par11'];

            $retorno =  (new NfeApi($this->pdo))->contaConfigConsulta($contas_origem_pk, $ds_cnpj,$ds_dominio);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function contaConfigListarEmpresas(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $contas_origem_pk = isset($data['contas_origem_pk'])? $data['contas_origem_pk'] : "";
            $ds_dominio = $_SESSION['session_user']['par11'];
            
            $retorno =  (new NfeApi($this->pdo))->contaConfigListarEmpresas($contas_origem_pk,$ds_dominio);
            
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function contaConfigConsultaPk(Request $request, Response $response, $args){
        try{
            
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $retorno =  (new NfeApi($this->pdo))->contaConfigConsultaPk($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function excluirDocs(Request $request, Response $response, $args){
        try{
            
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $retorno =  (new NfeApi($this->pdo))->excluirDocs($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function excluirServico(Request $request, Response $response, $args){
        try{
            
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $retorno =  (new NfeApi($this->pdo))->excluirServico($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function downloadCertificado(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $pk = isset($data['pk'])? $data['pk'] : "";
            $retorno =  (new NfeApi($this->pdo))->pegarDocConta($pk);

            header("Content-type: " . $retorno->data->tipo_docs);
            echo ($retorno->data->certificado);
            exit;
            
            
            Json::run(true, [], "Download com sucesso!");
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function isBinary($str) {
        for ($i = 0; $i < strlen($str); ++$i) {
            $charCode = ord($str[$i]);
            if ($charCode < 32 || $charCode > 126) {
                return true;
            }
        }
        return false;  
    }
    public function contaConfigSalvar(Request $request, Response $response, $args){
        try{
            
            //$data = $request->getQueryParams();
            $data = $_POST;
            
            $ds_senha_certificado = isset($data['ds_senha_certificado'])? $data['ds_senha_certificado'] : "";  
            $pk = isset($data['pk'])? $data['pk'] : "";
            $contas_leads_pk = isset($data['contas_leads_pk'])? $data['contas_leads_pk'] : "";
            $cpfCNPJ = isset($data['cpfCNPJ'])? $data['cpfCNPJ'] : "";
            $inscricaoMunicipal = isset($data['inscricaoMunicipal'])? $data['inscricaoMunicipal'] : "";
            $inscricaoEstadual = isset($data['inscricaoEstadual'])? $data['inscricaoEstadual'] : "";   
            $razaoSocial = isset($data['razaoSocial'])? $data['razaoSocial'] : "";   
            $nomeFantasia = isset($data['nomeFantasia'])? $data['nomeFantasia'] : "";    
            $certificado = isset($data['certificado'])? $data['certificado'] : "";  
            $ds_id_certificado = isset($data['ds_id_certificado'])? $data['ds_id_certificado'] : "";  
            
            $dt_criacao_certificado = isset($data['dt_criacao_certificado'])? $data['dt_criacao_certificado'] : "";  
            $dt_vencimento_certificado = isset($data['dt_vencimento_certificado'])? $data['dt_vencimento_certificado'] : "";  
            $simplesNacional = isset($data['simplesNacional'])? $data['simplesNacional'] : "";  
            $regimeTributario = isset($data['regimeTributario'])? $data['regimeTributario'] : "";  
            $incentivoFiscal = isset($data['incentivoFiscal'])? $data['incentivoFiscal'] : "";  
            $incentivadorCultural = isset($data['incentivadorCultural'])? $data['incentivadorCultural'] : "";  
            $simplesNacional = isset($data['simplesNacional'])? $data['simplesNacional'] : "";  
            $regimeTributarioEspecial = isset($data['regimeTributarioEspecial'])? $data['regimeTributarioEspecial'] : "";  
            $email = isset($data['email'])? $data['email'] : "";  
            $bairro = isset($data['bairro'])? $data['bairro'] : "";  
            $cep = isset($data['cep'])? $data['cep'] : "";  
            $codigoCidade = isset($data['codigoCidade'])? $data['codigoCidade'] : "";  //DS_CIDADE
            $estado = isset($data['estado'])? $data['estado'] : "";  
            $logradouro = isset($data['logradouro'])? $data['logradouro'] : "";  
            $numero = isset($data['numero'])? $data['numero'] : "";  
            $tipoLogradouro = isset($data['tipoLogradouro'])? $data['tipoLogradouro'] : "";  
            $codigoPais = isset($data['codigoPais'])? $data['codigoPais'] : "";  
            $complemento = isset($data['complemento'])? $data['complemento'] : "";  
            
            $descricaoPais = isset($data['descricaoPais'])? $data['descricaoPais'] : "";  
            $tipoBairro = isset($data['tipoBairro'])? $data['tipoBairro'] : "";  
            $ds_telefone = isset($data['ds_telefone'])? $data['ds_telefone'] : "";  
            $ddd = isset($data['ddd'])? $data['ddd'] : "";  
            $regimeTributario = isset($data['regimeTributario'])? $data['regimeTributario'] : "";  
            $nfse_ativo = isset($data['nfse_ativo'])? $data['nfse_ativo'] : "";  
            $regimeTributario = isset($data['regimeTributario'])? $data['regimeTributario'] : "";  
            $nfe_tipoContrato = isset($data['nfe_tipoContrato'])? $data['nfe_tipoContrato'] : "";  
            $nfce_ativo = isset($data['nfce_ativo'])? $data['nfce_ativo'] : "";  
            $nfce_tipoContrato = isset($data['nfce_tipoContrato'])? $data['nfce_tipoContrato'] : "";  
            $mdfe_ativo = isset($data['mdfe_ativo'])? $data['mdfe_ativo'] : "";  
            $mdfe_tipoContrato = isset($data['mdfe_tipoContrato'])? $data['mdfe_tipoContrato'] : "";  
            $contas_leads_pk = isset($data['contas_leads_pk'])? $data['contas_leads_pk'] : "";  
            $contas_config_pk = isset($data['contas_config_pk'])? $data['contas_config_pk'] : "";  
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";  
            $senhaPrefeitura = isset($data['senhaPrefeitura'])? $data['senhaPrefeitura'] : "";  
            $loginPrefeitura = isset($data['loginPrefeitura'])? $data['loginPrefeitura'] : "";  
            $numeroUltNota = isset($data['numeroUltNota'])? $data['numeroUltNota'] : "";  
            $serieNota = isset($data['serieNota'])? $data['serieNota'] : "";  
            $loteNota = isset($data['loteNota'])? $data['loteNota'] : ""; 
            $dadosServico = isset($data['dadosServico'])? $data['dadosServico'] : ""; 

            $cidade_sem_assento = Util::removerAcentos($codigoCidade);
            $descricaoCidade = $cidade_sem_assento.$estado;  
          
            $file = $_FILES;
            $diretorio = __DIR__ . '/../docs/';
            $arrFiles =[];
            $arquivo = ""; 
            $tamanho = "";
            $tipo    = "";
            $diretorioArquivo ="";
            $blob = "";
            $temp_name = "";

            if(!empty($file)){
                for($x=0;$x<count($file);$x++){
                    move_uploaded_file($_FILES[$x]['tmp_name'], $diretorio. $_FILES[$x]['name']);
                    $arrFiles = $file[$x];
                }
                //TRANSFORMANDO FILE EM BLOB
            
                $arquivo = $arrFiles["name"]; 
                $tamanho = $arrFiles["size"];
                $tipo    = $arrFiles["type"];
                $temp_name = $_FILES[0]['tmp_name'];
                $diretorioArquivo = $diretorio.$arquivo;
               
                $fp = fopen($diretorioArquivo, "rb");

                $blob = fread($fp, $tamanho);
                $blob = addslashes($blob);


                $conteudo_binario = file_get_contents($diretorioArquivo);

                if ($this->isBinary($conteudo_binario)) {
                    $retornoCertificado = (new NfeApi($this->pdo))->enviarCertificado($conteudo_binario,$ds_senha_certificado);
                } else {
                    Json::run(false, [], "Esse Doc não é compativel");
                }
            }
            else{
                
                $retorno =  (new NfeApi($this->pdo))->contaConfigConsultaPk($pk);
                
                $ds_nome_arquivo = $retorno->data->ds_nome_arquivo;


                $conteudo_binario = file_get_contents($diretorio.$ds_nome_arquivo);
                if($conteudo_binario!=""){
                    if ($this->isBinary($conteudo_binario)) {
                        $retornoCertificado =  (new NfeApi($this->pdo))->enviarCertificado($conteudo_binario,$ds_senha_certificado);
                    } else {
                        Json::run(false, [], "Esse Doc não é compativel");
                    }
                }
                else{
                    Json::run(false, [], "Selecione ao menos um documento");
                }
                
            }
            if(!$retornoCertificado->status){
                Json::run(false, [], $retornoCertificado->message);
            }
            else{
                $arrContaConfig = [
                    "dadosServico" => $dadosServico,
                    'usuario' => $_SESSION['session_user']['par1'],
                    'ds_dominio'=>$_SESSION['session_user']['par11']
                ];
                $contaConfig = [
                    "pk" =>$pk,
                    "contas_leads_pk" => $contas_leads_pk,
                    'cpfCNPJ'  => $cpfCNPJ,
                    'inscricaoMunicipal'  => $inscricaoMunicipal,
                    'inscricaoEstadual'  => $inscricaoEstadual,
                    'razaoSocial' => $razaoSocial,
                    'nomeFantasia'  => $nomeFantasia,
                    //'certificado'  => $certificado,
                    'ds_id_certificado'  => $retornoCertificado->data,
                    'ds_senha_certificado'  => $ds_senha_certificado,
                    'dt_criacao_certificado'  => Util::DataYMD($dt_criacao_certificado),
                    'dt_vencimento_certificado'  => Util::DataYMD($dt_vencimento_certificado),
                    'simplesNacional'  => $simplesNacional,
                    'regimeTributario'  => $regimeTributario,
                    'incentivoFiscal'  => $incentivoFiscal,
                    'incentivadorCultural'  => $incentivadorCultural,
                    'regimeTributarioEspecial'  => $regimeTributarioEspecial,
                    'email'  => $email,
                    'bairro'  => $bairro,
                    'cep'  => $cep,
                    'codigoCidade'  => $codigoCidade,
                    'estado'  => $estado,
                    'logradouro'  => $logradouro,
                    'numero'  => $numero,
                    'tipoLogradouro'  => $tipoLogradouro,
                    'codigoPais'  => $codigoPais,
                    'complemento'  => $complemento,
                    'descricaoCidade'  => $descricaoCidade,
                    'descricaoPais'  => $descricaoPais,
                    'tipoBairro'  => $tipoBairro,
                    'ds_telefone'  => $ds_telefone,
                    'ddd'  => $ddd,
                    'nfse_ativo'  => $nfse_ativo,
                    'nfe_tipoContrato'  => $nfe_tipoContrato,
                    'nfce_ativo'  => $nfce_ativo,
                    'nfce_tipoContrato'  => $nfce_tipoContrato,
                    'mdfe_ativo'  => $mdfe_ativo,
                    'mdfe_tipoContrato'  => $mdfe_tipoContrato,
                    'contas_leads_pk'  => $contas_leads_pk,
                    'contas_config_pk'  => $contas_config_pk,
                    'ic_status'  => $ic_status,
                    'senhaPrefeitura' => $senhaPrefeitura,
                    'loginPrefeitura' => $loginPrefeitura,
                    'numeroUltNota' => $numeroUltNota,
                    'serieNota' => $serieNota,
                    'loteNota' => $loteNota,
                    'usuario' => $_SESSION['session_user']['par1'],
                    'docs' =>utf8_encode($blob),
                    'ds_dominio'=>$_SESSION['session_user']['par11'],
                    'tipo_docs'=>$tipo,
                    'nome_arquivo'=>$arquivo,
                    'tmp_name'=>$temp_name,
                    'file'=>$file
                ];
            }
            

            
            $retorno =  (new NfeApi($this->pdo))->contaConfigSalvar($contaConfig);
            
            $retornoServico =  (new NfeApi($this->pdo))->salvarContasServicoConfig($arrContaConfig);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function salvarNfeServico(Request $request, Response $response, $args){
        try{
            
            $data = $request->getQueryParams();

            $num_codigo_servico = isset($data['num_codigo_servico'])? $data['num_codigo_servico'] : "";
            $ds_servico = isset($data['ds_servico'])? $data['ds_servico'] : "";
            $contas_pk = isset($data['contas_pk'])? $data['contas_pk'] : "";
            $contas_leads_pk = isset($data['contas_leads_pk'])? $data['contas_leads_pk'] : "";
            $codigo_tributacao = isset($data['codigo_tributacao'])? $data['codigo_tributacao'] : "";

            $nfeServico = [
                "num_codigo_servico" => $num_codigo_servico,
                'ds_servico'  => $ds_servico,
                'contas_pk'  => $contas_pk,
                'contas_leads_pk'  => $contas_leads_pk,
                'codigo_tributacao'  => $codigo_tributacao,
                'usuario' => $_SESSION['session_user']['par1'],
                'ds_dominio'=>$_SESSION['session_user']['par11']
            ];

            $retorno =  (new NfeApi($this->pdo))->salvarNfeServico($nfeServico);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarNfeServico(Request $request, Response $response, $args){
        try{
            
            $data = $request->getQueryParams();
            $contas_pk = isset($data['contas_pk'])? $data['contas_pk'] : "";
            $retorno =  (new NfeApi($this->pdo))->listarNfeServico($contas_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);

        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarDadosServico(Request $request, Response $response, $args){
        try{
            
            $data = $request->getQueryParams();
            $codigoServico = isset($data['codigoServico'])? $data['codigoServico'] : "";
            $retorno =  (new NfeApi($this->pdo))->listarDadosServico($codigoServico);
            Json::run($retorno->status, $retorno->data, $retorno->message);

        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarServicosPk(Request $request, Response $response, $args){
        try{
            
            $data = $request->getQueryParams();
            $servicos_pk = isset($data['servicos_pk'])? $data['servicos_pk'] : "";
            $retorno =  (new NfeApi($this->pdo))->listarServicosPk($servicos_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);

        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}