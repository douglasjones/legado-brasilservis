var tblResultado;
function fcPesquisar() {
    $(".chzn-select").chosen('destroy');
    tblResultado.clear().destroy();
    fcCarregarGrid();

}
function fcVoltar(){
    var objParametros = {};
    sendPost('colaborador','receptivo' ,objParametros);
}

function fcEditar() {
    var objParametros = {
        "colaborador_pk":$("#colaborador_pk").val(),
        "local":$("#local").val()
      };
      sendPost('colaborador','cadForm',objParametros)    
  
}

function fcImpressao() {
    var objParametros = {
        "pk":$("#colaborador_pk").val() 
      };
      sendPost('colaborador','print',objParametros)
}

function fcEditarAgenda(){
    var objParametros = {
        "pk":$("#agenda_colaborador_pk").val()
    };
    sendPost('agenda_colaborador_padrao','cadFormEscala',objParametros)

}

function fcAbrirGridForulario() {
    sendPost('colaborador','fcAbrirGridForulario', { "colaborador_pk": $("#colaborador_pk").val(),"local":1 });
}
function fcAbrirMensagemWhatsAppTel() {
    
    var url = "https://api.whatsapp.com/send?phone=55" + $("#ds_tel").val() + "&text=Olá"
    window.open(url, '_blank');
}


function fcAbrirAcompanhamentoFolhaPonto(){
    //LISTAR O POSTO DE TRABALHO

    $("#leads_consulta_folha_pk").val("");
    $("#agenda_consulta_folha_colaborador_pk").val("");
    $("#colaborador_consulta_folha_pk").val("");
    $(".chzn-select").chosen('destroy');
       
    $("#leads_consulta_folha_pk").val($("#leads_pk").val());
    $("#agenda_consulta_folha_colaborador_pk").val($("#agenda_colaborador_pk").val());
    

    //LISTAR O COLABORADOR
    fcCarregarColaboradorFolhaPonto();
    
    $("#colaborador_consulta_folha_pk").val($("#colaborador_pk").val());

    const dataAtual = new Date();

    // Obtém o mês atual (os meses são indexados a partir de 0, então adicionamos 1)
    const mes = dataAtual.getMonth() + 1;
    if(mes<=9){
        mesAtual = "0"+mes;
    }
    else{
        mesAtual = mes;
    }


    // Obtém o ano atual
    const anoAtual = dataAtual.getFullYear();
    
    $("#ic_consulta_folha_mes").val(mesAtual);


    var html = "";
    for(i=parseInt(anoAtual);i >= parseInt(anoAtual-3);i--){
     
        html += "<option value='" + i + "'>" + i + "</option>";
    }
    $("select[name=ic_consulta_folha_ano]").html(html);


    setTimeout(function () {
        $(".chzn-select").chosen('destroy');
        $(".chzn-select").chosen({ allow_single_deselect: true });
    }, 2000);
    
    fcCarregarGridPontoFolha();
    $("#consulta_folha_ponto").modal("show");
}
function fcAbrirDocumentoColaborador(){
    tblDocumentosColaborador.clear().destroy();
    fcCarregarGridDocumentosColaborador();
    $("#documento_colaborador").modal("show");
}

function fcFecharModalDocs(){
    $("#documento_colaborador").modal("hide");
}
function fcFecharConsultaFolhaPonto(){
    $("#consulta_folha_ponto").modal("hide");
}


