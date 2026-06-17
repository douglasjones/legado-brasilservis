<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\MultipartStream;
use SimpleXMLElement;
use stdClass;
use Throwable;

    
    

class NfeApi {

    public $pdo;

    private $client; // Removido o tipo de retorno
    protected $history = []; // Removido o tipo de retorno

    public function __construct($pdo) {
        try{
            $this->pdo = $pdo;
            $stack = HandlerStack::create(new CurlMultiHandler());
            $stack->push(Middleware::retry(
                (function () {
                    return function (
                        $retries,
                        Request $request,
                        Response $response = null,
                        RequestException $exception = null
                    ) {
                        if ($retries >= 5) { return false; }

                        if ($exception !== null) { return true; }

                        if ($response !== null) {
                            if ($response->getStatusCode() >= 500 && $response->getStatusCode() <= 599) { return true; }
                        }

                        return false;
                    };
                })(),
                (function () { return function ($numberOfRetries) { return 1000 * $numberOfRetries; }; })() //Delay do retry
            ));

            $history = Middleware::history($this->history);
            $stack->push($history);

            $this->client = new Client([
                'base_uri'          => 'https://nfe.gepros6.com.br/',
                'http_errors'       => false,
                'connect_timeout'   => 10.0,
                'timeout'           => 60.0,
                'force_ip_resolve'  => 'v4',
                'verify'            => false,
                'handler'           => $stack
            ]);
        }
        catch(Throwable $th){
            print_r($th->getMessage());
            die();
        }
        
    }

    public function contaLeadSalvar($body) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

