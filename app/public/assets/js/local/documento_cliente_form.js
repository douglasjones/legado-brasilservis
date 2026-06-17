function fcCarregarLeadsDocumento() {
    //Carrega os grupos

    var objParametros = {
        "pk":""
    };

    var arrCarregar = carregarController("lead", "listarTodosClientes", objParametros);

    carregarComboAjax($("#leads_pk_documento"), arrCarregar, " ", "pk", "ds_lead");

}

function fcCarregarGridDocumentos(){

    var objParametros = {
        "leads_pk": $("#leads_pk_documento").val()
    };


    var v_url = routes_api("documento", "listarDocumentoClienteLead", objParametros);

    //Trata a tabela
    tblDocumentos = $('#tblDocumentos').DataTable({
        searching: true,
        paging: true,
        scrollX: true,
        iDisplayLength: 10,
        processing: false,
        serverSide: true,
        ajax: v_url,
        responsive: true,
        language: {
            emptyTable: "Não existem Dados cadastrados"
        },
        order: [
            [0, "asc"]
        ],
        columns: [
            {
                mRender: function (data, type, full) {
                    return full['pk'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_documento'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_obs'];
                },
                'orderable': true,
                'searchable': false,
                width: '80px'

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_nome_original'];
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
        fcDownloadDocumento(data['pk_doc_bd'],data['ds_documento']);
    });
    $('#tblDocumentos tbody').on('click', '.function_delete', function () {
        var data;

        if(tblDocumentos.row( $(this).parents('li') ).data()){
            data = tblDocumentos.row( $(this).parents('li') ).data();
        }
        else if(tblDocumentos.row( $(this).parents('tr') ).data()){
            data = tblDocumentos.row( $(this).parents('tr') ).data();
        }

        if(data['pk'] != ""){
            fcExcluirDocumento(data['pk'],data['pk_doc_bd']);
        }
    });

}

function fcDownloadDocumento(pk_doc_bd,ds_documento){
    //var url_documento = (window.location.protocol+"//"+window.location.host+"/app/src/docs/"+ds_documento)
   
    //DOWNLOAD
    var v_url = "/documento/download?pk_doc_bd="+pk_doc_bd+"&ds_documento="+ds_documento;

    window.open(v_url, '_blank');
}

function fcExcluirDocumento(v_pk,v_pk_doc){
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

$(document).ready(function () {

    fcCarregarLeadsDocumento();
    fcCarregarGridDocumentos();

    $('#leads_pk_documento').change(function(){
        tblDocumentos.clear().destroy();
        fcCarregarGridDocumentos();
    });
    
});
