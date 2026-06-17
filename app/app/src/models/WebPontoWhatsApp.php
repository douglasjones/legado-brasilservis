<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;
use Throwable;

class WebPontoWhatsApp {

    public $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function tratarMensagem(
        $mensagem_from,
        $mensagemRecebida,
        $telRecebido,
        $tipoMensagem,
        $ds_link,
        $latitude,
        $longitude
        ){
        try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio

            $texto1 ="";
            $texto2 = "";
            $texto3 = "";
            $texto4 = "";
            $texto5 = "";
            $texto6 = "";
            $texto7 = "";
            //PEGAR O PONTO 
            $dataAtual = date('Y-m-d');


            
            


            $sql ="";
            $sql.="SELECT c.pk,c.ds_colaborador,c.ds_pin,ct.id_cliente";
            $sql.=" FROM colaboradores c";
            $sql.=" INNER JOIN contas ct on c.empresas_pk = ct.pk";
            $sql.=" WHERE REPLACE(REPLACE(REPLACE(SUBSTRING(c.ds_cel, LOCATE(')', c.ds_cel) + 1), '-', ''), ' ', ''), ')', '') like'%".$telRecebido."%'";
        
            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
            if(count($query)>0){
                $colaborador_pk = $query[0]['pk'];
                $ds_colaborador = $query[0]['ds_colaborador'];
                $ds_pin_tabela_colaborador = $query[0]['ds_pin'];
                $id_cliente = $query[0]['id_cliente'];

                //VAMOS VERIFICAR SE EXISTE UM CADASTRO DELE NA SOLICITAÇÃO DE PONTO.
                $sql ="";
                $sql.="SELECT p.ds_pin,p.ic_status";
                $sql.=" FROM ponto_solicitacao_liberacao_app p";
                $sql.=" WHERE p.colaborador_pk = ".$colaborador_pk;
                $stmt = $this->pdo->prepare( $sql );
                $stmt->execute();
                $query1 = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                if(count($query1)>0){
                    if($query1[0]['ic_status']!=1){
                        
                        $texto1 ="Olá *".$ds_colaborador."*, seja bem vindo ao batimento de ponto !!!\u2705 ";
                        $texto2 = " Seu acesso ainda não foi liberador, entre em contato com o RH.";
            
                        $this->enviarMensagem($texto1,$mensagem_from);
                        $this->enviarMensagem($texto2,$mensagem_from);
                    }
                    else{
                        $ds_pin = $query1[0]['ds_pin'];

                        $estado = $this->getEstadoConversa($mensagem_from);
                        if(empty($estado)){
                            $this->cadastrarEstadoSituacaoClienteChatBot($mensagem_from, "inicio");
                        } // Recarregar o estado após a condição

                        //CASO TENHA O REGISTRO
                        //CONSULTAR O POSTO DE TRABALHO DO COLABORADOR
                        $sql="";
                        $sql.="SELECT a.pk,";
                        $sql.=" l.pk leads_pk,";
                        $sql.=" l.ds_lead,";
                        $sql.=" concat(l.ds_endereco,', ',l.ds_numero,',',l.ds_cidade,',Brasil')ds_local_trabalho,";
                        $sql.=" c.ds_colaborador,";
                        $sql.="       a.turnos_pk,";
                        $sql.="       a.hr_inicio_expediente,";
                        $sql.="       a.hr_termino_expediente,";
                        $sql.=" TIMESTAMPDIFF(MINUTE, a.hr_inicio_expediente, CURRENT_TIME) AS diferenca_minutos,";
                        $sql.=" date_format(a.dt_inicio_agenda, '%d/%m/%Y') dt_inicio_agenda,";
                        $sql.=" date_format(a.dt_fim_agenda, '%d/%m/%Y') dt_fim_agenda,";
                        $sql.=" date_format(a.dt_cancelamento, '%d/%m/%Y') dt_cancelamento";
                        $sql.=" FROM agenda_colaborador_padrao a";
                        $sql.="     INNER JOIN leads l ON a.leads_pk = l.pk";
                        $sql.="     INNER JOIN colaboradores c ON a.colaboradores_pk = c.pk";
                        $sql.=" WHERE a.colaboradores_pk =".$colaborador_pk;
                        $sql.="       AND a.dt_cancelamento IS NULL";
                        $sql.="       AND a.dt_fim_agenda > sysdate()";

                        $stmt = $this->pdo->prepare( $sql );
                        $stmt->execute();
                        $queryLead = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        if(count($queryLead)>0){
                            //CASO TENHA O POSTO DE TRABALHO
                            $agenda_colaborador_pk = $queryLead[0]['pk'];
                            $leads_pk = $queryLead[0]['leads_pk'];
                            $ds_lead = $queryLead[0]['ds_lead'];
                            $turnos_pk = $queryLead[0]['turnos_pk'];
                            $diferenca_minutos = $queryLead[0]['diferenca_minutos'];
                            $hr_inicio_expediente = $queryLead[0]['hr_inicio_expediente'];
                            $ds_local_trabalho = $queryLead[0]['ds_local_trabalho'];
                            if($tipoMensagem == 'text'){
                                
                                if($mensagemRecebida=="oi" || $mensagemRecebida =="Oi" || $mensagemRecebida =="Olá" || $mensagemRecebida =="Começar"){
                                    $this->atualizarEstadoSituacaoClienteChatBot($mensagem_from, "inicio");
                                    $estado = $this->getEstadoConversa($mensagem_from); // Recarregar o estado após a condição
                                    if($estado == "inicio"){    
                                        $sql ="";
                                        $sql.=" SELECT ";
                                        $sql.="    p.ds_pin,";
                                        $sql.="    p.colaborador_pk,";
                                        $sql.="    p.tipo_ponto_pk";
                                        $sql.=" FROM";
                                        $sql.="    ponto p";
        
                                        $sql.=" WHERE 1 = 1 ";
                                        $sql.=" and p.ds_localizacao is not null";
                                        $sql.=" and p.ds_dispositivo = 'WhatsApp'";
                                        $sql.=" and p.img_ponto is not null";
                                        $sql.=" and p.dt_hora_ponto between '".$dataAtual." 00:00:00' and '".$dataAtual." 23:59:59'";
                                        if($colaborador_pk != ""){
                                            $sql.=" and p.colaborador_pk  =".$colaborador_pk;
                                        }
                                        
                                        
                                        $stmt = $this->pdo->prepare( $sql );
                                        $stmt->execute();
                                        $queryp = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                                        $ini_exp = 0;
                                        $ini_int = 0;
                                        $term_int = 0;
                                        $term_exp = 0;
                                        $allPontos = 0;
                                        if(count($queryp)>0){
                                            for($p=0;$p<count($queryp);$p++){
                                                if($queryp[$p]['tipo_ponto_pk']==1){
                                                    $ini_exp = 1;
                                                    $allPontos++;
                                                }
                                                if($queryp[$p]['tipo_ponto_pk']==2){
                                                    $term_exp = 1;
                                                    $allPontos++;
                                                }
                                                if($queryp[$p]['tipo_ponto_pk']==3){
                                                    $ini_int = 1;
                                                    $allPontos++;
                                                }
                                                if($queryp[$p]['tipo_ponto_pk']==4){
                                                    $term_int = 1;
                                                    $allPontos++;
                                                }
                                                
                                            }
                                        }
                                        $isFuturo = false;
                                        $HorarioPermitido = false;
                                        $comparacao = "-5";
                                        //CALCULAR HORA DO EXPEDIENTE.
                                        if($turnos_pk!=3){
                                            if(($diferenca_minutos) >= 0){
                                                $isFuturo = true;
                                            }
                                            if($isFuturo){
                                                $HorarioPermitido = true;
                                            }
                                            else{
                                                if(($diferenca_minutos) >= ($comparacao)){
                                                    $HorarioPermitido = true;
                                                }
                                            }
                                        }
        
                                        
        
                                        //if($HorarioPermitido){
                                            
                                            if($allPontos>3){
                                                $texto1 ="Olá *".$ds_colaborador."* ";
                                                $texto2 = " \u203c\ufe0f *Informamos que você já bateu todos os seus pontos diários.* \u203c\ufe0f";
                                    
                                                $this->enviarMensagem($texto1,$mensagem_from);
                                                $this->enviarMensagem($texto2,$mensagem_from);
                
                                            }
                                            else{
                                                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        
                                                // Obter o nome do host (ex: www.example.com)
                                                $host = $_SERVER['HTTP_HOST'];
                                                // Montar a URL completa
                                                $currentUrl = $protocol . $host;
                                                $texto1 ="Olá *".$ds_colaborador."*, seja bem vindo ao batimento de ponto  !!!\u2705 ";
                                                $texto2 = " Informe com o número qual o ponto.";
                                                $texto3 = "*⏰✅ 1 - Inicio de expediente.*";
                                                $texto4 = "*🍽️☕ 2 - Inicio Intervalo*";
                                                $texto5 = "*🔄🚶‍♂️ 3 - Volta do Intervalo*";
                                                $texto6 = "*👋🛑 4 - Final de expediente.*";
                                    
                                                $this->enviarMensagem($texto1,$mensagem_from);
                                                $this->enviarMensagem($texto2,$mensagem_from);
                                                if($ini_exp==0){
                                                    $this->enviarMensagem($texto3,$mensagem_from);
                                                }
                                                if($ini_int==0){
                                                    $this->enviarMensagem($texto4,$mensagem_from);
                                                }
                                                if($term_int==0){
                                                    $this->enviarMensagem($texto5,$mensagem_from);
                                                }
                                                if($term_exp==0){
                                                    $this->enviarMensagem($texto6,$mensagem_from);
                                                }
                                            }
                                        $this->atualizarEstadoSituacaoClienteChatBot($mensagem_from, "esperando_tipo_ponto");
                                        
                                        
                                    }
                                }
                                else if($estado == "esperando_tipo_ponto"){   
                                    //else if($mensagemRecebida==1 || $mensagemRecebida == 2|| $mensagemRecebida == 3|| $mensagemRecebida == 4){
                                    if($mensagemRecebida == 1){
                                        $mensagemRecebidaCad =1;
                                    }
                                    //SELECIONAR 2 SEGNIFICA SAIDA PARA O INTERVALO QUE NO SISTEMA É 3
                                    else if($mensagemRecebida==2){
                                        $mensagemRecebidaCad = 3;
                                    }
                                    //SELECIONAR 3 SEGNIFICA RETORNO DO INTERVALO QUE NO SISTEMA É 4
                                    else if($mensagemRecebida==3){
                                        $mensagemRecebidaCad = 4;
                                    }
                                    //SELECIONAR 4 SEGNIFICA FIM DO EXPEDIENTE QUE NO SISTEMA É 2
                                    else if($mensagemRecebida==4){
                                        $mensagemRecebidaCad = 2;
                                    }


                                    $sql ="";
                                    $sql.=" SELECT ";
                                    $sql.="    p.ds_pin,";
                                    $sql.="    p.colaborador_pk,";
                                    $sql.="    p.tipo_ponto_pk";
                                    $sql.=" FROM";
                                    $sql.="    ponto p";

                                    $sql.=" WHERE 1 = 1 ";
                                    $sql.=" and p.dt_hora_ponto between '".$dataAtual." 00:00:00' and '".$dataAtual." 23:59:59'";
                                    $sql.=" and p.ds_localizacao is not null";
                                    $sql.=" and p.ds_dispositivo = 'WhatsApp'";
                                    $sql.=" and p.img_ponto is not null";
                                    if($colaborador_pk != ""){
                                        $sql.=" and p.colaborador_pk  =".$colaborador_pk;
                                    }
                                    
                                    $stmt = $this->pdo->prepare( $sql );
                                    $stmt->execute();
                                    $queryp = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                                    $ini_exp = 0;
                                    $ini_int = 0;
                                    $term_int = 0;
                                    $term_exp = 0;
                                    $allPontos = 0;
                                    if(count($queryp)>0){
                                        for($p=0;$p<count($queryp);$p++){
                                            if($queryp[$p]['tipo_ponto_pk']==1){
                                                $ini_exp = 1;
                                                $allPontos++;
                                            }
                                            if($queryp[$p]['tipo_ponto_pk']==2){
                                                $term_exp = 1;
                                                $allPontos++;
                                            }
                                            if($queryp[$p]['tipo_ponto_pk']==3){
                                                $ini_int = 1;
                                                $allPontos++;
                                            }
                                            if($queryp[$p]['tipo_ponto_pk']==4){
                                                $term_int = 1;
                                                $allPontos++;
                                            }
                                            
                                        }
                                    }

                                    if($mensagemRecebidaCad==1 && $ini_exp==1){
                                        $texto1 ="Você já bateu seu ponto de entrada hoje ! .";
                                        $this->enviarMensagem($texto1,$mensagem_from);
                                    }
                                    else if($mensagemRecebidaCad==2 && $term_exp==1){
                                        $texto1 ="Você já bateu seu ponto de Saida hoje ! .";
                                        $this->enviarMensagem($texto1,$mensagem_from);
                                    }
                                    else if($mensagemRecebidaCad==3 && $ini_int==1){
                                        $texto1 ="Você já bateu seu ponto de saida para intervalo hoje ! .";
                                        $this->enviarMensagem($texto1,$mensagem_from);
                                    }
                                    else if($mensagemRecebidaCad==4 && $term_int==1){
                                        $texto1 ="Você já bateu seu ponto de retorno do intervalo hoje ! .";
                                        $this->enviarMensagem($texto1,$mensagem_from);
                                    }
                                    else{
                                        $texto1 ="Ótimo \ud83d\ude04 .";
                                        $texto2 = "Preciso que tire uma foto agora \ud83d\udcf8";
                                        $texto3 = "\u203c\ufe0f *Como deve ser feito* \u203c\ufe0f";
                                        $texto4 = "*Apenas do rosto, sem mascara, oculos ou boné.*";
                                        $texto5 = "*De frente sem inclinação e local iluminado.*";
                                        $texto6 = "*Uma foto nitida.*";
                        
                                        $this->enviarMensagem($texto1,$mensagem_from);
                                        $this->enviarMensagem($texto2,$mensagem_from);
                                        $this->enviarMensagem($texto3,$mensagem_from);
                                        $this->enviarMensagem($texto4,$mensagem_from);
                                        $this->enviarMensagem($texto5,$mensagem_from);
                                        $this->enviarMensagem($texto6,$mensagem_from);



                                        //SALVAR O TIPO PONTO 
                                        $fields = array();
                                        $fields['ds_pin'] = $ds_pin;
                                        
                                        $fields['ic_tipo_app'] = 1;
                                        $fields['ds_dispositivo'] = "WhatsApp";
                                        $fields['colaborador_pk'] = $colaborador_pk;
                                        $fields['tipo_ponto_pk'] = $mensagemRecebidaCad;
                                        $fields['dt_hora_ponto'] = 'sysdate()';
                                        $fields['agenda_colaborador_padrao_pk'] = $agenda_colaborador_pk;
                                        $fields['leads_pk'] = $leads_pk;
                                        $fields['ic_sincronizacao'] = 1;
                                        $fields["dt_ult_atualizacao"] = "sysdate()";
                                        $fields["usuario_ult_atualizacao_pk"] = 1;
                                        $fields["dt_cadastro"] = "sysdate()";
                                        $fields["usuario_cadastro_pk"]   = 1;
                                        Util::execInsert("ponto", $fields,$this->pdo);
                                    }


                                    $this->atualizarEstadoSituacaoClienteChatBot($mensagem_from, "esperando_foto");
                                
                                }
                                else{
                                    $texto1 ="Não entendi sua solicitação, por favor, envie Oi para iniciar novamente";
                                    $this->enviarMensagem($texto1,$mensagem_from);
                                }
                            }
                            else if($tipoMensagem == 'image' && $estado == "esperando_foto"){
                                //CONVERTER IMG PARA BLOB 
                                /*$urlAtual = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s" : "") . "://" . $_SERVER['HTTP_HOST']."/";
                                $conteudo_imagem = file_get_contents("https://www.gpros.com.br/wb/".$ds_link);*/

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, "https://www.gpros.com.br/wb/".$ds_link);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                $conteudo_imagem = curl_exec($ch);
                                curl_close($ch);
                                
                                
                                //CONSULTAR O PONTO PARA ARMAZENAR A FOTO 
                                $sql="";
                                $sql.="SELECT pk FROM ponto ";
                                $sql.=" where colaborador_pk  =".$colaborador_pk;
                                $sql.=" and ds_dispositivo = 'WhatsApp'";
                                $sql.=" and img_ponto is null";
                                $sql.=" and dt_hora_ponto between '".$dataAtual." 00:00:00' and '".$dataAtual." 23:59:59'";
                                $sql.=" order by pk desc";
                                $stmt = $this->pdo->prepare( $sql );
                                $stmt->execute();
                                $queryPonto = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                                if(count($queryPonto)>0){
                                    $ponto_pk = $queryPonto[0]['pk'];

                                    //SALVAR O IMG PONTO
                                    $fields = array();
                                    $fields['img_ponto'] = base64_encode($conteudo_imagem);
                                    $fields['ds_imagem'] = "https://www.gpros.com.br/wb/".$ds_link;
                                    $fields['dt_hora_ponto'] = 'sysdate()';

                                    Util::execUpdate("ponto", $fields, " pk = ".$ponto_pk,$this->pdo);

                                    // Definir o local para o Brasil
                                    date_default_timezone_set('America/Sao_Paulo');
                                    // Obter a data e hora atual no formato brasileiro
                                    $dataHoraAtual = date('d/m/Y H:i:s');
                                    $texto1 = "Perfeito \u263a\ufe0f .";
                                    $texto2 = " *Precisamos que envie agora a sua localização atual* \ud83d\udccd ";
                                    $this->enviarMensagem($texto1,$mensagem_from);
                                    $this->enviarMensagem($texto2,$mensagem_from);
                                }
                                else{
                                    //CASO O ARQUIVO NÃO SEJA UM IMAGEM
                                    $texto1 ="Tivemos um problema para bater o ponto, entre em contato com o suporte.";
                                    $this->enviarMensagem($texto1,$mensagem_from);
                                }

                                $this->atualizarEstadoSituacaoClienteChatBot($mensagem_from, "esperando_localizacao");
                            }
                            else if($estado == "esperando_localizacao" && $tipoMensagem == 'location'){
                                

                                $endereco = $this->transformarCoordenadasEmEndereco($latitude,$longitude);
                                

                                $location2 = Util::getCoordinates($ds_local_trabalho);
                                
                                $distancia_ponto= "km.";
                                // Verifica se as coordenadas foram obtidas com sucesso
                                if ($location2) {
                                    // Calcula a distância entre os dois pontos
                                    $distancia = Util::calcularDistancia($latitude,$longitude, $location2['lat'], $location2['lon']);
                                    $distancia_ponto = round($distancia, 2) . " km.";
                                }
                                
                                //CONSULTAR O PONTO PARA ARMAZENAR A LOCALIZAÇÃO 
                                $sql="";
                                $sql.="SELECT pk FROM ponto ";
                                $sql.=" where colaborador_pk  =".$colaborador_pk;
                                $sql.=" and ds_dispositivo = 'WhatsApp'";
                                $sql.=" and ds_localizacao is null";
                                $sql.=" and dt_hora_ponto between '".$dataAtual." 00:00:00' and '".$dataAtual." 23:59:59'";
                                $sql.=" order by pk desc";
                                $stmt = $this->pdo->prepare( $sql );
                                $stmt->execute();
                                $queryPonto = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                                if(count($queryPonto)>0){
                                    $ponto_pk = $queryPonto[0]['pk'];

                                    //SALVAR O IMG PONTO
                                    $fields = array();
                                    $fields['ds_localizacao'] = $endereco;
                                    $fields['ds_distancia_ponto'] = $distancia_ponto;
                                    $fields['dt_hora_ponto'] = 'sysdate()';

                                    Util::execUpdate("ponto", $fields, " pk = ".$ponto_pk,$this->pdo);


                                    // Definir o local para o Brasil
                                    date_default_timezone_set('America/Sao_Paulo');
                                    // Obter a data e hora atual no formato brasileiro
                                    $dataHoraAtual = date('d/m/Y H:i:s');
                                    $texto1 = "Finalizamos \u263a\ufe0f .";
                                    $texto2 = "Seu ponto foi batido com sucesso !!! \ud83c\udf89";
                                    $texto3 = "Data do Ponto:*".$dataHoraAtual."*";
                                    $texto4 = "Posto de trabalho:*".$ds_lead."*";
                                    $texto5 = "Distância da sua localização ao posto de trabalho:*".$distancia_ponto."*";
                                    $texto6 = "\u2139\ufe0f *Sua foto e sua localização será comparada com a do nosso banco de dados.* ";
                                    
                                    $this->enviarMensagem($texto1,$mensagem_from);
                                    $this->enviarMensagem($texto2,$mensagem_from);
                                    $this->enviarMensagem($texto3,$mensagem_from);
                                    $this->enviarMensagem($texto4,$mensagem_from);
                                    $this->enviarMensagem($texto5,$mensagem_from);
                                    $this->enviarMensagem($texto6,$mensagem_from);
                                }
                                $this->atualizarEstadoSituacaoClienteChatBot($mensagem_from, "concluido");
                                $this->atualizarEstadoSituacaoClienteChatBot($mensagem_from, "inicio");
                            }
                            else {

                                if($estado == "esperando_localizacao"){
                                    $texto1 ="Não entendi sua mensagem, estamos esperando você mandar sua localização atual ";
                                    $this->enviarMensagem($texto1,$mensagem_from);
                                }
                                else if($estado == "esperando_foto"){
                                    $texto1 ="Não entendi sua mensagem, estamos esperando você mandar sua Foto ";
                                    $this->enviarMensagem($texto1,$mensagem_from);
                                }
                                //CASO O ARQUIVO NÃO SEJA UM IMAGEM
                                
                                
                            }

                            
                        }
                        else{
                            //CASO NÃO LOCALIZE NENHUM POSTO DE TRABALHO DO COLABORADOR
                            $texto1 = $ds_colaborador." Não localizamos nenhum posto de trabalho, entre em contato com o RH da sua empresa";
                            $this->enviarMensagem($texto1,$mensagem_from);
                        }
                    }
                }
                else{
                    //CASO NÃO TENHA O REGISTRO !!!
                    if($tipoMensagem == 'text'){
                        
                        $texto1 ="Olá *".$ds_colaborador."*, bem vindo ao WhatsApp Ponto Gepros !!!\u2705 ";
                        $texto2 = "Vimos que você não tem um cadastro conosco !";
                        $texto3 = "Mas podemos fazer agora !";
                        $texto4 = "Só precisa tirar uma foto \ud83d\udcf8";
                        $texto5 = "*Apenas do rosto, sem mascara, oculos ou boné.*";
                        $texto6 = "*De frente sem inclinação e local iluminado.*";
                        $texto7 = "*Uma foto nitida.*";
            
                        $this->enviarMensagem($texto1,$mensagem_from);
                        $this->enviarMensagem($texto2,$mensagem_from);
                        $this->enviarMensagem($texto3,$mensagem_from);
                        $this->enviarMensagem($texto4,$mensagem_from);
                        $this->enviarMensagem($texto5,$mensagem_from);
                        $this->enviarMensagem($texto6,$mensagem_from);
                        $this->enviarMensagem($texto7,$mensagem_from);
                    }
                    else if($tipoMensagem == 'image'){
                        
                    
                        /*$urlAtual = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s" : "") . "://" . $_SERVER['HTTP_HOST']."/";
                        $conteudo_imagem = file_get_contents("https://www.gpros.com.br/wb/".$ds_link);*/
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "https://www.gpros.com.br/wb/".$ds_link);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $conteudo_imagem = curl_exec($ch);
                        curl_close($ch);

                        $fields = array();
                        $fields['ds_pin'] = $ds_pin_tabela_colaborador;
                        $fields['colaborador_pk'] = $colaborador_pk;
                        $fields['id_cliente'] = $id_cliente;
                        $fields['img_colaborador_cadastro'] = base64_encode($conteudo_imagem);
                        $fields['ds_link_imagem_cadastro'] =  "https://www.gpros.com.br/wb/".$ds_link;
                        $fields['IdTermoAceite'] = 1;
                        $fields['ic_tipo_app'] = 1;


                        $fields["dt_ult_atualizacao"] = "sysdate()";
                        $fields["usuario_ult_atualizacao_pk"] = 1;
                        

                        $fields['dt_solit_liberacao'] = "sysdate()";

                        $fields["dt_cadastro"] = "sysdate()";
                    
                        $fields["usuario_cadastro_pk"]   = 1;
                        
                        Util::execInsert("ponto_solicitacao_liberacao_app", $fields,$this->pdo);


                        $texto1 = "Perfeito \u263a\ufe0f .";
                        $texto2 = " *Entre em contato com o RH da sua empresa para solicitar a liberação do seu acesso!* \ud83d\udccd ";
                            
                        $this->enviarMensagem($texto1,$mensagem_from);
                        $this->enviarMensagem($texto2,$mensagem_from);
                            
                    }
                }
            
            
            }
            else{
            
                //SE NÃO EXISTIR, PEDIMOS PARA QUE ENTRE EM CONTATO COM O RH DA EMPRESA
                $texto1 ="Não localizamos seu registro, entre em contato com o RH da sua empresa";
                $this->enviarMensagem($texto1,$mensagem_from);
            }


            return json_encode($retorno);
        }   
        catch(Throwable $th){
             return $th->getMessage();
        } 
        
    }


    public function transformarCoordenadasEmEndereco($latitude,$longitude){
    
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&zoom=18&addressdetails=1";
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3');
        
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http_status == 200) {
            $data = json_decode($response, true);
            if (isset($data['display_name'])) {
                $address = $data['display_name'];
    
                $endereco = explode(",", $address);
                return $endereco[0].",".$endereco[1]."-".$endereco[3];
                
            } else {
                return "Endereço não encontrado.";
                //return null;
            }
        } else { 
            return "Erro ao processar a solicitação. Código de status HTTP: " . $http_status;
            //return null;
        }
    }

   public  function enviarMensagem($mensagemParaEnviar,$telefone){

   
    try{
        $phone_number_id ='314329865089134';
        $token = 'EAAM4IEWO1gsBOz6kF00qzSjjjQf4joZCqCsLTDRUEsmvss7oqji5zRshF7AnuceFTmO3asQA2HvU9K21ZBi6sDW9m8ufnQsPAifZChjQdWZAfg7jOxl1UnuuFDDTbMkqTPJkgu1c5ZA7GNv9Do56HuVWVjonejLpFHZAikQkZBnqxg9EMiCqW7jfpKcYcjfM9TTcwZDZD';
    
    
        $url = "https://graph.facebook.com/v16.0/".$phone_number_id."/messages";
        $header = [    
            'Authorization: Bearer '.$token,
            'Content-Type: application/json'
        ];
    
        $mensagem = "{ \"messaging_product\": \"whatsapp\", \"to\": \"".$telefone."\", \"type\": \"text\", \"text\": { \"preview_url\": false, \"body\": \"".$mensagemParaEnviar."\"} }";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
    
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$mensagem);  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        curl_close($ch);
        
        echo json_encode(["success" => $response]) ;
        
       }
       catch(Throwable $th){
        echo json_encode(["success" => $th->getMessage()]) ;
       }
    
    }
    public function getEstadoConversa($ds_tel) {
        try{
            if (substr($ds_tel, 0, 2) === "55") {
                $ds_tel = substr($ds_tel, 2);
            }
            $sql='
                SELECT estado
                FROM situacao_cliente_chatbot
                WHERE telefone ="'.$ds_tel.'"';
            // Função para recuperar o estado atual da conversa do cliente a partir do banco de dados
            $stmt = $this->pdo->prepare($sql);


            
            //$stmt->bindValue(':from', $ds_tel);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            
            //$this->enviarMensagem($result['estado'],'5511978344771');
            
            return $result['estado'] ;
        }
        catch(Throwable $e){
            $this->enviarMensagem($e->getMessage(),'5511978344771');

            print_r($e->getMessage());
            die();
        }
        
    }
    public function cadastrarEstadoSituacaoClienteChatBot($ds_tel, $novoEstado) {
        if (substr($ds_tel, 0, 2) === "55") {
            $ds_tel = substr($ds_tel, 2);
        }
        // Função para atualizar o estado atual da conversa no banco de dados
        $stmt = $this->pdo->prepare('
            INSERT INTO situacao_cliente_chatbot (telefone, estado)
            VALUES ("'.$ds_tel.'", "'.$novoEstado.'")
        ');
        $stmt->execute();
    }
    public function atualizarEstadoSituacaoClienteChatBot($ds_tel, $novoEstado) {
        try{
            
            if (substr($ds_tel, 0, 2) === "55") {
                $ds_tel = substr($ds_tel, 2);
            }
            // Função para atualizar o estado atual da conversa no banco de dados
            $stmt = $this->pdo->prepare('
                update situacao_cliente_chatbot set estado ="'.$novoEstado.'" 
                where telefone = "'.$ds_tel.'"
            ');
            $stmt->execute();
        }
        catch(Throwable $th){
            
                        
        }
        
    }
}


