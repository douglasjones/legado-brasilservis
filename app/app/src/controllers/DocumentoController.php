<?php

namespace App\Controller;
use setasign\Fpdi\Fpdi;
use App\Model\Log;
use App\Utils\Util;
use App\Utils\Json;
use App\Model\Documento;
use Exception;
use finfo;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;


final class DocumentoController extends BaseController {
    public function excluir(Request $request, Response $response, $args)
    {
        try{

            $data = $request->getQueryParams();
            $pk = isset($data['pk']) ? $data['pk'] : "";
            $pk_doc_bd = isset($data['pk_doc_bd']) ? $data['pk_doc_bd'] : "";
            if($pk!=""){
                (new Log($this->pdo))->salvar('documento',$pk);
                (new Documento($this->pdo))->excluir($pk,$pk_doc_bd);
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
    public function excluirDocBd(Request $request, Response $response, $args)
    {
        try{

            $data = $request->getQueryParams();
            $pk_doc_bd = isset($data['pk_doc_bd']) ? $data['pk_doc_bd'] : "";
            if($pk_doc_bd!=""){
                (new Documento($this->pdo))->excluirDocBd($pk_doc_bd);
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
            $ic_tipo_documento = isset($data['ic_tipo_documento'])? $data['ic_tipo_documento'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $colaborador_pk = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
            $ds_obs = isset($data['ds_obs'])? $data['ds_obs'] : "";
            $ds_arquivo = isset($data['ds_arquivo'])? $data['ds_arquivo'] : "";
            $compras_pk = isset($data['compras_pk'])? $data['compras_pk'] : "";
            $lancamentos_pk = isset($data['lancamentos_pk'])? $data['lancamentos_pk'] : "";

            if($ds_arquivo != "")
                $arrDsArquivos = json_decode ($ds_arquivo, true);

            if(count($arrDsArquivos) > 0){
                for($i = 0; $i < count($arrDsArquivos); $i++){
                    $documento =[
                        "pk"=>  "",
                        "ds_documento"=>  $arrDsArquivos[$i]['ds_documento'],
                        "ds_nome_original"=>  $arrDsArquivos[$i]['ds_nome_original'],
                        "leads_pk"=>  $leads_pk,
                        "ds_obs"=>  $ds_obs,
                        "colaboradores_pk"=>  $colaborador_pk,
                        "contratos_pk"=>  "",
                        "ic_tipo_documento"=>  $ic_tipo_documento,
                        "ocorrencias_pk"=>  "",
                        "agendas_pk"=>  "",
                        "agenda_colaborador_tarefa_pk"=>  "",
                        "lancamentos_pk"=>  $lancamentos_pk,
                        "compras_pk"=>  $compras_pk,
                        "pk_doc_bd"=>  $arrDsArquivos[$i]['pk_doc_bd']
                    ];

                    (new Documento($this->pdo))->salvar($documento);

                }
            }

            Json::run(true, [], "Registro salvo com sucesso!");
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function salvarLancamentos(Request $request, Response $response, $args) {
        $data = $request->getQueryParams();

        $lancamentos_pk = isset($data['lancamentos_pk'])? $data['lancamentos_pk'] : "";
        $ds_documento = isset($data['ds_documento'])? $data['ds_documento'] : "";
        $pk_doc_bd = isset($data['pk_doc_bd'])? $data['pk_doc_bd'] : "";
        $ds_nome_original = isset($data['ds_nome_original'])? $data['ds_nome_original'] : "";

        $documento =[
            "pk"=>  "",
            "ds_documento"=>  $ds_documento,
            "ds_nome_original"=>  $ds_nome_original,
            "leads_pk"=>  "",
            "ds_obs"=>  "",
            "colaboradores_pk"=>  "",
            "contratos_pk"=>  "",
            "ic_tipo_documento"=>  "",
            "ocorrencias_pk"=>  "",
            "agendas_pk"=>  "",
            "agenda_colaborador_tarefa_pk"=>  "",
            "lancamentos_pk"=>  $lancamentos_pk,
            "compras_pk"=>  "",
            "pk_doc_bd"=>  $pk_doc_bd
        ];
        (new Documento($this->pdo))->salvar($documento);
        Json::run(true, [], "Registro salvo com sucesso!");
    }
    public function listarDocumentosAgenda(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $agendas_pk = isset($data['agendas_pk'])? $data['agendas_pk'] : "";

            (new Documento($this->pdo))->listarDocumentosAgenda($agendas_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarDocumentosOc(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $ocorrencias_pk = isset($data['ocorrencias_pk'])? $data['ocorrencias_pk'] : "";

            (new Documento($this->pdo))->listarDocumentosOcorrencia($ocorrencias_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarDocumentosCompra(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $compras_pk = isset($data['compras_pk'])? $data['compras_pk'] : "";

            (new Documento($this->pdo))->listarDocumentosCompra($compras_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarDocumentosLead(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";

            (new Documento($this->pdo))->listarDocumentosLead($leads_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function listarDocumentosColaborador(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $colaboradores_pk = isset($data['colaboradores_pk'])? $data['colaboradores_pk'] : "";

            (new Documento($this->pdo))->listarDocumentosColaborador($colaboradores_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function renomearArquivoAgenda(Request $request, Response $response, $args) {
        try{
            $entity = new Documento($this->pdo);
            $data = $request->getQueryParams();
            $ds_arquivo = isset($data['ds_arquivo']) ? $data['ds_arquivo'] : "";
            $retorno = $entity->listarQuantidadeDocumentosAgendas();
            if(!isset($retorno->data[0]['total']) || empty($retorno->data[0]['total'])){
                $total = 0;
            }
            else{
                $total = $retorno->data[0]['total'];
            }
            $diretorio = __DIR__ . '/../docs/';
            rename($diretorio.$ds_arquivo, $diretorio."Agenda".(intval($total)+1)."-".$ds_arquivo);
            unlink($diretorio."Agenda".(intval($total)+1)."-".$ds_arquivo);
            $dataResult = [
                "t_ds_nome_salvo" => "Agenda".(intval($total)+1)."-".$ds_arquivo,
                "t_functions" => ""
            ];

            Json::run(true, $dataResult['t_ds_nome_salvo'], "Renomeado com sucesso!");
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function renomearArquivo(Request $request, Response $response, $args) {
        try{
            $entity = new Documento($this->pdo);
            $data = $request->getQueryParams();
            $ds_arquivo = isset($data['ds_arquivo']) ? $data['ds_arquivo'] : "";
            $leads_pk = isset($data['leads_pk']) ? $data['leads_pk'] : "";
            $retorno = $entity->listarQuantidadeDocumentosLead($leads_pk);
            if(!isset($retorno->data[0]['total']) || empty($retorno->data[0]['total'])){
                $total = 0;
            }
            else{
                $total = $retorno->data[0]['total'];
            }

            $diretorio = __DIR__ . '/../docs/';
            rename($diretorio.$ds_arquivo, $diretorio."L".$leads_pk."-".(intval($total)+1)."-".$ds_arquivo);
            unlink($diretorio."L".$leads_pk."-".(intval($total)+1)."-".$ds_arquivo);
            $dataResult = [
                "t_ds_nome_salvo" => "L".$leads_pk."-".(intval($total)+1)."-".$ds_arquivo,
                "t_functions" => ""
            ];

            Json::run(true, $dataResult['t_ds_nome_salvo'], "Renomeado com sucesso!");
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function renomearArquivoCompra(Request $request, Response $response, $args) {
        try{
            $entity = new Documento($this->pdo);
            $data = $request->getQueryParams();
            $ds_arquivo = isset($data['ds_arquivo']) ? $data['ds_arquivo'] : "";
            $compras_pk = isset($data['compras_pk']) ? $data['compras_pk'] : "";

            if($compras_pk!=""){
                $retorno = $entity->listarQuantidadeDocumentosCompra($compras_pk);
                if(!isset($retorno->data[0]['total']) || empty($retorno->data[0]['total'])){
                    $total = 0;
                }
                else{
                    $total = $retorno->data[0]['total'];
                }
            }
            else{
                $total = 0;
            }


            $diretorio = __DIR__ . '/../docs/';
            rename($diretorio.$ds_arquivo, $diretorio."Comp".$compras_pk."-".(intval($total)+1)."-".$ds_arquivo);
            unlink($diretorio."Comp".$compras_pk."-".(intval($total)+1)."-".$ds_arquivo);
            $dataResult = [
                "t_ds_nome_salvo" => "Comp".$compras_pk."-".(intval($total)+1)."-".$ds_arquivo,
                "t_functions" => ""
            ];

            Json::run(true, $dataResult['t_ds_nome_salvo'], "Renomeado com sucesso!");
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function renomearArquivoColaborador(Request $request, Response $response, $args) {
        try{
            $entity = new Documento($this->pdo);
            $data = $request->getQueryParams();
            $ds_arquivo = isset($data['ds_arquivo']) ? $data['ds_arquivo'] : "";
            $colaborador_pk = isset($data['colaborador_pk']) ? $data['colaborador_pk'] : "";

            if($colaborador_pk!=""){
                $retorno = $entity->listarQuantidadeDocumentosColaborador($colaborador_pk);
                if(!isset($retorno->data[0]['total']) || empty($retorno->data[0]['total'])){
                    $total = 0;
                }
                else{
                    $total = $retorno->data[0]['total'];
                }
            }
            else{
                $total = 0;
            }


            $diretorio = __DIR__ . '/../docs/';
            rename($diretorio.$ds_arquivo, $diretorio."C".$colaborador_pk."-".(intval($total)+1)."-".$ds_arquivo);
            unlink($diretorio."C".$colaborador_pk."-".(intval($total)+1)."-".$ds_arquivo);
            $dataResult = [
                "t_ds_nome_salvo" => "C".$colaborador_pk."-".(intval($total)+1)."-".$ds_arquivo,
                "t_functions" => ""
            ];

            Json::run(true, $dataResult['t_ds_nome_salvo'], "Renomeado com sucesso!");
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function renomearArquivoLancamento(Request $request, Response $response, $args) {
        try{
            $entity = new Documento($this->pdo);
            $data = $request->getQueryParams();
            $ds_arquivo = isset($data['ds_arquivo']) ? $data['ds_arquivo'] : "";
            $lancamento_pk = isset($data['lancamento_pk']) ? $data['lancamento_pk'] : "";

            if($lancamento_pk!=""){
                $retorno = $entity->listarQuantidadeDocumentosLancamento($lancamento_pk);
                if(!isset($retorno->data[0]['total']) || empty($retorno->data[0]['total'])){
                    $total = 0;
                }
                else{
                    $total = $retorno->data[0]['total'];
                }
            }
            else{
                $total = 0;
            }


            $diretorio = __DIR__ . '/../docs/';
            rename($diretorio.$ds_arquivo, $diretorio."LANC".$lancamento_pk."-".(intval($total)+1)."-".$ds_arquivo);
            unlink($diretorio."LANC".$lancamento_pk."-".(intval($total)+1)."-".$ds_arquivo);
            $dataResult = [
                "t_ds_nome_salvo" => "LANC".$lancamento_pk."-".(intval($total)+1)."-".$ds_arquivo,
                "t_functions" => ""
            ];

            Json::run(true, $dataResult['t_ds_nome_salvo'], "Renomeado com sucesso!");
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function removerArquivo(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $nome_arquivo = isset($data['nome_arquivo']) ? $data['nome_arquivo'] : "";
            $diretorio = __DIR__ . '/../docs/';
            if($nome_arquivo!=""){
                unlink($diretorio.$nome_arquivo);
            }

            Json::run(true, [], "Renomeado com sucesso!");
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }
    public function download(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();

            $pk_doc_bd = $data['pk_doc_bd']; 
            $ds_documento = $data['ds_documento'];
            
            if($pk_doc_bd>0){
                $retorno = (new Documento($this->pdo))->pegarDocumentoBd($pk_doc_bd);
                $pdfBlob = $retorno->data[0]["docsData"];
                $pdfType = $retorno->data[0]["docsType"];
                $fileName = 'documento.pdf'; // Nome do arquivo PDF
                
                if ($pdfBlob) {
                    if (Util::isBase64($pdfBlob)) {
                        // Decodificar e usar o PDF
                        $pdfBlob = base64_decode($pdfBlob);
                        // Continue com o processamento do PDF
                    }
                    else{
                        $pdfBlob = $pdfBlob;
                    }
                    // Enviar o PDF diretamente ao navegador
                    header("Content-Type: " . $pdfType);
                    header("Content-Disposition: inline; filename=" . $fileName);
                    echo ($pdfBlob);
                    exit();
            }
            else{
                set_time_limit(0);
                // Arqui você faz as validações e/ou pega os dados do banco de dados
                $aquivoNome = $ds_documento; // nome do arquivo que será enviado p/ download
                $ext = pathinfo($aquivoNome, PATHINFO_EXTENSION);

                $diretorio = __DIR__ . '/../docs/';
                $arquivoLocal = $diretorio.$aquivoNome; // caminho absoluto do arquivo
                // Verifica se o arquivo não existe
                if(!file_exists($arquivoLocal)) {
                    // Exiba uma mensagem de erro caso ele não exista
                    $arquivoLocal = 'https://ecol.gepros1.com.br/docs/'.$aquivoNome;
                    header("Location: $arquivoLocal");
                    die();
                }


                // Aqui você pode aumentar o contador de downloads
                // Definimos o novo nome do arquivo
                $novoNome = $aquivoNome;

                /********ABRIR DOCUMENTO**********/
                if($ext=="pdf"){
                    header('Content-Type: application/pdf');
                    header(sprintf("Content-disposition: inline;filename=%s", basename($arquivoLocal)));
                    echo file_get_contents($arquivoLocal);
                    exit;
                }
                else{
                    header('Content-Description: File Transfer');
                    header('Content-Disposition: attachment; filename="'.$novoNome.'"');
                    header('Content-Type: application/octet-stream');
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: ' . filesize($aquivoNome));
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Expires: 0');
                    // Envia o arquivo para o cliente
                    readfile($aquivoNome);
                }
            }
        }
            
            Json::run(true, [], "Download com sucesso!");
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarDocumentosLancamentos(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $lancamentos_pk = isset($data['lancamentos_pk'])? $data['lancamentos_pk'] : "";

            (new Documento($this->pdo))->listarDocumentosLancamentos($lancamentos_pk);

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarDocumentoClienteLead(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            (new Documento($this->pdo))->listarDocumentoClienteLead($leads_pk);
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }


    ///-----------------------API APLICATIVO-------------------------------------------//
    public function getDocumentosApp(Request $request, Response $response, $args){
        try {
            $data = (object)$request->getParsedBody();
            $retorno = (new Documento($this->pdo))->getDocumentosApp($data);

            // Repassa exatamente como frontend espera
            $responseData = [
                "success" => $retorno->success ?? false,
                "message" => $retorno->message ?? "Erro desconhecido",
                "data"    => $retorno->data ?? []
            ];

            $response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withStatus(200);

        } catch (\Throwable $th) {
            $error = json_encode([
                'success' => false,
                'message' => $th->getMessage()
            ]);

            $response->getBody()->write($error);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withStatus(500);
        }
    }
    public function getAssinaturaColaborador(Request $request, Response $response, $args){
        try {
            $data = (object)$request->getParsedBody();
            $retorno = (new Documento($this->pdo))->getAssinaturaColaborador($data);

            // Repassa exatamente como frontend espera
            $responseData = [
                "success" => $retorno->success ?? false,
                "message" => $retorno->message ?? "Erro desconhecido",
                "data"    => $retorno->data ?? []
            ];

            $response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withStatus(200);

        } catch (\Throwable $th) {
            $error = json_encode([
                'success' => false,
                'message' => $th->getMessage()
            ]);

            $response->getBody()->write($error);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withStatus(500);
        }
    }
    public function getDocumentoByIdApp(Request $request, Response $response, $args){
        try {
            $data = (object)$request->getParsedBody();
            $retorno = (new Documento($this->pdo))->getDocumentoByIdApp($data);

            // Repassa exatamente como frontend espera
            $responseData = [
                "success" => $retorno->success ?? false,
                "message" => $retorno->message ?? "Erro desconhecido",
                "data"    => $retorno->data ?? []
            ];

            $response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withStatus(200);

        } catch (\Throwable $th) {
            $error = json_encode([
                'success' => false,
                'message' => $th->getMessage()
            ]);

            $response->getBody()->write($error);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withStatus(500);
        }
    }
    public function salvarAssinaturaColaborador(Request $request, Response $response, $args){
        try {
            $data = (object)$request->getParsedBody();
            $retorno = (new Documento($this->pdo))->salvarAssinaturaColaborador($data);

            // Repassa exatamente como frontend espera
            $responseData = [
                "success" => $retorno->success ?? false,
                "message" => $retorno->message ?? "Erro desconhecido",
                "data"    => $retorno->data ?? []
            ];

            $response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withStatus(200);

        } catch (\Throwable $th) {
            $error = json_encode([
                'success' => false,
                'message' => $th->getMessage()
            ]);

            $response->getBody()->write($error);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withStatus(500);
        }
    }
    // Função principal
    public function assinarDocumentoApp(Request $request, Response $response, $args){
        try {
            $data = (object)$request->getParsedBody();

            if (empty($data->documento_id) || empty($data->ds_colaborador) || empty($data->ds_cpf)) {
                throw new Exception("Campos obrigatórios ausentes: documento_id, nome ou cpf");
            }

            // Buscar documento base64
            $arquivoBase64 = (new Documento($this->pdo))->getDocumentoByIdApp($data);
            if (!$arquivoBase64->data['docsData']) throw new Exception("Documento não encontrado");

            // Buscar assinatura
            $assinaturaBase64 = (new Documento($this->pdo))->getAssinaturaColaborador($data);
            if (!$assinaturaBase64->data[0]['assinatura']) throw new Exception("Assinatura não encontrada");

            // Detectar tipo do arquivo
            $tipoMime = $this->identificarTipoArquivoBase64($arquivoBase64->data['docsData']);

            switch ($tipoMime) {
                case 'application/pdf':
                    // já está OK
                    break;

                case 'application/msword':
                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                    $arquivoBase64->data['docsData'] = $this->converterWordParaPdf($arquivoBase64->data['docsData']);
                    break;

                case 'image/jpeg':
                case 'image/png':
                    $ext = explode('/', $tipoMime)[1];
                    $arquivoBase64->data['docsData'] = $this->converterImagemParaPdf($arquivoBase64->data['docsData'], $ext);
                    break;

                default:
                    throw new Exception("Tipo de arquivo não suportado para assinatura: $tipoMime");
            }

            // Dados adicionais
            $dadosTexto = [
                'nome' => $data->ds_colaborador,
                'cpf' => $data->ds_cpf,
                'data_hora' => date("d/m/Y H:i:s"),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Desconhecido',
                'dispositivo' => $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido'
            ];

            // Assinar PDF
            $pdfAssinadoBinario = $this->assinarPdfComImagem(
                $arquivoBase64->data['docsData'],
                $assinaturaBase64->data[0]['assinatura'],
                $dadosTexto
            );

            // Atualizar base
            (new Documento($this->pdo))->updateDocAssinado($data->documento_id, $pdfAssinadoBinario, $data);

            // Resposta final
            $responseData = [
                "success" => true,
                "message" => "PDF assinado com sucesso",
                "data" => [
                    "nome" => $data->ds_colaborador,
                    "cpf" => $data->ds_cpf,
                    "data_hora_assinatura" => $dadosTexto['data_hora'],
                    "ip" => $dadosTexto['ip'],
                    "dispositivo" => $dadosTexto['dispositivo']
                ]
            ];

            $response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withStatus(200);

        } catch (\Throwable $th) {
            $error = [
                'success' => false,
                'message' => $th->getMessage()
            ];

            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withStatus(500);
        }
    }


    private function assinarPdfComImagem(string $pdfBase64, string $assinaturaBase64, array $dadosTexto): string {
        // 1. Decodifica PDF base64
        $pdfBlob = base64_decode($pdfBase64);
        if ($pdfBlob === false) {
            throw new Exception("Falha ao decodificar PDF base64.");
        }

        // 2. Verifica e trata o base64 da imagem da assinatura (remove prefixo se tiver)
        if (preg_match('/^data:image\/(\w+);base64,/', $assinaturaBase64, $type)) {
            $tipoImagem = strtolower($type[1]); // ex: png, jpg, jpeg
            $assinaturaBase64 = substr($assinaturaBase64, strpos($assinaturaBase64, ',') + 1);
        } else {
            throw new Exception("Formato da imagem de assinatura inválido.");
        }

        $assinaturaBlob = base64_decode($assinaturaBase64);
        if ($assinaturaBlob === false) {
            throw new Exception("Falha ao decodificar imagem base64.");
        }

        // 3. Cria arquivos temporários para PDF e imagem
        $pdfTemp = tempnam(sys_get_temp_dir(), 'pdf');
        $assinaturaTemp = tempnam(sys_get_temp_dir(), 'img') . '.' . $tipoImagem;

        file_put_contents($pdfTemp, $pdfBlob);
        file_put_contents($assinaturaTemp, $assinaturaBlob);

        // 4. Inicializa FPDI e importa páginas do PDF original
        $pdf = new \setasign\Fpdi\Fpdi();
        $pageCount = $pdf->setSourceFile($pdfTemp);

        for ($i = 1; $i <= $pageCount; $i++) {
            $tplId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($tplId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tplId);
        }

        // 5. Adiciona nova página com dados e assinatura
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);

        $marginLeft = 10;
        $y = 30;

        $pdf->SetXY($marginLeft, $y);
        $pdf->Cell(0, 5, "Nome: " . ($dadosTexto['nome'] ?? ''), 0, 1);
        $pdf->SetX($marginLeft);
        $pdf->Cell(0, 5, "CPF: " . ($dadosTexto['cpf'] ?? ''), 0, 1);
        $pdf->SetX($marginLeft);
        $pdf->Cell(0, 5, "Data e Hora da Assinatura: " . ($dadosTexto['data_hora'] ?? ''), 0, 1);
        $pdf->SetX($marginLeft);
        $pdf->Cell(0, 5, "IP: " . ($dadosTexto['ip'] ?? ''), 0, 1);
        $pdf->SetX($marginLeft);
        $pdf->Cell(0, 5, "Dispositivo: " . ($dadosTexto['dispositivo'] ?? ''), 0, 1);

        $pdf->Image($assinaturaTemp, $marginLeft, $pdf->GetY() + 10, 40, 20);

        // 6. Remove arquivos temporários
        unlink($pdfTemp);
        unlink($assinaturaTemp);

        // 7. Retorna o PDF gerado como binário puro (pronto para LONG BLOB)
        return $pdf->Output('S'); 
    }
    private function identificarTipoArquivoBase64(string $base64): string {
        $decoded = base64_decode($base64, true);
        if ($decoded === false) return 'desconhecido';

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        return $finfo->buffer($decoded);
    }

    private function converterImagemParaPdf(string $imagemBase64, string $tipoImagem): string {
        $imagemBlob = base64_decode($imagemBase64);
        $imagemTemp = tempnam(sys_get_temp_dir(), 'img') . '.' . $tipoImagem;
        $pdfTemp = tempnam(sys_get_temp_dir(), 'pdf');

        file_put_contents($imagemTemp, $imagemBlob);

        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->Image($imagemTemp, 10, 10, 180);
        $pdf->Output('F', $pdfTemp);

        unlink($imagemTemp);
        return base64_encode(file_get_contents($pdfTemp));
    }

    private function converterWordParaPdf(string $wordBase64): string {
        // 1. Decodifica o conteúdo base64
        $wordBlob = base64_decode($wordBase64);
        if ($wordBlob === false) {
            throw new \Exception("Erro ao decodificar o conteúdo do Word.");
        }

        // 2. Cria arquivos temporários
        $wordTemp = tempnam(sys_get_temp_dir(), 'word') . '.docx';
        $pdfTemp = tempnam(sys_get_temp_dir(), 'pdf');

        file_put_contents($wordTemp, $wordBlob);

        // 3. Define o renderizador corretamente
        \PhpOffice\PhpWord\Settings::setPdfRendererName(\PhpOffice\PhpWord\Settings::PDF_RENDERER_DOMPDF);
        \PhpOffice\PhpWord\Settings::setPdfRendererPath(realpath(__DIR__ . '/../../../vendor/dompdf/dompdf'));

        // 4. Valida se DomPDF está disponível
        if (!class_exists(\Dompdf\Dompdf::class)) {
            throw new \Exception("DomPDF não foi encontrado. Verifique se 'dompdf/dompdf' está instalado corretamente.");
        }

        // 5. Carrega o Word e converte para PDF
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($wordTemp);
        $pdfWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
        $pdfWriter->save($pdfTemp);

        // 6. Retorna PDF em base64
        $pdfBase64 = base64_encode(file_get_contents($pdfTemp));

        // 7. Limpa temporários
        unlink($wordTemp);
        unlink($pdfTemp);

        return $pdfBase64;
    }







 


}