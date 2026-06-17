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
        sweetMensagem('warning','Você não tem permissão');
        return false;
    }
    var strJSONDadosTabela =  fcFormatarDadosArquivos();
    var v_ds_obs = $("#ds_obs_doc").val();
    var v_ic_tipo_documento = $("#ic_tipo_documento").val();

    if($("#colaborador_pk").val()==""){
        var colaborador_pk = 0;
    }
    else{
        var colaborador_pk = $("#colaborador_pk").val();
    }
    var objParametros = {
        "colaborador_pk": colaborador_pk,
        "ds_arquivo": strJSONDadosTabela,
        "ds_obs": v_ds_obs,
        "ic_tipo_documento": v_ic_tipo_documento
    };


    var arrEnviar = carregarController("documento", "salvar", objParametros);
    if (arrEnviar.status == true){
        // Reload datable
        $("#janela_documentos").modal("hide");
        utilsJS.toastNotify(true,arrEnviar.message);
        tblDocumentosColaborador.clear().destroy();
        fcCarregarGridDocumentosColaborador();
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
        "colaborador_pk": $("#colaborador_pk").val(),
        "ds_arquivo": v_arquivo
    };

    var arrEnviar = carregarController("documento", "renomearArquivoColaborador", objParametros);

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
            "<a class='function_delete'><span><i class='fa fa-trash' style='font-size:18px; color:blue'></i></span></a>"
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

        var  data = tblDocumentosColaborador.rows().data();

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
function fcCarregarGridDocumentosColaborador(){
    
    var objParametros = {
        "colaboradores_pk": $("#colaborador_pk").val()
    };

    var v_url = routes_api("documento", "listarDocumentosColaborador", objParametros);
    //Trata a tabela
    tblDocumentosColaborador = $('#tblDocumentosColaborador').DataTable({
        searching: true,
        paging: true,
        scrollX: true,
        pageLength: 10,
        aLengthMenu: [10, 25, 50, 100],
        iDisplayLength: 10,
        processing: false,
        serverSide: false,
        ajax: v_url,
        responsive: true,
        language: {
            emptyTable: "Não existem Dados cadastrados"
        },
        order: [
            [0, "asc"]
        ],
        order: [
            [0, "asc"]
        ],
        columns: [
            {
                mRender: function (data, type, full) {
                    return full['t_pk'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_documento'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_obs'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_nome_original'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    var buttonDelete = "<i class='fa fa-download function_download' style='font-size:18px; color:blue'' title='DOWNLOAD DOCUMENTO'></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class='fa fa-trash function_delete' style='font-size:18px; color:blue'' title='EXCLUIR O DOCUMENTO'></i>";
                    return  buttonDelete;
                },
                'orderable': false,
                'searchable': false,
                width: '80px'
            }
        ]
    });
    $('#tblDocumentosColaborador tbody').on('click', '.function_download', function () {
        var data;

        if(tblDocumentosColaborador.row( $(this).parents('li') ).data()){
            data = tblDocumentosColaborador.row( $(this).parents('li') ).data();
        }
        else if(tblDocumentosColaborador.row( $(this).parents('tr') ).data()){
            data = tblDocumentosColaborador.row( $(this).parents('tr') ).data();
        }
        fcDownloadDocumento(data['pk_doc_bd'],data['t_ds_documento']);
    });
    $('#tblDocumentosColaborador tbody').on('click', '.function_delete', function () {
        var data;

        if(tblDocumentosColaborador.row( $(this).parents('li') ).data()){
            data = tblDocumentosColaborador.row( $(this).parents('li') ).data();
        }
        else if(tblDocumentosColaborador.row( $(this).parents('tr') ).data()){
            data = tblDocumentosColaborador.row( $(this).parents('tr') ).data();
        }

        if(data['t_pk'] != ""){
            fcExcluirDocumento(data['t_pk'],data['pk_doc_bd']);
        }
    });
}

function fcDownloadDocumento(pk_doc_bd,ds_documento){
    var arrCarregar = permissao("documento", "ins");

    if (arrCarregar.status != true){
        sweetMensagem('warning','Você não tem permissão');
        return false;
    }

    //var url_documento = (window.location.protocol+"//"+window.location.host+"/app/src/docs/"+ds_documento)

    //DOWNLOAD
    var v_url = "/documento/download?pk_doc_bd="+pk_doc_bd+"&ds_documento="+ds_documento;

    window.open(v_url, '_blank');
}

function fcExcluirDocumento(v_pk,v_pk_doc){
    var arrCarregar = permissao("documento", "del");

    if (arrCarregar.status != true){
        sweetMensagem('warning','Você não tem permissão');
        return false;
    }
    if(v_pk != ""){

        var objParametros = {
            "pk": v_pk,
            "pk_doc_bd":v_pk_doc
        };

        var arrExcluir = carregarController("documento", "excluir", objParametros);

        if (arrExcluir.status == true){

            //Exibe a mensagem
            utilsJS.toastNotify(true,arrExcluir.message);
            //fcExcluirArquivo(v_ds_documento);
            tblDocumentosColaborador.clear().destroy();
            fcCarregarGridDocumentosColaborador();
        }
        else{
            utilsJS.toastNotify(false,'Falhou a requisição de exclusão.');
        }
    }
    else{
        sweetMensagem('warning','Código não encontrado');
    }
}

function recarregarGridDocs(){
    setTimeout(function(){
        tblDocumentosColaborador.ajax.reload();
    }, 100);
}

function fcAbrirFormNovoDocumento(){
    var arrCarregar = permissao("documento", "ins");

    if (arrCarregar.status != true){
        sweetMensagem('warning','Você não tem permissão');
        return false;
    }
    tblArquivos.clear().destroy();
    fcCarregarGridArquivos();
    $("#janela_documentos").modal("show");
    $("#ds_obs_doc").val("");
}

var formdata = null;

$(document).ready(function(){
   
    formdata = new FormData();

    $(document).on('click', '#cmdIncluirDocumento', fcAbrirFormNovoDocumento);
    
    //carrega dados da grid de documentos

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
    $(document).on('click', '#cmdEnviarDocumento', fcValidarDocumentos);


    fcCarregarGridDocumentosColaborador();
    fcCarregarGridArquivos();
});