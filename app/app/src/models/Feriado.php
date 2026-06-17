<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;
use Throwable;

class Feriado {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    
    public function excluir($pk){
        Util::execDelete('feriados', ' pk='.$pk, $this->pdo);
    }
    

    public function listarGrid($data){
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

        $search = "";
        if (isset($_GET['search']['value']) and $_GET['search']['value'] != '') {
            $pesq = $_GET['search']['value'];
            $search .= " AND (
                            nome LIKE '%".$pesq."%' OR
                            estado LIKE '%".$pesq."%' 
                            cidade LIKE '%".$pesq."%' 
                            date_format(data,'%d/%m/%Y') LIKE '%".$pesq."%' 
                        )";
        }
        
        $sql ="";
        $sql.="select pk ,dt_cadastro, usuario_cadastro_pk";
        $sql.="       ,nome ";
        $sql.="       ,estado ";
        $sql.="       ,cidade ";
        $sql.="       ,case tipo when 1 then 'Nacional' when 2 then 'Estadual' when 3 then 'Municipal' end tipo ";
        $sql.="       ,date_format(data,'%d/%m/%Y')data_feriado ";

        $sql.="  from feriados ";
        $sql.=" where 1=1 ";
        $sql.=$search;
        if($data['nome'] != ""){
            $sql.=" and nome like '%".$data['nome']."%' ";
        }
        if($data['cidade'] != ""){
            $sql.=" and cidade like '%".$data['cidade']."%' ";
        }
        if($data['estado'] != ""){
            $sql.=" and estado = '".$data['estado']."'";
        }
        if($data['tipo'] != ""){
            $sql.=" and tipo = ".$data['tipo'];
        }
        if($data['data'] != ""){
            $sql.=" and data = '".Util::DataYMD($data['data'])."'";
        }
        $sql.=" order by data_feriado asc ";

        $stmt = $this->pdo->prepare( $sql.$lengthSql );
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
    public function listarFeriadoRelogio($data){

        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio
            $dt_inicio = $data['dt_inicio'];
            $dt_fim = $data['dt_fim'];
            $colaborador_pk = $data['colaborador_pk'];


            // Primeiro dia do mês atual
            //$primeiroDiaMesAtual = date("Y-m-d", strtotime("$ic_ano-$ic_mes-01"));

            // Último dia do mês atual
            //$ultimoDiaMesAtual = date("Y-m-t", strtotime("$ic_ano-$ic_mes-01"));

            $primeiroDiaMesAtual = Util::DataYMD($dt_inicio);
            $ultimoDiaMesAtual = Util::DataYMD($dt_fim);
            // Verifica se tem apontamento de feriado para esse colaborador no período
            $sqlc = "SELECT af.feriado_pk 
            FROM agenda_colaborador_apontamento a 
            INNER JOIN apontamento_folga af ON af.agenda_colaborador_apontamento_pk = a.pk
            WHERE a.colaborador_pk = :colaborador_pk
            AND af.dt_folga BETWEEN :primeiroDiaMesAtual AND :ultimoDiaMesAtual";

            $stmt = $this->pdo->prepare($sqlc);
            $stmt->execute([
            ':colaborador_pk' => $colaborador_pk,
            ':primeiroDiaMesAtual' => $primeiroDiaMesAtual,
            ':ultimoDiaMesAtual' => $ultimoDiaMesAtual
            ]);
            $rowsc = $stmt->fetchAll(\PDO::FETCH_COLUMN); // Retorna um array com os feriado_pk

            // Monta a cláusula NOT IN
            $notInClause = "";
            if (!empty($rowsc)) {
                $notInClause = " AND f.pk NOT IN (" . implode(",", array_map('intval', $rowsc)) . ")";
            }

            // Segunda query para buscar os feriados
            $sql = "SELECT f.pk, f.dt_cadastro, f.usuario_cadastro_pk, 
                f.nome, f.estado, f.cidade, 
                CASE f.tipo WHEN 1 THEN 'Nacional' WHEN 2 THEN 'Estadual' WHEN 3 THEN 'Municipal' END AS tipo,
                DATE_FORMAT(f.data, '%d/%m/%Y') AS data_feriado
            FROM feriados f
            WHERE f.data BETWEEN :primeiroDiaMesAtual AND :ultimoDiaMesAtual
            $notInClause
            ORDER BY f.data ASC"; // Evita duplicação de DATE_FORMAT

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
            ':primeiroDiaMesAtual' => $primeiroDiaMesAtual,
            ':ultimoDiaMesAtual' => $ultimoDiaMesAtual
            ]);

            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $retorno->status = true;
            $retorno->message = 'Dados carregados com sucesso';
            $retorno->data = $rows;
            $retorno->iTotalDisplayRecords = count($rows);
            $retorno->iTotalRecords = count($rows);

            echo json_encode($retorno);
            exit(0);
        }
        catch(Throwable $e){
            print_r($e->getMessage());
            die();
        }
        
    }
    public function salvar($data){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['nome'] = $data['nome'];
        $fields['tipo'] = $data['tipo'];
        $fields['cidade'] = $data['cidade'];
        $fields['estado'] = $data['estado'];
        $fields['data'] = Util::DataYMD($data['data']);
        $fields["dt_cadastro"] = "sysdate()";
        $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];


        $pk = Util::execInsert("feriados", $fields,$this->pdo);
        $retorno->status = true;
        $retorno->message = 'Dados cadastrados com sucesso';
        $retorno->data = $pk;
       
        return $retorno;

    }
}
