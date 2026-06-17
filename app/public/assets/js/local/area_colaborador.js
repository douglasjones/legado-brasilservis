//REGISTRAR PONTO
function fcTelaParaRegistrarPonto(){
    var objParametros = {};
    sendPost("area_colaborador", "receptivoRegistrarPonto", objParametros);
}

//NOVO REGISTRO
function inicio(){
    var objParametros = {};
    sendPost("area_colaborador", "receptivo", objParametros);
}
function novoRegistro(){
    var objParametros = {};
    sendPost("area_colaborador", "passo1", objParametros);
}
function passo2(){
    var objParametros = {};
    sendPost("area_colaborador", "passo2", objParametros);
}


function fcBuscarColaborador(){
    var id_empresa = $("#id_empresa").val();
    var id_colaborador = $("#id_colaborador").val();

    if(id_empresa==""){
        sweetMensagem('warning',"Informe o Id Empresa.");
        return false;
    }
    if(id_colaborador==""){
        sweetMensagem('warning',"Informe o Id Colaborador.");
        return false;
    }

    var objParametros = {
        "id_empresa": id_empresa,
        "id_colaborador": (id_colaborador)
    };

    var arrEnviar = carregarController("area_colaborador", "buscarColaborador", objParametros);

    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);
        setTimeout(function(){
            sendPost("area_colaborador", "passo3", objParametros);
        }, 2000);

    }
    else{
        utilsJS.toastNotify(false, arrEnviar.message);
    }
}
function passo3(){
    var id_empresa = $("#id_empresa").val();
    var id_colaborador = $("#id_colaborador").val();
    var objParametros = {
        "id_empresa": id_empresa,
        "id_colaborador": (id_colaborador)
    };
    sendPost("area_colaborador", "passo3", objParametros);
}

function passo4(){
    var id_empresa = $("#id_empresa").val();
    var id_colaborador = $("#id_colaborador").val();
    var objParametros = {
        "id_empresa": id_empresa,
        "id_colaborador": (id_colaborador)
    };
    sendPost("area_colaborador", "passo4", objParametros);
}
function fcTirarFoto(){
    var id_empresa = $("#id_empresa").val();
    var id_colaborador = $("#id_colaborador").val();
    var objParametros = {
        "id_empresa": id_empresa,
        "id_colaborador": (id_colaborador)
    };
    sendPost("area_colaborador", "tirar_foto_novo_registro", objParametros);
}

function fcRefazer(){
    $("#entrada").css('display', 'inline');
    $("#webcamVideo").css('display', 'inline');
    $("#button_tirar_foto").css('display', 'inline');
    $("#confirmação").css('display', 'none');
    $("#button_confirmacao").css('display', 'none');
    $("#webcamCanvas").css('display', 'none');
}


function fcEnviarFoto(){
    const photoDataUrl = webcamCanvas.toDataURL("image/png");
    formdata.append("id_empresa",$("#id_empresa").val());
    formdata.append("id_colaborador",$("#id_colaborador").val());
    formdata.append("base64",photoDataUrl);

    $.ajax({
        type: 'POST',
        url: '/api/area_colaborador/salvarPrimeiroRegistro',
        data: formdata,
        processData: false,
        contentType: false,
        complete: function (response) {
            try {
                var log = JSON.parse(response.responseText);
                if(log.status==true) {
                    utilsJS.toastNotify(true, log.message);
                    setTimeout(function () {
                        sendPost("area_colaborador", "receptivo", {});
                    }, 2000);
                }
                else{
                    utilsJS.toastNotify(false,log.message);
                    setTimeout(function(){
                        sendPost("area_colaborador", "receptivo", {});
                    }, 2000);
                }


            } catch (e) {
                utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
            }
        }
    });
}

function fcSalvarPonto(tipo_ponto){

    if($("#leads_pk").val()==""){
        utilsJS.toastNotify(false,"Por favor, Informe o Posto de serviço.");
        return false;
    }

    formdataBaterPonto.append("ds_pin",$("#ds_pin_ponto").val());
    formdataBaterPonto.append("id_colaborador",$("#id_colaborador_ponto").val());
    formdataBaterPonto.append("base64",$("#base64Foto").val());
    formdataBaterPonto.append("leads_pk",$("#leads_pk").val());
    formdataBaterPonto.append("tipo_ponto_pk",tipo_ponto);

    $.ajax({
        type: 'POST',
        url: '/api/area_colaborador/salvarPonto',
        data: formdataBaterPonto,
        processData: false,
        contentType: false,
        complete: function (response) {
            try {
                var log = JSON.parse(response.responseText);
                if(log.status==true) {
                    utilsJS.toastNotify(true, log.message);
                    setTimeout(function () {
                        sendPost("area_colaborador", "receptivo", {});
                    }, 2000);
                }
                else{
                    utilsJS.toastNotify(false,log.message);
                    setTimeout(function(){
                        sendPost("area_colaborador", "receptivo", {});
                    }, 2000);
                }


            } catch (e) {
                utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
            }
        }
    });
}


var formdata = null;
var formdataPonto = null;
var formdataBaterPonto = null;

$(document).ready(function()
    {
        formdata = new FormData();
        formdataPonto = new FormData();
        formdataBaterPonto = new FormData();
        //Atribui os eventos NOVO REGISTRO VAI PARA O PASSO 1
        $(document).on('click', '#novoRegistro', novoRegistro);
        $(document).on('click', '#inicio', inicio);
        $(document).on('click', '#passo1', novoRegistro);
        $(document).on('click', '#passo2', passo2);
        $(document).on('click', '#passo3', passo3);
        $(document).on('click', '#buscarColaborador', fcBuscarColaborador);
        $(document).on('click', '#passo4', passo4);
        $(document).on('click', '#tirar_foto', fcTirarFoto);
        $(document).on('click', '#refazer', fcRefazer);
        $(document).on('click', '#enviarFoto', fcEnviarFoto);

        //REGISTRAR PONOT DIRETO

        $(document).on('click', '#registrarPonto', fcTelaParaRegistrarPonto);
        //MASCARA
        //$("#id_colaborador").mask("9999999999");

    }
);
