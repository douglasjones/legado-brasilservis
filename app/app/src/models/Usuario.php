<?php

namespace App\Model;

use App\Utils\Session;
use App\Utils\Util;
use App\Utils\Validation;

class Usuario {

	public $pdo;

	public function __construct($pdo) {
		$this->pdo = $pdo;
	}

    public function excluir($pk){
        Util::execDelete('usuarios', ' pk='.$pk, $this->pdo);
    }

    public function salvar($usuario){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $fields = array();
        $fields['ds_usuario'] = $usuario['ds_usuario'];
        $fields['ds_login'] = $usuario['ds_login'];
        $fields['ds_senha'] = $usuario['ds_senha'];
        $fields['ds_email'] = $usuario['ds_email'];
        $fields['ds_cel'] = $usuario['ds_cel'];
        $fields['ic_status'] = $usuario['ic_status'];
        $fields['grupos_pk'] = $usuario['grupos_pk'];
        $fields['leads_pk'] = $usuario['leads_pk'];
        $fields['contas_pk'] = $usuario['contas_pk'];

        $fields["dt_ult_atualizacao"] = "sysdate()";
        $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];

        if($usuario['pk']  == ""){

            $fields["dt_cadastro"] = "sysdate()";
            $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];


            $pk = Util::execInsert("usuarios", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;
        }
        else{
            Util::execUpdate("usuarios", $fields, " pk = ".$usuario['pk'],$this->pdo);
            $pk = $usuario['pk'];
            $retorno->status = true;
            $retorno->message = 'Dados atualizado com sucesso';
            $retorno->data = $pk;
        }
        return $retorno;

    }

    public function listar_supervisor($ds_supervisor){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_usuario ";
        $sql.="       ,ds_login ";
        $sql.="       ,ds_senha ";
        $sql.="       ,ds_email ";
        $sql.="       ,ds_cel ";
        $sql.="       ,ic_status ";
        $sql.="       ,grupos_pk ";
        $sql.="       ,leads_pk";

        $sql.="  from usuarios ";
        //$sql.="       inner join equipes_usuarios eu on u.pk = eu.usuarios_pk";
        $sql.=" where 1=1 ";
        $sql.="     and grupos_pk = 3";
        //$sql.="     and eu.ic_supervisor = 1";
        $sql.=" order by ds_usuario asc ";


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function listarPorPk($pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select u.pk, u.dt_cadastro, u.usuario_cadastro_pk, u.dt_ult_atualizacao, u.usuario_ult_atualizacao_pk  ";
        $sql.="       ,u.ds_usuario ";
        $sql.="       ,u.ds_login ";
        $sql.="       ,u.ds_senha ";
        $sql.="       ,u.ds_email ";
        $sql.="       ,u.ds_cel ";
        $sql.="       ,u.ic_status ";
        $sql.="       ,u.grupos_pk ";
        $sql.="       ,u.leads_pk";
        $sql.="       ,u.contas_pk";
        $sql.="       ,up.pk usuario_ponto_pk";
        $sql.="       ,up.hr_entrada_dom";
        $sql.="       ,up.hr_saida_dom";
        $sql.="       ,up.hr_entrada_seg";
        $sql.="       ,up.hr_saida_seg";
        $sql.="       ,up.hr_entrada_ter";
        $sql.="       ,up.hr_saida_ter";
        $sql.="       ,up.hr_entrada_qua";
        $sql.="       ,up.hr_saida_qua";
        $sql.="       ,up.hr_entrada_qui";
        $sql.="       ,up.hr_saida_qui";
        $sql.="       ,up.hr_entrada_sex";
        $sql.="       ,up.hr_saida_sex";
        $sql.="       ,up.hr_entrada_sab";
        $sql.="       ,up.hr_saida_sab";
        $sql.="       ,up.ic_dom";
        $sql.="       ,up.ic_seg";
        $sql.="       ,up.ic_ter";
        $sql.="       ,up.ic_qua";
        $sql.="       ,up.ic_qui";
        $sql.="       ,up.ic_sex";
        $sql.="       ,up.ic_sab";
        $sql.="       ,up.turnos_pk_dom";
        $sql.="       ,up.turnos_pk_seg";
        $sql.="       ,up.turnos_pk_ter";
        $sql.="       ,up.turnos_pk_qua";
        $sql.="       ,up.turnos_pk_qui";
        $sql.="       ,up.turnos_pk_sex";
        $sql.="       ,up.turnos_pk_sab";

        $sql.="  from usuarios u";
        $sql.="  left join usuario_ponto up on u.pk = up.usuarios_pk";
        $sql.=" where u.pk = $pk ";


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }


    public function listar_por_ds_usuario_ativo($ds_usuario){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_usuario ";
        $sql.="       ,ds_login ";
        $sql.="       ,ds_senha ";
        $sql.="       ,ds_email ";
        $sql.="       ,ds_cel ";
        $sql.="       ,ic_status ";
        $sql.="       ,grupos_pk ";
        $sql.="       ,leads_pk";

        $sql.="  from usuarios ";
        $sql.=" where 1=1 ";
        if($ds_usuario!=""){
            $sql.=" and ds_usuario like '%".$ds_usuario."%'";
        }
        $sql.=" and ic_status = 1";
        $sql.=" order by ds_usuario asc ";


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listarUsuarioLogado(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk  ";
        $sql.="       ,ds_usuario ";
        $sql.="       ,ds_login ";
        $sql.="       ,ds_senha ";
        $sql.="       ,ds_email ";
        $sql.="       ,ds_cel ";
        $sql.="       ,ic_status ";
        $sql.="       ,grupos_pk ";
        $sql.="       ,colaboradores_pk";
        $sql.="       ,leads_pk";
        $sql.="  from usuarios ";
        $sql.=" where pk =".$_SESSION['session_user']['par1'];


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows[0];
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function listarTodosSemAdm(){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select pk, dt_cadastro, usuario_cadastro_pk, dt_ult_atualizacao, usuario_ult_atualizacao_pk ";
        $sql.="       ,ds_usuario ";
        $sql.="       ,ds_login ";
        $sql.="       ,ds_senha ";
        $sql.="       ,ds_email ";
        $sql.="       ,ds_cel ";
        $sql.="       ,ic_status ";
        $sql.="       ,grupos_pk ";
        $sql.="       ,leads_pk";

        $sql.="  from usuarios ";
        $sql.=" where 1=1 ";
        $sql.=" and ic_status = 1";
        $sql.=" and pk not in (1)";
        $sql.=" order by ds_usuario asc ";


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function verificarPermissao($ds_dominio_modulo,$ic_acao){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select count('0') total ";
        $sql.="  from usuarios u ";
        $sql.="	   inner join grupos g on u.grupos_pk = g.pk ";
        $sql.="	   inner join modulos_grupos mg on mg.grupos_pk = g.pk ";
        $sql.="       inner join modulos m on mg.modulos_pk = m.pk ";
        $sql.=" where u.pk = ".$_SESSION['session_user']['par1'];
        $sql.="   and m.ds_dominio = '".$ds_dominio_modulo."' ";

        if($ic_acao == "ins"){
            $sql.=" and mg.ic_ins = 1 ";
        }
        else if($ic_acao == "cons"){
            $sql.=" and mg.ic_cons = 1 ";
        }
        else if($ic_acao == "upd"){
            $sql.=" and mg.ic_upd = 1 ";
        }
        else if($ic_acao == "del"){
            $sql.=" and mg.ic_del = 1 ";
        }
        //SE NÃO TIVER NENHUMA DAS AÇÕES FORÇAR SEM PERMISSAO
        else{
            $sql.=" and mg.ic_del = 3 ";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = ($rows[0]['total']);
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }
    public function verificarPermissaoMenu($ds_dominio_modulo,$ic_acao){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select count('0') total ";
        $sql.="  from usuarios u ";
        $sql.="	   inner join grupos g on u.grupos_pk = g.pk ";
        $sql.="	   left join modulos_grupos mg on mg.grupos_pk = g.pk ";
        $sql.="       left join modulos m on mg.modulos_pk = m.pk ";
        $sql.=" where u.pk = ".$_SESSION['session_user']['par1'];
        $sql.="   and m.ds_dominio = '".$ds_dominio_modulo."' ";

        if($ic_acao == "ins"){
            $sql.=" and mg.ic_ins = 1 ";
        }
        else if($ic_acao == "cons"){
            $sql.=" and mg.ic_cons = 1 ";
        }
        else if($ic_acao == "upd"){
            $sql.=" and mg.ic_upd = 1 ";
        }
        else if($ic_acao == "del"){
            $sql.=" and mg.ic_del = 1 ";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if($rows[0]['total'] > 0){
            $retorno->data = true;
        }
        else{
            $retorno->data = false;
        }
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;
    }

    public function listarAdmSistema(){    
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";
        $sql.="select u.pk usuario_aprovacao_pk, ";
        $sql.=" u.ds_usuario ds_usuaario_aprovacao";
        $sql.="  from usuarios u ";
        $sql.=" inner join grupos g on u.grupos_pk = g.pk";
        $sql.=" where 1=1 ";
        $sql.=" and g.ds_grupo in ('Controller')";
        $sql.=" and u.ic_status = 1";
        $sql.=" and u.pk not in (1)";
        $sql.=" order by u.ds_usuario asc ";
 
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;  
}

    public function listarGrid($ds_usuario,$ic_status,$contas_pk,$grupos_pk){
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
                            u.pk LIKE '%".$pesq."%' OR
                            u.ds_usuario LIKE '%".$pesq."%' OR
                            u.ds_login LIKE '%".$pesq."%' OR
                            g.ds_grupo LIKE '%".$pesq."%' OR
                            u.ds_email LIKE '%".$pesq."%' OR
                            u.ds_cel LIKE '%".$pesq."%' 
                        )";
        }
        

        $sql ="";
        $sql.="select u.pk t_pk, u.dt_cadastro, u.usuario_cadastro_pk, u.dt_ult_atualizacao, u.usuario_ult_atualizacao_pk ";
        $sql.="       ,u.ds_usuario t_ds_usuario";
        $sql.="       ,u.ds_login t_ds_login";
        $sql.="       ,u.ds_senha t_ds_senha";
        $sql.="       ,u.ds_email t_ds_email";
        $sql.="       ,u.ds_cel t_ds_cel";
        $sql.="       ,u.ic_status t_ic_status";
        $sql.="       ,u.grupos_pk t_grupos_pk";
        $sql.="       ,u.leads_pk t_leads_pk";
        $sql.="       ,g.ds_grupo t_ds_grupo";
        $sql.="       ,c.ds_conta t_ds_conta";
        $sql.="       ,case u.ic_status when 1 then 'Ativo' when 2 then 'Inativo' end t_ds_status";

        $sql.="  from usuarios u ";
        $sql.="       inner join grupos g on u.grupos_pk = g.pk";
        $sql.="       left join contas c on u.contas_pk = c.pk";
        $sql.=" where 1=1 ";
        $sql.=$search;
        if($ds_usuario!=""){
            $sql.=" and u.ds_usuario like '%".$ds_usuario."%'";
        }
        if($ic_status!=""){
            $sql.=" and u.ic_status = ".$ic_status;
        }
        if($contas_pk!=""){
            $sql.=" and u.contas_pk = ".$contas_pk;
        }

        if($grupos_pk!=""){
            $sql.=" and u.grupos_pk = ".$grupos_pk;
        }

        if($_SESSION['session_user']['par1']!=1){
            $sql.=" and u.pk not in (1)";
        }
        

        $sql.=" order by ds_usuario asc ";

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

    public function listarGruposUsuario($usuarios_pk){    
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $sql ="";
        $sql.="select u.pk, u.dt_cadastro, u.usuario_cadastro_pk, u.dt_ult_atualizacao, u.usuario_ult_atualizacao_pk ";
        $sql.="       ,u.ds_usuario ";
        $sql.="       ,g.ds_grupo ";
        $sql.="       ,u.grupos_pk ";

        $sql.="  from usuarios u ";
        $sql.="       inner join grupos g on u.grupos_pk = g.pk";
        $sql.=" where 1=1 ";
        if($usuarios_pk == ""){
            $sql.=" and u.pk = ".$_SESSION['session_user']['par1'];
        }else{
            $sql.=" and u.pk = ".$usuarios_pk;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $retorno->data = $rows;
        $retorno->status = true;
        $retorno->message = 'Dados Salvos com sucesso !';
        return $retorno;  
}

public function listarTodosGestores(){    
    $retorno = new \StdClass; //Estrutura de retorno para controller
    $retorno->status = false; //Retorno setado status como false
    $retorno->data = []; //Retorno data setado como vazio
    
    $sql ="";
    $sql.="select u.pk,  u.ds_usuario ";
    $sql.="  from usuarios u ";
    $sql.=" LEFT join grupos g on g.pk = u.grupos_pk ";
    $sql.=" where  g.ds_grupo like 'Controller' ";
    $sql.=" ORDER BY u.ds_usuario ASC ";
        
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


    $retorno->data = $rows;
    $retorno->status = true;
    $retorno->message = 'Dados Salvos com sucesso !';
    return $retorno;  
}

public function listarTodosAnalistas(){    
    $retorno = new \StdClass; //Estrutura de retorno para controller
    $retorno->status = false; //Retorno setado status como false
    $retorno->data = []; //Retorno data setado como vazio
    
    $sql ="";
    $sql.="select u.pk,  u.ds_usuario ";
    $sql.="  from usuarios u ";
    $sql.=" inner join grupos g on g.pk = u.grupos_pk ";
    $sql.=" where  g.ds_grupo like '%Analista Financeiro' ";
    $sql.=" ORDER BY u.ds_usuario ASC ";
            
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


    $retorno->data = $rows;
    $retorno->status = true;
    $retorno->message = 'Dados Salvos com sucesso !';
    return $retorno;  
}

public function listarDadosUsuario($usuarios_pk){
    $retorno = new \StdClass; //Estrutura de retorno para controller
    $retorno->status = false; //Retorno setado status como false
    $retorno->data = []; //Retorno data setado como vazio

    $sql ="";
    $sql.="select u.pk, u.dt_cadastro, u.usuario_cadastro_pk, u.dt_ult_atualizacao, u.usuario_ult_atualizacao_pk  ";
    $sql.="       ,u.ds_usuario ";
    $sql.="       ,u.ds_login ";
    $sql.="       ,u.ds_senha ";
    $sql.="       ,u.ds_email ";
    $sql.="       ,u.ds_cel ";
    $sql.="       ,u.ic_status ";
    $sql.="       ,u.grupos_pk ";
    $sql.="       ,u.colaboradores_pk";
    $sql.="       ,u.leads_pk";
    $sql.="       ,g.ds_grupo ";
    $sql.="  from usuarios u";
    $sql.="       inner join grupos g on u.grupos_pk = g.pk";
    $sql.=" where u.pk =".$usuarios_pk;
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


    $retorno->data = $rows;
    $retorno->status = true;
    $retorno->message = 'Dados Salvos com sucesso !';
    return $retorno;
} 



}
