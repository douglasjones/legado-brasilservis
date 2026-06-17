function fcCarregarGridDocumentosGeral(){
    var objParametros = {
        "lancamentos_pk": $("#lancamentos_pk").val()
    };
    var v_url = routes_api("documento", "listarDocumentosLancamentos", objParametros);
    //Trata a tabela
    tblDocumentosGeral = $('#tblDocumentosGeral').DataTable({
        searching: false,
        paging: false,
        pageLength: 10,
        aLengthMenu: [10, 25, 50, 100],
        iDisplayLength: 10,
        processing: false,
        serverSide: false,
        ajax: v_url,
        responsive: true,
        scrollX: true,
        language: {
            emptyTable: "Não existem Dados cadastrados"
        },
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
                    var buttonDelete = "<i class='fa fa-download function_download' style='font-size:12px; color:blue' title='DOWNLOAD DOCUMENTO'></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class='fa fa-trash function_delete' style='font-size:12px; color:black' title='EXCLUIR O DOCUMENTO'></i>";
                    return  buttonDelete;
                },
                'orderable': false,
                'searchable': false,
                width: '80px'
            }
        ]
    });
    $('#tblDocumentosGeral tbody').on('click', '.function_download', function () {
        var data;

        if(tblDocumentosGeral.row( $(this).parents('li') ).data()){
            data = tblDocumentosGeral.row( $(this).parents('li') ).data();
        }
        else if(tblDocumentosGeral.row( $(this).parents('tr') ).data()){
            data = tblDocumentosGeral.row( $(this).parents('tr') ).data();
        }
        fcDownloadDocumento(data['pk_doc_bd'],data['t_ds_documento']);
    });
    $('#tblDocumentosGeral tbody').on('click', '.function_delete', function () {
        var data;

        if(tblDocumentosGeral.row( $(this).parents('li') ).data()){
            data = tblDocumentosGeral.row( $(this).parents('li') ).data();
        }
        else if(tblDocumentosGeral.row( $(this).parents('tr') ).data()){
            data = tblDocumentosGeral.row( $(this).parents('tr') ).data();
        }

        if(data['t_pk'] != ""){
            fcExcluirDocumento(data['t_pk'],data['pk_doc_bd']);
        }
    });
}

function fcDownloadDocumento(pk_doc_bd,ds_documento){
    var arrCarregar = permissao("documento", "ins");

    if (arrCarregar.status != true){
        utilsJS.toastNotify(false,'Você não tem permissão');
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
        utilsJS.toastNotify(false,'Você não tem permissão');
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
            tblDocumentosGeral.clear().destroy();
            fcCarregarGridDocumentosGeral();
        }
        else{
            utilsJS.toastNotify(false,'Falhou a requisição de exclusão.');
        }
    }
    else{
        utilsJS.toastNotify(false,'Código não encontrado');
    }
}

function fcAbrirFormNovoDocumento(){
    var arrCarregar = permissao("documento", "ins");

    if (arrCarregar.status != true){
        utilsJS.toastNotify(false,'Você não tem permissão');
        return false;
    }
    tblArquivosGeral.clear().destroy();
    fcCarregarGridArquivosGeral();
    $("#janela_documentos").modal("show");
    $("#ds_obs_doc").val("");
}

function fcAnexarDocumento(pk) {

    $("#janela_docs").modal("show");

    $("#lancamentos_pk").val(pk);
    tblDocumentosGeral.clear().destroy();
    fcCarregarGridDocumentosGeral();
}

function fecharModalDocGeral(){
    $("#janela_docs").modal("hide");
}
$(document).ready(function(){

    $(document).on('click', '#cmdIncluirDocumento', fcAbrirFormNovoDocumento);

    //carrega dados da grid de documentos

    fcCarregarGridDocumentosGeral();
});