function fcCarregarColaboradorFolhaPonto() {
    //Carrega os grupos
    
    var objParametros = {
        "leads_pk": $("#leads_consulta_folha_pk").val()
    };

    var arrCarregar = carregarController("colaborador", "listarColaboradorLead", objParametros);
    //NewWindow(v_last_url)
    carregarComboAjax($("#colaborador_consulta_folha_pk"), arrCarregar, " ", "pk", "ds_colaborador");
    carregarComboAjax($("#colaborador_pk_modal"), arrCarregar, " ", "pk", "ds_colaborador");

}
function fcCarregarGridPontoFolha(){
    $("#grid_consulta_folha_ponto").html("");
    $("#grid_consulta_folha_ponto").append("");
    if($('#agenda_consulta_folha_colaborador_pk').val()==""){
        sweetMensagem('warning', 'Esse colaborador não tem escala!');
        return false;
    }
    if($('#leads_consulta_folha_pk').val()==""){
        sweetMensagem('warning', 'Preencha todos os campos!');
        return false;
    }
    if($('#colaborador_consulta_folha_pk').val()==""){
        sweetMensagem('warning', 'preencha todos os campos!');
        return false;
    }
    
    let objParametros = {
        "leads_pk": $('#leads_consulta_folha_pk').val(),
        "colaborador_pk": $('#colaborador_consulta_folha_pk').val(),
        "agenda_colaborador_pk": $('#agenda_consulta_folha_colaborador_pk').val(),
        "ic_mes": $('#ic_consulta_folha_mes').val(),
        "ic_ano": $('#ic_consulta_folha_ano').val()
    };
    let arrCarregar = carregarController("ponto_folha", "listarConsultaPontoColaborador", objParametros);
    var html ="";
    if (arrCarregar.status == true) {
        if(arrCarregar.data.length>0){
            html+="<div class='row'>";
            html+="<div class='table-container'>";
            html+="<table  style='width:100%;overflow-y: scroll;height: 20px;' id='tblResultado1'>";
            html+="<thead>";
            html+="<tr>";
            html+="                    <th  style='  text-align: center'>";
            html+="                        Validado";
            html+="             </th>";
            html+="                    <th  style='  text-align: center'>";
            html+="                        Data";
            html+="             </th>";
            html+="                             <th align='center' style=' text-align: center'>";
            html+="                             Dia Semana";
            html+="                            </th>";
            html+="       <th  style=' text-align: center'>";
            html+="                                Ini Exp ";
            html+="                            </th>";
            html+="                            <th  style=' text-align: center'>";
            html+="                                Ini Inter ";
            html+="                            </th>";
            html+="                            <th  style='  text-align: center'>";
            html+="                                Fim Inter";
            html+="                            </th>";
            html+="                             <th align='center' style=' text-align: center'>";
            html+="                             Fim Exp";
            html+="                            </th>";
            html+="                             <th align='center' style=' text-align: center'>";
            html+="                             Situação";
            html+="                            </th>";
            html+="                             <th align='center' style=' text-align: center'>";
            html+="                             Apontamento";
            html+="                            </th>";
            html+="                             <th align='center' style=' text-align: center'>";
            html+="                             Ação";
            html+="                            </th>";
            
            html+="                        </tr>";
            html+="                    </thead>";
            html+="                    <tbody >";
            var v_dias_trabalhados = 0;
            var v_ponto_batidos = 0;
            var v_dias_folga = 0;
            for(i=0;i<arrCarregar.data.length;i++){
                //-----------------------------//
                if(arrCarregar.data[i]['pontos_dia'][0]['situacao']=="Escala"){
                    v_dias_trabalhados++;
                }
                else{
                    v_dias_folga++;
                }
                //----------------------------------//
                if(arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_expediente']!=" " ||
                    arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_intervalo']!=" "  ||
                    arrCarregar.data[i]['pontos_dia'][0]['ponto_term_intervalo']!=" "  ||
                    arrCarregar.data[i]['pontos_dia'][0]['ponto_term_expediente']!=" " 
                ){
                    v_ponto_batidos++;
                }

                html+="<tr>";
                    
                html+='<th  style="  text-align: center">';
                if(arrCarregar.data[i]['pontos_dia'][0]['ic_apontamento']!=0){
                    html += "   <input type='checkbox' id='ic_validado" + i + "' size='3' checked value=1>";
                }
                else{
                    html += "   <input type='checkbox' id='ic_validado" + i + "' size='3' value=1>";
                }
                html+='</th>';
                html+='<th  style="  text-align: center">';
                html+= arrCarregar.data[i]['dt_hora_ponto'];
                html += "   <input type='hidden' id='dt_hora_ponto" + i + "' size='3' value='" + arrCarregar.data[i]['dt_hora_ponto'] + "'>";
                html+='</th>';
                html+='<th  style="  text-align: center">';
                html+= arrCarregar.data[i]['dia_da_semana'];
                html += "         <input type='hidden' id='dt_dia_semana" + i + "' size='3' value='" + arrCarregar.data[i]['dia_da_semana'] + "'>";
                html+='</th>';
                if(arrCarregar.data[i]['pontos_dia'][0]['ic_apontamento']!=0){
                    if(arrCarregar.data[i]['pontos_dia'][0]['tipo_ponto_pk']==1 && arrCarregar.data[i]['pontos_dia'][0]['ic_apontamento_ini']==1){
                        html+='<th  style="  text-align: center;background-color:#ADD8E6">';
                        
                        html += "<input type='text' id='hr_ini_expediente" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_expediente'] + "' onkeypress='mascara(this,horamask)'>";
                        html+='</th>';
                    }
                    else{
                        html+='<th  style="  text-align: center">';
                        html += "<input type='text' id='hr_ini_expediente" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_expediente'] + "' onkeypress='mascara(this,horamask)'>";
                        html+='</th>';
                    }
                    if(arrCarregar.data[i]['pontos_dia'][0]['tipo_ponto_pk']==1 && arrCarregar.data[i]['pontos_dia'][0]['ic_apontamento_ini_int']==3){
                        html+='<th  style="  text-align: center;background-color:#ADD8E6">';
                        html += "<input type='text' id='hr_ini_intervalo" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_intervalo'] + "' onkeypress='mascara(this,horamask)'>";
                        html+='</th>';
                    }
                    else{
                        html+='<th  style="  text-align: center">';
                        html += "<input type='text' id='hr_ini_intervalo" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_intervalo'] + "' onkeypress='mascara(this,horamask)'>";
                        html+='</th>';
                    }
                    if(arrCarregar.data[i]['pontos_dia'][0]['tipo_ponto_pk']==1 && arrCarregar.data[i]['pontos_dia'][0]['ic_apontamento_fim_int']==4){
                        html+='<th  style="  text-align: center;background-color:#ADD8E6">';
                        html += "<input type='text' id='hr_fim_intervalo" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_term_intervalo'] + "' onkeypress='mascara(this,horamask)'>";
                        
                        html+='</th>';
                    }
                    else{
                        html+='<th  style="  text-align: center">';
                        html += "<input type='text' id='hr_fim_intervalo" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_term_intervalo'] + "' onkeypress='mascara(this,horamask)'>";
                        html+='</th>';
                    }
                    
                    if(arrCarregar.data[i]['pontos_dia'][0]['tipo_ponto_pk']==1 && arrCarregar.data[i]['pontos_dia'][0]['ic_apontamento_ter']==2){
                        
                        html+='<th  style="  text-align: center;background-color:#ADD8E6">';
                        html += "<input type='text' id='hr_fim_expediente" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_term_expediente'] + "' onkeypress='mascara(this,horamask)'>";
                        html+='</th>';
                    }
                    else{
                        html+='<th  style="  text-align: center">';
                        html += "<input type='text' id='hr_fim_expediente" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_term_expediente'] + "' onkeypress='mascara(this,horamask)'>";
                        html+='</th>';
                    }
                    html+='<th  style="  text-align: center;background-color:#ADD8E6">';
                    html += arrCarregar.data[i]['pontos_dia'][0]['situacao'] ;
                    html+='</th>';
                    
                    html+='<th  style="  text-align: center">';
                    html+='<select class="form-control form-control-sm" id="tipo_ponto_pk'+i+'">';
                    html+='        <option value="">Selecione</option>';
                    html+='    <optgroup label="PONTO">';
                    html+='        <option value="1">Ponto/Expediente</option>';
                    html+='    </optgroup>';
                    html+='    <optgroup label="FALTA">';
                    html+='        <option value="2">Falta</option>';
                    html+='        <option value="11">Abonada</option>';
                    html+="        <option value='16'>Atestado</option>";
                    html+="        <option value='37'>Atestado de horas </option>";
                    html+="        <option value='18'>Declaração da defesa civil</option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="Folga">';
                    html+='        <option value="3">Folga</option>';
                    html+="        <option value='20'>Folga compensatória</option>";
                    html+="        <option value='21'>Folga de feriado</option>";
                    html+="        <option value='25'>Troca Folga</option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="Afastamento">';
                    html+="        <option value='5'>Afastamento</option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="Férias">';
                    html+="        <option value='6'>Férias</option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="Disciplina">';
                    html+="        <option value='8'>Disciplina</option>";
                    html+="        <option value='17'>Advertencia</option>";
                    html+="        <option value='19'>Demissão</option>";
                    html+="        <option value='22'>Justa causa</option>";
                    html+="        <option value='23'>Recisão indireta</option>";
                    html+="        <option value='24'>Suspensão</option>";
                    html+='    </optgroup>';
                    html+='</select>';
                    html+='</th>';



                    
                    html+='</th>';
                    html+='<th  style="  text-align: center">';
                    html += '     <a onclick="fcSalvarApontamentoReloginho('+i+')"><i class="bi-solid bi-check" style="color:green;cursor:pointer"></i></a> ';
                    html+='</th>';
                    
                }
                else{
                    html+='<th  style="  text-align: center">';
                    html += "<input type='text' id='hr_ini_expediente" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_expediente'] + "' onkeypress='mascara(this,horamask)'>";
                    html+='</th>';
                    html+='<th  style=" text-align: center">';
                    html += "<input type='text' id='hr_ini_intervalo" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_intervalo'] + "' onkeypress='mascara(this,horamask)'>";
                    html+='</th>';
                    html+='<th  style=" text-align: center">';
                    html += "<input type='text' id='hr_fim_intervalo" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_term_intervalo'] + "' onkeypress='mascara(this,horamask)'>";
                    html+='</th>';
                    html+='<th  style="  text-align: center">';
                    html += "<input type='text' id='hr_fim_expediente" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_term_expediente'] + "' onkeypress='mascara(this,horamask)'>";
                    html+='</th>';
                    html+='<th  style="  text-align: center">';
                    html += arrCarregar.data[i]['pontos_dia'][0]['situacao'] ;
                    html+='</th>';
                    html+='<th  style="  text-align: center">';
                    html+='<select class="form-control form-control-sm" id="tipo_ponto_pk'+i+'">';
                    html+='        <option value="">Selecione</option>';
                    html+='    <optgroup label="PONTO">';
                    html+='        <option value="1">Ponto/Expediente</option>';
                    html+='    </optgroup>';
                    html+='    <optgroup label="FALTA">';
                    html+='        <option value="2">Falta</option>';
                    html+='        <option value="11">Abonada</option>';
                    html+="        <option value='16'>Atestado</option>";
                    html+="        <option value='37'>Atestado de horas </option>";
                    html+="        <option value='18'>Declaração da defesa civil</option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="Folga">';
                    html+='        <option value="3">Folga</option>';
                    html+="        <option value='20'>Folga compensatória</option>";
                    html+="        <option value='21'>Folga de feriado</option>";
                    html+="        <option value='25'>Troca Folga</option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="Afastamento">';
                    html+="        <option value='5'>Afastamento</option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="Férias">';
                    html+="        <option value='6'>Férias</option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="Disciplina">';
                    html+="        <option value='8'>Disciplina</option>";
                    html+="        <option value='17'>Advertencia</option>";
                    html+="        <option value='19'>Demissão</option>";
                    html+="        <option value='22'>Justa causa</option>";
                    html+="        <option value='23'>Recisão indireta</option>";
                    html+="        <option value='24'>Suspensão</option>";
                    html+='    </optgroup>';
                    html+='</select>';
                    html+='</th>';
                    html+='<th  style="  text-align: center">';
                    html += '    <a onclick="fcSalvarApontamentoReloginho('+i+')"><i class="bi-solid bi-check" style="color:green;cursor:pointer"></i></a> ';
                    html+='</th>';
                    
                }
                html+='</tr>';
            }
                            
            html+='</tbody>';
            html+=' </table>';
            html+=' </div>';
            html+='</div>';
        }
        else{
            html+='<h1 class="pulsing-text">Este colaborador não tem registros!</h1>';
        }
        
    }
    
    $("#grid_consulta_folha_ponto").html(html);

}

