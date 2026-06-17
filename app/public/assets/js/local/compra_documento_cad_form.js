function fcValidarDocumentos(){
    var colunas = $('#tblArquivos tbody tr td');
    if ($(colunas[0]).text() == "Não existem Dados cadastrados"){
        $("#alert_documento").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_documento").slideUp(500);
        });
    }
    else{
        fcEnviarDocumento();

    }

}
function fcEnviarDocumento(){
    var arrCarregar = permissao("documento", "ins");

    if (arrCarregar.status != true){
        utilsJS.toastNotify(false,'Você não tem permissão');
        return false;
    }
    var strJSONDadosTabela =  fcFormatarDadosArquivos();
    var v_ds_obs = $("#ds_obs_doc").val();
    var v_ic_tipo_documento = $("#ic_tipo_documento").val();
    if($("#compras_pk").val()==""){
        var compras_pk = 0;
    }
    else{
        var compras_pk = $("#compras_pk").val();
    }

    var objParametros = {
        "compras_pk": compras_pk,
        "ds_arquivo": strJSONDadosTabela,
        "ds_obs": v_ds_obs,
        "ic_tipo_documento": v_ic_tipo_documento
    };


    var arrEnviar = carregarController("documento", "salvar", objParametros);
    if (arrEnviar.status == true){
        // Reload datable
        $("#janela_documentos").modal("hide");
        utilsJS.toastNotify(true,arrEnviar.message);
        tblDocumentos.clear().destroy();
        fcCarregarGridDocumentos();
    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}

function fcCarregarGridArquivos(){
    try {

        tblArquivos = $("#tblArquivos").DataTable(
            {
                "searching": false,
                "paging": false,
                "scrollX": true,
                "columnDefs" : [{
                    orderable: false,
                    targets: [0,1,2,3]
                }]
            }
        );
        return false;
    } catch (error) {
        utilsJS.toastNotify(false,error);
    }
}
//COMEÇO DOCUMENTOS UPLOAD

function fcAlterarNomeArquivo(v_arquivo){

    var objParametros = {
        "compras_pk": $("#compras_pk").val(),
        "ds_arquivo": v_arquivo
    };

    var arrEnviar = carregarController("documento", "renomearArquivoCompra", objParametros);

    if (arrEnviar.status == true){
        // Reload datable
        $("#ds_documento").text(arrEnviar.data);

    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}

function fcApagarArquivo(){
    var nome_arquivo = "";
    $('#tblArquivos tbody tr').click(function () {
        var colunas = $(this).children();
        nome_arquivo = $(colunas[0]).text();
        fcExcluirArquivo(nome_arquivo);
    });

    tblArquivos.row($(this).parents('tr')).remove().draw();
}

function fcCancelarEnvioDocumento(){
    $("#janela_documentos").modal("hide");
}


function fcExcluirArquivo(v_nome_arquivo){
    var objParametros = {
        "nome_arquivo": v_nome_arquivo
    };
    carregarController("documento", "removerArquivo", objParametros);
}
function fcIncluirLinhaArquivo(nome_original){
    tblArquivos.row.add(
        [   $("#pk_documento_bd").text(),
            $("#ds_documento").text(),
            nome_original,
            "<a class='function_delete'><span><i class='fa fa-trash' style='width: 13px'></i></span></a>"
        ]
    ).draw( false );

    //Adiciona o evento click na linha que acabou de ser adicionada.
    $(".function_delete").on("click",fcApagarArquivo);
    return false;
}


function fcFormatarDadosArquivos(){

    var dsDocumento = "";
    var dsNomeOriginal = "";

    var arrKeys = [];
    arrKeys[0] = "ds_documento";
    arrKeys[1] = "ds_nome_original";
    arrKeys[2] = "pk_doc_bd";

    var arrDados = [];
    var i = 0;
    $('#tblArquivos tbody tr').each(function () {
        var colunas = $(this).children();
        pkDocBd = $(colunas[0]).text();
        dsDocumento =  $(colunas[1]).text();
        dsNomeOriginal = $(colunas[2]).text();



        arrDados[i] = [dsDocumento, dsNomeOriginal,pkDocBd];
        i++;
    });

    return arrayToJson(arrKeys, arrDados);

}
function fcSalvarDocumentos(formdata){

    var url = "";


    url = "/documento/salvarDocumento";


    var arrRetornoCarregarControle;

    var request = $.ajax({
        url:          url,
        data:         formdata,
        processData:  false,
        cache:        false,
        async:        false,
        dataType:     'json',
        contentType:  false,
        type:         'post'
    });
    request.done(function(output){
        if (output.status == true){
            $("#pk_documento_bd").text(output.data);
        }else{
            utilsJS.toastNotify(false, 'Falhou a requisição: '+output.message);
        }
    });
    request.fail(function(jqXHR, textStatus){
        utilsJS.toastNotify(false, 'Falhou a requisição: '+textStatus);
    });

}

function fcAbrirFormNovoDocumento(){
    var arrCarregar = permissao("documento", "ins");

    if (arrCarregar.status != true){
        utilsJS.toastNotify(false,'Você não tem permissão');
        return false;
    }
    tblArquivos.clear().destroy();
    fcCarregarGridArquivos();
    $("#janela_documentos").modal("show");
    $("#ds_obs_doc").val("");
}

function fcFormatarDadosDocumentos(){


    try{
        var pk = "";
        var ds_documento = "";
        var ds_obs =  "";
        var ds_nome_original = "";

        var arrKeys = [];
        var arrDados = [];
        arrKeys[0] = "pk";
        arrKeys[1] = "ds_documento";
        arrKeys[2] = "ds_obs";
        arrKeys[3] = "ds_nome_original";

        var i = 0;

        var  data = tblDocumentos.rows().data();

        for(i = 0; i< data.length; i++) {
            pk = data[i]['t_pk'];
            ds_documento = data[i]['t_ds_documento'];
            ds_obs = data[i]['ds_obs'];
            ds_nome_original = data[i]['t_ds_nome_original'];

            arrDados[i] = [pk, ds_documento, ds_obs, ds_nome_original];

        }

            //if ($(this).find('td:nth-child(1) input').val() == "") {


        return arrayToJson(arrKeys, arrDados);
    }
    catch (err) {

        utilsJS.toastNotify(false, err);
    }

}
var formdata = null;

$(document).ready(function(){

    formdata = new FormData();
    $('#fileupload').change(function(){
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
    $(document).on('click', '#cmdCancelarDocumento', fcCancelarEnvioDocumento);

    fcCarregarGridArquivos();

});