<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class Grupo {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    
    public function excluir($pk){
        Util::execDelete('modulos_grupos', ' grupos_pk='.$pk, $this->pdo);
        Util::execDelete('grupos', ' pk='.$pk, $this->pdo);
    }
    public function excluirGruposModulosPk($pk){
        Util::execDelete('modulos_grupos', ' grupos_pk='.$pk, $this->pdo);
    }

    public function listarTodos(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_grupo ";
        $sql.="  from grupos ";
        $sql.=" where 1=1 ";
        

        $sql.=" order by ds_grupo asc ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }

    public function listarGrid($ds_grupo){
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
        $sql.="select pk t_pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_grupo t_ds_grupo ";

        $sql.="  from grupos ";
        $sql.=" where 1=1 ";
        if($ds_grupo != ""){
            $sql.=" and ds_grupo like '%".$ds_grupo."%' ";
        }
        $sql.=" order by ds_grupo asc ";

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

    public function listarPermissoesGrupo($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select modulos_pk t_modulos_pk, ic_ins t_ic_ins, ic_upd t_ic_upd, ic_del t_ic_del, ic_cons t_ic_cons";
        $sql.="  from modulos_grupos mg ";
        $sql.=" where grupos_pk = $pk ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }

    public function listarPorPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql.="       ,ds_grupo ";

        $sql.="  from grupos ";
        $sql.=" where pk = $pk ";
       
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }

    public function salvar($grupo){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_grupo'] = $grupo['ds_grupo'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($grupo['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];


            $pk = Util::execInsert("grupos", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("grupos", $fields, " pk = ".$grupo['pk'],$this->pdo);
            $pk = $grupo['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;

    }

    public function adicionarGruposModulos($grupo_pk, $modulos_pk, $ic_ins, $ic_upd, $ic_del, $ic_cons)
    {
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['grupos_pk'] = $grupo_pk;
        $fields['modulos_pk'] = $modulos_pk;
        $fields['ic_ins'] = $ic_ins;
        $fields['ic_upd'] = $ic_upd;
        $fields['ic_del'] = $ic_del;
        $fields['ic_cons'] = $ic_cons;

        $pk = Util::execInsert("modulos_grupos", $fields,$this->pdo);
        $retorno->status = true;
        $retorno->message = 'Dados cadastrados com sucesso';
        $retorno->data = $pk;

        return $retorno;
    }


}
