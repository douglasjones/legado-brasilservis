<?php

namespace App\Model;

use App\Utils\Util;
use DateTime;
use GuzzleHttp\Client;
use Throwable;

class PontoFolha {

    public $pdo;
    private $margemInicioTurnoNoturnoSegundos = 14400;
    private $margemFimTurnoNoturnoSegundos = 21600;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function excluir($pk){
        Util::execDelete('ponto_folha_registros', ' ponto_folha_pk='.$pk, $this->pdo);
        Util::execDelete('ponto_folha_colaborador', ' ponto_folha_pk='.$pk, $this->pdo);
        Util::execDelete('ponto_folha', ' pk='.$pk, $this->pdo);
    }
    public function excluirFolhaColaborador($pk,$colaborador_pk,$dt_periodo_ini,$dt_periodo_fim){

        $whereRegistros = " colaborador_pk =".$colaborador_pk;
        $whereRegistros.= " and dt_hora_ponto BETWEEN '".$dt_periodo_ini." 00:00:00' and '".$dt_periodo_fim." 23:59:59'";

        
        Util::execDelete('ponto_folha_colaborador', ' pk='.$pk, $this->pdo);
        Util::execDelete('ponto_folha_registros', $whereRegistros, $this->pdo);
     
    
    }


    public function listarGrid($empresas_pk, $leads_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $result = []; //Retorno data setado como vazio

        $sql ="";
        $sql.=" SELECT";
        $sql.="    c.ds_conta,";
        $sql.="    pf.pk,";
        $sql.="    l.ds_lead,";
        $sql.="    l.pk lead_pk";
        $sql.=" FROM ponto_folha pf";
        $sql.="  LEFT JOIN contas c ON pf.empresas_pk = c.pk";
        $sql.="  INNER JOIN leads l ON pf.leads_pk = l.pk";
        $sql.=" AND pf.empresas_pk is not null";
        if(!empty($empresas_pk)){
            $sql.=" AND pf.empresas_pk=".$empresas_pk;
        }
        if(!empty($leads_pk)){
            $sql.=" AND pf.leads_pk=".$leads_pk;
        }
        $sql.=" GROUP BY l.ds_lead";
        $sql.=" ORDER BY l.ds_lead";
     
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if(count($query) > 0){
            for($i=0; $i<count($query);$i++){
                $sql ="";
                $sql.=" SELECT YEAR(dt_periodo_ini) ano_folha";
                $sql.="   FROM ponto_folha pf";
                $sql.="  WHERE pf.leads_pk =". $query[$i]['lead_pk'];
                $sql.="  GROUP BY YEAR(dt_periodo_ini)";


                $stmt = $this->pdo->prepare( $sql );
                $stmt->execute();
                $ano = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $folhas = array();
                for($l=0; $l<count($ano);$l++){
                    $sql ="";
                    $sql.=" SELECT MONTH(dt_periodo_ini) mes_folha";
                    $sql.="  ,CASE MONTHNAME(pf.dt_periodo_ini)";
                    $sql.="   WHEN 'January' THEN 'Janeiro'";
                    $sql.="   WHEN 'February' THEN 'Fevereiro'";
                    $sql.="   WHEN 'March' THEN 'Março'";
                    $sql.="   WHEN 'April' THEN 'Abril'";
                    $sql.="   WHEN 'May' THEN 'Maio'";
                    $sql.="   WHEN 'June' THEN 'Junho'";
                    $sql.="   WHEN 'July' THEN 'Julho'";
                    $sql.="   WHEN 'August' THEN 'Agosto'";
                    $sql.="   WHEN 'September' THEN 'Setembro'";
                    $sql.="   WHEN 'October' THEN 'Outubro'";
                    $sql.="   WHEN 'November' THEN 'Novembro'";
                    $sql.="   WHEN 'December' THEN 'Dezembro'";
                    $sql.="    END as ds_mes";
                    $sql.="   FROM ponto_folha pf";
                    $sql.="  WHERE pf.leads_pk =". $query[$i]['lead_pk'];
                    $sql.="    AND YEAR(pf.dt_periodo_ini) = ".$ano[$l]['ano_folha'];
                    $sql.="  GROUP BY MONTH(pf.dt_periodo_ini)";
                    $sql.="  ORDER BY MONTH(pf.dt_periodo_ini)";
                    $stmt = $this->pdo->prepare( $sql );
                    $stmt->execute();
                    $mes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    $folhaPorMes = array();
                    for($a=0;$a<count($mes);$a++){
                        $sql ="";
                        $sql.=" SELECT DATE_FORMAT(pf.dt_cadastro, '%d/%m/%Y') dt_cadastro, pf.pk ponto_folha_pk";
                        $sql.="   FROM ponto_folha pf";
                        $sql.="  WHERE pf.leads_pk =". $query[$i]['lead_pk'];
                        $sql.="    AND YEAR(pf.dt_periodo_ini) = ".$ano[$l]['ano_folha'];
                        $sql.="    AND MONTH(pf.dt_periodo_ini) = ".$mes[$a]['mes_folha'];
                        $stmt = $this->pdo->prepare( $sql );
                        $stmt->execute();
                        $folhasMes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        $folhaPorMes[] = array(
                            "ds_mes" => $mes[$a]["ds_mes"],
                            "folhas_mes"=> $folhasMes
                        );

                    }

                    $folhas[] = array(
                        "ds_ano" => $ano[$l]["ano_folha"],
                        "folhaPorMes"=> $folhaPorMes
                    );

                }

                $result[] = array(
                    "pk" => $query[$i]["pk"],
                    "ds_conta"=>$query[$i]['ds_conta'],
                    "ds_lead"=>$query[$i]['ds_lead'],
                    "lead_pk"=>$query[$i]['lead_pk'],
                    "mesesNoAno"=>$folhas
                );
            }
        }


        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result;
        $retorno->iTotalDisplayRecords = count($result);
        $retorno->iTotalRecords = count($result);

        return $retorno;
    }
    public function salvarRegistros($jsonDadosRegistros){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        try{
            $arrDadosRegistros = json_decode($jsonDadosRegistros, true);
        
            for($i=0;$i<count($arrDadosRegistros);$i++){

                $fields = array();
                $fields['ponto_pk'] = $arrDadosRegistros[$i]['ponto_folha_pk'];
                $fields['tipo_ponto_pk'] = $arrDadosRegistros[$i]['tipo_ponto_pk'];
                if($arrDadosRegistros[$i]['dt_hora_ponto']!=""){
                    $fields['dt_hora_ponto'] = Util::DataYMD($arrDadosRegistros[$i]['dt_hora_ponto']);
                }
                
                $fields['colaborador_pk'] = $arrDadosRegistros[$i]['colaborador_pk'];
                $fields['ponto_folha_pk'] = $arrDadosRegistros[$i]['ponto_folha_pk'];
                if($arrDadosRegistros[$i]['hr_ini_expediente']!=""){
                    $fields['hr_ini_expediente'] = $arrDadosRegistros[$i]['hr_ini_expediente'];
                }
                else{
                    $fields['hr_ini_expediente'] = "";
                }
                if($arrDadosRegistros[$i]['hr_ini_intervalo']!=""){
                    $fields['hr_ini_intervalo'] = $arrDadosRegistros[$i]['hr_ini_intervalo'];
                }
                else{
                    $fields['hr_ini_intervalo'] = "";
                }
                
                if($arrDadosRegistros[$i]['hr_fim_intervalo']!=""){
                    $fields['hr_fim_intervalo'] = $arrDadosRegistros[$i]['hr_fim_intervalo'];
                }
                else{
                    $fields['hr_fim_intervalo'] = "";
                }
                
                if($arrDadosRegistros[$i]['hr_fim_expediente']!=""){
                    $fields['hr_fim_expediente'] = $arrDadosRegistros[$i]['hr_fim_expediente'];
                }
                else{
                    $fields['hr_fim_expediente'] = "";
                }
                
                $fields['hr_trabalhadas'] = $arrDadosRegistros[$i]['hr_trabalhadas'];
                $fields['hr_excedente'] = $arrDadosRegistros[$i]['hr_excedente'];
                $fields['hr_faltantes'] = $arrDadosRegistros[$i]['hr_faltantes'];
                $fields['hr_extra50'] = $arrDadosRegistros[$i]['hr_extra50'];           
                $fields['hr_extra100'] = $arrDadosRegistros[$i]['hr_extra100'];                 
                $fields['hr_adicional_noturno'] = $arrDadosRegistros[$i]['hr_adicional_noturno'];
                $fields['ic_status'] = $arrDadosRegistros[$i]['ic_status'];

                $fields['obs'] = $arrDadosRegistros[$i]['obs'];        
        
                $fields["dt_ult_atualizacao"] = "sysdate()";
                $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

                
                if($arrDadosRegistros[$i]['pk']  == ""){

                    $fields["dt_cadastro"] = "sysdate()";
                    $fields["usuario_cadastro_pk"]   =  $_SESSION['session_user']['par1'];

                    $pk = Util::execInsert("ponto_folha_registros", $fields,$this->pdo);
                    $retorno->status = true;
                    $retorno->message = 'Dados cadastrados com sucesso';
                    $retorno->data = $pk;
                }
                else{
                    
                        Util::execDelete('ponto_folha_registros'," pk = ".$arrDadosRegistros[$i]['pk'],$this->pdo);

                        $fieldsUpdate = array();
                        $fieldsUpdate['ponto_pk'] = $arrDadosRegistros[$i]['ponto_folha_pk'];
                        $fieldsUpdate['tipo_ponto_pk'] = $arrDadosRegistros[$i]['tipo_ponto_pk'];
                        if($arrDadosRegistros[$i]['dt_hora_ponto']!=""){
                            $fieldsUpdate['dt_hora_ponto'] = Util::DataYMD($arrDadosRegistros[$i]['dt_hora_ponto']);
                        } 
                        
                        $fieldsUpdate['colaborador_pk'] = $arrDadosRegistros[$i]['colaborador_pk'];
                        $fieldsUpdate['ponto_folha_pk'] = $arrDadosRegistros[$i]['ponto_folha_pk'];
                        $fieldsUpdate['colaborador_pk'] = $arrDadosRegistros[$i]['colaborador_pk'];
                        $fieldsUpdate['ponto_folha_pk'] = $arrDadosRegistros[$i]['ponto_folha_pk'];
                        $fieldsUpdate['hr_ini_expediente'] = $arrDadosRegistros[$i]['hr_ini_expediente'];
                        $fieldsUpdate['hr_ini_intervalo'] = $arrDadosRegistros[$i]['hr_ini_intervalo'];
                        $fieldsUpdate['hr_fim_intervalo'] = $arrDadosRegistros[$i]['hr_fim_intervalo'];
                        $fieldsUpdate['hr_fim_expediente'] = $arrDadosRegistros[$i]['hr_fim_expediente'];
                        $fieldsUpdate['hr_trabalhadas'] = $arrDadosRegistros[$i]['hr_trabalhadas'];
                        $fieldsUpdate['hr_excedente'] = $arrDadosRegistros[$i]['hr_excedente'];
                        $fieldsUpdate['hr_faltantes'] = $arrDadosRegistros[$i]['hr_faltantes'];
                        $fieldsUpdate['hr_extra50'] = $arrDadosRegistros[$i]['hr_extra50'];           
                        $fieldsUpdate['hr_extra100'] = $arrDadosRegistros[$i]['hr_extra100'];                 
                        $fieldsUpdate['hr_adicional_noturno'] = $arrDadosRegistros[$i]['hr_adicional_noturno'];
                        $fieldsUpdate['ic_status'] = $arrDadosRegistros[$i]['ic_status'];

                        $fieldsUpdate['obs'] = $arrDadosRegistros[$i]['obs'];       
                
                        $fieldsUpdate["dt_ult_atualizacao"] = "sysdate()";
                        $fieldsUpdate["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                        $fieldsUpdate["dt_cadastro"] = "sysdate()";
                        $fieldsUpdate["usuario_cadastro_pk"]   =  $_SESSION['session_user']['par1'];
                        
                        $pk = Util::execInsert("ponto_folha_registros", $fieldsUpdate, $this->pdo);
                        $retorno->status = true;
                        $retorno->message = 'Dados atualizado com sucesso';
                        $retorno->data = $pk;

                
                }
            }
        }
        catch(Throwable $e){
            print_r($e->getMessage());
            die();
        }
        

        return $retorno;

    }
    public function alterarRegistrosFolhaPonto($jsonDadosRegistros){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        try{
            $arrDadosRegistros = json_decode($jsonDadosRegistros, true);
            Util::execDelete('ponto_folha_colaborador', ' ponto_folha_pk='.$arrDadosRegistros[0]['ponto_folha_pk'].' and colaborador_pk = '.$arrDadosRegistros[0]['colaborador_pk'], $this->pdo);
            $fields = array();
            $fields['ponto_folha_pk'] = $arrDadosRegistros[0]['ponto_folha_pk'];
            $fields['colaborador_pk'] = $arrDadosRegistros[0]['colaborador_pk'];
            $fields["dt_ult_atualizacao"] = "sysdate()";
            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"] = $_SESSION['session_user']['par1'];
            $fields["ic_status"] =$arrDadosRegistros[0]['ic_folha_finalizada'];
            $ponto_folha_colaborador_pk = Util::execInsert("ponto_folha_colaborador", $fields, $this->pdo);

            $whereRegistros = " colaborador_pk =".$arrDadosRegistros[0]['colaborador_pk'];
            $whereRegistros.= " and ponto_folha_pk =".$arrDadosRegistros[0]['ponto_folha_pk'];

            Util::execDelete('ponto_folha_registros', $whereRegistros, $this->pdo);
            for($i=0;$i<count($arrDadosRegistros);$i++){
                $fieldsUpdate = array();
                $fieldsUpdate['tipo_ponto_pk'] = $arrDadosRegistros[$i]['tipo_ponto_pk'];
                if($arrDadosRegistros[$i]['dt_hora_ponto']!=""){
                    $fieldsUpdate['dt_hora_ponto'] = Util::DataYMD($arrDadosRegistros[$i]['dt_hora_ponto']);
                } 
                
                $fieldsUpdate['ponto_folha_pk'] = $arrDadosRegistros[0]['ponto_folha_pk'];
                $fieldsUpdate['colaborador_pk'] = $arrDadosRegistros[0]['colaborador_pk'];
                $fieldsUpdate['hr_ini_expediente'] = $arrDadosRegistros[$i]['hr_ini_expediente'];
                $fieldsUpdate['hr_ini_intervalo'] = $arrDadosRegistros[$i]['hr_ini_intervalo'];
                $fieldsUpdate['hr_fim_intervalo'] = $arrDadosRegistros[$i]['hr_fim_intervalo'];
                $fieldsUpdate['hr_fim_expediente'] = $arrDadosRegistros[$i]['hr_fim_expediente'];
                $fieldsUpdate['hr_trabalhadas'] = $arrDadosRegistros[$i]['hr_trabalhadas'];
                $fieldsUpdate['hr_excedente'] = $arrDadosRegistros[$i]['hr_excedentes'];
                $fieldsUpdate['hr_faltantes'] = $arrDadosRegistros[$i]['hr_faltantes'];
                $fieldsUpdate['hr_extra50'] = $arrDadosRegistros[$i]['hr_extra50'];           
                $fieldsUpdate['hr_extra100'] = $arrDadosRegistros[$i]['hr_extra100'];                 
                $fieldsUpdate['hr_adicional_noturno'] = $arrDadosRegistros[$i]['hr_adicional_noturno'];
                $fieldsUpdate['ic_status'] = $arrDadosRegistros[$i]['ic_status'];

                $fieldsUpdate['obs'] = $arrDadosRegistros[$i]['obs'];       
        
                $fieldsUpdate["dt_ult_atualizacao"] = "sysdate()";
                $fieldsUpdate["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                $fieldsUpdate["dt_cadastro"] = "sysdate()";
                $fieldsUpdate["usuario_cadastro_pk"]   =  $_SESSION['session_user']['par1'];
                
                $pk = Util::execInsert("ponto_folha_registros", $fieldsUpdate, $this->pdo);
            }

            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        catch(Throwable $e){
            print_r($e->getMessage());
            die();
        }
        

        return $retorno;

    }
    

    public function salvarFolhaFinalizada($pontoFolhaFinalizada){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        
        $fields['ic_status'] = $pontoFolhaFinalizada['ic_status'];       
        $fields['dt_validacao'] = "sysdate()";
        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
        
        $pk = Util::execUpdate("ponto_folha_colaborador", $fields, "ponto_folha_pk = ".$pontoFolhaFinalizada['pk']." and colaborador_pk = ".$pontoFolhaFinalizada['colaborador_pk'],$this->pdo);
        
        $retorno->status = true;
        $retorno->message = 'Dados cadastrados com sucesso';
        $retorno->data = $pk;
        return $retorno;
    }

    public function salvar($pontoFolha){
        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio

            $leads_colaboradores = json_decode($pontoFolha['leads_colaboradores'], true);
            $folha_ponto = json_decode($pontoFolha['folha_ponto'], true);

             // Converte as datas para o formato YMD
             $dt_periodo_ini = Util::DataYMD($pontoFolha['dt_periodo_ini']);
             $dt_periodo_fim = Util::DataYMD($pontoFolha['dt_periodo_fim']);
            foreach ($leads_colaboradores as $leads_pk => $colaboradores) {
                if (!isset($folha_ponto[$leads_pk])) {
                    // Define os campos para a inserção do lead na tabela `ponto_folha`
                    $fields = array();
                    $fields['empresas_pk'] = $pontoFolha['empresas_pk'];
                    $fields['dt_periodo_ini'] = $dt_periodo_ini;
                    $fields['dt_periodo_fim'] = $dt_periodo_fim;
                    $fields['obs'] = $pontoFolha['obs'];
                    $fields['leads_pk'] = $leads_pk; // Associa o leads_pk ao registro principal
                    $fields["dt_ult_atualizacao"] = "sysdate()";
                    $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
            
                    if (empty($pontoFolha['pk'])) {
                        $fields["dt_cadastro"] = "sysdate()";
                        $fields["usuario_cadastro_pk"] = $_SESSION['session_user']['par1'];
                        
                        // Insere o lead na tabela `ponto_folha`
                        $pk = Util::execInsert("ponto_folha", $fields, $this->pdo);
                
                        // Salva os colaboradores associados a este lead
                        $this->salvarColaborador($pk, $colaboradores, $dt_periodo_ini, $dt_periodo_fim);
                        
                        $retorno->status = true;
                        $retorno->message = 'Dados cadastrados com sucesso';
                        $retorno->data[] = $pk; // Armazena cada `pk` de `ponto_folha` para cada lead
                    }
                }
                else{
                   
                    // Salva os colaboradores associados a este lead
                    $this->salvarColaborador($folha_ponto[$leads_pk][0], $colaboradores, $dt_periodo_ini, $dt_periodo_fim);
                        
                    $retorno->status = true;
                    $retorno->message = 'Dados cadastrados com sucesso';
                    $retorno->data[] = $folha_ponto[$leads_pk]; 
                }
                
            }
            
            return $retorno;
        }
        catch(Throwable $th){
            print_r("Salvar - ".$th->getMessage());
            die();
        }
        

    }

    public function verificarFolhaColaborador($dt_periodo_ini,$dt_periodo_fim,$colaborador_pk,$ponto_folha_pk){


        $sql="";
        $sql.=" select pfc.pk from ponto_folha_colaborador pfc
                inner join ponto_folha p on pfc.ponto_folha_pk = p.pk 
        where 1=1
        and pfc.colaborador_pk = ".$colaborador_pk;
        if($ponto_folha_pk!=""){
            $sql.=" and p.pk = ".$ponto_folha_pk;
        }
       
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return ($rows);
        
        
    }

    public function salvarColaborador($ponto_folha_pk, $colaboradores, $dt_periodo_ini, $dt_periodo_fim){
        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio

            // Percorre a lista de colaboradores vinculados ao lead específico
                
                foreach ($colaboradores as $colaborador) {
                    $countColaborador = 0;
                    list($colaborador_pk, $agenda_colaborador_padrao_pk) = explode("|", $colaborador);
                    
                    //SÓ FAZ O CADASTRO PARA OS COLABORADORES QUE NÃO TEM FOLHA CADASTRADA PARA O PERIODO 
                    $countColaborador = $this->verificarFolhaColaborador($dt_periodo_ini,$dt_periodo_fim,$colaborador_pk,$ponto_folha_pk);
                    if(count($countColaborador)==0){
                        $fields = array();
                        $fields['ponto_folha_pk'] = $ponto_folha_pk;
                        $fields['colaborador_pk'] = $colaborador_pk;
                        //$fields['agenda_colaborador_padrao_pk'] = $agenda_colaborador_padrao_pk;

                        $fields["dt_ult_atualizacao"] = "sysdate()";
                        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                        $fields["dt_cadastro"] = "sysdate()";
                        $fields["usuario_cadastro_pk"] = $_SESSION['session_user']['par1'];
                        
                        // Insere o colaborador na tabela `ponto_folha_colaborador`
                        $pk = Util::execInsert("ponto_folha_colaborador", $fields, $this->pdo);
                        
                        // Salva os registros de ponto
                        $this->salvarItens($dt_periodo_ini, $dt_periodo_fim, $colaborador_pk, $agenda_colaborador_padrao_pk, $ponto_folha_pk);
                    }
                    
                }
            
            return $pk;
        }
        catch(Throwable $th){
            print_r("Salvar Colaborador - ".$th->getMessage());
            die();
        }
        

    }

    public function salvarItens($dt_periodo_ini, $dt_periodo_fim, $colaboradores_pk, $agenda_colaborador_padrao_pk, $ponto_folha_pk){
        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio

            // DADOS DE PARAMETRIZACAO PARA DADOS DE REGISTRO FOLHA
                //Retorna dados escala
                    $dadosEscala = $this->listarDadosEscala($dt_periodo_ini, $dt_periodo_fim, $colaboradores_pk, $agenda_colaborador_padrao_pk);
                    $hr_saida_intervalo = $dadosEscala[0]["hr_saida_intervalo"];
                    $hr_inicio_expediente = $dadosEscala[0]["hr_inicio_expediente"];
                    $hr_termino_expediente = $dadosEscala[0]["hr_termino_expediente"];
                    $hr_retorno_intervalo = $dadosEscala[0]["hr_retorno_intervalo"];
                    $ic_intrajornada = $dadosEscala[0]["ic_intrajornada"];
                    $hr_expediente = $dadosEscala[0]["hr_expediente"];
                    $turnos_pk = $dadosEscala[0]["turnos_pk"];

                //Retorna preenchimento automatico
                    $ic_preencher_folha = $this->listarPreenchimentoAutomatico();

                //Retorna dados Ponto 
                    //Dados 5x2
                        $dadosPonto = $this->listarDadosPonto5x2($dt_periodo_ini, 
                                                                $dt_periodo_fim, 
                                                                $colaboradores_pk, 
                                                                $hr_inicio_expediente, 
                                                                $hr_termino_expediente, 
                                                                $hr_saida_intervalo, 
                                                                $hr_retorno_intervalo, 
                                                                $hr_expediente, 
                                                                $ic_intrajornada, 
                                                                $agenda_colaborador_padrao_pk, 
                                                                $ic_preencher_folha
                                                            );


            //Salvar registros com base nos pontos/apontamentos informados. 
            for($i=0;$i<count($dadosPonto);$i++){
                $fields = array();
                $fields['dt_hora_ponto'] = $dadosPonto[$i]['dt_hora_ponto'];
                $fields['colaborador_pk'] = $colaboradores_pk;
                $fields['ponto_folha_pk'] = $ponto_folha_pk;
                $fields['hr_ini_expediente'] = $dadosPonto[$i]['pontos_dia'][0]['ponto_ini_expediente'];
                $fields['hr_ini_intervalo'] = $dadosPonto[$i]['pontos_dia'][0]['ponto_ini_intervalo'];
                $fields['hr_fim_intervalo'] = $dadosPonto[$i]['pontos_dia'][0]['ponto_term_intervalo'];
                $fields['hr_fim_expediente'] = $dadosPonto[$i]['pontos_dia'][0]['ponto_term_expediente'];
                $fields['hr_trabalhadas'] = $dadosPonto[$i]['pontos_dia'][0]['horas_trabalhadas'];
                $fields['hr_excedente'] = $dadosPonto[$i]['pontos_dia'][0]['hr_excedentes'];
                $fields['hr_faltantes'] = $dadosPonto[$i]['pontos_dia'][0]['hr_faltante'];
                $fields['tipo_ponto_pk'] = $dadosPonto[$i]['pontos_dia'][0]['tipo_ponto_pk'];
                $fields['obs'] = $dadosPonto[$i]['pontos_dia'][0]['obs'];
        
                $fields["dt_ult_atualizacao"] = "sysdate()";
                $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
        
                $fields["dt_cadastro"] = "sysdate()";
                $fields["usuario_cadastro_pk"]  = $_SESSION['session_user']['par1'];
        
                $pk = Util::execInsert("ponto_folha_registros", $fields,$this->pdo);
            }
           
         }catch(Throwable $th){
                print_r("Salvar Itens - ".$th->getMessage());
                die();
            }
        

    }
    public function regerar($ponto_folha_pk, $dt_periodo_ini, $dt_periodo_fim,$arrColaborador){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $arrColaborador = json_decode($arrColaborador,true);

        $dt_periodo_ini = Util::DataYMD($dt_periodo_ini);
        $dt_periodo_fim = Util::DataYMD($dt_periodo_fim);
        for($l=0;$l<count($arrColaborador);$l++){
            $colaborador_pk = $arrColaborador[$l];
            
            // DADOS DE PARAMETRIZACAO PARA DADOS DE REGISTRO FOLHA
                //Retorna dados escala
                $dadosEscala = $this->listarDadosEscala($dt_periodo_ini, $dt_periodo_fim, $colaborador_pk, '');
                
                if(count($dadosEscala)>0){
                    $hr_saida_intervalo = $dadosEscala[0]["hr_saida_intervalo"];
                    $hr_inicio_expediente = $dadosEscala[0]["hr_inicio_expediente"];
                    $hr_termino_expediente = $dadosEscala[0]["hr_termino_expediente"];
                    $hr_retorno_intervalo = $dadosEscala[0]["hr_retorno_intervalo"];
                    $ic_intrajornada = $dadosEscala[0]["ic_intrajornada"];
                    $hr_expediente = $dadosEscala[0]["hr_expediente"];
                    $agenda_colaborador_padrao_pk = $dadosEscala[0]["pk"];

                        
                //Retorna preenchimento automatico
                    $ic_preencher_folha = $this->listarPreenchimentoAutomatico();

                //Retorna dados Ponto /Apontamento
                    $dadosPonto = $this->listarDadosRegerar($ponto_folha_pk,
                                                            $dt_periodo_ini, 
                                                            $dt_periodo_fim, 
                                                            $colaborador_pk, 
                                                            $hr_inicio_expediente, 
                                                            $hr_termino_expediente, 
                                                            $hr_saida_intervalo, 
                                                            $hr_retorno_intervalo, 
                                                            $hr_expediente, 
                                                            $ic_intrajornada, 
                                                            $agenda_colaborador_padrao_pk,
                                                            $ic_preencher_folha);

             

                //Salvar registros com base nos pontos/apontamentos informados. 
                for($i=0;$i<count($dadosPonto);$i++){

                    
                    $fields = array();
                    $fields['dt_hora_ponto'] = $dadosPonto[$i]['dt_hora_ponto'];
                    $fields['colaborador_pk'] = $colaborador_pk;
                    $fields['ponto_folha_pk'] = $ponto_folha_pk;
                    $fields['hr_ini_expediente'] = $dadosPonto[$i]['pontos_dia'][0]['ponto_ini_expediente'];
                    $fields['hr_ini_intervalo'] = $dadosPonto[$i]['pontos_dia'][0]['ponto_ini_intervalo'];
                    $fields['hr_fim_intervalo'] = $dadosPonto[$i]['pontos_dia'][0]['ponto_term_intervalo'];
                    $fields['hr_fim_expediente'] = $dadosPonto[$i]['pontos_dia'][0]['ponto_term_expediente'];
                    $fields['hr_trabalhadas'] = $dadosPonto[$i]['pontos_dia'][0]['horas_trabalhadas'];
                    $fields['hr_excedente'] = $dadosPonto[$i]['pontos_dia'][0]['hr_excedentes'];
                    $fields['hr_faltantes'] = $dadosPonto[$i]['pontos_dia'][0]['hr_faltante'];
                    $fields['tipo_ponto_pk'] = $dadosPonto[$i]['pontos_dia'][0]['tipo_ponto_pk'];
                    $fields['obs'] = $dadosPonto[$i]['pontos_dia'][0]['obs'];

                    $fields["dt_ult_atualizacao"] = "sysdate()";
                    $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

                    
                    if($dadosPonto[$i]['pontos_dia'][0]['registrosFolhaPk']!=""){
                        Util::execUpdate('ponto_folha_registros',$fields," pk = ".$dadosPonto[$i]['pk'],$this->pdo);
                    }
                    else{
                        $fields["dt_cadastro"] = "sysdate()";
                        $fields["usuario_cadastro_pk"] = $_SESSION['session_user']['par1'];
                        Util::execInsert("ponto_folha_registros", $fields,$this->pdo);
                    }

                    
                }
            }   
                
        }
        $retorno->status = true;
        $retorno->message = 'Dados cadastrados com sucesso';
        $retorno->data = '';

        return $retorno;

    }

