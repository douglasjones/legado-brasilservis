<?php

namespace App\Controller;

use App\Model\Log;
use App\Utils\Json;
use App\Utils\Util;
use App\Model\Lead;
use App\Model\Contato;
use App\Model\Processo;
use App\Model\ProcessoDefault;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class LeadController extends BaseController {
    public function salvarQrCode(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $arr = isset($data['arrLocalPonto'])? $data['arrLocalPonto'] : "";
            
            $arrLocalPonto = json_decode($arr, true);
          
            if(count($arrLocalPonto)>0){
                //EXCLUIR OS DADOS 
                (new Lead($this->pdo))->excluirLocalQrCode($leads_pk);
                for($i=0; $i<count($arrLocalPonto); $i++) {
                    $lead =[
                        "leads_pk"=>$leads_pk,
                        "local_ponto"=>$arrLocalPonto[$i]['local_ponto']
                    ];

                    $retorno = (new Lead($this->pdo))->salvarQrCode($lead);
            
                }
            }

            

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function excluir(Request $request, Response $response, $args)
    {
        try {
            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";

            if($pk!=""){
                (new Log($this->pdo))->salvar('lead',$pk);

                (new Lead($this->pdo))->excluir($pk);
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
    public function listarQRCode(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";

            $retorno = (new Lead($this->pdo))->listarQRCode($leads_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function receptivo(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $local = isset($data['local'])? $data['local'] : "";
            $this->view->render($response, 'lead/lead_res.twig',array('ic_abertura'=>1,'local'=>$local));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function qrCode(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead'] : "";
            $local = isset($data['local'])? $data['local'] : "";
            $pk = isset($data['pk'])? $data['pk'] : "";
            $this->view->render($response, 'lead/qrCode.twig',array('pk'=>$pk,'ds_lead'=>$ds_lead));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function leadMainPainel(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $ic_abertura = isset($data['ic_abertura'])? $data['ic_abertura'] : "";
            $local = isset($data['local'])? $data['local'] : "";

            $pk = isset($data['pk'])? $data['pk'] : "";
            $this->view->render($response, 'lead/lead_main_form.twig',array('ic_abertura'=>$ic_abertura,'leads_pk'=>$pk,'local'=>$local));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function cadForm(Request $request, Response $response, $args){
        try{

            $data = $request->getQueryParams();

            $local = isset($data['local'])? $data['local'] : "";

            $this->view->render($response, 'lead/lead_cad_form.twig',
                array("leads_pk"=> '', "ic_processo_comercial"=> 2, "processo_default_configuracao_pk"=> '',"local"=>$local)
            );

        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function salvar(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $contatos_lead = isset($data['contatos_lead'])? $data['contatos_lead'] : "";
            //Contatos Lead
            $arrContatosLead = [];
            if($contatos_lead != ""){
                $arrContatosLead = json_decode ($contatos_lead, true);
            }

            $pk = isset($data['pk'])? $data['pk'] : "";
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead'] : "";
            $ds_endereco = isset($data['ds_endereco'])? $data['ds_endereco'] : "";
            $ds_numero = isset($data['ds_numero'])? $data['ds_numero'] : "";
            $ds_complemento = isset($data['ds_complemento'])? $data['ds_complemento'] : "";
            $ds_cep = isset($data['ds_cep'])? $data['ds_cep'] : "";
            $ds_bairro = isset($data['ds_bairro'])? $data['ds_bairro'] : "";
            $ds_cidade = isset($data['ds_cidade'])? $data['ds_cidade'] : "";
            $ds_uf = isset($data['ds_uf'])? $data['ds_uf'] : "";
            $ic_cliente = isset($data['ic_cliente'])? $data['ic_cliente'] : "";
            $n_qtde_torres = isset($data['n_qtde_torres'])? $data['n_qtde_torres'] : "";
            $ds_obs = isset($data['ds_obs'])? $data['ds_obs'] : "";
            $ds_razao_social = isset($data['ds_razao_social'])? $data['ds_razao_social'] : "";
            $ds_cpf_cnpj = isset($data['ds_cpf_cnpj'])? $data['ds_cpf_cnpj'] : "";
            $ds_ie = isset($data['ds_ie'])? $data['ds_ie'] : "";
            $ds_tel = isset($data['ds_tel'])? $data['ds_tel'] : "";
            $ds_fax = isset($data['ds_fax'])? $data['ds_fax'] : "";
            $ds_site = isset($data['ds_site'])? $data['ds_site'] : "";
            $ds_email = isset($data['ds_email'])? $data['ds_email'] : "";
            $supervisores_pk = isset($data['supervisores_pk'])? $data['supervisores_pk'] : "";
            $supervisor1_pk = isset($data['supervisor1_pk'])? $data['supervisor1_pk'] : "";
            $supervisor2_pk = isset($data['supervisor2_pk'])? $data['supervisor2_pk'] : "";
            $responsavel_pk = isset($data['responsavel_pk'])? $data['responsavel_pk'] : "";
            $segmentos_pk = isset($data['segmentos_pk'])? $data['segmentos_pk'] : "";
            $leads_pai_pk = isset($data['leads_pai_pk'])? $data['leads_pai_pk'] : "";
            $ic_tipo_lead = isset($data['ic_tipo_lead'])? $data['ic_tipo_lead'] : "";
            $ds_tipo = isset($data['ds_tipo'])? $data['ds_tipo'] : "";
            $ds_porte = isset($data['ds_porte'])? $data['ds_porte'] : "";
            $dt_abertura = isset($data['dt_abertura'])? $data['dt_abertura'] : "";
            $ds_atividade_principal = isset($data['ds_atividade_principal'])? $data['ds_atividade_principal'] : "";
            $ds_atividade_secundaria = isset($data['ds_atividade_secundaria'])? $data['ds_atividade_secundaria'] : "";
            $ds_socio1 = isset($data['ds_socio1'])? $data['ds_socio1'] : "";
            $ds_socio2 = isset($data['ds_socio2'])? $data['ds_socio2'] : "";
            $ds_socio3 = isset($data['ds_socio3'])? $data['ds_socio3'] : "";
            $dia_faturamento = isset($data['dia_faturamento'])? $data['dia_faturamento'] : "";
            $ic_inss_aplicacao = isset($data['ic_inss_aplicacao'])? $data['ic_inss_aplicacao'] : "";
            $ic_iss_retido_tomador = isset($data['ic_iss_retido_tomador'])? $data['ic_iss_retido_tomador'] : "";
            $processo_default_configuracao_pk = isset($data['processo_default_configuracao_pk'])? $data['processo_default_configuracao_pk'] : "";

            $dt_abertura_formatado = "";
            if($dt_abertura != ""){
                $dt_abertura_formatado = Util::DataYMD($dt_abertura);
            }
            
            $lead =[
                "pk"=>$pk,
                "ds_lead"=>$ds_lead,
                "ds_endereco"=>$ds_endereco,
                "ds_numero"=>$ds_numero,
                "ds_complemento"=>$ds_complemento,
                "ds_cep"=>$ds_cep,
                "ds_bairro"=>$ds_bairro,
                "ds_cidade"=>$ds_cidade,
                "ds_uf"=>$ds_uf,
                "ic_cliente"=>$ic_cliente,
                "n_qtde_torres"=>$n_qtde_torres,
                "ds_obs"=>$ds_obs,
                "ds_razao_social"=>$ds_razao_social,
                "ds_cpf_cnpj"=>$ds_cpf_cnpj,
                "ds_ie"=>$ds_ie,
                "ds_tel"=>$ds_tel,
                "ds_fax"=>$ds_fax,
                "ds_site"=>$ds_site,
                "ds_email"=>$ds_email,
                "supervisores_pk"=>$supervisores_pk,
                "supervisor1_pk"=>$supervisor1_pk,
                "supervisor2_pk"=>$supervisor2_pk,
                "responsavel_pk"=>$responsavel_pk,
                "segmentos_pk"=>$segmentos_pk,
                "leads_pai_pk"=>$leads_pai_pk,
                "ic_tipo_lead"=>$ic_tipo_lead,
                "ds_tipo"=>$ds_tipo,
                "ds_porte"=>$ds_porte,
                "dt_abertura"=>$dt_abertura_formatado,
                "ds_atividade_principal"=>$ds_atividade_principal,
                "ds_atividade_secundaria"=>$ds_atividade_secundaria,
                "ds_socio1"=>$ds_socio1,
                "ds_socio2"=>$ds_socio2,
                "ds_socio3"=>$ds_socio3,
                "dia_faturamento"=>$dia_faturamento,
                "ic_iss_retido_tomador"=>$ic_iss_retido_tomador,
                "ic_inss_aplicacao"=>$ic_inss_aplicacao,
            ];
            $retorno = (new Lead($this->pdo))->salvar($lead);


            if($retorno->data!=""){
                //processo
                //veririca se ja tem um processo registrado
                $leads_pk = $retorno->data;
                $queryProcesso = (new Processo($this->pdo))->verificarQtdeLead($leads_pk);

                if($queryProcesso->data == 0){
                    //cadastra um novo processo
                    $processo_default= (new ProcessoDefault($this->pdo))->listarTodos();

                    if($processo_default->data['pk']>0){
                        $processo = [
                            "pk"=>"",
                            "ds_processo"=>$processo_default->data['ds_processo_default'],
                            "processos_default_pk"=>$processo_default->data['pk'],
                            "leads_pk"=>$leads_pk,
                        ];

                        (new Processo($this->pdo))->salvar($processo);
                    }
                }
            
                if(count($arrContatosLead) > 0){
                    for($i = 0; $i < count($arrContatosLead); $i++){
                        if($arrContatosLead[$i]['ds_contato']!="undefined"){
                            $contato = [
                                "pk"=> $arrContatosLead[$i]['contatos_pk'],
                                "ds_contato"=> $arrContatosLead[$i]['ds_contato'],
                                "ds_cel"=> $arrContatosLead[$i]['ds_cel'],
                                "ic_whatsapp"=> $arrContatosLead[$i]['ic_whatsapp'],
                                "ds_email"=> $arrContatosLead[$i]['ds_email'],
                                "ds_tel"=> $arrContatosLead[$i]['ds_tel_contato'],
                                "cargos_pk"=> $arrContatosLead[$i]['cargos_pk'],
                                "leads_pk"=> $leads_pk,
                            ];
    
                            (new Contato($this->pdo))->salvar($contato);
                        }
                        
                    }
                }
            }

            /*if($processo_default_configuracao_pk != ''){
                $comercialdao->salvarProcessoMovimentacaoStatus($processo_default_configuracao_pk, $pk, '', '');
            }*/

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500,[]);
        }
    }

    public function listarDataTable(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();

            $ds_lead = isset($data['ds_lead'])? $data['ds_lead'] : "";
            $ds_lead_grid = isset($data['ds_lead_grid'])? $data['ds_lead_grid'] : "";
            $ic_cliente = isset($data['ic_cliente'])? $data['ic_cliente'] : "";
            $supervisores_pk = isset($data['supervisores_pk'])? $data['supervisores_pk'] : "";
            $segmentos_pk = isset($data['segmentos_pk'])? $data['segmentos_pk'] : "";
            $responsavel_pk = isset($data['responsavel_pk'])? $data['responsavel_pk'] : "";
            $ic_tipo_lead = isset($data['ic_tipo_lead'])? $data['ic_tipo_lead'] : "";
            $leads_pai_pk = isset($data['leads_pai_pk'])? $data['leads_pai_pk'] : "";
            $leads_clientes_pk = isset($data['leads_clientes_pk'])? $data['leads_clientes_pk'] : "";

            (new Lead($this->pdo))->listar_por_ds_lead($ds_lead,$ic_cliente,$supervisores_pk,$responsavel_pk,$ds_lead_grid,$ic_tipo_lead, $leads_pai_pk, $leads_clientes_pk,$segmentos_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarContatoLead(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";

            (new Contato($this->pdo))->carregarPorLeadsPk($leads_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarTodosPostTrabalho(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $ic_status = isset($data['ic_cliente'])? $data['ic_cliente'] : "";
            $ic_tipo_lead = isset($data['ic_tipo_lead'])? $data['ic_tipo_lead'] : "";
            $leads_pai_pk = isset($data['leads_pai_pk'])? $data['leads_pai_pk'] : "";

            $retorno = (new Lead($this->pdo))->listar_por_ds_lead_pai_pk($ic_tipo_lead, $leads_pai_pk,$ic_status);

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

            $retorno = (new Lead($this->pdo))->listarPorPk($pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarTodos(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $ds_lead = isset($data['ds_lead'])? $data['ds_lead'] : "";

            $retorno = (new Lead($this->pdo))->listarTodos($ds_lead);

            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarTodosClientes(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pai_pk = isset($data['leads_pai_pk'])? $data['leads_pai_pk'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $ic_tipo_lead = isset($data['ic_tipo_lead'])? $data['ic_tipo_lead'] : "";

            $retorno = (new Lead($this->pdo))->listar_por_ds_lead_pai_pk($ic_tipo_lead, $leads_pai_pk,$ic_status);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarLeadPai(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();

            $retorno = (new Lead($this->pdo))->listarLeadPai();
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function verificarCNPJ(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $cpfcnpj = isset($data['ds_cpf_cnpj'])? $data['ds_cpf_cnpj'] : "";
            $retorno = (new Lead($this->pdo))->verificarCNPJ($cpfcnpj);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarEnderecos(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $retorno = (new Lead($this->pdo))->listarEndereco($leads_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listaLeadsClientes(Request $request, Response $response, $args) {
        try{
            $retorno = (new Lead($this->pdo))->listaLeadsClientes();
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    
    public function listarCpfCnpjClientes(Request $request, Response $response, $args) {
        try{
            $retorno = (new Lead($this->pdo))->listarCpfCnpjClientes();
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listaLeadsPostosTrabalho(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $retorno = (new Lead($this->pdo))->listaLeadsPostosTrabalho($leads_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarClienteColaborador(Request $request, Response $response, $args) {
        try{
            $retorno = (new Lead($this->pdo))->listarClienteColaborador();
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listaColaboradorPostosTrabalho(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $retorno = (new Lead($this->pdo))->listaColaboradorPostosTrabalho($leads_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listaFornecedorPostosTrabalho(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $retorno = (new Lead($this->pdo))->listaFornecedorPostosTrabalho($leads_pk);
            Json::run($retorno->status, $retorno->data, $retorno->message);
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
	public function listarLeadsPorEmpresa(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk'] : "";

            $retorno = (new Lead($this->pdo))->listarLeadsPorEmpresa($empresas_pk);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
	public function listarLeadsClienteEmpresa(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $empresas_pk = isset($data['empresas_pk'])? $data['empresas_pk'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $ic_tipo_lead = isset($data['ic_tipo_lead'])? $data['ic_tipo_lead'] : "";
            $leads_pai_pk = isset($data['leads_pai_pk'])? $data['leads_pai_pk'] : "";

            $retorno = (new Lead($this->pdo))->listarLeadsClienteEmpresa($empresas_pk, $ic_status, $ic_tipo_lead, $leads_pai_pk);
            json::run($retorno->status,$retorno->data,$retorno->message);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
}

