<?php

namespace App\Model;

use App\Utils\Util;
use GuzzleHttp\Client;
use Throwable;
use DateTime;
use Exception;
use PDO;

class Ponto {

    public $pdo;
    private $maxPointImageWidth = 1280;
    private $pointImageJpegQuality = 75;
    private $margemInicioTurnoNoturnoSegundos = 14400;
    private $margemFimTurnoNoturnoSegundos = 21600;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    private function normalizePointImage($rawImage){
        if (empty($rawImage)) {
            return null;
        }

        if (
            !function_exists('imagecreatefromstring') ||
            !function_exists('imagejpeg') ||
            !function_exists('imagesx') ||
            !function_exists('imagesy')
        ) {
            return $rawImage;
        }

        $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $rawImage);
        $imgBin = base64_decode($base64, true);
        if ($imgBin === false) {
            return $rawImage;
        }

        $img = @imagecreatefromstring($imgBin);
        if ($img === false) {
            return $rawImage;
        }

        $width = imagesx($img);
        $height = imagesy($img);
        if ($width <= 0 || $height <= 0) {
            imagedestroy($img);
            return $rawImage;
        }

        $targetWidth = $width;
        $targetHeight = $height;

        if ($width > $this->maxPointImageWidth) {
            $ratio = $this->maxPointImageWidth / $width;
            $targetWidth = (int) round($width * $ratio);
            $targetHeight = (int) round($height * $ratio);
        }