//FUNÇÃO PARA FUNCIONAR O RELOAD DO APONTAMENTO
function fcSalvarApontamentoReloginho(index){

    var data_apontamento = $("#dt_hora_ponto"+index).val();
    var hr_ini_expediente = $("#hr_ini_expediente"+index).val();
    var hr_ini_intervalo = $("#hr_ini_intervalo"+index).val();
    var hr_fim_intervalo = $("#hr_fim_intervalo"+index).val();
    var hr_fim_expediente = $("#hr_fim_expediente"+index).val();
    var tipo_ponto_pk = $("#tipo_ponto_pk"+index).val();

    //QUANDO FOR FALTA MOTIVO FALTA DEFAULT 3 = Atestado
    var motivo_falta_pk = 3
    //QUANDO FOR FALTA MOTIVO AFASTAMENTO DEFAULT 1 = MOTIVOS_MEDICOS
    var motivo_afastamento_pk = 1


    if(tipo_ponto_pk==""){
        sweetMensagem('warning', 'Informe o Apontamento!');
        return false;
    }
    //SALVAR 
    var objParametros = {
        "leads_pk": $('#leads_consulta_folha_pk').val(),
        "colaborador_pk": $('#colaborador_consulta_folha_pk').val(),
        "agenda_colaborador_pk": $('#agenda_consulta_folha_colaborador_pk').val(),
        "dt_apontamento": data_apontamento,
        "tipo_apontamento_pk": tipo_ponto_pk,
        "hr_ini_expediente": hr_ini_expediente,
        "hr_ini_intervalo": hr_ini_intervalo,
        "hr_fim_intervalo": hr_fim_intervalo,
        "hr_fim_expediente": hr_fim_expediente,
        "motivo_falta_pk": motivo_falta_pk,
        "motivo_afastamento_pk": motivo_afastamento_pk
    };
    var arrEnviar = carregarController("agenda_colaborador_apontamento", "salvarApontamentoReloginho", objParametros);
    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);
        $("#grid_consulta_folha_ponto").html("");
        $("#grid_consulta_folha_ponto").append("");
        fcCarregarGridPontoFolha();
            
    }
    else{

        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }

}


   //Carrega os grupos

