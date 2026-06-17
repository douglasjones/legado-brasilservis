<?php

namespace App\Model;



use Throwable;

class EnviarEmail {
    public function enviarEmail($email_para,$assunto,$corpo){
        //mail.gpros.com.br
        //465
        // Dados de autenticação
        $smtpUsername = 'sistema@gepros1.com.br';
        $smtpPassword = '@Gepros12_';
        try{
            $message = (new \Swift_Message())
                ->setSubject($assunto)
                ->setFrom(array('sistema@gepros1.com.br'))
                //->setTo(array('douglas.lopes@gepros.com.br'))
                ->setTo(array($email_para))
                ->setBody($corpo,'text/html');

            $transport = (new \Swift_SmtpTransport('mail.gepros1.com.br', 465, 'ssl'))
                ->setUsername($smtpUsername)
                ->setPassword($smtpPassword);
            $mailer = new \Swift_Mailer($transport);


            //RETORNO VAI SER IGUAL A 1 PARA SUCESSO!!!
            return ($mailer->send($message));
        }
        catch (Throwable $th) {
            print_r($th->getMessage());
            die();
        }

    }
}