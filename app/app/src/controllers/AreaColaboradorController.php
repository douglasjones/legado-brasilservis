<?php

namespace App\Controller;

use App\Model\AgendaColaboradorPadrao;
use App\Model\AreaColaborador;
use App\Model\Log;
use App\Model\Empresa;
use App\Model\Lead;
use App\Model\Beneficio;
use App\Model\Ponto;
use App\Model\SolicitacaoAcessoApp;
use App\Utils\Json;
use App\Utils\Session;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Utils\SlimImage;
use App\Utils\SlimStatus;
use Throwable;

final class AreaColaboradorController extends BaseController {
    //REGISTRAR PONTO
    public function receptivoRegistrarPonto(Request $request, Response $response, $args){
        try{
            $retorno = (new SolicitacaoAcessoApp($this->pdo))->buscarTodosBase64();
            $this->view->render($response, 'partials/area_colaborador/receptivoRegistrarPonto.twig',array(
                "arrDadosBase64"=>($retorno->data)
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function pegarInfoColaborador(Request $request, Response $response, $args){
        try{
            $data = $_POST;

            $id_empresa = isset($data['id_empresa'])? $data['id_empresa']: "";
            $id_colaborador = isset($data['id_colaborador'])? $data['id_colaborador']: "";
            $arrEmpresa = explode('-',$id_empresa);
            $retorno = (new AreaColaborador($this->pdo))->buscarColaborador($arrEmpresa[0],$id_colaborador);

            //PEGA O LEAD DE ACORDO COM A ESCALA DO COLABORADOR.
            $retornoLeads = (new AgendaColaboradorPadrao($this->pdo))->verificaOutraEscalaColaborador($id_colaborador);

            $data =[
                "arrColaborador"=>$retorno->data[0],
                "arrLead"=>$retornoLeads->data
            ];
            Json::run(true, $data, "Dados Carregados com sucesso!");
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function salvarPonto(Request $request, Response $response, $args){
        try{
            $data = $_POST;
            $ds_pin = isset($data['ds_pin'])? $data['ds_pin']: "";
            $id_colaborador = isset($data['id_colaborador'])? $data['id_colaborador']: "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk']: "";
            $tipo_ponto_pk = isset($data['tipo_ponto_pk'])? $data['tipo_ponto_pk']: "";
            $base64 = isset($data['base64'])? $data['base64']: "";

            $retornoAgenda = (new AgendaColaboradorPadrao($this->pdo))->pegarPostoDeTrabalhoPorLeadEColaborador($leads_pk,$id_colaborador);
            $agenda_colaborador_pk = $retornoAgenda->data[0]['pk'];



            //$binaryData = base64_decode($base64);
            $binaryData = ($base64);


            // Crie um nome de arquivo único para a imagem (pode ser necessário ajustar isso dependendo do seu aplicativo)
            $fileName = uniqid() . '.png';

            // Especifique o diretório de destino dentro do seu projeto
            $targetDirectory = __DIR__ . '/../docs/ponto/';

            //$targetDirectory = 'https://webponto.gepros6.com.br/ponto/rh/';


            // Caminho completo para o novo arquivo
            $targetPath = $targetDirectory . $fileName;
            // Escreva os dados binários no arquivo
            $resultado = file_put_contents($targetPath, $binaryData );

            if ($resultado !== false) {
                $ponto = [
                    "ic_tipo_app"=>2,
                    "ds_dispositivo"=>'Desktop',
                    "colaborador_pk"=>$id_colaborador,
                    "id_cliente"=>$ds_pin,
                    "tipo_ponto_pk"=>$tipo_ponto_pk,
                    "agenda_colaborador_padrao_pk"=>$agenda_colaborador_pk,
                    "leads_pk"=>$leads_pk,
                    "ds_localizacao"=>"",
                    "img_ponto"=>$binaryData,
                    "ds_imagem"=>"",
                    "ic_sincronizacao"=>1,
                ];

                $pk=(new Ponto($this->pdo))->salvarPontoDeskTop($ponto);
                if($pk->data!=""){
                    Json::run(true, $pk->data, "Registro Salvo com Sucesso!");
                }
                else{
                    Json::run(true, [], "Erro ao registrar ponto!");
                }
            }
            else{
                Json::run(false, [], "Erro com a Foto!");
            }

        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    //NOVO CADASTRO
    public function receptivo(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'area_colaborador/receptivo.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function passo1(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'partials/area_colaborador/passo1.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function passo2(Request $request, Response $response, $args){
        try{
            $this->view->render($response, 'partials/area_colaborador/passo2.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function passo3(Request $request, Response $response, $args){
        try{

            $data = $request->getQueryParams();

            $id_empresa = isset($data['id_empresa'])? $data['id_empresa']: "";
            $id_colaborador = isset($data['id_colaborador'])? $data['id_colaborador']: "";

            $retorno = (new AreaColaborador($this->pdo))->buscarColaborador($id_empresa,$id_colaborador);

            $this->view->render($response, 'partials/area_colaborador/passo3.twig',array(
                "dados"=>$retorno->data[0]
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function passo4(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $id_empresa = isset($data['id_empresa'])? $data['id_empresa']: "";
            $id_colaborador = isset($data['id_colaborador'])? $data['id_colaborador']: "";

            $this->view->render($response, 'partials/area_colaborador/passo4.twig',array(
                "id_empresa"=>$id_empresa,
                "id_colaborador"=>$id_colaborador
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function tirarFotoNovoRegistro(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $id_empresa = isset($data['id_empresa'])? $data['id_empresa']: "";
            $id_colaborador = isset($data['id_colaborador'])? $data['id_colaborador']: "";

            $this->view->render($response, 'partials/area_colaborador/tirarFotoNovoRegistro.twig',array(
                "id_empresa"=>$id_empresa,
                "id_colaborador"=>$id_colaborador
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function salvarPrimeiroRegistro(Request $request, Response $response, $args){
        try{
            $data = $_POST;

            $id_empresa = isset($data['id_empresa'])? $data['id_empresa']: "";
            $id_colaborador = isset($data['id_colaborador'])? $data['id_colaborador']: "";
            $base64 = isset($data['base64'])? $data['base64']: "";
            $length = strlen($base64);



            // Separe o cabeçalho da string base64
            list($type, $valor) = explode(';', $base64);
            list(, $valor)      = explode(',', $valor);

            $length = strlen($valor);


            $binaryData = base64_decode($valor);


            // Crie um nome de arquivo único para a imagem (pode ser necessário ajustar isso dependendo do seu aplicativo)
            $fileName = uniqid() . '.png';

            // Especifique o diretório de destino dentro do seu projeto
            $targetDirectory = __DIR__ . '/../docs/ponto/';

            //$targetDirectory = 'https://webponto.gepros6.com.br/ponto/rh/';


            // Caminho completo para o novo arquivo
            $targetPath = $targetDirectory . $fileName;
            // Escreva os dados binários no arquivo
            $resultado = file_put_contents($targetPath, $binaryData );

            if ($resultado !== false) {

                //$ds_link_imagem_cadastro = $_SERVER["HTTP_HOST"]."/docs/ponto/".$fileName;
                $ds_link_imagem_cadastro = '';
                $solict_ponto = [
                    "ds_pin" => $id_empresa."-".$id_colaborador,
                    "colaborador_pk" => $id_colaborador,
                    "id_cliente" => $id_empresa,
                    "img_colaborador_cadastro" => ($valor), //Imagem blob
                    "ds_link_imagem_cadastro" => $ds_link_imagem_cadastro, //link da Imagem
                    "IdTermoAceite" => 1,
                    "ic_tipo_app" => 1,
                ];
                $solicitacaoLiberacaoAppModel = (new SolicitacaoAcessoApp($this->pdo));
                $pk = $solicitacaoLiberacaoAppModel->novoCadSolicitacaoAcessoAppPonto($solict_ponto);

                if($pk->data!=""){
                    Json::run(true, $pk->data, "Registro Salvo com Sucesso!");
                }
                else{
                    Json::run(true, [], "Existe um cadastro vinculado ao PIN, ente em contato com a Base!");
                }
            }
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function buscarColaborador(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $id_empresa = isset($data['id_empresa'])? $data['id_empresa']: "";
            $id_colaborador = isset($data['id_colaborador'])? $data['id_colaborador']: "";
            $retorno = (new AreaColaborador($this->pdo))->buscarColaborador($id_empresa,$id_colaborador);

            if(count($retorno->data)>0){
                Json::run(true, [], "Localizamos os seus dados !");
            }
            else{
                Json::run(false, [], "Não localizamos os seus dados, verifique as informações.");

            }
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }



}