        $outputImage = $img;
        if ($targetWidth !== $width || $targetHeight !== $height) {
            if (!function_exists('imagecreatetruecolor') || !function_exists('imagecopyresampled')) {
                imagedestroy($img);
                return $rawImage;
            }
            $resized = imagecreatetruecolor($targetWidth, $targetHeight);
            imagecopyresampled($resized, $img, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
            $outputImage = $resized;
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'img');
        if ($tmpFile === false || !@imagejpeg($outputImage, $tmpFile, $this->pointImageJpegQuality)) {
            if ($outputImage !== $img) {
                imagedestroy($outputImage);
            }
            imagedestroy($img);
            return $rawImage;
        }

        $newImgBin = file_get_contents($tmpFile);
        unlink($tmpFile);

        if ($outputImage !== $img) {
            imagedestroy($outputImage);
        }
        imagedestroy($img);

        if ($newImgBin === false) {
            return $rawImage;
        }

        return 'data:image/jpeg;base64,' . base64_encode($newImgBin);
    }
    public function validarImgPonto($pk){

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        try{
            


            $fields = array();
            $fields['ic_validacao_facial'] = 1;
            $fields['dt_validacao_facial'] = "sysdate()";
            $fields["usuario_validacao_facial"] = $_SESSION['session_user']['par1'];

            Util::execUpdate('ponto',$fields," pk = ".$pk,$this->pdo);
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

    function calculaTempo($hora_inicial, $hora_final) {
        $i = 1;
        $tempo_total = [];


        $tempos = array($hora_final, $hora_inicial);

        foreach($tempos as $tempo) {
            $segundos = 0;

            list($h, $m, $s) = explode(':', $tempo);

            $segundos += $h * 3600;
            $segundos += $m * 60;
            $segundos += $s;

            $tempo_total[$i] = $segundos;

            $i++;
        }
        $segundos = $tempo_total[1] - $tempo_total[2];

        $horas = floor($segundos / 3600);
        $segundos -= $horas * 3600;
        $minutos = str_pad((floor($segundos / 60)), 2, '0', STR_PAD_LEFT);
        $segundos -= $minutos * 60;
        $segundos = str_pad($segundos, 2, '0', STR_PAD_LEFT);

        return "$horas:$minutos:$segundos";
    }

    function converterHoraPMinuto($hora_inicial) {

        $i = 1;
        $tempo_total = [];


        $tempos = array($hora_inicial, $hora_inicial);

        foreach($tempos as $tempo) {
            $segundos = 0;

            list($h, $m, $s) = explode(':', $tempo);

            $segundos += $h * 3600;
            $segundos += $m * 60;
            $segundos += $s;

            $i++;
        }

        return $segundos;
    }

    private function normalizarHorarioEscala($horario, $fallback = "")
    {
        $horario = trim((string)$horario);
        if ($horario === "") {
            return $fallback;
        }

        return strlen($horario) === 5 ? $horario . ':00' : $horario;
    }

    private function montarJanelaOperacionalNoturna($dt_escala, $colaborador_pk, $agenda_colaborador_padrao_pk = "")
    {
        $escala = (new PontoFolha($this->pdo))->pegarHorarioDeEntradaPorDataDiaSemana($colaborador_pk, $dt_escala, $agenda_colaborador_padrao_pk);
        $hr_inicio_expediente = $this->normalizarHorarioEscala($escala['dados']['hr_inicio_expediente'] ?? "", '16:00:00');
        $hr_termino_expediente = $this->normalizarHorarioEscala($escala['dados']['hr_termino_expediente'] ?? "", '10:00:00');

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
        ];
    }

    public function verificarPontoAgenda($colaborador_pk, $dt_escala) {
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $sql="";
        $sql.="select p.colaborador_pk, p.tipo_ponto_pk, date_format(p.dt_hora_ponto, '%d/%m/%Y') dt_hora_ponto";
        $sql.="  from ponto p";
        $sql.=" where 1=1";
        if($colaborador_pk != ""){
            $sql.="   and p.colaborador_pk =" .$colaborador_pk;
        }
        if($dt_escala != ""){
            $sql.=" and date_format(dt_hora_ponto, '%Y/%m/%d') = date_format('".$dt_escala."', '%Y/%m/%d')";
        }
        //print_r($sql.PHP_EOL);



        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }
    public function listarColaborador($colaborador_pk,$dt_ini,$dt_fim,$leads_pk,$ic_cliente) {
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $mysql_data = []; //Retorno data setado como vazio

        $sql ="";
        $sql.="select ";
        $sql.="       p.colaborador_pk ";
        $sql.="       ,agp.ic_folga_inverter ";
        $sql.="       ,agp.leads_pk";
        $sql.="       ,date_format(p.dt_hora_ponto,'%d/%m/%Y') dt_ponto";
        $sql.="  from ponto p";
        $sql.="       INNER join colaboradores c on p.colaborador_pk = c.pk";
        $sql.="       INNER JOIN agenda_colaborador_padrao agp ON p.colaborador_pk = agp.colaboradores_pk";
        $sql.="       INNER JOIN leads l ON  agp.leads_pk = l.pk";

        $sql.=" where 1=1 ";
        $sql.=" and c.dt_demissao is null";
        //$sql.=" and l.ic_cliente=1";
        $sql.=" and agp.dt_fim_agenda >='".Util::DataYMD($dt_ini)." 00:00:00'";
        $sql.=" and p.dt_hora_ponto between '".Util::DataYMD($dt_ini)." 00:00:00' and '".Util::DataYMD($dt_fim)." 23:59:59'";

        if($colaborador_pk != ""){
            $sql.=" and p.colaborador_pk  =".$colaborador_pk;
        }
        if($leads_pk != ""){
            $sql.=" and p.leads_pk  =".$leads_pk;
        }
        $sql.=" and agp.dt_cancelamento is null";
        $sql.=" and c.ic_status =1";
        if($colaborador_pk!=""){
            $sql.=" group by agp.leads_pk";
            $sql.=" ORDER BY c.ds_colaborador";
        }else{
            $sql.=" group by c.pk,agp.leads_pk";
            $sql.=" ORDER BY c.ds_colaborador,p.dt_hora_ponto";
        }
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if(count($query) > 0){
            for($i=0;$i < count($query);$i++){
                $mysql_data[] = array(
                    "colaborador_pk" => $query[$i]["colaborador_pk"],
                    "leads_pk" => $query[$i]["leads_pk"],
                    "dt_ponto" => $query[$i]["dt_ponto"],
                    "ic_inverter_folga" => $query[$i]["ic_folga_inverter"]
                );
            }
        }

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $mysql_data;

        return $retorno;
    }
    public function relatorioPontoSinteticaAntigo($leads_pk,$colaborador_pk,$dt_ini,$dt_fim,$qtde_lead_colaborador) {
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $mysql_data = []; //Retorno data setado como vazio

        $ds_legenda[] = "";
        $hr_saida_intervalo = "";
        $hr_volta_intervalo = "";

        $hr_entrada = ("00:00:00");
        $hr_saida = ("23:59:59");


        $sql ="";
        $sql.="SELECT l.pk,";
        $sql.="       l.ds_lead,";
        $sql.="       col.pk colaboradores_pk,";
        $sql.="       concat(l.ds_endereco,', ',l.ds_numero,',',l.ds_cidade,',Brasil')ds_local_trabalho,";
        $sql.="       col.ds_re,";
        $sql.="       col.ds_pin,";
        $sql.="       col.ds_colaborador,";
        $sql.="       ps.ds_produto_servico,";
        $sql.="       pt.tipo_ponto_pk,";
        $sql.="       pt.ds_distancia_ponto,";
        $sql.="       date_format(pt.dt_hora_ponto,'%Y-%m-%d') dt_hora_ponto,";
        $sql.="       date_format(pt.dt_hora_ponto,'%d/%m/%Y') dt_rh_entratada,";
        $sql.="       date_format(pt.dt_hora_ponto,'%H:%i:%s') hr_entrada,";
        $sql.="       pt.ds_total_horas_trabalhadas,";
        $sql.="       pt.ds_localizacao,";
        $sql.="       pt.ds_imagem ds_imagem_entrada,";
        $sql.="       agp.hr_turno_dom,";
        $sql.="       agp.hr_turno_seg,";
        $sql.="       agp.hr_turno_ter,";
        $sql.="       agp.hr_turno_qua,";
        $sql.="       agp.hr_turno_qui,";
        $sql.="       agp.hr_turno_sex,";
        $sql.="       agp.hr_turno_sab,";
        $sql.="       agp.hr_turno_dom_saida,";
        $sql.="       agp.hr_turno_seg_saida,";
        $sql.="       agp.hr_turno_ter_saida,";
        $sql.="       agp.hr_turno_qua_saida,";
        $sql.="       agp.hr_turno_qui_saida,";
        $sql.="       agp.hr_turno_sex_saida,";
        $sql.="       agp.hr_turno_sab_saida,";
        $sql.="       agp.hr_intervalo_seg,";
        $sql.="       agp.hr_intervalo_ter,";
        $sql.="       agp.hr_intervalo_qua,";
        $sql.="       agp.hr_intervalo_qui,";
        $sql.="       agp.hr_intervalo_sex,";
        $sql.="       agp.hr_intervalo_sab,";
        $sql.="       agp.hr_intervalo_saida_seg,";
        $sql.="       agp.hr_intervalo_saida_ter,";
        $sql.="       agp.hr_intervalo_saida_qua,";
        $sql.="       agp.hr_intervalo_saida_qui,";
        $sql.="       agp.hr_intervalo_saida_sex,";
        $sql.="       agp.hr_intervalo_saida_sab,";
        $sql.="       agp.ic_dom,";
        $sql.="       agp.ic_seg,";
        $sql.="       agp.ic_ter,";
        $sql.="       agp.ic_qua,";
        $sql.="       agp.ic_qui,";
        $sql.="       agp.ic_sex,";
        $sql.="       agp.ic_sab,";
        $sql.="    agp.n_qtde_dias_semana,  ";
        $sql.="       pt.ds_imagem ds_imagem_saida,";
        $sql.="       psl.ds_link_imagem_cadastro ds_imagem_sistema";
        $sql.="  FROM ponto pt";
        $sql.="       INNER JOIN colaboradores col ON pt.colaborador_pk = col.pk";
        $sql.="       inner join colaboradores_produtos_servicos cps on col.pk = cps.colaboradores_pk";
        $sql.="       LEFT JOIN produtos_servicos ps ON cps.produtos_servicos_pk = ps.pk";
        $sql.="       INNER JOIN agenda_colaborador_padrao agp ON pt.colaborador_pk = agp.colaboradores_pk";
        $sql.="       LEFT JOIN leads l ON pt.leads_pk = l.pk";
        $sql.="       left join ponto_solicitacao_liberacao_app psl on col.pk = psl.colaborador_pk";
        $sql.=" where 1=1 ";
        //$sql.=" and pt.img_ponto is not null";

        $sql.=" and pt.dt_hora_ponto between '".Util::DataYMD($dt_ini)." ".$hr_entrada."' and '".Util::DataYMD($dt_fim)." ".$hr_saida."'";

        if($leads_pk != ""){
            $sql.=" and (pt.leads_pk = ".$leads_pk.")";
        }

        if($colaborador_pk != ""){
            $sql.=" and col.pk  = ".$colaborador_pk;
        }
        /*if($leads_pk != ""){
            $sql.=" and l.pk = ".$leads_pk;
        }*/
        $sql.=" and agp.dt_cancelamento is null";

        $sql.=" group by DATE_FORMAT(pt.dt_hora_ponto, '%H:%i:%s'),pt.tipo_ponto_pk ";
        $sql.=" order by col.ds_colaborador, pt.dt_hora_ponto asc ";
   


        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if(count($query) > 0){
            for($i=0;$i < count($query);$i++){
                $ds_total_horas_trabalhadas = "";
                $coordernadas_lead = "";
                $latitude_lead = "";
                $longitude_lead = "";
                $latitude_ponto = "";
                $latitude_ponto = "";
                $distancia_entre_pontos = "";
                $endereco_ponto = "";
                $ds_registro_ponto = "";

                $diasemana_numero = date('w', strtotime(Util::DataYMD($query[$i]['dt_rh_entratada'])));

                $horaA = "";
                $horaB = "";
                $horaD = "";
                $horaE = "";
                $hr_diferenca = "";
                $hr_diferenca_positivo = "";
                $diferenca_segundo_positivo = 0;

                if($diasemana_numero==0){
                    //if($query[$i]['ic_dom']==1){
                    $horaA = $query[$i]['hr_entrada'];
                    $horaB = $query[$i]['hr_turno_dom'].':00';
                    $horac = $query[$i]['hr_turno_dom_saida'].':00';

                    if($query[$i]['hr_intervalo_dom']!=""){
                        $horaD = $query[$i]['hr_intervalo_dom'].':00';
                    }
                    if($query[$i]['hr_intervalo_saida_dom']!=""){
                        $horaE = $query[$i]['hr_intervalo_saida_dom'].':00';
                    }


                    $hr_diferenca = Util::calculaTempo($horaB, $horaA);
                    //}

                }
                else if($diasemana_numero==1){
                    //if($query[$i]['ic_seg']==1){
                    $horaA = $query[$i]['hr_entrada'];
                    $horaB = $query[$i]['hr_turno_seg'].':00';
                    $horac = $query[$i]['hr_turno_seg_saida'].':00';
                    if($query[$i]['hr_intervalo_seg']!=""){
                        $horaD = $query[$i]['hr_intervalo_seg'].':00';
                    }
                    if($query[$i]['hr_intervalo_saida_seg']!=""){
                        $horaE = $query[$i]['hr_intervalo_saida_seg'].':00';
                    }

                    $hr_diferenca = Util::calculaTempo($horaB, $horaA);
                    //}
                }
                else if($diasemana_numero==2){
                    //if($query[$i]['ic_ter']==1){
                    $horaA = $query[$i]['hr_entrada'];
                    $horaB = $query[$i]['hr_turno_ter'].':00';
                    $horac = $query[$i]['hr_turno_ter_saida'].':00';

                    if($query[$i]['hr_intervalo_ter']!=""){
                        $horaD = $query[$i]['hr_intervalo_ter'].':00';
                    }
                    if($query[$i]['hr_intervalo_saida_ter']!=""){
                        $horaE = $query[$i]['hr_intervalo_saida_ter'].':00';
                    }

                    $hr_diferenca = Util::calculaTempo($horaB, $horaA);

                    //}
                }
                else if($diasemana_numero==3){
                    //if($query[$i]['ic_qua']==1){
                    $horaA = $query[$i]['hr_entrada'];
                    $horaB = $query[$i]['hr_turno_qua'].':00';
                    $horac = $query[$i]['hr_turno_qua_saida'].':00';
                    if($query[$i]['hr_intervalo_qua']!=""){
                        $horaD = $query[$i]['hr_intervalo_qua'].':00';
                    }
                    if($query[$i]['hr_intervalo_saida_qua']!=""){
                        $horaE = $query[$i]['hr_intervalo_saida_qua'].':00';
                    }

                    $hr_diferenca = Util::calculaTempo($horaB, $horaA);
                    //}
                }
                else if($diasemana_numero==4){
                    //if($query[$i]['ic_qui']==1){
                    $horaA = $query[$i]['hr_entrada'];
                    $horaB = $query[$i]['hr_turno_qui'].':00';
                    $horac = $query[$i]['hr_turno_qui_saida'].':00';
                    if($query[$i]['hr_intervalo_qui']!=""){
                        $horaD = $query[$i]['hr_intervalo_qui'].':00';
                    }
                    if($query[$i]['hr_intervalo_saida_qui']!=""){
                        $horaE = $query[$i]['hr_intervalo_saida_qui'].':00';
                    }

                    $hr_diferenca = Util::calculaTempo($horaB, $horaA);

                    //}
                }
                else if($diasemana_numero==5){
                    //if($query[$i]['ic_sex']==1){
                    $horaA = $query[$i]['hr_entrada'];
                    $horaB = $query[$i]['hr_turno_sex'].':00';
                    $horac = $query[$i]['hr_turno_sex_saida'].':00';
                    if($query[$i]['hr_intervalo_sex']!=""){
                        $horaD = $query[$i]['hr_intervalo_sex'].':00';
                    }
                    if($query[$i]['hr_intervalo_saida_sex']!=""){
                        $horaE = $query[$i]['hr_intervalo_saida_sex'].':00';
                    }

                    $hr_diferenca = Util::calculaTempo($horaB, $horaA);
                    //}
                }
                if($diasemana_numero==6){
                    //if($query[$i]['ic_sab']==1){
                    $horaA = $query[$i]['hr_entrada'];
                    $horaB = $query[$i]['hr_turno_sab'].':00';
                    $horac = $query[$i]['hr_turno_sab_saida'].':00';
                    if($query[$i]['hr_intervalo_sab']!=""){
                        $horaD = $query[$i]['hr_intervalo_sab'].':00';
                    }
                    if($query[$i]['hr_intervalo_saida_sab']!=""){
                        $horaE = $query[$i]['hr_intervalo_saida_sab'].':00';
                    }


                    $hr_diferenca = Util::calculaTempo($horaB, $horaA);
                    //}
                }


                $hr_diferenca_positivo = Util::calculaTempo($horaD, $horaE);



                $segundos_positivo = Util::converterHoraPMinuto($hr_diferenca_positivo);


                $segundos =Util::converterHoraPMinuto($hr_diferenca);

                //if($i==0){
                    if($query[$i]['tipo_ponto_pk']==1){
                        $hr_entrada = $query[$i]['hr_entrada'];
                        $ds_registro_ponto = $query[$i]['hr_entrada'];
                        $ds_legenda[$i] = "Inicio Expediente";
                    }
                    if($query[$i]['tipo_ponto_pk']==2){

                        $ds_registro_ponto = $query[$i]['hr_entrada'];
                        $hr_saida = $query[$i]['hr_entrada'];
                        $ds_legenda[$i] = "Fim Expediente";

                    }
                    if($query[$i]['tipo_ponto_pk']==3){

                        $dt_rh_saida_intervalo = $query[$i]['hr_entrada'];
                        $dt_hora_ponto_saida_intervalo = $query[$i]['dt_hora_ponto'];
                        $ds_registro_ponto = $query[$i]['hr_entrada'];
                        $ds_legenda[$i] = "Saída p/ Intervalo";

                    }
                    if($query[$i]['tipo_ponto_pk']==4){

                        $dt_rh_entratada_retorno = $query[$i]['hr_entrada'];
                        $dt_hora_ponto_entrada_retorno = $query[$i]['dt_hora_ponto'];

                        $ds_registro_ponto = $query[$i]['hr_entrada'];
                        $ds_legenda[$i] = "Retorno do Intervalo";

                    }

                /*}
                else{
                    if($query[$i]['tipo_ponto_pk']==1){
                        $hr_diferenca_ponto = Util::calculaTempo($query[0]['hr_entrada'],$query[$i]['hr_entrada']);

                        $segundos_ponto = Util::converterHoraPMinuto($hr_diferenca_ponto);

                        if($segundos_ponto<="24200"){
                            $ds_registro_ponto = $query[$i]['hr_entrada'];
                            $ds_legenda[$i] = "Retorno do Intervalo";
                        }
                        else if($segundos_ponto > "24200" && $segundos_ponto < "25000"){
                            $ds_registro_ponto = $query[$i]['hr_entrada'];
                            $ds_legenda[$i] = "Inicio Expediente";

                        }
                        else if($segundos_ponto > "25000"){
                            $ds_registro_ponto = $query[$i]['hr_entrada'];
                            $ds_legenda[$i] = "Retorno do Intervalo";

                        }


                    }
                    if($query[$i]['tipo_ponto_pk']==2){

                        $hr_diferenca_ponto = Util::calculaTempo($query[0]['hr_entrada'],$query[$i]['hr_entrada']);
                        $segundos_ponto = Util::converterHoraPMinuto($hr_diferenca_ponto);

                        if($segundos_ponto<="25200"){
                            if(($i+1)==count($query)){
                                $ds_registro_ponto = $query[$i]['hr_entrada'];
                                $hr_saida = $query[$i]['hr_entrada'];
                                $ds_legenda[$i] = "Fim Expediente";
                            }
                            else{
                                $ds_registro_ponto = $query[$i]['hr_entrada'];
                                $ds_legenda[$i] = "Saída p/ Intervalo";
                            }

                        }
                        else if($segundos_ponto > "25200"){
                            $ds_registro_ponto = $query[$i]['hr_entrada'];
                            $hr_saida = $query[$i]['hr_entrada'];
                            $ds_legenda[$i] = "Fim Expediente";
                        }

                    }
                    if($query[$i]['tipo_ponto_pk']==3){

                        $dt_rh_saida_intervalo = $query[$i]['hr_entrada'];
                        $dt_hora_ponto_saida_intervalo = $query[$i]['dt_hora_ponto'];
                        $ds_registro_ponto = $query[$i]['hr_entrada'];
                        $ds_legenda[$i] = "Saída p/ Intervalo";

                    }
                    if($query[$i]['tipo_ponto_pk']==4){

                        $dt_rh_entratada_retorno = $query[$i]['hr_entrada'];
                        $dt_hora_ponto_entrada_retorno = $query[$i]['dt_hora_ponto'];

                        $ds_registro_ponto = $query[$i]['hr_entrada'];
                        $ds_legenda[$i] = "Retorno do Intervalo";

                    }
                }*/
                if($hr_saida!="" && $hr_entrada!=""){
                    $ds_total_horas_trabalhadas = gmdate('H:i:s', strtotime($hr_saida) - strtotime($hr_entrada));
                }



                //CALCULA O ATRASO NA VOLTA DO INTERVALO
                if($ds_legenda[$i]=="Saída p/ Intervalo"){
                    $hr_saida_intervalo = $ds_registro_ponto;
                }
                if($ds_legenda[$i]=="Retorno do Intervalo"){
                    $hr_volta_intervalo = $ds_registro_ponto;
                }

                $hr_diferenca_intervalo = Util::calculaTempo($hr_saida_intervalo,$hr_volta_intervalo);
                $segundos_intervalo = Util::converterHoraPMinuto($hr_diferenca_intervalo);


                if($segundos_positivo > 0){
                    $diferenca_segundo_positivo = $segundos_positivo - $segundos_intervalo;
                }
                $distancia_entre_pontos =$query[$i]['ds_distancia_ponto'];
                /*if($query[$i]['ds_local_trabalho']!="" && $query[$i]['ds_localizacao']!=""){
                    // Obter as coordenadas dos dois endereços
                  
                    $location1 = $this->getCoordinates($query[$i]['ds_local_trabalho']);
                    $location2 = $this->getCoordinates($query[$i]['ds_localizacao']);
                    
                    // Verifica se as coordenadas foram obtidas com sucesso
                    if ($location1 && $location2) {
                        // Calcula a distância entre os dois pontos
                        $distancia = $this->calcularDistancia($location1['lat'], $location1['lon'], $location2['lat'], $location2['lon']);
                        $distancia_entre_pontos = round($distancia, 2) . " km.";
                    } else {
                        $distancia_entre_pontos = "Não foi possível calcular a distância.";
                    }
                }*/


                $mysql_data[] = array(
                    "pk" => $query[$i]["pk"],
                    "ds_lead"=>$query[$i]['ds_lead'],
                    "ds_re"=>$query[$i]['ds_re'],
                    "ds_pin"=>$query[$i]['ds_pin'],
                    "ds_colaborador"=>$query[$i]['ds_colaborador'],
                    "colaborador_pk"=>$colaborador_pk,
                    "leads_pk"=>$query[$i]['leads_pk'],
                    "ds_produto_servico"=>$query[$i]['ds_produto_servico'],
                    "periodo"=>$query[$i]['periodo'],
                    "n_qtde_dias_semana"=>$query[$i]['n_qtde_dias_semana'],
                    "dt_rh_entratada"=>$query[$i]['dt_rh_entratada'],
                    "hr_escala"=>$horaB." / ".$horac,
                    "hr_escala_intervalo"=>$horaD." / ".$horaE,
                    "segundos"=>$segundos,
                    "ds_local_trabalho"=>$query[$i]['ds_local_trabalho'],
                    "ds_imagem_entrada"=>$query[$i]['ds_imagem_entrada'],
                    "ds_legenda"=>$ds_legenda[$i],
                    "ds_registro_ponto"=>$ds_registro_ponto,
                    "ds_imagem_saida"=>$query[$i]['ds_imagem_saida'],
                    "ds_imagem_sistema"=>$query[$i]['ds_imagem_sistema'],
                    "diferenca_segundo_positivo"=>$diferenca_segundo_positivo,
                    "segundos_positivo"=>$segundos_positivo,
                    "hr_diferenca_intervalo"=>$hr_diferenca_intervalo,
                    "ds_distancia_entre_pontos" =>$distancia_entre_pontos,
                    "hr_diferenca"=>$hr_diferenca_intervalo,
                    "segundos_intervalo"=>$segundos_intervalo
                );
            }
        }

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $mysql_data;

        return $retorno;
    }
    public function relatorioPonto($leads_pk,$colaborador_pk,$dt_ini,$dt_fim,$qtde_lead_colaborador) {
       try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio
            $mysql_data = []; //Retorno data setado como vazio

            $ds_legenda[] = "";
            $hr_saida_intervalo = "";
            $hr_volta_intervalo = "";

            $hr_entrada = ("00:00:00");
            $hr_saida = ("23:59:59");




            $sql ="";
            $sql.="SELECT l.pk,";
            $sql.="       l.ds_lead,";
            $sql.="       l.pk leads_pk,";
            $sql.="       ll.ds_lead ds_lead_antigo,";
            $sql.="       col.pk colaboradores_pk,";
            $sql.="       concat(l.ds_endereco,', ',l.ds_numero,',',l.ds_cidade,',Brasil')ds_local_trabalho,";
            $sql.="       concat(ll.ds_endereco,', ',ll.ds_cidade,', Brasil')ds_local_trabalho_antigo,";
            $sql.="       col.ds_re,";
            $sql.="       col.ds_pin,";
            $sql.="       col.ds_colaborador,";
            $sql.="       ps.ds_produto_servico,";
            $sql.="       pt.tipo_ponto_pk,";
            $sql.="       date_format(pt.dt_hora_ponto,'%Y-%m-%d') dt_hora_ponto,";
            $sql.="       date_format(pt.dt_hora_ponto,'%d/%m/%Y') dt_rh_entratada,";
            $sql.="       date_format(pt.dt_hora_ponto,'%H:%i:%s') hr_entrada,";
            $sql.="       pt.ds_total_horas_trabalhadas,";
            $sql.="       pt.ds_localizacao,";
            $sql.="       pt.ds_imagem ds_imagem_entrada,";
            $sql.="       pt.img_ponto,";
            $sql.="       agp.hr_turno_dom,";
            $sql.="       agp.hr_turno_seg,";
            $sql.="       agp.hr_turno_ter,";
            $sql.="       agp.hr_turno_qua,";
            $sql.="       agp.hr_turno_qui,";
            $sql.="       agp.hr_turno_sex,";
            $sql.="       agp.hr_turno_sab,";
            $sql.="       agp.hr_turno_dom_saida,";
            $sql.="       agp.hr_turno_seg_saida,";
            $sql.="       agp.hr_turno_ter_saida,";
            $sql.="       agp.hr_turno_qua_saida,";
            $sql.="       agp.hr_turno_qui_saida,";
            $sql.="       agp.hr_turno_sex_saida,";
            $sql.="       agp.hr_turno_sab_saida,";
            $sql.="       agp.hr_intervalo_seg,";
            $sql.="       agp.hr_intervalo_ter,";
            $sql.="       agp.hr_intervalo_qua,";
            $sql.="       agp.hr_intervalo_qui,";
            $sql.="       agp.hr_intervalo_sex,";
            $sql.="       agp.hr_intervalo_sab,";
            $sql.="       agp.hr_intervalo_saida_seg,";
            $sql.="       agp.hr_intervalo_saida_ter,";
            $sql.="       agp.hr_intervalo_saida_qua,";
            $sql.="       agp.hr_intervalo_saida_qui,";
            $sql.="       agp.hr_intervalo_saida_sex,";
            $sql.="       agp.hr_intervalo_saida_sab,";
            $sql.="       agp.ic_dom,";
            $sql.="       agp.ic_seg,";
            $sql.="       agp.ic_ter,";
            $sql.="       agp.ic_qua,";
            $sql.="       agp.ic_qui,";
            $sql.="       agp.ic_sex,";
            $sql.="       agp.ic_sab,";
            $sql.="    agp.n_qtde_dias_semana,  ";
            $sql.="       pt.ds_imagem ds_imagem_saida,";
            $sql.="       pt.ds_distancia_ponto,";
            $sql.="       pt.ds_img ds_imagem_saida_antigo,";
            $sql.="       psl.ds_link_imagem_cadastro ds_imagem_sistema,";
            $sql.="       psl.img_colaborador_cadastro,";
            $sql.="       psl.ds_imagem ds_imagem_sistema_antiga";
            $sql.="  FROM ponto pt";
            $sql.="       INNER JOIN colaboradores col ON pt.colaborador_pk = col.pk";
            $sql.="       left join colaboradores_produtos_servicos cps on col.pk = cps.colaboradores_pk";
            $sql.="       left JOIN produtos_servicos ps ON cps.produtos_servicos_pk = ps.pk";
            $sql.="       INNER JOIN agenda_colaborador_padrao agp ON pt.colaborador_pk = agp.colaboradores_pk";
            $sql.="       LEFT JOIN leads l ON pt.leads_pk = l.pk";

            $sql.="       LEFT JOIN processos_etapas pe ON agp.processos_etapas_pk = pe.pk";
            $sql.="       LEFT JOIN processos pro ON pe.processos_pk = pro.pk";
            $sql.="       LEFT JOIN leads ll ON pro.leads_pk = ll.pk";
            $sql.="       left join ponto_solicitacao_liberacao_app psl on col.pk = psl.colaborador_pk";
            $sql.=" where 1=1 ";
            //$sql.=" and pt.img_ponto is not null   ";
            $sql.=" and pt.dt_hora_ponto between '".Util::DataYMD($dt_ini)." ".$hr_entrada."' and '".Util::DataYMD($dt_fim)." ".$hr_saida."'";

            if($leads_pk != ""){
                $sql.=" and (l.pk = ".$leads_pk." OR ll.pk= ".$leads_pk.")";
            }

            if($colaborador_pk != ""){
                $sql.=" and col.pk  = ".$colaborador_pk;
            }
            /*if($leads_pk != ""){
                $sql.=" and l.pk = ".$leads_pk;
            }*/
            $sql.=" and agp.dt_cancelamento is null";

            $sql.=" group by DATE_FORMAT(pt.dt_hora_ponto, '%H:%i:%s'),pt.tipo_ponto_pk ";
            $sql.=" order by col.ds_colaborador, pt.dt_hora_ponto asc ";
        
    
            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if(count($query) > 0){

                for($i=0;$i < count($query);$i++){
                    $ds_total_horas_trabalhadas = "";
                    $coordernadas_lead = "";
                    $latitude_lead = "";
                    $longitude_lead = "";
                    $latitude_ponto = "";
                    $latitude_ponto = "";
                    $distancia_entre_pontos = "";
                    $endereco_ponto = "";
                    $ds_registro_ponto = "";

                    $diasemana_numero = date('w', strtotime(Util::DataYMD($query[$i]['dt_rh_entratada'])));

                    $horaA = "";
                    $horaB = "";
                    $horaD = "";
                    $horaE = "";
                    $hr_diferenca = "";
                    $hr_diferenca_positivo = "";
                    $diferenca_segundo_positivo = "";

                    if($diasemana_numero==0){
                        //if($query[$i]['ic_dom']==1){
                        $horaA = $query[$i]['hr_entrada'];
                        $horaB = $query[$i]['hr_turno_dom'].':00';
                        $horac = $query[$i]['hr_turno_dom_saida'].':00';

                        if($query[$i]['hr_intervalo_dom']!=""){
                            $horaD = $query[$i]['hr_intervalo_dom'].':00';
                        }
                        if($query[$i]['hr_intervalo_saida_dom']!=""){
                            $horaE = $query[$i]['hr_intervalo_saida_dom'].':00';
                        }


                        $hr_diferenca = Util::calculaTempo($horaB, $horaA);
                        //}

                    }
                    else if($diasemana_numero==1){
                        //if($query[$i]['ic_seg']==1){
                        $horaA = $query[$i]['hr_entrada'];
                        $horaB = $query[$i]['hr_turno_seg'].':00';
                        $horac = $query[$i]['hr_turno_seg_saida'].':00';
                        if($query[$i]['hr_intervalo_seg']!=""){
                            $horaD = $query[$i]['hr_intervalo_seg'].':00';
                        }
                        if($query[$i]['hr_intervalo_saida_seg']!=""){
                            $horaE = $query[$i]['hr_intervalo_saida_seg'].':00';
                        }

                        $hr_diferenca = Util::calculaTempo($horaB, $horaA);
                        //}
                    }
                    else if($diasemana_numero==2){
                        //if($query[$i]['ic_ter']==1){
                        $horaA = $query[$i]['hr_entrada'];
                        $horaB = $query[$i]['hr_turno_ter'].':00';
                        $horac = $query[$i]['hr_turno_ter_saida'].':00';

                        if($query[$i]['hr_intervalo_ter']!=""){
                            $horaD = $query[$i]['hr_intervalo_ter'].':00';
                        }
                        if($query[$i]['hr_intervalo_saida_ter']!=""){
                            $horaE = $query[$i]['hr_intervalo_saida_ter'].':00';
                        }

                        $hr_diferenca = Util::calculaTempo($horaB, $horaA);

                        //}
                    }
                    else if($diasemana_numero==3){
                        //if($query[$i]['ic_qua']==1){
                        $horaA = $query[$i]['hr_entrada'];
                        $horaB = $query[$i]['hr_turno_qua'].':00';
                        $horac = $query[$i]['hr_turno_qua_saida'].':00';
                        if($query[$i]['hr_intervalo_qua']!=""){
                            $horaD = $query[$i]['hr_intervalo_qua'].':00';
                        }
                        if($query[$i]['hr_intervalo_saida_qua']!=""){
                            $horaE = $query[$i]['hr_intervalo_saida_qua'].':00';
                        }

                        $hr_diferenca = Util::calculaTempo($horaB, $horaA);
                        //}
                    }
                    else if($diasemana_numero==4){
                        //if($query[$i]['ic_qui']==1){
                        $horaA = $query[$i]['hr_entrada'];
                        $horaB = $query[$i]['hr_turno_qui'].':00';
                        $horac = $query[$i]['hr_turno_qui_saida'].':00';
                        if($query[$i]['hr_intervalo_qui']!=""){
                            $horaD = $query[$i]['hr_intervalo_qui'].':00';
                        }
                        if($query[$i]['hr_intervalo_saida_qui']!=""){
                            $horaE = $query[$i]['hr_intervalo_saida_qui'].':00';
                        }

                        $hr_diferenca = Util::calculaTempo($horaB, $horaA);

                        //}
                    }
                    else if($diasemana_numero==5){
                        //if($query[$i]['ic_sex']==1){
                        $horaA = $query[$i]['hr_entrada'];
                        $horaB = $query[$i]['hr_turno_sex'].':00';
                        $horac = $query[$i]['hr_turno_sex_saida'].':00';
                        if($query[$i]['hr_intervalo_sex']!=""){
                            $horaD = $query[$i]['hr_intervalo_sex'].':00';
                        }
                        if($query[$i]['hr_intervalo_saida_sex']!=""){
                            $horaE = $query[$i]['hr_intervalo_saida_sex'].':00';
                        }

                        $hr_diferenca = Util::calculaTempo($horaB, $horaA);
                        //}
                    }

                    else if($diasemana_numero==6){
                        //if($query[$i]['ic_sab']==1){
                        $horaA = $query[$i]['hr_entrada'];
                        $horaB = $query[$i]['hr_turno_sab'].':00';
                        $horac = $query[$i]['hr_turno_sab_saida'].':00';
                        if($query[$i]['hr_intervalo_sab']!=""){
                            $horaD = $query[$i]['hr_intervalo_sab'].':00';
                        }
                        if($query[$i]['hr_intervalo_saida_sab']!=""){
                            $horaE = $query[$i]['hr_intervalo_saida_sab'].':00';
                        }


                        $hr_diferenca = Util::calculaTempo($horaB, $horaA);
                        //}
                    }


                    $hr_diferenca_positivo = 0;
                    $segundos_positivo =0;
                    $segundos = 0;
                    if($horaD!="" && $horaE!=""){
                        $hr_diferenca_positivo = Util::calculaTempo($horaD, $horaE);



                        $segundos_positivo = Util::converterHoraPMinuto($hr_diferenca_positivo);


                        $segundos=Util::converterHoraPMinuto($hr_diferenca);
                    }



                    //if($i==0){

                        if($query[$i]['tipo_ponto_pk']==1){
                            $hr_entrada = $query[$i]['hr_entrada'];
                            $ds_registro_ponto = $query[$i]['hr_entrada'];
                            $ds_legenda[$i] = "Inicio Expediente";
                            $ds_total_horas_trabalhadas = gmdate('H:i:s', strtotime(date('H:i:s')) - strtotime($query[$i]['hr_entrada']));
                        }
                        if($query[$i]['tipo_ponto_pk']==2){

                            $ds_registro_ponto = $query[$i]['hr_entrada'];
                            $hr_saida = $query[$i]['hr_entrada'];
                            $ds_legenda[$i] = "Fim Expediente";
                            $ds_total_horas_trabalhadas = gmdate('H:i:s', strtotime($hr_saida) - strtotime($hr_entrada));

                        }
                        if($query[$i]['tipo_ponto_pk']==3){

                            $dt_rh_saida_intervalo = $query[$i]['hr_entrada'];
                            $dt_hora_ponto_saida_intervalo = $query[$i]['dt_hora_ponto'];
                            $ds_registro_ponto = $query[$i]['hr_entrada'];
                            $ds_legenda[$i] = "Saída p/ Intervalo";

                        }
                        if($query[$i]['tipo_ponto_pk']==4){

                            $dt_rh_entratada_retorno = $query[$i]['hr_entrada'];
                            $dt_hora_ponto_entrada_retorno = $query[$i]['dt_hora_ponto'];

                            $ds_registro_ponto = $query[$i]['hr_entrada'];
                            $ds_legenda[$i] = "Retorno do Intervalo";

                        }

                    /*}
                    else{
                        if($query[$i]['tipo_ponto_pk']==1){
                            $hr_diferenca_ponto = Util::calculaTempo($query[0]['hr_entrada'],$query[$i]['hr_entrada']);

                            $segundos_ponto = Util::converterHoraPMinuto($hr_diferenca_ponto);

                            if($segundos_ponto<="24200"){
                                $ds_registro_ponto = $query[$i]['hr_entrada'];
                                $ds_legenda[$i] = "Retorno do Intervalo";
                            }
                            else if($segundos_ponto > "24200" && $segundos_ponto < "25000"){
                                $ds_registro_ponto = $query[$i]['hr_entrada'];
                                $ds_legenda[$i] = "Inicio Expediente";

                            }
                            else if($segundos_ponto > "25000"){
                                $ds_registro_ponto = $query[$i]['hr_entrada'];
                                $ds_legenda[$i] = "Retorno do Intervalo";

                            }
                        }
                        if($query[$i]['tipo_ponto_pk']==2){

                            $hr_diferenca_ponto = Util::calculaTempo($query[0]['hr_entrada'],$query[$i]['hr_entrada']);
                            $segundos_ponto = Util::converterHoraPMinuto($hr_diferenca_ponto);

                            if($segundos_ponto<="25200"){
                                if(($i+1)==count($query)){
                                    $ds_registro_ponto = $query[$i]['hr_entrada'];
                                    $hr_saida = $query[$i]['hr_entrada'];
                                    $ds_legenda[$i] = "Fim Expediente";
                                    $ds_total_horas_trabalhadas = gmdate('H:i:s', strtotime($hr_saida) - strtotime($hr_entrada));
                                }
                                else{
                                    $ds_registro_ponto = $query[$i]['hr_entrada'];
                                    $ds_legenda[$i] = "Saída p/ Intervalo";
                                }

                            }
                            else if($segundos_ponto > "25200"){
                                $ds_registro_ponto = $query[$i]['hr_entrada'];
                                $hr_saida = $query[$i]['hr_entrada'];
                                $ds_legenda[$i] = "Fim Expediente";
                            }

                        }
                        if($query[$i]['tipo_ponto_pk']==3){

                            $dt_rh_saida_intervalo = $query[$i]['hr_entrada'];
                            $dt_hora_ponto_saida_intervalo = $query[$i]['dt_hora_ponto'];
                            $ds_registro_ponto = $query[$i]['hr_entrada'];
                            $ds_legenda[$i] = "Saída p/ Intervalo";

                        }
                        if($query[$i]['tipo_ponto_pk']==4){

                            $dt_rh_entratada_retorno = $query[$i]['hr_entrada'];
                            $dt_hora_ponto_entrada_retorno = $query[$i]['dt_hora_ponto'];

                            $ds_registro_ponto = $query[$i]['hr_entrada'];
                            $ds_legenda[$i] = "Retorno do Intervalo";

                        }
                    }*/


                    //CALCULA O ATRASO NA VOLTA DO INTERVALO
                    if($ds_legenda[$i]=="Saída p/ Intervalo"){
                        $hr_saida_intervalo = $ds_registro_ponto;
                    }
                    if($ds_legenda[$i]=="Retorno do Intervalo"){
                        $hr_volta_intervalo = $ds_registro_ponto;
                    }

                    $hr_diferenca_intervalo =0;
                    $segundos_intervalo = 0;

                    if($hr_saida_intervalo!="" && $hr_volta_intervalo!=""){
                        $hr_diferenca_intervalo = Util::calculaTempo($hr_saida_intervalo,$hr_volta_intervalo);
                        $segundos_intervalo = Util::converterHoraPMinuto($hr_diferenca_intervalo);
                    }


                    if($segundos_positivo > 0){
                        $diferenca_segundo_positivo = $segundos_positivo - $segundos_intervalo;
                    }

                  
                    $endereco_ponto = "";
                    $distancia_entre_pontos =$query[$i]['ds_distancia_ponto'];
                    /*if($query[$i]['ds_local_trabalho']!="" && $query[$i]['ds_localizacao']!=""){
                        // Obter as coordenadas dos dois endereços
                        $location1 = $this->getCoordinates($query[$i]['ds_local_trabalho']);
                        $location2 = $this->getCoordinates($query[$i]['ds_localizacao']);

                        // Verifica se as coordenadas foram obtidas com sucesso
                        if ($location1 && $location2) {
                            // Calcula a distância entre os dois pontos
                            $distancia = $this->calcularDistancia($location1['lat'], $location1['lon'], $location2['lat'], $location2['lon']);
                            $distancia_entre_pontos = round($distancia, 2) . " km.";
                        } else {
                            $distancia_entre_pontos = "Não foi possível calcular a distância.";
                        }
                    }*/

                    if(empty($query[$i]['ds_lead'])){
                        $ds_lead = $query[$i]['ds_lead_antigo'];
                    }
                    else{
                        $ds_lead = $query[$i]['ds_lead'];
                    }
                    if(empty($query[$i]['ds_imagem_saida'])){
                        $ds_imagem_saida = $query[$i]['ds_imagem_saida_antigo'];
                    }
                    else{
                        $ds_imagem_saida = $query[$i]['ds_imagem_saida'];
                    }
                    if(empty($query[$i]['ds_imagem_sistema'])){
                        $ds_imagem_sistema = $query[$i]['ds_imagem_sistema_antiga'];
                    }
                    else{
                        $ds_imagem_sistema = $query[$i]['ds_imagem_sistema'];
                    }
                    if(empty($query[$i]['ds_local_trabalho'])){
                        $ds_local_trabalho = $query[$i]['ds_local_trabalho_antigo'];
                    }
                    else{
                        $ds_local_trabalho = $query[$i]['ds_local_trabalho'];
                    }
                    if (strpos($query[$i]['img_ponto'], 'data:image/png;base64') !== false) {
                        $arr = (explode("data:image/png;base64,",$query[$i]['img_ponto']));
                    
                        $img_ponto = $arr[1];
                    } else {
                        $img_ponto = $query[$i]['img_ponto'];
                    }
                    if($colaborador_pk==1564){
                        $img_colab = "";
                    }
                    else{
                        $img_colab = $query[$i]['img_colaborador_cadastro'];
                    }

                    // Garante que $img_ponto tenha o prefixo
                    if (strpos($img_ponto, 'data:image') !== 0) {
                        $img_ponto = 'data:image/png;base64,' . $img_ponto;
                    }

                    // Garante que $img_colab tenha o prefixo
                    if (strpos($img_colab, 'data:image') !== 0) {
                        $img_colab = 'data:image/png;base64,' . $img_colab;
                    }
                    $mysql_data[] = array(
                        "pk" => $query[$i]["pk"],
                        "ds_lead"=>$ds_lead,
                        "ds_re"=>$query[$i]['ds_re'],
                        "ds_pin"=>$query[$i]['ds_pin'],
                        "ds_colaborador"=>$query[$i]['ds_colaborador'],
                        "colaborador_pk"=>$colaborador_pk,
                        "hora_saida"=>$hr_saida,
                        "leads_pk"=>$query[$i]['leads_pk'],
                        "ds_produto_servico"=>$query[$i]['ds_produto_servico'],
                        //"periodo"=>$query[$i]['periodo'],
                        "periodo"=>"",
                        "n_qtde_dias_semana"=>$query[$i]['n_qtde_dias_semana'],
                        "dt_rh_entratada"=>$query[$i]['dt_rh_entratada'],
                        "hr_escala"=>$horaB." / ".$horac,
                        "hr_escala_intervalo"=>$horaD." / ".$horaE,
                        "segundos"=>$segundos,
                        "ds_local_trabalho"=>$ds_local_trabalho,
                        "ds_imagem_entrada"=>$query[$i]['ds_imagem_entrada'],
                        "img_ponto"=>'<img width="60" height="60" src="'. ($img_ponto).'">',
                        //"teste"=>'<img width="60" height="60" src="data:image/png;base64,'. ($img_ponto).'">',
                        "img_colaborador_cadastro"=>"<img width='60' height='60' src='". ($img_colab)."'>",
                        "ds_legenda"=>$ds_legenda[$i],
                        "ds_registro_ponto"=>$ds_registro_ponto,
                        "ds_imagem_saida"=>$ds_imagem_saida,
                        "ds_imagem_sistema"=>$ds_imagem_sistema,
                        "diferenca_segundo_positivo"=>$diferenca_segundo_positivo,
                        "segundos_positivo"=>$segundos_positivo,
                        "hr_diferenca_intervalo"=>$hr_diferenca_intervalo,
                        "hr_diferenca"=>$hr_diferenca_intervalo,
                        "segundos_intervalo"=>$segundos_intervalo,
                        "ds_distancia_entre_pontos" =>$distancia_entre_pontos,
                        "ds_localizacao"=>$query[$i]['ds_localizacao'],
                        "ds_total_horas_trabalhadas"=>$ds_total_horas_trabalhadas,
                    );
                    
                }
            }
            $retorno->status = true;
            $retorno->message = 'Dados carregados com sucesso';
            $retorno->data = $mysql_data;
        
            return $retorno;
       }
       catch(Throwable $e){
        print_r($e->getMessage());
        die();
       }
    }
    public function reloginhoHistoricoPonto($leads_pk,$colaborador_pk,$dt_ini,$dt_fim,$agenda_colaborador_padrao_pk) {
       try{
            $retorno = new \StdClass; //Estrutura de retorno para controller
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio
            $mysql_data = []; //Retorno data setado como vazio

            $ds_legenda[] = "";

            $hr_entrada = ("00:00:00");
            $hr_saida = ("23:59:59");

            $turnos_pk = (new PontoFolha($this->pdo))->listarTurnosPk($agenda_colaborador_padrao_pk);
            $query =[];

            if ($turnos_pk == 3) {
                $query = $this->pegarPontoNoturno($dt_ini,$dt_fim,$colaborador_pk,$leads_pk,$agenda_colaborador_padrao_pk);
            } else {
                $query = $this->pegarPontoNormal($dt_ini,$dt_fim,$colaborador_pk,$leads_pk);
            }
            if(count($query) > 0){
                for($i=0;$i < count($query);$i++){
                    $ds_registro_ponto = "";
                    if($query[$i]['tipo_ponto_pk']==1){
                        $ds_registro_ponto = $query[$i]['hr_entrada'];
                        $ds_legenda[$i] = "Inicio Expediente";
                    }
                    if($query[$i]['tipo_ponto_pk']==2){

                        $ds_registro_ponto = $query[$i]['hr_entrada'];
                        $hr_saida = $query[$i]['hr_entrada'];
                        $ds_legenda[$i] = "Fim Expediente";
                    }
                    if($query[$i]['tipo_ponto_pk']==3){

                        $ds_registro_ponto = $query[$i]['hr_entrada'];
                        $ds_legenda[$i] = "Saída p/ Intervalo";

                    }
                    if($query[$i]['tipo_ponto_pk']==4){

                        $ds_registro_ponto = $query[$i]['hr_entrada'];
                        $ds_legenda[$i] = "Retorno do Intervalo";

                    }

                    if(empty($query[$i]['ds_lead'])){
                        $ds_lead = $query[$i]['ds_lead_antigo'];
                    }
                    else{
                        $ds_lead = $query[$i]['ds_lead'];
                    }
                    if(empty($query[$i]['ds_imagem_saida'])){
                        $ds_imagem_saida = $query[$i]['ds_imagem_saida_antigo'];
                    }
                    else{
                        $ds_imagem_saida = $query[$i]['ds_imagem_saida'];
                    }
                    if(empty($query[$i]['ds_imagem_sistema'])){
                        $ds_imagem_sistema = $query[$i]['ds_imagem_sistema_antiga'];
                    }
                    else{
                        $ds_imagem_sistema = $query[$i]['ds_imagem_sistema'];
                    }
                    if(empty($query[$i]['ds_local_trabalho'])){
                        $ds_local_trabalho = $query[$i]['ds_local_trabalho_antigo'];
                    }
                    else{
                        $ds_local_trabalho = $query[$i]['ds_local_trabalho'];
                    }
                    if (strpos($query[$i]['img_ponto'], 'data:image/png;base64') !== false) {
                        $arr = (explode("data:image/png;base64,",$query[$i]['img_ponto']));
                    
                        $img_ponto = $arr[1];
                    } else {
                        $img_ponto = $query[$i]['img_ponto'];
                    }
                    if($colaborador_pk==1564){
                        $img_colab = "";
                    }
                    else{
                        $img_colab = $query[$i]['img_colaborador_cadastro'];
                    }

                    // Garante que $img_ponto tenha o prefixo
                    if (strpos($img_ponto, 'data:image') !== 0) {
                        $img_ponto = 'data:image/png;base64,' . $img_ponto;
                    }

                    // Garante que $img_colab tenha o prefixo
                    if (strpos($img_colab, 'data:image') !== 0) {
                        $img_colab = 'data:image/png;base64,' . $img_colab;
                    }
                    $ds_localizacao = Util::fcTransformarCoordenadasEmEndereco($query[$i]['ds_latitude'],$$query[$i]['ds_longitude']);
                    $mysql_data[] = array(
                        "pk" => $query[$i]["pk"],
                        "pontos_pk" => $query[$i]["pontos_pk"],
                        "ds_lead"=>$ds_lead,
                        "ds_re"=>$query[$i]['ds_re'],
                        "ds_pin"=>$query[$i]['ds_pin'],
                        "ds_colaborador"=>$query[$i]['ds_colaborador'],
                        "colaborador_pk"=>$colaborador_pk,
                        "hora_saida"=>$hr_saida,
                        "leads_pk"=>$query[$i]['leads_pk'],
                        "ds_produto_servico"=>$query[$i]['ds_produto_servico'],
                        "ds_distancia_ponto"=>$query[$i]['ds_distancia_ponto'],
                        "periodo"=>"",
                        "n_qtde_dias_semana"=>$query[$i]['n_qtde_dias_semana'],
                        "ic_validacao_facial"=>$query[$i]['ic_validacao_facial'],
                        "dt_rh_entratada"=>$query[$i]['dt_rh_entratada'],
                        "ds_local_trabalho"=>$ds_local_trabalho,
                        "ds_imagem_entrada"=>$query[$i]['ds_imagem_entrada'],
                        "img_ponto"=>'<img width="80" height="80" src="'. ($img_ponto).'">',
                        "img_colaborador_cadastro"=>"<img width='80' height='80' src='". ($img_colab)."'>",
                        "ds_legenda"=>$ds_legenda[$i],
                        "ds_registro_ponto"=>$ds_registro_ponto,
                        "ds_imagem_saida"=>$ds_imagem_saida,
                        "ds_imagem_sistema"=>$ds_imagem_sistema,
                        "ds_localizacao"=>$ds_localizacao,
                    );
                    
                }
            }
            $retorno->status = true;
            $retorno->message = 'Dados carregados com sucesso';
            $retorno->data = $mysql_data;
        
            return $retorno;
       }
       catch(Throwable $e){
        print_r($e->getMessage());
        die();
       }
    }

