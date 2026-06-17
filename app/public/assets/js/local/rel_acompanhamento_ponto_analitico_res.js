var tblResultado;
var arrMes = [];
let rowsExport = [];
function fcCarregarGrid() {
    $(".loader").hide();
    $("#carregar").hide();
    $("#exibir").show();
    var strRetorno = "";
    var objParametros = {
        "leads_pk": $("#leads_pk").val(),
        "colaborador_pk": $("#colaborador_pk").val(),
        "ic_cliente":  $("#ic_cliente").val(),
        "dt_ini": $("#dt_periodo_ini").val(),
        "dt_fim": $("#dt_periodo_fim").val()
    };

    var arrCarregarc = carregarController("ponto", "listarColaborador", objParametros);

    if (arrCarregarc.status == true){
        if(arrCarregarc.data.length > 0){
            strRetorno+=    ' <div class="caixa_tabela">';
            strRetorno+="<table id='tabela'  class='table-responsive-sm' style='width:100%' id='tblResultado'>";
            strRetorno+="    <thead class='tab_dados'>";
            strRetorno+="       <tr>";
            strRetorno+="            <th><input type='text' id='rxtPostoTrabalho' placeholder='Pesquisar por'/></th>";
            strRetorno+="            <th><input type='text' id='txtColaborador' placeholder='Pesquisar por'/></th>";
            strRetorno+="            <th><input type='text' id='txtRE' placeholder='Pesquisar por'/></th>";
            strRetorno+="            <th><input type='text' id='txtDsPin' placeholder='Pesquisar por'/></th>";

            strRetorno+="            <th><input type='text' id='txtFuncao' placeholder='Pesquisar por'/></th>";
            //strRetorno+="            <th><input type='text' id='txtHorario' /></th>";
            strRetorno+="            <th><input type='text' id='txtEscala' placeholder='Pesquisar por'/></th>";
            strRetorno+="            <th><input type='text' id='txtDataEscala' placeholder='Pesquisar por'/></th>";
            strRetorno+="            <th><input type='text' id='txtHrEscala' placeholder='Pesquisar por'/></th>";
            strRetorno+="            <th><input type='text' id='txtDtEntradaPonto' placeholder='Pesquisar por'/></th>";
            strRetorno+="            <th><input type='text' id='txtlegenda' placeholder='Pesquisar por'/></th>";
            strRetorno+="            <th><input type='text' id='txtlegenda' placeholder='Pesquisar por'/></th>";

            strRetorno+="            <th><input type='text' id='txtDisabled' disabled/></th>";
            strRetorno+="            <th><input type='text' id='txtDisabled' disabled/></th>";
            strRetorno+="            <th><input type='text' id='txtDisabled' disabled/></th>";
            strRetorno+="            <th><input type='text' id='txtDisabled' disabled/></th>";
            strRetorno+="            <th><input type='text' id='txtDisabled' disabled/></th>";
            strRetorno+="            <th><input type='text' id='txtDisabled' disabled/></th>";
            //strRetorno+="            <th><input type='text' id='txtDisabled' disabled/></th>";
            //strRetorno+="            <th><input type='text' id='txtDisabled' disabled/></th>";
            strRetorno+="       </tr>";
            strRetorno+="    </thead>";
            for(i = 0; i< arrCarregarc.data.length; i++){
                var objParametrosP = {
                    "dt_final": $("#dt_periodo_fim").val(),
                    "leads_pk": arrCarregarc.data[i]['leads_pk'],
                    "colaborador_pk": arrCarregarc.data[i]['colaborador_pk'],
                    "qtde_lead_colaborador": arrCarregarc.data[i]['qtde_lead_colaborador'],
                    "dt_ponto": arrCarregarc.data[i]['dt_ponto'],
                    "ic_inverter_folga": arrCarregarc.data[i]['ic_inverter_folga'],
                    "dt_ini": $("#dt_periodo_ini").val()
                };

                var arrCarregarP = carregarController("ponto", "relatorioPonto", objParametrosP);
                //NewWindow(v_last_url)
                //var arrCarregarP = carregarController("ponto", "relatorioPontoSintetica", objParametrosP);

                if (arrCarregarP.status == true){

                    if(arrCarregarP.data!=null){
                        if(arrCarregarP.data.length > 0){
                            var segundo_ponto ="";
                            var segundo_intervalo = "";
                            var segundo_positivo = "";
                            var soma_segundo_positivo = 0;
                            var somar_segundos = "";
                            strRetorno+="    <tbody>";
                            strRetorno+="       <tr  align=center style='background-color:f5f5f5;border-color:b4b4b4;border-style: solid;'>";
                            strRetorno+="           <th  align=center>Posto Trabalho</th>";
                            strRetorno+="           <th >Colaborador</th>";
                            strRetorno+="           <th >R.E</th>";
                            strRetorno+="           <th >PIN</th>";

                            strRetorno+="           <th >Função</th>";
                            //strRetorno+="           <th>Horário</th>";
                            strRetorno+="           <th >Escala</th>";
                            strRetorno+="           <th >Hr Entrada / Saida Escala</th>";
                            strRetorno+="           <th >Hr Saida / Retorno Intervalo</th>";
                            strRetorno+="           <th >Legenda Registro Ponto</th>";
                            strRetorno+="           <th >Data Ponto</th>";

                            strRetorno+="           <th >Hr Registro Ponto</th>";
                            strRetorno+="           <th >Tempo Atraso</th>";
                            strRetorno+="           <th >Tempo Positivo</th>";
                            //strRetorno+="           <th>Dt/HR Saída</th>";
                            //strRetorno+="           <th>Img Ponto App Saida</th>";
                            strRetorno+="           <th >Total de Horas Trabalhadas</th>";



                            strRetorno+="           <th >Img Sistema</th>";
                            strRetorno+="           <th >Img Ponto App Entrada</th>";

                            strRetorno+="           <th >Local Ponto App</th>";
                            strRetorno+="           <th >Local Posto de Trabalho</th>";
                            strRetorno+="           <th >Distância Entre Pontos</th>";
                            //strRetorno+="           <th >Abrir Mapa</th>";
                            strRetorno+="       </tr>";
                            //strRetorno+="    </tbody>";
                            //strRetorno+="<tfoot>";

                            var $ds_total_horas_trabalhadas = "";
                            for(j=0; j < arrCarregarP.data.length ;j++){

                                
                                var $ds_lead = "";
                                var ds_local_trabalho = "";
                                var $ds_re = "";
                                var $ds_pin = "";
                                var $ds_colaborador = "";
                                var $ds_produto_servico = "";
                                var $n_qtde_dias_semana = "";
                                var $dt_rh_entratada = "";
                                var $dt_rh_saida = "";
                                var ds_localizacao = "";
                                var ds_distancia_entre_pontos = "";
                                var ds_imagem_entrada = "";
                                var ds_imagem_sistema = "";
                                var ds_legenda = "";
                                var ds_registro_ponto = "";
                                if(arrCarregarP.data[j]['ds_lead']!= null){
                                    $ds_lead = arrCarregarP.data[j]['ds_lead'];
                                }
                                if(arrCarregarP.data[j]['ds_local_trabalho']!= null){
                                    ds_local_trabalho = arrCarregarP.data[j]['ds_local_trabalho'];
                                }

                                if(arrCarregarP.data[j]['ds_re']!= null){
                                    $ds_re = arrCarregarP.data[j]['ds_re'];
                                }

                                if(arrCarregarP.data[j]['ds_pin']!= null){
                                    $ds_pin = arrCarregarP.data[j]['ds_pin'];
                                }

                                if(arrCarregarP.data[j]['ds_colaborador']!= null){
                                    $ds_colaborador = arrCarregarP.data[j]['ds_colaborador'];
                                }

                                if(arrCarregarP.data[j]['ds_produto_servico']!= null){
                                    $ds_produto_servico = arrCarregarP.data[j]['ds_produto_servico'];
                                }

                                if(arrCarregarP.data[j]['n_qtde_dias_semana']!= null){
                                    $n_qtde_dias_semana = arrCarregarP.data[j]['n_qtde_dias_semana'];
                                }

                                if(arrCarregarP.data[j]['dt_rh_entratada']!= null){
                                    $dt_rh_entratada = arrCarregarP.data[j]['dt_rh_entratada'];
                                }

                                if(arrCarregarP.data[j]['dt_rh_saida']!= null){
                                    $dt_rh_saida = arrCarregarP.data[j]['dt_rh_saida'];
                                }
                                if(arrCarregarP.data[j]['ds_total_horas_trabalhadas']!= null){
                                    $ds_total_horas_trabalhadas = arrCarregarP.data[j]['ds_total_horas_trabalhadas'];
                                }



                                if(arrCarregarP.data[j]['ds_localizacao']!= null){
                                    ds_localizacao = arrCarregarP.data[j]['ds_localizacao'];
                                }
                                if(arrCarregarP.data[j]['ds_distancia_entre_pontos']!= null){
                                    ds_distancia_entre_pontos = arrCarregarP.data[j]['ds_distancia_entre_pontos'];
                                }


                                if(arrCarregarP.data[j]['ds_imagem_entrada']==""){
                                    ds_imagem_entrada='';
                                }else{
                                    ds_imagem_entrada = arrCarregarP.data[j]['ds_imagem_entrada'];
                                }
                                if(arrCarregarP.data[j]['ds_imagem_sistema']==""){
                                    ds_imagem_sistema='';
                                }else{
                                    ds_imagem_sistema = arrCarregarP.data[j]['ds_imagem_sistema'];
                                }

                                if(arrCarregarP.data[j]['ds_legenda']==null){
                                    ds_legenda="";
                                }else{
                                    ds_legenda=arrCarregarP.data[j]['ds_legenda'];
                                }
                                if(arrCarregarP.data[j]['ds_registro_ponto']==null){
                                    ds_registro_ponto="";
                                }else{
                                    ds_registro_ponto=arrCarregarP.data[j]['ds_registro_ponto'];
                                }



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

                                   //TEMPO POSITIVO
                                    if (parseInt(arrCarregarP.data[j]['diferenca_segundo_positivo']) > 0) {
                                        segundo_positivo = parseInt(arrCarregarP.data[j]['diferenca_segundo_positivo']);
                                        soma_segundo_positivo += segundo_positivo;
                                        ds_background = '#34ac54';


                                    }
                                } else {
                                    segundo_positivo = 0;
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

                                strRetorno+="<td  width='10%'>"+$ds_lead+"</td>";
                                strRetorno+="<td  width='10%'>"+$ds_colaborador+"</td>";
                                strRetorno+="<td  width='10%'>"+$ds_re+"</td>";
                                strRetorno+="<td  width='10%'>"+$ds_pin+"</td>";

                                strRetorno+="<td  width='10%'>"+$ds_produto_servico+"</td>";
                                //strRetorno+="<td width='10%'>"+arrCarregarP.data[j]['periodo']+"</td>";
                                strRetorno+="<td  width='10%'>"+$n_qtde_dias_semana+"</td>";
                                strRetorno+="<td  width='10%'>"+arrCarregarP.data[j]['hr_escala']+"</td>";
                                strRetorno+="<td  width='10%'>"+arrCarregarP.data[j]['hr_escala_intervalo']+"</td>";
                                strRetorno+="<td  width='10%'>"+ds_legenda+"</td>";
                                strRetorno+="<td  width='10%'>"+$dt_rh_entratada+"</td>";

                                strRetorno+="<td  width='10%'>"+ds_registro_ponto+"</td>";
                                if(ds_legenda=="Inicio Expediente"){
                                    strRetorno+="<td  width='10%' >"+ds_hr_dif+"</td>";
                                }
                                else if(ds_legenda=="Retorno do Intervalo"){
                                    strRetorno+="<td  width='10%' >"+ds_hr_dif+"</td>";
                                }
                                else{
                                    strRetorno+="<td  width='10%' ></td>";
                                }

                                if(segundo_positivo>0){
                                    strRetorno+="<td  width='10%'>"+toTimeString(segundo_positivo)+"</td>";
                                }
                                else{
                                    strRetorno+="<td  width='10%'></td>";
                                }

                                //strRetorno+="<td width='10%'>"+$dt_rh_saida+"</td>";
                                //strRetorno+="<td width='10%'><img src="+ds_imagem_saida+" width='40'</td>";
                                strRetorno+="<td  width='10%'>"+$ds_total_horas_trabalhadas+"</td>";


                               if(ds_imagem_sistema==""){
                                   strRetorno+='<td align=center  width="40%" class="galeria">'+arrCarregarP.data[j]['img_colaborador_cadastro']+'</td>';
                                   //strRetorno+='<td align=center  width="40%" class="galeria"><img src="/assets/img/profile/avatar.jpg" width="60" height="60"></td>';
                                }
                               else{
                                   strRetorno+="<td align=center  width='40%' class='galeria'><img src="+ds_imagem_sistema+" width='40'</td>";
                               }
                                //strRetorno+='<td align=center  width="40%" class="galeria"><img src="/assets/img/profile/avatar.jpg" width="100" height="100"></td>';
                                strRetorno+='<td align=center  width="40%" class="galeria">'+arrCarregarP.data[j]['img_ponto']+'</td>';

                               
                                //strRetorno+="<td width='10%'>"+$status+"</td>";
                                strRetorno+="<td  width='10%'>"+ds_localizacao+"</td>";
                                strRetorno+="<td  width='10%'>"+ds_local_trabalho+"</td>";
                            
                                
                              
                                strRetorno+="<td  width='10%'>"+ds_distancia_entre_pontos+"</td>";
                               
                                //strRetorno+="<td align=center  width='60%'></td>";
                                
                                strRetorno+="</tr>";


                                let rowExport = [
                                    $ds_lead,
                                    $ds_colaborador,
                                    $ds_re,
                                    $ds_pin,
                                    $ds_produto_servico,
                                    $n_qtde_dias_semana,
                                    arrCarregarP.data[j]['hr_escala'] || '',
                                    arrCarregarP.data[j]['hr_escala_intervalo'] || '',
                                    ds_legenda,
                                    $dt_rh_entratada,
                                    ds_registro_ponto,
                                    ds_hr_dif || '',
                                    toTimeString(segundo_positivo) || '',
                                    $ds_total_horas_trabalhadas,
                                    extractBase64(ds_imagem_sistema),      // pode ser <img> ou base64 ou URL
                                    extractBase64(arrCarregarP.data[j]['img_ponto']),
                                    ds_localizacao,
                                    ds_local_trabalho,
                                    ds_distancia_entre_pontos
                                    ];

                                    rowsExport.push(rowExport);




                            }
                            //QUEBRA DE LINHA PARA INFORMAR A QUANTIDADE DE HORAS DE ATRASO



                            strRetorno+="<tr align=center style='border-color:#b4b4b4;background-color:#f5f5f5;border-style: solid;'>";
                            strRetorno+="           <th colspan=11  align=center>&nbsp;</th>";

                            strRetorno+="           <th colspan=1  align=center style='font-size:14px'>Total de Horas Atraso <br>"+toTimeString(somar_segundos);+"</th>";
                            strRetorno+="           <th colspan=1  align=center style='font-size:14px'>Total de Horas Positivo <br>"+toTimeString(soma_segundo_positivo);+"</th>";

                            strRetorno+="           <th colspan=1  align=center style='font-size:14px'>Total de Horas Trabalhadas<br> "+$ds_total_horas_trabalhadas;+"</th>";
                            strRetorno+="           <th colspan=6  align=center>&nbsp;</th>";
                            strRetorno+="       </tr>";
                            strRetorno+="<tr>";
                            strRetorno+="           <th colspan=18  align=center>&nbsp;</th>";
                            strRetorno+="       </tr>";
                            somar_segundos = "";
                            segundo_ponto = "";
                            segundo_intervalo = "";








                        }
                    }
                }
            }

        }
    }
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
function fcFecharModal(){
    $("#janela_modal_mapa").modal("hide");
}
function fcAbrirMapa(origem,destino){
    $("#janela_modal_mapa").modal("show");
    $("#html_maps").html('<iframe width="750" scrolling="no" height="350" frameborder="0" id="map" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?saddr='+origem+'&daddr='+destino+'&output=embed"></iframe>');
}

function extractBase64(imgTag) {
  if (!imgTag) return '';
  if (typeof imgTag !== 'string') {
    console.warn('extractBase64 recebeu tipo diferente de string:', typeof imgTag, imgTag);
    return '';
  }

  // Se for tag <img>
  if (imgTag.startsWith('<img')) {
    const match = imgTag.match(/src=["']([^"']+)["']/);
    if (match && match[1]) return match[1];
    return '';
  }

  // Já é base64 ou URL
  return imgTag;
}


async function urlToBase64(url) {
  try {
    const response = await fetch(url);
    const blob = await response.blob();
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onloadend = () => resolve(reader.result);
      reader.onerror = reject;
      reader.readAsDataURL(blob);
    });
  } catch (e) {
    console.warn('Erro ao converter URL para base64:', url, e);
    return '';
  }
}

