<?php

namespace App\Model;

use App\Utils\Session;
use App\Utils\Util;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use Throwable;

class SolicitacaoAcessoApp {

	public $pdo;
    private $client; // Removido o tipo de retorno
    protected $history = []; // Removido o tipo de retorno

	public function __construct($pdo) {
        $this->pdo = $pdo;

        $stack = HandlerStack::create(new CurlMultiHandler());

        $stack->push(Middleware::retry(
            (function () {
                return function (
                    $retries,
                    \GuzzleHttp\Psr7\Request $request,
                    \GuzzleHttp\Psr7\Response $response = null,
                    \GuzzleHttp\Exception\RequestException $exception = null
                ) {
                    if ($retries >= 5) {
                        return false;
                    }

                    if ($exception !== null) {
                        return true;
                    }

                    if ($response !== null) {
                        if ($response->getStatusCode() >= 500 && $response->getStatusCode() <= 599) {
                            return true;
                        }
                    }

                    return false;
                };
            })(),
            (function () {
                return function ($numberOfRetries) {
                    return 1000 * $numberOfRetries;
                };
            })() // Delay do retry
        ));

        $history = Middleware::history($this->history);
        $stack->push($history);

        $this->client = new Client([
            'http_errors'      => false,
            'connect_timeout'  => 10.0,
            'timeout'          => 60.0,
            'force_ip_resolve' => 'v4',
            'verify'           => false,
            'handler'          => $stack
        ]);
    }

    public function excluir($pk){
        Util::execDelete('ponto_solicitacao_liberacao_app', ' pk='.$pk, $this->pdo);
    }