    public function relAcompanhamentoPontoSintetico($leads_pk,$colaborador_pk,$ic_cliente,$dt_periodo_ini,$dt_periodo_fim) {
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        //PEGA PRIMEIRO OS COLABORADORES QUE TEM PONTO NO PERIODO
        $sql ="";
        $sql.=" Select ";
        $sql.="    acp.pk agenda_colaborador_padaro_pk ";
        $sql.="    , acp.colaboradores_pk  ";
        $sql.="    , l.ds_lead ds_posto_trabalho";
        $sql.="    , c.ds_cel ";
        $sql.="    , c.ds_colaborador ";
        $sql.="    , ps.ds_produto_servico ds_funcao";
        $sql.="    , acp.n_qtde_dias_semana ds_escala ";
        $sql.="    , t.ds_turno ";
        $sql.="    , acp.hr_inicio_expediente ";
        $sql.="    , acp.hr_termino_expediente";
        $sql.=" from agenda_colaborador_padrao acp ";
        $sql.="    inner join ponto_solicitacao_liberacao_app psla on acp.colaboradores_pk = psla.colaborador_pk ";
        $sql.="    inner join escala_dados_colaborador edc on acp.pk = edc.agenda_colaborador_padrao ";
        $sql.="    inner join colaboradores c on acp.colaboradores_pk = c.pk";
        $sql.="      left join produtos_servicos ps on acp.produtos_servicos_pk = ps.pk ";
        $sql.="      left join leads l on acp.leads_pk = l.pk ";
        $sql.="      left join turnos t on acp.turnos_pk = t.pk ";
        $sql.="    where psla.dt_liberacao is not null";
        $sql.=" and acp.dt_cancelamento is null";
        $sql.=" and c.ic_status = 1";
        $sql.=" and edc.dt_escala >='".Util::DataYMD($dt_periodo_ini)." 00:00:00'";
        $sql.=" and edc.dt_escala <='".Util::DataYMD($dt_periodo_fim)." 23:59:59'";

        if($leads_pk!=""){
            $sql.=" and l.pk = ".$leads_pk;
        }
        if($ic_cliente!=""){
            $sql.=" and l.ic_cliente = ".$ic_cliente;
        }
        if($colaborador_pk!=""){
            $sql.=" and c.pk = ".$colaborador_pk;
        }
        $sql.=" group by acp.pk ";
        $sql.=" order by l.ds_lead, c.ds_colaborador ";


        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if(count($query) > 0){
            for($i = 0; $i < count($query); $i++){

            }

        }
        else{
            $result = [];
        }

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result;


        return $retorno;
    }
    


    





