function fcValidarDocumentosGeral(){
    var colunas = $('#tblArquivosGeral tbody tr td');
    if ($(colunas[0]).text() == "Não existem Dados cadastrados"){
        $("#alert_documento").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_documento").slideUp(500);
        });
    }
    else{
        fcEnviarDocumentoGeral();
    }

}
function fcEnviarDocumentoGeral(){
    var arrCarregar = permissao("documento", "ins");

    if (arrCarregar.status != true){
        utilsJS.toastNotify(false,'Você não tem permissão');
        return false;
    }
    var strJSONDadosTabela =  fcFormatarDadosArquivosGeral();
    var v_ds_obs = $("#ds_obs_docGeral").val();

    var objParametros = {
        "lancamentos_pk": $("#lancamentos_pk").val(),
        "ds_arquivo": strJSONDadosTabela,
        "ds_obs": v_ds_obs
    };


    var arrEnviar = carregarController("documento", "salvar", objParametros);
    if (arrEnviar.status == true){
        // Reload datable
        $("#janela_documentos").modal("hide");
        utilsJS.toastNotify(true,arrEnviar.message);
        tblDocumentosGeral.clear().destroy();
        fcCarregarGridDocumentosGeral();
    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}

function fcCarregarGridArquivosGeral(){
    try {

        tblArquivosGeral = $("#tblArquivosGeral").DataTable(
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

function fcAlterarNomeArquivoGeral(v_arquivo){

    var objParametros = {
        "lancamentos_pk": $("#lancamentos_pk").val(),
        "ds_arquivo": v_arquivo
    };

    var arrEnviar = carregarController("documento", "renomearArquivoLancamento", objParametros);

    if (arrEnviar.status == true){
        // Reload datable
        $("#ds_documentoGeral").text(arrEnviar.data);

    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}

function fcApagarArquivoGeral(){
    var nome_arquivo = "";
    $('#tblArquivosGeral tbody tr').click(function () {
        var colunas = $(this).children();
        nome_arquivo = $(colunas[0]).text();
        fcExcluirArquivoGeral(nome_arquivo);
    });

    tblArquivos.row($(this).parents('tr')).remove().draw();
}

function fcCancelarEnvioDocumentoGeral(){
    $("#janela_documentos").modal("hide");
}


function fcExcluirArquivoGeral(v_nome_arquivo){
    var objParametros = {
        "nome_arquivo": v_nome_arquivo
    };
    carregarController("documento", "removerArquivo", objParametros);
}
function fcIncluirLinhaArquivoGeral(nome_original){
    tblArquivosGeral.row.add(
        [   $("#pk_documento_bdGeral").text(),
            $("#ds_documentoGeral").text(),
            nome_original,
            "<a class='function_delete'><span><i class='fa fa-trash' style='width: 13px'></i></span></a>"
        ]
    ).draw( false );

    //Adiciona o evento click na linha que acabou de ser adicionada.
    $(".function_delete").on("click",fcApagarArquivo);
    return false;
}


function fcFormatarDadosArquivosGeral(){

    var dsDocumento = "";
    var dsNomeOriginal = "";

    var arrKeys = [];
    arrKeys[0] = "ds_documento";
    arrKeys[1] = "ds_nome_original";
    arrKeys[2] = "pk_doc_bd";

    var arrDados = [];
    var i = 0;
    $('#tblArquivosGeral tbody tr').each(function () {
        var colunas = $(this).children();
        pkDocBd = $(colunas[0]).text();
        dsDocumento =  $(colunas[1]).text();
        dsNomeOriginal = $(colunas[2]).text();



        arrDados[i] = [dsDocumento, dsNomeOriginal,pkDocBd];
        i++;
    });

    return arrayToJson(arrKeys, arrDados);

}
function fcSalvarDocumentosGeral(formdata){

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
            $("#pk_documento_bdGeral").text(output.data);
        }else{
            utilsJS.toastNotify(false, 'Falhou a requisição: '+output.message);
        }
    });
    request.fail(function(jqXHR, textStatus){
        utilsJS.toastNotify(false, 'Falhou a requisição: '+textStatus);
    });

}
var formdataGeral = null;

$(document).ready(function(){

    formdataGeral = new FormData();
    $('#fileuploadGeral').change(function(){
        //on change event
        if($(this).prop('files').length > 0){
            $.each($(this).prop('files'), function (index, file) {
                formdataGeral.append(index, file);

                fcSalvarDocumentosGeral(formdataGeral);

                $("#ds_nome_originalGeral").html(file.name);

                fcAlterarNomeArquivoGeral(file.name);
                fcIncluirLinhaArquivoGeral(file.name);

            });

        }
    });
    $(document).on('click', '#cmdCancelarDocumento', fcCancelarEnvioDocumentoGeral);
    $(document).on('click', '#cmdEnviarDocumento', fcValidarDocumentosGeral);

    fcCarregarGridArquivosGeral();

});