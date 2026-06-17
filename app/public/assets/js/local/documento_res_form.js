function fcCarregarGridDocumentos(){
    var objParametros = {
        "leads_pk": $("#leads_pk").val()
    };

    var v_url = routes_api("documento", "listarDocumentosLead", objParametros);
    //Trata a tabela
    tblDocumentos = $('#tblDocumentos').DataTable({
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
                    var buttonDelete = "<i class='fa fa-download function_download' style='font-size:18px; color:blue' title='DOWNLOAD DOCUMENTO'></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class='fa fa-trash function_delete' style='font-size:18px; color:blue' title='EXCLUIR O DOCUMENTO'></i>";
                    return  buttonDelete;
                },
                'orderable': false,
                'searchable': false,
                width: '80px'
            }
        ]
    });
    $('#tblDocumentos tbody').on('click', '.function_download', function () {
        var data;

        if(tblDocumentos.row( $(this).parents('li') ).data()){
            data = tblDocumentos.row( $(this).parents('li') ).data();
        }
        else if(tblDocumentos.row( $(this).parents('tr') ).data()){
            data = tblDocumentos.row( $(this).parents('tr') ).data();
        }
        fcDownloadDocumento(data['pk_doc_bd'],data['t_ds_documento']);
    });
    $('#tblDocumentos tbody').on('click', '.function_delete', function () {
        var data;

        if(tblDocumentos.row( $(this).parents('li') ).data()){
            data = tblDocumentos.row( $(this).parents('li') ).data();
        }
        else if(tblDocumentos.row( $(this).parents('tr') ).data()){
            data = tblDocumentos.row( $(this).parents('tr') ).data();
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
            tblDocumentos.clear().destroy();
            fcCarregarGridDocumentos();
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
    tblArquivos.clear().destroy();
    fcCarregarGridArquivos();
    $("#janela_documentos").modal("show");
    $("#ds_obs_doc").val("");
}

function fcCarregarInfoDocumentos(){
    if($("#leads_pk").val() > 0){
        var objParametros = {
            "pk": $("#leads_pk").val()
        };
        var arrCarregar = carregarController("lead", "listarPk", objParametros);

        $("#ds_lead_titulo_documentos").html("<b>"+arrCarregar.data[0]['ds_lead']+"</b>");
        $("#id_lead_documentos").html("Cód Lead: "+arrCarregar.data[0]['pk']);
        $("#dt_cadastro_lead_documentos").html("Dt de Cad: "+arrCarregar.data[0]['dt_cadastro']);
        $("#dt_ult_atualizacao_lead_documentos").html("Dt Utl atualização: "+arrCarregar.data[0]['dt_ult_atualizacao']);
        $("#ds_usuario_cadastro_documentos").html("Usuário de Cad: "+arrCarregar.data[0]['ds_usuario_cadastro']);
    }
}

$(document).ready(function(){

    $(document).on('click', '#cmdIncluirDocumento', fcAbrirFormNovoDocumento);

    //carrega dados da grid de documentos
    fcCarregarGridDocumentos();
    fcCarregarInfoDocumentos();
});