function fcCarregarLeads() {
   var objParametros = {
    "pk": ""
};

var arrCarregar = carregarController("lead", "listarTodos", objParametros);

carregarComboAjax($("#leads_consulta_folha_pk"), arrCarregar, " ", "pk", "ds_lead");

}

function fcCarregarColaborador() {
//Carrega os grupos

var objParametros = {
    "leads_pk": $("#leads_pk").val()
};

var arrCarregar = carregarController("colaborador", "listarColaboradorLead", objParametros);
//NewWindow(v_last_url)
carregarComboAjax($("#colaborador_pk"), arrCarregar, " ", "pk", "ds_colaborador");

}

function fcAbrirGridForulario() {
    sendPost('colaborador','fcAbrirGridForulario', { "colaborador_pk": $("#colaborador_pk").val(),"local":1 });
}
$(document).ready(function () {
    $(".chzn-select").chosen({ allow_single_deselect: true });
  
    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdEditar', fcEditar); 
    $(document).on('click', '#cmdVoltar', fcVoltar); 



    //RELOGINHO
    fcCarregarLeads();
    fcCarregarColaborador();
    $("#ic_consulta_folha_mes").change(function () {
        fcCarregarGridPontoFolha();

    });
    $("#ic_consulta_folha_mes").change(function () {
        fcCarregarGridPontoFolha();

    });


    $('#fileuploadDocsColaborador').change(function(){
        //on change event
        if($(this).prop('files').length > 0){
            $.each($(this).prop('files'), function (index, file) {
                formdata.append(index, file);
                fcSalvarDocumentos(formdata);

                $("#ds_nome_original").html(file.name);

                fcAlterarNomeArquivo(file.name);
                fcIncluirLinhaArquivo(file.name);

            });

        }
    });
});
