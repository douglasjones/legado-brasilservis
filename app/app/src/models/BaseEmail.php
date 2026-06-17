<?php

namespace App\Model;

class BaseEmail {

	public $email = null;
	public $body = null;

	public function setEmail($template) {
		try {
			$this->email = $template;
			$body = $_SERVER['DOCUMENT_ROOT'] . '/templates-email/' . $this->email . '.html';
			if (file_exists($body)) {
				ob_start();
				readfile($body);
				$this->body = ob_get_clean();
				return true;
			} else {
				return false;
			}
		} catch (\Exception $e) {
			return false;
		}
	}

	public function set($valores = array()) {
		try {
			foreach ($valores as $key => $value) {
				$this->body = str_replace('{{' . $key . '}}', $value, $this->body);
			}
		} catch (\Exception $e) {
			return false;
		}
	}

	public function send($params) {
		$mail = new \PHPMailer();
		$mail->isSMTP();
		$mail->isHTML(true);
		$mail->setFrom('contato@mitodarodada.com.br', 'Mito da Rodada');
		$mail->addAddress($params['email'], $params['nome']);
		$mail->Username = 'AKIAVIFL535MK2CGLSNB';
		$mail->Password = 'BM4K2jpLAVlMvynkgTGNXQRF2JP6tyzVPi12W2H97q1i';
		/*$mail->SMTPDebug = 1;*/
		$mail->CharSet = 'UTF-8';
		$mail->Host = 'email-smtp.us-east-1.amazonaws.com';
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = 'tls';
		$mail->Port = 587;
		$mail->Subject = $params['assunto'];
		$mail->Body = $params['corpo'];
		$mail->AltBody = $params['altBody'];
		$retorno = ($mail->send()) ? true : false;
		$mail->ClearAddresses();
		return $retorno;
	}
}