            $request = $this->client->request('POST','https://nfe.gepros6.com.br/contaLeadSalvar', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);

            
        
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $data =  json_decode($response);
    
            
            
            

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $data;
    }
    public function salvarControleNfse($body) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

           

            $request = $this->client->request('POST','https://nfe.gepros6.com.br/salvarControleNfse', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);
            

        
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $data =  json_decode($response);


    
         
            
            

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $data;
    }

    public function salvarIssMunicipio($body) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

           

            $request = $this->client->request('POST','https://nfe.gepros6.com.br/salvarIssMunicipio', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);
            

        
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $data =  json_decode($response);

         
            
            

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $data;
    }
    public function listarIssMunicipioPorPk($pk) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

            $body = [
                'pk'  => $pk
            ];


            $request = $this->client->request('GET','https://nfe.gepros6.com.br/listarIssMunicipioPorPk', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);


        
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $data =  json_decode($response);
            
            
            

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $data;
    }
    public function pegarAliquotaPorMunicipio($body) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty



            $request = $this->client->request('POST','https://nfe.gepros6.com.br/pegarAliquotaPorMunicipio', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);


        
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $data =  json_decode($response);
            
            
            
            

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $data;
    }
    
    public function contaLeadConsulta($ds_dominio, $ds_cnpj) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

            $body = [
                'ds_dominio'  => $ds_dominio,
                'ds_cnpj' => $ds_cnpj
            ];


            $request = $this->client->request('GET','https://nfe.gepros6.com.br/contaLeadConsulta', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);


        
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $data =  json_decode($response);
            
            

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $data;
    }

    public function listarCidade($ds_uf) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

            $body = [
                'ds_uf'  => $ds_uf
            ];


            $request = $this->client->request('GET','https://nfe.gepros6.com.br/listarCidade', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);


        
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $data =  json_decode($response);
            
            

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $data;
    }

    public function listarGridIssMunicipio(
        $ds_uf,
        $ds_cidade,
        $ic_status
    ) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

            $body = [
                'ds_uf'  => $ds_uf,
                'ds_cidade' => $ds_cidade,
                'ic_status' => $ic_status,
                "ds_dominio"=>$_SESSION['session_user']['par11']
            ];


            $request = $this->client->request('GET','https://nfe.gepros6.com.br/listarGridIssMunicipio', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);


        
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $data =  json_decode($response);
            
           
            echo json_encode($data);
            exit(0);

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $data;
    }

    

    public function contaConfigConsulta($contas_origem_pk, $ds_cnpj,$ds_dominio) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

            $body = [
                'contas_origem_pk'  => $contas_origem_pk,
                'ds_cnpj' => $ds_cnpj,
                'ds_dominio'=>$ds_dominio
            ];

           
            $request = $this->client->request('GET','https://nfe.gepros6.com.br/contaConfigConsulta', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);


        
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $data =  json_decode($response);
            
           
            

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        //return $data->data;
        echo json_encode($data->data);
        exit(0);
    }
    public function contaConfigListarEmpresas($contas_origem_pk,$ds_dominio) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

            $body = [
                'contas_origem_pk'  => $contas_origem_pk,
                'ds_dominio' =>$ds_dominio
            ];

            $request = $this->client->request('POST','https://nfe.gepros6.com.br/contaConfigListarEmpresas', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);
    
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents(); 
            $data =  json_decode($response);
            
            

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return $th->getMessage();
        }
        return $data;
    }

    public function contaConfigConsultaPk($pk) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

            $body = [
                'pk'  => $pk
            ];

            $request = $this->client->request('POST','https://nfe.gepros6.com.br/contaConfigConsultaPk', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);
        
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $retorno =  json_decode($response);

            if($retorno->status){
                $return->status = true; //status false
                $return->data = $retorno->data[0]; //Return data como empty
                $return->message = "Dados carregados com sucesso!"; //Return data como empty
            }
            else{
                $return->data = []; //Return data como empty
                $return->message = "Não localizamos nenhum dado"; //Return data como empty
            }

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $return;
    }

    public function excluirDocs($pk) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

            $body = [
                'pk'  => $pk
            ];

            $request = $this->client->request('POST','https://nfe.gepros6.com.br/excluirDocs', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);
            
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $retorno =  json_decode($response);
         
          
            $return->status = true; //status false
            $return->data = []; //Return data como empty
            $return->message = "Excluido com sucesso!"; //Return data como empty

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $return;
    }
    public function excluirServico($pk) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

            $body = [
                'pk'  => $pk
            ];

            $request = $this->client->request('POST','https://nfe.gepros6.com.br/excluirServico', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);
            
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $retorno =  json_decode($response);
         
          
            $return->status = true; //status false
            $return->data = []; //Return data como empty
            $return->message = "Excluido com sucesso!"; //Return data como empty

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $return;
    }

    public function pegarDocConta($body) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

            $request = $this->client->request('POST','https://nfe.gepros6.com.br/pegarDocsConta', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);
        
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $retorno =  json_decode($response);

            if($retorno->status){
                $return->status = true; //status false
                $return->data = $retorno->data[0]; //Return data como empty
                $return->message = "Dados carregados com sucesso!"; //Return data como empty
            }
            else{
                $return->data = []; //Return data como empty
                $return->message = "Não localizamos nenhum dado"; //Return data como empty
            }

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $return;
    }

    public function contaConfigSalvar($body) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

      
            $request = $this->client->request('POST','https://nfe.gepros6.com.br/contaConfigSalvar', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);


            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
           
            
            $retorno =  json_decode($response);
            
           
            
            if($retorno->status){
                $return->status = true; //status false
                $return->data = $retorno->data; //Return data como empty
                $return->message = $retorno->message; //Return data como empty
            }else{
                $return->data = []; //Return data como empty
                $return->message = $retorno->message; //Return data como empty
            }

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $return;
    }
    public function salvarNfeServico($body) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty
        
            $request = $this->client->request('POST','https://nfe.gepros6.com.br/salvarNfeServico', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);

            
            
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $retorno =  json_decode($response);
           
           
            
            if($retorno->status){
                $return->status = true; //status false
                $return->data = $retorno->data; //Return data como empty
                $return->message = "Dados carregados com sucesso!"; //Return data como empty
            }
            else{
                $return->data = []; //Return data como empty
                $return->message = "Não foi possivel inserir"; //Return data como empty
            }

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $return;
    }
    public function listarNfeServico($contas_pk) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

            $body =[
                "contas_pk"=>$contas_pk,
                "ds_dominio"=>$_SESSION['session_user']['par11']
            ];

            $request = $this->client->request('POST','https://nfe.gepros6.com.br/listarNfeServico', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);
            
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $retorno =  json_decode($response);
            
           
            $return->status = true; //status false
            $return->data = $retorno->data; //Return data como empty
            $return->message = "Informações carregados com sucesso!"; //Return data como empty
                

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return ($return);
       
    }
    public function listarDadosServico($codigoServico) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

            $body = [
                "codigoServico"=>$codigoServico
            ];

            $request = $this->client->request('POST','https://nfe.gepros6.com.br/listarDadosServico', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);
            
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $retorno =  json_decode($response);
            
            if($retorno->status){
                $return->status = true; //status false
                $return->data = $retorno->data; //Return data como empty
                $return->message = "Dados carregados com sucesso!"; //Return data como empty
            }
            else{
                $return->data = []; //Return data como empty
                $return->message = "Não localizamos nenhum dado"; //Return data como empty
            }

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $return;
    }

    public function listarServicosPk($servicos_pk) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty
            $body =[
                "servicos_pk"=>$servicos_pk,
                "ds_dominio"=>$_SESSION['session_user']['par11']
            ];
            $request = $this->client->request('POST','https://nfe.gepros6.com.br/listarServicosPk', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);
            
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $retorno =  json_decode($response);

            
            if($retorno->status){
                $return->status = true; //status false
                $return->data = $retorno->data; //Return data como empty
                $return->message = "Dados carregados com sucesso!"; //Return data como empty
            }
            else{
                $return->data = []; //Return data como empty
                $return->message = "Não localizamos nenhum dado"; //Return data como empty
            }

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $return;
    }
    public function salvarContasServicoConfig($body) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty


            $request = $this->client->request('POST','https://nfe.gepros6.com.br/salvarContasServicoConfig', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);
            
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $retorno =  json_decode($response);
            
            if($retorno->status){
                $return->status = true; //status false
                $return->data = $retorno->data; //Return data como empty
                $return->message = "Dados carregados com sucesso!"; //Return data como empty
            }
            else{
                $return->data = []; //Return data como empty
                $return->message = "Não localizamos nenhum dado"; //Return data como empty
            }

        } catch (Throwable $th) {
           
            $return->data = []; //Return data como empty
            $return->message = $th->getMessage(); //Return data como empty $th->getMessage();
        }
        return $return;
    }

    public function listarNfse($body) {
        try {
            
            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

        

            $request = $this->client->request('POST','https://nfe.gepros6.com.br/listarNfse', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);
    
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents(); 
            $data =  json_decode($response);
           
            try{
                for($i=0;$i<count($data->data->data);$i++){

                    
                    if($data->data->data[$i]->ic_status==1){
                     
                     //VERIFICA SE EXISTE O LANCAMENTO, SE NÃO EXISTIR CADASTRAR.
                         if($data->data->data[$i]->numero!=""){
                           
                             $sql ="";
                             $sql.="SELECT * FROM lancamentos_financeiros WHERE nfse_pk = ".$data->data->data[$i]->numero;
                             
                             $stmt = $this->pdo->prepare( $sql );
                             $stmt->execute();
                             $count = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                             $sql ="";
                             $sql.="SELECT * FROM lancamentos_financeiros WHERE ds_num_documento = '".$data->data->data[$i]->numero."'";
                             
                             $stmt = $this->pdo->prepare( $sql );
                             $stmt->execute();
                             $countNum = $stmt->fetchAll(\PDO::FETCH_ASSOC);
     
                             if(count($count)==0 && count($countNum)==0){



                                //PEGAR PK LEAD POR RAZAO SOCIAL
                                $grupo_lancamento_pk = (new Lead($this->pdo))->pegarPkPorRazaoSocial($data->data->data[$i]->ds_tomador,$data->data->data[$i]->ds_cnpj_tomador);
                                //PEGAR PEGAR EMPRESA POR RAZAOSOCIAL
                                $empresa_lancamento_pk = (new Conta($this->pdo))->pegarPkPorRazaoSocial($data->data->data[$i]->ds_prestador);

                                 $fields = array();
                                 $fields['dt_cadastro'] = "sysdate()";
                                 $fields["dt_ult_atualizacao"] = "sysdate()";
                                 $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                                 $fields["usuario_cadastro_pk"] = $_SESSION['session_user']['par1'];
                                 $fields["ds_lancamento"] = "Receita NFE ".$data->data->data[$i]->numero;
                                 $fields["ic_tipo_num_documento"] = 2;
                                 $fields["ds_num_documento"] = $data->data->data[$i]->numero;
                                 $fields["categorias_financeiras_pk"] = 2;
                                 $fields["tipo_lancamento_pk"] = 1;
                                 $fields["tipos_operacao_pk"] = 344;
                                 $fields["tipo_grupo_lancamento_pk"] = 1;
                                 $fields["grupo_lancamento_pk"] = $grupo_lancamento_pk;
                                 $fields["ic_parcela"] = 1;
                                 $fields["metodos_pagamento_pk"] = 5;
                                 $fields["dt_vencimento"] = $data->data->data[$i]->dt_vencimento;
                                 //$fields["dt_faturamento"] = Util::DataYMD($data->data->data[$i]->dataEmissao);
                                 $fields["dt_faturamento"] = date('Y-m-d');
                                 $fields["vl_lancamento"] = $data->data->data[$i]->valor_liquido;
                                 $fields["empresa_lancamento_pk"] = $empresa_lancamento_pk;
                                 $fields["ic_status_lancamento"] = 2;
                                 $fields["obs_lancamento"] = 'Carga de XML de NFSE';
                                 $fields["ic_status_pagamento"] = 0;
                                 $fields["nfse_pk"] = $data->data->data[$i]->numero;
                         
                                 Util::execInsert("lancamentos_financeiros", $fields,$this->pdo);
                             }
                         }
                    } 
                    if($data->data->data[$i]->ic_status==2 || $data->data->data[$i]->ic_status==3){
                         if($data->data->data[$i]->numero!=""){
                             Util::execDelete('lancamentos_financeiros', ' nfse_pk='.$data->data->data[$i]->numero, $this->pdo);
                         }
                    }
                 }
            }
            catch (Throwable $e){
                print_r($e->getMessage());
                die();
            }
            
            
            $sql ="";
            $sql.="SELECT `pk`, `numero_nota`, `ds_prestador`, `ds_tomador`, `data_emissao`, `valor_total` FROM `nfse_local` ";
            $sql.=" where 1=1";
            if($body['ds_numero_nfse']!=""){
                $sql.=" and numero_nota =".$body['ds_numero_nfse'];
            }
            if(trim($body['ds_prestador'])!=""){
                $sql.=" and ds_prestador like '%".$body['ds_prestador']."%'";
            }
            if(trim($body['ds_tomador'])!=""){
                $sql.=" and ds_tomador like '%".$body['ds_tomador']."%'";
            }
            //PARA NÃO CARREGAR AS EMITIDAS NO SISTEMA
            if($body=['ic_status']!="" && $body=['ic_status']!=1){
                $sql.=" and numero_nota =00000";
            }
            /*if($body=['dt_emissao_ini']!=""){
                $sql.=" and nr.data_emissao BETWEEN '".$body=['dt_emissao_ini']." 00:00:00' and '".$body=['dt_emissao_fim']." 23:59:59'";
            }*/
            if($body['dt_cancelamento_ini']!=""){
                $sql.=" limit 0";
            }
          
            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


            
            for($i=count($data->data->data);$i<count($rows);$i++){
                $data->data->data[$i]['pk'] = $rows[$i]['pk'];
                $data->data->data[$i]['id_notas'] = 0;
                $data->data->data[$i]['ic_verificado'] = 1;
                $data->data->data[$i]['numero'] = $rows[$i]['numero_nota'];
                $data->data->data[$i]['dataEmissao'] = $rows[$i]['data_emissao'];
                $data->data->data[$i]['dt_cancelamento'] = "";
                $data->data->data[$i]['ds_tomador'] = $rows[$i]['ds_tomador'];
                $data->data->data[$i]['ds_prestador'] = $rows[$i]['ds_prestador'];
                $data->data->data[$i]['valor'] = str_replace(",",".",$rows[$i]['valor_total']);
                $data->data->data[$i]['minutos_passados'] = 15;
                $data->data->data[$i]['ds_status'] = "Emitido fora da plataforma";
            }
            
            

            
            

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        echo json_encode($data->data);
        exit(0);
    }

    public function cancelarNota($id_nota,$pk) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = true; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

        
            $body =[
                "id_notas"=>$id_nota,
                "pk"=>$pk
            ];
            $request = $this->client->request('POST','https://nfe.gepros6.com.br/cancelarNota', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);
    
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents(); 
            $data =  json_decode($response);
           
            
            

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        return $return;
    }

    public function downloadNfse($id_notas) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

        

            $request = $this->client->request('GET','https://api.plugnotas.com.br/nfse/pdf/'.$id_notas, [
                'headers' => [
                    //'X-API-Key' => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                    'X-API-Key' => 'afc1f32748f103c355fe26bd417e2f8b'
                ],
                //'json'=>$body
            ]);
    
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents(); 
            $data =  json_decode($response);

            
            // Crie um arquivo temporário para salvar os bytes do PDF
            $tmpFilePath = tempnam(sys_get_temp_dir(), 'pdf_');

            // Abra o arquivo temporário para escrita
            $fileHandle = fopen($tmpFilePath, "wb");
            // Escreva os bytes do PDF no arquivo
            fwrite($fileHandle, $response);
            // Feche o arquivo
            fclose($fileHandle);

            // Indique ao navegador que o conteúdo é um PDF
            header('Content-Type: application/pdf');
            // Indique ao navegador para exibir o conteúdo, não fazer download
            header('Content-Disposition: inline; filename="documento.pdf"');
            // Envie o conteúdo do arquivo para o navegador
            readfile($tmpFilePath);

            // Apague o arquivo temporário
            unlink($tmpFilePath);  

            echo json_encode($data->data);
            exit(0);
            
                  
            

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        
    }
    public function downloadNfseLancamento($notas_pk) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

            $body =[
                "numero"=>$notas_pk
            ];
            $request = $this->client->request('POST','https://nfe.gepros6.com.br/getIdNotasForLancamento', [
                /*'headers' => [
                    'X-API-Key'     => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                ],*/
                'json'=>$body
            ]);

            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents(); 
            $data =  json_decode($response);

        

            $request = $this->client->request('GET','https://api.plugnotas.com.br/nfse/pdf/'.$data->data->id_notas, [
                'headers' => [
                    //'X-API-Key' => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                    'X-API-Key' => 'afc1f32748f103c355fe26bd417e2f8b'
                ],
                //'json'=>$body
            ]);
    
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents(); 
            $data =  json_decode($response);

            
            // Crie um arquivo temporário para salvar os bytes do PDF
            $tmpFilePath = tempnam(sys_get_temp_dir(), 'pdf_');

            // Abra o arquivo temporário para escrita
            $fileHandle = fopen($tmpFilePath, "wb");
            // Escreva os bytes do PDF no arquivo
            fwrite($fileHandle, $response);
            // Feche o arquivo
            fclose($fileHandle);

            // Indique ao navegador que o conteúdo é um PDF
            header('Content-Type: application/pdf');
            // Indique ao navegador para exibir o conteúdo, não fazer download
            header('Content-Disposition: inline; filename="documento.pdf"');
            // Envie o conteúdo do arquivo para o navegador
            readfile($tmpFilePath);

            // Apague o arquivo temporário
            unlink($tmpFilePath);  

            echo json_encode($data->data);
            exit(0);
            
                  
            

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        
    }

    public function exibirXML($id_notas) {
        try {

            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            $return->code = ""; //Return data como empty

        
           
            $request = $this->client->request('GET','https://api.plugnotas.com.br/nfse/xml/'.$id_notas, [
                'headers' => [
                    //'X-API-Key' => '2da392a6-79d2-4304-a8b7-959572c7e44d'
                    'X-API-Key' => 'afc1f32748f103c355fe26bd417e2f8b'
                ],
                //'json'=>$body
            ]);
    
            $code = $request->getStatusCode();
            
            $response = $request->getBody()->getContents(); 
          
            $data =  json_decode($response);
            
            // Converte a resposta XML em um objeto SimpleXMLElement
            //$xml = simplexml_load_string($response);
            $xml = $this->stringToXML($response);

            // Verifica se a conversão foi bem-sucedida
            if ($xml !== false) {
                
                // Exibe o conteúdo do XML
                header('Content-Type: application/xml');
                echo $xml;
            } else {
                echo 'Erro ao processar o XML.';
            }
            die();
            
            

        } catch (Throwable $th) {
            print_r($th->getMessage());
            die();
            return null;
        }
        echo json_encode($data->data);
        exit(0);
    }

    // Função para gerar XML
    public function stringToXML($string) {
        $xml = new SimpleXMLElement('<root/>');
        
        // Separar a string com base em alguns delimitadores conhecidos
        // Aqui, usamos espaços como delimitadores genéricos, mas você pode ajustar conforme necessário
        $elements = preg_split('/\s+/', $string);
        
        // Adiciona cada elemento ao XML
        foreach ($elements as $index => $element) {
            // Cria um nome de nó genérico (element0, element1, etc.)
            $child = $xml->addChild("element$index", htmlspecialchars($element));
        }

        return $xml->asXML();
    }





    public function enviarCertificado($arquivo,$senha_certificado){

        try {
            $return = new StdClass; //Structure de return para controller
            $return->status = false; //status false
            $return->data = ""; //Return data como empty
            
            $headers = [
                'X-API-Key' => 'afc1f32748f103c355fe26bd417e2f8b'
            ];

            $multipartStream = new MultipartStream([
                [
                    'name'     => 'arquivo',
                    'contents'=> ($arquivo),
                    'filename' => "certificado-tansoft.pfx"
                ],
                [
                    'name'     => 'senha',
                    'contents' => $senha_certificado
                ]
            ]);

            
            $request = $this->client->request('POST','https://api.plugnotas.com.br/certificado', [
                'headers' => $headers,
                'body' =>$multipartStream
            ]);

            $code = $request->getStatusCode();
           
            $response = $request->getBody()->getContents();
            $data =  json_decode($response, true);
            
            
            if($code==409 || $code==200 || $code==201){
                if($data['error']!=''){
                    $return->status = true;
                    $return->message = $data['error']['message'];
                    $return->data = $data['error']['data']["current"]['id'];
                }
                else{
                    $return->status = true;
                    $return->message = $data['message'];
                    $return->data = $data['data']['id'];
                }
            }
            else{
                $return->status = false;
                $return->message = "PlugNotas- Erro ao cadastrar certificado.";
                $return->data = [];
            }
            
           
            
            return $return;

        } catch (Throwable $th) {
            $return->status = false;
            $return->message = $th->getMessage();
            $return->data =[];

            return $return;
        }
        
    }
    

}