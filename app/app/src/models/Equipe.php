<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;

class Equipe {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function excluir($pk){
        Util::execDelete('equipes', ' pk='.$pk, $this->pdo);
    }

    public function excluirEquipeUsuario($pk){
        Util::execDelete('equipes_usuarios', ' equipes_pk='.$pk, $this->pdo);
    }

    public function salvar($equipe){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_equipe'] = $equipe['ds_equipe'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($equipe['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];

            $pk = Util::execInsert("equipes", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("equipes", $fields, " pk = ".$equipe['pk'],$this->pdo);
            $pk = $equipe['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;
    }

    public function adicionarEquipesUsuarios($equipes_pk,$usuarios_pk,$ic_bko,$ic_supervisor){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields["equipes_pk"] = $equipes_pk;
        $fields["usuarios_pk"] = $usuarios_pk;
        $fields["ic_bko"] = $ic_bko;
        $fields["ic_supervisor"] = $ic_supervisor;

        $pk = Util::execInsert("equipes_usuarios", $fields,$this->pdo);
        $retorno->status = true;
        $retorno->message = 'Dados cadastrados com sucesso';
        $retorno->data = $pk;

        return $retorno;
    }

    public function listarGrid($ds_equipe){
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
        $sql.="       ,ds_equipe ";

        $sql.="  from equipes ";
        $sql.=" where 1=1 ";
        if($ds_equipe != ""){
            $sql.=" and ds_equipe like '%".$ds_equipe."%' ";
        }
        $sql.=" order by ds_equipe asc ";

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

    public function listar_por_ds_equipe($ds_equipe){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_equipe ";

        $sql.="  from equipes ";
        $sql.=" where 1=1 ";
        if($ds_equipe != ""){
            $sql.=" and ds_equipe like '%".$ds_equipe."%' ";
        }
        $sql.=" order by ds_equipe asc ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }

    public function listarEquipePorUsuario($solicitante_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        if($solicitante_pk!=""){
            $sql ="";
            $sql.="Select equipes_usuarios.equipes_pk from equipes_usuarios where equipes_usuarios.usuarios_pk=$solicitante_pk";

            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $query0 = $stmt->fetchAll(\PDO::FETCH_ASSOC);


            if(count($query0)>0){
                if($query0[0]["equipes_pk"]!=""){
                    $query = $this->listarResponsavelEquipe($query0[0]["equipes_pk"]);

                    if(count($query) > 0){
                        for($i = 0; $i < count($query); $i++){
                            $mysql_data[] = array(
                                "usuario_aprovacao_pk" => $query[$i]["usuario_aprovacao_pk"],
                                "ds_usuaario_aprovacao" => $query[$i]["ds_usuaario_aprovacao"]
                            );
                        }
                    }
                    else{
                        $mysql_data = [];
                    }
                }else{
                    $mysql_data []= array(
                        "usuario_aprovacao_pk" => 0
                    );
                }
            }else{
                $mysql_data []= array(
                    "usuario_aprovacao_pk" => 0
                );
            }
        }
        else{
            $mysql_data []= array(
                "usuario_aprovacao_pk" => ""
            );
        }

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $mysql_data;

        return $retorno;
    }

    public function listarResponsavelEquipe($equipe_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="SELECT u.pk usuario_aprovacao_pk,";
        $sql.="       concat(u.ds_usuario, ' - ', e.ds_equipe) ds_usuaario_aprovacao";
        $sql.=" FROM equipes e";
        $sql.="     INNER JOIN equipes_usuarios eu ON e.pk = eu.equipes_pk";
        $sql.="     INNER JOIN usuarios u ON eu.usuarios_pk = u.pk";
        $sql.=" WHERE e.pk=".$equipe_pk;
        $sql.=" AND eu.ic_bko = 1 OR eu.ic_supervisor = 1";
        
        $sql.=" ORDER BY u.ds_usuario, e.ds_equipe";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno->data;
    }  

    public function listarPorPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql.="       ,ds_equipe ";

        $sql.="  from equipes ";
        $sql.=" where pk = $pk ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }

    public function listar_usuarios_equipe($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select e.pk, e.dt_cadastro, e.usuario_cadastro_pk, e.dt_ult_atualizacao, e.usuario_ult_atualizacao_pk ";
        $sql.="       ,e.ds_equipe ";
        $sql.="       ,eu.usuarios_pk";
        $sql.="       ,eu.ic_bko";
        $sql.="       ,eu.ic_supervisor";

        $sql.="  from equipes e";
        $sql.="       inner join equipes_usuarios eu on e.pk = eu.equipes_pk";
        $sql.=" where 1=1 ";
        if($pk != ""){
            $sql.=" and eu.equipes_pk=".$pk;
        }
        $sql.=" order by e.ds_equipe asc ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }
    public function listarEquipeUsuarioLogado(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select e.pk, e.dt_cadastro, e.usuario_cadastro_pk, e.dt_ult_atualizacao, e.usuario_ult_atualizacao_pk  ";
        $sql.="       ,e.ds_equipe ";
        $sql.="       ,u.grupos_pk";

        $sql.="  from equipes e";
        $sql.="       left join equipes_usuarios eu on e.pk = eu.equipes_pk";
        $sql.="       left join usuarios u on u.pk = eu.usuarios_pk";
        if($_SESSION['session_user']['par10']!=1){
            $sql.=" where eu.usuarios_pk = ".$_SESSION['session_user']['par1'];
        }

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }

    


}