async function prepareImages(rows) {
  for (let i = 0; i < rows.length; i++) {
    // Coluna 14: imagem sistema
    if (typeof rows[i][14] === 'string' && rows[i][14].startsWith('http')) {
      rows[i][14] = await urlToBase64(rows[i][14]);
    }

    // Coluna 15: imagem ponto
    if (typeof rows[i][15] === 'string' && rows[i][15].startsWith('http')) {
      rows[i][15] = await urlToBase64(rows[i][15]);
    }
  }
  return rows;
}


async function  fcExport() {

   /* var htmlPlanilha = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
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
    document.body.removeChild(hyperlink);*/
    // Prepara imagens base64
  // Prepara imagens base64
  rowsExport = await prepareImages(rowsExport);
console.log('✅ rowsExport após prepareImages:', rowsExport);

// Inicia PDF
const { jsPDF } = window.jspdf;
const doc = new jsPDF('l', 'pt', 'a2');

// Define colunas
const columns = [
  { header: 'Posto Trabalho', dataKey: 'posto' },
  { header: 'Colaborador', dataKey: 'colaborador' },
  { header: 'R.E', dataKey: 're' },
  { header: 'PIN', dataKey: 'pin' },
  { header: 'Função', dataKey: 'funcao' },
  { header: 'Escala', dataKey: 'escala' },
  { header: 'Hr Entrada / Saida Escala', dataKey: 'hr_entrada_saida' },
  { header: 'Hr Saida / Retorno Intervalo', dataKey: 'hr_saida_retorno' },
  { header: 'Legenda', dataKey: 'legenda' },
  { header: 'Registro Ponto', dataKey: 'registro_ponto' },
  { header: 'Data Ponto', dataKey: 'data_ponto' },
  { header: 'Tempo Atraso', dataKey: 'tempo_atraso' },
  { header: 'Tempo Positivo', dataKey: 'tempo_positivo' },
  { header: 'Total de Horas Trabalhadas', dataKey: 'total_horas' },
  { header: 'Img Sistema', dataKey: 'img_sistema' },
  { header: 'Img Ponto App Entrada', dataKey: 'img_ponto' },
  { header: 'Local Ponto App', dataKey: 'local_ponto' },
  { header: 'Local Posto de Trabalho', dataKey: 'local_posto' },
  { header: 'Distância Entre Pontos', dataKey: 'distancia' },
];

// Transforma cada linha em objeto nomeado (array original)
const dataRows = rowsExport.map((row, i) => {
  const obj = {
    posto: row[0],
    colaborador: row[1],
    re: row[2],
    pin: row[3],
    funcao: row[4],
    escala: row[5],
    hr_entrada_saida: row[6],
    hr_saida_retorno: row[7],
    legenda: row[8],
    registro_ponto: row[9],
    data_ponto: row[10],
    tempo_atraso: row[11],
    tempo_positivo: row[12],
    total_horas: row[13],
    img_sistema: row[14],
    img_ponto: row[15],
    local_ponto: row[16],
    local_posto: row[17],
    distancia: row[18],
  };

  // Logs para verificar base64
  console.log(`📸 Linha ${i} - img_sistema início:`, (obj.img_sistema || '').substring(0, 50));
  console.log(`📸 Linha ${i} - img_ponto início:`, (obj.img_ponto || '').substring(0, 50));

  return obj;
});

// Remove o texto base64 das colunas de imagem para não imprimir o texto na tabela
const dataWithoutBase64Text = dataRows.map(row => ({
  ...row,
  img_sistema: '',  // Limpa o texto base64 para a célula
  img_ponto: '',    // Limpa o texto base64 para a célula
}));

// Geração da tabela com imagens
doc.autoTable({
  head: [columns.map(c => c.header)],
  body: dataWithoutBase64Text, // usa a versão limpa das imagens para o texto da tabela
  columns: columns,
  startY: 20,
  styles: { fontSize: 8, cellPadding: 2 },
  tableWidth: 'auto',
  didDrawCell: function (data) {
    // Desenha imagens somente nas linhas do corpo da tabela
    if (data.section !== 'body') return;

    const key = data.column.dataKey;

    if (key === 'img_sistema' || key === 'img_ponto') {
      // Pega base64 original da array dataRows pelo índice da linha
      const originalImgData = data.row.index >= 0 ? dataRows[data.row.index][key] : null;

      console.log(`🧩 Célula linha ${data.row.index}, coluna ${key}:`, originalImgData?.substring(0, 50));

      if (typeof originalImgData === 'string' && originalImgData.startsWith('data:image/')) {
        try {
          const mime = originalImgData.split(';')[0];
          if (!['data:image/png', 'data:image/jpeg'].includes(mime)) {
            console.warn('❌ Formato de imagem não suportado:', mime);
            return;
          }

          const imgType = mime.endsWith('png') ? 'PNG' : 'JPEG';

          // Tamanho máximo da imagem
          const maxImgWidth = 40;
          const maxImgHeight = 40;

          // Calcula tamanho respeitando a célula
          const cellW = data.cell.width;
          const cellH = data.cell.height;
          const imgW = Math.min(cellW - 4, maxImgWidth);
          const imgH = Math.min(cellH - 4, maxImgHeight);

          // Centraliza imagem na célula
          const x = data.cell.x + (cellW - imgW) / 2;
          const y = data.cell.y + (cellH - imgH) / 2;

          doc.addImage(originalImgData, imgType, x, y, imgW, imgH);
          console.log(`✅ Imagem desenhada em [${x}, ${y}] tamanho (${imgW}x${imgH})`);
        } catch (e) {
          console.error('❌ Erro ao desenhar imagem no PDF:', e);
        }
      } else {
        console.warn(`⚠️ Imagem inválida ou ausente na célula (${key})`);
      }
    }
  }
});

// Salva o PDF
doc.save("Relatorio-Ponto.pdf");





}


function fcCancelar(){
    var objParametros = {};
    sendPost("relatorio", "pesqAcompanhamentoPontoAnalitico", objParametros);
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


