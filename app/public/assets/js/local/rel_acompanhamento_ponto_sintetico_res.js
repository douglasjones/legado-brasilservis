var tblResultado;
var arrMes = [];

function fcCarregarGrid() {
    var strRetorno = "";
    var objParametros = {
        "leads_pk": $("#leads_pk").val(),
        "colaborador_pk": $("#colaborador_pk").val(),
        "ic_cliente":  $("#ic_cliente").val(),
        "dt_ini": $("#dt_periodo_ini").val(),
        "dt_fim": $("#dt_periodo_fim").val()
    };

    var arrCarregarc = carregarController("ponto", "listarColaborador", objParametros);
    if (arrCarregarc.status == true) {
        if (arrCarregarc.data.length > 0) {
            strRetorno+=    '                                <div class="caixa_tabela">';
            strRetorno += "<table id='tabela'  class='table-responsive-sm tab_dados' style='width:100%' id='tblResultado'>";
            strRetorno+=    ' <thead class="tab_dados">';
            strRetorno += "       <tr>";
            //strRetorno += "            <th><input type='text' id='rxtPostoTrabalho' placeholder='Pesquisar por'/></th>";
            //strRetorno += "            <th><input type='text' id='txtColaborador' placeholder='Pesquisar por'/></th>";
            //strRetorno += "            <th><input type='text' id='txtRE' placeholder='Pesquisar por'/></th>";
            //strRetorno += "            <th><input type='text' id='txtDsPin' placeholder='Pesquisar por'/></th>";
            //strRetorno += "            <th><input type='text' id='txtFuncao' placeholder='Pesquisar por'/></th>";
            //strRetorno += "            <th><input type='text' id='txtEscala' placeholder='Pesquisar por'/></th>";
            strRetorno += "            <th><input type='text' id='txtDataEscala' placeholder='Pesquisar por'/></th>";
            strRetorno += "            <th><input type='text' id='txtHrEscala' placeholder='Pesquisar por'/></th>";
            strRetorno += "            <th><input type='text' id='txtDtEntradaPonto' placeholder='Pesquisar por'/></th>";
            strRetorno += "            <th><input type='text' id='txtlegenda' placeholder='Pesquisar por'/></th>";
            strRetorno += "            <th><input type='text' id='txtlegenda' placeholder='Pesquisar por'/></th>";
            strRetorno += "            <th><input type='text' id='txtDisabled' disabled/></th>";
            strRetorno += "            <th><input type='text' id='txtDisabled' disabled/></th>";
            strRetorno += "       </tr>";
            strRetorno += "    </thead>";
            strRetorno += "    <tbody>";
            strRetorno += "       <tr  align=center style='background-color:#f5f5f5;border-color:#b4b4b4;border-style: solid;'>";
            strRetorno += "           <th  align=center>Posto Trabalho</th>";
            strRetorno += "           <th >Colaborador</th>";
            //strRetorno += "           <th >R.E</th>";
            //strRetorno += "           <th >PIN</th>";

            strRetorno += "           <th >Função</th>";
            //strRetorno+="           <th>Horário</th>";
            //strRetorno += "           <th >Escala</th>";
            //strRetorno += "           <th >Hr Entrada / Saida Escala</th>";
            //strRetorno += "           <th >Hr Saida / Retorno Intervalo</th>";
            strRetorno += "           <th >Legenda Registro Ponto</th>";
            strRetorno += "           <th >Data Ponto</th>";

            strRetorno += "           <th >Hr Registro Ponto</th>";
            strRetorno += "           <th >Tempo Atraso</th>";
            strRetorno += "           <th >Distância Ponto</th>";
            //strRetorno += "           <th >Tempo Positivo</th>";
            //strRetorno+="           <th>Dt/HR Saída</th>";
            //strRetorno+="           <th>Img Ponto App Saida</th>";
            strRetorno += "       </tr>";

            for (var i = 0; i < arrCarregarc.data.length; i++) {
                var objParametrosP = {
                    //"ic_cliente":  $("#ic_cliente").val(),
                    "dt_ini": $("#dt_periodo_ini").val(),
                    "dt_fim": $("#dt_periodo_fim").val(),
                    "leads_pk": arrCarregarc.data[i]['leads_pk'],
                    "colaborador_pk": arrCarregarc.data[i]['colaborador_pk'],
                    "dt_ponto": arrCarregarc.data[i]['dt_ponto'],
                    "ic_inverter_folga": arrCarregarc.data[i]['ic_inverter_folga'],
                };
                var arrCarregarP = carregarController("ponto", "relatorioPontoSinteticaAntigo", objParametrosP);
                if (arrCarregarP.status == true) {
                    if (arrCarregarP.data.length > 0) {
                        var segundo_ponto = "";
                        var segundo_intervalo = "";
                        var segundo_positivo = "";
                        var soma_segundo_positivo = "";
                        var $ds_lead = "";
                        var $ds_re = "";
                        var ds_distancia_entre_pontos = "";
                        
                        var $ds_pin = "";
                        var $ds_colaborador = "";
                        var $ds_produto_servico = "";
                        var $n_qtde_dias_semana = "";
                        var $dt_rh_entratada = "";
                        var $dt_rh_saida = "";
                        var $ds_total_horas_trabalhadas = "";
                        var ds_legenda = "";
                        var ds_registro_ponto = "";

                        //strRetorno+="    </tbody>";
                        //strRetorno+="<tfoot>";
                        for (j = 0; j < arrCarregarP.data.length; j++) {
                            if (arrCarregarP.data[j]['ds_lead'] != null) {
                                $ds_lead = arrCarregarP.data[j]['ds_lead'];
                            } else {
                                $ds_lead = "";
                            }

                            if (arrCarregarP.data[j]['ds_re'] != null) {
                                $ds_re = arrCarregarP.data[j]['ds_re'];
                            } else {
                                $ds_re = "";
                            }

                            if (arrCarregarP.data[j]['ds_pin'] != null) {
                                $ds_pin = arrCarregarP.data[j]['ds_pin'];
                            } else {
                                $ds_pin = "";
                            }

                            if (arrCarregarP.data[j]['ds_colaborador'] != null) {
                                $ds_colaborador = arrCarregarP.data[j]['ds_colaborador'];
                            } else {
                                $ds_colaborador = "";
                            }

                            if (arrCarregarP.data[j]['ds_produto_servico'] != null) {
                                $ds_produto_servico = arrCarregarP.data[j]['ds_produto_servico'];
                            } else {
                                $ds_produto_servico = "";
                            }

                            if (arrCarregarP.data[j]['n_qtde_dias_semana'] != null) {
                                $n_qtde_dias_semana = arrCarregarP.data[j]['n_qtde_dias_semana'];
                            } else {
                                $n_qtde_dias_semana = "";
                            }

                            if (arrCarregarP.data[j]['dt_rh_entratada'] != null) {
                                $dt_rh_entratada = arrCarregarP.data[j]['dt_rh_entratada'];
                            } else {
                                $dt_rh_entratada = "";
                            }

                            if (arrCarregarP.data[j]['dt_rh_saida'] != null) {
                                $dt_rh_saida = arrCarregarP.data[j]['dt_rh_saida'];
                            } else {
                                $dt_rh_saida = "";
                            }

                            if (arrCarregarP.data[j]['ds_total_horas_trabalhadas'] != null) {
                                $ds_total_horas_trabalhadas = arrCarregarP.data[j]['ds_total_horas_trabalhadas'];
                            } else {
                                $ds_total_horas_trabalhadas = "";
                            }

                            /*if(arrCarregarP.data[j]['status']!= null){
                                $status = arrCarregarP.data[j]['status'];
                            }else{
                                $status = "";
                            }*/

                            var ds_background = "";
                            if (arrCarregarP.data[j]['ds_legenda'] == null) {
                                ds_legenda = "";
                            } else {
                                ds_legenda = arrCarregarP.data[j]['ds_legenda'];
                            }
                            if (arrCarregarP.data[j]['ds_registro_ponto'] == null) {
                                ds_registro_ponto = "";
                            } else {
                                ds_registro_ponto = arrCarregarP.data[j]['ds_registro_ponto'];
                            }


                            if (ds_legenda == "Inicio Expediente") {
                                //DIFERENCA DO HORARIO
                                if (arrCarregarP.data[j]['hr_diferenca'] == null) {
                                    var ds_hr_dif = 'Vazio';
                                } else {
                                    if (parseInt(arrCarregarP.data[j]['segundos']) < 0) {
                                        var ds_hr_dif = "Dentro do Horário";
                                        ds_background = '#34ac54';
                                    } else if (parseInt(arrCarregarP.data[j]['segundos']) == 0) {
                                        var ds_hr_dif = "";

                                    } else {
                                        var ds_hr_dif = arrCarregarP.data[j]['hr_diferenca'];
                                        segundo_ponto = parseInt(arrCarregarP.data[j]['segundos']);
                                    }
                                }
                                //COR DOS HORARIOS

                                var ds_font_color = "black";
                                if (parseInt(arrCarregarP.data[j]['segundos']) >= 60 && parseInt(arrCarregarP.data[j]['segundos']) < 600) {
                                    ds_background = '#c3c3c1';
                                }
                                if (parseInt(arrCarregarP.data[j]['segundos']) >= 600) {
                                    ds_background = '#e6df55';
                                }
                                if (parseInt(arrCarregarP.data[j]['segundos']) >= 900) {
                                    ds_background = '#f99856';
                                }
                                if (parseInt(arrCarregarP.data[j]['segundos']) >= 1500) {
                                    ds_background = '#ec1c24';
                                    ds_font_color = 'black';
                                }


                            }
                            //ATRASO INTERVALO
                            else if (ds_legenda == "Retorno do Intervalo") {
                                var ds_hr_dif = "";
                                //DIFERENCA DO HORARIO
                                if (arrCarregarP.data[j]['hr_diferenca_intervalo'] == null) {
                                    var ds_hr_dif = 'Vazio';
                                } else {
                                    if (parseInt(arrCarregarP.data[j]['segundos_intervalo']) < 0) {
                                        var ds_hr_dif = "Dentro do Horário";
                                        ds_background = '#34ac54';
                                    } else if (parseInt(arrCarregarP.data[j]['segundos_intervalo']) == 0) {
                                        var ds_hr_dif = "";
                                    } else {
                                        //verifica se tem cadastro na escala o horario de intervalo

                                        if (arrCarregarP.data[j]['hr_escala_intervalo'] != " / ") {
                                            if (parseInt(arrCarregarP.data[j]['diferenca_segundo_positivo']) > 0) {
                                                //if(parseInt(arrCarregarP.data[j]['segundos_intervalo'] > 3600)){
                                                segundo_intervalo = parseInt(arrCarregarP.data[j]['segundos_intervalo'] - parseInt(arrCarregarP.data[j]['segundos_positivo']));
                                                //}
                                                if (segundo_intervalo > 0 && segundo_intervalo > 60) {
                                                    var ds_hr_dif = toTimeString(segundo_intervalo);
                                                } else {
                                                    segundo_intervalo = 0;
                                                    var ds_hr_dif = "";
                                                }
                                            }
                                        }

                                        /*else{
                                            //if(parseInt(arrCarregarP.data[j]['segundos_intervalo'] > 3600)){
                                                segundo_intervalo = parseInt(arrCarregarP.data[j]['segundos_intervalo'] - 3600);
                                            //}
                                            if(segundo_intervalo > 0 && segundo_intervalo > 60){
                                                var ds_hr_dif = toTimeString(segundo_intervalo);
                                            }
                                            else{
                                                segundo_intervalo = 0;
                                                var ds_hr_dif = "";
                                            }
                                        }*/


                                    }
                                }
                                //ATRASO INTERVALO
                                //COR DOS HORARIOS
                                var ds_background = "";
                                //verifica se tem cadastro na escala o horario de intervalo
                                if (parseInt(arrCarregarP.data[j]['diferenca_segundo_positivo']) > 0) {
                                    if (parseInt(arrCarregarP.data[j]['segundos_intervalo']) > parseInt(arrCarregarP.data[j]['segundos_positivo'])) {
                                        if (parseInt(arrCarregarP.data[j]['segundos_intervalo']) > (parseInt(arrCarregarP.data[j]['segundos_positivo']) + 59) && parseInt(arrCarregarP.data[j]['segundos_intervalo']) < (parseInt(arrCarregarP.data[j]['diferenca_segundo_positivo']) + 600)) {
                                            ds_background = '#c3c3c1';
                                        }
                                        if (parseInt(arrCarregarP.data[j]['segundos_intervalo']) >= (parseInt(arrCarregarP.data[j]['segundos_positivo']) + 600)) {
                                            ds_background = '#e6df55';
                                        }
                                        if (parseInt(arrCarregarP.data[j]['segundos_intervalo']) >= (parseInt(arrCarregarP.data[j]['segundos_positivo']) + 900)) {
                                            ds_background = '#f99856';
                                        }
                                        if (parseInt(arrCarregarP.data[j]['segundos_intervalo']) >= (parseInt(arrCarregarP.data[j]['segundos_positivo']) + 1500)) {
                                            ds_background = '#ec1c24';
                                            ds_font_color = 'black';
                                        }
                                    }
                                }

                                //SE NÃO TIVER O HORARIO DE INTERVALO
                                /*else{
                                    if(parseInt(arrCarregarP.data[j]['segundos_intervalo'])>3600){
                                        if(parseInt(arrCarregarP.data[j]['segundos_intervalo'])> 3659 && parseInt(arrCarregarP.data[j]['segundos_intervalo'])< 4200){
                                            ds_background = '#c3c3c1';
                                        }
                                        if(parseInt(arrCarregarP.data[j]['segundos_intervalo'])>= 4200){
                                            ds_background = '#e6df55';
                                        }
                                        if(parseInt(arrCarregarP.data[j]['segundos_intervalo'])>= 4500){
                                            ds_background = '#f99856';
                                        }
                                        if (parseInt(arrCarregarP.data[j]['segundos_intervalo'])>= 5100){
                                            ds_background = '#ec1c24';
                                        }
                                    }
                                }*/

                                //TEMPO POSITIVO
                                if (parseInt(arrCarregarP.data[j]['diferenca_segundo_positivo']) > 0) {
                                    segundo_positivo = parseInt(arrCarregarP.data[j]['diferenca_segundo_positivo']);
                                    soma_segundo_positivo += segundo_positivo;
                                    ds_background = '#34ac54';


                                }
                            } else {
                                segundo_positivo = 0;
                            }

                            if(arrCarregarP.data[j]['ds_distancia_entre_pontos']!= null){
                                ds_distancia_entre_pontos = arrCarregarP.data[j]['ds_distancia_entre_pontos'];
                            }


                            var somar_segundos = segundo_ponto + segundo_intervalo;


                            if (ds_legenda == "Inicio Expediente") {
                                strRetorno += "<tr align=center style='color:" + ds_font_color + ";border-color:b4b4b4;border-style: solid;background-color:" + ds_background + "'>";
                            } else if (ds_legenda == "Retorno do Intervalo") {
                                strRetorno += "<tr align=center style='color:" + ds_font_color + ";border-color:b4b4b4;border-style: solid;background-color:" + ds_background + "'>";
                            } else if (segundo_positivo > 0) {
                                strRetorno += "<tr align=center style='color:" + ds_font_color + ";border-color:b4b4b4;border-style: solid;background-color:" + ds_background + "'>";
                            } else {
                                strRetorno += "<tr align=center style='color:" + ds_font_color + ";border-color:b4b4b4;border-style: solid;'>";
                            }

                            strRetorno += "<td  width='10%'>" + $ds_lead + "</td>";
                            strRetorno += "<td  width='10%'>" + $ds_colaborador + "</td>";
                            //strRetorno += "<td  width='10%'>" + $ds_re + "</td>";
                            //strRetorno += "<td  width='10%'>" + $ds_pin + "</td>";

                            strRetorno += "<td  width='10%'>" + $ds_produto_servico + "</td>";
                            //strRetorno += "<td  width='10%'>" + $n_qtde_dias_semana + "</td>";
                            //strRetorno += "<td  width='10%'>" + arrCarregarP.data[j]['hr_escala'] + "</td>";
                            //strRetorno += "<td  width='10%'>" + arrCarregarP.data[j]['hr_escala_intervalo'] + "</td>";
                            strRetorno += "<td  width='10%'>" + ds_legenda + "</td>";
                            strRetorno += "<td  width='10%'>" + $dt_rh_entratada + "</td>";

                            strRetorno += "<td  width='10%'>" + ds_registro_ponto + "</td>";
                            if (ds_legenda == "Inicio Expediente") {
                                strRetorno += "<td  width='10%' >" + ds_hr_dif + "</td>";
                            } else if (ds_legenda == "Retorno do Intervalo") {
                                strRetorno += "<td  width='10%' >" + ds_hr_dif + "</td>";
                            } else {
                                strRetorno += "<td  width='10%' ></td>";
                            }
                            strRetorno+="<td  width='10%'>"+ds_distancia_entre_pontos+"</td>";

                            /*if (segundo_positivo > 0) {
                                strRetorno += "<td  width='10%'>" + toTimeString(segundo_positivo) + "</td>";
                            } else {
                                strRetorno += "<td  width='10%'></td>";
                            }*/

                            //strRetorno+="<td width='10%'>"+$dt_rh_saida+"</td>";
                            //strRetorno+="<td width='10%'><img src="+ds_imagem_saida+" width='40'</td>";
                            strRetorno += "</tr>";

                        }
                    }
                }
            }


            strRetorno += "<tr align=center style='border-color:#b4b4b4;background-color:#f5f5f5;border-style: solid;'>";
            strRetorno += "           <th colspan=13  align=center>&nbsp;</th>";
            strRetorno += "       </tr>";
        }
    }

    strRetorno += "</tbody>";
    strRetorno += "</table>";
    strRetorno += "</div>";

    if (strRetorno != "") {
        $("#grid").append(strRetorno);
    } else {
        $("#grid").append("");
    }

    $(".loader").hide();
    $("#carregar").hide();
    $("#exibir").show();
}
function toTimeString(seconds) {
    return (new Date(seconds * 1000)).toUTCString().match(/(\d\d:\d\d:\d\d)/)[0];
}
/*function fcCarregarGrid(){
    var strRetorno = "";
    var objParametros = {
        "leads_pk": $("#leads_pk").val(),
        "colaborador_pk": $("#colaborador_pk").val(),
        "ic_cliente":  $("#ic_cliente").val(),
        "dt_periodo_ini":  $("#dt_periodo_ini").val(),
        "dt_periodo_fim": $("#dt_periodo_fim").val()
    };

    var arrCarregar = carregarController("ponto", "relAcompanhamentoPontoSintetico", objParametros);
    if (arrCarregar.status == true) {
        if (arrCarregar.data.length > 0) {
            for(var i =0;i<arrCarregar.data.length;i++){
                if(arrCarregar.data[i]['arrPonto'].length > 0){
                    strRetorno+='                                <div class="cabecalho" >';
                    strRetorno+=    '                                    <table class="tab_dados_cabecalho" style="width: 100%;text-align: left;font-size: 12px">';
                    strRetorno+=    '                                        <tr>';
                    strRetorno+=    '                                          <th style="font-size: 12px">&nbsp;&nbsp;<b><i class="bi bi-person-square" style="font-size=12px;"></i></b> - &nbsp;'+arrCarregar.data[i]['ds_colaborador']+'</th>';
                    strRetorno+=    '                                        </tr>';
                    strRetorno+=    '                                        <tr>';
                    strRetorno+=    '                                            <th style="font-size: 12px">&nbsp;&nbsp;<b><i class="bi bi-building-add" style="font-size=12px;"></i> - &nbsp;'+arrCarregar.data[i]['ds_lead']+'</th>';
                    strRetorno+=    '                                        </tr>';
                    strRetorno+=    '                                        <tr>';
                    strRetorno+=    '                                            <th style="font-size: 12px">&nbsp;&nbsp;<b><i class="bi bi-tools" style="font-size=12px;"></i></b> - &nbsp;'+arrCarregar.data[i]['ds_funcao']+'</th>';
                    strRetorno+=    '                                        </tr>';
                    strRetorno+=    '                                        <tr>';
                    strRetorno+=    '                                            <th style="font-size: 12px">&nbsp;&nbsp;<b><i class="bi bi-calendar" style="font-size=12px;"></i></b> - &nbsp;'+arrCarregar.data[i]['ds_escala']+'</th>';
                    strRetorno+=    '                                        </tr>';
                    strRetorno+=    '                                        <tr>';
                    strRetorno+=    '                                            <th style="font-size: 12px">&nbsp;&nbsp;<b><i class="bi bi-clock-history" style="font-size=12px;"></i></b> - &nbsp;'+arrCarregar.data[i]['horario']+'</th>';
                    strRetorno+=    '                                        </tr>';
                    strRetorno+=    '                                        <tr>';
                    strRetorno+=    '                                            <th style="font-size: 12px">&nbsp;&nbsp;<b><i class="bi bi-textarea-t" style="font-size=12px;"></i></b> - &nbsp;'+arrCarregar.data[i]['ds_turno']+'</th>';
                    strRetorno+=    '                                        </tr>';
                    strRetorno+=    '                                    </table>';
                    strRetorno+=    '                                </div>';

                    strRetorno+=    '                                <div class="caixa_tabela">';
                    strRetorno+=    '                                    <table class="tab_dados">';
                    strRetorno+=    '                                        <tr>';
                    for(var j = 0;j < arrCarregar.data[i]['arrPonto'].length;j++ ) {
                        strRetorno += '                 <th>'+arrCarregar.data[i]['arrPonto'][j]['dt_escala']+'</th>';
                    }
                    strRetorno+=    '                                        </tr>';
                    strRetorno+=    '                                        <tr>';

                    for(var j= 0;j < arrCarregar.data[i]['arrPonto'].length;j++ ) {
                        if(arrCarregar.data[i]['arrPonto'][j]['ds_status']=="Ponto Não Registrado"){
                            strRetorno += '                                            <td style="font-size: 10px"><b>'+arrCarregar.data[i]['arrPonto'][j]['ds_status']+'</b></td>';
                        }
                        else if(arrCarregar.data[i]['arrPonto'][j]['ic_status']==""){
                            strRetorno += '                                            <td style="background-color: orange;font-size: 10px;color: white;">'+arrCarregar.data[i]['arrPonto'][j]['ds_status']+'</td>';
                        }
                        else if(arrCarregar.data[i]['arrPonto'][j]['ic_status']==0){
                            strRetorno += '                                            <td style="background-color: #34ac54;font-size: 10px">'+arrCarregar.data[i]['arrPonto'][j]['ds_status']+'<br>'+arrCarregar.data[i]['arrPonto'][j]['horaPonto']+'</td>';
                        }
                        else if(arrCarregar.data[i]['arrPonto'][j]['ic_status']==10){
                            strRetorno += '                                            <td style="background-color: #e6df55;font-size: 10px">'+arrCarregar.data[i]['arrPonto'][j]['ds_status']+'<br>'+arrCarregar.data[i]['arrPonto'][j]['horaPonto']+'</td>';
                        }
                        else if(arrCarregar.data[i]['arrPonto'][j]['ic_status']==15){
                            strRetorno += '                                            <td style="background-color: #f99856;font-size: 10px">'+arrCarregar.data[i]['arrPonto'][j]['ds_status']+'<br>'+arrCarregar.data[i]['arrPonto'][j]['horaPonto']+'</td>';
                        }
                        else if(arrCarregar.data[i]['arrPonto'][j]['ic_status']==25){
                            strRetorno += '                                            <td style="background-color: #ec1c24;color: white;font-size: 10px">'+arrCarregar.data[i]['arrPonto'][j]['ds_status']+'<br>'+arrCarregar.data[i]['arrPonto'][j]['horaPonto']+'</td>';
                        }
                    }
                    strRetorno+=    '                                        </tr>';
                    strRetorno+=    '                                    </table>';
                    strRetorno+=    '                                </div>';
                    strRetorno+=    '                                <br>';
                    strRetorno+=    '                                <br>';
                }
            }
        }
    }

    strRetorno += "</tbody>";
    strRetorno += "</table>";



    if (strRetorno != "") {
        $("#grid").append(strRetorno);
    } else {
        $("#grid").append("");
    }
    $(".loader").hide();
    $("#carregar").hide();
    $("#exibir").show();
}*/