    public function verificarApontamentoAgenda($colaborador_pk, $dt_escala) {
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio


        $sql="";
        $sql.="select acp.colaborador_pk, acp.tipo_apontamento_pk,  date_format(acp.dt_apontamento, '%d/%m/%Y') dt_apontamento";
        $sql.="  from agenda_colaborador_apontamento acp";
        $sql.=" where 1=1";
        if($colaborador_pk != ""){
            $sql.="   and acp.colaborador_pk =" .$colaborador_pk;
        }
        if($dt_escala != ""){
            $sql.=" and date_format(dt_apontamento, '%Y/%m/%d') = date_format('".$dt_escala."', '%Y/%m/%d')";
        }



        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $rows;

        return $retorno;
    }

    public function popUpAtraso($dt_ini,$dt_fim,$diasemana_numero,$ic_inverter_folga, $leads_pk,
    $colaborador_pk,
    $turnos_pk,
    $funcao_pk) {
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        $result = []; //Retorno data setado como vazio

        

        $sql ="";
        $sql.=" Select ";
        $sql.="    acp.pk agenda_colaborador_padaro_pk ";
        $sql.="    , acp.colaboradores_pk  ";
        $sql.="    , l.ds_lead ds_posto_trabalho";
        $sql.="    , c.ds_cel ";
        $sql.="    , c.ds_colaborador ";
        $sql.="    , ps.ds_produto_servico ds_funcao";
        $sql.="    , acp.n_qtde_dias_semana ds_escala ";
        $sql.="    , t.ds_turno ";
        $sql.="    , acp.hr_inicio_expediente ";
        $sql.="    , acp.hr_termino_expediente";
        $sql.="    , edc.dt_escala ";
        $sql.=" from agenda_colaborador_padrao acp ";
        $sql.="    INNER join escala_dados_colaborador edc on acp.pk = edc.agenda_colaborador_padrao ";
        $sql.="    INNER join ponto_solicitacao_liberacao_app psla on acp.colaboradores_pk = psla.colaborador_pk ";
        $sql.="    INNER join colaboradores c on acp.colaboradores_pk = c.pk";
        
        $sql.="    INNER join produtos_servicos ps on acp.produtos_servicos_pk = ps.pk";


        $sql.="    left join turnos t on acp.turnos_pk = t.pk";
        $sql.="    left join leads l on acp.leads_pk = l.pk";
        $sql.="    where psla.dt_liberacao is not null";
        $sql.=" and acp.dt_cancelamento is null";
        $sql.=" and c.ic_status = 1";
        $sql.=" and edc.dt_escala >='".Util::DataYMD($dt_ini)." 00:00:00'";
        $sql.=" and edc.dt_escala <='".Util::DataYMD($dt_ini)." 23:59:59'";

        if($leads_pk!=""){
            $sql.=" and l.pk = ".$leads_pk;
        }
        if($colaborador_pk!=""){
            $sql.=" and c.pk = ".$colaborador_pk;
        }
        if($turnos_pk!=""){
            $sql.=" and t.pk = ".$turnos_pk;
        }
        if($funcao_pk!=""){
            $sql.=" and ps.pk = ".$funcao_pk;
        }
        $sql.=" and edc.ic_escala = 1";
        $sql.=" group by acp.pk ";
        $sql.=" order by acp.hr_inicio_expediente, l.ds_lead, c.ds_colaborador ";
        


        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        if(count($query) > 0){
            for($i = 0; $i < count($query); $i++){
                $arrApontamento = (new PontoFolha($this->pdo))->listarDadosApontamento(Util::DataYMD($dt_ini), $query[$i]["colaboradores_pk"], $query[$i]["pk"],0);
                
                if(count($arrApontamento[0]['arrApontamento']) > 0){
                    $arrDadosApontamento = $arrApontamento[0]['arrApontamento'];
                    for($a=0;$a<count($arrDadosApontamento);$a++){

                        $tipo = (int)$arrApontamento[0]['tipo_apontamento_pk'];
                        $tipoComp = (int)$arrDadosApontamento[$a]['tipo_apontamento_dados_pk'];

                        if($tipo == $tipoComp){

                            //AFASTAMENTO E FERIAS
                            if($tipo!=5 || $tipo!=6){
                                $ds_status = "";
                                $ic_status = "";
                                //Bateram o Ponto mesmo que atrasado
                                $dt_hora_escala = Util::DataYMD($dt_ini)." ".$query[$i]['hr_inicio_expediente'];
                                $TotalTempoAtraso = 0;
                                $sql ="";
                                $sql.=" select";
                                $sql.="    p.pk";
                                $sql.="    ,p.colaborador_pk";
                                $sql.="    ,date_format(p.dt_hora_ponto, '%H:%i') hr_ponto";
                                $sql.="    ,TIMESTAMPDIFF(minute , '".$dt_hora_escala."' , p.dt_hora_ponto) atraso";
                                $sql.=" from ponto p";
                                $sql.="     where p.colaborador_pk = ".$query[$i]["colaboradores_pk"];
                                $sql.="     and p.tipo_ponto_pk = 1";
                                $sql.="     and p.dt_hora_ponto >='".Util::DataYMD($dt_ini)." 00:00:00'";
                                $sql.="     and p.dt_hora_ponto <='".Util::DataYMD($dt_ini)." 23:59:59'";
                                $sql.=" order by TIMESTAMPDIFF(minute , '".$dt_hora_escala."' , p.dt_hora_ponto) desc";
                                $stmt = $this->pdo->prepare( $sql );
                                $stmt->execute();
                                $query1 = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                                if(count($query1) == 0){
                                    //NÃO BATERAM O PONTO
                                    $dt_hora_atual = date('y-m-d H:i');
                                    $ds_status = "Ponto Não Registrado";
                                    $sql ="";
                                    $sql.=" select  TIMESTAMPDIFF(MINUTE , '".$dt_hora_escala."' , '".$dt_hora_atual."') atraso";

                                    $stmt = $this->pdo->prepare( $sql );
                                    $stmt->execute();
                                    $query1 = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                                    $atraso = Util::minuto2Hora($query1[0]['atraso']);
                                    if($query1[0]['atraso'] <=5 && $query1[0]['atraso'] > 0 ){
                                        $ic_status = 5;
                                        $TotalTempoAtraso = "";
                                    }elseif ($query1[0]['atraso'] >5 && $query1[0]['atraso'] <=10){
                                        $ic_status = 10;
                                        $TotalTempoAtraso = $atraso;
                                    }else if ($query1[0]['atraso'] > 10){
                                        $ic_status = 25;
                                        $TotalTempoAtraso = $atraso;
                                    }
                                }

                                $result[] = array (
                                    'agenda_colaborador_padaro_pk'=>$query[$i]['agenda_colaborador_padaro_pk'],
                                    'colaborador_pk'=>$query[$i]['colaboradores_pk'],
                                    'ds_posto_trabalho'=>$query[$i]['ds_posto_trabalho'],
                                    'ds_cel'=>$query[$i]['ds_cel'],
                                    'ds_colaborador'=>$query[$i]['ds_colaborador'],
                                    'ds_funcao'=>$query[$i]['ds_funcao'],
                                    'ds_escala'=>$query[$i]['ds_escala'],
                                    'ds_turno'=>$query[$i]['ds_turno'],
                                    'hr_inicio_expediente'=>$query[$i]['hr_inicio_expediente'],
                                    'ds_status'=>$ds_status,
                                    'ic_status'=>$ic_status,
                                    'TotalTempoAtraso'=>$TotalTempoAtraso,
                                );
                            }
                        }
                    }
                }
            }
        }

    
        $retorno->status = true;
        $retorno->message = 'Dados carregados com sucesso';
        $retorno->data = $result;
        $retorno->iTotalDisplayRecords = count($query);
        $retorno->iTotalRecords = count($query);

        
        
        

        echo json_encode($retorno);
        exit(0);
    }

    

    
    public function webPontoRetornaUltPontoColaboradorPosto($dados){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        try{
            $sql = "";
            $sql.="SELECT max(p.pk) ponto_pk";
            $sql.="    FROM ponto p";
            $sql.="     INNER JOIN leads l ON p.leads_pk = l.pk";
            //$sql.="    WHERE p.colaborador_pk = ".$dados['colaborador_pk'];
            $sql.="    WHERE 1=1";
            if(!empty($dados['agenda_colaborador_padrao_pk'])){
                $sql.="     AND p.agenda_colaborador_padrao_pk = ".$dados['agenda_colaborador_padrao_pk'];
            }
            if(!empty($dados['leads_pk'])){
                $sql.="     AND p.leads_pk = ".$dados['leads_pk'];
            }
            if(!empty($dados['colaborador_pk'])){
                $sql.="     AND p.colaborador_pk = ".$dados['colaborador_pk'];
            }



            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if(count($query) > 0){

                $sql = "";
                $sql.="SELECT p.pk ponto_pk,";
                $sql.="        p.tipo_ponto_pk,";
                $sql.="        date_format(p.dt_hora_ponto,'%Y-%m-%d')dt_ponto,";
                $sql.="        TIME_FORMAT(p.dt_hora_ponto,'%H:%i')hr_ponto,";
                $sql.="        CASE";
                $sql.="            WHEN p.tipo_ponto_pk = 1 THEN 'Inicio de Expediente'";
                $sql.="            WHEN p.tipo_ponto_pk = 2 THEN 'Término de Expediente'";
                $sql.="            WHEN p.tipo_ponto_pk = 3 THEN 'Início de Intervalo'";
                $sql.="            WHEN p.tipo_ponto_pk = 4 THEN 'Término de intervalo'";
                $sql.="        END ds_tipo_ponto,";
                $sql.="        l.ds_lead";
                $sql.="    FROM ponto p";
                $sql.="     INNER JOIN leads l ON p.leads_pk = l.pk";
                $sql.="    WHERE p.pk=".$query[0]["ponto_pk"];
                if(!empty($dados['colaborador_pk'])){
                    $sql.="     AND p.colaborador_pk = ".$dados['colaborador_pk'];
                }

                $stmt = $this->pdo->prepare( $sql );
                $stmt->execute();
                $query1 = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $retorno->status = true;
                $retorno->message = 'Dados carregados com sucesso';
                $retorno->data = $query1;
            }

            return $retorno;
        }
        catch(\Throwable $e){
            return $retorno;
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

   
   
    public function salvarPontoDeskTop($dados_ponto){

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        try{
            $new_base64 = $this->normalizePointImage($dados_ponto['img_ponto']);
            $fields = array();
            if(isset($dados_ponto['ic_ponto_fora_turno'])){
                $fields['ic_ponto_fora_turno'] = $dados_ponto['ic_ponto_fora_turno'];
            }
            $fields['ic_tipo_app'] = $dados_ponto['ic_tipo_app'];
            $fields['ds_dispositivo'] = $dados_ponto['ds_dispositivo'];
            $fields['colaborador_pk'] = $dados_ponto['colaborador_pk'];
            $fields['ds_pin'] = $dados_ponto['id_cliente'];
            $fields['tipo_ponto_pk'] = $dados_ponto['tipo_ponto_pk'];
            $fields['dt_hora_ponto'] = 'sysdate()';
            $fields['agenda_colaborador_padrao_pk'] = $dados_ponto['agenda_colaborador_padrao_pk'];
            $fields['leads_pk'] = $dados_ponto['leads_pk'];
            $fields['ds_localizacao'] = $dados_ponto['ds_localizacao'];
            $fields['img_ponto'] = ($new_base64);
            $fields['ds_imagem'] = $dados_ponto['ds_imagem'];
            $fields['ic_sincronizacao'] = $dados_ponto['ic_sincronizacao'];

            $fields["dt_ult_atualizacao"] = "sysdate()";

            if(!isset($_SESSION['session_user']['par1'])){
                $fields["usuario_ult_atualizacao_pk"] = 1;
            }else{
                $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
            }

            //$fields['dt_solit_liberacao'] = "sysdate()";

            $fields["dt_cadastro"] = "sysdate()";
            if(!isset($_SESSION['session_user']['par1'])){
                $fields["usuario_cadastro_pk"]   = 1;
            }else{
                $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
            }

            $pk = Util::execInsert("ponto", $fields,$this->pdo);
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

    public function webPontoRetornaHistoricoPontoPeriodo($dados){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio
        try{
            $sql = "";
            $sql.="SELECT p.pk,";
            $sql.="        p.dt_hora_ponto,";
            $sql.="        p.tipo_ponto_pk,";
            $sql.="        CASE";
            $sql.="            WHEN p.tipo_ponto_pk = 1 THEN 'Ini Exp'";
            $sql.="            WHEN p.tipo_ponto_pk = 2 THEN 'Tér Exp'";
            $sql.="            WHEN p.tipo_ponto_pk = 3 THEN 'Iní Int'";
            $sql.="            WHEN p.tipo_ponto_pk = 4 THEN 'Tér Int'";
            $sql.="        END ds_tipo_ponto";
            $sql.="    FROM ponto p";
            $sql.="     INNER JOIN leads l ON p.leads_pk = l.pk";
            $sql.="    WHERE p.colaborador_pk = ".$dados['colaborador_pk'];
            if(!empty($dados['agenda_colaborador_padrao_pk'])){
                $sql.="     AND p.agenda_colaborador_padrao_pk = ".$dados['agenda_colaborador_padrao_pk'];
            }
            if(empty($dados['leads_pk'])){
                $sql.="     AND p.leads_pk = ".$dados['leads_pk'];
            }
            if(empty($dt_ponot_atual)){
                $sql.="     AND p.dt_hora_ponto >='".$dados['dt_ini']." 00:00:00'";
                $sql.="     AND p.dt_hora_ponto <='".$dados['dt_fim']." 23:59:59'";
            }



            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);


            $retorno->status = true;
            $retorno->message = 'Dados carregados com sucesso';
            $retorno->data = $query;
            return $retorno;
        }
        catch(\Throwable $e){
            return $retorno;
        }

    }
    
    public function webPontoretornaDadosPontoComprovante($dados){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        try{
            $sql = "";
            $sql.="  select";
            $sql.="      p.pk ponto_pk,";
            $sql.="      c.ds_colaborador,";
            $sql.="      c.ds_cpf, ";
            $sql.="      ps.ds_produto_servico,";
            $sql.="      l.ds_lead,";
            $sql.="      l.ds_endereco,";
            $sql.="      t.ds_turno,";
            $sql.="      acp.n_qtde_dias_semana,";
            $sql.="      acp.hr_inicio_expediente,";
            $sql.="      acp.hr_termino_expediente, ";
            $sql.="      acp.ic_folga_inverter ic_intrajornada, ";
            $sql.="      acp.hr_saida_intervalo hr_inicio_intervalo, ";
            $sql.="      acp.hr_retorno_intervalo hr_termino_intervalo, ";
            $sql.="      p.ds_imagem, ";
            $sql.="      p.dt_hora_ponto,";
            $sql.="      DATE_FORMAT(p.dt_hora_ponto, '%H:%i') hora_ponto,";
            $sql.="      p.tipo_ponto_pk,";
            $sql.="        CASE";
            $sql.="            WHEN p.tipo_ponto_pk = 1 THEN 'Ini Exp'";
            $sql.="            WHEN p.tipo_ponto_pk = 2 THEN 'Tér Exp'";
            $sql.="            WHEN p.tipo_ponto_pk = 3 THEN 'Iní Int'";
            $sql.="            WHEN p.tipo_ponto_pk = 4 THEN 'Tér Int'";
            $sql.="        END ds_tipo_ponto";
            $sql.="  from ponto p";
            $sql.="      inner join colaboradores c on p.colaborador_pk = c.pk";
            $sql.="      inner join agenda_colaborador_padrao acp on p.colaborador_pk  = acp.colaboradores_pk ";
            $sql.="      left join produtos_servicos ps on acp.produtos_servicos_pk = ps.pk ";
            $sql.="      left join leads l on acp.leads_pk = l.pk ";
            $sql.="      left join turnos t on acp.turnos_pk = t.pk ";
            $sql.="  where 1=1";
            if($dados['ponto_pk']!=""){
                $sql.=" and p.pk = ".$dados['ponto_pk'];
            }
            $sql.="  and acp.pk=".$dados['agenda_colaborador_padaro_pk'];


            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);


            $retorno->status = true;
            $retorno->message = 'Dados carregados com sucesso';
            $retorno->data = $query;
            return $retorno;
        }
        catch(\Throwable $e){
            return $retorno;
        }

    }


    public function pegarInformacaoPontoAfd(
        $dt_periodo_ini,
        $dt_periodo_fim,
        $conta_pk,
        $leads_pk,
        $colaborador_pk
    ){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        try{
            $sql = "";
            $sql.="  SELECT ";
            $sql.="        date_format(p.dt_hora_ponto,'%Y-%m-%d')dt_ponto,";
            $sql.="        date_format(p.dt_hora_ponto,'%H:%i:%s')hora_ponto,";
            $sql.="        c.ds_pis";
            $sql.="        FROM ponto p";
            $sql.="        inner join colaboradores c on p.colaborador_pk = c.pk ";
            $sql.="        inner join agenda_colaborador_padrao acp on p.colaborador_pk  = acp.colaboradores_pk";
            $sql.="        inner join leads l on acp.leads_pk = l.pk";
            $sql.="        where 1=1 ";
            $sql.="        and p.dt_hora_ponto between '".Util::DataYMD($dt_periodo_ini)." 00:00:00' and '".Util::DataYMD($dt_periodo_fim)." 23:59:59'";
            if($leads_pk!=""){
                $sql.="        and l.pk = ".$leads_pk;
            }
            $sql.="        and c.pk  = ".$colaborador_pk;
          

            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);


            $retorno->status = true;
            $retorno->message = 'Dados carregados com sucesso';
            $retorno->data = $query;
            return $retorno;
        }
        catch(\Throwable $e){
            return $retorno;
        }

    }


    public function getCoordinates($address) {
        $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($address) . "&format=json&limit=1";

        // Inicializa o cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Mozilla/5.0' // Cabeçalho User-Agent obrigatório
        ));