    public function salvar($solicitacao_acesso_app){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        
        $fields = array();
        $fields['ds_pin']  = $solicitacao_acesso_app['ds_pin'];
        $fields['colaborador_pk'] = $solicitacao_acesso_app['colaborador_pk'];
        $fields['id_cliente'] = $solicitacao_acesso_app['id_cliente'];
        $fields['ds_imagem'] = $solicitacao_acesso_app['ds_imagem'];
        
        $fields['ds_aparelho'] = $solicitacao_acesso_app['ds_aparelho'];
        $fields['usuario_aprovacao_pk'] = $solicitacao_acesso_app['usuario_aprovacao_pk'];
        $fields['obs'] = $solicitacao_acesso_app['obs'];
        $fields['ic_status'] = $solicitacao_acesso_app['ic_status'];

        if($solicitacao_acesso_app['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("ponto_solicitacao_liberacao_app", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("ponto_solicitacao_liberacao_app", $fields, " pk = ".$solicitacao_acesso_app['pk'],$this->pdo);
            $pk = $solicitacao_acesso_app['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;
    }

    public function liberarAcesso($solicitacao_acesso_app){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ic_status'] = $solicitacao_acesso_app['ic_status'];
        $fields['usuario_aprovacao_pk'] = $_SESSION['session_user']['par1'];
        $fields['dt_liberacao'] = "sysdate()";

        $apiPk = isset($solicitacao_acesso_app['api_pk']) ? $solicitacao_acesso_app['api_pk'] : "";
        $colaboradorPk = isset($solicitacao_acesso_app['colaborador_pk']) ? $solicitacao_acesso_app['colaborador_pk'] : "";

        if ($apiPk != "") {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'];
            $currentUrl = $protocol . $host;

            $body = [
                'ds_link' => $currentUrl,
                'pk' => $apiPk,
                'ic_status' => $solicitacao_acesso_app['ic_status'],
                'usuario_aprovacao_pk' => $_SESSION['session_user']['par1'],
            ];

            try {
                $request = $this->client->request('POST', 'https://webservice.gepros6.com.br/work/action.php?action=liberarAcesso', [
                    'json' => $body
                ]);

                $code = $request->getStatusCode();
                $response = $request->getBody()->getContents();

                if ($code < 200 || $code >= 300) {
                    $retorno->message = 'Falha ao liberar acesso na API externa';
                    $retorno->data = $response;
                    return $retorno;
                }

                $data = json_decode($response);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $retorno->message = 'Resposta inválida da API externa';
                    $retorno->data = $response;
                    return $retorno;
                }

                if ((isset($data->status) && $data->status === false) || isset($data->error)) {
                    $retorno->message = isset($data->message) ? $data->message : 'API externa recusou a liberação';
                    $retorno->data = $data;
                    return $retorno;
                }
            } catch (\Throwable $e) {
                $retorno->message = 'Erro ao comunicar com a API externa: '.$e->getMessage();
                return $retorno;
            }

            if ($colaboradorPk != "") {
                Util::execUpdate(
                    "ponto_solicitacao_liberacao_app",
                    $fields,
                    " colaborador_pk = ".$colaboradorPk." and dt_liberacao is null",
                    $this->pdo
                );
            }

            $retorno->status = true;
            $retorno->message = 'Acesso liberado com sucesso';
            $retorno->data = $apiPk;
            return $retorno;
        }

        Util::execUpdate("ponto_solicitacao_liberacao_app", $fields, " pk = ".$solicitacao_acesso_app['pk'],$this->pdo);
        $retorno->status = true;
        $retorno->message = 'Dados atualizado com sucesso';
        $retorno->data = $solicitacao_acesso_app['pk'];

        return $retorno;
    }

    
    public function listarGrid($colaborador_pk,$ds_pin,$ds_re,$ic_status){
        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio
            $return =[];

            //PAGINAÇÃO
            if(isset($_GET['start']) && $_GET['start']!=0){
                $displayStart = $_GET['start'];
            }
            else{
                $displayStart = 0;
            }

            if(isset($_GET['length'])){
                $displayRange = $_GET['length'];
                $lengthSql = " LIMIT ".intval($displayRange)." OFFSET ".intval($displayStart);
            }
            else{
                $lengthSql = " ";
            }
            
            $sql ="";
            $sql.="select psl.pk, psl.dt_cadastro, psl.usuario_cadastro_pk, psl.dt_ult_atualizacao, psl.usuario_ult_atualizacao_pk ";
            $sql.="       ,c.ds_colaborador";
            $sql.="       ,psl.colaborador_pk";
            $sql.="       ,psl.ds_pin";
            $sql.="       ,c.ds_re"; 
            $sql.="       ,psl.ds_link_imagem_cadastro ds_imagem";
            $sql.="       ,psl.img_colaborador_cadastro";
            $sql.="       ,date_format(psl.dt_solit_liberacao,'%d/%m/%Y %H:%m:%s')dt_solit_liberacao  ";
            $sql.="       ,date_format(psl.dt_liberacao,'%d/%m/%Y %H:%m:%s')dt_liberacao ";
            $sql.="       ,psl.usuario_aprovacao_pk ";
            $sql.="       ,u.ds_usuario ";
            $sql.="       ,psl.obs ";
            $sql.="       ,case when psl.ic_status = 1 then 'Liberado' when psl.ic_status = 2 then 'Pendente' else 'Pendente' end status  ";
            $sql.="  from ponto_solicitacao_liberacao_app psl ";
            $sql.="  inner join colaboradores c on psl.colaborador_pk = c.pk";
            $sql.="  left join usuarios u on psl.usuario_aprovacao_pk = u.pk";
            $sql.=" where 1=1 ";
            $sql.=" and c.ic_status = 1 ";

            if($colaborador_pk != ""){
                $sql.=" and psl.colaborador_pk =".$colaborador_pk;
            }        
            

            if($ic_status == 1){
                $sql.=" and psl.dt_liberacao is not null";
            }else if($ic_status == 2){
                $sql.=" and psl.dt_liberacao is null";
            }
            
            $sql.=" order by psl.dt_solit_liberacao desc ";
        
            
            $stmt = $this->pdo->prepare( $sql.$lengthSql );
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);



            $sqlCount ="";
            $sqlCount.="select psl.pk, psl.dt_cadastro, psl.usuario_cadastro_pk, psl.dt_ult_atualizacao, psl.usuario_ult_atualizacao_pk ";
            $sqlCount.="  from ponto_solicitacao_liberacao_app psl ";
            $sqlCount.="  inner join colaboradores c on psl.colaborador_pk = c.pk";
            $sqlCount.="  left join usuarios u on psl.usuario_aprovacao_pk = u.pk";
            $sqlCount.=" where 1=1 ";
            $sqlCount.=" and c.ic_status = 1 ";

            if($colaborador_pk != ""){
                $sqlCount.=" and psl.colaborador_pk =".$colaborador_pk;
            }        
            

            if($ic_status == 1){
                $sqlCount.=" and psl.dt_liberacao is not null";
            }else if($ic_status == 2){
                $sqlCount.=" and psl.dt_liberacao is null";
            }
            
            $stmtCount = $this->pdo->prepare( $sqlCount );
            $stmtCount->execute();
            $rowsCount = $stmtCount->fetchAll(\PDO::FETCH_ASSOC);

            if(count($query) > 0){
                for($i = 0; $i < count($query); $i++){
                    if($query[$i]['img_colaborador_cadastro']==NULL){
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $query[$i]['ds_imagem']);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $conteudo_imagem = curl_exec($ch);


                        curl_close($ch);
                        
                        $fields = array();
                        $fields['img_colaborador_cadastro'] = base64_encode($conteudo_imagem);
                        
                        Util::execUpdate("ponto_solicitacao_liberacao_app", $fields, " pk = ".$query[$i]['pk'],$this->pdo);


                        if (strpos(base64_encode($conteudo_imagem), 'data:image/png;base64') !== false) {
                            $arr = (explode("data:image/png;base64,",base64_encode($conteudo_imagem)));
                        
                            $img_ponto = $arr[1];
                        } else {
                            $img_ponto = base64_encode($conteudo_imagem);
                        }
                    }
                    else{
                        if (strpos($query[$i]['img_colaborador_cadastro'], 'data:image/png;base64') !== false) {
                            $arr = (explode("data:image/png;base64,",$query[$i]['img_colaborador_cadastro']));
                        
                            $img_ponto = $arr[1];
                        } else {
                            $img_ponto = $query[$i]['img_colaborador_cadastro'];
                        }
                    }


                    
                    
                    $img = '<img width=30 height=30 src="data:image/png;base64,'. ($img_ponto).'">';
                

                    $return[] = array(
                        "t_pk" => $query[$i]["pk"],
                        "t_colaborador_pk"=>$query[$i]['colaborador_pk'],
                        "t_ds_colaborador"=>$query[$i]['ds_colaborador'],
                        "t_ds_imagem"=>$img,
                        "t_ds_link_imagem"=>$query[$i]['ds_imagem'],
                        "t_dt_solit_liberacao"=>$query[$i]['dt_solit_liberacao'],
                        "t_dt_liberacao"=>$query[$i]['dt_liberacao'],
                        "t_usuario_aprovacao_pk"=>$query[$i]['usuario_aprovacao_pk'],
                        "t_ds_usuario"=>$query[$i]['ds_usuario'],
                        "t_obs"=>$query[$i]['obs'],
                        "t_status"=>$query[$i]['status'],

                        "t_functions" => ""
                    );
                }
            }


            



            /*$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            // Obter o nome do host (ex: www.example.com)
            $host = $_SERVER['HTTP_HOST'];
            // Montar a URL completa
            $currentUrl = $protocol . $host;

            $body = [
                'ds_link' =>$currentUrl,
                'colaborador_pk' =>$colaborador_pk,
                'ic_status' =>$ic_status
            ];

            $request = $this->client->request('POST','https://webservice.gepros6.com.br/work/action.php?action=consultaAcessoApp', [
                'json'=>$body
            ]);
        
            $code = $request->getStatusCode();
            $response = $request->getBody()->getContents();
            $data =  json_decode($response);

            if(isset($data->data->registros)){
                $query = $data->data->registros;
                for($i = 0; $i < count($query); $i++){
                    $img = '<img width=30 height=30 src="data:image/png;base64,'. ($query[$i]->img_colaborador).'">';
                

                    $return[] = array(
                        "t_pk" => $query[$i]->pk,
                        "t_colaborador_pk"=>$query[$i]->colaborador_pk,
                        "t_ds_lead"=>$query[$i]->ds_lead,
                        "t_ds_colaborador"=>$query[$i]->ds_colaborador,
                        "t_ds_imagem"=>$img,
                        "t_ds_link_imagem"=>"",
                        "t_dt_solit_liberacao"=>$query[$i]->dt_solicitacao,
                        "t_dt_liberacao"=>$query[$i]->dt_liberacao,
                        "t_usuario_aprovacao_pk"=>"",
                        "t_ds_usuario"=>"",
                        "t_status"=>$query[$i]->ic_status,

                        "t_functions" => ""
                    );
                }
            }*/



            $retorno->status = true;
            $retorno->message = 'Dados carregados com sucesso';
            $retorno->data = $return;
            $retorno->iTotalDisplayRecords = count($rowsCount);
            $retorno->iTotalRecords = count($rowsCount);

            echo json_encode($retorno);
            exit(0);
        }
        catch(Throwable $th){
            print_r($th->getMessage());
            die();
        }
        
    }

    public function novoCadSolicitacaoAcessoAppPonto($dados){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        try{

            //$arrImg = json_decode($dados['img_colaborador_cadastro'], true);

            $fields = array();
            $fields['ds_pin'] = $dados['ds_pin'];
            $fields['colaborador_pk'] = $dados['colaborador_pk'];
            $fields['id_cliente'] = $dados['id_cliente'];
            $fields['img_colaborador_cadastro'] = $dados['img_colaborador_cadastro'];
            $fields['ds_link_imagem_cadastro'] = $dados['ds_link_imagem_cadastro'];
            $fields['IdTermoAceite'] = $dados['IdTermoAceite'];
            $fields['ic_tipo_app'] = $dados['ic_tipo_app'];


            $fields["dt_ult_atualizacao"] = "sysdate()";
            if(!isset($_SESSION['session_user']['par1'])){
                $fields["usuario_ult_atualizacao_pk"] = 1;
            }else{
                $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
            }

            $fields['dt_solit_liberacao'] = "sysdate()";

            $fields["dt_cadastro"] = "sysdate()";
            if(!isset($_SESSION['session_user']['par1'])){
                $fields["usuario_cadastro_pk"]   = 1;
            }else{
                $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
            }

            //FORMATO PARA PEGAR A IMG ou com o base64_decode()
            /*echo '<img src="data:image/gif;base64,', ($imgData),'">';;
            exit();*/

            $pk = Util::execInsert("ponto_solicitacao_liberacao_app", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;

            return $retorno;
        }
        catch(\Throwable $e){
            $retorno->data = "";
            return $retorno;
        }


    }

    public function buscarTodosBase64(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $return =[];


        $sql ="";
        $sql.="select psl.pk, psl.dt_cadastro, psl.usuario_cadastro_pk, psl.dt_ult_atualizacao, psl.usuario_ult_atualizacao_pk ";
        $sql.="       ,c.ds_colaborador";
        $sql.="       ,c.pk colaborador_pk";
        $sql.="       ,c.empresas_pk";
        $sql.="       ,psl.ds_pin";
        $sql.="       ,psl.img_colaborador_cadastro";
        $sql.="  from ponto_solicitacao_liberacao_app psl ";
        $sql.="  inner join colaboradores c on psl.colaborador_pk = c.pk";
        $sql.="  left join usuarios u on psl.usuario_aprovacao_pk = u.pk";
        $sql.=" where 1=1 ";
        $sql.=" and c.ic_status = 1 ";
        $sql.=" and psl.ic_status = 1 ";



        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $query;

        return $retorno;
    }
}
