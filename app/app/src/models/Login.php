<?php

namespace App\Model;

use App\Utils\Session;
use App\Utils\Util;
use App\Utils\Validation;

class Login {

	public $pdo;

	public function __construct($pdo) {
        $this->pdo = $pdo;
	}

	public function login($data) {
	    $retorno = new \StdClass;
		$retorno->status = false;
		$retorno->data = [];

		//$data = Util::trimInArray($data);
		

		/* VALID FORM */
		$v = Validation::check([
			'user'     => [
				'value'    => @$data['login'],
				'error'    => 'Preencha seu usuário',
				'required' => true
			],
			'password' => [
				'value'    => @$data['password'],
				'error'    => 'Preencha sua senha',
				'required' => true
			]
		]);

		if (!$v['status']) {
			$retorno->message = $v['messages'][0]->message;
			return $retorno;
		}
		$SQL = "select u.pk par1,
		               u.ds_usuario par2, 
		               u.ds_login par3, 
		               date_format(date_add(sysdate(), interval 10 hour),'%Y%m%d%H%m%s') par4, 
		               u.colaboradores_pk par5,
		               u.leads_pk par6, 
		               u.contas_pk par7,
		               g.ds_grupo par8,
		               u.ds_email par9,
		               u.grupos_pk par10,
					   c.ds_dominio par11
                from usuarios u
                INNER JOIN grupos g on u.grupos_pk = g.pk
				LEFT JOIN contas c on c.pk = u.contas_pk
                where u.ds_login = :login
                  and u.ds_senha = :password";

        $stmt = $this->pdo->prepare( $SQL );
        $stmt->execute(
            [
                ':login' => $data['login'],
                ':password' => $data['password']
            ]
        );

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		/* USUARIO NAO ENCONTRADO */
		if (!$rows) {
			$retorno->message = 'Credenciais Inválidas usuario não encontrado';
			return $retorno;
		}
		$_SESSION['session_user'] = $rows[0];
		

		$retorno->status = true;
		$retorno->message = 'Usuário autenticado com sucesso';
		$retorno->data = $rows[0];

		return $retorno;
	}

	public function UpdateSenha($data){
		$retorno = new \StdClass;
		$retorno->status = false;
		$retorno->data = [];

		$fields = array();
		//FIELDS ========== NOMECLATURA IGUAL A DO BANCO DE DADOS!!!!
		//PARAMETRO, NESSE CASO O DARA QUE VEM DENTRO DOS "()" DA FUNÇÃO, PRECISA VERIDICAR SEMPRE O NOME QUE ESTÁ SENDO PASSADO!!!
		$fields['ds_senha'] = $data['password1'];

		Util::execUpdate("usuarios", $fields, " ds_login = '".$data['ds_login_nova_senha']."'",$this->pdo);

		$SQL = "select u.pk par1,
		               u.ds_usuario par2, 
		               u.ds_login par3, 
		               date_format(date_add(sysdate(), interval 10 hour),'%Y%m%d%H%m%s') par4, 
		               u.colaboradores_pk par5,
		               u.leads_pk par6, 
		               u.contas_pk par7,
		               g.ds_grupo par8,
		               u.ds_email par9,
		               u.grupos_pk par10,
					   c.ds_dominio par11
                from usuarios u
                LEFT JOIN grupos g on u.grupos_pk = g.pk
				LEFT JOIN contas c on u.contas_pk = c.pk
                where u.ds_login = :login
                  and u.ds_senha = :password";

        $stmt = $this->pdo->prepare( $SQL );
        $stmt->execute(
            [
                ':login' => $data['ds_login_nova_senha'],
                ':password' => $data['password1']
            ]
        );

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		/* USUARIO NAO ENCONTRADO */
		if (!$rows) {
			$retorno->message = 'Credenciais Inválidas usuario não encontrado';
			return $retorno;
		}
		$_SESSION['session_user'] = $rows[0];
	

		$retorno->status = true;
		$retorno->message = 'Usuário autenticado com sucesso';
		$retorno->data = $rows[0];

		return $retorno;



	}

    public function apiLogoff() {
        $retorno = new \StdClass;
        $retorno->status = false;
        $retorno->data = [];

        Session::cleanSession();

        $retorno->status = true;
        $retorno->message = 'Logout efetuado com sucesso';
        return $retorno;
    }


	public function verificarTrocaSenha($data) {
	    $retorno = new \StdClass;
		$retorno->status = false;
		$retorno->data = [];

		//$data = Util::trimInArray($data);
		

		/* VALID FORM */
		$v = Validation::check([
			'user'     => [
				'value'    => @$data['login'],
				'error'    => 'Preencha seu usuário',
				'required' => true
			],
			'password' => [
				'value'    => @$data['password'],
				'error'    => 'Preencha sua senha',
				'required' => true
			]
		]);

		if (!$v['status']) {
			$retorno->message = $v['messages'][0]->message;
			return $retorno;
		}
		$SQL = "select *
                from usuarios u
                INNER JOIN grupos g on u.grupos_pk = g.pk
				LEFT JOIN contas c on c.pk = u.contas_pk
                where u.ds_login = :login
                  and u.ds_senha = :password";

        $stmt = $this->pdo->prepare( $SQL );
        $stmt->execute(
            [
                ':login' => $data['login'],
                ':password' => $data['password']
            ]
        );

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		/* USUARIO NAO ENCONTRADO */
		if (!$rows) {
			$retorno->message = 'Credenciais Inválidas usuario não encontrado';
			return $retorno;
		}
		
		

		$retorno->status = true;
		$retorno->message = 'Usuário autenticado com sucesso';
		$retorno->data = count($rows);

		return $retorno;
	}
}