    public function listarDadosEscala($dt_periodo_ini, $dt_periodo_fim, $colaboradores_pk, $agenda_colaborador_padrao_pk){
        $result = [];
        
        $sql="";
        $sql.=" SELECT tipo_escala,";
        $sql.="   CASE tipo_escala";
        $sql.="        WHEN 1 THEN 'ímpar'";
        $sql.="        WHEN 2 THEN 'par'";
        $sql.="   END ds_tipo_escala,";
        $sql.="   turnos_pk,";
        $sql.="   pk,";
        $sql.="   CASE turnos_pk";
        $sql.="        WHEN 1 THEN 'Manhã'";
        $sql.="        WHEN 2 THEN 'Tarde'";
        $sql.="        WHEN 3 THEN 'Noite'";
        $sql.="        WHEN 4 THEN 'Dia Todo'";
        $sql.="   END ds_turno,";
        $sql.="   hr_inicio_expediente,";
        $sql.="   hr_termino_expediente,";
        $sql.="   hr_saida_intervalo,";
        $sql.="   hr_retorno_intervalo,";
        $sql.="   hr_total_expediente,";
        $sql.="   hr_jornada_trabalho_intervalo,";
        $sql.="   ic_intrajornada";
        $sql.="  FROM agenda_colaborador_padrao";
        $sql.="  WHERE dt_inicio_agenda <= '".$dt_periodo_fim."'";
        $sql.="    AND (dt_fim_agenda IS NULL OR dt_fim_agenda >= '".$dt_periodo_ini."')";
        $sql.="   AND colaboradores_pk =".$colaboradores_pk;
        $sql.="   AND dt_cancelamento IS NULL";
    
        if(!empty($agenda_colaborador_padrao_pk)){
            $sql.="   AND pk =".$agenda_colaborador_padrao_pk;
        }   
        $sql.=" ORDER BY dt_inicio_agenda DESC, pk DESC";
        
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);  
        if(count($query)>0){
            if($query[0]['ic_intrajornada'] == "2" || $query[0]['ic_intrajornada'] == ''){
                $hr_intervalo_expediente = $this->retornarDifHora($query[0]['hr_saida_intervalo'], $query[0]['hr_retorno_intervalo']);
                $hr_intervalo_expediente = $hr_intervalo_expediente[0]['dif'];
                
            }else{
                $hr_intervalo_expediente = '01:00';
            }
    
            /* if($query[0]['turnos_pk'] == 3 && $query[0]['hr_termino_expediente'] != ''){
                // Adicionar 24 horas ao fim do expediente
                $hr_inicio_expediente = date('H:i', strtotime($query[0]['hr_inicio_expediente'] . ' + 12 hours'));
            }else{
                $hr_inicio_expediente = $query[0]['hr_inicio_expediente'];
            }
            */
            $hr_expediente = $this->retornarDifHora($query[0]['hr_inicio_expediente'], $query[0]['hr_termino_expediente']);
            $hr_expediente = $hr_expediente[0]['dif'];
    
            $hr_trabalhadas_expediente = $this->retornarDifHora($hr_intervalo_expediente, $hr_expediente);
            $hr_trabalhadas_expediente = $hr_trabalhadas_expediente[0]['dif'];
            
    
            $result[] = array(
                "tipo_escala" => $query[0]["tipo_escala"],
                "ds_tipo_escala"=>$query[0]['ds_tipo_escala'],
                "turnos_pk"=>$query[0]['turnos_pk'],
                "ds_turno"=>$query[0]['ds_turno'],
                "pk"=>$query[0]['pk'],
                "hr_inicio_expediente"=>$query[0]['hr_inicio_expediente'],
                "hr_termino_expediente"=>$query[0]['hr_termino_expediente'],
                "hr_saida_intervalo"=>$query[0]['hr_saida_intervalo'],
                "hr_retorno_intervalo"=>$query[0]['hr_retorno_intervalo'],
                "ic_intrajornada"=>$query[0]['ic_intrajornada'],
                "hr_expediente"=>$hr_trabalhadas_expediente
            );
        }
        
        
        return $result;

    }
    

    public function listarDiasEscala($agenda_colaborador_padrao_pk,$dt_periodo_ini,$dt_periodo_fim){

        $sql="";
        $sql.="SELECT ic_escala,";
        $sql.="       dt_escala,";
        $sql.="       tipo_escala_pk,";
        $sql.="      CASE 
                        WHEN DAYOFWEEK(dt_escala) = 1 THEN 'Dom'
                        WHEN DAYOFWEEK(dt_escala) = 2 THEN 'Seg'
                        WHEN DAYOFWEEK(dt_escala) = 3 THEN 'Ter'
                        WHEN DAYOFWEEK(dt_escala) = 4 THEN 'Qua'
                        WHEN DAYOFWEEK(dt_escala) = 5 THEN 'Qui'
                        WHEN DAYOFWEEK(dt_escala) = 6 THEN 'Sex'
                        WHEN DAYOFWEEK(dt_escala) = 7 THEN 'Sáb'
                    END AS dia_da_semana,";
        $sql.="       date_format(dt_escala,'%d/%m/%Y')dt_format";
        $sql.="  FROM escala_dados_colaborador";
        $sql.=" WHERE agenda_colaborador_padrao =".$agenda_colaborador_padrao_pk;
        $sql.="   AND dt_escala >='".$dt_periodo_ini."'";
        $sql.="   AND dt_escala <='".$dt_periodo_fim."'";
        $sql.="   group by dt_escala";
       
       
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);   
        return $query;
    }
    public function listarDiasEscalaPorColaborador($colaborador_pk,$dt_periodo_ini,$dt_periodo_fim,$agenda_colaborador_padrao_pk = ""){

        $sql="";
        $sql.="SELECT e.ic_escala,";
        $sql.="       e.dt_escala,";
        $sql.="       e.tipo_escala_pk,";
        $sql.="      CASE 
                        WHEN DAYOFWEEK(e.dt_escala) = 1 THEN 'Dom'
                        WHEN DAYOFWEEK(e.dt_escala) = 2 THEN 'Seg'
                        WHEN DAYOFWEEK(e.dt_escala) = 3 THEN 'Ter'
                        WHEN DAYOFWEEK(e.dt_escala) = 4 THEN 'Qua'
                        WHEN DAYOFWEEK(e.dt_escala) = 5 THEN 'Qui'
                        WHEN DAYOFWEEK(e.dt_escala) = 6 THEN 'Sex'
                        WHEN DAYOFWEEK(e.dt_escala) = 7 THEN 'Sáb'
                    END AS dia_da_semana,";
        $sql.="       date_format(e.dt_escala,'%d/%m/%Y')dt_format";
        $sql.="  FROM escala_dados_colaborador e";
        $sql.="  INNER JOIN agenda_colaborador_padrao a on a.pk = e.agenda_colaborador_padrao";
        $sql.=" WHERE a.colaboradores_pk =".$colaborador_pk;
        $sql.="   AND a.dt_cancelamento IS NULL";
        $sql.="   AND e.dt_escala >='".$dt_periodo_ini."'";
        $sql.="   AND e.dt_escala <='".$dt_periodo_fim."'";
        if(!empty($agenda_colaborador_padrao_pk)){
            $sql.="   AND e.agenda_colaborador_padrao =".$agenda_colaborador_padrao_pk;
        }
        $sql.="   group by e.dt_escala";
        $sql.="   order by e.dt_escala";
       
       
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);   
        return $query;
    }
    public function listarTurnosPk($agenda_colaborador_padrao_pk){

        $sql="";
        $sql.="SELECT turnos_pk";
        $sql.="  FROM agenda_colaborador_padrao";
        $sql.=" WHERE pk =".$agenda_colaborador_padrao_pk;
       
       
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);   
        return $query[0]['turnos_pk'];
    }

    public function listarDadosRegistrosFolha($ponto_folha_pk, $colaborador_pk ,$dt_periodo_ini,$dt_periodo_fim){

        $sql="";
        $sql.="SELECT pk, DATE(dt_hora_ponto)";
        $sql.="       ,tipo_ponto_pk";
        $sql.="       ,ic_status";
        $sql.="  FROM ponto_folha_registros";
        $sql.=" WHERE ponto_folha_pk =".$ponto_folha_pk;
        $sql.="   AND colaborador_pk =".$colaborador_pk;
        $sql.="   AND dt_hora_ponto >='".$dt_periodo_ini." 00:00:00'";
        $sql.="   AND dt_hora_ponto <='".$dt_periodo_fim." 23:59:59'";
        $sql.=" ORDER BY dt_hora_ponto";
       
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);  
        if(count($query)>0){
            return $query[0]['pk'];
        } 
        else{
            return "";
        }
        
    }

    public function getVerificado($dt_escala, $colaboradores_pk,$agenda_colaborador_padrao_pk){
        $sql = "";
        $sql.= "  select v.pk, v.ic_verificado 
                  from validar_reloginho v
                  inner join agenda_colaborador_padrao a on a.leads_pk = v.leads_pk
                  where 1=1
                  and v.colaborador_pk = ".$colaboradores_pk."
                  and v.dt_hora_ponto ='".$dt_escala."'
                  ORDER BY v.dt_cadastro desc
                  limit 1 ";
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $query;
    }

    public function listarDadosApontamento($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk,$ic_historico) {
        $arrApontamento = [];
        $arrDadosApontamento = [];
        $tipo_apontamento_pk = '';
        $apontamento_pk = null; // <-- inicializa para evitar "Undefined variable"

        // Listar os apontamentos por tipo (protege se as funções retornarem null/false)
        $arrApontamentoAfastamento = $this->listarApontamentoAfastamento($dt_escala, $colaboradores_pk, $ic_historico);
        if (is_array($arrApontamentoAfastamento) && count($arrApontamentoAfastamento) !== 0) {
            $arrApontamento = array_merge($arrApontamento, $arrApontamentoAfastamento);
        }

        $arrApontamentoFerias = $this->listarApontamentoFerias($dt_escala, $colaboradores_pk, $ic_historico);
        if (is_array($arrApontamentoFerias) && count($arrApontamentoFerias) !== 0) {
            $arrApontamento = array_merge($arrApontamento, $arrApontamentoFerias);
        }

        $arrApontamentoPonto = $this->listarApontamentoPonto($dt_escala, $colaboradores_pk, $ic_historico);
        if (is_array($arrApontamentoPonto) && count($arrApontamentoPonto) !== 0) {
            $arrApontamento = array_merge($arrApontamento, $arrApontamentoPonto);
        }

        $arrApontamentoFalta = $this->listarApontamentoFalta($dt_escala, $colaboradores_pk, $ic_historico);
        if (is_array($arrApontamentoFalta) && count($arrApontamentoFalta) !== 0) {
            $arrApontamento = array_merge($arrApontamento, $arrApontamentoFalta);
        }

        $arrApontamentoFolga = $this->listarApontamentoFolga($dt_escala, $colaboradores_pk, $ic_historico);
        if (is_array($arrApontamentoFolga) && count($arrApontamentoFolga) !== 0) {
            $arrApontamento = array_merge($arrApontamento, $arrApontamentoFolga);
        }

        $arrApontamentoDisciplina = $this->listarApontamentoDisciplina($dt_escala, $colaboradores_pk, $ic_historico);
        if (is_array($arrApontamentoDisciplina) && count($arrApontamentoDisciplina) !== 0) {
            $arrApontamento = array_merge($arrApontamento, $arrApontamentoDisciplina);
        }

        // Ordenar o array pelo campo 'dt_registro' em ordem crescente (com proteção)
        usort($arrApontamento, function ($a, $b) {
            $ta = isset($a['dt_registro']) ? strtotime($a['dt_registro']) : 0;
            $tb = isset($b['dt_registro']) ? strtotime($b['dt_registro']) : 0;
            if ($ta === $tb) return 0;
            return ($ta < $tb) ? -1 : 1;
        });

        // Pega o tipo_apontamento_pk do registro mais recente (último do array ordenado)
        if (count($arrApontamento) > 0) {
            $ultimo = $arrApontamento[count($arrApontamento) - 1];
            $tipo_apontamento_pk = $ultimo['tipo_apontamento_dados_pk'] ?? '';
            $apontamento_pk = $ultimo['apontamento_pk'] ?? null;
        }

        // Adiciona os dados do apontamento ordenado
        $arrDadosApontamento[] = [
            "apontamento_pk" => $apontamento_pk,
            "tipo_apontamento_pk" => $tipo_apontamento_pk,
            "arrApontamento" => $arrApontamento
        ];

        return $arrDadosApontamento;

    }
    
    

    
    public function listarApontamentoPonto($dt_escala, $colaboradores_pk,$ic_historico){
        $sql = "";
        $sql.= "  SELECT CASE acp.ic_status when 1 then 'Ativo' when 2 then 'Excluido' end ds_status ,(1) AS tipo_apontamento_dados_pk,ap.hr_excedentes,ap.hr_faltantes,ap.hr_trabalhadas, ap.dt_cadastro dt_registro, date_format(acp.dt_cadastro,'%d/%m/%Y %H:%i:%s')dt_cadastro , u.ds_usuario,acp.pk apontamento_pk,acp.tipo_apontamento_pk,ap.dt_ponto, ap.hr_ponto, ap.tipo_ponto_pk, ap.ds_obs_ponto";
        $sql.= "    FROM apontamento_ponto ap";
        $sql.= "   INNER JOIN usuarios u ON ap.usuario_cadastro_pk = u.pk";
        $sql.= "   INNER JOIN agenda_colaborador_apontamento acp ON ap.agenda_colaborador_apontamento_pk = acp.pk";
        $sql.= "   WHERE ap.dt_ponto ='".$dt_escala."'";
        $sql.= "     AND acp.colaborador_pk  = ".$colaboradores_pk;
        // SE O IC_HISTORICO FOR DIFERENTE DE 0 SIGNIFICA QUE PEGA TODOS OS VALORES
        //SE FOR IGUAL A 0 SÓ EXIBE OS APONTAMENTOS ATIVOS
        if($ic_historico==0){
            $sql.= "     AND acp.ic_status  = 1";
        }
        
       
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $query;
    }

    public function listarApontamentoFalta($dt_escala, $colaboradores_pk,$ic_historico){
        try{
            $sql = "";
            $sql.= "  SELECT CASE acp.ic_status when 1 then 'Ativo' when 2 then 'Excluido' end ds_status , (2) AS tipo_apontamento_dados_pk, af.dt_cadastro dt_registro, date_format(acp.dt_cadastro,'%d/%m/%Y %H:%i:%s')dt_cadastro , u.ds_usuario,acp.pk apontamento_pk,acp.tipo_apontamento_pk,af.motivo_falta_pk, af.dt_falta, af.ds_obs_falta";
            $sql.= "    FROM apontamento_falta af";
            $sql.= "   INNER JOIN agenda_colaborador_apontamento acp ON af.agenda_colaborador_apontamento_pk = acp.pk";
            $sql.= "   INNER JOIN usuarios u ON af.usuario_cadastro_pk = u.pk";
            //$sql.= "   WHERE af.dt_falta ='".$dt_escala."'";
            $sql.= "   WHERE '".$dt_escala."' BETWEEN af.dt_inicio_atestado AND af.dt_fim_atestado";
            
            $sql.= "     AND acp.colaborador_pk  = ".$colaboradores_pk;
            // SE O IC_HISTORICO FOR DIFERENTE DE 0 SIGNIFICA QUE PEGA TODOS OS VALORES
            //SE FOR IGUAL A 0 SÓ EXIBE OS APONTAMENTOS ATIVOS
            if($ic_historico==0){
                $sql.= "     AND acp.ic_status  = 1";
            }
            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $query;
        }
        catch(Throwable $e){
            print_r($e->getMessage());
            die();
        }
        
    }

    public function listarApontamentoFolga($dt_escala, $colaboradores_pk,$ic_historico){
        $sql = "";
        $sql.= "  SELECT CASE acp.ic_status when 1 then 'Ativo' when 2 then 'Excluido' end ds_status , (3) AS tipo_apontamento_dados_pk, af.dt_cadastro dt_registro, date_format(acp.dt_cadastro,'%d/%m/%Y %H:%i:%s')dt_cadastro , u.ds_usuario,acp.pk apontamento_pk,acp.tipo_apontamento_pk,af.motivo_folga_pk, af.dt_folga, af.ds_obs_folga, af.apontamento_falta_pk,f.nome nome_feriado";
        $sql.= "    FROM apontamento_folga af";
        
        $sql.= "   INNER JOIN usuarios u ON af.usuario_cadastro_pk = u.pk";
        $sql.= "   INNER JOIN agenda_colaborador_apontamento acp ON af.agenda_colaborador_apontamento_pk = acp.pk";
        $sql.= "   LEFT JOIN feriados f ON af.feriado_pk = f.pk";
        //$sql.= "   WHERE '".$dt_escala."' BETWEEN af.dt_inicio_atestado AND af.dt_fim_atestado";
        $sql.= "   WHERE af.dt_folga ='".$dt_escala."'";
        $sql.= "     AND acp.colaborador_pk  = ".$colaboradores_pk;
        // SE O IC_HISTORICO FOR DIFERENTE DE 0 SIGNIFICA QUE PEGA TODOS OS VALORES
        //SE FOR IGUAL A 0 SÓ EXIBE OS APONTAMENTOS ATIVOS
        if($ic_historico==0){
            $sql.= "     AND acp.ic_status  = 1";
        }
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $query;

    }
    public function listarApontamentoDisciplina($dt_escala, $colaboradores_pk,$ic_historico){
        $sql = "";
        $sql.= "  SELECT CASE acp.ic_status when 1 then 'Ativo' when 2 then 'Excluido' end ds_status , (8) AS tipo_apontamento_dados_pk, af.dt_cadastro dt_registro, date_format(acp.dt_cadastro,'%d/%m/%Y %H:%i:%s')dt_cadastro , u.ds_usuario,acp.pk apontamento_pk,acp.tipo_apontamento_pk, af.dt_disciplina";
        $sql.= "    FROM apontamento_disciplina af";
        
        $sql.= "   INNER JOIN usuarios u ON af.usuario_cadastro_pk = u.pk";
        $sql.= "   INNER JOIN agenda_colaborador_apontamento acp ON af.agenda_colaborador_pk = acp.pk";
        //$sql.= "   WHERE '".$dt_escala."' BETWEEN af.dt_inicio_atestado AND af.dt_fim_atestado";
        $sql.= "   WHERE af.dt_disciplina ='".$dt_escala."'";
        $sql.= "     AND acp.colaborador_pk  = ".$colaboradores_pk;
        // SE O IC_HISTORICO FOR DIFERENTE DE 0 SIGNIFICA QUE PEGA TODOS OS VALORES
        //SE FOR IGUAL A 0 SÓ EXIBE OS APONTAMENTOS ATIVOS
        if($ic_historico==0){
            $sql.= "     AND acp.ic_status  = 1";
        }
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $query;

    }

    public function listarApontamentoAfastamento($dt_escala, $colaboradores_pk,$ic_historico){

        $sql = "";
        $sql.= "  SELECT CASE acp.ic_status when 1 then 'Ativo' when 2 then 'Excluido' end ds_status ,  (5) AS tipo_apontamento_dados_pk, aa.dt_cadastro dt_registro, date_format(acp.dt_cadastro,'%d/%m/%Y %H:%i:%s')dt_cadastro , u.ds_usuario,acp.pk apontamento_pk,acp.tipo_apontamento_pk,aa.motivo_afastamento_pk, aa.dt_ini_afastamento, aa.dt_fim_afastamento, aa.ds_obs_afastamento";
        $sql.= "    FROM apontamento_afastamento aa";
        $sql.= "   INNER JOIN agenda_colaborador_apontamento acp ON aa.agenda_colaborador_apontamento_pk = acp.pk";
        $sql.= "   INNER JOIN usuarios u ON aa.usuario_cadastro_pk = u.pk";
        $sql.= "   WHERE '".$dt_escala."' BETWEEN aa.dt_ini_afastamento AND aa.dt_fim_afastamento";
        $sql.= "     AND acp.colaborador_pk  = ".$colaboradores_pk;
        // SE O IC_HISTORICO FOR DIFERENTE DE 0 SIGNIFICA QUE PEGA TODOS OS VALORES
        //SE FOR IGUAL A 0 SÓ EXIBE OS APONTAMENTOS ATIVOS
        if($ic_historico==0){
            $sql.= "     AND acp.ic_status  = 1";
        }
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $query;
    }

    public function listarApontamentoFerias($dt_escala, $colaboradores_pk,$ic_historico){

        $sql = "";
        $sql.= "  SELECT CASE acp.ic_status when 1 then 'Ativo' when 2 then 'Excluido' end ds_status , (6) AS tipo_apontamento_dados_pk, af.dt_cadastro dt_registro, date_format(acp.dt_cadastro,'%d/%m/%Y %H:%i:%s')dt_cadastro , u.ds_usuario,acp.pk apontamento_pk,acp.tipo_apontamento_pk,af.dt_ini_ferias, af.dt_fim_ferias, af.ds_obs_ferias";
        $sql.= "    FROM apontamento_ferias af";
        $sql.= "   INNER JOIN usuarios u ON af.usuario_cadastro_pk = u.pk";
        $sql.= "   INNER JOIN agenda_colaborador_apontamento acp ON af.agenda_colaborador_apontamento_pk = acp.pk";
        $sql.= "   WHERE af.dt_ini_ferias <='".$dt_escala."'";
        $sql.= "     AND af.dt_fim_ferias  >='".$dt_escala."'";
        $sql.= "     AND acp.colaborador_pk  = ".$colaboradores_pk;
        // SE O IC_HISTORICO FOR DIFERENTE DE 0 SIGNIFICA QUE PEGA TODOS OS VALORES
        //SE FOR IGUAL A 0 SÓ EXIBE OS APONTAMENTOS ATIVOS
        if($ic_historico==0){
            $sql.= "     AND acp.ic_status  = 1";
        }
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $query;
    }


    public function listarDadosPonto5x2($dt_periodo_ini, $dt_periodo_fim, $colaboradores_pk, $hr_inicio_expediente, $hr_termino_expediente, $hr_saida_intervalo, $hr_retorno_intervalo, $hr_expediente, $ic_intrajornada, $agenda_colaborador_padrao_pk, $ic_preencher_folha){              

        $diasEscala = $this->listarDiasEscala($agenda_colaborador_padrao_pk,$dt_periodo_ini, $dt_periodo_fim);
        $turnos_pk = $this->listarTurnosPk($agenda_colaborador_padrao_pk);
        for($i=0; $i<count($diasEscala); $i++){  
            $dt_escala = $diasEscala[$i]['dt_escala'];
            $arrApontamento = $this->listarDadosApontamento($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk,0);
            $ponto_ini_expediente = "";
            $ponto_term_expediente = "";
            $ponto_ini_intervalo = "";
            $ponto_term_intervalo = "";
            $hr_excedentes = " ";
            $hr_faltante = " ";
            $horas_trabalhadas = " ";
            $obs = " ";

            $arrPontos = [];
            $diaAtual = date('Y-m-d');

            /*echo $diaAtual.'-';
            echo $dt_escala.'<br>';*/

            //if(count($arrApontamento[0]['arrApontamento']) == 0){

                //if($diasEscala[$i]['ic_escala'] == 1){
        
                    if($turnos_pk!=3){
                        $arrNoturno = $this->verificarPontoEscalaNoturna($dt_escala,$colaboradores_pk);
                        $arrNormal = $this->verificarPontoEscalaNormal($dt_escala,$colaboradores_pk);
                        $query =[];
                        if(count($arrNormal)>0){
                            $query = $arrNormal;
                        }
                        else if(count($arrNoturno)>3){
                            $query = $arrNoturno;
                            
                        }
                    }
                    else{
                        $arrNoturno = $this->verificarPontoEscalaNoturna($dt_escala,$colaboradores_pk);
                        $arrNormal = $this->verificarPontoEscalaNormal($dt_escala,$colaboradores_pk);
                        $query =[];
                        if(count($arrNormal)>2){
                            $query = $arrNormal;
                        }
                        else if(count($arrNoturno)>1){
                            $query = $arrNoturno;
                            
                        }
                    }

                    for ($l = 0; $l < count($query); $l++) {
                        switch ($query[$l]['tipo_ponto_pk']) {
                        case 1:  // Início do expediente
        
                            $ponto_ini_expediente = $query[$l]["hora_ponto"];
                            
                            break;
                        case 2:  // Término do expediente
                                $ponto_term_expediente = $query[$l]["hora_ponto"];
                            
                            break;
            
                        case 3:  // Início do intervalo
                                $ponto_ini_intervalo = $query[$l]["hora_ponto"];
                            
                            break;
            
                        case 4:  // Término do intervalo
                                $ponto_term_intervalo = $query[$l]["hora_ponto"];
                            
                            break;
                        }
                    
                    }

                    if(
                        $ponto_ini_expediente != '' ||
                        $ponto_term_expediente != '' ||
                        $ponto_ini_intervalo != '' ||
                        $ponto_term_intervalo != ''
                    ){
                        //VERIFICA SE BATEU O PONTO NO DIA DA FOLGA 
                        if($diasEscala[$i]['ic_escala'] == 1){
                            $tipo_ponto_pk = 1;
                        }
                        else{
                            $tipo_ponto_pk = 5;
                        }
                        
                    }
                    else{
                        
                        if($diasEscala[$i]['ic_escala'] == 1){
                            //FALTA
                            $tipo_ponto_pk = 10;
                        }
                        else{
                            //FOLGA
                            $tipo_ponto_pk = 5;
                        }
                        
                        $ponto_ini_expediente = "";
                        $ponto_term_expediente = "";
                        $ponto_ini_intervalo = "";
                        $ponto_term_intervalo = "";
                        
                    }
                    if(count($arrApontamento[0]['arrApontamento']) > 0){
                        $arrDadosApontamento = $arrApontamento[0]['arrApontamento'];
                        for($a=0;$a<count($arrDadosApontamento);$a++){
                            
                            $tipo = (int)$arrApontamento[0]['tipo_apontamento_pk'];
                            $tipoComp = (int)$arrDadosApontamento[$a]['tipo_apontamento_dados_pk'];

                            if($tipo == $tipoComp){
                                switch($tipo){
                                case 1:
                                    $tipo_ponto_pk = 1;
                                    if($arrDadosApontamento[$a]['tipo_ponto_pk'] == 1){
                                        $ponto_ini_expediente = $arrDadosApontamento[$a]["hr_ponto"];
                                    }
                                    if($arrDadosApontamento[$a]['tipo_ponto_pk'] == 2){
                                        $ponto_term_expediente = $arrDadosApontamento[$a]["hr_ponto"];
                                    }
                                    if($arrDadosApontamento[$a]['tipo_ponto_pk'] == 3){
                                        $ponto_ini_intervalo = $arrDadosApontamento[$a]["hr_ponto"];
                                    }
                                    if($arrDadosApontamento[$a]['tipo_ponto_pk'] == 4){
                                        $ponto_term_intervalo = $arrDadosApontamento[$a]["hr_ponto"];
                                    }
                                    //Função que calcula as horas trabalhadas
                                    $horas_trabalhadas = $this->calcularHrsTrabalhadas($ponto_ini_expediente, $ponto_term_expediente, $ponto_ini_intervalo, $ponto_term_intervalo, $hr_retorno_intervalo, $hr_saida_intervalo, $ic_intrajornada, '');
                                    
                                    //Calcula HE e HF
                                    if($hr_expediente!="" && $horas_trabalhadas > "06:00"){
                                        if($horas_trabalhadas < $hr_expediente){
                                            $queryfaltantes = $this->retornarDifHoraFaltantes($hr_expediente,$horas_trabalhadas); 
                                            $hr_faltante = str_replace("-","",$queryfaltantes[0]['dif']);                            
                                        }else if ($horas_trabalhadas > $hr_expediente){
                                            $queryexcedente = $this->retornarDifHora($hr_expediente,$horas_trabalhadas); 
                                            $hr_excedentes = $queryexcedente[0]['dif'];
                                        }else {
                                            $hr_excedentes = " ";
                                            $hr_faltante = " ";
                                        }                
                                    }else{
                                        $hr_excedentes = " ";
                                        $hr_faltante = " ";
                                    } 
                                    break;
                                case 2:
                                    $motivo_falta_pk = $arrDadosApontamento[$a]['motivo_falta_pk'];
                                    $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
        
                                    if($tipo_ponto_pk==2){
                                        $obs = "Falta";
                                    } else if($tipo_ponto_pk==11){
                                        $obs = "Abonada";
                                    }else if($tipo_ponto_pk==16){
                                        $obs = "Atestado";
                                        $ponto_ini_expediente = "";
                                        $ponto_term_expediente = "";
                                        $ponto_ini_intervalo = "";
                                        $ponto_term_intervalo = "";
                                    }else if($tipo_ponto_pk==18){
                                        $obs = "Declaração da defesa civil";
                                    }
                                    else if($tipo_ponto_pk==28){
                                        $obs = "Apoio Operacional ";
                                    }
                                    else if($tipo_ponto_pk==29){
                                        $obs = "Atestado por acompanhar filho ate 5 anos";
                                    }
                                    else if($tipo_ponto_pk==30){
                                        $obs = "Atestado por serviço Justiça Eleitoral";
                                    }
                                    else if($tipo_ponto_pk==31){
                                        $obs = "Doação de sangue";
                                    }
                                    else if($tipo_ponto_pk==32){
                                        $obs = "Atraso";
                                    }
                                    else if($tipo_ponto_pk==33){
                                        $obs = "Declaração de horas abonar";
                                    }
                                    else if($tipo_ponto_pk==34){
                                        $obs = "Sem Justificativa";
                                    }
                                    else if($tipo_ponto_pk==35){
                                        $obs = "Reciclagem";
                                    }
                                    else if($tipo_ponto_pk==36){
                                        $obs = "Audiência ";
                                    }
                                    
                                    for($l=0; $l<count($query); $l++){
                    
                      
                                        if($query[$l]['tipo_ponto_pk'] == 1){
                                            $ponto_ini_expediente = $query[$l]["hora_ponto"];
                                        }
                                        if($query[$l]['tipo_ponto_pk'] == 2){
                                            $ponto_term_expediente = $query[$l]["hora_ponto"];
                                        }
                                        if($query[$l]['tipo_ponto_pk'] == 3){
                                            $ponto_ini_intervalo = $query[$l]["hora_ponto"];
                                        }
                                        if($query[$l]['tipo_ponto_pk'] == 4){
                                            $ponto_term_intervalo = $query[$l]["hora_ponto"];
                                        }
                                         
                                    }
                                    
                                    
                                    break;
                                case 3:
                                    $motivo_folga_pk = $arrDadosApontamento[$a]['motivo_folga_pk'];
                                    $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                    if($tipo_ponto_pk==3){
                                        $obs = "Folga";
                                    } else if($tipo_ponto_pk==20){
                                        $obs = "Folga compensatória";
                                    }else if($tipo_ponto_pk==21){
                                        $obs = "Folga de feriado";
                                    }else if($tipo_ponto_pk==25){
                                        $obs = "Troca Folga";
                                    }
                                    else if($tipo_ponto_pk==26){
                                        $obs = "Folga trabalhada";
                                    }
                                    else if($tipo_ponto_pk==27){
                                        $obs = "Escala Errada";
                                    }
                                    $ponto_ini_expediente = "";
                                    $ponto_term_expediente = "";
                                    $ponto_ini_intervalo = "";
                                    $ponto_term_intervalo = "";
                                    if($motivo_folga_pk == '1'){
                                        $situacao = "Folga Trabalhada";
                                    }else if($motivo_folga_pk == '2'){
                                        $situacao = "Escala Errada";
                                    }else if($motivo_folga_pk == '3'){
                                        $situacao = "Convocação Normal";
                                    }
        
                                    
                                    break;
                                case 5:
                                    $motivo_afastamento_pk = $arrDadosApontamento[$a]['motivo_afastamento_pk'];
                                    $tipo_ponto_pk = 15;
                                    $ponto_ini_expediente = "";
                                    $ponto_term_expediente = "";
                                    $ponto_ini_intervalo = "";
                                    $ponto_term_intervalo = "";
                                    if($motivo_afastamento_pk == 1){
                                        $obs = "Motivos Médicos";
                                    }else if($motivo_afastamento_pk == 2){
                                        $obs = "Invalides";
                                    } 
                                    
                                    break;
                                case 6:
                                    $tipo_ponto_pk = 12;
                                    $ponto_ini_expediente = "";
                                    $ponto_term_expediente = "";
                                    $ponto_ini_intervalo = "";
                                    $ponto_term_intervalo = "";
                                    break;
                                } 
                            } 
                        }
                    }
                    
                    if($tipo_ponto_pk == 1){
                        //Função que calcula as horas trabalhadas
                        $horas_trabalhadas = $this->calcularHrsTrabalhadas($ponto_ini_expediente, $ponto_term_expediente, $ponto_ini_intervalo, $ponto_term_intervalo, $hr_retorno_intervalo, $hr_saida_intervalo, $ic_intrajornada);
                        //echo $horas_trabalhadas.'<br>';
                        //Calcula HE e HF
                        if($hr_expediente!="" && $horas_trabalhadas > "06:00"){
                            if($horas_trabalhadas < $hr_expediente){
                                $queryfaltantes = $this->retornarDifHoraFaltantes($hr_expediente,$horas_trabalhadas); 
                                $hr_faltante = str_replace("-","",$queryfaltantes[0]['dif']);                            
                            }else if ($horas_trabalhadas > $hr_expediente){
                                $queryexcedente = $this->retornarDifHora($hr_expediente,$horas_trabalhadas); 
                                $hr_excedentes = $queryexcedente[0]['dif'];
                            }else {
                                $hr_excedentes = " ";
                                $hr_faltante = " ";
                            }                
                        }else{
                            $hr_excedentes = " ";
                            $hr_faltante = " ";
                        } 
                    }else{
                        $hr_excedentes = " ";
                        $hr_faltante = " ";
                        $horas_trabalhadas = " ";
                    }

                    //array de pontos por dia
                    $arrPontos[] = array(
                        "tipo_ponto_pk" => $tipo_ponto_pk,
                        "ponto_ini_expediente" => $ponto_ini_expediente,
                        "ponto_ini_intervalo" => $ponto_ini_intervalo,
                        "ponto_term_intervalo" => $ponto_term_intervalo,
                        "ponto_term_expediente" => $ponto_term_expediente,
                        "horas_trabalhadas" => $horas_trabalhadas,
                        "hr_excedentes" => $hr_excedentes,
                        "hr_faltante" => $hr_faltante,
                        "obs" => $obs
                    );

                    //array de dias por período 
                    $arrDias[] = array(
                        "dt_hora_ponto" => $dt_escala." 00:00:00",
                        "pontos_dia"=>$arrPontos
                    );
                }
        
             return $arrDias;
    }

    public function listarPontosDia($colaboradores_pk, $dt_escala){
        //Query de verificação dos pontos
        $sql='';
        $sql.='Select p.pk';
        $sql.='      ,p.tipo_ponto_pk';
        $sql.='      ,DATE_FORMAT(p.dt_hora_ponto, "%H:%i") hora_ponto'; 
        $sql.='      ,DATE_FORMAT(p.dt_hora_ponto, "%d-%m-%Y") dt_ponto'; 
        $sql.='  from ponto p';
        $sql.=' where p.colaborador_pk ='.$colaboradores_pk;
        $sql.='   and p.dt_hora_ponto >="'.$dt_escala.' 00:00:00"';
        $sql.='   and p.dt_hora_ponto <="'.$dt_escala.' 23:59:59"';
        $sql.=" order by p.dt_hora_ponto";
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC); 
        
        $ic_status = 'Falta';

        //Verificação de tipo de ponto
        for($l=0; $l<count($query); $l++){
            $ic_status = 'Expediente';
            if($query[$l]['tipo_ponto_pk'] == 1){
                $ponto_ini_expediente = $query[$l]["hora_ponto"];
            }
            if($query[$l]['tipo_ponto_pk'] == 2){
                $ponto_term_expediente = $query[$l]["hora_ponto"];
            }
            if($query[$l]['tipo_ponto_pk'] == 3){
                $ponto_ini_intervalo = $query[$l]["hora_ponto"];
            }
            if($query[$l]['tipo_ponto_pk'] == 4){
                $ponto_term_intervalo = $query[$l]["hora_ponto"];
            }
        }
    
        $arrPontos[] = array(
            "status" => $ic_status,
            "ponto_ini_expediente" => $ponto_ini_expediente,
            "ponto_ini_intervalo" => $ponto_ini_intervalo,
            "ponto_term_intervalo" => $ponto_term_intervalo,
            "ponto_term_expediente" => $ponto_term_expediente
        );
        return $arrPontos;
    }

    public function listarModalPonto($colaborador_pk, $dt_escala){
        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio

            $dadosEscala = $this->listarDadosEscala($dt_escala, $dt_escala, $colaborador_pk, '');
            $hr_saida_intervalo = $dadosEscala[0]["hr_saida_intervalo"];
            $hr_inicio_expediente = $dadosEscala[0]["hr_inicio_expediente"];
            $hr_termino_expediente = $dadosEscala[0]["hr_termino_expediente"];
            $hr_retorno_intervalo = $dadosEscala[0]["hr_retorno_intervalo"];
            $ic_intrajornada = $dadosEscala[0]["ic_intrajornada"];
            $hr_expediente = $dadosEscala[0]["hr_expediente"];
            $agenda_colaborador_padrao_pk = $dadosEscala[0]["pk"];

            

            $diasEscala = $this->listarDiasEscala($agenda_colaborador_padrao_pk,$dt_escala, $dt_escala);

            $arrApontamento = $this->listarDadosApontamento($dt_escala, $colaborador_pk, $agenda_colaborador_padrao_pk,1);
            
            $hora ="";
            $arrDadosApontamento = $arrApontamento[0]['arrApontamento'];
            
                for($a=0;$a<count($arrDadosApontamento);$a++){
                        $nome_feriado ="";
                        $tipoComp = (int)$arrDadosApontamento[$a]['tipo_apontamento_dados_pk'];
                        $usuario_cadastro = $arrDadosApontamento[$a]['ds_usuario']; 
                        $dt_cadastro = $arrDadosApontamento[$a]['dt_cadastro']; 
                        $ds_status = $arrDadosApontamento[$a]['ds_status']; 
                        
                        //if($tipo == $tipoComp){
                            switch($tipoComp){
                                case 1:
                                    $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                    $ic_apontamento = 1;
                                    if($tipo_ponto_pk==1){
                                        $obs = "Ponto/Expediente";
                                    }
                                    else if($tipo_ponto_pk==33){
                                        $obs = "Declaração de horas abonar";
                                    }
                                    else if($tipo_ponto_pk==36){
                                        $obs = "Audiência";
                                    }
                                    else if($tipo_ponto_pk==37){
                                        $obs = "Atestado de horas";
                                    }
                                    if($arrDadosApontamento[$a]['tipo_ponto_pk'] == 1){
                                        $ic_apontamento_ini = 1;
                                        $hora = $arrDadosApontamento[$a]["hr_ponto"];
                                    }
                                    if($arrDadosApontamento[$a]['tipo_ponto_pk'] == 2){
                                    
                                        $ic_apontamento_ter = 2;
                                        $hora = $arrDadosApontamento[$a]["hr_ponto"];
                                    }
                                    if($arrDadosApontamento[$a]['tipo_ponto_pk'] == 3){
                                    
                                        $ic_apontamento_ini_int = 3;
                                        $hora = $arrDadosApontamento[$a]["hr_ponto"];
                                    }
                                    if($arrDadosApontamento[$a]['tipo_ponto_pk'] == 4){
                                        
                                        $ic_apontamento_fim_int = 4;
                                        $hora = $arrDadosApontamento[$a]["hr_ponto"];
                                    }

                                    $hr_excedentes = $arrDadosApontamento[$a]["hr_excedentes"];
                                    $hr_faltante = $arrDadosApontamento[$a]["hr_faltantes"];
                                    $horas_trabalhadas = $arrDadosApontamento[$a]["hr_trabalhadas"];
                                    break;
                                case 2:
                                    $ic_apontamento = 1;
                                    $motivo_falta_pk = $arrDadosApontamento[$a]['motivo_falta_pk'];
                                    $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
    
                                    
                                    if($tipo_ponto_pk==2){
                                        $obs = "Falta";
                                        $ponto_ini_expediente = "";
                                        $ponto_term_expediente = "";
                                        $ponto_ini_intervalo = "";
                                        $ponto_term_intervalo = "";
                                        $hr_excedentes = "";
                                        $hr_faltante = "";
                                        $horas_trabalhadas = "";
                                    } else if($tipo_ponto_pk==11){
                                        $obs = "Abonada";
                                    }else if($tipo_ponto_pk==16){
                                        $obs = "Atestado";
                                        $ponto_ini_expediente = "";
                                        $ponto_term_expediente = "";
                                        $ponto_ini_intervalo = "";
                                        $ponto_term_intervalo = "";
                                        $hr_excedentes = "";
                                        $hr_faltante = "";
                                        $horas_trabalhadas = "";
                                    }else if($tipo_ponto_pk==18){
                                        $obs = "Declaração da defesa civil";
                                    }
                                    else if($tipo_ponto_pk==28){
                                        $obs = "Apoio Operacional ";
                                    }
                                    else if($tipo_ponto_pk==29){
                                        $obs = "Atestado por acompanhar filho ate 5 anos";
                                    }
                                    else if($tipo_ponto_pk==30){
                                        $obs = "Atestado por serviço Justiça Eleitoral";
                                    }
                                    else if($tipo_ponto_pk==31){
                                        $obs = "Doação de sangue";
                                    }
                                    else if($tipo_ponto_pk==32){
                                        $obs = "Atraso";
                                    }
                                    else if($tipo_ponto_pk==33){
                                        $obs = "Declaração de horas abonar";
                                    }
                                    else if($tipo_ponto_pk==34){
                                        $obs = "Sem Justificativa";
                                    }
                                    else if($tipo_ponto_pk==35){
                                        $obs = "Reciclagem";
                                    }
                                    else if($tipo_ponto_pk==36){
                                        $obs = "Audiência ";
                                    }
                                    
                                    
                                    
                                    
                                    break;
                                case 3:
                                    $ic_apontamento = 1;
                                    $motivo_folga_pk = $arrDadosApontamento[$a]['motivo_folga_pk'];
                                    $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                    $nome_feriado = $arrDadosApontamento[$a]['nome_feriado'];
                                    
                            
                                    if($motivo_folga_pk == '1'){
                                        $obs = "Folga Trabalhada";
                                    }else if($motivo_folga_pk == '2'){
                                        $obs = "Escala Errada";
                                    }else if($motivo_folga_pk == '3'){
                                        $obs = "Convocação Normal";
                                    }
                                    if($tipo_ponto_pk==3){
                                        $obs = "Folga";
                                    } else if($tipo_ponto_pk==20){
                                        $obs = "Folga compensatória";
                                    }else if($tipo_ponto_pk==21){
                                        $obs = "Folga de feriado";
                                    }else if($tipo_ponto_pk==25){
                                        $obs = "Troca Folga";
                                    }
                                    else if($tipo_ponto_pk==26){
                                        $obs = "Folga trabalhada";
                                    }
                                    else if($tipo_ponto_pk==27){
                                        $obs = "Escala Errada";
                                    }
                                    $ponto_ini_expediente = "";
                                    $ponto_term_expediente = "";
                                    $ponto_ini_intervalo = "";
                                    $ponto_term_intervalo = "";
                                    
    
                                    
                                    break;
                                case 5:
                                    $ic_apontamento = 1;
                                    $motivo_afastamento_pk = $arrDadosApontamento[$a]['motivo_afastamento_pk'];
                                    $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                    $ponto_ini_expediente = "";
                                    $ponto_term_expediente = "";
                                    $ponto_ini_intervalo = "";
                                    $ponto_term_intervalo = "";
                                    if($motivo_afastamento_pk == 1){
                                        $obs = "Motivos Médicos";
                                    }else if($motivo_afastamento_pk == 2){
                                        $obs = "Invalides";
                                    }                             
                                    break;
                                case 6:
                                 
                                    $ic_apontamento = 1;
                                    $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                    $obs = "Férias";
                                    $ponto_ini_expediente = "";
                                    $ponto_term_expediente = "";
                                    $ponto_ini_intervalo = "";
                                    $ponto_term_intervalo = "";
                                    break;
                                case 8:
                                    $ic_apontamento = 1;
                                    $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                    if($tipo_ponto_pk==8){
                                        $obs = "Disciplina";
                                    } else if($tipo_ponto_pk==17){
                                        $obs = "Advertencia";
                                    }else if($tipo_ponto_pk==19){
                                        $obs = "Demissão";
                                    }else if($tipo_ponto_pk==22){
                                        $obs = "Justa causa";
                                    }else if($tipo_ponto_pk==23){
                                        $obs = "Recisão indireta";
                                    }else if($tipo_ponto_pk==24){
                                        $obs = "Suspensão";
                                    }
                                    $ponto_ini_expediente = "";
                                    $ponto_term_expediente = "";
                                    $ponto_ini_intervalo = "";
                                    $ponto_term_intervalo = "";
                                    break;
                            } 
                        //} 
                    
                        $arrPontos[] = array(
                            "indice" => ($a+1),
                            "dt_escala" => $dt_escala,
                            "hora" => $hora,
                            "tipo_apontamento" => $obs." ".$nome_feriado,
                            "usuario_cadastro" => $usuario_cadastro,
                            "ds_status" => $ds_status,
                            "dt_cadastro" => $dt_cadastro,
                        );
                        
                    }
            

        
            
            $retorno->data = $arrPontos;
            $retorno->status = true;
            $retorno->message = 'Dados Salvos com sucesso !';
            $retorno->iTotalDisplayRecords = count($arrPontos);
            $retorno->iTotalRecords = count($arrPontos);

            echo json_encode($retorno);
            exit(0);
        }
        catch(Throwable $e){
            print_r($e->getMessage);
            die();
        }
        
    }
    
    public function listarDadosRegerar($ponto_folha_pk,$dt_periodo_ini, $dt_periodo_fim, $colaboradores_pk, $hr_inicio_expediente, $hr_termino_expediente, $hr_saida_intervalo, $hr_retorno_intervalo, $hr_expediente, $ic_intrajornada, $agenda_colaborador_padrao_pk, $ic_preencher_folha){              

        
        $diasEscala = $this->listarDiasEscala($agenda_colaborador_padrao_pk,$dt_periodo_ini, $dt_periodo_fim);
        $turnos_pk = $this->listarTurnosPk($agenda_colaborador_padrao_pk);
        
        for($i=0; $i<count($diasEscala); $i++){  
            $dt_escala = $diasEscala[$i]['dt_escala'];
            $arrPontos = [];


                $registrosFolhaPk = $this->listarDadosRegistrosFolha($ponto_folha_pk, $colaboradores_pk ,$dt_escala,$dt_escala);
        
                $arrApontamento = $this->listarDadosApontamento($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk,0);
                $ponto_ini_expediente = " ";
                $ponto_term_expediente = " ";
                $ponto_ini_intervalo = " ";
                $ponto_term_intervalo = " ";
                $hr_excedentes = " ";
                $hr_faltante = " ";
                $horas_trabalhadas = " ";
                $obs = " ";
                
                //if(count($arrApontamento[0]['arrApontamento']) == 0){
    
                    //if($diasEscala[$i]['ic_escala'] == 1){
            
                        //Query de verificação dos pontos
                                   //Query de verificação dos pontos
                if($turnos_pk!=3){
                    $arrNoturno = $this->verificarPontoEscalaNoturna($dt_escala,$colaboradores_pk);
                    $arrNormal = $this->verificarPontoEscalaNormal($dt_escala,$colaboradores_pk);
                    $query =[];
                    if(count($arrNormal)>0){
                        $query = $arrNormal;
                    }
                    else if(count($arrNoturno)>3){
                        $query = $arrNoturno;
                        
                    }
                }
                else{
                    $arrNoturno = $this->verificarPontoEscalaNoturna($dt_escala,$colaboradores_pk);
                    $arrNormal = $this->verificarPontoEscalaNormal($dt_escala,$colaboradores_pk);
                    $query =[];
                    if(count($arrNormal)>2){
                        $query = $arrNormal;
                    }
                    else if(count($arrNoturno)>1){
                        $query = $arrNoturno;
                        
                    }
                }
                for($l=0; $l<count($query); $l++){
            
                    
                    if($query[$l]['tipo_ponto_pk'] == 1){
                        $ponto_ini_expediente = $query[$l]["hora_ponto"];
                    }
                    if($query[$l]['tipo_ponto_pk'] == 2){
                        $ponto_term_expediente = $query[$l]["hora_ponto"];
                    }
                    if($query[$l]['tipo_ponto_pk'] == 3){
                        $ponto_ini_intervalo = $query[$l]["hora_ponto"];
                    }
                    if($query[$l]['tipo_ponto_pk'] == 4){
                        $ponto_term_intervalo = $query[$l]["hora_ponto"];
                    }
                        
                }
    
                if(
                    $ponto_ini_expediente != '' ||
                    $ponto_term_expediente != '' ||
                    $ponto_ini_intervalo != '' ||
                    $ponto_term_intervalo != ''
                ){
                    //VERIFICA SE BATEU O PONTO NO DIA DA FOLGA 
                    if($diasEscala[$i]['ic_escala'] == 1){
                        $tipo_ponto_pk = 1;
                    }
                    else{
                        $tipo_ponto_pk = 5;
                    }
                    
                }
                else{
                    if($ic_preencher_folha == 1){
                        $tipo_ponto_pk = 1;
                        $ponto_ini_expediente = $hr_inicio_expediente;
                        $ponto_term_expediente = $hr_termino_expediente;
                        $ponto_ini_intervalo = $hr_saida_intervalo;
                        $ponto_term_intervalo = $hr_retorno_intervalo;

                    }
                    else{
                        if($diasEscala[$i]['ic_escala'] == 1){
                            //FALTA
                            $tipo_ponto_pk = 10;
                        }
                        else{
                            //FOLGA
                            $tipo_ponto_pk = 5;
                        }
                        
                        $ponto_ini_expediente = "";
                        $ponto_term_expediente = "";
                        $ponto_ini_intervalo = "";
                        $ponto_term_intervalo = "";
                    }
                }
                if(count($arrApontamento[0]['arrApontamento']) > 0){
                    $arrDadosApontamento = $arrApontamento[0]['arrApontamento'];
                    for($a=0;$a<count($arrDadosApontamento);$a++){
                        $tipo = (int)$arrApontamento[0]['tipo_apontamento_pk'];
                        $tipoComp = (int)$arrDadosApontamento[$a]['tipo_apontamento_dados_pk'];

                        if($tipo == $tipoComp){
                            switch($tipo){
                            case 1:
                                $tipo_ponto_pk = 1;
                                if($arrDadosApontamento[$a]['tipo_ponto_pk'] == 1){
                                    $ponto_ini_expediente = $arrDadosApontamento[$a]["hr_ponto"];
                                }
                                if($arrDadosApontamento[$a]['tipo_ponto_pk'] == 2){
                                    $ponto_term_expediente = $arrDadosApontamento[$a]["hr_ponto"];
                                }
                                if($arrDadosApontamento[$a]['tipo_ponto_pk'] == 3){
                                    $ponto_ini_intervalo = $arrDadosApontamento[$a]["hr_ponto"];
                                }
                                if($arrDadosApontamento[$a]['tipo_ponto_pk'] == 4){
                                    $ponto_term_intervalo = $arrDadosApontamento[$a]["hr_ponto"];
                                }
                                //Função que calcula as horas trabalhadas
                                $horas_trabalhadas = $this->calcularHrsTrabalhadas($ponto_ini_expediente, $ponto_term_expediente, $ponto_ini_intervalo, $ponto_term_intervalo, $hr_retorno_intervalo, $hr_saida_intervalo, $ic_intrajornada, '');
                                
                                //Calcula HE e HF
                                if($hr_expediente!="" && $horas_trabalhadas > "06:00"){
                                    if($horas_trabalhadas < $hr_expediente){
                                        $queryfaltantes = $this->retornarDifHoraFaltantes($hr_expediente,$horas_trabalhadas); 
                                        $hr_faltante = str_replace("-","",$queryfaltantes[0]['dif']);                            
                                    }else if ($horas_trabalhadas > $hr_expediente){
                                        $queryexcedente = $this->retornarDifHora($hr_expediente,$horas_trabalhadas); 
                                        $hr_excedentes = $queryexcedente[0]['dif'];
                                    }else {
                                        $hr_excedentes = " ";
                                        $hr_faltante = " ";
                                    }                
                                }else{
                                    $hr_excedentes = " ";
                                    $hr_faltante = " ";
                                } 

                                break;  
                                
                            case 2:
                                $motivo_falta_pk = $arrApontamento[$a]['motivo_falta_pk'];
                                $tipo_ponto_pk = 10;
                                for($l=0; $l<count($query); $l++){
                    
                      
                                    if($query[$l]['tipo_ponto_pk'] == 1){
                                        $ponto_ini_expediente = $query[$l]["hora_ponto"];
                                    }
                                    if($query[$l]['tipo_ponto_pk'] == 2){
                                        $ponto_term_expediente = $query[$l]["hora_ponto"];
                                    }
                                    if($query[$l]['tipo_ponto_pk'] == 3){
                                        $ponto_ini_intervalo = $query[$l]["hora_ponto"];
                                    }
                                    if($query[$l]['tipo_ponto_pk'] == 4){
                                        $ponto_term_intervalo = $query[$l]["hora_ponto"];
                                    }
                                     
                                }
                                $hr_excedentes = " ";
                                $hr_faltante = " ";
                                $horas_trabalhadas = " ";
                                if($motivo_falta_pk == 1){
                                    $obs = "Abonada";
                                }else if($motivo_falta_pk == 2){
                                    $obs = "Apoio Operacional";
                                }else if($motivo_falta_pk == 3){
                                    $obs = "Atestado";
                                }else if($motivo_falta_pk == 4){
                                    $obs = "Atraso";
                                }else if($motivo_falta_pk == 5){
                                    $obs = "Extensão SDF";
                                }else if($motivo_falta_pk == 6){
                                    $obs = "Falta de efetivo";
                                }else if($motivo_falta_pk == 7){
                                    $obs = "Falta sem justificativa";
                                }else if($motivo_falta_pk == 8){
                                    $obs = "Licença";
                                }else if($motivo_falta_pk == 9){
                                    $obs = "Remanejamento";
                                }else if($motivo_falta_pk == 10){
                                    $obs = "Reciclagem";
                                }
                                else if($motivo_falta_pk == 20){
                                    $obs = "Atestado de Comparecimento";
                                }
                                else if($motivo_falta_pk == 21){
                                    $obs = "Audiência";
                                }
                                else if($motivo_falta_pk == 22){
                                    $obs = "Assunto Particular";
                                }
                                else if($motivo_falta_pk == 23){
                                    $obs = "Mudança de Posto";
                                }
                                break;
                            case 3:
                                $tipo_ponto_pk = 5;
                                $ponto_ini_expediente = " ";
                                $ponto_term_expediente = " ";
                                $ponto_ini_intervalo = " ";
                                $ponto_term_intervalo = " ";
                                $hr_excedentes = " ";
                                $hr_faltante = " ";
                                $horas_trabalhadas = " ";
                                break;
                            case 5:
                                $motivo_afastamento_pk = $arrApontamento[$a]['motivo_afastamento_pk'];
                                $tipo_ponto_pk = 15;
                                $ponto_ini_expediente = " ";
                                $ponto_term_expediente = " ";
                                $ponto_ini_intervalo = " ";
                                $ponto_term_intervalo = " ";
                                $hr_excedentes = " ";
                                $hr_faltante = " ";
                                $horas_trabalhadas = " ";
                                if($motivo_afastamento_pk == 1){
                                    $obs = "Motivos Médicos";
                                }else if($motivo_afastamento_pk == 2){
                                    $obs = "Invalides";
                                } 
                                break;
                            case 6:
                                $tipo_ponto_pk = 12;
                                $ponto_ini_expediente = " ";
                                $ponto_term_expediente = " ";
                                $ponto_ini_intervalo = " ";
                                $ponto_term_intervalo = " ";
                                $hr_excedentes = " ";
                                $hr_faltante = " ";
                                $horas_trabalhadas = " ";
                                break;
                            } 
                        } 
                    }
                }

                if($tipo_ponto_pk == 1){
                    //Função que calcula as horas trabalhadas
                    $horas_trabalhadas = $this->calcularHrsTrabalhadas($ponto_ini_expediente, $ponto_term_expediente, $ponto_ini_intervalo, $ponto_term_intervalo, $hr_retorno_intervalo, $hr_saida_intervalo, $ic_intrajornada, '');
                                        
                    //Calcula HE e HF
                    if($hr_expediente!="" && $horas_trabalhadas > "06:00"){
                        if($horas_trabalhadas < $hr_expediente){
                            $queryfaltantes = $this->retornarDifHoraFaltantes($hr_expediente,$horas_trabalhadas); 
                            $hr_faltante = str_replace("-","",$queryfaltantes[0]['dif']);                            
                        }else if ($horas_trabalhadas > $hr_expediente){
                            $queryexcedente = $this->retornarDifHora($hr_expediente,$horas_trabalhadas); 
                            $hr_excedentes = $queryexcedente[0]['dif'];
                        }else {
                            $hr_excedentes = " ";
                            $hr_faltante = " ";
                            $horas_trabalhadas = " ";
                        }                
                    }else{
                        $hr_excedentes = " ";
                        $hr_faltante = " ";
                        $horas_trabalhadas = " ";
                    } 
                }else{
                    $hr_excedentes = " ";
                    $hr_faltante = " ";
                    $horas_trabalhadas = " ";
                }
                        
                
                    
                //array de pontos por dia
                $arrPontos[] = array(
                    "tipo_ponto_pk" => $tipo_ponto_pk,
                    "ponto_ini_expediente" => $ponto_ini_expediente,
                    "ponto_ini_intervalo" => $ponto_ini_intervalo,
                    "ponto_term_intervalo" => $ponto_term_intervalo,
                    "ponto_term_expediente" => $ponto_term_expediente,
                    "horas_trabalhadas" => $horas_trabalhadas,
                    "hr_excedentes" => $hr_excedentes,
                    "hr_faltante" => $hr_faltante,
                    "registro_folha_pk" => $registrosFolhaPk,
                    "obs" => $obs
                );
            

            //array de dias por período 
            $arrDias[] = array(
                "dt_hora_ponto" => $dt_escala." 00:00:00",
                "pontos_dia"=>$arrPontos
            );
        }
        
        return $arrDias;
    }

    public function retornarDifHora($hr_1,$hr_2){
        //Retorna a diferença entre dois horários 
        $sql ="";
        //$sql.="SELECT TIME_FORMAT(TIMEDIFF('$hr_2','$hr_1'),'%H:%i')dif";
        $sql.="SELECT CASE WHEN '".$hr_2."' >= '".$hr_1."' THEN TIMEDIFF('".$hr_2."', '".$hr_1."')
                ELSE ADDTIME(TIMEDIFF('24:00:00', '".$hr_1."'), '".$hr_2."')
            END AS dif";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);  

        return $query;
    }

    public function retornarDifHoraFaltantes($hr_1,$hr_2){
        //Retorna a diferença entre dois horários 
        $sql ="";
        //$sql.="SELECT TIME_FORMAT(TIMEDIFF('$hr_2','$hr_1'),'%H:%i')dif";
        $sql.="SELECT CASE WHEN '".$hr_2."' <= '".$hr_1."' THEN TIMEDIFF('".$hr_2."', '".$hr_1."')
                ELSE ADDTIME(TIMEDIFF('24:00:00', '".$hr_1."'), '".$hr_2."')
            END AS dif";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);   

        return $query;
    }

    public function calcularHrsTrabalhadas($hr_ini_expediente, $hr_fim_expediente, $hr_ini_intervalo, $hr_fim_intervalo, $hr_termino_intervalo, $hr_ini_intervalo_tb, $ic_intrajornada){

        //Verificar intrajornada para calcular o horário de almoço
        if($ic_intrajornada != 1){
            //Verifica se o campo está preenchido, se não tiver ele calcula com base no informado na escala do colaborador 
            if($hr_ini_intervalo!="" and $hr_fim_intervalo!=""){
                $hr_intervalo = $this->retornarDifHora($hr_ini_intervalo, $hr_fim_intervalo);
                $hr_intervalo = $hr_intervalo[0]['dif'];
            }else{
                $hr_intervalo = $this->retornarDifHora($hr_ini_intervalo_tb, $hr_termino_intervalo);
                $hr_intervalo = $hr_intervalo[0]['dif'];
            }

        }else{
            $hr_intervalo = '01:00';
        }

        $hr_expediente = $this->retornarDifHora($hr_ini_expediente, $hr_fim_expediente);
        $hr_expediente = $hr_expediente[0]['dif'];

        $hr_trabalhadas = $this->retornarDifHora($hr_intervalo, $hr_expediente);
        $hr_trabalhadas = $hr_trabalhadas[0]['dif'];

        return $hr_trabalhadas;
    }

    public function listarPreenchimentoAutomatico(){
        
        $ic_preencher_folha = 2;
        //Retorna se o preenchimento automatico está ativo para a conta ou não  
        if($_SESSION['session_user']['par7'] != ''){
            
            $sql="";
            $sql.="SELECT ic_preencher_folha";
            $sql.="  FROM contas";
            $sql.=" WHERE pk=".$_SESSION['session_user']['par7'];

            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $ic_preencher_folha = $query[0]['ic_preencher_folha'];

        }
        return $ic_preencher_folha;
        
    }

    public function listarPontoFolhaPK($ponto_folha_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

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
        $sql.="select pfr.ponto_folha_pk";
        $sql.="       ,pfr.pk";
        $sql.="       ,p.dt_periodo_ini";
        $sql.="       ,p.dt_periodo_fim";
        $sql.="       ,pfr.colaborador_pk";
        $sql.="       ,date_format(pfr.dt_cadastro,'%d/%m/%Y') dt_cadastro ";
        $sql.="       ,date_format(pfr.dt_ult_atualizacao,'%d/%m/%Y') dt_ult_atualizacao";
        $sql.="       ,c.ds_colaborador";
        $sql.="       ,pfr.ic_status ic_status_pk";
        $sql.="       ,pfr.ic_status, case when pfr.ic_status = 1 Then 'Finalizada' Else 'Não Finalizada' end ic_status";
        $sql.="  from ponto_folha_colaborador pfr ";
        $sql.="  inner join ponto_folha p on pfr.ponto_folha_pk = p.pk";
        $sql.="  inner join colaboradores c on pfr.colaborador_pk = c.pk";

        $sql.=" WHERE pfr.ponto_folha_pk=".$ponto_folha_pk;
        
        $sql.=" order by pfr.pk ";
        

        $stmt = $this->pdo->prepare( $sql.$lengthSql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmtCount = $this->pdo->prepare( $sql );
        $stmtCount->execute();
        $rowsCount = $stmtCount->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;
        $retorno->iTotalDisplayRecords = count($rowsCount);
        $retorno->iTotalRecords = count($rowsCount);

        echo json_encode($retorno);
        exit(0);
        
    }

    public function listarFolhasRegistros($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.=" SELECT pf.pk,";
        $sql.="    c.ds_conta,";
        $sql.="    l.ds_lead,";
        $sql.="    l.pk leads_pk,";
        $sql.="    date_format(pf.dt_periodo_ini, '%d/%m/%Y') dt_periodo_ini,";
        $sql.="    date_format(pf.dt_periodo_fim, '%d/%m/%Y') dt_periodo_fim,";
        $sql.="    pf.obs";
        $sql.=" FROM ponto_folha pf";
        $sql.="  LEFT JOIN contas c ON pf.empresas_pk = c.pk";
        $sql.="  INNER JOIN leads l ON pf.leads_pk = l.pk";
        if(!empty($pk)){
            $sql.=" WHERE pf.pk=".$pk;
        }      
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $query;

        return $retorno;
        
    }
    public function listarFolhaPorPeridoColaborador($dt_periodo_ini, $dt_periodo_fim, $colaborador_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pfr.ponto_folha_pk";
        $sql.="       ,pfr.pk";
        $sql.="       ,p.dt_periodo_ini";
        $sql.="       ,p.dt_periodo_fim";
        $sql.="       ,p.leads_pk";
        $sql.="       ,pfr.colaborador_pk";
        $sql.="  from ponto_folha_colaborador pfr ";
        $sql.="  inner join ponto_folha p on pfr.ponto_folha_pk = p.pk";
        $sql.="  inner join colaboradores c on pfr.colaborador_pk = c.pk";

        $sql.=" WHERE c.pk=".$colaborador_pk;
        $sql.=" and p.dt_periodo_ini >='".Util::DataYMD($dt_periodo_ini)."'";
        $sql.=" and p.dt_periodo_fim <='".Util::DataYMD($dt_periodo_fim)."'";
        
        $sql.=" order by p.dt_periodo_ini ";
        
  
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $query;

        return $retorno;
        
    }

    public function listarFolhaRegistrosAgrupadoData($ponto_folha_pk,$colaborador_pk){
        
        $sql ="";
        $sql.="SELECT pf.pk ponto_folha_pk,";
        $sql.="        pfr.pk ponto_folha_registro_pk,";
        $sql.="        pfr.colaborador_pk,";
        $sql.="        date_format(pfr.dt_hora_ponto, '%d/%m/%Y') dt_ponto,";
        $sql.="        date_format(pfr.dt_hora_ponto, '%Y-%m-%d') dt_ponto_usa,";
        $sql.="        TIME_FORMAT(pfr.hr_ini_expediente, '%H:%i') hr_ini_expediente,";
        $sql.="        TIME_FORMAT(pfr.hr_fim_expediente, '%H:%i') hr_fim_expediente,";
        $sql.="        TIME_FORMAT(pfr.hr_ini_intervalo, '%H:%i') hr_ini_intervalo,";
        $sql.="        TIME_FORMAT(pfr.hr_fim_intervalo, '%H:%i') hr_fim_intervalo,";
        $sql.="        TIME_FORMAT(pfr.hr_trabalhadas, '%H:%i') hr_trabalhadas,";
        $sql.="        TIME_FORMAT(pfr.hr_excedente, '%H:%i') hr_excedentes,";
        $sql.="        TIME_FORMAT(pfr.hr_faltantes, '%H:%i') hr_faltantes,";
        $sql.="        TIME_FORMAT(pfr.hr_extra50, '%H:%i') hr_extra50,";
        $sql.="        TIME_FORMAT(pfr.hr_extra100, '%H:%i') hr_extra100,";
        $sql.="        TIME_FORMAT(pfr.hr_adicional_noturno, '%H:%i') hr_adicional_noturno,";
        $sql.="        TIME_FORMAT(pfr.hr_saldo, '%H:%i:%s') hr_saldo,";
        $sql.="        pfr.tipo_ponto_pk,"; 
        $sql.="         CASE 
                            WHEN DAYOFWEEK(pfr.dt_hora_ponto) = 1 THEN 'Dom'
                            WHEN DAYOFWEEK(pfr.dt_hora_ponto) = 2 THEN 'Seg'
                            WHEN DAYOFWEEK(pfr.dt_hora_ponto) = 3 THEN 'Ter'
                            WHEN DAYOFWEEK(pfr.dt_hora_ponto) = 4 THEN 'Qua'
                            WHEN DAYOFWEEK(pfr.dt_hora_ponto) = 5 THEN 'Qui'
                            WHEN DAYOFWEEK(pfr.dt_hora_ponto) = 6 THEN 'Sex'
                            WHEN DAYOFWEEK(pfr.dt_hora_ponto) = 7 THEN 'Sáb'
                        END AS dia_da_semana,";
        $sql.="        pfr.ic_status,"; 
        $sql.="        pfr.obs"; 
        $sql.=" FROM ponto_folha pf";
        $sql.="      INNER JOIN ponto_folha_registros pfr ON pf.pk = pfr.ponto_folha_pk";
        $sql.="      INNER JOIN ponto_folha_colaborador pc on pfr.colaborador_pk = pc.colaborador_pk";
        $sql.=" WHERE pf.pk = ".$ponto_folha_pk;        
        $sql.=" AND pc.colaborador_pk =".$colaborador_pk;
        $sql.=" group by date_format(pfr.dt_hora_ponto, '%d/%m/%Y')";
        //$sql.=" ORDER BY pfr.dt_hora_ponto";
   
        

    
       
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $query;
    }

    public function listarRegistros($pk, $leads_pk, $colaborador_pk){

        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio

            $sql ="";
            $sql.=" SELECT pf.pk,";
            $sql.="    c.ds_razao_social ds_empresa,";
            $sql.="    c.ds_endereco,";
            $sql.="    c.ds_numero,";
            $sql.="    c.ds_cpf_cnpj ds_cnpj_conta,";
            $sql.="    l.ds_lead ds_posto_trabalho,";
            $sql.="    date_format(pf.dt_periodo_ini, '%d/%m/%Y') dt_periodo_ini,";
            $sql.="    date_format(pf.dt_periodo_fim, '%d/%m/%Y') dt_periodo_fim,";
            $sql.="    date_format(pf.dt_cadastro, '%d/%m/%Y') dt_cadastro,";
            $sql.="    col.ds_colaborador,";
            $sql.="    col.ds_cpf,";
            $sql.="    ps.ds_produto_servico ds_cargo,";
            $sql.="    a.pk agenda_colaborador_pk,";
            $sql.="    a.n_qtde_dias_semana,";
            $sql.="    t.ds_turno,";
            $sql.="    t.pk turnos_pk,";
            $sql.="    a.hr_inicio_expediente,";
            $sql.="    a.hr_termino_expediente,";
            $sql.="    a.hr_saida_intervalo,";
            $sql.="    a.hr_retorno_intervalo, ";      
            $sql.="    pfr.hr_extra100,";        
            $sql.="    pfr.hr_extra50,";        
            $sql.="    pfr.hr_adicional_noturno,";        
            $sql.="    pfc.ponto_folha_pk, ";  
            $sql.="    pfc.colaborador_pk,";   
            $sql.="    date_format(col.dt_admissao, '%d/%m/%Y') dt_admissao,";        
            $sql.="    pfc.ic_status ";  
            
            $sql.=" FROM ponto_folha pf";
            $sql.="  LEFT JOIN contas c ON pf.empresas_pk = c.pk";
            $sql.="  INNER JOIN leads l ON pf.leads_pk = l.pk";
            
            $sql.="  INNER JOIN ponto_folha_colaborador pfc ON pf.pk = pfc.ponto_folha_pk";  
            $sql.="  INNER JOIN ponto_folha_registros pfr ON pfc.colaborador_pk = pfr.colaborador_pk";  
            $sql.="  INNER JOIN agenda_colaborador_padrao a ON pfc.colaborador_pk = a.colaboradores_pk";
            $sql.="  LEFT JOIN turnos t ON a.turnos_pk = t.pk";
            $sql.="  INNER JOIN colaboradores col ON pfc.colaborador_pk = col.pk";
            $sql.="  INNER JOIN colaboradores_produtos_servicos cps  ON col.pk = cps.colaboradores_pk";
            $sql.="  INNER JOIN produtos_servicos ps ON cps.produtos_servicos_pk = ps.pk";
            
            if(!empty($pk)){
                $sql.=" WHERE pf.pk=".$pk;
            }

            if(!empty($colaborador_pk) || $colaborador_pk != 'null'){
                $sql.=" AND pfc.colaborador_pk=".$colaborador_pk;
            }
           
            
            if(!empty($leads_pk)){
                $sql.=" AND pf.leads_pk=".$leads_pk;
                $sql.=" AND a.leads_pk=".$leads_pk;
            }
            //$sql.=" AND a.dt_cancelamento is null";

            $sql.=" GROUP BY pf.pk";

        
        
            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if(count($query) > 0){
                //Total Horas Trabalhadas
                $v_total_ht = $this->TotalHrTrabalhada($pk,$colaborador_pk);     
                            
                //Total Horas Excedentes
                $v_total_he = $this->TotalHrExcedentes($pk,$colaborador_pk);          
                            
                //Total Horas Excedentes
                $v_total_hf = $this->TotalHrFaltantes($pk,$colaborador_pk);      
                
                //Total Hora extra 50%
                $v_total_he50 = $this->TotalHrExtra50($pk,$colaborador_pk);  

                //Total Hora extra 100%
                $v_total_he100 = $this->TotalHrExtra100($pk,$colaborador_pk);      
                
                //Total Hora Adicional Noturno
                $v_total_hadn = $this->TotalHrAdn($pk,$colaborador_pk);       
                            
                $queryTempoExpediente  = $this->retornarDifHora($query[0]['hr_inicio_expediente'],$query[0]['hr_termino_expediente']);
                $expediente = "";
                $expediente = $queryTempoExpediente[0]['dif']; 

                $queryTempoIntervalo  = $this->retornarDifHora($query[0]['hr_saida_intervalo'],$query[0]['hr_retorno_intervalo']);
                $intervalo_diario = "";
                $intervalo_diario = $queryTempoIntervalo[0]['dif']; 

                $queryTempo  = $this->retornarDifHora($intervalo_diario,$expediente);
                $expediente_diario = "";
                $expediente_diario = $queryTempo[0]['dif']; 
                
                for($i = 0; $i < count($query); $i++){                
                    $query0 = $this->listarFolhaRegistrosAgrupadoData($query[$i]['pk'],$query[$i]['colaborador_pk']);
            
                    $DadosFolhaRegistros[] = "";
                    for($j = 0; $j < count($query0); $j++){ 
                        

                        $dt_registro_ponto = $query0[$j]['dt_ponto'];
                        $dt_registro_ponto_usa = $query0[$j]['dt_ponto_usa'];


                        //CONSULTAR APONTAMENTOS
                        $arrApontamento = $this->listarDadosApontamento($dt_registro_ponto_usa, $query[$i]['colaborador_pk'], $query[$i]['agenda_colaborador_pk'],0);
                        
                        $situacao = "";
                        $tipo_ponto_pk = "";
                        $arrDadosApontamento = $arrApontamento[0]['arrApontamento'];
                        for($a=0;$a<count($arrDadosApontamento);$a++){
                            $tipo = (int)$arrApontamento[0]['tipo_apontamento_pk'];
                            $tipoComp = (int)$arrDadosApontamento[$a]['tipo_apontamento_dados_pk'];

                            if($tipo == $tipoComp){
                                switch($tipo){
                                    case 1:
                                        $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                        if($tipo_ponto_pk==33){
                                            $situacao = "Declaração de horas abonar";
                                        }
                                        else if($tipo_ponto_pk==1){
                                            $situacao = "Ponto/Expediente";
                                        }
                                        else if($tipo_ponto_pk==37){
                                            $situacao = "Atestado de horas";
                                        }
                                        else if($tipo_ponto_pk==36){
                                            $situacao = "Audiência ";
                                        }
                                        break;
                                    case 2:
                                        $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
        
                                        if($tipo_ponto_pk==2){
                                            $situacao = "Falta";
                                        } else if($tipo_ponto_pk==11){
                                            $situacao = "Abonada";
                                        }else if($tipo_ponto_pk==16){
                                            $situacao = "Atestado";
                                            $ponto_ini_expediente = "";
                                            $ponto_term_expediente = "";
                                            $ponto_ini_intervalo = "";
                                            $ponto_term_intervalo = "";
                                        }else if($tipo_ponto_pk==18){
                                            $situacao = "Declaração da defesa civil";
                                        }
                                        else if($tipo_ponto_pk==28){
                                            $situacao = "Apoio Operacional ";
                                        }
                                        else if($tipo_ponto_pk==29){
                                            $situacao = "Atestado por acompanhar filho ate 5 anos";
                                        }
                                        else if($tipo_ponto_pk==30){
                                            $situacao = "Atestado por serviço Justiça Eleitoral";
                                        }
                                        else if($tipo_ponto_pk==31){
                                            $situacao = "Doação de sangue";
                                        }
                                        else if($tipo_ponto_pk==32){
                                            $situacao = "Atraso";
                                        }
                                        else if($tipo_ponto_pk==33){
                                            $situacao = "Declaração de horas abonar";
                                        }
                                        else if($tipo_ponto_pk==34){
                                            $situacao = "Sem Justificativa";
                                        }
                                        else if($tipo_ponto_pk==35){
                                            $situacao = "Reciclagem";
                                        }
                                        else if($tipo_ponto_pk==36){
                                            $situacao = "Audiência ";
                                        }
                                        
                                        
                                        break;
                                    case 3:
                                        $motivo_folga_pk = $arrDadosApontamento[$a]['motivo_folga_pk'];
                                        $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                        
                                
                                        if($motivo_folga_pk == '1'){
                                            $situacao = "Folga Trabalhada";
                                        }else if($motivo_folga_pk == '2'){
                                            $situacao = "Escala Errada";
                                        }else if($motivo_folga_pk == '3'){
                                            $situacao = "Convocação Normal";
                                        }
                                        if($tipo_ponto_pk==3){
                                            $situacao = "Folga";
                                        } else if($tipo_ponto_pk==20){
                                            $situacao = "Folga compensatória";
                                        }else if($tipo_ponto_pk==21){
                                            $situacao = "Folga de feriado";
                                        }else if($tipo_ponto_pk==25){
                                            $situacao = "Troca Folga";
                                        }
                                        else if($tipo_ponto_pk==26){
                                            $situacao = "Folga trabalhada";
                                        }
                                        else if($tipo_ponto_pk==27){
                                            $situacao = "Escala Errada";
                                        }
                                        break;
                                    case 5:
                                        $motivo_afastamento_pk = $arrDadosApontamento[$a]['motivo_afastamento_pk'];
                                        $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                    
                                        if($motivo_afastamento_pk == 1){
                                            $situacao = "Motivos Médicos";
                                        }else if($motivo_afastamento_pk == 2){
                                            $situacao = "Invalides";
                                        }                             
                                        break;
                                    case 6:
                                        $ic_apontamento = 1;
                                        $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                        $situacao = "Férias";
                                        break;
                                    case 8:
                                        $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                        if($tipo_ponto_pk==8){
                                            $situacao = "Disciplina";
                                        } else if($tipo_ponto_pk==17){
                                            $situacao = "Advertencia";
                                        }else if($tipo_ponto_pk==19){
                                            $situacao = "Demissão";
                                        }else if($tipo_ponto_pk==22){
                                            $situacao = "Justa causa";
                                        }else if($tipo_ponto_pk==23){
                                            $situacao = "Recisão indireta";
                                        }else if($tipo_ponto_pk==24){
                                            $situacao = "Suspensão";
                                        }
                                        break;
                                } 
                            }
                        }
                    





                        $arrPontos = $this->listarDadosPonto($dt_registro_ponto_usa, $query[$i]['colaborador_pk'],$query[$i]['agenda_colaborador_pk']);


                        

                        $hr_ini_expediente= $query0[$j]['hr_ini_expediente'];
                        $hr_ini_intervalo = $query0[$j]['hr_ini_intervalo'];
                        $hr_fim_intervalo = $query0[$j]['hr_fim_intervalo'];
                        $hr_fim_expediente = $query0[$j]['hr_fim_expediente'];
                        /*if($query0[$j]['tipo_ponto_pk']==1){
                            if($query[$i]['turnos_pk'] == 3 && isset($query0[$j+1])) {
                                $hr_ini_expediente= $query0[$j]['hr_ini_expediente'];
                                $hr_ini_intervalo = $query0[$j+1]['hr_ini_intervalo'];
                                $hr_fim_intervalo = $query0[$j+1]['hr_fim_intervalo'];
                                $hr_fim_expediente = $query0[$j+1]['hr_fim_expediente'];
                                
                            }
                        }*/
                        

                        $DadosFolhaRegistros[$j] = array(
                            "arrApontamento"=>$arrApontamento,
                            "arrPontos"=>$arrPontos,
                            "ponto_folha_pk"=>$query0[$j]['ponto_folha_pk'],
                            "ponto_folha_registro_pk"=>$query0[$j]['ponto_folha_registro_pk'],
                            "colaborador_pk"=>$query0[$j]['colaborador_pk'],
                            "dt_registro_ponto"=>$query0[$j]['dt_ponto'],
                            "tipo_ponto_pk"=>$query0[$j]['tipo_ponto_pk'],
                            "hr_ini_expediente"=>$hr_ini_expediente,
                            "hr_ini_intervalo"=>$hr_ini_intervalo,
                            "hr_fim_intervalo"=>$hr_fim_intervalo,
                            "hr_fim_expediente"=>$hr_fim_expediente,
                            "hr_trabalhadas"=>$query0[$j]['hr_trabalhadas'],
                            "hr_excedentes"=>$query0[$j]['hr_excedentes'],
                            "hr_faltantes"=>$query0[$j]['hr_faltantes'],
                            "ic_status"=>$query0[$j]['ic_status'],
                            "hr_extra50"=>$query0[$j]['hr_extra50'],
                            "hr_extra100"=>$query0[$j]['hr_extra100'],
                            "hr_adicional_noturno"=>$query0[$j]['hr_adicional_noturno'],  
                            "dia_da_semana"=>$query0[$j]['dia_da_semana'],  
                            "tipo_apontamento_pk"=>$tipo_ponto_pk,  
                            "situacao"=>$situacao,  
                            "obs"=>$query0[$j]['obs']
                        );

                    }

                    $result[] = array(
                        "pk" => $query[$i]["pk"],
                        "ds_periodo"=>$query[$i]['dt_periodo_ini']." a ".$query[$i]['dt_periodo_fim'],                    
                        "ds_empresa"=>$query[$i]['ds_empresa'],
                        "ds_endereco"=>$query[$i]['ds_endereco']." ,".$query[$i]['ds_numero'],
                        "ds_cnpj"=>$query[$i]['ds_cnpj_conta'],
                        "agenda_colaborador_pk"=>$query[$i]['agenda_colaborador_pk'],
                        "ds_colaborador"=>$query[$i]['ds_colaborador'],
                        "dt_admissao"=>$query[$i]['dt_admissao'],  
                        "ds_cpf"=>$query[$i]['ds_cpf'],
                        "ds_cargo"=>$query[$i]['ds_cargo'],
                        "ds_posto_trabalho"=>$query[$i]['ds_posto_trabalho'],
                        "ds_escala"=>$query[$i]['n_qtde_dias_semana'],
                        "ds_turno"=>$query[$i]['ds_turno'],
                        "turnos_pk"=>$query[$i]['turnos_pk'],
                        "ic_folha_finalizada"=>$query[$i]['ic_status'],
                        "ds_hr_expediente"=>$query[$i]['hr_inicio_expediente']." a ".$query[$i]['hr_termino_expediente'],
                        "registrosfolha"=>$DadosFolhaRegistros,
                        "total_ht"=> $v_total_ht,
                        "total_he"=> $v_total_he,
                        "total_hf"=> $v_total_hf,
                        "total_he50"=> $v_total_he50,
                        "total_he100"=> $v_total_he100,
                        "total_hadn"=> $v_total_hadn,
                        "expediente_diario"=> $expediente_diario
                    );
                }
            }
            $retorno->data = $result;
            $retorno->status = true;
            $retorno->message = 'Dados carregados com sucesso';
            

            return $retorno;
        }
        catch(Throwable $e){
            print_r($e->getMessage());
            die();
        }
        
        
    }

    

    public function TotalHrTrabalhada($pk,$colaborador_pk){
        $sql ="";


        $sql.="SELECT TIME_FORMAT(
            SEC_TO_TIME(SUM(TIME_TO_SEC(hr_trabalhadas))),
            '%H:%i'
        ) AS total_hr_trabalhadas";
        $sql.=" FROM (";
        $sql.=" SELECT DISTINCT DATE(dt_hora_ponto) AS data_unica, hr_trabalhadas";
        $sql.=" FROM ponto_folha_registros";
        $sql.=" WHERE ponto_folha_pk =".$pk;
        $sql.="  AND colaborador_pk =" .$colaborador_pk;
        $sql.=" ) AS registros_unicos";
      
        
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $query[0]['total_hr_trabalhadas'];
    }

    public function TotalHrExcedentes($pk,$colaborador_pk){
        $sql ="";
       // $sql.="SELECT TIME_FORMAT(sum(hr_excedente), '%H:%i') total_hr_excedente";

       $sql.="SELECT TIME_FORMAT(
            SEC_TO_TIME(ROUND(SUM(TIME_TO_SEC(hr_excedente)))),
            '%H:%i'
        ) AS total_hr_excedente";
        $sql.=" FROM (";
        $sql.=" SELECT DISTINCT DATE(dt_hora_ponto) AS data_unica, hr_excedente";
        $sql.=" FROM ponto_folha_registros";
        $sql.=" WHERE ponto_folha_pk =".$pk;
        $sql.="  AND colaborador_pk =" .$colaborador_pk;
        $sql.=" ) AS registros_unicos";
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $query[0]['total_hr_excedente'];
    }
    public function horaExcedenteMesAnterior($colaborador_pk,$leads_pk,$ic_mes,$ic_ano){

        // Primeiro dia do mês anterior
        $primeiroDiaMesAnterior = date("Y-m-d", strtotime("first day of -1 month", strtotime("$ic_ano-$ic_mes-01")));

        // Último dia do mês anterior
        $ultimoDiaMesAnterior = date("Y-m-t", strtotime("last day of -1 month", strtotime("$ic_ano-$ic_mes-01")));



        $sql="";
        $sql.="SELECT TIME_FORMAT(
            SEC_TO_TIME(ROUND(SUM(TIME_TO_SEC(hr_excedente)))),
            '%H:%i'
        ) AS total_hr_excedente";
        $sql.=" FROM (";
        $sql.=" SELECT DISTINCT DATE(pfr.dt_hora_ponto) AS data_unica, pfr.hr_excedente";
        $sql.=" FROM ponto_folha_registros pfr"; 
        $sql.="  INNER JOIN ponto_folha pf ON pf.pk = pfr.ponto_folha_pk";  
        $sql.=" WHERE  pfr.colaborador_pk =".$colaborador_pk;
        $sql.=" and pf.leads_pk =".$leads_pk;
        $sql.=" and pfr.dt_hora_ponto BETWEEN '".$primeiroDiaMesAnterior." 00:00:00' and '".$ultimoDiaMesAnterior." 23:59:59'";
        $sql.=" ) AS registros_unicos";
       
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $query[0]['total_hr_excedente'];
    }

    
    public function TotalHrFaltantes($pk,$colaborador_pk){

        $sql ="";
        $sql.="SELECT TIME_FORMAT(
            SEC_TO_TIME(ROUND(SUM(TIME_TO_SEC(hr_faltantes)))),
            '%H:%i'
        ) AS total_hr_faltantes";
        $sql.=" FROM (";
        $sql.=" SELECT DISTINCT DATE(dt_hora_ponto) AS data_unica, hr_faltantes";
        $sql.=" FROM ponto_folha_registros";
        $sql.=" WHERE ponto_folha_pk =".$pk;
        $sql.="  AND colaborador_pk =" .$colaborador_pk;
        $sql.=" ) AS registros_unicos";
        
    
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $query[0]['total_hr_faltantes'];
    }   
    
    public function TotalHrExtra50($pk,$colaborador_pk){


        $sql ="";
        $sql.="SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(hr_extra50))), '%H:%i') AS total_hr_extra50";
        $sql.=" FROM (";
        $sql.=" SELECT DISTINCT DATE(dt_hora_ponto) AS data_unica, hr_extra50";
        $sql.=" FROM ponto_folha_registros";
        $sql.=" WHERE ponto_folha_pk =".$pk;
        $sql.="  AND colaborador_pk =" .$colaborador_pk;
        $sql.=" ) AS registros_unicos";
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $query[0]['total_hr_extra50'];
    } 
    
    public function TotalHrExtra100($pk,$colaborador_pk){

        $sql ="";
        $sql.="SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(hr_extra100))), '%H:%i') AS total_hr_extra100";
        $sql.=" FROM (";
        $sql.=" SELECT DISTINCT DATE(dt_hora_ponto) AS data_unica, hr_extra100";
        $sql.=" FROM ponto_folha_registros";
        $sql.=" WHERE ponto_folha_pk =".$pk;
        $sql.="  AND colaborador_pk =" .$colaborador_pk;
        $sql.=" ) AS registros_unicos";
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $query[0]['total_hr_extra100'];
    } 
    
    public function TotalHrAdn($pk,$colaborador_pk){

        $sql ="";
        $sql.="SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(hr_adicional_noturno))), '%H:%i') AS total_hr_adicional_noturno";
        $sql.=" FROM (";
        $sql.=" SELECT DISTINCT DATE(dt_hora_ponto) AS data_unica, hr_adicional_noturno";
        $sql.=" FROM ponto_folha_registros";
        $sql.=" WHERE ponto_folha_pk =".$pk;
        $sql.="  AND colaborador_pk =" .$colaborador_pk;
        $sql.=" ) AS registros_unicos";
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $query[0]['total_hr_adicional_noturno'];
    } 

    public function listarDadosImpressao($leads_pk, $colaborador_pk, $ponto_folha_pk){
        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio

            $sql ="";
            $sql.=" SELECT DISTINCT pf.pk,";
            $sql.="        c.ds_razao_social ds_empresa,";
            $sql.="        c.ds_endereco,";
            $sql.="        c.ds_numero,";
            $sql.="        c.ds_cpf_cnpj ds_cnpj_conta,";
            $sql.="        l.ds_lead ds_posto_trabalho,";
            $sql.="        date_format(pf.dt_periodo_ini, '%d/%m/%Y') dt_periodo_ini,";
            $sql.="        date_format(pf.dt_periodo_fim, '%d/%m/%Y') dt_periodo_fim,";
            $sql.="        date_format(pf.dt_cadastro, '%d/%m/%Y') dt_cadastro,";
            $sql.="        col.ds_colaborador,";
            $sql.="        col.ds_cpf,";
            $sql.="        ps.ds_produto_servico ds_cargo,";
            $sql.="        a.n_qtde_dias_semana,";
            $sql.="        a.n_qtde_dias_semana,";
            $sql.="        t.ds_turno,";
            $sql.="        a.hr_inicio_expediente,";
            $sql.="        a.pk agenda_colaborador_pk,";
            $sql.="        a.hr_termino_expediente,";
            $sql.="        a.hr_saida_intervalo,";
            $sql.="        a.hr_retorno_intervalo, ";
            $sql.="        a.turnos_pk,";        
            $sql.="        pfc.ponto_folha_pk, ";  
            $sql.="    date_format(col.dt_admissao, '%d/%m/%Y') dt_admissao,";  
            $sql.="        pfc.colaborador_pk ";    
            $sql.="   FROM ponto_folha pf";
            $sql.="  LEFT JOIN contas c ON pf.empresas_pk = c.pk";
            $sql.="  INNER JOIN leads l ON pf.leads_pk = l.pk";
            $sql.="  INNER JOIN ponto_folha_colaborador pfc ON pf.pk = pfc.ponto_folha_pk";  
            $sql.="  INNER JOIN ponto_folha_registros pfr ON pf.pk = pfr.ponto_folha_pk";  
            $sql.="  INNER JOIN agenda_colaborador_padrao a ON pfc.colaborador_pk = a.colaboradores_pk";
            $sql.="  LEFT JOIN turnos t ON a.turnos_pk = t.pk";
            $sql.="  INNER JOIN colaboradores col ON pfc.colaborador_pk = col.pk";
            $sql.="  INNER JOIN colaboradores_produtos_servicos cps  ON col.pk = cps.colaboradores_pk";
            $sql.="  INNER JOIN produtos_servicos ps ON cps.produtos_servicos_pk = ps.pk";
            $sql.=" WHERE pfc.ponto_folha_pk=".$ponto_folha_pk;
            
            if(!empty($leads_pk)){
                $sql.=" AND pf.leads_pk=".$leads_pk;
                $sql.=" AND a.leads_pk =".$leads_pk;
                
            }

            if($colaborador_pk != 'null'){
                $sql.=" AND pfc.colaborador_pk=".$colaborador_pk;
            }
            

            //$sql.=" AND a.dt_cancelamento is null";
          
            
            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            if(count($query) > 0){

                for($i = 0; $i < count($query); $i++){ 
                    $DadosFolhaRegistros = "";

                    //Total Horas Trabalhadas
                    $queryHrTrabalhadas = $this->TotalHrTrabalhada($ponto_folha_pk,$query[$i]['colaborador_pk']);                       
                    
                    $v_total_ht = "";
                    $v_total_ht = $queryHrTrabalhadas;
                                
                    //Total Horas Excedentes
                    $queryHrExcedentes = $this->TotalHrExcedentes($ponto_folha_pk,$query[$i]['colaborador_pk']);                       
                    $v_total_he = "";
                    $v_total_he = $queryHrExcedentes;
                                
                    //Total Horas Excedentes
                    $queryHrFaltantes = $this->TotalHrFaltantes($ponto_folha_pk,$query[$i]['colaborador_pk']);                       
                    $v_total_hf = "";
                    $v_total_hf = $queryHrFaltantes;
                    
                    //Total Hora extra 50%
                    $queryHrExtra50 = $this->TotalHrExtra50($ponto_folha_pk,$query[$i]['colaborador_pk']);                       
                    $v_total_he50 = "";
                    $v_total_he50 = $queryHrExtra50;

                    //Total Hora extra 100%
                    $queryHrExtra100 = $this->TotalHrExtra100($ponto_folha_pk,$query[$i]['colaborador_pk']);                       
                    $v_total_he100 = "";
                    $v_total_he100 = $queryHrExtra100;  
                    
                    //Total Hora Adicional Noturno
                    $queryHrAdn = $this->TotalHrAdn($ponto_folha_pk,$query[$i]['colaborador_pk']);                       
                    $v_total_hadn = "";
                    $v_total_hadn = $queryHrExtra100;  
                                
                    $queryTempoExpediente  = $this->retornarDifHora($query[$i]['hr_inicio_expediente'],$query[$i]['hr_termino_expediente']);
                    $expediente_diario = "";
                    $expediente_diario = $queryTempoExpediente[$i]['dif']; 

                    $query0 = $this->listarFolhaRegistrosAgrupadoData($query[$i]['ponto_folha_pk'],$query[$i]['colaborador_pk']);
                    $DadosFolhaRegistros=array();
                    for($j = 0; $j < count($query0); $j++){ 
                        $dt_registro_ponto_usa = $query0[$j]['dt_ponto_usa'];
                        $arrApontamento = $this->listarDadosApontamento($dt_registro_ponto_usa, $query[$i]['colaborador_pk'], $query[$i]['agenda_colaborador_pk'],0);                       
                         $situacao = "";
                        $tipo_ponto_pk = "";
                        $arrDadosApontamento = $arrApontamento[0]['arrApontamento'];
                        for($a=0;$a<count($arrDadosApontamento);$a++){
                            $tipo = (int)$arrApontamento[0]['tipo_apontamento_pk'];
                            $tipoComp = (int)$arrDadosApontamento[$a]['tipo_apontamento_dados_pk'];

                            if($tipo == $tipoComp){
                                switch($tipo){
                                    case 2:
                                        $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
        
                                        if($tipo_ponto_pk==2){
                                            $situacao = "Falta";
                                        } else if($tipo_ponto_pk==11){
                                            $situacao = "Abonada";
                                        }else if($tipo_ponto_pk==16){
                                            $situacao = "Atestado";
                                            $ponto_ini_expediente = "";
                                            $ponto_term_expediente = "";
                                            $ponto_ini_intervalo = "";
                                            $ponto_term_intervalo = "";
                                        }else if($tipo_ponto_pk==18){
                                            $situacao = "Declaração da defesa civil";
                                        }
                                        else if($tipo_ponto_pk==28){
                                            $situacao = "Apoio Operacional ";
                                        }
                                        else if($tipo_ponto_pk==29){
                                            $situacao = "Atestado por acompanhar filho ate 5 anos";
                                        }
                                        else if($tipo_ponto_pk==30){
                                            $situacao = "Atestado por serviço Justiça Eleitoral";
                                        }
                                        else if($tipo_ponto_pk==31){
                                            $situacao = "Doação de sangue";
                                        }
                                        else if($tipo_ponto_pk==32){
                                            $situacao = "Atraso";
                                        }
                                        else if($tipo_ponto_pk==33){
                                            $situacao = "Declaração de horas abonar";
                                        }
                                        else if($tipo_ponto_pk==34){
                                            $situacao = "Sem Justificativa";
                                        }
                                        else if($tipo_ponto_pk==35){
                                            $situacao = "Reciclagem";
                                        }
                                        else if($tipo_ponto_pk==36){
                                            $situacao = "Audiência ";
                                        }
                                        
                                        
                                        break;
                                    case 3:
                                        $motivo_folga_pk = $arrDadosApontamento[$a]['motivo_folga_pk'];
                                        $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                        
                                
                                        if($motivo_folga_pk == '1'){
                                            $situacao = "Folga Trabalhada";
                                        }else if($motivo_folga_pk == '2'){
                                            $situacao = "Escala Errada";
                                        }else if($motivo_folga_pk == '3'){
                                            $situacao = "Convocação Normal";
                                        }
                                        if($tipo_ponto_pk==3){
                                            $situacao = "Folga";
                                        } else if($tipo_ponto_pk==20){
                                            $situacao = "Folga compensatória";
                                        }else if($tipo_ponto_pk==21){
                                            $situacao = "Folga de feriado";
                                        }else if($tipo_ponto_pk==25){
                                            $situacao = "Troca Folga";
                                        }
                                        else if($tipo_ponto_pk==26){
                                            $situacao = "Folga trabalhada";
                                        }
                                        else if($tipo_ponto_pk==27){
                                            $situacao = "Escala Errada";
                                        }
                                        break;
                                    case 5:
                                        $motivo_afastamento_pk = $arrDadosApontamento[$a]['motivo_afastamento_pk'];
                                        $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                    
                                        if($motivo_afastamento_pk == 1){
                                            $situacao = "Motivos Médicos";
                                        }else if($motivo_afastamento_pk == 2){
                                            $situacao = "Invalides";
                                        }                             
                                        break;
                                    case 6:
                                        $ic_apontamento = 1;
                                        $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                        $situacao = "Férias";
                                        break;
                                    case 8:
                                        $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                        if($tipo_ponto_pk==8){
                                            $situacao = "Disciplina";
                                        } else if($tipo_ponto_pk==17){
                                            $situacao = "Advertencia";
                                        }else if($tipo_ponto_pk==19){
                                            $situacao = "Demissão";
                                        }else if($tipo_ponto_pk==22){
                                            $situacao = "Justa causa";
                                        }else if($tipo_ponto_pk==23){
                                            $situacao = "Recisão indireta";
                                        }else if($tipo_ponto_pk==24){
                                            $situacao = "Suspensão";
                                        }
                                        break;
                                } 
                            }
                        }
                        
                        $arrPontos = $this->listarDadosPonto($dt_registro_ponto_usa, $query[$i]['colaborador_pk'],$query[$i]['agenda_colaborador_pk']);
    
    
                        $dt_registro_ponto = $query0[$j]['dt_ponto'];


                        
                        $hr_ini_expediente= $query0[$j]['hr_ini_expediente'];
                        $hr_ini_intervalo = $query0[$j]['hr_ini_intervalo'];
                        $hr_fim_intervalo = $query0[$j]['hr_fim_intervalo'];
                        $hr_fim_expediente = $query0[$j]['hr_fim_expediente'];
                        /*if($query0[$j]['tipo_ponto_pk']==1){
                            if($query[$i]['turnos_pk'] == 3 && isset($query0[$j+1])) {
                                $hr_ini_expediente= $query0[$j]['hr_ini_expediente'];
                                $hr_ini_intervalo = $query0[$j+1]['hr_ini_intervalo'];
                                $hr_fim_intervalo = $query0[$j+1]['hr_fim_intervalo'];
                                $hr_fim_expediente = $query0[$j+1]['hr_fim_expediente'];
                                
                            }
                        }*/
                        
    
                        $DadosFolhaRegistros[$j] = array(
                            "arrApontamento"=>$arrApontamento,
                            "arrPontos"=>$arrPontos,
                            "ponto_folha_pk"=>$query0[$j]['ponto_folha_pk'],
                            "ponto_folha_registro_pk"=>$query0[$j]['ponto_folha_registro_pk'],
                            "colaborador_pk"=>$query0[$j]['colaborador_pk'],
                            "dt_registro_ponto"=>$query0[$j]['dt_ponto'],
                            "tipo_ponto_pk"=>$query0[$j]['tipo_ponto_pk'],
                            "hr_ini_expediente"=>$hr_ini_expediente,
                            "hr_ini_intervalo"=>$hr_ini_intervalo,
                            "hr_fim_intervalo"=>$hr_fim_intervalo,
                            "hr_fim_expediente"=>$hr_fim_expediente,
                            "hr_trabalhadas"=>$query0[$j]['hr_trabalhadas'],
                            "hr_excedentes"=>$query0[$j]['hr_excedentes'],
                            "hr_faltantes"=>$query0[$j]['hr_faltantes'],
                            "ic_status"=>$query0[$j]['ic_status'],
                            "hr_extra50"=>$query0[$j]['hr_extra50'],
                            "hr_extra100"=>$query0[$j]['hr_extra100'],
                            "hr_adicional_noturno"=>$query0[$j]['hr_adicional_noturno'],  
                            "dia_da_semana"=>$query0[$j]['dia_da_semana'],  
                            "tipo_apontamento_pk"=>$tipo_ponto_pk,  
                            "situacao"=>$situacao,  
                            "obs"=>$query0[$j]['obs']
                        );
    
                    }
                    $result[] = array(
                        "pk" => $query[$i]["pk"],
                        "ds_periodo"=>$query[$i]['dt_periodo_ini']." a ".$query[$i]['dt_periodo_fim'],                    
                        "ds_empresa"=>$query[$i]['ds_empresa'],
                        "ds_endereco"=>$query[$i]['ds_endereco']." ,".$query[$i]['ds_numero'],
                        "ds_cnpj"=>$query[$i]['ds_cnpj_conta'],
                        "ds_colaborador"=>$query[$i]['ds_colaborador'],
                        "dt_admissao"=>$query[$i]['dt_admissao'],
                        "ds_cpf"=>$query[$i]['ds_cpf'],
                        "ds_cargo"=>$query[$i]['ds_cargo'],
                        "ds_posto_trabalho"=>$query[$i]['ds_posto_trabalho'],
                        "ds_escala"=>$query[$i]['n_qtde_dias_semana'],
                        "ds_turno"=>$query[$i]['ds_turno'],
                        "ds_hr_expediente"=>$query[$i]['hr_inicio_expediente']." a ".$query[$i]['hr_termino_expediente'],
                        "registrosfolha"=>$DadosFolhaRegistros,
                        "total_ht"=> $v_total_ht,
                        "total_he"=> $v_total_he,
                        "total_hf"=> $v_total_hf,
                        "total_he50"=> $v_total_he50,
                        "total_he100"=> $v_total_he100,
                        "total_hadn"=> $v_total_hadn,
                        "expediente_diario"=> $expediente_diario
                    ); 
                }    

            }

            $retorno->data = $result;
            $retorno->status = true;
            $retorno->message = 'Dados Salvos com sucesso !';
            return $retorno;
        }
        catch(Throwable $e){
            print_r($e->getMessage());
            die();
        }
        
    } 

  public function pegarHorarioDeEntradaPorDataDiaSemana($colaboradores_pk, $dt_escala, $agenda_colaborador_padrao_pk = "")
    {
        try{
            // Descobre o dia da semana (1 = segunda, ..., 7 = domingo)
            $diaSemana = date('N', strtotime($dt_escala));

            // Mapeia o nome do campo conforme o dia da semana
            $dias = [
                1 => 'seg',
                2 => 'ter',
                3 => 'qua',
                4 => 'qui',
                5 => 'sex',
                6 => 'sab',
                7 => 'dom'
            ];

            $diaCampo = $dias[$diaSemana];

            // Query dinâmica com base no dia da semana
            $sql = "
                SELECT 
                    pk,
                    colaboradores_pk,
                    dt_inicio_agenda,
                    dt_fim_agenda,
                    hr_turno_{$diaCampo} AS hr_inicio_expediente,
                    hr_intervalo_{$diaCampo} AS hr_saida_intervalo,
                    hr_intervalo_saida_{$diaCampo} AS hr_retorno_intervalo,
                    hr_turno_{$diaCampo}_saida AS hr_termino_expediente,
                    {$diaCampo}_turnos_pk AS turno_pk,
                    ic_{$diaCampo} AS ic_dia_ativo,
                    ic_{$diaCampo}_folga AS ic_folga
                FROM agenda_colaborador_padrao
                WHERE colaboradores_pk = :colaboradores_pk
            ";

            if (!empty($agenda_colaborador_padrao_pk)) {
                $sql .= " AND pk = :agenda_colaborador_padrao_pk";
            } else {
                $sql .= " AND dt_cancelamento IS NULL";
                $sql .= " AND dt_inicio_agenda <= :dt_escala";
                $sql .= " AND (dt_fim_agenda IS NULL OR dt_fim_agenda >= :dt_escala)";
            }
            $sql .= " ORDER BY dt_inicio_agenda DESC, pk DESC LIMIT 1";
           

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':colaboradores_pk', $colaboradores_pk, \PDO::PARAM_INT);
            if (!empty($agenda_colaborador_padrao_pk)) {
                $stmt->bindValue(':agenda_colaborador_padrao_pk', $agenda_colaborador_padrao_pk, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':dt_escala', $dt_escala);
            }
            $stmt->execute();

            $dadosEscala = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (!$dadosEscala) {
                return [];
            }

            // Extrai os horários conforme o dia da semana
            $hr_inicio_expediente = $dadosEscala[0]['hr_inicio_expediente'];
            $hr_saida_intervalo = $dadosEscala[0]['hr_saida_intervalo'];
            $hr_retorno_intervalo = $dadosEscala[0]['hr_retorno_intervalo'];
            $hr_termino_expediente = $dadosEscala[0]['hr_termino_expediente'];

            // Retorna os dados já com os horários do dia específico
            return [
                'dia_semana' => ucfirst($diaCampo),
                'dados' => [
                    'pk' => $dadosEscala[0]['pk'],
                    'colaboradores_pk' => $dadosEscala[0]['colaboradores_pk'],
                    'turno_pk' => $dadosEscala[0]['turno_pk'],
                    'ic_dia_ativo' => $dadosEscala[0]['ic_dia_ativo'],
                    'ic_folga' => $dadosEscala[0]['ic_folga'],
                    'hr_inicio_expediente' => $hr_inicio_expediente,
                    'hr_saida_intervalo' => $hr_saida_intervalo,
                    'hr_retorno_intervalo' => $hr_retorno_intervalo,
                    'hr_termino_expediente' => $hr_termino_expediente
                ]
            ];
        }
        catch(Throwable $e){
            print_r($e->getMessage());
            die();
        }
        
    }



    private function horarioCruzaMeiaNoite($hr_inicio_expediente, $hr_termino_expediente)
    {
        $hr_inicio_expediente = trim((string)$hr_inicio_expediente);
        $hr_termino_expediente = trim((string)$hr_termino_expediente);

        if ($hr_inicio_expediente === "" || $hr_termino_expediente === "") {
            return false;
        }

        return strtotime($hr_inicio_expediente) > strtotime($hr_termino_expediente);
    }

    private function normalizarHorarioEscala($horario, $fallback = "")
    {
        $horario = trim((string)$horario);
        if ($horario === "") {
            return $fallback;
        }

        return strlen($horario) === 5 ? $horario . ':00' : $horario;
    }

    private function montarJanelaOperacionalEscala($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk = "", $fallbackInicio = '16:00:00', $fallbackFim = '10:00:00')
    {
        $escala = $this->pegarHorarioDeEntradaPorDataDiaSemana($colaboradores_pk, $dt_escala, $agenda_colaborador_padrao_pk);
        $hr_inicio_expediente = $this->normalizarHorarioEscala($escala['dados']['hr_inicio_expediente'] ?? "", $fallbackInicio);
        $hr_termino_expediente = $this->normalizarHorarioEscala($escala['dados']['hr_termino_expediente'] ?? "", $fallbackFim);

        $cruzaMeiaNoite = $hr_inicio_expediente !== "" &&
            $hr_termino_expediente !== "" &&
            strtotime($hr_inicio_expediente) > strtotime($hr_termino_expediente);

        $dt_fim_operacional = $cruzaMeiaNoite
            ? date('Y-m-d', strtotime($dt_escala . ' +1 day'))
            : $dt_escala;

        $dt_inicio_operacional = $dt_escala . ' ' . $hr_inicio_expediente;
        $dt_fim_operacional_com_hora = $dt_fim_operacional . ' ' . $hr_termino_expediente;

        if ($cruzaMeiaNoite) {
            $dt_inicio_operacional = date('Y-m-d H:i:s', strtotime($dt_inicio_operacional) - $this->margemInicioTurnoNoturnoSegundos);
            $dt_fim_operacional_com_hora = date('Y-m-d H:i:s', strtotime($dt_fim_operacional_com_hora) + $this->margemFimTurnoNoturnoSegundos);
        }

        return [
            'inicio' => $dt_inicio_operacional,
            'fim' => $dt_fim_operacional_com_hora,
            'cruza_meia_noite' => $cruzaMeiaNoite,
        ];
    }

    private function buscarUltimosPontosPorJanela($colaboradores_pk, $dt_inicio, $dt_fim, $agenda_colaborador_padrao_pk = "")
    {
        $sql = '
            SELECT
                p.pk,
                p.tipo_ponto_pk,
                p.ic_validacao_facial,
                l.ds_lead,
                p.dt_validacao_facial,
                p.usuario_validacao_facial,
                acp.turnos_pk,
                DATE_FORMAT(p.dt_hora_ponto, "%H:%i") AS hora_ponto,
                DATE_FORMAT(p.dt_hora_ponto, "%d-%m-%Y") AS dt_ponto,
                DATE_FORMAT(p.dt_hora_ponto, "%Y-%m-%d") AS dt_compared
            FROM ponto p
            INNER JOIN (
                SELECT tipo_ponto_pk, MAX(dt_hora_ponto) AS max_dt
                FROM ponto
                WHERE colaborador_pk = :colaborador_pk
                  AND dt_hora_ponto BETWEEN :dt_inicio AND :dt_fim
                GROUP BY tipo_ponto_pk
            ) ultimos
                ON p.tipo_ponto_pk = ultimos.tipo_ponto_pk
               AND p.dt_hora_ponto = ultimos.max_dt
            INNER JOIN colaboradores c ON p.colaborador_pk = c.pk
            INNER JOIN agenda_colaborador_padrao acp ON p.colaborador_pk = acp.colaboradores_pk
            LEFT JOIN produtos_servicos ps ON acp.produtos_servicos_pk = ps.pk
            INNER JOIN leads l ON p.leads_pk = l.pk
            LEFT JOIN turnos t ON acp.turnos_pk = t.pk
            WHERE p.colaborador_pk = :colaborador_pk
              AND p.dt_hora_ponto BETWEEN :dt_inicio AND :dt_fim
        ';

        if ($agenda_colaborador_padrao_pk != "") {
            $sql .= ' AND acp.pk = :agenda_colaborador_padrao_pk ';
        }

        $sql .= '
            GROUP BY p.tipo_ponto_pk
            ORDER BY p.dt_hora_ponto DESC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':colaborador_pk', $colaboradores_pk, \PDO::PARAM_INT);
        $stmt->bindValue(':dt_inicio', $dt_inicio);
        $stmt->bindValue(':dt_fim', $dt_fim);
        if ($agenda_colaborador_padrao_pk != "") {
            $stmt->bindValue(':agenda_colaborador_padrao_pk', $agenda_colaborador_padrao_pk, \PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function listarConsultaPontoColaborador($leads_pk, $colaboradores_pk, $dt_periodo_ini,$dt_periodo_fim,$agenda_colaborador_padrao_pk){              

        $dadosEscala = $this->listarDadosEscala($dt_periodo_ini, $dt_periodo_fim, $colaboradores_pk, $agenda_colaborador_padrao_pk);
        
        $agenda_colaborador_padrao_pk = $dadosEscala[0]["pk"];
        $ds_turno = $dadosEscala[0]["ds_turno"];
        $expediente_diario = $dadosEscala[0]["hr_jornada_trabalho_intervalo"];
        $ic_intrajornada = $dadosEscala[0]["ic_intrajornada"];
        $hr_expediente = $dadosEscala[0]["hr_expediente"];
        $intervalo_diario = "";
        $expediente = "";
        $arrPontoFolha = [];
        $queryTempoExpediente = [];
        $arrPontoFolha = $this->getPontoFolhaByColaboradorPeriodoColaborador($colaboradores_pk, $dt_periodo_ini,$dt_periodo_fim);
        if (!is_array($arrPontoFolha)) {
            $arrPontoFolha = [];
        }
        
        
        $arrPontos = [];
        $arrDias = [];
        $arrVerificado = [];
        $arrApontamento = [];

        $situacao = "";
        $tipo_ponto_pk = "";

        //PEGAR O TURNO DA AGENDA
        

        $diasEscala = $this->listarDiasEscalaPorColaborador($colaboradores_pk,$dt_periodo_ini, $dt_periodo_fim, $agenda_colaborador_padrao_pk);
        $turnos_pk = $this->listarTurnosPk($agenda_colaborador_padrao_pk);

        for($i=0; $i<count($diasEscala); $i++){  
            $dt_escala = $diasEscala[$i]['dt_escala'];
            $dt_format = $diasEscala[$i]['dt_format'];
            $dia_da_semana = $diasEscala[$i]['dia_da_semana'];
            //PEGAR O HR_ENTRADA E INTERVALO DE ACORODO COM O DIA 
            $escala = $this->pegarHorarioDeEntradaPorDataDiaSemana($colaboradores_pk, $dt_escala, $agenda_colaborador_padrao_pk);
            $hr_saida_intervalo = $escala['dados']['hr_saida_intervalo'];
            $hr_inicio_expediente = $escala['dados']['hr_inicio_expediente'];
            $hr_termino_expediente = $escala['dados']['hr_termino_expediente'];
            $hr_retorno_intervalo = $escala['dados']['hr_retorno_intervalo'];
            


            $queryTempoExpediente  = $this->retornarDifHora($hr_inicio_expediente,$hr_termino_expediente);
     
            $expediente = $queryTempoExpediente[0]['dif']; 

            $queryTempoIntervalo  = $this->retornarDifHora($hr_saida_intervalo,$hr_retorno_intervalo);
            
            $intervalo_diario = $queryTempoIntervalo[0]['dif']; 

            $queryTempo  = $this->retornarDifHora($intervalo_diario,$expediente);
            if($dadosEscala[0]["hr_jornada_trabalho_intervalo"]==null){
                $expediente_diario = $queryTempo[0]['dif']; 
            }

            $dt_escala_obj = new DateTime($dt_escala);
            $dt_escala_obj->modify('+1 day'); // Subtrai 1 dia
            
            // Formata a data no formato desejado
            $dt_escala_modified = $dt_escala_obj->format('Y-m-d');
            
            $arrApontamento = $this->listarDadosApontamento($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk,0);
          
            $arrVerificado = $this->getVerificado($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk);
           
            
            $ponto_ini_expediente = "";
            $ic_preencher_folha = 1;
            $ponto_term_expediente = "";
            $ponto_ini_intervalo = "";
            $ponto_term_intervalo = "";
            $ic_validacao_facial_ini_expediente = "";
            $ic_validacao_facial_ini_intervalor = "";
            $ic_validacao_facial_termino_intervalor = "";
            $ic_validacao_facial_termino_expediente = "";
            
            


            $ic_apontamento_ini = "";
      
            $ic_apontamento_ter = "";
       
            $ic_apontamento_ini_int = "";
         
            $ic_apontamento_fim_int = "";
            $hr_excedentes = "";
            $hr_faltante = "";
            $horas_trabalhadas = "";
            $obs = " ";
            $ic_apontamento = 0;
            $apontamento_pk = "";
            $situacao = "";
            $arrPontos = [];

            
            $diaAtual = date('Y-m-d');

            $turnoDiaPk = isset($escala['dados']['turno_pk']) ? (int)$escala['dados']['turno_pk'] : (int)$turnos_pk;
            $isTurnoNoturno = $turnoDiaPk === 3 || (
                $hr_inicio_expediente != "" &&
                $hr_termino_expediente != "" &&
                strtotime($hr_inicio_expediente) > strtotime($hr_termino_expediente)
            );

            $arrNoturno = $this->verificarPontoEscalaNoturna($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk);
            $arrNormal = $this->verificarPontoEscalaNormal($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk);
            $query = [];

            if ($isTurnoNoturno) {
                $query = $this->verificarPontoEscalaNoturnaPorDiaFechamento($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk);
            } elseif (!empty($arrNormal)) {
                $tipos = array_column($arrNormal, 'tipo_ponto_pk');
                $temEntrada = in_array(1, $tipos);
                $temSaida = in_array(2, $tipos);

                if ($temEntrada && $temSaida) {
                    $query = $arrNormal;
                } elseif ($temEntrada || $temSaida) {
                    $query = $arrNormal;
                } elseif (count($arrNormal) >= 1) {
                    $query = $arrNormal;
                } else {
                    $query = $arrNoturno;
                }
            } else {
                $query = $arrNoturno;
            }
            
            
           
            

            for ($l = 0; $l < count($query); $l++) {
                
                switch ($query[$l]['tipo_ponto_pk']) {
                case 1:  // Início do expediente
                    $ponto_ini_expediente = $query[$l]["hora_ponto"];
                    $ic_validacao_facial_ini_expediente = $query[$l]["ic_validacao_facial"];
                    
                    break;
                case 2:  // Término do expediente
                        $ponto_term_expediente = $query[$l]["hora_ponto"];
                        $ic_validacao_facial_termino_expediente = $query[$l]["ic_validacao_facial"];
                    
                    break;
    
                case 3:  // Início do intervalo
                        $ponto_ini_intervalo = $query[$l]["hora_ponto"];
                        $ic_validacao_facial_ini_intervalor = $query[$l]["ic_validacao_facial"];
                    
                    break;
    
                case 4:  // Término do intervalo
                        $ponto_term_intervalo = $query[$l]["hora_ponto"];
                        $ic_validacao_facial_termino_intervalor = $query[$l]["ic_validacao_facial"];
                    
                    break;
                }
            
            }
            if($diasEscala[$i]['ic_escala']==1){
                $situacao = "Escala";
            }
            else{
                $situacao = "Folga";
                $tipo_ponto_pk = 5;          
            }

            
            
            
            /*echo $diaAtual . '-';
            echo $dt_escala . '<br>';*/
            
            // Define a situação com base na escala
           
            if(count($arrApontamento[0]['arrApontamento']) > 0){
                
                $arrDadosApontamento = $arrApontamento[0]['arrApontamento'];
                for($a=0;$a<count($arrDadosApontamento);$a++){
                        $tipo = (int)$arrApontamento[0]['tipo_apontamento_pk'];
                        $apontamento_pk = (int)$arrApontamento[0]['apontamento_pk'];
                        $tipoComp = (int)$arrDadosApontamento[$a]['tipo_apontamento_dados_pk'];

                        if($tipo == $tipoComp){
                            switch($tipo){
                                case 1:
                                    $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                    $ic_apontamento = 1;
                                    if($tipo_ponto_pk==1){
                                        $situacao = "Ponto/Expediente";
                                    }
                                    else if($tipo_ponto_pk==33){
                                        $situacao = "Declaração de horas abonar";
                                    }
                                    else if($tipo_ponto_pk==36){
                                        $situacao = "Audiência";
                                    }
                                    else if($tipo_ponto_pk==37){
                                        $situacao = "Atestado de horas";
                                    }
                                    if($arrDadosApontamento[$a]['tipo_ponto_pk'] == 1){
                                        $ic_apontamento_ini = 1;
                                        $ponto_ini_expediente = $arrDadosApontamento[$a]["hr_ponto"];
                                    }
                                    if($arrDadosApontamento[$a]['tipo_ponto_pk'] == 2){
                                    
                                        $ic_apontamento_ter = 2;
                                        $ponto_term_expediente = $arrDadosApontamento[$a]["hr_ponto"];
                                    }
                                    if($arrDadosApontamento[$a]['tipo_ponto_pk'] == 3){
                                    
                                        $ic_apontamento_ini_int = 3;
                                        $ponto_ini_intervalo = $arrDadosApontamento[$a]["hr_ponto"];
                                    }
                                    if($arrDadosApontamento[$a]['tipo_ponto_pk'] == 4){
                                        
                                        $ic_apontamento_fim_int = 4;
                                        $ponto_term_intervalo = $arrDadosApontamento[$a]["hr_ponto"];
                                    }

                                    $hr_excedentes = $arrDadosApontamento[$a]["hr_excedentes"];
                                    $hr_faltante = $arrDadosApontamento[$a]["hr_faltantes"];
                                    $horas_trabalhadas = $arrDadosApontamento[$a]["hr_trabalhadas"];
                                    break;
                                case 2:
                                    $ic_apontamento = 1;
                                    $motivo_falta_pk = $arrDadosApontamento[$a]['motivo_falta_pk'];
                                    $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
    
                                    for($l=0; $l<count($query); $l++){
                    
                      
                                        if($query[$l]['tipo_ponto_pk'] == 1){
                                            $ponto_ini_expediente = $query[$l]["hora_ponto"];
                                            $ic_validacao_facial_ini_expediente = $query[$l]["ic_validacao_facial"];
                                        }
                                        if($query[$l]['tipo_ponto_pk'] == 2){
                                            $ponto_term_expediente = $query[$l]["hora_ponto"];
                                            $ic_validacao_facial_termino_expediente = $query[$l]["ic_validacao_facial"];
                                        }
                                        if($query[$l]['tipo_ponto_pk'] == 3){
                                            $ponto_ini_intervalo = $query[$l]["hora_ponto"];
                                            $ic_validacao_facial_ini_intervalor = $query[$l]["ic_validacao_facial"];
                                        }
                                        if($query[$l]['tipo_ponto_pk'] == 4){
                                            $ponto_term_intervalo = $query[$l]["hora_ponto"];
                                            $ic_validacao_facial_termino_intervalor = $query[$l]["ic_validacao_facial"];
                                        }
                                    }
                                    if($tipo_ponto_pk==2){
                                        $situacao = "Falta";
                                        $ponto_ini_expediente = "";
                                        $ponto_term_expediente = "";
                                        $ponto_ini_intervalo = "";
                                        $ponto_term_intervalo = "";
                                        $hr_excedentes = "";
                                        $hr_faltante = "";
                                        $horas_trabalhadas = "";
                                    } else if($tipo_ponto_pk==11){
                                        $situacao = "Abonada";
                                    }else if($tipo_ponto_pk==16){
                                        $situacao = "Atestado";
                                        $ponto_ini_expediente = "";
                                        $ponto_term_expediente = "";
                                        $ponto_ini_intervalo = "";
                                        $ponto_term_intervalo = "";
                                        $hr_excedentes = "";
                                        $hr_faltante = "";
                                        $horas_trabalhadas = "";
                                    }else if($tipo_ponto_pk==18){
                                        $situacao = "Declaração da defesa civil";
                                    }
                                    else if($tipo_ponto_pk==28){
                                        $situacao = "Apoio Operacional ";
                                    }
                                    else if($tipo_ponto_pk==29){
                                        $situacao = "Atestado por acompanhar filho ate 5 anos";
                                    }
                                    else if($tipo_ponto_pk==30){
                                        $situacao = "Atestado por serviço Justiça Eleitoral";
                                    }
                                    else if($tipo_ponto_pk==31){
                                        $situacao = "Doação de sangue";
                                    }
                                    else if($tipo_ponto_pk==32){
                                        $situacao = "Atraso";
                                    }
                                    else if($tipo_ponto_pk==33){
                                        $situacao = "Declaração de horas abonar";
                                    }
                                    else if($tipo_ponto_pk==34){
                                        $situacao = "Sem Justificativa";
                                    }
                                    else if($tipo_ponto_pk==35){
                                        $situacao = "Reciclagem";
                                    }
                                    else if($tipo_ponto_pk==36){
                                        $situacao = "Audiência ";
                                    }
                                    
                                    
                                    
                                    
                                    break;
                                case 3:
                                    $ic_apontamento = 1;
                                    $motivo_folga_pk = $arrDadosApontamento[$a]['motivo_folga_pk'];
                                    $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                    
                            
                                    if($motivo_folga_pk == '1'){
                                        $situacao = "Folga Trabalhada";
                                    }else if($motivo_folga_pk == '2'){
                                        $situacao = "Escala Errada";
                                    }else if($motivo_folga_pk == '3'){
                                        $situacao = "Convocação Normal";
                                    }
                                    if($tipo_ponto_pk==3){
                                        $situacao = "Folga";
                                    } else if($tipo_ponto_pk==20){
                                        $situacao = "Folga compensatória";
                                    }else if($tipo_ponto_pk==21){
                                        $situacao = "Folga de feriado";
                                    }else if($tipo_ponto_pk==25){
                                        $situacao = "Troca Folga";
                                    }
                                    else if($tipo_ponto_pk==26){
                                        $situacao = "Folga trabalhada";
                                    }
                                    else if($tipo_ponto_pk==27){
                                        $situacao = "Escala Errada";
                                    }
                                    $ponto_ini_expediente = "";
                                    $ponto_term_expediente = "";
                                    $ponto_ini_intervalo = "";
                                    $ponto_term_intervalo = "";
                                    
    
                                    
                                    break;
                                case 5:
                                    $ic_apontamento = 1;
                                    $motivo_afastamento_pk = $arrDadosApontamento[$a]['motivo_afastamento_pk'];
                                    $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                    $ponto_ini_expediente = "";
                                    $ponto_term_expediente = "";
                                    $ponto_ini_intervalo = "";
                                    $ponto_term_intervalo = "";
                                    if($motivo_afastamento_pk == 1){
                                        $situacao = "Motivos Médicos";
                                    }else if($motivo_afastamento_pk == 2){
                                        $situacao = "Invalides";
                                    }                             
                                    break;
                                case 6:
                                    $ic_apontamento = 1;
                                    $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                    $situacao = "Férias";
                                    $ponto_ini_expediente = "";
                                    $ponto_term_expediente = "";
                                    $ponto_ini_intervalo = "";
                                    $ponto_term_intervalo = "";
                                    break;
                                case 8:
                                    $ic_apontamento = 1;
                                    $tipo_ponto_pk = $arrDadosApontamento[$a]['tipo_apontamento_pk'];
                                    if($tipo_ponto_pk==8){
                                        $situacao = "Disciplina";
                                    } else if($tipo_ponto_pk==17){
                                        $situacao = "Advertencia";
                                    }else if($tipo_ponto_pk==19){
                                        $situacao = "Demissão";
                                    }else if($tipo_ponto_pk==22){
                                        $situacao = "Justa causa";
                                    }else if($tipo_ponto_pk==23){
                                        $situacao = "Recisão indireta";
                                    }else if($tipo_ponto_pk==24){
                                        $situacao = "Suspensão";
                                    }
                                    $ponto_ini_expediente = "";
                                    $ponto_term_expediente = "";
                                    $ponto_ini_intervalo = "";
                                    $ponto_term_intervalo = "";
                                    break;
                            } 
                        }
                    }
                }
            //array de pontos por dia
            $arrPontos[] = array(
                "dt_escala_modified" => $dt_escala_modified,
                "ponto_ini_expediente" => $ponto_ini_expediente,
                "ponto_ini_intervalo" => $ponto_ini_intervalo,
                "ic_validacao_facial_ini_expediente" => $ic_validacao_facial_ini_expediente,
                "ic_validacao_facial_ini_intervalo" => $ic_validacao_facial_ini_intervalor,
                "ic_validacao_facial_termino_intervalo" => $ic_validacao_facial_termino_intervalor,
                "ic_validacao_facial_termino_expediente" => $ic_validacao_facial_termino_expediente,
                "ponto_term_intervalo" => $ponto_term_intervalo,
                "ponto_term_expediente" => $ponto_term_expediente,
                "hr_excedentes" => $hr_excedentes,
                "hr_faltante" => $hr_faltante,
                "horas_trabalhadas" => $horas_trabalhadas,
                "ic_apontamento_ini" => $ic_apontamento_ini,
                "hr_inicio_expediente" => $hr_inicio_expediente,
                "hr_termino_expediente" => $hr_termino_expediente,
                "expediente_diario" => $expediente_diario,
                "ic_apontamento_ter" =>$ic_apontamento_ter ,
        
                "ic_apontamento_ini_int" =>$ic_apontamento_ini_int,
            
                "ic_apontamento_fim_int" => $ic_apontamento_fim_int,
                "situacao" => $situacao,
                "ic_apontamento" => $ic_apontamento,
                "apontamento_pk" => $apontamento_pk,
                "ds_lead" => isset($query[0]['ds_lead']) ? $query[0]['ds_lead'] : null,
                "tipo_ponto_pk" => $tipo_ponto_pk
            );

            //array de dias por período 
            $arrDias[] = array(
                "dt_hora_ponto" => $dt_format,
                "dt_hora_ponto_usa" => $dt_escala,
                "dia_da_semana" => $dia_da_semana,
                "hr_inicio_expediente" => $hr_inicio_expediente,
                "hr_termino_expediente" => $hr_termino_expediente,
                "ds_turno" => $ds_turno,
                "turnos_pk" => $turnos_pk,
                "ponto_folha_pk" => isset($arrPontoFolha['pk']) ? $arrPontoFolha['pk'] : "",
                "ic_status_ponto_folha_pk" => isset($arrPontoFolha['ic_status']) ? $arrPontoFolha['ic_status'] : "",
                "arrVerificado" => $arrVerificado,
                "pontos_dia"=>$arrPontos
            );
        }
        
        return $arrDias;
    }

    public function verificarPontoEscalaNormal($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk = "")
    {
        $sql = '
            SELECT 
                p.pk,
                p.tipo_ponto_pk,
                p.ic_validacao_facial,
                l.ds_lead,
                p.dt_validacao_facial,
                p.usuario_validacao_facial,
                acp.turnos_pk,
                DATE_FORMAT(p.dt_hora_ponto, "%H:%i") AS hora_ponto,
                DATE_FORMAT(p.dt_hora_ponto, "%d-%m-%Y") AS dt_ponto,
                DATE_FORMAT(p.dt_hora_ponto, "%Y-%m-%d") AS dt_compared
            FROM ponto p
            INNER JOIN (
                SELECT tipo_ponto_pk, MAX(dt_hora_ponto) AS max_dt
                FROM ponto
                WHERE colaborador_pk = :colaborador_pk
                AND DATE(dt_hora_ponto) = :dt_escala
                GROUP BY tipo_ponto_pk
            ) ultimos 
                ON p.tipo_ponto_pk = ultimos.tipo_ponto_pk 
            AND p.dt_hora_ponto = ultimos.max_dt
            INNER JOIN colaboradores c ON p.colaborador_pk = c.pk
            INNER JOIN agenda_colaborador_padrao acp ON p.colaborador_pk = acp.colaboradores_pk
            LEFT JOIN produtos_servicos ps ON acp.produtos_servicos_pk = ps.pk
            INNER JOIN leads l ON p.leads_pk = l.pk
            LEFT JOIN turnos t ON acp.turnos_pk = t.pk
            WHERE p.colaborador_pk = :colaborador_pk
            AND DATE(p.dt_hora_ponto) = :dt_escala
        ';

        if($agenda_colaborador_padrao_pk != ""){
            $sql .= " AND acp.pk = :agenda_colaborador_padrao_pk ";
        }

        $sql .= '
            GROUP BY p.tipo_ponto_pk
            ORDER BY p.dt_hora_ponto DESC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':colaborador_pk', $colaboradores_pk, \PDO::PARAM_INT);
        $stmt->bindValue(':dt_escala', $dt_escala);
        if($agenda_colaborador_padrao_pk != ""){
            $stmt->bindValue(':agenda_colaborador_padrao_pk', $agenda_colaborador_padrao_pk, \PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
    public function verificarPontoEscalaNoturna($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk = "")
    {
        $janela = $this->montarJanelaOperacionalEscala($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk);

        return $this->buscarUltimosPontosPorJanela(
            $colaboradores_pk,
            $janela['inicio'],
            $janela['fim'],
            $agenda_colaborador_padrao_pk
        );
    }

    public function verificarPontoEscalaNoturnaPorDiaFechamento($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk = "")
    {
        $janela = $this->montarJanelaOperacionalEscala($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk);

        return $this->buscarUltimosPontosPorJanela(
            $colaboradores_pk,
            $janela['inicio'],
            $janela['fim'],
            $agenda_colaborador_padrao_pk
        );
    }

    public function listarDadosPonto($dt_escala,$colaboradores_pk,$agenda_colaborador_padrao_pk){
        $turnos_pk = $this->listarTurnosPk($agenda_colaborador_padrao_pk);
        $escala = $this->pegarHorarioDeEntradaPorDataDiaSemana($colaboradores_pk, $dt_escala, $agenda_colaborador_padrao_pk);
        $hr_inicio_expediente = $escala['dados']['hr_inicio_expediente'] ?? "";
        $hr_termino_expediente = $escala['dados']['hr_termino_expediente'] ?? "";

        $arrNoturno = $this->verificarPontoEscalaNoturna($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk);
        $arrNormal  = $this->verificarPontoEscalaNormal($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk);
        $turnoDiaPk = isset($escala['dados']['turno_pk']) ? (int)$escala['dados']['turno_pk'] : (int)$turnos_pk;
        $isTurnoNoturno = $turnoDiaPk === 3 || (
            $hr_inicio_expediente != "" &&
            $hr_termino_expediente != "" &&
            strtotime($hr_inicio_expediente) > strtotime($hr_termino_expediente)
        );

        $query = [];

        if ($isTurnoNoturno) {
            $query = $this->verificarPontoEscalaNoturnaPorDiaFechamento($dt_escala, $colaboradores_pk, $agenda_colaborador_padrao_pk);
        } elseif (!empty($arrNormal)) {
            $tipos = array_column($arrNormal, 'tipo_ponto_pk');
            $temEntrada = in_array(1, $tipos);
            $temSaida   = in_array(2, $tipos);

            if ($temEntrada && $temSaida) {
                $query = $arrNormal;
            } elseif ($temEntrada || $temSaida) {
                $query = $arrNormal;
            } elseif (count($arrNormal) >= 1) {
                $query = $arrNormal;
            } else {
                $query = $arrNoturno;
            }
        } else {
            $query = $arrNoturno;
        }
        
        $ponto_ini_expediente = "";
        $ponto_term_expediente = "";
        $ponto_ini_intervalo = "";
        $ponto_term_intervalo = "";
            
        //Verificação de tipo de ponto
        for($l=0; $l<count($query); $l++){
        
            
            if($query[$l]['tipo_ponto_pk'] == 1){
                $ponto_ini_expediente = $query[$l]["hora_ponto"];
            }
            if($query[$l]['tipo_ponto_pk'] == 2){
                $ponto_term_expediente = $query[$l]["hora_ponto"];
            }
            if($query[$l]['tipo_ponto_pk'] == 3){
                $ponto_ini_intervalo = $query[$l]["hora_ponto"];
            }
            if($query[$l]['tipo_ponto_pk'] == 4){
                $ponto_term_intervalo = $query[$l]["hora_ponto"];
            }
                
        }

        //array de dias por período 
        $arrPonto[] = array(
            "ponto_ini_expediente" => $ponto_ini_expediente,
            "ponto_term_expediente" => $ponto_term_expediente,
            "ponto_ini_intervalo" => $ponto_ini_intervalo,
            "ponto_term_intervalo"=>$ponto_term_intervalo
        );
    
    
    return $arrPonto;
    }


    public function listarFolhaPorPeriodoByLeads($leads_pk, $dt_periodo_ini,$dt_periodo_fim){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select max(p.pk)pk,l.ds_lead,l.pk leads_pk from ponto_folha p 
                inner join leads l on p.leads_pk = l.pk
                where p.dt_periodo_ini = '".Util::DataYMD($dt_periodo_ini)."' AND p.dt_periodo_fim = '".Util::DataYMD($dt_periodo_fim)."'
                and l.pk in(".$leads_pk.",0)
                group by l.pk
                order by p.pk desc";
                

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }
    public function getHorasColaborador($colaborador_pk,$leads_pk,$ic_mes,$ic_ano,$ds_mes){
        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio

            // Primeiro dia do mês atual
            $primeiroDiaMesAtual = date("Y-m-d", strtotime("$ic_ano-$ic_mes-01"));

            // Último dia do mês atual
            $ultimoDiaMesAtual = date("Y-m-t", strtotime("$ic_ano-$ic_mes-01"));

            $sql ="";
            $sql.=" SELECT pf.pk,";    
            $sql.="    pfc.ponto_folha_pk, ";  
            $sql.="    pf.leads_pk, ";  
            $sql.="    l.ds_lead, ";  
            $sql.="   CASE a.turnos_pk";
            $sql.="        WHEN 1 THEN 'Manhã'";
            $sql.="        WHEN 2 THEN 'Tarde'";
            $sql.="        WHEN 3 THEN 'Noite'";
            $sql.="        WHEN 4 THEN 'Dia Todo'";
            $sql.="   END ds_turno,";
            $sql.="    col.ds_colaborador,";
            $sql.="    pfc.colaborador_pk";
            
            $sql.=" FROM ponto_folha pf";
            $sql.="  LEFT JOIN contas c ON pf.empresas_pk = c.pk";
            $sql.="  INNER JOIN leads l ON pf.leads_pk = l.pk";
            
            $sql.="  INNER JOIN ponto_folha_colaborador pfc ON pf.pk = pfc.ponto_folha_pk";  
            $sql.="  INNER JOIN ponto_folha_registros pfr ON pf.pk = pfr.ponto_folha_pk";  
            $sql.="  INNER JOIN agenda_colaborador_padrao a ON pfc.colaborador_pk = a.colaboradores_pk";
            $sql.="  LEFT JOIN turnos t ON a.turnos_pk = t.pk";
            $sql.="  INNER JOIN colaboradores col ON pfc.colaborador_pk = col.pk";
            $sql.="  INNER JOIN colaboradores_produtos_servicos cps  ON col.pk = cps.colaboradores_pk";
            $sql.="  INNER JOIN produtos_servicos ps ON cps.produtos_servicos_pk = ps.pk";
            $sql.=" WHERE 1=1";
            $sql.=" AND pfc.ic_status is not null";

            if(!empty($colaborador_pk)){
                $sql.=" AND pfc.colaborador_pk=".$colaborador_pk;
            }
           
            
            if(!empty($leads_pk)){
                $sql.=" AND pf.leads_pk=".$leads_pk;
                $sql.=" AND a.leads_pk=".$leads_pk;
            }
            //$sql.=" AND a.dt_cancelamento is null";
            $sql.=" and pfr.dt_hora_ponto BETWEEN '".$primeiroDiaMesAtual." 00:00:00' and '".$ultimoDiaMesAtual." 23:59:59'";

            $sql.=" GROUP BY col.pk";
            $sql.=" ORDER BY col.ds_colaborador, l.ds_lead";

           


        
        
            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if(count($query) > 0){
                for($i = 0; $i < count($query); $i++){
                    $v_total_ht = "";
                    $v_total_he = "";
                    $v_total_hf = "";
                    $v_he_mes_anterior = "";

                    //Total Horas Trabalhadas
                    $v_total_ht = $this->TotalHrTrabalhada($query[$i]['pk'],$query[$i]['colaborador_pk']);     
                                
                    //Total Horas Excedentes
                    $v_total_he = $this->TotalHrExcedentes($query[$i]['pk'],$query[$i]['colaborador_pk']);          
                                
                    //Total Horas Excedentes
                    $v_total_hf = $this->TotalHrFaltantes($query[$i]['pk'],$query[$i]['colaborador_pk']);      
                    
                    //PEGAR HORAS EXCEDENTES MÊS ANTERIOR 
                    $v_he_mes_anterior = $this->horaExcedenteMesAnterior($query[$i]['colaborador_pk'],$query[$i]['leads_pk'],$ic_mes,$ic_ano);
                                
                    //array de dias por período 
                    $result[] = array(
                        "colaborador_pk" => $query[$i]['colaborador_pk'],
                        "leads_pk" => $query[$i]['leads_pk'],
                        "ds_colaborador" => $query[$i]['ds_colaborador'],
                        "ds_lead" => $query[$i]['ds_lead'],
                        "ds_turno" => $query[$i]['ds_turno'],
                        "ano" => $ic_ano,
                        "mes"=>$ds_mes,
                        "total_horas_trabalhadas"=>$v_total_ht,
                        "total_horas_excedentes"=>$v_total_he,
                        "total_horas_excedentes_mes_anterior"=>$v_he_mes_anterior,
                        "total_horas_faltantes"=>$v_total_hf,
                        "ponto_folha_pk"=>$query[$i]['pk'],
                    );

                }
            }
            $retorno->data = $result;
            $retorno->status = true;
            $retorno->message = 'Dados carregados com sucesso';
            

            return $retorno;
        }
        catch(Throwable $e){
            print_r($e->getMessage());
            die();
        }
    }
    public function getPontoFolhaPk($leads_pk,$colaborador_pk,$ic_mes,$ic_ano){
        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio

            // Primeiro dia do mês atual
            $primeiroDiaMesAtual = date("Y-m-d", strtotime("$ic_ano-$ic_mes-01"));

            // Último dia do mês atual
            $ultimoDiaMesAtual = date("Y-m-t", strtotime("$ic_ano-$ic_mes-01"));

            $sql ="";
            $sql.=" SELECT pf.pk";
            $sql.="        ,pfc.ic_status";
            $sql.=" FROM ponto_folha pf";
            $sql.="  LEFT JOIN contas c ON pf.empresas_pk = c.pk";
            $sql.="  LEFT JOIN ponto_folha_colaborador pfc ON pfc.ponto_folha_pk = pf.pk";
            $sql.="  INNER JOIN leads l ON pf.leads_pk = l.pk"; 
            $sql.=" WHERE 1=1";           
            
            
            if(!empty($leads_pk)){
                $sql.=" AND pf.leads_pk=".$leads_pk;
            }
      
            //$sql.=" AND a.dt_cancelamento is null";
            $sql.=" and pf.dt_periodo_ini = '".$primeiroDiaMesAtual."' and dt_periodo_fim= '".$ultimoDiaMesAtual."'";

            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if(count($query)>0){
                return $query[0]['pk'];
            }
            else{

                $dados = (new Colaborador($this->pdo))->listarPk($colaborador_pk);
                $fields = array();
                $fields['empresas_pk'] = $dados->data[0]['empresas_pk'];
                $fields['dt_periodo_ini'] = $primeiroDiaMesAtual;
                $fields['dt_periodo_fim'] = $ultimoDiaMesAtual;
                $fields['leads_pk'] = $leads_pk; // Associa o leads_pk ao registro principal
                $fields["dt_ult_atualizacao"] = "sysdate()";
                $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
        
                if (empty($pontoFolha['pk'])) {
                    $fields["dt_cadastro"] = "sysdate()";
                    $fields["usuario_cadastro_pk"] = $_SESSION['session_user']['par1'];
                    
                    // Insere o lead na tabela `ponto_folha`
                    $pk = Util::execInsert("ponto_folha", $fields, $this->pdo);
                    return $pk; // Armazena cada `pk` de `ponto_folha` para cada lead
                }
            }

            
            

            
        }
        catch(Throwable $e){
            print_r($e->getMessage());
            die();
        }
    }
    public function getPontoFolhaByColaboradorPeriodoLead($leads_pk,$colaborador_pk,$primeiroDiaMesAtual,$ultimoDiaMesAtual){
        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio


            $sql ="";
            $sql.=" SELECT pf.pk";
            $sql.="        ,pfc.ic_status";
            $sql.=" FROM ponto_folha pf";
            $sql.="  LEFT JOIN contas c ON pf.empresas_pk = c.pk";
            $sql.="  INNER JOIN leads l ON pf.leads_pk = l.pk"; 
            $sql.="  INNER JOIN ponto_folha_colaborador pfc ON pf.pk = pfc.ponto_folha_pk"; 
            $sql.=" WHERE 1=1";           
            
            if(!empty($leads_pk)){
                $sql.=" AND pf.leads_pk=".$leads_pk;
            }
            if(!empty($colaborador_pk)){
                $sql.=" AND pfc.colaborador_pk=".$colaborador_pk;
            }
      
            //$sql.=" AND a.dt_cancelamento is null";
            $sql.=" and pf.dt_periodo_ini = '".$primeiroDiaMesAtual."' and dt_periodo_fim= '".$ultimoDiaMesAtual."'";

            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if(count($query)>0){
                return $query[0];
            }
            else{

                return [];
            }
        }
        catch(Throwable $e){
            print_r($e->getMessage());
            die();
        }
    }
    public function getPontoFolhaByColaboradorPeriodoColaborador($colaborador_pk,$primeiroDiaMesAtual,$ultimoDiaMesAtual){
        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio


            $sql ="";
            $sql.=" SELECT pf.pk";
            $sql.="        ,pfc.ic_status";
            $sql.=" FROM ponto_folha pf";
            $sql.="  LEFT JOIN contas c ON pf.empresas_pk = c.pk";
            $sql.="  INNER JOIN leads l ON pf.leads_pk = l.pk"; 
            $sql.="  INNER JOIN ponto_folha_colaborador pfc ON pf.pk = pfc.ponto_folha_pk"; 
            $sql.=" WHERE 1=1";           
            if(!empty($colaborador_pk)){
                $sql.=" AND pfc.colaborador_pk=".$colaborador_pk;
            }
      
            //$sql.=" AND a.dt_cancelamento is null";
            $sql.=" and pf.dt_periodo_ini = '".$primeiroDiaMesAtual."' and dt_periodo_fim= '".$ultimoDiaMesAtual."'";

            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if(count($query)>0){
                return $query[0];
            }
            else{

                return "";
            }
        }
        catch(Throwable $e){
            print_r($e->getMessage());
            die();
        }
    }




    public function gerarFolhaPontoByRelogio($arrDados){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
       
        try{
            $arrDados = json_decode($arrDados, true);
            if (!is_array($arrDados) || count($arrDados) == 0) {
                $retorno->message = 'Nenhum registro informado para gerar a folha';
                return $retorno;
            }
            if (empty($arrDados[0]['ic_mes']) || empty($arrDados[0]['ic_ano'])) {
                $dtInicio = isset($arrDados[0]['dt_inicio']) ? $arrDados[0]['dt_inicio'] : "";
                if (!empty($dtInicio)) {
                    $dtInicioYmd = strpos($dtInicio, '/') !== false ? Util::DataYMD($dtInicio) : $dtInicio;
                    $arrDados[0]['ic_mes'] = date("m", strtotime($dtInicioYmd));
                    $arrDados[0]['ic_ano'] = date("Y", strtotime($dtInicioYmd));
                }
            }
            $p1 = $arrDados[0]['ic_ano']."-"+$arrDados[0]['ic_mes']."-01";
            // Primeiro dia do mês atual
            $primeiroDiaMesAtual = date("Y-m-d", strtotime($p1));

            // Último dia do mês atual
            $ultimoDiaMesAtual = date("Y-m-t", strtotime($p1));

          
            //if($arrDados[0]['ic_status_ponto_folha_pk']!=1){
                //Verificar se existe a folha 
                $ponto_folha_pk = $this->getPontoFolhaPk($arrDados[0]['leads_pk'],$arrDados[0]['colaborador_pk'],$arrDados[0]['ic_mes'],$arrDados[0]['ic_ano']);

                
                //Salva o ponto colaborador
                //SÓ FAZ O CADASTRO PARA OS COLABORADORES QUE NÃO TEM FOLHA CADASTRADA PARA O PERIODO 
                $countColaborador = $this->verificarFolhaColaborador($primeiroDiaMesAtual,$ultimoDiaMesAtual,$arrDados[0]['colaborador_pk'],$ponto_folha_pk);
                    
                if(count($countColaborador)==0){
                    $fields = array();
                    $fields['ponto_folha_pk'] = $ponto_folha_pk;
                    $fields['colaborador_pk'] = $arrDados[0]['colaborador_pk'];
                    
                    
                    //$fields['agenda_colaborador_padrao_pk'] = $agenda_colaborador_padrao_pk;

                    $fields["dt_ult_atualizacao"] = "sysdate()";
                    $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                    $fields["dt_cadastro"] = "sysdate()";
                    $fields["usuario_cadastro_pk"] = $_SESSION['session_user']['par1'];
                    $fields["ic_status"] =$arrDados[0]['ic_status_ponto_folha_pk'];

                    // Insere o colaborador na tabela `ponto_folha_colaborador`
                    $ponto_folha_colaborador_pk = Util::execInsert("ponto_folha_colaborador", $fields, $this->pdo);
                    $whereRegistros = " colaborador_pk =".$arrDados[0]['colaborador_pk'];
                    $whereRegistros.= " and ponto_folha_pk =".$ponto_folha_pk;

                    Util::execDelete('ponto_folha_registros', $whereRegistros, $this->pdo);
                    for($i=0;$i<count($arrDados);$i++){

                        $fields = array();
                        if($arrDados[$i]['dt_hora_ponto']!=""){
                            $fields['dt_hora_ponto'] = Util::DataYMD($arrDados[$i]['dt_hora_ponto']);
                        }
                        
                        $fields['colaborador_pk'] = $arrDados[$i]['colaborador_pk'];
                        $fields['ponto_folha_pk'] = $ponto_folha_pk;
                        if(isset($arrDados[$i]['ic_status']) && $arrDados[$i]['ic_status']==0){
                            $fields['ic_status'] = 1;
                        }
                        if($arrDados[$i]['hr_ini_expediente']!=""){
                            $fields['hr_ini_expediente'] = $arrDados[$i]['hr_ini_expediente'];
                        }
                        else{
                            $fields['hr_ini_expediente'] = "";
                        }
                        if($arrDados[$i]['hr_ini_intervalo']!=""){
                            $fields['hr_ini_intervalo'] = $arrDados[$i]['hr_ini_intervalo'];
                        }
                        else{
                            $fields['hr_ini_intervalo'] = "";
                        }
                        
                        if($arrDados[$i]['hr_fim_intervalo']!=""){
                            $fields['hr_fim_intervalo'] = $arrDados[$i]['hr_fim_intervalo'];
                        }
                        else{
                            $fields['hr_fim_intervalo'] = "";
                        }
                        
                        if($arrDados[$i]['hr_fim_expediente']!=""){
                            $fields['hr_fim_expediente'] = $arrDados[$i]['hr_fim_expediente'];
                        }
                        else{
                            $fields['hr_fim_expediente'] = "";
                        }
                        
                        $fields['hr_trabalhadas'] = $arrDados[$i]['hr_trabalhadas'];
                        $fields['hr_excedente'] = $arrDados[$i]['hr_excedentes'];
                        $fields['hr_faltantes'] = $arrDados[$i]['hr_faltantes'];     
                
                        $fields["dt_ult_atualizacao"] = "sysdate()";
                        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                        
                        $pk = Util::execInsert("ponto_folha_registros", $fields, $this->pdo);
                        $retorno->status = true;
                        $retorno->message = 'Dados atualizado com sucesso';
                        $retorno->data = $pk;
                    }

                    
                }
                else{
                    //if($arrDados[0]['ic_status_ponto_folha_pk']!=1){
                        Util::execDelete('ponto_folha_colaborador', ' pk='.$countColaborador[0]['pk'], $this->pdo);
                        $fields = array();
                        $fields['ponto_folha_pk'] = $ponto_folha_pk;
                        $fields['colaborador_pk'] = $arrDados[0]['colaborador_pk'];
                        //$fields['agenda_colaborador_padrao_pk'] = $agenda_colaborador_padrao_pk;

                        $fields["dt_ult_atualizacao"] = "sysdate()";
                        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                        $fields["dt_cadastro"] = "sysdate()";
                        $fields["usuario_cadastro_pk"] = $_SESSION['session_user']['par1'];
                        $fields["ic_status"] =$arrDados[0]['ic_status_ponto_folha_pk'];
                        // Insere o colaborador na tabela `ponto_folha_colaborador`
                        $ponto_folha_colaborador_pk = Util::execInsert("ponto_folha_colaborador", $fields, $this->pdo);
                        $whereRegistros = " colaborador_pk =".$arrDados[0]['colaborador_pk'];
                        $whereRegistros.= " and ponto_folha_pk =".$ponto_folha_pk;

                        Util::execDelete('ponto_folha_registros', $whereRegistros, $this->pdo);
                        for($i=0;$i<count($arrDados);$i++){

                            $fields = array();
                            if($arrDados[$i]['dt_hora_ponto']!=""){
                                $fields['dt_hora_ponto'] = Util::DataYMD($arrDados[$i]['dt_hora_ponto']);
                            }
                            
                            $fields['colaborador_pk'] = $arrDados[0]['colaborador_pk'];
                            $fields['ponto_folha_pk'] = $ponto_folha_pk;
                            if(isset($arrDados[$i]['ic_status']) && $arrDados[$i]['ic_status']==0){
                                $fields['ic_status'] = 1;
                            }
                            if($arrDados[$i]['hr_ini_expediente']!=""){
                                $fields['hr_ini_expediente'] = $arrDados[$i]['hr_ini_expediente'];
                            }
                            else{
                                $fields['hr_ini_expediente'] = "";
                            }
                            if($arrDados[$i]['hr_ini_intervalo']!=""){
                                $fields['hr_ini_intervalo'] = $arrDados[$i]['hr_ini_intervalo'];
                            }
                            else{
                                $fields['hr_ini_intervalo'] = "";
                            }
                            
                            if($arrDados[$i]['hr_fim_intervalo']!=""){
                                $fields['hr_fim_intervalo'] = $arrDados[$i]['hr_fim_intervalo'];
                            }
                            else{
                                $fields['hr_fim_intervalo'] = "";
                            }
                            
                            if($arrDados[$i]['hr_fim_expediente']!=""){
                                $fields['hr_fim_expediente'] = $arrDados[$i]['hr_fim_expediente'];
                            }
                            else{
                                $fields['hr_fim_expediente'] = "";
                            }
                            
                            $fields['hr_trabalhadas'] = $arrDados[$i]['hr_trabalhadas'];
                            $fields['hr_excedente'] = $arrDados[$i]['hr_excedentes'];
                            $fields['hr_faltantes'] = $arrDados[$i]['hr_faltantes'];     
                    
                            $fields["dt_cadastro"] = "sysdate()";
                            $fields["dt_ult_atualizacao"] = "sysdate()";
                            $fields["usuario_cadastro_pk"] = $_SESSION['session_user']['par1'];
                            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                            
                            $pk = Util::execInsert("ponto_folha_registros", $fields, $this->pdo);
                            $retorno->status = true;
                            $retorno->message = 'Dados atualizado com sucesso';
                            $retorno->data = $pk;
                        }
                    /*}
                    else{
                        $retorno->status = true;
                        $retorno->message = 'Dados atualizado com sucesso';
                        $retorno->data = [];
                    }*/
                    

                }
            //}
            /*else{
                $retorno->status = true;
                $retorno->message = 'Folha fechada não pode atualizar';
                $retorno->data = [];
            }*/
            
            
        }
        catch(Throwable $e){
            print_r($e->getMessage());
            die();
        }
        

        return $retorno;

    }
    public function finalizarFolhaByReloginho($arrDados){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
       
        try{
            $arrDados = json_decode($arrDados, true);
            if (!is_array($arrDados) || count($arrDados) == 0) {
                $retorno->message = 'Nenhum registro informado para finalizar a folha';
                return $retorno;
            }
            if (empty($arrDados[0]['ic_mes']) || empty($arrDados[0]['ic_ano'])) {
                $dtInicio = isset($arrDados[0]['dt_inicio']) ? $arrDados[0]['dt_inicio'] : "";
                if (!empty($dtInicio)) {
                    $dtInicioYmd = strpos($dtInicio, '/') !== false ? Util::DataYMD($dtInicio) : $dtInicio;
                    $arrDados[0]['ic_mes'] = date("m", strtotime($dtInicioYmd));
                    $arrDados[0]['ic_ano'] = date("Y", strtotime($dtInicioYmd));
                }
            }
            $p1 = $arrDados[0]['ic_ano']."-"+$arrDados[0]['ic_mes']."-01";
            // Primeiro dia do mês atual
            $primeiroDiaMesAtual = date("Y-m-d", strtotime($p1));

            // Último dia do mês atual
            $ultimoDiaMesAtual = date("Y-m-t", strtotime($p1));

          
            //if($arrDados[0]['ic_status_ponto_folha_pk']!=1){
                //Verificar se existe a folha 
                $ponto_folha_pk = $this->getPontoFolhaPk($arrDados[0]['leads_pk'],$arrDados[0]['colaborador_pk'],$arrDados[0]['ic_mes'],$arrDados[0]['ic_ano']);

                
                //Salva o ponto colaborador
                //SÓ FAZ O CADASTRO PARA OS COLABORADORES QUE NÃO TEM FOLHA CADASTRADA PARA O PERIODO 
                $countColaborador = $this->verificarFolhaColaborador($primeiroDiaMesAtual,$ultimoDiaMesAtual,$arrDados[0]['colaborador_pk'],$ponto_folha_pk);
                    
                if(count($countColaborador)==0){
                    $fields = array();
                    $fields['ponto_folha_pk'] = $ponto_folha_pk;
                    $fields['colaborador_pk'] = $arrDados[0]['colaborador_pk'];
                    
                    
                    //$fields['agenda_colaborador_padrao_pk'] = $agenda_colaborador_padrao_pk;

                    $fields["dt_ult_atualizacao"] = "sysdate()";
                    $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                    $fields["dt_cadastro"] = "sysdate()";
                    $fields["usuario_cadastro_pk"] = $_SESSION['session_user']['par1'];
                    $fields["ic_status"] =1;

                    // Insere o colaborador na tabela `ponto_folha_colaborador`
                    $ponto_folha_colaborador_pk = Util::execInsert("ponto_folha_colaborador", $fields, $this->pdo);
                    $whereRegistros = " colaborador_pk =".$arrDados[0]['colaborador_pk'];
                    $whereRegistros.= " and ponto_folha_pk =".$ponto_folha_pk;

                    Util::execDelete('ponto_folha_registros', $whereRegistros, $this->pdo);
                    for($i=0;$i<count($arrDados);$i++){

                        $fields = array();
                        if($arrDados[$i]['dt_hora_ponto']!=""){
                            $fields['dt_hora_ponto'] = Util::DataYMD($arrDados[$i]['dt_hora_ponto']);
                        }
                        
                        $fields['colaborador_pk'] = $arrDados[$i]['colaborador_pk'];
                        $fields['ponto_folha_pk'] = $ponto_folha_pk;
                        $fields['ic_status'] = 1;
                        
                        if($arrDados[$i]['hr_ini_expediente']!=""){
                            $fields['hr_ini_expediente'] = $arrDados[$i]['hr_ini_expediente'];
                        }
                        else{
                            $fields['hr_ini_expediente'] = "";
                        }
                        if($arrDados[$i]['hr_ini_intervalo']!=""){
                            $fields['hr_ini_intervalo'] = $arrDados[$i]['hr_ini_intervalo'];
                        }
                        else{
                            $fields['hr_ini_intervalo'] = "";
                        }
                        
                        if($arrDados[$i]['hr_fim_intervalo']!=""){
                            $fields['hr_fim_intervalo'] = $arrDados[$i]['hr_fim_intervalo'];
                        }
                        else{
                            $fields['hr_fim_intervalo'] = "";
                        }
                        
                        if($arrDados[$i]['hr_fim_expediente']!=""){
                            $fields['hr_fim_expediente'] = $arrDados[$i]['hr_fim_expediente'];
                        }
                        else{
                            $fields['hr_fim_expediente'] = "";
                        }
                        
                        $fields['hr_trabalhadas'] = $arrDados[$i]['hr_trabalhadas'];
                        $fields['hr_excedente'] = $arrDados[$i]['hr_excedentes'];
                        $fields['hr_faltantes'] = $arrDados[$i]['hr_faltantes'];     
                
                        $fields["dt_ult_atualizacao"] = "sysdate()";
                        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                        
                        $pk = Util::execInsert("ponto_folha_registros", $fields, $this->pdo);
                        $retorno->status = true;
                        $retorno->message = 'Dados atualizado com sucesso';
                        $retorno->data = $pk;
                    }

                    
                }
                else{
                    //if($arrDados[0]['ic_status_ponto_folha_pk']!=1){
                        Util::execDelete('ponto_folha_colaborador', ' pk='.$countColaborador[0]['pk'], $this->pdo);
                        $fields = array();
                        $fields['ponto_folha_pk'] = $ponto_folha_pk;
                        $fields['colaborador_pk'] = $arrDados[0]['colaborador_pk'];
                        //$fields['agenda_colaborador_padrao_pk'] = $agenda_colaborador_padrao_pk;

                        $fields["dt_ult_atualizacao"] = "sysdate()";
                        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                        $fields["dt_cadastro"] = "sysdate()";
                        $fields["usuario_cadastro_pk"] = $_SESSION['session_user']['par1'];
                        $fields["ic_status"] =$arrDados[0]['ic_status_ponto_folha_pk'];
                        // Insere o colaborador na tabela `ponto_folha_colaborador`
                        $ponto_folha_colaborador_pk = Util::execInsert("ponto_folha_colaborador", $fields, $this->pdo);
                        $whereRegistros = " colaborador_pk =".$arrDados[0]['colaborador_pk'];
                        $whereRegistros.= " and ponto_folha_pk =".$ponto_folha_pk;

                        Util::execDelete('ponto_folha_registros', $whereRegistros, $this->pdo);
                        for($i=0;$i<count($arrDados);$i++){

                            $fields = array();
                            if($arrDados[$i]['dt_hora_ponto']!=""){
                                $fields['dt_hora_ponto'] = Util::DataYMD($arrDados[$i]['dt_hora_ponto']);
                            }
                            
                            $fields['colaborador_pk'] = $arrDados[0]['colaborador_pk'];
                            $fields['ponto_folha_pk'] = $ponto_folha_pk;
                            if(isset($arrDados[$i]['ic_status']) && $arrDados[$i]['ic_status']==0){
                                $fields['ic_status'] = 1;
                            }
                            if($arrDados[$i]['hr_ini_expediente']!=""){
                                $fields['hr_ini_expediente'] = $arrDados[$i]['hr_ini_expediente'];
                            }
                            else{
                                $fields['hr_ini_expediente'] = "";
                            }
                            if($arrDados[$i]['hr_ini_intervalo']!=""){
                                $fields['hr_ini_intervalo'] = $arrDados[$i]['hr_ini_intervalo'];
                            }
                            else{
                                $fields['hr_ini_intervalo'] = "";
                            }
                            
                            if($arrDados[$i]['hr_fim_intervalo']!=""){
                                $fields['hr_fim_intervalo'] = $arrDados[$i]['hr_fim_intervalo'];
                            }
                            else{
                                $fields['hr_fim_intervalo'] = "";
                            }
                            
                            if($arrDados[$i]['hr_fim_expediente']!=""){
                                $fields['hr_fim_expediente'] = $arrDados[$i]['hr_fim_expediente'];
                            }
                            else{
                                $fields['hr_fim_expediente'] = "";
                            }
                            
                            $fields['hr_trabalhadas'] = $arrDados[$i]['hr_trabalhadas'];
                            $fields['hr_excedente'] = $arrDados[$i]['hr_excedentes'];
                            $fields['hr_faltantes'] = $arrDados[$i]['hr_faltantes'];     
                    
                            $fields["dt_cadastro"] = "sysdate()";
                            $fields["dt_ult_atualizacao"] = "sysdate()";
                            $fields["usuario_cadastro_pk"] = $_SESSION['session_user']['par1'];
                            $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
                            
                            $pk = Util::execInsert("ponto_folha_registros", $fields, $this->pdo);
                            $retorno->status = true;
                            $retorno->message = 'Dados atualizado com sucesso';
                            $retorno->data = $pk;
                        }
                    /*}
                    else{
                        $retorno->status = true;
                        $retorno->message = 'Dados atualizado com sucesso';
                        $retorno->data = [];
                    }*/
                    

                }
            //}
            /*else{
                $retorno->status = true;
                $retorno->message = 'Folha fechada não pode atualizar';
                $retorno->data = [];
            }*/
            
            
        }
        catch(Throwable $e){
            print_r($e->getMessage());
            die();
        }
        

        return $retorno;

    }

}