        // Executa a requisição e pega a resposta
        $response = curl_exec($ch);
        curl_close($ch);

        // Converte a resposta JSON para um array
        $data = json_decode($response, true);
       
        // Se a resposta contiver dados válidos, retorna a latitude e longitude
        if (!empty($data)) {
            return [
                'lat' => $data[0]['lat'],
                'lon' => $data[0]['lon']
            ];
        } else {
            return false;
        }
    }
    public function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
        $R = 6371; // Raio da Terra em km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distancia = $R * $c; // Distância em km
        return $distancia;
    } 


    public function pegarPontoNormal($dt_ini,$dt_fim,$colaborador_pk,$leads_pk){

        $dt_escala = Util::DataYMD($dt_ini);

        $sql ="";
        $sql.="SELECT l.pk,";
        $sql.="       pt.pk pontos_pk,";
        $sql.="       l.ds_lead,";
        $sql.="       l.pk leads_pk,";
        $sql.="       ll.ds_lead ds_lead_antigo,";
        $sql.="       col.pk colaboradores_pk,";
        $sql.="       concat(l.ds_endereco,', ',l.ds_numero,',',l.ds_cidade,',Brasil')ds_local_trabalho,";
        $sql.="       concat(ll.ds_endereco,', ',ll.ds_cidade,', Brasil')ds_local_trabalho_antigo,";
        $sql.="       col.ds_re,";
        $sql.="       col.ds_pin,";
        $sql.="       col.ds_colaborador,";
        $sql.="       ps.ds_produto_servico,";
        $sql.="       pt.tipo_ponto_pk,";
        $sql.="       date_format(pt.dt_hora_ponto,'%Y-%m-%d') dt_hora_ponto,";
        $sql.="       date_format(pt.dt_hora_ponto,'%d/%m/%Y') dt_rh_entratada,";
        $sql.="       date_format(pt.dt_hora_ponto,'%H:%i:%s') hr_entrada,";
        $sql.="       pt.ds_total_horas_trabalhadas,";
        $sql.="       pt.ds_localizacao,";
        $sql.="       pt.ds_latitude,";
        $sql.="       pt.ds_longitude,";
        $sql.="       pt.ds_imagem ds_imagem_entrada,";
        $sql.="       pt.img_ponto,";
        $sql.="       agp.hr_turno_dom,";
        $sql.="       agp.hr_turno_seg,";
        $sql.="       agp.hr_turno_ter,";
        $sql.="       agp.hr_turno_qua,";
        $sql.="       agp.hr_turno_qui,";
        $sql.="       agp.hr_turno_sex,";
        $sql.="       agp.hr_turno_sab,";
        $sql.="       agp.hr_turno_dom_saida,";
        $sql.="       agp.hr_turno_seg_saida,";
        $sql.="       agp.hr_turno_ter_saida,";
        $sql.="       agp.hr_turno_qua_saida,";
        $sql.="       agp.hr_turno_qui_saida,";
        $sql.="       agp.hr_turno_sex_saida,";
        $sql.="       agp.hr_turno_sab_saida,";
        $sql.="       agp.hr_intervalo_seg,";
        $sql.="       agp.hr_intervalo_ter,";
        $sql.="       agp.hr_intervalo_qua,";
        $sql.="       agp.hr_intervalo_qui,";
        $sql.="       agp.hr_intervalo_sex,";
        $sql.="       agp.hr_intervalo_sab,";
        $sql.="       agp.hr_intervalo_saida_seg,";
        $sql.="       agp.hr_intervalo_saida_ter,";
        $sql.="       agp.hr_intervalo_saida_qua,";
        $sql.="       agp.hr_intervalo_saida_qui,";
        $sql.="       agp.hr_intervalo_saida_sex,";
        $sql.="       agp.hr_intervalo_saida_sab,";
        $sql.="       agp.ic_dom,";
        $sql.="       agp.ic_seg,";
        $sql.="       agp.ic_ter,";
        $sql.="       agp.ic_qua,";
        $sql.="       agp.ic_qui,";
        $sql.="       agp.ic_sex,";
        $sql.="       agp.ic_sab,";
        $sql.="       pt.ic_validacao_facial,";
        $sql.="       agp.n_qtde_dias_semana,";
        $sql.="       pt.ds_imagem ds_imagem_saida,";
        $sql.="       pt.ds_distancia_ponto,";
        $sql.="       pt.ds_img ds_imagem_saida_antigo,";
        $sql.="       psl.ds_link_imagem_cadastro ds_imagem_sistema,";
        $sql.="       psl.img_colaborador_cadastro,";
        $sql.="       psl.ds_imagem ds_imagem_sistema_antiga";
        $sql.="  FROM ponto pt";
        $sql.="       INNER JOIN colaboradores col ON pt.colaborador_pk = col.pk";
        $sql.="       left join colaboradores_produtos_servicos cps on col.pk = cps.colaboradores_pk";
        $sql.="       left JOIN produtos_servicos ps ON cps.produtos_servicos_pk = ps.pk";
        $sql.="       INNER JOIN agenda_colaborador_padrao agp ON pt.colaborador_pk = agp.colaboradores_pk";
        $sql.="       LEFT JOIN leads l ON pt.leads_pk = l.pk";
        $sql.="       LEFT JOIN processos_etapas pe ON agp.processos_etapas_pk = pe.pk";
        $sql.="       LEFT JOIN processos pro ON pe.processos_pk = pro.pk";
        $sql.="       LEFT JOIN leads ll ON pro.leads_pk = ll.pk";
        $sql.="       left join ponto_solicitacao_liberacao_app psl on col.pk = psl.colaborador_pk";
        $sql .= " 
            WHERE (
                (pt.dt_hora_ponto BETWEEN '".$dt_escala." 00:00:00' AND '".$dt_escala." 23:59:59')
            )
        ";


        if($leads_pk != ""){
            $sql.=" and (l.pk = ".$leads_pk." OR ll.pk= ".$leads_pk.")";
        }

        if($colaborador_pk != ""){
            $sql.=" and col.pk  = ".$colaborador_pk;
        }

        $sql.=" group by pt.tipo_ponto_pk ";
    
  
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($query as $registro) {
            //if ($registro['tipo_ponto_pk'] == 1) {
                // Se encontrado, retorna todos os dados
                return $query;
            //}
        }

        return $query; 
    }
    public function pegarPontoNoturno($dt_ini,$dt_fim,$colaborador_pk,$leads_pk,$agenda_colaborador_padrao_pk = ""){

        $dt_escala = Util::DataYMD($dt_ini);
        $janela = $this->montarJanelaOperacionalNoturna($dt_escala, $colaborador_pk, $agenda_colaborador_padrao_pk);

        $sql ="";
        $sql.="SELECT l.pk,";
        $sql.="       l.ds_lead,";
        $sql.="       pt.pk pontos_pk,";
        $sql.="       l.pk leads_pk,";
        $sql.="       ll.ds_lead ds_lead_antigo,";
        $sql.="       col.pk colaboradores_pk,";
        $sql.="       concat(l.ds_endereco,', ',l.ds_numero,',',l.ds_cidade,',Brasil')ds_local_trabalho,";
        $sql.="       concat(ll.ds_endereco,', ',ll.ds_cidade,', Brasil')ds_local_trabalho_antigo,";
        $sql.="       col.ds_re,";
        $sql.="       col.ds_pin,";
        $sql.="       col.ds_colaborador,";
        $sql.="       ps.ds_produto_servico,";
        $sql.="       pt.tipo_ponto_pk,";
        $sql.="       date_format(pt.dt_hora_ponto,'%Y-%m-%d') dt_hora_ponto,";
        $sql.="       date_format(pt.dt_hora_ponto,'%d/%m/%Y') dt_rh_entratada,";
        $sql.="       date_format(pt.dt_hora_ponto,'%H:%i:%s') hr_entrada,";
        $sql.="       pt.ds_total_horas_trabalhadas,";
        $sql.="       pt.ds_localizacao,";
        $sql.="       pt.ds_latitude,";
        $sql.="       pt.ds_longitude,";
        $sql.="       pt.ds_imagem ds_imagem_entrada,";
        $sql.="       pt.img_ponto,";
        $sql.="       agp.hr_turno_dom,";
        $sql.="       agp.hr_turno_seg,";
        $sql.="       agp.hr_turno_ter,";
        $sql.="       agp.hr_turno_qua,";
        $sql.="       agp.hr_turno_qui,";
        $sql.="       agp.hr_turno_sex,";
        $sql.="       agp.hr_turno_sab,";
        $sql.="       agp.hr_turno_dom_saida,";
        $sql.="       agp.hr_turno_seg_saida,";
        $sql.="       agp.hr_turno_ter_saida,";
        $sql.="       agp.hr_turno_qua_saida,";
        $sql.="       agp.hr_turno_qui_saida,";
        $sql.="       agp.hr_turno_sex_saida,";
        $sql.="       agp.hr_turno_sab_saida,";
        $sql.="       agp.hr_intervalo_seg,";
        $sql.="       agp.hr_intervalo_ter,";
        $sql.="       agp.hr_intervalo_qua,";
        $sql.="       agp.hr_intervalo_qui,";
        $sql.="       agp.hr_intervalo_sex,";
        $sql.="       agp.hr_intervalo_sab,";
        $sql.="       agp.hr_intervalo_saida_seg,";
        $sql.="       agp.hr_intervalo_saida_ter,";
        $sql.="       agp.hr_intervalo_saida_qua,";
        $sql.="       agp.hr_intervalo_saida_qui,";
        $sql.="       agp.hr_intervalo_saida_sex,";
        $sql.="       agp.hr_intervalo_saida_sab,";
        $sql.="       agp.ic_dom,";
        $sql.="       agp.ic_seg,";
        $sql.="       agp.ic_ter,";
        $sql.="       agp.ic_qua,";
        $sql.="       agp.ic_qui,";
        $sql.="       agp.ic_sex,";
        $sql.="       agp.ic_sab,";
        $sql.="    agp.n_qtde_dias_semana,  ";
        $sql.="       pt.ds_imagem ds_imagem_saida,";
        $sql.="       pt.ds_distancia_ponto,";
        $sql.="       pt.ds_img ds_imagem_saida_antigo,";
        $sql.="       pt.ic_validacao_facial,";
        $sql.="       psl.ds_link_imagem_cadastro ds_imagem_sistema,";
        $sql.="       psl.img_colaborador_cadastro,";
        $sql.="       psl.ds_imagem ds_imagem_sistema_antiga";
        $sql.="  FROM ponto pt";
        $sql.="       INNER JOIN colaboradores col ON pt.colaborador_pk = col.pk";
        $sql.="       left join colaboradores_produtos_servicos cps on col.pk = cps.colaboradores_pk";
        $sql.="       left JOIN produtos_servicos ps ON cps.produtos_servicos_pk = ps.pk";
        $sql.="       INNER JOIN agenda_colaborador_padrao agp ON pt.colaborador_pk = agp.colaboradores_pk";
        $sql.="       LEFT JOIN leads l ON pt.leads_pk = l.pk";

        $sql.="       LEFT JOIN processos_etapas pe ON agp.processos_etapas_pk = pe.pk";
        $sql.="       LEFT JOIN processos pro ON pe.processos_pk = pro.pk";
        $sql.="       LEFT JOIN leads ll ON pro.leads_pk = ll.pk";
        $sql.="       left join ponto_solicitacao_liberacao_app psl on col.pk = psl.colaborador_pk";
        $sql.=" where 1=1 ";

        $sql.=' AND (
            (pt.dt_hora_ponto BETWEEN "'.$janela['inicio'].'" AND "'.$janela['fim'].'")
        )';
        if($leads_pk != ""){
            $sql.=" and (l.pk = ".$leads_pk." OR ll.pk= ".$leads_pk.")";
        }

        if($colaborador_pk != ""){
            $sql.=" and pt.colaborador_pk  = ".$colaborador_pk;
        }
        if($agenda_colaborador_padrao_pk != ""){
            $sql.=" and agp.pk = ".$agenda_colaborador_padrao_pk;
        }
        $sql.=" group by pt.tipo_ponto_pk ";
        $sql.=" order by pt.dt_hora_ponto asc ";
        
       
        

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $query; 
    }


    //APP
    public function salvarPontoApp($dados_ponto){

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        try{
            $new_base64 = $this->normalizePointImage($dados_ponto['img_ponto']);
            $ds_localizacao = $dados_ponto['ds_localizacao'];
            $fields = array();
            if(isset($dados_ponto['ic_ponto_fora_turno'])){
                $fields['ic_ponto_fora_turno'] = $dados_ponto['ic_ponto_fora_turno'];
            }
            $fields['ic_tipo_app'] = $dados_ponto['ic_tipo_app'];
            $fields['ds_dispositivo'] = $dados_ponto['ds_dispositivo'];
            $fields['colaborador_pk'] = $dados_ponto['colaborador_pk'];
            $fields['tipo_ponto_pk'] = $dados_ponto['tipo_ponto_pk'];
            $fields['agenda_colaborador_padrao_pk'] = $dados_ponto['agenda_colaborador_padrao_pk'];
            $fields['dt_hora_ponto'] = $dados_ponto['dt_hora_ponto'];
            $fields['leads_pk'] = $dados_ponto['leads_pk'];
            $fields['ds_localizacao'] = $ds_localizacao;
            $fields['img_ponto'] = $new_base64;
            $fields['ds_imagem'] = $dados_ponto['ds_imagem'];
            $fields['ic_sincronizacao'] = $dados_ponto['ic_sincronizacao'];
            $fields['ds_latitude'] = $dados_ponto['ds_latitude'];
            $fields['ds_longitude'] = $dados_ponto['ds_longitude'];
            
            $fields["dt_ult_atualizacao"] = "sysdate()";

            if(!isset($_SESSION['session_user']['par1'])){
                $fields["usuario_ult_atualizacao_pk"] = 1;
            }else{
                $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
            }

            $fields["dt_cadastro"] = "sysdate()";
            if(!isset($_SESSION['session_user']['par1'])){
                $fields["usuario_cadastro_pk"]   = 1;
            }else{
                $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
            }

            $pk = Util::execInsert("ponto", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;

            return $retorno;
        }
        catch(\Throwable $e){

            $retorno->data = "";
            $retorno->message = $e->getMessage();
            return $retorno;
        }

    }
    public function sincronizarPontoApp($dados_ponto){

        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        try{
            $new_base64 = $this->normalizePointImage($dados_ponto['img_ponto']);
            $ds_localizacao = $dados_ponto['ds_localizacao'];
            $fields = array();
            if(isset($dados_ponto['ic_ponto_fora_turno'])){
                $fields['ic_ponto_fora_turno'] = $dados_ponto['ic_ponto_fora_turno'];
            }
            $fields['ic_tipo_app'] = $dados_ponto['ic_tipo_app'];
            $fields['ds_dispositivo'] = $dados_ponto['ds_dispositivo'];
            $fields['colaborador_pk'] = $dados_ponto['colaborador_pk'];
            $fields['tipo_ponto_pk'] = $dados_ponto['tipo_ponto_pk'];
            $fields['agenda_colaborador_padrao_pk'] = $dados_ponto['agenda_colaborador_padrao_pk'];
            $fields['dt_hora_ponto'] = $dados_ponto['dt_hora_ponto'];
            $fields['leads_pk'] = $dados_ponto['leads_pk'];
            $fields['ds_localizacao'] = $ds_localizacao;
            $fields['img_ponto'] = $new_base64;
            $fields['ds_imagem'] = $dados_ponto['ds_imagem'];
            $fields['ic_sincronizacao'] = $dados_ponto['ic_sincronizacao'];
            $fields['ds_latitude'] = $dados_ponto['ds_latitude'];
            $fields['ds_longitude'] = $dados_ponto['ds_longitude'];

            $fields["dt_ult_atualizacao"] = "sysdate()";

            if(!isset($_SESSION['session_user']['par1'])){
                $fields["usuario_ult_atualizacao_pk"] = 1;
            }else{
                $fields["usuario_ult_atualizacao_pk"] = $_SESSION['session_user']['par1'];
            }

            $fields["dt_cadastro"] = "sysdate()";
            if(!isset($_SESSION['session_user']['par1'])){
                $fields["usuario_cadastro_pk"]   = 1;
            }else{
                $fields["usuario_cadastro_pk"]   = $_SESSION['session_user']['par1'];
            }

            $pk = Util::execInsert("ponto", $fields,$this->pdo);
            $retorno->status = true;
            $retorno->message = 'Dados cadastrados com sucesso';
            $retorno->data = $pk;

            return $retorno;
        }
        catch(\Throwable $e){

            $retorno->data = "";
            $retorno->message = $e->getMessage();
            return $retorno;
        }

    }

    public function getTurnoExpedienteApp($ds_cpf,$colaborador_pk){

        $sql="";
        $sql.="SELECT a.turnos_pk,
                      a.pk,
                      a.hr_inicio_expediente,
                      a.hr_termino_expediente";
        $sql.="  FROM agenda_colaborador_padrao a";
        $sql.="  inner join colaboradores c on a.colaboradores_pk = c.pk  ";
        $sql.=" WHERE a.dt_cancelamento is null";
        if($colaborador_pk!=""){
            $sql.=" and c.pk = ".$colaborador_pk;
        }
        if($ds_cpf!=""){
            $sql.=" and c.ds_cpf = '".$ds_cpf."'";
        }
       
   
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);   
        return $query;
    }
   public function acompanhamentoPontoDiario($colaborador_pk, $leads_pk, $turnos_pk_ignorado, $dt_pesquisa) {
        try {
            $resultado = [];

            // 1. Validar data
            $data = DateTime::createFromFormat('d/m/Y', $dt_pesquisa);
            if (!$data) {
                throw new Exception('Data de pesquisa inválida.');
            }

            $dt_escala = $data->format('Y-m-d');
            $dt_escala_modified = $data->modify('+1 day')->format('Y-m-d');

            // 2. Buscar leads
            $sqlLeads = "
                SELECT 
                    l.pk AS lead_pk,
                    l.ds_lead
                FROM leads l
                WHERE EXISTS (
                    SELECT 1 FROM agenda_colaborador_padrao a
                    WHERE a.leads_pk = l.pk
                    AND a.dt_cancelamento IS NULL
                )
            ";

            $params = [];

            if (!empty($leads_pk)) {
                $sqlLeads .= " AND l.pk = :leads_pk";
                $params['leads_pk'] = $leads_pk;
            }

            $sqlLeads .= " ORDER BY l.ds_lead ASC";

            $stmt = $this->pdo->prepare($sqlLeads);
            $stmt->execute($params);
            $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Para cada lead, buscar colaboradores e pontos
            foreach ($leads as $lead) {
                $leadPk = $lead['lead_pk'];

                // 3.1 Buscar colaboradores e turnos vinculados ao lead
                $sqlColabs = "
                    SELECT 
                        c.pk AS colaborador_pk,
                        c.ds_colaborador,
                        a.turnos_pk,
                        a.hr_inicio_expediente,
                        a.hr_termino_expediente,
                        a.hr_saida_intervalo,
                        a.hr_retorno_intervalo
                    FROM colaboradores c
                    INNER JOIN agenda_colaborador_padrao a 
                        ON a.colaboradores_pk = c.pk
                    WHERE a.leads_pk = :lead_pk
                    AND a.dt_cancelamento IS NULL
                ";

                $paramsColabs = ['lead_pk' => $leadPk];

                if (!empty($colaborador_pk)) {
                    $sqlColabs .= " AND c.pk = :colaborador_pk";
                    $paramsColabs['colaborador_pk'] = $colaborador_pk;
                }

                $stmt = $this->pdo->prepare($sqlColabs);
                $stmt->execute($paramsColabs);
                $colaboradores = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // 3.2 Para cada colaborador, buscar pontos
                foreach ($colaboradores as &$colab) {
                    $colabPk = $colab['colaborador_pk'];
                    $turnoPk = $colab['turnos_pk'];

                    $sqlPonto = "
                        SELECT 
                            tipo_ponto_pk,
                            ic_validacao_facial,
                            dt_validacao_facial,
                            usuario_validacao_facial,
                            DATE_FORMAT(MAX(CASE WHEN tipo_ponto_pk = 1 THEN dt_hora_ponto END), '%H:%i') AS hora_ponto_1,
                            DATE_FORMAT(MAX(CASE WHEN tipo_ponto_pk = 2 THEN dt_hora_ponto END), '%H:%i') AS hora_ponto_2
                        FROM ponto
                        WHERE colaborador_pk = :colaborador_pk
                        AND leads_pk = :leads_pk
                    ";

                    $paramsPonto = [
                        'colaborador_pk' => $colabPk,
                        'leads_pk' => $leadPk
                    ];

                    // Regras por turno
                    if ($turnoPk == 3) {
                        $sqlPonto .= " AND (
                            (dt_hora_ponto BETWEEN :escala_hoje_inicio AND :escala_modificado_02 AND tipo_ponto_pk = 1)
                            OR
                            (dt_hora_ponto BETWEEN :escala_modificado_inicio AND :escala_modificado_1030 AND tipo_ponto_pk != 1)
                        )";
                        $paramsPonto['escala_hoje_inicio'] = $dt_escala . ' 14:30:00';
                        $paramsPonto['escala_modificado_02'] = $dt_escala_modified . ' 02:00:00';
                        $paramsPonto['escala_modificado_inicio'] = $dt_escala_modified . ' 00:00:00';
                        $paramsPonto['escala_modificado_1030'] = $dt_escala_modified . ' 10:30:00';
                    } else {
                        $sqlPonto .= " AND DATE(dt_hora_ponto) = (
                            SELECT DATE(MIN(p2.dt_hora_ponto))
                            FROM ponto p2
                            WHERE p2.colaborador_pk = :colaborador_pk
                            AND p2.leads_pk = :leads_pk
                            AND p2.dt_hora_ponto BETWEEN :dt_escala_inicio AND :dt_escala_fim
                        )";
                        $paramsPonto['dt_escala_inicio'] = $dt_escala . ' 00:00:00';
                        $paramsPonto['dt_escala_fim'] = $dt_escala . ' 23:59:59';
                    }

                    $stmtPonto = $this->pdo->prepare($sqlPonto);
                    $stmtPonto->execute($paramsPonto);
                    $ponto = $stmtPonto->fetch(PDO::FETCH_ASSOC);

                    $colab['ponto'] = $ponto ?: null;
                }

                $resultado[] = [
                    'lead' => $lead,
                    'colaboradores' => $colaboradores
                ];
            }

            return $resultado;
        } catch (Throwable $e) {
            print_r($e->getMessage());
            die();
        }

    }   
    public function listarPostoTrabalhoApp($ds_cpf, $colaborador_pk) {
        $retorno = new \StdClass;
        $retorno->status = false;
        $retorno->data = [];

        $dt_atual = date('Y-m-d');

        try {
            $arrTurnosExpediente = $this->getTurnoExpedienteApp($ds_cpf, $colaborador_pk);

            for ($i = 0; $i < count($arrTurnosExpediente); $i++) {

                $sql = "
                    SELECT 
                        a.pk AS agenda_colaborador_padrao_pk,
                        l.pk AS leads_pk,
                        l.ds_lead AS ds_posto_trabalho,
                        t.ds_turno,
                        a.hr_inicio_expediente,
                        a.hr_termino_expediente,
                        COALESCE(edc.dt_escala, '{$dt_atual}') AS dt_escala,

                        -- INÍCIO DE EXPEDIENTE
                        CASE
                            WHEN (
                                SELECT COUNT(*) FROM ponto p1
                                WHERE p1.colaborador_pk = c.pk
                                AND p1.agenda_colaborador_padrao_pk = a.pk
                                AND p1.leads_pk = l.pk
                                AND p1.tipo_ponto_pk = 1
                                AND DATE(p1.dt_hora_ponto) = '{$dt_atual}'
                            ) > 0 THEN (
                                SELECT MIN(p1.dt_hora_ponto)
                                FROM ponto p1
                                WHERE p1.colaborador_pk = c.pk
                                AND p1.agenda_colaborador_padrao_pk = a.pk
                                AND p1.leads_pk = l.pk
                                AND p1.tipo_ponto_pk = 1
                                AND DATE(p1.dt_hora_ponto) = '{$dt_atual}'
                            )
                            WHEN EXISTS (
                                SELECT 1 FROM ponto pcheck
                                WHERE pcheck.colaborador_pk = c.pk
                                AND DATE(pcheck.dt_hora_ponto) = '{$dt_atual}'
                                AND pcheck.dt_hora_ponto BETWEEN DATE_SUB(NOW(), INTERVAL 8 HOUR) AND NOW()
                            ) THEN NULL
                            ELSE NULL
                        END AS inicio_expediente,

                        -- INÍCIO DE INTERVALO
                        CASE
                            WHEN (
                                SELECT COUNT(*) FROM ponto p3
                                WHERE p3.colaborador_pk = c.pk
                                AND p3.agenda_colaborador_padrao_pk = a.pk
                                AND p3.leads_pk = l.pk
                                AND p3.tipo_ponto_pk = 3
                                AND DATE(p3.dt_hora_ponto) = '{$dt_atual}'
                            ) > 0 THEN (
                                SELECT MIN(p3.dt_hora_ponto)
                                FROM ponto p3
                                WHERE p3.colaborador_pk = c.pk
                                AND p3.agenda_colaborador_padrao_pk = a.pk
                                AND p3.leads_pk = l.pk
                                AND p3.tipo_ponto_pk = 3
                                AND DATE(p3.dt_hora_ponto) = '{$dt_atual}'
                            )
                            WHEN EXISTS (
                                SELECT 1 FROM ponto pcheck
                                WHERE pcheck.colaborador_pk = c.pk
                                AND DATE(pcheck.dt_hora_ponto) = '{$dt_atual}'
                                AND pcheck.dt_hora_ponto BETWEEN DATE_SUB(NOW(), INTERVAL 8 HOUR) AND NOW()
                            ) THEN NULL
                            ELSE NULL
                        END AS inicio_intervalo,

                        -- TÉRMINO DE INTERVALO
                        CASE
                            WHEN (
                                SELECT COUNT(*) FROM ponto p4
                                WHERE p4.colaborador_pk = c.pk
                                AND p4.agenda_colaborador_padrao_pk = a.pk
                                AND p4.leads_pk = l.pk
                                AND p4.tipo_ponto_pk = 4
                                AND DATE(p4.dt_hora_ponto) = '{$dt_atual}'
                            ) > 0 THEN (
                                SELECT MIN(p4.dt_hora_ponto)
                                FROM ponto p4
                                WHERE p4.colaborador_pk = c.pk
                                AND p4.agenda_colaborador_padrao_pk = a.pk
                                AND p4.leads_pk = l.pk
                                AND p4.tipo_ponto_pk = 4
                                AND DATE(p4.dt_hora_ponto) = '{$dt_atual}'
                            )
                            WHEN EXISTS (
                                SELECT 1 FROM ponto pcheck
                                WHERE pcheck.colaborador_pk = c.pk
                                AND DATE(pcheck.dt_hora_ponto) = '{$dt_atual}'
                                AND pcheck.dt_hora_ponto BETWEEN DATE_SUB(NOW(), INTERVAL 8 HOUR) AND NOW()
                            ) THEN NULL
                            ELSE NULL
                        END AS termino_intervalo,

                        -- TÉRMINO DE EXPEDIENTE
                        CASE
                            WHEN (
                                SELECT COUNT(*) FROM ponto p2
                                WHERE p2.colaborador_pk = c.pk
                                AND p2.agenda_colaborador_padrao_pk = a.pk
                                AND p2.leads_pk = l.pk
                                AND p2.tipo_ponto_pk = 2
                                AND DATE(p2.dt_hora_ponto) = '{$dt_atual}'
                            ) > 0 THEN (
                                SELECT MAX(p2.dt_hora_ponto)
                                FROM ponto p2
                                WHERE p2.colaborador_pk = c.pk
                                AND p2.agenda_colaborador_padrao_pk = a.pk
                                AND p2.leads_pk = l.pk
                                AND p2.tipo_ponto_pk = 2
                                AND DATE(p2.dt_hora_ponto) = '{$dt_atual}'
                            )
                            WHEN EXISTS (
                                SELECT 1 FROM ponto pcheck
                                WHERE pcheck.colaborador_pk = c.pk
                                AND DATE(pcheck.dt_hora_ponto) = '{$dt_atual}'
                                AND pcheck.dt_hora_ponto BETWEEN DATE_SUB(NOW(), INTERVAL 8 HOUR) AND NOW()
                            ) THEN NULL
                            ELSE NULL
                        END AS termino_expediente,

                        a.ic_ponto_fora_horario,
                        a.ic_tempo_antes_ponto
                    FROM agenda_colaborador_padrao a
                    INNER JOIN colaboradores c ON a.colaboradores_pk = c.pk
                    INNER JOIN leads l ON a.leads_pk = l.pk
                    LEFT JOIN turnos t ON a.turnos_pk = t.pk
                    LEFT JOIN escala_dados_colaborador edc 
                        ON edc.agenda_colaborador_padrao = a.pk 
                        AND edc.ic_escala = 1 
                        AND edc.dt_escala = '{$dt_atual}'
                    WHERE a.dt_cancelamento IS NULL
                    AND a.pk = {$arrTurnosExpediente[$i]['pk']}
                ";

                if ($colaborador_pk != "") {
                    $sql .= " AND c.pk = {$colaborador_pk}";
                }
                if ($ds_cpf != "") {
                    $sql .= " AND c.ds_cpf = '{$ds_cpf}'";
                }

                $sql .= " GROUP BY l.pk ORDER BY l.ds_lead";

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                if (count($query) > 0) {
                    $retorno->data = array_merge($retorno->data, $query);
                }
            }

            $retorno->status = true;
            $retorno->message = 'Dados carregados com sucesso';
            return $retorno;

        } catch (\Throwable $e) {
            $retorno->status = false;
            $retorno->data = [];
            $retorno->message = $e->getMessage();
            return $retorno;
        }
    }
    /*public function listarPostoTrabalhoApp($ds_cpf,$colaborador_pk){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        $dt_atual = date('Y-m-d');        

        try{

            //PEGAR TURNO COM COLABORADOR_PK/CPF
            $arrTurnosExpediente = $this->getTurnoExpedienteApp($ds_cpf,$colaborador_pk);
     
            for($i=0;$i<count($arrTurnosExpediente);$i++){
                $hr_inicio_expediente = $arrTurnosExpediente[$i]['hr_inicio_expediente'];
                $hr_termino_expediente = $arrTurnosExpediente[$i]['hr_termino_expediente'];
                $sql = "";
                $sql.="select 
                        a.pk agenda_colaborador_padrao_pk,
                        l.pk leads_pk,
                        l.ds_lead ds_posto_trabalho,
                        t.ds_turno,
                        a.hr_inicio_expediente,
                        a.hr_termino_expediente,
                        edc.dt_escala,
                        p.dt_hora_ponto inicio_expediente,
                        p1.dt_hora_ponto inicio_intervalo,
                        p2.dt_hora_ponto termino_intervalo,
                        p3.dt_hora_ponto termino_expediente,
                        a.ic_ponto_fora_horario,
                        a.ic_tempo_antes_ponto
                    from agenda_colaborador_padrao a 
                        inner join colaboradores c on a.colaboradores_pk = c.pk 
                        inner join leads l on a.leads_pk = l.pk 
                        left join turnos t on a.turnos_pk = t.pk ";
                $sql.=" left join escala_dados_colaborador edc on edc.agenda_colaborador_padrao = a.pk and edc.dt_escala ='".$dt_atual."' and edc.ic_escala =1 "; 
                if($arrTurnosExpediente[$i]['turnos_pk'] == 3){
                    $dt_escala_obj = new DateTime($dt_atual);
                    $dt_escala_obj->modify('-1 day'); // Subtrai 1 dia
                    
                    // Formata a data no formato desejado
                    $dt_escala_modified = $dt_escala_obj->format('Y-m-d');
                    $sql.=" left join ponto p on c.pk = p.colaborador_pk and a.pk = p.agenda_colaborador_padrao_pk and p.leads_pk = l.pk and (p.dt_hora_ponto BETWEEN '".$dt_escala_modified." 00:00:00' and  '".$dt_atual." 23:59:59' AND p.tipo_ponto_pk = 1)";
                    $sql.=" left join ponto p1 on c.pk = p1.colaborador_pk and a.pk = p1.agenda_colaborador_padrao_pk and p1.leads_pk = l.pk and p1.dt_hora_ponto BETWEEN '".$dt_escala_modified." 00:00:00' and '".$dt_atual." 23:59:59' and p1.tipo_ponto_pk = 3";
                    $sql.=" left join ponto p2 on c.pk = p2.colaborador_pk and a.pk = p2.agenda_colaborador_padrao_pk and p2.leads_pk = l.pk and p2.dt_hora_ponto BETWEEN '".$dt_escala_modified." 00:00:00' and '".$dt_atual." 23:59:59' and p2.tipo_ponto_pk = 4";
                    $sql.=" left join ponto p3 on c.pk = p3.colaborador_pk and a.pk = p3.agenda_colaborador_padrao_pk and p3.leads_pk = l.pk and p3.dt_hora_ponto BETWEEN '".$dt_escala_modified." 00:00:00' and '".$dt_atual." 23:59:59a' and p3.tipo_ponto_pk = 2";

                }else{
                    $sql.=" left join ponto p on c.pk = p.colaborador_pk and a.pk = p.agenda_colaborador_padrao_pk and p.leads_pk = l.pk and p.dt_hora_ponto >='".$dt_atual." 00:00:00' and p.dt_hora_ponto <='".$dt_atual." 23:59:59' and p.tipo_ponto_pk = 1";
                    $sql.=" left join ponto p1 on c.pk = p1.colaborador_pk and a.pk = p1.agenda_colaborador_padrao_pk and p1.leads_pk = l.pk and p1.dt_hora_ponto >='".$dt_atual." 00:00:00' and p1.dt_hora_ponto <='".$dt_atual." 23:59:59' and p1.tipo_ponto_pk = 3";
                    $sql.=" left join ponto p2 on c.pk = p2.colaborador_pk and a.pk = p2.agenda_colaborador_padrao_pk and p2.leads_pk = l.pk and p2.dt_hora_ponto >='".$dt_atual." 00:00:00' and p2.dt_hora_ponto <='".$dt_atual." 23:59:59' and p2.tipo_ponto_pk = 4";
                    $sql.=" left join ponto p3 on c.pk = p3.colaborador_pk and a.pk = p3.agenda_colaborador_padrao_pk and p3.leads_pk = l.pk and p3.dt_hora_ponto >='".$dt_atual." 00:00:00' and p3.dt_hora_ponto <='".$dt_atual." 23:59:59' and p3.tipo_ponto_pk = 2";
                }
                
                $sql.=" where a.dt_cancelamento is null";
                $sql.=" and a.pk = ".$arrTurnosExpediente[$i]['pk'];
                if($colaborador_pk!=""){
                    $sql.=" and c.pk = ".$colaborador_pk;
                }
                if($ds_cpf!=""){
                    $sql.=" and c.ds_cpf = '".$ds_cpf."'";
                }
                $sql.=" group by l.pk";
                $sql.=" order by l.ds_lead";
                
               
                $stmt = $this->pdo->prepare( $sql );
                $stmt->execute();
                $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                if(count($query)>0){
                   
                    $retorno->data = array_merge($retorno->data, $query);
                   
                }
            }

            $retorno->status = true;
            $retorno->message = 'Dados carregados com sucesso';
            return $retorno;
        }
        catch(\Throwable $e){
            
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio
            $retorno->message = $e->getMessage(); //Retorno data setado como vazio
            return $retorno;
        }

    }*/
    public function pesquisarPontoApp($ds_cpf, $dt_ini,$dt_fim){
        $retorno = new \StdClass; //Estrutura de retorno para controller
        $retorno->status = false; //Retorno setado status como false
        $retorno->data = []; //Retorno data setado como vazio

        
        if(!empty($dt_fim)){
            $data_fim = $dt_fim;
        }
        else{
            $data_fim = $dt_ini;
        }

        try{
            $sql = "";
            $sql.="select 
                    l.pk leads_pk,
                    l.ds_lead ds_posto_trabalho,
                    t.ds_turno,
                    p.pk ponto_pk,
                    p.dt_hora_ponto,
                    case p.tipo_ponto_pk when 1 then 'Inicio Expediente' 
                    when 2 then 'Termino Expediente'
                    when 3 then 'Inicio Intervalo'
                    when 4 then 'Termino Intervalo' end ds_tipo_ponto,
                    p.ds_localizacao,
                    p.ds_imagem
                from ponto p 
                    inner join colaboradores c on p.colaborador_pk = c.pk 
                    inner join leads l on p.leads_pk = l.pk ";
            $sql.=" inner join agenda_colaborador_padrao a on a.pk = p.agenda_colaborador_padrao_pk and p.leads_pk = l.pk and p.dt_hora_ponto >='".($dt_ini)." 00:00:00' and p.dt_hora_ponto <='".($data_fim)." 23:59:59'";
            $sql.=" left join turnos t on a.turnos_pk = t.pk";
            $sql.=" left join escala_dados_colaborador edc on edc.agenda_colaborador_padrao = a.pk and edc.dt_escala ='".($dt_ini)."' and edc.dt_escala <='".($data_fim)."' and edc.ic_escala =1 "; 
            $sql.=" where 1=1";
            if($ds_cpf!=""){
                $sql.=" and c.ds_cpf = '".$ds_cpf."'";
            }
            $sql.=" and a.dt_cancelamento is null";
            $sql.=" group by l.pk";
            $sql.=" order by l.ds_lead";
          
            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if(count($query)>0){
                $retorno->status = true;
                $retorno->message = 'Dados carregados com sucesso';
                $retorno->data = $query;
                return $retorno;
            }
            else{
                $retorno->status = true;
                $retorno->message = 'Não existe ponto nesse periodo';
                $retorno->data = [];
                return $retorno;
            }
           
        }
        catch(\Throwable $e){
            $retorno->status = false; //Retorno setado status como false
            $retorno->data = []; //Retorno data setado como vazio
            $retorno->message = $e->getMessage(); //Retorno data setado como vazio
            return $retorno;
        }

    }

    public function diminuirImgPonto($offset = 0, $limit = 10000) {
        ini_set('max_execution_time', 0); // tempo ilimitado
        ini_set('memory_limit', '30000M'); // ajuste conforme memória disponível

        try {
            

             $stmt = $this->pdo->prepare("
            SELECT pk, img_ponto 
            FROM ponto 
            ORDER BY dt_cadastro ASC 
        ");
        $stmt->execute();
        $rows = $stmt->fetchAll();

        if (empty($rows)) {
            echo "Todos os registros processados.\n";
            return;
        }

        $updates = [];
        foreach ($rows as $row) {
            $pk = $row['pk'];
            $base64_png = preg_replace('#^data:image/\w+;base64,#i', '', $row['img_ponto']);
            $img_bin = base64_decode($base64_png);

            $img = imagecreatefromstring($img_bin);
            if ($img === false) continue;

            $tmp_file = tempnam(sys_get_temp_dir(), 'img');
            imagejpeg($img, $tmp_file, 70);
            imagedestroy($img);

            $new_img_bin = file_get_contents($tmp_file);
            unlink($tmp_file);

            $new_base64 = 'data:image/jpeg;base64,' . base64_encode($new_img_bin);
            $updates[$pk] = $new_base64;
        }

        if (!empty($updates)) {
            $cases = [];
            $pks = [];
            foreach ($updates as $pk => $img) {
                $cases[] = "WHEN {$pk} THEN :img_{$pk}";
                $pks[] = $pk;
            }

            $sql = "UPDATE ponto SET img_ponto = CASE pk " . implode(' ', $cases) . " END WHERE pk IN (" . implode(',', $pks) . ")";
            $stmtUpdate = $this->pdo->prepare($sql);
            foreach ($updates as $pk => $img) {
                $stmtUpdate->bindValue(":img_{$pk}", $img, PDO::PARAM_STR);
            }
            $stmtUpdate->execute();
        }

        echo "Processados registros de {$offset} a " . ($offset + $limit - 1) . "\n";

        // Chama a função novamente para o próximo lote
        //$this->diminuirImgPonto($offset + $limit, $limit);

        } catch (Throwable $e) {
            echo "Erro: " . $e->getMessage();
        }
        
    }


    function debugSQL($sql, $params) {
        foreach ($params as $key => $value) {
            // trata valores nulos, numéricos e strings
            $v = is_null($value) ? 'NULL' : (is_numeric($value) ? $value : "'{$value}'");
            // substitui :param pelo valor
            $sql = preg_replace('/:' . preg_quote($key, '/') . '\b/', $v, $sql);
        }
        return $sql;
        }

public function pegarDadosFechamento($leads_pk, $colaborador_pk, $dt_inicio, $dt_fim, $ic_historico = 0)
{
    try {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        $resultado = [];

        $horaParaMinutos = function ($hora) {
            if (empty($hora)) return 0;
            $parts = explode(':', $hora);
            $h = isset($parts[0]) ? intval($parts[0]) : 0;
            $m = isset($parts[1]) ? intval($parts[1]) : 0;
            return ($h * 60) + $m;
        };

        $dataInicio = DateTime::createFromFormat('d/m/Y', $dt_inicio);
        $dataFim = DateTime::createFromFormat('d/m/Y', $dt_fim);
        if (!$dataInicio || !$dataFim) {
            throw new Exception("Datas inválidas.");
        }

        $sqlLeads = "
            SELECT l.pk AS lead_pk, l.ds_lead
            FROM leads l
            WHERE EXISTS (
                SELECT 1 FROM agenda_colaborador_padrao a
                WHERE a.leads_pk = l.pk AND a.dt_cancelamento IS NULL
            )
        ";
        $params = [];
        if (!empty($leads_pk)) {
            $sqlLeads .= " AND l.pk = :leads_pk";
            $params['leads_pk'] = $leads_pk;
        }
        $sqlLeads .= " ORDER BY l.ds_lead ASC";

        $stmt = $this->pdo->prepare($sqlLeads);
        $stmt->execute($params);
        $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($leads as $lead) {
            $leadPk = $lead['lead_pk'];

            $sqlColabs = "
                SELECT 
                    c.pk AS colaborador_pk, 
                    c.ds_colaborador, 
                    c.pk ds_re,
                    DATE_FORMAT(c.dt_admissao,'%d/%m/%Y') dt_admissao, 
                    c.vl_salario,
                    a.pk AS agenda_pk, 
                    a.turnos_pk, 
                    t.ds_turno,
                    a.hr_inicio_expediente, 
                    a.hr_termino_expediente,
                    a.hr_saida_intervalo, 
                    a.hr_retorno_intervalo
                FROM colaboradores c
                INNER JOIN agenda_colaborador_padrao a ON a.colaboradores_pk = c.pk
                INNER JOIN turnos t ON a.turnos_pk = t.pk
                WHERE a.leads_pk = :lead_pk AND a.dt_cancelamento IS NULL
            ";

            $paramsColabs = ['lead_pk' => $leadPk];
            if (!empty($colaborador_pk)) {
                $sqlColabs .= " AND c.pk = :colaborador_pk";
                $paramsColabs['colaborador_pk'] = $colaborador_pk;
            }

            $stmt = $this->pdo->prepare($sqlColabs);
            $stmt->execute($paramsColabs);
            $colaboradores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($colaboradores as &$colab) {
                $colabPk = $colab['colaborador_pk'];
                $agendaPk = $colab['agenda_pk'];
                $turnoPk = $colab['turnos_pk'];

                $dias_escala = $dias_trabalhados = $faltas_total = $dias_falta =
                $atestado_total = $dias_atestado = $atrasos = 0;
                $minutos_excedentes_total = 0;
                $vt_valor = 0;
                $tolerancia = 5;

                $sqlEscala = "
                    SELECT dt_escala, ic_escala
                    FROM escala_dados_colaborador
                    WHERE agenda_colaborador_padrao = :agenda_pk
                    AND dt_escala BETWEEN :dt_inicio AND :dt_fim
                    GROUP BY dt_escala
                    ORDER BY dt_escala
                ";

                $stmtEscala = $this->pdo->prepare($sqlEscala);
                $stmtEscala->execute([
                    'agenda_pk' => $agendaPk,
                    'dt_inicio' => $dataInicio->format('Y-m-d'),
                    'dt_fim' => $dataFim->format('Y-m-d')
                ]);
                $escalas = $stmtEscala->fetchAll(PDO::FETCH_ASSOC);

                foreach ($escalas as $escala) {
                    if ($escala['ic_escala'] != 1) continue;
                    $dias_escala++;

                    $sqlPonto = "
                        SELECT 
                            tipo_ponto_pk,
                            DATE_FORMAT(MAX(CASE WHEN tipo_ponto_pk = 1 THEN dt_hora_ponto END), '%H:%i:%s') AS hora_ponto_1,
                            DATE_FORMAT(MAX(CASE WHEN tipo_ponto_pk = 2 THEN dt_hora_ponto END), '%H:%i:%s') AS hora_ponto_2
                        FROM ponto
                        WHERE colaborador_pk = :colaborador_pk
                        AND leads_pk = :leads_pk
                    ";

                    $paramsPonto = [
                        'colaborador_pk' => $colabPk,
                        'leads_pk' => $leadPk,
                    ];

                    if ($turnoPk == 3) {
                        $dt_escala = $escala['dt_escala'];
                        $dt_mod = (new DateTime($dt_escala))->modify('+1 day')->format('Y-m-d');

                        $sqlPonto .= "
                            AND (
                                (dt_hora_ponto BETWEEN :escala_hoje_inicio AND :escala_modificado_02 AND tipo_ponto_pk = 1)
                                OR
                                (dt_hora_ponto BETWEEN :escala_modificado_inicio AND :escala_modificado_1030 AND tipo_ponto_pk != 1)
                            )
                        ";

                        $paramsPonto['escala_hoje_inicio'] = $dt_escala . ' 14:30:00';
                        $paramsPonto['escala_modificado_02'] = $dt_mod . ' 02:00:00';
                        $paramsPonto['escala_modificado_inicio'] = $dt_mod . ' 00:00:00';
                        $paramsPonto['escala_modificado_1030'] = $dt_mod . ' 10:30:00';
                    } else {
                        $sqlPonto .= " AND DATE(dt_hora_ponto) = DATE(:dt_escala) ";
                        $paramsPonto['dt_escala'] = $escala['dt_escala'];
                    }

                    $sqlPonto .= " GROUP BY tipo_ponto_pk";
                    $stmtPonto = $this->pdo->prepare($sqlPonto);
                    $stmtPonto->execute($paramsPonto);
                    $pontos = $stmtPonto->fetchAll(PDO::FETCH_ASSOC);

                    $arrApont = (new PontoFolha($this->pdo))->listarDadosApontamento(
                        $escala['dt_escala'],
                        $colabPk,
                        $agendaPk,
                        $ic_historico
                    );

                    $apontamentosPonto = [];
                    if (!empty($arrApont) && isset($arrApont[0]['arrApontamento'])) {
                        foreach ($arrApont[0]['arrApontamento'] as $a) {
                            if (($a['tipo_apontamento_dados_pk'] ?? '') == 1) {
                                $apontamentosPonto[] = $a;
                            }
                        }
                    }

                    $arrAtestado = (new PontoFolha($this->pdo))->listarApontamentoFalta($escala['dt_escala'], $colabPk, $ic_historico);
                    $arrAfast = (new PontoFolha($this->pdo))->listarApontamentoAfastamento($escala['dt_escala'], $colabPk, $ic_historico);

                    $temAtestado = is_array($arrAtestado) && count($arrAtestado) > 0;
                    $temAfast = is_array($arrAfast) && count($arrAfast) > 0;

                    if (count($pontos) > 0 || count($apontamentosPonto) > 0) {
                        $dias_trabalhados++;
                    } elseif ($temAtestado || $temAfast) {
                        if ($temAtestado) {
                            $atestado_total += count($arrAtestado);
                            $dias_atestado += 1;
                        }
                        $dias_escala = max(0, $dias_escala - 1);
                    } else {
                        $faltas_total++;
                        $dias_falta++;
                    }

                    // --- CÁLCULO DE HORAS EXCEDENTES AJUSTADO ---
                    if (!($temAtestado || $temAfast)) {
                        $minutos_excedentes_dia = 0;

                        $hora_saida_prevista = $horaParaMinutos($colab['hr_termino_expediente'] ?? '00:00');
                        $hora_inicio_prevista = $horaParaMinutos($colab['hr_inicio_expediente'] ?? '00:00');

                        if ($turnoPk == 3 && $hora_saida_prevista < $hora_inicio_prevista) {
                            $hora_saida_prevista += 24 * 60;
                        }

                        foreach ($pontos as $ponto) {
                            $hora_str = $ponto['hora_ponto_1'] ?? $ponto['hora_ponto_2'] ?? null;
                            if (empty($hora_str)) continue;

                            $hora_ponto = $horaParaMinutos($hora_str);
                            $tipo = intval($ponto['tipo_ponto_pk'] ?? 0);

                            if ($tipo === 1) {
                                $atraso = max(0, $hora_ponto - $hora_inicio_prevista);
                                if ($atraso > $tolerancia) $atrasos++;
                            } elseif ($tipo === 2) {
                                $hora_ponto_relativa = $hora_ponto;
                                if ($turnoPk == 3 && $hora_ponto < $hora_inicio_prevista) {
                                    $hora_ponto_relativa += 24 * 60;
                                }
                                $min_exced = max(0, $hora_ponto_relativa - $hora_saida_prevista - $tolerancia);
                                $minutos_excedentes_dia += $min_exced;
                            }
                        }

                        $minutos_excedentes_total += $minutos_excedentes_dia;
                    }

                } // fim escalas

                $sqlVT = "SELECT vl_beneficio FROM colaboradores_beneficios WHERE colaborador_pk = :colab_pk AND beneficios_pk = 1";
                $stmtVT = $this->pdo->prepare($sqlVT);
                $stmtVT->execute(['colab_pk' => $colabPk]);
                $vt = $stmtVT->fetch(PDO::FETCH_ASSOC);
                if ($vt) $vt_valor = floatval($vt['vl_beneficio']);

                $colab['fechamento'] = [
                    'dias_escala' => $dias_escala,
                    'dias_trabalhados' => $dias_trabalhados,
                    'faltas_total' => $faltas_total,
                    'dias_falta' => $dias_falta,
                    'atestado_total' => $atestado_total,
                    'dias_atestado' => $dias_atestado,
                    'atrasos' => $atrasos,
                    'hr_excedentes' => sprintf('%02d:%02d', floor($minutos_excedentes_total / 60), $minutos_excedentes_total % 60),
                    'vt_valor' => $vt_valor
                ];
            }

            $resultado[] = [
                'lead' => $lead,
                'colaboradores' => $colaboradores
            ];
        }

        return $resultado;

    } catch (Throwable $e) {
        error_log("Erro em pegarDadosFechamento: " . $e->getMessage());
        error_log($e->getTraceAsString());
        return ['erro' => $e->getMessage()];
    }
}








}