function fcExport() {

    var htmlPlanilha = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    htmlPlanilha += '<head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>PlanilhaTeste</x:Name>';
    htmlPlanilha += '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml>';
    htmlPlanilha += '<![endif]--></head><body><table>' + $("#form").html() + '</table></body></html>';

    var htmlBase64 = btoa(htmlPlanilha);
    var link = "data:application/vnd.ms-excel;base64," + htmlBase64;

    var hyperlink = document.createElement("a");
    hyperlink.download = "Acompanhamento-Ponto-Sintetico.xls";
    hyperlink.href = link;
    hyperlink.style.display = 'none';

    document.body.appendChild(hyperlink);
    hyperlink.click();
    document.body.removeChild(hyperlink);
}
function fcCancelar(){
    var objParametros = {};
    sendPost("relatorio", "pesqAcompanhamentoPontoSintetico", objParametros);
}


$(document).ready(function(){
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdExport', fcExport);

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var yyyy = today.getFullYear();
    var hh = today.getHours();
    var min = today.getMinutes();
    var seg = today.getSeconds();
    //data
    if(dd<10) {
        dd = '0'+dd
    }

    if(mm<10) {
        mm = '0'+mm
    }
    //hora
    if(hh<10) {
        hh = '0'+hh
    }

    if(min<10) {
        min = '0'+min
    }
    if(seg<10) {
        seg = '0'+seg
    }

    today = dd + '/' + mm + '/' + yyyy + ' '+hh+':'+min+':'+seg;


    $("#dt_emissao").text(today);

    fcCarregarGrid();


    $("#tabela input").keyup(function () {
        var index = $(this).parent().index();
        var nth = "#tabela td:nth-child(" + (index + 1).toString() + ")";
        var valor = $(this).val().toUpperCase();
        $("#tabela tbody tr").show();
        $(nth).each(function () {
            if ($(this).text().toUpperCase().indexOf(valor) < 0) {
                $(this).parent().hide();
            }
        });
    });
    $("#tabela input").blur(function () {
        $(this).val("");
    });
});


