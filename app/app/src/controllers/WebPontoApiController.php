<?php

namespace App\Controller;

use App\Model\Colaborador;
use App\Model\Ponto;
use App\Model\PontoFolha;
use App\Model\SolicitacaoAcessoApp;
use App\Utils\Json;
use App\Utils\Util;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class WebPontoApiController extends BaseController {
    private function getRequestData(Request $request){
        $data = $request->getParsedBody();

        if (is_array($data)) {
            return $data;
        }

        if (is_object($data)) {
            return (array)$data;
        }

        $rawBody = (string)$request->getBody();
        if ($rawBody !== "") {
            $decoded = json_decode($rawBody, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    private function resolveColaboradorPkPorCpf(array $data){
        $colaboradorPk = isset($data['colaborador_pk']) ? trim((string)$data['colaborador_pk']) : '';
        $dsCpf = isset($data['ds_cpf']) ? trim((string)$data['ds_cpf']) : '';

        if ($colaboradorPk !== '') {
            return [
                'status' => true,
                'colaborador_pk' => $colaboradorPk,
                'ds_cpf' => $dsCpf
            ];
        }

        if ($dsCpf === '') {
            return [
                'status' => false,
                'message' => 'Informe o colaborador por ds_cpf ou colaborador_pk.'
            ];
        }

        $retornoColaborador = (new Colaborador($this->pdo))->buscarColaboradorPorCpfApp($dsCpf);
        if (!$retornoColaborador->status || empty($retornoColaborador->data[0]['pk'])) {
            return [
                'status' => false,
                'message' => $retornoColaborador->message ?: 'Colaborador não encontrado para o CPF informado.'
            ];
        }

        return [
            'status' => true,
            'colaborador_pk' => $retornoColaborador->data[0]['pk'],
            'ds_cpf' => $retornoColaborador->data[0]['ds_cpf'] ?? $dsCpf
        ];
    }


    public function registraPontoApp(Request $request, Response $response, $args){
        try {
            $data = $this->getRequestData($request);

            $dadosResolucao = $data;
            if (isset($data['ds_cpf']) && trim((string)$data['ds_cpf']) !== '') {
                unset($dadosResolucao['colaborador_pk']);
            }

            $colaboradorResolvido = $this->resolveColaboradorPkPorCpf($dadosResolucao);
            if (!$colaboradorResolvido['status']) {
                $response->getBody()->write(json_encode([
                    "result" => "false",
                    "ic_status" => "2",
                    "message" => $colaboradorResolvido['message'],
                    "data" => "",
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }

            if (empty($data['dt_hora_ponto']) || empty($data['tipo_ponto_pk'])) {
                $response->getBody()->write(json_encode([
                    "result" => "false",
                    "ic_status" => "2",
                    "message" => "Dados insuficientes",
                    "data" => "",
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }

            $colaborador_pk = $colaboradorResolvido['colaborador_pk'];
            $dt_hora_ponto = $data['dt_hora_ponto'];
            $tipo_ponto_pk = $data['tipo_ponto_pk'];
            $ds_latitude = isset($data['ds_latitude']) ? $data['ds_latitude'] : null;
            $ds_longitude = isset($data['ds_longitude']) ? $data['ds_longitude'] : null;
            $img_ponto = isset($data['img_ponto']) ? $data['img_ponto'] : null;
            $leads_pk = isset($data['leads_pk']) ? $data['leads_pk'] : null;
            $agenda_colaborador_pk = isset($data['agenda_colaborador_padrao_pk']) ? $data['agenda_colaborador_padrao_pk'] : null;
            $contas_pk = isset($data['contas_pk']) ? $data['contas_pk'] : null;
            $icPontoForaTurno = isset($data['ic_ponto_fora_turno']) ? $data['ic_ponto_fora_turno'] : (isset($data['icPontoForaTurno']) ? $data['icPontoForaTurno'] : null);
            if(!empty($ds_latitude)){
                // PEGAR ENDEREÇO
                $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$ds_latitude}&lon={$ds_longitude}";
                
                $responseLocal = @file_get_contents($url);
                $geoData = $responseLocal ? json_decode($responseLocal, true) : null;
                
                $ds_localizacao = $geoData['display_name'] ?? "Endereço não encontrado";
            }
            else{
                $ds_localizacao = "Endereço não encontrado";
            }
            
        
            // Criando o objeto de modelo e salvando no banco
            $pontoModel = new Ponto($this->pdo);
            
            $dados_ponto = [
                "ic_tipo_app" => 2,
                "ds_dispositivo" => "New Aplicativo",
                "colaborador_pk" => $colaborador_pk,
                "contas_pk" => $contas_pk,
                "tipo_ponto_pk" => $tipo_ponto_pk,
                "dt_hora_ponto" => $dt_hora_ponto,
                "agenda_colaborador_padrao_pk" => $agenda_colaborador_pk,
                "leads_pk" => $leads_pk,
                "ds_localizacao" => $ds_localizacao,
                "img_ponto" => $img_ponto,
                "ds_imagem" => "FotoPonto.png",
                "ds_latitude" => $ds_latitude,
                "ds_longitude" => $ds_longitude,
                "ic_sincronizacao" => 1,
                "ic_ponto_fora_turno" => $icPontoForaTurno,
            ];
        
            $pk = $pontoModel->salvarPontoApp($dados_ponto);
            
            if (!empty($pk->data)) {
                $responseData = [
                    "result" => "true",
                    "ic_status" => "1",
                    "message" => "Registro salvo com sucesso.",
                    "data" => "",
                ];
            } else {
                $responseData = [
                    "result" => "false",
                    "ic_status" => "2",
                    "message" => $pk->message,
                    "data" => "",
                ];
            }
        
            // Retorna a resposta JSON corretamente no Slim Framework
            $response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        
        } catch (Throwable $th) {
            $errorResponse = ["error" => $th->getMessage()];

            $response->getBody()->write(json_encode($errorResponse, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
        
    }
    public function sincronizarPontoApp(Request $request, Response $response, $args){
        try {
            $data = $this->getRequestData($request);

            $colaboradorResolvido = $this->resolveColaboradorPkPorCpf($data);
            if (!$colaboradorResolvido['status']) {
                $response->getBody()->write(json_encode([
                    "result" => "false",
                    "ic_status" => "2",
                    "message" => $colaboradorResolvido['message'],
                    "data" => "",
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }

            if (empty($data['dt_hora_ponto']) || empty($data['tipo_ponto_pk'])) {
                $response->getBody()->write(json_encode([
                    "result" => "false",
                    "ic_status" => "2",
                    "message" => "Dados insuficientes",
                    "data" => "",
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }

            $colaborador_pk = $colaboradorResolvido['colaborador_pk'];
            $dt_hora_ponto = $data['dt_hora_ponto'];
            $tipo_ponto_pk = $data['tipo_ponto_pk'];
            $ds_latitude = isset($data['ds_latitude']) ? $data['ds_latitude'] : null;
            $ds_longitude = isset($data['ds_longitude']) ? $data['ds_longitude'] : null;
            $img_ponto = isset($data['img_ponto']) ? $data['img_ponto'] : null;
            $leads_pk = isset($data['leads_pk']) ? $data['leads_pk'] : null;
            $agenda_colaborador_pk = isset($data['agenda_colaborador_padrao_pk']) ? $data['agenda_colaborador_padrao_pk'] : null;
            $contas_pk = isset($data['contas_pk']) ? $data['contas_pk'] : null;
            $icPontoForaTurno = isset($data['ic_ponto_fora_turno']) ? $data['ic_ponto_fora_turno'] : (isset($data['icPontoForaTurno']) ? $data['icPontoForaTurno'] : null);
        
            if(!empty($ds_latitude)){
                $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$ds_latitude}&lon={$ds_longitude}";
                
                $responseLocal = @file_get_contents($url);
                $geoData = $responseLocal ? json_decode($responseLocal, true) : null;
                
                $ds_localizacao = $geoData['display_name'] ?? "Endereço não encontrado";
            }
            else{
                $ds_localizacao = "Endereço não encontrado";
            }
        
            // Criando o objeto de modelo e salvando no banco
            $pontoModel = new Ponto($this->pdo);
            
            $dados_ponto = [
                "ic_tipo_app" => 2,
                "ds_dispositivo" => "New Aplicativo",
                "colaborador_pk" => $colaborador_pk,
                "contas_pk" => $contas_pk,
                "tipo_ponto_pk" => $tipo_ponto_pk,
                "dt_hora_ponto" => $dt_hora_ponto,
                "agenda_colaborador_padrao_pk" => $agenda_colaborador_pk,
                "leads_pk" => $leads_pk,
                "ds_localizacao" => $ds_localizacao,
                "img_ponto" => $img_ponto,
                "ds_imagem" => "FotoPonto.png",
                "ds_latitude" => $ds_latitude,
                "ds_longitude" => $ds_longitude,
                "ic_sincronizacao" => 1,
                "ic_ponto_fora_turno" => $icPontoForaTurno,
            ];
        
            $pk = $pontoModel->sincronizarPontoApp($dados_ponto);
            
            if (!empty($pk->data)) {
                $responseData = [
                    "result" => "true",
                    "ic_status" => "1",
                    "message" => "Registro salvo com sucesso.",
                    "data" => "",
                ];
            } else {
                $responseData = [
                    "result" => "false",
                    "ic_status" => "2",
                    "message" => $pk->message,
                    "data" => "",
                ];
            }
        
            // Retorna a resposta JSON corretamente no Slim Framework
            $response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        
        } catch (Throwable $th) {
            $errorResponse = ["error" => $th->getMessage()];

            $response->getBody()->write(json_encode($errorResponse, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
        
    }
    public function listarPostoTrabalhoApp(Request $request, Response $response, $args)
    {
        try {
            $data = $this->getRequestData($request);
            $ds_cpf = $data['ds_cpf'] ?? "";
            $colaborador_pk = $data['colaborador_pk'] ?? "";

            $dadosResolucao = $data;
            if (trim((string)$ds_cpf) !== '') {
                unset($dadosResolucao['colaborador_pk']);
            }

            $resolucaoColaborador = $this->resolveColaboradorPkPorCpf($dadosResolucao);

            if ($resolucaoColaborador['status']) {
                $colaborador_pk = $resolucaoColaborador['colaborador_pk'];
                $ds_cpf = $resolucaoColaborador['ds_cpf'];
            }
            
            $pontoModel = new Ponto($this->pdo);
            $return = $pontoModel->listarPostoTrabalhoApp($ds_cpf, $colaborador_pk);

            // Verifica se há dados antes de acessar $return->data[0]
            $dados = $return->data ?? [];

            $responseData = [
                "result"  => $return->status ?? false,
                "message" => $return->message ?? "Erro desconhecido",
                "dados"   => $dados // Retorna o array completo
            ];

            // Retorna a resposta JSON corretamente no Slim Framework
            $response->getBody()->write(json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            
        } catch (Throwable $th) {
            $errorResponse = ["error" => $th->getMessage()];

            $response->getBody()->write(json_encode($errorResponse, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    public function pesquisarPonto(Request $request, Response $response, $args)
    {
        try {
            $data = $this->getRequestData($request);
            $resolucaoColaborador = $this->resolveColaboradorPkPorCpf($data);

            $ds_cpf = $data['ds_cpf'] ?? "";
            if ($resolucaoColaborador['status']) {
                $ds_cpf = $resolucaoColaborador['ds_cpf'];
            }

            $dt_ini = $data['dt_ini'] ?? "";
            $dt_fim = $data['dt_fim'] ?? "";
            
            $pontoModel = new Ponto($this->pdo);
            $return = $pontoModel->pesquisarPontoApp($ds_cpf, $dt_ini,$dt_fim);

            // Verifica se há dados antes de acessar $return->data[0]
            $dados = $return->data ?? [];
            $responseData = [
                "result"  => $return->status ?? false,
                "message" => $return->message ?? "Erro desconhecido",
                "dados"   => $dados // Retorna o array completo
            ];

            // Retorna a resposta JSON corretamente no Slim Framework
            $response->getBody()->write(json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            
        } catch (Throwable $th) {
            $errorResponse = ["error" => $th->getMessage()];

            $response->getBody()->write(json_encode($errorResponse, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

}
