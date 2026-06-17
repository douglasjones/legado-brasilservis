<?php

namespace App\Controller;

use App\Model\Afd;
use App\Model\Colaborador;
use App\Model\Conta;
use App\Model\Lead;
use App\Model\Ponto;
use App\Utils\Json;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class AfdController extends BaseController {

    public function receptivo(Request $request, Response $response, $args) {
        try{
            $this->view->render($response, 'afd/receptivo.twig');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function cadForm(Request $request, Response $response, $args){
        try{
            $data = $request->getQueryParams();
            $dadosConta = (new Conta($this->pdo))->listarTodos();
            $dadosLead = (new Lead($this->pdo))->listarTodos("");
            
            foreach ($dadosConta->data as $dadosConta){
                $conta[] = array(
                    "pk" => $dadosConta["pk"],
                    "ds_razao_social"=> $dadosConta["ds_razao_social"],
                );
            }

            foreach ($dadosLead->data as $dadosLead){
                $lead[] = array(
                    "pk" => $dadosLead["pk"],
                    "ds_lead"=> $dadosLead["ds_lead"],
                );
            }

          
           
            $this->view->render($response, 'afd/cadForm.twig',array(
                "dadosConta"=>$conta,
                "dadosLead"=>$lead
            ));
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function salvar(Request $request, Response $response, $args){
        try{
            $data = $_POST;
            
            $ds_periodo = isset($data['date_range_filter'])? $data['date_range_filter'] : "";
            $conta_pk = isset($data['conta_pk'])? $data['conta_pk'] : "";
            $leads_pk = isset($data['leads_pk'])? $data['leads_pk'] : "";
            $arrColaborador = isset($data['colaborador_pk'])? $data['colaborador_pk'] : "";
          
            $aux = explode('-', $ds_periodo);
            $dt_periodo_ini = trim($aux[0]);
            $dt_periodo_fim = trim($aux[1]) ;

          
            if(!isset($data['colaborador_pk'])){
                
                Json::run(false,[],"Selecione ao menos um colaborador!!");
                die();
            }
            
            for($i=0;$i<count($arrColaborador);$i++){
                
                //PEGAR DADOS CONTA
                $dadosConta = (new Conta($this->pdo))->listarPorPk($conta_pk);
                
                //PEGAR DADOS COLABORADOR;
                $dadosColaborador = (new Colaborador($this->pdo))->listarPk($arrColaborador[$i]);
                
                //PEGAR DADOS PONTO
                $dadosPonto = (new Ponto($this->pdo))->pegarInformacaoPontoAfd(
                    $dt_periodo_ini,
                    $dt_periodo_fim,
                    $conta_pk,
                    $leads_pk,
                    $arrColaborador[$i]
                );
                
                for($l=0;$l<count($dadosPonto->data);$l++){
                    $records[] = array(
                        
                            'type' => '3', // Tipo de registro 3 - Marcações de ponto
                            'date' => $dadosPonto->data[$l]['dt_ponto'],
                            'time' => $dadosPonto->data[$l]['hora_ponto'],
                            'pis' => $dadosPonto->data[$l]['ds_pis'],
                            'nfr' => '00000000000000123'
                        );
                }

               
               
                //SALVAR NA TABELA AFD
                
                //CRIAR ARQUIVO AFD
                $filePath = __DIR__ . '/../docs/afd/'.$arrColaborador[$i].'-'.date('dmY').'.afd';
                $header = [
                    'cnpj' => $dadosConta->data[0]['ds_cpf_cnpj'],      // CNPJ da empresa
                    'cei' => $dadosConta->data[0]['ds_cei'],         // CEI da empresa
                    'companyName' => $dadosConta->data[0]['ds_razao_social'], // Nome da empresa
                    'serialNumber' => '12345678901234567', // Número de série do REP
                    'date' => date('dmY'),           // Data de geração do arquivo
                    'time' => date('His')            // Hora de geração do arquivo
                ];
                
                $directory = dirname($filePath);

                if (!is_dir($directory)) {
                    if (!mkdir($directory, 0755, true)) {
                        throw new Exception("Unable to create directory: " . $directory);
                    }
                }

                $file = fopen($filePath, 'w');
        
                if ($file === false) {
                    throw new Exception("Unable to open file for writing: " . $filePath);
                }
        
                // Escreve o cabeçalho do arquivo
                fwrite($file, $this->generateHeader($header) . "\n");
        
                // Escreve os registros
                foreach ($records as $record) {
                    fwrite($file, $this->generateRecord($record) . "\n");
                }
                // Escreve o trailer do arquivo
                fwrite($file, $this->generateTrailer(count($records)) . "\n");

        
                fclose($file);

                //PEGAR O ARQUIVO E TRANSFORMAR EM BLOB

                $fileContent = file_get_contents($filePath);

                
               
                $afd = [
                    "contas_pk"=>$conta_pk,
                    "leads_pk"=>$leads_pk,
                    "colaborador_pk"=> $arrColaborador[$i],
                    "dt_periodo_ini"=>$dt_periodo_ini,
                    "dt_periodo_fim"=>$dt_periodo_fim,
                    "doc"=> ($fileContent)
                ];

                

                $retorno = (new Afd($this->pdo))->salvar($afd);
                
            }
            
            Json::run(true,[],"Salvo com sucesso!!");
           
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function downloadAfd(Request $request, Response $response, $args) {
        try{
            $data = $request->getQueryParams();

            $pk = $args['pk']; 
    
            $retorno = (new Afd($this->pdo))->listarPorPk($pk);
            
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment;filename="anexo-afd.afd');
            header('Content-Transfer-Enconding: binary');
            echo ($retorno->data[0]["doc"]);
            die();
           
            Json::run(true, [], "Download com sucesso!");
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    public function listarGrid(Request $request, Response $response, $args) {
        
        try{
            $data = $request->getQueryParams();
            $ds_beneficio = isset($data['ds_beneficio'])? $data['ds_beneficio'] : "";
            $ic_status = isset($data['ic_status'])? $data['ic_status'] : "";
            $retorno = (new Afd($this->pdo))->listarGrid();

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Throwable $th) {
            return $response->withJson((object)[
                'error' => $th->getMessage()
            ], 500, []);
        }
    }

    private function generateHeader($header) {
        // Exemplo de cabeçalho ajustado conforme as especificações do AFD
        return "1" .
               str_pad($header['cnpj'], 14, "0", STR_PAD_LEFT) .
               str_pad($header['cei'], 12, "0", STR_PAD_LEFT) .
               str_pad($header['companyName'], 150) .
               str_pad($header['serialNumber'], 17) .
               str_pad($header['date'], 8) .
               str_pad($header['time'], 6);
    }

    private function generateRecord($record) {
        // Exemplo de registro de detalhe ajustado conforme as especificações do AFD
        return "3" .
               $record['type'] .
               date('dmY', strtotime($record['date'])) .
               date('His', strtotime($record['time'])) .
               str_pad($record['pis'], 12, "0", STR_PAD_LEFT) .
               str_pad($record['nfr'], 17, "0", STR_PAD_LEFT) .
               str_pad("", 16, "0");
    }

    private function generateTrailer($recordCount) {
        // Exemplo de trailer ajustado conforme as especificações do AFD
        return "9" . str_pad($recordCount, 9, "0", STR_PAD_LEFT);
    }
}